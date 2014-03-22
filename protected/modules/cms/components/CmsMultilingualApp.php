<?php
/**
 * CmsMultilingualApp class file.
 * @author Christoffer Niska <ChristofferNiska@gmail.com>
 * @copyright Copyright &copy; 2012, Christoffer Niska
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package cms.components
 */

/**
 * Application behavior for setting the language using a user state.
 * @property CWebApplication $owner
 */
class CmsMultilingualApp extends CBehavior
{
	/**
	 * @return array the behavior events.
	 */
	public function events()
	{
		return array(
			'onBeginRequest'=>'setLanguage',
		);
	}

	/**
	 * Sets the application language from a user state if applicable.
	 */
	protected function setLanguage()
	{
		$matches = array();

		if ($this->owner->user->hasState('__locale'))
			$language = $this->owner->user->getState('__locale');
		else if (preg_match('/^\/([a-z]{2}(?:_[a-z]{2})?)\//i',
				substr($this->owner->request->url, strlen($this->owner->baseUrl)), $matches) !== false
				&& isset($matches[1]) && in_array($matches[1], array_keys($this->owner->cms->languages)))
			$language = $matches[1];
		else
			$language = $this->owner->cms->defaultLocale;

		$this->owner->language = $language;
	}
}
