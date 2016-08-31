<?php
@include('init.php');
include('universal_functions.php');
$allowed="users";
include('security_checks.php');

$num=(int)($_POST['num']);

if($num==1)
{

    $border_color=clean_string($_POST['border_color']);
    $query=mysql_query("SELECT display_colors FROM user_display WHERE user_id=$_SESSION[id] LIMIT 1");
    if($query&&mysql_num_rows($query)==1)
    {
        $array=mysql_fetch_row($query);

        $display_colors=explode('|^|*|', $array[0]);
        $display_colors[0]=$border_color;
        $display_colors=implode('|^|*|', $display_colors);
        $query=mysql_query("UPDATE user_display SET display_colors='$display_colors' WHERE user_id=$_SESSION[id]");
    }
}
else if($num==2)
{
    $background_color=clean_string($_POST['background_color']);
    $query=mysql_query("SELECT display_colors FROM user_display WHERE user_id=$_SESSION[id] LIMIT 1");
    if($query&&mysql_num_rows($query)==1)
    {
        $array=mysql_fetch_row($query);

        $display_colors=explode('|^|*|', $array[0]);
        $display_colors[1]=$background_color;
        $display_colors=implode('|^|*|', $display_colors);
        $query=mysql_query("UPDATE user_display SET display_colors='$display_colors' WHERE user_id=$_SESSION[id]");
    }
}
else if($num==3)
{
    $text_color=clean_string($_POST['text_color']);
    $query=mysql_query("SELECT display_colors FROM user_display WHERE user_id=$_SESSION[id] LIMIT 1");
    if($query&&mysql_num_rows($query)==1)
    {
        $array=mysql_fetch_row($query);

        $display_colors=explode('|^|*|', $array[0]);
        $display_colors[2]=$text_color;
        $display_colors=implode('|^|*|', $display_colors);
        $query=mysql_query("UPDATE user_display SET display_colors='$display_colors' WHERE user_id=$_SESSION[id]");
    }
}
else if($num==4)
{
    $opacity=(int)($_POST['opacity']);
    if($opacity>=0&&$opacity<=100)
    {
        $query=mysql_query("SELECT display_colors FROM user_display WHERE user_id=$_SESSION[id] LIMIT 1");
        if($query&&mysql_num_rows($query)==1)
        {
            $array=mysql_fetch_row($query);
            
            $display_colors=explode('|^|*|', $array[0]);
            $display_colors[3]=$opacity;
            $display_colors=implode('|^|*|', $display_colors);
            $query=mysql_query("UPDATE user_display SET display_colors='$display_colors' WHERE user_id=$_SESSION[id]");
        }
    }
}