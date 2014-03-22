<?php
/**
 * CmsBlockContent class file.
 * @author Christoffer Niska <christoffer.niska@nordsoftware.com>
 * @copyright Copyright &copy; 2012, Nord Software Ltd
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package cms.models
 * @since 2.0.0
 */

/**
 * This is the model class for table "cms_block_content".
 *
 * The followings are the available columns in table 'cms_block_content':
 * @property string $id
 * @property string $blockId
 * @property string $locale
 * @property string $body
 */
class CmsBlockContent extends CmsContent
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return CmsBlockContent the static model class
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
		return 'cms_block_content';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('blockId, locale', 'required'),
			array('blockId', 'numerical', 'integerOnly'=>true),
			array('blockId', 'length', 'max'=>10),
			array('locale', 'length', 'max'=>50),
			array('body', 'safe'),
			array('id, blockId, locale, body', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '#',
			'blockId' => Yii::t('CmsModule.core', 'Block'),
			'locale' => Yii::t('CmsModule.core', 'Locale'),
			'heading' => Yii::t('CmsModule.core', 'Heading'),
			'body' => Yii::t('CmsModule.core', 'Body'),
			'published' => Yii::t('CmsModule.core', 'Published'),
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		$criteria = new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('blockId',$this->blockId,true);
		$criteria->compare('locale',$this->locale,true);
		$criteria->compare('body',$this->body,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}
