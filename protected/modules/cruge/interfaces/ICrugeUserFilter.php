<?php
/** ICrugeUserFilter

interfaz usada por Yii::app()->user->save(..) para aceptar a un usuario que pretende
registrarse.

si esta interfaz retorna false, tambien debe informar el error al modelo mediante
la llamada a addError('fieldname','error descripcion');

	@author: Christian Salazar H. <christiansalazarh@gmail.com> @salazarchris74
	@license protected/modules/cruge/LICENSE
 */
interface ICrugeUserFilter
{
    public function canInsert(ICrugeStoredUser $model);

    public function canUpdate(ICrugeStoredUser $model);
}

?>
