<?php
/** Filtro por defecto para controlar el otorgamiento de sesiones

@author: Christian Salazar H. <christiansalazarh@gmail.com> @salazarchris74
@license protected/modules/cruge/LICENSE
 */
class DefaultSessionFilter implements ICrugeSessionFilter
{

    public $lastErrorDescr = "";

    /**
    @returns string, descripcion del error.
     */
    public function getLastErrorDescr()
    {
        return $this->lastErrorDescr;
    }

    /**
    @erturns string, nombre corto de este filtro.
     */
    public function getName()
    {
        return "default";
    }

    /*  invocado por CrugeWebUser cuando un usuario solicita login()

        @returns ICrugeSession instancia.  nueva o reutilizada. o null si ocurrio un error o no
        se debe dar acceso.
    */
    public function startSession(/*ICrugeStoredUser*/ $user, /*ICrugeSystem*/ $sys)
    {
        $this->lastErrorDescr = "";

        $model = null;

        // primera regla. no admite sesiones de ningun tipo, causando la negacion a todos
        // los usuarios DEL SISTEMA SELECCIONADO.
        if ($sys->getn('systemdown') == 1) {
            $this->lastErrorDescr = "el sistema se encuentra detenido temporalmente";
            return null;
        }

        // no puede iniciar sesion si su cuenta no esta activada o si esta suspendida
        // los estados estan definidos en CrugeUserManager::getUserStateOptions
        if ($user->state == CRUGEUSERSTATE_NOTACTIVATED) {
            $this->lastErrorDescr = "su cuenta necesita ser activada, revise su correo y haga click en el vinculo de activacion que se le envio";
            return null;
        }
        if ($user->state == CRUGEUSERSTATE_SUSPENDED) {
            $this->lastErrorDescr = "su cuenta se encuentra suspendida";
            return null;
        }

        // busca una sesion abierta para este usuario, para reutilizarla
        //
        $model = Yii::app()->user->um->findSession($user);

        if ($model == null) {
            // no encontro un sesion reutilizable, procede a crear una nueva si el sistema
            // se lo permite

            if ($sys->getn('systemnonewsessions') == 1) {
                $this->lastErrorDescr =
                    "el sistema esta inhabilitado para otorgar una nueva sesion";
                return null;
            }
            // procede a crear la sesion para el usuario
            $model = Yii::app()->user->um->createSession($user, $sys);
        } else {
            if ($model->isSessionExpired()) {
                self::onSessionExpired($model);
                return null;
            } else {
                // TODO: evento para la reutilizacion de una sesion
                //
            }
            Yii::log("DefaultSessionFilter. Reutilizando sesion: " . $model->getPrimaryKey(), "info");
            $model->onReusage();
        }


        return $model;
    }

    /*
        evento lanzado por CrugeWebUser cuando detecta que una sesion ha expirado



    */
    public function onSessionExpired(/*ICrugeSession*/ $model)
    {
        Yii::log(__CLASS__ . ".onSessionExpired", "info");
        $this->lastErrorDescr = "su sesion ha expirado. debe iniciar sesion nuevamente";
		if($model != null){
       	 	$model->expiresession();
        	self::onStore($model);
		}
        if (!empty(CrugeUtil::config()->afterSessionExpiredUrl)) {
			// este flag cruge_redirect_count es puesto a cero tras login
			// en: CrugeWebUser::login(). se usa para evitar un bucle de 
			// redireccion infinita.
			if(!isset($_SESSION['cruge_redirect_count']))
				$_SESSION['cruge_redirect_count']=0;
			if($_SESSION['cruge_redirect_count']==0){
				$_SESSION['cruge_redirect_count']++;
				Yii::app()->getController()->redirect(CHtml::normalizeUrl(
					CrugeUtil::config()->afterSessionExpiredUrl));
			}
		}
    }

    /*
        implementa el almacen de la sesion creada por getSession

        @returns boolean true para indicar que se continue la autenticacion, false aborta
    */
    public function onStore(/*ICrugeSession*/ $model)
    {
		if($model != null)
       	 return $model->store();
		return true;
    }

    /*
        evento lanzado por CrugeWebUser al momento de iniciar sesion
    */
    public function onLogin(/*ICrugeSession*/ $model)
    {
        Yii::log(__CLASS__ . ".onLogin", "info");
		if($model != null){
        $user = Yii::app()->user->um->getUserFromSession($model);
        Yii::app()->user->um->recordLogon($user);
        Yii::app()->user->um->save($user);
        if (!empty(CrugeUtil::config()->afterLoginUrl)) {
            Yii::app()->user->returnUrl = CrugeUtil::config()->afterLoginUrl;
        }
		}
    }

    /*
        evento lanzado por CrugeWebUser al momento de cerrar sesion mediante
        una llamada a Yii::app()->user->logout
    */
    public function onLogout(/*ICrugeSession*/ $model)
    {
        Yii::log(__CLASS__ . ".onLogout", "info");
        if (!empty(CrugeUtil::config()->afterLogoutUrl)) {
            Yii::app()->getController()->redirect(
				CHtml::normalizeUrl(CrugeUtil::config()->afterLogoutUrl));
        }
    }


}
