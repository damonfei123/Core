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
namespace Hummer\Component\Session;

use Hummer\Component\Helper\Arr;
use Hummer\Component\Context\Context;

class DBStrategy implements IStrategy{

    /**
     *  @var $sDBConfig
     **/
    protected $sDBConfig;

    /**
     *  @var Session db
     **/
    protected $sDBName;

    /**
     *  @var DB Instance
     **/
    protected $DB;

    /**
     *  @var $Instance
     **/
    private static $Instance;

    /**
     *  Single Mode
     **/
    public static function getInstance(
        $Module,
        $sDBConfig,
        array $aDBConfig = array()
    ){
        if (null === self::$Instance) {
            self::$Instance = new self($Module, $sDBConfig, $aDBConfig);
        }
        return self::$Instance;
    }

    private function __construct(
        $Module,
        $sDBConfig,
        array $aDBConfig = array()
    ){
        $this->sDBConfig = $sDBConfig;
        $this->sDBName   = Arr::get($aDBConfig, 'db', 'session');
        $this->k         = Arr::get($aDBConfig, 'key', 'k');
        $this->v         = Arr::get($aDBConfig, 'value', 'v');
        $this->t         = Arr::get($aDBConfig, 'time', 't');
        $this->DB        = $Module->get($this->sDBName, $this->sDBConfig);
    }

    /**
     *  Open
     **/
    public function open($sPath, $sSessionName)
    {
        return true;
    }

    /**
     *  close
     **/
    public function close()
    {
        return true;
    }

    /**
     * Read
     **/
    public function read($id)
    {
        $V = $this->DB->find(array($this->k => $id));
        return $V ? $V->{$this->v} : null;
    }

    /**
     *  write
     **/
    public function write($id, $mV)
    {
        $aData = array($this->k => $id, $this->v => $mV, $this->t => time());
        if ($this->DB->find(array($this->k => $id))) {
            return $this->DB->where(array($this->k => $id))->data($aData)->update();
        }else{
            return $this->DB->data($aData)->save();
        }
    }

    /**
     *  Destroy
     **/
    public function destroy($id)
    {
        return $this->DB->where(array($this->k => $id))->delete();
    }

    /**
     *  GC
     **/
    public function gc($maxlifetime)
    {
        Context::getInst()->HttpResponse->httpFastClose();
        $this->DB->where(array($this->t.' <' => time() - $maxlifetime))->delete();
    }
}
