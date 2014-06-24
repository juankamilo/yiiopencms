<?php

$current = isset($lang[$currentLang]) ? $lang[$currentLang] : 'English';
if($type == 'menu'){
        echo ''.$current.'<b class="caret"></b>';
        echo '<ul class="dropdown-menu">';
        foreach($lang as $key=>$langs) {
            echo '<li><a href="'.$this->getOwner()->createMultilanguageReturnUrl($key).'">'.$langs.'</a></li>';
        }
        echo '</ul>';
}
elseif($type == 'menu2'){
        foreach($lang as $key=>$langs) {
            $menu[] = array('label'=>$langs, 'url'=>$this->getOwner()->createMultilanguageReturnUrl($key));
        }
        echo CJSON::encode(array(
                    'label'=>$current, 'url'=>'#',
                    'itemOptions'=>array('class'=>'dropdown idiomaSelector'),
                    'linkOptions' => array('class'=>'dropdown-toggle ', 'data-toggle' => 'dropdown'),
                    'items'=>$menu,
            ));

}
else{
        // Render options as dropDownList
        echo CHtml::form();
        foreach($lang as $key=>$langs) {
            echo CHtml::hiddenField(
                $key,
                $this->getOwner()->createMultilanguageReturnUrl($key));
        }
        echo CHtml::dropDownList('lang', $currentLang, $lang,
            array(
                'submit'=>'','class'=>'form-control'
            )
        );
        echo CHtml::endForm();
}
