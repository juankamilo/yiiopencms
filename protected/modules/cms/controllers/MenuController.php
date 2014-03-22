<?php
/**
 * MenuController class file.
 * @author Eric Nishio <eric.nishio@nordsoftware.com>
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
class MenuController extends NodeController
{
	/**
	 * Displays the page to create a new model.
	 */
	public function actionCreate()
	{
		$model = new CmsMenu();

		if (isset($_POST['CmsMenu']))
		{
			$model->attributes = $_POST['CmsMenu'];
			if ($model->save())
			{
				Yii::app()->user->setFlash($this->module->flashes['success'], Yii::t('CmsModule.core', 'Menu created.'));
				$this->redirect(array('update', 'id'=>$model->id));
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

		/*
		if (isset($_POST['ajax']) && $_POST['ajax'] === 'addMenuItem')
		{
			echo CActiveForm::validate(new CmsMenuItem());
			Yii::app()->end();
		}
		*/

		if (isset($_POST['CmsMenu']))
		{
			$model->attributes = $_POST['CmsMenu'];
			if ($model->save())
			{
				Yii::app()->user->setFlash($this->module->flashes['success'], Yii::t('CmsModule.core', 'Menu updated.'));
				$this->redirect(array('index'));
			}
		}

		$this->render('update', array('model' => $model));
	}

	public function actionIndex()
	{
		$model = new CmsMenu('search');
		$model->unsetAttributes();  // clear any default values
		$this->render('index', array('model' => $model,));
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
		Yii::app()->user->setFlash($this->module->flashes['success'], Yii::t('CmsModule.core', 'Menu deleted.'));

		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if (!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : Yii::app()->homeUrl);
	}

	/**
	 * Updates the item weights of a particular model.
	 */
	public function actionAjaxSortable()
	{
		$model = $this->loadModel($_POST['id']);

		$data = array();
		foreach ($_POST['data'] as $item)
			$data[] = preg_replace('/.*_(\d+)/i', '$1', $item);

		$model->updateItemWeights($data);

		Yii::app()->end();
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return CmsMenu the model
	 * @throws CHttpException if the menu does not exist.
	 */
	public function loadModel($id)
	{
		$model = CmsMenu::model()->findByPk($id);

		if ($model === null)
			throw new CHttpException(404, Yii::t('CmsModule.core', 'The requested page does not exist.'));

		return $model;
	}
}
