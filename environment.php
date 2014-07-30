<?php

$production = false;
date_default_timezone_set('America/Bogota');

if ($production){
    error_reporting(0);
    defined('YII_ENV') or define('YII_ENV','prod');
    defined('YII_DEBUG') or define('YII_DEBUG',false);
}else{
    defined('YII_ENV') or define('YII_ENV','local');
    defined('YII_DEBUG') or define('YII_DEBUG',true);
    defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',7);
}

defined('YII_ENV_LOCAL') or define('YII_ENV_LOCAL',YII_ENV == 'local');
defined('YII_ENV_PROD') or define('YII_ENV_PROD',YII_ENV == 'prod');

return YII_ENV;
