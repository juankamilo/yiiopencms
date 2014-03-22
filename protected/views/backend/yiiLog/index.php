<?php
/* @var $this YiiLogController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Yii Log',
);

$this->menu=array(
	array('label'=>'Create YiiLog', 'url'=>array('create')),
	array('label'=>'Manage YiiLog', 'url'=>array('admin')),
);
?>
<div class="module2">
    <header>
        <h3>Yii Log</h3>
    </header>
    <div class="module_content">
<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
    </div>
</div>