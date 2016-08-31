<?php
@include('init.php');
include('universal_functions.php');
$allowed="users";
include('security_checks.php');


$picture_id=clean_string($_POST['picture_id']);
$ID=(int)($_POST['user_id']);
$comment_id=(int)($_POST['comment_id']);
$type=clean_string($_POST['type']);

if($type=='user')
{
    if(is_id($ID)&&user_id_exists($ID)&&$comment_id>=0)
    {
        $query=mysql_query("SELECT pictures, comment_dislikes, comments_user_sent, comment_ids FROM pictures WHERE user_id=$ID LIMIT 1");
        if($query&&mysql_num_rows($query)==1)
        {
            $array=mysql_fetch_row($query);
            $picture_ids=explode('|^|*|', $array[0]);
            $comment_dislikes=explode('|^|*|', $array[1]);
            $comments_users_sent=explode('|^|*|', $array[2]);
            $comment_ids=explode('|^|*|', $array[3]);

            //gets index
            $index=-1;
            for($x = 0; $x < sizeof($picture_ids); $x++)
            {
                if($picture_ids[$x]==$picture_id)
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
                    $comments_users_sent[$index]=explode('|%|&|', $comments_users_sent[$index]);
                    $comment_poster_id=$comments_users_sent[$index][$comment_index];
                    $comment_dislikes[$index][$comment_index]=explode('|@|$|', $comment_dislikes[$index][$comment_index]);


                    if($comment_poster_id!=$_SESSION['id'])
                    {
                        if($comment_dislikes[$index][$comment_index][0]=='')
                            $comment_dislikes[$index][$comment_index][0]=$_SESSION['id'];
                        else
                        {
                            $bool=false;
                            for($x = 0; $x < sizeof($comment_dislikes[$index][$comment_index]); $x++)
                            {
                                if($comment_dislikes[$index][$comment_index][$x]==$_SESSION['id'])
                                    $bool=true;
                            }
                            if($bool==false)
                                $comment_dislikes[$index][$comment_index][]=$_SESSION['id'];
                        }
                    }



                    $comment_dislikes[$index][$comment_index]=implode('|@|$|', $comment_dislikes[$index][$comment_index]);
                    $comment_dislikes[$index]=implode('|%|&|', $comment_dislikes[$index]);
                    $comment_dislikes=implode('|^|*|', $comment_dislikes);

                    $query=mysql_query("UPDATE pictures SET comment_dislikes='$comment_dislikes' WHERE user_id=$ID");
                    if(!$query)
                    {
                        echo "Something went wrong";
                        log_error("dislike_photo_comment.php: ", mysql_error());
                    }
                }
            }
        }
    }
}
else if($type=='page')
{
    
}