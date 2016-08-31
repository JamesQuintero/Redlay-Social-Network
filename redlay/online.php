<?php
include('init.php');
include('universal_functions.php');
$allowed="users";
include('security_checks.php');

if(isset($_SESSION['id']))
{
    $num=(int)($_POST['num']);      

    //puts user online
    if($num==1)
    {
        $type='Desktop';
        $timestamp=array();
        $timestamp[0]='online';
        $timestamp[1]=$type;
        $timestamp=implode('|^|*|', $timestamp);
        $query=mysql_query("UPDATE online SET timestamp='$timestamp' WHERE user_id=$_SESSION[id]");
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
                $online=explode('|^|*|', $array[0]);

                $is_online=$online[0];


                if($is_online=='online')
                    return true;
                else
                    return false;
            }
        }
    }

    //puts user offline
    else if($num==3)
    {
        $type='Desktop';
        $timestamp=array();
        $timestamp[0]='offline';
        $timestamp[1]=$type;
        $timestamp=implode('|^|*|', $timestamp);
        $query=mysql_query("UPDATE online SET timestamp='$timestamp' WHERE user_id=$_SESSION[id]");
    }
}