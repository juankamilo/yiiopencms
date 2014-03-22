<?php $this->breadcrumbs=array(
	Yii::t('CmsModule.core','Cms')=>array('admin/index'),
	Yii::t('CmsModule.core','Menus')=>array('/cms/menu'),
	ucfirst($model->menu->name)=>array('menu/update','id'=>$model->menu->id),
	ucfirst($model->label),
) ?>

<div class="cms-menu-update">
	<div class="inner">

		<h1><?php echo Yii::t('CmsModule.core','{label} link', array('{label}'=>ucfirst($model->label))); ?></h1>

		<?php $this->renderPartial('_form',array('model'=>$model,'menu'=>$menu)); ?>

	</div>
</div>
