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
namespace Hummer\Component\RDB\ORM\Model;

use Hummer\Component\Helper\Arr;
use Hummer\Component\Helper\Helper;
use Hummer\Util\Validator\Validator;
use Hummer\Component\Context\Context;
use Hummer\Component\RDB\ORM\PDODecorator;

class Model{

    /**
     *  @var $PDODecorator  Hummer\Component\RDB\ORM\PDODecorator
     **/
    public $PDODecorator;

    /**
     *  @var $sTable Table
     **/
    public $sTable;


    /*
     *
     */
    public $aConfig;

    /**
     *  @var $_aProperty
     **/
    public $_aProperty;

    /**
     *  $_sErrMsg
     **/
    private $_sErrMsg = null;

    /**
     *  @var MODEL_INSERT
     **/
    const MODEL_INSERT = 1;
    /**
     *  @var MODEL_INSERT
     **/
    const MODEL_UPDATE = 2;
    /**
     *  @var MODEL_INSERT
     **/
    const MODEL_BOTH  = 3;

    /*
     * @var $_validator;
     */
    public $_validator;

    /*
     * @var $_auto;
     */
    public $_auto;

    /*
     * PDO Attr
     */
    public $aWhere = array();
    public $aData  = array();
    public $sSelect     = '*';
    public $sJoinTable  = '';
    public $sForceIndex ='';
    public $sLimit;
    public $sGroupBy;
    public $sHaving;
    public $sOrder;

    /**
     *  @var $sItemClassName  Hummer\Component\RDB\ORM\Model\Item;
     **/
    public $sItemClassName;

    public function __construct(
        $sModelName,
        $PDODecorator,
        $aConfig,
        $Factory
    ) {
        $this->PDODecorator = $PDODecorator;
        $sCalledClassName = get_called_class();

        $sCalledNS = '';
        if(false !== ($iPos = strrpos($sCalledClassName, '\\'))){
            $sCalledNS = substr($sCalledClassName, 0, $iPos);
        }
        #Item class
        if (false !== ($iPos=strpos($sModelName, '|'))) {
            $sItemModelName = substr($sModelName, 0, $iPos);
        }else{
            $sItemModelName = $sModelName;
        }
        $bAppItem = isset($aConfig['item_class']) AND $aConfig['item_class'];
        $this->sItemClassName = sprintf('%s%s%s',
            Helper::TOOP($bAppItem, $sCalledNS, __NAMESPACE__),
            '\\',
            Arr::get($aConfig, 'item_class', 'Item')
        );

        #Config
        $this->aConfig = $aConfig;
        #table
        $this->setTable($sModelName);

        #primary key
        if (isset($aConfig['pk'])) {
            $this->PDODecorator->sPrimaryKey = $aConfig['pk'];
        }
        #check Property
        $this->getModelProperty(new \ReflectionClass($this));

        #$this->PDODecorator->_Model[$this->sTable] = $this;
        $this->PDODecorator->setModel($this->sTable, $this);
        #__init__
        if (method_exists($this, '__init__')) {
            call_user_func_array(array($this, '__init__'), array());
        }
    }

    public function initModel($sModelName)
    {
        $this->PDODecorator->resetCondition();
        $this->setTable($sModelName);
    }

    public function getModelProperty($Model)
    {
        foreach ($Model->getProperties(\ReflectionProperty::IS_PUBLIC) as $Prop) {
            $this->_aProperty[$Prop->getName()] = true;
        }
    }

    public function isPublicProp($sProperty)
    {
        return isset($this->_aProperty[$sProperty]);
    }

    public function setTable($sModelName)
    {
        $sTable = Arr::get($this->aConfig, 'table', strtolower($sModelName));
        if (false !== ($iPOS = strpos($sModelName, '|'))) {
            $sTable = sprintf('%s|%s', $sTable, substr($sModelName, $iPOS + 1));
        }
        $this->PDODecorator->table($sTable, $sRealTable);
        $this->sTable = $sRealTable;
    }

