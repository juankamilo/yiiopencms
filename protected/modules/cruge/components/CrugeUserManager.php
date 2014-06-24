<?php
/** CrugeUserManager

funciona como una interfaz para el core del sistema cruge, opera como un API.

se accede exclusivamente asi:

$um = Yii::app()->user->um;

dependencias:

CrugeFactory
CrugeUtil
CrugeTranslator

@author: Christian Salazar H. <christiansalazarh@gmail.com> @salazarchris74
@license protected/modules/cruge/LICENSE
 */

define("CRUGEUSERSTATE_NOTACTIVATED", 0);
define("CRUGEUSERSTATE_ACTIVATED", 1);
define("CRUGEUSERSTATE_SUSPENDED", 2);

define("CRUGEFIELDTYPE_TEXTBOX", 0);
define("CRUGEFIELDTYPE_TEXTAREA", 1);
define("CRUGEFIELDTYPE_BOOLEAN", 2);
define("CRUGEFIELDTYPE_LISTBOX", 3);

define("CRUGE_ACTIVATION_OPTION_INMEDIATE", 0);
define("CRUGE_ACTIVATION_OPTION_EMAIL", 1);
define("CRUGE_ACTIVATION_OPTION_MANUAL", 2);


class CrugeUserManager
{

    /*
        retorna un array con los estatus que puede tener un usuario.
        este array tambien puede ser utilizado directamente en un dropDownList
    */
    public function getUserStateOptions()
    {
        $stAr = array();
        for ($i = CRUGEUSERSTATE_NOTACTIVATED; $i <= CRUGEUSERSTATE_SUSPENDED; $i++) {
            $stAr[$i] = $this->getStateName($i);
        }
        return $stAr;
    }

    public function getStateName($state)
    {
        switch ($state) {
            case CRUGEUSERSTATE_NOTACTIVATED:
                return CrugeTranslator::t("Cuenta sin Activar");
            case CRUGEUSERSTATE_ACTIVATED:
                return CrugeTranslator::t("Cuenta Activada");
            case CRUGEUSERSTATE_SUSPENDED:
                return CrugeTranslator::t("Cuenta Suspendida");
        }
        return $state;
    }

    public function getFieldTypeOptions()
    {
        $stAr = array();
        for ($i = CRUGEFIELDTYPE_TEXTBOX; $i <= CRUGEFIELDTYPE_LISTBOX; $i++) {
            $stAr[$i] = $this->getFieldTypeName($i);
        }
        return $stAr;
    }

    public function getFieldTypeName($fieldType)
    {
        switch ($fieldType) {
            case CRUGEFIELDTYPE_TEXTBOX:
                return CrugeTranslator::t("TextBox");
            case CRUGEFIELDTYPE_TEXTAREA:
                return CrugeTranslator::t("TextArea");
            case CRUGEFIELDTYPE_BOOLEAN:
                return CrugeTranslator::t("CheckBox");
            case CRUGEFIELDTYPE_LISTBOX:
                return CrugeTranslator::t("ListBox");
        }
        return $fieldType;
    }

    public function getUserActivationOptions()
    {
        $stAr = array();
        for ($i = CRUGE_ACTIVATION_OPTION_INMEDIATE; $i <= CRUGE_ACTIVATION_OPTION_MANUAL; $i++) {
            $stAr[$i] = $this->getUserActivationName($i);
        }
        return $stAr;
    }

    public function getUserActivationName($state)
    {
        switch ($state) {
            case CRUGE_ACTIVATION_OPTION_INMEDIATE:
                return CrugeTranslator::t("Activacion inmediata");
            case CRUGE_ACTIVATION_OPTION_EMAIL:
                return CrugeTranslator::t("Activar mediante correo");
            case CRUGE_ACTIVATION_OPTION_MANUAL:
                return CrugeTranslator::t("Activacion manual");
        }
        return $state;
    }


    /* se encarga de crear una nueva llave de autenticacion para el usuario.
       el modelo debera ser guardado tras esta llamada.

        @see getActivationUrl
    */
    public function generateAuthenticationKey(ICrugeStoredUser $user)
    {
        $user->authkey = CrugeUtil::hash($user->username . "-" . $user->password);
    }


    /*
        entrega una CArrayDataProvider obtenido desde un array de userid.

        es una funcion helper que invoca a listUsers con los parametros adecuados.
    */
    public function listUsersDataProviderFromArray($arrayUserId, $pageSize = 20
			,$boolLoadCustomFields = false)
    {
        return $this->listUsers(null, true, $pageSize, true, $arrayUserId
				,$boolLoadCustomFields);
    }

    public function listAllUsersDataProvider($params = array(), $pageSize = 20
			,$boolLoadCustomFields = false)
    {
        return $this->listUsers($params, true, $pageSize, false, null
				,$boolLoadCustomFields);
    }

