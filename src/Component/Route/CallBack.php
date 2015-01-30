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
namespace Hummer\Component\Route;

use Hummer\Component\Helper\Arr;
use Hummer\Component\Context\Context;
use Hummer\Bundle\Framework\Controller\C_Web;
use Hummer\Component\Route\RouteErrorException;

class CallBack{

    /**
     *  @var Context \Hummer\Component\Context\Context
     **/
    protected $Context;

    /**
     *  @var mCallable
     **/
    protected $mCallable;

    public function __construct()
    {
        $this->Context = Context::getInst();
    }

    public function setCBObject($sControllerPath, $sAction, $aArgs=array())
    {
        $this->mCallable = array($sControllerPath, $sAction, $aArgs);
        return $this;
    }

    public function call()
    {
        #Disable Smarty Tpl
        C_Web::disableTpl();

        $mClassOrObject = $this->mCallable[0];
        if (is_string($mClassOrObject)) {
            if (!class_exists($mClassOrObject)) {
                throw new RouteErrorException(
                    sprintf('[class] : %s does not exsits', $mClassOrObject)
                );
            }
            $Ref = new \ReflectionClass($mClassOrObject);
            $this->mCallable[0] = $mClassOrObject = $Ref->newInstanceArgs();
            $sClassOrObjectName = is_object($mClassOrObject) ?
                get_class($mClassOrObject) :
                $mClassOrObject;
        }else{
            throw new \DomainException('[CallBack] : ERROR');
        }

        #get callable method
        $aCallableMethod = array();
        foreach ($Ref->getMethods(\ReflectionMethod::IS_PUBLIC) as $Method) {
            $aCallableMethod[$Method->getName()] = $Method->getName();
        }

        #get Method
        $sMethod = $this->mCallable[1];
        if (!isset($aCallableMethod[$sMethod])) {
            throw new RouteErrorException(sprintf('[ROUTE] : There is no method %s in class %s',
                $sMethod,
                $sClassOrObjectName
            ));
        }
        #Enable Smarty Tpl
        C_Web::enableTpl();

        #register
        $this->Context->registerMulti(array(
            'sControllerName' => $sClassOrObjectName,
            'sActionName'     => $this->mCallable[1],
        ));

        #aArgs
        $aArgs = (array)$this->mCallable[2];

        #before and after
        $sBefore = $sAfter = null;
        $sMethodBefore = sprintf('__before_%s__', $sMethod);
        $sMethodAfter  = sprintf('__after_%s__', $sMethod);
        $sBefore = Arr::get($aCallableMethod, $sMethodBefore, '__before__');
        $sAfter  = Arr::get($aCallableMethod, $sMethodAfter, '__after__');
        $sBefore = Arr::get($aCallableMethod, $sBefore);
        $sAfter  = Arr::get($aCallableMethod, $sAfter);

        $bContinue = true;
        if ($sBefore !== null) {
            $bContinue = call_user_func(array($mClassOrObject, $sBefore), $aArgs);
        }
        if ($bContinue !== false) {
            $bContinue = call_user_func(array(
                $this->mCallable[0],
                $this->mCallable[1]
            ), $aArgs);
        }
        if ($bContinue !== false && $sAfter !== null) {
            call_user_func(array($mClassOrObject, $sAfter), $aArgs);
        }
    }
}
