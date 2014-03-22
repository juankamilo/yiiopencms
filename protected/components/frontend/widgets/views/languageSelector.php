<div id="language-select">
<?php 
    if(sizeof($lang) < 3) {
        // Render options as links
        $lastElement = end($lang);
        foreach($lang as $key=>$langs) {
            if($key != $currentLang) {
                echo CHtml::link(
                     $langs, 
                     $this->getOwner()->createMultilanguageReturnUrl($key));
            } else echo '<b>'.$lang.'</b>';
            if($lang != $lastElement) echo ' | ';
        }
    }
    else {
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
?>
</div>