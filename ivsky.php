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

function str_substr($str, $start, $end = '')
{
	$temp = explode($start, $str, 2);
	if ( !isset($temp[1]) ) {
		return false;
	}
	if ( empty($end) ) {
		return trim($temp[1]);
	}
	$content = explode($end, $temp[1], 2);
	return trim($content[0]);
}

class Ivsky
{

	private $url = '';
	private $savepath = '';
	private $flage = true;
	private $weburl = 'http://www.ivsky.com';

	public function __construct($url, $savepath)
	{
		$this->url = $url;
		if ( !empty($savepath) && !is_dir($savepath) ) {
			mkdir($savepath, 0777, true);
		}
		$this->savepath = $savepath;
	}

	/**
	 * 开始采集
	 */
	public function start()
	{
		while ( $this->flage ) {
			$str = file_get_contents($this->url);
			$this->flage = str_substr($str, "class='page-next'", "</a>");
			if ( $this->flage ) {
				$this->url = $this->weburl . str_substr($this->flage, "href='", "'");
			}
			$main_str = str_substr($str, '<ul class="ali">', '</ul>');
//			file_put_contents('nvxing', $str);die('succ');
			$this->paserhtml($main_str);
		}
	}

	/**
	 * 通过主要的html去分析得到图片的地址
	 * @param type $html
	 */
	private function paserhtml($html)
	{
		$arr = explode('</li>', $html);
		array_pop($arr);
		foreach ( $arr as $val ) {
			$url = str_substr($val, 'src="', '"');
			preg_match('/\((\d+)/iu', $val, $matchs);
			$this->dowimage($url, $matchs[1]);
		}
	}

	private function dowimage($url, $num)
	{
		//http://img.ivsky.com/img/bizhi/pic/201408/23/mia_aegerter.jpg
		//http://img.ivsky.com/img/bizhi/li/201408/23/mia_aegerter-011.jpg
		$imgurl = array ();
		$savename = array ();
		$filearr = explode('/', $url);
		$filename = array_pop($filearr);
		$last_dir = array_pop($filearr);
		$secen_dir = array_pop($filearr);
		$baseurl = 'http://img.ivsky.com/img/bizhi/pic/' . $secen_dir . '/' . $last_dir . '/';
		$savedir = $this->savepath . DIRECTORY_SEPARATOR . $secen_dir . DIRECTORY_SEPARATOR . $last_dir;
		if ( !empty($savedir) && !is_dir($savedir) ) {
			mkdir($savedir, 0777, true);
		}
		$filenamearr = explode('.', $filename);
		if ( strpos($filenamearr[0], '-') === false ) {
			// 就是第一张了
			$basename = $filenamearr[0];
		} else {
			$basename = strstr($filenamearr[0], '-', TRUE);
		}
		$savename[] = $savedir . DIRECTORY_SEPARATOR . $basename . '.' . $filenamearr[1];
		$imgurl[] = $baseurl . $basename . '.' . $filenamearr[1];
		for ( $i = 1; $i < $num; $i++ ) {
			$tmpfilename = $basename . $this->paserntint($i) . '.' . $filenamearr[1];
			$tmp = $baseurl . $tmpfilename;
			$tmp_file =  $savedir . DIRECTORY_SEPARATOR . $tmpfilename;
			if(!file_exists($tmp_file)){
				$savename[] = $tmp_file;
				$imgurl[] = $tmp;
			}
		}
		$this->muildow($imgurl, $savename);
	}

	/**
	 * 格式化数字，为了拼接那名字
	 * @param string $id
	 * @return type
	 */
	private function paserntint($id)
	{
		$id .='';
		switch ( strlen($id) )
		{
			case 3:
				return '-' . $id;
			case 2:
				return '-0' . $id;
			case 1:
				return '-00' . $id;
		}
	}

	/**
	 * 多线程开始下载吧
	 * @param type $urls
	 * @param type $savename
	 */
	private function muildow($urls, $savename)
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
		$imgs = $mutil->getRes();
		foreach ($imgs as $k => $v){
			if(!file_exists($savename[$k]) && !empty($v)){
				echo $savename[$k] ."\r\n";
				file_put_contents($savename[$k], $v);
			}
		}
	}

}

$url = 'http://www.ivsky.com/bizhi/fengjing/';
$savepath = __DIR__ . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'ivsky' . DIRECTORY_SEPARATOR;
$iv = new Ivsky($url, $savepath);
$iv->start();

