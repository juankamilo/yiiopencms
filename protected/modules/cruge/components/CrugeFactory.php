<?php
/**
 * CrugeFactory

centraliza la creacion de instancias para lograr dar abstraccion al modelo OOP.

es importante comprender que CrugeFactory es un punto para lograr la abstraccion
solo factory accede a los modelos especificos, es decir, en ninguna parte
se veran llamadas directas a clases del modelo, en cambio a interfaces si.

dependencias:

1. CrugeUtil
2. package cruge.models
3. package cruge.models

como se accede al factory: (ejemplo)

$value = CrugeFactory::get()->getConfiguredAuthMethodName();

quien accede al factory:

solo ciertas clases como CrugeUserManager o similares...

EL USUARIO DE CRUGE NO DEBE ACCEDER A ESTA CLASE
 * @package
 * @version $id$
 * @author Christian Salazar H. <christiansalazarh@gmail.com> @salazarchris74
 * @license SEE ALSO yourapp/protected/modules/cruge/LICENSE
 */
class CrugeFactory
{

    public static function get()
    {
        return CrugeUtil::factory();
    }


    /** devuelve el identificador activo de sesion pero bajo interpretacion de una clase
    que implemente a ICrugeSession

     */
    public function getICrugeSession($idsession)
    {
        return CrugeSession::loadModel($idsession);
    }

    public function getICrugeSessionFindLastByUser($iduser)
    {
        return CrugeSession::findLast($iduser);
    }

    public function getICrugeSessionCreate($iduser, $durationMins)
    {
        return CrugeSession::create($iduser, $durationMins);
    }

    public function getICrugeSystemByName($systemName)
    {
        $system = CrugeSystem::findSystem($systemName);
        if ($system == null) {
            if (CrugeUtil::config()->debug == true) {
                $sys = new CrugeSystem();
                $sys->name = 'default';
                $sys->sessionmaxdurationmins = 30;
                $sys->registerusingactivation = 0;
                $sys->registerusingcaptcha = 0;
                $sys->registerusingterms = 0;


                if ($sys->insert()) {
                    return $sys;
                } else {
                    throw new CrugeException("no se pudo crear el sistema de configuracion");
                }
            } else {
                /*
                  la causa mas comun de este error es que la tabla cruge_system esta vacia
                */
                throw new CrugeException("no se pudo hallar el sistema de configuracion, quiza la tabla cruge_system esta vacia o ha indicado un identificador de sistema inexistente.");
            }
        } else {
            return $system;
        }
    }

    public function getICrugeSystemList()
    {
        return CrugeSystem::listModels();
    }

    public function getICrugeStoredUserList($param = array())
    {
        return CrugeStoredUser::listModels($param);
    }

    public function getICrugeStoredUserSortFieldNames()
    {
        return CrugeStoredUser::getSortFieldNames();
    }

    public function getNewICrugeStoredUserForSearch()
    {
        return new CrugeStoredUser('search');
    }

    public function getNewICrugeSessionForSearch()
    {
        return new CrugeSession('search');
    }

    public function getNewICrugeFieldForSearch()
    {
        return new CrugeField('search');
    }

    public function getICrugeFieldSortFieldNames()
    {
        return CrugeField::getSortFieldNames();
    }


    /*
        buscador multiproposito.  $id puede ser el iduser o el authkey, depende del flag boolean
        de seleccion: $boolFindByKey
    */
    public function getICrugeStoredUserLoadModel(
        $id,
        $booleanThrowsExceptionIfNull = true
        ,
        $boolFindByKey = false
    ) {
        if ($boolFindByKey == false) {
            $model = CrugeStoredUser::loadModel($id);
        } else {
            $model = CrugeStoredUser::loadModel($id, 'authkey');
        }
        if ($booleanThrowsExceptionIfNull == true && $model == null) {
            throw new CrugeException("usuario no encontrado");
        }
        return $model;
    }

    public function getICrugeStoredUserNewModel()
    {
        $model = new CrugeStoredUser();
        $model->regdate = CrugeUtil::now();
        return $model;
    }

    public function getICrugeStoredUserModel($scenario)
    {
        return new CrugeStoredUser($scenario);
    }

