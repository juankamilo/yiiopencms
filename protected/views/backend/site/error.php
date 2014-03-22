<?php
$this->pageTitle=Yii::app()->name . ' - Error';
$this->breadcrumbs=array(
	'Error',
);
?>
<div id="centrar">
    <div id="contenedorc">
        <div id="reserva-head">

            <h2><?php echo CHtml::encode($message); ?></h2>

            <div class="error">
                <?php //echo CHtml::encode($message); ?>
            </div>
        </div>
    </div>
</div>