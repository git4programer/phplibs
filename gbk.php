<?php	echo 'ÕâÊÇÊ²Ã´±àÂëÄØ£¿gbk,gb2312<br />';

	$con = mysql_connect('localhost','root','');	var_dump($con);
	mysql_set_charset('utf8',$con) or die("Invalid query: " . mysql_error());
	mysql_query('use `bag-hr`',$con)		or die("Invalid query: " . mysql_error());

	$sql = 'SELECT articleTitle,articleContent FROM `rcw_article` WHERE `articleId` = 24';
	$res = mysql_query($sql,$con)		or die("Invalid query: " . mysql_error());
	$result = mysql_fetch_row($res);	print_r($result);
?>
