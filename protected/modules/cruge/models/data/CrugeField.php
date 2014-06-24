<?php
/**
 * CrugeField
 *
 * @property integer $idfield
 * @property string $fieldname
 * @property string $longname
 * @property integer $position
 * @property integer $required
 * @property integer $fieldtype
 * @property integer $fieldsize
 * @property integer $maxlength
 * @property integer $showinreports  si este campo es visible en listas de usuario del administrador
 * @property string $useregexp    expresion regular, dejar en blanco si no se usa.
 * @property string $useregexpmsg    mensaje cuando la expresion regular falla
 * @property string $predetvalue    valor predeterminado, usado ademas para llenar listas de opcion
 *
 * @uses CActiveRecord
 * @author: Christian Salazar H. <christiansalazarh@gmail.com> @salazarchris74
 * @license protected/modules/cruge/LICENSE
 */
class CrugeField extends CActiveRecord
    implements ICrugeField
    //,IModelErrorReport
{


    private $_value;
    private $_errors;

    /*
        debido a que varios atributos aqui son sensibles los espacios entonces
        se les hara trim a todos.
    */
    public function onBeforeValidate($event)
    {
        foreach ($this->getIterator() as $atributo => $valor) {
            $this[$atributo] = trim($valor);
        }
    }


    /*
        devuelve un objeto que implementa a ICrugeField
    */
    public static function loadModel($id)
    {
        return self::model()->findByPk($id);
    }

    public static function loadModelByName($name)
    {
        return self::model()->findByAttributes(array('fieldname' => $name));
    }

    /* entrega un array con los nombres de los atributos clave para orden,
        colocar de primero el primaryKey
    */
    public static function getSortFieldNames()
    {
        return array('fieldname', 'longname', 'required');
    }

    public function getRequiredName()
    {
        if ($this->required == 1) {
            return CrugeTranslator::t("Si");
        }
        return CrugeTranslator::t("");
    }

    /**
    devuelve un array de objetos que implementan a ICrugeField
     */
    public static function listModels()
    {
        return self::model()->findAllByAttributes(array(), array('order' => 'position ASC'));
    }

    public function setFieldValue($value)
    {
        $this->_value = $value;
    }

    public function getFieldValue()
    {
        return $this->_value;
    }

    /*
        pregunta si este campo es visible en listas de usuario del administrador
    */
    public function isVisibleInAdminList()
    {
        return $this->showinreports == 1;
    }

    /*
        hace una validacion de este campo
    */
    public function validateField()
    {

        $validateResult = true;
        $_val = trim($this->getFieldValue());

        if (($_val == "") && ($this->required != 0)) {
            $validateResult = false;
            $this->addError(
                $this->fieldname
                ,
                CrugeTranslator::t("este campo es requerido") . ". [" . $this->longname . "]"
            );
        }

		if($this->maxlength != -1)
        if (strlen($_val) > $this->maxlength) {
            $validateResult = false;
            $this->addError(
                $this->fieldname
                ,
                CrugeTranslator::t("el tamano maximo permitido es")
                    . $this->maxlength . " " . CrugeTranslator::t("caracteres o digitos")
                    . ". [" . $this->longname . "]"
            );
        }


        if ((trim($this->useregexp) != "") && (trim($this->getFieldValue()) != "")) {
            // aplica regexp segun el usuario haya configurado el campo
            if (preg_match("/" . trim($this->useregexp) . "/", $this->getFieldValue())) {
                // todo bien
            } else {
                $validateResult = false;
                $this->addError(
                    $this->fieldname
                    ,
                    CrugeTranslator::t($this->useregexpmsg)
                );
            }
        }

        return $validateResult;
    }

    /**
    retorna el nombre de la tabla
     */
    public function tableName()
    {
        return CrugeUtil::getTableName("field");
    }

    /*
        devuelve "el valor" del indice primario
    */
    public function getPrimaryKey()
    {
        return $this->idfield;
    }


    /**
     * Returns the static model of the specified AR class.
     * @return CrugeField the static model class
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

            array('fieldname', 'length', 'max' => 20),
            array('longname', 'length', 'max' => 50),
            array('useregexp', 'length', 'max' => 512),
            array('useregexpmsg', 'length', 'max' => 512),
            array('predetvalue', 'length', 'max' => 4096),
            array(
                'fieldname',
                'match'
            ,
                'pattern' => '/^([a-zA-Z]{3,20})$/'
            ,
                'message' => CrugeTranslator::t("solo use de 3 a 20 letras (a-z), sin espacios")
            ),
            array(
                'position, required, fieldtype, fieldsize, maxlength, showinreports'
            ,
                'numerical',
                'integerOnly' => true
            ),
            array(
                'fieldname, longname, fieldtype, fieldsize, maxlength'
            ,
                'required'
            ),
            array('fieldname', 'unique'),
            array('position', 'numerical', 'min' => 0, 'max' => 99),
            array('fieldsize', 'numerical', 'min' => 1, 'max' => 100),
            array('maxlength', 'numerical', 'min' => -1, /*'max' => 512*/),
            array('predetvalue', 'safe'),
            array('idfield, fieldname, longname, position, required, fieldtype', 'safe', 'on' => 'search'),
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
            'idfield' => 'Idfield',
            'fieldname' => ucwords(CrugeTranslator::t('Nombre Interno')),
            'longname' => ucwords(CrugeTranslator::t('Nombre Publico')),
            'position' => ucwords(CrugeTranslator::t('Posicion')),
            'required' => ucwords(CrugeTranslator::t('Requerido')),
            'fieldtype' => ucwords(CrugeTranslator::t('Tipo')),
            'fieldsize' => ucwords(CrugeTranslator::t('Ancho Caracteres')),
            'maxlength' => ucwords(CrugeTranslator::t('Longitud Maxima')),
            'showinreports' => ucwords(CrugeTranslator::t('Ver en Reportes')),
            'useregexp' => ucwords(CrugeTranslator::t('Expresion Regular')),
            'useregexpmsg' => ucwords(CrugeTranslator::t('Mensaje de error')),
            'predetvalue' => ucwords(CrugeTranslator::t('Valor Predeterminado / Opciones de Lista')),
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

        $criteria->compare('idfield', $this->idfield);
        $criteria->compare('fieldname', $this->fieldname, true);
        $criteria->compare('longname', $this->longname, true);
        $criteria->compare('position', $this->position);
        $criteria->compare('required', $this->required);
        $criteria->compare('fieldtype', $this->fieldtype);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
            'sort' => array(
                'defaultOrder' => array('position' => false),
            ),
        ));
    }
}
