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
    if($privacy[0][2]=='yes'||$ID==$_SESSION['id'])
    {
        $query=mysql_query("SELECT video_ids, video_dislikes FROM content WHERE user_id=$ID LIMIT 1");
        if($query&&mysql_num_rows($query)==1)
        {
            $array=mysql_fetch_row($query);
            $video_ids=explode('|^|*|', $array[0]);
            $dislikes=explode('|^|*|', $array[1]);

            $index=-1;
            for($x = 0; $x < sizeof($video_ids); $x++)
            {
                if($video_id==$video_ids[$x])
                    $index=$x;
            }

            if($index!=-1)
            {
                $dislikes[$index]=explode('|%|&|', $dislikes[$index]);

                $temp_likes=array();
                for($x = 0; $x < sizeof($dislikes[$index]); $x++)
                {
                    if($dislikes[$index][$x]!=$_SESSION['id'])
                        $temp_likes[]=$dislikes[$index][$x];
                }
                
                if(isset($temp_likes[0]))
                    $dislikes[$index]=$temp_likes;
                else
                    $dislikes[$index]='0';

                
                $dislikes[$index]=implode('|%|&|', $dislikes[$index]);

                $dislikes=implode('|^|*|', $dislikes);
                $query=mysql_query("UPDATE content SET video_dislikes='$dislikes' WHERE user_id=$ID");
                if($query)
                    echo "Success";
                else
                {
                    echo "Something went wrong. We are working on fixing it";
                    log_error("undislike_video.php: (2): ",mysql_error());
                }
            }
        }
        else
        {
            echo "Something went wrong. We are working on fixing it";
            log_error("undislike_video.php: (1): ",mysql_error());
        }
    }
    else
        echo "You don't have permission to do this";
}
else
    echo "Invalid user ID";