<?php
/**
 * CmsPageContent class file.
 * @author Christoffer Niska <christoffer.niska@nordsoftware.com>
 * @copyright Copyright &copy; 2012, Nord Software Ltd
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package cms.models
 * @since 2.0.0
 */

/**
 * This is the model class for table "cms_page_content".
 *
 * The following properties are available in this model:
 * @property string $id
 * @property string $pageId
 * @property string $locale
 * @property string $heading
 * @property string $body
 * @property string $url
 * @property string $pageTitle
 * @property string $breadcrumb
 * @property string $metaTitle
 * @property string $metaDescription
 * @property string $metaKeywords
 */
class CmsPageContent extends CmsContent
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className the class name
	 * @return CmsPageContent the static model class
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
		return 'cms_page_content';
	}

	/**
	 * @return array validation rules for model attributes
	 */
	public function rules()
	{
		return array(
			array('pageId, locale', 'required'),
			array('pageId', 'length', 'max'=>10),
			array('locale', 'length', 'max'=>50),
			array('heading, url, pageTitle, breadcrumb, metaTitle, metaDescription, metaKeywords', 'length', 'max'=>255),
			array('pageId', 'numerical', 'integerOnly'=>true),
			array('body', 'safe'),
			array('id, pageId, locale, heading, url, pageTitle, breadcrumb, metaTitle, metaDescription, metaKeywords',
					'safe', 'on'=>'search'),
		);
	}
        
        /**
         * @return array relational rules.
         */
        public function relations()
        {
            // NOTE: you may need to adjust the relation name and the related
            // class name for the relations automatically generated below.
            return array(
                'page' => array(self::BELONGS_TO, 'CmsPage', 'pageId'),
            );
        }

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '#',
			'pageId' => Yii::t('CmsModule.core', 'Page'),
			'locale' => Yii::t('CmsModule.core', 'Locale'),
			'heading' => Yii::t('CmsModule.core', 'Heading'),
			'body' => Yii::t('CmsModule.core', 'Body'),
			'url' => Yii::t('CmsModule.core', 'URL Alias'),
			'pageTitle' => Yii::t('CmsModule.core', 'Page Title'),
			'breadcrumb' => Yii::t('CmsModule.core', 'Breadcrumb'),
			'metaTitle' => Yii::t('CmsModule.core', 'Meta Title'),
			'metaDescription' => Yii::t('CmsModule.core', 'Meta Description'),
			'metaKeywords' => Yii::t('CmsModule.core', 'Meta Keywords'),
			'published' => Yii::t('CmsModule.core', 'Published'),
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('pageId',$this->pageId,true);
		$criteria->compare('locale',$this->locale,true);
		$criteria->compare('heading',$this->heading,true);
		$criteria->compare('body',$this->body,true);
		$criteria->compare('url',$this->url,true);
		$criteria->compare('pageTitle',$this->pageTitle,true);
		$criteria->compare('breadcrumb',$this->breadcrumb,true);
		$criteria->compare('metaTitle',$this->metaTitle,true);
		$criteria->compare('metaDescription',$this->metaDescription,true);
		$criteria->compare('metaKeywords',$this->metaKeywords,true);
		$criteria->compare('published',$this->published);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the HTMLPurifier instance for this content.
	 * @return CHtmlPurifier the purifier
	 */
	protected function getPurifier()
	{
		$purifier = new CHtmlPurifier();
		$purifier->options = CMap::mergeArray(Yii::app()->cms->htmlPurifierOptions, array(
			'Attr.EnableID'=>true, // we need to enable the id attribute
		));
		return $purifier;
	}
}
