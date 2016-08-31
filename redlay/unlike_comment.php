<?php
@include('init.php');
include('universal_functions.php');
$allowed="users";
include('security_checks.php');

$post_id=(int)($_POST['post_id']);
$comment_id=(int)($_POST['comment_id']);
$profile_id=(int)($_POST['profile_id']);


if(is_id($profile_id) && user_id_exists($profile_id) && $comment_id>=0)
{
    $query=mysql_query("SELECT post_ids, comment_ids, comment_likes, comments_user_id FROM content WHERE user_id=$profile_id LIMIT 1");
    if($query&&mysql_num_rows($query)==1)
    {
        $array=mysql_fetch_row($query);
        $post_ids=explode('|^|*|', $array[0]);
        $comment_ids=explode('|^|*|', $array[1]);
        $comment_likes=explode('|^|*|', $array[2]);
        $comment_user_ids=explode('|^|*|', $array[3]);
        
        $index=-1;
        for($x = 0; $x < sizeof($post_ids); $x++)
        {
            if($post_id==$post_ids[$x])
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
                $comment_likes[$index][$comment_index]=explode('|@|$|', $comment_likes[$index][$comment_index]);

                //removes point
                remove_point($comment_user_ids[$index][$comment_index]);

                $temp_likes=array();
                for($x = 0; $x < sizeof($comment_likes[$index][$comment_index]); $x++)
                {
                    if($comment_likes[$index][$comment_index][$x]!=$_SESSION['id'])
                        $temp_likes[]=$comment_likes[$index][$comment_index][$x];
                }
                $comment_likes[$index][$comment_index]=$temp_likes;




                $comment_likes[$index][$comment_index]=implode('|@|$|', $comment_likes[$index][$comment_index]);
                $comment_likes[$index]=implode('|%|&|', $comment_likes[$index]);
                $comment_likes=implode('|^|*|', $comment_likes);

                $query=mysql_query("UPDATE content SET comment_likes='$comment_likes' WHERE user_id=$profile_id");
            }
        }
    }
}