<?php  
//测试时文件的编码方式要是UTF8  
$str='中文a字1符';  
echo '原字符串:'.$str.'<br />';
echo 'strlen的字符长度:'.strlen($str).'<br>';//14  
echo 'mb_strlen utf8对应的长度:'. mb_strlen($str,'utf8').'<br>';//6  
echo 'mb_strlen GBK对应的长度:'.mb_strlen($str,'gbk').'<br>';//8  
echo 'mb_strlen GB2312对应的长度:'. mb_strlen($str,'gb2312').'<br>';//10  

