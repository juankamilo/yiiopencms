<?php Yii::app()->clientScript->registerCoreScript('jquery.ui');
Yii::app()->clientScript->registerScript('CmsMenuController#cmsMenuItemGrid_'.$locale,"
	!function($) {
		var grid = $('#cmsMenuItemGrid_".$locale."');

		grid.find('tbody').children().each(function() {
			$(this).attr('id', 'CmsMenu_sortable_' + $(this).find('span.id').html());
		});

		grid.find('tbody').sortable({
			axis: 'y',
			containment: 'parent',
			cursor: 'pointer',
			//delay: 100,
			//distance: 5,
			forcePlaceholderSize: true,
			tolerance: 'pointer',
			placeholder: 'sortable-placeholder',
			helper: function(event, tr) {
				var helper = tr.clone();
				helper.children().each(function(index) {
					$(this).width(tr.children().eq(index).width());
				});
				return helper;
			},
			stop: function(event, ui) {
				grid.addClass('grid-view-loading');
			},
			update: function(event, ui) {
				$.ajax({
					type: 'POST',
					url: '".Yii::app()->controller->createUrl('ajaxSortable')."',
					data: { id: ".$model->menu->id.", data: $(this).sortable('toArray') },
					complete: function(jqXHR, textStatus) {
						grid.removeClass('grid-view-loading');
					}
				});
			},
		}).disableSelection();
	}(jQuery);
"); ?>

<?php $this->widget('bootstrap.widgets.TbButton',array(
	'icon'=>'plus white',
	'label'=>Yii::t('CmsModule.core','Add link'),
	'url'=>array('menuItem/add','menuId'=>$model->menu->id,'locale'=>$locale),
	'type'=>'primary',
	'htmlOptions'=>array('class'=>'add-button'),
)); ?>

<?php $this->widget('bootstrap.widgets.TbGridView',array(
	'id'=>'cmsMenuItemGrid_'.$locale,
	'type'=>array('striped','condensed'),
	'dataProvider'=>$model->menu->getMenuItems($locale),
	'template'=>'{items} {pager}',
	'showTableOnEmpty'=>false,
	'filter'=>null,
	'columns'=>array(
		array(
			'type'=>'raw',
			'value'=>'\'<i class="icon-resize-vertical"></i> <span class="id" style="display: none">\'.$data->id.\'</span>\'',
			'htmlOptions'=>array('class'=>'draggable'),
		),
		array(
			'name'=>'label',
			'type'=>'raw',
			'value'=>'CHtml::link(CHtml::encode(ucfirst($data->label)),array("menuItem/update","id"=>$data->id,"locale"=>"'.$locale.'"))',
		),
		'url',
		array(
			'name'=>'visible',
			'value'=>'Yii::app()->format->formatBoolean($data->visible)',
		),
		array(
			'class'=>'bootstrap.widgets.TbButtonColumn',
			'template'=>'{update} {delete}',
			'updateButtonUrl'=>'Yii::app()->createUrl("cms/menuItem/update", array("id"=>$data->id));',
                        'deleteButtonUrl'=>'Yii::app()->createUrl("cms/menuItem/delete", array("id"=>$data->id));',
		),
	),
)) ?>
