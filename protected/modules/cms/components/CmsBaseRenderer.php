<?php
/**
 * CmsBaseRenderer class file.
 * @author Christoffer Niska <christoffer.niska@nordsoftware.com>
 * @copyright Copyright &copy; 2011, Nord Software Ltd
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package cms.components
 */

/**
 * Cms renderer base class. All renderers must be extended from this class.
 */
class CmsBaseRenderer extends CComponent
{
	protected $_patterns = array(
		'block'=>'/{{block:([\w\d\._-]+)}}/i',
		'email'=>'/{{email:([\w\d!#$%&\'*+\\/=?^_`{|}~-]+(?:\.[\w\d!#$%&\'*+\\/=?^_`{|}~-]+)*@(?:[\w\d](?:[\w\d-]*[\w\d])?\.)+[\w\d](?:[\w\d-]*[\w\d])?)}}/i',
		'file'=>'/{{file:([\d]+)}}/i',
		'image'=>'/{{image:([\d]+)}}/i',
		'link'=>'/{{(#?[\w\d\._-]+|https?:\/\/[\w\d_-]*(\.[\w\d_-]*)+.*)\|([\w\d\s-]+)}}/i',
		'menu'=>'/{{menu:([\w\d\._-]+)}}/i',
		'url'=>'/{{url:([\w\d\._-]+)}}/i',
	);

	/**
	 * Renders the given page.
	 * @param CmsPage $page the page to render
	 * @return string the rendered content
	 */
	public function renderPage($page)
	{
		$content = $page->body;
		$content = $this->renderURLs($content);
		$content = $this->renderLinks($content);
		$content = $this->renderEmails($content);
		$content = $this->renderImages($content);
		$content = $this->renderAttachments($content);
		$content = $this->renderBlocks($content);
		$content = $this->renderMenus($content);
		return $content;
	}

	/**
	 * Renders the given block.
	 * @param CmsBlock $block the block to render
	 * @return string the rendered content
	 */
	public function renderBlock($block)
	{
		$content = $block->body;
		$content = $this->renderURLs($content);
		$content = $this->renderLinks($content);
		$content = $this->renderEmails($content);
		$content = $this->renderImages($content);
		$content = $this->renderAttachments($content);
		$content = $this->removeBlocks($content); // blocks cannot be rendered inside blocks
		return $content;
	}

	/**
	 * Renders blocks within the given content.
	 * @param string $content the content being rendered
	 * @return string the content
	 */
	protected function renderBlocks($content)
	{
		$matches = array();
		preg_match_all($this->_patterns['block'], $content, $matches);

		$pairs = array();
		foreach ($matches[1] as $index => $name)
		{
			/** @var CmsBlock $block */
			$block = Yii::app()->cms->loadBlock($name);
			$pairs[$matches[0][$index]] = $block->render();
		}

		if (!empty($pairs))
			$content = strtr($content, $pairs);

		return $content;
	}

	/**
	 * Renders links within the given content.
	 * @param string $content the content being rendered
	 * @return string the content
	 */
	protected function renderLinks($content)
	{
		$matches = array();
		preg_match_all($this->_patterns['link'], $content, $matches);

		$pairs = array();
		foreach ($matches[1] as $index => $target)
		{
			// If the target doesn't include 'http' it's treated as an internal link.
			if (strpos($target, '#') !== 0 && strpos($target, 'http') === false)
			{
				/** @var Cms $cms */
				$cms = Yii::app()->cms;

				/** @var CmsPage $page */
				$page = $cms->loadPage($target);
				$target = $page instanceof CmsPage ? $page->getUrl() : '#';
			}

			$text = $matches[3][$index];
			$pairs[$matches[0][$index]] = CHtml::link($text, $target);
		}

		if (!empty($pairs))
			$content = strtr($content, $pairs);

		return $content;
	}

	/**
	 * Renders URLS within the given content.
	 * @param string $content the content being rendered
	 * @return string the content
	 */
	protected function renderURLs($content)
	{
		$matches = array();
		preg_match_all($this->_patterns['url'], $content, $matches);

		$pairs = array();
		foreach ($matches[1] as $index => $target)
		{
			// If the target doesn't include 'http' it's treated as an internal link.
			if (strpos($target, '#') !== 0 && strpos($target, 'http') === false)
			{
				/** @var Cms $cms */
				$cms = Yii::app()->cms;

				/** @var CmsPage $page */
				$page = $cms->loadPage($target);
				$target = $page instanceof CmsPage ? $page->getUrl() : '#';
			}

			$pairs[$matches[0][$index]] = $target;
		}

		if (!empty($pairs))
			$content = strtr($content, $pairs);

		return $content;
	}