    /*
        @param, lista de parametros a pasar a ICrugeStoredUser::listmodels
        @booleanAsDataProvider, false=retorna un array, true=retorna un carraydataprovider
        @pageSize, solo tiene sentido bajo un carraydataprovider
        @buildFromThisUsersIdArray, si se especifica el dataprovider se construira en base
        a estos iduser entregados en un array

        retorna un array de objetos ICrugeStoredUser o un CArrayDataProvider
    */
    public function listUsers(
        $param = array()
        ,
        $booleanAsDataProvider = false
        ,
        $pageSize = 20
        ,
        $boolUseArray = false
        ,
        $buildFromThisUsersIdArray = null
		,
		$boolLoadCustomFields = false
    ) {
        $ar = array();
        // si buildFromThisUsersIdArray es null, entonces se buscan los usuarios directamente
        if ($boolUseArray == false) {
            $ar = CrugeFactory::get()->getICrugeStoredUserList($param);
        } else {
            if ($buildFromThisUsersIdArray != null) {
                foreach ($buildFromThisUsersIdArray as $userid) {
                    $user = $this->loadUserById($userid);
                    $ar[] = $user;
                }
            }
        }

		// levanta sus campos personalizados, segun peticion explicita
		// podria ralentizar en alto volumen de usuarios..asi que cuidado.
		if($boolLoadCustomFields == true)
			foreach($ar as $user)
				$this->loadUserFields($user);

        if ($booleanAsDataProvider == true) {
            $sortFields = CrugeFactory::get()->getICrugeStoredUserSortFieldNames();
            return new CArrayDataProvider($ar, array(
                'keyField' => $sortFields[0],
                'sort' => array(
                    'attributes' => $sortFields,
                ),
                'pagination' => array(
                    'pageSize' => $pageSize,
                ),
            ));
        } else {
            return $ar;
        }
    }

	public function getSearchUserModel(){
		return CrugeFactory::get()->getICrugeStoredUserModel('search');
	}

	/**
	  	retorna un dataprovider que contiene todos los usuarios asignados
		directamente a un rol. (no hace busquedas recursivas)
	 
	 	@returns CActiveDataProvider
	 */
	public function searchUsersByAuthItem(
			$authItemName, $pageSize=20, $defaultOrder=null, $_model=null){
		if($_model == null){
		$model = $this->getSearchUserModel();
		}else
			$model = $_model;
		return	$model->searchByAuthItem(
				$authItemName, $pageSize, $defaultOrder);
	}

    /*
        @returns instancia ICrugeStoredUser del usuario cuyo iduser sea el $id pasado por argumento.

        para que el user cargado tenga los campos de perfil hay que llamar a:
        @see loadUserFields (poner el arg a true: $boolAndLoadFields)
        @see loadUser
    */
    public function loadUserById($id, $boolAndLoadFields = false)
    {
        $user = CrugeFactory::get()->getICrugeStoredUserLoadModel($id, false);
        if (($boolAndLoadFields == true) && ($user != null)) {
            $this->loadUserFields($user);
        }
        return $user;
    }

    public function loadUserByKey($id, $boolAndLoadFields = false)
    {
        $user = CrugeFactory::get()->getICrugeStoredUserLoadModel($id, false, true);
        if (($boolAndLoadFields == true) && ($user != null)) {
            $this->loadUserFields($user);
        }
        return $user;
    }

    /*
        @returns instancia ICrugeStoredUser del usuario cuyo iduser sea el $id pasado por argumento.

        para que el user cargado tenga los campos de perfil hay que llamar a:
        @see loadUserFields (poner el arg a true: $boolAndLoadFields)
        @see loadUserById
    */
    public function loadUser($usernameOrEmail, $boolAndLoadFields = false)
    {
        Yii::log(__METHOD__ . "\nusernameOrEmail=" . $usernameOrEmail, "info");
        $user = CrugeFactory::get()->getICrugeStoredUser($usernameOrEmail);
        if (($boolAndLoadFields == true) && ($user != null)) {
            $this->loadUserFields($user);
        }
        return $user;
    }

    /**
     * loadUserByUsername
     *    busca un usuario por su username exclusivamente.
     * @param mixed $username
     * @param mixed $boolAndLoadFields flag, true para precargar campos personalizados.
     * @access public
     * @return instancia de CrugeStoredUser
     */
    public function loadUserByUsername($username, $boolAndLoadFields = false)
    {
        $user = CrugeFactory::get()->getICrugeStoredUserByUsername($username);
        if (($boolAndLoadFields == true) && ($user != null)) {
            $this->loadUserFields($user);
        }
        return $user;
    }

