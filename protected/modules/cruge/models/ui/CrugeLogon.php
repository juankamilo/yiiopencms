<?php
/**
CrugeLogon

Modelo para el formulario de Login y Password Recovery

funciona bajo dos escenarios: 'login' y 'pwdrec'


CrugeLogon es un modelo (CFormModel) para el formulario de Login y Password Recovery
que aparte de validar que los datos de ambos fomularios esten correctos tambien
ayuda al proceso de llamar a Yii::app()->user->login mediante un metodo llamado login().

basicamente es como el modelo LoginForm que trae Yii por defecto.


@author: Christian Salazar H. <christiansalazarh@gmail.com> @salazarchris74
@license protected/modules/cruge/LICENSE
 */
class CrugeLogon extends CFormModel
{
    public $username;
    public $password;
    public $authMode; // este valor se le da en UiController::actionLogin
    public $verifyCode;
    public $rememberMe;
    private $_model;
    private $_identity;

    public function getModel()
    {
        return $this->_model;
    }

    public function rules()
    {
        return array(
            array('username', 'required'),
            array('username', 'length', 'max' => 45),
            array('username', 'user_exists_validator'),
            array('rememberMe', 'boolean', 'on' => 'login'),
            array('password', 'required', 'on' => 'login'),
            array('password', 'length', 'max' => 20, 'on' => 'login'),
            array(
                'verifyCode',
                'captcha',
            	'allowEmpty'=>(($this->scenario=='login') ? true : false),
                'on'=>'pwdrec'
            ),
            array('password', 'authenticate', 'on' => 'login'),
        );
    }

    public function user_exists_validator($arg, $param)
    {
        // carga el usuario por su username o su email,
        // ademas de cargar sus campos de perfil (flag=true)
        $this->_model = Yii::app()->user->um->loadUser($this[$arg], true);
        if ($this->_model == null) {
            $this->addError($arg, CrugeTranslator::t('logon', 'User not found'));
        }
    }

    public function authenticate($arg = 'password', $param = '')
    {
        Yii::log(__CLASS__ . "\nauthenticate()\n", "info");
        $this->_identity = Yii::app()->user->um->getNewCrugeUser($this->username, $this->password, $this->authMode);
        if (!$this->_identity->authenticate()) {
            $this->addError($arg, $this->_identity->getLastError());
        }
    }

    public function attributeLabels()
    {
        // la etiqueta $label cambiara depende de como este configuado el sistema
        //
        return array(
            'username' => $this->_getUsernameLabel(),
            'password' => ucfirst(CrugeTranslator::t('logon', 'Password') . ":"),
            'rememberMe' => ucfirst(CrugeTranslator::t('logon', 'Remember this machine') . ":"),
            'verifyCode' => ucfirst(CrugeTranslator::t('logon', 'Security code') . ":"),
        );
    }

    private function _getUsernameLabel()
    {
        $label = "";
        $sep = "";
        foreach (CrugeUtil::config()->availableAuthModes as $k => $v) {
            $label .= $sep . CrugeTranslator::t('logon', ucfirst(CrugeUtil::config()->availableAuthModes[$k]));
            $sep = " " . CrugeTranslator::t('logon', 'or') . " ";
        }
        $label .= ":";
        return $label;
    }

    public function login($booleanThrowException = true)
    {
        if ($this->_identity === null) {
            $this->_identity = Yii::app()->user->um->getNewCrugeUser($this->username, $this->password, $this->authMode);
            $this->_identity->authenticate();
        }
        if ($this->_identity->hasErrors() == false) {
			$duration=0;
			if($this->rememberMe){
				$sys = Yii::app()->user->um->getDefaultSystem();
				$duration = $sys->getn('sessionmaxdurationmins') * 60;
			}
			Yii::log(__METHOD__."\nintento iniciar sesion. duration=".$duration.", username=".$this->username,"info");
			if (!Yii::app()->user->login($this->_identity, $duration)) {
                if ($booleanThrowException == true) {
                    throw new CrugeException(Yii::app()->user->getLastError());
                }
                return false;
            }
            return true;
        } else {
            return false;
        }
    }
}
