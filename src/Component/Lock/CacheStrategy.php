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
namespace Hummer\Component\Lock;

use Hummer\Component\Helper\Arr;
use Hummer\Component\Context\Context;

class CacheStrategy implements IStrategy{

    /**
     *  @var $Instance
     **/
    private static $Instance;

    /**
     *  @var $Cache
     **/
    private $Cache;

    /**
     *  @var $sKey
     **/
    private $sKey;

    /**
     *  @var $_sPrivatePreLockKey
     **/
    private $_sPrivatePreLockKey = '@(L*&O^(C*!!K*()))';

    /**
     *  Single Mode
     **/
    public static function getInstance($Cache){
        if (null === self::$Instance) {
            self::$Instance = new self($Cache);
        }
        return self::$Instance;
    }

    private function __construct($Cache){
        $this->Cache = $Cache;
    }

    public function setKey($sKey=null)
    {
        $this->sKey = $sKey ?: $this->getDefaultLockKey();
    }

    public function getKey()
    {
        return $this->sKey ?: $this->getDefaultLockKey();
    }

    public function lock($iExpire = 86400)
    {
        return $this->Cache->store($this->getKey(), 1, $iExpire);
    }

    public function locked()
    {
        return (boolean)$this->Cache->fetch($this->getKey());
    }

    public function unlock()
    {
        return $this->Cache->delete($this->getKey());
    }

    /**
     *  Default Lock Key
     *  Context Controller . Action
     **/
    private function getDefaultLockKey()
    {
        $Context = Context::getInst();
        return md5(sprintf('%s%s%s',
            $this->_sPrivatePreLockKey,
            $Context->_sControllerName,
            $Context->_sActionName
        ));
    }
}
