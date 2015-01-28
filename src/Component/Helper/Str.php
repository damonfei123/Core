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

class Str{

    public static function mblen($sStr='', $sCharset='utf8')
    {
        return mb_strlen($sStr, $sCharset);
    }

    /**
     *  Substr
     **/
    public static function sub($str, $length = 0, $sAppend = '...', $sCharset='utf8')
    {
        $str = trim($str);
        $strlength = strlen($str);
        if ($length == 0 || $length >= $strlength) {
            return $str;
        } elseif ($length < 0) {
            $length = $strlength + $length;
            if ($length < 0) {
                $length = $strlength;
            }
        }

        if (function_exists('mb_substr')) {
            $newstr = mb_substr($str, 0, $length, $sCharset);
        } elseif (function_exists('iconv_substr')) {
            $newstr = iconv_substr($str, 0, $length, $sCharset);
        } else {
            $newstr = substr($str, 0, $length);
        }

        if ($sAppend && $str != $newstr) {
            $newstr .= $sAppend;
        }
        return $newstr;
    }
}
