<?php
/**
 * CmsBlock class file.
 * @author Christoffer Niska <christoffer.niska@nordsoftware.com>
 * @copyright Copyright &copy; 2012, Nord Software Ltd
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package cms.models
 * @since 2.0.0
 */

/**
 * This is the model class for table "cms_block".
 *
 * The followings are the available columns in table 'cms_block':
 * @property string $id
 * @property string $created
 * @property string $updated
 * @property string $name
 * @property boolean $published
 * @property boolean $deleted
 *
 * @property string $body the body content
 */
class CmsBlock extends CmsNode
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return CmsBlock the static model class
	 */
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'cms_block';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('name', 'required'),
			array('published, deleted', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>255),
			array('name', 'unique'),
			array('created, updated', 'safe'),
			array('id, created, updated, name, published', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
			'translations'=>array(self::HAS_MANY, 'CmsBlockContent', 'blockId'),
			'content'=>array(
				self::HAS_ONE, 'CmsBlockContent', 'blockId',
				'condition'=>'locale=:locale',
				'params'=>array(':locale'=>Yii::app()->language),
			),
			'default'=>array(self::HAS_ONE, 'CmsBlockContent', 'blockId',
				'condition'=>'locale=:locale',
				'params'=>array(':locale'=>Yii::app()->cms->defaultLocale),
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
			'created' => Yii::t('CmsModule.core', 'Created'),
			'updated' => Yii::t('CmsModule.core', 'Updated'),
			'name' => Yii::t('CmsModule.core', 'System name'),
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
		$criteria->compare('created', $this->created, true);
		$criteria->compare('updated', $this->updated, true);
		$criteria->compare('name', $this->name, true);
		$criteria->compare('published', $this->published);

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
		));
	}

	/**
	 * Creates content for this node.
	 * @param string $locale the locale id, e.g. 'en'
	 * @return CmsContent the content model
	 */
	public function createTranslation($locale)
	{
		$content = new CmsBlockContent();
		$content->blockId = $this->id;
		$content->locale = $locale;
		$content->save();
		return $content;
	}

	/**
	 * Returns the associated content in a specific language.
	 * @param string $locale the locale id, e.g. 'en'
	 * @return CmsContent the content model
	 */
	public function getTranslation($locale)
	{
		return CmsBlockContent::model()->findByAttributes(array(
			'blockId'=>$this->id,
			'locale'=>$locale,
		));
	}

	/**
	 * Returns the body for this node.
	 * @return string the body
	 */
	public function getBody()
	{
		return $this->getTranslatedAttribute('body', '');
	}

	/**
	 * Renders the block content.
	 * @return string the rendered content
	 */
	public function render()
	{
		return Yii::app()->cms->renderer->renderBlock($this);
	}
}
