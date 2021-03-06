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
namespace Hummer\Component\Http;

use Hummer\Component\Helper\Arr;
use Hummer\Component\Helper\Helper;

class HttpRequest{

    /**
     *  @var $aFILE $_FILES
     **/
    protected $aFILE;

    /**
     *  @var $aSERVER $_SERVER
     **/
    protected $aSERVER;

    /**
     *  @var $BagPOST $_POSt
     **/
    protected $BagPOST;

    /**
     * @var $BagGET $_GET
     **/
    protected $BagGET;

    /**
     *  @var $BagCOOKIE $_COOKIE
     **/
    protected $BagCOOKIE;

    /**
     *  @var $BagREQUEST $_REQUEST
     **/
    protected $BagREQUEST;
     /**
      *  @var $FORGE_REQUEST_URI
      **/
    public static $FORGE_REQUEST_URI = null;


    function __construct(
        $aSERVER  = null,
        $aPOST    = null,
        $aGET     = null,
        $aCOOKIE  = null,
        $aFILE    = null,
        $aREQUEST = null
    ) {
        $this->aFILE      = $aFILE    === null ? $_FILES   : $aFILE;
        $this->aSERVER    = $aSERVER  === null ? $_SERVER  : $aSERVER;
        $this->BagPOST    = new Bag_Param($aPOST    === null ? $_POST    : $aPOST);
        $this->BagGET     = new Bag_Param($aGET     === null ? $_GET     : $aGET);
        $this->BagCOOKIE  = new Bag_Param($aCOOKIE  === null ? $_COOKIE  : $aCOOKIE);
        $this->BagREQUEST = new Bag_Param($aREQUEST === null ? $_REQUEST : $aREQUEST);
    }

    public function getF($sFile=null)
    {
        return $sFile ===  null ? $this->aFILE : Arr::get($this->aFILE, $sFile, null);
    }


    public function getSRV($mKey=null)
    {
        return $mKey === null ? $this->aSERVER : Arr::get($this->aSERVER, $mKey, null);
    }

    public function issetG($mKey=null)
    {
        return $mKey === null ? false : $this->BagGET->checkIsSet($mKey);
    }

    public function getG($mKeyOrKeys=null)
    {
        return $mKeyOrKeys === null ? $this->BagGET->All() : $this->BagGET->get($mKeyOrKeys);
    }
    public function setG($mKeyOrKeys=null, $mValue=null)
    {
        return $this->BagGET->set($mKeyOrKeys, $mValue);
    }

    public function issetP($mKey=null)
    {
        return $mKey === null ? false : $this->BagPOST->checkIsSet($mKey);
    }

    public function getP($mKeyOrKeys=null)
    {
        return $mKeyOrKeys === null ? $this->BagPOST->All() : $this->BagPOST->get($mKeyOrKeys);
    }

    public function setP($mKeyOrKeys=null, $mValue=null)
    {
        return $this->BagPOST->set($mKeyOrKeys, $mValue);
    }

    public function issetC($mKey = null)
    {
        return $mKey === null ? false : $this->BagCOOKIE->checkIsSet($mKey);
    }

    public function getC($mKeyOrKeys=null)
    {
        return $mKeyOrKeys === null ? $this->BagCOOKIE->All() : $this->BagCOOKIE->get($mKeyOrKeys);
    }

    public function issetGP($mKey=null)
    {
        return $mKey === null ? false : $this->BagREQUEST->checkIsSet($mKey);
    }
    public function getGP($mKeyOrKeys=null)
    {
        return $mKeyOrKeys === null ? $this->BagREQUEST->All() : $this->BagREQUEST->get($mKeyOrKeys);
    }

    public function getRequestMethod()
    {
        return strtoupper($this->aSERVER['REQUEST_METHOD']);
    }

    public function getRequestURI()
    {
        return $this->aSERVER['REQUEST_URI'];
    }
    public function getSmartyRequestURI()
    {
        return Helper::TOOP(self::$FORGE_REQUEST_URI, self::$FORGE_REQUEST_URI, $this->getRequestURI());
    }

    public function getQueryString()
    {
        return $this->aSERVER['QUERY_STRING'];
    }

    public function getProtocol()
    {
        return $this->aSERVER['SERVER_PROTOCOL'];
    }

    public function getScriptName()
    {
        return $this->aSERVER['SCRIPT_NAME'];
    }

    public function getHeader($sVar)
    {
        $sKeyName = 'HTTP_'.strtoupper($sVar);
        return Arr::get($this->aSERVER, $sKeyName, null);
    }

    public function isAjax()
    {
        return strtolower($this->getHeader('X_Requested_With')) === 'xmlhttprequest';
    }

    /**
     *  get client ip
     *
     **/
    public function getClientIP()
    {
        $ip = null;
        $sHttpClientIP  = getenv('HTTP_CLIENT_IP');
        $sHttpXForWard  = getenv('HTTP_X_FORWARDED_FOR');
        $sRemoteAddr    = getenv('REMOTE_ADDR');

        if ($sHttpClientIP && strcasecmp($sHttpClientIP, 'unknown')){
            $ip = $sHttpClientIP;
        } elseif ($sHttpXForWard && strcasecmp($sHttpXForWard, 'unknown')){
            $ip = $sHttpXForWard;
        } elseif ($sRemoteAddr && strcasecmp($sRemoteAddr, 'unknown')){
            $ip = $sRemoteAddr;
        } elseif (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] &&
            strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')
        ){
            $ip = $_SERVER['REMOTE_ADDR'];
        } else{
            $ip = 'unknown';
        }
        return $ip !== 'unknown' ? $ip : null;
    }
}
