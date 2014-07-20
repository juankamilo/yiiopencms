<?php

$environment = require_once(dirname(__FILE__).'/environment.php');

$config = dirname(__FILE__) . "/protected/config/frontend/{$environment}.php";

// change the following paths if necessary
//$yii=dirname(__FILE__).'/../../framework/yii.php';
$yii = '/opt/framework/yii.php';

require_once($yii);

Yii::createWebApplication($config)->runEnd('frontend');
