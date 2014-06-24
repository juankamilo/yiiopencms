<h2>Bienvenido !</h2>
<p>Estimado usuario, este correo se le ha enviado porque se ha creado una nueva cuenta para 
usted en el sistema, por favor tome nota:</p>
<b>Usuario y Clave</b>
<p><?php echo "usuario: ".$model->username;?></p>
<p><?php echo "email: ".$model->email;?></p>
<p><?php echo "su clave es: ".$password;?></p>

<h3>Debe activar su cuenta:</h3>
<p>Por favor siga este enlace para activar su cuenta:</p>
<p><?php echo Yii::app()->user->um->getActivationUrl($model); ?>
</p>
<p>Por favor tome las precauciones necesarias para guardar esta información</p>