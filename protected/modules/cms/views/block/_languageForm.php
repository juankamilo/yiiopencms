<div class="control-group">
    <?php echo $form->labelEx($model,'['.$model->locale.']body') ?>
    <div class="controls">
	    <?php $this->widget('cms.widgets.markitup.CmsMarkItUp',array(
	        'model'=>$model,
	        'attribute'=>'['.$model->locale.']body',
	        'set'=>'html',
	    )) ?>
	    <?php echo $form->error($model,'['.$model->locale.']body') ?>

	    <?php $this->renderPartial('_tags'); ?>
    </div>
</div>
