<?php
@include('init.php');
include('universal_functions.php');
$allowed="all";
include('security_checks.php');


$num=(int)($_POST['num']);

//gets photos
if($num==1)
{
    $timezone=(int)($_POST['timezone']);
    $page=(int)($_POST['page'])*10;
    
    $query=mysql_query("SELECT picture_ids, picture_descriptions, pictures_users_sent, picture_timestamps, original_picture_ids, picture_types FROM public WHERE num=1");
    if($query&&mysql_num_rows($query)==1)
    {
        $array=mysql_fetch_row($query);
        $picture_ids=explode('|^|*|', $array[0]);
        $picture_descriptions=explode('|^|*|', $array[1]);
        $pictures_users_sent=explode('|^|*|', $array[2]);
        $picture_timestamps=explode('|^|*|', $array[3]);
        $original_picture_ids=explode('|^|*|', $array[4]);
        $picture_types=explode('|^|*|', $array[5]);
        
        
        //gets rid of terminated users
        $temp_picture_ids=array();
        $temp_picture_descriptions=array();
        $temp_pictures_users_sent=array();
        $temp_picture_timestamps=array();
        $temp_original_picture_ids=array();
        $temp_picture_types=array();
        for($x = 0; $x < sizeof($pictures_users_sent); $x++)
        {
            if(!user_id_terminated($pictures_users_sent[$x]))
            {
                $temp_picture_ids[]=$picture_ids[$x];
                $temp_picture_descriptions[]=$picture_descriptions[$x];
                $temp_pictures_users_sent[]=$pictures_users_sent[$x];
                $temp_picture_timestamps[]=$picture_timestamps[$x];
                $temp_original_picture_ids[]=$original_picture_ids[$x];
                $temp_picture_types[]=$picture_types[$x];
            }
        }
        
        $picture_ids=$temp_picture_ids;
        $picture_descriptions=$temp_picture_descriptions;
        $pictures_users_sent=$temp_pictures_users_sent;
        $picture_timestamps=$temp_picture_timestamps;
        $original_picture_ids=$temp_original_picture_ids;
        $picture_types=$temp_picture_types;
        
        
        $likes=array();
        $dislikes=array();
        $user_comment_ids=array();
        for($x = 0; $x < sizeof($picture_ids); $x++)
        {
            $query=mysql_query("SELECT pictures, picture_likes, picture_dislikes, comment_ids, image_types FROM pictures WHERE user_id=$pictures_users_sent[$x] LIMIT 1");
            if($query&&mysql_num_rows($query)==1)
            {
                $array=mysql_fetch_row($query);
                $pictures=explode('|^|*|', $array[0]);
                $picture_likes=explode('|^|*|', $array[1]);
                $picture_dislikes=explode('|^|*|', $array[2]);
                $user_comment_ids=explode('|^|*|', $array[3]);
                
                $index=array_search($original_picture_ids[$x], $pictures);
                
                if($index!=-1)
                {
                    $user_comment_ids[$index]=explode('|%|&|', $user_comment_ids[$index]);
                    $picture_likes[$index]=explode('|%|&|', $picture_likes[$index]);
                    $picture_dislikes[$index]=explode('|%|&|', $picture_dislikes[$index]);
                    
                    $likes[]=$picture_likes[$index];
                    $dislikes[]=$picture_dislikes[$index];
                    $comment_ids[]=$user_comment_ids[$index];
                }
                else
                {
                    $likes[]=array();
                    $dislikes[]=array();
                    $comment_ids[]=array();
                }
            }
            else
            {
                $likes[]=array();
                $dislikes[]=array();
                $comment_ids[]=array();
            }
        }
        
        
        $size=sizeof($picture_ids);
        $total_size=$size;

        if($size<10)
        {
            $empty=true;
            
            //takes the <10 size of current array and makes it an even 10
            for($x =0; $x < 10; $x++)
            {
                if(isset($picture_ids[$x]))
                {
                    $final_post_array_1[]=$picture_ids[$x];
                    $final_post_array_2[]=$picture_descriptions[$x];
                    $final_post_array_3[]=$pictures_users_sent[$x];
                    $final_post_array_4[]=$picture_timestamps[$x];
                    $final_post_array_5[]=$original_picture_ids[$x];
                    $final_post_array_6[]=$picture_types[$x];
                    $final_post_array_7[]=$likes[$x];
                    $final_post_array_8[]=$dislikes[$x];
                    $final_post_array_9[]=$comment_ids[$x];
                }
            }
        }
        else
        {
            $temp=sizeof($picture_ids)-$page;
            if($page>$size)
            {
                $temp=sizeof($picture_ids)%10;
                $empty=true;
                $end=$temp-1;
                $start=0;
                $size=$size%10;
            }
            else
            {
                if($page==$size)
                    $empty=true;
                else
                    $empty=false;
                $start=$temp;
                $end=$temp+9;
                $size=10;
            }

            //reverses posts to be in chronological order
            for($x = $start; $x <= $end; $x++)
            {
                $final_post_array_1[]=$picture_ids[$x];
                $final_post_array_2[]=$picture_descriptions[$x];
                $final_post_array_3[]=$pictures_users_sent[$x];
                $final_post_array_4[]=$picture_timestamps[$x];
                $final_post_array_5[]=$original_picture_ids[$x];
                $final_post_array_6[]=$picture_types[$x];
                $final_post_array_7[]=$likes[$x];
                $final_post_array_8[]=$dislikes[$x];
                $final_post_array_9[]=$comment_ids[$x];
            }
        }
        
        $profile_pictures=array();
        $photos_users_sent_names=array();
        $badges=array();
        $timestamp_seconds=array();
        $num_adds=array();
        $num_likes=array();
        $num_dislikes=array();
        $has_liked=array();
        $has_disliked=array();
        $num_comments=array();
        for($x = 0; $x < sizeof($final_post_array_1); $x++)
        {
            $profile_pictures[]=get_profile_picture($final_post_array_3[$x]);
            
            //gets names
            $photos_users_sent_names[]=get_user_name($final_post_array_3[$x]);
            
            //gets adjusted timestamps
            $temp_timestamp=$final_post_array_4[$x];
            $final_post_array_4[$x]=get_time_since($final_post_array_4[$x], $timezone);
            $timestamp_seconds[$x]=get_time_since_seconds($temp_timestamp, $timezone);
            
            //gets badges
            $badges[$x]=get_badges($final_post_array_3[$x]);
            
            //gets the number of adds
            $query=mysql_query("SELECT user_friends FROM user_data WHERE user_id=$final_post_array_3[$x] LIMIT 1");
            if($query&&mysql_num_rows($query)==1)
            {
                $array=mysql_fetch_row($query);
                $adds=explode('|^|*|', $array[0]);
                
                if($array[0]=='')
                    $num_adds[$x]=0;
                else
                    $num_adds[$x]=sizeof($adds);
            }
            
            //gets num likes
            if($final_post_array_7[$x][0]==""||$final_post_array_7[$x][0]=="0")
                $num_likes[]=0;
            else
                $num_likes[]=sizeof($final_post_array_7[$x]);
            
            //gets num dislikes
            if($final_post_array_8[$x][0]==""||$final_post_array_8[$x][0]=="0")
                $num_dislikes[]=0;
            else
                $num_dislikes[]=sizeof($final_post_array_8[$x]);
            
            //gets has liked
            if(in_array($_SESSION['id'], $final_post_array_7[$x])||$final_post_array_7[$x][0]==$_SESSION['id'])
                $has_liked[$x]=true;
            else
                $has_liked[$x]=false;
            
            //gets has disliked
            if(in_array($_SESSION['id'], $final_post_array_8[$x])||$final_post_array_8[$x][0]==$_SESSION['id'])
                $has_disliked[$x]=true;
            else
                $has_disliked[$x]=false;
            
            //gets comment stuff
            if($final_post_array_9[$x][0]=="")
                $num_comments[]=0;
            else
                $num_comments[]=sizeof($final_post_array_9[$x]);
        }
        
        
        $JSON=array();
        $JSON['photo_ids']=$final_post_array_1;
        $JSON['photo_descriptions']=$final_post_array_2;
        $JSON['photos_users_sent']=$final_post_array_3;
        $JSON['photos_users_sent_names']=$photos_users_sent_names;
        $JSON['profile_pictures']=$profile_pictures;
        $JSON['timestamps']=$final_post_array_4;
        $JSON['timestamp_seconds']=$timestamp_seconds;
        $JSON['is_empty']=$empty;
        $JSON['total_size']=$total_size;
        $JSON['badges']=$badges;
        $JSON['original_picture_ids']=$final_post_array_5;
        $JSON['picture_types']=$final_post_array_6;
        $JSON['num_adds']=$num_adds;
        $JSON['num_likes']=$num_likes;
        $JSON['num_dislikes']=$num_dislikes;
        $JSON['has_liked']=$has_liked;
        $JSON['has_disliked']=$has_disliked;
        $JSON['comment_ids']=$comment_ids;
        $JSON['num_comments']=$num_comments;
        echo json_encode($JSON);
        exit();
    }
}

