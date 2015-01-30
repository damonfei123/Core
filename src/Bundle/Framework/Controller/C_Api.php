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
namespace Hummer\Bundle\Framework\Controller;

use Hummer\Component\Context\Context;

class C_Api extends C_Web_TBase{

    /**
     *  @var $sRetType
     **/
    protected $sRetType = 'json';

    /**
     *  @var Key Status
     **/
    protected $sKeyStatus = 'status';

    /**
     *  @var Key sMsg
     **/
    protected $sKeySMsg = 'sMsg';

    /**
     *  @var Key aData
     **/
    protected $sKeyAData = 'aData';

    public function __set($sKey, $mV)
    {
        $this->$sKey = $mV;
    }

    /**
     *  @var $aData
     **/
    protected $aData;

    public function setRetType($sType)
    {
        $this->sRetType = $sType;
    }

    public function success($iStatus=0, $sMsg='', $aData=array())
    {
        $this->aData = array(
            $this->sKeyStatus => $iStatus,
            $this->sKeySMsg   => $sMsg,
            $this->sKeyAData  => $aData,
        );
    }

    public function error($iStatus=-1, $sMsg='', $aData=array())
    {
        $this->aData = array(
            $this->sKeyStatus => $iStatus,
            $this->sKeySMsg   => $sMsg,
            $this->sKeyAData  => $aData,
        );
    }

    /**
     *  Json
     **/
    public function _renderJSON()
    {
        return json_encode($this->aData);
    }

    public function __after__()
    {
        #disable WebLog
        foreach ($this->Log->aWriter as $aWriter) {
            if ($aWriter instanceof \Hummer\Component\Log\Writer\WebPageStrategy){
                $aWriter->disable();
            }
        }
        $sMethod = sprintf('_render%s', strtoupper($this->sRetType));
        $this->HttpResponse->setHeader('Content-Type', 'text/javascript', false);
        $this->HttpResponse->setContent($this->$sMethod());
    }
}
