<?php
@include('init.php');
include('universal_functions.php');
$allowed="users";
include('security_checks.php');

$ID=(int)($_POST['user_id']);
$timezone=(int)($_POST['timezone']);
$page=(int)($_POST['page'])*10;


if($ID!='' && is_id($ID) && user_id_exists($ID) && !user_id_terminated($ID))
{
    $query=mysql_query("SELECT comment_ids, comments, comment_likes, comment_dislikes, comments_user_id, comment_timestamps, post_ids FROM content WHERE user_id=$ID LIMIT 1");
    if($query&&mysql_num_rows($query)==1)
    {
        $array=mysql_fetch_row($query);
        $comment_ids=explode('|^|*|', $array[0]);
        $comments=explode('|^|*|', $array[1]);
        $comment_likes=explode('|^|*|', $array[2]);
        $comment_dislikes=explode('|^|*|', $array[3]);
        $comments_users_sent=explode('|^|*|', $array[4]);
        $comment_timestamps=explode('|^|*|', $array[5]);
        $post_ids=explode('|^|*|', $array[6]);

        //explodes everything
        for($x =0; $x < sizeof($comments_users_sent); $x++)
        {
            $comment_ids[$x]=explode('|%|&|', $comment_ids[$x]);
            $comments[$x]=explode('|%|&|', $comments[$x]);
            $comment_likes[$x]=explode('|%|&|', $comment_likes[$x]);
            $comment_dislikes[$x]=explode('|%|&|', $comment_dislikes[$x]);
            for($y = 0; $y < sizeof($comment_likes[$x]); $y++)
            {
                $comment_likes[$x][$y]=explode('|@|$|', $comment_likes[$x][$y]);
                $comment_dislikes[$x][$y]=explode('|@|$|', $comment_dislikes[$x][$y]);
            }
            $comments_users_sent[$x]=explode('|%|&|', $comments_users_sent[$x]);
            $comment_timestamps[$x]=explode('|%|&|', $comment_timestamps[$x]);
            
        }
        
        $temp_post_ids=array();
        $temp_posts=array();
        $temp_user_ids_posted=array();
        $temp_comments=array();
        $temp_likes=array();
        $temp_dislikes=array();
        $temp_timestamps=array();
        $temp_adjusted_timestamps=array();
        $temp_timestamp_seconds=array();
        $temp_time_since=array();

        $adjusted_timestamps=array();
        $time_since=array();
        $timestamp_seconds=array();

        for($x=0; $x < sizeof($post_ids); $x++)
        {
            $time_since[$x]=get_time_since($timestamps[$x], $timezone);
            $timestamp_seconds[$x]=get_time_since_seconds($timestamps[$x], $timezone);
            $adjusted_timestamps[$x]=get_adjusted_date($timestamps[$x], $timezone);
            $temp_timestamps[$x]=explode(' ', str_replace(",", "", $adjusted_timestamps[$x]));


            //asks whether the timestamps of the posts correspond to the month and year specified
            //and asks whether the post can be viewed by the correct audience that the user is in
            //or if it can be viewed by the post being viewed by everyone and the user not being friends with the current user
            if((  $month=="all"||$temp_timestamps[$x][0]==$month  )&&(  $year=="all"||$temp_timestamps[$x][2]==$year  )&&(  $phrase=='none'||strpos($posts[$x], $phrase)!==false  )&&(can_view($user_audiences, $audiences[$x])||$ID==$_SESSION['id'] ))
            {
                $temp_post_ids[]=$post_ids[$x];
                $temp_posts[]=$posts[$x];
                $temp_user_ids_posted[]=$user_ids_posted[$x];
                $temp_comments[]=$comments[$x];
                $temp_likes[]=$likes[$x];
                $temp_dislikes[]=$dislikes[$x];
                $temp_timestamps[]=$timestamps[$x];
                $temp_adjusted_timestamps[]=$adjusted_timestamps[$x];
                $temp_time_since[]=$time_since[$x];
                $temp_timestamp_seconds[]=$timestamp_seconds[$x];
            }
        }

        $post_ids=$temp_post_ids;
        $posts=$temp_posts;
        $user_ids_posted=$temp_user_ids_posted;
        $comments=$temp_comments;
        $likes=$temp_likes;
        $dislikes=$temp_dislikes;
        $timestamps=$temp_timestamps;
        $time_since=$temp_time_since;
        $timestamp_seconds=$temp_timestamp_seconds;


        //sorts from oldest to newest
        //if sort != 2, sorts default (newest to oldest)
        if($sort==2)
        {
            $temp_post_ids=array();
            $temp_posts=array();
            $temp_user_ids_posted=array();
            $temp_comments=array();
            $temp_likes=array();
            $temp_dislikes=array();
            $temp_timestamps=array();
            $temp_time_since=array();
            $temp_timestamp_seconds=array();

            for($x = sizeof($post_ids)-1; $x >= 0; $x--)
            {
                $temp_post_ids[]=$post_ids[$x];
                $temp_posts[]=$posts[$x];
                $temp_user_ids_posted[]=$user_ids_posted[$x];
                $temp_comments[]=$comments[$x];
                $temp_likes[]=$likes[$x];
                $temp_dislikes[]=$dislikes[$x];
                $temp_timestamps[]=$timestamps[$x];
                $temp_time_since[]=$time_since[$x];
                $temp_timestamp_seconds[]=$timestamp_seconds[$x];
            }

            $post_ids=$temp_post_ids;
            $posts=$temp_posts;
            $user_ids_posted=$temp_user_ids_posted;
            $comments=$temp_comments;
            $likes=$temp_likes;
            $dislikes=$temp_dislikes;
            $timestamps=$temp_timestamps;
            $time_since=$temp_time_since;
            $timestamp_seconds=$temp_timestamp_seconds;
        }
        
        
        if($array[1]!='')
            $total_size=sizeof($comments);
        else
            $total_size=0;
        
        if($total_size<10)
        {
            $empty=true;

            //reverses because it adds backwards in the else statement below
            $temp_comment_ids=array();
            $temp_comments=array();
            $temp_comment_likes=array();
            $temp_comment_dislikes=array();
            $temp_comments_users_sent=array();
            $temp_comment_timestamps=array();
            $temp_post_ids=array();

            for($x = sizeof($comment_ids)-1; $x >=0; $x--)
            {
                $temp_comment_ids[]=$comment_ids[$x];
                $temp_comments[]=$comments[$x];
                $temp_comment_likes[]=$comment_likes[$x];
                $temp_comment_dislikes[]=$comment_dislikes[$x];
                $temp_comments_users_sent[]=$comments_users_sent[$x];
                $temp_comment_timestamps[]=$comment_timestamps[$x];
                $temp_post_ids[]=$post_ids[$x];
            }
            $comment_ids=$temp_comment_ids;
            $comments=$temp_comments;
            $comment_likes=$temp_comment_likes;
            $comment_dislikes=$temp_comment_dislikes;
            $comments_users_sent=$temp_comments_users_sent;
            $comment_timestamps=$temp_comment_timestamps;
            $post_ids=$temp_post_ids;
        }
        else
        {
            if($total_size-$page<=0)
                $empty=true;
            else
                $empty=false;

            $temp_comment_ids=array();
            $temp_comments=array();
            $temp_comment_likes=array();
            $temp_comment_dislikes=array();
            $temp_comments_users_sent=array();
            $temp_comment_timestamps=array();
            $temp_post_ids=array();

            if($page==10)
                $index=sizeof($comment_ids)-$page+10-1;
            else
                $index=sizeof($comment_ids)-$page+10-2;

            while(sizeof($temp_comment_ids)<=10)
            {
                if($comments[$index]!='')
                {
                    $temp_comment_ids[]=$comment_ids[$index];
                    $temp_comments[]=$comments[$index];
                    $temp_comment_likes[]=$comment_likes[$index];
                    $temp_comment_dislikes[]=$comment_dislikes[$index];
                    $temp_comments_users_sent[]=$comments_users_sent[$index];
                    $temp_comment_timestamps[]=$comment_timestamps[$index];
                    $temp_post_ids[]=$post_ids[$index];
                }
                else
                {
                    $temp_comment_ids[]='';
                    $temp_comments[]='';
                    $temp_comment_likes[]='';
                    $temp_comment_dislikes[]='';
                    $temp_comments_users_sent[]='';
                    $temp_comment_timestamps[]='';
                    $temp_post_ids[]='';
                }

                $index--;
            }

            $comment_ids=$temp_comment_ids;
            $comments=$temp_comments;
            $comment_likes=$temp_comment_likes;
            $comment_dislikes=$temp_comment_dislikes;
            $comments_users_sent=$temp_comments_users_sent;
            $comment_timestamps=$temp_comment_timestamps;
            $post_ids=$temp_post_ids;
        }
        

        $num_likes=array();
        $num_dislikes=array();
        $has_liked=array();
        $has_disliked=array();
        
        $comment_names=array();
        $profile_pictures=array();
        $badges=array();
        $timestamp_seconds=array();
        for($x = 0; $x < sizeof($comment_likes); $x++)
        {
            $num_likes[$x]=array();
            $num_dislikes[$x]=array();
            $has_liked[$x]=array();
            $has_disliked[$x]=array();
            
            $comment_names[$x]=array();
            $profile_pictures[$x]=array();
            $badges[$x]=array();
            $timestamp_seconds[$x]=array();
            if($comment_likes[$x]!='')
            {
                for($y = 0; $y < sizeof($comment_likes[$x]); $y++)
                {
                    //gets badges
                    $badges[$x][$y]=get_badges($comments_users_sent[$x][$y]);

                    //gets number of likes
                    if($comment_likes[$x][$y][0]!='')
                        $num_likes[$x][$y]=sizeof($comment_likes[$x][$y]);
                    else
                        $num_likes[$x][$y]=0;

                    //gets number of dislikes
                    if($comment_dislikes[$x][$y][0]!='')
                        $num_dislikes[$x][$y]=sizeof($comment_dislikes[$x][$y]);
                    else
                        $num_dislikes[$x][$y]=0;

                    //gets has liked
                    $liked=false;
                    for($z = 0; $z < sizeof($comment_likes[$x][$y]); $z++)
                    {
                        if($comment_likes[$x][$y][$z]==$_SESSION['id'])
                            $liked=true;
                    }
                    $has_liked[$x][$y]=$liked;

                    //gets has disliked
                    $disliked=false;
                    for($z = 0; $z < sizeof($comment_dislikes[$x][$y]); $z++)
                    {
                        if($comment_dislikes[$x][$y][$z]==$_SESSION['id'])
                            $disliked=true;
                    }
                    $has_disliked[$x][$y]=$disliked;

                    //gets timestamps
                    if($comment_timestamps[$x][$y]!='')
                    {
                        $temp_timestamp=$comment_timestamps[$x][$y];
                        $comment_timestamps[$x][$y]=get_time_since($temp_timestamp, $timezone);
                        $timestamp_seconds[$x][$y]=get_time_since_seconds($temp_timestamp, $timezone);
                    }
                    else
                    {
                        $comment_timestamps[$x][$y]='';
                        $timestamp_seconds[$x][$y]='';
                    }

                    //gets comment names
                    $comment_names[$x][$y]=get_user_name($comments_users_sent[$x][$y]);
                    $profile_pictures[$x][$y]=get_profile_picture($comments_users_sent[$x][$y]);
                }               
            }
        }
        

        $JSON=array();
        $JSON['comments']=$comments;
        $JSON['comment_ids']=$comment_ids;
        $JSON['comment_timestamps']=$comment_timestamps;
        $JSON['comments_users_sent']=$comments_users_sent;
        $JSON['comment_names']=$comment_names;
        $JSON['post_ids']=$post_ids;
        $JSON['profile_pictures']=$profile_pictures;
        $JSON['badges']=$badges;
        $JSON['comment_timestamp_seconds']=$timestamp_seconds;
        $JSON['has_liked']=$has_liked;
        $JSON['has_disliked']=$has_disliked;
        $JSON['num_likes']=$num_likes;
        $JSON['num_dislikes']=$num_dislikes;
        echo json_encode($JSON);
        exit();
    }
    else
        log_error("user_post_comment_query.php: ".mysql_error());
}