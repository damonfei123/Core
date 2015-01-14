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
namespace Hummer\Component\Log\Writer;

use Hummer\Component\Helper\Dir;
use Hummer\Component\Helper\Time;
use Hummer\Component\Log\LogFactory;

class STDIOStrategy implements IStrategy{

    protected $aData = array();
    protected $sGUID = null;

    protected $sFileFormat;
    protected $sContentFormat;
    protected $bEnable = true;

    /**
     *  @var $Instance Single
     **/
    protected static $Instance = null;

    /**
     *  Single Mode
     **/
    public static function getInstance($sContentFormat=null) {
        if (null === self::$Instance) {
            self::$Instance = new self($sContentFormat);
        }
        return self::$Instance;
    }

    private function __construct($sContentFormat=null) {
        $this->sContentFormat = is_null($sContentFormat) ?
            '[{iLevel}] : {sTime} : {sContent}' :
            $sContentFormat;
    }

    public function setDisable()
    {
        $this->bEnable = false;
    }
    public function setEnable()
    {
        $this->bEnable = true;
    }

    public function acceptData($aRow)
    {
        if ($this->bEnable) {
            $sLevelName = LogFactory::getLogNameByLevelID($aRow['iLevel']);
            $sLogMsg = str_replace(
                array('{sGUID}', '{iLevel}', '{sTime}', '{sContent}'),
                array($this->sGUID, $sLevelName, $aRow['sTime'], $aRow['sMessage']),
                $this->sContentFormat
            ) . PHP_EOL;

            #flush to STDIO
            fprintf(STDOUT, sprintf('%s', self::handle($aRow['iLevel'], $sLogMsg)), null);
        }
    }

    public function setGUID($sGUID)
    {
        #GUID should be same for one request
        $this->sGUID  = $sGUID;
    }

    public static function handle($iLevel , $sMsg)
    {
        if (($iLevel & LogFactory::LEVEL_DEBUG) ||
            ($iLevel & LogFactory::LEVEL_INFO)
        ) {
            return $sMsg;
        }
        return sprintf("\033[41;90m %s \033[0m", $sMsg);
    }
}
