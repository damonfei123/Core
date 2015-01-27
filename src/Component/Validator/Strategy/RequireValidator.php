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
namespace Hummer\Component\Validator\Strategy;

use Hummer\Component\Helper\Arr;
use Hummer\Component\Helper\Helper;

class RequireValidator extends AValidator{

    public function validator()
    {
        return $this->mValue || $this->mValue === 0 || $this->mValue === '0' ?
            true : $this->fail('require');
    }
}
