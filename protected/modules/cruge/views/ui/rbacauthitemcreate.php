<?php
	/*
		$model:  es una instancia que implementa a CrugeAuthItemEditor
	*/
	
?>
<h1><?php echo ucwords(CrugeTranslator::t("Creando")." ".CrugeTranslator::t($model->categoria));?></h1>
<?php $this->renderPartial('_authitemform',array('model'=>$model),false);?>