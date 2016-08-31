<?php
@include('init.php');
include('universal_functions.php');
$allowed="users";
include('security_checks.php');

$comment=clean_string($_POST['comment_text']);
$ID=(int)($_POST['user_id']);
$video_id=(int)($_POST['video_id']);

if($comment!='')
{
    if(is_id($ID)&&user_id_exists($ID)&&!user_id_terminated($ID))
    {
        $privacy=get_user_privacy_settings($ID);
        if($privacy[0][2]=='yes'||$ID==$_SESSION['id']||user_is_friends($ID, $_SESSION['id'])=='true')
        {
            $query=mysql_query("SELECT video_ids, video_comment_ids, video_comments, video_comment_timestamps, video_comment_likes, video_comment_dislikes, video_comments_users_sent FROM content WHERE user_id=$ID LIMIT 1");
            if($query&&mysql_num_rows($query)==1)
            {
                //gets the string array and explodes them
                $array=mysql_fetch_row($query);
                $video_ids=explode('|^|*|', $array[0]);
                $comment_ids=explode('|^|*|', $array[1]);
                $comments=explode('|^|*|', mysql_real_escape_string($array[2]));
                $timestamps=explode('|^|*|', $array[3]);
                $likes=explode('|^|*|', $array[4]);
                $dislikes=explode('|^|*|', $array[5]);
                $users_sent=explode('|^|*|', $array[6]);

                $index=-1;
                for($x = 0; $x < sizeof($video_ids); $x++)
                {
                    if($video_ids[$x]==$video_id)
                        $index=$x;
                }

                if($index!=-1)
                {
                    $likes[$index]=explode('|%|&|', $likes[$index]);
                    $dislikes[$index]=explode('|%|&|', $dislikes[$index]);
                    
                    for($y = 0; $y < sizeof($likes[$index]); $y++)
                    {
                        $likes[$index][$y]=explode('|@|$|', $likes[$index][$y]);
                        $dislikes[$index][$y]=explode('|@|$|', $dislikes[$index][$y]);
                    }


                    $comment_ids[$index]=explode('|%|&|', $comment_ids[$index]);
                    $temp_comments_index=$comments[$index];
                    $comments[$index]=explode('|%|&|', $comments[$index]);
                    $timestamps[$index]=explode('|%|&|', $timestamps[$index]);
                    $users_sent[$index]=explode('|%|&|', $users_sent[$index]);

                    //if there aren't already comments
                    if($temp_comments_index=='')
                    {
                        $new_comment_id=0;
                        $comment_ids[$index][0]=0;
                        $comments[$index][0]=$comment;
                        $timestamps[$index][0]=get_date();
                        $users_sent[$index][0]=$_SESSION['id'];
                        $likes[$index][0]=0;
                        $dislikes[$index][0]=0;
                    }
                    else
                    {
                        $new_comment_id=((int)(end($comment_ids[$index])))+1;
                        $comment_ids[$index][]=$new_comment_id;
                        $comments[$index][]=$comment;
                        $timestamps[$index][]=get_date();
                        $users_sent[$index][]=$_SESSION['id'];
                        $likes[$index][]=0;
                        $dislikes[$index][]=0;
                    }
                    $temp_comments_sent=$users_sent[$index];


                    for($y = 0; $y < sizeof($likes[$index]); $y++)
                    {
                        $likes[$index][$y]=implode('|@|$|', $likes[$index][$y]);
                        $dislikes[$index][$y]=implode('|@|$|', $dislikes[$index][$y]);
                    }
                    $likes[$index]=implode('|%|&|', $likes[$index]);
                    $dislikes[$index]=implode('|%|&|', $dislikes[$index]);


                    $comment_ids[$index]=implode('|%|&|', $comment_ids[$index]);
                    $comments[$index]=implode('|%|&|', $comments[$index]);
                    $timestamps[$index]=implode('|%|&|', $timestamps[$index]);
                    $users_sent[$index]=implode('|%|&|', $users_sent[$index]);


                    //final implode of the string arrays
                    $comment_ids=implode('|^|*|', $comment_ids);
                    $comments=implode('|^|*|', $comments);
                    $users_sent=implode('|^|*|', $users_sent);
                    $timestamps=implode('|^|*|', $timestamps);
                    $likes=implode('|^|*|', $likes);
                    $dislikes=implode('|^|*|', $dislikes);

                    //storing the new string arrays
                    $query=mysql_query("UPDATE content SET video_comment_ids='$comment_ids', video_comments='$comments', video_comment_likes='$likes', video_comment_dislikes='$dislikes', video_comments_users_sent='$users_sent', video_comment_timestamps='$timestamps' WHERE user_id=$ID LIMIT 1");
                    if($query)
                    {

                        //gets extra information for dynamic comment adding

                        $JSON=array();
                        $JSON['current_profile_picture']=get_profile_picture($_SESSION['id']);
                        $JSON['current_name']=get_user_name($_SESSION['id']);
                        $JSON['new_comment_id']=$new_comment_id;
                        $JSON['current_user']=$_SESSION['id'];
                        $JSON['errors']='';
                        $JSON['badges']=get_badges($_SESSION['id']);
                        echo json_encode($JSON);

                        //send alerts
                        if($_SESSION['id']!=$ID)
                        {
                            $information=array();
                            $information[0]='video_comment';
                            $information[1]=$ID;
                            $information[2]=$video_id;

                            add_alert($ID, $information);

                            $email=get_email_settings($ID, 'video_comments_on_post');
                            if($email==1)
                            {
                                $information[0]='video_comment_on_post';
                                send_mail_alert($ID, $information);
                            }
                        }

                        $already_sent=array();
                        $information=array();
                        $information[0]='video_comment_same_post';
                        $information[1]=$video_id;
                        $information[2]=$ID;
                        
                        for($x = 0; $x < sizeof($temp_comments_sent); $x++)
                        {
                            if(!in_array($temp_comments_sent[$x], $already_sent))
                            {
                                if($temp_comments_sent[$x]!=$_SESSION['id']&&$temp_comments_sent[$x]!=$ID)
                                {
                                    add_alert($temp_comments_sent[$x], $information);
                                    $already_sent[]=$temp_comments_sent[$x];
                                }
                            }
                        }

                        //records post comment
                        record_post_comment($video_id, $ID);
                    }
                    else
                    {
                        send_mail_error("comment_video.php: (2): ", mysql_error());

                        $JSON=array();
                        $JSON['current_profile_picture']='';
                        $JSON['current_name']='';
                        $JSON['new_comment_id']='';
                        $JSON['current_user']=$_SESSION['id'];
                        $JSON['errors']="Something went wrong";
                        $JSON['badges']=array();
                        echo json_encode($JSON);
                        exit();
                    }
                }
                else
                    echo "Video doesn't exist";
            }
            else
            {
                echo "Something went wrong. We are working on fixing it";
                log_error("comment_video.php: (1): ", mysql_error());
            }
        }
        else
            echo "You don't have the permission to comment";
    }
    else
        echo "Invalid user ID";
}
else
    echo "Your comment seems to be empty!";
