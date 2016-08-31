<?php
@include('init.php');
include('universal_functions.php');
$allowed="users";
include('security_checks.php');


$ID=(int)($_POST['user_id']);

if(is_id($ID)&&user_id_exists($ID))
{
    if(!user_id_terminated($ID)&&user_is_friends($_SESSION['id'], $ID)=='false')
    {
        $query=mysql_query("SELECT blocked_users, blocked_user_timestamps FROM user_data WHERE user_id=$_SESSION[id] LIMIT 1");
        if($query&&mysql_num_rows($query)==1)
        {
            $array=mysql_fetch_row($query);
            $blocked_users=explode('|^|*|', $array[0]);
            $blocked_user_timestamps=explode('|^|*|', $array[1]);
            
            if(!in_array($ID, $blocked_users))
            {
                
                //if user isn't current user's friend
                if($array[0]=='')
                {
                    $blocked_users[0]=$ID;
                    $blocked_user_timestamps[0]=get_date();
                }
                else
                {
                    $blocked_users[]=$ID;
                    $blocked_user_timestamps[]=get_date();
                }

                $blocked_users=implode('|^|*|', $blocked_users);
                $blocked_user_timestamps=implode('|^|*|', $blocked_user_timestamps);


                $query=mysql_query("UPDATE user_data SET blocked_users='$blocked_users', blocked_user_timestamps='$blocked_user_timestamps' WHERE user_id=$_SESSION[id]");
                
                if($query)
                    echo "User blocked";
                else
                {
                    echo "Something went wrong. We are working on fixing it";
                    log_error("block_user.php: (2): ", mysql_error());
                }
            }
            else
                echo "User is already blocked";
        }
        else
        {
            echo "Something went wrong. We are working to fix it";
            log_error("block_user.php: (1): ", mysql_error());
        }
    }
    else
        echo "This user can't be blocked";
}
else
    echo "invalid user ID";
