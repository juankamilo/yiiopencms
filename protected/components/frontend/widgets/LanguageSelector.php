<?php
class LanguageSelector extends CWidget
{
    public function run()
    {
        $currentLang = Yii::app()->language;
        $lang = Yii::app()->params->languages;
        $this->render('languageSelector', array('currentLang' => $currentLang, 'lang'=>$lang));
    }
}
?>