    /**
     * loadUserByCustomField
     *    busca a un usuario de forma por un valor de un campo personalizado
     * @param mixed $customFieldName nombre del campo, ej 'cedula'
     * @param mixed $customFieldVal  valor del campo, ej '12182989'
     * @access public
     * @return instancia de CrugeStoredUser o null.
     */
    public function loadUserByCustomField($customFieldName, $customFieldVal)
    {
        // primero. busca el campo referenciado por ese nombre
        $field = $this->loadFieldByName($customFieldName);
        if ($field == null) {
            return null;
        } // campo no existe

        $fieldvalueInstance = CrugeFactory::get()->
            getICrugeFieldValueByValue($field, $customFieldVal);

        if ($fieldvalueInstance == null) {
            return null;
        } // no hay coincidencias

        return $this->loadUserById($fieldvalueInstance->iduser, true);
    }

    /*
        crea una nueva instancia de ICrugeStoredUser
    */
    public function createBlankUser()
    {
        $user = CrugeFactory::get()->getICrugeStoredUserNewModel();
        if ($user != null) {
            // asegura que no falle al validar por terminos y condiciones
            $user->terminosYCondiciones = true;
            // asegura que no falle al validar por captcha
            //	cruge\models\data\CrugeStoredUser.php (bypassCaptcha y _getCaptchaRule)
            $user->bypassCaptcha = true;
            return $user;
        } else {
            return null;
        }
    }

    /*
        activa la cuenta, estampando la fecha de activacion.

        solo aplica si el estado del modelo es: CRUGEUSERSTATE_NOTACTIVATED
    */
    public function activateAccount(ICrugeStoredUser $user)
    {
        if ($user->state != CRUGEUSERSTATE_NOTACTIVATED) {
            return false;
        }
        $user->state = CRUGEUSERSTATE_ACTIVATED;
        $user->actdate = CrugeUtil::now();
    }

    /*
        entrega la URL de activacion
        @see generateAuthenticationKey
    */
    public function getActivationUrl(ICrugeStoredUser $user)
    {
        return
            rtrim(CrugeUtil::config()->baseUrl, "/")
            . CrugeUtil::uiaction('activationurl', array('key' => $user->authkey));
    }

    /*
        marca la fecha de logon del usuario, normalmente para alterar el campo logondate,
    */
    public function recordLogon(ICrugeStoredUser $user)
    {
        $user->logondate = CrugeUtil::now();
    }

    /*
        guarda a este usuario, bajo un escenario especial llamado 'internal', para
        poder pasar por encima de algunas reglas de validacion que puede que apliquen
        solo para el usuario que manipula el modelo mediante formularios.

        si el escenario es 'insert' (caso crear usuario o registrar usuario),
        entonces se aplica un filtro de registro instanciado por algun ICrugeRegistrationFilter
        declarado en la configuracion del modulo Cruge.

    */
    public function save(ICrugeStoredUser $user, $scenario = 'internal')
    {
        $user->scenario = $scenario;
        // aplica el filtro ICrugeUserFilter configurado en el modulo
        //
        if (($user->scenario == 'insert') || ($user->scenario == 'update')) {
            $filtro = CrugeFactory::get()->getUserFilter();
            if ($filtro != null) {
                if ($user->scenario == 'insert') {
                    if ($filtro->canInsert($user) == false) {
                        return false;
                    }
                } else {
                    if ($user->scenario == 'update') {
                        if ($filtro->canUpdate($user) == false) {
                            return false;
                        }
                    }
                }
            }
        }
        return $user->save();
    }


    /*
        le cambia la clave al usuario.  el modelo debera ser guardado con $model->save() tras
        esta llamada.
    */
    public function changePassword(ICrugeStoredUser $user, $newPassword)
    {
        $epwd = $newPassword;
        if (CrugeUtil::config()->useEncryptedPassword == true) {
            $epwd = CrugeUtil::hash($newPassword);
        }
        $user->password = $epwd;
    }

    /* busca la sesion abierta mas reciente del usuario

        returns ICrugeSession la sesion abierta mas reciente del usuario
    */
    public function findSession(ICrugeStoredUser $user)
    {
        return CrugeFactory::get()->getICrugeSessionFindLastByUser($user->getPrimaryKey());
    }

    public function loadSession($idsession)
    {
        return CrugeFactory::get()->getICrugeSession($idsession);
    }

    /*
        retorna instancia de ICrugeSessionFilter del filtro de sesion instalado
    */
    public function getSessionFilter()
    {
        return CrugeFactory::get()->getICrugeSessionFilter();
    }


    /*
        busca un ICrugeSystem por su nombre
    */
    public function loadSystemByName($systemName)
    {
        return CrugeFactory::get()->getICrugeSystemByName($systemName);
    }

    public function getDefaultSystem()
    {
        return $this->loadSystemByName('default');
    }


