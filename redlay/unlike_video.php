<?php
@include('init.php');
include('universal_functions.php');
$allowed="users";
include('security_checks.php');

$video_id=(int)($_POST['video_id']);
$ID=(int)($_POST['user_id']);


if(is_id($ID)&&user_id_exists($ID))
{
    $privacy=get_user_privacy_settings($ID);
    if($privacy[0][2]=='yes'||user_is_friends($ID, $_SESSION['id'])=='true')
    {
        $query=mysql_query("SELECT video_ids, video_likes FROM content WHERE user_id=$ID LIMIT 1");
        if($query&&mysql_num_rows($query)==1)
        {
            $array=mysql_fetch_row($query);
            $video_ids=explode('|^|*|', $array[0]);
            $likes=explode('|^|*|', $array[1]);

            $index=-1;
            for($x = 0; $x < sizeof($video_ids); $x++)
            {
                if($video_id==$video_ids[$x])
                    $index=$x;
            }

            if($index!=-1)
            {
                $likes[$index]=explode('|%|&|', $likes[$index]);

                $temp_likes=array();
                for($x = 0; $x < sizeof($likes[$index]); $x++)
                {
                    if($likes[$index][$x]!=$_SESSION['id'])
                        $temp_likes[]=$likes[$index][$x];
                }
                
                if(isset($temp_likes[0]))
                    $likes[$index]=$temp_likes;
                else
                    $likes[$index]='0';

                //subtracts a point from poster
                remove_point($ID);
                
                $likes[$index]=implode('|%|&|', $likes[$index]);

                $likes=implode('|^|*|', $likes);
                $query=mysql_query("UPDATE content SET video_likes='$likes' WHERE user_id=$ID");
                if($query)
                    echo "Success";
                else
                {
                    echo "Something went wrong. We are working on fixing it";
                    log_error("unlike_video.php: (2): ", mysql_error());
                }
            }
            else
                echo "Video doesn't exist";
        }
        else
        {
            echo "Something went wrong. We are working on fixing it";
            log_error("unlike_video.php: (1): ", mysql_error());
        }
    }
    else
        echo "You don't have permission to do this";
}
else
    echo "Invalid user ID";