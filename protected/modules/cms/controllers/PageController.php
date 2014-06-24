<?php
/**
 * NodeController class file.
 * @author Christoffer Niska <christoffer.niska@nordsoftware.com>
 * @copyright Copyright &copy; 2012, Nord Software Ltd
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package cms.controllers
 * @since 2.0.0
 */

/**
 * Page controller.
 * @property CmsModule $module
 */
class PageController extends NodeController
{
	/**
	 * @return array the action filters for this controller.
	 */
	public function filters()
	{
		return array(
			array('cms.components.CmsPageViewFilter + view'),
		);
	}

	/**
	 * Displays the page to create a new model.
	 */
	public function actionCreate()
	{
		$model = new CmsPage();

		if (isset($_POST['CmsPage']))
		{
			$model->attributes = $_POST['CmsPage'];
			if ($model->save())
			{
				Yii::app()->user->setFlash($this->module->flashes['success'], Yii::t('CmsModule.core', 'Page created.'));
				$this->redirect(array('update', 'id' => $model->id));
			}
		}

		$this->render('create', array('model' => $model));
	}

	/**
	 * Display the page to update a particular model.
	 * @param integer $id the id of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model = $this->loadModel($id);

		$translations = $this->getTranslations($model);

		if (isset($_POST['CmsPage'], $_POST['CmsPageContent']))
		{
			$valid = true;
			foreach ($translations as $language => $content)
			{
				$content->attributes = $_POST['CmsPageContent'][$language];
				$valid = $valid && $content->validate();
				$translations[$language] = $content;
			}

			if ($valid)
			{
				$model->attributes = $_POST['CmsPage'];
				$model->save(); // we need to save the page so that the 'updated' column is updated

				foreach ($translations as $content)
					$content->save();

				Yii::app()->user->setFlash($this->module->flashes['success'], Yii::t('CmsModule.core', 'Page updated.'));
				$this->redirect(array('index'));
			}
		}

		$this->render('update', array('model' => $model));
	}

	public function actionIndex()
	{
		$model = new CmsPage('search');
		$model->unsetAttributes(); // clear any default values
		$this->render('index', array('model' => $model));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the id of the model to be deleted
	 */
	public function actionDelete($id)
	{
		// we only allow deletion via POST request
		$this->loadModel($id)->delete();
		Yii::app()->user->setFlash($this->module->flashes['success'], Yii::t('CmsModule.core', 'Page deleted.'));

		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if (!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : Yii::app()->homeUrl);
	}

	/**
	 * Displays a particular page.
	 * @param integer $id the id of the model to display
	 */
	public function actionView($id)
	{
		$model = $this->loadModel($id);

		if ($model->hasContent())
		{
			$this->pageTitle = $model->pageTitle;
			$this->breadcrumbs = $model->breadcrumbs;
			/** @var CClientScript $cs */
			$cs = Yii::app()->getClientScript();
			//$cs->registerMetaTag($model->content->metaTitle, 'title');
			$cs->registerMetaTag($model->content->metaDescription, 'description');
			$cs->registerMetaTag($model->content->metaKeywords, 'keywords');
                        foreach ($model->contents as $v => $k){
                            $cs->registerLinkTag('alternate',  null, 
                                    Yii::app()->createAbsoluteUrl('cms/page/view', array('id'=>$k->pageId, 'name'=>$k->url, 'lang'=>$k->locale))
                                    , null,array('hreflang'=>$k->locale));
                        }
                        $cs->registerLinkTag('canonical', null,  Yii::app()->getBaseUrl(true).$model->getUrl(array('lang'=>Yii::app()->language)));
		}

		$this->layout = $this->module->pageLayout;

		$this->render('cms.views.page.view', array(
			'model' => $model,
			'heading' => $model->heading,
			'content' => $model->render(),
		));
	}

	/**
	 * Returns the form tabs for BootTabbable.
	 * @param CForm $form the form model
	 * @param CmsPage $model the page model
	 * @return array the tabs
	 */
	public function getFormTabs($form, $model)
	{
		return array(
			$this->getContentTab($form, $model),
			$this->getImagesTab($form, $model),
			$this->getAttachmentsTab($form, $model),
			$this->getPageTab($form, $model),
			//$this->getPreviewTab(),
		);
	}

	/**
	 * Returns the attachment tab for BootTabbable.
	 * @param CForm $form the form model
	 * @param CmsPage $model the page model
	 * @return array the tab configuration
	 */
	protected function getAttachmentsTab($form, $model)
	{
		return array(
			'label' => Yii::t('CmsModule.core', 'Attachments'),
			'content' => $this->renderPartial('_attachmentsForm', array('form' => $form, 'model' => $model), true),
			'active' => isset($_GET['tab']) && $_GET['tab'] === 'attachments',
		);
	}

	/**
	 * Returns the page tab for BootTabbable.
	 * @param CForm $form the form model
	 * @param CmsPage $model the model
	 * @return array the tab configuration
	 */
	protected function getPageTab($form, $model)
	{
		return array(
			'label' => Yii::t('CmsModule.core', 'Page'),
			'content' => $this->renderPartial('_form', array('form' => $form, 'model' => $model), true),
			'active' => isset($_GET['tab']) && $_GET['tab'] === 'model',
		);
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return CmsPage the model
	 * @throws CHttpException if the node does not exist
	 */
	public function loadModel($id)
	{
		$model = CmsPage::model()->findByPk($id);

		if ($model === null)
			throw new CHttpException(404, Yii::t('CmsModule.core', 'The requested page does not exist.'));

                return $model;
	}
        /*
        *  Se agrega opcion de traduccion segun sistema antiguo para el manejo, ya que el de nord no funciona
        * 
        */   
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
        /*
         * Controla el dropdown de los idiomas
         */
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
