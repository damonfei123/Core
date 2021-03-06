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

use Hummer\Bundle\Framework\Bootstrap;
use Hummer\Component\Helper\Time;
use Hummer\Component\Filesystem\Dir;
use Hummer\Component\Log\LogFactory;
use Hummer\Component\Context\Context;

class FileStrategy implements IStrategy{

    /**
     *  @var $aData Log Container
     **/
    protected $aData = array();

    /**
     *  @var $sGUID
     **/
    protected $sGUID = null;

    /**
     *  @var $sFileFormat Log FileName Format
     **/
    protected $sFileFormat;

    /**
     *  @var $sContentFormat Log Content Format
     **/
    protected $sContentFormat;

    /**
     *  @var $bEnable Need Log Or Not
     **/
    protected $bEnable = true;

    /**
     *  @var $Instance Single
     **/
    protected static $Instance = null;

    /**
     *  Single Mode
     **/
    public static function getInstance(
        $sFileFormat,
        $sContentFormat=null,
        $sMonthFormat='Ym',
        $sDateFormat='Ymd'
    ) {
        if (null === self::$Instance) {
            self::$Instance = new self(
                $sFileFormat,
                $sContentFormat,
                $sMonthFormat,
                $sDateFormat
            );
        }
        return self::$Instance;
    }

    private function __construct(
        $sFileFormat,
        $sContentFormat=null,
        $sMonthFormat=null,
        $sDateFormat=null
    ) {
        $this->sMonthFormat = $sMonthFormat;
        $this->sDateFormat  = $sDateFormat;
        $this->sFileFormat  = $sFileFormat;
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

    public function writeIn($aRow)
    {
        if (!$this->bEnable) {
            return;
        }
        $sLevelName = LogFactory::getLogNameByLevelID($aRow['iLevel']);
        $sLogMsg = str_replace(
            array('{sGUID}', '{iLevel}', '{sTime}', '{sContent}'),
            array($this->sGUID, $sLevelName, $aRow['sTime'], $aRow['sMessage']),
            $this->sContentFormat
        ) . PHP_EOL;

        #Add to queue
        $this->aData[$sLevelName][] = $sLogMsg;
        Context::getInst()->sRunMode == Bootstrap::S_RUN_CLI && $this->flush();
    }

    public function setGUID($sGUID)
    {
        #GUID should be same for one request
        $this->sGUID  = $sGUID;
    }

    /**
     *  END
     *  Flush log to file
     **/
    public function __destruct()
    {
        $this->flush();
    }
    public function flush()
    {
        $sDate  = Time::time(null,$this->sDateFormat);
        $sMonth = Time::time(null,$this->sMonthFormat);
        foreach ($this->aData as $sLevelName => $aContent) {
            $sFilePath = str_replace(
                array('{level}', '{date}', '{month}'),
                array($sLevelName, $sDate, $sMonth),
                $this->sFileFormat
            );
            if(!Dir::makeDir($sDirPath=dirname($sFilePath))){
                throw new \RuntimeException('[Log] : Make Dir Error, Dir Path : ' . $sDirPath);
            }
            file_put_contents(
                $sFilePath ,
                sprintf('%s[%s]%s%s',PHP_EOL, $this->sGUID, PHP_EOL,implode('',$aContent)),
                FILE_APPEND|LOCK_EX
            );
            unset($this->aData[$sLevelName]);
        }
    }
}
