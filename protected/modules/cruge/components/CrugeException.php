<?php
/**    CrugeException

centraliza la emision de excepciones, ayudando a traducir los mensajes usando CrugeTranslator

@author: Christian Salazar H. <christiansalazarh@gmail.com> @salazarchris74
@license protected/modules/cruge/LICENSE
 */
class CrugeException extends CHttpException
{
    public $classParent;
    public $extra;
    public $code;

    public function __construct($message, $code = 500, $extra = "")
    {
        parent::__construct($code, $message);
        $this->code = $code;
        $this->extra = $extra;
    }

    public function __toString()
    {
        /*
        return $this->classParent . ": [{$this->code}]: ".CrugeTranslator::t($this->message)."\n".$extra;
        */
        return CrugeTranslator::t($this->message) . "<br/>" . $this->code;
    }
}
