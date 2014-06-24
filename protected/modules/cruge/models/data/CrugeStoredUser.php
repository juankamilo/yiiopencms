<?php
/**
 * CrugeStoredUser
 *
 *  Modelo que realiza la persistencia de components.CrugeUser
 *
 * @property integer $iduser
 * @property string $username
 * @property string $email
 * @property string $password
 * @property string $authkey
 * @property integer $state
 * @property integer $totalsessioncounter
 * @property integer $currentsessioncounter
 * @property string $regdate    fecha de registro
 * @property string $actdate    fecha de activacion
 * @property string $logondate    ultimo login exitoso
 * @author: Christian Salazar H. <christiansalazarh@gmail.com> @salazarchris74
 * @license protected/modules/cruge/LICENSE
 */
class CrugeStoredUser extends CActiveRecord implements ICrugeStoredUser
{
    public $_fields = array();
    public $deleteConfirmation; // required on 'delete'
    public $newPassword; // declararlo 'safe'

    // terminos y condiciones, caso registration,
    public $terminosYCondiciones;
    public $verifyCode;

    // establecer a true si se quiere saltar la validacion de captcha.
    // ver acerca de: cruge\components\CrugeUserManager.php::createBlankUser
    public $bypassCaptcha;

	public function getCustomFieldValue($fieldname, $defValue=""){
		$field = $this->getCustomField($fieldname);
		if($field != null)
			return $field->getFieldValue();
		return $defValue;
	}

	public function getCustomField($fieldname){
		foreach($this->getFields() as $obj)
			if($fieldname == $obj->fieldname)
				return $obj;
		return null;
	}

	public function getUserDescription($boolLoadUserFields=false, $sep=','){
		$fieldNames = CrugeUtil::config()->userDescriptionFieldsArray;
		$tmp = "";
		if(in_array("username",$fieldNames))
			$tmp .= $sep.$this->username;
		if(in_array("email",$fieldNames))
			$tmp .= $sep.$this->email;
		if($fieldNames != null){
			if($boolLoadUserFields == true)
				$this->setFields(
					CrugeFactory::get()->getICrugeFieldListModels($this));
			foreach($fieldNames as $fname)
			if(($fname != "username") && ($fname != "email")){
				$tmp .= $sep.$this->getCustomFieldValue($fname,$fname);
			}
		}
		if($tmp == "")
			$tmp = $this->getUsername();
		return ltrim($tmp,$sep." ");
	}

    /* es un loadModel de uso multiple. $modo puede ser: 'iduser','username' o 'email' para
        indicar por cual campo se quiere cargar el modelo.
        @returns ICrugeStoredUser
    */
    public static function loadModel($id, $modo = 'iduser')
    {
        return self::model()->findByAttributes(array($modo => $id));
    }

    /* entrega un array con los nombres de los atributos clave para orden, de primero el userid */
    public static function getSortFieldNames()
    {
        return array('iduser', 'username', 'email', 'state', 'logondate');
    }

    public function getStateName()
    {
        return Yii::app()->user->um->getStateName($this->state);
    }

    /*
        recibe un array de instancias de ICrugeField previamente cargada de valores
    */
    public function setFields($arFields)
    {
        $this->_fields = $arFields;
    }

    public function getFields()
    {
        if ($this->_fields == null) {
            return array();
        }
        return $this->_fields;
    }

    public function setAttributes($values, $safeOnly = true)
    {

        if (count($this->getFields()) > 0) {
            $test = __CLASS__ . ".setAttributes:\n";
            foreach ($values as $k => $v) {
                $test .= "[{$k}={$v}]\n";
            }
            $test .= "\nparse field values:\n";
            foreach ($values as $fieldName => $value) {
                $test .= "{$fieldName}...";

                $boolFound = false;
                foreach ($this->getFields() as $f) {
                    if ($f->fieldname == $fieldName) {
                        $test .= " found. setfieldvalue:[{$value}]\n";
                        $f->setFieldValue($value);
                        $boolFound = true;
                        break;
                    }
                }
                if ($boolFound == false) {
                    $test .= " [not found]\n";
                }
            }
            Yii::log($test, "info");
        }

        parent::setAttributes($values);
    }

