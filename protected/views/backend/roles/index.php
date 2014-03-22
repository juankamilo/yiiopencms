<?php
/* @var $this RolesController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Roles',
);

$this->menu=array(
	array('label'=>'Create Roles', 'url'=>array('create')),
	array('label'=>'Manage Roles', 'url'=>array('admin')),
);
?>

<h1>Roles</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
