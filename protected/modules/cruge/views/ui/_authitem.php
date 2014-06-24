<?php 
	/*
		esta es una subvista referenciada por: 
				_listauthitems.php
		quien a su vez es renderizada por:
				rbaclisttasks.php
				rbaclistroles.php
				rbaclistops.php
			
		$data es una instancia de CAuthItem
	*/

	$rbac = Yii::app()->user->rbac;


	$asignaciones = $rbac->getCountUsersAssigned($data->name);
	
	$referencias =  $rbac->getParents($data->name);
	$count_ref = count($referencias);

	// da un color especial a aquellos TASK que son marcadas como
	// MENUES o SUBMENUES usando la sintaxis de la descripcion del CAuthItem
	//
	$colorEspecialBkTaskTipoMenuitem='';
	if($data->type == CAuthItem::TYPE_TASK){
		$extra='';
		if($rbac->isTaskTopMenuItem($data))
			$extra = 'border: 2px solid gray;';
		if($rbac->isTaskMenuItem($data))	
			$colorEspecialBkTaskTipoMenuitem="style='background-color: #ffffe0;{$extra}'";
		if($rbac->isTaskSubMenuItem($data)){	
			$colorEspecialBkTaskTipoMenuitem="style='background-color: #e0ffff;{$extra}'";
			if(!$rbac->getParentMenuAuthItem($data))
				$colorEspecialBkTaskTipoMenuitem="style='background-color: #ffaaaa;{$extra}'";
		}
	}

	
	// crea un DropDownList con las operaciones de la tarea
	// pre seleccionando aquella que esta marcada en la sintaxis de la tarea
	//
	//	el evento 'onchange' del dropdown sera manejado en la vista maestra:
	//		_listaauthitems.php
	//
	$oplist = '';
	if($data->type == CAuthItem::TYPE_TASK){
		if($rbac->isTaskSubMenuItem($data)){
			// enumera las operaciones bajo esta tarea	
			$oplistitems = array();
			foreach($rbac->getItemChildren($data->name) as $item)
				if($item->type == CAuthItem::TYPE_OPERATION)
					if(strtolower(substr($item->name,0,7))=='action_')
						$oplistitems[] = $item;

			if(!empty($oplistitems)){
				// tiene operaciones hijas
				$current_action = $rbac->getTaskActionItemName($data);
				$oplist = CHtml::dropDownList('crugeavailableops_'.$data->name
						,$current_action
						,array(''=>'--'.CrugeTranslator::t('seleccione action')
							.'--')+CHtml::listData($oplistitems,'name','name')
						,array('alt'=>$data->name)
					);
			}
		}	
	}

	//  a las TAREAS que son menues de 1er nivel les crea un link ajax
	//	para que el usuario cree una nueva tarea hija (sub menu) con 
	//  la sintaxis de enlace lista.
	$newChildTask='';
	if($data->type == CAuthItem::TYPE_TASK){
		if($rbac->isTaskTopMenuItem($data)){

			$url = Yii::app()->user->ui->getRbacAuthItemCreateUrl(
				CAuthItem::TYPE_TASK, $data->name);

			$newChildTask = CHtml::link(
				 CrugeTranslator::t("Nuevo sub menu"),$url);
		}
	}


?>

<div class='row' <?php echo $colorEspecialBkTaskTipoMenuitem;?> >
	<div class='col authname'><?php echo $data->name;?></div>
	
	
	<div class='col operacion'>
		<?php echo CHtml::link(CrugeTranslator::t("propiedades"),
			Yii::app()->user->ui->getRbacAuthItemUpdateUrl($data->name));?>
	</div>

	<?php if($data->type != CAuthItem::TYPE_OPERATION) { ?>
	<div class='col operacion'>
		<?php echo CHtml::link(CrugeTranslator::t("editar permisos"),
			Yii::app()->user->ui->getRbacAuthItemChildItemsUrl($data->name));?>
	</div>
	<?php } ?>

	<div class='col operacion'>
		<b><?php 
			if($asignaciones > 0) 
				echo "<span style='cursor: pointer;' title='".CrugeTranslator::t("Usuarios a los que les ha sido asignado este ".$rbac->getAuthItemTypeName($data->type))."'>".$asignaciones."&nbsp;".CrugeTranslator::t("asignaciones")."</span>";
			?>
		</b>
	</div>
	
	<div class='col operacion'>
		<?php 	
			$tit = CrugeTranslator::t(
				"muestra aquellos objetos que hacen referencia a ")." ".$data->name."";
			if($count_ref > 0) {
				echo "<a class='referencias' title='$tit' href='#'>".$count_ref." refs.</a>";
				echo "<ul class='detallar-referencias'>";
				foreach($referencias as $ref)
					echo "<li>".CHtml::link(
						$ref->name
						,Yii::app()->user->ui->getRbacAuthItemChildItemsUrl($ref->name)
						,array('target'=>'_blank')
						)."</li>";
				echo "</ul>";
			}
			?>
	</div>
	
	<div class='col operacion operacion-eliminar'>
		<?php 
			$url = '#';
			$imagen = 'delete-off.png';
			$titulo='no puede eliminar porque tiene asignaciones';
			if($asignaciones == 0)
			{
				$titulo='eliminar';
				$url = Yii::app()->user->ui->getRbacAuthItemDeleteUrl($data->name);
				$imagen = 'delete.png';
			}
			echo CHtml::link(CHtml::image(
				Yii::app()->user->ui->getResource($imagen)),$url
				,array('title'=>CrugeTranslator::t($titulo))
				);
		?>
	</div>
	
	
	<div class='col descr'>	
		<?php 	
			if(trim($data->description) != '')
				echo "<hr/>"."<span class='description'>"
					.$data->description."</span>";
		?>
	</div>

	<?php if($oplist != '') { ?>
	<div style='float: right;' title='<?php echo CrugeTranslator::t("action que sera tomado como url del menuitem") ?>' >
		<?php 
			echo CrugeTranslator::t("Action Maestro")." : ".$oplist;
		?>
	</div>
	<?php } ?>

	<?php if($newChildTask != '') { ?>
	<div style='float: right;' title='<?php echo CrugeTranslator::t("Creara un sub menu item enlazado a esta tarea.") ?>' >
		<?php 
			echo $newChildTask;
		?>
	</div>
	<?php } ?>



</div>