    public function validate($attributes = null, $clearErrors = true)
    {
		// si el metodo de autenticacion es solo email, y, username es blanco
		// se genera uno automaticamente:
		if($this->scenario == 'insert'){
		$declared_authmodes = CrugeUtil::config()->availableAuthModes;
		if(count($declared_authmodes == 1)){
			if(($declared_authmodes[0] == 'email') && ($this->username=='')){
				$um = new CrugeUserManager();
				$this->username = $um->generateNewUserName($this->email);
			}else
			if(($declared_authmodes[0] == 'username') && ($this->email=='')){
				$this->email = $this->username.'@noemail.local';
			}
		}}

        // realiza la validacion normal sobre los atributos de este modelo
        $validateResult = parent::validate();

        // ahora realiza la validacion sobre aquellos campos personalizados
        // y copia todos los errores al objeto mayor ($this)
        //
        foreach ($this->getFields() as $f) {
            if ($f->validateField() == false) {
                $this->addErrors($f->getErrors());
                $validateResult = false;
            }
        }

        return $validateResult;
    }

    public function save($runValidation = true, $attributes = null)
    {
        Yii::log(__METHOD__, "info");
        if ($this->hasErrors()) {
            Yii::log(__METHOD__ . " return false, has errors.", "info");
            return false;
        }
        $ok = parent::save($runValidation,$attributes);
		$this->saveFields();
        Yii::log(__METHOD__ . " returns: [" . $ok . "]", "info");
        return $ok;
    }

    public function saveFields()
    {
        foreach ($this->getFields() as $f) {
            // buscar el objeto ICrugeFieldValue, darle valores y guardarlo
            $crugeFieldValueInst = Yii::app()->user->um->loadICrugeFieldValue($this, $f);
            $boolOk = false;
            if ($crugeFieldValueInst != null) {
                $crugeFieldValueInst->value = $f->getFieldValue();
                $boolOk = $crugeFieldValueInst->save();
            }
            Yii::log(
                "\n" . __METHOD__ . " \nfieldname='" . $f->fieldname . "'\nfieldvalue='" . $f->getFieldValue()
                    . "'\n boolOk=[" . $boolOk . "]\ncrugeFieldValueInst=[" . ($crugeFieldValueInst == null ? 'null' : 'not null') . "]\n\n",
                "info"
            );
        }
    }

