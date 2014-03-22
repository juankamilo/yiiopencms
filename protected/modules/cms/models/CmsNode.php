<?php
/**
 * CmsNode class file.
 * @author Christoffer Niska <christoffer.niska@nordsoftware.com>
 * @copyright Copyright &copy; 2012, Nord Software Ltd
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package cms.models
 * @since 0.9.0
 */

Yii::import('cms.components.CmsActiveRecord');


/**
 * Node model.
 * @property boolean $published
 *
 * The following relations are available for this model:
 * @property CmsContent $content the translation model for the current language
 * @property CmsContent $default the translation model for the default language
 */
abstract class CmsNode extends CmsActiveRecord
{
	/**
	 * Creates a translation for this node.
	 * @param string $locale the locale id, e.g. 'en'
	 * @return CmsContent the content model
	 * @abstract
	 */
	abstract public function createTranslation($locale);

	/**
	 * Returns the associated translation in a specific language.
	 * @param string $locale the locale id, e.g. 'en'
	 * @return CmsContent the translation model
	 * @abstract
	 */
	abstract public function getTranslation($locale);

	/**
	 * Returns the translated value for the given attribute in the active language.
	 * @param string $name the attribute name
	 * @param string $defaultValue the default value
	 * @return string the value
	 */
	protected function getTranslatedAttribute($name, $defaultValue)
	{
		if ($this->content !== null && !empty($this->content->{$name}))
	        $value = $this->content->{$name};
	    else if ($this->default !== null && !empty($this->default->{$name}))
		    $value = $this->default->{$name};
	    else
		    $value = $defaultValue;

		return $value;
	}

	/**
	 * Returns whether the node is published.
	 * @return boolean the result
	 */
	public function getPublished()
	{
		return $this->published === '1';
	}
}
