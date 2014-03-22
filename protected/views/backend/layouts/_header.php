
<?php $this->widget('bootstrap.widgets.TbNavbar', array(
    'type'=>'inverse', // null or 'inverse'
    'collapse'=>true, // requires bootstrap-responsive.css
    'fluid'=>true, // full width
    'items'=>array(
        array(
            'class'=>'bootstrap.widgets.TbMenu',
            'items'=>array(
                array('label'=>'Home', 'url'=>'#', 'active'=>true),
                
            ),
        ),
        array(
            'class'=>'bootstrap.widgets.TbMenu',
            'htmlOptions'=>array('class'=>'pull-right'),
            'items'=>array(
                '---',
                array('label'=>Yii::app()->user->name, 'icon'=>'user white','url'=>'#', 'items'=>array(
                    array('label'=>'Profile', 'icon'=>'user','url'=>'#'),
                    array('label'=>'Action', 'icon'=>'tasks','url'=>'#'),
                    array('label'=>'Settings', 'icon'=>'cog','url'=>'#'),
                    '---',
                    array('label'=>'Log Out', 'icon'=>'off', 'url'=>array('/site/logout')),
                )),
            ),
        ),
    ),
)); ?>
<div class="menu-left row-fluid">
<?php if(isset($this->breadcrumbs)):?>
        <?php $this->widget('bootstrap.widgets.TbBreadcrumbs', array(
                'links'=>$this->breadcrumbs,
        )); ?><!-- breadcrumbs -->
<?php endif?>
<?php $this->widget('bootstrap.widgets.TbMenu', array(
    'type'=>'list',
    'items'=>array(
        
        array('label'=>'CMS','visible'=>Yii::app()->user->checkAccess('cms')),
            array('label'=>'Pages', 'icon'=>'file white', 'url'=>array('/cms/page/index'),'visible'=>Yii::app()->user->checkAccess('cms')),
            array('label'=>'Block', 'icon'=>'folder-open white', 'url'=>array('/cms/block/index'),'visible'=>Yii::app()->user->checkAccess('cms')),
            array('label'=>'Menu', 'icon'=>'book white', 'url'=>array('/cms/menu/index'),'visible'=>Yii::app()->user->checkAccess('cms')),
        '---',
        array('label'=>'Users','visible'=>Yii::app()->user->checkAccess('admin')),
            array('label'=>'Users', 'icon'=>'th-list white', 'url'=>array('/usuarios/admin'),'visible'=>Yii::app()->user->checkAccess('admin')),
            array('label'=>'Rols', 'icon'=>'book white', 'url'=>array('/roles/admin'),'visible'=>Yii::app()->user->checkAccess('admin')),
         
    ),
    
    
   
)); ?>
</div>

