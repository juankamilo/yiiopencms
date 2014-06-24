<?php
	/* formulario de edicion de CrugeSystem

		argumento:

		$model: instancia de ICrugeSession
	*/
?>

<?php
	if(Yii::app()->user->hasFlash('systemFormFlash'))  {
		echo "<div class='flash-success'>";
		echo Yii::app()->user->getFlash('systemFormFlash');
		echo "</div>";
	}
?>

<div class="form">
<?php $form = $this->beginWidget('CActiveForm', array(
    'id'=>'CrugeSystem-Form',
    'enableAjaxValidation'=>false,
    'enableClientValidation'=>false,
)); ?>
<div class="row form-group">
	<h6><?php echo ucwords(CrugeTranslator::t("opciones de sesion"));?></h6>
	<div class='col'>
		<?php echo $form->labelEx($model,'systemdown'); ?>
		<?php echo $form->checkBox($model,'systemdown'); ?>
		<?php echo $form->error($model,'systemdown'); ?>
	</div>
	<div class='col'>
		<?php echo $form->labelEx($model,'systemnonewsessions'); ?>
		<?php echo $form->checkBox($model,'systemnonewsessions'); ?>
		<?php echo $form->error($model,'systemnonewsessions'); ?>
	</div>
	<div class='col'>
		<?php echo $form->labelEx($model,'sessionmaxdurationmins'); ?>
		<?php echo $form->textField($model,'sessionmaxdurationmins'
			,array('size'=>5,'maxlength'=>4)); ?>
		<?php echo $form->error($model,'sessionmaxdurationmins'); ?>
	</div>
</div>
<div class="row form-group">
	<h6><?php echo ucwords(CrugeTranslator::t("opciones de registro de usuarios"));?></h6>
	<div class='col'>
		<?php echo $form->labelEx($model,'registerusingcaptcha'); ?>
		<?php echo $form->checkBox($model,'registerusingcaptcha'); ?>
		<?php echo $form->error($model,'registerusingcaptcha'); ?>
	</div>
	<div class='col'>
		<?php echo $form->labelEx($model,'registerusingactivation'); ?>
		<?php echo $form->dropDownList($model,'registerusingactivation'
			,Yii::app()->user->um->getUserActivationOptions()); ?>
		<?php echo $form->error($model,'registerusingactivation'); ?>
	</div>
	<div class='col'>
		<?php echo $form->labelEx($model,'defaultroleforregistration'); ?>
		<?php echo $form->dropDownList($model,'defaultroleforregistration'
			,Yii::app()->user->rbac->getRolesAsOptions(CrugeTranslator::t(
				"--no asignar ningun rol--"))); ?>
		<?php echo $form->error($model,'defaultroleforregistration'); ?>
	</div>
	<div class='col'>
		<?php echo $form->labelEx($model,'registrationonlogin'); ?>
		<?php echo $form->checkBox($model,'registrationonlogin'); ?>
		<?php echo $form->error($model,'registrationonloginn'); ?>
	</div>
</div>

<div class="row form-group">
	<h6><?php echo ucwords(CrugeTranslator::t("terminos y condiciones de registro"));?></h6>
	<div class='row'>
		<div class='col'>
			<?php echo $form->labelEx($model,'registerusingterms'); ?>
			<?php echo $form->checkBox($model,'registerusingterms'); ?>
			<?php echo $form->error($model,'registerusingterms'); ?>
		</div>
		<div class='col'>
			<?php echo $form->labelEx($model,'registerusingtermslabel'); ?>
			<?php echo $form->textField($model,'registerusingtermslabel'
				,array('size'=>45,'maxlength'=>100)); ?>
			<?php echo $form->error($model,'registerusingtermslabel'); ?>
		</div>
	</div>
	<hr/>
	<div class='row'>
		<?php echo $form->labelEx($model,'terms'); ?>
		<?php echo $form->textArea($model,'terms'
			,array('rows'=>10,'cols'=>50)); ?>
		<?php echo $form->error($model,'terms'); ?>
	</div>
</div>


<div class="row buttons">
	<?php Yii::app()->user->ui->tbutton("Actualizar"); ?>
</div>
<?php echo $form->errorSummary($model); ?>
<?php $this->endWidget(); ?>
</div>