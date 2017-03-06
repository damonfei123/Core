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
        if(strtolower(PHP_SAPI) === \Hummer\Bundle\Framework\Bootstrap::S_RUN_CLI AND !ini_get('apc.enable_cli')) {
            throw new ModuleErrorException('[Cache] : apc cli module is not allowed, please enable apc in cli by config ini=> apc.enable_cli');
        }
        $this->iExpire = $iExpire;
    }

    /**
     *  Cache Data
     *  Apc cache caches mix data,such as int, string, array, object ...
     **/
    public function store($sKey, $mVal=null, $iExpire=null)
    {
        if (!is_array($sKey)) {
            if($bRet=!!apc_store($this->getStoreFile($sKey), $mVal, Helper::TOOP($iExpire, time() + $iExpire, time() + $this->iExpire))){
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
        return apc_exists($sStoreFile) ? apc_get($sStoreFile) : null;
    }

    /**
     *  Del Cache
     **/
    public function delete($sKey)
    {
        $sStoreFile = $this->getStoreFile($sKey);
        apc_exists($sStoreFile) AND apc_delete($sStoreFile) AND $this->deleteKey($sKey);
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
