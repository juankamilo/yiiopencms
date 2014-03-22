<?php

class SiteController extends BackendController
{
    /**
    * @return array action filters
    */
   public function filters()
   {
           return array(
                   'accessControl', // perform access control for CRUD operations
                   'postOnly + delete', // we only allow deletion via POST request
           );
   }
    public function accessRules()
    {
            return array(
                    array('allow',  // allow all users to perform 'index' and 'view' actions
                            'actions'=>array('login','error'),
                            'users'=>array('*'),
                    ),
                    array('allow',  // allow all users to perform 'index' and 'view' actions
                            'actions'=>array('logout'),
                            'users'=>array('@'),
                    ),
                    array('allow', // allow admin user to perform 'admin' and 'delete' actions
                            'actions'=>array('admin','delete','index'),
                            'users'=>array('admin'),
                    ),
                    array('deny',  // deny all users
                            'users'=>array('*'),
                    ),
            );
    }
    /**
     * This is the default 'index' action that is invoked
     * when an action is not explicitly requested by users.
     */
    public function actionIndex()
    {
        //$this->render('index');
        $model = new Reservas;
        $this->render('//usuarios/admin',array('model'=>$model,));
         
    }

    /**
     * This is the action to handle external exceptions.
     */
    public function actionError()
	{
	    if($error=Yii::app()->errorHandler->error)
	    {
	    	if(Yii::app()->request->isAjaxRequest)
                    echo $error['message'];
	    	else
                    $this->render('error', $error);
                        
                $ip =Yii::app()->request->userHostAddress;
                //$url = Yii::app()->request->getUrl();
                //$urlA = Yii::app()->request->getUrlReferrer();
                Yii::log(" IP: $ip","solicitud",'IP');
                
	    }
	}
    
    /**
    * Displays the login page
    */
   public function actionLogin()
    {
            $model=new LoginForm;

            // if it is ajax validation request
            if(isset($_POST['ajax']) && $_POST['ajax']==='login-form')
            {
                    echo CActiveForm::validate($model);
                    Yii::app()->end();
            }

            // collect user input data
            if(isset($_POST['LoginForm']))
            {
                    $model->attributes=$_POST['LoginForm'];
                    // validate user input and redirect to the previous page if valid
                    if($model->validate() && $model->login())
                            $this->redirect(Yii::app()->user->returnUrl);
            }
            // display the login form
            $this->render('login',array('model'=>$model));
    }
   /**
    * Logs out the current user and redirect to homepage.
    */
    public function actionLogout()
    {
           Yii::app()->user->logout();
           $this->redirect(Yii::app()->homeUrl);
    }
}