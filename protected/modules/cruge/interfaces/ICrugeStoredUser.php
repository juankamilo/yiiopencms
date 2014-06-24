<?php
/*	ICrugeStoredUser

	permite abstraer al sistema de un ORDBM que no sea CActiveRecord, para inyectarle
	los metodos requeridos.
	
	@author: Christian Salazar H. <christiansalazarh@gmail.com> @salazarchris74
	@license protected/modules/cruge/LICENSE
*/
interface ICrugeStoredUser
{
    /* es un loadModel de uso multiple. $modo puede ser: 'iduser','username' o 'email' para
        indicar por cual campo se quiere cargar el modelo.
        @returns ICrugeStoredUser
    */
    public static function loadModel($id, $modo = 'iduser');

    /*
        filters es un array 'attribute'=>'value', que se usa para que se entreguen
        aquellos registros que cumplan con.
    */
    public static function listModels($param = array());

    /* entrega un array con los nombres de los atributos clave para orden,
        colocar de primero el primaryKey
    */
    public static function getSortFieldNames();

    /**
    @retuns string nombre de usuario (para login).
     */
    public function getUserName();

    public function getEmail();


    /*
        devuelve el nombre del atributo state, para efectos de listas y reportes,
        es decir para que no muestre un codigo sino el texto
    */
    public function getStateName();

    public function tableName();

    public function getPrimaryKey();

    /*
        recibe un array de instancias de ICrugeField previamente cargada de valores
    */
    public function setFields($arFields);

    /*
        devuelve una lista de campos previamente establecidos con setFields
    */
    public function getFields();

    /*
        debe redefinirse el metodo validate(), para que incluya la validacion
        de cada uno de sus campos extra
    */
    public function validate($attributes = null, $clearErrors = true);

    /*
        debe redefinirse para que incluya a cada uno de sus campos extra
    */
    public function setAttributes($values, $safeOnly = true);

    /*
        debe redefinirse para que guarde el valor de cada uno de sus campos extra
        @see saveFields()
    */
    public function save($runValidation = true, $attributes = null);

    /*
        debe llamarse despues de parent::save() para que guarde
        cada uno de sus campos
    */
    public function saveFields();
}	
	
