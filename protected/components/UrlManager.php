<?php
/**
 * 
 */
class UrlManager extends CUrlManager
{
    public function createUrl($route,$params=array(),$ampersand='&')
    {
        /*if(preg_match('/[A-Z]/',$route)!==0)
        {
            $route=strtolower(preg_replace('/(?<=\\w)([A-Z])/','-\\1',$route));
        }*/
        if (!isset($params['lang'])) {
            if (Yii::app()->user->hasState('lang'))
                Yii::app()->language = Yii::app()->user->getState('lang');
            else if(isset(Yii::app()->request->cookies['lang']))
                Yii::app()->language = Yii::app()->request->cookies['lang']->value;
            $params['lang']=Yii::app()->language;
        }
        return parent::createUrl($route,$params,$ampersand);
    }

    public function parseUrl($request)
    {
        $route=parent::parseUrl($request);
        if(substr_count($route,'-')>0)
        {
            $route=lcfirst(str_replace(' ','',ucwords(str_replace('-',' ',$route))));
        }
        return $route;
    }
}