<?php
@include('init.php');
include('universal_functions.php');
$allowed="users";
include('security_checks.php');

$num=(int)($_POST['num']);
$timezone=(int)($_POST['timezone']);
$page=(int)($_POST['page'])*20;

//gets names of users on message list
if($num==1)
{
    $query=mysql_query("SELECT * FROM messages WHERE user_id=$_SESSION[id] LIMIT 1");
    if($query&&mysql_num_rows($query)==1)
    {
        $array=mysql_fetch_array($query);
        $user_id_2=explode('|^|*|', $array['user_id_2']);
        $ids=explode('|^|*|', $array['users_listed']);
        $messages=explode('|^|*|', mysql_real_escape_string($array['messages']));
        $new_messages=explode('|^|*|', $array['new_messages']);
        $users_sent=explode('|^|*|', $array['user_sent']);
        $timestamps=explode('|^|*|', $array['timestamps']);
        
        //checks for terminated accounts
        $temp_user_id_2=array();
        $temp_messages=array();
        $temp_new_messages=array();
        $temp_users_sent=array();
        $temp_timestamps=array();
        $changed=false;
        for($x = 0; $x < sizeof($ids); $x++)
        {
           if(user_id_exists($user_id_2[$x])&&!user_id_terminated($user_id_2[$x]))
           {
              $temp_user_id_2[]=$user_id_2[$x];
              $temp_messages[]=$messages[$x];
              $temp_new_messages[]=$new_messages[$x];
              $temp_users_sent[]=$users_sent[$x];
              $temp_timestamps[]=$timestamps[$x];
           }
           else
              $changed=true;
        }
        
        //checks listed users for terminated accounts
        $temp_ids=array();
        for($x = 0; $x < sizeof($ids); $x++)
        {
            if(user_id_exists($ids[$x])&&!user_id_terminated($ids[$x]))
            {
                $temp_ids[]=$ids[$x];
            }
            else
                $changed=true;
        }
        
        if($changed==true)
        {
            $temp_temp_ids=implode('|^|*|', $temp_ids);
           $temp_temp_user_id_2=implode('|^|*|', $temp_user_id_2);
           $temp_temp_messages=implode('|^|*|', $temp_messages);
           $temp_temp_new_messages=implode('|^|*|', $temp_new_messages);
           $temp_temp_users_sent=implode('|^|*|', $temp_users_sent);
           $temp_temp_timestamps=implode('|^|*|', $temp_timestamps);
           $query=mysql_query("UPDATE messages SET user_id_2='$temp_temp_user_id_2', users_listed='$temp_temp_ids', messages='$temp_temp_messages', new_messages='$temp_temp_new_messages', user_sent='$temp_temp_users_sent', timestamps='$temp_temp_timestamps' WHERE user_id=$_SESSION[id]");
           if($query)
           {
              $ids=$temp_ids;
              $new_messages=$temp_new_messages;
           }
           else
           {
              echo "Something went wrong. We are working on fixing it";
              send_mail_error("message_query.php: (1): ", mysql_error());
           }
        }
        
        //rearranges new messages to match listed users
        $temp_new_messages=array();
        $temp_user_id_2=$user_id_2;
        for($x = 0; $x < sizeof($ids); $x++)
        {
            $number=array_search($ids[$x], $temp_user_id_2);
            $temp_user_id_2[$number]='';
            
            $temp_new_messages[$x]=$new_messages[$number];
        }
        $new_messages=$temp_new_messages;
        
        
        //gets extra stuff
        $names=array();
        $profile_pictures=array();
        for($x = 0; $x < sizeof($ids); $x++)
        {
            $names[$x]=get_user_name($ids[$x]);
            $profile_pictures[$x]=get_profile_picture($ids[$x]);
        }

        if($names[0]!='')
        {
            $JSON=array();
            $JSON['names']=$names;
            $JSON['user_ids']=$ids;
            $JSON['new_messages']=$new_messages;
            $JSON['profile_pictures']=$profile_pictures;
            echo json_encode($JSON);
            exit();
        }
        else
        {
            $JSON=array();
            $JSON['names']='none';
            $JSON['user_ids']='none';
            $JSON['new_messages']='none';
            $JSON['profile_pictures']=array();
            echo json_encode($JSON);
            exit();
        }
    }
}

