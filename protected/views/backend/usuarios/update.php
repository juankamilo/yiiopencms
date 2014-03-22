<?php
/* @var $this UsuariosController */
/* @var $model Usuarios */

$this->breadcrumbs=array(
	'Usuarioses'=>array('index'),
	$model->usu_id=>array('view','id'=>$model->usu_id),
	'Update',
);

$this->menu=array(
	array('label'=>'List Usuarios', 'url'=>array('index')),
	array('label'=>'Create Usuarios', 'url'=>array('create')),
	array('label'=>'View Usuarios', 'url'=>array('view', 'id'=>$model->usu_id)),
	array('label'=>'Manage Usuarios', 'url'=>array('admin')),
);
?>

<h1>Update Usuarios <?php echo $model->usu_id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>