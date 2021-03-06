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
namespace Hummer\Component\Helper;

class Time {

    /**
     *  Change Time To Readable
     **/
    public static function time($iSec=null, $sFormat='Y-m-d H:i:s')
    {
        return date($sFormat, $iSec === null ? time() : $iSec);
    }

    /**
     *  Change To humman Readable Time by timestamp
     **/
    public static function humanTime($iMicsecond)
    {
        if ((int)$iMicsecond < 1000) {
            return sprintf('%s%s', $iMicsecond, 'ms');
        }
        return gmstrftime('%H时%M分%S秒', $iMicsecond/1000);
    }
}