//gets specific user's messages
else if($num==2)
{
    $ID=(int)($_POST['user_id']);
//   $ID=(int)($_GET['user_id']);
    
    if(is_id($ID)&&user_id_exists($ID))
    {
        $query=mysql_query("SELECT messages, timestamps, user_sent, user_id_2, new_messages FROM messages WHERE user_id=$_SESSION[id] LIMIT 1");
        if($query&&mysql_num_rows($query)==1)
        {
            $array=mysql_fetch_row($query);
            $messages=explode('|^|*|', $array[0]);
            $timestamps=explode('|^|*|', $array[1]);
            $users_sent=explode('|^|*|', $array[2]);
            $user_ids_2=explode('|^|*|', $array[3]);
            $new_messages=explode('|^|*|', $array[4]);
            
            //gets index
            $index=-1;
            for($x = 0; $x < sizeof($user_ids_2); $x++)
            {
                if($user_ids_2[$x]==$ID)
                    $index=$x;
            }
            
            if($index!=-1)
            {
                //resets number of unread messages
                $new_messages[$index]=0;
                $new_messages=implode('|^|*|', $new_messages);
                $query=mysql_query("UPDATE messages SET new_messages='$new_messages' WHERE user_id=$_SESSION[id]");
                
                
                //explodes everything
                $messages=explode('|%|&|', $messages[$index]);
                $timestamps=explode('|%|&|', $timestamps[$index]);
                $users_sent=explode('|%|&|', $users_sent[$index]);



                if($array[1]!='')
                    $total_size=sizeof($messages);
                else
                    $total_size=0;


                if($total_size<20)
                {
                    $empty=true;

                    //reverses because it adds backwards in the else statement below
                    $temp_messages=array();
                    $temp_timestamps=array();
                    $temp_users_sent=array();

                    for($x = sizeof($messages)-1; $x >=0; $x--)
                    {
                        $temp_messages[]=$messages[$x];
                        $temp_timestamps[]=$timestamps[$x];
                        $temp_users_sent[]=$users_sent[$x];
                    }

                    $messages=$temp_messages;
                    $timestamps=$temp_timestamps;
                    $users_sent=$temp_users_sent;
                }
                else
                {
                    if($total_size-$page<=0)
                        $empty=true;
                    else
                        $empty=false;

                    $temp_messages=array();
                    $temp_timestamps=array();
                    $temp_users_sent=array();

                    $index=sizeof($messages)-$page+20-($page/20);

                    while(sizeof($temp_messages)<=20)
                    {
                        if($messages[$index]!='')
                        {
                            $temp_messages[]=$messages[$index];
                            $temp_timestamps[]=$timestamps[$index];
                            $temp_users_sent[]=$users_sent[$index];
                        }
                        else
                        {
                            $temp_messages[]='';
                            $temp_timestamps[]='';
                            $temp_users_sent[]='';
                        }

                        $index--;
                    }

                    $messages=$temp_messages;
                    $timestamps=$temp_timestamps;
                    $users_sent=$temp_users_sent;
                }



                //gets correct timestamps
                $profile_pictures=array();
                $timestamps_seconds=array();
                $message_spaces=array();
                for($x = 0; $x < sizeof($timestamps); $x++)
                {
                    //gets timestamp
                    $temp_timestamp=$timestamps[$x];
                    $timestamps[$x]=get_time_since($timestamps[$x], $timezone);
                    
                    //gets timestamp seconds
                    $timestamps_seconds[$x]=get_time_since_seconds($temp_timestamp, $timezone);
                    
                    //gets profile picture
                    $profile_pictures[$x]=get_profile_picture($users_sent[$x]);
                    
                    //gets message with new lines
                    $message_spaces[$x]=nl2br($messages[$x]);
                }

                
                //gets user's names
                $names=array();
                for($x = 0; $x < sizeof($users_sent); $x++)
                    $names[$x]=get_user_name($users_sent[$x]);

                $JSON=array();
                $JSON['messages']=$messages;
                $JSON['message_spaces']=$message_spaces;
                $JSON['message_timestamps']=$timestamps;
                $JSON['message_timestamps_seconds']=$timestamps_seconds;
                $JSON['message_user_sent']=$users_sent;
                $JSON['message_names']=$names;
                $JSON['profile_pictures']=$profile_pictures;
                $JSON['total_size']=$total_size;
                $JSON['empty']=$empty;
                echo json_encode($JSON);
                exit();
            }
        }
    }
}

