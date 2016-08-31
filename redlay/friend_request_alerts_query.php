<?php
@include('init.php');
include('universal_functions.php');
$allowed="users";
include('security_checks.php');

$timezone=(int)($_POST['timezone']);

$query=mysql_query("SELECT * FROM pending_friend_requests WHERE user_id=$_SESSION[id] LIMIT 1");
if($query&&mysql_num_rows($query)==1)
{
    $array=mysql_fetch_array($query);
    $other_user_ids=explode('|^|*|', $array['other_user_id']);
    $users_sent=explode('|^|*|', $array['user_sent']);
    $timestamps=explode('|^|*|', $array['timestamp']);
    $messages=explode('|^|*|', mysql_real_escape_string($array['message']));
    $groups=explode('|^|*|', $array['audience']);

    //checks if users are terminated
    $changed=false;
    $temp_other_user_ids=array();
    $temp_users_sent=array();
    $temp_timestamps=array();
    $temp_messages=array();
    $temp_groups=array();
    for($x = 0; $x < sizeof($other_user_ids); $x++)
    {
        if(!user_id_terminated($other_user_ids[$x]))
        {
            $temp_other_user_ids[]=$other_user_ids[$x];
            $temp_users_sent[]=$users_sent[$x];
            $temp_timestamps[]=$timestamps[$x];
            $temp_messages[]=$messages[$x];
            $temp_groups[]=$groups[$x];
        }
        else
            $changed=true;
    }
    
    if($changed)
    {
        $other_user_ids=$temp_other_user_ids;
        $users_sent=$temp_users_sent;
        $timestamps=$temp_timestamps;
        $messages=$temp_messages;
        
        $temp_other_user_ids=implode('|^|*|', $temp_other_user_ids);
        $temp_users_sent=implode('|^|*|', $temp_users_sent);
        $temp_timestamps=implode('|^|*|', $temp_timestamps);
        $temp_messages=implode('|^|*|', $temp_messages);
        $temp_groups=implode('|^|*|', $temp_groups);
        
        $query=mysql_query("UPDATE pending_friend_requests SET other_user_id='$temp_other_user_ids', user_sent='$temp_users_sent', message='$temp_messages', audience='$temp_groups', timestamp='$temp_timestamps' WHERE user_id=$_SESSION[id]");
        if(!$query)
            send_mail_error("friend_request_alerts_query.php: (2): ", mysql_error());
    }

    //gets rid of current user sent add requests
    $temp_other_user_ids=array();
    $temp_users_sent=array();
    $temp_timestamps=array();
    $temp_messages=array();
    $temp_groups=array();
    
    for($x = 0; $x < sizeof($users_sent); $x++)
    {
        if($users_sent[$x]!=$_SESSION[id])
        {
            $temp_other_user_ids[]=$other_user_ids[$x];
            $temp_users_sent[]=$users_sent[$x];
            $temp_timestamps[]=$timestamps[$x];
            $temp_messages[]=$messages[$x];
            $temp_groups[]=$groups[$x];
        }
    }
    
    $other_user_ids=$temp_other_user_ids;
    $users_sent=$temp_users_sent;
    $timestamps=$temp_timestamps;
    $messages=$temp_messages;
    $groups=$temp_groups;
    
    
    $profile_pictures=array();
    $timestamp_seconds=array();
    for($x =0; $x < sizeof($other_user_ids); $x++)
    {
        //gets names
        $names[$x]=get_user_name($other_user_ids[$x]);

        //gets profile pictures
        $profile_pictures[$x]=get_profile_picture($other_user_ids[$x]);

        if(user_is_friends($_SESSION[id], $other_user_ids)=="true")
             $user_is_friends[$x]=true;
        else
            $user_is_friends[$x]=false;

        //gets num adds
        $num_adds[$x]=get_num_adds($other_user_ids[$x]);
        
        //gets timestamp seconds
        $timestamp_seconds[$x]=get_time_since_seconds($timestamps[$x], $timezone);
    }
     
    if($array['other_user_id']!='')
    {
        $JSON=array();
        $JSON['other_user_ids']=$other_user_ids;
        $JSON['users_sent']=$users_sent;
        $JSON['timestamps']=$timestamps;
        $JSON['timestamp_seconds']=$timestamp_seconds;
        $JSON['user_names']=$names;
        $JSON['user_is_friends']=$user_is_friends;
        $JSON['messages']=$messages;
        $JSON['profile_pictures']=$profile_pictures;
        $JSON['num_adds']=$num_adds;
        echo json_encode($JSON);
        exit();
    }
    else
    {
        $JSON=array();
        $JSON['other_user_ids']=array();
        $JSON['users_sent']=array();
        $JSON['timestamps']=array();
        $JSON['user_names']=array();
        $JSON['user_is_friends']=array();
        $JSON['messages']=array();
        $JSON['profile_pictures']=array();
        $JSON['num_adds']=array();
        echo json_encode($JSON);
        exit();
    }
}
else
{
    echo "Something went wrong";
    log_error("friend_request_alerts_query.php: (1): ", mysql_error());
}