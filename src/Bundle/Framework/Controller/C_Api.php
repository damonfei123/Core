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

use Hummer\Component\Helper\Arr;
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
        die($this->__after__());
    }

    public function error($iStatus=-1, $sMsg='', $aData=array())
    {
        $this->aData = array(
            $this->sKeyStatus => $iStatus,
            $this->sKeySMsg   => $sMsg,
            $this->sKeyAData  => $aData,
        );
        die($this->__after__());
    }

    /**
     *  Json
     **/
    public function _renderJSON()
    {
        return json_encode($this->aData);
    }

    /**
     *  XML
     **/
    public function _renderXML()
    {
        return Arr::Arr2XML(Arr::get($this->aData, $this->sKeyAData));
    }

    public function __after__()
    {
        /*
        #disable WebLog
        foreach ($this->Log->aWriter as $aWriter) {
            if ($aWriter instanceof \Hummer\Component\Log\Writer\WebPageStrategy){
                $aWriter->disable();
            }
        }
        $this->HttpResponse->setHeader('Content-Type', 'text/javascript', false);
        */
        $sMethod = sprintf('_render%s', strtoupper($this->sRetType));
        $this->HttpResponse->setContent($this->$sMethod());
        $this->HttpResponse->send();
    }
}
