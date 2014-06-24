<?php
/**
 * Cms class file.
 * @author Christoffer Niska <christoffer.niska@nordsoftware.com>
 * @copyright Copyright &copy; 2011, Nord Software Ltd
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package cms.components
 */

Yii::import('cms.models.*');

/**
 * Application component that allows for application-wide access to the cms.
 */
class Cms extends CApplicationComponent
{
	/**
	 * @var array languages that content can be translated in (locale=>language).
	 */
	public $languages = array('en'=>'English');
	/**
	 * @var string default locale (defaults to 'en').
	 */
	public $defaultLocale = 'en';
	/**
	 * @var string allowed files types.
	 */
	public $allowedFileTypes = 'txt, doc, docx, xls, xlsx, ppt, pptx, pdf, jpg, gif, png';
        /**
         * @var integer maximum allowed file size for attachments (in bytes).
         */
        public $allowedFileSize = 10240000;
	/**
	 * @var string path for saving attached files.
	 */
	public $attachmentPath = 'files/cms/attachments/';
	/**
	 * @var string[] the names of the users that are allowed to updated the cms (defaults to 'admin').
	 */
	public $users = array('admin','david');
	/**
	 * @var boolean indicates whether to auto create nodes when they are requested (defaults to true).
	 */
	public $autoCreate = true;
	/**
	 * @var array the HTML purifier options.
	 */
	public $htmlPurifierOptions = array();
	/**
	 * @var array renderer configuration.
	 */
	public $renderer = array('class'=>'cms.components.CmsBaseRenderer');

        private $_assetsUrl;

        /**
         * Initializes the component.
         * @throws CException if a dependency is missing.
         */
        public function init()
        {
            parent::init();

                if (!Yii::app()->getComponent('image') instanceof ImageManager)
                        throw new CException(__CLASS__.': Failed to initialize component! Image extension not found.');

                // Create the renderer.
                $this->renderer = Yii::createComponent($this->renderer);

                    // Register assets.
            Yii::app()->clientScript->registerCssFile($this->getAssetsUrl().'/css/cms.css');
            Yii::app()->clientScript->registerScriptFile($this->getAssetsUrl().'/js/es5-shim.js');
        }

	/**
	 * Creates a page URL.
	 * @param string $name the content name
	 * @param array $params additional parameters
	 * @return string the URL
	 * @throws CException if the URL cannot be created
	 */
	public function createUrl($name, $params=array())
	{
		$page = $this->loadPage($name);

		if ($page === null)
			throw new CException(__CLASS__.': Failed to create URL. Page could not be created.');

		return $page->getUrl($params);
	}
        /**
	 * Renders headers.
	 * @param string $name the page name
	 */
	public function pageHeader($name = 'index')
	{
		$page = CmsPage::model()->findByAttributes(array('name'=>$name));
                /** @var CClientScript $cs */
                if(isset($page->content->metaTitle)){
                $cs = Yii::app()->getClientScript();
                //$cs->registerMetaTag($page->content->metaTitle, 'title');
                $cs->registerMetaTag($page->content->metaDescription, 'description');
                $cs->registerMetaTag($page->content->metaKeywords, 'keywords');
                foreach ($page->contents as $v => $k){
                    $cs->registerLinkTag('alternate',  null, 
                            Yii::app()->createAbsoluteUrl('//site/'.$name, array('lang'=>$k->locale))
                            , null,array('hreflang'=>$k->locale));
                }
                
                
                $cs->registerLinkTag('canonical', null, Yii::app()->createAbsoluteUrl('//site/index', array('lang'=>Yii::app()->language))); 
		// Ensure that we only render published blocks.
                return $page->content->metaTitle;
                }
	}

	/**
	 * Renders the block with the given name.
	 * @param string $name the block name
	 */
	public function block($name)
	{
		Yii::app()->controller->widget('cms.widgets.CmsBlockWidget', array('name'=>$name));
	}

	/**
	 * Renders the menu with the given name.
	 * @param string $name the menu name
	 */
	public function menu($name)
	{
		Yii::app()->controller->widget('cms.widgets.CmsMenuWidget', array('name'=>$name));
	}

	/**
	 * Loads the page model with the given name.
	 * @param string $name the page name
	 * @return CmsPage the model
	 * @throws CException if the page could not be loaded
	 */
	public function loadPage($name)
	{
		$page = CmsPage::model()->findByAttributes(array('name'=>$name));

		if ($page === null)
		{
			if (!$this->autoCreate)
				throw new CException(__CLASS__.': Failed to load page. Page "'.$name.'" not found. (auto create disabled)');

			$page = $this->createPage($name);
		}

		return $page;
	}

