<?php
/** ICrugeFieldValue

interfaz para inyectarle al ORDBM seleccionado los metodos a implementar relevante a campos
personalizados y el valor asignado a un usuario.

@author: Christian Salazar H. <christiansalazarh@gmail.com> @salazarchris74
@license protected/modules/cruge/LICENSE
 */
interface ICrugeFieldValue
{

    /*
        devuelve un objeto que implementa a ICrugeFieldValue
    */
    public static function loadModel($id);

    public static function loadModelBy($iduser, $idfield);

    public static function loadByValue($idfield, $value);

    /**
    devuelve un array de objetos que implementan a ICrugeFieldValue
     */
    public static function listModels($iduser);


    /**
    retorna el nombre de la tabla
     */
    public function tableName();

    /*
        devuelve "el valor" del indice primario
    */
    public function getPrimaryKey();

}
