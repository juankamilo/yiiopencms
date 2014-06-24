<?php /**
CrugeUser

Implementa la autenticidad de la identidad que quiere usar el sistema, antes por defecto
provisto por CUserIdentity por defecto como parte del Yii Core. Usado por CrugeWebUser.

Aqui no se inicia sesion. Solo se autentica al usuario.

el principal consumidor de esta clase es CrugeLogon

Principalmente, el metodo matriz de esta clase es proveer a authenticate(), en ingles debido a
la derivacion en la herencia de su clase mayor.  Cuando authenticate() es invocado
se aplica una validacion del usuario segun el modo en que se pretende autenticar,

Esta clase es una de las primeras a configurarse en una instalacion de Cruge, de modo
que hay que sustiuir el uso de UserIdentity (provista por defecto) por CrugeUser.

@see CrugeLogon
@see CrugeAuthDefault
@see ICrugeAuth

@author: Christian Salazar H. <christiansalazarh@gmail.com> @salazarchris74
@license protected/modules/cruge/LICENSE
 */
class CrugeUser extends CBaseUserIdentity implements IUserIdentity
{

    public $username;
    public $password;
    public $authmode; //alguno de los authName definidos en cada clase modele.auth.CrugeAuthXXXX()

    private $_lastErrorDescr = "";
    public $storeduser; // ofrece acceso al usuario autenticado.  ICrugeStoredUser

    /* es un metodo que no pertenece a la interfaz IUserIdentity pero
        que da acceso al ICrugeUser desde la aplicacion usando:
            Yii::app()->user->user
        o lo que es lo mismo:
            Yii::app()->user->getUser()

        @returns instancia de ICrugeStoredUser o null.
    */
    public function getUser()
    {
        return $this->storeduser;
    }


    public function hasErrors()
    {
        return ($this->errorCode !== self::ERROR_NONE);
    }

    /**    Contructor

    Normalmente este contructor es invocado en una Modelo de autenticacion que soporta
    a un formulario de Login. Por ejemplo: application.modules.LoginForm, en la app
    que Yii Framework provee por defecto.

    authmode permite indicar con cual metodo de autenticacion se pretende iniciar sesion
    los metodos dependen del sistema al cual el formulario de login pertenece.
    ->si es null: se comprobaran todos los metodos que el sistema tenga configurados

    idsystem indica a que sistema en el cual estaremos trabajando.

     */
    public function __construct($username, $password, $authmode = 'default')
    {
        $this->username = $username;
        $this->password = $password;
        $this->authmode = $authmode;
        $this->storeduser = null;

        Yii::log(__METHOD__ . "\n", "info");
    }


    public function authenticate()
    {
        Yii::log(__METHOD__ . "\n", "info");

        $this->_lastErrorDescr = "";
        // se ha solicitado un metodo de autenticacion (ex: 'facebook' o 'default')
        //
        Yii::log(__METHOD__ . "\nauthmode es:\n" . $this->authmode, "info");
        $auth = Yii::app()->user->um->getAuthenticationFilterByName($this->authmode);

        // auth es una instancia de un metodo de autenticacion

        if ($this->_performAuth($auth) == true) {
            Yii::log(__METHOD__ . "\n_performAuth es true\n", "info");
            return true;
        } else {

            switch ($this->errorCode) {
                case self::ERROR_USERNAME_INVALID:
                    $this->_lastErrorDescr = "usuario o correo invalido";
                    break;
                case self::ERROR_PASSWORD_INVALID:
                    $this->_lastErrorDescr = "clave invalida";
                    break;
                default:
                    $this->_lastErrorDescr = "error desconocido";
            }
            Yii::log(__METHOD__ . "\n_performAuth es false.\n" . $this->_lastErrorDescr, "info");
        }
        return false;
    }

    /** realiza la autenticacion en el metodo seleccionado

    @return Boolean, indicando si pudo o no pudo iniciar
     */
    private function _performAuth(ICrugeAuth $modAuth)
    {

        Yii::log(__METHOD__ . "\n", "info");

        $modAuth->setParameters($this->username, $this->password /* , TODO: pasarle opciones*/);
        $boolRet = $modAuth->authenticate();
        $this->errorCode = $modAuth->errorCode; // se vale de CBaseUserIdentity
        $this->storeduser = $modAuth->getUser();
        return $boolRet;
    }


    /** getId

    solo retorna el identificador obtenido con authenticate, nada mas

    @returns identificador de ICrugeStoredUser o cero
     */
    public function getId()
    {
        if ($this->storeduser != null) {
            return $this->storeduser->getPrimaryKey();
        }
        return 0;
    }

    public function getName()
    {
        if ($this->storeduser != null) {
            return $this->storeduser->getUserName();
        }
        return CrugeTranslator::t("invitado");
    }

    public function getLastError()
    {
        return CrugeTranslator::t($this->_lastErrorDescr);
    }

}
