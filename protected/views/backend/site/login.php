<?php
?>
<div class="module">
    <div class="module_content">
        <div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'login-form',
	'enableClientValidation'=>true,
	'clientOptions'=>array(
		'validateOnSubmit'=>true,
	),
)); ?>

            <p class="note"><?php echo Yii::t('app', 'Fields with') ?><span class="required">*</span> <?php echo Yii::t('app', 'are required') ?>.</p>

	<div class="row">
		<?php echo $form->labelEx($model,'username'); ?>
		<?php echo $form->textField($model,'username'); ?>
		<?php echo $form->error($model,'username'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'password'); ?>
		<?php echo $form->passwordField($model,'password'); ?>
		<?php echo $form->error($model,'password'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Login', array('class'=>'btn btn-primary')); ?>
            <?php //echo CHtml::Button(Yii::t('app', 'Login'),array('class'=>'btn btn-primary', 'submit'=>Yii::app()->controller->createUrl('site/login')));?>
            <?php  //echo CHtml::ajaxSubmitButton (''.Yii::t('app', 'Login').'', CController::createUrl('site/login'),array ('success' => 'function(data){if(data==="success"){location.reload(); }}',), array('class'=>'btn btn-primary'));?>
	</div>
   
<?php $this->endWidget(); ?>
</div><!-- form -->
</div>
</div>
