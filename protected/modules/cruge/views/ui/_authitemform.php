<?php 
	/* formulario comun para create y update
	
		argumento:
		
		$model: instancia de CrugeAuthItemEditor
	*/
?>
<div class="form">
<?php $form = $this->beginWidget('CActiveForm', array(
    'id'=>'authitem-form',
    'enableAjaxValidation'=>false,
    'enableClientValidation'=>false,
)); ?>
<div class="row form-group">
	<div class='row'>
		<?php echo $form->labelEx($model,'name'); ?>
		<?php echo $form->textField($model,'name',array('size'=>64,'maxlength'=>64)); ?>
		<?php echo $form->error($model,'name'); ?>
	</div>
	<div class='row'>
		<?php echo $form->labelEx($model,'description'); ?>
		<?php echo $form->textField($model,'description',array('size'=>50,'maxlength'=>100)); ?>
		<?php echo $form->error($model,'description'); ?>
		<?php if($model->categoria  == "tarea") { ?>
		<div class='hint'>Tip: precede este valor con un ":" para que la tarea sea exportada como un menuitem al usar<br/> <span class='code'>
		Yii::app()->user->rbac->getMenu();</span> y finalizala con un {nombremenuitem} para que quede dentro de un -nombremenuitem-.
		ejemplo: <span class='code'>":Edita tu Perfil{menuprincipal}"</span></div>
		<?php } ?>
	</div>
	<div class='row'>
		<?php echo $form->labelEx($model,'businessRule'); ?>
		<?php echo $form->textField($model,'businessRule',array('size'=>50,'maxlength'=>512)); ?>
		<?php echo $form->error($model,'businessRule'); ?>
		<p class='hint'>
			<?php echo CrugeTranslator::t("define una regla de negocio que sera ejecutada cada vez que este item sea evaluado mediante una llamada a checkAccess, el argumento params es entregado a checkAccess de forma opcional:"); ?>
			<br/>
			<?php echo CrugeTranslator::t(
			"regla de ejemplo:"); ?>
			<br/>
			<div class='code'>return Yii::app()->user->id==$params["post"]->authID;</div>
			<br/>
			<div class='code'>
				$params = ...<?php echo CrugeTranslator::t("cualquier cosa"); ?>...;<br/>
				if(Yii::app()->user->checkAccess('<?php echo $model->name;?>', $params)){ ... }
			</div>
			<br/>
		</p>
	</div>
</div>

<div class="row buttons">
	
	<?php Yii::app()->user->ui->tbutton(($model->isNewRecord ? 'Crear Nuevo' : 'Actualizar')); ?>
	<?php Yii::app()->user->ui->bbutton("Volver",'volver'); ?>
</div>
<?php echo $form->errorSummary($model); ?>
<?php $this->endWidget(); ?>
</div>
