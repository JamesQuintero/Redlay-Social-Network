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
    if(($privacy[0][2]=='yes' || user_is_friends($ID, $_SESSION['id'])=='true') && $ID!=$_SESSION['id'])
    {
        $query=mysql_query("SELECT video_ids, video_dislikes FROM content WHERE user_id=$ID LIMIT 1");
        if($query&&mysql_num_rows($query)==1)
        {
            $array=mysql_fetch_row($query);
            $video_ids=explode('|^|*|', $array[0]);
            $dislikes=explode('|^|*|', $array[1]);

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
                    if($dislikes[$index]=='0'||$dislikes[$index]=='')
                        $dislikes[$index]=$_SESSION['id'];
                    else
                    {
                        $dislikes[$index]=explode('|%|&|', $dislikes[$index]);
                        if(!in_array($_SESSION['id'], $dislikes[$index]))
                            $dislikes[$index][]=$_SESSION['id'];
                        else
                        {
                            echo "You can't dislike a video you have already disliked";
                            exit();
                        }
                        $dislikes[$index]=implode('|%|&|', $dislikes[$index]);
                    }

                }
                else
                    $dislikes[0]=$_SESSION['id'];
                
                $dislikes=implode('|^|*|', $dislikes);
                $query=mysql_query("UPDATE content SET video_dislikes='$dislikes' WHERE user_id=$ID");
                if($query)
                {
                    echo "Video disliked";
                }
                else
                {
                    echo "Something went wrong";
                    log_error("dislike_status_update.php: (2): ",mysql_error());
                }
            }
        }
        else
        {
            echo "Something went wrong";
            log_error("dislike_status_update.php: (1): ",mysql_error());
        }
    }
    else
        echo "You don't have permission to do this";
}