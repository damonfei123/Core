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
namespace Hummer\Component\Http;

use Hummer\Component\Helper\Arr;

class Bag_Param extends Bag_Base{

    /**
     *  @var $aData Params
     **/
    protected $aData;

    public function get($mKeyOrKeys)
    {
        if (is_array($mKeyOrKeys)) {
            return array_intersect_key($this->aData, array_flip($mKeyOrKeys));
        }else{
            return Arr::get($this->aData, $mKeyOrKeys, null);
        }
    }

    public function set($mKeyOrKeys, $mValue=null)
    {
        if (is_array($mKeyOrKeys)) {
            $this->aData = array_merge($this->aData, $mKeyOrKeys);
        }else{
            $this->aData[$mKeyOrKeys] = $mValue;
        }
        return true;
    }

    /*
     *  check if isset
     */
    public function checkIsSet($mKey)
    {
        return array_key_exists($mKey, $this->aData);
    }

    public function All()
    {
        return $this->aData;
    }
}
