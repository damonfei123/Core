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
namespace Hummer\Util\Image;

use Hummer\Component\Helper\Helper;

class Verify{

    /**
     *  @var $sCharset
     **/
    private $sCharset;

    /**
     *  @var $sCode
     **/
    private $sCode;

    /**
     *  @var $iCodeLen
     **/
    private $iCodeLen;

    /**
     *  @var $iWidth Verify Img Width
     **/
    private $iWidth;

    /**
     *  @var $iHeight Verify Img Height
     **/
    private $iHeight;

    /**
     *  @var $Img Source
     **/
    private $Img;

    /**
     *  @sFont
     **/
    private $sFont;

    /**
     *  @var $iFontSize Font Size
     **/
    private $iFontSize;

    /**
     *  @var $sFontColor Font Color
     **/
    private $sFontColor;

    /**
     *  @var $sCodeType
     **/
    private $sCodeType;

    /**
     *  @var $iLineCount
     **/
    private $iLineCount = 6;

    /**
     *  @var $iStringNum
     **/
    private $iStringNum = 60;

    /**
     *  @var $sStringPad
     **/
    private $sStringPad = '*';


    public function __construct(
        $iCodeLen  = null,
        $iCodeType = null,
        $iWidth    = null,
        $iHeight   = null,
        $iFontSize = null,
        $sFont     = null
    ) {
        $this->iCodeLen  = Helper::TOOP($iCodeLen, $iCodeLen, 4);
        $this->iWidth    = Helper::TOOP($iWidth, $iWidth, 130);
        $this->iHeight   = Helper::TOOP($iHeight, $iHeight, 50);
        $this->iFontSize = Helper::TOOP($iFontSize, $iFontSize, 20);
        $this->iCodeType = Helper::TOOP($iCodeType, $iCodeType, 1);
        $this->sFont     = Helper::TOOP($sFont , $sFont , dirname(__FILE__).'/font/timesi.ttf');
        $this->initCharset($this->iCodeType);
    }

    public function create() {
        $this->createBg();
        $this->createCode();
        $this->createLine();
        $this->createFont();
        $this->outPut();
    }

    /**
     *  Public Get Created Code
     **/
    public function getCode() {
        return $this->sCode;
    }

    public function setCodeLen($iCodeLen)
    {
        $this->iCodeLen = $iCodeLen;
    }

    public function setWidth($iWidth)
    {
        $this->iWidth = $iWidth;
    }

    public function setHeight($iHeight)
    {
        $this->iHeight = $iHeight;
    }

    public function setFont($sFontPath)
    {
        $this->sFont = $sFontPath;
    }

    public function setFontSize($iFontSize)
    {
        $this->iFontSize = $iFontSize;
    }

    public function setCodeType($iCodeType)
    {
        $this->iCodeType = $iCodeType;
        $this->initCharset($this->iCodeType);
    }

    public function setLineCount($iLineCount)
    {
        $this->iLineCount = $iLineCount;
    }

    public function setStringNum($iStringNum)
    {
        $this->iStringNum = $iStringNum;
    }

    public function setStringPad($sStringPad)
    {
        $this->sStringPad = $sStringPad;
    }

    public function initCharset($iCodeType)
    {
        $sChar = 'abcdefghkmnprstuvwxyzABCDEFGHKMNPRSTUVWXYZ';
        $sNum  = '23456789';
        switch ($iCodeType)
        {
            case 1:
                $sCharset = $sChar;
                break;
            case 2:
                $sCharset = $sNum;
                break;
            default:
                $sCharset = sprintf('%s%s', $sChar, $sNum);
                break;
        }
        $this->sCharset = $sCharset;
    }

    /**
     *  Create Code By Random
     **/
    private function createCode() {
        $_len = strlen($this->sCharset)-1;
        for ($i=0; $i < $this->iCodeLen; $i++) {
            $this->sCode .= $this->sCharset[mt_rand(0,$_len)];
        }
    }

    private function createBg() {
        $this->Img = imagecreatetruecolor($this->iWidth, $this->iHeight);
        $color     = imagecolorallocate(
            $this->Img,
            mt_rand(157,255),
            mt_rand(157,255),
            mt_rand(157,255)
        );
        imagefilledrectangle($this->Img, 0, $this->iHeight, $this->iWidth , 0, $color);
    }

    private function createFont() {
        $_x = $this->iWidth / $this->iCodeLen;
        for ($i = 0; $i < $this->iCodeLen; $i++) {
            $this->sFontColor = imagecolorallocate(
                $this->Img,
                mt_rand(0,156),
                mt_rand(0,156),
                mt_rand(0,156)
            );
            imagettftext(
                $this->Img,
                $this->iFontSize,
                mt_rand(-30,30),
                $_x*$i + mt_rand(1,5),
                $this->iHeight / 1.4,
                $this->sFontColor,
                $this->sFont,
                $this->sCode[$i]
            );
        }
    }

    private function createLine() {
        for ($i = 0; $i < $this->iLineCount; $i++) {
            $color = imagecolorallocate(
                $this->Img,
                mt_rand(0,156),
                mt_rand(0,156),
                mt_rand(0,156)
            );
            imageline(
                $this->Img,
                mt_rand(0,$this->iWidth),
                mt_rand(0,$this->iHeight),
                mt_rand(0,$this->iWidth),
                mt_rand(0,$this->iHeight),
                $color
            );
        }
        for ($i = 0; $i < $this->iStringNum; $i++) {
            $color = imagecolorallocate(
                $this->Img,
                mt_rand(100,255),
                mt_rand(100,255),
                mt_rand(100,255)
            );
            imagestring(
                $this->Img,
                mt_rand(1,5),
                mt_rand(0,$this->iWidth),
                mt_rand(0,$this->iHeight),
                $this->sStringPad,
                $color
            );
        }
    }

    private function outPut() {
        header('Content-type: image/png');
        imagepng($this->Img);
        imagedestroy($this->Img);
    }
}
