<?php $this->breadcrumbs=array(
	Yii::t('CmsModule.core','Cms')=>array('admin/index'),
	Yii::t('CmsModule.core','Pages')=>array('/cms/page'),
	ucfirst($page->name)=>array('/cms/page/update','id'=>$page->id),
	Yii::t('CmsModule.core','Add file')
) ?>

<div class="cms-attachment-add">
	<div class="inner">

		<h1><?php echo Yii::t('CmsModule.core','Add file') ?></h1>

		<?php /** @var TbActiveForm $form */
		$form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
			'htmlOptions'=>array('enctype'=>'multipart/form-data'),
		)); ?>

			<?php echo $form->textFieldRow($model,'name'); ?>

			<?php echo $form->fileFieldRow($model,'file'); ?>

			<div class="form-actions">

				<?php $this->widget('bootstrap.widgets.TbButton',array(
					'label'=>Yii::t('CmsModule.core','Save'),
					'buttonType'=>'submit',
					'type'=>'primary',
				)); ?>

				<?php $this->widget('bootstrap.widgets.TbButton',array(
					'label'=>Yii::t('CmsModule.core','Cancel'),
					'url'=>array('page/update','id'=>$page->id, 'tab'=>'attachments'),
					'htmlOptions'=>array('confirm'=>Yii::t('CmsModule.core','Are you sure you want to cancel? All changes will be lost.')),
				)); ?>

			</div>

		<?php $this->endWidget(); ?>

	</div>
</div>
