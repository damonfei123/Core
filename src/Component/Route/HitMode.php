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

use Hummer\Component\Helper\Arr;
use Hummer\Component\Helper\Helper;

class HitMode{

    const HitMode_GOON = 1;
    const HitMode_STOP = 2;

    public function __construct()
    {
        $this->setModeStop();
    }

    /**
     *  @var $iMode
     **/
    protected $iMode;

    public function getCurMode()
    {
        return $this->iMode;
    }

    public function setModeStop()
    {
        $this->iMode = self::HitMode_STOP;
    }

    public function setModeGOON()
    {
        $this->iMode = self::HitMode_GOON;
    }

    public function ifNeedGOON()
    {
        return $this->iMode == self::HitMode_GOON;
    }
}
