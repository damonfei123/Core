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

class DateValidator extends AbstractValidator{

    /**
     *  date format, extend for later
     **/
    private $aDateFormateValidate = array(
        'Y-m-d',
        'y-m-d',
        'Y-m-d H:i:s',
        'y-m-d H:i:s',
        'Y/m/d',
        'y/m/d',
        'Y/m/d H:i:s',
        'y/m/d H:i:s',
    );

    public function validator()
    {
        if(!$mDateFormateValidate=array_shift($this->aRule)) {
            $mDateFormateValidate = $this->aDateFormateValidate;
        }
        if (!is_array($mDateFormateValidate)) {
            $mDateFormateValidate = (array)$mDateFormateValidate;
        }
        foreach ($mDateFormateValidate as $sFormatDate) {
            if (date($sFormatDate, strtotime($this->mValue)) == $this->mValue) {
                return true;
            }
        }
        return $this->fail('date');
    }
}
