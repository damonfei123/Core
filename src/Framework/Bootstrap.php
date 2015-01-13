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
use Hummer\Component\Helper\Time;
use Hummer\Component\Helper\Helper;
use Hummer\Component\Context\Context;
use Hummer\Component\RDB\ORM\Factory;
use Hummer\Component\Http\HttpRequest;
use Hummer\Component\Http\HttpResponse;
use Hummer\Component\Route\RouteErrorException;
use Hummer\Component\Context\InvalidClassException;

class Bootstrap{

    /**
     *  @var S_RUN_CLI Run PHP WITH Cli
     **/
    const S_RUN_CLI  = 'cli';

    /**
     *  @var S_RUN_HTTP Run PHP With Http
     **/
    const S_RUN_HTTP = 'http';

    /**
     *  UnCaught Error
     **/
    private static $mCBUncaughtException = array(
        'Hummer\\Framework\\Handle',
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
        #Register
        $this->Context->registerMulti($aRegisterMap);
    }

    public function run($sRouteKey=null)
    {
        $sTS = microtime(true);
        $this->Context->Log->info('RUN START');
        try{
            $this->Context->sRunMode === self::S_RUN_HTTP ?
                $this->runHttp($sRouteKey) :
                $this->runCli($sRouteKey);
        }catch(InvalidClassException $E){
            $this->Context->Log->fatal($E->getMessage());
            call_user_func(self::$mCBUncaughtException, $E);
            exit(1);
        }catch(\SmartyException $E){
            #Smarty Exception
            $this->Context->Log->warn($E->getMessage());
        }catch(RouteErrorException $E){
            #Route Error
            $this->Context->HttpResponse->setStatus(404);
            $this->Context->HttpResponse->setContent(
                $this->Context->Template->fetch($this->Context->Config->get('syspage.404'))
            );
            $this->Context->HttpResponse->send();
            $this->Context->Log->fatal($E->getMessage());
        }catch(\Exception $E){
            #Uncatch Error
            $this->Context->Log->warn($E->getMessage());
            call_user_func(self::$mCBUncaughtException, $E);
            exit(1);
        }
        #End Log
        $this->Context->Log->info(sprintf('RUN END: Time: %s, Mem: %s',
            Time::humanTime(round(microtime(true) - $sTS, 6) * 1000),
            Helper::Mem()
        ));
    }

    /**
     *  Route Run Mode With Cli
     **/
    protected function runHttp($sRouteKey=null)
    {
        $aCallBack = $this->Context->Route->generateFromHttp(
            $this->Context->HttpRequest,
            $this->Context->HttpResponse,
            $this->Context->Config->get($sRouteKey === null ? 'route.http' : $sRouteKey)
        );
        foreach ($aCallBack as $CallBack) {
            $CallBack->call();
        }
        #send header & content
        $this->Context->HttpResponse->send();
    }

    /**
     *  Route Run Mode With Cli
     **/
    protected function runCli($sRouteKey=null)
    {
        $aCallBack = $this->Context->Route->generateFromCli(
            $this->Context->aArgv,
            $this->Context->Config->get($sRouteKey === null ? 'route.cli' : $sRouteKey)
        );
        foreach ($aCallBack as $CallBack) {
            $CallBack->call();
        }
    }

    public static function setHandle(
        $mCBErrorHandle = array('Hummer\\Framework\\Handle', 'handleError'),
        $iErrType = null,
        $mCBUncaughtException = null
    ) {
        Handle::setHandle($mCBErrorHandle, $iErrType, $mCBUncaughtException);
    }

    public static function setDefaultErrorPage()
    {
        Handle::setDefaultErrorPage();
    }
}
