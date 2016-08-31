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
        $query=mysql_query("SELECT pictures, comment_likes, comments_user_sent, comment_ids FROM pictures WHERE user_id=$ID LIMIT 1");
        if($query&&mysql_num_rows($query)==1)
        {
            $array=mysql_fetch_row($query);
            $picture_ids=explode('|^|*|', $array[0]);
            $comment_likes=explode('|^|*|', $array[1]);
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
                    $comment_likes[$index]=explode('|%|&|', $comment_likes[$index]);
                    $comments_users_sent[$index]=explode('|%|&|', $comments_users_sent[$index]);
                    $comment_poster_id=$comments_users_sent[$index][$comment_index];
                    $comment_likes[$index][$comment_index]=explode('|@|$|', $comment_likes[$index][$comment_index]);


                    if($comment_poster_id!=$_SESSION['id'])
                    {
                        if($comment_likes[$index][$comment_index][0]=='')
                            $comment_likes[$index][$comment_index][0]=$_SESSION['id'];
                        else
                        {
                            $bool=false;
                            for($x = 0; $x < sizeof($comment_likes[$index][$comment_index]); $x++)
                            {
                                if($comment_likes[$index][$comment_index][$x]==$_SESSION['id'])
                                    $bool=true;
                            }
                            if($bool==false)
                                $comment_likes[$index][$comment_index][]=$_SESSION['id'];
                        }
                    }



                    $comment_likes[$index][$comment_index]=implode('|@|$|', $comment_likes[$index][$comment_index]);
                    $comment_likes[$index]=implode('|%|&|', $comment_likes[$index]);
                    $comment_likes=implode('|^|*|', $comment_likes);

                    $query=mysql_query("UPDATE pictures SET comment_likes='$comment_likes' WHERE user_id=$ID");
                    if($query)
                    {
                        if($_SESSION['id']!=$comment_poster_id)
                        {
                            $information=array();
                            $information[0]='liked_picture_comment';
                            $information[1]=$picture_id;
                            $information[2]=$ID;

                            //adds point
                            add_point($comment_poster_id);
                            
                            //adds alert
                            add_alert($comment_poster_id, $information);

                            $email=get_email_settings($comment_poster_id, 'liked_comment');
                            if($email==1)
                            {
                                $information[0]='like_photo_comment';
                                $information[3]='user';
                                send_mail_alert($comment_poster_id, $information);
                            }
                        }
                    }
                    else
                    {
                        echo "Something went wrong";
                        log_error("like_photo_comment: (): ", mysql_error());
                    }
                }
            }
        }
    }
}
else if($type=='page')
{
    
}