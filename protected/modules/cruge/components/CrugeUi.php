<?php
/** CrugeUi


provee herramientas para la interfaz de usuario:

1. Url (en forma de array, relativas al modulo:  /cruge/ui/xxxx)
2. Links
3. array para menues (para usar en los portlets basicamente)
4. rutas a recursos (getResource)
5. dar formato (a fechas por ejemplo, con formatDate)

dependencias:

1. CrugeUtil
2. CrugeTranslator

se accede exclusivamente mediante:

Yii::app()->user->ui


los links principales son:

<?php echo Yii::app()->user->ui->loginLink; ?>
<?php echo Yii::app()->user->ui->logoutLink; ?>
<?php echo Yii::app()->user->ui->passwordRecoveryLink; ?>
<?php echo Yii::app()->user->ui->userManagementAdminLink; ?>

@author: Christian Salazar H. <christiansalazarh@gmail.com> @salazarchris74
@license protected/modules/cruge/LICENSE
 */
class CrugeUi extends CComponent
{

    public function getResource($filename = "")
    {
        return Yii::app()->getController()->basePath . "/" . $filename;
    }

    public function formatDate($longdatevalue)
    {
        if ($longdatevalue <= 0) {
            return "";
        }
        return Yii::app()->format->formatDateTime($longdatevalue);
    }

    public function getLoginUrl()
    {
        return CrugeUtil::uiaction('login');
    }

    public function getLogoutUrl()
    {
        return CrugeUtil::uiaction('logout');
    }

    public function getPasswordRecoveryUrl()
    {
        return CrugeUtil::uiaction('pwdrec');
    }

    public function getUserManagementAdminUrl()
    {
        return CrugeUtil::uiaction('usermanagementadmin');
    }

    public function getUserManagementCreateUrl()
    {
        return CrugeUtil::uiaction('usermanagementcreate');
    }

    public function getEditProfileUrl()
    {
        return CrugeUtil::uiaction('editprofile');
    }

    public function getUserManagementDeleteUrl()
    {
        return CrugeUtil::uiaction('usermanagementdelete');
    }

    public function getRegistrationUrl()
    {
        return CrugeUtil::uiaction('registration');
    }

    public function getFieldsAdminListUrl()
    {
        return CrugeUtil::uiaction('fieldsadminlist');
    }

    public function getFieldsAdminCreateUrl()
    {
        return CrugeUtil::uiaction('fieldsadmincreate');
    }

    public function getFieldsAdminUpdateUrl()
    {
        return CrugeUtil::uiaction('fieldsadminupdate');
    }

    public function getRbacListRolesUrl()
    {
        return CrugeUtil::uiaction('rbaclistroles');
    }

    public function getRbacListTasksUrl()
    {
        return CrugeUtil::uiaction('rbaclisttasks');
    }

    public function getRbacListOpsUrl()
    {
        return CrugeUtil::uiaction('rbaclistops');
    }

    // argumento extra es usado en CrugeAuthManager para crear
    // tareas enlazadas a otras en forma de submenu, extra==authitem_parent.
    public function getRbacAuthItemCreateUrl($type, $extra = '')
    {
        // aqui type es uno de los valores de
        // CAuthItem::TYPE_ROLE,CAuthItem::TYPE_TASK,CAuthItem::TYPE_OPERATION
        return CrugeUtil::uiaction(
            'rbacauthitemcreate',
            array('type' => $type, 'extra' => $extra)
        );
    }

    public function getRbacAuthItemDeleteUrl($id)
    {
        return CrugeUtil::uiaction('rbacauthitemdelete', array('id' => $id));
    }

    public function getRbacAuthItemUpdateUrl($id)
    {
        return CrugeUtil::uiaction('rbacauthitemupdate', array('id' => $id));
    }

    public function getRbacAuthItemChildItemsUrl($id = null)
    {
        if ($id == null) {
            return CrugeUtil::uiaction('rbacauthitemchilditems');
        }
        return CrugeUtil::uiaction('rbacauthitemchilditems', array('id' => $id));
    }

    public function getRbacUsersAssignmentsUrl()
    {
        return CrugeUtil::uiaction('rbacusersassignments');
    }

