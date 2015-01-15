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
namespace Hummer\Bundle\Framework;

use Hummer\Component\Context\Context;

class Handle{

    /**
     *  Get Context
     **/
    public static function CTX()
    {
        return Context::getInst();
    }

    public static function setHandle(
        $mCBErrorHandle = array('Hummer\\Framework\\Handle', 'handleError'),
        $iErrType = null,
        $mCBUncaughtException = null
    ) {
        set_error_handler(
            $mCBErrorHandle,
            $iErrType === null ? (E_ALL | E_STRICT) : (int)$iErrType
        );
        if ($mCBUncaughtException !== null) {
            self::$mCBUncaughtException = $mCBUncaughtException;
        }
    }

    public static function handleError($iErrNum, $sErrStr, $sErrFile, $iErrLine, $sErrContext)
    {
        $sStr = sprintf('Catch Error[%d] : %s In File [%s], line %d ',
            $iErrNum,
            $sErrStr,
            $sErrFile,
            $iErrLine
        );
        $CTX = self::CTX();
        if ($CTX === null || !$CTX->isRegister('Log')) {
            trigger_error($sStr, E_USER_WARNING);
        }else{
            switch ($iErrNum)
            {
                case E_NOTICE:
                case E_USER_NOTICE:
                    $CTX->Log->notice($sStr);
                    break;
                case E_USER_ERROR:
                    $CTX->Log->error($sStr);
                    throw new \ErrorException('[Bootstrap] : Error' . $sStr);
                    break;
                case E_USER_WARNING:
                default:
                    $CTX->Log->warn($sStr);
                    break;
            }
        }
    }

    public static function handleUnCaughtError(\Exception $E)
    {
        $CTX = self::CTX();
        $sStr = $E->getMessage();
        if ($CTX === null || !$CTX->isRegister('Log')) {
            trigger_error($sStr, E_USER_ERROR);
            return;
        }
        $CTX->Log->error($sStr);
        if ($CTX->sRunMode === Bootstrap::S_RUN_HTTP) {
            if ($CTX->HttpResponse->getStatus() < 400) {
                $CTX->HttpResponse->setStatus(500);
            }
            if ($CTX->isRegister('mCBErrPage')) {
                call_user_func($CTX->mCBErrPage, $E);
            }
            $CTX->HttpResponse->send();
        }
        exit(1);
    }

    public static function setDefaultErrorPage()
    {
        $CTX = self::CTX();
        if ($CTX->sRunMode !== Bootstrap::S_RUN_HTTP) {
            return;
        }
        $RES = $CTX->HttpResponse;
        $CTX->register(
            'mCBErrPage',
            function (\Exception $E) use ($RES){
                $aArr = $E->getTrace();
                foreach ($aArr as $iK => $aItem) {
                    if (isset($aItem['args'])) {
                        unset($aItem[$iK]['args']);
                    }
                }
                $RES->setContent(
                    sprintf(
                        '<h1>%s</h1><h2>%d:%s</h2><h3>File:%s;Line:%s</h3><div><pre>%s</pre></div>',
                        get_class($E),
                        $E->getCode(),
                        $E->getMessage(),
                        $E->getFile(),
                        $E->getLine(),
                        print_r($aArr, true)
                    )
                );
            }
        );
    }
}
