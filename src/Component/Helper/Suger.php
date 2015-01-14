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
namespace Hummer\Component\Helper;

class Suger{

    public static function createObj($sClassName, array $aArgv = array())
    {
        if (empty($aArgv)) {
            return new $sClassName();
        }else{
            $Ref = new \ReflectionClass($sClassName);
            return $Ref->newInstanceArgs($aArgv);
        }
    }

    /**
     *  Create Obj By Single
     **/
    public static function createSingle(
        $sClassName,
        array $aArgv = array(),
        $sMethodInstance = null
    ){
        return call_user_func_array(array(
            $sClassName,
            Helper::TOOP($sMethodInstance, $sMethodInstance, 'getInstance')
        ), $aArgv);
    }

    /**
     *  Create Obj By New
     **/
    public static function createObjAdaptor(
        $sNS,
        array $aClassAndArgs = array(),
        $sAdaptorClassPre  = 'Adaptor_',
        $sAdaptorClassTail = ''
    ) {
        if (empty($aClassAndArgs)) {
            throw new \InvalidArgumentException('[Suger] : Error');
        }
        $sClassName = array_shift($aClassAndArgs);
        if ($sClassName[0] === '@') {
            $sClassName = sprintf('%s%s%s%s%s',
                $sNS, '\\', $sAdaptorClassPre, substr($sClassName, 1), $sAdaptorClassTail
            );
        }
        return self::createObj($sClassName, $aClassAndArgs) ;
    }

    /**
     *  Create Obj By Single
     **/
    public static function createObjSingle(
        $sNS,
        array $aClassAndArgs = array(),
        $sAdaptorClassPre  = 'Adaptor_',
        $sAdaptorClassTail = '',
        $sMethodInstance   = null
    ) {
        if (empty($aClassAndArgs)) {
            throw new \InvalidArgumentException('[Suger] : Error');
        }
        $sClassName = array_shift($aClassAndArgs);
        if ($sClassName[0] === '@') {
            $sClassName = sprintf('%s%s%s%s%s',
                $sNS, '\\', $sAdaptorClassPre, substr($sClassName, 1), $sAdaptorClassTail
            );
        }
        return self::createSingle($sClassName, $aClassAndArgs);
    }
}
