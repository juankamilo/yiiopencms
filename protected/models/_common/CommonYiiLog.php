<?php

Yii::import('application.models._base.BaseYiiLog');

class CommonYiiLog extends BaseYiiLog
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
