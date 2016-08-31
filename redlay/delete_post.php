<?php
@include('init.php');
include('universal_functions.php');
$allowed="users";
include('security_checks.php');

$post_id=(int)($_POST['post_id']);
$ID=(int)($_POST['user_id']);

if(is_id($ID) && user_id_exists($ID))
{
    if($post_id>=0)
    {
        $query=mysql_query("SELECT post_ids, post_groups, posts, user_ids_posted, comments, comment_likes, comment_dislikes, comments_user_id, comment_timestamps, likes, dislikes, timestamps FROM content WHERE user_id=$ID LIMIT 1");
        if($query&&mysql_num_rows($query)==1)
        {
            $array=mysql_fetch_row($query);
            $post_ids=explode('|^|*|', $array[0]);
            $audience_groups=explode('|^|*|', $array[1]);
            $posts=explode('|^|*|', $array[2]);
            $user_ids_posted=explode('|^|*|', $array[3]);
            $comments=explode('|^|*|', $array[4]);
            $comment_likes=explode('|^|*|', $array[5]);
            $comment_dislikes=explode('|^|*|', $array[6]);
            $comments_users_sent=explode('|^|*|', $array[7]);
            $comment_timestamps=explode('|^|*|', $array[8]);
            $likes=explode('|^|*|', $array[9]);
            $dislikes=explode('|^|*|', $array[10]);
            $timestamps=explode('|^|*|', $array[11]);

            $index=-1;
            for($x = 0; $x < sizeof($post_ids); $x++)
            {
                if($post_id==$post_ids[$x])
                    $index=$x;
            }

            if($index!=-1)
            {
                if($ID==$_SESSION['id']||$user_ids_posted[$index]==$_SESSION['id'])
                {
                    $temp_post_ids=array();
                    $temp_audience_groups=array();
                    $temp_posts=array();
                    $temp_user_ids_posted=array();
                    $temp_comments=array();
                    $temp_comment_likes=array();
                    $temp_comment_dislikes=array();
                    $temp_comments_users_sent=array();
                    $temp_comment_timestamps=array();
                    $temp_likes=array();
                    $temp_dislikes=array();
                    $temp_timestamps=array();

                    if(sizeof($post_ids)>1)
                    {
                        for($x = 0; $x < sizeof($post_ids); $x++)
                        {
                            if($x!=$index)
                            {
                                $temp_post_ids[]=$post_ids[$x];
                                $temp_audience_groups[]=$audience_groups[$x];
                                $temp_posts[]=mysql_real_escape_string($posts[$x]);
                                $temp_user_ids_posted[]=$user_ids_posted[$x];
                                $temp_comments[]=mysql_real_escape_string($comments[$x]);
                                $temp_comment_likes[]=$comment_likes[$x];
                                $temp_comment_dislikes[]=$comment_dislikes[$x];
                                $temp_comments_users_sent[]=$comments_users_sent[$x];
                                $temp_comment_timestamps[]=$comment_timestamps[$x];
                                $temp_likes[]=$likes[$x];
                                $temp_dislikes[]=$dislikes[$x];
                                $temp_timestamps[]=$timestamps[$x];
                            }
                        }

                        $post_ids=implode('|^|*|', $temp_post_ids);
                        $audience_groups=implode('|^|*|', $temp_audience_groups);
                        $posts=implode('|^|*|', $temp_posts);
                        $user_ids_posted=implode('|^|*|', $temp_user_ids_posted);
                        $comments=implode('|^|*|', $temp_comments);
                        $comment_likes=implode('|^|*|', $temp_comment_likes);
                        $comment_dislikes=implode('|^|*|', $temp_comment_dislikes);
                        $comments_users_sent=implode('|^|*|', $temp_comments_users_sent);
                        $comment_timestamps=implode('|^|*|', $temp_comment_timestamps);
                        $likes=implode('|^|*|', $temp_likes);
                        $dislikes=implode('|^|*|', $temp_dislikes);
                        $timestamps=implode('|^|*|', $temp_timestamps);

                    }
                    else
                    {
                        $post_ids='';
                        $audience_groups='';
                        $posts='';
                        $user_ids_posted='';
                        $comments='';
                        $comment_likes='';
                        $comment_dislikes='';
                        $comments_users_sent='';
                        $comment_timestamps='';
                        $likes='';
                        $dislikes='';
                        $timestamps='';
                    }


                    $query=mysql_query("UPDATE content SET post_ids='$post_ids', post_groups='$audience_groups', posts='$posts', user_ids_posted='$user_ids_posted', comments='$comments', comment_likes='$comment_likes', comment_dislikes='$comment_dislikes', comments_user_id='$comments_users_sent', comment_timestamps='$comment_timestamps', likes='$likes', dislikes='$dislikes', timestamps='$timestamps' WHERE user_id=$ID");
                    if($query)
                        echo "Success";
                    else
                    {
                        echo "Something went wrong. We are working on fixing it";
                        log_error("delete_post.php: ", mysql_error());
                    }
                }
            }
            else
                echo "Post id is invalid";
        }
        else
        {
            echo "Something went wrong. We are working on fixing it";
            log_error("delete_post.php: ", mysql_error());
        }
    }
    else
        echo "Invalid post id";
}
else
    echo "ID of user is invalid";