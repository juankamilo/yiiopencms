<?php

Yii::import('application.models._common.CommonLoginForm');

class LoginForm extends CommonLoginForm
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