//gets posts
else if($num==2)
{
    $timezone=(int)($_POST['timezone']);
    $page=(int)($_POST['page'])*10;
    
    $query=mysql_query("SELECT posts, posts_users_sent, post_ids, post_timestamps, original_post_ids FROM public WHERE num=1");
    if($query&&mysql_num_rows($query)==1)
    {
        $array=mysql_fetch_row($query);
        $posts=explode('|^|*|', $array[0]);
        $posts_users_sent=explode('|^|*|', $array[1]);
        $post_ids=explode('|^|*|', $array[2]);
        $post_timestamps=explode('|^|*|', $array[3]);
        $original_post_ids=explode('|^|*|', $array[4]);
        
        //gets rid of terminated users
        $temp_posts=array();
        $temp_posts_users_sent=array();
        $temp_post_ids=array();
        $temp_post_timestamps=array();
        $temp_original_post_ids=array();
        for($x = 0; $x < sizeof($post_ids); $x++)
        {
            if(!user_id_terminated($posts_users_sent[$x]))
            {
                $temp_posts[]=$posts[$x];
                $temp_posts_users_sent[]=$posts_users_sent[$x];
                $temp_post_ids[]=$post_ids[$x];
                $temp_post_timestamps[]=$post_timestamps[$x];
                $temp_original_post_ids[]=$original_post_ids[$x];
            }
        }
        $posts=$temp_posts;
        $posts_users_sent=$temp_posts_users_sent;
        $post_ids=$temp_post_ids;
        $post_timestamps=$temp_post_timestamps;
        $original_post_ids=$temp_original_post_ids;
        
        
        $likes=array();
        $dislikes=array();
        $comment_ids=array();
        
        
        //gets extra post information
        for($x = 0; $x < sizeof($posts_users_sent); $x++)
        {  
            $query=mysql_query("SELECT post_ids, likes, dislikes, comment_ids, comments, comment_likes, comment_dislikes, comments_user_id, comment_timestamps FROM content WHERE user_id=$posts_users_sent[$x] LIMIT 1");
            if($query&&mysql_num_rows($query)==1)
            {
                $array=mysql_fetch_row($query);
                $user_post_ids=explode('|^|*|', $array[0]);
                $user_likes=explode('|^|*|', $array[1]);
                $user_dislikes=explode('|^|*|', $array[2]);
                $user_comment_ids=explode('|^|*|', $array[3]);
                
                $index=array_search($original_post_ids[$x], $user_post_ids);
                
                if($index!=-1)
                {
                    $user_comment_ids[$index]=explode('|%|&|', $user_comment_ids[$index]);
                    $user_likes[$index]=explode('|%|&|', $user_likes[$index]);
                    $user_dislikes[$index]=explode('|%|&|', $user_dislikes[$index]);
                    
                    
                    
                    
                    $likes[]=$user_likes[$index];
                    $dislikes[]=$user_dislikes[$index];
                    $comment_ids[]=$user_comment_ids[$index];
                }
                else
                {
                    $likes[]=array();
                    $dislikes[]=array();
                    $comment_ids[]=array();
                }
            }
            else
            {
                $likes[]=array();
                $dislikes[]=array();
                $comment_ids[]=array();
            }
        }
        
        $size=sizeof($post_ids);
        $total_size=$size;
        
        $final_post_array_1=array();
        $final_post_array_2=array();
        $final_post_array_3=array();
        $final_post_array_4=array();
        $final_post_array_5=array();
        $final_post_array_6=array();
        $final_post_array_7=array();
        $final_post_array_13=array();
        
        if($size<10)
        {
            $empty=true;
            
            //takes the <10 size of current array and makes it an even 10
            for($x =0; $x < 10; $x++)
            {
                if(isset($post_ids[$x]))
                {
                    $final_post_array_1[]=$posts[$x];
                    $final_post_array_2[]=$posts_users_sent[$x];
                    $final_post_array_3[]=$post_ids[$x];
                    $final_post_array_4[]=$post_timestamps[$x];
                    $final_post_array_5[]=$likes[$x];
                    $final_post_array_6[]=$dislikes[$x];
                    $final_post_array_7[]=$comment_ids[$x];
                    $final_post_array_13[]=$original_post_ids[$x];
                }
            }
        }
        else
        {
            $temp=sizeof($post_ids)-$page;
            if($page>$size)
            {
                $temp=sizeof($post_ids)%10;
                $empty=true;
                $end=$temp-1;
                $start=0;
                $size=$size%10;
            }
            else
            {
                if($page==$size)
                    $empty=true;
                else
                    $empty=false;
                $start=$temp;
                $end=$temp+9;
                $size=10;
            }

            //reverses posts to be in chronological order
            for($x = $start; $x <= $end; $x++)
            {
                $final_post_array_1[]=$posts[$x];
                $final_post_array_2[]=$posts_users_sent[$x];
                $final_post_array_3[]=$post_ids[$x];
                $final_post_array_4[]=$post_timestamps[$x];
                $final_post_array_5[]=$likes[$x];
                $final_post_array_6[]=$dislikes[$x];
                $final_post_array_7[]=$comment_ids[$x];
                $final_post_array_13[]=$original_post_ids[$x];
            }
        }
        
        $profile_pictures=array();
        $names=array();
        $badges=array();
        $timestamp_seconds=array();
        $num_likes=array();
        $num_dislikes=array();
        $has_liked=array();
        $has_disliked=array();
        $num_comments=array();
        for($x = 0; $x < sizeof($final_post_array_1); $x++)
        {
            $profile_pictures[]=get_profile_picture($final_post_array_2[$x]);
            
            //gets names
            $names[]=get_user_name($final_post_array_2[$x]);
            
            //gets adjusted timestamps
            $temp_timestamp=$final_post_array_4[$x];
            $final_post_array_4[$x]=get_time_since($final_post_array_4[$x], $timezone);
            $timestamp_seconds[$x]=get_time_since_seconds($temp_timestamp, $timezone);
            
            //gets badges
            $badges[$x]=get_badges($final_post_array_2[$x]);
            
            //gets num likes
            if($final_post_array_5[$x][0]==""||$final_post_array_5[$x][0]=="0")
                $num_likes[]=0;
            else
                $num_likes[]=sizeof($final_post_array_5[$x]);
            
            //gets num dislikes
            if($final_post_array_6[$x][0]==""||$final_post_array_6[$x][0]=="0")
                $num_dislikes[]=0;
            else
                $num_dislikes[]=sizeof($final_post_array_6[$x]);
            
            //gets has liked
            if(in_array($_SESSION['id'], $final_post_array_5[$x])||$final_post_array_5[$x][0]==$_SESSION['id'])
                $has_liked[$x]=true;
            else
                $has_liked[$x]=false;
            
            //gets has disliked
            if(in_array($_SESSION['id'], $final_post_array_6[$x])||$final_post_array_6[$x][0]==$_SESSION['id'])
                $has_disliked[$x]=true;
            else
                $has_disliked[$x]=false;
            
            //gets comment stuff
            if($final_post_array_7[$x][0]=="")
                $num_comments[]=0;
            else
                $num_comments[]=sizeof($final_post_array_7[$x]);
        }
        
        $JSON=array();
        $JSON['post_ids']=$final_post_array_3;
        $JSON['original_post_ids']=$final_post_array_13;
        $JSON['posts_users_sent']=$final_post_array_2;
        $JSON['names']=$names;
        $JSON['posts']=$final_post_array_1;
        $JSON['profile_pictures']=$profile_pictures;
        $JSON['timestamps']=$final_post_array_4;
        $JSON['timestamp_seconds']=$timestamp_seconds;
        $JSON['is_empty']=$empty;
        $JSON['total_size']=$total_size;
        $JSON['badges']=$badges;
        $JSON['num_likes']=$num_likes;
        $JSON['num_dislikes']=$num_dislikes;
        $JSON['has_liked']=$has_liked;
        $JSON['has_disliked']=$has_disliked;
        $JSON['num_comments']=$num_comments;
        echo json_encode($JSON);
        exit();
    }
}

