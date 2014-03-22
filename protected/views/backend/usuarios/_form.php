<?php
/* @var $this UsuariosController */
/* @var $model Usuarios */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'usuarios-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'usu_nombre'); ?>
		<?php echo $form->textField($model,'usu_nombre',array('size'=>45,'maxlength'=>45)); ?>
		<?php echo $form->error($model,'usu_nombre'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'usu_login'); ?>
		<?php echo $form->textField($model,'usu_login',array('size'=>45,'maxlength'=>45)); ?>
		<?php echo $form->error($model,'usu_login'); ?>
	</div>

	<div class="row">
            <?php echo $form->labelEx($model,'new_password'); ?>
            <?php echo $form->passwordField($model,'new_password',array('maxlength'=>100)); ?>
            <?php echo $form->error($model,'new_password'); ?>
        </div>

	<div class="row">
		<?php echo $form->labelEx($model,'usu_activo'); ?>
		<?php echo $form->dropDownList($model,'usu_activo',array(''=>'Seleccione','1'=>'Si','2'=>'No')); ?>
		<?php echo $form->error($model,'usu_activo'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'usu_correo'); ?>
		<?php echo $form->textField($model,'usu_correo',array('size'=>45,'maxlength'=>45)); ?>
		<?php echo $form->error($model,'usu_correo'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'rol_id'); ?>
                <?php echo $form->dropDownList($model,'rol_id', CHtml::listData(Roles::model()->findAll(), 'rol_id', 'rol_nombre'),array('prompt'=>'Seleccione')); ?>
		<?php echo $form->error($model,'rol_id'); ?>
	</div>
	<div class="row">
		<?php echo $form->labelEx($model,'age_id'); ?>
                <?php echo $form->dropDownList($model,'age_id', CHtml::listData(Agencias::model()->findAll(), 'age_id', 'nombre'),array('prompt'=>'Seleccione')); ?>
		<?php echo $form->error($model,'age_id'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->