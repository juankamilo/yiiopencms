<?php
/** CrugeFieldValue
 * This is the model class for table "cruge_fieldvalue".
 *
 * The followings are the available columns in table 'cruge_fieldvalue':
 * @property integer $idfieldvalue
 * @property integer $iduser
 * @property integer $idfield
 * @property string $value
 * @author: Christian Salazar H. <christiansalazarh@gmail.com> @salazarchris74
 * @license protected/modules/cruge/LICENSE
 */
class CrugeFieldValue extends CActiveRecord
{
    /*
        devuelve un objeto que implementa a ICrugeFieldValue
    */
    public static function loadModel($id)
    {
        return self::model()->findByPk($id);
    }

    public static function loadModelBy($iduser, $idfield)
    {
        return self::model()->findByAttributes(array('iduser' => $iduser, 'idfield' => $idfield));
    }

    /**
     * loadByValue
     *    busca un FieldValue por su valor y campo
     * @param mixed $idfield
     * @param mixed $value
     * @static
     * @access public
     * @return instancia de CrugeFieldValue o null
     */
    public static function loadByValue($idfield, $value)
    {
        // para ocasiones podria ser util un indice en el modelo
        // para esta busqueda.
        $filtro = array(
            ":idfield" => $idfield,
            ":value" => $value
        );
        foreach (self::model()->findAll(
                     array(
                         "condition" =>
                         "idfield = :idfield AND value = :value",
                         "params" => $filtro
                     )
                 ) as $obj) {
            return $obj;
        }
        return null;
    }

    /**
    devuelve un array de objetos que implementan a ICrugeFieldValue
     */
    public static function listModels($iduser)
    {
        return self::model()->findAllByAttributes(array('iduser' => $iduser));
    }


    /**
    retorna el nombre de la tabla
     */
    public function tableName()
    {
        return CrugeUtil::getTableName("fieldvalue");
    }

    /*
        devuelve "el valor" del indice primario
    */
    public function getPrimaryKey()
    {
        return $this->idfieldvalue;
    }


    /**
     * Returns the static model of the specified AR class.
     * @return CrugeFieldValue the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('iduser, idfield', 'required'),
            array('iduser, idfield', 'numerical', 'integerOnly' => true),
            array('value', 'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('idfieldvalue, iduser, idfield, value', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array();
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'idfieldvalue' => 'Idfieldvalue',
            'iduser' => 'Iduser',
            'idfield' => 'Idfield',
            'value' => 'Value',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria = new CDbCriteria;

        $criteria->compare('idfieldvalue', $this->idfieldvalue);
        $criteria->compare('iduser', $this->iduser);
        $criteria->compare('idfield', $this->idfield);
        $criteria->compare('value', $this->value, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }
}