    /* crea una nueva sesion para el usuario basado en los parametros del sistema seleccionado.

       returns ICrugeSession
    */
    public function createSession(ICrugeStoredUser $user, ICrugeSystem $sys)
    {

        Yii::log(
            __CLASS__ . "::createSession. user=#"
                . $user->getPrimaryKey(),
            "info"
        );

        return CrugeFactory::get()->getICrugeSessionCreate(
            $user->getPrimaryKey(),
            $sys->getn('sessionmaxdurationmins')
        );
    }

    /*
        retorna una instancia de ICrugeStoredUser de la sesion indicada.
    */
    public function getUserFromSession(ICrugeSession $session)
    {
        return CrugeFactory::get()->getSessionUser($session);
    }

    /*
        carga un filtro que implementa a ICrugeAuth hallado por su nombre
    */
    public function getAuthenticationFilterByName($byName)
    {
        return CrugeFactory::get()->getICrugeAuthByName($byName);
    }

    /*
        retorna una instancia de ICrugeField buscada por su idfield.
    */
    public function loadFieldById($id)
    {
        return CrugeFactory::get()->getICrugeFieldLoadModel($id);
    }

    /*
        retorna una instancia de ICrugeField buscada por su fieldname
    */
    public function loadFieldByName($fieldname)
    {
        return CrugeFactory::get()->getICrugeFieldLoadModelByName($fieldname);
    }

    /*
        retorna una instancia de ICrugeField nueva en blanco
    */
    public function createEmptyField()
    {
        return CrugeFactory::get()->getICrugeFieldCreate(CRUGEFIELDTYPE_TEXTBOX);
    }

    /*
        recibe una instancia de ICrugeStoredUser y carga en esta todos los campos personalizados
        de perfil que el administrador ha definido.

        @returns: un array de instancias ICrugeField con el valor (fieldvalue) correspondiente.

        @see loadUserById
    */
    public function loadUserFields(ICrugeStoredUser $user)
    {
        $user->setFields(CrugeFactory::get()->getICrugeFieldListModels($user));
        return $user->getFields();
    }

    /*
        retorna la lista de campos personalizados, sin referencias a ningun usuario.
        @returns array de ICrugeField (sin valor asignado)
    */
    public function getUserFields()
    {
        return CrugeFactory::get()->getICrugeFieldListModels();
    }

    /**
    limpia los campos personalizados.
     */
    public function clearUserFields(ICrugeStoredUser $user)
    {
        $user->setFields(CrugeFactory::get()->getICrugeFieldListModels($user));
        foreach ($user->getFields() as $field) {
            $field->setFieldValue("");
        }
        return $user->getFields();
    }

    /*
        retorna el objeto que implementa a ICrugeFieldValue de un campo aplicado a un usuario,
    */
    public function loadICrugeFieldValue(ICrugeStoredUser $user, ICrugeField $field)
    {
        return CrugeFactory::get()->getICrugeFieldValue($user, $field);
    }

    /*
        obtiene el valor escalar de un campo para un usuario.

        @iduser: mixed.  puede ser el IDUSER o una instancia de ICrugeStoredUser
        @idfield: mixed.  puede ser el FIELDNAME, IDFIELD o una instancia de ICrugeField
    */
    public function getFieldValue($iduser, $idfield)
    {

        if (is_string($iduser)) {
            $u = $this->loadUserById($iduser);
        } else {
            $u = $iduser;
        }

        if ($u != null) {

            if (is_numeric($idfield)) {
                // busca por idfield
                //
                $field = $this->loadFieldById($idfield);
                if ($field == null) {
                    return "";
                }
            } else {
                // busca por nombre
                //
                if (is_string($idfield)) {
                    $field = $this->loadFieldByName($idfield);
                    if ($field == null) {
                        return "";
                    }
                } else {
                    // asume que es una instancia que implementa a ICrugeField
                    $field = $idfield;
                }
            }

            if ($field != null) {
                $fv = CrugeFactory::get()->getICrugeFieldValue($u, $field);
                if ($fv != null) {
                    return $fv->value;
                }
            }
        }
        return "";
    }

    /**
     * getFieldValueInstance
     *     retorna el objeto
     * @param mixed $iduser primarykey o instancia de ICrugeStoredUser
     * @param mixed $idfield primarykey del Field o instancia o fieldname
     * @access public
     * @return instancia de CrugeFieldValue (a diferencia de getFieldValue
    quien retorna solo el valor).
     */
    public function getFieldValueInstance($iduser, $idfield)
    {
        // verifica si iduser es un ID numerico (su primarykey)
        //	o si es una instancia..
        if (is_string($iduser)) {
            $u = $this->loadUserById($iduser);
        } else {
            $u = $iduser;
        }
        // ahora,
        if ($u != null) {
            if (is_numeric($idfield)) {
                // busca por el primarykey de un campo
                //
                $field = $this->loadFieldById($idfield);
                if ($field == null) {
                    return null;
                }
            } else {
                // busca por nombre
                //
                if (is_string($idfield)) {
                    $field = $this->loadFieldByName($idfield);
                    if ($field == null) {
                        return null;
                    }
                } else {
                    // asume que es una instancia que implementa a ICrugeField
                    $field = $idfield;
                }
            }
            // asocia el campo con el valor de este usuario
            if ($field != null) {
                $fv = CrugeFactory::get()->getICrugeFieldValue($u, $field);
                if ($fv != null) {
                    return $fv;
                }
            }
        }
        return null;
    }


