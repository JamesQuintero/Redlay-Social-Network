<?php
@include('init.php');
include('universal_functions.php');
$allowed="both";
include('security_checks.php');

$comment=clean_string($_POST['comment_text']);

//ID is profile id
$ID=(int)($_POST['profile_id']);

//poster_id is post user owner
$poster_id=(int)($_POST['poster_id']);
$post_id=(int)($_POST['post_id']);

if($comment!='')
{
    if(is_id($ID)&&user_id_exists($ID)&&!user_id_terminated($ID))
    {
        $privacy=get_user_privacy_settings($ID);
        if($privacy[0][2]=='yes'||$ID==$_SESSION['id']||user_is_friends($ID, $_SESSION['id']))
        {
            $query=mysql_query("SELECT post_ids, comment_ids, comments, comment_timestamps, comment_likes, comment_dislikes, comments_user_id FROM content WHERE user_id=$ID LIMIT 1");
            if($query&&mysql_num_rows($query)==1)
            {
                //gets the string array and explodes them
                $array=mysql_fetch_row($query);
                $post_ids=explode('|^|*|', $array[0]);
                $comment_ids=explode('|^|*|', $array[1]);
                $comments=explode('|^|*|', mysql_real_escape_string($array[2]));
                $timestamps=explode('|^|*|', $array[3]);
                $likes=explode('|^|*|', $array[4]);
                $dislikes=explode('|^|*|', $array[5]);
                $users_sent=explode('|^|*|', $array[6]);

                $index=-1;
                for($x = 0; $x < sizeof($post_ids); $x++)
                {
                    if($post_ids[$x]==$post_id)
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
                    $query=mysql_query("UPDATE content SET comment_ids='$comment_ids', comments='$comments', comment_likes='$likes', comment_dislikes='$dislikes', comments_user_id='$users_sent', comment_timestamps='$timestamps' WHERE user_id=$ID LIMIT 1");
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
                        if($_SESSION['id']!=$poster_id)
                        {
                            $information=array();
                            $information[0]='comment';
                            $information[1]=$post_id;
                            $information[2]=$ID;

                            add_alert($poster_id, $information);

                            $email=get_email_settings($poster_id, 'comments_on_post');
                            if($email==1)
                            {
                                $information[0]='comment_on_post';
                                send_mail_alert($poster_id, $information);
                            }
                        }

                        $already_sent=array();
                        $information=array();
                        $information[0]='comment_same_post';
                        $information[1]=$post_id;
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
                        record_post_comment($post_id, $ID);
                    }
                    else
                    {
                        log_error("comment.php: ", mysql_error());

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
                    echo "Post doesn't exist";
            }
            else
            {
                echo "Something went wrong. We are working on fixing it";
                log_error("comment.php: (1): ", mysql_error());
            }
        }
        else
            echo "You don't have the permission to comment";
    }
    else
        echo "There seems to be something wrong with the user ID";
}
else
    echo "Your comment seems to be empty!";