<?php
@include('init.php');
include('universal_functions.php');
$allowed="users";
include('security_checks.php');

$ID=(int)($_POST['user_id']);
$message=clean_string($_POST['message']);
$audience=$_POST['audience'];


if(is_id($ID)&&user_id_exists($ID))
{
    $privacy=get_user_privacy_settings($ID);
    $general=$privacy[0];
    if($general[0]=='yes')
    {
        if(isset($audience[0]))
        {
           if(strlen($message)<=250)
           {
              if(is_id($ID)&&user_id_exists($ID))
              {
                 //checks whether all audience groups are valid
                 $bool=true;
                 for($x = 0; $x < sizeof($audience); $x++)
                 {
                    if(!is_valid_audience($audience[$x]))
                       $bool=false;
                 }

                 if($bool==false)
                {
                    $audience[0]='Friends';
                    $bool=true;
                }

                 if($bool==true)
                 {
                       if(user_is_friends($_SESSION['id'], $ID)=="false")
                       {
                          if(!user_blocked($ID, $_SESSION['id'], 'user'))
                          {
                             if(!pending_request($_SESSION['id'], $ID))
                             {
                                   $query=mysql_query("SELECT * FROM pending_friend_requests WHERE user_id=$_SESSION[id] LIMIT 1");
                                   $query2=mysql_query("SELECT * FROM pending_friend_requests WHERE user_id=$ID LIMIT 1");
                                   if($query&&mysql_num_rows($query)==1&&$query2&&mysql_num_rows($query2)==1)
                                   {
                                      $array=mysql_fetch_array($query);
                                      $array2=mysql_fetch_array($query2);
                                      $other_user_ids=explode('|^|*|', $array['other_user_id']);
                                      $other_user_ids2=explode('|^|*|', $array2['other_user_id']);
                                      $users_sent=explode('|^|*|', $array['user_sent']);
                                      $users_sent2=explode('|^|*|', $array2['user_sent']);
                                      $timestamps=explode('|^|*|', $array['timestamp']);
                                      $timestamps2=explode('|^|*|', $array2['timestamp']);
                                      $messages=explode('|^|*|', mysql_real_escape_string($array['message']));
                                      $messages2=explode('|^|*|', mysql_real_escape_string($array2['message']));
                                      $audiences=explode('|^|*|', $array['audience']);
                                      $audiences2=explode('|^|*|', $array2['audience']);

                                      $date=get_date();
                                      if($array['other_user_id']=='')
                                      {
                                         $other_user_ids[0]=$ID;
                                         $users_sent[0]=$_SESSION['id'];
                                         $timestamps[0]=$date;
                                         $messages[0]=$message;
                                         $audiences[0]=implode('|%|&|', $audience);
                                      }
                                      else
                                      {
                                         $other_user_ids[]=$ID;
                                         $users_sent[]=$_SESSION['id'];
                                         $timestamps[]=$date;
                                         $messages[]=$message;
                                         $audiences[]=implode('|%|&|', $audience);
                                      }

                                      if($array2['other_user_id']=='')
                                      {
                                         $other_user_ids2[0]=$_SESSION['id'];
                                         $users_sent2[0]=$_SESSION['id'];
                                         $timestamps2[0]=$date;
                                         $messages2[0]=$message;
                                         $audiences2[0]=implode('|%|&|', $audience);
                                      }
                                      else
                                      {
                                         $other_user_ids2[]=$_SESSION['id'];
                                         $users_sent2[]=$_SESSION['id'];
                                         $timestamps2[]=$date;
                                         $messages2[]=$message;
                                         $audiences2[]=implode('|%|&|', $audience);
                                      }


                                      $other_user_ids=implode('|^|*|', $other_user_ids);
                                      $users_sent=implode('|^|*|', $users_sent);
                                      $timestamps=implode('|^|*|', $timestamps);
                                      $other_user_ids2=implode('|^|*|', $other_user_ids2);
                                      $users_sent2=implode('|^|*|', $users_sent2);
                                      $timestamps2=implode('|^|*|', $timestamps2);
                                      $messages=implode('|^|*|', $messages);
                                      $messages2=implode('|^|*|', $messages2);
                                      $audiences=implode('|^|*|', $audiences);
                                      $audiences2=implode('|^|*|', $audiences2);


                                      $query=mysql_query("UPDATE pending_friend_requests SET other_user_id='$other_user_ids', user_sent='$users_sent', timestamp='$timestamps', message='$messages', audience='$audiences' WHERE user_id=$_SESSION[id]");
                                      $query2=mysql_query("UPDATE pending_friend_requests SET other_user_id='$other_user_ids2', user_sent='$users_sent2', timestamp='$timestamps2', message='$messages2', audience='$audiences2' WHERE user_id=$ID");
                                      if($query&&$query2)
                                      {
                                         $query=mysql_query("SELECT new_friend_alerts FROM alerts WHERE user_id=$ID");
                                         if($query)
                                         {
                                               $array=mysql_fetch_row($query);
                                               $new_friend_alerts=$array[0];

                                               $new_friend_alerts++;

                                               $query=mysql_query("UPDATE alerts SET new_friend_alerts='$new_friend_alerts' WHERE user_id=$ID");
                                               if($query)
                                                  echo "Add request sent!";
                                               else
                                               {
                                                  echo "Something is wrong with the database. We are working on it";
                                                  log_error("add_friend.php: (4): ", mysql_error());
                                               }
                                         }
                                         else
                                         {
                                            echo "Something is wrong with the database. We are working on it";
                                            log_error("add_friend.php: (3): ", mysql_error());
                                         }
                                      }
                                      else
                                      {
                                         echo "Something is wrong with the database. We are working on it";
                                         log_error("add_friend.php: (2): ", mysql_error());
                                      }

                                   }
                                   else
                                   {
                                      echo "Something is wrong with the database. We are working on it";
                                      log_error("add_friend.php: (1): ", mysql_error());
                                   }
                             }
                             else
                                   echo "You have already sent a friend request";
                          }
                          else
                             echo "You can't friend request someone you have blocked!";
                       }
                       else
                          echo "You are already friends!";
                 }
                 else
                       echo "Invalid Group";
              }
              else
                 echo "Invalid ID";
           }
           else
              echo "Message is too long";
        }
        else
            echo "Please choose a group or groups to add this user to";
    }
    else
        echo "This user has blocked add requests";
}