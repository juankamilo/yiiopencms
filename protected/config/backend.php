<?php

return CMap::mergeArray(
    require(dirname(__FILE__) . '/common.php'), array(
        'name' => 'YiiOpenCMS - Backend',
        'theme' => 'backend',
        'language'=>'en',
        'import' => array(
            'application.models.backend.*',
            'application.components.backend.*',
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
                'class' => 'system.caching.CFileCache',
            ),
            'urlManager' => array(
                'class' => 'UrlManager',
                'urlFormat' => 'path',
                'showScriptName' => false,
                'rules' => require(dirname(__FILE__) . '/routes-back.php'),
            ),
        ),
        'params' => array(),
    )
);
