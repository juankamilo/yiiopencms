<?php
/**
 * CmsNodeFilter class file.
 * @author Christoffer Niska <christoffer.niska@nordsoftware.com>
 * @copyright Copyright &copy; 2011, Nord Software Ltd
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package cms.components
 */

/**
 * Filter that ensures that the page is published and issues a 301 redirect to the correct URL
 * in case an incorrect page URL was requested.
 */
class CmsPageViewFilter extends CFilter
{
	/**
	 * Performs the pre-action filtering.
	 * @param CFilterChain $filterChain the filter chain that the filter is on
	 * @return boolean whether the filtering process should continue and the action should be executed
	 * @throws CHttpException if the page isn't published
	 */
	protected function preFilter($filterChain)
	{
		$controller = $filterChain->controller;

		if (isset($_GET['id']) && method_exists($controller, 'loadModel'))
		{
			$model = $controller->loadModel($_GET['id']);

			// Prevent accessing of unpublished pages.
			if (!$model->published)
				throw new CHttpException(404, Yii::t('CmsModule.core', 'The requested page does not exist.'));

			$url = $model->getUrl();
			if (strpos(Yii::app()->request->getRequestUri(), $url) === false)
				$controller->redirect($url, true, 301);
		}

		return parent::preFilter($filterChain);
	}
}
