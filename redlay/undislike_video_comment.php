<?php
@include('init.php');
include('universal_functions.php');
$allowed="users";
include('security_checks.php');

$video_id=(int)($_POST['video_id']);
$comment_id=(int)($_POST['comment_id']);
$profile_id=(int)($_POST['profile_id']);


if(is_id($profile_id)&&user_id_exists($profile_id)&&$comment_id>=0)
{
    $query=mysql_query("SELECT video_ids, video_comment_ids, video_comment_dislikes FROM content WHERE user_id=$profile_id LIMIT 1");
    if($query&&mysql_num_rows($query)==1)
    {
        $array=mysql_fetch_row($query);
        $video_ids=explode('|^|*|', $array[0]);
        $comment_ids=explode('|^|*|', $array[1]);
        $comment_dislikes=explode('|^|*|', $array[2]);
        
        $index=-1;
        for($x =0; $x < sizeof($video_ids); $x++)
        {
            if($video_id==$video_ids[$x])
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
                $comment_dislikes[$index]=explode('|%|&|', $comment_dislikes[$index]);
                $comment_dislikes[$index][$comment_index]=explode('|@|$|', $comment_dislikes[$index][$comment_index]);



                $temp_dislikes=array();
                for($x = 0; $x < sizeof($comment_dislikes[$index][$comment_index]); $x++)
                {
                    if($comment_dislikes[$index][$comment_index][$x]!=$_SESSION['id'])
                        $temp_dislikes[]=$comment_dislikes[$index][$comment_index][$x];
                }
                $comment_dislikes[$index][$comment_index]=$temp_dislikes;




                $comment_dislikes[$index][$comment_index]=implode('|@|$|', $comment_dislikes[$index][$comment_index]);
                $comment_dislikes[$index]=implode('|%|&|', $comment_dislikes[$index]);
                $comment_dislikes=implode('|^|*|', $comment_dislikes);

                $query=mysql_query("UPDATE content SET video_comment_dislikes='$comment_dislikes' WHERE user_id=$profile_id");
                if(!$query)
                {
                    log_error("undislike_video_comment.php: ", mysql_error());
                }
            }
        }
    }
}