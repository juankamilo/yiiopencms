<?php

Yii::import('application.models._common.CommonUsuarios');

class Usuarios extends CommonUsuarios
{
    /**
     * Returns the static model of the specified AR class.
     * @return User the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
} 