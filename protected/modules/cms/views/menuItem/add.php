<?php $this->breadcrumbs=array(
	Yii::t('CmsModule.core','Cms')=>array('admin/index'),
	Yii::t('CmsModule.core','Menus')=>array('/cms/menu'),
	ucfirst($menu->name)=>array('/cms/menu/update','id'=>$menu->id),
	Yii::t('CmsModule.core','Add link')
) ?>

<div class="cms-menu-item-add">
	<div class="inner">

		<h1><?php echo Yii::t('CmsModule.core','Add link') ?></h1>

		<?php $this->renderPartial('_form',array('model'=>$model,'menu'=>$menu)); ?>

	</div>
</div>
