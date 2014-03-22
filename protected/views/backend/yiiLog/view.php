<?php
/* @var $this YiiLogController */
/* @var $model YiiLog */

$this->breadcrumbs=array(
	'Yii Logs'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List YiiLog', 'url'=>array('index')),
	array('label'=>'Create YiiLog', 'url'=>array('create')),
	array('label'=>'Update YiiLog', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete YiiLog', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage YiiLog', 'url'=>array('admin')),
);
?>
<div class="module2">
    <header>
        <h3>YiiLog #<?php echo $model->id; ?></h3>
    </header>
    <div class="module_content">
<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'level',
		'category',
		'logtime',
		'message',
	),
)); ?>
    </div>
</div>
