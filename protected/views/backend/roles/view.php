<?php
/* @var $this RolesController */
/* @var $model Roles */

$this->breadcrumbs=array(
	'Roles'=>array('index'),
	$model->rol_id,
);

$this->menu=array(
	array('label'=>'List Roles', 'url'=>array('index')),
	array('label'=>'Create Roles', 'url'=>array('create')),
	array('label'=>'Update Roles', 'url'=>array('update', 'id'=>$model->rol_id)),
	array('label'=>'Delete Roles', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->rol_id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Roles', 'url'=>array('admin')),
);
?>

<h1>View Roles #<?php echo $model->rol_id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'rol_id',
		'rol_nombre',
	),
)); ?>
