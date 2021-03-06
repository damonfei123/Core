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
namespace Hummer\Component\Context;

use Hummer\Component\Configure\Configure;

/**
 *  Context For Framework
 *  @property sEnv          String
 *  @property sRunMode      Run Mode
 *  @property Arr           Framework Arr Container
 *  @property aArgv         Params When Run With Cli
 *  @property sControllerName Called Controller
 *  @property sActionName   Called Action
 *  @property Config        \Hummer\Component\Configure\Configure
 *  @property Route         \Hummer\Component\Route\Route
 *  @property Template      \Hummer\Component\Template\TemplateAdaptor
 *  @property HttpRequest   \Hummer\Component\Http\HttpRequest
 *  @property HttpResponse  \Hummer\Component\Http\HttpResponse
 **/
class Context {

    /**
     *  Generate Context
     **/
    public static function makeInst()
    {
        if (!isset($GLOBALS['__SELF__CONTEXT'])) {
            $GLOBALS['__SELF__CONTEXT'] = array();
        }
        return $GLOBALS['__SELF__CONTEXT'][] = new static();
    }

    public static function getInst()
    {
        return end($GLOBALS['__SELF__CONTEXT']);
    }

    /**
     *  @var $__aVarRegister__ Cache All Registed Model|Var
     **/
    private $__aVarRegister__;

    public function register($sK, $mV)
    {
        $this->$sK                   = $mV;
        $this->__aVarRegister__[$sK] = true;
    }

    public function registerMulti($aRegisterMap)
    {
        foreach ($aRegisterMap as $sK => $mV) {
            $this->register($sK, $mV);
        }
    }

    public function isRegister($sRegName)
    {
        return isset($this->__aVarRegister__[$sRegName]) AND $this->__aVarRegister__[$sRegName];
    }

    private $__aModuleConf__;

    public function __get($sModelName)
    {
        if (!$this->isRegister('Config')) {
            throw new \DomainException('[CTX] : ERROR : NONE CTX CONFIG');
        }
        $Obj = $this->createObj(
            Configure::parseRecursion($this->loadModule($sModelName), $this->Config)
        );
        #save for cache
        $this->$sModelName= $Obj;
        $this->__aVarRegister__[$sModelName] = $Obj;
        return $Obj;
    }

    /**
     *  @var $aAllModelConfig
     **/
    protected static $aAllModelConfig = null;

    protected function loadModule($sModelName)
    {
        if (self::$aAllModelConfig === null) {
            self::$aAllModelConfig = $this->Config->get('module');
        }
        foreach (self::$aAllModelConfig as $aModule) {
            if (isset($aModule['module']) AND isset($aModule['class'])) {
                if ($aModule['module'] == $sModelName AND
                   (!isset($aModule['run_mode']) || $aModule['run_mode'] == $this->sRunMode)
                ){
                    $this->__aModuleConf__[$sModelName] = $aModule;
                }
            }else{
                throw new \DomainException('[CTX] : Module Config Error, No module Or class set');
            }
        }
        if (!isset($this->__aModuleConf__[$sModelName])) {
            throw new \DomainException('[CTX] : Error : no module['.$sModelName.'] found');
        }
        return $this->__aModuleConf__[$sModelName];
    }

    protected function createObj(array $aModuleConfig)
    {
        if (!isset($aModuleConfig['params'])) {
            $aModuleConfig['params'] = array();
        }
        if (empty($aModuleConfig['params'])) {
            $Obj = new $aModuleConfig['class']();
        }else{
            $Ref = new \ReflectionClass($aModuleConfig['class']);
            $Obj = $Ref->newInstanceArgs($aModuleConfig['params']);
        }
        if (isset($aModuleConfig['packer'])) {
            $Obj = new Packer($Obj, $aModuleConfig['packer']);
        }
        return $Obj;
    }
}
