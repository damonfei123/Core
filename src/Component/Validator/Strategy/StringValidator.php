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

class StringValidator extends AValidator{

    protected function isString()
    {
        return is_string($this->mValue);
    }
    protected function max()
    {
        $this->setMSet(Arr::get($this->aRule, 'max'));
        return isset($this->aRule['max']) AND $this->aRule['max'] >= strlen($this->mValue);
    }
    protected function min()
    {
        $this->setMSet(Arr::get($this->aRule, 'min'));
        return isset($this->aRule['min']) AND $this->aRule['min'] <= strlen($this->mValue);
    }

    public function validator()
    {
        //判断类型
        if (!$this->isString()) {
            return $this->fail('string', array(strlen($this->mValue)));
        }
        //判断大小
        if (!$this->max()) {
            return $this->fail('string.max', array(strlen($this->mValue)));
        }
        if (!$this->min()) {
            return $this->fail('string.min', array(strlen($this->mValue)));
        }
        return true;
    }
}