    public function getRbacAjaxSetChildItemUrl()
    {
        return CrugeUtil::uiaction('rbacajaxsetchilditem');
    }

    public function getRbacAjaxAssignmentUrl()
    {
        return CrugeUtil::uiaction('rbacajaxassignment');
    }

    public function getRbacAjaxGetAssignmentBzUrl()
    {
        return CrugeUtil::uiaction('rbacajaxgetassignmentbizrule');
    }

    public function getRbacAjaxSetAssignmentBzUrl()
    {
        return CrugeUtil::uiaction('rbacajaxsetassignmentbizrule');
    }

    public function getAjaxGenerateNewPasswordUrl()
    {
        return CrugeUtil::uiaction('ajaxgeneratenewpassword');
    }

    public function getAjaxResendRegistrationEmailUrl($id)
    {
        return CrugeUtil::uiaction('ajaxresendregistrationemail', array('id' => $id));
    }

    public function getSessionAdminUrl()
    {
        return CrugeUtil::uiaction('sessionadmin');
    }

    public function getSystemUpdateUrl()
    {
        return CrugeUtil::uiaction('systemupdate');
    }

    public function getLoginLink($label = null)
    {
        if ($label === null) {
            $label = 'Login';
        }
        return CHtml::link(CrugeTranslator::t('logon', $label), self::getLoginUrl());
    }

    public function getLogoutLink($label = null)
    {
        if ($label === null) {
            $label = 'Logout';
        }
        return CHtml::link(CrugeTranslator::t('logon', $label), self::getLogoutUrl());
    }

    public function getPasswordRecoveryLink($label = null)
    {
        if ($label === null) {
            $label = 'Lost Password?';
        }
        return CHtml::link(CrugeTranslator::t('logon', $label), self::getPasswordRecoveryUrl());
    }

    public function getEditProfileLink($label = null)
    {
        if ($label === null) {
            $label = 'Update Profile';
        }
        return CHtml::link(CrugeTranslator::t('admin', $label), self::getEditProfileUrl());
    }

    public function getUserManagementAdminLink($label = null)
    {
        if ($label === null) {
            $label = 'Control Panel';
        }
        return CHtml::link(CrugeTranslator::t('admin', $label), self::getUserManagementAdminUrl());
    }

    public function getUserManagementCreateLink($label = null)
    {
        if ($label === null) {
            $label = 'Create User';
        }
        return CHtml::link(CrugeTranslator::t('admin', $label), self::getUserManagementCreateUrl());
    }

    public function getRegistrationLink($label = null)
    {
        if ($label === null) {
            $label = 'Register';
        }
        return CHtml::link(CrugeTranslator::t('logon', $label), self::getRegistrationUrl());
    }


    public function getFieldAdminListLink($label = null)
    {
        if ($label === null) {
            $label = 'List Fields';
        }
        return CHtml::link(CrugeTranslator::t('admin', $label), self::getFieldsAdminListUrl());
    }

    public function getFieldAdminCreateLink($label = null)
    {
        if ($label === null) {
            $label = 'Create Field';
        }
        return CHtml::link(CrugeTranslator::t('admin', $label), self::getFieldsAdminCreateUrl());
    }


