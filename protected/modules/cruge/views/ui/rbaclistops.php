<h1><?php echo ucwords(CrugeTranslator::t("operaciones"));?></h1>

<div class='auth-item-create-button'>
<?php echo CHtml::link(CrugeTranslator::t("Crear Nueva Operacion")
	,Yii::app()->user->ui->getRbacAuthItemCreateUrl(CAuthItem::TYPE_OPERATION));?>
</div>

<?php 
	echo CrugeTranslator::t("Filtrar por Controlador:");
	$ar = array(
		'0'=>CrugeTranslator::t('Ver Todo'),
		'1'=>CrugeTranslator::t('Otras'),
		'2'=>CrugeTranslator::t('Cruge'),
		//'3'=>CrugeTranslator::t('Controladoras'),
	);
	foreach(Yii::app()->user->rbac->enumControllers() as $c)
		$ar[$c] = $c;
	// build list
	echo "<ul class='cruge_filters'>";
	foreach($ar as $filter=>$text)
		echo "<li>".CHtml::link($text, array('/cruge/ui/rbaclistops',
			'filter'=>$filter))."</li>";
	echo "</ul>";
?>

<?php $this->renderPartial('_listauthitems'
	,array('dataProvider'=>$dataProvider)
	,false
	);?>
