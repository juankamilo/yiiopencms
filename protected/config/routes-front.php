<?php

return array(

    '<lang:[\w_]+>/<parent>/<name>-<id:\d+>'=>'cms/page/view',
    '<lang:[\w_]+>/<name>-<id:\d+>'=>'cms/page/view',
    '<lang:[\w_]+>/' => 'site/index',
    '/'=>'site/index',
    '<lang:[\w_]+>/<action:\w+>'=>'site/<action>',
    '<lang:[\w_]+>/<controller:\w+>/<action:\w+>/*'=>'<controller>/<action>',
    '<lang:[\w_]+>/<controller:\w+>/<id:\d+>'=>'<controller>/view',
    '<lang:[\w_]+>/<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
    '<lang:[\w_]+>/<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
    '<lang:[\w_]+>/<action:.*>'=>'site/<action>',

);
