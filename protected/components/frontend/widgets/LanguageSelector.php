<?php
class LanguageSelector extends CWidget
{
    //Define el Tipo de menu
    public $type;

    public function run()
    {
        $currentLang = Yii::app()->language;
        $lang = Yii::app()->params->languages;
        $this->render('languageSelector', array('currentLang' => $currentLang, 'lang'=>$lang, 'type'=>$this->type));
    }
}
?>