<?php
/**
 * CrugeModule 
 * 
 * @uses CWebModule
 * @author Christian Salazar H. <christiansalazarh@gmail.com> 
 * @license /protected/modules/cruge/LICENSE
 */
class CrugeModule extends CWebModule
{
	public $debug = false;					// poner a true para ayudar en la instalacion
	public $tableprefix = 'cruge_';			// agrega un prefijo para buscar las tablas en db
	public $maptables = array();			// permite cambiar los nombres de las tablas

	public $baseUrl = "";					// usada para enviar los links de activacion de la cuenta de usuario
											// se usa mediante CrugeUtil::config()->baseUrl
											// para principalmente en CrugeUserManagement::getActivationUrl()

	public $superuserName = 'admin';		// username del super usuario. cualquier llamada
											// a Yii::app()->user->checkAccess retornara true
											// si el superuserName coincide con este valor

	// permite que se puedan configurar los campos (incluso personalizados)
	//	a la hora de consultar a:  $usuario->getUserDescription()  (CrugeStoredUser)
	//
	//	se debe retornar un array con "username" o "email" o cualquier nombre de campo personalizado
	//  ejemplos: array("firstname","lastname","chipnumber","address")
	//	lo cual concatenara una lista separada por comas con los valores de estos
	//	campos.
	//
	public $userDescriptionFieldsArray = array("email" /*, firstname, lastname */);


	public $afterLoginUrl;				// por defecto son usadas por el filtro de sesion: DefaultSessionFilter
	public $afterLogoutUrl;				//	tras el evento onLogin y onLogout
	public $afterSessionExpiredUrl;		//

	// la clase a usar para los CGridView, debido a que BootStrap ofrece clases con distinto estilo
	// modificando este valor se causa que los CGridView se renderizen usando la clase indicada
	//
	// por defecto poner: zii.widgets.grid.CGridView
	public $useCGridViewClass = 'zii.widgets.grid.CGridView';

	// el estilo del boton que la UI usara:
	//	normal, jui, bootstrap
	//  por defecto: normal
	public $buttonStyle = 'normal';
	public $buttonConf = 'small';// large, small o mini

	// ponerla a true para que se creen de forma automatica las operaciones en el sistema de Rbac
	// cuando se haga una llamada a Yii::app()->user->checkAccess() y esta retorne false.
	//
	// public function actionCreatePost(){
	//    if(Yii::app()->user->checkAccess('createpost_get')==false)
	//    		throw new CrugeException('acceso denegado');
	// }
	//
	// en este action de ejemplo (arriba), se solicita una operacion llamada: 'createpost_get'
	// que quiza no haya sido insertada en la lista de operaciones del sistema de rbac. entonces,
	// cuando rbacSetupEnabled es true esta operacion sera insertada para que podamos usarla
	// para ser asignada a los diferentes roles o tareas.
	//
	public $rbacSetupEnabled=false;
	// cuando esta en true y el modo de setup de rbac tambien lo esta (rbacSetupEnabled=true)
	// entonces permitira al usuario acecder a la funcion denegada aunque no tenga el permiso
	//
	public $allowUserAlways=false;


	// el iduser del usuario invitado. por defecto es 2. (admin es 1)
	//
	public $guestUserId=2;

	// los nombres de los modulos de autenticacion habilitados para reconocer usuarios:
	// cada nombre debe coincidir con el valor devuelto por ICrugeAuth::authName()
	//
	// en UiController::actionLogin se lee esta variable para saber con que filtro de autenticacion se procesara
	// el request de login del usuario, por defecto 'default' el cual usa a models.auth.CrugeAuthDefault
	//
	// para leer este valor usar:
	//	$valor = CrugeFactory::get()->getConfiguredAuthMethodName();
	//
	public $availableAuthMethods = array('default');

	// los campos por los cuales se puede buscar a un usuario cuando hace login
	public $availableAuthModes	 = array('email','username');