//gets videos
else if($num==3)
{
    $timezone=(int)($_POST['timezone']);
    $page=(int)($_POST['page'])*10;
    
    
    $query=mysql_query("SELECT video_ids, videos, video_types, videos_users_sent, video_timestamps FROM public WHERE num=1");
    if($query&&mysql_num_rows($query)==1)
    {
        $array=mysql_fetch_row($query);
        $video_ids=explode('|^|*|', $array[0]);
        $videos=explode('|^|*|', $array[1]);
        $video_types=explode('|^|*|', $array[2]);
        $videos_users_sent=explode('|^|*|', $array[3]);
        $video_timestamps=explode('|^|*|', $array[4]);
        
        
        //gets rid of terminated accounts
        $temp_video_ids=array();
        $temp_videos=array();
        $temp_video_types=array();
        $temp_videos_users_sent=array();
        $temp_video_timestamps=array();
        for($x = 0; $x < sizeof($video_ids); $x++)
        {
            if(!user_id_terminated($videos_users_sent[$x]))
            {
                $temp_video_ids[]=$video_ids[$x];
                $temp_videos[]=$videos[$x];
                $temp_video_types[]=$video_types[$x];
                $temp_videos_users_sent[]=$videos_users_sent[$x];
                $temp_video_timestamps[]=$video_timestamps[$x];
            }
        }
        
        $video_ids=$temp_video_ids;
        $videos=$temp_videos;
        $video_types=$temp_video_types;
        $videos_users_sent=$temp_videos_users_sent;
        $video_timestamps=$temp_video_timestamps;
        
        
        $size=sizeof($videos);
        $total_size=$size;

        if($size<10)
        {
            $empty=true;
            
            //takes the <10 size of current array and makes it an even 10
            for($x =0; $x < 10; $x++)
            {
                if(isset($videos[$x]))
                {
                    $final_post_array_5[]=convert_video($videos[$x], $video_types[$x]);
                    $final_post_array_2[]=$videos_users_sent[$x];
                    $final_post_array_3[]=get_time_since($video_timestamps[$x], $timezone);
                    $final_post_array_4[]=get_video_preview($videos[$x], $video_types[$x]);
                    $final_post_array_1[]=$videos[$x];
                    $final_post_array_6[]=$video_ids[$x];
                    $final_post_array_7[]=get_time_since_seconds($video_timestamps[$x], $timezone);
                }
            }
        }
        else
        {
            $temp=sizeof($videos)-$page;
            if($page>$size)
            {
                $temp=sizeof($videos)%10;
                $empty=true;
                $end=$temp-1;
                $start=0;
                $size=$size%10;
            }
            else
            {
                if($page==$size)
                    $empty=true;
                else
                    $empty=false;
                $start=$temp;
                $end=$temp+9;
                $size=10;
            }

            //reverses posts to be in chronological order
            for($x = $start; $x <= $end; $x++)
            {
                $final_post_array_5[]=convert_video($videos[$x], $video_types[$x]);
                $final_post_array_2[]=$videos_users_sent[$x];
                $final_post_array_3[]=get_time_since($video_timestamps[$x], $timezone);
                $final_post_array_4[]=get_video_preview($videos[$x], $video_types[$x]);
                $final_post_array_1[]=$videos[$x];
                $final_post_array_6[]=$video_ids[$x];
                $final_post_array_7[]=get_time_since_seconds($video_timestamps[$x], $timezone);
            }
        }
        
        
        
        $profile_pictures=array();
        $names=array();
        $badges=array();
        $num_adds=array();
        for($x = 0; $x < sizeof($final_post_array_2); $x++)
        {
            //gets profile pictures
            $profile_pictures[]=get_profile_picture($final_post_array_2[$x]);
            
            //gets names
            $names[]=get_user_name($final_post_array_2[$x]);
            
            //gets badges
            $badges[]=get_badges($final_post_array_2[$x]);
            
            //gets num adds
            $query=mysql_query("SELECT user_friends FROM user_data WHERE user_id=$final_post_array_2[$x] LIMIT 1");
            if($query&&mysql_num_rows($query)==1)
            {
                $array=mysql_fetch_row($query);
                $adds=explode('|^|*|', $array[0]);
                
                if($array[0]=='')
                    $num_adds[]=0;
                else
                    $num_adds[]=sizeof($adds);
            }
        }
        
        
        
        $JSON=array();
        $JSON['videos']=$final_post_array_1;
        $JSON['video_previews']=$final_post_array_4;
        $JSON['videos_users_sent']=$final_post_array_2;
        $JSON['video_timestamps']=$final_post_array_3;
        $JSON['timestamp_seconds']=$final_post_array_7;
        $JSON['video_embeds']=$final_post_array_5;
        $JSON['video_ids']=$final_post_array_6;
        $JSON['total_size']=$total_size;
        $JSON['empty']=$empty;
        $JSON['profile_pictures']=$profile_pictures;
        $JSON['user_names']=$names;
        $JSON['badges']=$badges;
        $JSON['num_adds']=$num_adds;
        echo json_encode($JSON);
        exit();
    }
}

