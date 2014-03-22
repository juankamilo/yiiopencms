<?php

/**
 * This is the model class for table "usuarios".
 *
 * The followings are the available columns in table 'usuarios':
 * @property integer $usu_id
 * @property string $usu_nombre
 * @property string $usu_login
 * @property string $usu_clave
 * @property integer $usu_activo
 * @property string $usu_correo
 * @property string $usu_session
 * @property integer $rol_id
 * @property integer $age_id
 *
 * The followings are the available model relations:
 * @property Reservas[] $reservases
 * @property TareaComentarios[] $tareaComentarioses
 * @property Tareas[] $tareases
 * @property Agencias $age
 * @property Roles $rol
 */
class BaseUsuarios extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Usuarios the static model class
	 */
        public $new_password;
        public $username;
        public $password;
        
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'usuarios';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('usu_nombre, usu_login, usu_activo, usu_clave, rol_id, age_id', 'required'),
			array('rol_id, usu_activo, age_id', 'numerical', 'integerOnly'=>true),
			array('usu_nombre, usu_login, usu_clave, usu_correo, usu_session', 'length', 'max'=>45),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('usu_id, usu_nombre, usu_login, usu_clave, usu_activo, usu_correo, usu_session, rol_id', 'safe', 'on'=>'search'),
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
                        'reservases' => array(self::HAS_MANY, 'Reservas', 'usu_id'),
			'tareaComentarioses' => array(self::HAS_MANY, 'TareaComentarios', 'usu_id'),
			'tareases' => array(self::HAS_MANY, 'Tareas', 'usu_id'),
			'age' => array(self::BELONGS_TO, 'Agencias', 'age_id'),
			'rol' => array(self::BELONGS_TO, 'Roles', 'rol_id'),
		);
	}

        /**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'usu_id' => 'ID',
			'usu_nombre' => 'Nombre',
			'usu_login' => 'Login',
			'usu_clave' => 'Clave',
			'usu_activo' => 'Activo',
			'usu_correo' => 'Correo',
			'usu_session' => 'Session',
			'rol_id' => 'Rol',
                        'age_id' => 'Agencia',
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

		$criteria=new CDbCriteria;

		$criteria->compare('usu_id',$this->usu_id);
		$criteria->compare('usu_nombre',$this->usu_nombre,true);
		$criteria->compare('usu_login',$this->usu_login,true);
		$criteria->compare('usu_clave',$this->usu_clave,true);
		$criteria->compare('usu_activo',$this->usu_activo,true);
		$criteria->compare('usu_correo',$this->usu_correo,true);
		$criteria->compare('usu_session',$this->usu_session,true);
		$criteria->compare('rol_id',$this->rol_id);
                $criteria->compare('age_id',$this->age_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}