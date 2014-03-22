<div class="form-actions">

	<?php $this->widget('bootstrap.widgets.TbButton',array(
		'label'=>Yii::t('CmsModule.core','Save'),
		'buttonType'=>'submit',
		'type'=>'primary',
	)); ?>

	<?php $this->widget('bootstrap.widgets.TbButton',array(
		'label'=>Yii::t('CmsModule.core','Cancel'),
		'url'=>array('index'),
		'htmlOptions'=>array('confirm'=>Yii::t('CmsModule.core','Are you sure you want to cancel? All changes will be lost.')),
	)); ?>

</div>
