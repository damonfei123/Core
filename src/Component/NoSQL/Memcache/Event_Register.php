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
namespace Hummer\Component\NoSQL\Memcache;

use Hummer\Component\Event\Event;
use Hummer\Component\Context\Context;

class Event_Register{

    const E_ALL_BEFORE = 'Hummer.Component.Memcache.Memcache.__ALL__:before';
    const E_ALL_AFTER  = 'Hummer.Component.Memcache.Memcache.__ALL__:after';
    const E_REDIS_MODE = 'Hummer.Component.Memcache.Event_Register:time';

    public static function register_All($bEvent=true)
    {
        if ($bEvent) {
            Event::register(
                self::E_ALL_BEFORE,
                function($sMethodName, $aArgs=array()){
                    Context::getInst()->Arr[self::E_REDIS_MODE] = microtime(true);
                }
            );
            Event::register(
                self::E_ALL_AFTER,
                function($mResult, $sMethodName, $aArgs=array()){
                    $Log   = Context::getInst()->Log;
                    $iCost = @round(microtime(true) - Context::getInst()->Arr[self::E_REDIS_MODE], 6);
                    $Log->info(
                        '[Memcache] : Time : {cost}; cmd : {cmd}; Args : {args}',
                        array(
                            'cost' => $iCost,
                            'args' => json_encode($aArgs),
                            'cmd'  => $sMethodName
                        )
                    );
                }
            );
        }
    }
}
