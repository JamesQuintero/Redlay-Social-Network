<?php
@include('init.php');
include('universal_functions.php');
$allowed="users";
include('security_checks.php');


$ID=(int)($_POST['user_id']);

//deletes user from current user's pending friend request list
$query=mysql_query("SELECT other_user_id, user_sent, message, audience, timestamp FROM pending_friend_requests WHERE user_id=$_SESSION[id] LIMIT 1");
if($query&&mysql_num_rows($query)==1)
{
    $array=mysql_fetch_row($query);
    $other_user_ids=explode('|^|*|', $array[0]);
    $users_sent=explode('|^|*|', $array[1]);
    $messages=explode('|^|*|', str_replace("'", "\'", $array[2]));
    $audiences=explode('|^|*|', $array[3]);
    $timestamps=explode('|^|*|', $array[4]);

    $temp_other_user_ids=array();
    $temp_users_sent=array();
    $temp_messages=array();
    $temp_audiences=array();
    $temp_timestamps=array();
    for($x = 0; $x < sizeof($other_user_ids); $x++)
    {
        if($other_user_ids[$x]!=$ID)
        {
            $temp_other_user_ids[]=$other_user_ids[$x];
            $temp_users_sent[]=$users_sent[$x];
            $temp_messages[]=$messages[$x];
            $temp_audiences[]=$audiences[$x];
            $temp_timestamps[]=$timestamps[$x];
        }
    }

    $other_user_ids=implode('|^|*|', $temp_other_user_ids);
    $users_sent=implode('|^|*|', $temp_users_sent);
    $messages=implode('|^|*|', $temp_messages);
    $audiences=implode('|^|*|', $temp_audiences);
    $timestamps=implode('|^|*|', $temp_timestamps);

    $query=mysql_query("UPDATE pending_friend_requests SET other_user_id='$other_user_ids', user_sent='$users_sent', message='$messages', audience='$audiences', timestamp='$timestamps' WHERE user_id=$_SESSION[id]");
    if($query)
    {
        //delete's current user from user's pending_friend_request list
        $query=mysql_query("SELECT other_user_id, user_sent, message, audience, timestamp FROM pending_friend_requests WHERE user_id=$ID LIMIT 1");

        $array=mysql_fetch_row($query);
        $other_user_ids=explode('|^|*|', $array[0]);
        $users_sent=explode('|^|*|', $array[1]);
        $messages=explode('|^|*|', str_replace("'", "\'", $array[2]));
        $audiences=explode('|^|*|', $array[3]);
        $timestamps=explode('|^|*|', $array[4]);

        $temp_other_user_ids=array();
        $temp_users_sent=array();
        $temp_messages=array();
        $temp_audiences=array();
        $temp_timestamps=array();
        for($x = 0; $x < sizeof($other_user_ids); $x++)
        {
            if($other_user_ids[$x]!=$_SESSION['id'])
            {
                $temp_other_user_ids[]=$other_user_ids[$x];
                $temp_users_sent[]=$users_sent[$x];
                $temp_messages[]=$messages[$x];
                $temp_audiences[]=$audiences[$x];
                $temp_timestamps[]=$timestamps[$x];
            }
        }

        $other_user_ids=implode('|^|*|', $temp_other_user_ids);
        $users_sent=implode('|^|*|', $temp_users_sent);
        $messages=implode('|^|*|', $temp_messages);
        $audiences=implode('|^|*|', $temp_audiences);
        $timestamps=implode('|^|*|', $temp_timestamps);

        $query=mysql_query("UPDATE pending_friend_requests SET other_user_id='$other_user_ids', user_sent='$users_sent', message='$messages', audience='$audiences', timestamp='$timestamps' WHERE user_id=$ID");
        if($query)
        {
            $query=mysql_query("SELECT new_friend_alerts FROM alerts WHERE user_id=$_SESSION[id] LIMIT 1");
            if($query&&mysql_num_rows($query)==1)
            {
              $array=mysql_fetch_row($query);
              $num_add_alerts=$array[0];
              $num_add_alerts--;
              $query=mysql_query("UPDATE alerts SET new_friend_alerts='$num_add_alerts' WHERE user_id=$_SESSION[id]");
              if($query)
                  echo "User add request deleted!";
            }
        }
        else
        {
            echo "Something went wrong. We are working to fix it. 7";
            log_error("accept_friend.php: ", mysql_error());
        }
    }
    else
    {
        echo "Something went wrong. We are working to fix it. 6";
        log_error("accept_friend.php: ", mysql_error());
    }
}
else
{
    echo "Something went wrong. We are working to fix it. 5";
    log_error("accept_friend.php: ", mysql_error());
}
