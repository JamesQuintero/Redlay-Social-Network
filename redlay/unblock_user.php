<?php
@include('init.php');
include('universal_functions.php');
$allowed="users";
include('security_checks.php');

$ID=(int)($_POST['user_id']);

if(is_id($ID) && user_id_exists($ID) && !user_id_terminated($ID) && user_blocked($_SESSION['id'], $ID))
{
    $query=mysql_query("SELECT blocked_users, blocked_user_timestamps FROM user_data WHERE user_id=$_SESSION[id] LIMIT 1");
    if($query&&mysql_num_rows($query)==1)
    {
        $array=mysql_fetch_row($query);
        $blocked_users=explode("|^|*|", $array[0]);
        $blocked_user_timestamps=explode('|^|*|', $array[1]);
        
        $temp_blocked_users=array();
        $temp_blocked_user_timestamps=array();
        for($x = 0; $x < sizeof($blocked_users); $x++)
        {
            if($blocked_users[$x]!=$ID)
            {
                $temp_blocked_users[]=$blocked_users[$x];
                $temp_blocked_user_timestamps[]=$blocked_user_timestamps[$x];
            }
        }
        
        if($temp_blocked_users[0]!=null)
        {
            $blocked_users=implode('|^|*|', $temp_blocked_users);
            $blocked_user_timestamps=implode('|^|*|', $temp_blocked_user_timestamps);
        }
        else
        {
            $blocked_users='';
            $blocked_user_timestamps='';
        }
        
        $query=mysql_query("UPDATE user_data SET blocked_users='$blocked_users', blocked_user_timestamps='$blocked_user_timestamps' WHERE user_id=$_SESSION[id]");
        if($query)
            echo "User unblocked";
        else
        {
            echo "Something went wrong. We are working on fixing it";
            log_error("unblock_user.php: ", mysql_error());
        }
    }
    else
    {
        echo "Something went wrong. We are working on fixing it";
        log_error("unblock_user.php: ", mysql_error());
    }
}
else
    echo "Invalid ID";
?>