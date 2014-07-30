<?php

return CMap::mergeArray(
    require(dirname(__FILE__) . '/common.php'), array(
        'name' => 'Yii OpenCMS',
        'theme' => 'frontend',
        'language'=>'en',
        'import' => array(
            'application.models.frontend.*',
            'application.components.frontend.*',
            //'ext.debugtoolbar.XWebDebugRouter',
        ),
        'components' => array(
            'log' => array(
                'class' => 'CLogRouter',
                'routes' => array(
                    array(
                        'class' => 'CFileLogRoute',
                        'levels' => 'error, warning, trace, info',
                    ),
                    array(
                        'class'=>'CDbLogRoute',
                        'levels'=>'error, warning, tarjeta, solicitud',
                        'connectionID'=>'db',
                    ),
                    // debug toolbar configuration
                    // array(
                    //     'class' => 'XWebDebugRouter',
                    //     'config' => 'alignLeft, opaque, runInDebug, fixedPos, collapsed, yamlStyle',
                    //     'levels' => 'error, warning, trace, profile, info',
                    //     'allowedIPs' => array('127.0.0.1', $_SERVER['REMOTE_ADDR']),
                    // ),
                ),
            ),
            'cache' => array(
                'class'=>'system.caching.CFileCache',
            ),
            'urlManager' => array(
                'class' => 'UrlManager',
                'urlFormat' => 'path',
                'showScriptName' => false,
                'rules' => require(dirname(__FILE__) . '/routes-front.php'),
            ),

        ),
        'params'=> array(

        ),
    )
);
