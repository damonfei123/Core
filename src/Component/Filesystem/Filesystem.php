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

class Filesystem{

    /**
     *  Create Dir
     **/
    public static function createDir($sDirName, $sPerm, $bRecursion)
    {
        return @mkdir($sDirName, $sPerm, $bRecursion);
    }

    /**
     *  Check If File Exists
     **/
    public static function Exists($sFilePath)
    {
        return $sFilePath And file_exists($sFilePath);
    }

    /**
     *  Check If File
     **/
    public static function isFile($sFilePath)
    {
        return $sFilePath AND is_file($sFilePath);
    }

    /**
     *  Check Is Dir
     **/
    public static function isDir($sDirName)
    {
        return $sDirName AND is_dir($sDirName);
    }

    /**
     *  Check Valid FileName
     **/
    public static function IsValidFileName($sFileName)
    {
        return $sFileName AND $sFileName != '.' AND $sFileName != '..';
    }
}