    /*
        funciona como lo haria un CActiveForm::labelEx, pero considerando que estos
        campos aqui indicados no pertenecen al modelo como tal porque son campos definidos
        por el admin.

        lo que se hara aqui es presentar una etiqueta pero con una clase "required" y un
        asterisco para indicar que el campo es requerido si la config del campo asi lo decide.
    */
    public function getLabelField(ICrugeField $field)
    {

        $r = "";
        $ast = "";
        $text = ucfirst(CrugeTranslator::t($field->longname));
        if ($field->required == 1) {
            $r = " class='required' ";
            $ast = "<span {$r}>*</span>";
        }

        return "<label {$r}>{$text} {$ast}</label>";
    }

    /*
        retorna el elemento de UI correspondiente a la configuracion del campo personalizado.

        Igualmente recibe el valor correspondiente que el usuario ha ingresado para este campo.

        $model:  es la clase que alojara los datos del formulario, no se usa para nada mas
        de este modo si otro modelo quiere incorporar campos personalizados solo pone "$this" y asi
        los items del form se pondran de acuerdo a la clase indicada.

        $field:  es un campo, instancia de ICrugeField cuyo atributo fieldvalue fue previamente
        establecido. @see loadUserFields (para saber como se carga fieldvalue)

        este metodo es basicamente en las vista:
            usermanagementupdate.php
            registration.php

        @returns Elemento tag de CHtml de acuerdo a la configuracion de $field->fieldtype
        @see loadUserFields (para saber como se carga fieldvalue)
    */
    public function getInputField($model, ICrugeField $field)
    {

        $className = get_class($model);

        $name = $className . "[" . $field->fieldname . "]";
        $htmlOpt = array(
            'id' => $className . "_" . $field->fieldname
        ,
            'size' => $field->fieldsize
        ,
            'maxlength' => $field->maxlength
        ,
            'rows' => 5
        ,
            'cols' => $field->fieldsize
        );

        // caso listas: Listbox
        // se espera que venga cada valor que se pasara al <option></option>
        // venga en la forma "VALUE, TEXT"
        //
        $arOpt = array();
        if ($field->fieldtype == CRUGEFIELDTYPE_LISTBOX) {
            $arOpt = CrugeUtil::explodeOptions($field->predetvalue);
            $htmlOpt['rows'] = null;
            $htmlOpt['cols'] = null;
            $htmlOpt['size'] = null;
            $htmlOpt['maxlength'] = null;
        }

        // estos tipos definidos estan en CrugeUserManager

        switch ($field->fieldtype) {
            case CRUGEFIELDTYPE_TEXTBOX:
                return CHtml::textField($name, $field->getFieldValue(), $htmlOpt) . "\n";
            case CRUGEFIELDTYPE_TEXTAREA:
                return CHtml::textArea($name, $field->getFieldValue(), $htmlOpt) . "\n";
            case CRUGEFIELDTYPE_BOOLEAN:
                return CHtml::checkBox($name, $field->getFieldValue(), $htmlOpt) . "\n";
            case CRUGEFIELDTYPE_LISTBOX:
                return CHtml::dropDownList(
                    $name,
                    $field->getFieldValue(),
                    $arOpt,
                    $htmlOpt
                ) . "\n";
        }
        return null;
    }

    public function getSearchModelICrugeStoredUser()
    {
        return CrugeFactory::get()->getNewICrugeStoredUserForSearch();
    }

    public function getSearchModelICrugeSession()
    {
        return CrugeFactory::get()->getNewICrugeSessionForSearch();
    }

    public function getSearchModelICrugeField()
    {
        return CrugeFactory::get()->getNewICrugeFieldForSearch();
    }

    /*
        crea una nueva instancia del modelo CrugeLogon bajo el escenario indicado {login o pwdrec}

        CrugeLogon es un modelo (CFormModel) para el formulario de Login y Password Recovery
        que aparte de validar que los datos de ambos fomularios esten correctos tambien
        ayuda al proceso de llamar a Yii::app()->user->login mediante un metodo llamado login().

        basicamente es como el modelo LoginForm que trae Yii por defecto.

        @see CrugeLogon
        returns instancia de CrugeLogon
    */
    public function getNewCrugeLogon($scenario)
    {
        return new CrugeLogon($scenario);
    }

