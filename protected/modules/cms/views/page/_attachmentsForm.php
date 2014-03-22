<fieldset class="form-attachments">

    <h2><?php echo Yii::t('CmsModule.core','Attachments') ?></h2>

	<?php $this->widget('bootstrap.widgets.TbButton',array(
		'icon'=>'plus white',
		'label'=>Yii::t('CmsModule.core','Add file'),
		'url'=>array('/cms/attachment/add','pageId'=>$model->id),
		'type'=>'primary',
		'htmlOptions'=>array('class'=>'add-button'),
	)); ?>

    <?php $this->widget('bootstrap.widgets.TbGridView',array(
		'type'=>array('striped','condensed'),
        'id'=>'attachments',
        'dataProvider'=>$model->getAttachments(),
        'template'=>'{items} {pager}',
        'emptyText'=>Yii::t('CmsModule.core', 'No attachments found.'),
        'showTableOnEmpty'=>false,
        'columns'=>array(
			array(
				'name'=>'id',
				'header'=>'#',
				'value'=>'$data->id',
			),
			array(
                'name'=>'name',
                'value'=>'$data->resolveName()',
            ),
			array(
				'header'=>Yii::t('CmsModule.core', 'Tag'),
				'value'=>'$data->renderTag()',
				'sortable'=>false,
			),
            array(
                'class'=>'bootstrap.widgets.TbButtonColumn',
                'template'=>'{delete}',
                'buttons'=>array(
                    'delete'=>array(
                        'url'=>'Yii::app()->controller->createUrl("attachment/delete", array("id"=>$data->id))',
                    ),
                ),
            ),
        ),
    )); ?>

</fieldset>