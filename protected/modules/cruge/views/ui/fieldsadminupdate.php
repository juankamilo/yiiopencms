<?php
	/*
		$model:  es una instancia que implementa a ICrugeField
	*/
?>
<h1><?php echo ucwords(CrugeTranslator::t(	
		(($model->isNewRecord==1) ? "creando nuevo campo personalizado" :"editando campo personalizado")
	));?></h1>
<div class="form">
<?php $form = $this->beginWidget('CActiveForm', array(
    'id'=>'crugefield-form',
    'enableAjaxValidation'=>false,
    'enableClientValidation'=>false,
)); ?>
<div class="row form-group">
	<h6><?php echo ucfirst(CrugeTranslator::t("datos del campo"));?></h6>
	<div class='col'>
		<?php echo $form->labelEx($model,'fieldname'); ?>
		<?php echo $form->textField($model,'fieldname',array('size'=>15,'maxlength'=>20)); ?>
		<?php echo $form->error($model,'fieldname'); ?>
	</div>
	<div class='col'>
		<?php echo $form->labelEx($model,'longname'); ?>
		<?php echo $form->textField($model,'longname'); ?>
		<?php echo $form->error($model,'longname'); ?>
	</div>
	<div class='col'>
		<?php echo $form->labelEx($model,'position'); ?>
		<?php echo $form->textField($model,'position',array('size'=>5,'maxlength'=>3)); ?>
		<?php echo $form->error($model,'position'); ?>
	</div>
	<div class='col'>
		<?php echo $form->labelEx($model,'required'); ?>
		<?php echo $form->checkBox($model,'required'); ?>
		<?php echo $form->error($model,'required'); ?>
	</div>
	<div class='col'>
		<?php echo $form->labelEx($model,'showinreports'); ?>
		<?php echo $form->checkBox($model,'showinreports'); ?>
		<?php echo $form->error($model,'showinreports'); ?>
	</div>
</div>

<div class="row form-group">
	<h6><?php echo ucfirst(CrugeTranslator::t("datos del contenido"));?></h6>
	
	<div class="row field-group">
		<div class='col'>
			<?php echo $form->labelEx($model,'fieldtype'); ?>
			<?php echo $form->dropDownList($model,'fieldtype'
				,Yii::app()->user->um->getFieldTypeOptions()); ?>
			<?php echo $form->error($model,'fieldtype'); ?>
		</div>
		<div class='col'>
			<?php echo $form->labelEx($model,'fieldsize'); ?>
			<?php echo $form->textField($model,'fieldsize',array('size'=>5,'maxlength'=>3)); ?>
			<?php echo $form->error($model,'fieldsize'); ?>
		</div>
		<div class='col'>
			<?php echo $form->labelEx($model,'maxlength'); ?>
			<?php echo $form->textField($model,'maxlength',array('size'=>8,'maxlength'=>20)); ?>
			<?php echo $form->error($model,'maxlength'); ?>
			<i><?php echo CrugeTranslator::t("maxlength = -1 causa que no se valide el tamano de este campo");?></i>
		</div>
	</div>
	
	<div class="row field-group">
		<div class='col'>
			<?php echo $form->labelEx($model,'predetvalue'); ?>
			<?php echo $form->textArea($model,'predetvalue',array('rows'=>5,'cols'=>40)); ?>
			<?php echo $form->error($model,'predetvalue'); ?>
			<p class='hint'><?php echo CrugeTranslator::t(
"si el fieldtype es un Listbox ponga aqui las opciones una por cada linea,
 el valor coloquelo al inicio seguido de una coma, ejemplo:
 <ul style='list-style: none;'>
 <li>1, azul</li>
 <li>2, rojo</li>
 <li>3, verde</li>
 </ul>
"
);?></p>
		</div>
	</div>
</div>

<div class="row form-group">
	<h6><?php echo ucfirst(CrugeTranslator::t("datos de validacion"));?></h6>
	
	<div class='field-group'>
		<div class='col'>
			<?php echo $form->labelEx($model,'useregexp'); ?>
			<?php echo $form->textArea($model,'useregexp',array('rows'=>5,'cols'=>40)); ?>
			<?php echo $form->error($model,'useregexp'); ?>
			<p class='hint'><?php echo CrugeTranslator::t("dejar en blanco si no se quiere usar");?></p>
		</div>
		<?php echo ucfirst(CrugeTranslator::t(
				"La expresion regular (regexp) es una lista de caracteres
				 que validan la sintaxis de lo que el usuario ingrese en este campo.
				 por ejemplo:"
			));
		?>
		<?php
		echo "<br/><u>".CrugeTranslator::t("telefono:")."</u><br/>^([0-9-.+ \(\)]{3,20})$";
		echo "<br/><u>".CrugeTranslator::t("digitos y letras:")."</u><br/>^([a-zA-Z0-9]+)$";
		?>
	</div>
	<div class='col'>
		<?php echo $form->labelEx($model,'useregexpmsg'); ?>
		<?php echo $form->textField($model,'useregexpmsg',array('size'=>50,'maxlength'=>512)); ?>
		<?php echo $form->error($model,'useregexpmsg'); ?>
	</div>
</div>



<div class="row buttons">
	<?php Yii::app()->user->ui->tbutton(($model->isNewRecord ? "Crear Campo" : "Actualizar Campo")); ?>
</div>
<?php echo $form->errorSummary($model); ?>
<?php $this->endWidget(); ?>
</div>
