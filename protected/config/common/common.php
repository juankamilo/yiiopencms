<?php
/**
 * common configuration with different tier
 */
return array(
    'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '../..',
    'import' => array(
        'application.components.*',
        'application.helpers.*',
        'application.modules.cruge.components.*',
        'application.modules.cruge.extensions.crugemailer.*',

    ),
    'preload'=>array('log'),
    'aliases' => array(
        'bootstrap' => ('ext.bootstrap'),
    ),
    'modules'=>array(
        'cms'=>array(
            'class' => 'application.modules.cms.CmsModule',
        ),
        'cruge'=>array(

                'class' => 'application.modules.cruge.CrugeModule',
                //'class' => 'application.components.backend.CrugeActions',

                'tableprefix'=>'cruge_',

                // para que utilice a protected.modules.cruge.models.auth.CrugeAuthDefault.php
                //
                // en vez de 'default' pon 'authdemo' para que utilice el demo de autenticacion alterna
                // para saber mas lee documentacion de la clase modules/cruge/models/auth/AlternateAuthDemo.php
                //
                'availableAuthMethods'=>array('default'),


                'availableAuthModes'=>array('username','email'),

                // url base para los links de activacion de cuenta de usuario
                'baseUrl'=>'http://www.yiicms.com/',

                 // NO OLVIDES PONER EN FALSE TRAS INSTALAR
                 'debug'=>true,
                 'rbacSetupEnabled'=>true,
                 'allowUserAlways'=>true,

                // MIENTRAS INSTALAS..PONLO EN: false
                // lee mas abajo respecto a 'Encriptando las claves'
                //
                'useEncryptedPassword' => false,

                // Algoritmo de la función hash que deseas usar
                // Los valores admitidos están en: http://www.php.net/manual/en/function.hash-algos.php
                'hash' => 'md5',


                // Estos tres atributos controlan la redirección del usuario. Solo serán son usados si no
                // hay un filtro de sesion definido (el componente MiSesionCruge), es mejor usar un filtro.
                //  lee en la wiki acerca de:
                //   "CONTROL AVANZADO DE SESIONES Y EVENTOS DE AUTENTICACION Y SESION"
                //
                // ejemplo:
                //		'afterLoginUrl'=>array('/site/welcome'),  ( !!! no olvidar el slash inicial / )
                //		'afterLogoutUrl'=>array('/site/page','view'=>'about'),
                //
                'afterLoginUrl'=>null,
                'afterLogoutUrl'=>null,
                'afterSessionExpiredUrl'=>null,


                // manejo del layout con cruge.
                //
                'loginLayout'=>'//layouts/column2',
                'registrationLayout'=>'//layouts/column2',
                'activateAccountLayout'=>'//layouts/column2',
                'editProfileLayout'=>'//layouts/column2',
                // en la siguiente puedes especificar el valor "ui" o "column2" para que use el layout
                // de fabrica, es basico pero funcional.  si pones otro valor considera que cruge
                // requerirá de un portlet para desplegar un menu con las opciones de administrador.
                //
                'generalUserManagementLayout'=>'ui',


                // permite indicar un array con los nombres de campos personalizados,
                // incluyendo username y/o email para personalizar la respuesta de una consulta a:
                // $usuario->getUserDescription();
                'userDescriptionFieldsArray'=>array('email'),


        ),
    ),
    'components' => array(
        'messages'=>array(
            'class'=>'CDbMessageSource',
            'sourceMessageTable' => 'cms_source_message',
            'translatedMessageTable' => 'cms_message',
            'onMissingTranslation' => function($event) {Yii::app()->cms->missingTranslation($event);},
        ),
        'bootstrap' => array(
            'class' => 'bootstrap.components.Bootstrap',
        ),
        'cms'=>array(
            'class'=>'cms.components.Cms',
            'defaultLocale'=>'en',
            'languages'=>array('en'=>'English', 'es'=>'Español', 'br'=>'Portuges'),
        ),
        'curl' => array(
            'class' => 'ext.curl.Curl',
        ),
        'image'=>array(
                'class'=>'ext.image.components.ImageManager',
                'versions'=>array(
                        'span1'=>array('width'=>60, 'height'=>45, 'format'=>'jpg', 'resizeMethod'=>'adaptiveResize'),
                        'span2'=>array('width'=>140, 'height'=>105, 'format'=>'jpg', 'resizeMethod'=>'adaptiveResize'),
                        'span3'=>array('width'=>220, 'height'=>165, 'format'=>'jpg', 'resizeMethod'=>'adaptiveResize'),
                        'span4'=>array('width'=>300, 'height'=>225, 'format'=>'jpg', 'resizeMethod'=>'adaptiveResize'),
                        'span5'=>array('width'=>380, 'height'=>285, 'format'=>'jpg', 'resizeMethod'=>'adaptiveResize'),
                        'span6'=>array('width'=>460, 'height'=>345, 'format'=>'jpg', 'resizeMethod'=>'adaptiveResize'),
                        'span7'=>array('width'=>540, 'height'=>405, 'format'=>'jpg', 'resizeMethod'=>'adaptiveResize'),
                        'span8'=>array('width'=>620, 'height'=>465, 'format'=>'jpg', 'resizeMethod'=>'adaptiveResize'),
                        'span9'=>array('width'=>700, 'height'=>525, 'format'=>'jpg', 'resizeMethod'=>'adaptiveResize'),
                        'span10'=>array('width'=>780, 'height'=>585, 'format'=>'jpg', 'resizeMethod'=>'adaptiveResize'),
                        'span11'=>array('width'=>860, 'height'=>645, 'format'=>'jpg', 'resizeMethod'=>'adaptiveResize'),
                        'span12'=>array('width'=>940, 'height'=>705, 'format'=>'jpg', 'resizeMethod'=>'adaptiveResize'),
                ),
        ),
        'request'=>array(
            'enableCookieValidation'=>true,
            //'enableCsrfValidation'=>true,
        ),
        'user'=>array(

                'allowAutoLogin'=>true,
                'class' => 'application.modules.cruge.components.CrugeWebUser',
                'loginUrl' => array('/cruge/ui/login'),
        ),
        'authManager' => array(
                'class' => 'application.modules.cruge.components.CrugeAuthManager',
        ),
        'format' => array(
                'datetimeFormat'=>"d M, Y h:m:s a",
        ),
        'crugemailer'=>array(
                'class' => 'application.modules.cruge.components.CrugeMailer',
                'mailfrom' => 'email-desde-donde-quieres-enviar-los-mensajes@xxxx.com',
                'subjectprefix' => 'Tu Encabezado del asunto - ',
                'debug' => true,
        ),

    ),

    'params' => require(dirname(__FILE__) . '/params.php'),
    'behaviors' => array(
        // separate frontend and backend
        'runEnd' => array(
            'class' => 'application.components.WebApplicationEndBehavior',
        ),
    ),
);