//gets new users
else if($num==4)
{
    $timezone=(int)($_POST['timezone']);
    $page=(int)($_POST['page'])*20;
    
    $query=mysql_query("SELECT user_ids, user_names, user_timestamps FROM public WHERE num=1");
    if($query&&mysql_num_rows($query)==1)
    {
        $array=mysql_fetch_row($query);
        $user_ids=explode('|^|*|', $array[0]);
        $user_names=explode('|^|*|', $array[1]);
        $user_timestamps=explode('|^|*|', $array[2]);
        
        //gets rid of terminated accounts
        $temp_user_ids=array();
        $temp_user_names=array();
        $temp_user_timestamps=array();
        for($x = 0; $x < sizeof($user_ids); $x++)
        {
            if(!user_id_terminated($user_ids[$x]))
            {
                $temp_user_ids[]=$user_ids[$x];
                $temp_user_names[]=$user_names[$x];
                $temp_user_timestamps[]=$user_timestamps[$x];
            }
        }
        $user_ids=$temp_user_ids;
        $user_names=$temp_user_names;
        $user_timestamps=$temp_user_timestamps;
        
        
        if($array[0]!='')
            $total_size=sizeof($user_ids);
        else
            $total_size=0;


        if($total_size<20)
        {
            $empty=true;

            //reverses because it adds backwards in the else statement below
            $temp_user_ids=array();
            $temp_user_names=array();
            $temp_user_timestamps=array();

            for($x = sizeof($user_ids)-1; $x >=0; $x--)
            {
                $temp_user_ids[]=$user_ids[$x];
                $temp_user_names[]=$user_names[$x];
                $temp_user_timestamps[]=$user_timestamps[$x];
            }

            $user_ids=$temp_user_ids;
            $user_names=$temp_user_names;
            $user_timestamps=$temp_user_timestamps;

        }
        else
        {
            if($total_size-$page<=0)
                $empty=true;
            else
                $empty=false;

            $temp_user_ids=array();
            $temp_user_names=array();
            $temp_user_timestamps=array();

            if($page==20)
                $index=sizeof($user_ids)-$page+20-1;
            else
                $index=sizeof($user_ids)-$page+20-2;

            while(sizeof($temp_user_ids)<=20)
            {
                if($user_ids[$index]!='')
                {
                    $temp_user_ids[]=$user_ids[$index];
                    $temp_user_names[]=$user_names[$index];
                    $temp_user_timestamps[]=$user_timestamps[$index];
                }
                else
                {
                    $temp_user_ids[]='';
                    $temp_user_names[]='';
                    $temp_user_timestamps[]='';
                }

                $index--;
            }

            $user_ids=$temp_user_ids;
            $user_names=$temp_user_names;
            $user_timestamps=$temp_user_timestamps;
        }
        
        
        
        $profile_pictures=array();
        $adjusted_timestamps=array();
        $add_status=array();
        $timestamp_seconds=array();
        for($x = 0; $x < sizeof($user_ids); $x++)
        {
            //gets profile pictures
            $profile_pictures[$x]=get_profile_picture($user_ids[$x]);
            
            //gets adjusted timestamps
            $adjusted_timestamps[$x]=get_time_since($user_timestamps[$x], $timezone);
            $timestamp_seconds[$x]=get_time_since_seconds($user_timestamps[$x], $timezone);
            
            //gets add status
            if(isset($_SESSION['id'])&&user_is_friends($_SESSION['id'], $user_ids[$x])=='true')
                $add_status[$x]=3;
            else if(isset($_SESSION['id'])&&pending_request($_SESSION['id'], $user_ids[$x]))
                $add_status[$x]=2;
            else
                $add_status[$x]=1;
        }
        
        $JSON=array();
        $JSON['user_ids']=$user_ids;
        $JSON['user_names']=$user_names;
        $JSON['user_real_timestamps']=$user_timestamps;
        $JSON['user_timestamps']=$adjusted_timestamps;
        $JSON['timestamp_seconds']=$timestamp_seconds;
        $JSON['profile_pictures']=$profile_pictures;
        $JSON['total_size']=$total_size;
        $JSON['empty']=$empty;
        $JSON['add_status']=$add_status;
        echo json_encode($JSON);
        exit();
        
    }
}

