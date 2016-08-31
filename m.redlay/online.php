<?php
@include('init.php');
include('../universal_functions.php');
$allowed="users";
include("security_checks.php");



$num=(int)($_POST['num']);

//puts user online
if($num==1)
{
    $date=get_date();
    $type='Mobile';
    $timestamp=array();
    $timestamp[0]=$date;
    $timestamp[1]=$type;
    $timestamp=implode('|^|*|', $timestamp);
    $query=mysql_query("UPDATE online SET timestamp='$timestamp' WHERE user_id=$_SESSION[id] LIMIT 1");
}

//if user_id is online
else if($num==2)
{
    $ID=(int)($_POST['user_id']);
    
    if(is_id($ID)&&user_id_exists($ID)&&user_id_friends($_SESSION['id'], $ID))
    {
        $query=mysql_query("SELECT timestamp FROM online WHERE user_id=$ID LIMIT 1");
        if($query&&mysql_num_rows($query)==1)
        {
            $array=mysql_fetch_row($query);
            $prev_date=explode('|^|*|', $array[0]);
            
            $type=$prev_date[1];
            $prev_date=$prev_date[0];
            
            $date=get_date();
            
            if($date-$prev_date<=5)
                return true;
            else
                return false;
        }
    }
}