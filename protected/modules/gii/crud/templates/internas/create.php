<?php
/**
 * The following variables are available in this template:
 * - $this: the CrudCode object
 */
?>
<?php echo "<?php\n"; ?>
/* @var $this <?php echo $this->getControllerClass(); ?> */
/* @var $model <?php echo $this->getModelClass(); ?> */

<?php
$label=$this->class2name($this->modelClass);
echo "\$this->breadcrumbs=array(
	'$label'=>array('index'),
	'Create',
);\n";
?>

$this->menu=array(
	array('label'=>'List <?php echo $this->modelClass; ?>', 'url'=>array('index')),
	array('label'=>'Manage <?php echo $this->modelClass; ?>', 'url'=>array('admin')),
);
?>
<div class="module2">
    <header>
        <h3>Create <?php echo $this->modelClass; ?></h3>
    </header>
    <div class="module_content">
<?php echo "<?php echo \$this->renderPartial('_form', array('model'=>\$model)); ?>"; ?>
    </div>
</div>