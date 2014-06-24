<?php

/**
 * This is the model class for table "CrugeSession".
 *
 * The followings are the available columns in table 'CrugeSession':
 * @property integer $idsession
 * @property integer $iduser
 * @property string $created
 * @property string $expire
 * @property integer $status
 * @property string $ipaddress
 * @property string $ipaddressout
 * @property string $logoutdate
 * @property integer $usagecount
 * @property string $lastusage
 * @author: Christian Salazar H. <christiansalazarh@gmail.com> @salazarchris74
 * @license protected/modules/cruge/LICENSE
 */
class CrugeSession extends CActiveRecord implements ICrugeSession
{

    /*
        @returns nueva instancia de CrugeSession
    */
    public static function create($iduser, $durationMins)
    {
        $model = new CrugeSession();
        $model->iduser = $iduser;
        $model->created = CrugeUtil::now();
        $model->lastusage = $model->created;
        $model->expire = CrugeUtil::makeExpirationDateTime($durationMins);
        $model->status = 1;
        $model->usagecount = 1;
        $model->ipaddress = CrugeUtil::getIpAddress();
        return $model;
    }

    /** que hacer cuando la sesion es reutilizada
    @returns void.
     */
    public function onReusage()
    {
        $this->usagecount++;
        $this->lastusage = CrugeUtil::now();
        $this->ipaddress = CrugeUtil::getIpAddress();
    }

    /*	almacena la sesion, que puede ser nueva o reutilizada
        @returns boolean. false=causa que la sesion no se asigne.
    */
    public function store()
    {
        $ret = false;
        if ($this->getIsNewRecord()) {
            $ret = $this->insert();
        } else {
            $ret = $this->update();
        }

        Yii::log(
            __CLASS__ . "::store() #" . $this->getPrimaryKey()
                . "\nresult=" . $ret . "\nerrorinfo:" . CHtml::errorSummary($this) . "\n"
                . "json: " . CJSON::encode($this) . "\n"
            ,
            "info"
        );

        return $ret;
    }


    /**
    es importante devolver el username porque este resultado podria evaluarse
    sobre un CAccessRule el cual comparara el nombre del user de su regla con este que
    aqui se retorna.

    @returns string Nombre del usuario (username) de esta sesion
     */
    public function getSessionName()
    {
        return $this->user->username;
    }

    /**
    @returns CrugeSession una instancia del modelo hallada por su IDSESSION
     */
    public static function loadModel($id)
    {
        return self::model()->findByPk($id);
    }

    /*
        @returns CrugeSession instancia de la sesion mas reciente hallada para este usuario
    */
    public static function findLast($iduser)
    {

        $model = null;

        // busca si este usuario tiene una sesion valida, para reutilizarla
        //
        $curSession = self::model()->findByAttributes(
            array('iduser' => $iduser),
            array('order' => 'idsession DESC')
        );
        Yii::log(__CLASS__ . "::findLast iduser={$iduser}\ncurSession:" . CJSON::encode($curSession), "info");

        $info = "";
        if ($curSession != null) {
            $info .= "[sesion encontrada]";
            if ($curSession->validateSession() == true) {
                $info .= "[sesion validada]";
                $model = $curSession;
            } else {
                $info .= "[sesion no validada]";
            }
        } else {
            $info .= "[sesion no hallada]";
        }

        Yii::log(__CLASS__ . "::findLast\nresult:" . $info, "info");

        return $model;
    }

    /**
    @returns Boolean indicando que la sesion es valida para ser utilizada y asignada
     */
    public function validateSession()
    {
        if ((1 * ($this->status)) != 1) {
            Yii::log("CrugeSession. validateSession. causa: estatus cerrado", "info");
            return false;
        }

        if ($this->isSessionExpired() == true) {
            Yii::log(
                "CrugeSession. validateSession. causa: sesion expirada. date.now="
                    . CrugeUtil::now(),
                "info"
            );
            return false;
        }

        return true;
    }

    /*
        @returns boolean true indicando que la sesion ha expirado
    */
    public function isSessionExpired()
    {
        return CrugeUtil::isExpired($this->expire) != '';
    }

    /* es invocado por CrugeWebUser via interfaz ICrugeSession

        @returns VOID
    */
    public function logout()
    {
        $this->logoutdate = CrugeUtil::now();
        $this->ipaddressout = CrugeUtil::getIpAddress();
        $this->status = 0;
    }

    /*
        @returns VOID
    */
    public function expiresession()
    {
        $this->status = 0;
    }

    public function tableName()
    {
        return CrugeUtil::getTableName('session');
    }

    public function getPrimaryKey()
    {
        return $this->idsession;
    }


    /**
     * Returns the static model of the specified AR class.
     * @return CrugeSession the static model class
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
            array('iduser', 'required'),
            array('iduser, status', 'numerical', 'integerOnly' => true),
            array('ipaddress, ipaddressout', 'length', 'max' => 45),
            array('created, expire', 'required'),
            array('usagecount, lastusage', 'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('idsession, iduser, created, expire, status, ipaddress', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'user' => array(self::BELONGS_TO, 'crugestoreduser', 'iduser'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'idsession' => 'Idsession',
            'iduser' => 'Iduser',
            'sessionname' => CrugeTranslator::t("usuario"),
            'created' => CrugeTranslator::t("creacion"),
            'expire' => CrugeTranslator::t("expira"),
            'lastusage' => CrugeTranslator::t("ultimo uso"),
            'status' => CrugeTranslator::t("estado"),
            'usagecount' => CrugeTranslator::t("contador<br/>login"),
            'ipaddress' => 'Ipaddress',
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

        $criteria->compare('idsession', $this->idsession);
        $criteria->compare('iduser', $this->iduser);
        $criteria->compare('created', $this->created, true);
        $criteria->compare('expire', $this->expire, true);
        $criteria->compare('status', $this->status);
        $criteria->compare('ipaddress', $this->ipaddress, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
            'sort' => array(
                'defaultOrder' => array('idsession' => true),
            ),
        ));
    }
}