    /*
        entrega los items para un CPortlet o cualquier CMenu compatible, incluso Bootstrap.

        actualmente este metodo se invoca desde /cruge/layouts/ui.php
    */
    public function getAdminItems()
    {
        return array(
            array('label' => CrugeTranslator::t('admin', 'User Manager')),
            array(
                'label' => CrugeTranslator::t('admin', 'Update Profile')
            ,
                'url' => $this->getEditProfileUrl()
            ),
            array(
                'label' => CrugeTranslator::t('admin', 'Create User')
            ,
                'url' => $this->getUserManagementCreateUrl()
            ),
            array(
                'label' => CrugeTranslator::t('admin', 'Manage Users')
            ,
                'url' => $this->getUserManagementAdminUrl()
            ),
            array('label' => CrugeTranslator::t('admin', 'Custom Fields')),
            array(
                'label' => CrugeTranslator::t('admin', 'List Profile Fields')
            ,
                'url' => $this->getFieldsAdminListUrl()
            ),
            array(
                'label' => CrugeTranslator::t('admin', 'Create Profile Field')
            ,
                'url' => $this->getFieldsAdminCreateUrl()
            ),
            array('label' => CrugeTranslator::t('admin', 'Roles and Assignments')),
            array(
                'label' => CrugeTranslator::t('admin', 'Roles')
            ,
                'url' => $this->getRbacListRolesUrl()
            ),
            array(
                'label' => CrugeTranslator::t('admin', 'Tasks')
            ,
                'url' => $this->getRbacListTasksUrl()
            ),
            array(
                'label' => CrugeTranslator::t('admin', 'Operations')
            ,
                'url' => $this->getRbacListOpsUrl()
            ),
            array(
                'label' => CrugeTranslator::t('admin', 'Assign Roles to Users')
            ,
                'url' => $this->getRbacUsersAssignmentsUrl()
            ),
            array('label' => CrugeTranslator::t('admin', 'System')),
            array(
                'label' => CrugeTranslator::t('admin', 'Sessions')
            ,
                'url' => $this->getSessionAdminUrl()
            ),
            array(
                'label' => CrugeTranslator::t('admin', 'System Variables')
            ,
                'url' => $this->getSystemUpdateUrl()
            ),
        );
    }

    /*
		una utilidad para usar una extension como EMenu para mostrar menues
		verticales.
		$this->widget('EMenu', array(
			'theme'=>'adobe',
			'items'=>Yii::app()->user->ui->adminItemsAlternative
		));
    */
    public function getAdminItemsAlternative()
    {
        return array(
            array('label' => CrugeTranslator::t('admin', 'User Manager'),'items'=>array(
				array(
					'label' => CrugeTranslator::t('admin', 'Update Profile')
				,
					'url' => $this->getEditProfileUrl()
				),
				array(
					'label' => CrugeTranslator::t('admin', 'Create User')
				,
					'url' => $this->getUserManagementCreateUrl()
				),
				array(
					'label' => CrugeTranslator::t('admin', 'Manage Users')
				,
					'url' => $this->getUserManagementAdminUrl()
				)
			)),
            array('label' => CrugeTranslator::t('admin', 'Custom Fields'),'items'=>array(
				array(
					'label' => CrugeTranslator::t('admin', 'List Profile Fields')
				,
					'url' => $this->getFieldsAdminListUrl()
				),
				array(
					'label' => CrugeTranslator::t('admin', 'Create Profile Field')
				,
					'url' => $this->getFieldsAdminCreateUrl()
				)
			)),
            array('label' => CrugeTranslator::t('admin', 'Roles and Assignments'),'items'=>array(
				array(
					'label' => CrugeTranslator::t('admin', 'Roles')
				,
					'url' => $this->getRbacListRolesUrl()
				),
				array(
					'label' => CrugeTranslator::t('admin', 'Tasks')
				,
					'url' => $this->getRbacListTasksUrl()
				),
				array(
					'label' => CrugeTranslator::t('admin', 'Operations')
				,
					'url' => $this->getRbacListOpsUrl()
				),
				array(
					'label' => CrugeTranslator::t('admin', 'Assign Roles to Users')
				,
					'url' => $this->getRbacUsersAssignmentsUrl()
				),
			)),
            array('label' => CrugeTranslator::t('admin', 'System'),'items'=>array(
				array(
					'label' => CrugeTranslator::t('admin', 'Sessions')
				,
					'url' => $this->getSessionAdminUrl()
				),
				array(
					'label' => CrugeTranslator::t('admin', 'System Variables')
				,
					'url' => $this->getSystemUpdateUrl()
				),
			)),
        );
    }


    /*	acumula los errores obtenidos durante el request para su posterior visualizacion con displayErrorConsole

        este metodo es llamado desde CrugeWebUser::checkAccess cuando el flag
        CrugeModule::rbacSetupEnabled esta habilitado, el objetivo es informar que el $itemName
        indicado ha fallado para el usuario activo.

        con este metodo se pretende ayudar al operador a que programe los permisos requeridos
        para el rol o los roles asignados a un usuario en particular.

        @see displayErrorConsole
    */
    public function addError($itemName, $tipoItemName = '', $description = "")
    {
        if (CrugeUtil::config()->rbacSetupEnabled == true) {
            CrugeUtil::config()->globalErrors[] =
                array('itemName' => $itemName, 'type' => $tipoItemName, 'descr' => $description);
        }
        // siempre reporta el error de acceso en logger:
        //
        Yii::log(
            CrugeTranslator::t('logger', 'PERMISSION IS REQUIRED') . ":\n" . Yii::app(
            )->user->name . "\niduser=" . Yii::app()->user->id
                . "\ntipo:{$tipoItemName}\nitemName:{$itemName}\n" . $description
            ,
            "rbac"
        );
    }

