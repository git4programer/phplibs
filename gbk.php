<?php

	$con = mysql_connect('localhost','root','');
	mysql_set_charset('utf8',$con) or die("Invalid query: " . mysql_error());
	mysql_query('use `bag-hr`',$con)

	$sql = 'SELECT articleTitle,articleContent FROM `rcw_article` WHERE `articleId` = 24';
	$res = mysql_query($sql,$con)
	$result = mysql_fetch_row($res);
?>