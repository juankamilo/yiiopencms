<?php $this->breadcrumbs=array(
	Yii::t('CmsModule.core','Cms')=>array('/cms'),
	Yii::t('CmsModule.core','Blocks')=>array('/cms/block'),
	ucfirst($model->name),
); ?>

<div class="cms-page-update">
	<div class="inner">

	    <h1><?php echo Yii::t('CmsModule.core','{name} block', array('{name}'=>ucfirst($model->name))); ?></h1>

		<?php /** @var TbActiveForm $form */
		$form = $this->beginWidget('bootstrap.widgets.TbActiveForm'); ?>

			<?php $this->widget('bootstrap.widgets.TbTabs',array(
				'tabs'=>$this->getFormTabs($form, $model),
				'htmlOptions'=>array('class'=>'cms-form-tabs'),
			)); ?>

			<?php $this->renderPartial('cms.views.node._formActions'); ?>

		<?php $this->endWidget(); ?>

	</div>
</div>
