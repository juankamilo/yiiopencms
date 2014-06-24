<h1><?php echo ucwords(CrugeTranslator::t("registrarse"));?></h1>
<div class="form">
<?php
	/*
		$model:  es una instancia que implementa a ICrugeStoredUser
	*/
?>
<?php $form = $this->beginWidget('CActiveForm', array(
    'id'=>'registration-form',
    'enableAjaxValidation'=>false,
    'enableClientValidation'=>false,
)); ?>
<div class="row form-group-vert">
	<h6><?php echo ucfirst(CrugeTranslator::t("datos de la cuenta"));?></h6>
	<?php 
		foreach (CrugeUtil::config()->availableAuthModes as $authmode){
			echo "<div class='col'>";
			echo $form->labelEx($model,$authmode);
			echo $form->textField($model,$authmode);
			echo $form->error($model,$authmode);
			echo "</div>";
		}
	?>
	<div class="col">
		<?php echo $form->labelEx($model,'newPassword'); ?>
		<div class='item'>
			<?php echo $form->textField($model,'newPassword'); ?>
			<p class='hint'><?php echo CrugeTranslator::t(
				"su contraseña, letras o digitos o los caracteres @#$%. minimo 6 simbolos.");?></p>
		</div>
		<?php echo $form->error($model,'newPassword'); ?>
		<script>
			function fnSuccess(data){
				$('#CrugeStoredUser_newPassword').val(data);
			}
			function fnError(e){
				alert("error: "+e.responseText);
			}
		</script>
		<?php echo CHtml::ajaxbutton(
			CrugeTranslator::t("Generar una nueva clave")
			,Yii::app()->user->ui->ajaxGenerateNewPasswordUrl
			,array('success'=>new CJavaScriptExpression('fnSuccess'),
				'error'=>new CJavaScriptExpression('fnError'))
		); ?>
	</div>
</div>


<!-- inicio de campos extra definidos por el administrador del sistema -->
<?php 
	if(count($model->getFields()) > 0){
		echo "<div class='row form-group-vert'>";
		echo "<h6>".ucfirst(CrugeTranslator::t("perfil"))."</h6>";
		foreach($model->getFields() as $f){
			// aqui $f es una instancia que implementa a: ICrugeField
			echo "<div class='col'>";
			echo Yii::app()->user->um->getLabelField($f);
			echo Yii::app()->user->um->getInputField($model,$f);
			echo $form->error($model,$f->fieldname);
			echo "</div>";
		}
		echo "</div>";
	}
?>
<!-- fin de campos extra definidos por el administrador del sistema -->


<!-- inicio - terminos y condiciones -->
<?php
	if(Yii::app()->user->um->getDefaultSystem()->getn('registerusingterms') == 1)
	{
?>
<div class='form-group-vert'>
	<h6><?php echo ucfirst(CrugeTranslator::t("terminos y condiciones"));?></h6>
	<?php echo CHtml::textArea('terms'
		,Yii::app()->user->um->getDefaultSystem()->get('terms')
		,array('readonly'=>'readonly','rows'=>5,'cols'=>'80%')
		); ?>
	<div><span class='required'>*</span><?php echo CrugeTranslator::t(Yii::app()->user->um->getDefaultSystem()->get('registerusingtermslabel')); ?></div>
	<?php echo $form->checkBox($model,'terminosYCondiciones'); ?>
	<?php echo $form->error($model,'terminosYCondiciones'); ?>
</div>
<!-- fin - terminos y condiciones -->
<?php } ?>



<!-- inicio pide captcha -->
<?php if(Yii::app()->user->um->getDefaultSystem()->getn('registerusingcaptcha') == 1) { ?>
<div class='form-group-vert'>
	<h6><?php echo ucfirst(CrugeTranslator::t("codigo de seguridad"));?></h6>
	<div class="row">
		<div>
			<?php $this->widget('CCaptcha'); ?>
			<?php echo $form->textField($model,'verifyCode'); ?>
		</div>
		<div class="hint"><?php echo CrugeTranslator::t("por favor ingrese los caracteres o digitos que vea en la imagen");?></div>
		<?php echo $form->error($model,'verifyCode'); ?>
	</div>
</div>
<?php } ?>
<!-- fin pide captcha-->



<div class="row buttons">
	<?php Yii::app()->user->ui->tbutton("Registrarse"); ?>
</div>
<?php echo $form->errorSummary($model); ?>
<?php $this->endWidget(); ?>
</div>