//gets photo comments
else if($num==5)
{
    $data=$_POST['data'];
    $original_picture_id=(int)($data[0]);
    $user_id=(int)($data[1]);
    $timezone=(int)($data[2]);
    
    if(is_id($user_id))
    {
        $query=mysql_query("SELECT pictures, comment_ids, picture_comments, comment_likes, comment_dislikes, comments_user_sent, comment_timestamps FROM pictures WHERE user_id=$user_id LIMIT 1");
        if($query&&mysql_num_rows($query)==1)
        {
            $array=mysql_fetch_row($query);
            $picture_ids=explode('|^|*|', $array[0]);
            $comment_ids=explode('|^|*|', $array[1]);
            $comments=explode('|^|*|', $array[2]);
            $comment_likes=explode('|^|*|', $array[3]);
            $comment_dislikes=explode('|^|*|', $array[4]);
            $comments_users_sent=explode('|^|*|', $array[5]);
            $comment_timestamps=explode('|^|*|', $array[6]);

            $index=array_search($original_picture_id, $picture_ids);

            if($index!=false)
            {
                $comment_ids[$index]=explode('|%|&|', $comment_ids[$index]);
                $comments[$index]=explode('|%|&|', $comments[$index]);
                $comment_likes[$index]=explode('|%|&|', $comment_likes[$index]);
                $comment_dislikes[$index]=explode('|%|&|', $comment_dislikes[$index]);
                $comments_users_sent[$index]=explode('|%|&|', $comments_users_sent[$index]);
                $comment_timestamps[$index]=explode('|%|&|', $comment_timestamps[$index]);

                $temp_comment_timestamps=array();
                $comment_timestamp_seconds=array();
                $comment_names=array();
                $comment_profile_pictures=array();
                $comment_badges=array();
                $num_comment_likes=array();
                $num_comment_dislikes=array();
                $comments_has_liked=array();
                $comments_has_disliked=array();
                for($x = 0; $x < sizeof($comments[$index]); $x++)
                {
                    $comment_likes[$index][$x]=explode('|@|$|', $comment_likes[$index][$x]);
                    $comment_dislikes[$index][$x]=explode('|@|$|', $comment_dislikes[$index][$x]);

                    //gets comment names
                    $comment_names[]=get_user_name($comments_users_sent[$index][$x]);

                    //gets comment profile pictures
                    $comment_profile_pictures[]=get_profile_picture($comments_users_sent[$index][$x]);

                    //gets comment bages
                    $comment_badges[]=get_badges($comments_users_sent[$index][$x]);

                    //gets num comment likes
                    if($comment_likes[$index][$x][0]==""||$comment_likes[$index][$x][0]=="0")
                        $num_comment_likes[]=0;
                    else 
                        $num_comment_likes[]=sizeof($comment_likes[$index][$x]);

                    //gets num comment dislikes
                    if($comment_dislikes[$index][$x][0]==""||$comment_dislikes[$index][$x][0]=="0")
                        $num_comment_dislikes[]=0;
                    else 
                        $num_comment_dislikes[]=sizeof($comment_dislikes[$index][$x]);

                    //gets has liked
                    if(in_array($_SESSION['id'], $comment_likes[$index][$x])||$comment_likes[$index][$x][0]==$_SESSION['id'])
                        $comments_has_liked[]=true;
                    else
                        $comments_has_liked[]=false;

                    //gets has disliked
                    if(in_array($_SESSION['id'], $comment_dislikes[$index][$x])||$comment_dislikes[$index][$x][0]==$_SESSION['id'])
                        $comments_has_disliked[]=true;
                    else
                        $comments_has_disliked[]=false;

                    //gets comment timestamps
                    $temp_comment_timestamps[]=get_time_since($comment_timestamps[$index][$x], $timezone);

                    //gets comment timestamp seconds
                    $comment_timestamp_seconds[]=get_time_since_seconds($comment_timestamps[$index][$x], $timezone);
                    
                }

                $JSON['comments']=$comments[$index];
                $JSON['comment_ids']=$comment_ids[$index];
                $JSON['comments_users_sent']=$comments_users_sent[$index];
                $JSON['comment_num_likes']=$num_comment_likes;
                $JSON['comment_num_dislikes']=$num_comment_dislikes;
                $JSON['comments_has_liked']=$comments_has_liked;
                $JSON['comments_has_disliked']=$comments_has_disliked;
                $JSON['comment_timestamps']=$temp_comment_timestamps;
                $JSON['comment_timestamp_seconds']=$comment_timestamp_seconds;
                $JSON['comment_profile_pictures']=$comment_profile_pictures;
                $JSON['comment_names']=$comment_names;
                $JSON['comment_badges']=$comment_badges;
                echo json_encode($JSON);
                exit();
            }
        }
    }
}

