<?php
@include('init.php');
include("universal_functions.php");
$allowed="uses";
include("security_checks.php");

$num=(int)($_POST['num']);

//stores sent friends
if($num==1)
{
    $sent=$_POST['sent'];

    //validates input
    $invalid=false;
    for($x = 0; $x < sizeof($sent); $x++)
    {
        if(!is_id($sent[$x]))
            $invalid=true;
    }

    if($invalid==false)
    {
        $query=mysql_query("SELECT sent FROM facebook_invite WHERE user_id=$_SESSION[id] LIMIT 1");
        if($query&&mysql_num_rows($query)==1)
        {
            $array=mysql_fetch_row($query);
            $old_sent=explode('|^|*|', $array[0]);

            if($old_sent[0]=='')
                $old_sent=array();

            for($x = 0; $x < sizeof($sent); $x++)
            {
                if(!in_array($sent[$x], $old_sent))
                $old_sent[]=$sent[$x];
            }
            
            $sent=implode('|^|*|', $old_sent);
            $query=mysql_query("UPDATE facebook_invite SET sent='$sent' WHERE user_id=$_SESSION[id]");
        }
    }
}

//stores user friends
else if($num==2)
{
    $friends=$_POST['friends'];
    
    //validates input
    $invalid=false;
    $temp_friends=array();
    for($x = 0; $x < sizeof($friends); $x++)
    {
        if(!is_id($friends[$x]['id']))
            $invalid=true;
        else
            $temp_friends[]=$friends[$x]['id'];
    }
    $friends=implode('|^|*|', $temp_friends);

    if($invalid==false)
    {
        $query=mysql_query("UPDATE facebook_invite SET friends='$friends' WHERE user_id=$_SESSION[id]");
    }
}

//stores user in facebook_invite table
else if($num==3)
{
    $query=mysql_query("SELECT friends FROM facebook_invite WHERE user_id=$_SESSION[id]");
    if($query)
    {
        if(mysql_num_rows($query)==0)
            $query=mysql_query("INSERT INTO facebook_invite SET friends='', sent='', user_id=$_SESSION[id]");
    }
}