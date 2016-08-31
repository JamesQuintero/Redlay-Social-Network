<?php
@include('init.php');
if(!isset($_SESSION['id'])&&!isset($_SESSION['page_id']))
{
    header("Location: http://m.redlay.com/index.php");
    exit();
}


session_unset();
session_destroy();
session_write_close();
setcookie(session_name(),'',0,'/');
session_regenerate_id(true);

if(isset($_COOKIE['acc_id']))
{
    setcookie('acc_id', '0', (time()-(1)), null, null, false, true);
    header("Location: http://m.redlay.com");
}
else if(isset($_COOKIE['acc_page']))
{
    setcookie('acc_page', '0', (time()-(1)), null, null, false, true);
    header("Location: http://m.redlay.com");
}
else
    header("Location: http://m.redlay.com");