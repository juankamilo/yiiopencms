<?php
/**    ICrugeMailer

interfaz para el manejo de envio de correos

si un componente del usuario requiere personalizar el envio de correos puede crear
un nuevo componente que implemente esta interfaz y extienda de CrugeMailer

@author: Christian Salazar H. <christiansalazarh@gmail.com> @salazarchris74
http://www.yiiframeworkenespanol.org/licencia
 */
interface ICrugeMailer
{
    public function t($text);

    /**
     *
     * @method sendmailer
     * @param String $to correo destinatario
     * @param String $subject asunto del correo
     * @param String $body cuerpo del correo
     * @return boolean
     * @throws CrugeMailerException
     */
    public function sendEmail($to, $subject, $body);
}