    /*
        crea una nueva instancia del modelo de autenticacion CrugeUser el cual
        representa a un usuario que quiere iniciar sesion (no es un usuario almacenado)
    */
    public function getNewCrugeUser($username, $password, $authMode = 'default')
    {
        return new CrugeUser($username, $password, $authMode);
    }

    public function getSortFieldNamesForICrugeStoredUser()
    {
        return CrugeFactory::get()->getICrugeStoredUserSortFieldNames();
    }

    public function getSortFieldNamesForICrugeField()
    {
        return CrugeFactory::get()->getICrugeFieldSortFieldNames();
    }


    public function loadSessionById($id)
    {
        return CrugeFactory::get()->getICrugeSession($id);
    }

    /**
     * generateNewUsername
     *     genera un username no existente agregando un ".NNNN" al final del email recortado hasta su arroba.
     * @param mixed $emailBased email del cual basarse.
     * @access public
     * @return void un username no existente. ejmplo: micorreo.8217i
     *
     *    importante:
     *        si el correo como tal (cortado hasta su arroba) no existe al
     *        buscar un usuario por su username, pues se usará, luego
     *        se le ira agregando un numero secuencial hasta dar con un username
     *        que no exista.
     */
    public function generateNewUsername($emailBased)
    {
        $username = strtolower(substr($emailBased, 0, strpos($emailBased, '@')));
        $ok = false;
        $sec = 1;
        $sep = '.';
        do {
            $u = $this->loadUserByUsername($username);
            if ($u == null) {
                // no existe, lo usamos.
                $ok = true;
            } else {
                // si existe, anexamos un separador y un numero secuencial
                $username .= $sep . $sec;
                $sep = '';
                $sec++;
            }
        } while (!$ok);
        return $username;
    }

    /**
     * createNewUser
     *    inserta un nuevo usuario con los valores mapeados indicados.
     *
     *    mapped_values, es un array -indexado- con los siguientes campos:
     *            'username'=>'userxxx', 'email'=>'xyz@gmail.com',
     *            y los siguientes serian los nombres de los campos
     *            personalizados
     *            'nombre'=>'pedro', 'apellido'=>'perez'
     *  si username es vacio (en el array de mapped_values) pues se genera uno.
     *
     * @param array $mapped_values array con valores mapeados
	 * @param string $role_name usar 'default' para usar rol definido en variables de sistema.
	 * @param boolean $bool_send_email true para que envie correos de notificacion
     * @access private
     * @return CrugeStoredUser retornado por um->createBlankUser
     */
    public function createNewUser($mapped_values,$role_name='default', $bool_send_email=true)
    {
        // para crear el usuario se requiere como minimo el email
        // si el username no fue provisto se creará uno en base
        // al email:
        //
        $password = CrugeUtil::passwordGenerator();
        $user = $this->createBlankUser();
        $user->email = $mapped_values['email'];
        if (isset($mapped_values['username'])) {
            $user->username = $mapped_values['username'];
        }
        // genera un username si el provisto es vacio
        if (empty($user->username)) {
            $user->username =
                $this->generateNewUsername($user->email);
        }
        // la establece como "Activada"
        $this->activateAccount($user);
        // ahora a ponerle una clave
        $this->changePassword($user, $password);
		// revisa que no duplique
        $_prev = CrugeFactory::get()->getICrugeStoredUser($user->username);
		if($_prev != null)
            throw new CrugeException("nombre usuario duplicado.");
        $_prev = CrugeFactory::get()->getICrugeStoredUser($user->email);
		if($_prev != null)
            throw new CrugeException("correo duplicado.");
        // guarda usando el API. No pasa por filtros. se reparara a futuro.
        if ($this->save($user)) {
            foreach ($mapped_values as $fieldname => $value) {
                if (($fieldname == 'username') || ($fieldname == 'email')) {
                    continue;
                } else {
                    // campo personalizado:
                    $fv = $this->getFieldValueInstance(
                        $user,
                        $fieldname
                    );
                    if ($fv != null) {
                        // evita una excepcion si se indica un nombre
                        // de campo personalizado inexistente.
                        //
                        $fv->value = $value;
                        $fv->update();
                    }
                }
            }
            // le asigna un rol por defecto
            //
			if($role_name == 'default'){
            	$role = $this->getDefaultSystem()->get("defaultroleforregistration");
			}else{ $role = $role_name; }
            Yii::log(__METHOD__ . "\n role: " . $role, "info");
            if (Yii::app()->user->rbac->getAuthItem($role) != null) {
                Yii::log(
                    __METHOD__ . "\n asignando role: " . $role . " a userid:"
                        . $user->getPrimaryKey(),
                    "info"
                );
                Yii::app()->user->rbac->assign($role, $user->getPrimaryKey());
            }
            // le envia un email con su clave generada automaticamente
			if($bool_send_email == true)
            	Yii::app()->crugemailer->sendPasswordTo($user, $password);
            return $user;
        } else {
            // un error de validacion. emitido por alguna regla de
            // cruge.models.data.CrugeStoredUser
            //
            $errores = CHtml::errorSummary($user);
            throw new CrugeException("no se pudo crear el usuario: " . $errores);
        }
    }

