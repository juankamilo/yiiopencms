<?php
/**
CrugeAuthManager

este modulo requiere instalacion en config/main.php :
'components'=>array(
'authManager' => array(
'class' => 'application.modules.cruge.components.CrugeAuthManager',
),
),

para acceder a el:
Yii::app()->authManager
o
Yii::app()->user->rbac

para consultar si el usuario actual tiene permiso para alguna operacion:
if(Yii::app()->user->checkAccess('createPost')){...}


FUNCIONES EXTENDIDAS
====================

permiten listar items. no estan declaradas como parte de la interfaz original de Yii

Yii::app()->user->rbac->roles;
Yii::app()->user->rbac->tasks;
Yii::app()->user->rbac->operations;

SINTAXIS PARA LA DESCRIPCION DEL AUTHITEM / MENU ITEMS
======================================================

Para obtener menu items en base a RBAC se usa el metodo:

Yii::app()->user->rbac->getMenu([$args]);

Para que los menu items funcionen deben contener una SINTAXIS en
la DESCRIPCION como describo a continuacion.

[$args] son argumentos en forma de array para adosarle a las URL
de los menu items aqui obtenidos, leer mas abajo en este mismo apartado.

SINTAXIS

La descripcion del CAuthItem puede venir en dos formas:

A) Estandar.   	"Mi Descripcion"

B) Extendia.	":2 Menu Usuario {menu_principal} {action_site_index}"

El caso (A) no tiene mayor explicación, la descripcion se usa como
un texto informativo y nada mas.

El caso (B) representa la "Sintaxis de la Descripcion"

SU FORMA ES:

[:]+[nn]+[Texto]+[{auth_item_padre}{acion_auth_item_name}]

en donde:

":" 		indica que el CAuthItem es un MENU o un SUB MENU

"nn"		indica la posicion ordinal, si es un MENU o u SUB MENU

"Texto"		indica el texto del menu o submenu

{parent}	el nombre del CAuthItem superior (al existir este argumento
se considera al CAuthItem como un SUBMENU de "parent"

{action}	el nombre del CAuthItem que servirá como ACTION para la
url del sub menu item.

FORMAS POSIBLES:

":texto"
menu de primer nivel con etiqueta "Texto"

":N texto"
menu de primer nivel con etiqueta "Texto" en posicion N

":texto {parent} {action}"
menu de segundo nivel con etiqueta "Texto" relativo a "parent" quien
debe ser otro CAuthItem con sintaxis de descripcion, pero de tipo Menu.
siendo action el nombre del CAuthItem que va a lanzar la URL

":texto {parent}" este caso no tiene sentido. (Un submenu sin URL ?!)


MANEJO DE LA URL / MAPPINGS

La url se toma del nombre de un CAuthItem que ha sido indicada
como la seleccionada para el submenu item usando la sintaxis aqui
indicada.

ejemplo:

action item:			usada como:

action_site_index		array('site/index')

action_ui_editprofile	array('/cruge/ui/editprofile')

nota acerca de donde salio: "/cruge/ui/":

esta clase tiene un atributo llamado "mappings" el cual
va a convertir patrones de URL,

por ejemplo:

"action_ui_XXX" sera convertida a "action_cruge_ui_XXX"


URL: CASO MODULOS

El uso de mapping tambien ayuda para cuando los actions
declarados como "actions de menu item" apuntan a aquellos
que estan definidos en un modulo de usuario,

por ejemplo, tienes un action que realmente esta definido
dentro de un modulo llamado "tumodulox"

action_default_index

si no usas un mapping, este action se generara relativo a
la aplicacion sin modulo, es decir:

array('default/index')  Y DARA UN ERROR, porque
asume que lo que "para tu modulo" era el controller default
se le pedira a la aplicacion base y fallará porque no existe.

por tanto, se resolveria en config main pasandole a esta clase
un nuevo mapping:

'mapping' => array(
'action_ui_'=>'action_cruge_ui_',
'action_default_' => 'action_tumodulox_default_',
),

ahora, al invocar el menu se producira correctamente la URL
apuntando a tu modulo:

array('/tumodulox/default/index'),


ARGUMENTOS DE LA URL ($args)

La url recibe argumentos cuando se invoca a Yii::app()->rbac->getMenu

Por ejemplo, queremos que todos los menu items tengas adosado un
parametro (o mas):

Yii::app()->user->rbac->getMenu(array('idempresa'=>123));

esto generara un array de menu items (para CMenu o MbMenu o EMenu etc)
cuya url será finalmente asi:

array('label'=>'cosa', 'url'=>'', 'items'=>
array('cosa menor','url'=>array('site/index','idempresa'=>123))
..
..
)


EJEMPLO USO DE ESTA CLASE
=========================

$auth=Yii::app()->authManager;

$auth->createOperation('createPost','create a post');
$auth->createOperation('readPost','read a post');
$auth->createOperation('updatePost','update a post');
$auth->createOperation('deletePost','delete a post');

$bizRule='return Yii::app()->user->id==$params["post"]->authID;';
$task=$auth->createTask('updateOwnPost','update a post by author himself',$bizRule);
$task->addChild('updatePost');

$role=$auth->createRole('reader');
$role->addChild('readPost');

$role=$auth->createRole('author');
$role->addChild('reader');
$role->addChild('createPost');
$role->addChild('updateOwnPost');

$role=$auth->createRole('editor');
$role->addChild('reader');
$role->addChild('updatePost');

$role=$auth->createRole('admin');
$role->addChild('editor');
$role->addChild('author');
$role->addChild('deletePost');

// se asignan los roles a los usuarios, aqui el iduser es el nombre, pero puede (y debe)
// ser el Yii::app()->user->id (id=que invoca a user->getId())

$auth->assign('reader','readerA');
$auth->assign('author','authorB');
$auth->assign('editor','editorC');
$auth->assign('admin','adminD');

@author: original de Maurizio Domba <mdomba@gmail.com>
@author: Christian Salazar H. <christiansalazarh@gmail.com> @salazarchris74
@license protected/modules/cruge/LICENSE
 */
class CrugeAuthManager extends CAuthManager implements IAuthManager
{

    // este mapping es usado para el caso de obtener una URL en base a
    // un CAuthItem.getDescription() usando el mecanismo de  sintaxis
    // descrito mas abajo.
    //
    //	resuelve el problema de 'action_ui_editprofile' el cual
    //	realmente representa al action action_cruge_ui_editprofile
    //  al usar getTaskUrl se obtendrá: array('/cruge/ui/editprofile')
    //  en vez de: array('/ui/editprofile')
    //
    //	Importante:
    //	  este es un mapping de patrones, no de indices directos.
    //
    public $mapping = array(
        'action_ui_' => 'action_cruge_ui_',
    );
    private $_enumcontrollers;
    private $_enumactions;

    /**
     * @var string the ID of the {@link CDbConnection} application component. Defaults to 'db'.
     * The database must have the tables as declared in "framework/web/auth/*.sql".
     */
    public $connectionID = 'db';
    /**
     * @var CDbConnection the database connection. By default, this is initialized
     * automatically as the application component whose ID is indicated as {@link connectionID}.
     */
    public $db;


    public function init()
    {
        parent::init();
        $this->getDbConnection(); // para inicializar db
    }

