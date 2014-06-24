<?php
/**
UiController

Controladora comun para todas las vistas predefinidas en views/ui

dependencias:

La controladora no accede de ninguna manera a la capa de CrugeFactory,
todo lo hace mediante los componentes:

Yii::app()->user->um
Yii::app()->user->rbac
Yii::app()->user->ui

igualmente, no hay instancias directas a modelos de datos, esto es para ayudar
a la insercion de diferentes ORDBM.

@author: Christian Salazar H. <christiansalazarh@gmail.com> @salazarchris74
@license protected/modules/cruge/LICENSE
 */
class UiController extends Controller
{
    public $basePath; // usada por CrugeUi::getResource()

    public function init()
    {
        $this->registerScripts();
        $this->layout = CrugeUtil::config()->generalUserManagementLayout;
    }

    public function registerScripts()
    {
        $this->basePath = Yii::app()->getAssetManager()->publish(
            dirname(__FILE__) . "/../resources"
        ) . "/";

        $cs = Yii::app()->getClientScript();

        $cs->registerCoreScript('jquery');

        $cs->registerCssFile($this->basePath . "estilos.css");
    }

    private function _publicActionsList()
    {
        return array(
            'captcha',
            'registration',
            'login',
            'logout',
            'pwdrec'
        ,
            'activationurl',
            'ajaxgeneratenewpassword',
            'welcome'
        );
    }

    public function filters()
    {
        return array_merge(
            array(
                // con accessControl se garantiza que un usuario NO autenticado NO tenga acceso
                // a las funciones NO públicas.
                'accessControl',
                // con CrugeUiAccessControlFilter se garantiza que a los actions no publicos
                // solo accedan aquellos usuarios que tengan la operacion asignada
                // directamente o mediante una tarea o un rol.
                //
                // al usar este filtro Y si la configuracion del modulo indica que:
                //
                //	SI rbacSetupEnabled es TRUE entonces:
                //
                //		1. 	el filtro "creará" si es necesario la operacion en base
                //			al controller/action
                //
                //		2.	el filtro concede paso si la operacion controller/action esta
                //		  	asignada al usuario.
                //
                //	SI rbacSetupEnabled es FALSE entonces:
                //
                //		1.	el filtro concede paso si la operacion controller/action esta
                //		  	asignada al usuario.
                //
                array('CrugeUiAccessControlFilter', 'publicActions' => self::_publicActionsList()),

            )
      //     ,parent::filters()		// esta linea causa problemas en una instalacion estandar de Cruge
        );
    }

    public function accessRules()
    {
        return array(
            array(
                'allow',
                'actions' => self::_publicActionsList(),
                'users' => array('*'),
            ),
            array(
                'allow',
                'users' => array('@'),
            ),
            array(
                'deny', // deny all users
                'users' => array('*'),
            ),
        );
    }

    public function actions()
    {
        return array(
            // captcha action renders the CAPTCHA image displayed on the contact page
            'captcha' => array(
                'class' => 'CCaptchaAction',
                'backColor' => 0xFFFFFF,
            ),
        );
    }

    public function actionLogin()
    {

        $this->layout = CrugeUtil::config()->loginLayout;

        $model = Yii::app()->user->um->getNewCrugeLogon('login');

        // por ahora solo un metodo de autenticacion por vez es usado, aunque sea un array en config/main
        //
        $model->authMode = CrugeFactory::get()->getConfiguredAuthMethodName();

        Yii::app()->user->setFlash('loginflash', null);

        Yii::log(__CLASS__ . "\nactionLogin\n", "info");

        if (isset($_POST[CrugeUtil::config()->postNameMappings['CrugeLogon']])) {
            $model->attributes = $_POST[CrugeUtil::config()->postNameMappings['CrugeLogon']];
            if ($model->validate()) {
                if ($model->login(false) == true) {

                    Yii::log(__CLASS__ . "\nCrugeLogon->login() returns true\n", "info");

                    // a modo de conocimiento, Yii::app()->user->returnUrl es
                    // establecida automaticamente por CAccessControlFilter cuando
                    // preFilter llama a accessDenied quien a su vez llama a
                    // CWebUser::loginRequired que es donde finalmente se llama a setReturnUrl
                    $this->redirect(Yii::app()->user->returnUrl);
                } else {
                    Yii::app()->user->setFlash('loginflash', Yii::app()->user->getLastError());
                }
            } else {
                Yii::log(
                    __CLASS__ . "\nCrugeUser->validate es false\n" . CHtml::errorSummary($model)
                    ,
                    "error"
                );
            }
        }
        $this->render('login', array('model' => $model));
    }

    public function actionPwdRec()
    {

        $this->layout = CrugeUtil::config()->resetPasswordLayout;

        $model = Yii::app()->user->um->getNewCrugeLogon('pwdrec');

        Yii::app()->user->setFlash('pwdrecflash', null);

        if (isset($_POST[CrugeUtil::config()->postNameMappings['CrugeLogon']])) {
            $model->attributes = $_POST[CrugeUtil::config()->postNameMappings['CrugeLogon']];
            if ($model->validate()) {
                $newPwd = CrugeUtil::passwordGenerator();
                Yii::app()->user->um->changePassword($model->getModel(), $newPwd);
                Yii::app()->crugemailer->sendPasswordTo($model->getModel(), $newPwd);
                Yii::app()->user->um->save($model->getModel());

                Yii::app()->user->setFlash(
                    'pwdrecflash'
                    ,
                    CrugeTranslator::t('Una nueva clave ha sido enviada a su correo')
                );
            }
        }
        $this->render('pwdrec', array('model' => $model));
    }