    /*  entrega la lista de campos, si se le da el usuario, va a pasar el valor del usuario
        al fieldvalue del campo.

        @returns una lista (array) de instancias de ICrugeField

        si $userInst es una instancia entonces renderiza cada campo con el valor asignado
        al usuario.  si $userInst es null solo se retorna la lista de campos sin renderizar valor
    */
    public function getICrugeFieldListModels( /*ICrugeStoredUser*/
        $userInst = null
    ) {
        $fields = CrugeField::listModels();

        foreach ($fields as $f) {
            $f->setFieldValue($f->predetvalue);
        }

        // ahora, asocia cada campo con el valor del usuario seleccionado
        if ($userInst != null) {

            // busca los campos de este usuario
            $fieldvalues = CrugeFieldValue::listModels($userInst->getPrimaryKey());
            foreach ($fields as $f) {
                foreach ($fieldvalues as $fv) {
                    if ($f->idfield == $fv->idfield) {
                        $f->setFieldValue($fv->value == null ? "" : $fv->value);
                        break;
                    }
                }
            }

        }
        return $fields;
    }


    /**
     * getICrugeFieldValueByValue
     *     busca un objeto de clase FieldValue por su valor y campo.
     *
     * @param mixed $field
     * @param mixed $value
     * @access public
     * @return instancia de FieldValue
     */
    public function getICrugeFieldValueByValue($field, $value)
    {
        return CrugeFieldValue::loadByValue($field->primaryKey, $value);
    }


    /*
        va a retornar un objeto que implementa a ICrugeFieldValue, el cual esta compuesto
        de dos objetos: el usuario y el campo.

        importante: si el campo solicitado no es hallado entonces sera creado para asegurar
        que siempre exista.
    */
    public function getICrugeFieldValue(ICrugeStoredUser $user, ICrugeField $field)
    {
        $model = CrugeFieldValue::loadModelBy($user->getPrimaryKey(), $field->getPrimaryKey());
        if ($model == null) {
            // lo crea
            $model = new CrugeFieldValue();
            $model->iduser = $user->getPrimaryKey();
            $model->idfield = $field->getPrimaryKey();
            $model->value = "";
            if ($model->save()) {
                return $model;
            } else {
                Yii::log(
                    "error creando un nuevo CrugeFieldValue:\n"
                        . "iduser: {$user->getPrimaryKey()}\n"
                        . "idfield: {$field->getPrimaryKey()}\n"
                        . "fieldvalue: {$field->getFieldValue()}\n"
                        . "errorSummary:\n " . CHtml::errorSummary($model) . "\n"
                    ,
                    "error"
                );
                return null;
            }
        }
        return $model;
    }

    public function getICrugeFieldLoadModel($id)
    {
        return CrugeField::loadModel($id);
    }

    public function getICrugeFieldLoadModelByName($name)
    {
        return CrugeField::loadModelByName($name);
    }

    public function getICrugeFieldCreate($fieldtype)
    {
        $model = new CrugeField();
        $model->fieldtype = $fieldtype;
        $model->fieldname = CrugeTranslator::t("nuevocampo");
        $model->longname = CrugeTranslator::t("Nuevo Campo");
        $model->position = 0;
        $model->fieldsize = 20;
        $model->maxlength = 45;
        $model->required = false;
        $model->showinreports = false;
        return $model;
    }


    /*
        se trae un modulo de autenticacion hallado por su nombre (authName)
        retorna una instancia de este, siempre, sino una excepcion

        @returns instancia de ICrugeAuth buscada por su nombre y configurada en el sistema
    */
    public function getICrugeAuthByName($authName)
    {
        // verifica si el metodo esta disponible en config
        if (self::isAuthMethodAvailable($authName)) {
            foreach (self::_getAuthModes() as $mode) {
                if ($mode->authName() == $authName) {
                    return $mode;
                }
            }
            throw new CrugeException(
                "el atributo authmode no coincide con ningun modulo de autenticacion instalado");
        } else {
            throw new CrugeException(
                "el atributo authmode no esta definido como valido en config");
        }
    }

