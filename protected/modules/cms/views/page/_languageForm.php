<?php echo $form->textFieldRow($model,'['.$model->locale.']heading',array('class'=>'input-xxlarge')); ?>

<div class="control-group">
    <?php echo $form->labelEx($model,'['.$model->locale.']body'); ?>
    <div class="controls">
	    <?php $this->widget('cms.widgets.markitup.CmsMarkItUp',array(
	        'model'=>$model,
	        'attribute'=>'['.$model->locale.']body',
	        'set'=>'cmshtml',
	    )) ?>
	    <?php echo $form->error($model,'['.$model->locale.']body'); ?>

	    <?php $this->renderPartial('_tags'); ?>
    </div>
</div>

<hr />

<h3><?php echo Yii::t('CmsModule.core','Properties'); ?></h3>

<?php echo $form->textFieldRow($model,'['.$model->locale.']url',array('class'=>'input-xxlarge')) ?>
<?php echo $form->textFieldRow($model,'['.$model->locale.']pageTitle',array('class'=>'input-xxlarge')) ?>
<?php echo $form->textFieldRow($model,'['.$model->locale.']breadcrumb',array('class'=>'input-xxlarge')) ?>
<?php echo $form->textFieldRow($model,'['.$model->locale.']metaTitle',array('class'=>'input-xxlarge')) ?>
<?php echo $form->textAreaRow($model,'['.$model->locale.']metaDescription',array('class'=>'input-xxlarge','rows'=>3)) ?>
<?php echo $form->textFieldRow($model,'['.$model->locale.']metaKeywords',array('class'=>'input-xxlarge')) ?>
