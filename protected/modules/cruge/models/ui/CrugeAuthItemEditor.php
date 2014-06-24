<?php
/**
CrugeAuthItemEditor

Modelo para editar un CAuthItem.

@author: Christian Salazar H. <christiansalazarh@gmail.com> @salazarchris74
@license protected/modules/cruge/LICENSE
 */
class CrugeAuthItemEditor extends CFormModel
{

    public $name;
    public $businessRule;
    public $description;
    public $categoria;

    public $isNewRecord; // es establecido a true o false directamente desde UiController
    public $deleteConfirmation = false;

    public function onBeforeValidate($event)
    {
        foreach ($this->getIterator() as $atributo => $valor) {
            $this[$atributo] = trim($valor);
        }
    }

    public function rules()
    {
        return array(
            array('name', 'required',),
            array(
                'name',
                'match'
            ,
                'pattern' => '/^([a-zA-Z_-]{3,64})$/'
            ,
                'message' => CrugeTranslator::t(
                    "solo use de 3 a 20 letras (a-z) sin espacios, puede usar caracteres: _-"
                )
            ,
                'on' => 'insert, update'
            ),
            array('name', 'validar_duplicado', 'on' => 'insert'),
            array(
                'description',
                'match'
            ,
                'pattern' => '/^([a-zA-Z0-9.,+\-\_ \{\}\:áéíóúÁÉÍÓÚñÑ]{1,100})$/'
            ,
                'message' => CrugeTranslator::t(
                    "solo use letras A-Z, espacio, digitos o los simbolos .,+-_{}:"
                )
            ),
            array('deleteConfirmation', 'required', 'on' => 'delete'),
            array(
                'deleteConfirmation',
                'compare',
                'compareValue' => '1'
            ,
                'on' => 'delete',
                'message' => CrugeTranslator::t("por favor confirme con la casilla de chequeo")
            ),
            array('businessRule', 'length', 'max' => 512),
        );
    }

    public function validar_duplicado($attr, $param)
    {
        if (Yii::app()->user->rbac->getAuthItem($this[$attr]) !== null) {
            $this->addError($attr, CrugeTranslator::t("este nombre ya esta en uso"));
        }
    }

    public function attributeLabels()
    {
        return array(
            'name' => ucfirst(CrugeTranslator::t('nombre')),
            'description' => ucfirst(CrugeTranslator::t('descripcion corta')),
            'businessRule' => ucfirst(CrugeTranslator::t('regla de negocio')),
            'deleteConfirmation' => ucfirst(CrugeTranslator::t('confirmar eliminacion')),
        );
    }
}
