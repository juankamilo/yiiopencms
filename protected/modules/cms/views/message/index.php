<?php
$this->breadcrumbs=array(
	Yii::t('CmsModule.core','Cms')=>array('admin/index'),
	Yii::t('CmsModule.core','Messages'),
);
?>

<div class="cms-block-index">
	<div class="inner">

		<h1><?php echo Yii::t('CmsModule.core','Message'); ?></h1>
		<div class="cms-admin-buttons">
			<?php $this->widget('bootstrap.widgets.TbButton',array(
				'icon'=>'plus white',
				'label'=>Yii::t('CmsModule.core','Create block'),
				'url'=>array('block/create'),
				'type'=>'primary',
			)); ?>
		</div>


		<?php $this->widget('bootstrap.widgets.TbGridView',array(
			'type'=>array('striped','condensed'),
			'dataProvider'=>$model->search(),
			'template'=>'{items} {pager}',
			'showTableOnEmpty'=>false,
                        'filter'=>$model,
			'columns'=>array(
				'id',
                                'category',
				array(
					'name'=>'message',
					'type'=>'raw',
					'value'=>'CHtml::link(CHtml::encode(ucfirst($data->message)),array("update","id"=>$data->id))',
                                    
				),
				
				array(
					'class'=>'bootstrap.widgets.TbButtonColumn',
					'template'=>'{update} {delete}',
				),
			),
		)) ?>

	</div>
</div>