    /** busca un ICrugeStoredUser en el almacen de datos que cumpla con el identificador solicitado
    (usernameORemail) y que cumpla con la configuracion general del sistema

    @returns instancia que implementa a: ICrugeStoredUser o null
     */
    public function getICrugeStoredUser($usernameORemail)
    {
        Yii::log(__METHOD__ . "\nusernameOrEmail=" . $usernameORemail, "info");
        // verifica en la lista corta de setup del modulo a ver cuantos modos hay definidos
        //
        $n = count(CrugeUtil::config()->availableAuthModes);
        $model = null;
        if ($n == 0) {
            // seguramente se ha borrado o mal configurado el parametro de config/main.php
            // availableAuthModes.
            throw new CrugeException("no se definieron modos de autenticacion en config");
        } else {
            if ($n == 1) {
                // solo un metodo, username o email
                $key = CrugeUtil::config()->availableAuthModes[0];
                Yii::log(__METHOD__ . "\n buscando por '" . $key . "' a " . $usernameORemail, "info");
                $model = CrugeStoredUser::loadModel($usernameORemail, $key);
            } else {
                // son los dos metodos: username e email
                Yii::log(__METHOD__ . "\n buscando por -username- a " . $usernameORemail, "info");
                $model = CrugeStoredUser::loadModel($usernameORemail, 'username');
                if ($model == null) {
                    Yii::log(__METHOD__ . "\n buscando por -email- a " . $usernameORemail, "info");
                    $model = CrugeStoredUser::loadModel($usernameORemail, 'email');
                }
            }
            if ($model == null) {
                Yii::log(__METHOD__ . "\n **NO HALLADO** " . $usernameORemail, "warning");
            } else {
                Yii::log(__METHOD__ . "\n **USUARIO HA SIDO HALLADO** " . $usernameORemail, "info");
            }
            return $model;
        }
    }

    public function isAuthMethodAvailable($authName)
    {
        foreach (CrugeUtil::config()->availableAuthMethods as $key => $val) {
            if ($key == $authName) {
                return true;
            }
        }
        return false;
    }

    /*
        lee de CrugeModule el nombre del filtro de autenticacion a utilizar, por ahora
        solo un filtro se usa a la vez,a futuro se pretenden usar varios filtros, por eso es un array.

        retorna:  un string, el nombre del filtro de autenticacion, el nombre sera validado contra
        el valor que retorne el filtro usando la interfaz ICrugeAuth.authName()
    */
    public function getConfiguredAuthMethodName()
    {
        foreach (CrugeUtil::config()->availableAuthMethods as $key => $val) {
            return $val;
        }
        return false;
    }


    /** entrega el filtro para procesar sesiones de usuario.

    si sessionfilter no es declarada en config, entonces se usa a _defaultSessionFilter.

    @returns instancia que implementa a ICrugeSessionFilter.
     */
    public function getICrugeSessionFilter()
    {

        Yii::log(__CLASS__ . "\ngetICrugeSessionFilter\n", "info");

        //$filterClass = $this->sessionfilter;
        //if($filterClass == null || $filterClass=='')
        $filterClass = CrugeUtil::config()->defaultSessionFilter;

        $filepath = Yii::getPathOfAlias($filterClass) . ".php";
        $className = CrugeUtil::getClassNameFromPhp($filepath);

        if (is_file($filepath)) {
            if (!class_exists($className, false)) {
                require($filepath);
            }
            if (class_exists($className, false)) {
                Yii::log(__CLASS__ . "\ngetICrugeSessionFilter\nnew instance for: " . $className, "info");
                return new $className();
            } else {

                Yii::log("clase no hallada." . $className, "error");
                throw new CrugeException("clase no hallada. ver log.");
            }
        } else {
            Yii::log("ruta de clase es invalida:" . $filepath, "error");
            throw new CrugeException("ruta de clase es invalida. ver log.");
        }
    }


