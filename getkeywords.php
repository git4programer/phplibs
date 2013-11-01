<?php
/**
 * 获得网页中的关键字
 */
$meta = gte meta_tags('hhhhh');
$keywords = $meta['keywords'];
$keywords = explode(','$keywords);

$keywords = array_map('trim',$keywords);
$keywords = array_filter($keywords);
?>
