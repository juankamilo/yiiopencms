<?php
/**
 * CmsMenuItem class file.
 * @author Christoffer Niska <christoffer.niska@nordsoftware.com>
 * @copyright Copyright &copy; 2011, Nord Software Ltd
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package cms.models
 * @since 2.0.0
 */

Yii::import('cms.components.CmsActiveRecord');

/**
 * This is the model class for table "cms_menu_item".
 *
 * The followings are the available columns in table 'cms_menu_item':
 * @property string $id
 * @property string $menuId
 * @property string $locale
 * @property string $label
 * @property string $url
 * @property string $weight
 * @property boolean $visible
 *
 * The following relations are available for this model:
 * @property CmsMenu $menu the associated menu
 */
class CmsMenuItem extends CmsActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className the class name
	 * @return CmsMenuItem the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'cms_menu_item';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('menuId, locale, label, url, weight', 'required'),
			array('menuId, weight, visible', 'numerical', 'integerOnly'=>true),
			array('menuId', 'length', 'max'=>10),
			array('locale', 'length', 'max'=>50),
			array('label, url', 'length', 'max'=>255),
			array('id, menuId, label, url, visible', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
			'menu' => array(self::BELONGS_TO, 'CmsMenu', 'menuId'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '#',
			'menuId' => Yii::t('CmsModule.core', 'Menu'),
			'label' => Yii::t('CmsModule.core', 'Label'),
			'url' => Yii::t('CmsModule.core', 'URL'),
			'weight' => Yii::t('CmsModule.core', 'Weight'),
			'visible' => Yii::t('CmsModule.core', 'Visible'),
		);
	}

	/**
	 * Creates the menu item config for BootMenu.
	 * @return array the config
	 */
	public function createConfig()
	{
		$config = array();

		if ($this->visible)
		{
			$visible = true;

			if (strpos($this->url, 'http') !== false)
				$url = $this->url;
			else if (strpos($this->url, '/') !== false)
				$url = array($this->url);
			else
			{
				/** @var Cms $cms */
				$cms = Yii::app()->cms;
				$name = $this->url;
				$page = $cms->loadPage($name);

				// Ensure that we don't like to unpublished pages.
				if (!$page->published)
					$visible = false;

				$url = $cms->createUrl($name);
				$active = $cms->isActive($name);
			}

			if (isset($url))
			{
				$config['label'] = $this->label;
				$config['url'] = $url;
				$config['visible'] = $visible;

				if (isset($active))
					$config['active'] = $active;
			}
		}

		return $config;
	}

	/**
	 * Returns whether the link is visible.
	 * @return boolean the result
	 */
	public function getVisible()
	{
		return $this->visible === '1';
	}
}
