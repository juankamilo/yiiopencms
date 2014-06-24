<?php
/** DefaultUserFilter

es un filtro que permite a un usuario registrarse o actualizarse

este filtro es configurado en el modulo principal mediante:

CrugeModule::userfilter = 'cruge.models.filters.DefaultUserFilter';


como se tiene acceso a los campos personalizados del usuario ?

usando:

$model->getFields(); la cual da un array de ICrugeField


@author: Christian Salazar H. <christiansalazarh@gmail.com> @salazarchris74
@license protected/modules/cruge/LICENSE
 */
class DefaultUserFilter implements ICrugeUserFilter
{
    public function canInsert(ICrugeStoredUser $model)
    {
        // si hay algun error, retornar false y reportar el error asi:
        // $model->addError('','algun error');
        return true;
    }

    public function canUpdate(ICrugeStoredUser $model)
    {
        // si hay algun error, retornar false y reportar el error asi:
        // $model->addError('','algun error');
        return true;
    }
}

?>
