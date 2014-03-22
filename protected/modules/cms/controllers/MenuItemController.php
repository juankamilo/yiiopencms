<?php
/**
 * MenuItemController class file.
 * @author Eric Nishio <eric.nishio@nordsoftware.com>
 * @author Christoffer Niska <christoffer.niska@nordsoftware.com>
 * @copyright Copyright &copy; 2012, Nord Software Ltd
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package cms.controllers
 * @since 2.0.0
 */

/**
 * Menu item controller controller.
 * @property CmsModule $module
 */
class MenuItemController extends CmsController
{
	/**
	 * Displays the page to create a new model.
	 * @param integer $menuId the associated menu ID.
	 * @param string $locale the locale id, e.g. 'en'
	 */
	public function actionAdd($menuId, $locale)
	{
		$model = new CmsMenuItem();
		$menu = CmsMenu::model()->findByPk($menuId);

		if (isset($_POST['CmsMenuItem']))
		{
			$model->attributes = $_POST['CmsMenuItem'];
			$model->menuId = $menuId;
			$model->locale = $locale;
			if ($model->save())
			{
				Yii::app()->user->setFlash($this->module->flashes['success'], Yii::t('CmsModule.core', 'Link added.'));
				$this->redirect(array('menu/update', 'id'=>$menuId));
			}
		}

		$this->render('add', array('model' => $model, 'menu' => $menu));
	}

	/**
	 * Display the page to update a particular model.
	 * @param integer $id the id of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model = $this->loadModel($id);

		if (isset($_POST['CmsMenuItem']))
		{
			$model->attributes = $_POST['CmsMenuItem'];
			if ($model->save())
			{
				Yii::app()->user->setFlash($this->module->flashes['success'], Yii::t('CmsModule.core', 'Link updated.'));
				$this->redirect(array('menu/update', 'id'=>$model->menu->id));
			}
		}

		$this->render('update', array('model' => $model, 'menu' => $model->menu));
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
		Yii::app()->user->setFlash($this->module->flashes['success'], Yii::t('CmsModule.core', 'Link deleted.'));

		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if (!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : Yii::app()->homeUrl);
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return CmsMenuItem the model
	 * @throws CHttpException if the menu does not exist.
	 */
	public function loadModel($id)
	{
		$model = CmsMenuItem::model()->findByPk($id);

		if ($model === null)
			throw new CHttpException(404, Yii::t('CmsModule.core', 'The requested page does not exist.'));

		return $model;
	}
}
