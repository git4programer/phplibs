<?php
    
/**
 * gethtml by curl;
 */
function getHtml($url){
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (X11; Ubuntu; Linux i686; rv:27.0) Gecko/20100101 Firefox/27.0');
    $out = curl_exec($ch);

    if (curl_errno($ch)) {
        print curl_error($ch);
        return false;
    } else {
        curl_close($ch);
        return $out;
    }
}


function postDateByCurl($url,$data){
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (X11; Ubuntu; Linux i686; rv:27.0) Gecko/20100101 Firefox/27.0');
    curl_setopt($ch,CURLOPT_POST,1);
    curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
    $out = curl_exec($ch);

    if (curl_errno($ch)) {
        print curl_error($ch);
        return false;
    } else {
        curl_close($ch);
        return $out;
    }
}

function ajaxGetHtml($url,$data=''){
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (X11; Ubuntu; Linux i686; rv:27.0) Gecko/20100101 Firefox/27.0');
    if (!empty($data)) {
        curl_setopt($ch,CURLOPT_POST,1);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
    }
    curl_setopt($ch,CURLOPT_HTTPHEADER,array('X-Requested-With:XMLHttpRequest'));

    $out = curl_exec($ch);

    if (curl_errno($ch)) {
        print curl_error($ch);
        return false;
    } else {
        curl_close($ch);
        return $out;
    }
}
ajaxGetHtml('http://localhost/up.php');

?>
