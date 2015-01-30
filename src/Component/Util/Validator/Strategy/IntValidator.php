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
namespace Hummer\Component\Util\Validator\Strategy;

use Hummer\Component\Helper\Arr;
use Hummer\Component\Helper\Helper;

class IntValidator extends AbstractValidator{

    protected function isInt()
    {
        return is_int($this->mValue);
    }
    protected function max()
    {
        $this->setMSet(Arr::get($this->aRule, 'max'));
        return isset($this->aRule['max']) AND $this->aRule['max'] >= $this->mValue;
    }
    protected function min()
    {
        $this->setMSet(Arr::get($this->aRule, 'min'));
        return isset($this->aRule['min']) AND $this->aRule['min'] <= $this->mValue;
    }

    public function validator()
    {
        //判断类型
        if (!$this->isInt()) {
            return $this->fail('int.int', array($this->mValue));
        }
        //判断大小
        if (!$this->max()) {
            return $this->fail('int.max', array($this->mValue));
        }
        if (!$this->min()) {
            return $this->fail('int.min', array($this->mValue));
        }
        return true;
    }
}