    /**
     *  Find One
     *  @param $mWhere mix(string|array) Where Condition
     *  @param $bAssoc boolean           Return Obj Or Assoc
     **/
    public function find($mWhere=null)
    {
        $this->setPDOData();
        $aItem = $this->PDODecorator->limit(1)->querySmarty($mWhere);
        $aItem = empty($aItem) ? array() : array_shift($aItem);
        return new $this->sItemClassName($aItem, $this);
    }

    /**
     *  Find Multi By Return Array
     **/
    public function findCustom($mWhere=null)
    {
        $this->setPDOData();
        return $this->PDODecorator->querySmarty($mWhere);
    }

    /**
     *  Find Multi By Return Items
     **/
    public function findMulti($mWhere=null)
    {
        $this->setPDOData();
        $aItems   = $this->PDODecorator->querySmarty($mWhere);
        $aGroup   = array();
        foreach ($aItems as $aItem) {
            $aGroup[] = new $this->sItemClassName($aItem, $this);
        }
        return $aGroup;
    }

    /**
     *  Batch Save Data
     *  Use Transaction
     **/
    public function batchSave(array $aSaveData=array(), $iChunk = 1000)
    {
        $this->setPDOData();
        if (count($aSaveData) == 0) {
            return true;
        }
        $aColumnInfo = array_map(array(
            $this->PDODecorator, '_addQuote'),
            array_keys($aSaveData[0])
        );
        //Column
        $sBaseSQL = sprintf('INSERT INTO %s(%s) VALUES',
            $this->PDODecorator->getRealMapTable(),
            implode(',', $aColumnInfo)
        );
        $iChunkNum    = 0;
        $bChunkSave   = true;
        $sChunkColumn = sprintf('(%s)',
            implode(',', array_pad(array(), count($aColumnInfo), '?'))
        );
        $this->PDODecorator->begin();
        while ($aChunkData=array_slice($aSaveData, $iChunkNum * $iChunk, $iChunk))
        {
            $aChunkBind   = array();
            $aChunkColumn = array_pad(array(), count($aChunkData), $sChunkColumn);
            foreach ($aChunkData as $aCData) {
                $aChunkBind = array_merge($aChunkBind, array_values($aCData));
            }
            $sChunkSQL = sprintf('%s%s',$sBaseSQL, implode(',', $aChunkColumn));
            if(!($bChunkSave=$this->PDODecorator->exec($sChunkSQL, $aChunkBind))){
                goto END;
            }
            $iChunkNum++;
        }

        END:
        $bChunkSave ? $this->PDODecorator->commit() : $this->PDODecorator->rollback();
        return $bChunkSave;
    }

    public function batchAdd(array $aSaveData=array(), $iChunk = 1000)
    {
        return $this->batchSave($aSaveData, $iChunk);
    }

    public function __get($sVarName)
    {
        return property_exists($this->PDODecorator, $sVarName) ?
            $this->PDODecorator->$sVarName :
            null;
    }

    public function __set($sK, $mV)
    {
        $this->PDODecorator->$sK = $mV;
    }

    public function __call($sMethod, $aArgv)
    {
        if (!method_exists($this->PDODecorator, $sMethod)) {
            throw new \BadMethodCallException('[Model] : method{'.$sMethod.'} error !!! ');
        }
        $this->setPDOData();
        $mResult = call_user_func_array(array($this->PDODecorator, $sMethod), $aArgv);
        if (is_object($mResult) AND $mResult instanceof PDODecorator) {
            return $this;
        }
        return $mResult;
    }

    /**
     *  Deep clone
     **/
    public function __clone()
    {
        $this->PDODecorator = clone $this->PDODecorator;
    }

    /**
     *  @function create
     **/
    public function create($aData=null)
    {
        $this->setPDOData();
        $aData = Helper::TOOP(null === $aData, Context::getInst()->HttpRequest->getP(), $aData);
        $this->aData = array();
        foreach ($aData as $sField => $mValue) {
            $sRealField = Arr::get($this->_map, $sField, $sField);
            $this->aData[$sRealField] = $mValue;
        }
        return true;
    }

