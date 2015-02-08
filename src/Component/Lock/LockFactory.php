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
namespace Hummer\Component\Lock;

use Hummer\Component\Helper\Suger;

class LockFactory {

    /**
     *  @var $Instance;
     **/
    private $Instance;

    public function __construct()
    {
        $this->Instance = Suger::createObjSingle(
            sprintf('%s\%s', __NAMESPACE__, 'Strategy'),
            func_get_args(),
            '',
            'Lock'
        );
    }

    public function __call($sMethod, $aArgs)
    {
        return call_user_func_array(array($this->Instance, $sMethod), $aArgs);
    }
}
