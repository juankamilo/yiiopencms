<?php
/**
 * CrugeTranslator
 * Provee centralización para aplicar la traducción de mensajes de inglés a otros idiomas
 * @author: Christian Salazar H. <christiansalazarh@gmail.com> @salazarchris74
 * @license application.modules.cruge.LICENSE
 */
class CrugeTranslator
{
    /**
    va a traducir a $keyword en el lenguaje configurado. si la traduccion no existe devuelve
    la palabra solicitada y la agrega al indice.
     */
    public static function t($category, $keyword = null, $params = array())
    {

        //$lang = Yii::app()->language;
        // Agregado para evitar incompatibilidad
        // Así que debe eliminarse cuando se termine la traducción
        if (empty($keyword)) {
            return $category;
        }

        return Yii::t("CrugeModule.$category", $keyword, $params);
    }
}