    /*
     * Set PDO Table
     */
    public function setPDOData()
    {
        $this->PDODecorator->sTable = $this->sTable;
    }

    /**
     *  Auto Validator
     **/
    public function validator($iModel = null)
    {
        $this->setPDOData();
        $aData = $aRule = $aMsg = array();
        #Get Validator
        $aTmpData = $this->aData;
        if(method_exists($this, '__setValidator__')) {
            $this->_validator = call_user_func_array(array($this, '__setValidator__'), array());
        }
        $this->aData = $aTmpData;

        if ($this->_validator) foreach ($this->_validator as $aValidator) {
            $iValidateModel = $aValidator[count($aValidator) - 1];
            if (!self::_checkRuleRun($iValidateModel, $iModel, $bEnvModel)) continue;
            $sField  = strtolower(trim(array_shift($aValidator)));
            $aData[$sField] = Arr::get($this->aData, $sField);
            if ($bEnvModel) array_pop($aValidator);
            if(!is_array($mErrMsg = array_pop($aValidator))){
                $aMsg[$sField][$aValidator[0]] = $mErrMsg;
            }else{
                $aMsg[$sField] = array_merge($aMsg[$sField], $mErrMsg);
            }
            if ('unique' == $aValidator[0]) {
                $cloneModel = clone $this;
                array_push(
                    $aValidator,
                    !$cloneModel->where(array($sField => $aData[$sField]))->findCount()
                );
            }
            array_unshift($aValidator, $sField);
            $aRule[] = $aValidator;
        }
        $Validator = new Validator($aData, $aRule, $aMsg);
        if(true !== ($mResult = $Validator->validate())){
            $this->_sErrMsg = $mResult;
        }
        return $mResult;
    }

    /**
     *  @function auto
     **/
    public function auto($iModel=null)
    {
        $this->setPDOData();
        #Get Auto
        if(method_exists($this, '__setAuto__')) {
            $this->_auto = call_user_func_array(array($this, '__setAuto__'), array());
        }
        if ($this->_auto) foreach ($this->_auto as $aAuto) {
            if(count($aAuto) < 4 ) $aAuto = array_pad($aAuto, 4, '');
            $sField     = $aAuto[0];
            $sFuncName  = $aAuto[1];
            $iModelEnv  = $aAuto[2];
            $sType      = strtolower($aAuto[3]);
            if (isset($this->aData[$sField]) AND $this->aData[$sField]) {
                continue;
            }
            if (!self::_checkRuleRun($iModelEnv, $iModel)) continue;
            if ($sType == 'function') {
                $this->aData[$sField] = $sFuncName(
                    Arr::get($this->aData, $sField)
                );
            }elseif($sType == 'callback'){
                $this->aData[$sField] = call_user_func_array(
                    array($this, $sFuncName),
                    array(Arr::get($this->aData, $sField))
                );
            }else{
                $this->aData[$sField] = $sFuncName;
            }
        }
    }

    /**
     *  check $_validator | $_auo Run
     **/
    private function _checkRuleRun(
        $_mRule = null,
        $iMode = null,
        &$bEnvModel = false
    ) {
        $this->setPDOData();
        $_aModel   = array(self::MODEL_INSERT, self::MODEL_UPDATE, self::MODEL_BOTH);
        $bEnvModel = in_array($_mRule, $_aModel);
        if ($bEnvModel AND
            $iMode !== $_mRule AND
            $_mRule !== self::MODEL_BOTH
        ){
            return false;
        }
        return true;
    }

    /**
     *  Get Validator ErrorMsg
     **/
    public function getError($sDefault=null)
    {
        return Helper::TOOP(
            empty($this->_sErrMsg) AND null !== $sDefault,
            $sDefault,
            $this->_sErrMsg
        );
    }
}
