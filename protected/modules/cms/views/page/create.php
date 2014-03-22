<?php $this->breadcrumbs = array(
	Yii::t('CmsModule.core','Cms')=>array('admin/index'),
	Yii::t('CmsModule.core','Pages')=>array('/cms/page'),
	Yii::t('CmsModule.core','Create')
) ?>

<div class="cms-page-create">
	<div class="inner">

		<h1><?php echo Yii::t('CmsModule.core','Create page') ?></h1>

		<?php /** @var TbActiveForm $form */
		$form = $this->beginWidget('bootstrap.widgets.TbActiveForm'); ?>

			<?php $this->renderPartial('_form',array('form'=>$form,'model'=>$model)); ?>

			<?php $this->renderPartial('cms.views.node._formActions'); ?>

		<?php $this->endWidget(); ?>

	</div>
</div>
