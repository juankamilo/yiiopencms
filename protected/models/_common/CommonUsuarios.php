<?php

Yii::import('application.models._base.BaseUsuarios');

class CommonUsuarios extends BaseUsuarios
{
    /**
     * Returns the static model of the specified AR class.
     * @return CommonUser the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

     /*
         * Revisa si el password es correcto
         * @param string del pass a validar
         * @return boolean si el password es valido
         */
        public function validatePassword($password)
        {
            return $this->hashPassword($password, $this->usu_session) === $this->usu_clave;
        }
        
        /*
         * Genera el hash del password
         * @param string del password
         * @param string de la session "salt"
         * @return string con el hash
         */
        public function hashPassword($password,$salt)
        {
            return md5($salt.$password);
        }
        
        /*
         * Generates a salt that can be used to generate a password hash.
         * @return string the salt
         */
        public function generateSalt()
        {
            return uniqid('',true);
        }
} 