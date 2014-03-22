<?php
/**
 * CmsAttachment class file.
 * @author Christoffer Niska <christoffer.niska@nordsoftware.com>
 * @copyright Copyright &copy; 2011, Nord Software Ltd
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package cms.models
 * @since 1.0.0
 */

Yii::import('cms.components.CmsActiveRecord');

/**
 * This is the model class for table "cms_attachment".
 *
 * The following are the available columns in this model:
 * @property string $id
 * @property string $created
 * @property string $pageId
 * @property string $name
 * @property string $extension
 * @property string $filename
 * @property string $mimeType
 * @property string $byteSize
 *
 * The following relations are available for this model:
 * @property CmsPage $owner the model that this attachment belongs to
 */
class CmsAttachment extends CmsActiveRecord
{
	/**
	 * @var CUploadedFile
	 */
	public $file;

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className the class name
	 * @return CmsAttachment the static model class
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
		return 'cms_attachment';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('pageId, name, extension, filename, mimeType, byteSize, file', 'required'),
			array('pageId, byteSize', 'length', 'max'=>10),
			array('extension', 'length', 'max'=>50),
			array('name, filename, mimeType', 'length', 'max'=>255),
			array('file', 'file', 'types'=>Yii::app()->cms->allowedFileTypes,
					'maxSize'=>Yii::app()->cms->allowedFileSize, 'allowEmpty'=>true),
			array('id, created, pageId, extension, filename, mimeType, byteSize', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
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
			'created' => Yii::t('CmsModule.core', 'Created'),
			'pageId' => Yii::t('CmsModule.core', 'Page'),
			'name' => Yii::t('CmsModule.core', 'Name'),
			'extension' => Yii::t('CmsModule.core', 'Extension'),
			'filename' => Yii::t('CmsModule.core', 'Filename'),
			'mimeType' => Yii::t('CmsModule.core', 'Mime Type'),
			'byteSize' => Yii::t('CmsModule.core', 'Size (bytes)'),
			'file' => Yii::t('CmsModule.core', 'File'),
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		$criteria = new CDbCriteria;

		$criteria->compare('id', $this->id);
		$criteria->compare('created', $this->created);
		$criteria->compare('pageId', $this->pageId);
		$criteria->compare('name', $this->name, true);
		$criteria->compare('extension', $this->extension);
		$criteria->compare('filename', $this->filename, true);
		$criteria->compare('mimeType', $this->mimeType, true);
		$criteria->compare('byteSize', $this->byteSize);

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
		));
	}

	/**
	 * Parses the information from an uploaded file.
	 * @param CUploadedFile $file the file
	 */
	public function parseFile($file)
	{
		if ($file instanceof CUploadedFile)
		{
			$this->extension = strtolower($file->getExtensionName());
			$this->filename = $file->getName();
			$this->mimeType = $file->getType();
			$this->byteSize = $file->getSize();
			$this->file = $file;
		}
	}

	/**
	 * Saves the current record.
	 * @param boolean $runValidation whether to perform validation before saving the record
	 * @param array $attributes list of attributes that need to be saved
	 * @return boolean whether the saving succeeds
	 */
	public function save($runValidation = true, $attributes = null)
	{
		if (empty($this->name))
			$this->name = substr($this->filename, 0, strrpos($this->filename, '.'));

		if (parent::save($runValidation, $attributes))
			return $this->saveFile();
		else
			return false;
	}

	/**
	 * Saves a file for this attachment.
	 * @return boolean boolean whether the saving succeeds
	 */
	public function saveFile()
	{
		if ($this->file instanceof CUploadedFile)
		{
			$path = $this->getAttachmentPath();

			if (!file_exists($path))
				mkdir($path, 0777, true);

			return $this->file->saveAs($path.$this->resolveName());
		}
		else
			return false;
	}

	/**
	 * Returns the URL to this attachment.
	 * @return string the URL
	 */
	public function getUrl()
	{
		return Yii::app()->request->baseUrl.Yii::app()->cms->attachmentPath.$this->resolveName();
	}

	/**
	 * Returns the tag for this attachment.
	 * @return string the tag
	 */
	public function renderTag()
	{
		return '{{file:'.$this->id.'}}';
	}

	/**
	 * Returns the filename for this attachment.
	 * @return string the filename
	 */
	public function resolveName()
	{
		return $this->name.'.'.strtolower($this->extension);
	}

	/**
	 * Returns the attachment path.
	 * @return string the path
	 */
	protected function getAttachmentPath()
	{
		return Yii::app()->basePath.'/../'.Yii::app()->cms->attachmentPath;
	}
}
