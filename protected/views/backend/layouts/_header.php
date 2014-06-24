<?php
$this->widget('bootstrap.widgets.TbNavbar', array(
    'type'=>'inverse', // null or 'inverse'
    'collapse'=>true, // requires bootstrap-responsive.css
    'fluid'=>true, // full width
    'items'=>array(
        array(
            'class'=>'bootstrap.widgets.TbMenu',
            'items'=>array(

                array('label'=>'CMS','visible'=>Yii::app()->user->checkAccess('cms'),'active'=>$this->id=='cms'?true:false, 'items'=>array(
                    array('label'=>'Pages', 'icon'=>'file', 'url'=>array('/cms/page/index'),'visible'=>Yii::app()->user->checkAccess('cms')),
                    array('label'=>'Block', 'icon'=>'folder-open', 'url'=>array('/cms/block/index'),'visible'=>Yii::app()->user->checkAccess('cms')),
                    array('label'=>'Menu', 'icon'=>'book', 'url'=>array('/cms/menu/index'),'visible'=>Yii::app()->user->checkAccess('cms')),
                    array('label'=>'PROMOS','visible'=>Yii::app()->user->checkAccess('manage')),
                    array('label'=>'List', 'icon'=>'th-list', 'url'=>array('/promocion/admin'),'visible'=>Yii::app()->user->checkAccess('manage')),
                    array('label'=>'Messages','visible'=>Yii::app()->user->checkAccess('manage')),
                    array('label'=>'List', 'icon'=>'th-list', 'url'=>array('/cms/message/index'),'visible'=>Yii::app()->user->checkAccess('manage')),

                )),
                array('label'=>'Logs','visible'=>(Yii::app()->user->isSuperAdmin),'active'=>$this->id=='yiiLog'?true:false, 'items'=>array(
                    array('label'=>'List', 'icon'=>'fire', 'url'=>array('/yiiLog/admin'),'visible'=>(Yii::app()->user->isSuperAdmin)),
                )),
                array('label'=>'Administrar Usuarios','visible'=>Yii::app()->user->checkAccess('admin'),
                    'class'=>'bootstrap.widgets.BootMenu',
                        'items'=> Yii::app()->user->ui->adminItems,
                ),

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
));
