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

class C_Web extends C_Web_TBase{

    public function __destruct()
    {
        if (self::$bEnableTpl &&
            !$this->bCalledDisplay &&
            !$this->HttpRequest->isAjax() &&
            $this->HttpRequest->getRequestMethod() === 'GET'
        ) {
            $this->display();
        }
    }
}
