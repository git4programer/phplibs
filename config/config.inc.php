<?php

function getBrowers($id){
    $browes = array(
        '0' =>'Mozilla/5.0 (Windows NT 6.3; WOW64; rv:27.0) Gecko/20100101 Firefox/27.0',
        '1' =>'Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/33.0.1750.149 Safari/537.36',
        '2' =>'Mozilla/5.0 (Windows NT 6.3; WOW64; Trident/7.0; LCJB; rv:11.0) like Gecko',
        '3' =>'Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/29.0.1547.66 Safari/537.36 LBBROWSER',
        '4' =>'Mozilla/5.0 (Windows NT 6.3; WOW64; rv:28.0) Gecko/20100101 Firefox/28.0',
        '5' =>'Mozilla/5.0 (Macintosh; U; Intel Mac OS X10_6_8; en-us) AppleWebKit/534.50 (KHTML, like Gecko) Version/5.1 Safari/534.50',
        '6' =>'Mozilla/5.0 (Windows; U; Windows NT 6.1;en-us) AppleWebKit/534.50 (KHTML, like Gecko) Version/5.1 Safari/534.50',
        '7' =>'Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1;Trident/5.0',
        '8' =>'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0;Trident/4.0)',
        '9' =>'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1;360SE)',
        '10' =>'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1;Trident/4.0; TencentTraveler 4.0; .NET CLR 2.0.50727)',
        '11' =>'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/33.0.1750.154 Safari/537.36',
        '12'=> 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.1; WOW64; Trident/6.0; SLCC2; .NET CLR 2.0.50727; .NET4.0C; .NET4.0E)',
    );

    if($id >=0 && $id < 13){
        return $browes[$id];
    } else {
        return $browes[0];
    }

}


/**
 * 字符串截取函数    
 * @param $start 开始截取的位置
 * @param $end   结束的位置
 * @return string|bool 如果存在这样的字符串就返回截取后的字符串，否则返回false
 */ 
function str_substr($start, $end, $str)    
{
    $temp = explode($start, $str, 2);
    if(!isset($temp[1])){
        return false;
    }
    $content = explode($end, $temp[1], 2);
    return $content[0];
}

//=================

/**
 * curl简单封装函数
 * @param $url
 * @return bool|mixed
 */
function getHTML($url,$browesid='2',$header='0')
{
    $browes = getBrowers($browesid);
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL => trim($url),
        CURLOPT_USERAGENT => $browes,
    ));
	if($header){
		curl_setopt($curl,CURLOPT_HEADER,1);
		curl_setopt($curl,CURLOPT_COOKIEFILE,'cookie.txt');
		curl_setopt($curl,CURLOPT_COOKIEJAR,'cookie.txt');
	}
    $resp = curl_exec($curl);
    if (curl_errno($curl)) {
        print curl_error($curl);
        return false;
    } else {
        curl_close($curl);
        return $resp;
    }
}

/** 
 *  通过ajax来请求得到页面信息
 */
function getAjaxHtml($url,$referer,$cookie,$host,$browesid='2'){
    $browes = getBrowers($browesid);
	$curl = curl_init();
	curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);
	curl_setopt($curl,CURLOPT_URL,trim($url));
	curl_setopt($curl,CURLOPT_REFERER,$referer);
	curl_setopt($curl,CURLOPT_COOKIE,$cookie);
	curl_setopt($curl,CURLOPT_USERAGENT,$browes);
	curl_setopt($curl,CURLOPT_HTTPHEADER,array("X-Requested-With:XMLHttpRequest"));

    $resp = curl_exec($curl);
    if (curl_errno($curl)) {
        print curl_error($curl);
        return false;
    } else {
        curl_close($curl);
        return $resp;
    }
}
/**
 * 通过post数据来获得html的信息
 */
function getHTMLByPost($url,$data,$browesid){
    $browes = getBrowers($browesid);
	$curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL => trim($url),
        CURLOPT_POST => 1,
        CURLOPT_POSTFIELDS =>$data,
        CURLOPT_USERAGENT => $browes ,
    ));
    $resp = curl_exec($curl);
    if (curl_errno($curl)) {
        print curl_error($curl);
        return false;
    } else {
        curl_close($curl);
        return $resp;
    }
}
/**
 * ajax upload file and post data
 */
function ajaxPostData($url,$data,$browesid='5'){
    $browes = getBrowers($browesid);
    $curl = curl_init();
    curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);
    curl_setopt($curl,CURLOPT_URL,trim($url));
    curl_setopt($curl,CURLOPT_POST,1);
    curl_setopt($curl,CURLOPT_POSTFIELDS,$data);
    curl_setopt($curl,CURLOPT_USERAGENT,$browes);
    curl_setopt($curl,CURLOPT_HTTPHEADER,array("X-Requested-With:XMLHttpRequest"));
    $resp = curl_exec($curl);
    if (curl_errno($curl)) {
        print curl_error($curl);
        return false;
    } else {
        curl_close($curl);
        return $resp;
    }
}

/**
 * 下载图片
 * @param $imgurl 图片的网页上面的地址
 * @param $savename 图片保存的路径。
 */
function dowlaondIMG($imgurl,$savename){
    // imgs 采集下来
    if (!file_exists($savename) || filesize($savename) == 432) {
        $img = getHTML($imgurl);
        if (!empty($img)) {
            file_put_contents($savename, $img);
        }
    }
}

/**
 * 下载图片返回图片地址
 */
