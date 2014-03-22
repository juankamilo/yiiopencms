<?php
/* @var $this YiiLogController */
/* @var $model YiiLog */

$this->breadcrumbs=array(
	'Yii Logs'=>array('index'),
	'Manage',
);

$this->menu=array(
	array('label'=>'List YiiLog', 'url'=>array('index')),
	array('label'=>'Create YiiLog', 'url'=>array('create')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('yii-log-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>


<div class="module">
    <header>
        <h3>Errores Log</h3>
    </header>
    <div class="module_content">

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'yii-log-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'id',
                array(
                    'name'=>'level',
                    'value'=>'$data->level',
                    'filter'=>array('error'=>'error', 'warning'=>'warning','tarjeta'=>'tarjeta','solicitud'=>'solicitud'),
                ),
		'category',
		array(
                    'name'=>'logtime',
                    'value'=>'date("Y-m-d H:i:s",$data->logtime)',
                ),
		'message',
		array(
			'class'=>'CButtonColumn',
		),
	),
)); ?>
    </div>
</div>