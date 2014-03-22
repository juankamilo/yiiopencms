<?php /** @var BootActiveForm $form */
$form=$this->beginWidget('bootstrap.widgets.TbActiveForm'); ?>

	<?php echo $form->textFieldRow($model,'label'); ?>

	<?php echo $form->textFieldRow($model,'url'); ?>

	<?php echo $form->checkBoxRow($model,'visible'); ?>

	<div class="form-actions">

		<?php $this->widget('bootstrap.widgets.TbButton',array(
			'label'=>Yii::t('CmsModule.core','Save'),
			'buttonType'=>'submit',
			'type'=>'primary',
		)); ?>

		<?php $this->widget('bootstrap.widgets.TbButton',array(
			'label'=>Yii::t('CmsModule.core','Cancel'),
			'url'=>array('menu/update','id'=>$menu->id),
			'htmlOptions'=>array('confirm'=>Yii::t('CmsModule.core','Are you sure you want to cancel? All changes will be lost.')),
		)); ?>

	</div>

<?php $this->endWidget(); ?>
