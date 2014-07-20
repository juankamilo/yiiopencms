<?php ?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo Yii::app()->language; ?>" lang="<?php echo Yii::app()->language; ?>">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="msvalidate.01" content="CB233410A882D2515D4EFA2B490319E5" />
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?php
        if(isset($_SERVER['HTTP_USER_AGENT']) && (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false))
                header('X-UA-Compatible: IE=edge,chrome=1');
        ?>
        <title><?php echo CHtml::encode($this->pageTitle); ?></title>
        <?php Yii::app()->bootstrap->register(); ?>
        <?php Yii::app()->clientScript->registerCssFile(Yii::app()->theme->baseUrl.'/css/main.css?v=110220120632'); ?>
</head>
<body>
    <?php $this->renderPartial('//layouts/_header') ?>
    <div class="container" id="page">
	<?php if(isset($this->breadcrumbs)):?>
		<?php $this->widget('bootstrap.widgets.TbBreadcrumbs', array(
			'links'=>$this->breadcrumbs,
		)); ?><!-- breadcrumbs -->
	<?php endif?>
        <?php echo $content; ?>
	<div class="clear"></div>
        <?php $this->renderPartial('//layouts/_footer') ?>
    </div><!-- page -->
</body>
</html>
