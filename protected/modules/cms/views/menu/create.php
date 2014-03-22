<?php $this->breadcrumbs=array(
	Yii::t('CmsModule.core','Cms')=>array('admin/index'),
	Yii::t('CmsModule.core','Menus')=>array('/cms/menu'),
	Yii::t('CmsModule.core','Create')
) ?>

<div class="cms-menu-create">
	<div class="inner">

		<h1><?php echo Yii::t('CmsModule.core','Create menu') ?></h1>

		<?php /** @var TbActiveForm $form */
		$form = $this->beginWidget('bootstrap.widgets.TbActiveForm'); ?>

			<?php echo $form->textFieldRow($model,'name'); ?>

			<?php $this->renderPartial('cms.views.node._formActions'); ?>

		<?php $this->endWidget(); ?>

	</div>
</div>
