<?php 
	header('Content-type: text/html');
	echo preg_replace_callback('~-([a-z])~',function($match){
		return strtoupper($match[1]);
	},'hello - world');

echo '<br />===========================<br />';

$greet = function($name){
	printf("Hello %s\r\n",$name);
};

$greet('world');
$greet('PHP');


