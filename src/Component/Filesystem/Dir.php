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
namespace Hummer\Component\Filesystem;

class Dir extends Filesystem{

    public static function makeDir($sDirName, $sPerm=0777, $bRecursion=true)
    {
        if (!self::Exists($sDirName)) {
            return self::createDir($sDirName, $sPerm, $bRecursion);
        }
        return true;
    }

    /**
     *  Get Dir File
     *  @param $sDirName Dir Name
     *  @param $bRecursion Get Dir File By Recursion
     *  @param $aFile      Default File
     *  @return array | null
     **/
    public static function showList($sDirName, $bRecursion=false, &$aFile=array())
    {
        if (self::Check($sDirName)) {
            $Dir = opendir($sDirName);
            while ($sFileName=readdir($Dir)) {
                if (self::IsValidFileName($sFileName)) {
                    $sFilePath = sprintf('%s%s%s', $sDirName, DIRECTORY_SEPARATOR, $sFileName);
                    if (self::isDir($sFilePath) AND $bRecursion) {
                        self::showList($sFilePath, $bRecursion, $aFile);
                    }else{
                        array_push($aFile, $sFilePath);
                    }
                }
            }
            return $aFile;
        }
        return null;
    }

    /**
     *  Check Dir
     **/
    protected static function Check($sDirName){
        return self::isDir($sDirName) AND is_readable($sDirName);
    }
}