    /**
    @retuns string nombre de usuario (para login).
     */
    public function getUserName()
    {
        return $this->username;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function tableName()
    {
        return CrugeUtil::getTableName('user');
    }

    public function getPrimaryKey()
    {
        return $this->iduser;
    }

    public static function listModels($param = array())
    {
        return self::model()->findAllByAttributes($param);
    }

    public function getUpdateUrl()
    {
        return 'index.php?r=test' . $this->getPrimaryKey();
    }


    /**
     * Returns the static model of the specified AR class.
     * @return CrugeStoredUser the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }


    /**  hay un escenario llamado 'internal', que es puesto por CrugeUserManager::save()
     *   para poder guardar atributos especificos sin ser afectado por las reglas para formularios
     *
     *
     *
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array(
                'username',
                'match',
                'pattern' => '/^[a-zA-Z0-9\_\-\.]{3,45}$/'
            ,
                'message' => CrugeTranslator::t('logon', 'Invalid username')
            ),
            array('username,email', 'required'),
            array('newPassword', 'safe', 'on' => 'update'),
            array('newPassword', 'required', 'on' => 'insert, manualcreate'),
            array('newPassword', 'length', 'min' => 6, 'max' => 20),
            array(
                'newPassword',
                'match'
            ,
                'pattern' => '/^[a-zA-Z0-9@#$%\_\-\.]{6,20}$/'
            ,
                'message' => CrugeTranslator::t(
                    'logon',
                    'Password may contain numbers or symbols ({symbols}) and between {min} and {max} characters',
                    array('{symbols}' => '@#$%', '{min}' => 6, '{max}' => 20)
                )
            ),
            array('username, password', 'length', 'max' => 64),
            array('state', 'numerical', 'integerOnly' => true),
            array('authkey', 'length', 'max' => 100),
            array('email', 'email'),
            array('email', 'length', 'max' => 100),
            array('username,email', 'validate_unique'),
            array('deleteConfirmation', 'required', 'on' => 'delete'),
            array(
                'deleteConfirmation',
                'compare',
                'compareValue' => '1'
            ,
                'on' => 'delete',
                'message' => CrugeTranslator::t('logon', 'Please, confirm checking the checkbox')
            ),
            array(
                'terminosYCondiciones',
                'required'
            ,
                'requiredValue' => '1'
            ,
                'on' => 'insert'
            ,
                'message' => CrugeTranslator::t('logon', 'Please, check if you understand and accept the terms of use'),
            ),
            array(
                'verifyCode',
                $this->_getCaptchaRule(),
                'on' => 'insert',
                'message' => CrugeTranslator::t('logon', 'Security code is mandatory'),
            ),
            array(
                'verifyCode',
                'captcha',
                'on' => 'insert',
                'allowEmpty' => true,
                'message' => CrugeTranslator::t('logon', 'Security code is invalid'),
            ),
            array('iduser, username, email, state, logondate', 'safe', 'on' => 'search'),

        );
    }

    /**
    al establecer $_crugeStoredUser->bypassCaptcha = true;
    entonces el captcha no sera tomado en cuenta.

    esta funcion es util cuando se quiere crear un nuevo usuario de cruge por la via del API.
     */
    private function _getCaptchaRule()
    {
        if (Yii::app()->user->um->getDefaultSystem()->getn('registerusingcaptcha') == 1) {
            // el administrador decidio pedir captcha para registrar los usuarios,
            // 	pero quiza el flag bypassCaptcha este activo.
            if ($this->bypassCaptcha == true) {
                // captcha es requerido, pero sera no sera tomado en cuenta.
                $this->verifyCode = null;
                return 'safe';
            } else {
                return 'required';
            } // captcha es requerido
        } else {
            // el administrador ha deshabilitado el uso de captcha.
            $this->verifyCode = null;
            return 'safe';
        }
    }


    public function validate_unique($att, $params)
    {
        $model = self::model()->findByAttributes(array($att => $this[$att]));
        if ($model != null) {
            $duptext = CrugeTranslator::t('logon', '\'{attribute}\' already in use', array('attribute' => $att));
            if ($this->scenario == 'insert') {
                $this->addError($att, $duptext);
                return;
            }
            if ($this->scenario == 'update') {
                if ($this->iduser != $model->iduser) {
                    $this->addError($att, $duptext);
                }
                return;
            }
        }
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'sessions' => array(self::HAS_MANY, 'crugesession', 'iduser'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'idusuario' => ucfirst(CrugeTranslator::t('usuario#')),
            'username' => ucfirst(CrugeTranslator::t('username')),
            'email' => ucfirst(CrugeTranslator::t('correo')),
            'password' => ucfirst(CrugeTranslator::t('clave')),
            'authkey' => ucfirst(CrugeTranslator::t('llave de autenticacion')),
            'state' => ucfirst(CrugeTranslator::t('estado de la cuenta')),
            'newPassword' => ucfirst(CrugeTranslator::t('clave')),
            'deleteConfirmation' => ucfirst(CrugeTranslator::t('confirmar eliminacion')),
            'regdate' => ucfirst(CrugeTranslator::t('registrado')),
            'actdate' => ucfirst(CrugeTranslator::t('activado')),
            'logondate' => ucfirst(CrugeTranslator::t('ultimo acceso')),
            'terminosYCondiciones' => ucfirst(CrugeTranslator::t('comprendo y acepto, por favor registrarme')),
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria = new CDbCriteria;

        $criteria->compare('iduser', $this->iduser);
        $criteria->compare('username', $this->username, true);
        $criteria->compare('email', $this->email, true);
        $criteria->compare('state', $this->state);
        $criteria->compare('logondate', $this->logondate);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
            'sort' => array(
                'defaultOrder' => array('iduser' => true),
            ),
        ));
    }


	public function searchByAuthItem($authItemName, $pageSize=20, $defaultOrder=null){
        $criteria = new CDbCriteria;
		$criteria->distinct = true;
		$authMan = new CrugeAuthManager();
		$table_assign = $authMan->getTableName("authassignment");
		$criteria->join = "left join ".$table_assign." ASG "
			."on ASG.userid = t.iduser";
		$criteria->compare("ASG.itemname",$authItemName);
		// extra optionals, for filtering:
        $criteria->compare('username', $this->username, true);
        $criteria->compare('email', $this->email, true);
        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
			'pagination' => array(
				'pageSize' => $pageSize,
			),
            'sort' => array(
                'defaultOrder' => (($defaultOrder==null) ? 
					array('username' => false) : $defaultOrder),
            ),
        ));
	}

}
