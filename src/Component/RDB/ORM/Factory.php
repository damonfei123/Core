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
namespace Hummer\Component\RDB\ORM;

use Hummer\Component\Helper\Arr;
use Hummer\Component\Helper\Suger;
use Hummer\Component\Helper\Helper;
use Hummer\Component\RDB\ORM\PDODecorator;
use Hummer\Component\Context\InvalidArgumentException;

class Factory {

    /**
     *  @var $_aDBConfig All Database config
     **/
    private static $_aDBConfig;

    /**
     * @var $_aAopCallBack
     **/
    private static $_aAopCallBack;

    /**
     * @var $_aModelConf model config
     **/
    private static $_aModelConf;

    /**
     *  @var $_sAppModelNS APP namespace
     **/
    private static $_sAppModelNS;

    /**
     *  @var $_sDefaultModelClass Default Model
     **/
    private static $_sDefaultModelClass;

    /**
     *  @var $_sDefaultDB Default Database Config
     **/
    private $_sDefaultDB = 'default';

    public function __construct(
        array $aDBConfig,     //DB config
        array $aModelConfig,  //Model config
        $sAppModelNS        = '',
        $sDefaultModelClass = 'Hummer\\Component\\RDB\\ORM\\Model\\Model',
        $aAopCallBack = null
    ) {
        self::$_aDBConfig          = $aDBConfig;
        self::$_aModelConf         = $aModelConfig;
        self::$_aAopCallBack       = $aAopCallBack;
        self::$_sAppModelNS        = $sAppModelNS;
        self::$_sDefaultModelClass = $sDefaultModelClass;
    }

    public function __call($sModel, $aArgs=array())
    {
        if (($sModel = substr($sModel, 3)) !== false) {
            $aArgs   = (array)$aArgs;
            $sModel  = sprintf('%s %s', $sModel, array_shift($aArgs));
            return $this->get($sModel, array_shift($aArgs));
        }else{
            throw new \BadMethodCallException('[ Factory ] : Err : call undefined method');
        }
    }

    /**
     *  @var $_aPDODecorator All PDODecorator Object Cache
     **/
    private static $_aPDODecorator;

    /**
     *  @var $_aModel Model Cache
     **/
    private static $_aModel;

    /**
     *  @param $sModelName  string Model
     *      ex: user | user u
     **/
    public function get($sModelName, $sDB = '')
    {
        $sModelName = str_replace(' ', '|', Helper::TrimInValidURI(trim($sModelName), '  ', ' '));
        $sRealModel = self::getRealModel($sModelName);
        #config
        $aConf = Arr::get( self::$_aModelConf, $sRealModel, array() );
        $sDB   = Helper::TOOP($sDB, $sDB, Arr::get($aConf, 'db', $this->_sDefaultDB));
        $_sTmpModel = sprintf('%s_%s', $sDB, $sRealModel);

        $sModelClassName = isset($aConf['model_class']) ?
            sprintf('%s%s%s', self::$_sAppModelNS, '\\', $aConf['model_class']) :
            self::$_sDefaultModelClass;

        $Model = new $sModelClassName(
            $sModelName,
            $this->initPDODecorator($sModelName, $sDB),
            $aConf,
            $this
        );
        #init Model
        $Model->initModel($sModelName);
        #Return
        return $Model;
    }

    /**
     *  Parse Model, Get Real Model
     *  Ex:  user -> User | user u -> User
     **/
    public static function getRealModel($sModelName)
    {
        if (false !== ($iPos=strpos($sModelName, '|'))) {
            $sModelName = substr($sModelName, 0, $iPos);
        }
        return ucfirst($sModelName);
    }

    /**
     *  Init PDODecorator By Deferent Database
     **/
    public function initPDODecorator($sModelName, $sModelDB=null)
    {
        if (!isset(self::$_aPDODecorator[$sModelDB])) {
            if (array_key_exists($sModelDB, self::$_aDBConfig)) {
                self::$_aPDODecorator[$sModelDB] = Suger::createObjSingle(
                    __NAMESPACE__,
                    array(
                        '@PDODecorator',
                        self::$_aDBConfig[$sModelDB]['dsn'],
                        self::$_aDBConfig[$sModelDB]['username'],
                        self::$_aDBConfig[$sModelDB]['password'],
                        self::$_aDBConfig[$sModelDB]['option'],
                        self::$_aAopCallBack
                    ), ''
                );
            }else{
                throw new \InvalidArgumentException('[ FACTORY ] : NONE DB CONFIG');
            }
        }
        return self::$_aPDODecorator[$sModelDB];
    }

    /**
     *  Check Model Is Empty
     **/
    public function isModelDataEmpty($Model)
    {
        return $Model === null || (is_array($Model) && count($Model) == 0);
    }
}
