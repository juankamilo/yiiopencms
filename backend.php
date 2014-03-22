<?php

$environment = require_once(dirname(__FILE__).'/environment.php');

$config = dirname(__FILE__) . "/protected/config/backend/{$environment}.php";
$yii = '/opt/framework/yii.php';

require_once($yii);
Yii::createWebApplication($config)->runEnd('backend');

