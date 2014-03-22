<?php
/**
 * CmsLanguageWidget class file.
 * @author Christoffer Niska <christoffer.niska@nordsoftware.com>
 * @copyright Copyright &copy; 2012, Nord Software Ltd
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package cms.widgets
 * @since 2.0.0
 */

Yii::import('bootstrap.widgets.BootMenu');

/**
 * Widget that renders a "choose language"-menu.
 */
class CmsLanguageWidget extends BootMenu
{
	/**
	 * Initializes the widget.
	 */
	public function init()
	{
		$languages = Yii::app()->cms->languages;
		$activeLocale = Yii::app()->language;

		$items = array(array('label'=>Yii::t('cms', 'Language')));

		foreach ($languages as $locale => $language)
		{
			if ($locale === $activeLocale)
				$activeLanguage = $language;

			$items[] = array(
				'label' => $language,
				'url' => array('/cms/language/change', 'locale'=>$locale),
				'active' => $locale === $activeLocale,
			);
		}

		$label = isset($activeLanguage) ? $activeLanguage : Yii::t('cms', 'Unknown');
		$this->items = CMap::mergeArray(array(array('label'=> $label, 'items'=>$items)), $this->items);

		parent::init();
	}
}
