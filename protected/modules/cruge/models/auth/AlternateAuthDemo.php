<?php
/*
	Esta es una clase de demostracion para que se conozca como crear metodos alternos de inicio de sesion.

	en esta clase se autenticara al usuario contra la lista de user y password definida en config/main asi:


	// EN CONFIG/MAIN LE INDICAS A CRUGE QUE USE ESTA CLASE 'authdemo':

		'cruge'=>array(
			'tableprefix'=>'cruge_',
			// 'availableAuthMethods'=>array('default'),
			'availableAuthMethods'=>array('authdemo'),
			...
			...
			(el string "authdemo" debe esta definido en la clase de autenticacion,
			este string es devuelto en la clase: AlternateAuthDemo.php )


 	@author: Christian Salazar H. <christiansalazarh@gmail.com> @salazarchris74
	@license protected/modules/cruge/LICENSE
*/
class AlternateAuthDemo extends CBaseUserIdentity implements ICrugeAuth
{

    private $username;
    private $password;
    private $options;

    private $_user;

    /**
    este nombre sera referenciado en config/main para hacerle saber a Cruge que use esta clase
    para autenticar:

    'availableAuthMethods'=>array('authdemo'),
     */
    public function authName()
    {
        return "authdemo";
    }

    /*	no confundir con un getUserName, esto es un getUser a nivel de instancia,
        debe retornar algun objeto que implemente a ICrugeStoredUser, por defecto se puede usar un
        objeto de clase CrugeStoredUser.

        @returns instancia de ICrugeStoredUser hallado tras la autenticacion exitosa
    */
    public function getUser()
    {
        return $this->_user;
    }

    /*
        recibe desde cruge parametros considerados como user y password, pueden no ser user y password a nivel
        conceptual..sino por ejemplo, cedula y clave, numerotarjeta y clave, etc.
    */
    public function setParameters($username, $password, $options = array())
    {
        $this->username = $username;
        $this->password = $password;
        $this->options = $options;
    }

    public function authenticate()
    {

        // en errorcode reporta el error generado
        //
        $this->errorCode = self::ERROR_USERNAME_INVALID;


        // retorna boolean, true si la autenticacion es exitosa
        //
        return $this->errorCode == self::ERROR_NONE;
    }
}

