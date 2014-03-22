<?php
/**
 * NodeController class file.
 * @author Christoffer Niska <christoffer.niska@nordsoftware.com>
 * @copyright Copyright &copy; 2012, Nord Software Ltd
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package cms.controllers
 */

/**
 * Abstract node controller.
 * @property CmsModule @module
 */
class NodeController extends CmsController
{
	private $_translations;

	/**
	 * Returns the language tabs for BootTabbable.
	 * @param CForm $form the form model
	 * @param CmsPage $model the model
	 * @return array the tabs
	 */
	public function getLanguageTabs($form, $model)
	{
		$tabs = array();

		$translations = $this->getTranslations($model);

		foreach ($translations as $locale => $item)
		{
			$tabs[] = array(
				'active' => $locale === Yii::app()->language,
				'label' => Yii::app()->cms->languages[$locale],
				'content' => $this->renderPartial('_languageForm', array(
					'model' => $item,
					'form' => $form,
					'node' => $model,
					'locale' => $locale,
					'language' => Yii::app()->cms->languages[$locale],
				), true),
			);
		}

		return $tabs;
	}

	/**
	 * Returns the associated content for the given model creating them if necessary.
	 * @param CmsNode $model the page model
	 * @return CmsContent[] the content models
	 */
	protected function getTranslations($model)
	{
		if (!isset($this->_translations))
		{
			$translations = array();
			foreach (array_keys(Yii::app()->cms->languages) as $language)
			{
				$item = $model->getTranslation($language);

				if ($item === null)
					$item = $model->createTranslation($language);

				$translations[$language] = $item;
			}
			$this->_translations = $translations;
		}

		return $this->_translations;
	}

	/**
	 * Returns the content tab for BootTabbable.
	 * @param CForm $form the form model
	 * @param CmsPage $model the model
	 * @return array the tab configuration
	 */
	protected function getContentTab($form, $model)
	{
		return array(
			'label' => Yii::t('CmsModule.core', 'Content'),
			'content' => $this->renderPartial('cms.views.node._contentForm', array('form' => $form, 'model' => $model), true),
			'active' => !isset($_GET['tab']) || (isset($_GET['tab']) && $_GET['tab'] === 'content'),
		);
	}

	/**
	 * Returns the image tab for BootTabbable.
	 * @param CForm $form the form model
	 * @param CmsPage $model the model
	 * @return array the tab configuration
	 */
	protected function getImagesTab($form, $model)
	{
		return array(
			'label' => Yii::t('CmsModule.core', 'Images'),
			'content' => $this->renderPartial('cms.views.node._imagesForm', array('form' => $form, 'model' => $model), true),
			'active' => isset($_GET['tab']) && $_GET['tab'] === 'images',
		);
	}

	/*
	protected function getPreviewTab()
	{
		return array(
			'label' => Yii::t('CmsModule.core', 'Preview'),
			'content' => $this->renderPartial('cms.views.node._preview', null, true),
			'active' => isset($_GET['tab']) && $_GET['tab'] === 'preview',
			'itemOptions' => array('class' => 'pull-right'),
		);
	}
	*/
}
