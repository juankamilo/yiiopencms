<?php $this->breadcrumbs=array(
	Yii::t('CmsModule.core','Cms'),
);
//echo $this->module->pageLayout;
?>

<div class="cms-admin-index">
	<div class="inner">

		<h1><?php echo Yii::t('CmsModule.core','Cms'); ?></h1>

		<div class="row">

			<div class="span4">
				<div class="pages">
					<h2><?php echo CHtml::link(Yii::t('CmsModule.core','Pages'),array('page/index')); ?></h2>
					<p><?php echo Yii::t('CmsModule.core','Administer pages.'); ?></p>
				</div>
			</div>

			<div class="span4">
				<div class="blocks">
					<h2><?php echo CHtml::link(Yii::t('CmsModule.core','Blocks'),array('block/index')); ?></h2>
					<p><?php echo Yii::t('CmsModule.core','Administer blocks.'); ?></p>
				</div>
			</div>

			<div class="span4">
				<div class="menus">
					<h2><?php echo CHtml::link(Yii::t('CmsModule.core','Menus'),array('menu/index')); ?></h2>
					<p><?php echo Yii::t('CmsModule.core','Administer menus.'); ?></p>
				</div>
			</div>

		</div>

		<!--
		<div class="messages">
			<h2><?php echo CHtml::link(Yii::t('CmsModule.core','Messages'),array('message/index')); ?></h2>
			<p><?php echo Yii::t('CmsModule.core','Administer messages.'); ?></p>
		</div>
		-->

	</div>
</div>
