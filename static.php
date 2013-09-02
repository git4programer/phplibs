<?php
class Request{
	public static function createFromGlobals(){
		$request = new static($_GET,$_POST,array(),$_COOKIE,$_FILES,$_SERVER);
		return $request;
	}

}
$request = Request::createFromGlobals();
var_dump($request);
?>
