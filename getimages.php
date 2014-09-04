<?php

/**
 * curl多线程下载类
 */
class MultiHttpRequest
{

	public $urls = array ();
	private $res = array ();
	private $curlopt_header = 0;
	private $method = "GET";
	private $curlopt = array ();

	public function __construct($urls = false, $curlopt = array ())
	{
		$this->urls = $urls;
		if ( !empty($curlopt) ) {
			$this->curlopt = $curlopt;
		}
	}

	public function set_urls($urls)
	{
		$this->urls = $urls;
		return $this;
	}

	public function set_curlopt($name, $vale)
	{
		$this->curlopt[$name] = $vale;
	}

	public function is_return_header($b)
	{
		$this->curlopt_header = $b;
		return $this;
	}

	public function get_curlopt()
	{
		return $this->curlopt;
	}

	public function set_method($m)
	{
		$this->medthod = strtoupper($m);
		return $this;
	}

	public function set_curlopts($arr)
	{
		$this->curlopt = $arr;
	}

	public function start()
	{
		if ( !is_array($this->urls) || count($this->urls) == 0 ) {
			return false;
		}

		$handle = curl_multi_init();
		foreach ( $this->urls as $k => $v ) {
			$curl[$k] = $this->add_handle($handle, $v);
		}

		$this->exec_handle($handle);
		foreach ( $this->urls as $k => $v ) {
			$this->res[$k] = curl_multi_getcontent($curl[$k]);
			curl_multi_remove_handle($handle, $curl[$k]);
		}
		curl_multi_close($handle);
	}

	private function add_handle($handle, $url)
	{
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		foreach ( $this->curlopt as $k => $v ) {
			curl_setopt($curl, $k, $v);
		}
		curl_multi_add_handle($handle, $curl);
		return $curl;
	}

	private function exec_handle($handle)
	{
		$flag = null;
		do {
			curl_multi_exec($handle, $flag);
		} while ( $flag > 0 );
	}

	public function getRes()
	{
		return $this->res;
	}

}

class HDbutifulyGril
{
	private $url = '';
	private $savepath = '';
	private $imagessavepath = array();
	
	public function __construct($url,$savepath='')
	{
		$this->url = $url;
		if( !empty($savepath) && !is_dir($savepath)){
			mkdir($savepath,0777,true);
		}
		$this->savepath = $savepath;
	}

	/**
	 * 得到所有大图的url地址
	 * @param type $id
	 * @return array();
	 */
	private function getimagesurl($id = '')
	{
		$url = $this->url . $id;
		$str = file_get_contents($url);
		$arr = json_decode($str, true);
		$images = array ();
		$savepath = array();
		if ( is_array($arr) && !empty($arr) ) {
			foreach ( $arr['data'] as $v ) {
				$url = $v['image']['original'];
				$filename = $this->getFilenameBy($url);
				if(  !file_exists($filename)){ // 如果图片已经采集了就不要再采了
					$images[] = $url;
					$savepath[] = $filename;
				}
			}
		}
		$this->imagessavepath = $savepath;
		return $images;
	}

	/**
	 * 通过url确定图片的保存地址
	 * @param string $url
	 * @return string 
	 */
	private function getFilenameBy($url)
	{
		$tmp = explode('/',$url);
		return $this->savepath . str_replace(',', '_', array_pop($tmp));
	}

	/**
	 * 开始下载
	 * @param int $id pid
	 */
	public function start($id = '')
	{
		$imgurl = $this->getimagesurl($id);
		$imgs = $this->MultiDownByUrls($imgurl);
		foreach ( $imgs as $k => $v ) {
			if ( !empty($v) ) {
				file_put_contents($this->imagessavepath[$k], $v);
			}
		}
	}

	/**
	 * 通过urls多线程下载图片
	 * @param array $urls
	 * @return array
	 */
	private function MultiDownByUrls($urls)
	{
		$opts = array (
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_AUTOREFERER => 1,
			CURLOPT_HEADER => 0,
			CURLOPT_FOLLOWLOCATION => 1,
			CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/33.0.1750.154 Safari/537.36'
		);
		$mutil = new MultiHttpRequest($urls, $opts);
		$mutil->start();
		return $mutil->getRes();
	}

}

$url = 'http://api.lovebizhi.com/macos_v4.php?a=category&spdy=1&tid=3&order=hot&color_id=3&device=105&uuid=436e4ddc389027ba3aef863a27f6e6f9&mode=0&retina=0&client_id=1008&device_id=31547324&model_id=105&size_id=0&channel_id=70001&screen_width=1920&screen_height=1200&bizhi_width=1920&bizhi_height=1200&version_code=19&language=zh-Hans&jailbreak=0&mac=&p=';
$savepath = __DIR__.DIRECTORY_SEPARATOR.'images' . DIRECTORY_SEPARATOR;
$hd = new HDbutifulyGril($url,$savepath);
for ( $i = 1; $i < 100; $i++ ) {
	$hd->start($i);
}
