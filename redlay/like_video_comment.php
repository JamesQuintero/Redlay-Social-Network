<?php
@include('init.php');
include('universal_functions.php');
$allowed="users";
include('security_checks.php');


$video_id=(int)($_POST['video_id']);
$comment_id=(int)($_POST['comment_id']);
$profile_id=(int)($_POST['profile_id']);


$privacy=get_user_privacy_settings($profile_id);
$general=$privacy[0];

if($general[2]=='yes'||user_is_friends($profile_id, $_SESSION['id'])=='true'||$profile_id==$_SESSION['id'])
{
    if(is_id($profile_id)&&user_id_exists($profile_id)&&$comment_id>=0)
    {
        $query=mysql_query("SELECT video_ids, video_comment_likes, video_comments_users_sent, video_comment_ids FROM content WHERE user_id=$profile_id LIMIT 1");
        if($query&&mysql_num_rows($query)==1)
        {
            $array=mysql_fetch_row($query);
            $video_ids=explode('|^|*|', $array[0]);
            $comment_likes=explode('|^|*|', $array[1]);
            $comments_users_sent=explode('|^|*|', $array[2]);
            $comment_ids=explode('|^|*|', $array[3]);

            //gets post index
            $index=-1;
            for($x = 0; $x < sizeof($video_ids); $x++)
            {
                if($video_ids[$x]==$video_id)
                    $index=$x;
            }

            if($index!=-1)
            {
                $comment_ids[$index]=explode('|%|&|', $comment_ids[$index]);
                $comment_index=-1;
                for($x = 0; $x < sizeof($comment_ids[$index]); $x++)
                {
                    if($comment_id==$comment_ids[$index][$x])
                        $comment_index=$x;
                }

                if($comment_index!=-1)
                {
                    $comment_likes[$index]=explode('|%|&|', $comment_likes[$index]);
                    $comments_users_sent[$index]=explode('|%|&|', $comments_users_sent[$index]);
                    $comment_likes[$index][$comment_index]=explode('|@|$|', $comment_likes[$index][$comment_index]);
                    
                    $comment_poster_id=$comments_users_sent[$index][$comment_index];
 

                    if($comment_poster_id!=$_SESSION['id'])
                    {
                        if($comment_likes[$index][$comment_index][0]=='')
                            $comment_likes[$index][$comment_index][0]=$_SESSION['id'];
                        else
                        {
                            if(!in_array($_SESSION['id'], $comment_likes[$index][$comment_index]))
                                $comment_likes[$index][$comment_index][]=$_SESSION['id'];
                        }
                        
                    }

                    $comment_likes[$index][$comment_index]=implode('|@|$|', $comment_likes[$index][$comment_index]);
                    $comment_likes[$index]=implode('|%|&|', $comment_likes[$index]);
                    $comment_likes=implode('|^|*|', $comment_likes);

                    $query=mysql_query("UPDATE content SET video_comment_likes='$comment_likes' WHERE user_id=$profile_id");
                    if($query)
                    {
                        echo "Comment liked";
                        if($_SESSION['id']!=$comment_poster_id)
                        {
                            $information=array();
                            $information[0]='video_comment_like';
                            $information[1]=$profile_id;
                            $information[2]=$video_id;

                            //adds point
                            add_point($comment_poster_id);
                            
                            //adds alert
                            add_alert($comment_poster_id, $information);

                            $email=get_email_settings($comment_poster_id, 'liked_video_comment');
                            if($email==1)
                            {
                                $information[0]='video_comment_like';
                                send_mail_alert($comment_poster_id, $information);
                            }
                        }
                    }
                }
                else
                    echo "Comment doesn't exist";
            }
            else
                echo "Video doesn't exist";
        }
        else
        {
            echo "Something went wrong. We are working on fixing it";
            log_error("like_video_comment.php: (1): ", mysql_error());
        }
    }
    else
        echo "Invalid user id";
}
else
    echo "You don't have permission to like this comment";