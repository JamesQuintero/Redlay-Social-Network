<?php
@include('init.php');
include('universal_functions.php');
$allowed="users";
include("security_checks.php");

$picture_id=clean_string($_POST['photo_id']);
$comment_id=(int)($_POST['comment_id']);
$profile_id=(int)($_POST['profile_id']);
$type=clean_string($_POST['type']);

if($type=='user')
{
    $query=mysql_query("SELECT pictures, comment_ids, picture_comments, comment_likes, comment_dislikes, comments_user_sent, comment_timestamps FROM pictures WHERE user_id=$profile_id LIMIT 1");
    if($query&&mysql_num_rows($query)==1)
    {
        $array=mysql_fetch_row($query);
        $pictures=explode('|^|*|', $array[0]);
        $comment_ids=explode('|^|*|', $array[1]);
        $comments=explode('|^|*|', mysql_real_escape_string($array[2]));
        $comment_likes=explode('|^|*|', $array[3]);
        $comment_dislikes=explode('|^|*|', $array[4]);
        $comments_user_id=explode('|^|*|', $array[5]);
        $comment_timestamps=explode('|^|*|', $array[6]);

        $index=-1;
        for($x = 0; $x < sizeof($pictures); $x++)
        {
            if($pictures[$x]==$picture_id)
                $index=$x;
        }

        //if the picture exists
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

                    $query=mysql_query("UPDATE pictures SET comment_ids='$comment_ids', picture_comments='$comments', comment_likes='$comment_likes', comment_dislikes='$comment_dislikes', comments_user_sent='$comments_user_id', comment_timestamps='$comment_timestamps' WHERE user_id=$profile_id");
                    if($query)
                        echo "Success";
                    else
                    {
                        echo "Something went wrong";
                        log_error("delete_comment.php: ", mysql_error());
                    }
                }
            }
        }   
    }
}
