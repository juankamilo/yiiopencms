<?php
/** ICrugeSessionFilter

Se podria ver a esta interfaz como un implementador de llave que te pregunta:
"usuario xxx quiere entrar a un sistema, le damos una sesion ? cual ?"

Interfaz para las clases que pretendan hacer de filtro de sesion.
por defecto el modulo usa a: application.modules.cruge.filters.DefaultSessionFilter
pero se puede extender la funcionalidad por defecto para incorporar otros controles
que no se hayan pensado aqui.

quien consume esta interfaz ?  CrugeUser::authenticate, tras una identificacion exitosa.

@author: Christian Salazar H. <christiansalazarh@gmail.com> @salazarchris74
@license protected/modules/cruge/LICENSE
 */
interface ICrugeSessionFilter
{

    /**
    @returns string, descripcion del error.
     */
    public function getLastErrorDescr();

    /**
    @erturns string, nombre corto de este filtro.
     */
    public function getName();

    /*  invocado por CrugeWebUser cuando un usuario solicita login()

        @returns ICrugeSession instancia.  nueva o reutilizada. o null
    */
    public function startSession(/*ICrugeStoredUser*/ $user, /*ICrugeSystem*/ $sys);

    /*
        implementa el almacen de la sesion creada por startSession
        @returns boolean true para indicar que se continue la autenticacion, false aborta
    */
    public function onStore(/*ICrugeSession*/ $model);

    /*
        evento lanzado por CrugeWebUser al momento de iniciar sesion
    */
    public function onLogin(/*ICrugeSession*/ $model);

    /*
        evento lanzado por CrugeWebUser al momento de cerrar sesion
    */
    public function onLogout(/*ICrugeSession*/ $model);

    /*
        evento lanzado por CrugeWebUser cuando detecta que una sesion ha expirado
    */
    public function onSessionExpired(/*ICrugeSession*/ $model);
}
