<?php

/**
 * This is the model class for table "image".
 *
 * The followings are the available columns in table 'image':
 * @property string $id
 * @property string $created
 * @property string $ownerId
 * @property string $owner
 * @property string $name
 * @property string $path
 * @property string $extension
 * @property string $filename
 * @property string $byteSize
 * @property string $mimeType
 */
class Image extends CActiveRecord
{
    const METHOD_RESIZE = 'resize';
    const METHOD_RESIZE_PERCENT = 'resizePercent';
    const METHOD_ADAPTIVE_RESIZE = 'adaptiveResize';
    const METHOD_CROP = 'crop';
    const METHOD_CROP_CENTER = 'cropFromCenter';
    const METHOD_ROTATE = 'rotate';
    const METHOD_ROTATE_DEGREES = 'rotateDegrees';

    const DIRECTION_CLOCKWISE = 'CW';
    const DIRECTION_COUNTER_CLOCKWISE = 'CCW';
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'image';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('created, ownerId, owner, name, path, extension, filename, byteSize, mimeType', 'required'),
            array('ownerId, byteSize', 'length', 'max'=>10),
            array('owner, name, path, extension, filename, mimeType', 'length', 'max'=>255),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, created, ownerId, owner, name, path, extension, filename, byteSize, mimeType', 'safe', 'on'=>'search'),
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
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'created' => 'Created',
            'ownerId' => 'Owner',
            'owner' => 'Owner',
            'name' => 'Name',
            'path' => 'Path',
            'extension' => 'Extension',
            'filename' => 'Filename',
            'byteSize' => 'Byte Size',
            'mimeType' => 'Mime Type',
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
        $criteria->compare('created',$this->created,true);
        $criteria->compare('ownerId',$this->ownerId,true);
        $criteria->compare('owner',$this->owner,true);
        $criteria->compare('name',$this->name,true);
        $criteria->compare('path',$this->path,true);
        $criteria->compare('extension',$this->extension,true);
        $criteria->compare('filename',$this->filename,true);
        $criteria->compare('byteSize',$this->byteSize,true);
        $criteria->compare('mimeType',$this->mimeType,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Image the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
    
    /**
        * Renders this image.
        * @param string $version the image version to render.
        * @param string $alt the alternative text.
        * @param array $htmlOptions the html options.
        */
       public function render($version, $alt = '', $htmlOptions = array())
       {
               $src = $this->getUrl($version);
               echo CHtml::image($src, $alt, $htmlOptions);
       }

       /**
        * Returns the URL to the given image version.
        * @param string $version the image version.
        * @return string|boolean the URL or false if the version is invalid.
        */
       public function getUrl($version)
       {
               return Yii::app()->image->getUrl($this->id, $version);
       }

       /**
        * @return string the path for this image.
        */
       public function getPath()
       {
               return !empty($this->path) ? $this->path.'/' : '';
       }

       /**
        * @return string the image file name.
        */
       public function resolveFilename()
       {
               return $this->name.'.'.$this->extension;
       }
    
}