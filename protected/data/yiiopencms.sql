-- phpMyAdmin SQL Dump
-- version 3.5.8.2
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tiempo de generación: 31-07-2014 a las 22:32:34
-- Versión del servidor: 5.5.37-MariaDB
-- Versión de PHP: 5.5.14

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de datos: `yiicms`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cms_attachment`
--

CREATE TABLE IF NOT EXISTS `cms_attachment` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `pageId` int(10) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `extension` varchar(50) NOT NULL,
  `mimeType` varchar(255) NOT NULL,
  `byteSize` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `pageId` (`pageId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cms_block`
--

CREATE TABLE IF NOT EXISTS `cms_block` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated` timestamp NULL DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `published` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `deleted` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `name_deleted` (`name`,`deleted`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Volcado de datos para la tabla `cms_block`
--

INSERT INTO `cms_block` (`id`, `created`, `updated`, `name`, `published`, `deleted`) VALUES
(1, '2014-07-20 02:46:04', NULL, 'Index', 1, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cms_block_content`
--

CREATE TABLE IF NOT EXISTS `cms_block_content` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `blockId` int(10) unsigned NOT NULL,
  `locale` varchar(50) NOT NULL,
  `body` longtext,
  PRIMARY KEY (`id`),
  UNIQUE KEY `blockId_locale` (`blockId`,`locale`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Volcado de datos para la tabla `cms_block_content`
--

INSERT INTO `cms_block_content` (`id`, `blockId`, `locale`, `body`) VALUES
(1, 1, 'en', '<div class="hero-unit"><h1>Welcome to Yii OpenCMS</h1>\r\n<p>Congratulations! You have successfully created your Yii CMS application.</p>\r\n</div>\r\n<h3>Example Content</h3>\r\n<p>You may change the content of this page by modifying the following two files:</p>'),
(2, 1, 'es', '<div class="hero-unit"><h1>Bienvenido a OpenCMS</h1>\r\n<p>Felicidades! Ha creado correctamente la aplicación Yii CMS.</p>\r\n</div>\r\n<h3>Contenido de Ejemplo</h3>\r\n<p>Usted puede cambiar el contenido de esta página modificando los dos archivos siguientes:</p>'),
(3, 1, 'br', '<div class="hero-unit"><h1>Bem-vindo ao Yii OpenCMS</h1>\r\n<p>Felicidades! Ha creado correctamente la aplicación Yii CMS.</p>\r\n</div>\r\n<h3>Contenido de Ejemplo</h3>\r\n<p>Usted puede cambiar el contenido de esta página modificando los dos archivos siguientes:</p>');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cms_menu`
--

CREATE TABLE IF NOT EXISTS `cms_menu` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `created` timestamp NULL DEFAULT NULL,
  `updated` timestamp NULL DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `deleted` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `name_deleted` (`name`,`deleted`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Volcado de datos para la tabla `cms_menu`
--

INSERT INTO `cms_menu` (`id`, `created`, `updated`, `name`, `deleted`) VALUES
(1, '2014-03-22 02:38:10', NULL, 'navigation', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cms_menu_item`
--

CREATE TABLE IF NOT EXISTS `cms_menu_item` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `menuId` int(10) unsigned NOT NULL,
  `locale` varchar(50) NOT NULL,
  `label` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `weight` int(10) unsigned NOT NULL DEFAULT '0',
  `visible` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `menuId_locale` (`menuId`,`locale`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Volcado de datos para la tabla `cms_menu_item`
--

INSERT INTO `cms_menu_item` (`id`, `menuId`, `locale`, `label`, `url`, `weight`, `visible`) VALUES
(1, 1, 'en', 'About Us', 'aboutus', 2, 1),
(2, 1, 'en', 'Home', '//site/index', 0, 1),
(3, 1, 'es', 'Inicio', '//site/index', 0, 1),
(4, 1, 'es', 'Acerca de', 'aboutus', 0, 1),
(5, 1, 'en', 'Get Started', 'GetStarted', 1, 1),
(6, 1, 'es', 'Empieza aquí', 'GetStarted', 0, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cms_message`
--

CREATE TABLE IF NOT EXISTS `cms_message` (
  `id` int(11) NOT NULL DEFAULT '0',
  `language` varchar(16) NOT NULL DEFAULT '',
  `translation` text NOT NULL,
  PRIMARY KEY (`id`,`language`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `cms_message`
--

INSERT INTO `cms_message` (`id`, `language`, `translation`) VALUES
(1, 'br', ''),
(1, 'en', ''),
(1, 'es', ''),
(53, 'br', 'Comentários'),
(53, 'en', 'Body'),
(53, 'es', 'Comentarios'),
(97, 'br', 'Conosco'),
(97, 'en', 'Contact Us'),
(97, 'es', 'Contacto'),
(100, 'br', 'Nome'),
(100, 'en', 'Name'),
(100, 'es', 'Nombre'),
(108, 'br', 'Assunto'),
(108, 'en', 'Subject'),
(108, 'es', 'Asunto'),
(110, 'br', ''),
(110, 'en', 'Please enter the letters as they are shown in the image above. Letters are not case-sensitive.'),
(110, 'es', 'Por favor introduce las letras tal como se muestran en la imagen. Las letras no distinguen entre mayúsculas y minúsculas.'),
(112, 'br', ''),
(112, 'en', 'Submit'),
(112, 'es', 'Enviar'),
(117, 'br', 'Comentários'),
(117, 'en', 'Body'),
(117, 'es', 'Comentarios'),
(119, 'br', 'Código de Verificación'),
(119, 'en', 'Verification Code'),
(119, 'es', 'Código de Verificación'),
(120, 'br', ''),
(120, 'en', 'If you have business inquiries or other questions, please fill out the following form to contact us. Thank you.'),
(120, 'es', 'Si tiene consultas comerciales u otras preguntas, por favor completa el siguiente formulario para contactar con nosotros. Gracias.'),
(122, 'br', ''),
(122, 'en', 'Fields with {ast} are required'),
(122, 'es', 'Campos con {ast} son requeridos'),
(125, 'br', ''),
(125, 'en', 'Contact'),
(125, 'es', 'Contacto');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cms_page`
--

CREATE TABLE IF NOT EXISTS `cms_page` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated` timestamp NULL DEFAULT NULL,
  `parentId` int(10) unsigned NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL,
  `type` varchar(255) DEFAULT NULL,
  `published` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `deleted` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `name_deleted` (`name`,`deleted`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Volcado de datos para la tabla `cms_page`
--

INSERT INTO `cms_page` (`id`, `created`, `updated`, `parentId`, `name`, `type`, `published`, `deleted`) VALUES
(1, '2014-03-22 02:19:46', NULL, 0, 'index', '', 1, 0),
(2, '2014-03-22 02:38:23', NULL, 0, 'about', '', 1, 1),
(3, '2014-03-22 02:39:37', NULL, 0, 'aboutus', '', 1, 0),
(4, '2014-07-30 03:08:38', NULL, 0, 'GetStarted', '0', 1, 0),
(5, '2014-07-30 03:14:46', NULL, 0, 'Empieza-aqui', NULL, 1, 1),
(6, '2014-07-30 03:17:35', NULL, 0, 'Empieza-aqui-4', NULL, 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cms_page_content`
--

CREATE TABLE IF NOT EXISTS `cms_page_content` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pageId` int(10) unsigned NOT NULL,
  `locale` varchar(50) NOT NULL,
  `heading` varchar(255) DEFAULT NULL,
  `body` longtext,
  `url` varchar(255) DEFAULT NULL,
  `pageTitle` varchar(255) DEFAULT NULL,
  `breadcrumb` varchar(255) DEFAULT NULL,
  `metaTitle` varchar(255) DEFAULT NULL,
  `metaDescription` varchar(255) DEFAULT NULL,
  `metaKeywords` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `contentId_locale` (`pageId`,`locale`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=13 ;

--
-- Volcado de datos para la tabla `cms_page_content`
--

INSERT INTO `cms_page_content` (`id`, `pageId`, `locale`, `heading`, `body`, `url`, `pageTitle`, `breadcrumb`, `metaTitle`, `metaDescription`, `metaKeywords`) VALUES
(1, 1, 'en', 'Index', '', 'index', 'Yii OpenCMS ', 'Index', 'Yii OpenCMS ', 'Simple cms based on Yii Framework', ''),
(2, 1, 'es', 'Inicio', '', 'inicio', 'Yii OpenCMS', 'Inicio', 'Yii OpenCMS ', 'Simple cms basado en Yii Framework', ''),
(3, 1, 'br', '', '', '', '', '', '', '', ''),
(4, 2, 'en', 'About Us', '{{image:1}}\r\n<p>\r\nLorem ipsum dolor sit amet, consectetur adipiscing elit. Nam tempor mauris ac purus iaculis congue. Curabitur et eros sem. In lacinia vehicula risus sed molestie. In velit ligula, bibendum sed cursus ac, pellentesque in elit. Integer feugiat eleifend nisl, vel convallis orci placerat eu. Pellentesque porta, leo quis molestie iaculis, ipsum lacus placerat justo, nec mollis lorem erat nec nisl. Proin varius elit vel dui adipiscing hendrerit. Sed mollis vestibulum quam, vel luctus quam imperdiet aliquam. Pellentesque a neque ut erat vulputate blandit. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Morbi et ligula neque, ut elementum lectus. Nam non lacus dolor. Nunc eget orci sed nulla lacinia eleifend viverra sit amet velit. Fusce auctor sem et leo pretium quis tincidunt magna auctor. Mauris porta arcu at lorem bibendum faucibus.</p>', 'about', 'About Us', 'About', '', '', ''),
(5, 2, 'es', '', '', '', '', '', '', '', ''),
(6, 2, 'br', '', '', '', '', '', '', '', ''),
(7, 3, 'en', 'About Us', '{{image:2}}\r\nLorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry''s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.', 'About-Us', 'About Us', 'About-Us', 'About-Us', 'About-Us Description', ''),
(8, 3, 'es', 'Sobre Nosotros', '{{image:2}}\r\nLorem Ipsum es simplemente el texto de relleno de las imprentas y archivos de texto. Lorem Ipsum ha sido el texto de relleno estándar de las industrias desde el año 1500, cuando un impresor (N. del T. persona que se dedica a la imprenta) desconocido usó una galería de textos y los mezcló de tal manera que logró hacer un libro de textos especimen. No sólo sobrevivió 500 años, sino que tambien ingresó como texto de relleno en documentos electrónicos, quedando esencialmente igual al original. Fue popularizado en los 60s con la creación de las hojas "Letraset", las cuales contenian pasajes de Lorem Ipsum, y más recientemente con software de autoedición, como por ejemplo Aldus PageMaker, el cual incluye versiones de Lorem Ipsum.	 	 ', 'sobre-nosotros', 'Sobre Nosotros', 'Sobre Nosotros', 'Sobre Nosotros', '', ''),
(9, 3, 'br', 'Sobre Nós', '{{image:2}}\r\nO Lorem Ipsum é um texto modelo da indústria tipográfica e de impressão. O Lorem Ipsum tem vindo a ser o texto padrão usado por estas indústrias desde o ano de 1500, quando uma misturou os caracteres de um texto para criar um espécime de livro. Este texto não só sobreviveu 5 séculos, mas também o salto para a tipografia electrónica, mantendo-se essencialmente inalterada. Foi popularizada nos anos 60 com a disponibilização das folhas de Letraset, que continham passagens com Lorem Ipsum, e mais recentemente com os programas de publicação como o Aldus PageMaker que incluem versões do Lorem Ipsum.', 'sobre-nos', 'Sobre Nós', 'Sobre Nós', '', '', ''),
(10, 4, 'en', 'Get Started', '<div class="container">\r\n    <div class="span9">\r\n        <section>\r\n            <div class="page-header">\r\n                <h1>1. Download</h1>\r\n            </div>\r\n            <p class="lead">Download the package and unzip its contents in your www folder. \r\n            \r\n            <div class="row-fluid">\r\n                <div class="span6">\r\n                    <h2>Download</h2>\r\n                    <p><strong>Fastest way to get started:</strong> get the application and unzip its contents in your www folder and configure it.</p>\r\n                    <p><a class="btn btn-large btn-primary" href="https://github.com/juankamilo/yiiopencms/archive/master.zip">Download Yii OpemCMS</a></p>\r\n                </div>\r\n                <div class="span6">\r\n                    <h2>Clone</h2>\r\n                    <p>You can also clone the git repository by heading to the GitHub project page and following the instructions there.</p>\r\n                    <p><a class="btn btn-large" href="https://github.com/juankamilo/yiiopencms">Yii OpemCMS on GitHub</a></p>\r\n                </div>\r\n            </div>\r\n<pre>\r\n<span class="com">// Unzip File\r\n// Standard for linux</span>\r\n<span class="str">/var/www/html/</span>\r\n<span class="com">-----</span>\r\n<span class="com">// Standard for Windows</span>\r\n<span class="str">C:\\www\\</span>\r\n            </pre>\r\n\r\n        </section>\r\n\r\n        <section id="configuration">\r\n            <div class="page-header">\r\n                <h1>2. Configuration</h1>\r\n            </div>\r\n\r\n            <p class="lead">Within the download you''ll find the following file structure.</p>\r\n<pre>\r\n<code>\r\n/protected\r\n    config\r\n        backend.php       contains backend configurations\r\n        common.php        contains shared configurations\r\n	dbconnect.php     contains DB configuration\r\n        frontend.php      contains frontend configurations\r\n	params.php        contains global app parameters\r\n	routes-back.php   contains routes for backend app\r\n        routes-front.php  contains routes for frontend app\r\n    controllers\r\n        backend/        contains backend controller\r\n        frontend/       contains frontend controller\r\n    models\r\n        _base           contains base models (generated by gii)\r\n        _common         contains common classes used in backend, frontend\r\n        backend         contains backend-specific classes\r\n        frontend        contains frontend-specific classes\r\n\r\n/environment.php        contains production or develop config\r\n/index.php         	contains Yii Route \r\n\r\n</code>\r\n</pre>\r\n<p>Open <strong>/index.php</strong> and modify it according to the following example:</p>\r\n<pre>\r\n<span class="pun">&lt;?</span><span class="pln">php</span>\r\n\r\n$environment = require_once(dirname(__FILE__).''/environment.php'');\r\n$config = dirname(__FILE__) . "/protected/config/frontend.php";\r\n\r\n<span class="com">// change the following paths</span>\r\n<span class="str">$yii=dirname(__FILE__).''/../framework/yii.php'';</span>\r\nrequire_once($yii);\r\nYii::createWebApplication($config)->runEnd(''frontend'');\r\n</pre>\r\n<p>Import database:</p>\r\n<pre>\r\n<span class="com">// Create db yiiopencms and import de following</span>\r\n<span class="str">protected/data/yiiopencms.sql</span>\r\n</pre>\r\n<p>Open <strong>protected/config/dbconnect.php</strong> and modify it according to the following example:</p>\r\n<pre>\r\n<span class="pun">&lt;?</span><span class="pln">php</span>\r\n\r\nreturn array(\r\n    ''connectionString'' => ''mysql:host=<span class="str">localhost</span>;dbname=<span class="str">yiiopencms</span>'',\r\n    ''emulatePrepare'' => true,\r\n    ''username'' => <span class="str">''admin''</span>,\r\n    ''password'' => <span class="str">''admin''</span>,\r\n    ''charset'' => ''utf8'',\r\n    ''schemaCachingDuration'' => 60*60,\r\n);\r\n</pre>\r\n\r\n</section>\r\n\r\n<section>\r\n	 <div class="page-header">\r\n                <h1>3. Enjoy</h1>\r\n         </div>\r\n<p>Open your browser:</p>\r\n<pre>\r\n<span class="com">// Go to:</span>\r\n<span class="str"><a href="http://localhost/yiiopencms">http://localhost/yiiopencms</a></span>\r\n<span class="str"><a href="http://localhost/yiiopencms/backend">http://localhost/yiiopencms/backend</a></span>\r\nuser:<span class="str">admin</span>\r\npaswd:<span class="str">admin</span>\r\n\r\n</pre>\r\n\r\n</section>\r\n<section>\r\n	<div class="page-header">\r\n		<h1>4. Docs</h1>\r\n	</div>\r\n	<p class="com">Coming soon!</p>\r\n</section>\r\n    </div>\r\n</div>', '', 'Get Started - Yii OpenCMS', '', '', '', ''),
(11, 4, 'es', 'Empieza aquí', '<div class="container">\r\n    <div class="span9">\r\n        <section>\r\n            <div class="page-header">\r\n                <h1>1. Descargar</h1>\r\n            </div>\r\n            <p class="lead">Descarge el archivo y descomprimalo en la carpeta www de su sistema. \r\n            <div class="row-fluid">\r\n                <div class="span6">\r\n                    <h2>Descarga</h2>\r\n                    <p><strong>La forma mas rápida de empezar:</strong> Obtenga la aplicación, descomprimala en la carpeta www de su sistema y configure.</p>\r\n                    <p><a class="btn btn-large btn-primary" href="https://github.com/juankamilo/yiiopencms/archive/master.zip">Download Yii OpemCMS</a></p>\r\n                </div>\r\n                <div class="span6">\r\n                    <h2>Clone</h2>\r\n                    <p>También puedes clonar el repositorio git y seguir las instrucciones.</p>\r\n                    <p><a class="btn btn-large" href="https://github.com/juankamilo/yiiopencms">Yii OpemCMS en GitHub</a></p>\r\n                </div>\r\n            </div>\r\n<pre>\r\n<span class="com">// Descomprima el archivo en\r\n// Ruta estandar en linux</span>\r\n<span class="str">/var/www/html/</span>\r\n<span class="com">-----</span>\r\n<span class="com">// Ruta estandar en Windows</span>\r\n<span class="str">C:\\www\\</span>\r\n            </pre>\r\n\r\n        </section>\r\n\r\n        <section id="configuration">\r\n            <div class="page-header">\r\n                <h1>2. Configuración</h1>\r\n            </div>\r\n\r\n            <p class="lead">Con la descarga encontraras la siguiente estructura de archivos.</p>\r\n<pre>\r\n<code>\r\n/protected\r\n    config\r\n        backend.php       contiene las configuraciones del backend\r\n        common.php        contiene las configuraciones compartidas (entre frontend y backend)\r\n	dbconnect.php     contiene la configuracion de la Base de Datos\r\n        frontend.php      contiene las configuraciones del frontend\r\n	params.php        contiene los parametros globalees de la app\r\n	routes-back.php   contiene las routas para el backend\r\n        routes-front.php  contiene las routas para el frontend\r\n    controllers\r\n        backend/        contiene los controladores exclusivos del backend\r\n        frontend/       contiene los controladores exclusivos del frontend\r\n    models\r\n        _base           contiene los modelos base (Son los generados por gii)\r\n        _common         contiene las classes compartidas usadas en el backend y el frontend\r\n        backend         contiene las classes especificas del backend\r\n        frontend        contiene las classes especificas del frontend\r\n\r\n/environment.php        contiene la configuración para producción o develop\r\n/index.php         	contiene la ruta e inicio de Yii  \r\n\r\n</code>\r\n</pre>\r\n<p>Edite <strong>/index.php</strong> y modifiquelo acorde al siguiente ejemplo:</p>\r\n<pre>\r\n<span class="pun">&lt;?</span><span class="pln">php</span>\r\n\r\n$environment = require_once(dirname(__FILE__).''/environment.php'');\r\n$config = dirname(__FILE__) . "/protected/config/frontend.php";\r\n\r\n<span class="com">// modifique la siguiente ruta, segun el lugar donde tenga su yii framework</span>\r\n<span class="str">$yii=dirname(__FILE__).''/../framework/yii.php'';</span>\r\nrequire_once($yii);\r\nYii::createWebApplication($config)->runEnd(''frontend'');\r\n</pre>\r\n<p>Importe la base de datos:</p>\r\n<pre>\r\n<span class="com">// Cree la db yiiopencms, importe el siguiente archivo</span>\r\n<span class="str">protected/data/yiiopencms.sql</span>\r\n</pre>\r\n<p>Edite <strong>protected/config/dbconnect.php</strong> y modifiquelo acorde al siguiente ejemplo</p>\r\n<pre>\r\n<span class="pun">&lt;?</span><span class="pln">php</span>\r\n\r\nreturn array(\r\n    ''connectionString'' => ''mysql:host=<span class="str">localhost</span>;dbname=<span class="str">yiiopencms</span>'',\r\n    ''emulatePrepare'' => true,\r\n    ''username'' => <span class="str">''admin''</span>,\r\n    ''password'' => <span class="str">''admin''</span>,\r\n    ''charset'' => ''utf8'',\r\n    ''schemaCachingDuration'' => 60*60,\r\n);\r\n</pre>\r\n\r\n</section>\r\n\r\n<section>\r\n	 <div class="page-header">\r\n                <h1>3. Disfrute</h1>\r\n         </div>\r\n<p>Abra su navegador:</p>\r\n<pre>\r\n<span class="com">// Dirigase a:</span>\r\n<span class="str"><a href="http://localhost/yiiopencms">http://localhost/yiiopencms</a></span>\r\n<span class="str"><a href="http://localhost/yiiopencms/backend">http://localhost/yiiopencms/backend</a></span>\r\nuser:<span class="str">admin</span>\r\npaswd:<span class="str">admin</span>\r\n\r\n</pre>\r\n\r\n</section>\r\n<section>\r\n	<div class="page-header">\r\n		<h1>4. Documentacion</h1>\r\n	</div>\r\n	<p class="com">Proximamente!</p>\r\n</section>\r\n    </div>\r\n</div>', 'Empieza-aqui', 'Empieza aquí', 'Empieza aquí', '', '', ''),
(12, 4, 'br', 'Começar', '<div class="container">\r\n    <div class="span9">\r\n        <section>\r\n            <div class="page-header">\r\n<h1>Portuges Comming Soon!</h1>\r\n                <h1>1. Download</h1>\r\n            </div>\r\n            <p class="lead">Download the package and unzip its contents in your www folder. \r\n            \r\n            <div class="row-fluid">\r\n                <div class="span6">\r\n                    <h2>Download</h2>\r\n                    <p><strong>Fastest way to get started:</strong> get the application and unzip its contents in your www folder and configure it.</p>\r\n                    <p><a class="btn btn-large btn-primary" href="https://github.com/juankamilo/yiiopencms/archive/master.zip">Download Yii OpemCMS</a></p>\r\n                </div>\r\n                <div class="span6">\r\n                    <h2>Clone</h2>\r\n                    <p>You can also clone the git repository by heading to the GitHub project page and following the instructions there.</p>\r\n                    <p><a class="btn btn-large" href="https://github.com/juankamilo/yiiopencms">Yii OpemCMS on GitHub</a></p>\r\n                </div>\r\n            </div>\r\n<pre>\r\n<span class="com">// Unzip File\r\n// Standar for linux</span>\r\n<span class="str">/var/www/html/</span>\r\n<span class="com">-----</span>\r\n<span class="com">// Standar for Windows</span>\r\n<span class="str">C:\\www\\</span>\r\n            </pre>\r\n\r\n        </section>\r\n\r\n        <section id="configuration">\r\n            <div class="page-header">\r\n                <h1>2. Configuration</h1>\r\n            </div>\r\n\r\n            <p class="lead">Within the download you''ll find the following file structure.</p>\r\n<pre>\r\n<code>\r\n/protected\r\n    config\r\n        backend.php       contains backend configurations\r\n        common.php        contains shared configurations\r\n	dbconnect.php     contains DB configuration\r\n        frontend.php      contains frontend configurations\r\n	params.php        contains global app parameters\r\n	routes-back.php   contains routes for backend app\r\n        routes-front.php  contains routes for frontend app\r\n    controllers\r\n        backend/        contains backend controller\r\n        frontend/       contains frontend controller\r\n    models\r\n        _base           contains base models (generated by gii)\r\n        _common         contains common classes used in backend, frontend and api\r\n        backend         contains backend-specific classes\r\n        frontend        contains frontend-specific classes\r\n\r\n/environment.php        contains production or develop config\r\n/index.php         	contains Yii Route \r\n\r\n</code>\r\n</pre>\r\n<p>Open <strong>/index.php</strong> and modify it according to the following example:</p>\r\n<pre>\r\n<span class="pun">&lt;?</span><span class="pln">php</span>\r\n\r\n$environment = require_once(dirname(__FILE__).''/environment.php'');\r\n$config = dirname(__FILE__) . "/protected/config/frontend.php";\r\n\r\n<span class="com">// change the following paths</span>\r\n<span class="str">$yii=dirname(__FILE__).''/../framework/yii.php'';</span>\r\nrequire_once($yii);\r\nYii::createWebApplication($config)->runEnd(''frontend'');\r\n</pre>\r\n<p>Import database:</p>\r\n<pre>\r\n<span class="com">// Create db yiiopencms and import de following</span>\r\n<span class="str">protected/data/yiiopencms.sql</span>\r\n</pre>\r\n<p>Open <strong>protected/config/dbconnect.php</strong> and modify it according to the following example:</p>\r\n<pre>\r\n<span class="pun">&lt;?</span><span class="pln">php</span>\r\n\r\nreturn array(\r\n    ''connectionString'' => ''mysql:host=<span class="str">localhost</span>;dbname=<span class="str">yiiopencms</span>'',\r\n    ''emulatePrepare'' => true,\r\n    ''username'' => <span class="str">''admin''</span>,\r\n    ''password'' => <span class="str">''admin''</span>,\r\n    ''charset'' => ''utf8'',\r\n    ''schemaCachingDuration'' => 60*60,\r\n);\r\n</pre>\r\n\r\n</section>\r\n\r\n<section>\r\n	 <div class="page-header">\r\n                <h1>3. Enjoy</h1>\r\n         </div>\r\n<p>Open your browser:</p>\r\n<pre>\r\n<span class="com">// Go to:</span>\r\n<span class="str"><a href="http://localhost/yiiopencms">http://localhost/yiiopencms</a></span>\r\n<span class="str"><a href="http://localhost/yiiopencms/backend">http://localhost/yiiopencms/backend</a></span>\r\nuser:<span class="str">admin</span>\r\npaswd:<span class="str">admin</span>\r\n\r\n</pre>\r\n\r\n</section>\r\n<section>\r\n	<div class="page-header">\r\n		<h1>4. Docs</h1>\r\n	</div>\r\n	<p class="com">Coming soon!</p>\r\n</section>\r\n    </div>\r\n</div>', '', '', '', '', '', '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cms_source_message`
--

CREATE TABLE IF NOT EXISTS `cms_source_message` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category` varchar(32) DEFAULT NULL,
  `message` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=137 ;

--
-- Volcado de datos para la tabla `cms_source_message`
--

INSERT INTO `cms_source_message` (`id`, `category`, `message`) VALUES
(1, 'CmsModule.core', 'Cms'),
(2, 'CmsModule.core', 'Messages'),
(3, 'CmsModule.core', 'Message'),
(4, 'CmsModule.core', 'Create block'),
(5, 'CrugeModule.admin', 'User Manager'),
(6, 'CrugeModule.admin', 'Update Profile'),
(7, 'CrugeModule.admin', 'Create User'),
(8, 'CrugeModule.admin', 'Manage Users'),
(9, 'CrugeModule.admin', 'Custom Fields'),
(10, 'CrugeModule.admin', 'List Profile Fields'),
(11, 'CrugeModule.admin', 'Create Profile Field'),
(12, 'CrugeModule.admin', 'Roles and Assignments'),
(13, 'CrugeModule.admin', 'Roles'),
(14, 'CrugeModule.admin', 'Tasks'),
(15, 'CrugeModule.admin', 'Operations'),
(16, 'CrugeModule.admin', 'Assign Roles to Users'),
(17, 'CrugeModule.admin', 'System'),
(18, 'CrugeModule.admin', 'Sessions'),
(19, 'CrugeModule.admin', 'System Variables'),
(20, 'CmsModule.core', 'Blocks'),
(21, 'CrugeModule.admin', 'Crear un nuevo Campo Personalizado'),
(22, 'CrugeModule.admin', '*** You are working as Super Administrator ***'),
(23, 'CrugeModule.logon', 'Login'),
(24, 'CrugeModule.logon', 'Username'),
(25, 'CrugeModule.logon', 'or'),
(26, 'CrugeModule.logon', 'Email'),
(27, 'CrugeModule.logon', 'Password'),
(28, 'CrugeModule.logon', 'Remember this machine'),
(29, 'CrugeModule.logon', 'Security code'),
(30, 'CrugeModule.logon', 'Lost Password?'),
(31, 'CrugeModule.logon', 'Register'),
(32, 'CrugeModule.logger', 'PERMISSION IS REQUIRED'),
(33, 'CrugeModule.logger', 'Returned User'),
(34, 'CrugeModule.logon', 'Invalid username'),
(35, 'CrugeModule.logon', 'Password may contain numbers or symbols ({symbols}) and between {min} and {max} characters'),
(36, 'CrugeModule.logon', 'Please, confirm checking the checkbox'),
(37, 'CrugeModule.logon', 'Please, check if you understand and accept the terms of use'),
(38, 'CrugeModule.logon', 'Security code is mandatory'),
(39, 'CrugeModule.logon', 'Security code is invalid'),
(40, 'CrugeModule.logon', '''{attribute}'' already in use'),
(41, 'CmsModule.core', 'Pages'),
(42, 'CmsModule.core', 'Create page'),
(43, 'CmsModule.core', 'Created'),
(44, 'CmsModule.core', 'Updated'),
(45, 'CmsModule.core', 'System name'),
(46, 'CmsModule.core', 'Parent'),
(47, 'CmsModule.core', 'Type'),
(48, 'CmsModule.core', '{name} page'),
(49, 'CmsModule.core', 'Content'),
(50, 'CmsModule.core', 'Page'),
(51, 'CmsModule.core', 'Locale'),
(52, 'CmsModule.core', 'Heading'),
(53, 'CmsModule.core', 'Body'),
(54, 'CmsModule.core', 'URL Alias'),
(55, 'CmsModule.core', 'Page Title'),
(56, 'CmsModule.core', 'Breadcrumb'),
(57, 'CmsModule.core', 'Meta Title'),
(58, 'CmsModule.core', 'Meta Description'),
(59, 'CmsModule.core', 'Meta Keywords'),
(60, 'CmsModule.core', 'Published'),
(61, 'CmsModule.core', 'Available tags'),
(62, 'CmsModule.core', 'displays a content block'),
(63, 'CmsModule.core', 'displays a menu'),
(64, 'CmsModule.core', 'creates an URL to a page'),
(65, 'CmsModule.core', 'displays an image'),
(66, 'CmsModule.core', 'creates a link to an attached file'),
(67, 'CmsModule.core', 'creates a mailto link'),
(68, 'CmsModule.core', 'creates a link to a page'),
(69, 'CmsModule.core', 'creates an external link'),
(70, 'CmsModule.core', 'creates a link to an anchor on the page'),
(71, 'CmsModule.core', 'Properties'),
(72, 'CmsModule.core', 'Images'),
(73, 'CmsModule.core', 'Add image'),
(74, 'CmsModule.core', 'No images found.'),
(75, 'CmsModule.core', 'Tag'),
(76, 'CmsModule.core', 'Attachments'),
(77, 'CmsModule.core', 'Add file'),
(78, 'CmsModule.core', 'No attachments found.'),
(79, 'CmsModule.core', 'No parent'),
(80, 'CmsModule.core', 'None'),
(81, 'CmsModule.core', 'Save'),
(82, 'CmsModule.core', 'Cancel'),
(83, 'CmsModule.core', 'Are you sure you want to cancel? All changes will be lost.'),
(84, 'CmsModule.core', 'Menus'),
(85, 'CmsModule.core', 'Create menu'),
(86, 'CmsModule.core', '{name} menu'),
(87, 'CmsModule.core', 'Links'),
(88, 'CmsModule.core', 'Menu'),
(89, 'CmsModule.core', 'Label'),
(90, 'CmsModule.core', 'URL'),
(91, 'CmsModule.core', 'Weight'),
(92, 'CmsModule.core', 'Visible'),
(93, 'CmsModule.core', 'Add link'),
(94, 'CmsModule.core', 'Page updated.'),
(95, 'CmsModule.core', 'You are not allowed to access this page.'),
(96, 'app', 'Discounts on rent a car. Best brands. Best prices. Rentingcarz.com'),
(97, 'app', 'Contact Us'),
(98, 'app', 'Contact Form'),
(99, 'app', 'Regarding'),
(100, 'app', 'Name'),
(101, 'app', 'E-mail'),
(102, 'app', 'Select'),
(103, 'app', 'New Reservation'),
(104, 'app', 'Existing Reservation'),
(105, 'app', 'Refunds for Pay Now'),
(106, 'app', 'Customer Service'),
(107, 'app', 'Others'),
(108, 'app', 'Subject'),
(109, 'app', 'Comments'),
(110, 'app', 'Please enter the letters as they are shown in the image above. Letters are not case-sensitive.'),
(111, 'app', 'Fields with <span class="required">*</span> are required.'),
(112, 'app', 'Submit'),
(113, 'app', 'Information'),
(114, 'CmsModule.core', 'Link added.'),
(115, 'CmsModule.core', 'Message: {message}'),
(116, 'CmsModule.core', 'Message updated.'),
(117, 'app', 'Body'),
(118, 'app', 'Email'),
(119, 'app', 'Verification Code'),
(120, 'app', 'If you have business inquiries or other questions, please fill out the following form to contact us. Thank you.'),
(122, 'app', 'Fields with {ast} are required'),
(123, 'CmsModule.core', 'Message deleted.'),
(124, 'app', 'Please enter the letters as they are shown in the image above.<br/>Letters are not case-sensitive.'),
(125, 'app', 'Contact'),
(126, 'CmsModule.core', 'Page deleted.'),
(127, 'CmsModule.core', 'Image added.'),
(128, 'CmsModule.core', 'Create'),
(129, 'CmsModule.core', 'Block created.'),
(130, 'CmsModule.core', '{name} block'),
(131, 'CmsModule.core', 'Block'),
(132, 'CmsModule.core', 'Block updated.'),
(133, 'CmsModule.core', 'Page created.'),
(134, 'CmsModule.core', '{label} link'),
(135, 'CmsModule.core', 'Link updated.'),
(136, 'CmsModule.core', 'Menu updated.');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cruge_authassignment`
--

CREATE TABLE IF NOT EXISTS `cruge_authassignment` (
  `userid` int(11) NOT NULL,
  `bizrule` text,
  `data` text,
  `itemname` varchar(64) NOT NULL,
  PRIMARY KEY (`userid`,`itemname`),
  KEY `fk_cruge_authassignment_cruge_authitem1` (`itemname`),
  KEY `fk_cruge_authassignment_user` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cruge_authitem`
--

CREATE TABLE IF NOT EXISTS `cruge_authitem` (
  `name` varchar(64) NOT NULL,
  `type` int(11) NOT NULL,
  `description` text,
  `bizrule` text,
  `data` text,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `cruge_authitem`
--

INSERT INTO `cruge_authitem` (`name`, `type`, `description`, `bizrule`, `data`) VALUES
('action_site_contact', 0, '', NULL, 'N;'),
('action_site_error', 0, '', NULL, 'N;'),
('action_site_index', 0, '', NULL, 'N;'),
('action_site_login', 0, '', NULL, 'N;'),
('action_site_logout', 0, '', NULL, 'N;'),
('action_ui_editprofile', 0, '', NULL, 'N;'),
('action_ui_fieldsadminlist', 0, '', NULL, 'N;'),
('action_ui_rbacauthitemchilditems', 0, '', NULL, 'N;'),
('action_ui_rbacauthitemcreate', 0, '', NULL, 'N;'),
('action_ui_rbaclistroles', 0, '', NULL, 'N;'),
('action_ui_rbaclisttasks', 0, '', NULL, 'N;'),
('action_ui_systemupdate', 0, '', NULL, 'N;'),
('action_ui_usermanagementadmin', 0, '', NULL, 'N;'),
('action_yiiLog_admin', 0, '', NULL, 'N;'),
('action_yiilog_create', 0, '', NULL, 'N;'),
('action_yiilog_delete', 0, '', NULL, 'N;'),
('action_yiilog_index', 0, '', NULL, 'N;'),
('action_yiilog_update', 0, '', NULL, 'N;'),
('action_yiilog_view', 0, '', NULL, 'N;'),
('admin', 0, '', NULL, 'N;'),
('administrator', 2, '', '', 'N;'),
('cms', 0, '', NULL, 'N;'),
('controller_site', 0, '', NULL, 'N;'),
('controller_yiilog', 0, '', NULL, 'N;'),
('manage', 0, '', NULL, 'N;');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cruge_authitemchild`
--

CREATE TABLE IF NOT EXISTS `cruge_authitemchild` (
  `parent` varchar(64) NOT NULL,
  `child` varchar(64) NOT NULL,
  PRIMARY KEY (`parent`,`child`),
  KEY `child` (`child`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cruge_field`
--

CREATE TABLE IF NOT EXISTS `cruge_field` (
  `idfield` int(11) NOT NULL AUTO_INCREMENT,
  `fieldname` varchar(20) NOT NULL,
  `longname` varchar(50) DEFAULT NULL,
  `position` int(11) DEFAULT '0',
  `required` int(11) DEFAULT '0',
  `fieldtype` int(11) DEFAULT '0',
  `fieldsize` int(11) DEFAULT '20',
  `maxlength` int(11) DEFAULT '45',
  `showinreports` int(11) DEFAULT '0',
  `useregexp` varchar(512) DEFAULT NULL,
  `useregexpmsg` varchar(512) DEFAULT NULL,
  `predetvalue` mediumblob,
  PRIMARY KEY (`idfield`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cruge_fieldvalue`
--

CREATE TABLE IF NOT EXISTS `cruge_fieldvalue` (
  `idfieldvalue` int(11) NOT NULL AUTO_INCREMENT,
  `iduser` int(11) NOT NULL,
  `idfield` int(11) NOT NULL,
  `value` blob,
  PRIMARY KEY (`idfieldvalue`),
  KEY `fk_cruge_fieldvalue_cruge_user1` (`iduser`),
  KEY `fk_cruge_fieldvalue_cruge_field1` (`idfield`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cruge_session`
--

CREATE TABLE IF NOT EXISTS `cruge_session` (
  `idsession` int(11) NOT NULL AUTO_INCREMENT,
  `iduser` int(11) NOT NULL,
  `created` bigint(30) DEFAULT NULL,
  `expire` bigint(30) DEFAULT NULL,
  `status` int(11) DEFAULT '0',
  `ipaddress` varchar(45) DEFAULT NULL,
  `usagecount` int(11) DEFAULT '0',
  `lastusage` bigint(30) DEFAULT NULL,
  `logoutdate` bigint(30) DEFAULT NULL,
  `ipaddressout` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`idsession`),
  KEY `crugesession_iduser` (`iduser`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=32 ;

--
-- Volcado de datos para la tabla `cruge_session`
--

INSERT INTO `cruge_session` (`idsession`, `iduser`, `created`, `expire`, `status`, `ipaddress`, `usagecount`, `lastusage`, `logoutdate`, `ipaddressout`) VALUES
(17, 1, 1403570873, 1403572673, 0, '127.0.0.1', 1, 1403570873, NULL, NULL),
(18, 1, 1403573917, 1403575717, 0, '127.0.0.1', 1, 1403573917, NULL, NULL),
(19, 1, 1403576773, 1403578573, 0, '127.0.0.1', 1, 1403576773, 1403576774, '127.0.0.1'),
(20, 1, 1403576783, 1403578583, 0, '127.0.0.1', 1, 1403576783, NULL, NULL),
(21, 1, 1403579274, 1403581074, 1, '127.0.0.1', 1, 1403579274, NULL, NULL),
(22, 1, 1405815083, 1405816883, 0, '127.0.0.1', 2, 1405816521, NULL, NULL),
(23, 1, 1405817097, 1405818897, 0, '127.0.0.1', 1, 1405817097, 1405817097, '127.0.0.1'),
(24, 1, 1405817105, 1405818905, 0, '127.0.0.1', 1, 1405817105, NULL, NULL),
(25, 1, 1405819040, 1405820840, 0, '127.0.0.1', 1, 1405819040, 1405819040, '127.0.0.1'),
(26, 1, 1405819047, 1405820847, 0, '127.0.0.1', 1, 1405819047, NULL, NULL),
(27, 1, 1405821076, 1405826476, 0, '127.0.0.1', 2, 1405821125, NULL, NULL),
(28, 1, 1406689689, 1406695089, 0, '127.0.0.1', 1, 1406689689, NULL, NULL),
(29, 1, 1406767876, 1406773276, 0, '127.0.0.1', 1, 1406767876, 1406767876, '127.0.0.1'),
(30, 1, 1406767891, 1406773291, 0, '127.0.0.1', 1, 1406767891, NULL, NULL),
(31, 1, 1406817632, 1406823032, 1, '127.0.0.1', 1, 1406817632, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cruge_system`
--

CREATE TABLE IF NOT EXISTS `cruge_system` (
  `idsystem` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) DEFAULT NULL,
  `largename` varchar(45) DEFAULT NULL,
  `sessionmaxdurationmins` int(11) DEFAULT '30',
  `sessionmaxsameipconnections` int(11) DEFAULT '10',
  `sessionreusesessions` int(11) DEFAULT '1' COMMENT '1yes 0no',
  `sessionmaxsessionsperday` int(11) DEFAULT '-1',
  `sessionmaxsessionsperuser` int(11) DEFAULT '-1',
  `systemnonewsessions` int(11) DEFAULT '0' COMMENT '1yes 0no',
  `systemdown` int(11) DEFAULT '0',
  `registerusingcaptcha` int(11) DEFAULT '0',
  `registerusingterms` int(11) DEFAULT '0',
  `terms` blob,
  `registerusingactivation` int(11) DEFAULT '1',
  `defaultroleforregistration` varchar(64) DEFAULT NULL,
  `registerusingtermslabel` varchar(100) DEFAULT NULL,
  `registrationonlogin` int(11) DEFAULT '1',
  PRIMARY KEY (`idsystem`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Volcado de datos para la tabla `cruge_system`
--

INSERT INTO `cruge_system` (`idsystem`, `name`, `largename`, `sessionmaxdurationmins`, `sessionmaxsameipconnections`, `sessionreusesessions`, `sessionmaxsessionsperday`, `sessionmaxsessionsperuser`, `systemnonewsessions`, `systemdown`, `registerusingcaptcha`, `registerusingterms`, `terms`, `registerusingactivation`, `defaultroleforregistration`, `registerusingtermslabel`, `registrationonlogin`) VALUES
(1, 'default', NULL, 90, 10, 1, -1, -1, 0, 0, 0, 0, '', 0, '', '', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cruge_user`
--

CREATE TABLE IF NOT EXISTS `cruge_user` (
  `iduser` int(11) NOT NULL AUTO_INCREMENT,
  `regdate` bigint(30) DEFAULT NULL,
  `actdate` bigint(30) DEFAULT NULL,
  `logondate` bigint(30) DEFAULT NULL,
  `username` varchar(64) DEFAULT NULL,
  `email` varchar(45) DEFAULT NULL,
  `password` varchar(64) DEFAULT NULL COMMENT 'Hashed password',
  `authkey` varchar(100) DEFAULT NULL COMMENT 'llave de autentificacion',
  `state` int(11) DEFAULT '0',
  `totalsessioncounter` int(11) DEFAULT '0',
  `currentsessioncounter` int(11) DEFAULT '0',
  PRIMARY KEY (`iduser`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Volcado de datos para la tabla `cruge_user`
--

INSERT INTO `cruge_user` (`iduser`, `regdate`, `actdate`, `logondate`, `username`, `email`, `password`, `authkey`, `state`, `totalsessioncounter`, `currentsessioncounter`) VALUES
(1, NULL, NULL, 1406817632, 'admin', 'admin@tucorreo.com', 'admin', NULL, 1, 0, 0),
(2, NULL, NULL, NULL, 'invitado', 'invitado', 'nopassword', NULL, 1, 0, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `image`
--

CREATE TABLE IF NOT EXISTS `image` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ownerId` int(10) unsigned NOT NULL,
  `owner` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `path` varchar(255) NOT NULL,
  `extension` varchar(255) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `byteSize` int(10) unsigned NOT NULL,
  `mimeType` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Volcado de datos para la tabla `image`
--

INSERT INTO `image` (`id`, `created`, `ownerId`, `owner`, `name`, `path`, `extension`, `filename`, `byteSize`, `mimeType`) VALUES
(1, '2014-03-22 02:46:02', 2, 'CmsPage', 'logo', 'page', 'png', 'logo.png', 11329, 'image/png'),
(2, '2014-07-20 01:44:21', 3, 'CmsPage', 'technicsqc11703806', 'page', 'jpg', 'technics-q-c-1170-380-6.jpg', 43290, 'image/jpeg');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `YiiLog`
--

CREATE TABLE IF NOT EXISTS `YiiLog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `level` varchar(128) DEFAULT NULL,
  `category` varchar(128) DEFAULT NULL,
  `logtime` int(11) DEFAULT NULL,
  `message` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=18 ;

--
-- Volcado de datos para la tabla `YiiLog`
--

INSERT INTO `YiiLog` (`id`, `level`, `category`, `logtime`, `message`) VALUES
(1, 'error', 'php', 1406681190, 'Trying to get property of non-object (/var/www/html/yiicms/protected/views/frontend/layouts/main.php:15)\nStack trace:\n#0 /opt/framework/web/widgets/CContentDecorator.php(76): SiteController->renderFile()\n#1 /opt/framework/web/widgets/CContentDecorator.php(54): CContentDecorator->decorate()\n#2 /opt/framework/web/widgets/COutputProcessor.php(44): CContentDecorator->processOutput()\n#3 /opt/framework/web/CBaseController.php(206): CContentDecorator->run()\n#4 /opt/framework/web/CBaseController.php(300): SiteController->endWidget()\n#5 /var/www/html/yiicms/protected/views/frontend/layouts/column1.php(9): SiteController->endContent()\n#6 /opt/framework/web/CBaseController.php(126): require()\n#7 /opt/framework/web/CBaseController.php(95): SiteController->renderInternal()\n#8 /opt/framework/web/CController.php(784): SiteController->renderFile()\n#9 /var/www/html/yiicms/protected/controllers/frontend/SiteController.php(33): SiteController->render()\n#10 /opt/framework/web/actions/CInlineAction.php(49): SiteController->actionIndex()\n#11 /opt/framework/web/CController.php(308): CInlineAction->runWithParams()\n#12 /opt/framework/web/CController.php(286): SiteController->runAction()\n#13 /opt/framework/web/CController.php(265): SiteController->runActionWithFilters()\n#14 /opt/framework/web/CWebApplication.php(282): SiteController->run()\n#15 /opt/framework/web/CWebApplication.php(141): CWebApplication->runController()\n#16 /opt/framework/base/CApplication.php(180): CWebApplication->processRequest()\n#17 /var/www/html/yiicms/protected/components/WebApplicationEndBehavior.php(25): CWebApplication->run()\n#18 unknown(0): WebApplicationEndBehavior->runEnd()\n#19 /opt/framework/base/CComponent.php(261): call_user_func_array()\n#20 /var/www/html/yiicms/index.php(14): CWebApplication->__call()\n#21 /var/www/html/yiicms/index.php(14): CWebApplication->runEnd()\nREQUEST_URI=/yiicms/\nin /var/www/html/yiicms/protected/views/frontend/layouts/main.php (15)\nin /var/www/html/yiicms/protected/views/frontend/layouts/column1.php (9)\nin /var/www/html/yiicms/protected/controllers/frontend/SiteController.php (33)\nin /var/www/html/yiicms/protected/components/WebApplicationEndBehavior.php (25)\nin /var/www/html/yiicms/index.php (14)\nin /var/www/html/yiicms/index.php (14)'),
(2, 'error', 'exception.CHttpException.404', 1406681209, 'exception ''CHttpException'' with message ''Unable to resolve the request "ui/login".'' in /opt/framework/web/CWebApplication.php:286\nStack trace:\n#0 /opt/framework/web/CWebApplication.php(141): CWebApplication->runController(''ui/login'')\n#1 /opt/framework/base/CApplication.php(180): CWebApplication->processRequest()\n#2 /var/www/html/yiicms/protected/components/WebApplicationEndBehavior.php(25): CApplication->run()\n#3 [internal function]: WebApplicationEndBehavior->runEnd(''frontend'')\n#4 /opt/framework/base/CComponent.php(261): call_user_func_array(Array, Array)\n#5 /var/www/html/yiicms/index.php(14): CComponent->__call(''runEnd'', Array)\n#6 /var/www/html/yiicms/index.php(14): CWebApplication->runEnd(''frontend'')\n#7 {main}\nREQUEST_URI=/yiicms/cruge/ui/login/lang/backend\n---'),
(3, 'error', 'php', 1406681269, 'Trying to get property of non-object (/var/www/html/yiicms/protected/views/frontend/layouts/main.php:15)\nStack trace:\n#0 /opt/framework/web/widgets/CContentDecorator.php(76): SiteController->renderFile()\n#1 /opt/framework/web/widgets/CContentDecorator.php(54): CContentDecorator->decorate()\n#2 /opt/framework/web/widgets/COutputProcessor.php(44): CContentDecorator->processOutput()\n#3 /opt/framework/web/CBaseController.php(206): CContentDecorator->run()\n#4 /opt/framework/web/CBaseController.php(300): SiteController->endWidget()\n#5 /var/www/html/yiicms/protected/views/frontend/layouts/column1.php(9): SiteController->endContent()\n#6 /opt/framework/web/CBaseController.php(126): require()\n#7 /opt/framework/web/CBaseController.php(95): SiteController->renderInternal()\n#8 /opt/framework/web/CController.php(784): SiteController->renderFile()\n#9 /var/www/html/yiicms/protected/controllers/frontend/SiteController.php(33): SiteController->render()\n#10 /opt/framework/web/actions/CInlineAction.php(49): SiteController->actionIndex()\n#11 /opt/framework/web/CController.php(308): CInlineAction->runWithParams()\n#12 /opt/framework/web/CController.php(286): SiteController->runAction()\n#13 /opt/framework/web/CController.php(265): SiteController->runActionWithFilters()\n#14 /opt/framework/web/CWebApplication.php(282): SiteController->run()\n#15 /opt/framework/web/CWebApplication.php(141): CWebApplication->runController()\n#16 /opt/framework/base/CApplication.php(180): CWebApplication->processRequest()\n#17 /var/www/html/yiicms/protected/components/WebApplicationEndBehavior.php(25): CWebApplication->run()\n#18 unknown(0): WebApplicationEndBehavior->runEnd()\n#19 /opt/framework/base/CComponent.php(261): call_user_func_array()\n#20 /var/www/html/yiicms/index.php(14): CWebApplication->__call()\n#21 /var/www/html/yiicms/index.php(14): CWebApplication->runEnd()\nREQUEST_URI=/yiicms/\nin /var/www/html/yiicms/protected/views/frontend/layouts/main.php (15)\nin /var/www/html/yiicms/protected/views/frontend/layouts/column1.php (9)\nin /var/www/html/yiicms/protected/controllers/frontend/SiteController.php (33)\nin /var/www/html/yiicms/protected/components/WebApplicationEndBehavior.php (25)\nin /var/www/html/yiicms/index.php (14)\nin /var/www/html/yiicms/index.php (14)'),
(4, 'error', 'php', 1406681332, 'Trying to get property of non-object (/var/www/html/yiicms/protected/views/frontend/layouts/main.php:15)\nStack trace:\n#0 /opt/framework/web/widgets/CContentDecorator.php(76): SiteController->renderFile()\n#1 /opt/framework/web/widgets/CContentDecorator.php(54): CContentDecorator->decorate()\n#2 /opt/framework/web/widgets/COutputProcessor.php(44): CContentDecorator->processOutput()\n#3 /opt/framework/web/CBaseController.php(206): CContentDecorator->run()\n#4 /opt/framework/web/CBaseController.php(300): SiteController->endWidget()\n#5 /var/www/html/yiicms/protected/views/frontend/layouts/column1.php(9): SiteController->endContent()\n#6 /opt/framework/web/CBaseController.php(126): require()\n#7 /opt/framework/web/CBaseController.php(95): SiteController->renderInternal()\n#8 /opt/framework/web/CController.php(784): SiteController->renderFile()\n#9 /var/www/html/yiicms/protected/controllers/frontend/SiteController.php(33): SiteController->render()\n#10 /opt/framework/web/actions/CInlineAction.php(49): SiteController->actionIndex()\n#11 /opt/framework/web/CController.php(308): CInlineAction->runWithParams()\n#12 /opt/framework/web/CController.php(286): SiteController->runAction()\n#13 /opt/framework/web/CController.php(265): SiteController->runActionWithFilters()\n#14 /opt/framework/web/CWebApplication.php(282): SiteController->run()\n#15 /opt/framework/web/CWebApplication.php(141): CWebApplication->runController()\n#16 /opt/framework/base/CApplication.php(180): CWebApplication->processRequest()\n#17 /var/www/html/yiicms/protected/components/WebApplicationEndBehavior.php(25): CWebApplication->run()\n#18 unknown(0): WebApplicationEndBehavior->runEnd()\n#19 /opt/framework/base/CComponent.php(261): call_user_func_array()\n#20 /var/www/html/yiicms/index.php(14): CWebApplication->__call()\n#21 /var/www/html/yiicms/index.php(14): CWebApplication->runEnd()\nREQUEST_URI=/yiicms/\nin /var/www/html/yiicms/protected/views/frontend/layouts/main.php (15)\nin /var/www/html/yiicms/protected/views/frontend/layouts/column1.php (9)\nin /var/www/html/yiicms/protected/controllers/frontend/SiteController.php (33)\nin /var/www/html/yiicms/protected/components/WebApplicationEndBehavior.php (25)\nin /var/www/html/yiicms/index.php (14)\nin /var/www/html/yiicms/index.php (14)'),
(5, 'error', 'exception.CHttpException.404', 1406681358, 'exception ''CHttpException'' with message ''Unable to resolve the request "ui/login".'' in /opt/framework/web/CWebApplication.php:286\nStack trace:\n#0 /opt/framework/web/CWebApplication.php(141): CWebApplication->runController(''ui/login'')\n#1 /opt/framework/base/CApplication.php(180): CWebApplication->processRequest()\n#2 /var/www/html/yiicms/protected/components/WebApplicationEndBehavior.php(25): CApplication->run()\n#3 [internal function]: WebApplicationEndBehavior->runEnd(''frontend'')\n#4 /opt/framework/base/CComponent.php(261): call_user_func_array(Array, Array)\n#5 /var/www/html/yiicms/index.php(14): CComponent->__call(''runEnd'', Array)\n#6 /var/www/html/yiicms/index.php(14): CWebApplication->runEnd(''frontend'')\n#7 {main}\nREQUEST_URI=/yiicms/cruge/ui/login/lang/backend\n---'),
(6, 'error', 'exception.CHttpException.404', 1406681364, 'exception ''CHttpException'' with message ''Unable to resolve the request "ui/login".'' in /opt/framework/web/CWebApplication.php:286\nStack trace:\n#0 /opt/framework/web/CWebApplication.php(141): CWebApplication->runController(''ui/login'')\n#1 /opt/framework/base/CApplication.php(180): CWebApplication->processRequest()\n#2 /var/www/html/yiicms/protected/components/WebApplicationEndBehavior.php(25): CApplication->run()\n#3 [internal function]: WebApplicationEndBehavior->runEnd(''frontend'')\n#4 /opt/framework/base/CComponent.php(261): call_user_func_array(Array, Array)\n#5 /var/www/html/yiicms/index.php(14): CComponent->__call(''runEnd'', Array)\n#6 /var/www/html/yiicms/index.php(14): CWebApplication->runEnd(''frontend'')\n#7 {main}\nREQUEST_URI=/yiicms/cruge/ui/login/lang/backend\n---'),
(7, 'error', 'exception.CHttpException.404', 1406681369, 'exception ''CHttpException'' with message ''Unable to resolve the request "ui/login".'' in /opt/framework/web/CWebApplication.php:286\nStack trace:\n#0 /opt/framework/web/CWebApplication.php(141): CWebApplication->runController(''ui/login'')\n#1 /opt/framework/base/CApplication.php(180): CWebApplication->processRequest()\n#2 /var/www/html/yiicms/protected/components/WebApplicationEndBehavior.php(25): CApplication->run()\n#3 [internal function]: WebApplicationEndBehavior->runEnd(''frontend'')\n#4 /opt/framework/base/CComponent.php(261): call_user_func_array(Array, Array)\n#5 /var/www/html/yiicms/index.php(14): CComponent->__call(''runEnd'', Array)\n#6 /var/www/html/yiicms/index.php(14): CWebApplication->runEnd(''frontend'')\n#7 {main}\nREQUEST_URI=/yiicms/cruge/ui/login/lang/backend\n---'),
(8, 'error', 'exception.CHttpException.404', 1406681375, 'exception ''CHttpException'' with message ''Unable to resolve the request "ui/login".'' in /opt/framework/web/CWebApplication.php:286\nStack trace:\n#0 /opt/framework/web/CWebApplication.php(141): CWebApplication->runController(''ui/login'')\n#1 /opt/framework/base/CApplication.php(180): CWebApplication->processRequest()\n#2 /var/www/html/yiicms/protected/components/WebApplicationEndBehavior.php(25): CApplication->run()\n#3 [internal function]: WebApplicationEndBehavior->runEnd(''frontend'')\n#4 /opt/framework/base/CComponent.php(261): call_user_func_array(Array, Array)\n#5 /var/www/html/yiicms/index.php(14): CComponent->__call(''runEnd'', Array)\n#6 /var/www/html/yiicms/index.php(14): CWebApplication->runEnd(''frontend'')\n#7 {main}\nREQUEST_URI=/yiicms/cruge/ui/login/lang/backend\n---'),
(9, 'error', 'exception.CHttpException.404', 1406681918, 'exception ''CHttpException'' with message ''Unable to resolve the request "ui/login".'' in /opt/framework/web/CWebApplication.php:286\nStack trace:\n#0 /opt/framework/web/CWebApplication.php(141): CWebApplication->runController(''ui/login'')\n#1 /opt/framework/base/CApplication.php(180): CWebApplication->processRequest()\n#2 /var/www/html/yiicms/protected/components/WebApplicationEndBehavior.php(25): CApplication->run()\n#3 [internal function]: WebApplicationEndBehavior->runEnd(''frontend'')\n#4 /opt/framework/base/CComponent.php(261): call_user_func_array(Array, Array)\n#5 /var/www/html/yiicms/index.php(14): CComponent->__call(''runEnd'', Array)\n#6 /var/www/html/yiicms/index.php(14): CWebApplication->runEnd(''frontend'')\n#7 {main}\nREQUEST_URI=/yiicms/cruge/ui/login/lang/en\n---'),
(10, 'error', 'exception.CHttpException.404', 1406681921, 'exception ''CHttpException'' with message ''Unable to resolve the request "frontend/css".'' in /opt/framework/web/CWebApplication.php:286\nStack trace:\n#0 /opt/framework/web/CWebApplication.php(141): CWebApplication->runController(''frontend/css'')\n#1 /opt/framework/base/CApplication.php(180): CWebApplication->processRequest()\n#2 /var/www/html/yiicms/protected/components/WebApplicationEndBehavior.php(25): CApplication->run()\n#3 [internal function]: WebApplicationEndBehavior->runEnd(''frontend'')\n#4 /opt/framework/base/CComponent.php(261): call_user_func_array(Array, Array)\n#5 /var/www/html/yiicms/index.php(14): CComponent->__call(''runEnd'', Array)\n#6 /var/www/html/yiicms/index.php(14): CWebApplication->runEnd(''frontend'')\n#7 {main}\nREQUEST_URI=/yiicms/themes/frontend/css/backend.css?v=110220120632\nHTTP_REFERER=http://localhost/yiicms/backend/yiiLog/\n---'),
(11, 'error', 'exception.CHttpException.404', 1406681922, 'exception ''CHttpException'' with message ''Unable to resolve the request "frontend/css".'' in /opt/framework/web/CWebApplication.php:286\nStack trace:\n#0 /opt/framework/web/CWebApplication.php(141): CWebApplication->runController(''frontend/css'')\n#1 /opt/framework/base/CApplication.php(180): CWebApplication->processRequest()\n#2 /var/www/html/yiicms/protected/components/WebApplicationEndBehavior.php(25): CApplication->run()\n#3 [internal function]: WebApplicationEndBehavior->runEnd(''frontend'')\n#4 /opt/framework/base/CComponent.php(261): call_user_func_array(Array, Array)\n#5 /var/www/html/yiicms/index.php(14): CComponent->__call(''runEnd'', Array)\n#6 /var/www/html/yiicms/index.php(14): CWebApplication->runEnd(''frontend'')\n#7 {main}\nREQUEST_URI=/yiicms/themes/frontend/css/backend.css?v=110220120632\nHTTP_REFERER=http://localhost/yiicms/backend/yiiLog/\n---'),
(12, 'error', 'exception.CHttpException.404', 1406681928, 'exception ''CHttpException'' with message ''Unable to resolve the request "ui/login".'' in /opt/framework/web/CWebApplication.php:286\nStack trace:\n#0 /opt/framework/web/CWebApplication.php(141): CWebApplication->runController(''ui/login'')\n#1 /opt/framework/base/CApplication.php(180): CWebApplication->processRequest()\n#2 /var/www/html/yiicms/protected/components/WebApplicationEndBehavior.php(25): CApplication->run()\n#3 [internal function]: WebApplicationEndBehavior->runEnd(''frontend'')\n#4 /opt/framework/base/CComponent.php(261): call_user_func_array(Array, Array)\n#5 /var/www/html/yiicms/index.php(14): CComponent->__call(''runEnd'', Array)\n#6 /var/www/html/yiicms/index.php(14): CWebApplication->runEnd(''frontend'')\n#7 {main}\nREQUEST_URI=/yiicms/cruge/ui/login/lang/themes\n---'),
(13, 'error', 'exception.CHttpException.404', 1406682165, 'exception ''CHttpException'' with message ''Unable to resolve the request "frontend/css".'' in /opt/framework/web/CWebApplication.php:286\nStack trace:\n#0 /opt/framework/web/CWebApplication.php(141): CWebApplication->runController(''frontend/css'')\n#1 /opt/framework/base/CApplication.php(180): CWebApplication->processRequest()\n#2 /var/www/html/yiicms/protected/components/WebApplicationEndBehavior.php(25): CApplication->run()\n#3 [internal function]: WebApplicationEndBehavior->runEnd(''frontend'')\n#4 /opt/framework/base/CComponent.php(261): call_user_func_array(Array, Array)\n#5 /var/www/html/yiicms/index.php(14): CComponent->__call(''runEnd'', Array)\n#6 /var/www/html/yiicms/index.php(14): CWebApplication->runEnd(''frontend'')\n#7 {main}\nREQUEST_URI=/yiicms/themes/frontend/css/backend.css?v=110220120632\nHTTP_REFERER=http://localhost/yiicms/backend/cruge/ui/login?lang=en\n---'),
(14, 'error', 'exception.CHttpException.404', 1406682167, 'exception ''CHttpException'' with message ''Unable to resolve the request "frontend/css".'' in /opt/framework/web/CWebApplication.php:286\nStack trace:\n#0 /opt/framework/web/CWebApplication.php(141): CWebApplication->runController(''frontend/css'')\n#1 /opt/framework/base/CApplication.php(180): CWebApplication->processRequest()\n#2 /var/www/html/yiicms/protected/components/WebApplicationEndBehavior.php(25): CApplication->run()\n#3 [internal function]: WebApplicationEndBehavior->runEnd(''frontend'')\n#4 /opt/framework/base/CComponent.php(261): call_user_func_array(Array, Array)\n#5 /var/www/html/yiicms/index.php(14): CComponent->__call(''runEnd'', Array)\n#6 /var/www/html/yiicms/index.php(14): CWebApplication->runEnd(''frontend'')\n#7 {main}\nREQUEST_URI=/yiicms/themes/frontend/css/backend.css?v=110220120632\nHTTP_REFERER=http://localhost/yiicms/backend/cruge/ui/login?lang=en\n---'),
(15, 'error', 'exception.CHttpException.404', 1406682252, 'exception ''CHttpException'' with message ''Unable to resolve the request "frontend/css".'' in /opt/framework/web/CWebApplication.php:286\nStack trace:\n#0 /opt/framework/web/CWebApplication.php(141): CWebApplication->runController(''frontend/css'')\n#1 /opt/framework/base/CApplication.php(180): CWebApplication->processRequest()\n#2 /var/www/html/yiicms/protected/components/WebApplicationEndBehavior.php(25): CApplication->run()\n#3 [internal function]: WebApplicationEndBehavior->runEnd(''frontend'')\n#4 /opt/framework/base/CComponent.php(261): call_user_func_array(Array, Array)\n#5 /var/www/html/yiicms/index.php(14): CComponent->__call(''runEnd'', Array)\n#6 /var/www/html/yiicms/index.php(14): CWebApplication->runEnd(''frontend'')\n#7 {main}\nREQUEST_URI=/yiicms/themes/frontend/css/backend.css?v=110220120632\nHTTP_REFERER=http://localhost/yiicms/backend/cruge/ui/login?lang=en\n---'),
(16, 'error', 'exception.CHttpException.404', 1406682254, 'exception ''CHttpException'' with message ''Unable to resolve the request "frontend/css".'' in /opt/framework/web/CWebApplication.php:286\nStack trace:\n#0 /opt/framework/web/CWebApplication.php(141): CWebApplication->runController(''frontend/css'')\n#1 /opt/framework/base/CApplication.php(180): CWebApplication->processRequest()\n#2 /var/www/html/yiicms/protected/components/WebApplicationEndBehavior.php(25): CApplication->run()\n#3 [internal function]: WebApplicationEndBehavior->runEnd(''frontend'')\n#4 /opt/framework/base/CComponent.php(261): call_user_func_array(Array, Array)\n#5 /var/www/html/yiicms/index.php(14): CComponent->__call(''runEnd'', Array)\n#6 /var/www/html/yiicms/index.php(14): CWebApplication->runEnd(''frontend'')\n#7 {main}\nREQUEST_URI=/yiicms/themes/frontend/css/backend.css?v=110220120632\nHTTP_REFERER=http://localhost/yiicms/backend/cruge/ui/login?lang=en\n---'),
(17, 'error', 'exception.CHttpException.404', 1406682459, 'exception ''CHttpException'' with message ''Unable to resolve the request "frontend/css".'' in /opt/framework/web/CWebApplication.php:286\nStack trace:\n#0 /opt/framework/web/CWebApplication.php(141): CWebApplication->runController(''frontend/css'')\n#1 /opt/framework/base/CApplication.php(180): CWebApplication->processRequest()\n#2 /var/www/html/yiicms/protected/components/WebApplicationEndBehavior.php(25): CApplication->run()\n#3 [internal function]: WebApplicationEndBehavior->runEnd(''frontend'')\n#4 /opt/framework/base/CComponent.php(261): call_user_func_array(Array, Array)\n#5 /var/www/html/yiicms/index.php(14): CComponent->__call(''runEnd'', Array)\n#6 /var/www/html/yiicms/index.php(14): CWebApplication->runEnd(''frontend'')\n#7 {main}\nREQUEST_URI=/yiicms/themes/frontend/css/backend.css?v=110220120632\nHTTP_REFERER=http://localhost/yiicms/backend/cruge/ui/login?lang=en\n---');

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `cms_message`
--
ALTER TABLE `cms_message`
  ADD CONSTRAINT `FK_cms_message_cms_source_message` FOREIGN KEY (`id`) REFERENCES `cms_source_message` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `cms_page_content`
--
ALTER TABLE `cms_page_content`
  ADD CONSTRAINT `cms_page_content_ibfk_1` FOREIGN KEY (`pageId`) REFERENCES `cms_page` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `cruge_authassignment`
--
ALTER TABLE `cruge_authassignment`
  ADD CONSTRAINT `fk_cruge_authassignment_cruge_authitem1` FOREIGN KEY (`itemname`) REFERENCES `cruge_authitem` (`name`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_cruge_authassignment_user` FOREIGN KEY (`userid`) REFERENCES `cruge_user` (`iduser`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Filtros para la tabla `cruge_authitemchild`
--
ALTER TABLE `cruge_authitemchild`
  ADD CONSTRAINT `crugeauthitemchild_ibfk_1` FOREIGN KEY (`parent`) REFERENCES `cruge_authitem` (`name`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `crugeauthitemchild_ibfk_2` FOREIGN KEY (`child`) REFERENCES `cruge_authitem` (`name`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `cruge_fieldvalue`
--
ALTER TABLE `cruge_fieldvalue`
  ADD CONSTRAINT `fk_cruge_fieldvalue_cruge_field1` FOREIGN KEY (`idfield`) REFERENCES `cruge_field` (`idfield`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_cruge_fieldvalue_cruge_user1` FOREIGN KEY (`iduser`) REFERENCES `cruge_user` (`iduser`) ON DELETE CASCADE ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
