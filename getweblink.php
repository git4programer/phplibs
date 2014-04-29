<?php
/**
 * 抓取网页上的链接
 */

$html = file_get_contents('http://baidu.com');
$dom = new DOMDocument();
@$dom->loadHtml($html);
$xpath = new DOMXpath($dom);
$hrefs = $xpath->evaluate("/html/body//a");

for ($i = 0; $i < $hrefs->length; $i++) {
	$href = $hrefs->item($i);
	$url = $href->getAttribute('href');
	echo $url.'<br />';
}


?>
