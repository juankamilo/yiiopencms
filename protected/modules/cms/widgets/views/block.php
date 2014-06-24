<div class="cms-block">

	<div class="cms-block-content">
		<?php echo $content; ?>
	</div>

	<?php if (Yii::app()->cms->checkAccess()): ?>
		<?php $this->widget('bootstrap.widgets.TbButton',array(
			'icon'=>'glyphicon glyphicon-pencil',
			'url'=>array('/backend/cms/block/update','id'=>$model->id),
			'type'=>'inverse',
			'size'=>'small',
			'htmlOptions'=>array('class'=>'edit-link'),
		)); ?>
	<?php endif ?>

</div>
