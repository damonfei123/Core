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

class NumberValidator extends AbstractValidator{

    public function validator()
    {
        return is_numeric($this->mValue) ? true : $this->fail('number');
    }
}
