<div class="cms-page">
    <div class="inner">
        <?php /*<h1 class="cms-page-heading"><?php echo CHtml::encode($heading); ?></h1> */?>
        <div class="cms-page-content">
            <?php echo $content ?>
        </div>
        <?php if (Yii::app()->cms->checkAccess()): ?>
            <?php $this->widget('bootstrap.widgets.TbButton',array(
                    'icon'=>'glyphicon glyphicon-pencil',
                    'url'=>array('/backend/cms/page/update','id'=>$model->id),
                    'type'=>'inverse',
                    'size'=>'small',
                    'htmlOptions'=>array('class'=>'edit-link'),
            )); ?>
        <?php endif ?>
    </div>
</div>