	/**
		crea usuarios ficticios en la base de datos de usuarios de cruge.
		
		1. considerar que: 
		si dura mucho creando usuarios entonces puede haber
		problemas con la variable de PHP max_execution_time

		2. considerar que: 
		firstname_field y lastname_field son los nombres de los campos personalizados
		que recibiran el nombre y apellido aleatorio, deben existir.

		@param integer $size numero de registros a crear.
		@param string $role_name el nombre del rol, o usar 'default' para usar el del sistema.
		@param string $firstname_field nombre del campo personalizado que se usara como "Nombre"
		@param string $lastname_field nombre del campo personalizado que se usara como "Apellido"
	*/
	public function createRandomUsers($size, $role_name='default',
			$firstname_field='firstname',$lastname_field='lastname') {
		$firstnames = array('jhonn','peter','christian','frederik','william',
				'mathew','carl','grian','alex','jhonson','michael','francis',
				'ann','mary','vanessa','robin','glenda','viki','anahi','rose');
		$lastnames = array('carpenter','romina','julian','camary','olson',
				'wells','dunkan','lloyd','marcus','veham','dairus','fenix',
				'samuelson','gregorian','grimald','carlson');
		$ar = array();
    	for($i=0;$i<$size;$i++){
    		$names = $this->_getRandName($firstnames, $lastnames, $ar);
    		$firstname = $names[0];
    		$lastname =  $names[1];
    		$basemail = $firstname[0].$lastname;
    		$map = array('email'=>$basemail.'@localhost.com',
    				$firstname_field=>$firstname, $lastname_field=>$lastname);
    		$this->createNewUser($map,$role_name,false);
    	}				
	}

	/**
	 	usada por createRandomUsers para generar nombres de usuario aleatorio.
	 */
	private function _getRandName($f,$l,&$ar){
		$n=0;
		while(true){
			$firstname = $f[rand(0,count($f)-1)];
			$lastname = $l[rand(0,count($l)-1)];
			if(!isset($ar[$firstname.'-'.$lastname])){
				$ar[$firstname.'-'.$lastname]=array($firstname,$lastname);
				return array($firstname,$lastname);
			} 
			if($n++ > 20)
				throw new Exception('no more names combinations available. please enlarge the names array.');
		}
	}

    /**
     * loginUser
     *     metodo que emula el inicio de sesion tal cual se hubiese usado
     *  el formulario de login, respetandose el control de sesion y demas.
     *
     * @param string $usernameOremail
     * @access public
     * @return instancia de ICrugeStoredUser o null
     */
    public function loginUser($usernameOremail)
    {
        Yii::log(__METHOD__ . " usernameOremail=" . $usernameOremail, "info");
        $user = $this->loadUser($usernameOremail);
        if ($user != null) {
            // crea un modelo de formulario y le pasa los datos, sin validar.
            // aqui model es una instancia de cruge.models.ui.CrugeLogon
            //
            $model = $this->getNewCrugeLogon('login');
            $model->authMode = CrugeFactory::get()->getConfiguredAuthMethodName();
            $model->username = $usernameOremail;
            $model->password = $user->password;
            if ($model->validate()) {
                if ($model->login(true) == true) {
                    Yii::log(__METHOD__ . " retorna al usuario. exitoso.", "info");
                    return $user;
                } else {
                    // no deberia ocurrir, porque se le esta dando el password,
                    // pero respetemos el resultado de login()...paranoia.
                    Yii::log(__METHOD__ . " retorna null. no deberia.", "error");
                }
            } else {
                Yii::log(
                    __METHOD__ . " retorna null."
                        . CHtml::errorSummary($model, 'falla de validacion'),
                    "error"
                );
            }
        }
        return null;
    }


