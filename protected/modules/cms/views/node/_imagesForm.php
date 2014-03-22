<fieldset class="form-images">

    <h2><?php echo Yii::t('CmsModule.core','Images') ?></h2>

	<?php $this->widget('bootstrap.widgets.TbButton',array(
		'icon'=>'plus white',
		'label'=>Yii::t('CmsModule.core','Add image'),
		'url'=>array('/cms/image/add','pageId'=>$model->id),
		'type'=>'primary',
		'htmlOptions'=>array('class'=>'add-button'),
	)); ?>

    <?php $this->widget('bootstrap.widgets.TbGridView',array(
		'type'=>array('striped','condensed'),
        'id'=>'images',
        'dataProvider'=>$model->getImages(),
        'template'=>'{items} {pager}',
        'emptyText'=>Yii::t('CmsModule.core', 'No images found.'),
        'showTableOnEmpty'=>false,
        'columns'=>array(
			array(
				'name'=>'id',
				'header'=>'#',
				'value'=>'$data->id',
			),
			array(
                'name'=>'name',
                'value'=>'$data->resolveFilename()',
            ),
			array(
				'header'=>Yii::t('CmsModule.core', 'Tag'),
				'value'=>'\'{{image:\'.$data->id.\'}}\'',
				'sortable'=>false,
			),
            array(
                'class'=>'bootstrap.widgets.TbButtonColumn',
                'template'=>'{delete}',
                'buttons'=>array(
                    'delete'=>array(
                        'url'=>'Yii::app()->controller->createUrl("image/delete", array("id"=>$data->id))',
                    ),
                ),
            ),
        ),
    )); ?>

</fieldset>