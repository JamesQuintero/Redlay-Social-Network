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
        $query=mysql_query("SELECT post_ids, likes FROM content WHERE user_id=$ID LIMIT 1");
        if($query&&mysql_num_rows($query)==1)
        {
            $array=mysql_fetch_row($query);
            $post_ids=explode('|^|*|', $array[0]);
            $likes=explode('|^|*|', $array[1]);

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
                $query=mysql_query("UPDATE content SET likes='$likes' WHERE user_id=$ID");
                if($query)
                {
                    echo "Post liked";
                    if($_SESSION['id']!=$poster_id)
                    {
                        $information=array();
                        $information[0]='like';
                        $information[1]=$post_id;
                        $information[2]=$ID;
                        
                        //adds point
                        add_point($poster_id);

                        //adds alert
                        add_alert($poster_id, $information);

                        $emails=get_email_settings($poster_id, 'post_like');
                        if($emails==1)
                        {
                            $information[0]='post_like';
                            send_mail_alert($poster_id, $information);
                        }
                        
                        //records post like
                        record_post_like($post_id, $ID);
                    }
                }
                else
                {
                    echo "Something went wrong";
                    log_error("like_post.php: (2): ",mysql_error());
                }
            }
        }
        else
        {
            echo "Something went wrong";
            log_error("like_post.php: (1): ",mysql_error());
        }
    }
    else
        echo "You don't have permission to do this";
}