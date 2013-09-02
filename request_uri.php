<?php
	$uri = $_SERVER['REQUEST_URI'];
	$foo = $_GET['foo'];
	header('Content-type:text/html');
	echo "The URI requested is :".$uri;
	echo 'The value of the "foo" parameter is:'.$foo;
?>
