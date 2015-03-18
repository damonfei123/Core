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
namespace Hummer\Util\Validator;

use Hummer\Component\Helper\Arr;
use Hummer\Component\Helper\Suger;
use Hummer\Component\Helper\Helper;

class Validator {

    /**
     * @var array list of built-in validators (name => class or configuration)
     */
    public static $builtInValidators = array(
        'boolean' => 'BooleanValidator',
        'qq'      => 'QQValidator',
        'int'     => 'IntValidator',
        'url'     => 'UrlValidator',
        'ip'      => 'IPValidator',
        'number'  => 'NumberValidator',
        'float'   => 'FloatValidator',
        'require' => 'RequireValidator',
        'string'  => 'StringValidator',
        'enum'    => 'EnumValidator',
        'regex'   => 'RegexValidator',
        'mobile'  => 'MobileValidator',
        'email'   => 'EmailValidator',
        'express' => 'ExpressValidator',
    );

    /**
     *  @var $aRule
     **/
    protected $aRule;

    /**
     *  @var $aData
     **/
    protected $aData;

    /**
     *  @var $aMsg
     **/
    protected $aMsg;

    public function __construct(
        array $aData = array(),
        array $aRule = array(),
        array $aMsg  = array()
    ) {
        $this->setRule($aRule);
        $this->setData($aData);
        $this->setMsg($aMsg);
    }

    /**
     *  Register Rule Data
     **/
    public function setRule($aRule=array())
    {
        $this->aRule = $aRule;
    }

    /**
     *  Register Data
     **/
    public function setData($aData=array())
    {
        $this->aData = $aData;
    }

    /**
     *  Register aMsg
     **/
    public function setMsg($aMsg=array())
    {
        $this->aMsg = $aMsg;
    }

    /**
     *  Validate Data From Rule
     **/
    public function validate()
    {
        foreach ($this->aRule as $aRule) {
            if (count($aRule) < 2) {
                throw ValidatorException('[Validator] : Rule Error : '.var_export($aRule, true));
            }
            $sKey       = array_shift($aRule);
            $sValidator = array_shift($aRule);
            $Validator  = $this->createValidator(
                $sValidator,
                $sKey,
                Arr::get($this->aData, $sKey),
                $aRule,
                Arr::get($this->aMsg, $sKey, array())
            );
            if(true !== ($sValidMsg = $Validator->validator())){
                return $sValidMsg;
            }
        }
        return true;
    }

    /**
     *  Create Validator
     *  @param $sValidator string   Validator Name
     *  @param $sKey       string   Value To Validated
     *  @param $mValue     mix      Value To Validated
     *  @param $aRule      array    Rule To Validate
     *  @return Validator
     **/
    public function createValidator(
        $sValidator,
        $sKey,
        $mValue = null,
        array $aRule = array(),
        array $aMsg=array()
    ) {
        if(!($sBuiltInValidators = Arr::get(self::$builtInValidators, $sValidator, ''))) {
            throw ValidatorException('[Validator] : Validator Not Set: '.$sValidator);
        }
        return Suger::createObjSingle(
            __NAMESPACE__ . '\Strategy',
            array('@'.ucfirst($sBuiltInValidators), $sValidator, $sKey, $mValue, $aRule, $aMsg), ''
        );
    }
}
