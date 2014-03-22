<?php

return array(
    
    '<lang:[\w_]+>/<name><id:\d+>'=>'cms/page/view',
    '<lang:(es|br|en)>/' => 'site/index',
    '/'=>'site/index',
    '<lang:(es|br|en)>/<action:\w+>'=>'site/<action>',
    '<lang:(es|br|en)>/<controller:\w+>/<action:\w+>/*'=>'<controller>/<action>',
    '<lang:(es|br|en)>/<controller:\w+>/<id:\d+>'=>'<controller>/view',
    '<lang:(es|br|en)>/<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
    '<lang:(es|br|en)>/<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
                        
);