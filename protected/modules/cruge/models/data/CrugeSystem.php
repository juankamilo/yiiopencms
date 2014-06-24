<?php
/**
CrugeSystem

Es una division del sistema que incopora reglas, grupos, metodos de autenticacion etc.

parametros de configuracion:

$sessionmaxdurationmins			marca el tiempo de expiracion del objeto CrugeSession
$sessionmaxsameipconnections	umbral de sesiones activas de misma IP, si supera no crea sesion
$sessionreusesessions			si es 1, reutiliza la sesion si esta no ha expirado
$sessionmaxsessionsperday		limitador de sesiones diarias globales. -1 = cualquiera.
$sessionmaxsessionsperuser		limitador de sesiones diarias por usuario. -1 = cualquiera.
$systemnonewsessions			si es 1, no admite sesiones nuevas
$systemdown						si es 1, no admite el uso de ninguna sesion

$registerusingcaptcha			1 si quiere usar captcha para registro de usuarios
$registerusingactivation		0 activa de inmediato, 1 envia mail, 2 activa manualmente
$registerusingterms				1 muestra los $terms como requisito para activar boton submit
$registerusingtermslabel		etiqueta del checkbox
$terms							los terminos para registrarse
$defaultroleforregistration		el rol que se aplicara por defecto a los usuarios registrados
$registrationonlogin			1 o 0, para que aparezca el link de "registration" en el form login
 * @author: Christian Salazar H. <christiansalazarh@gmail.com> @salazarchris74
 * @license protected/modules/cruge/LICENSE
 */
class CrugeSystem extends CActiveRecord implements ICrugeSystem
{

    public function get($attribute)
    {
        if (isset($this[$attribute])) {
            return trim($this[$attribute]);
        }
        // es posible que esta excepcion aparezca tras el commit:
        //	https://bitbucket.org/christiansalazarh/cruge/changeset/a1b8d66ae2
        //
        throw new CrugeException("se detecto una solicitud de atributo invalido a CrugeSystem.  este error se debe a que estas pidiendo un atributo que no esta presente en la tabla: " . $this->tableName(
        ));
    }

    public function getn($attribute)
    {
        return (1 * ($this->get($attribute)));
    }

    public static function findSystem($systemName)
    {
        return self::model()->findByAttributes(array('name' => trim($systemName)));
    }

    /*
        entrega un array de ICrugeSystem
    */
    public static function listModels()
    {
        return self::model()->findAll();
    }

    public function getShortName()
    {
        return $this->name;
    }

    public function getLargeName()
    {
        return $this->largename;
    }

    /*
        @returns boolean true si el sistema esta disponible para iniciar sesion
    */
    public function isAvailableForLogin()
    {
        return ($this->getn('systemdown') != 1);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return CrugeUtil::getTableName('system');
    }

    public function getPrimaryKey()
    {
        return $this->idsystem;
    }


    /**
     * Returns the static model of the specified AR class.
     * @return CrugeSystem the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('name', 'required'),
            array('name', 'length', 'max' => 45),
            array('systemdown', 'safe',),
            array('systemnonewsessions', 'safe',),
            array('sessionmaxdurationmins', 'required',),
            array(
                'sessionmaxdurationmins',
                'numerical',
                'min' => 0,
                'max' => '9999'
            ,
                'message' => CrugeTranslator::t('Use un valor entre 0 y 9999')
            ),
            array('registerusingactivation', 'safe',),
            array('registerusingterms', 'safe',),
            array('registerusingtermslabel', 'safe',),
            array('registerusingtermslabel', 'length', 'max' => 100),
            array('registerusingcaptcha', 'safe',),
            array('defaultroleforregistration', 'safe',),
            array('terms', 'safe',),
            array('registrationonlogin', 'safe',),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array();
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'idsystem' => 'Idsystem',
            'name' => 'Name',
            'systemdown' => CrugeTranslator::t("Detener Sistema"),
            'systemnonewsessions' => CrugeTranslator::t("No Admitir Nuevas Sesiones"),
            'sessionmaxdurationmins' => CrugeTranslator::t("Minutos de Duracion de la Sesion"),
            'registerusingactivation' => CrugeTranslator::t("Activacion del usuario registrado"),
            'defaultroleforregistration' => CrugeTranslator::t("Asignar Rol a usuarios registrados"),
            'registerusingterms' => CrugeTranslator::t("Registrarse usando terminos"),
            'registerusingtermslabel' => CrugeTranslator::t("Etiqueta"),
            'registrationonlogin' => CrugeTranslator::t("Ofrecer opción de Registrarse en pantalla de Login"),
            'registerusingcaptcha' => CrugeTranslator::t("Registrarse usando captcha"),
            'terms' => CrugeTranslator::t("Terminos y Condiciones de Registro"),
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria = new CDbCriteria;

        $criteria->compare('idsystem', $this->idsystem);
        $criteria->compare('name', $this->name, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }
}
