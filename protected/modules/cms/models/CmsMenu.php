<?php
/**
 * CmsMenu class file.
 * @author Christoffer Niska <christoffer.niska@nordsoftware.com>
 * @copyright Copyright &copy; 2011, Nord Software Ltd
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package cms.models
 * @since 2.0.0
 */

/**
 * This is the model class for table "cms_menu".
 *
 * The followings are the available columns in table 'cms_menu':
 * @property string $id
 * @property string $name
 * @property string $created
 * @property string $updated
 * @property boolean $deleted
 *
 * @property CmsMenuItem[] $items the associated menu items for the current language
 * @property CmsMenuItem[] $defaultItems the associated menu items for the default language
 */
class CmsMenu extends CmsNode
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className the class name
	 * @return CmsMenu the static model class
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
		return 'cms_menu';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('name', 'required'),
			array('deleted', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>255),
			array('name', 'unique'),
			array('created, updated', 'safe'),
			array('id, name, created, updated, deleted', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
			'items' => array(
				self::HAS_MANY, 'CmsMenuItem', 'menuId',
				'condition'=>'locale=:locale',
				'params'=>array(':locale'=>Yii::app()->language),
				'order'=>'weight ASC',
			),
			'defaultItems' => array(
				self::HAS_MANY, 'CmsMenuItem', 'menuId',
				'condition'=>'locale=:locale',
				'params'=>array(':locale'=>Yii::app()->cms->defaultLocale),
				'order'=>'weight ASC',
			),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '#',
			'name' => 'System name',
			'created' => 'Created',
			'updated' => 'Updated',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		$criteria = new CDbCriteria;

		$criteria->compare('id', $this->id, true);
		$criteria->compare('name', $this->name, true);
		$criteria->compare('created', $this->created, true);
		$criteria->compare('updated', $this->updated, true);

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
		));
	}

	/**
	 * Returns the associated links.
	 * @param string $locale the locale id, e.g. 'en'
	 * @return CActiveDataProvider
	 */
	public function getMenuItems($locale)
	{
		return new CActiveDataProvider('CmsMenuItem', array(
			'criteria' => array(
				'condition' => 'menuId=:menuId AND locale=:locale',
				'params' => array(':menuId' => $this->id, ':locale' => $locale),
			),
			'sort'=>array(
				'defaultOrder'=>'weight ASC',
			),
		));
	}

	/**
	 * Creates a translation for this node.
	 * @param string $locale the locale id, e.g. 'en'
	 * @return CmsContent the content model
	 */
	public function createTranslation($locale)
	{
		$item = new CmsMenuItem();
		$item->menuId = $this->id;
		$item->locale = $locale;
		$item->save();
		return $item;
	}

	/**
	 * Creates the item configurations for the menu widget.
	 * @return array the configurations
	 */
	public function createItems()
	{
		$items = array();
		$menuItems = $this->items;

		if (empty($menuItems))
			$menuItems = $this->defaultItems;

		foreach ($menuItems as $item)
			$items[] = $item->createConfig();

		return $items;
	}

	/**
	 * Updates the menu item weights.
	 * @param integer[] $data the new ID order
	 */
	public function updateItemWeights($data)
	{
		$weight = 0;
		$items = $this->items;
		foreach ($data as $id)
		{
			foreach ($items as $item)
			{
				if ($item->id == $id)
				{
					$item->weight = $weight++;
					$item->save(false);
				}
			}
		}
	}

	/**
	 * Returns the associated translation in a specific language.
	 * @param string $locale the locale id, e.g. 'en'
	 * @return CmsContent the translation model
	 */
	public function getTranslation($locale)
	{
		return CmsMenuItem::model()->findByAttributes(array(
			'menuId' => $this->id,
			'locale' => $locale,
		));
	}

	/**
	 * Renders the menu content.
	 * @return string the rendered content
	 */
	public function render()
	{
		ob_start();
		Yii::app()->cms->menu($this->name);
		return ob_get_clean();
	}
}
