<?php /**
CrugeUtil

funciones variadas que se usan durante toda la aplicacion.

@author: Christian Salazar H. <christiansalazarh@gmail.com> @salazarchris74
@license protected/modules/cruge/LICENSE
 */
class CrugeUtil extends CComponent
{

    public static function config()
    {
        return Yii::app()->getModule('cruge');
    }

    public static function factory()
    {
        return Yii::app()->getModule('cruge')->factory;
    }

    /*
        crea una URL normalizada relativa al action UiController
    */
    public static function uiaction($actionName, $params = array())
    {
        return Yii::app()->createUrl('/' . self::config()->id . '/ui/' . $actionName, $params);
    }

    public static function passwordGenerator()
    {
        return substr(self::hash(rand() . rand()), 0, 8);
    }

    /* normaliza el nombre de la tabla anexandole el prefijo y aplicando mapping

    */
    public static function getTableName($tableName)
    {
        $prfx = self::config()->tableprefix;
        $_tableName = trim(strtolower($tableName));
        if (isset(self::config()->maptables[$_tableName])) {
            $_tableName = self::config()->maptables[$_tableName];
        }
        return $prfx . $_tableName;
    }

    public static function isPhpFile($filename)
    {
        return "php" === strtolower(trim(strrev(substr(strrev(trim($filename)), 0, 3))));
    }

    public static function getClassNameFromPhp($filename)
    {
        $noext = trim(substr(strrev(trim($filename)), 4, strlen(trim($filename)) - 4));
        $k = 0;
        for ($i = 0; $i < strlen($noext); $i++) {
            if (($noext[$i] == '\\') || ($noext[$i] == '/')) {
                $k = $i;
            }
            if ($k > 0) {
                break;
            }
        }
        if ($k == 0) {
            $k = strlen($noext);
        }

        return strrev(substr($noext, 0, $k));
    }

    public static function now()
    {
        return time();
    }

    public static function makeExpirationDateTime($minutesPlus)
    {
        return time() + ($minutesPlus * 60);
    }

    public static function isExpired($expirationdate)
    {
        return !(self::now() <= $expirationdate);
    }

    public static function getIpAddress()
    {
        return Yii::app()->request->userHostAddress;
    }

    /**
    toma una tira de valores:
    "1, azul\n2, rojo\n3, verde"

    y devuelve un array asi:

    ar[1] = azul
    ar[2] = rojo
    ar[3] = verde
     */
    public static function explodeOptions($listValues)
    {
        $lista = explode("\n", $listValues);
        $ar = array();
        foreach ($lista as $item) {
            $k = explode(",", $item);
            $val = "";
            $text = "";
            if (count($k) == 2) {
                $val = trim($k[0]);
                $text = trim($k[1]);
            } else {
                $val = "0";
                $text = trim($k[0]);
            }
            $ar[$val] = $text;
        }
        return $ar;
    }

    /**
     * Devuelve el hash del valor en el parámetro $value
     * @param $value
     * @return string
     */
    public static function hash($value)
    {
        $algo = self::config()->hash;
        return hash($algo, $value);
    }

}