    /**
    este metodo deberia ser invocado al final del layout principal del cliente para que le muestre los errores obtenidos y recopilados por addError

    @see addError
     */
    public function displayErrorConsole()
    {
        $outputText = "";
        if (CrugeUtil::config()->rbacSetupEnabled == true) {
            $n = 0;
            foreach (CrugeUtil::config()->globalErrors as $gerr) {
                if ($n == 0) {

                    $user = "ID=" . Yii::app()->user->id . ", NAME=" . Yii::app()->user->name;

                    $title = CrugeTranslator::t(
                        'logger',
                        'This page displays the roles, tasks and operations that are required by the current user but unassigned. This message is displayed because CrugeModule::rbacSetupEnabled = true'
                    );
                    $icon = "";
                    $outputText = "<div title='$title' class='rbac-global-error-list'>";
                    $outputText .= "<h6>" . $icon . ucwords(
                        CrugeTranslator::t('logger', 'Assignments required by the user')
                    ) . ":" . $user . "</h6>";
                    $outputText .= "<ul>";
                }
                $tipo = "";
                if ($gerr['type'] != '') {
                    $tipo = " ({$gerr['type']})";
                }
                $desc = "";
                if ($gerr['descr'] != '') {
                    $desc = " ({$gerr['descr']})";
                }
                $outputText .= "<li><b>" . $gerr['itemName'] . "</b>" . $tipo . $desc . "</li>";
                $n++;
            }
            if ($n > 0) {
                $outputText .= "</ul></div>";
            }
        }
        return $outputText;
    }

    public function superAdminNote()
    {
        return "<div class='is-superadmin-note'>" .
            CrugeTranslator::t('admin', '*** You are working as Super Administrator ***') .
            "</div>";
    }

    public function setupAlert($message)
    {

        $estilo = "text-align: center;background-color: rgb(255,140,140);margin: 3px;padding: 3px;border-radius: 5px;color: black; font-weight: bold;box-shadow: 3px 3px 3px #333;overflow: auto; position: absolute; top:0px; left: 10%;";
        $alertReason = CrugeTranslator::t(
            'admin',
            'This message is displayed because you have "debug" parameter enabled in the config file.'
        );
        return "<div style='$estilo'>$message<br/><span style=\"color: white; font-size: small;\">{$alertReason}</span></div>";
    }

    public function getCGridViewClass()
    {
        return CrugeUtil::config()->useCGridViewClass;
    }

    public function bbutton($texto, $name = 'volver')
    {
        $this->_button($texto, $name);
    }

    public function tbutton($texto)
    {
        $this->_button($texto);
    }

    private function _button($label, $name = null)
    {
        $ar = array();
        $_type = 'submit'; // siempre son submit
        $_icon = 'remove white';
        if ($name == null) {
            $_icon = 'ok white';
            $name = 'submit';
        }
        $ar = array('name' => $name);

        $label = ucwords($label);

        $estiloBoton = CrugeUtil::config()->buttonStyle;

        if ($estiloBoton == 'jui') {
            Yii::app()->getController()->widget(
                'zii.widgets.jui.CJuiButton',
                array(
                    'name' => $name,
                    'caption' => $label,
                )
            );
            return;
        }

        if ($estiloBoton == 'bootstrap') {
            Yii::app()->getController()->widget(
                'bootstrap.widgets.TbButton'
                ,
                array(
                    'buttonType' => $_type,
                    'type' => 'primary',
                    'htmlOptions' => $ar,
                    'icon' => $_icon,
                    'label' => $label,
                    'size' => CrugeUtil::config()->buttonConf, // '', 'large', 'small' or 'mini'
                )
            );
            return;
        }

        echo CHtml::submitButton($label, $ar);
    }

}
