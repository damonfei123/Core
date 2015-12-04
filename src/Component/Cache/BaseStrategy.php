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
namespace Hummer\Component\Cache;

class BaseStrategy{

    const CACHE_TIME         = 31536000;//default cache one year
    const __CACHE_KEY_DATA__ = 'hummer.src.Component.Cache.__CACHE_KEY_DATA__';

    /**
     *  @var $aKeys
     **/
    protected $aKeys;

    /*
     *  Cache Data
     */
    public function keys()
    {
        $aKeys = $this->fetch(self::__CACHE_KEY_DATA__);
        unset($aKeys[self::__CACHE_KEY_DATA__]);
        return $aKeys;
    }

    /*
     *  添加缓存索引
     */
    protected function addKey($mKey, $iExpire)
    {
        $aKeys = $this->fetch(self::__CACHE_KEY_DATA__);
        if (is_array($mKey)) {
            foreach ($mKey as $k => $v) {
                $sKey    = $v ? array_shift($v) : null;
                $mData   = $v ? array_shift($v) : null;
                $iExpire = $v ? array_shift($v) : null;
                $aKeys[$sKey] = time() + $iExpire;
            }
        }else{
            $aKeys[$mKey] = time() + $iExpire;
        }
        return $this->store(self::__CACHE_KEY_DATA__, $aKeys, time() + self::CACHE_TIME);
    }

    /*
     *  删除缓存索引
     */
    protected function deleteKey($sKey)
    {
        $aKeys = $this->fetch(self::__CACHE_KEY_DATA__);
        if (isset($aKeys[$sKey])) unset($aKeys[$sKey]);
        return $this->store(self::__CACHE_KEY_DATA__, $aKeys, time() + self::CACHE_TIME);
    }

}