//adds user to list of users
else if($num==3)
{
    $ID=(int)($_POST['user_id']);
    if(is_id($ID)&&user_id_exists($ID)&&!user_id_terminated($ID)&&user_is_friends($ID, $_SESSION['id']))
    {
        $query=mysql_query("SELECT user_id_2, users_listed, messages, new_messages, user_sent, timestamps FROM messages WHERE user_id=$_SESSION[id] LIMIT 1");
        if($query&&mysql_num_rows($query)==1)
        {
            $array=mysql_fetch_row($query);
            $other_users=explode('|^|*|', $array[0]);
            $users_listed=explode('|^|*|', $array[1]);
            

            //if ID hasn't been messaged yet by current user
            if(!in_array($ID, $other_users))
            {
                //gets the content that is going to be used
                $messages=explode('|^|*|', $array[2]);
                $new_messages=explode('|^|*|', $array[3]);
                $users_sent=explode('|^|*|', $array[4]);
                $timestamps=explode('|^|*|', $array[5]);
                
                //if current user hasn't messaged anyone yet
                if($array[0]=='')
                {
                    $other_users[0]=$ID;
                    $users_listed[0]=$ID;
                    $messages[0]='';
                    $new_messages[0]=0;
                    $users_sent[0]='';
                    $timestamps[0]='';
                }
                else
                {
                    $other_users[]=$ID;
                    $users_listed[]=$ID;
                    $messages[]='';
                    $new_messages[]=0;
                    $users_sent[]='';
                    $timestamps[]='';
                }

                $other_users=implode('|^|*|', $other_users);
                $users_listed=implode('|^|*|', $users_listed);
                $messages=implode('|^|*|', $messages);
                $new_messages=implode('|^|*|', $new_messages);
                $users_sent=implode('|^|*|', $users_sent);
                $timestamps=implode('|^|*|', $timestamps);

                $query=mysql_query("UPDATE messages SET user_id_2='$other_users', users_listed='$users_listed', messages='$messages', new_messages='$new_messages', user_sent='$users_sent', timestamps='$timestamps' WHERE user_id=$_SESSION[id]");
                if(!$query)
                {
                    echo "Something went wrong";
                    log_error("messages_query.php: (2): ", mysql_error());
                }
            }
            
            //if ID has already been messaged, just add ID to list of messaging users
            else
            {
                $users_listed[]=$ID;
                
                $users_listed=implode('|^|*|', $users_listed);
                $query=mysql_query("UPDATE messages SET users_listed='$users_listed' WHERE user_id=$_SESSION[id]");
            }
        }
    }
}

//deletes user from list of users
else if($num==4)
{
    $ID=(int)($_POST['user_id']);
    
    if(is_id($ID)&&user_id_exists($ID)&&user_is_friends($ID, $_SESSION['id']))
    {
        $query=mysql_query("SELECT users_listed FROM messages WHERE user_id=$_SESSION[id] LIMIT 1");
        if($query&&mysql_num_rows($query)==1)
        {
            $array=mysql_fetch_row($query);
            $users_listed=explode('|^|*|', $array[0]);
            
            $temp_users_listed=array();
            for($x = 0; $x < sizeof($users_listed); $x++)
            {
                if($users_listed[$x]!=$ID)
                    $temp_users_listed[]=$users_listed[$x];
            }
            $users_listed=implode('|^|*|', $temp_users_listed);
            
            
            $query=mysql_query("UPDATE messages SET users_listed='$users_listed' WHERE user_id=$_SESSION[id]");
            if(!$query)
            {
                echo "Something went wrong";
                log_error("messages_query.php: (3): ", mysql_error());
            }
        }
    }
}

//gets adds
else if($num==5)
{
    //determines whether current user has messaged selected add
    $query=mysql_query("SELECT user_id_2, users_listed FROM messages WHERE user_id=$_SESSION[id] LIMIT 1");
    if($query&&mysql_num_rows($query)==1)
    {
        $array=mysql_fetch_row($query);
        $other_users=explode('|^|*|', $array[0]);
        $users_listed=explode('|^|*|', $array[1]);
    }
    
    $profile_pictures=array();
    $names=array();
    $num_adds=array();
    $is_added=array();
    for($x = 0; $x < sizeof($other_users); $x++)
    {
        if(in_array($other_users[$x], $users_listed))
            $is_added[$x]=true;
        else
            $is_added[$x]=false;
        
        //gets profile pictures
        $profile_pictures[$x]=get_profile_picture($other_users[$x]);
        
        //gets names
        $names[$x]=get_user_name($other_users[$x]);

        //gets number of adds
        $query=mysql_query("SELECT user_friends FROM user_data WHERE user_id=$other_users[$x] LIMIT 1");
        if($query&&mysql_num_rows($query)==1)
        {
            $array=mysql_fetch_row($query);
            $num_adds[$x]=sizeof(explode('|^|*|', $array[0]));
        }
    }

    $JSON=array();
    $JSON['adds']=$other_users;
    $JSON['profile_pictures']=$profile_pictures;
    $JSON['names']=$names;
    $JSON['num_adds']=$num_adds;
    $JSON['is_added']=$is_added;
    echo json_encode($JSON);
    exit();
}