    public function actionLogout()
    {
		// retorna false si ocurrio un error O si el filtro de sesion
		// dispone de onBeforeLogin el cual ha retornado false.
        if(Yii::app()->user->logout() == false){
			// se devuelve a la URL de donde vino
	        $this->redirect(Yii::app()->user->returnUrl);
			return;
		}else{
			
        	$this->redirect(Yii::app()->user->ui->loginurl);
		}
    }

    public function actionUserManagementAdmin()
    {
        $model = Yii::app()->user->um->getSearchModelICrugeStoredUser();
        $model->unsetAttributes();
        if (isset($_GET[CrugeUtil::config()->postNameMappings['CrugeStoredUser']])) {
            $model->attributes = $_GET[CrugeUtil::config()->postNameMappings['CrugeStoredUser']];
        }
        $dataProvider = $model->search();
        $this->render("usermanagementadmin", array('model' => $model, 'dataProvider' => $dataProvider));
    }

    public function actionEditProfile()
    {

        $this->layout = CrugeUtil::config()->editProfileLayout;

        if (!Yii::app()->user->isGuest) {
            $this->_editUserProfile(Yii::app()->user->user, false);
        } else {
            throw new CrugeException("necesita iniciar sesion para editar su perfil");
        }
    }

    public function actionUserManagementUpdate($id)
    {
        $this->_editUserProfile(Yii::app()->user->um->loadUserById($id), true);
    }

    public function _editUserProfile(ICrugeStoredUser $model, $boolIsUserManagement)
    {
        // carga los campos definidos por el administrador
        // trayendo consigo el atributo "value" accesible mediante $xx->fieldvalue
        Yii::app()->user->um->loadUserFields($model);
        $this->performAjaxValidation('crugestoreduser-form', $model);
        if (isset($_POST[CrugeUtil::config()->postNameMappings['CrugeStoredUser']])) {
            $model->attributes = $_POST[CrugeUtil::config()->postNameMappings['CrugeStoredUser']];
            if ($model->validate()) {
                // el modelo ICrugeStoredUser ha validado bien, incluso cada uno de sus campos extra

                /*
                    si se ha especificado algun valor en $model->newPassword se asume
                    que se quiere cambiar la clave:
                */
                $newPwd = trim($model->newPassword);
                Yii::log("deteccion de nueva clave: newPassword: [" . $newPwd . "]", "info");
                if ($newPwd != '') {
                    Yii::log("\n\n***NUEVA CLAVE***\n\n", "info");
                    Yii::app()->user->um->changePassword($model, $newPwd);
                    Yii::app()->crugemailer->sendPasswordTo($model, $newPwd);
                }

                if (Yii::app()->user->um->save($model, 'update')) {
                    if ($boolIsUserManagement == true) {
                        $this->redirect(array('usermanagementadmin'));
                    } else {
                        $this->redirect(array('usersaved', 'layout' => $this->layout));
                    }
                }
            }
        }
        $this->render(
            "usermanagementupdate",
            array(
                'model' => $model
            ,
                'boolIsUserManagement' => $boolIsUserManagement
            )
        );
    }

    /*
        solo se crea el ICrugeStoredUser, no todo el perfil.
    */
    public function actionUserManagementCreate()
    {
        $model = Yii::app()->user->um->createBlankUser();

        if (isset($_POST[CrugeUtil::config()->postNameMappings['CrugeStoredUser']])) {
            $model->attributes = $_POST[CrugeUtil::config()->postNameMappings['CrugeStoredUser']];

            $model->terminosYCondiciones = true;

            $model->scenario = 'manualcreate';

            if ($model->validate()) {

                $newPwd = trim($model->newPassword);
                Yii::app()->user->um->changePassword($model, $newPwd);

                Yii::app()->user->um->generateAuthenticationKey($model);

                if (Yii::app()->user->um->save($model, 'insert')) {

                    $this->onNewUser($model, $newPwd);

                    $this->redirect(array('usermanagementadmin'));
                }
            }
        }
        $this->render("usermanagementcreate", array('model' => $model));
    }

