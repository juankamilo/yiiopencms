<?php $this->breadcrumbs=array(
	Yii::t('CmsModule.core','Cms')=>array('admin/index'),
	Yii::t('CmsModule.core','Menus')=>array('/cms/menu'),
	ucfirst($model->name),
) ?>

<div class="cms-menu-update">
	<div class="inner">

		<h1><?php echo Yii::t('CmsModule.core','{name} menu', array('{name}'=>ucfirst($model->name))); ?></h1>

		<?php /** @var TbActiveForm $form */
		$form = $this->beginWidget('bootstrap.widgets.TbActiveForm'); ?>

			<?php echo $form->textFieldRow($model,'name'); ?>

			<h2><?php echo Yii::t('CmsModule.core','Links'); ?></h2>

			<?php $this->widget('bootstrap.widgets.TbTabs',array(
				'type'=>'pills',
				'tabs'=>$this->getLanguageTabs($form, $model),
			)); ?>

			<?php $this->renderPartial('cms.views.node._formActions'); ?>

		<?php $this->endWidget(); ?>

	</div>
</div>
