<?php
@include('init.php');
include('universal_functions.php');
$allowed="users";
include('security_checks.php');

$ID=(int)($_POST['user_id']);
$audience_options_list=$_POST['audience_options_list'];


if(is_id($ID)&&user_id_exists($ID))
{
    if(isset($audience_options_list[0]))
    {
    //checks if an audience group has been selected
    if(sizeof($audience_options_list)>0)
    {
       //checks whether all audience groups are valid
       $bool=true;
       for($x = 0; $x < sizeof($audience_options_list); $x++)
       {
          if(!is_valid_audience($audience_options_list[$x]))
             $bool=false;
       }
    }
    else
        $bool=false;
    
    if($bool==false)
    {
        $audience_options_list[0]='Friends';
        $bool=true;
    }

       if($bool==true)
       {
         if(pending_request($_SESSION['id'], $ID))
         {
               if(user_is_friends($_SESSION['id'], $ID)=='false')
               {
                  //adds user to current user's friend list
                  $query=mysql_query("SELECT user_friends, audience_groups, friend_timestamps FROM user_data WHERE user_id=$_SESSION[id] LIMIT 1");
                  if($query&&mysql_num_rows($query)==1)
                  {
                     $array=mysql_fetch_row($query);
                     $friends=explode('|^|*|', $array[0]);
                     $audience_groups=explode('|^|*|', $array[1]);
                     $friend_timestamps=explode('|^|*|', $array[2]);


                     $date=get_date();
                     if($friends[0]=='')
                     {
                           $friends[0]=$ID;
                           $audience_groups[0]=implode('|%|&|', $audience_options_list);
                           $friend_timestamps[0]=$date;
                     }
                     else
                     {
                           $friends[]=$ID;
                           $audience_groups[]=implode('|%|&|', $audience_options_list);
                           $friend_timestamps[]=$date;
                     }

                     $friends=implode('|^|*|', $friends);
                     $audience_groups=implode('|^|*|', $audience_groups);
                     $friend_timestamps=implode('|^|*|', $friend_timestamps);

                     $query=mysql_query("UPDATE user_data SET user_friends='$friends', audience_groups='$audience_groups', friend_timestamps='$friend_timestamps' WHERE user_id=$_SESSION[id]");
                     if($query)
                     {
                           //adds current user to user's friend's list
                           $query=mysql_query("SELECT user_friends, audience_groups, friend_timestamps FROM user_data WHERE user_id=$ID LIMIT 1");
                           $query2=mysql_query("SELECT other_user_id, audience FROM pending_friend_requests WHERE user_id=$ID LIMIT 1");
                           if($query&&mysql_num_rows($query)==1&&$query2&&mysql_num_rows($query2)==1)
                           {
                              $array=mysql_fetch_row($query);
                              $array2=mysql_fetch_row($query2);

                              $other_user_ids=explode('|^|*|', $array2[0]);
                              $audiences=explode('|^|*|', $array2[1]);

                              $friends=explode('|^|*|', $array[0]);
                              $audience_groups=explode('|^|*|', $array[1]);
                              $friend_timestamps=explode('|^|*|', $array[2]);

                              $index=-1;
                              for($x = 0; $x < sizeof($other_user_ids); $x++)
                              {
                                 if($other_user_ids[$x]==$_SESSION['id'])
                                       $index=$x;
                              }

                              //
                              $date=get_date();
                              if($array[0]=='')
                              {
                                 $friends[0]=$_SESSION['id'];
                                 $audience_groups[0]=$audiences[$index];
                                 $friend_timestamps[0]=$date+1;
                              }
                              else
                              {
                                 $friends[]=$_SESSION['id'];
                                 $audience_groups[]=$audiences[$index];
                                 $friend_timestamps[]=$date+1;
                              }

                              $friends=implode('|^|*|', $friends);
                              $audience_groups=implode('|^|*|', $audience_groups);
                              $friend_timestamps=implode('|^|*|', $friend_timestamps);

                              $query=mysql_query("UPDATE user_data SET user_friends='$friends', audience_groups='$audience_groups', friend_timestamps='$friend_timestamps' WHERE user_id=$ID");
                              if($query)
                              {
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
                                              //subtracts 1 from the number of add requests
                                              $query=mysql_query("SELECT new_friend_alerts FROM alerts WHERE user_id=$_SESSION[id] LIMIT 1");
                                              if($query&&mysql_num_rows($query)==1)
                                              {
                                                  $array=mysql_fetch_row($query);
                                                  $num_add_alerts=$array[0];
                                                  $num_add_alerts--;
                                                  $query=mysql_query("UPDATE alerts SET new_friend_alerts='$num_add_alerts' WHERE user_id=$_SESSION[id]");
                                                  if($query)
                                                  {
                                                      echo "User added!";
                                                      if($_SESSION['id']!=$ID)
                                                      {
                                                          $information=array();
                                                          $information[0]='accept_add_request';

                                                          add_alert($ID, $information);
                                                          $email=get_email_settings($ID, 'accept_add_request');
                                                          if($email==1)
                                                              send_mail_alert($ID, $information);
                                                      }
                                                  }
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
                              }
                              else
                              {
                                 echo "Something went wrong. We are working to fix it. 4";
                                 log_error("accept_friend.php: ", mysql_error());
                              }
                           }
                           else
                           {
                              echo "Something went wrong. We are working to fix it. 3";
                              log_error("accept_friend.php: ", mysql_error());
                           }
                     }
                     else
                     {
                           echo "Something went wrong. We are working to fix it. 2";
                           log_error("accept_friend.php: ", mysql_error());
                     }
                  }
                  else
                  {
                     echo "Something went wrong. We are working to fix it. 1";
                     log_error("accept_friend.php: ", mysql_error());
                  }
               }
               else
                  echo "You are already friends with this user";
         }
         else
               echo "This user has not sent you a friend request";
       }
    }
    else
        echo "Please choose a group or groups to add this user to";
}
else
    echo "User ID is invalid";