    public function actionRegistration($datakey = '')
    {

        $this->layout = CrugeUtil::config()->registrationLayout;


        $model = Yii::app()->user->um->createBlankUser();
		$model->bypassCaptcha = false;
        $model->terminosYCondiciones = false;
        if (Yii::app()->user->um->getDefaultSystem()->getn('registerusingterms') == 0) {
            $model->terminosYCondiciones = true;
        }

        // para que cargue los campos del usuario
        Yii::app()->user->um->loadUserFields($model);

        // 'datakey' es el nombre de una variable de sesion
        // establecida por alguna parte que invoque a actionRegistration
        // y que se le pasa a este action para de ahi se lean datos.
        //
        // el dato esperado alli es un array indexado ('attribuye'=>'value')
        // tales valores deberan usarse para inicializar el formulario
        // del usuario como se indica aqui:
        //
        // ejemplo de array en sesion:
        //	array('username'=>'csalazar','email'=>'micorreo@x.com'
        //	,'nombre'=>'christian', 'apellido'=>'salazar')
        //
        // siendo: "nombre" y "apellido" los nombre de campos personalizados
        //	que inmediantamente tras registro seran inicializados.
        //
        if ($datakey != null) {
            // leo la data de la varibale de sesion
            $s = new CHttpSession();
            $s->open();
            $values = $s[$datakey];
            $s->close();
            // asumo que es un array, asi que aqui vamos
            //
            $model->username = $values['username'];
            $model->email = $values['email'];
            // ahora, procesa los campos personalizados,
            // rellenando aquellos mapeados contra los campos existentes:
            foreach ($model->getFields() as $f) {
                if (isset($values[$f->fieldname])) {
                    $f->setFieldValue($values[$f->fieldname]);
                }
            }
        }

        if (isset($_POST[CrugeUtil::config()->postNameMappings['CrugeStoredUser']])) {
            $model->attributes = $_POST[CrugeUtil::config()->postNameMappings['CrugeStoredUser']];
            if ($model->validate()) {

                $newPwd = trim($model->newPassword);
                Yii::app()->user->um->changePassword($model, $newPwd);

                Yii::app()->user->um->generateAuthenticationKey($model);

                if (Yii::app()->user->um->save($model, 'insert')) {

                    $this->onNewUser($model, $newPwd);

                    $this->redirect(array('welcome'));
                }
            }
        }
        $this->render("registration", array('model' => $model));
    }

    /* este es un evento emitido por actionRegistration y actionUserManagementCreate
        el cual informa que un nuevo usuario ha sido creado.

        segun la configuracion general del sistema este usuario
        sera activado de inmediato, o por email, o manualmente.
    */
    private function onNewUser(ICrugeStoredUser $model, $newPwd = "")
    {
        Yii::log(__METHOD__ . "\n", "info");

        $opt = Yii::app()->user->um->getDefaultSystem()->getn("registerusingactivation");

        $role = Yii::app()->user->um->getDefaultSystem()->get("defaultroleforregistration");
        Yii::log(__METHOD__ . "\n role: " . $role, "info");
        if (Yii::app()->user->rbac->getAuthItem($role) != null) {
            Yii::log(
                __METHOD__ . "\n asignando role: " . $role . " a userid:"
                    . $model->getPrimaryKey(),
                "info"
            );
            Yii::app()->user->rbac->assign($role, $model->getPrimaryKey());
        }

        if ($opt == CRUGE_ACTIVATION_OPTION_INMEDIATE) {
            // lo activa inmediatamente y le manda la clave al usuario
            $model->state = CRUGEUSERSTATE_ACTIVATED;
            Yii::app()->user->um->save($model);
            Yii::app()->crugemailer->sendPasswordTo($model, $newPwd);
        }
        if ($opt == CRUGE_ACTIVATION_OPTION_EMAIL) {
            // queda en estado no activado, pero envia un email para que
            // el usuario lo active
            Yii::app()->crugemailer->sendRegistrationEmail($model, $newPwd);
        }
        if ($opt == CRUGE_ACTIVATION_OPTION_MANUAL) {
            // lo activa manualmente, envia un email de espera por activacion manual
            Yii::app()->crugemailer->sendWaitForActivation($model, $newPwd);
        }
    }


    public function actionWelcome()
    {
        $this->layout = CrugeUtil::config()->registrationLayout;
        $this->render("welcome");
    }

    public function actionUserSaved($layout = null)
    {
        if ($layout != null) {
            $this->layout = $layout;
        }
        $this->render("usersaved");
    }


    public function actionUserManagementDelete($id)
    {
        $model = Yii::app()->user->um->loadUserById($id);
        $model->scenario = 'delete';
        $model->deleteConfirmation = 0;

        if (isset($_POST[CrugeUtil::config()->postNameMappings['CrugeStoredUser']])) {
            if (isset($_POST['cancelar'])) {
                $this->redirect(array('usermanagementadmin'));
            }

            $model->attributes = $_POST[CrugeUtil::config()->postNameMappings['CrugeStoredUser']];
            if ($model->validate()) {
                if ($model->deleteConfirmation == 1) {
                    if ($model->delete()) {
                        $this->redirect(array('usermanagementadmin'));
                    }
                }
            } else {
                //error, no ha confirmado con la casilla
            }
        }

        $this->render("usermanagementdelete", array('model' => $model));
    }


    public function actionFieldsAdminList()
    {
        $model = Yii::app()->user->um->getSearchModelICrugeField();
        $model->unsetAttributes();
        if (isset($_GET[CrugeUtil::config()->postNameMappings['CrugeField']])) {
            $model->attributes = $_GET[CrugeUtil::config()->postNameMappings['CrugeField']];
        }
        $dataProvider = $model->search();
        $this->render("fieldsadminlist", array('model' => $model, 'dataProvider' => $dataProvider));
    }