	/**
	 * Creates a new page model.
	 * @param string $name the page name
	 * @return CmsPage the model
	 * @throws CException if the page could not be created
	 */
	protected function createPage($name)
	{
		// Validate the system name before creation.
		if (preg_match('/^[\w\d\._-]+$/i', $name) === 0)
			throw new CException(__CLASS__.': Failed to create page. Name "'.$name.'" is invalid.');

		$page = new CmsPage();
		$page->name = $name;
		$page->save(false);
		return $page;
	}

	/**
	 * Loads the block model with the given name.
	 * @param string $name the block name
	 * @return CmsBlock the model
	 * @throws CException if the block could not be loaded
	 */
	public function loadBlock($name)
	{
		$block = CmsBlock::model()->findByAttributes(array('name'=>$name));

		if ($block === null)
		{
			if (!$this->autoCreate)
				throw new CException(__CLASS__.': Failed to load block. Block "'.$name.'" not found. (auto create disabled)');

			$block = $this->createBlock($name);
		}

		return $block;
	}

	/**
	 * Creates a new block model.
	 * @param string $name the block name
	 * @return CmsBlock the model
	 * @throws CException if the block could not be created
	 */
	protected function createBlock($name)
	{
		// Validate the system name before creation.
		if (preg_match('/^[\w\d\._-]+$/i', $name) === 0)
			throw new CException(__CLASS__.': Failed to create block. Name "'.$name.'" is invalid.');

		$block = new CmsBlock();
		$block->name = $name;
		$block->save(false);
		return $block;
	}

	/**
	 * Loads the menu model with the given name.
	 * @param string $name the page name
	 * @return CmsPage the model
	 * @throws CException if the page could not be loaded
	 */
	public function loadMenu($name)
	{
		$menu = CmsMenu::model()->findByAttributes(array('name'=>$name));

		if ($menu === null)
		{
			if (!$this->autoCreate)
				throw new CException(__CLASS__.': Failed to load menu. Menu "'.$name.'" not found. (auto create disabled)');

			$menu = $this->createMenu($name);
		}

		return $menu;
	}

	/**
	 * Creates a new menu model.
	 * @param string $name the block name
	 * @return CmsMenu the model
	 * @throws CException if the block could not be created
	 */
	protected function createMenu($name)
	{
		// Validate the system name before creation.
		if (preg_match('/^[\w\d\._-]+$/i', $name) === 0)
			throw new CException(__CLASS__.': Failed to create menu. Name "'.$name.'" is invalid.');

		$menu = new CmsMenu();
		$menu->name = $name;
		$menu->save(false);
		return $menu;
	}

	/**
	 * Returns whether a specific page is active.
	 * @param string $name the content name
	 * @return boolean the result
	 */
	public function isActive($name)
	{
		$page = $this->loadPage($name);
		$controller = Yii::app()->getController();
		return ($controller->module !== null
				&& $controller->module->id === 'cms'
				&& $controller->id === 'page'
				&& $controller->action->id === 'view'
				&& isset($_GET['id']) && $_GET['id'] === $page->id)
				|| $this->isChildActive($page);
	}

	/**
	 * Returns whether a child node of a specific page is active.
	 * @param CmsPage $node the node
	 * @return boolean the result
	 */
	protected function isChildActive($node)
	{
		foreach ($node->children as $child)
			if ($this->isActive($child->name) || $this->isChildActive($child))
				return true;

		return false;
	}
	
	/**
	 * Returns whether the currently logged in user has access to update cms content.
	 * Override this method to implement your own access control.
	 * @return boolean
	 */
	public function checkAccess()
	{
		if(Yii::app()->user->checkAccess('cms'))
                    return true;
                else
                    return false;
                
	}

	/**
        * Returns the url to assets publishing the folder if necessary.
        * @return string the assets url
        */
        protected function getAssetsUrl()
        {
            if ($this->_assetsUrl !== null)
                return $this->_assetsUrl;
            else
            {
                $assetsPath = Yii::getPathOfAlias('cms.assets');
                            $assetsUrl = Yii::app()->assetManager->publish($assetsPath, false, -1, YII_DEBUG);
                return $this->_assetsUrl = $assetsUrl;
            }
        }
        /**
	 * Method that handles the on missing translation event. If no messagesource is found
	 * then add it to the DB for its future edition from the user's control panel -that is up to you! :).
	 * @param CMissingTranslationEvent $event
	 * @return string the message to translate
	 */
	public static function missingTranslation($event)
	{

		$attributes = array('category' => $event->category, 'message' => $event->message);
		if (($model = CmsSourceMessage::model()->find('message=:message AND category=:category', $attributes)) === null)
		{
			$model = new CmsSourceMessage();
			$model->attributes = $attributes;
                        
			if ($model->save())
				return $event;
		}
		
		
	}
}
