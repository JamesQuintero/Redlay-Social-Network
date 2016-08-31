<?php
@include('init.php');
include('universal_functions.php');

//if user isn't logged in, redirect
if(!isset($_SESSION['id'])&&!isset($_SESSION['page_id']))
{
    header("Location: http://www.redlay.com");
    exit();
}


$type='Desktop';
$timestamp=array();
$timestamp[0]='offline';
$timestamp[1]=$type;
$timestamp=implode('|^|*|', $timestamp);
mysql_query("UPDATE online SET timestamp='$timestamp' WHERE user_id=$_SESSION[id]");
record_logout();

//pauses code to let user get logged out
usleep(1000000);
session_unset();
session_destroy();
session_write_close();
setcookie(session_name(),'',0,'/');
session_regenerate_id(true);

if(isset($_COOKIE['acc_id']))
{
    setcookie('acc_id', '0', (time()-(1)), null, null, false, true);
    header("Location: http://www.redlay.com");
}
else if(isset($_COOKIE['acc_page']))
{
    setcookie('acc_page', '0', (time()-(1)), null, null, false, true);
    header("Location: http://www.redlay.com");
}
else
    header("Location: http://www.redlay.com");