    public function actionFieldsAdminUpdate($id)
    {
        $model = Yii::app()->user->um->loadFieldById($id);
		if($model != null){
			$this->_fieldAdminForm($model);
		}else
			throw new CrugeException("Identificador de campo es invalido");
    }

    public function actionFieldsAdminCreate()
    {
        $model = Yii::app()->user->um->createEmptyField();
		$this->_fieldAdminForm($model);
    }
	
	private function _fieldAdminForm($model){
        if (isset($_POST[CrugeUtil::config()->postNameMappings['CrugeField']])) {
            $model->attributes = $_POST[CrugeUtil::config()->postNameMappings['CrugeField']];
            if ($model->save()) {
                $this->redirect(array('fieldsadminlist'));
            }
        }
        $this->render("fieldsadminupdate", array('model' => $model));
	}

    public function actionFieldsAdminDelete($id)
    {
        $model = Yii::app()->user->um->loadFieldById($id);
        if ($model != null) {
            if (Yii::app()->request->isAjaxRequest) {
                $model->delete();
            }
        }
    }


    public function actionRbacListRoles()
    {
        Yii::app()->user->rbac->autoDetect();
        $dataProvider = Yii::app()->user->rbac->getDataProviderRoles();
        $this->render('rbaclistroles', array('dataProvider' => $dataProvider));
    }

    public function actionRbacListTasks()
    {
        Yii::app()->user->rbac->autoDetect();
        $dataProvider = Yii::app()->user->rbac->getDataProviderTasks();
        $this->render('rbaclisttasks', array('dataProvider' => $dataProvider));
    }

    public function actionRbacListOps($filter = '0')
    {
        Yii::app()->user->rbac->autoDetect();
        $dataProvider = Yii::app()->user->rbac->getDataProviderOperations(
            $filter
        );
        $this->render('rbaclistops', array('dataProvider' => $dataProvider));
    }


    /**
     * actionAjaxRbacItemDescr
     *    cambia la descripcion de un $itemname.
     *
     *    Cruge incorpora una sintaxis para la descripcion de los CAuthItem.
     *        esa descripcion incorpora entre otras cosas el "$action"
     *        que se usara para el $itemname que opera en modo de SubMenuItem.
     *
     *    Este action es invocado via ajax desde:
     *        view/ui/_listauthitems.php
     *
     *    Para saber mas consulta acerca del funcionamiento de la sintaxis
     *    de la descripcion de un CAuthItem.  Info en CrugeAuthItemManager.php
     * PARAMS VIA POST:
     * @param string $action  el nombre action del cual se adosara a la descr.
     * @param string $itemname el item cuya descripcion se alteara
     * @access public
     * @return void
     */
    public function actionAjaxRbacItemDescr()
    {
		$action = $_POST['action']; 
		$itemname = $_POST['itemname'];
        $item = Yii::app()->user->rbac->getAuthItem($itemname);
        Yii::app()->user->rbac->setTaskAction($item, $action);
        header("Content-type: application/json");
        echo CJSON::encode(array('description' => $item->getDescription()));
    }

    // aqui type es uno de los valores de
    // CAuthItem::TYPE_ROLE,CAuthItem::TYPE_TASK,CAuthItem::TYPE_OPERATION
    // parametro llamado 'extra' es usado y enviado por CrugeAuthManager
    // para indicar la creacion de una tarea enlazada a otra
    public function actionRbacAuthItemCreate($type)
    {

        $editor = new CrugeAuthItemEditor('insert');
        $editor->name = "";
        $editor->description = "";
        $editor->businessRule = "";
        $editor->categoria = Yii::app()->user->rbac->getAuthItemTypeName($type);
        $editor->isNewRecord = true;

        if (($type == CAuthItem::TYPE_TASK) && isset($_GET['extra'])) {
            if (strlen($_GET['extra']) > 0) {
                // enlaza esta tarea con otra superior.
                // aqui se esta usando el mecanism de sintaxis de descripcion
                // del authitem definido en la documentacion de CrugeAuthManager
                $editor->description = ":Item Label{" . $_GET['extra'] . "}";
            }
        }

        if (isset($_POST['CrugeAuthItemEditor'])) {
            if (isset($_POST['volver'])) {
                if ($type == CAuthItem::TYPE_ROLE) {
                    $this->redirect(array('rbaclistroles'));
                }
                if ($type == CAuthItem::TYPE_TASK) {
                    $this->redirect(array('rbaclisttasks'));
                }
                if ($type == CAuthItem::TYPE_OPERATION) {
                    $this->redirect(array('rbaclistops'));
                }
            }

            $editor->attributes = $_POST['CrugeAuthItemEditor'];
            if ($editor->validate()) {


                $newAi = Yii::app()->user->rbac->createAuthItem(
                    $editor->name,
                    $type,
                    $editor->description,
                    $editor->businessRule
                );


                // se va de vuelta a la lista de donde vino
                if ($type == CAuthItem::TYPE_ROLE) {
                    $this->redirect(array('rbaclistroles'));
                }
                if ($type == CAuthItem::TYPE_TASK) {
                    $this->redirect(array('rbaclisttasks'));
                }
                if ($type == CAuthItem::TYPE_OPERATION) {
                    $this->redirect(array('rbaclistops'));
                }
            }
        }
        $this->render('rbacauthitemcreate', array('model' => $editor));
    }


