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
namespace Hummer\Framework;

use Hummer\Component\Route\Route;
use Hummer\Component\RDS\Factory;
use Hummer\Component\Helper\Helper;
use Hummer\Component\Context\Context;
use Hummer\Component\Http\HttpRequest;
use Hummer\Component\Http\HttpResponse;
use Hummer\Component\Route\RouteErrorException;
use Hummer\Component\Context\InvalidClassException;

class Bootstrap{

    const S_RUN_CLI  = 'cli';
    const S_RUN_HTTP = 'http';

    private static $mCBUncaughtException = array(
        'Hummer\\Framework\\Bootstrap',
        'handleUnCaughtError'
    );

    public function __construct(
        $Configure,
        $sEnv = null
    ) {
        Context::makeInst();
        $this->Context = Context::getInst();
        $aRegisterMap = array(
            'Config'    => $Configure,
            'sEnv'      => $sEnv,
            'Arr'       => array(),
            'Route'     => new Route($this->Context),
            'sRunMode'  => Helper::TOOP(
                strtolower(PHP_SAPI) === self::S_RUN_CLI,
                self::S_RUN_CLI,
                self::S_RUN_HTTP
            )
        );

        if ($aRegisterMap['sRunMode'] == self::S_RUN_HTTP) {
            $aRegisterMap['HttpRequest']  = new HttpRequest();
            $aRegisterMap['HttpResponse'] = new HttpResponse();
        }elseif(self::S_RUN_CLI == $aRegisterMap['sRunMode']){
            $aRegisterMap['aArgv'] = $GLOBALS['argv'];
        }
        $this->Context->registerMulti($aRegisterMap);
    }

    public static function setHandle(
        $mCBErrorHandle = array('Hummer\\Framework\\Bootstrap', 'handleError'),
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
        $C = Context::getInst();
        if ($C === null || !$C->isRegister('Log')) {
            trigger_error($sStr, E_USER_WARNING);
        }else{
            switch ($iErrNum)
            {
                case E_NOTICE:
                case E_USER_NOTICE:
                    $C->Log->notice($sStr);
                    break;
                case E_USER_ERROR:
                    $C->Log->error($sStr);
                    throw new \ErrorException('[Bootstrap] : Error' . $sStr);
                    break;
                case E_USER_WARNING:
                default:
                    $C->Log->warn($sStr);
                    break;
            }
        }
    }

    public static function handleUnCaughtError(\Exception $E)
    {
        $CTX  = Context::getInst();
        $sStr = $E->getMessage();
        if ($CTX === null || !$CTX->isRegister('Log')) {
            trigger_error($sStr, E_USER_ERROR);
            return;
        }
        $CTX->Log->error($sStr);
        if ($CTX->sRunMode === self::S_RUN_HTTP) {
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
        $CTX = Context::getInst();
        if ($CTX->sRunMode !== self::S_RUN_HTTP) {
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

    public function run($sRouteKey=null)
    {
        $sTS = microtime(true);
        $C   = $this->Context;
        $Log = $C->Log;
        $Log->info('RUN START');
        try{
            switch ($C->sRunMode)
            {
                case self::S_RUN_HTTP:
                    $aCallBack = $C->Route->generateFromHttp(
                        $C->HttpRequest,
                        $C->HttpResponse,
                        $C->Config->get($sRouteKey === null ? 'route.http' : $sRouteKey)
                    );
                    foreach ($aCallBack as $CallBack) {
                        $CallBack->call();
                    }
                    #send header & content
                    $C->HttpResponse->send();
                    break;
                case self::S_RUN_CLI:
                    $aCallBack = $C->Route->generateFromCli(
                        $C->aArgv,
                        $C->Config->get($sRouteKey === null ? 'route.cli' : $sRouteKey)
                    );
                    foreach ($aCallBack as $CallBack) {
                        $CallBack->call();
                    }
                    break;
                default:
                    throw new \RuntimeException('[Bootstrap] : ERROR RUN MODE');
            }
        }catch(InvalidClassException $E){
            $Log->fatal($E->getMessage());
            call_user_func(self::$mCBUncaughtException, $E);
            exit(1);
        }catch(\SmartyException $E){
            #Smarty Exception
            $Log->warn($E->getMessage());
        }catch(RouteErrorException $E){
            #Route Error
            $C->HttpResponse->setStatus(404);
            $C->HttpResponse->setContent($C->Template->fetch($C->Config->get('syspage.404')));
            $C->HttpResponse->send();
            $Log->fatal($E->getMessage());
        }catch(\Exception $E){
            #Uncatch Error
            $Log->warn($E->getMessage());
            call_user_func(self::$mCBUncaughtException, $E);
            exit(1);
        }

        $Log->info(sprintf('RUN END: Time: %s, Mem: %s',
            self::humanTime(round(microtime(true) - $sTS, 6) * 1000),
            Helper::Mem()
        ));
    }

    private static function humanTime($iMicsecond)
    {
        if ((int)$iMicsecond < 1000) {
            return sprintf('%s%s', $iMicsecond, 'ms');
        }
        return gmstrftime('%H时%M分%S秒', $iMicsecond/1000);
    }
}
