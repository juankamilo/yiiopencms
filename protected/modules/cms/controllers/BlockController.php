<?php
/**
 * BlockController class file.
 * @author Christoffer Niska <christoffer.niska@nordsoftware.com>
 * @copyright Copyright &copy; 2012, Nord Software Ltd
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package cms.controllers
 * @since 2.0.0
 */

/**
 * Block controller.
 * @property CmsModule $module
 */
class BlockController extends NodeController
{
	/**
	 * Displays the page to create a new model.
	 */
	public function actionCreate()
	{
		$model = new CmsBlock();

		if (isset($_POST['CmsBlock']))
		{
			$model->attributes = $_POST['CmsBlock'];
			if ($model->save())
			{
				Yii::app()->user->setFlash($this->module->flashes['success'], Yii::t('CmsModule.core', 'Block created.'));
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

		if (isset($_POST['CmsBlock'], $_POST['CmsBlockContent']))
		{
			$valid = true;
			foreach ($translations as $language => $content)
			{
				$content->attributes = $_POST['CmsBlockContent'][$language];
				$valid = $valid && $content->validate();
				$translations[$language] = $content;
			}

			if ($valid)
			{
				$model->attributes = $_POST['CmsBlock'];
				$model->save(); // we need to save the page so that the 'updated' column is updated

				foreach ($translations as $content)
					$content->save();

				Yii::app()->user->setFlash($this->module->flashes['success'], Yii::t('CmsModule.core', 'Block updated.'));
				$this->redirect(array('index'));
			}
		}

		$this->render('update', array('model' => $model));
	}

	public function actionIndex()
	{
		$model = new CmsBlock('search');
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
		Yii::app()->user->setFlash($this->module->flashes['success'], Yii::t('CmsModule.core', 'Block deleted.'));

		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if (!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : Yii::app()->homeUrl);
	}

	/**
	 * Returns the form tabs for BootTabbable.
	 * @param CForm $form the form model
	 * @param CmsPage $model the model
	 * @return array the tabs
	 */
	public function getFormTabs($form, $model)
	{
		return array(
			$this->getContentTab($form, $model),
			$this->getBlockTab($form, $model),
		);
	}

	/**
	 * Returns the page tab for BootTabbable.
	 * @param CForm $form the form model
	 * @param CmsPage $model the model
	 * @return array the tab configuration
	 */
	protected  function getBlockTab($form, $model)
	{
		return array(
			'label' => Yii::t('CmsModule.core', 'Block'),
			'content' => $this->renderPartial('_form', array('form' => $form, 'model' => $model), true),
			'active' => isset($_GET['tab']) && $_GET['tab'] === 'model',
		);
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return CmsBlock the model
	 * @throws CHttpException if the node does not exist
	 */
	public function loadModel($id)
	{
		$model = CmsBlock::model()->findByPk($id);

		if ($model === null)
			throw new CHttpException(404, Yii::t('CmsModule.core', 'The requested page does not exist.'));

		return $model;
	}
}