    public function actionRbacAuthItemUpdate($id)
    {

        $aiModel = Yii::app()->user->rbac->getAuthItem($id);
        if ($aiModel == null) {
            throw new CrugeException("el item de autenticacion senalado no existe");
        }

        $editor = new CrugeAuthItemEditor('update');
        $editor->isNewRecord = false;
        $editor->name = $aiModel->name;
        $editor->description = $aiModel->description;
        $editor->businessRule = $aiModel->bizRule;
        $editor->categoria = Yii::app()->user->rbac->getAuthItemTypeName($aiModel->type);

        if (isset($_POST['CrugeAuthItemEditor'])) {
            if (isset($_POST['volver'])) {
                if ($aiModel->type == CAuthItem::TYPE_ROLE) {
                    $this->redirect(array('rbaclistroles'));
                }
                if ($aiModel->type == CAuthItem::TYPE_TASK) {
                    $this->redirect(array('rbaclisttasks'));
                }
                if ($aiModel->type == CAuthItem::TYPE_OPERATION) {
                    $this->redirect(array('rbaclistops'));
                }
            }

            $editor->attributes = $_POST['CrugeAuthItemEditor'];
            if ($editor->validate()) {
                // la guarda de regreso al aiModel
                $oldName = $aiModel->name;
                $aiModel->name = $editor->name;
                $aiModel->description = $editor->description;
                $aiModel->bizRule = $editor->businessRule;
                Yii::app()->user->rbac->saveAuthItem($aiModel, $oldName);

                // se va de vuelta a la lista de donde vino
                if ($aiModel->type == CAuthItem::TYPE_ROLE) {
                    $this->redirect(array('rbaclistroles'));
                }
                if ($aiModel->type == CAuthItem::TYPE_TASK) {
                    $this->redirect(array('rbaclisttasks'));
                }
                if ($aiModel->type == CAuthItem::TYPE_OPERATION) {
                    $this->redirect(array('rbaclistops'));
                }
            }
        }

        $this->render('rbacauthitemupdate', array('model' => $editor));
    }

    public function actionRbacAuthItemDelete($id)
    {
        $aiModel = Yii::app()->user->rbac->getAuthItem($id);
        if ($aiModel == null) {
            throw new CrugeException("el item de autenticacion senalado no existe");
        }

        $editor = new CrugeAuthItemEditor('delete');
        $editor->deleteConfirmation = false;
        $editor->isNewRecord = false;
        $editor->name = $aiModel->name;
        $editor->description = $aiModel->description;
        $editor->businessRule = $aiModel->bizRule;
        $editor->categoria = Yii::app()->user->rbac->getAuthItemTypeName($aiModel->type);

        if (isset($_POST['CrugeAuthItemEditor'])) {
            if (isset($_POST['volver'])) {
                // nada
            } else {
                $editor->attributes = $_POST['CrugeAuthItemEditor'];
                if ($editor->validate()) {
                    // elimina el CAuthItem
                    if ($editor->deleteConfirmation == 1) {
                        Yii::app()->user->rbac->removeAuthItem($id);
                    }
                } else {
                    $this->render('rbacauthitemdelete', array('model' => $editor));
                    return;
                }
            }
            if ($aiModel->type == CAuthItem::TYPE_ROLE) {
                $this->redirect(array('rbaclistroles'));
            }
            if ($aiModel->type == CAuthItem::TYPE_TASK) {
                $this->redirect(array('rbaclisttasks'));
            }
            if ($aiModel->type == CAuthItem::TYPE_OPERATION) {
                $this->redirect(array('rbaclistops'));
            }
        }

        $this->render('rbacauthitemdelete', array('model' => $editor));
    }

    public function actionRbacAuthItemChildItems($id)
    {
        Yii::app()->user->rbac->autoDetect();
        $aiModel = Yii::app()->user->rbac->getAuthItem($id);
        if ($aiModel == null) {
            throw new CrugeException("el item de autenticacion senalado no existe");
        }
        $this->render('rbacauthitemchilditems', array('model' => $aiModel));
    }

