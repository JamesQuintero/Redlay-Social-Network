<?php
@include('init.php');
include('universal_functions.php');
$allowed="users";
include('security_checks.php');

$top=(int)($_POST['top']);

//gets width and height of banner
$query=mysql_query("SELECT banner_data FROM user_display WHERE user_id=$_SESSION[id] LIMIT 1");
if($query&&mysql_num_rows($query)==1)
{
    $array=mysql_fetch_row($query);
    $banner_dimensions=explode('|^|*|', $array[0]);
    
    $height=$banner_dimensions[0];
//    $width=$banner_dimensions[1];
//    $current_top=$banner_dimensions[2];
}
else if(!$query)
{
    echo "Something went wrong. We are working on fixing it";
    send_mail_error("change_banner_position.php: (1): ", mysql_error());
}

//if banner isn't out of bounds
if($top<0&&$top+$height>200)
{
    $banner_dimensions[2]=$top;
    $banner_dimensions=implode('|^|*|', $banner_dimensions);
    $query=mysql_query("UPDATE user_display SET banner_data='$banner_dimensions' WHERE user_id=$_SESSION[id]");
    if(!$query)
    {
        echo "Something went wrong. We are working on fixing it";
        send_mail_error("change_banner_position.php: (2): ", mysql_error());
    }
}