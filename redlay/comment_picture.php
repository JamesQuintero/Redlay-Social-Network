<?php
@include('init.php');
include('universal_functions.php');
$allowed="users";
include('security_checks.php');

$ID=(int)($_POST['user_id']);
$picture_id=clean_string($_POST['picture_id']);
$comment=clean_string($_POST['comment']);
$type=clean_string($_POST['type']);

if($type=='user')
{
    $query=mysql_query("SELECT pictures, picture_comments, comment_timestamps, comment_likes, comment_dislikes, comments_user_sent, comment_ids FROM pictures WHERE user_id=$ID LIMIT 1");
    if($query && mysql_num_rows($query)==1)
    {
        $array=mysql_fetch_row($query);
        $pictures=explode('|^|*|', $array[0]);
        $comments=explode('|^|*|', str_replace("'", "\'", $array[1]));
        $comment_timestamps=explode('|^|*|', $array[2]);
        $comment_likes=explode('|^|*|', $array[3]);
        $comment_dislikes=explode('|^|*|', $array[4]);
        $comments_user_sent=explode('|^|*|', $array[5]);
        $comment_ids=explode('|^|*|', $array[6]);

        $index=-1;
        for($x = 0; $x < sizeof($pictures); $x++)
        {
            if($pictures[$x]==$picture_id)
                $index=$x;
        }

        if($index!=-1)
        {
            $comments[$index]=explode('|%|&|', $comments[$index]);
            $comment_timestamps[$index]=explode('|%|&|', $comment_timestamps[$index]);
            $comment_likes[$index]=explode('|%|&|', $comment_likes[$index]);
            $comment_dislikes[$index]=explode('|%|&|', $comment_dislikes[$index]);
            $comments_user_sent[$index]=explode('|%|&|', $comments_user_sent[$index]);
            $comment_ids[$index]=explode('|%|&|', $comment_ids[$index]);



            $date=get_date();
            if($comments[$index][0]=='')
            {
                $new_comment_id=0;
                $comment_ids[$index][0]=0;
                $comments[$index][0]=$comment;
                $comment_timestamps[$index][0]=$date;
                $comment_likes[$index][0]='';
                $comment_dislikes[$index][0]='';
                $comments_user_sent[$index][0]=$_SESSION['id'];
            }
            else
            {
                $new_comment_id=$comment_ids[$index][sizeof($comment_ids[$index])-1]+1;
                $comment_ids[$index][]=$new_comment_id;
                $comments[$index][]=$comment;
                $comment_timestamps[$index][]=$date;
                $comment_likes[$index][]='';
                $comment_dislikes[$index][]='';
                $comments_user_sent[$index][]=$_SESSION['id'];
            }

            $temp_comments_sent=$comments_user_sent[$index];

            
           $comments[$index]=implode('|%|&|', $comments[$index]);
           $comment_timestamps[$index]=implode('|%|&|', $comment_timestamps[$index]);
           $comment_likes[$index]=implode('|%|&|', $comment_likes[$index]);
           $comment_dislikes[$index]=implode('|%|&|', $comment_dislikes[$index]);
           $comments_user_sent[$index]=implode('|%|&|', $comments_user_sent[$index]);
           $comment_ids[$index]=implode('|%|&|', $comment_ids[$index]);


            $comments=implode('|^|*|', $comments);
            $comment_timestamps=implode('|^|*|', $comment_timestamps);
            $comment_likes=implode('|^|*|', $comment_likes);
            $comment_dislikes=implode('|^|*|', $comment_dislikes);
            $comments_user_sent=implode('|^|*|', $comments_user_sent);
            $comment_ids=implode('|^|*|', $comment_ids);

            $query=mysql_query("UPDATE pictures SET picture_comments='$comments', comment_timestamps='$comment_timestamps', comments_user_sent='$comments_user_sent', comment_likes='$comment_likes', comment_dislikes='$comment_dislikes', comment_ids='$comment_ids' WHERE user_id=$ID");
            if($query)
            {
                
                //send back information
                $current_profile_picture=get_profile_picture($_SESSION['id']);
                $current_name=get_user_name($_SESSION['id']);
                $badges=get_badges($_SESSION['id']);
                
                
                $JSON=array();
                $JSON['current_profile_picture']=$current_profile_picture;
                $JSON['current_name']=$current_name;
                $JSON['new_comment_id']=$new_comment_id;
                $JSON['current_user']=$_SESSION['id'];
                $JSON['badges']=$badges;
                echo json_encode($JSON);
                
                //records photo comment
                record_photo_comment($picture_id, $ID);
                
                
                if($_SESSION['id']!=$ID)
                {
                    $information=array();
                    $information[0]='picture_comment';
                    $information[1]=$picture_id;

                    add_alert($ID, $information);

                    $emails=get_email_settings($ID, 'picture_comment');
                    if($emails==1)
                    {
                        $information[0]='photo_comment';
                        send_mail_alert ($ID, $information);
                    }
                }

                $already_sent=array();
                $information=array();
                $information[0]='comment_same_picture';
                $information[1]=$picture_id;
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
            }
            else
            {
                echo "Something went wrong. We are working on fixing it";
                log_error("comment_picture.php: (2): ", mysql_error());
            }
        }
        else
            echo "Photo doesn't exist. Invalid photo ID";
    }
    else
    {
        echo "Something went wrong. We are working on fixing it";
        log_error("comment_picture.php: (1): ", mysql_error());
    }
}
else if($type=='page')
{
    
}
