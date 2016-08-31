<?php
@include('init.php');
include('universal_functions.php');
$allowed="users";
include('security_checks.php');

$post_id=(int)($_POST['post_id']);
$ID=(int)($_POST['profile_id']);
$poster_id=(int)($_POST['poster_id']);


if(is_id($ID) && user_id_exists($ID) && $post_id>=0)
{
    $privacy=get_user_privacy_settings($ID);
    if($privacy[0][2]=='yes'||user_is_friends($ID, $_SESSION['id'])=='true')
    {
        $query=mysql_query("SELECT post_ids, dislikes FROM content WHERE user_id=$ID LIMIT 1");
        if($query&&mysql_num_rows($query)==1)
        {
            $array=mysql_fetch_row($query);
            $post_ids=explode('|^|*|', $array[0]);
            $dislikes=explode('|^|*|', $array[1]);

            //gets the index for other arrays
            $index=-1;
            for($x = 0; $x < sizeof($post_ids); $x++)
            {
                if($post_ids[$x]==$post_id)
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
                            echo "You can't dislike a post you have already disliked";
                            exit();
                        }
                        $dislikes[$index]=implode('|%|&|', $dislikes[$index]);
                    }

                }
                else
                    $dislikes[0]=$_SESSION['id'];
                
                $dislikes=implode('|^|*|', $dislikes);
                $query=mysql_query("UPDATE content SET dislikes='$dislikes' WHERE user_id=$ID");
                if($query)
                {
                    echo "Post disliked";
                    if($_SESSION['id']!=$poster_id)
                    {
                        //records post dislike
                        record_post_dislike($post_id, $ID);
                    }
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