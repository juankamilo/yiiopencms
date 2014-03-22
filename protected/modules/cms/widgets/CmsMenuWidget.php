<?php
/**
 * CmsMenuWidget class file.
 * @author Christoffer Niska <christoffer.niska@nordsoftware.com>
 * @copyright Copyright &copy; 2012, Nord Software Ltd
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package cms.widgets
 * @since 2.0.0
 */

Yii::import('bootstrap.widgets.TbMenu');

/**
 * Widget that renders a cms menu.
 */
class CmsMenuWidget extends TbMenu
{
	/**
	 * @var string menu system name.
	 */
	public $name;

	/**
	 * Initializes the widget.
	 */
	public function init()
	{
		if (!isset($this->type))
			$this->type = TbMenu::TYPE_LIST;

		/** @var CmsMenu $model */
		$model = Yii::app()->cms->loadMenu($this->name);
		$this->items = CMap::mergeArray($model->createItems(), $this->items);

		parent::init();
	}
}
