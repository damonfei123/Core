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

use Hummer\Component\Helper\Time;
use Hummer\Component\Filesystem\Dir;
use Hummer\Component\Log\LogFactory;

class NullStrategy implements IStrategy{

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
        return true;
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
    }
}
