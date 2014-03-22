<?php
/* @var $this UsuariosController */
/* @var $model Usuarios */

$this->breadcrumbs=array(
	'Usuarioses'=>array('index'),
	$model->usu_id,
);

$this->menu=array(
	array('label'=>'List Usuarios', 'url'=>array('index')),
	array('label'=>'Create Usuarios', 'url'=>array('create')),
	array('label'=>'Update Usuarios', 'url'=>array('update', 'id'=>$model->usu_id)),
	array('label'=>'Delete Usuarios', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->usu_id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Usuarios', 'url'=>array('admin')),
);
?>

<h1>View Usuarios #<?php echo $model->usu_id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'usu_id',
		'usu_nombre',
		'usu_login',
		'usu_clave',
		'usu_activo',
		'usu_correo',
		'usu_session',
		'rol_id',
	),
)); ?>
