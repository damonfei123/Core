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
namespace Hummer\Component\Util\Validator\Strategy;

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
            //enum
            'enum'    => '{key} value is not range',
            //boolean
            'boolean' => '{key} must be boolean',
            //express
            'express' => '{key} is not valid',
            //string
            'string'  => '{key} must be string',
            'string.max'  => '{key} max len is {rule}, now is {value}',
            'string.min'  => '{key} min len is {rule}, now is {value}',
            //int
            'int.int' => '{key} must be int',
            'int.max' => '{key} max is {rule}, send value is {value}',
            'int.min' => '{key} min is {rule}, send value is {value}',
            //regex
            'regex'   => '{key} is error pattern',
            //mobile
            'mobile'  => '{key} is not a mobile',
            //email
            'email'   => '{key} : {value} is not a email',
        );
        $iPosKeyRuleName = false !== ($iPos = strpos($sKey, '.')) ? $iPos+1 : 0;
        $sMessage = Arr::get(
            $this->aMsg,
            substr($sKey,$iPosKeyRuleName),
            Arr::get($aMessage, $sKey, '')
        );
        $mValue = $aParam ? array_shift($aParam) : $this->mValue;
        return str_replace(
            array('{key}','{rule}', '{value}'),
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

    public static function getInstance(
        $sValidator,
        $sKey,
        $mValue,
        array $aRule = array(),
        array $aMsg = array()
    ) {
        $Instance = Arr::get(self::$Instance, $sValidator, null);
        if (null === $Instance) {
            self::$Instance[$sValidator] = new static();
        }
        self::$Instance[$sValidator]->init($sKey, $mValue, $aRule, $aMsg);
        return self::$Instance[$sValidator];
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
