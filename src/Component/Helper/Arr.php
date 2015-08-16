<?php
/*************************************************************************************

   +-----------------------------------------------------------------------------+
   | Hummer [ Make Code Beauty And Web Easy ]                                    |
   +-----------------------------------------------------------------------------+
   | Copyright (c) 2014 https://github.com/damonfei123 All rights reserved.      |
   +-----------------------------------------------------------------------------+
   | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )                     |
   +-----------------------------------------------------------------------------+
   | Author: Damon <zhangyinfei313com@163.com>                                   |
   +-----------------------------------------------------------------------------+

**************************************************************************************/
namespace Hummer\Component\Helper;

class Arr{

    public static function get($aData, $sKey, $sDefault = null, $bExplain=true)
    {
        if (!is_array($aData)) {
            return $aData;
        }
        if ($bExplain AND false !== strpos($sKey, '.')) {
            return self::getBySmarty($aData, $sKey, $sDefault);
        }
        return isset($aData[$sKey]) ? $aData[$sKey] : $sDefault;
    }

    public static function changeIndex(array $aArr, $sKey='id')
    {
        $aRetArr = array();
        foreach ($aArr as $Arr) {
            $aRetArr[$Arr[$sKey]] = $Arr;
        }
        return $aRetArr;
    }

    public static function changeIndexToKVMap(array $aArr, $sKey, $sVal)
    {
        $aRetArr = array();
        foreach ($aArr as $Arr) {
            $aRetArr[$Arr[$sKey]] = $Arr[$sVal];
        }
        return $aRetArr;
    }

    public static function changeValue(array $aArr, $sKey)
    {
        $aRetArr = array();
        foreach ($aArr as $Arr) {
            $aRetArr[] = $Arr[$sKey];
        }
        return $aRetArr;
    }
    /**
     *  Get Array Data By Smarty Way
     *  getBySmarty(array('name' => array('first' => 'zhang')), 'name.first');
     **/
    public static function getBySmarty($mArr, $sKey = '', $sDefault = null,  $sSepetator='.')
    {
        while ($sKey AND
               is_array($mArr) AND
               false !== ($iPos=strpos($sKey, $sSepetator))
        ){
            $sTK  = substr($sKey, 0, $iPos);
            $mArr = Arr::get($mArr, $sTK, $sDefault);
            $sKey = substr($sKey, $iPos + 1);
        }
        return Helper::TOOP(
            is_array($mArr) AND $sKey,
            Arr::get($mArr, $sKey, $sDefault, false),
            $sDefault
        );
    }
}
