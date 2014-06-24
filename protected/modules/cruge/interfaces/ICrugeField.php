<?php
/** ICrugeField

interfaz para inyectarle al ORDBM seleccionado los metodos a implementar relevante a campos
de perfil.

@author: Christian Salazar H. <christiansalazarh@gmail.com> @salazarchris74
@license protected/modules/cruge/LICENSE
 */
interface ICrugeField
{

    /*
        devuelve un objeto que implementa a ICrugeField
    */
    public static function loadModel($id);

    public static function loadModelByName($name);

    /**
    devuelve un array de objetos que implementan a ICrugeField
     */
    public static function listModels();

    /* entrega un array con los nombres de los atributos clave para orden,
        colocar de primero el primaryKey
    */
    public static function getSortFieldNames();

    public function getRequiredName();


    /*
        debido a que varios atributos aqui son sensibles los espacios entonces
        se les hara trim a todos en el evento de CModel::onBeforeValidate()
    */
    public function onBeforeValidate($event);

    /**
    retorna el nombre de la tabla
     */
    public function tableName();

    /*
        devuelve "el valor" del indice primario
    */
    public function getPrimaryKey();

    public function setFieldValue($value);

    public function getFieldValue();

    /*
        hace una validacion de este campo
    */
    public function validateField();

    /*
        pregunta si este campo es visible en listas de usuario del administrador
    */
    public function isVisibleInAdminList();
}