    /**
    este action debe ser invocado bajo request POST, y su postdata debe contener
    un objeto JSON de esta forma:

    ejemplo:

    { parent: 'updateOwnPost' , child: 'updatePost' , setflag: true }

    esto indica que al 'parent' se le agregara o removera (setflag), el item 'child'

    cualquier error debe ser reportado bajo una excepcion.
     */
    public function actionRbacAjaxSetChildItem()
    {
        if (Yii::app()->request->isAjaxRequest) {
            if (Yii::app()->request->isPostRequest) {

                $rbac = Yii::app()->user->rbac;

                $jsondata = trim(file_get_contents('php://input'));

                Yii::log(__CLASS__ . "\nactionRbacAjaxSetChildItem\njsondata:\n" . $jsondata, "info");

                $obj = CJSON::decode($jsondata);
                $parent = $obj['parent'];
                $child = $obj['child'];
                $setflag = ($obj['setflag'] == 1 ? "true" : "false");

                Yii::log(
                    __CLASS__ . "\nactionRbacAjaxSetChildItem\natributos leidos:\n"
                        . "parent=" . $parent . "\n"
                        . "child=" . $child . "\n"
                        . "setflag='" . $setflag . "'\n"
                    ,
                    "info"
                );

                // parent y child deben existir
                $_parent = $rbac->getAuthItem($parent);
                $_child = $rbac->getAuthItem($child);
                if ($_parent == null) {
                    throw new CrugeException("parent: no existe.[" . $parent . "]");
                }
                if ($_child == null) {
                    throw new CrugeException("child: no existe.[" . $child . "]");
                }

                $accion = '...';
                if ($setflag == "true") {
                    $accion = '[try add..]';
                    if (!$rbac->hasItemChild($parent, $child)) {
                        $rbac->addItemChild($parent, $child);
                        $accion .= 'addItemChild';
                    }
                } else {
                    $accion = '[try remove..]';
                    if ($rbac->hasItemChild($parent, $child)) {
                        $rbac->removeItemChild($parent, $child);
                        $accion .= 'removeItemChild';
                    }
                }

                // OK
                Yii::log(__CLASS__ . "\nactionRbacAjaxSetChildItem\nRESULTADO OK {$accion}\n", "info");

                $result = array();
                $result['result'] = $rbac->hasItemChild($parent, $child);
                $result['parent'] = $parent;
                $result['child'] = $child;
                header("Content-type: application/json");
                echo CJSON::encode($result);
            } else {
                throw new CrugeException("por favor no invoque este action manualmente");
            }
        } else {
            throw new CrugeException("por favor no invoque este action manualmente");
        }
    }


    /* asigna o revoca un authitem a un usuario en particular. puede ser un rol, una tarea o una operacion, pero por razones de orden en la vista que llama a este action solo se habilitan roles para ser agregados.

    la contraparte de este action es:
    actionRbacUsersAssignments()  la cual maneja la vista que asigna/revoca un rol de forma masiva
    a varios usuarios.

        funciona via ajax y post, espera que el cuerpo del post traiga lo siguiente:
        { authitem: 'nombrerol' , userid: 123 , setflag: true }

        lo que hara con ese objeto json es:

        asignar (<setflag> es true=asignar) el <authitem>, al usuario con primaryKey <userid>,
        si no puede emite una excepcion
    */
    public function actionRbacAjaxAssignment()
    {
        if (Yii::app()->request->isAjaxRequest) {
            if (Yii::app()->request->isPostRequest) {
                $rbac = Yii::app()->user->rbac;
                $jsondata = trim(file_get_contents('php://input'));
                Yii::log(__CLASS__ . "\nactionRbacAjaxAssignment\njsondata:\n" . $jsondata, "info");

                $obj = CJSON::decode($jsondata);
                $authitemName = $obj['authitem'];
                $userId = $obj['userid'];
                $setflag = ($obj['setflag'] == 1 ? "true" : "false");

                Yii::log(
                    __CLASS__ . "\nactionRbacAjaxAssignment\natributos leidos:\n"
                        . "authitemName=" . $authitemName . "\n"
                        . "userId=" . $userId . "\n"
                        . "setflag='" . $setflag . "'\n"
                    ,
                    "info"
                );

                // comprueba que el authitem exista
                $_ai = $rbac->getAuthItem($authitemName);
                if ($_ai == null) {
                    throw new CrugeException("authitem: no existe.[" . $authitemName . "]");
                }

                // verifica al usuario
                $user = Yii::app()->user->um->loadUserById($userId);
                if ($user == null) {
                    throw new CrugeException("userId: no existe.[" . $userId . "]");
                }

                Yii::log(__CLASS__ . "\nactionRbacAjaxAssignment\nprocede a asignar o a revocar\n", "info");

                $accion = "";

                if ($setflag == 'true') {
                    // quiere asignar
                    if ($rbac->isAssigned($authitemName, $userId)) {
                        $accion .= "[ya estaba previamente asignado]";
                    } else {
                        if ($rbac->assign($authitemName, $userId)) {
                            $accion .= "[asignado exitosamente]";
                        } else {
                            Yii::log(
                                __CLASS__ . "\nactionRbacAjaxAssignment\n"
                                    . $accion . "\n[no se pudo asignar]\n",
                                "error"
                            );
                            throw new CrugeException("no se pudo asignar");
                        }
                    }
                } else {
                    // quiere revocar
                    if (!$rbac->isAssigned($authitemName, $userId)) {
                        $accion .= "[no estaba previamente asignado]";
                    } else {
                        if ($rbac->revoke($authitemName, $userId)) {
                            $accion .= "[revocado exitosamente]";
                        } else {
                            Yii::log(
                                __CLASS__ . "\nactionRbacAjaxAssignment\n"
                                    . $accion . "\n[no se pudo revocar]\n",
                                "error"
                            );
                            throw new CrugeException("no se pudo revocar");
                        }
                    }
                }

                Yii::log(
                    __CLASS__ . "\nactionRbacAjaxAssignment\n"
                        . $accion . "\n[operacion finalizada]\n",
                    "info"
                );

                $result = array();
                $result['result'] = $rbac->isAssigned($authitemName, $userId);
                header("Content-type: application/json");
                echo CJSON::encode($result);
            } else {
                throw new CrugeException("por favor no invoque este action manualmente");
            }
        } else {
            throw new CrugeException("por favor no invoque este action manualmente");
        }
    }