//gets group chat names
else if($num==6)
{
    $query=mysql_query("SELECT chat_id, chat_name, new_messages FROM group_chats WHERE user_id=$_SESSION[id] LIMIT 20");
    if($query)
    {
        $array=array();
        $chat_ids=array();
        $chat_names=array();
        $new_messages=array();
        for($x = 0; $x < mysql_num_rows($query); $x++)
        {
            $array[$x]=mysql_fetch_row($query);
            $chat_ids[$x]=$array[$x][0];
            $chat_names[$x]=$array[$x][1];
            $new_messages[$x]=$array[$x][2];
        }
        
        $JSON=array();
        $JSON['chat_ids']=$chat_ids;
        $JSON['chat_names']=$chat_names;
        $JSON['new_messages']=$new_messages;
        echo json_encode($JSON);
        exit();
    }
    else
    {
        echo "Something went wrong";
        log_error("messages_query.php: (4): ", mysql_error());
    }
}

//creates a new chat
else if($num==7)
{
    $name=clean_string($_POST['new_chat_name']);
    
    if($name!='')
    {
        //if name already exists with current user
        $query=mysql_query("SELECT chat_id FROM group_chats WHERE chat_name='".$name."' AND user_id=$_SESSION[id] LIMIT 1");
        if(mysql_num_rows($query)==1)
        {
            $JSON=array();
            $JSON['chat_id']="";
            $JSON['errors']="You already have a chat with this name";
            echo json_encode($JSON);
            exit();
        }
        
        //if name isn't already in use
        else
        {
            $query=mysql_query("INSERT INTO group_chats SET user_id=$_SESSION[id], chat_name='".$name."', user_ids='$_SESSION[id]' ");
            if($query)
            {
                $query=mysql_query("SELECT chat_id FROM group_chats WHERE chat_name='$name' AND user_id=$_SESSION[id] LIMIT 1");
                
                $array=mysql_fetch_row($query);
                $chat_id=$array[0];
                
                $JSON=array();
                $JSON['chat_id']=$chat_id;
                $JSON['errors']="";
                echo json_encode($JSON);
                exit();
            }
        }
        
    }
    else
    {
        $JSON=array();
        $JSON['chat_id']="";
        $JSON['errors']="Name can't be empty";
        echo json_encode($JSON);
        exit();
    }
}

//gets chat information
else if($num==8)
{
    $chat_id=(int)($_POST['chat_id']);
    $query=mysql_query("SELECT user_id, chat_name, user_ids, new_messages FROM group_chats WHERE chat_id=$chat_id  LIMIT 1");
    if($query&&mysql_num_rows($query)==1)
    {
        $array=mysql_fetch_row($query);
        $user_id=$array[0];
        $chat_name=$array[1];
        $user_ids=explode('|^|*|', $array[2]);
        $new_messages=explode('|^|*|', $array[3]);
        
        
        $query=mysql_query("SELECT user_friends FROM user_data WHERE user_id=$_SESSION[id] LIMIT 1");
        if($query&&mysql_num_rows($query)==1)
        {
            $array=mysql_fetch_row($query);
            $adds=explode('|^|*|', $array[0]);
        }
        

        $profile_pictures=array();
        $names=array();
        $is_added=array();
        for($x = 0; $x < sizeof($adds); $x++)
        {
            if(in_array($user_ids[$x], $adds))
                $is_added[$x]=true;
            else
                $is_added[$x]=false;

            //gets profile pictures
            $profile_pictures[$x]=get_profile_picture($adds[$x]);

            //gets names
            $names[$x]=get_user_name($adds[$x]);
        }
        
        
        
        $creator_name=get_user_name($user_id);
        
        $JSON=array();
        $JSON['adds']=$adds;
        $JSON['profile_pictures']=$profile_pictures;
        $JSON['names']=$names;
        $JSON['is_member']=$is_added;
        $JSON['creator']=$user_id;
        $JSON['creator_name']=$creator_name;
        $JSON['chat_name']=$chat_name;
        $JSON['members']=$user_ids;
        $JSON['num_new_messages']=$new_messages;
        echo json_encode($JSON);
        exit();
    }
}