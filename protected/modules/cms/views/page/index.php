<?php
$this->breadcrumbs=array(
	Yii::t('CmsModule.core','Cms')=>array('admin/index'),
	Yii::t('CmsModule.core','Pages'),
);
?>

<div class="cms-node-index">
	<div class="inner">

		<h1><?php echo Yii::t('CmsModule.core','Pages'); ?></h1>
		<div class="cms-admin-buttons">
			<?php $this->widget('bootstrap.widgets.TbButton',array(
				'icon'=>'plus white',
				'label'=>Yii::t('CmsModule.core','Create page'),
				'url'=>array('page/create'),
				'type'=>'primary',
                                
			)); ?>
		</div>


		<?php $this->widget('bootstrap.widgets.TbGridView',array(
			'type'=>array('striped','condensed'),
			'dataProvider'=>$model->search(),
			'template'=>'{items} {pager}',
			'showTableOnEmpty'=>false,
			'columns'=>array(
				'id',
				array(
					'name'=>'name',
					'type'=>'raw',
					'value'=>'CHtml::link(CHtml::encode(ucfirst($data->name)),array("update","id"=>$data->id))',
				),
				array(
					'name'=>'parentId',
					'value'=>'$data->parent !== null ? $data->parent->name : \'\'',
				),
				array(
					'name'=>'type',
					'value'=>'$data->type == 1 ? "Post" : ($data->type == 01 ? "Page" : "Page")',
				),
				array(
					'name'=>'published',
					'value'=>'Yii::app()->format->formatBoolean($data->published)',
				),
				array(
					'class'=>'bootstrap.widgets.TbButtonColumn',
					'viewButtonUrl'=>'Yii::app()->createUrl("cms/page/view", array("id"=>$data->id))',
				),
			),
		)) ?>

	</div>
</div>