//gets post comments
else if($num==6)
{
    $data=$_POST['data'];
    $original_post_id=(int)($data[0]);
    $user_id=(int)($data[1]);
    $timezone=(int)($data[2]);
    
    if(is_id($user_id))
    {
        $query=mysql_query("SELECT post_ids, comment_ids, comments, comment_likes, comment_dislikes, comments_user_id, comment_timestamps FROM content WHERE user_id=$user_id LIMIT 1");
        if($query&&mysql_num_rows($query)==1)
        {
            $array=mysql_fetch_row($query);
            $post_ids=explode('|^|*|', $array[0]);
            $comment_ids=explode('|^|*|', $array[1]);
            $comments=explode('|^|*|', $array[2]);
            $comment_likes=explode('|^|*|', $array[3]);
            $comment_dislikes=explode('|^|*|', $array[4]);
            $comments_users_sent=explode('|^|*|', $array[5]);
            $comment_timestamps=explode('|^|*|', $array[6]);

            $index=array_search($original_post_id, $post_ids);

            if($index!=false)
            {
                $comment_ids[$index]=explode('|%|&|', $comment_ids[$index]);
                $comments[$index]=explode('|%|&|', $comments[$index]);
                $comment_likes[$index]=explode('|%|&|', $comment_likes[$index]);
                $comment_dislikes[$index]=explode('|%|&|', $comment_dislikes[$index]);
                $comments_users_sent[$index]=explode('|%|&|', $comments_users_sent[$index]);
                $comment_timestamps[$index]=explode('|%|&|', $comment_timestamps[$index]);

                $temp_comment_timestamps=array();
                $comment_timestamp_seconds=array();
                $comment_names=array();
                $comment_profile_pictures=array();
                $comment_badges=array();
                $num_comment_likes=array();
                $num_comment_dislikes=array();
                $comments_has_liked=array();
                $comments_has_disliked=array();
                for($x = 0; $x < sizeof($comments[$index]); $x++)
                {
                    $comment_likes[$index][$x]=explode('|@|$|', $comment_likes[$index][$x]);
                    $comment_dislikes[$index][$x]=explode('|@|$|', $comment_dislikes[$index][$x]);

                    //gets comment names
                    $comment_names[]=get_user_name($comments_users_sent[$index][$x]);

                    //gets comment profile pictures
                    $comment_profile_pictures[]=get_profile_picture($comments_users_sent[$index][$x]);

                    //gets comment bages
                    $comment_badges[]=get_badges($comments_users_sent[$index][$x]);

                    //gets num comment likes
                    if($comment_likes[$index][$x][0]==""||$comment_likes[$index][$x][0]=="0")
                        $num_comment_likes[]=0;
                    else 
                        $num_comment_likes[]=sizeof($comment_likes[$index][$x]);

                    //gets num comment dislikes
                    if($comment_dislikes[$index][$x][0]==""||$comment_dislikes[$index][$x][0]=="0")
                        $num_comment_dislikes[]=0;
                    else 
                        $num_comment_dislikes[]=sizeof($comment_dislikes[$index][$x]);

                    //gets has liked
                    if(in_array($_SESSION['id'], $comment_likes[$index][$x])||$comment_likes[$index][$x][0]==$_SESSION['id'])
                        $comments_has_liked[]=true;
                    else
                        $comments_has_liked[]=false;

                    //gets has disliked
                    if(in_array($_SESSION['id'], $comment_dislikes[$index][$x])||$comment_dislikes[$index][$x][0]==$_SESSION['id'])
                        $comments_has_disliked[]=true;
                    else
                        $comments_has_disliked[]=false;

                    //gets comment timestamps
                    $temp_comment_timestamps[]=get_time_since($comment_timestamps[$index][$x], $timezone);

                    //gets comment timestamp seconds
                    $comment_timestamp_seconds[]=get_time_since_seconds($comment_timestamps[$index][$x], $timezone);
                }

                $JSON['comments']=$comments[$index];
                $JSON['comment_ids']=$comment_ids[$index];
                $JSON['comments_users_sent']=$comments_users_sent[$index];
                $JSON['comment_num_likes']=$num_comment_likes;
                $JSON['comment_num_dislikes']=$num_comment_dislikes;
                $JSON['comments_has_liked']=$comments_has_liked;
                $JSON['comments_has_disliked']=$comments_has_disliked;
                $JSON['comment_timestamps']=$temp_comment_timestamps;
                $JSON['comment_timestamp_seconds']=$comment_timestamp_seconds;
                $JSON['comment_profile_pictures']=$comment_profile_pictures;
                $JSON['comment_names']=$comment_names;
                $JSON['comment_badges']=$comment_badges;
                echo json_encode($JSON);
                exit();
            }
        }
    }
}

//gets video comments
else if($num==7)
{
    
}