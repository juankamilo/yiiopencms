<fieldset class="form-content">

	<h2><?php echo Yii::t('CmsModule.core', 'Content') ?></h2>

	<?php $this->widget('bootstrap.widgets.TbTabs',array(
		'type'=>'pills',
		'tabs'=>$this->getLanguageTabs($form, $model),
	)); ?>

</fieldset>
