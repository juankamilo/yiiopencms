<?php
/**
 * CmsBlockWidget class file.
 * @author Christoffer Niska <christoffer.niska@nordsoftware.com>
 * @copyright Copyright &copy; 2012, Nord Software Ltd
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package cms.widgets
 * @since 0.9.0
 */

/**
 * Widget that renders the block with the given name.
 */
class CmsBlockWidget extends CWidget
{
	/**
	 * @var string block system name.
	 */
	public $name;

	/**
	 * Runs the widget.
	 */
	public function run()
	{
		/** @var CmsBlock $model */
		$model = Yii::app()->cms->loadBlock($this->name);

		// Ensure that we only render published blocks.
		if ($model->published)
			$this->render('block', array('model'=>$model, 'content'=>$model->render()));
	}
}
