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
namespace Hummer\Util\Validator\Strategy;

use Hummer\Component\Helper\Arr;
use Hummer\Component\Helper\Helper;

class MobileValidator extends AbstractValidator{

    public function validator()
    {
        array_push($this->aRule, '#^1\d{10}$#');
        $Regex = RegexValidator::getInstance(
            'regex',
            $this->sKey,
            $this->mValue,
            $this->aRule,
            $this->aMsg
        );
        return true === $Regex->validator() ? true : $this->fail('mobile');
    }
}
