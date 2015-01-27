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
namespace Hummer\Component\Validator\Strategy;

use Hummer\Component\Helper\Arr;
use Hummer\Component\Helper\Helper;

abstract class AValidator {

    /**
     *  @var $Instance
     **/
    protected static $Instance = array();

    /**
     *  @var $mValue
     **/
    protected $mValue = null;

    /**
     *  @var $mSet
     **/
    protected $mSet = null;

    /**
     *  @var $sKey
     **/
    protected $sKey;

    private function __construct() {}

    public function retMessage($sKey, array $aParam = array())
    {
        $aMessage = array(
            'require' => '{key} is required',
            //boolean
            'boolean' => '{key} must be boolean',
            //string
            'string'  => '{key} must be string',
            'string.max'  => '{key} max len is {set}, now is {value}',
            'string.min'  => '{key} min len is {set}, now is {value}',
            //int
            'int.int' => '{key} must be int',
            'int.max' => '{key} max is {set}, send value is {value}',
            'int.min' => '{key} min is {set}, send value is {value}',
        );
        $iPosKeyRuleName = false !== ($iPos = strpos($sKey, '.')) ? $iPos+1 : 0;
        $sMessage = Arr::get(
            $this->aMsg,
            substr($sKey,$iPosKeyRuleName),
            Arr::get($aMessage, $sKey, '')
        );
        $mValue = $aParam ? array_shift($aParam) : $this->mValue;
        return str_replace(
            array('{key}','{set}', '{value}'),
            array($this->sKey, $this->mSet, $mValue),
            $sMessage
        );
    }

    /**
     *  Set Current Rule Value
     **/
    public function setMSet($mSet=null)
    {
        $this->mSet = $mSet;
    }

    public function init($sKey, $mValue, array $aRule = array(), array $aMsg = array())
    {
        $this->sKey   = $sKey;
        $this->mValue = $mValue;
        $this->aRule  = $aRule;
        $this->aMsg   = $aMsg;
    }

    public static function getInstance($sKey, $mValue, array $aRule = array(), array $aMsg = array())
    {
        $Instance = Arr::get(self::$Instance, $sKey, null);
        if (null === $Instance) {
            self::$Instance[$sKey] = new static();
        }
        self::$Instance[$sKey]->init($sKey, $mValue, $aRule, $aMsg);
        return self::$Instance[$sKey];
    }

    public function fail($sKey, $aParam=array())
    {
        return $this->retMessage($sKey, $aParam);
    }

    /**
     *  Validate Must Be !!!
     **/
    abstract function validator();
}