    public function actionRbacAjaxGetAssignmentBizRule()
    {
        if (Yii::app()->request->isAjaxRequest) {
            if (Yii::app()->request->isPostRequest) {
                $rbac = Yii::app()->user->rbac;
                $jsondata = trim(file_get_contents('php://input'));

                $obj = CJSON::decode($jsondata);
                $authitemName = $obj['authitem'];
                $userId = $obj['userid'];

                // comprueba que el authitem exista
                $_ai = $rbac->getAuthItem($authitemName);
                if ($_ai == null) {
                    throw new CrugeException("authitem: no existe.[" . $authitemName . "]");
                }

                // verifica al usuario
                $user = Yii::app()->user->um->loadUserById($userId);
                if ($user == null) {
                    throw new CrugeException("userId: no existe.[" . $userId . "]");
                }

                $aa = $rbac->getAuthAssignment($authitemName, $userId);
                if ($aa == null) {
                    throw new CrugeException("asignacion no hallada");
                }

                $result = array();
                $result['bz'] = $aa->bizRule;
                $result['obj'] = $aa;
                header("Content-type: application/json");
                echo CJSON::encode($result);
            } else {
                throw new CrugeException("por favor no invoque este action manualmente");
            }
        } else {
            throw new CrugeException("por favor no invoque este action manualmente");
        }
    }

    public function actionRbacAjaxSetAssignmentBizRule()
    {
        if (Yii::app()->request->isAjaxRequest) {
            if (Yii::app()->request->isPostRequest) {
                $rbac = Yii::app()->user->rbac;
                $jsondata = trim(file_get_contents('php://input'));

                $obj = CJSON::decode($jsondata);
                $authitemName = $obj['authitem'];
                $userId = $obj['userid'];
                $nuevoBz = $obj['bz']; // el business rule modificado

                // comprueba que el authitem exista
                $_ai = $rbac->getAuthItem($authitemName);
                if ($_ai == null) {
                    throw new CrugeException("authitem: no existe.[" . $authitemName . "]");
                }

                // verifica al usuario
                $user = Yii::app()->user->um->loadUserById($userId);
                if ($user == null) {
                    throw new CrugeException("userId: no existe.[" . $userId . "]");
                }

                $aa = $rbac->getAuthAssignment($authitemName, $userId);
                if ($aa == null) {
                    throw new CrugeException("asignacion no hallada");
                }

                $aa->bizRule = $nuevoBz;
                $rbac->saveAuthAssignment($aa);

                $result = array();
                $result['bz'] = $aa->bizRule;
                $result['obj'] = $aa;
                header("Content-type: application/json");
                echo CJSON::encode($result);
            } else {
                throw new CrugeException("por favor no invoque este action manualmente");
            }
        } else {
            throw new CrugeException("por favor no invoque este action manualmente");
        }
    }

    /**
    maneja la asignacion masiva de usuarios a roles.  su contraparte es el action:
    actionRbacAjaxAssignment.

    como funciona este action:

    La vista 'rbacusersassignments.php' tiene dos CGridView, cada uno llenado
    con los DataProviders que aqui se emiten. El primer es la lista de usuarios
    asignados a un rol, el segundo es la lista completa de usuarios.

    por eso en la llamada cruda a la vista se entregan dos data providers,
    ,array(
    'roleUsersDataProvider'=>$roleUsersDataProvider,
    'allUsersDataProvider'=>$allUsersDataProvider,
    )

    una vez en la vista se muestra una -lista de roles- a la cual al hacerle click en un
    rol se actualizara al CGridView que presenta al dataprovider: roleUsersDataProvider,
    mientras que el segundo CGridView permanece inalterado.

    el CGridView que aloja a roleUsersDataProvider sera actualizado cada vez que se haga
    click en un rol, eso generara una llamada con modalidad ajaxRequest a este action,
    y aqui se recibira entonces un argumento extra llamado: itemName

    // invocado cuando se hace click en un ROL (itemName)
    $.fn.yiiGridView.update('_lista1',{ data : "itemName="+itemName });

    cuando un usuario hace click en el link "#asignarSeleccion", lo que se hace
    basicamente es volver a invocar a $.fn.yiiGridView.update('_lista1',{ data : "itemName="+itemName });,  pero con mas argumentos:

    mode	: puede ser 'assign' : 'revoke'
    userid	: lista de userid

    por esta razon se ve que se hace:
    if(Yii::app()->request->isAjaxRequest && isset($_GET['mode']))

    para poder reconocer el ajaxRequest de:
    $.fn.yiiGridView.update('_lista1',{ data : "itemName="+itemName });
    del otro que trae los argumentos extra.

    cuando se detecta que el argumento MODE viene en la URL entonces se sabe
    que se quiere asignar o revocar un permiso (itemName) a una lista de usuarios (userid).

     */
    public function actionRbacUsersAssignments()
    {

        $pageSize = 20;
        $rbac = Yii::app()->user->rbac;
        $um = Yii::app()->user->um;

        $debug = "";
        if (Yii::app()->request->isAjaxRequest && isset($_GET['mode'])) {

            $debug .= "AJAX REQUEST EN CURSO CON INDICACION DE ASIGNAR O REVOCAR\n";
            $mode = $_GET['mode'];
            $itemName = $_GET['itemName'];
            $userid = isset($_GET['userid']) ? $_GET['userid'] : '0';
            $ids = explode(",", $userid);

            $debug .= "-itemName es: {$itemName}\n";
            $debug .= "-modalidad es: {$mode}\n";
            $debug .= "-userid es: {$userid}\n";

            foreach ($ids as $uid) {
                $debug .= "OPERANDO '{$mode}' {$uid} AL AUTHITEM: {$itemName}\n";
                if ($mode == 'assign') {
                    if (!$rbac->isAssigned($itemName, $uid)) {
                        $rbac->assign($itemName, $uid);
                    }
                } else {
                    if ($mode == 'revoke') {
                        if ($rbac->isAssigned($itemName, $uid)) {
                            $rbac->revoke($itemName, $uid);
                        }
                    }
                }
            }
        }


        // entrega los usuarios asignados a este CAuthItem
        //
        $authItemName = "";
        if (isset($_GET['itemName'])) {
            $authItemName = $_GET['itemName'];
        }

        $debug .= "arg: itemName={$authItemName}\n";

        Yii::log(__CLASS__ . "\nactionRbacUsersAssignments\ndebug:\n" . $debug, "info");

		$boolLoadCustomFields = true;

        $roleUsersDataProvider = $um->listUsersDataProviderFromArray(
            $rbac->getUsersAssigned($authItemName),
            $pageSize, $boolLoadCustomFields
        );
        $allUsersDataProvider = $um->listAllUsersDataProvider(
			array(), $pageSize, $boolLoadCustomFields);

        $this->render(
            'rbacusersassignments'
            ,
            array(
                'roleUsersDataProvider' => $roleUsersDataProvider,
                'allUsersDataProvider' => $allUsersDataProvider,
            )
        );
    }

