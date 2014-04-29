<?php
/**
 * Timer 类 用来简单地测试一下运行的时间
 **/
class Timer
{
	private $start = 0;
	private $end = 0;

	private function now()
	{
		list($usec,$sec) = explode(" ",microtime());
		return ((float)$usec + (float)$sec);
	}
	public function start()
	{
		$this->start = $this->now();
	}
	public function end()
	{
		$this->end = $this->now();
	}
	public function getTime()
	{
		return (float)($this->end - $this->start);
	}
	public function printTime()
	{
		printf("Program run use time: %fs", $this->getTime());
	}
}
?>
