<?php
@include('init.php');
include('universal_functions.php');
$allowed="users";
include('security_checks.php');

$ID=(int)($_POST['user_id']);
$new_message=nl2br($_POST['message']);
$message=clean_string($_POST['message']);

$sent=false;

if(is_id($ID) && user_id_exists($ID) && !user_id_terminated($ID))
{
   if($message!='')
   {
   
      //adds message to current user's data
      $query=mysql_query("SELECT user_id_2, users_listed, messages, timestamps, user_sent, new_messages FROM messages WHERE user_id=$_SESSION[id] LIMIT 1");
      if($query&&mysql_num_rows($query)==1)
      {
         $array=mysql_fetch_row($query);
         $other_users=explode('|^|*|', $array[0]);
         $users_listed=explode('|^|*|', $array[1]);
         $messages=explode('|^|*|', mysql_real_escape_string($array[2]));
         $timestamps=explode('|^|*|', $array[3]);
         $users_sent=explode('|^|*|', $array[4]);
         $new_messages=explode('|^|*|', $array[5]);


         //if haven't messaged before
         if(!in_array($ID, $other_users))
         {
            if($array[0]!='')
            {
                  $other_users[]=$ID;
                  $messages[]=$message;
                  $timestamps[]=get_date();
                  $users_sent[]=$_SESSION['id'];
                  $new_messages[]=0;
                  $users_listed[]=$ID;
            }
            else
            {
                  $other_users[0]=$ID;
                  $messages[0]=$message;
                  $timestamps[0]=get_date();
                  $new_messages[0]=0;
                  $users_sent[0]=$_SESSION['id'];
                  $users_listed[0]=$ID;
            }
         }
         else
         {
            //gets index of ID
            $index=-1;
            for($x = 0; $x < sizeof($other_users); $x++)
            {
                  if($ID==$other_users[$x])
                     $index=$x;
            }

            if($index!=-1)
            {
                  $messages[$index]=explode('|%|&|', $messages[$index]);
                  $timestamps[$index]=explode('|%|&|', $timestamps[$index]);
                  $users_sent[$index]=explode('|%|&|', $users_sent[$index]);

                  if($messages[$index][0]!='')
                  {
                    $messages[$index][]=$message;
                    $timestamps[$index][]=get_date();
                    $users_sent[$index][]=$_SESSION['id'];
//                    $new_messages[$index]=$new_messages[$index]++;
                  }
                  else
                  {
                      $messages[$index][0]=$message;
                    $timestamps[$index][0]=get_date();
                    $users_sent[$index][0]=$_SESSION['id'];
//                    $new_messages[$index]=$new_messages[$index]++;
                  }

                  $messages[$index]=implode('|%|&|', $messages[$index]);
                  $timestamps[$index]=implode('|%|&|', $timestamps[$index]);
                  $users_sent[$index]=implode('|%|&|', $users_sent[$index]);
            }
            
            if(!in_array($ID, $users_listed))
            {
                if($array[1]!='')
                    $users_listed[]=$ID;
                else
                    $users_listed[0]=$ID;
            }
         }
         
         $other_users=implode('|^|*|', $other_users);
         $users_listed=implode('|^|*|', $users_listed);
         $messages=implode('|^|*|', $messages);
         $timestamps=implode('|^|*|', $timestamps);
         $users_sent=implode('|^|*|', $users_sent);
         $new_messages=implode('|^|*|', $new_messages);

         $query=mysql_query("UPDATE messages SET user_id_2='$other_users', users_listed='$users_listed', messages='$messages', timestamps='$timestamps', user_sent='$users_sent', new_messages='$new_messages' WHERE user_id=$_SESSION[id]");
         if($query)
            $sent=true;
         else
         {
            $sent=false;
            $errors="Something went wrong. We are working on fixing it";
            log_error("message_user.php: ", mysql_error());
         }
      }
      else
      {
         $errors="Something went wrong. We are working on fixing it";
         log_error("message_user.php: ", mysql_error());
      }


      //adds message to other user's data
      $query=mysql_query("SELECT user_id_2, users_listed, messages, timestamps, user_sent, new_messages FROM messages WHERE user_id=$ID LIMIT 1");
      if($query&&mysql_num_rows($query)==1)
      {
         $array=mysql_fetch_row($query);
         $other_users=explode('|^|*|', $array[0]);
         $users_listed=explode('|^|*|', $array[1]);
         $messages=explode('|^|*|', mysql_real_escape_string($array[2]));
         $timestamps=explode('|^|*|', $array[3]);
         $users_sent=explode('|^|*|', $array[4]);
         $new_messages=explode('|^|*|', $array[5]);

         //if haven't messaged before
         if(!in_array($_SESSION['id'], $other_users))
         {
            if($array[0]!='')
            {
                  $other_users[]=$_SESSION['id'];
                  $users_listed[]=$_SESSION['id'];
                  $messages[]=$message;
                  $timestamps[]=get_date();
                  $users_sent[]=$_SESSION['id'];
                  $new_messages[]=1;
            }
            else
            {
                  $other_users[0]=$_SESSION['id'];
                  $users_listed[0]=$_SESSION['id'];
                  $messages[0]=$message;
                  $timestamps[0]=get_date();
                  $users_sent[0]=$_SESSION['id'];
                  $new_messages[0]=1;
            }
         }
         else
         {
            //gets index of ID
            $index=-1;
            for($x = 0; $x < sizeof($other_users); $x++)
            {
                  if($_SESSION['id']==$other_users[$x])
                     $index=$x;
            }

            if($index!=-1)
            {
                  $messages[$index]=explode('|%|&|', $messages[$index]);
                  $timestamps[$index]=explode('|%|&|', $timestamps[$index]);
                  $users_sent[$index]=explode('|%|&|', $users_sent[$index]);

                  $messages[$index][]=$message;
                  $timestamps[$index][]=get_date();
                  $users_sent[$index][]=$_SESSION['id'];
                  $new_messages[$index]++;

                  $messages[$index]=implode('|%|&|', $messages[$index]);
                  $timestamps[$index]=implode('|%|&|', $timestamps[$index]);
                  $users_sent[$index]=implode('|%|&|', $users_sent[$index]);
            }
            
            if(!in_array($_SESSION['id'], $users_listed))
            {
                if($array[1]!='')
                    $users_listed[]=$_SESSION['id'];
                else
                    $users_listed[0]=$_SESSION['id'];
            }
                
         }
         $other_users=implode('|^|*|', $other_users);
         $users_listed=implode('|^|*|', $users_listed);
         $messages=implode('|^|*|', $messages);
         $timestamps=implode('|^|*|', $timestamps);
         $users_sent=implode('|^|*|', $users_sent);
         $new_messages=implode('|^|*|', $new_messages);

         $query=mysql_query("UPDATE messages SET user_id_2='$other_users', users_listed='$users_listed', messages='$messages', timestamps='$timestamps', user_sent='$users_sent', new_messages='$new_messages' WHERE user_id=$ID");
         if($query&&$sent)
         {
             
            //send email that user messaged current user
            $emails=get_email_settings($ID, 'message');
            if($emails==1)
            {
                  $information=array();
                  $information[0]='message';

                  if(is_online($ID)==false)
                    send_mail_alert($ID, $information);
            }

            $errors="Message sent!";
         }
         else
         {
            $errors="Something went wrong. We are working on fixing it";
            log_error("message_user.php: ", mysql_error());
         }
      }
      else
      {
         $errors="Something went wrong. We are working on fixing it";
         log_error("message_user.php: ", mysql_error());
      }
   }
   else
      $errors="You can't send an empty message.";
}

$JSON=array();
$JSON['current_user']=$_SESSION['id'];
$JSON['user_name']=get_user_name($_SESSION['id']);
$JSON['profile_picture']=get_profile_picture($_SESSION['id']);
$JSON['new_message']=$new_message;
$JSON['errors']=$errors;
echo json_encode($JSON);
exit();
