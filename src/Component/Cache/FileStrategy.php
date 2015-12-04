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

use Hummer\Component\Helper\Arr;
use Hummer\Component\Helper\Helper;
use Hummer\Component\Filesystem\Dir;

class FileStrategy extends BaseStrategy implements IStrategy{

    /**
     *  @var $sCacheDir Cache Dir
     **/
    protected $sCacheDir;

    /**
     *  @var $iExpire
     **/
    protected $iExpire;

    /**
     *  @var Private key
     **/
    const __CACHE__KEY__ = '#(*@#&*@@)!_)#+';

    /**
     *  @var $sCacheDir Cache Dir
     *  @var $iCacheTime Default Expire Time
     **/
    public function __construct($sCacheDir, $iExpire = 86400)
    {
        if (!file_exists($sCacheDir) || !is_writable($sCacheDir)) {
            throw new \InvalidArgumentException(
                sprintf('[cache] : Cache Dir [%s] Error(not exists or unwritable)', $sCacheDir));
        }
        $this->iExpire   = $iExpire;
        $this->sCacheDir = Helper::TrimEnd($sCacheDir, '/', 'r');
    }

    /**
     *  Cache Data
     **/
    public function store($sKey, $mVal, $iExpire=null)
    {
        if (is_resource($mVal)) {
            throw new \InvalidArgumentException('[Cache] : ERROR serialize can a resource');
        }
        if($bRet = !!file_put_contents($this->getStoreFile($sKey), sprintf('%s:%s:%s',
            Helper::TOOP($iExpire, time() + $iExpire, time() + $this->iExpire),
            Helper::TOOP(is_object($mVal), 1, 2),
            Helper::TOOP(is_object($mVal), serialize($mVal), json_encode($mVal))
        ))){
            if ($sKey != self::__CACHE_KEY_DATA__) {
                $this->addKey($sKey, $iExpire);
            }
        };
        return $bRet;
    }

    /**
     *  Get Cache
     **/
    public function fetch($sKey, $bGC = true)
    {
        $sStoreFile = $this->getStoreFile($sKey);
        if (!file_exists($sStoreFile)) {
            return null;
        }
        $sContent   = file_get_contents($sStoreFile);
        $iExpire    = substr($sContent, 0, strpos($sContent, ':'));
        $sContent   = substr($sContent, strpos($sContent, ':') + 1);
        $iType      = substr($sContent, 0, strpos($sContent, ':'));
        $sContent   = substr($sContent, strpos($sContent, ':') + 1);
        $mStoreData = 1 == $iType ?
            unserialize($sContent) :
            json_decode($sContent, true);
        //expire
        if ($iExpire < time()) {
            if ($bGC) $this->delete($sKey);
            return null;
        }
        return $mStoreData;
    }

    /**
     *  Del Cache
     **/
    public function delete($sKey)
    {
        if(file_exists($sStoreFile=$this->getStoreFile($sKey))){
            @unlink($sStoreFile) AND $this->deleteKey($sKey);
        }
    }

    /**
     *  Get Save File
     *  Hash Storage
     **/
    protected function getStoreFile($sKey)
    {
        $sSubDir       = substr(abs(crc32($sKey)), 0,2);
        $sCacheFullDir = sprintf('%s%s%s',$this->sCacheDir, $sSubDir, '/');
        if (!file_exists($sCacheFullDir) && !Dir::makeDir($sCacheFullDir)) {
            throw new \RuntimeException('[Cache] : Store Dir not exists And Make Fail');
        }
        return sprintf('%s%s',$sCacheFullDir, md5($sKey . self::__CACHE__KEY__));
    }
}
