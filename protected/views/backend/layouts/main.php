<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="language" content="en" />
	<title><?php echo CHtml::encode($this->pageTitle); ?></title>
	<?php Yii::app()->bootstrap->register(); ?>
        <?php Yii::app()->clientScript->registerCssFile(Yii::app()->theme->baseUrl.'/css/backend.css?v=110220120632'); ?>
</head>
<body>
<?php $this->renderPartial('//layouts/_header') ?>
    <div class="cont">
    <div class="container-fluid">
        <div class="main">
        <?php echo $content; ?>
        </div>
        
    </div>
    </div>
    <div class="clear"></div>
<?php $this->renderPartial('//layouts/_footer') ?>
</body>
</html>
