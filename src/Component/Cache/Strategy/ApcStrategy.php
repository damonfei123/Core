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
namespace Hummer\Component\Cache\Strategy;

use Hummer\Component\Helper\Arr;
use Hummer\Component\Helper\Helper;

class ApcStrategy extends BaseStrategy implements IStrategy{

    /**
     *  @var $sCacheDir Cache Dir
     **/
    protected $sCacheDir;

    /**
     *  @var $iExpire
     **/
    protected $iExpire;

    static $Instance;

    /**
     *  @var Private key
     **/
    const __CACHE__KEY__     = 'hummer.src.Component.Cache.#(*@#&*@@)!_)#+';

    /**
     *  @var $sCacheDir Cache Dir
     *  @var $iCacheTime Default Expire Time
     **/
    public function __construct($apc, $iExpire = 86400)
    {
        if ( ! extension_loaded('apc')) {
            throw new ModuleErrorException('[Cache] : module apc extension is not loaded');
        }
        $this->iExpire = $iExpire;
    }

    /**
     *  Cache Data
     **/
    public function store($sKey, $mVal=null, $iExpire=null)
    {
        if (!is_array($sKey)) {
            if (is_resource($mVal)) {
                throw new \InvalidArgumentException('[Cache] : ERROR serialize can a resource');
            }
            if($bRet=!!apc_store($this->getStoreFile($sKey), sprintf('%s:%s:%s',
                Helper::TOOP($iExpire, time() + $iExpire, time() + $this->iExpire),
                Helper::TOOP(is_object($mVal), 1, 2),
                Helper::TOOP(is_object($mVal), serialize($mVal), json_encode($mVal))
            ))){
                if ($sKey != self::__CACHE_KEY_DATA__) {
                    $this->addKey($sKey, $iExpire);
                }
            };
            return $bRet;
        }else{
            $aTData = $aData;
            foreach ($sKey as $aData) {
                if(!$this->store(
                    $aData ? array_shift($aData) : null,
                    $aData ? array_shift($aData) : null,
                    $aData ? array_shift($aData) : null
                )){
                    return false;
                }
            }
            if ($sKey != self::__CACHE_KEY_DATA__) {
                $this->addKey($sKey, $aTData);
            }
            return true;
        }
    }

    /**
     *  Get Cache
     **/
    public function fetch($sKey, $bGC = true)
    {
        $sStoreFile = $this->getStoreFile($sKey);
        if(!$sContent = apc_get($sStoreFile)){
            return null;
        };
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
        apc_delete($this->getStoreFile($sKey)) AND $this->deleteKey($sKey);
    }

    /**
     *  Get Save File
     *  Hash Storage
     **/
    protected function getStoreFile($sKey)
    {
        return sprintf('%s',md5($sKey . self::__CACHE__KEY__));
    }
}