    /**
     * remoteLoginInterface
     *    es una interfaz para iniciar sesion o registrar a un usuario de forma
     *  automatizada.
     *
     * $fieldmap: que campos de Cruge estan relacionados
     * con cuales campos que nos ha enviado facebook o google.
     * por ejemplo: array('email'=>'contact/email'),
     *
     * $values se espera que sea un array indexado, cuyo
     * indice sea un campo de facebook o google:
     * por ejemplo:  array('contact/email'=>'juanperez@abc.com'),
     *
     * $modality:  la modalidad de registro de un usuario, puede ser una de:
     *
     *     'auto'        : registra al usuario de inmediato y le inicia la sesion
     *    'manual'    : lo envia a la pantalla de registro con datos precargados.
     *    'none'        : si el usuario no esta registrado no procede.
     *
     * @param array $fieldmap
     * @param array $values
     * @param string $modality 'auto', 'manual' , 'none'
     * @param string $errorResult (out) error result string
	 * @param string $role_name usar 'default' para usar rol definido en variables de sistema.
	 * @param boolean $bool_send_email true para que envie correos de notificacion
     * @access public
     * @return false o una URL (array o string) para ir a ella.
     */
    public function remoteLoginInterface(
        $fieldmap,
        $_values,
        $modality,
        &$errorResult,
        $debug = false,
		$role_name = 'default',		// aplican cuando modality es 'auto'
		$bool_send_email = true		// causando invocacion a createNewUser
    ) {

        $values = '';
        if (is_string($_values)) {
            $values = CJSON::decode($_values);
        } else {
            $values = $_values;
        }

        // para depurar:
        //
        // die(CJSON::encode(array('fieldmap'=>$fieldmap,'values'=>$values)));
        //

        // se genera un array con datos que cruge entienda a partir
        // de los valores y fieldmap entregados:
        //
        // cada par del array contendrá a la salida:
        //	array('username'=>'csalazar', 'email'=>'csalazar@abc.com'
        //		, 'nombre'=>'christian','apellido'=>'salazar')
        $mapped_values = array();
        foreach ($fieldmap as $localfield => $remotefield) {
            $mapped_values[$localfield] = '';
            if (isset($values[$remotefield])) {
                $mapped_values[$localfield] = $values[$remotefield];
            }
        }

        // para depurar:
        //
        if ($debug == true) {
            echo CJSON::encode(array('fieldmap' => $fieldmap, 'values' => $values));
            die("<hr/>" . CJSON::encode($mapped_values));
        }

        $email = '';
        if (isset($mapped_values['email'])) {
            $email = $mapped_values['email'];
        }
        if (empty($email)) {
            // hay algun problema con los valores entregados
            $errorResult = 'El email no esta presente en los datos entregados';
            return false;
        }

        // primero pedirle a cruge que inicie sesion
        // con el 'email' detectado. (si el caso es google, solo vendra email,
        // si el caso es facebook vendra username e email, por eso usamos
        // email como base).
        $crugeUser = $this->loginUser($email);

        // PASO 4. Quiza el usuario no exista y haya que registrarlo, por
        // tanto aplicariamos una logica de negocio propia de tu aplicacion.
        // si el usuario ya estaba registrado loginUser retornara ese usuario.
        $logged_on = false;
        if ($crugeUser == null) {
            // usuario no registrado en Cruge, segun tu decision, podemos
            // aplicar ciertas modalidades:
            //	'auto', 'manual' o 'none'

            if ($modality == 'auto') {
                // automaticamente registra al usuario y le inicia sesion
                //
                $crugeUser = $this->createNewUser($mapped_values, 
					$role_name, $bool_send_email);
                if ($crugeUser == null) {
                    $errorResult = 'No se pudo crear el usuario';
                    return false;
                }
                $crugeUser = $this->loginUser(
                    $mapped_values['email']
                );
                if ($crugeUser != null) {
                    $logged_on = true;
                } else {
                    $errorResult = 'No se pudo iniciar sesion.';
                    return false;
                }
            } elseif ($modality == 'manual') {
                // le pone algunos campos prefijados obtenidos de facebook
                // o google, pero el usuario debe continuar su proceso
                // de registro manualmente

                // el actionRegistration de cruge ofrece una ventaja:
                // se le puede dar un nombre de variable de sesion en la cual
                // se almacenan datos para que inicialize el usuario antes
                // de presentar el form de registro.
                $s = new CHttpSession();
                $s->open();
                $s['_crugeregistration_'] = $mapped_values;
                $s->close();

                // nos vamos al action de registro de Cruge pasandole
                // como argumento esta variable de sesion:
                $errorResult = 'registration';
                return array(
                    '/cruge/ui/registration',
                    'datakey' => '_crugeregistration_'
                );
            } elseif ($modality == 'none') {
                // no esta permitido el registro por facebook o google:
                $errorResult = 'Debe registrarse manualmente.';
                return false;
            }
        } else {
            $logged_on = true;
        }

        if ($logged_on == true) {
            // el usuario ya estaba registrado en Cruge
            // lo llevamos a la pagina de usuario bienvenido de cruge
            //
            // returnUrl fue establecido automaticamente por:
            //		cruge.models.filters.CrugeDefaultSession::onLogin
            //	al valor de tu configuracion:  'afterLoginUrl'
            return Yii::app()->user->returnUrl;
        } else {
            $errorResult = 'No se pudo iniciar sesion con su cuenta.';
            return false;
        }

    }
}
