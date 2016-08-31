<?php
@include('init.php');
include('universal_functions.php');
$allowed="users";
include('security_checks.php');


$video_id=(int)($_POST['video_id']);
$ID=(int)($_POST['user_id']);


if(is_id($ID) && user_id_exists($ID) && $video_id>=0)
{
    $privacy=get_user_privacy_settings($ID);
    if(($privacy[0][2]=='yes'||user_is_friends($ID, $_SESSION['id'])=='true')&&$ID!=$_SESSION['id'])
    {
        $query=mysql_query("SELECT video_ids, video_likes FROM content WHERE user_id=$ID LIMIT 1");
        if($query&&mysql_num_rows($query)==1)
        {
            $array=mysql_fetch_row($query);
            $video_ids=explode('|^|*|', $array[0]);
            $likes=explode('|^|*|', $array[1]);

            //gets the index for other arrays
            $index=-1;
            for($x = 0; $x < sizeof($video_ids); $x++)
            {
                if($video_ids[$x]==$video_id)
                    $index=$x;
            }
            
            if($index!=-1)
            {
                if($array[1]!='')
                {
                    if($likes[$index]==''||$likes[$index]=='0')
                        $likes[$index]=$_SESSION['id'];
                    else
                    {
                        $likes[$index]=explode('|%|&|', $likes[$index]);
                        
                        if(!in_array($_SESSION['id'], $likes[$index]))
                            $likes[$index][]=$_SESSION['id'];
                        else
                        {
                            echo "You can't like a post you already liked";
                            exit();
                        }
                        
                        $likes[$index]=implode('|%|&|', $likes[$index]);
                    }
                }
                else
                    $likes[0]=$_SESSION['id'];
                
                $likes=implode('|^|*|', $likes);
                $query=mysql_query("UPDATE content SET video_likes='$likes' WHERE user_id=$ID");
                if($query)
                {
                    echo "Video liked";
                    if($_SESSION['id']!=$ID)
                    {
                        $information=array();
                        $information[0]='video_like';
                        $information[1]=$ID;
                        $information[2]=$video_id;
                        
                        //adds point
                        add_point($ID);

                        //adds alert
                        add_alert($ID, $information);

                        $emails=get_email_settings($ID, 'video_like');
                        if($emails==1)
                        {
                            $information[0]='video_like';
                            send_mail_alert($ID, $information);
                        }
                        
                        //records post like
//                        record_video_like($video__id, $ID);
                    }
                }
                else
                {
                    echo "Something went wrong";
                    log_error("like_video.php: (2): ",mysql_error());
                }
            }
        }
        else
        {
            echo "Something went wrong";
            log_error("like_video.php: (1): ",mysql_error());
        }
    }
    else
        echo "You don't have permission to do this";
}
else
    echo "Invalid user ID";