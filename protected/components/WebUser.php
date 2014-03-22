<?php 
class WebUser extends CWebUser
{
    /**
     * Overrides a Yii method that is used for roles in controllers (accessRules).
     *
     * @param string $operation Name of the operation required (here, a role).
     * @param mixed $params (opt) Parameters for this operation, usually the object to access.
     * @return bool Permission granted?
     */
    public function checkAccess($operation, $params=array())
    {
        if (empty($this->id)) {
            // No identificado => no permisos
            return false;
        }
        $role = $this->getState("roles");
        if ($role === 'admin') {
            return true; // el rol de admin tiene acceso a todo
        }
        // habilita el acceso a la operacion solicitada si el usuario pertenece a ese rol
        return ($operation === $role);
    }
}