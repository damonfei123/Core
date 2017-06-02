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

    /**
     *  @Rule
     *  array('key')
     *  array('key', array('Y-m')) or array('key', 'Y-m')   only validate for array('Y-m')
     *  array('key', array('Y-m'), 1)  validate for array('Y-m') + $this->aDateFormateValidate
     **/
    public function validator()
    {
        if(!$mDateFormateValidate=(array)array_shift($this->aRule)) {
            $mDateFormateValidate = $this->aDateFormateValidate;
        }
        array_shift($this->aRule) && ($mDateFormateValidate=array_merge($mDateFormateValidate, $this->aDateFormateValidate));
        foreach (array_unique($mDateFormateValidate) as $sFormatDate) {
            if (date($sFormatDate, strtotime($this->mValue)) == $this->mValue) {
                return true;
            }
        }
        return $this->fail('date');
    }
}
