<?php
/**
 * CmsSourceMessage class file.
 * @author juan Restrepo <juanrestrepo@dmwared.com>
 * @copyright Copyright &copy; 2014, 
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package cms.models
 * @since 2.0.1
 */

/**
 * This is the model class for table "cms_source_message".
 *
 * The followings are the available columns in table 'cms_source_message':
 * @property integer $id
 * @property string $category
 * @property string $message
 *
 * The followings are the available model relations:
 * @property CmsMessage[] $cmsMessages
 */
class CmsSourceMessage extends CmsNode
{

        /**
         * Returns the static model of the specified AR class.
         * Please note that you should have this exact method in all your CActiveRecord descendants!
         * @param string $className active record class name.
         * @return CmsSourceMessage the static model class
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
            return 'cms_source_message';
        }

        /**
         * @return array validation rules for model attributes.
         */
        public function rules()
        {
            // NOTE: you should only define rules for those attributes that
            // will receive user inputs.
            return array(
                array('category', 'length', 'max'=>32),
                array('message', 'safe'),
                // The following rule is used by search().
                // @todo Please remove those attributes that should not be searched.
                array('id, category, message', 'safe', 'on'=>'search'),
            );
        }

        /**
         * @return array relational rules.
         */
        public function relations()
        {
            return array(
			'translations'=>array(self::HAS_MANY, 'CmsMessage', 'id'),
			'content'=>array(
				self::HAS_ONE, 'CmsMessage', 'id',
				'condition'=>'language=:language',
				'params'=>array(':language'=>Yii::app()->language),
			),
			'default'=>array(self::HAS_ONE, 'CmsMessage', 'id',
				'condition'=>'language=:language',
				'params'=>array(':language'=>Yii::app()->cms->defaultLocale),
			),
		);
        }

        /**
         * @return array customized attribute labels (name=>label)
         */
        public function attributeLabels()
        {
            return array(
                'id' => 'ID',
                'category' => 'Category',
                'message' => 'Message',
            );
        }

        /**
         * Retrieves a list of models based on the current search/filter conditions.
         *
         * Typical usecase:
         * - Initialize the model fields with values from filter form.
         * - Execute this method to get CActiveDataProvider instance which will filter
         * models according to data in model fields.
         * - Pass data provider to CGridView, CListView or any similar widget.
         *
         * @return CActiveDataProvider the data provider that can return the models
         * based on the search/filter conditions.
         */
        public function search()
        {
            // @todo Please modify the following code to remove attributes that should not be searched.

            $criteria=new CDbCriteria;

            $criteria->compare('id',$this->id);
            $criteria->compare('category',$this->category,true);
            $criteria->compare('message',$this->message,true);

            return new CActiveDataProvider($this, array(
                'criteria'=>$criteria,
            ));
        }

        /**
         * Creates content for this node.
         * @param string $locale the locale id, e.g. 'en'
         * @return CmsContent the content model
         */
        public function createTranslation($locale)
        {
                $content = new CmsMessage();
                $content->id = $this->id;
                $content->language = $locale;
                $content->save();
                
                return $content;
        }

        /**
         * Returns the associated content in a specific language.
         * @param string $locale the locale id, e.g. 'en'
         * @return CmsContent the content model
         */
        public function getTranslation($locale = 'en')
        {
                $model=CmsMessage::model()->findByAttributes(array(
                        'id'=>$this->id,
                        'language'=>$locale,
                ));
		if($model!==null)
                    return $model;
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