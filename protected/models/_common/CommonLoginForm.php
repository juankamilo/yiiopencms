<?php

Yii::import('application.models._base.BaseLoginForm');

class CommonLoginForm extends BaseLoginForm
{
    /**
     * Returns the static model of the specified AR class.
     * @return CommonUser the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

} 