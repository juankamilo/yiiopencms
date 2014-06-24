<?php
/**
ICrugeAuth

Esta interfaz es consumida por components.CrugeUser al momento de autenticar un usuario
mediante authenticate().

Una clase que implemente esta interfaz podr� cumplir con la funcionalidad de extender
la autenticacion hacia otros sistemas como Facebook, Twitter, OpenID,

es necesario extender la clase de CBaseUserIdentity debido a que esta clase
provee codigos de error estandar para la aplicacion Yii.

una declaracion estandar deber�a ser:
class CrugeAuthDemo extends CBaseUserIdentity implements ICrugeAuth { ... }


@author: Christian Salazar H. <christiansalazarh@gmail.com> @salazarchris74
@license protected/modules/cruge/LICENSE
 */
interface ICrugeAuth
{

    /*
        es un nombre clave para el metodo de autenticacion, usado en el config
        para hacer saber que metodos de autenticacion se van a implementar
    */
    public function authName();

    /*
        @returns Boolean true=login aceptado false=error de conexion.
    */
    public function setParameters($username, $password, $options = array());

    /*
        @returns instancia de ICrugeStoredUser hallado tras la autenticacion exitosa
    */
    public function getUser();
}
