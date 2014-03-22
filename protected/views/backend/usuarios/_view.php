<?php
/* @var $this UsuariosController */
/* @var $data Usuarios */
?>

<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('usu_id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->usu_id), array('view', 'id'=>$data->usu_id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('usu_nombre')); ?>:</b>
	<?php echo CHtml::encode($data->usu_nombre); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('usu_login')); ?>:</b>
	<?php echo CHtml::encode($data->usu_login); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('usu_clave')); ?>:</b>
	<?php echo CHtml::encode($data->usu_clave); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('usu_activo')); ?>:</b>
	<?php echo CHtml::encode($data->usu_activo); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('usu_correo')); ?>:</b>
	<?php echo CHtml::encode($data->usu_correo); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('usu_session')); ?>:</b>
	<?php echo CHtml::encode($data->usu_session); ?>
	<br />

	<?php /*
	<b><?php echo CHtml::encode($data->getAttributeLabel('rol_id')); ?>:</b>
	<?php echo CHtml::encode($data->rol_id); ?>
	<br />

	*/ ?>

</div>