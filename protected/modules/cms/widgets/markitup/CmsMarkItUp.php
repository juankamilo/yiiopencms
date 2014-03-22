<?php
/**
 * MarkItUp class file.
 * @author Christoffer Niska <ChristofferNiska@gmail.com>
 * @copyright Copyright &copy; Christoffer Niska 2011-
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 */

class CmsMarkItUp extends CInputWidget
{
	/**
	 * @var string the markitup set.
	 */
	public $set = 'default';
	/**
	 * @var string the markitup skin.
	 */
	public $skin = 'simple';
	/**
	 * @var array plugin options.
	 */
	public $options = array();

	/**
	 * Initializes the widget.
	 */
	public function init()
	{
		list($name, $id) = $this->resolveNameID();

		if (isset($this->htmlOptions['id']))
			$id = $this->htmlOptions['id'];
		else
			$this->htmlOptions['id'] = $id;

		if (isset($this->htmlOptions['name']))
			$name = $this->htmlOptions['name'];

		$this->registerClientScript($id);

		if ($this->hasModel())
			echo CHtml::activeTextArea($this->model, $this->attribute, $this->htmlOptions);
		else
			echo CHtml::textArea($name, $this->value, $this->htmlOptions);
	}

	public function registerClientScript($id)
	{
		$assetPath = Yii::app()->assetManager->publish(dirname(__FILE__).'/assets', false, -1, YII_DEBUG);

		/** @var CClientScript $cs */
		$cs = Yii::app()->getClientScript();
		$cs->registerCoreScript('jquery');
		$cs->registerScriptFile($assetPath.'/markitup/jquery.markitup.js', CClientScript::POS_END);

		if (isset($this->set))
		{
			$cs->registerScriptFile($assetPath.'/markitup/sets/'.$this->set.'/set.js', CClientScript::POS_END);
			$cs->registerCssFile($assetPath.'/markitup/sets/'.$this->set.'/style.css');
		}

		$cs->registerCssFile($assetPath.'/markitup/skins/'.$this->skin.'/style.css');

		$options = CJavaScript::encode($this->options);
		$cs->registerScript(__CLASS__.'#'.$id, "
			!function($) {
				var markItUp = $('#".$id."').markItUp(mySettings || {}, {$options});
				var frame = $('<iframe class=\"markItUpPreviewFrame\">');
				frame.appendTo($('#markItUpPreview'));
				markItUp.previewWindow = frame[frame.length - 1].contentWindow || frame[frame.length - 1];
			}(jQuery);
		", CClientScript::POS_READY);
	}
}