<?php
/**
 * ImageController class file.
 * @author Eric Nishio <eric.nishio@nordsoftware.com>
 * @author Christoffer Niska <christoffer.niska@nordsoftware.com>
 * @copyright Copyright &copy; 2012, Nord Software Ltd
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package cms.controllers
 * @since 2.0.0
 */

/**
 * Image controller.
 * @property CmsModule $module
 */
class ImageController extends CmsController
{
	/**
	 * Displays the page to create a new model.
	 * @param integer $pageId the associated page ID.
	 */
	public function actionAdd($pageId)
	{
		$model = new Image();
		$page = CmsPage::model()->findByPk($pageId);

		if (isset($_POST['Image']))
		{
			$model->attributes = $_POST['Image'];
			$file = CUploadedFile::getInstance($model, 'file');

			if ($file instanceof CUploadedFile)
			{
				$page->saveImage($file, $model->name, 'page');
				Yii::app()->user->setFlash($this->module->flashes['success'], Yii::t('CmsModule.core', 'Image added.'));
				$this->redirect(array('page/update', 'id'=>$page->id, 'tab'=>'images'));
			}
		}

		$this->render('add', array('model' => $model, 'page' => $page));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the id of the model to be deleted
	 */
	public function actionDelete($id)
	{
		// we only allow deletion via POST request
		Yii::app()->image->delete($id);
		Yii::app()->user->setFlash($this->module->flashes['success'], Yii::t('CmsModule.core', 'Image deleted.'));

		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if (!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : Yii::app()->homeUrl);
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return CmsAttachment the model
	 * @throws CHttpException if the menu does not exist.
	 */
	public function loadModel($id)
	{
		$model = CmsAttachment::model()->findByPk($id);

		if ($model === null)
			throw new CHttpException(404, Yii::t('CmsModule.core', 'The requested page does not exist.'));

		return $model;
	}
}
