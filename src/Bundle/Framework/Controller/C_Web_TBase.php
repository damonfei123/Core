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
use Hummer\Component\Helper\Helper;

class C_Web_TBase extends C_Base{

    public function __construct($sTpl='') {
        parent::__construct($sTpl);
        $this->HttpRequest  = $this->Context->HttpRequest;
        $this->HttpResponse = $this->Context->HttpResponse;
    }

    public function display($sTemplate=true)
    {
        $this->bCalledDisplay = true;
        if ($sTemplate) {
            $this->template->display(
                $this->getTplPath($this->HttpRequest, $sTemplate,$this->sTpl)
            );
        }
    }

    public function fetch($sTemplate='')
    {
        $sContent = '';
        if ($sTemplate OR $sTemplate === '') {
            $sContent = $this->template->fetch(
                $this->getTplPath($this->HttpRequest, $sTemplate,  $this->sTpl)
            );
        }
        return $sContent;
    }

    /**
     *  @param $REQ        {HttpRequest}
     *  @param $sTemplate  {string}
     *      ex: xx | /xx | /xx/xx/xx
     *  @param $sTpl       {string}
     *  @return {string}
     **/
    public static function getTplPath($REQ, $sTemplate=null, $sTpl)
    {
        if ($sTemplate && '/' == $sTemplate[0]) {
            $sTemplate = sprintf('%s.%s', substr($sTemplate, 1), $sTpl);
        }else{
            $sURL      = Helper::TrimInValidURI(
                Arr::get(parse_url($REQ->getSmartyRequestURI()),'path','')
            );
            $aURLPATH  = explode('/', strtolower(substr($sURL,1)));
            $sTplFile  = array_pop($aURLPATH);
            $sTplFile  = Helper::TOOP(
                $sTemplate AND is_string($sTemplate),
                $sTemplate,
                Helper::TOOP($sTplFile == '', 'default' , $sTplFile)
            );
            $sTplFile  = sprintf('%s.%s', Helper::ReplaceLineToUpper($sTplFile), $sTpl);
            $sTemplate = sprintf('%s%s%s',join('/', $aURLPATH),'/', $sTplFile);
        }
        return $sTemplate;
    }

    protected static $bEnableTpl = true;
    public static function disableTpl()
    {
        self::$bEnableTpl = false;
    }
    public static function enableTpl()
    {
        self::$bEnableTpl = true;
    }
}
