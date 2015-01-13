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
namespace Hummer\Component\Log;

interface ILogger {

    /**
     *  Debug
     *  @param $sMessage Log Message
     *  @param $aContext Replace Context
     **/
    public function debug($sMessage, array $aContext);

    /**
     *  Warn
     *  @param $sMessage Log Message
     *  @param $aContext Replace Context
     **/
    public function warn($sMessage, array $aContext);

    /**
     *  Info
     *  @param $sMessage Log Message
     *  @param $aContext Replace Context
     **/
    public function info($sMessage, array $aContext);

    /**
     *  Notice
     *  @param $sMessage Log Message
     *  @param $aContext Replace Context
     **/
    public function Notice($sMessage, array $aContext);

    /**
     *  Error
     *  @param $sMessage Log Message
     *  @param $aContext Replace Context
     **/
    public function error($sMessage, array $aContext);

    /**
     *  Fatal
     *  @param $sMessage Log Message
     *  @param $aContext Replace Context
     **/
    public function fatal($sMessage, array $aContext);
}
