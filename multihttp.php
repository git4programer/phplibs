<?php
/**
 * 多线程curl类
 * 2014-04-01 
 */
class MultiHttpRequest{
	public $urls=array();
	private $res = array();
	private $curlopt_header=0;
	private $method="GET";
	private $curlopt = array();

	public function __construct($urls=false,$curlopt=array()){
		$this->urls=$urls;
		if(!empty($curlopt)){
			$this->curlopt = $curlopt;
		}
	}

	public function set_urls($urls){
		$this->urls=$urls;
		return $this;
	}
	public function set_curlopt($name,$vale){
		$this->curlopt[$name] = $vale;
	}
	public function is_return_header($b){
		$this->curlopt_header=$b;
		return $this;
	}
    public function get_curlopt(){
        return $this->curlopt;
    }

	public function set_method($m){
		$this->medthod = strtoupper($m);
		return $this;
	}

    public  function set_curlopts($arr){
        $this->curlopt = $arr;
    }
	public function start(){
		if(!is_array($this->urls) || count($this->urls)==0){
			return false;
		}

		$handle=curl_multi_init();
		foreach($this->urls as $k=>$v){
			$curl[$k]=$this->add_handle($handle,$v);
		}

		$this->exec_handle($handle);
		foreach($this->urls as $k=>$v){
			$this->res[$k] = curl_multi_getcontent($curl[$k]);
			curl_multi_remove_handle($handle,$curl[$k]);
		}
		curl_multi_close($handle);
	}

	private function add_handle($handle,$url){
		$curl=curl_init();
		curl_setopt($curl,CURLOPT_URL,$url);
		foreach ($this->curlopt as $k => $v){
			curl_setopt($curl,$k,$v);
		}
		curl_multi_add_handle($handle,$curl);
		return $curl;
	}

	private function exec_handle($handle){
		$flag=null;
		do{
			curl_multi_exec($handle,$flag);
		} while ($flag > 0);
	}

	public function getRes(){
		return $this->res;
	}
}
function getMultiResByUrls($urls){
    $mutil = new MultiHttpRequest($urls);
    $opts = array(
        CURLOPT_RETURNTRANSFER =>1,
        CURLOPT_AUTOREFERER => 1,
        CURLOPT_HEADER => 0,
        CURLOPT_FOLLOWLOCATION => 1,
        CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/33.0.1750.154 Safari/537.36'
    );
    $mutil->set_curlopts($opts);
    $mutil->start();
    return $mutil->getRes();
}


?>
