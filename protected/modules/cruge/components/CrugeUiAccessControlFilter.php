<?php
/*	CrugeUiAccessControlFilter
		
		es una extension de CrugeAccessControlFilter (ver documentacion en esa clase base).
		
		esta clase difiere de CrugeAccessControlFilter en que aqui se le puede pasar
		la lista de actions a los cuales no se requiere una operacion para acceder a ellas.
	
		se usa excluisivamente en UiController, bajo esta configuracion:
				array('CrugeAccessControlFilter'
						,'publicActions'=>	
							array('registration','login','pwdrec'
							,'activationurl','ajaxgeneratenewpassword')
					),
		
	@author: Christian Salazar H. <christiansalazarh@gmail.com> @salazarchris74
	@license protected/modules/cruge/LICENSE	
*/
class CrugeUiAccessControlFilter extends CrugeAccessControlFilter
{

    public $publicActions;

    public function init()
    {
        parent::init();
    }

    protected function preFilter($filterChain)
    {
        // si el action es alguno de los de la lista, permite el paso y deja de procesar
        // los demas filtros
        $currentActionName = Yii::app()->getController()->action->id;
        if (in_array($currentActionName, $this->publicActions)) {
            return true;
        } else {
            parent::preFilter($filterChain);
        }
    }

    protected function postFilter($filterChain)
    {
    }
}

?>