    /*
        este metodo prepara y entrega los metodos de autenticacion
        normalmente, quien llama a este metodo es: CrugeUser en authenticate()

        las clases de autenticacion estan en models.auth y cada debe implementar
        la interfaz components.ICrugeAuth y debe extender de CBaseUserIdentity

        @return Un array de instancias de cada clase de autenticacion, solo de aquellas existentes
        en el paquete models.auth
    */
    private function _getAuthModes()
    {
        if (CrugeUtil::config()->_lazyAuthModes == null) {
            CrugeUtil::config()->_lazyAuthModes = array();

            // levantamos las clases directo del disco
            $ruta = Yii::getPathOfAlias('cruge.models.auth');
            Yii::log("levantando clases de autenticacion de:\n" . $ruta . "\n", "info");
            $files = scandir($ruta);
            foreach ($files as $f) {
                if ($f != '.' && $f != '..') {
                    Yii::log(
                        "findfile: " . $f . ", isPhp?" . (CrugeUtil::isPhpFile($f) ? "YES" : "NO"),
                        "info"
                    );
                    if (CrugeUtil::isPhpFile($f)) {
                        $className = CrugeUtil::getClassNameFromPhp($f);
                        if (class_exists($className)) {

                            if (!is_subclass_of($className, 'CBaseUserIdentity')) {
                                Yii::log(
                                    "clase de autenticacion no es subclase de "
                                        . "CBaseUserIdentity. clase=" . $className,
                                    "error"
                                );
                                throw new CrugeException(
                                    "clase de autenticacion no extiende de CBaseUserIdentity. ver log."
                                );
                            }
                            // se asume que la clase provee la interfaz ICrugeAuth
                            // y crea la instancia:
                            $inst = new $className();

                            if (self::isAuthMethodAvailable($inst->authName()) == true) {
                                CrugeUtil::config()->_lazyAuthModes[] = $inst;
                            } else {
                                // existe, pero no esta definida en config por tanto no se agrega
                                //
                                Yii::log(
                                    "Clase de autenticacion encontrada [{$className}] "
                                        . "pero no esta definida en config. "
                                        . "Su authName definido es: [" . $inst->authName() . "]"
                                    ,
                                    "info"
                                );
                            }
                        } // endif class_exist
                        else {
                            Yii::log(
                                "clase de autenticacion no es valida class_exist retorna false. "
                                    . className,
                                "error"
                            );
                            throw new CrugeException("clase de autenticacion no es valida. ver log.");
                        }
                    }
                    // endif isPhpFile
                }
            }

            if (count(CrugeUtil::config()->_lazyAuthModes) == 0) {
                //
                // si llegamos a este punto es porque no se ha configurado
                // ningun metodo de autenticacion, o porque la carpeta models.auth esta vacia
                // o ninguno de sus archivos cumple con lo necesario.
                //
                Yii::log("Ninguna clase de autenticacion declarada cumple requerimientos", "error");
                throw new CrugeException("ninguna clase de autenticacion instalada "
                    . "cumplio requerimientos");
            }

        }
        // endif lazy init
        return CrugeUtil::config()->_lazyAuthModes;
    }

    /* obtiene el ICrugeStoredUser desde una sesion ICrugeSession

        porque la pongo aqui y no como una relacion de ICrugeSession directamente: para evitar una dependencia.

        @returns ICrugeStoredUser o null
    */
    public function getSessionUser(ICrugeSession $sesion)
    {
        if ($sesion == null) {
            return null;
        }
        // no voy a hacer uso de relations() porque quiza otros ORDBM no tengan ese mecanismo
        return CrugeStoredUser::loadModel($sesion->iduser, 'iduser');
    }

    /*
        crea una instancia del filtro ICrugeUserFilter instalado en el modulo,

        este metodo es referenciado en CrugeUserManager::save()

        @returns instancia que implementa a ICrugeUserFilter
    */
    public function getUserFilter()
    {

        $filterClass = CrugeUtil::config()->userFilter;

        $filepath = Yii::getPathOfAlias($filterClass) . ".php";
        $className = CrugeUtil::getClassNameFromPhp($filepath);

        if (is_file($filepath)) {
            if (!class_exists($className, false)) {
                require($filepath);
            }
            if (class_exists($className, false)) {
                Yii::log(__METHOD__ . "\nnueva instancia de: " . $className, "info");
                return new $className();
            } else {

                Yii::log(__METHOD__ . " clase no hallada." . $className, "error");
                throw new CrugeException("clase no hallada. ver log.");
            }
        } else {
            Yii::log(__METHOD__ . " ruta de clase es invalida:" . $filepath, "error");
            throw new CrugeException(__METHOD__ . " ruta de clase es invalida. ver log.");
        }
    }

    /**
     * getICrugeStoredUserByUsername
     *  busca un usuario por su username exclusivamente.
     * @param mixed $username
     * @access public
     * @return CrugeStoredUser instancia
     */
    public function getICrugeStoredUserByUsername($username)
    {
        return CrugeStoredUser::loadModel($username, 'username');
    }
}
