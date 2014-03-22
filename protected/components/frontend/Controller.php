<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class Controller extends CController
{
	/**
	 * @var string the default layout for the controller view. Defaults to '//layouts/column1',
	 * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
	 */
	public $layout='//layouts/column1';
	/**
	 * @var array context menu items. This property will be assigned to {@link CMenu::items}.
	 */
	public $menu=array();
	/**
	 * @var array the breadcrumbs of the current page. The value of this property will
	 * be assigned to {@link CBreadcrumbs::links}. Please refer to {@link CBreadcrumbs::links}
	 * for more details on how to specify this property.
	 */
	public $breadcrumbs=array();
        
        public function __construct($id,$module=null){
            parent::__construct($id,$module);
            // If there is a post-request, redirect the application to the provided url of the selected language 
            if(isset($_POST['lang'])) {
                $lang = $_POST['lang'];
                $MultilangReturnUrl = $_POST[$lang];
                $this->redirect($MultilangReturnUrl);
            }
            // Set the application language if provided by GET, session or cookie
            if(isset($_GET['lang'])) {
                Yii::app()->language = $_GET['lang'];
                Yii::app()->user->setState('lang', $_GET['lang']); 
                Yii::app()->user->setState('__locale', $_GET['lang']); 
                $cookie = new CHttpCookie('lang', $_GET['lang']);
                $cookie->expire = time() + (60*60*24*365); // (1 year)
                Yii::app()->request->cookies['lang'] = $cookie; 
            }
            else if (Yii::app()->user->hasState('lang'))
                Yii::app()->language = Yii::app()->user->getState('lang');
            else if(isset(Yii::app()->request->cookies['lang']))
                Yii::app()->language = Yii::app()->request->cookies['lang']->value;
        }
        public function createMultilanguageReturnUrl($lang='en'){
            if (count($_GET)>0){
                $arr = $_GET;
                $arr['lang']= $lang;
            }
            else 
                $arr = array('lang'=>$lang);
            return $this->createUrl('', $arr);
        }
}