    /** retorna el nombre de una tabla configurandola para los prefijos definidos en el modulo
    $table: uno de {'authitem', 'authitemchild', 'authassignment'}
     */
    public function getTableName($table)
    {
        return CrugeUtil::getTableName($table);
    }

    public function usingSqlite()
    {
        return false;
    }


    /**
     * Performs access check for the specified user.
     * @param string $itemName the name of the operation that need access check
     * @param mixed $userId the user ID. This should can be either an integer and a string representing
     * the unique identifier of a user. See {@link IWebUser::getId}.
     * @param array $params name-value pairs that would be passed to biz rules associated
     * with the tasks and roles assigned to the user.
     * @return boolean whether the operations can be performed by the user.
     */
    public function checkAccess($itemName, $userId, $params = array())
    {
        $assignments = $this->getAuthAssignments($userId);
        return $this->checkAccessRecursive($itemName, $userId, $params, $assignments);
    }

    /**
     * Performs access check for the specified user.
     * This method is internally called by {@link checkAccess}.
     * @param string $itemName the name of the operation that need access check
     * @param mixed $userId the user ID. This should can be either an integer and a string representing
     * the unique identifier of a user. See {@link IWebUser::getId}.
     * @param array $params name-value pairs that would be passed to biz rules associated
     * with the tasks and roles assigned to the user.
     * @param array $assignments the assignments to the specified user
     * @return boolean whether the operations can be performed by the user.
     * @since 1.1.3
     */
    protected function checkAccessRecursive($itemName, $userId, $params, $assignments)
    {
        if (($item = $this->getAuthItem($itemName)) === null) {
            return false;
        }
        Yii::trace('Checking permission "' . $item->getName() . '"', 'system.web.auth.CDbAuthManager');
        if ($this->executeBizRule($item->getBizRule(), $params, $item->getData())) {
            if (in_array($itemName, $this->defaultRoles)) {
                return true;
            }
            if (isset($assignments[$itemName])) {
                $assignment = $assignments[$itemName];
                if ($this->executeBizRule($assignment->getBizRule(), $params, $assignment->getData())) {
                    return true;
                }
            }
            $parents = $this->db->createCommand()
                ->select('parent')
                ->from($this->getTableName('authitemchild'))
                ->where('child=:name', array(':name' => $itemName))
                ->queryColumn();
            foreach ($parents as $parent) {
                if ($this->checkAccessRecursive($parent, $userId, $params, $assignments)) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Adds an item as a child of another item.
     * @param string $itemName the parent item name
     * @param string $childName the child item name
     * @throws CException if either parent or child doesn't exist or if a loop has been detected.
     */
    public function addItemChild($itemName, $childName)
    {
        if ($itemName === $childName) {
            throw new CException(Yii::t(
                'yii',
                'Cannot add "{name}" as a child of itself.',
                array('{name}' => $itemName)
            ));
        }

        $rows = $this->db->createCommand()
            ->select()
            ->from($this->getTableName('authitem'))
            ->where(
            'name=:name1 OR name=:name2',
            array(
                ':name1' => $itemName,
                ':name2' => $childName
            )
        )
            ->queryAll();

        if (count($rows) == 2) {
            if ($rows[0]['name'] === $itemName) {
                $parentType = $rows[0]['type'];
                $childType = $rows[1]['type'];
            } else {
                $childType = $rows[0]['type'];
                $parentType = $rows[1]['type'];
            }
            $this->checkItemChildType($parentType, $childType);
            if ($this->detectLoop($itemName, $childName)) {
                throw new CrugeException(Yii::t(
                    'yii',
                    'Cannot add "{child}" as a child of "{name}". A loop has been detected.',
                    array('{child}' => $childName, '{name}' => $itemName)
                ));
            }

            $this->db->createCommand()
                ->insert(
                $this->getTableName('authitemchild'),
                array(
                    'parent' => $itemName,
                    'child' => $childName,
                )
            );
        } else {
            throw new CrugeException(Yii::t(
                'yii',
                'Either "{parent}" or "{child}" does not exist.',
                array('{child}' => $childName, '{parent}' => $itemName)
            ));
        }
    }

    /**
     * Removes a child from its parent.
     * Note, the child item is not deleted. Only the parent-child relationship is removed.
     * @param string $itemName the parent item name
     * @param string $childName the child item name
     * @return boolean whether the removal is successful
     */
    public function removeItemChild($itemName, $childName)
    {
        return $this->db->createCommand()
            ->delete(
            $this->getTableName('authitemchild'),
            'parent=:parent AND child=:child',
            array(
                ':parent' => $itemName,
                ':child' => $childName
            )
        ) > 0;
    }

    /**
     * Returns a value indicating whether a child exists within a parent.
     * @param string $itemName the parent item name
     * @param string $childName the child item name
     * @return boolean whether the child exists
     */
    public function hasItemChild($itemName, $childName)
    {
        return $this->db->createCommand()
            ->select('parent')
            ->from($this->getTableName('authitemchild'))
            ->where(
            'parent=:parent AND child=:child',
            array(
                ':parent' => $itemName,
                ':child' => $childName
            )
        )
            ->queryScalar() !== false;
    }

    /**
     * Returns the children of the specified item.
     * @param mixed $names the parent item name. This can be either a string or an array.
     * The latter represents a list of item names (available since version 1.0.5).
     * @return array all child items of the parent
     */
    public function getItemChildren($names)
    {
        if (is_string($names)) {
            $condition = 'parent=' . $this->db->quoteValue($names);
        } else {
            if (is_array($names) && $names !== array()) {
                foreach ($names as &$name) {
                    $name = $this->db->quoteValue($name);
                }
                $condition = 'parent IN (' . implode(', ', $names) . ')';
            }
        }

        $rows = $this->db->createCommand()
            ->select('name, type, description, bizrule, data')
            ->from(
            array(
                $this->getTableName('authitem'),
                $this->getTableName('authitemchild')
            )
        )
            ->where($condition . ' AND name=child')
            ->queryAll();

        $children = array();
        foreach ($rows as $row) {
            if (($data = @unserialize($row['data'])) === false) {
                $data = null;
            }
            $children[$row['name']] = new CAuthItem($this, $row['name'], $row['type'], $row['description'], $row['bizrule'], $data);
        }
        return $children;
    }

    /**
     * Assigns an authorization item to a user.
     * @param string $itemName the item name
     * @param mixed $userId the user ID (see {@link IWebUser::getId})
     * @param string $bizRule the business rule to be executed when {@link checkAccess} is called
     * for this particular authorization item.
     * @param mixed $data additional data associated with this assignment
     * @return CAuthAssignment the authorization assignment information.
     * @throws CrugeException if the item does not exist or if the item has already been assigned to the user
     */
    public function assign($itemName, $userId, $bizRule = null, $data = null)
    {
        /*
        if($this->usingSqlite() && $this->getAuthItem($itemName)===null)
            throw new CrugeException(Yii::t('yii','The item "{name}" does not exist.',array('{name}'=>$itemName)));
        */

        // por christian salazar
        if ($userId == '' || $userId == null) {
            return null;
        }

        $this->db->createCommand()
            ->insert(
            $this->getTableName('authassignment'),
            array(
                'itemname' => $itemName,
                'userid' => $userId,
                'bizrule' => $bizRule,
                'data' => serialize($data)
            )
        );
        return new CAuthAssignment($this, $itemName, $userId, $bizRule, $data);
    }

    /**
     * Revokes an authorization assignment from a user.
     * @param string $itemName the item name
     * @param mixed $userId the user ID (see {@link IWebUser::getId})
     * @return boolean whether removal is successful
     */
    public function revoke($itemName, $userId)
    {
        return $this->db->createCommand()
            ->delete(
            $this->getTableName('authassignment'),
            'itemname=:itemname AND userid=:userid',
            array(
                ':itemname' => $itemName,
                ':userid' => $userId
            )
        ) > 0;
    }

    /**
     * Returns a value indicating whether the item has been assigned to the user.
     * @param string $itemName the item name
     * @param mixed $userId the user ID (see {@link IWebUser::getId})
     * @return boolean whether the item has been assigned to the user.
     */
    public function isAssigned($itemName, $userId)
    {
        return $this->db->createCommand()
            ->select('itemname')
            ->from($this->getTableName('authassignment'))
            ->where(
            'itemname=:itemname AND userid=:userid',
            array(
                ':itemname' => $itemName,
                ':userid' => $userId
            )
        )
            ->queryScalar() !== false;
    }

    /**
     * Returns the item assignment information.
     * @param string $itemName the item name
     * @param mixed $userId the user ID (see {@link IWebUser::getId})
     * @return CAuthAssignment the item assignment information. Null is returned if
     * the item is not assigned to the user.
     */
    public function getAuthAssignment($itemName, $userId)
    {
        $row = $this->db->createCommand()
            ->select()
            ->from($this->getTableName('authassignment'))
            ->where(
            'itemname=:itemname AND userid=:userid',
            array(
                ':itemname' => $itemName,
                ':userid' => $userId
            )
        )
            ->queryRow();
        if ($row !== false) {
            if (($data = @unserialize($row['data'])) === false) {
                $data = null;
            }
            return new CAuthAssignment($this, $row['itemname'], $row['userid'], $row['bizrule'], $data);
        } else {
            return null;
        }
    }


    /**
     * Retorna un array con los userid que tienen el item asignado
     * @param string $itemName el item a buscar
     * @return array con los $userid
     */
    public function getUsersAssigned($itemName)
    {
        $rows = $this->db->createCommand()
            ->select()
            ->from($this->getTableName('authassignment'))
            ->where(
            'itemname=:itemname ',
            array(
                ':itemname' => $itemName,
            )
        )
        //->group('userid')
            ->queryAll();
        $users = array();
        if ($rows != null) {
            foreach ($rows as $row) {
                if (!in_array($row['userid'], $users)) {
                    $users[] = $row['userid'];
                }
            }
        }
        return $users;
    }


    /**
     * Retorna un array con todos los "parents" de un item hallados en authitemchild
     *
     * este metodo permite ir hacia atras, lo opuesto a getChildrens, permitiendo conocer
     * quienes hacen referencia a un authItem
     *
     * @param string $itemName el item a buscar
     * @return array con los CAuthItems que hacen la referencia al item.
     */
    public function getParents($itemName)
    {
        $rows = $this->db->createCommand()
            ->select()
            ->from($this->getTableName('authitemchild'))
            ->where(
            'child=:itemname ',
            array(
                ':itemname' => $itemName,
            )
        )
            ->queryAll();
        $parents = array();
        if ($rows != null) {
            foreach ($rows as $row) {
                $parents[] = $this->getAuthItem($row['parent']);
            }
        }
        return $parents;
    }


    /**
     * Retorna el numero de userid's que tienen el item asignado
     * @param string $itemName el item a buscar
     * @return cantidad de usuarios asignados
     */
    public function getCountUsersAssigned($itemName)
    {
        // TODO: optimizar esto con una consulta de agrupacion y cuenta
        //
        $ar = $this->getUsersAssigned($itemName);
        return count($ar);
    }

    /**
     * Returns the item assignments for the specified user.
     * @param mixed $userId the user ID (see {@link IWebUser::getId})
     * @return array the item assignment information for the user. An empty array will be
     * returned if there is no item assigned to the user.
     */
    public function getAuthAssignments($userId)
    {
        $rows = $this->db->createCommand()
            ->select()
            ->from($this->getTableName('authassignment'))
            ->where('userid=:userid', array(':userid' => $userId))
            ->queryAll();
        $assignments = array();
        foreach ($rows as $row) {
            if (($data = @unserialize($row['data'])) === false) {
                $data = null;
            }
            $assignments[$row['itemname']] = new CAuthAssignment($this, $row['itemname'], $row['userid'], $row['bizrule'], $data);

        }
        return $assignments;
    }

    /**
     * Saves the changes to an authorization assignment.
     * @param CAuthAssignment $assignment the assignment that has been changed.
     */
    public function saveAuthAssignment($assignment)
    {
        $this->db->createCommand()
            ->update(
            $this->getTableName('authassignment'),
            array(
                'bizrule' => $assignment->getBizRule(),
                'data' => serialize($assignment->getData()),
            ),
            'itemname=:itemname AND userid=:userid',
            array(
                'itemname' => $assignment->getItemName(),
                'userid' => $assignment->getUserId()
            )
        );
    }

    /**
     * Returns the authorization items of the specific type and user.
     * @param integer $type the item type (0: operation, 1: task, 2: role). Defaults to null,
     * meaning returning all items regardless of their type.
     * @param mixed $userId the user ID. Defaults to null, meaning returning all items even if
     * they are not assigned to a user.
     * @return array the authorization items of the specific type.
     */
    public function getAuthItems($type = null, $userId = null)
    {
        if ($type === null && $userId === null) {
            $command = $this->db->createCommand()
                ->select()
                ->from($this->getTableName('authitem'));
        } else {
            if ($userId === null) {
                $command = $this->db->createCommand()
                    ->select()
                    ->from($this->getTableName('authitem'))
                    ->where('type=:type', array(':type' => $type));
            } else {
                if ($type === null) {
                    $command = $this->db->createCommand()
                        ->select('name,type,description,t1.bizrule,t1.data')
                        ->from(
                        array(
                            $this->getTableName('authitem') . ' t1',
                            $this->getTableName('authassignment') . ' t2'
                        )
                    )
                        ->where('name=itemname AND userid=:userid', array(':userid' => $userId));
                } else {
                    $command = $this->db->createCommand()
                        ->select('name,type,description,t1.bizrule,t1.data')
                        ->from(
                        array(
                            $this->getTableName('authitem') . ' t1',
                            $this->getTableName('authassignment') . ' t2'
                        )
                    )
                        ->where(
                        'name=itemname AND type=:type AND userid=:userid',
                        array(
                            ':type' => $type,
                            ':userid' => $userId
                        )
                    );
                }
            }
        }
        $items = array();
        foreach ($command->queryAll() as $row) {
            if (($data = @unserialize($row['data'])) === false) {
                $data = null;
            }
            $items[$row['name']] = new CAuthItem($this, $row['name'], $row['type'], $row['description'], $row['bizrule'], $data);
        }
        return $items;
    }

    /**
     * Creates an authorization item.
     * An authorization item represents an action permission (e.g. creating a post).
     * It has three types: operation, task and role.
     * Authorization items form a hierarchy. Higher level items inheirt permissions representing
     * by lower level items.
     * @param string $name the item name. This must be a unique identifier.
     * @param integer $type the item type (0: operation, 1: task, 2: role).
     * @param string $description description of the item
     * @param string $bizRule business rule associated with the item. This is a piece of
     * PHP code that will be executed when {@link checkAccess} is called for the item.
     * @param mixed $data additional data associated with the item.
     * @return CAuthItem the authorization item
     * @throws CrugeException if an item with the same name already exists
     */
    public function createAuthItem($name, $type, $description = '', $bizRule = null, $data = null)
    {
        $this->db->createCommand()
            ->insert(
            $this->getTableName('authitem'),
            array(
                'name' => $name,
                'type' => $type,
                'description' => $description,
                'bizrule' => $bizRule,
                'data' => serialize($data)
            )
        );
        return new CAuthItem($this, $name, $type, $description, $bizRule, $data);
    }

    /**
     * Removes the specified authorization item.
     * @param string $name the name of the item to be removed
     * @return boolean whether the item exists in the storage and has been removed
     */
    public function removeAuthItem($name)
    {
        if ($this->usingSqlite()) {
            $this->db->createCommand()
                ->delete(
                $this->getTableName('authitemchild'),
                'parent=:name1 OR child=:name2',
                array(
                    ':name1' => $name,
                    ':name2' => $name
                )
            );
            $this->db->createCommand()
                ->delete(
                $this->getTableName('authassignment'),
                'itemname=:name',
                array(
                    ':name' => $name,
                )
            );
        }

        return $this->db->createCommand()
            ->delete(
            $this->getTableName('authitem'),
            'name=:name',
            array(
                ':name' => $name
            )
        ) > 0;
    }

    /**
     * Returns the authorization item with the specified name.
     * @param string $name the name of the item
     * @return CAuthItem the authorization item. Null if the item cannot be found.
     */
    public function getAuthItem($name)
    {
        $row = $this->db->createCommand()
            ->select()
            ->from($this->getTableName('authitem'))
            ->where('name=:name', array(':name' => $name))
            ->queryRow();

        if ($row !== false) {
            if (($data = @unserialize($row['data'])) === false) {
                $data = null;
            }
            return new CAuthItem($this, $row['name'], $row['type'], $row['description'], $row['bizrule'], $data);
        } else {
            return null;
        }
    }

    /**
     * Saves an authorization item to persistent storage.
     * @param CAuthItem $item the item to be saved.
     * @param string $oldName the old item name. If null, it means the item name is not changed.
     */
    public function saveAuthItem($item, $oldName = null)
    {
        if ($this->usingSqlite() && $oldName !== null && $item->getName() !== $oldName) {
            $this->db->createCommand()
                ->update(
                $this->getTableName('authitemchild'),
                array(
                    'parent' => $item->getName(),
                ),
                'parent=:whereName',
                array(
                    ':whereName' => $oldName,
                )
            );
            $this->db->createCommand()
                ->update(
                $this->getTableName('authitemchild'),
                array(
                    'child' => $item->getName(),
                ),
                'child=:whereName',
                array(
                    ':whereName' => $oldName,
                )
            );
            $this->db->createCommand()
                ->update(
                $this->getTableName('authassignment'),
                array(
                    'itemname' => $item->getName(),
                ),
                'itemname=:whereName',
                array(
                    ':whereName' => $oldName,
                )
            );
        }

        $this->db->createCommand()
            ->update(
            $this->getTableName('authitem'),
            array(
                'name' => $item->getName(),
                'type' => $item->getType(),
                'description' => $item->getDescription(),
                'bizrule' => $item->getBizRule(),
                'data' => serialize($item->getData()),
            ),
            'name=:whereName',
            array(
                ':whereName' => $oldName === null ? $item->getName() : $oldName,
            )
        );

        // extra: manejo de sintaxis.
        //
        //	cuando un CAuthItem se guarda, se asegura que en caso de ser
        //  un item tipo TASK que usa sintaxis de descripcion entonces
        //	vigile el menuitem superior, asignando o desasignando automatica-
        //	-mente a este TASK con las tareas superior.


        if ($item->getType() == CAuthItem::TYPE_TASK) {
            if ($this->isTaskSubMenuItem($item)) {
                // es un submenu de otra tarea $parent
                $parent = $this->getParentMenuAuthItem($item);
                if ($parent != null) {
                    // no es huerfana
                    // a inserta a $item como hija de $parent
                    if (!$this->hasItemChild($parent->name, $item->name)) {
                        $this->addItemChild($parent->name, $item->name);
                    }
                }
            }
        }

    }

    /**
     * Saves the authorization data to persistent storage.
     */
    public function save()
    {
    }

    /**
     * Removes all authorization data.
     */
    public function clearAll()
    {
        $this->clearAuthAssignments();
        $this->db->createCommand()->delete($this->getTableName('authitemchild'));
        $this->db->createCommand()->delete($this->getTableName('authitem'));
    }

    /**
     * Removes all authorization assignments.
     */
    public function clearAuthAssignments()
    {
        $this->db->createCommand()->delete($this->getTableName('authassignment'));
    }

    /**
     * Checks whether there is a loop in the authorization item hierarchy.
     * @param string $itemName parent item name
     * @param string $childName the name of the child item that is to be added to the hierarchy
     * @return boolean whether a loop exists
     */
    public function detectLoop($itemName, $childName)
    {
        if ($childName === $itemName) {
            return true;
        }
        foreach ($this->getItemChildren($childName) as $child) {
            if ($this->detectLoop($itemName, $child->getName())) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return CDbConnection the DB connection instance
     * @throws CrugeException if {@link connectionID} does not point to a valid application component.
     */
    protected function getDbConnection()
    {
        if ($this->db !== null) {
            return $this->db;
        } else {
            if (($this->db = Yii::app()->getComponent($this->connectionID)) instanceof CDbConnection) {
                return $this->db;
            } else {
                throw new CrugeException(Yii::t(
                    'yii',
                    'CDbAuthManager.connectionID "{id}" is invalid. Please make sure it refers to the ID of a CDbConnection application component.',
                    array('{id}' => $this->connectionID)
                ));
            }
        }
    }


    /* extension:  no pertenece a la interfaz

    */
    public function getAuthItemTypeName($type, $booleanPlural = false)
    {
        if ($type == CAuthItem::TYPE_ROLE) {
            return $booleanPlural == false ? "rol" : "roles";
        }
        if ($type == CAuthItem::TYPE_TASK) {
            return $booleanPlural == false ? "tarea" : "tareas";
        }
        if ($type == CAuthItem::TYPE_OPERATION) {
            return $booleanPlural == false ? "operacion" : "operaciones";
        }
        return $type;
    }

    public function nextType(CAuthItem $item)
    {
        if ($item->type == CAuthItem::TYPE_ROLE) {
            return CAuthItem::TYPE_TASK;
        }
        if ($item->type == CAuthItem::TYPE_TASK) {
            return CAuthItem::TYPE_OPERATION;
        }
        return null;
    }

    public function getRoles($userId = null)
    {
        return $this->getAuthItems(CAuthItem::TYPE_ROLE);
    }

    public function getTasks($userId = null)
    {
        return $this->getAuthItems(CAuthItem::TYPE_TASK);
    }

    public function getOperations($userId = null)
    {
        return $this->getAuthItems(CAuthItem::TYPE_OPERATION);
    }

    private function _filtroCruge($ops)
    {
        $crugeKey = "action_cruge_ui_";
        $ar = array();
        foreach ($ops as $op) {
            $actionFull = $this->_mapAction($op->name, $this->mapping);
            // ejemplo: convirtio action_ui_editprofile
            //			a : 	  action_cruge_ui_editprofile
            if (substr($actionFull, 0, strlen($crugeKey)) == $crugeKey) {
                $ar[] = $op;
            }
        }
        return $ar;
    }

    private function _filtroNoCruge($ops)
    {
        $crugeKey = "action_cruge_ui_";
        $ar = array();
        foreach ($ops as $op) {
            $actionFull = $this->_mapAction($op->name, $this->mapping);
            // ejemplo: convirtio action_ui_editprofile
            //			a : 	  action_cruge_ui_editprofile
            if (substr($actionFull, 0, strlen($crugeKey)) != $crugeKey) {
                $ar[] = $op;
            }
        }
        return $ar;
    }

    private function _filtroNoController($ops)
    {
        $ar = array();
        $arcontrollers = array();
        foreach ($this->enumControllers() as $controllerName) {
            $arcontrollers[] = strtolower("action_" . $controllerName . "_");
        }

        foreach ($ops as $op) {
            $found = false;
            // busca a ver si esta operacion esta en la lista
            // de controllers definidos.
            foreach ($arcontrollers as $cn) {
                if (substr($op->name, 0, strlen($cn)) == $cn) {
                    $found = true;
                    break;
                }
            }
            // es una operacion no asociada a un controller
            // por tanto le damos paso en este filtro
            if (!$found) {
                $ar[] = $op;
            }
        }
        return $ar;
    }

    // solo aquellas operaciones: "controller_site"
    // (acceso maestro a controllers)
    private function _filtroControllerMaestro($ops)
    {
        $ar = array();
        $arcontrollers = array();
        foreach ($this->enumControllers() as $controllerName) {
            $arcontrollers[] = strtolower("controller_" . $controllerName);
        }
        $arcontrollers[] = "controller_ui"; // cruge

        foreach ($ops as $op) {
            $found = false;
            // busca a ver si esta operacion esta en la lista
            // de controllers definidos.
            foreach ($arcontrollers as $cn) {
                if (substr($op->name, 0, strlen($cn)) == $cn) {
                    $found = true;
                }
            }
            // es una operacion no asociada a un controller
            // por tanto le damos paso en este filtro
            if ($found == true) {
                $ar[] = $op;
            }
        }
        return $ar;
    }

    private function _filtroNotControllerMaestro($ops)
    {
        $ar = array();
        $arcontrollers = array();
        foreach ($this->enumControllers() as $controllerName) {
            $arcontrollers[] = strtolower("controller_" . $controllerName);
        }
        $arcontrollers[] = "controller_ui"; // cruge
        foreach ($ops as $op) {
            $found = false;
            // busca a ver si esta operacion esta en la lista
            // de controllers definidos.
            foreach ($arcontrollers as $cn) {
                if (substr($op->name, 0, strlen($cn)) == $cn) {
                    $found = true;
                }
            }
            // es una operacion no asociada a un controller
            // por tanto le damos paso en este filtro
            if ($found == false) {
                $ar[] = $op;
            }
        }
        return $ar;
    }

    private function _filtroControllerName($ops, $controllerName)
    {
        $key = '_' . strtolower($controllerName) . '_';
        $ar = array();
        foreach ($ops as $item) {
            if (strstr($item->name, $key)) {
                $ar[] = $item;
            }
        }
        return $ar;
    }


    /**
     * getOperationsFiltered
     *    entrega un array de CAuthItem de tipo "operacion" pero en base a
     *    un filtro:
     *
     *        0 => todas
     *        1 => aquellas definidas en codigo
     *        2 => las de cruge
     *        3 => solo contollers maestro
     *        X => solo las del controller name = X
     *
     * @param string $filter
     * @access public
     * @return array de CAuthItem
     */
    public function getOperationsFiltered($filter, $oprList = null)
    {
        $ar = array();
        if ($oprList == null) {
            $oprList = $this->getOperations();
        }
        if (($filter == '') || ($filter == '0')) {
            // entrega el array completo de operaciones
            $ar = $oprList;
        } elseif ($filter == '1') {
            // OTRAS
            // aquellas que no pertenecen a ningun controller especifico
            // 	(porque no indican ningun "action_controller_" conocido)
            // y que tampoco son de Cruge..
            $ar = $this->_filtroNotControllerMaestro(
                $this->_filtroNoCruge(
                    $this->_filtroNoController($oprList)
                )
            );
        } elseif ($filter == '2') {
            // CRUGE
            // aqui se usa el $this->mapping tambien para reconocer
            // los actions de Cruge
            $ar = $this->_filtroCruge($oprList);
        } elseif ($filter == '3') {
            // CONTROLLERS
            // solo aquellos controllers maestros
            $ar = $this->_filtroControllerMaestro(
                $this->_filtroNoController($oprList)
            );
        } else {
            // CONTROLLER X
            // aquellas que coinciden con un nombre de controller seleccion.
            $ar = $this->_filtroControllerName($oprList, $filter);
        }
        return $ar;
    }


    public function getDataProviderRoles($pageSize = 20)
    {
        return new CArrayDataProvider($this->getRoles(), array(
            'keyField' => 'name',
            'sort' => array(
                'defaultOrder' => array('name'),
            ),
            'pagination' => array(
                'pageSize' => $pageSize,
            ),
        ));
    }

    public function getDataProviderTasks($pageSize = 50)
    {
        return new CArrayDataProvider(
            $this->reorderItemArray($this->getTasks())
            , array(
                'keyField' => 'name',
                'sort' => array(
                    'defaultOrder' => array('name'),
                ),
                'pagination' => array(
                    'pageSize' => $pageSize,
                ),
            ));
    }

    /**
     * getDataProviderOperations
     *    entrega un dataprovider segun el filtro seleccionado.
     *
     * @see getOperationsFiltered
     *
     * @param string $filter
     * @param int $pageSize
     * @access public
     * @return CArrayDataProvider de elementos CAuthItem
     */
    public function getDataProviderOperations($filter = '', $pageSize = 50)
    {
        return new CArrayDataProvider($this->getOperationsFiltered($filter)
            , array(
                'keyField' => 'name',
                'sort' => array(
                    'defaultOrder' => array('name'),
                ),
                'pagination' => array(
                    'pageSize' => $pageSize,
                ),
            ));
    }

    public function getRolesAsOptions($emptyLabel = null)
    {
        $ar = array();
        if ($emptyLabel != null) {
            $ar[''] = $emptyLabel;
        }

        foreach ($this->roles as $rol) {
            $ar[$rol->name] = $rol->name;
        }
        return $ar;
    }


    // FUNCIONES PARA EL MANEJO DE SINTAXIS APLICADA A LA DESCRIPCION
    // DEL AUTH ITEM:
    //
    //
    //

    /**
     * isItem
     *     detecta si la descripcion del item indica que es un menu (o submenu).
     *     lo hace buscando el simbolo ":" al inicio de la descripcion
     * @param mixed $obj
     * @access private
     * @return boolean true es un menu o un submenuitem.
     */
    private function isItem($obj)
    {
        $d = trim($obj->getDescription());
        if (strlen($d) > 0) {
            return ($d[0] == ':') ? true : false;
        }
        return false;
    }


    /**
     * isSubItem
     *    detecta si el item es un submenu, primero preguntando si isItem()
     *    y finalmente preguntando si contiene caracteres { }
     *
     * @param mixed $obj
     * @access private
     * @return bool true si es un subitem.
     */
    public function isSubItem($obj)
    {
        $d = trim($obj->getDescription());
        if (!$this->isItem($obj)) {
            return false;
        }
        // sin mucho analisis lexico-sintaxis...
        // asi facilito..
        return strstr($d, "{") && strstr($d, "}");
    }


    /**
     * getTaskText
     *    devuelve la descripcion pura de un CAuthItem considerando la
     *    sintaxis:
     *
     *        ":Descripcion Pura{menu_padre}{action_xxx}"
     *
     *        entregando de aqui solo a: "Descripcion Pura"
     *
     * @param mixed $obj
     * @access private
     * @return void
     */
    public function getTaskText($obj)
    {
        return $this->_getTextFromDescription($obj->getDescription());
    }

    /**
     * getItemPosition
     *    obtiene de la descripcion el argumento numerico de posicion tras
     *    el simbolo inicial ":".
     *      ejemplo ":123 mi item" devolvera: 123, sino 0
     *
     * @param mixed $obj
     * @access private
     * @return integer
     */
    public function getItemPosition($obj)
    {
        $d = trim(ltrim($obj->getDescription(), ':'));
        $dig = '';
        for ($i = 0; $i < strlen($d); $i++) {
            if (ctype_digit($d[$i])) {
                $dig .= $d[$i];
            } else {
                return ($dig) * 1;
            }
        }
        return null;
    }


    /**
     * _getParentMenuName
     *    de un texto ":hola {menu_item}" devolvera: "menu_item"
     * @param mixed $m
     * @access private
     * @return void
     */
    private function _getParentMenuName($m)
    {
        if ($m == null) {
            return "";
        }
        if (strlen($m) == 0) {
            return "";
        }
        $r = "";
        $s = 0;
        for ($i = 0; $i < strlen($m); $i++) {
            if ($s == 0) {
                if ($m[$i] == '{') {
                    $s = 1;
                }
            } elseif ($s == 1) {
                if ($m[$i] == '}') {
                    return trim($r);
                } elseif ($m[$i] == '{') {
                    $r = "";
                } else {
                    $r .= $m[$i];
                }
            }
        }
        return trim($r);
    }

    /**
     * _getActionItemName
     *    obtiene el contenido de la segunda llave en la sintaxis de la descr
     *        ejemplo:
     *            $m = "blabla {perrito} y {gatico}";
     *        retorna:
     *            "gatico" (la segunda llave)
     * @param string $m  contenido de la descripcion
     * @access protected
     * @return string
     */
    private function _getActionItemName($m)
    {
        if ($m == null) {
            return "";
        }
        if (strlen($m) == 0) {
            return "";
        }
        $r = "";
        $s = 0;
        for ($i = 0; $i < strlen($m); $i++) {
            if ($s == 0) {
                if ($m[$i] == '{') {
                    $s = 1;
                }
            } elseif ($s == 1) {
                if ($m[$i] == '}') {
                    $s = 2;
                }
            } elseif ($s == 2) {
                if ($m[$i] == '{') {
                    $s = 3;
                    $r = '';
                }
            } elseif ($s == 3) {
                if ($m[$i] == '}') {
                    return trim($r);
                } elseif ($m[$i] == '{') {
                    $r = '';
                } else {
                    $r .= $m[$i];
                }
            }
        }
        return trim($r);
    }

    private function _getTextFromDescription($description)
    {
        // limpia cualquier ":" delante
        $d = trim(ltrim($description, ':'));
        // pasa el numero que pudiese venir a continuacion
        $p = 0;
        for ($i = 0; $i < strlen($d); $i++) {
            if (ctype_digit($d[$i])) {
                continue;
            } else {
                $p = $i;
                break;
            }
        }

        if ($p == 0) {
            // no hay numero
        } else {
            // si hay numero, descripcion continua en posicion $p
            $d = substr($d, $p);
        }

        // busca hasta algun posible "{"
        $tmp = "";
        for ($i = 0; $i < strlen($d); $i++) {
            if ($d[$i] != '{') {
                $tmp .= $d[$i];
            } else {
                break;
            }
        }

        return trim($tmp);
    }

    private function _mapAction($action, $mappings)
    {
        if (trim($action) == "") {
            return "";
        }
        foreach ($mappings as $map => $xy) {
            if (substr($action, 0, strlen($map)) == $map) {
                return $xy . substr($action, strlen($map));
            }
        }
        return $action;
    }


    public function isTaskMenuItem($obj)
    {
        return $this->isItem($obj);
    }

    public function isTaskSubMenuItem($obj)
    {
        return $this->isSubItem($obj);
    }

    public function isTaskTopMenuItem($obj)
    {
        $n = trim($obj->getName());
        if ($this->isItem($obj)) {
            if (strpos($n, "menu_")===0) {
                return true;
            }
            return false;
        } else {
            return false;
        }
    }

    /**
     * getTaskParentMenuName
     *    de una tarea con descripcion: ":Menu1 {menu_principal}" devolvera:
     *        el string "menu_principal"
     * @param mixed $obj
     * @access public
     * @return string  o ""
     */
    public function getTaskParentMenuName($obj)
    {
        if ($this->isTaskSubMenuItem($obj)) {
            return $this->_getParentMenuName($obj->getDescription());
        } else {
            return "";
        }
    }

    /**
     * getParentMenuAuthItem
     *    pregunta si el CAuthItem $obj tiene un itename padre en su sintaxis
     *    de descripcion y si este item name existe como un CAuthItem.
     * @param CAuthItem $obj el authitem a consultar.
     * @access public
     * @return CAuthItem del padre dado por la sintaxis de la descripcion.
     */
    public function getParentMenuAuthItem($obj)
    {
        $itemname_padre = $this->getTaskParentMenuName($obj);
        return $this->getAuthItem($itemname_padre);
    }

    /**
     * getTaskActionItemName
     *    de una tarea con descripcion: ":Menu1 {menu_principal} {action_site_index}"
     *         devolvera:
     *            "action_site_index" (el itemname de aquella operacion "child"
    que sera usada como disparador del menu)
     *
     * @param mixed $obj
     * @access public
     * @return string o ""
     */
    public function getTaskActionItemName($obj)
    {
        if ($this->isTaskSubMenuItem($obj)) {
            return $this->_getActionItemName($obj->getDescription());
        } else {
            return "";
        }
    }

    /**
     * getTaskUrl
     *    de aquella tarea que usa sintaxis en su descripcion para manejar menues
     *    retorna la parte que hace referencia al auth item name del action
     *    seleccionado por el usuario para responder para este menu.
     *
     * @param CAuthItem $obj la tarea
     * @param array $args Argumentos en forma de array para adosar
     * @access public
     * @return array url en forma de array
     */
    public function getTaskUrl($obj, $args = null)
    {

        $itemname = $this->getTaskActionItemName($obj);
        if ($itemname == '') {
            return '';
        }

        // ver si hay un mapping para el action
        //	este no es un mapping tradicional, sino de patrones:
        //		ejemplo:
        //		mapping: "action_ui_" cambiara por "action_cruge_ui_"
        //
        $itemname = $this->_mapAction($itemname, $this->mapping);
        
        // ejemplo, recibe: action_site_index
        // lo descompone en array('site/index', ..$args..)
        $e = explode('_', $itemname);
        if (sizeof($e) == 3) {
            $controllerName = $e[1];
            $actionName = $e[2];
            $a = array();
            $a[] = "/" . $controllerName . "/" . $actionName;
            foreach ($args as $k => $v) {
                $a[$k] = $v;
            }
            return $a;
        } elseif (sizeof($e) == 4) {
            $moduleName = $e[1];
            $controllerName = $e[2];
            $actionName = $e[3];
            $a = array();
            $a[] = "/".$moduleName . '/' . $controllerName . "/" . $actionName;
            foreach ($args as $k => $v) {
                $a[$k] = $v;
            }
            return $a;
        } else {
            return array();
        }
    }

    /**
     * explodeTask
     *    descompone la descripcion de un TASK en sus partes segun sintaxis:
     *        ":POS descripcion{menu}{action}"
     *
     * @param CAuthItem $obj
     * @access public
     * @return array indexado array('description','position','menu','action')
     */
    public function explodeTask($obj)
    {
        return array(
            'description' => $this->getTaskText($obj),
            'position' => $this->getItemPosition($obj),
            'menu' => $this->getTaskParentMenuName($obj),
            'action' => $this->getTaskActionItemName($obj),
        );
    }

    /**
     * setTaskAction
     *    Actualiza un CAuthItem ($obj), le pondrá en su descripcion (usando la
     *    sintaxis para menues) al nuevo action, considerando cualquier action
     *    existente.
     *
     *    ejemplo:
     *        descripcion original: ":Subitem X {menu_prinicipal}"
     *        el $action_item_name es: "action_site_prueba"
     *        el resultado de la descripcion final sera:
     *            ":Subitem X {menu_prinicipal} {action_site_prueba}"
     *
     *            ***** EL CAuthItem sera Actualizado *****
     *
     * @param mixed $obj el objeto CAuthItem a quien se le modificara la descr.
     * @param mixed $action_item_name El action a usar (itemname del CAuthItem)
     * @access public
     * @return void
     */
    public function setTaskAction($obj, $action_item_name)
    {
        $ar = $this->explodeTask($obj);
        $newDescr = ":" . $ar['position'] . " " . $ar['description']
            . "{" . $ar['menu'] . "}{" . $action_item_name . "}";
        $obj->setDescription($newDescr);
        $this->saveAuthItem($obj);
    }

    /**
     * isTaskMenuItemChild
     *    detecta si un CAuthItem ($item) es un hijo de otro ($posibleSuperior)
     *  utiliza la sintaxis del atributo Description para detectarlo.
     *
     * @param CAuthItem $item
     * @param CAuthItem $posibleSuperior
     * @access public
     * @return void
     */
    public function isTaskMenuItemChild($item, $posibleSuperior)
    {
        return ($this->getTaskParentMenuName($item)
            == $posibleSuperior->getName());
    }


    /**
     * explodeTaskArray
     *    separa un array original de CAuthItem TASK en partes organizadas por
     *    tipo: Menu, MenuItems y Tareas Regulares.
     *
     *    ejemplo:
     *
     *        1. se le da una lista entera de todas las tareas del sistema
     *        usando $this->getTasks():
     *
     *        2. se invoca a este metodo
     *
     *        3. el metodo retorna un array con esta forma:
     *
     *            array(
     *                'topmenu'=>array(...),
     *                'childmenu'=>array('authitemname'=>array(), ... ),
     *                'regular'=>array(...)
     *            );
     *
     *        se ven 3 categorias (indices) de array aqui:
     *            topmenu
     *            childmenu
     *            regular
     *
     *        topmenu:     array de CAuthItem tipo TASK de todos aquellos
     *                     considerados Menu de 1er nivel al usar sintaxis.
     *
     *        childmenu:    array de arrays de CAuthItem indexado por el nombre del
     *                    CAuthItem padre del item usando la sintaxis de menu
     *        orphan:        array de CAuthItem de aquellos marcados con sintaxis
     *                    de sub menu item pero cuyo padre no existe.
     *
     *        regular:    array de CAuthItem de todas las tareas que no son menues.
     *
     *
     * @param array $originTaskList CAuthItem tipo TASK
     * @access public
     * @return array
     */
    public function explodeTaskArray($originTaskList)
    {

        $top = array(); // top menu
        $child = array(); // menuitems de alguien (index)
        $error = array(); // huerfanos
        $regular = array(); // tareas normales

        // detecta TOP menu tasks
        foreach ($originTaskList as $task) {
            if ($this->istaskTopMenuItem($task)) {
                $top[] = $task;
            }
        }

        // busca las tareas que son hijas de las primeras
        // halladas. (son hijas dada la sintaxis de descripcion del CAuthItem)
        //
        foreach ($top as $topmenuitem) {
            foreach ($originTaskList as $task) {
                if ($this->isTaskMenuItemChild($task, $topmenuitem)) {
                    $child[$topmenuitem->name][] = $task;
                }
            }
        }


        // agrega los huerfanos.
        // aquellas tareas marcadas como menuitems cuyo padre no existe
        //
        foreach ($originTaskList as $task) {
            if ($this->isTaskSubMenuItem($task)) {
                if (!$this->getParentMenuAuthItem($task)) {
                    $error[] = $task;
                }
            }
        }

        // agrega todas aquellas tareas que no son menuitems
        //
        foreach ($originTaskList as $task) {
            if (!$this->isTaskMenuItem($task)) {
                $regular[] = $task;
            }
        }

        return array(
            'topmenu' => $top,
            'childmenu' => $child,
            'orphan' => $error,
            'regular' => $regular
        );
    }

    /**
     * reorderItemArray
     *    ordernara un array de CAuthItem  de tipo TASK de modo que
     *    el array resultante este organizado asi:
     *
     *        MENU_ITEM_1
     *            SUB_MENU_ITEM {parent: MENU_ITEM_1}
     *            SUB_MENU_ITEM    "
     *            SUB_MENU_ITEM    "
     *        MENU_ITEM_2
     *            SUB_MENU_ITEM {parent: MENU_ITEM_2}
     *            SUB_MENU_ITEM    "
     *            SUB_MENU_ITEM    "
     *        NO_MENU_ITEM_1
     *        NO_MENU_ITEM_2
     *        NO_MENU_ITEM_3
     *        NO_MENU_ITEM_N
     *
     * @param mixed $itemArray array de CAuthItem
     * @access public
     * @return array de CAuthItem
     */
    public function reorderItemArray($itemArray)
    {

        $r = array();

        // busca aquellas operaciones que son tareas
        // y que son menu items, pero que no son sub menu items
        $r1 = array();
        foreach ($itemArray as $item) {
            if ($this->isTaskMenuItem($item)
                && !$this->isTaskSubMenuItem($item)
            ) {
                $r1[] = $item;
            }
        }

        // busca las tareas que son hijas de las primeras
        // halladas. (son hijas dada la sintaxis de descripcion del CAuthItem)
        //
        foreach ($r1 as $menuitem) {
            $r[] = $menuitem;
            foreach ($itemArray as $item) {
                if ($this->isTaskMenuItemChild($item, $menuitem)) {
                    $r[] = $item;
                }
            }
        }

        // agrega los huerfanos
        // aquellas tareas marcadas como menuitems cuyo padre no existe
        //
        foreach ($itemArray as $item) {
            if ($this->isTaskSubMenuItem($item)) {
                if (!$this->getParentMenuAuthItem($item)) {
                    $r[] = $item;
                }
            }
        }

        // agrega todas aquellas tareas que no son menuitems
        //
        foreach ($itemArray as $item) {
            if (!$this->isTaskMenuItem($item)) {
                $r[] = $item;
            }
        }

        return $r;
    }


    /**
     * getMenu
     *  devuelve un array indexado listo para usar en CMenu
     *      incluso con una entrada extra para subitems: 'items'
     *      para menues extendidos.
     *
     *    el array es obtenido usando la sintaxis de la descripcion.
     *
     * @access public
     * @param $arguments adjunta argumentos a la url, ej: array('abc'=>'123')
     * @return array indexado ('label','url' [,'items'=>array(...)])
     */
    public function getMenu($userid = -1, $arguments = array())
    {

        if ($userid == -1) {
            $userid = Yii::app()->user->id;
        }

        $r = array();

        // todas las TAREAS a las que puede acceder este usuario

        // este metodo no sirve porque solo lista elementos directamente
        // relacionados al userid y no lista aquellos derivados,
        //$itemArray = $this->getAuthItems(CAuthItem::TYPE_TASK,$userid);

        $tasklist = $this->tasks;

        // por tanto a lo anterior: listo todas las tareas de tipo menuitem
        // y pregunto si el usuario tiene acceso a ellas:
        $itemArray = array();
        foreach ($tasklist as $task) {
            if ($this->isTaskMenuItem($task) && !$this->isTaskSubMenuItem($task)) {
                if ($this->checkAccess($task->getName(), $userid)) {
                    $itemArray[] = $task;
                }
            }
        }

        // todas las tareas consideradas subitems, no importa
        // si estan asignadas al usuario
        //
        $allsubitems = array();
        foreach ($tasklist as $task) {
            if ($this->isTaskSubMenuItem($task)) {
                $allsubitems[] = $task;
            }
        }

        // Menues de Primer Nivel
        //
        // busca aquellas operaciones que son tareas
        // y que son menu items, pero que no son sub menu items
        $r1 = array();
        foreach ($itemArray as $item) {
            if ($this->isTaskMenuItem($item) && !$this->isTaskSubMenuItem($item)) {
                $r1[] = $item;
            }
        }

        // busca las tareas que son hijas de las primeras
        // halladas. (son hijas dada la sintaxis de descripcion del CAuthItem)
        //
        foreach ($r1 as $menuitem) {
            // child menu items
            $items = array();
            // agrega al menuitem de 1er nivel todas los subitems (tasks)
            // sin importar si fueron otorgadas al usuario con checkAccess
            //
            foreach ($allsubitems as $task) {
                if ($this->isTaskMenuItemChild($task, $menuitem)) {
                    $items[] = array(
                        'label' => $this->getTaskText($task),
                        'url' => $this->getTaskUrl($task, $arguments),
                    );
                }
            }
            // top level menu
            if (!sizeof($items)) {
                $items = null;
            }
            $r[] = array(
                'label' => $this->getTaskText($menuitem),
                'url' => '',
                'items' => $items,
            );
        }
        return $r;
    }


    /**
     * enumControllers
     *    lista los nombres de los controllers declarados.
     * @access public
     * @return array con nombre del controller
     */
    public function enumControllers()
    {
        if ($this->_enumcontrollers == null) {
            $this->_enumcontrollers = array();
            $p = Yii::app()->getControllerPath();
            foreach (scandir($p) as $f) {
                if ($f == '.' || $f == '..') {
                    continue;
                }
                if (strlen($f)) {
                    if ($f[0] == '.') {
                        continue;
                    }
                }
                if ($pos = strpos(strtolower($f), "controller.php")) {
                    $this->_enumcontrollers[] = substr($f, 0, $pos);
                }
            }
            return $this->_enumcontrollers;
        } else {
            return $this->_enumcontrollers;
        }
    }

    /**
     * enumActions
     *    devuelve un array con los nombres de los actions del controller
     * @param mixed $controllerName nombre del controller
     * @access public
     * @return array lista de actions.
     */
    public function enumActions($controllerName)
    {
        $this->_enumactions = array();
        $className = $controllerName . 'Controller';
        Yii::import('application.controllers.' . $className, true);
        $refx = new ReflectionClass($className);
        foreach ($refx->getMethods() as $method) {
            if ($method->name != 'actions') {
                if (substr($method->name, 0, 6) == "action") {
                    $this->_enumactions[] = substr($method->name, 6);
                }
            }
        }
        return $this->_enumactions;
    }

    /**
     * autoDetect
     *    lee todos los controllers y actions y los almacena si previamente
     *    no estaban registrados.
     * @access public
     * @return void
     */
    public function autoDetect()
    {

        // agrega cada actiond e cada controller detectado en codigo fuente
        //
        foreach ($this->enumControllers() as $c) {
            // cada controller
            $itemName = "controller_" . strtolower($c);
            if (!$this->getAuthItem($itemName)) {
                $this->createAuthItem(
                    $itemName,
                    CAuthItem::TYPE_OPERATION,
                    ""
                );
            }
            // cada action
            foreach ($this->enumActions($c) as $action) {
                $itemName = "action_" . strtolower($c) . "_" . strtolower($action);
                if (!$this->getAuthItem($itemName)) {
                    $this->createAuthItem(
                        $itemName,
                        CAuthItem::TYPE_OPERATION,
                        ""
                    );
                }

            }
        }

        $this->ensureMenuItemIntegrity();
    }


    /**
     * ensureMenuItemIntegrity
     *    se asegura que todas aquellas tareas que usan sintaxis de descripcion
     *    y que sean subitems de uno superior (debido a la sintaxis)
     *    se asegura que cada subitem este asignado como un child auth item a
     *  la tarea superior.
     *
     * @access public
     * @return void
     */
    public function ensureMenuItemIntegrity()
    {
        $data = $this->explodeTaskArray($this->getTasks());
        $submenues = $data['childmenu'];
        foreach ($submenues as $parentItemName => $tasks) {
            // determina si esta tarea esta asignada a su padre
            foreach ($tasks as $task) {
                if (!$this->hasItemChild($parentItemName, $task->name)) {
                    $this->addItemChild($parentItemName, $task->name);
                }
            }
        }
    }


}// finclase
