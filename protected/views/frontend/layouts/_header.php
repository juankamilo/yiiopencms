<?php
//Llamamos el widget para las traducciones
$json = CJSON::decode($this->widget('application.components.frontend.widgets.LanguageSelector', array('type'=>'menu2'),true));

$this->widget('bootstrap.widgets.TbNavbar',array(
    'items'=>array(
        array(
            'class'=>'cms.widgets.CmsMenuWidget',
            'name'=>'navigation',
            'type'=>'',
            'items'=>array(
                array('label'=>Yii::t('app', 'Contact'), 'url'=>array('/site/contact')),
                $json,
            ),
        ),
    ),
));
