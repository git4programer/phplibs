<?php
/**
 * 中文截取字符串,无乱码
 * $string 原来的字符串
 * $length 需要截取的长度.
 * $start 开始截取时的位置.
 * $dot 截取后,后面补回的字符.
 */
function cut_str($string, $length, $start=0,$dot='') 
{
    $string = str_replace(array('&amp;', '&quot;', '&lt;', '&gt;'), array('&', '"', '<', '>'), $string);
    if(preg_match('/^.*$/u', $string) > 0){
    /*
     * utf-8 中文截取无乱码
     */
    if ($length >= mb_strlen($string)) {
            return $string;
        }
        $str = "";
        $i = $con = $start;
        while ($con < $length) {
            if (ord(substr($string, $i, 1)) >= 128) {
                $str .= substr($string, $i, 3);
                $i += 3;
            } else {
                $str .= substr($string, $i, 1);
                $i++;
            }
            $con++;
        }
        return $str . $dot;
    }else{
                /* 
                 * GBK的中文截取
                 */
            $length=$length*2;
            if(strlen($string) <= $length) {
                return $string;
            }
            $strcut = '';	 
                for($i = 0; $i < $length; $i++) {
                    $strcut .= ord($string[$i]) > 127 ? $string[$i].$string[++$i] : $string[$i];
                }
            $strcut = str_replace(array('&', '"', '<', '>'), array('&amp;', '&quot;', '&lt;', '&gt;'), $strcut);
            return $strcut.$dot;
    }
}

?>
