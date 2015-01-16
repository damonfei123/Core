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
namespace Hummer\Component\Route;

class Route{

    /**
     *  @var $Context
     **/
    protected $Context;

    function __construct($Context=null) {
        $this->Context = $Context;
    }

    /**
     *  RUN FOR HTTP
     *  @param $REQ     HttpRequest
     *  @param $RES     HttpResponse
     *  @param $aRule   array
     **/
    public function generateFromHttp($REQ, $RES, $aRule=array())
    {
        $aCallBack = array();
        if ($aRule AND is_array($aRule)) {
            foreach ($aRule as $aV) {
                if (is_array($aV) AND count($aV) >= 4) {
                    $mV              = array_shift($aV);
                    $sControllerPath = array_shift($aV);
                    $sControllerPre  = array_shift($aV);
                    $sActionPre      = array_shift($aV);
                    $aCallBack[] = call_user_func_array(
                        $mV,
                        array($REQ, $RES, $sControllerPath, $sControllerPre, $sActionPre)
                    );
                }else{
                    throw new \DomainException('[Route] : ERROR CONFIG');
                }
            }
        }else{
            throw new \InvalidArgumentException('[Route] : ERROR ROUTE PARAM');
        }
        return $aCallBack;
    }

    /**
     *  RUN FOR CLI
     *  @param $argv    $argv
     *  @param $aRule   array
     **/
    public function generateFromCli($aArgv, $aRule=array())
    {
        $aCallBack = array();
        if ($aRule AND is_array($aRule)) {
            foreach ($aRule as $aV) {
                if (is_array($aV) AND count($aV) >= 4) {
                    $mV              = array_shift($aV);
                    $sControllerPath = array_shift($aV);
                    $sControllerPre  = array_shift($aV);
                    $sActionPre      = array_shift($aV);
                    $aCallBack[] = call_user_func_array(
                        $mV,
                        array($aArgv, $sControllerPath, $sControllerPre, $sActionPre)
                    );
                }else{
                    throw new \DomainException('[Route] : ERROR CONFIG');
                }
            }
        }else{
            throw new \InvalidArgumentException('[Route] : ERROR ROUTE PARAM');
        }
        return $aCallBack;
    }
}
