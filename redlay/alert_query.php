<?php
@include('init.php');
include('universal_functions.php');
$allowed="users";
include('security_checks.php');

$query=mysql_query("SELECT new_friend_alerts, new_alerts FROM alerts WHERE user_id=$_SESSION[id] LIMIT 1");
$query2=mysql_query("SELECT new_messages, user_id_2, timestamps FROM messages WHERE user_id=$_SESSION[id] LIMIT 1");
if($query&&mysql_num_rows($query)==1&&$query2&&mysql_num_rows($query2)==1)
{
    $array=mysql_fetch_row($query);
    $array2=mysql_fetch_row($query2);

    $friend_alerts=$array[0];
    $alerts=$array[1];
    $new_messages=explode('|^|*|', $array2[0]);
    
    $count=0;
    for($x = 0; $x < sizeof($new_messages); $x++)
        $count+=$new_messages[$x];
    
    //if you have a new message
    //gets user_id of new message
    if($count>0)
    {
        $user_ids=explode('|^|*|', $array2[1]);
        $timestamps=explode('|^|*|', $array2[2]);
        
        $index=0;
        $max=0;
        for($x = 0; $x < sizeof($timestamps); $x++)
        {
            $timestamps[$x]=explode('|%|&|', $timestamps[$x]);
            if(end($timestamps[$x])>$max)
            {
                $index=$x;
                $max=end($timestamps[$x]);
            }
        }
        
        $new_message_id=$user_ids[$index];
    }
    
    

    $JSON=array();
    $JSON['new_friends']=$friend_alerts;
    $JSON['new_alerts']=$alerts;
    $JSON['new_messages']=$count;
    $JSON['new_message_id']=$new_message_id;
    echo json_encode($JSON);
    exit();
}