	/**
	 * Renders emails within the given content.
	 * @param string $content the content being rendered
	 * @return string the content
	 */
	protected function renderEmails($content)
	{
		$matches = array();
		preg_match_all($this->_patterns['email'], $content, $matches);

		$pairs = array();
		foreach ($matches[1] as $index => $id)
		{
			$email = str_rot13($matches[1][$index]);
			$pairs[$matches[0][$index]] = CHtml::mailto($email, $email, array('class'=>'email', 'rel'=>'nofollow'));
		}

		if (!empty($pairs))
		{
			$content = strtr($content, $pairs);

			/** @var CClientScript $cs */
			$cs = Yii::app()->getClientScript();
			$cs->registerScriptFile(Yii::app()->cms->getAssetsUrl().'/js/cms-rot13.js');
			$cs->registerScript(__CLASS__.'#'.uniqid(true, true).'_emailObfuscation', "
				!function($) {
					$('.email').each(function() {
						var element = $(this);

						if (!element.attr('data-decoded')) {
							var	href = $(this).attr('href'),
								address = Cms.Rot13.decode(href.substring(href.indexOf(':') + 1)),
								value = Cms.Rot13.decode($(this).text());

							element.attr('href', 'mailto:' + address);
							element.text(value);
							element.attr('data-decoded', 1);
						}
					});
				}(jQuery);
			");
		}

		return $content;
	}

	/**
	 * Renders images within the given content.
	 * @param string $content the content being rendered
	 * @return string the content
	 */
	protected function renderImages($content)
	{
		$matches = array();
		preg_match_all($this->_patterns['image'], $content, $matches);

		$pairs = array();
		foreach ($matches[1] as $index => $id)
		{ 
			/** @var Image $image*/ 
			$image = Yii::app()->image->load($id);
			if ($image instanceof Image)
			{
				//$url = $image->getUrl($matches[0][$index]);
                                $url =  Yii::app()->request->baseUrl.'/files/images/originals/'.$image->path.'/'.$image->name.'-'.$image->id.'.'.$image->extension;
                                if ($url !== false)
					$pairs[$matches[0][$index]] = CHtml::image($url, $image->name);
			}
		}

		if (!empty($pairs) )
			$content = strtr($content, $pairs);

		return $content;
	}

	/**
	 * Renders attachments within the given content.
	 * @param string $content the content being rendered
	 * @return string the content
	 */
	protected function renderAttachments($content)
	{
		$matches = array();
		preg_match_all($this->_patterns['file'], $content, $matches);

		$pairs = array();
		foreach ($matches[1] as $index => $id)
		{
			/** @var CmsAttachment $attachment */
			$attachment = CmsAttachment::model()->findByPk($id);
			if ($attachment instanceof CmsAttachment)
			{
				$url = $attachment->getUrl();
				$name = $attachment->resolveName();
				$pairs[$matches[0][$index]] = CHtml::link($name, $url);
			}
		}

		if (!empty($pairs))
			$content = strtr($content, $pairs);

		return $content;
	}

	/**
	 * Removes the block tags within the given content.
	 * @param string $content the content being rendered
	 * @return string the content
	 */
	public function removeBlocks($content)
	{
		return preg_replace($this->_patterns['block'], '', $content);
	}

	/**
	 * Renders menus within the given content.
	 * @param string $content the content being rendered
	 * @return string the content
	 */
	protected function renderMenus($content)
	{
		$matches = array();
		preg_match_all($this->_patterns['menu'], $content, $matches);

		$pairs = array();
		foreach ($matches[1] as $index => $name)
		{
			/** @var CmsMenu $menu */
			$menu = Yii::app()->cms->loadMenu($name);
			$pairs[$matches[0][$index]] = $menu->render();
		}

		if (!empty($pairs))
			$content = strtr($content, $pairs);

		return $content;
	}
}
