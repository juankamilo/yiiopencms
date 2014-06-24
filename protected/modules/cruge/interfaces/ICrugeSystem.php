<?php
/** ICrugeSystem

	@author: Christian Salazar H. <christiansalazarh@gmail.com> @salazarchris74
	@license protected/modules/cruge/LICENSE
 */
interface ICrugeSystem
{

    /*
        entrega el valor string de un atributo
    */
    public function get($attribute);

    /*
        entrega el valor numerico de un atributo
    */
    public function getn($attribute);

    /*
        encuentra un sistema por su nombre
    */
    public static function findSystem($systemName);

    /*
        entrega un array de ICrugeSystem
    */
    public static function listModels();

    /*
        retorna el nombre corto de un sistema
    */
    public function getShortName();

    public function getLargeName();

    /*
        @returns boolean true si el sistema esta disponible para iniciar sesion
    */
    public function isAvailableForLogin();

    public function tableName();

    public function getPrimaryKey();

}
