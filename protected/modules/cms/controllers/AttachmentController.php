<?php
/**
 * AttachmentController class file.
 * @author Eric Nishio <eric.nishio@nordsoftware.com>
 * @author Christoffer Niska <christoffer.niska@nordsoftware.com>
 * @copyright Copyright &copy; 2012, Nord Software Ltd
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package cms.controllers
 * @since 2.0.0
 */

/**
 * Attachment controller.
 * @property CmsModule $module
 */
class AttachmentController extends CmsController
{
	/**
	 * Displays the page to create a new model.
	 * @param integer $pageId the associated page ID.
	 */
	public function actionAdd($pageId)
	{
		$model = new CmsAttachment();
		$page = CmsPage::model()->findByPk($pageId);

		if (isset($_POST['CmsAttachment']))
		{
			$model->attributes = $_POST['CmsAttachment'];
			$model->pageId = $pageId;
			$model->parseFile(CUploadedFile::getInstance($model, 'file'));
			if ($model->save())
			{
				Yii::app()->user->setFlash($this->module->flashes['success'], Yii::t('CmsModule.core', 'Attachment added.'));
				$this->redirect(array('page/update', 'id'=>$page->id, 'tab'=>'attachments'));
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
		$this->loadModel($id)->delete();
		Yii::app()->user->setFlash($this->module->flashes['success'], Yii::t('CmsModule.core', 'Attachment deleted.'));

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