    public function actionSessionAdmin()
    {
        $model = Yii::app()->user->um->getSearchModelICrugeSession();
        $model->unsetAttributes();
        if (isset($_GET[CrugeUtil::config()->postNameMappings['CrugeSession']])) {
            $model->attributes = $_GET[CrugeUtil::config()->postNameMappings['CrugeSession']];
        }
        $dataProvider = $model->search();
        $this->render("sessionadmin", array('model' => $model, 'dataProvider' => $dataProvider));
    }

    public function actionSessionAdminDelete($id)
    {
        if (Yii::app()->request->isAjaxRequest) {
            $model = Yii::app()->user->um->loadSessionById($id);
            if ($model != null) {
                $model->delete();
            }
        }
    }

    public function actionSystemUpdate()
    {
        $model = Yii::app()->user->um->getDefaultSystem();

        Yii::app()->user->setFlash('systemFormFlash', null);

        if (isset($_POST[CrugeUtil::config()->postNameMappings['CrugeSystem']])) {
            $model->attributes = $_POST[CrugeUtil::config()->postNameMappings['CrugeSystem']];
            if ($model->validate()) {
                if ($model->save()) {
                    Yii::app()->user->setFlash(
                        'systemFormFlash'
                        ,
                        CrugeTranslator::t("Los datos del sistema han sido actualizados.")
                    );
                }
            }
        }

        $this->render('systemupdate', array('model' => $model));
    }

    /*
        este action debera tener acceso directo desde la calle para activar una cuenta.

        en el caso de este action, quiza no se quiera presentar el mismo layout que
        los demas actions, en este caso se puede tomar un valor de config del modulo
        para indicar que layout se quiere usar.
    */
    public function actionActivationUrl($key)
    {

        $this->layout = CrugeUtil::config()->activateAccountLayout;

        $model = Yii::app()->user->um->loadUserByKey($key);
        if ($model != null) {
            if ($model->state == CRUGEUSERSTATE_NOTACTIVATED) {

                $resp = CrugeTranslator::t("disculpe, no se pudo activar su cuenta");

                Yii::app()->user->um->activateAccount($model);

                if (Yii::app()->user->um->save($model)) {
                    $resp = CrugeTranslator::t(
                        "su cuenta ha sido activada, ahora debe iniciar sesion con las credenciales otorgadas"
                    );
                }

                $this->renderText($resp);
            }
        }
    }

    public function actionAjaxResendRegistrationEmail($id)
    {
        $newPassword = CrugeUtil::passwordGenerator();
        $model = Yii::app()->user->um->loadUserById($id);
        if ($model != null) {
            Yii::app()->user->um->changePassword($model, $newPassword);
            Yii::app()->user->um->generateAuthenticationKey($model);
            Yii::app()->user->um->save($model);
            Yii::app()->crugemailer->sendRegistrationEmail($model, $newPassword);
            echo CrugeTranslator::t("correo enviado");
        } else {
            echo CrugeTranslator::t("usuario no hallado");
        }
    }

    public function actionAjaxGenerateNewPassword()
    {
        echo CrugeUtil::passwordGenerator();
    }

    protected function performAjaxValidation($formid, $model)
    {
        if (isset($_POST['ajax']) && $_POST['ajax'] === $formid) {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }
}
