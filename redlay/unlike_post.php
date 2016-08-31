<?php
@include('init.php');
include('universal_functions.php');
$allowed="users";
include('security_checks.php');

$post_index=(int)($_POST['post_id']);
$ID=(int)($_POST['profile_id']);


if(is_id($ID) && user_id_exists($ID))
{
    $privacy=get_user_privacy_settings($ID);
    if($privacy[0][2]=='yes'||$ID==$_SESSION['id'])
    {
        $query=mysql_query("SELECT post_ids, likes, user_ids_posted FROM content WHERE user_id=$ID LIMIT 1");
        if($query&&mysql_num_rows($query)==1)
        {
            $array=mysql_fetch_row($query);
            $post_ids=explode('|^|*|', $array[0]);
            $likes=explode('|^|*|', $array[1]);
            $user_ids_posted=explode('|^|*|', $array[2]);

            $index=-1;
            for($x = 0; $x < sizeof($post_ids); $x++)
            {
                if($post_index==$post_ids[$x])
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
                remove_point($user_ids_posted[$index]);
                
                $likes[$index]=implode('|%|&|', $likes[$index]);

                $likes=implode('|^|*|', $likes);
                $query=mysql_query("UPDATE content SET likes='$likes' WHERE user_id=$ID");
                if($query)
                    echo "Success";
                else
                {
                    echo "Something went wrong";
                    log_error(mysql_error());
                }
            }
        }
        else
        {
            echo "Something went wrong";
            log_error(mysql_error());
        }
    }
}