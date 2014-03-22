<?php
/**
 * LanguageController class file.
 * @author Christoffer Niska <ChristofferNiska@gmail.com>
 * @copyright Copyright &copy; 2012, Christoffer Niska
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package cms.controllers
 * @since 2.0.0
 */

/**
 * Language controller.
 * @property CmsModule $module
 */
class LanguageController extends CmsController
{
	/**
	 * @var string default controller action.
	 */
	public $defaultAction = 'change';

	/**
	 * Changes the application language and sets it as a user state.
	 * @param string $locale the language, e.g. 'en'
	 */
	public function actionChange($locale)
	{
		if (in_array($locale, array_keys(Yii::app()->cms->languages)))
                        if (Yii::app()->user->hasState('__locale'))
                            $locale = Yii::app()->user->hasState('lang');
                        else
                            Yii::app()->user->setState('__locale', $locale);

		$this->redirect(Yii::app()->homeUrl.$locale.'/');
	}
}
