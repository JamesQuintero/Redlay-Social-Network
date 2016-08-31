<?php
@include('init.php');
include('universal_functions.php');
$allowed="users";
include('security_checks.php');

$video_id=(int)($_POST['video_id']);
$comment_id=(int)($_POST['comment_id']);
$ID=(int)($_POST['user_id']);

if(is_id($ID) && user_id_exists($ID) && !user_id_terminated($ID))
{
    $query=mysql_query("SELECT video_ids, video_comment_ids, video_comments, video_comment_likes, video_comment_dislikes, video_comments_users_sent, video_comment_timestamps FROM content WHERE user_id=$ID LIMIT 1");
    if($query&&mysql_num_rows($query)==1)
    {
        $array=mysql_fetch_row($query);
        $video_ids=explode('|^|*|', $array[0]);
        $comment_ids=explode('|^|*|', $array[1]);
        $comments=explode('|^|*|', mysql_real_escape_string($array[2]));
        $comment_likes=explode('|^|*|', $array[3]);
        $comment_dislikes=explode('|^|*|', $array[4]);
        $comments_user_id=explode('|^|*|', $array[5]);
        $comment_timestamps=explode('|^|*|', $array[6]);

        $index=-1;
        for($x = 0; $x < sizeof($video_ids); $x++)
        {
            if($video_ids[$x]==$video_id)
                $index=$x;
        }

        //if there is a post where the comment is located
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
                $comments[$index]=explode('|%|&|', $comments[$index]);
                $comment_likes[$index]=explode('|%|&|', $comment_likes[$index]);
                $comment_dislikes[$index]=explode('|%|&|', $comment_dislikes[$index]);
                $comments_user_id[$index]=explode('|%|&|', $comments_user_id[$index]);
                $comment_timestamps[$index]=explode('|%|&|', $comment_timestamps[$index]);

                if($comments_user_id[$index][$comment_index]==$_SESSION['id'])
                {
                    $temp_comment_ids=array();
                    $temp_comments=array();
                    $temp_comment_likes=array();
                    $temp_comment_dislikes=array();
                    $temp_comments_users_sent=array();
                    $temp_comment_timestamps=array();

                    for($x = 0; $x < sizeof($comments[$index]); $x++)
                    {
                        if($x!=$comment_index)
                        {
                            $temp_comment_ids[]=$comment_ids[$index][$x];
                            $temp_comments[]=$comments[$index][$x];
                            $temp_comment_likes[]=$comment_likes[$index][$x];
                            $temp_comment_dislikes[]=$comment_dislikes[$index][$x];
                            $temp_comments_users_sent[]=$comments_user_id[$index][$x];
                            $temp_comment_timestamps[]=$comment_timestamps[$index][$x];
                        }
                    }

                    $comment_ids[$index]=implode('|%|&|', $temp_comment_ids);
                    $comments[$index]=implode('|%|&|', $temp_comments);
                    $comment_likes[$index]=implode('|%|&|', $temp_comment_likes);
                    $comment_dislikes[$index]=implode('|%|&|', $temp_comment_dislikes);
                    $comments_user_id[$index]=implode('|%|&|', $temp_comments_users_sent);
                    $comment_timestamps[$index]=implode('|%|&|', $temp_comment_timestamps);

                    $comment_ids=implode('|^|*|', $comment_ids);
                    $comments=implode('|^|*|', $comments);
                    $comment_likes=implode('|^|*|', $comment_likes);
                    $comment_dislikes=implode('|^|*|', $comment_dislikes);
                    $comments_user_id=implode('|^|*|', $comments_user_id);
                    $comment_timestamps=implode('|^|*|', $comment_timestamps);

                    $query=mysql_query("UPDATE content SET video_comment_ids='$comment_ids', video_comments='$comments', video_comment_likes='$comment_likes', video_comment_dislikes='$comment_dislikes', video_comments_users_sent='$comments_user_id', video_comment_timestamps='$comment_timestamps' WHERE user_id=$ID");
                    if($query)
                        echo "success";
                    else
                    {
                        echo "Something went wrong";
                        log_error("delete_comment.php: ", mysql_error());
                    }
                }
                else
                    echo "You can't delete a comment that's not yours";
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
        log_error("delete_comment_video: (1): ", mysql_error());
    }
}
else
    echo "Invalid user ID";