function downloadDetailImg($url,$savePath,$ji=1){
	$tmpname = explode('/',$url);
	$count = count($tmpname);
	$imgname = '';
    if($count < $ji){
        return false;
    }
	for ($i=$ji; $i > 0 ;$i--){
		$imgname .= $tmpname[$count-$i];
	}
	if(!is_dir($savePath)){
		@mkdir($savePath,0755);
	}
	$savePath .= $imgname;
	dowlaondIMG($url,$savePath);
	return $imgname;
}
/**
 * 拼装插入的mysql的sql
 * @param $tablename 表名。
 * @param arrar $data 要插入的数据的关联数组.
 */
function buildInsertSql($tablename,$data){
	if (empty($data)){
		return false;
	}
	$sql = '';
	$sql .= "INSERT INTO `".$tablename."` (`";
	$sql .= implode("`,`", array_keys($data));
	$sql .="`) VALUES('";
	$sql .= implode("','",$data);
	$sql .="')";
	return $sql;
}
/**
 * mysql的按索引取出数组
 */
function mysqlGetRows($res){
    $data = array();
    if(mysql_num_rows($res) == 0){
        return false;
    }
    while ($re = mysql_fetch_assoc($res)) {
        $data[]= $re;
    }
    return $data;
}

function getMysqlOneRows($res){
    $data = mysqlGetRows($res);
    return $data ? $data[0]: false;
}
 
/**
 * 引入一个模版
 */
function inclueTemplate($template,$style=''){
    if (empty($style)){
        $tmpla = './html';
    } else {
        $tmpla = './html/';
    }

    $head_file = $tmpla.$style.'/head.html';
    if(!file_exists($head_file)){
        exit('head.html not exist!!');
    }
    $main = file_get_contents($head_file);
    if(is_array($template) && !empty($template)){
        foreach($template as $v){
            $main_file =$tmpla.$style.'/'.$v;
            if(!file_exists($main_file)){
                $msg = $main_file . ' file is not found';
                exit($msg);
            }
            $main .= file_get_contents($main_file);
        }
    } else {
        $main_file =$tmpla.$style.'/'.$template;
        if(!file_exists($main_file)){
            $msg = $main_file . ' file is not found';
            exit($msg);
        }
        $main .= file_get_contents($main_file);
    }

    $foot_file = $tmpla.$style.'/foot.html';
    if(!file_exists($foot_file)){
        $msg = $foot_file .' file is not found';
        exit($msg);
    }
    $main .= file_get_contents($foot_file);
    global $urllink;
    return str_replace('{localurl}',$urllink,$main);
}

/**
 * 下载量的数据转换
 */
function convertion($str){
	$str = trim($str);
	if (is_numeric($str)) {
		return $str;
	}
	if(empty($str)){
		return 0;
	}
	$preg_abb = '/([\d]+)\s*([\x{4e00}-\x{9fa5}])/isuU';
	preg_match($preg_abb,$str,$macth);
    if(isset($macth[2])){
        switch($macth[2]){
            case '万':
                return $macth[1] * 10000;
            case '亿':
                return $macth[1] * 100000000;
            default:
                return $macth[1];
        }
    }else {
        return 0;
    }
}
function builSelectSql($tablename,$data,$where='',$order='',$limit='10'){
	$sql  = 'select ';
	$sql .= implode(',',$data);
	$sql .= ' from '.$tablename;
	if(is_array($where)){
		$sql .=' where 1 ';
		foreach ($where as $k=>$v){
			$sql .= ' AND `'.$k."`='".$v."'";
		}
	} else if(is_string($where)){
		$sql .= 'AND '.$where;
	}
	if(!empty($order)){
		$sql .=' order by '.$order;
	}
	$sql .= ' LIMIT '.$limit;
	return $sql;
}

function buildUpdateSql($tablename,$data,$where){
	$sql = '';
	$sql .= "UPDATE `".$tablename."` set ";
	foreach ($data as $k=>$v){
		$sql .="`".$k."`='".$v."',";
	}
	$sql = substr($sql,0,-1);
	$sql .= ' where '.$where;
	return $sql;
}

function AllWebGetDetails($contents,$savepath=''){
    global $urllink;
    // $contents =str_substr('<div class="data-tabcon brief-con">','<div class="data-tabcon comment-con"',$str);
    $img_preg = '/<\s*img\s+[^>]*?src\s*=\s*(\'|\")(.*?)\\1[^>]*?\/?\s*>/i';
    preg_match_all($img_preg, $contents, $detail_img_match);
    // 下载详细页里面的内容图片
    if(empty($savepath)){
        $savepath = __DIR__.DIRECTORY_SEPARATOR .'..'.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'detail/';
    }
    $img_url = $urllink.'/images/detail/';
    foreach($detail_img_match[2] as $value){
        $tmp_img_array = explode('/',$value);
        $img_name = array_pop($tmp_img_array);
        $re_img_url = $img_url.$img_name;
        $savename = $savepath.$img_name;
        dowlaondIMG($value,$savename);
        $contents = str_replace($value, $re_img_url, $contents);
    }
    // 删除js
    file_put_contents('1.html',$contents);
    $contents = preg_replace("'<script[^>]*?>.*?</script>'si",'',$contents);
    $contents = preg_replace("'<table[^>]*?>.*?</table>'si",'',$contents);
    $contents = preg_replace("'<div[^>]class=\"j-scrollbar-wrap\".*?>'si",'<div id="scrollbar" class="scroll-cont">',$contents);
    $contents = preg_replace("'<div[^>]class=\"view-box\".*?>'si",'<div class="viewport">',$contents);
    $contents = preg_replace("'<div[^>]data-length.*class=\"overview\".*?>'si",'<div class="overview">',$contents);
    return  $contents;
}

?>
