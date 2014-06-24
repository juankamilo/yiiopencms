<?php
/*	CrugeAccessControlFilter

    Concede acceso en base a verificar si el nombre del controller y el action a ser usado
    estan definidos en las operaciones creadas por el administrador de RBAC.

    como funciona:

        si este filtro es usado y un usuario quiere acceder al action: "site/contact"
        entonces:

            1. sistema arma un nombre de OPERACION asi: "action_site_contact"

            2. concede paso tras verificar si el usuario que autenticado tiene acceso con:
                Yii::app()->user->checkAccess("action_site_contact")

    como se usa:

        se declara el uso de esta clase en el metodo filters() de la clase del usuario,
        asi:
            array('CrugeAccessControlFilter'),

        en la clase del usuario asi:

            public function filters()
            {
                return array(
                    //'accessControl', // perform access control for CRUD operations
                    array('CrugeAccessControlFilter'),
                );
            }


    la excepcion reportara error 401 indicando 'Access Denied'. Estandar http.

 @author: Christian Salazar H. <christiansalazarh@gmail.com> @salazarchris74
@license protected/modules/cruge/LICENSE
*/
class CrugeAccessControlFilter extends CFilter
{

    public function init()
    {
        parent::init();
    }

    protected function preFilter($filterChain)
    {


        $controllerItemName = "controller_" . Yii::app()->getController()->id;

        $currentActionName = Yii::app()->getController()->action->id;
        $actionItemName = "action_" . Yii::app()->getController()->id . "_" . $currentActionName;

        // se dara acceso siempre a site/error ya que por defecto
        // es el punto de muestra de errores de Yii. esto es para facilitar el uso de cruge
        if ((Yii::app()->getController()->id == 'site') && (Yii::app()->getController()->action->id == 'error')) {
            return true;
        }


        // tiene permiso para la controladora indicada ?
        //
        // tiene permiso para la accion indicada ?
        //
        //if(Yii::app()->user->checkAccess($controllerItemName))
        //{
        // si tiene permiso.

        // tiene permiso para la accion indicada ?
        //
        if (Yii::app()->user->checkAccess($actionItemName)) {
            // si tiene autorizacion para la accion indicada

        } else {
            // no esta autorizado
            //
            $this->reportError($actionItemName);
        }
        /*
        }
        else{
            // no esta autorizado a la controladora en general.
            //
            $this->reportError($controllerItemName);
        }
        */

        $filterChain->run();
    }

    private function reportError($itemName)
    {
        if (CrugeUtil::config()->allowUserAlways == false) {
            throw new CrugeException($itemName, 401);
        }
    }

    protected function postFilter($filterChain)
    {
    }
}

?>
