<?php
/**
 * The following variables are available in this template:
 * - $this: the CrudCode object
 */
?>
<?php echo "<?php\n"; ?>
/* @var $this <?php echo $this->getControllerClass(); ?> */
/* @var $dataProvider CActiveDataProvider */

<?php
$label=$this->class2name($this->modelClass);
echo "\$this->breadcrumbs=array(
	'$label',
);\n";
?>

$this->menu=array(
	array('label'=>'Create <?php echo $this->modelClass; ?>', 'url'=>array('create')),
	array('label'=>'Manage <?php echo $this->modelClass; ?>', 'url'=>array('admin')),
);
?>
<div class="module2">
    <header>
        <h3><?php echo $label; ?></h3>
    </header>
    <div class="module_content">
<?php echo "<?php"; ?> $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
    </div>
</div>