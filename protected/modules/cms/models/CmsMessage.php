<?php
/**
 * CmsMessage class file.
 * @author juan Restrepo <juanrestrepo@dmwared.com>
 * @copyright Copyright &copy; 2014, 
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package cms.models
 * @since 2.0.1
 */

/**
 * This is the model class for table "cms_message".
 *
 * The followings are the available columns in table 'cms_message':
 * @property integer $id
 * @property string $language
 * @property string $translation
 *
 * The followings are the available model relations:
 * @property CmsSourceMessage $id0
 */
class CmsMessage extends CmsContent
{

       /**
        * Returns the static model of the specified AR class.
        * Please note that you should have this exact method in all your CActiveRecord descendants!
        * @param string $className active record class name.
        * @return CmsMessage the static model class
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
            return 'cms_message';
        }

        /**
         * @return array validation rules for model attributes.
         */
        public function rules()
        {
            // NOTE: you should only define rules for those attributes that
            // will receive user inputs.
            return array(
                array('id', 'numerical', 'integerOnly'=>true),
                array('language', 'length', 'max'=>16),
                array('translation', 'safe'),
                // The following rule is used by search().
                // @todo Please remove those attributes that should not be searched.
                array('id, language, translation', 'safe', 'on'=>'search'),
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
                'id0' => array(self::BELONGS_TO, 'CmsSourceMessage', 'id'),
            );
        }

        /**
         * @return array customized attribute labels (name=>label)
         */
        public function attributeLabels()
        {
            return array(
                'id' => 'ID',
                'language' => 'Language',
                'translation' => 'Translation',
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

            $criteria->compare('id',$this->id,true);
            $criteria->compare('language',$this->language,true);
            $criteria->compare('message',$this->message,true);

            return new CActiveDataProvider($this, array(
                'criteria'=>$criteria,
            ));
        }

    
}