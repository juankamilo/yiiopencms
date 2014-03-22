<?php
/* @var $this UsuariosController */
/* @var $model Usuarios */
/* @var $form CActiveForm */
?>

<div class="wide form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>

	<div class="row">
		<?php echo $form->label($model,'usu_id'); ?>
		<?php echo $form->textField($model,'usu_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'usu_nombre'); ?>
		<?php echo $form->textField($model,'usu_nombre',array('size'=>45,'maxlength'=>45)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'usu_login'); ?>
		<?php echo $form->textField($model,'usu_login',array('size'=>45,'maxlength'=>45)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'usu_clave'); ?>
		<?php echo $form->textField($model,'usu_clave',array('size'=>45,'maxlength'=>45)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'usu_activo'); ?>
		<?php echo $form->textField($model,'usu_activo',array('size'=>45,'maxlength'=>45)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'usu_correo'); ?>
		<?php echo $form->textField($model,'usu_correo',array('size'=>45,'maxlength'=>45)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'usu_session'); ?>
		<?php echo $form->textField($model,'usu_session',array('size'=>45,'maxlength'=>45)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'rol_id'); ?>
		<?php echo $form->textField($model,'rol_id'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Search'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->