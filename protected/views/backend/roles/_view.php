<?php
/* @var $this RolesController */
/* @var $data Roles */
?>

<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('rol_id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->rol_id), array('view', 'id'=>$data->rol_id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('rol_nombre')); ?>:</b>
	<?php echo CHtml::encode($data->rol_nombre); ?>
	<br />


</div>