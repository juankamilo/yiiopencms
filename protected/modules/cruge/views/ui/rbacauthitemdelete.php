<h1><?php echo ucwords(CrugeTranslator::t("eliminar"));?></h1>
<div class="form">
<?php
	/*
		$model:  es una instancia que implementa a CrugeAuthItemEditor
	*/
?>
<?php $form = $this->beginWidget('CActiveForm', array(
    'id'=>'crugestoreduser-form',
    'enableAjaxValidation'=>false,
    'enableClientValidation'=>false,
)); ?>
<h2><?php echo $model->name; ?></h2>
<p>
	<?php echo ucfirst(CrugeTranslator::t("marque la casilla para confirmar la eliminacion")); ?>
	<?php echo $form->checkBox($model,'deleteConfirmation'); ?>
	<?php echo $form->error($model,'deleteConfirmation'); ?>
</P>
<div class="row buttons">
	<?php Yii::app()->user->ui->tbutton("Eliminar"); ?>
	<?php Yii::app()->user->ui->bbutton("Volver",'volver'); ?>
</div>
<?php echo $form->errorSummary($model); ?>
<?php $this->endWidget(); ?>
</div>