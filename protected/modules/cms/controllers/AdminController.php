<?php
/**
 * AdminController class file.
 * @author Christoffer Niska <christoffer.niska@nordsoftware.com>
 * @copyright Copyright &copy; 2011, Nord Software Ltd
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package cms.controllers
 * @since 1.0.0
 */

/**
 * Administration controller.
 * @property CmsModule $module
 */
class AdminController extends CmsController
{
	/**
	 * Displays the index page.
	 */
	public function actionIndex()
	{
		$this->render('index');
	}
}
