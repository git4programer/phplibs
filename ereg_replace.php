<?php
$string = "This is a test";
echo str_replace(" is", " was", $string);
echo ereg_replace("( )is", "\\1was", $string);
echo ereg_replace("(( )is)", "\\2was", $string);
	
?>
