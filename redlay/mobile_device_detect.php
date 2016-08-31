<?php
if( strstr($_SERVER['HTTP_USER_AGENT'],'Android') ||
    //strstr($_SERVER['HTTP_USER_AGENT'],'webOS') ||
    strstr($_SERVER['HTTP_USER_AGENT'],'iPhone') ||
    strstr($_SERVER['HTTP_USER_AGENT'],'iPod') ||
    strstr($_SERVER['HTTP_USER_AGENT'],'BlackBerry')
    )

{
   if(strpos($_SERVER["SERVER_NAME"],'m.redlay')==false)
       header("Location: http://m.redlay.com");
}
//$isMobile = false;
//$isBot = false;
//
//$op = strtolower($_SERVER['HTTP_X_OPERAMINI_PHONE']);
//$ua = strtolower($_SERVER['HTTP_USER_AGENT']);
//$ac = strtolower($_SERVER['HTTP_ACCEPT']);
//$ip = $_SERVER['REMOTE_ADDR'];
//
//$isMobile = strpos($ac, 'application/vnd.wap.xhtml+xml') !== false
//        || $op != ''
//        || strpos($ua, 'sony') !== false
//        || strpos($ua, 'symbian') !== false
//        || strpos($ua, 'nokia') !== false
//        || strpos($ua, 'samsung') !== false
//        || strpos($ua, 'mobile') !== false
//        || strpos($ua, 'windows ce') !== false
//        || strpos($ua, 'epoc') !== false
//        || strpos($ua, 'opera mini') !== false
//        || strpos($ua, 'nitro') !== false
//        || strpos($ua, 'j2me') !== false
//        || strpos($ua, 'midp-') !== false
//        || strpos($ua, 'cldc-') !== false
//        || strpos($ua, 'netfront') !== false
//        || strpos($ua, 'mot') !== false
//        || strpos($ua, 'up.browser') !== false
//        || strpos($ua, 'up.link') !== false
//        || strpos($ua, 'audiovox') !== false
//        || strpos($ua, 'blackberry') !== false
//        || strpos($ua, 'ericsson,') !== false
//        || strpos($ua, 'panasonic') !== false
//        || strpos($ua, 'philips') !== false
//        || strpos($ua, 'sanyo') !== false
//        || strpos($ua, 'sharp') !== false
//        || strpos($ua, 'sie-') !== false
//        || strpos($ua, 'portalmmm') !== false
//        || strpos($ua, 'blazer') !== false
//        || strpos($ua, 'avantgo') !== false
//        || strpos($ua, 'danger') !== false
//        || strpos($ua, 'palm') !== false
//        || strpos($ua, 'series60') !== false
//        || strpos($ua, 'palmsource') !== false
//        || strpos($ua, 'pocketpc') !== false
//        || strpos($ua, 'smartphone') !== false
//        || strpos($ua, 'rover') !== false
//        || strpos($ua, 'ipaq') !== false
//        || strpos($ua, 'au-mic,') !== false
//        || strpos($ua, 'alcatel') !== false
//        || strpos($ua, 'ericy') !== false
//        || strpos($ua, 'up.link') !== false
//        || strpos($ua, 'vodafone/') !== false
//        || strpos($ua, 'wap1.') !== false
//        || strpos($ua, 'wap2.') !== false;
//        if($isMobile&&strpos($_SERVER["SERVER_NAME"],'m.redlay')==false)
//           header("Location: http://m.redlay.com");