	// ruta de la clase que implementa a ICrugeSessionFilter.
	// si es null se usa a defaultSessionFilter
	// son usados en: CrugeFactory::getICrugeSessionFilter para determinar con que conceder la sesion
	public $sessionfilter=null;
	public $defaultSessionFilter = 'cruge.models.filters.DefaultSessionFilter';

	// este filtro permite o niega la creacion o actualizacion de un usuario.
	//
	// aqui se espera una clase que implemente a: ICrugeUserFilter
	//
	public $userFilter='cruge.models.filters.DefaultUserFilter';

	// indica si una clave es almacenada con hash o no.
	//
	public $useEncryptedPassword = false;

    // Indica el algoritmo de hash a usarse

    public $hash = 'md5';

	// estos atributos llamados xxxLayout, son para indicar que layout usar
	// para los actions:
	//
	// generalUserManagementLayout: todos los actions de usermanagament (admin,create,update..)
	// activateAccountLayout:  el action que presenta la pagina de activar cuenta
	// registrationLayout: el action para registrar usuarios
	//
	// por defecto usar valor: "ui".  eso hara que lea el archivo llamado "ui" ubicado en
	// 		/protected/modules/cruge/views/layouts/ui.php
	// si se quisiera usar un layout de la aplicacion en vez del modulo:
	//		/protected/views/layouts/otrolayout.php
	// entonces configurar asi:
	//		"//layouts/otrolayout"
	public $generalUserManagementLayout = 'ui';
	public $editProfileLayout = 'ui';
	public $activateAccountLayout = '//layouts/column1';
	public $registrationLayout = '//layouts/column1';
	public $loginLayout = '//layouts/column1';
	public $resetPasswordLayout = '//layouts/column1';

	// sirve para que la controladora UiController pueda que nombre pasar a $_POST['??']
	// para actualizar los atributos de una clase.  Si se llegase a cambiar una clase
	// por otra entonces con solo cambiar aqui el nombre el formulario podr� trabajar de nuevo
	//
	// Es utilizada en cada referencia a $_POST o $_GET en la controladora de UiController
	//
	// ejemplo: Ma�ana no nos gusta CrugeField y cambiamos la clase por CrugeMyOwnField
	// entonces en este array se mapearia asi:  'CrugeField'=>'CrugeMyOwnField'
	//
	public $postNameMappings = array(
		'CrugeLogon'=>'CrugeLogon',
		'CrugeStoredUser'=>'CrugeStoredUser',
		'CrugeField'=>'CrugeField',
		'CrugeSystem'=>'CrugeSystem',
		'CrugeSession'=>'CrugeSession',
	);

	// estos parametros no deben manipularse
	public $defaultController = 'ui';
	public $uicontroller='ui';
	public $_lazyAuthModes = null;
	private $_factory;

	// este array es usado por CrugeUi para almacenar errors que han sido reportados
	// por CrugeUi::addError() , luego, para desplegar los errores observados
	// puede usarse al pie de la pagina web la siguiente linea:
	//
	//	echo Yii::app()->user->ui->displayErrorConsole();
	//
	public $globalErrors = array();



	public function init()
	{
		$this->setImport(array(
			'cruge.models.*',
			'cruge.models.data.*',	// clases del modelo de datos
			'cruge.models.auth.*',	// clases de autenticacion
			'cruge.models.filters.*',// clases de filtros de sesion
			'cruge.models.ui.*',// clases de interfaz de usuario
			'cruge.components.*',	// clases del modelo
			'cruge.interfaces.*',	// interfaces
			'cruge.extensions.crugemailer.*',	// extensiones consumidas por el modulo

		));

	}

	public function getUiControllerName(){
		return $this->uicontroller;
	}

	public function getFactory(){
		if($this->_factory == null)
			$this->_factory = new CrugeFactory();
		return $this->_factory;
	}



	public function beforeControllerAction($controller, $action)
	{
		if(parent::beforeControllerAction($controller, $action))
		{
			return true;
		}
		else
			return false;
	}



}
