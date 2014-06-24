<?php
/**
 * CmsPage class file.
 * @author Christoffer Niska <christoffer.niska@nordsoftware.com>
 * @copyright Copyright &copy; 2012, Nord Software Ltd
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package cms.models
 * @since 2.0.0
 */

/**
 * This is the model class for table "cms_page".
 *
 * The following properties are available in this model:
 * @property integer $id
 * @property string $created
 * @property string $updated
 * @property integer $parentId
 * @property string $name
 * @property string $type
 * @property boolean $published
 * @property boolean $deleted
 *
 * The following relations are available for this model:
 * @property CmsPage $parent the parent node
 * @property CmsPage[] $children the child pages
 * @property CmsPageContent $content the content model for the current language
 * @property CmsPageContent $default the content model for the default language
 * @property CmsPageContent[] $translations the related content models
 * @property Image[] $images the associated images
 *
 * @property array $breadcrumbs the breadcrumbs
 * @property string $urlAlias the URL alias
 * @property string $pageTitle the page title
 * @property string $breadcrumb the breadcrumb text
 * @property string $heading the heading text
 * @property string $body the body content
 */
class CmsPage extends CmsNode
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className the class name
	 * @return CmsPage the static model class
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
		return 'cms_page';
	}

	public function behaviors()
	{
		return array(
			'image' => array('class'=>'image.components.ImageBehavior'),
		);
	}

	/**
	 * @return array validation rules for model attributes
	 */
	public function rules()
	{
		return array(
			array('name', 'required'),
			array('parentId, published, deleted', 'numerical', 'integerOnly'=>true),
			array('name, type', 'length', 'max'=>255),
			array('name', 'unique'),
			array('created, updated', 'safe'),
			array('id, created, updated, parentId, name, deleted', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules
	 */
	public function relations()
	{
		return array(
			'parent'=>array(self::BELONGS_TO, 'CmsPage', 'parentId'),
			'children'=>array(self::HAS_MANY, 'CmsPage', 'parentId'),
			'translations'=>array(self::HAS_MANY, 'CmsPageContent', 'pageId'),
			'content'=>array(
				self::HAS_ONE, 'CmsPageContent', 'pageId',
				'condition'=>'locale=:locale',
				'params'=>array(':locale'=>Yii::app()->language),
			),
                        'contents'=>array(
				self::HAS_MANY, 'CmsPageContent', 'pageId',
			),
			'default'=>array(
				self::HAS_ONE, 'CmsPageContent', 'pageId',
				'condition'=>'locale=:locale',
				'params'=>array(':locale'=>Yii::app()->cms->defaultLocale),
			),
			'images'=>array(
				self::HAS_MANY, 'Image', 'ownerId',
				'condition'=>'owner=:owner',
				'params'=>array(':owner'=>get_class($this)),
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
			'parentId' => Yii::t('CmsModule.core', 'Parent'),
			'type' => Yii::t('CmsModule.core', 'Type'),
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
		$criteria->compare('created', $this->created, true);
		$criteria->compare('updated', $this->updated, true);
		$criteria->compare('name', $this->name, true);
		$criteria->compare('parentId', $this->updated);
		$criteria->compare('type', $this->type);
		$criteria->compare('published', $this->published);

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
		));
	}

	/**
	 * Creates content for this node.
	 * @param string $locale the locale id, e.g. 'en'
	 * @return CmsPageContent the content model
	 */
	public function createTranslation($locale)
	{
		$content = new CmsPageContent();
		$content->pageId = $this->id;
		$content->locale = $locale;
		$content->save();
		return $content;
	}

	/**
	 * Returns the associated content in a specific language.
	 * @param string $locale the locale id, e.g. 'en'
	 * @return CmsPageContent the content model
	 */
	public function getTranslation($locale)
	{
		return CmsPageContent::model()->findByAttributes(array(
			'pageId' => $this->id,
			'locale' => $locale,
		));
	}

	/**
	 * Returns the images associated with the page.
	 * @return CActiveDataProvider the images
	 */
	public function getImages()
	{
		return new CActiveDataProvider('Image', array(
			'criteria' => array(
				'condition' => 'owner=:owner AND ownerId=:ownerId',
				'params' => array(':owner' => get_class($this), ':ownerId' => $this->id),
			),
		));
	}

	/**
	 * Returns the attachments associated with the page.
	 * @return CActiveDataProvider the attachments
	 */
	public function getAttachments()
	{
		return new CActiveDataProvider('CmsAttachment', array(
			'criteria' => array(
				'condition' => 'pageId=:pageId',
				'params' => array(':pageId' => $this->id),
			),
		));
	}

	/**
	 * Returns the breadcrumb text for this node.
	 * @param boolean $link indicates whether to return the breadcrumb as a link
	 * @return string the breadcrumb text
	 */
	public function getBreadcrumbs($link = false)
	{
		$breadcrumbs = array();

		if ($this->parent !== null)
			$breadcrumbs = $this->parent->getBreadcrumbs(true); // get the parent as a link

		if ($link)
			$breadcrumbs[$this->breadcrumb] = $this->getUrl();
		else
			$breadcrumbs[] = $this->breadcrumb;

		return $breadcrumbs;
	}

	/**
	 * Returns the SEO optimized name of this page.
	 * @return string the name
	 */
	public function getUrlAlias()
	{
		return $this->getTranslatedAttribute('url', ucfirst($this->name));
	}

	/**
	 * Returns the page title for this node.
	 * @return string the page title
	 */
	public function getPageTitle()
	{
		return $this->getTranslatedAttribute('pageTitle', ucfirst($this->name));
	}

	/**
	 * Returns the breadcrumb text for the page.
	 * @return string the text
	 */
	public function getBreadcrumb()
	{
		return $this->getTranslatedAttribute('breadcrumb', ucfirst($this->name));
	}

        /**
         * Returns the heading for this node.
         * @return string the heading
         */
        public function getHeading()
        {
                return $this->getTranslatedAttribute('heading', ucfirst($this->name));
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
	 * Returns the URL for this node.
	 * @param array $params additional GET parameters (name=>value)
	 * @return string the URL
	 */
	public function getUrl($params = array())
	{
            if($this->getParentName() == '')
            	return Yii::app()->createUrl('cms/page/view',CMap::mergeArray($params, array('id'=>$this->id, 'name'=>$this->urlAlias)));
            else
            	return Yii::app()->createUrl('cms/page/view',CMap::mergeArray($params, array('id'=>$this->id, 'name'=>$this->urlAlias,'parent'=>$this->getParentName())));
	}

	/**
	 * Returns the absolute URL for this model.
	 * @param array $params additional GET parameters (name=>value)
	 * @return string the URL
	 */
	public function getAbsoluteUrl($params = array())
	{
		return Yii::app()->createAbsoluteUrl('cms/page/view',
				CMap::mergeArray($params, array('id'=>$this->id, 'name'=>$this->urlAlias, 'parent'=>$this->getParentName())));
	}

	/**
	 * Renders the page content.
	 * @return string the rendered content
	 */
	public function render()
	{
		return Yii::app()->cms->renderer->renderPage($this);
	}

	/**
	 * Returns the parent select options formatted as a tree.
	 * @return array the options
	 */
	public function getParentOptionTree()
	{
		$pages = CmsPage::model()->findAll();

		if (!$this->isNewRecord)
		{
			$children = $this->getChildren($pages, true);
			$exclude = CMap::mergeArray(array($this->id), array_keys($children));
			$pages = CmsPage::model()->findAll('id NOT IN (:exclude)', array(':exclude'=>implode(',', $exclude)));
		}

		$tree = $this->getTree($pages);

		$options = array('0' => Yii::t('CmsModule.core', 'No parent'));
		foreach ($tree as $branch)
			$options = CMap::mergeArray($options, $this->getParentOptionBranch($branch));

		return $options;
	}

	/**
	 * Returns a single branch of the parent select option tree.
	 * @param array $branch the branch
	 * @param int $depth the depth of the branch
	 * @return array the options
	 */
	protected function getParentOptionBranch($branch, $depth = 0)
	{
		$options = array();

		$options[$branch['model']->id] = str_repeat('...', $depth + 1).' '.$branch['model']->name;

		if (!empty($branch['children']))
			foreach ($branch['children'] as $leaf)
				$options = CMap::mergeArray($options, $this->getParentOptionBranch($leaf, $depth + 1));

		return $options;
	}

	/**
	 * Returns the given pages as a tree.
	 * @param CmsPage[] $pages the pages to process
	 * @param bool $includeOrphans indicated whether to include nodes which parent has been deleted.
	 * @return array the tree
	 */
	public function getTree($pages, $includeOrphans = false)
	{
		$tree = $this->getBranch($pages);

		// Get the orphan nodes as well (i.e. those which parent has been deleted) if necessary.
		if ($includeOrphans)
			foreach($pages as $page)
				$tree[$page->id] = array('model'=>$page, 'children'=>$this->getBranch($pages, $page->id));

		return $tree;
	}

	/**
	 * Returns the given pages as a branch.
	 * @param CmsPage[] $pages the pages to process
	 * @param int $parentId the parent id
	 * @return array the branch
	 */
	protected function getBranch(&$pages, $parentId = 0)
	{
		$children = array();

		foreach ($pages as $idx => $page)
		{
			if ((int) $page->parentId === (int) $parentId)
			{
				$children[$page->id] = array('model'=>$page, 'children'=>$this->getBranch($pages, $page->id));
				unset($pages[$idx]);
			}
		}

		return $children;
	}

	/**
	 * Returns the children for this page.
	 * @param CmsPage[] $pages the pages to process
	 * @param bool $recursive indicates whether to include grandchildren
	 * @return CmsPage[] the children
	 */
	protected function getChildren(&$pages, $recursive = false)
	{
		$children = array();

		foreach ($pages as $idx => $node)
		{
			if ((int) $node->parentId === (int) $this->id)
			{
				$children[$node->id] = $node;
				unset($pages[$idx]);

				if ($recursive)
					$children = CMap::mergeArray($children, $node->getChildren($pages, $recursive));
			}
		}

		return $children;
	}
        
        /**
	 * @return parent urlAlias.
	 */
	public function getParentName()
	{
		if(isset($this->parent->urlAlias))
                    return $this->parent->urlAlias;
		
	}

	/**
	 * Renders the page tree.
	 */
	public function renderTree()
	{
		$pages = CmsPage::model()->findAll();
		$tree = $this->getTree($pages, true);

		echo CHtml::openTag('div', array('class'=>'page-tree'));
		echo CHtml::openTag('ul', array('class'=>'root'));

		foreach ($tree as $branch)
			$this->renderBranch($branch);

		echo '</ul>';
		echo '</div>';
	}
	

	/**
	 * Renders a single branch in the page tree.
	 * @param array $branch the branch
	 */
	protected function renderBranch($branch)
	{
		echo '<li>';
		echo CHtml::link($branch['model']->name, array('node/update','id'=>$branch['model']->id));

		if (!empty($branch['children']))
		{
			echo CHtml::openTag('ul', array('class'=>'branch'));

			foreach ($branch['children'] as $leaf)
				$this->renderBranch($leaf);

			echo '</ul>';
		}

		echo '</li>';
	}

	/**
	 * Returns the type select options.
	 * @return array the options
	 */
	public function getTypeOptions()
	{
		return CMap::mergeArray(array(''=>Yii::t('CmsModule.core','None')), Yii::app()->controller->module->pageTypes);
	}

	/**
	 * Returns whether this page has associated content.
	 * @return boolean the result
	 */
	public function hasContent()
	{
		return $this->content instanceof CmsContent;
	}
}
