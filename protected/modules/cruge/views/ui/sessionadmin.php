<div class="form">
<h1><?php echo ucwords(CrugeTranslator::t("sesiones de usuario"));?></h1>
<?php 
$this->widget(Yii::app()->user->ui->CGridViewClass, array(
    'dataProvider'=>$dataProvider,
    'columns'=>array(
		'idsession',
		array('name'=>'iduser','htmlOptions'=>array('width'=>'50px')),
		array('name'=>'sessionname','filter'=>''),
		array('name'=>'status','filter'=>array(1=>'Activa',0=>'Cerrada')
			,'value'=>'$data->status==1 ? \'activa\' : \'cerrada\' '),
		array('name'=>'created','type'=>'datetime'),
		array('name'=>'lastusage','type'=>'datetime'),
		array('name'=>'usagecount','type'=>'number'),
		array('name'=>'expire','type'=>'datetime'),
		'ipaddress',
		array(
			'class'=>'CButtonColumn',
			'template' => '{delete}',
			'deleteConfirmation'=>CrugeTranslator::t("Esta seguro de eliminar esta sesion ?"),
			'buttons' => array(
					'delete'=>array(
						'label'=>CrugeTranslator::t("eliminar sesion"),
						'imageUrl'=>Yii::app()->user->ui->getResource("delete.png"),
						'url'=>'array("sessionadmindelete","id"=>$data->getPrimaryKey())'
					),
				),	
		)	
	),
	'filter'=>$model,
));
?>
</div>