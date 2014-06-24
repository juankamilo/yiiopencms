<?php
/** ICrugeSession

interfaz para inyectarle al ORDBM seleccionado los metodos a implementar

@author: Christian Salazar H. <christiansalazarh@gmail.com> @salazarchris74
@license protected/modules/cruge/LICENSE
 */
interface ICrugeSession
{
    /**
    @returns CrugeSession una instancia del modelo hallada por su IDSESSION
     */
    public static function loadModel($id);

    /*
        @returns CrugeSession instancia de la sesion mas reciente hallada para este usuario
    */
    public static function findLast($iduser);

    /*
        @returns nueva instancia de CrugeSession
    */
    public static function create($iduser, $durationMins);

    /** que hacer cuando la sesion es reutilizada
    @returns void.
     */
    public function onReusage();

    /*	almacena la sesion, que puede ser nueva o reutilizada
        @returns boolean. false=causa que la sesion no se asigne.
    */
    public function store();

    /**
    @returns Boolean indicando que la sesion es valida para ser utilizada y asignada
     */
    public function validateSession();

    /*
        @returns boolean true indicando que la sesion ha expirado
    */
    public function isSessionExpired();

    /*
        @returns VOID
    */
    public function logout();

    /*
        @returns VOID
    */
    public function expiresession();

    /**
    @returns string Nombre del usuario (username) de esta sesion
     */
    public function getSessionName();

    public function tableName();

    public function getPrimaryKey();
}
