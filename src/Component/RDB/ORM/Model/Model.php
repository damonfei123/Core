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
    }

    public function initModel($sModelName)
    {
        $this->PDODecorator->resetCondition();
        $this->setTable($sModelName);
    }

    public function setTable($sModelName)
    {
        $sTable = Arr::get($this->aConfig, 'table', strtolower($sModelName));
        $this->PDODecorator->table($sTable);
    }

    /**
     *  Find One
     *  @param $mWhere mix(string|array) Where Condition
     *  @param $bAssoc boolean           Return Obj Or Assoc
     **/
    public function find($mWhere=null, $bAssoc=false)
    {
        $aItem = $this->PDODecorator->limit(1)->querySmarty($mWhere);
        return empty($aItem) ? null : (
            $bAssoc ? array_shift($aItem) :
            new $this->sItemClassName(array_shift($aItem), $this)
        );
    }

    public function findCustom($mWhere=null)
    {
        return $this->PDODecorator->querySmarty($mWhere);
    }

    public function findMulti($mWhere=null)
    {
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
}
