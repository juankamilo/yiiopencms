<?php
/**
 * CmsUrlManager class file.
 * @author Christoffer Niska <christoffer.niska@nordsoftware.com>
 * @copyright Copyright &copy; 2012, Nord Software Ltd
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package cms.components
 */

/**
 * URL manager that appends the application language to each URL to allow for multilingual URLs.
 */
class CmsUrlManager extends CUrlManager
{
	/**
	 * Constructs a URL.
	 * @param string $route the controller and the action (e.g. article/read)
	 * @param array $params list of GET parameters (name=>value).
	 * @param string $ampersand the token separating name-value pairs in the URL. Defaults to '&'.
	 * @return string the constructed URL
	 * @see CUrlManager::createUrl
	 */
	public function createUrl($route, $params = array(), $ampersand = '&')
	{
		
		return parent::createUrl($route, $params, $ampersand);
	}

}
