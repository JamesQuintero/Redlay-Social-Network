<?php
include('init.php');
include('universal_functions.php');
$allowed="users";
include('security_checks.php');

$num=(int)($_POST['num']);
//gets video
if($num==1)
{
    $video_id=(int)($_POST['video_id']);
    $profile_id=(int)($_POST['user_id']);
    $timezone=(int)($_POST['timezone']);
    
    if(is_id($profile_id)&&user_id_exists($profile_id)&&!user_id_terminated($profile_id))
    {
        $query=mysql_query("SELECT video_ids, videos, video_types, video_audience, video_likes, video_dislikes, video_comment_ids, video_comments, video_comment_likes, video_comment_dislikes, video_comments_users_sent, video_comment_timestamps, video_timestamps FROM content WHERE user_id=$profile_id LIMIT 1");
        if($query&&mysql_num_rows($query)==1)
        {
            $array=mysql_fetch_array($query);
            $video_ids=explode('|^|*|', $array[0]);
            $videos=explode('|^|*|', $array[1]);
            $video_types=explode('|^|*|', $array[2]);
            $video_audience=explode('|^|*|', $array[3]);
            $video_likes=explode('|^|*|', $array[4]);
            $video_dislikes=explode('|^|*|', $array[5]);
            $video_comment_ids=explode('|^|*|', $array[6]);
            $video_comments=explode('|^|*|', $array[7]);
            $video_comment_likes=explode('|^|*|', $array[8]);
            $video_comment_dislikes=explode('|^|*|', $array[9]);
            $video_comments_users_sent=explode('|^|*|', $array[10]);
            $video_comment_timestamps=explode('|^|*|', $array[11]);
            $video_timestamps=explode('|^|*|', $array[12]);

            $index=-1;
            for($x = 0; $x < sizeof($video_ids); $x++)
            {
                if($video_ids[$x]==$video_id)
                    $index=$x;
            }
            
            
            if($index!=-1)
            {
                $video_type=$video_types[$index];
                $video_audience=explode('|%|&|', $video_audience[$index]);
                $video_likes=explode('|%|&|', $video_likes[$index]);
                $video_dislikes=explode('|%|&|', $video_dislikes[$index]);
                $video_comment_ids=explode('|%|&|', $video_comment_ids[$index]);
                $video_comments=explode('|%|&|', $video_comments[$index]);
                $video_comment_likes=explode('|%|&|', $video_comment_likes[$index]);
                $video_comment_dislikes=explode('|%|&|', $video_comment_dislikes[$index]);
                $video_comments_users_sent=explode('|%|&|', $video_comments_users_sent[$index]);
                $video_comment_timestamps=explode('|%|&|', $video_comment_timestamps[$index]);
                
                for($x = 0; $x < sizeof($video_comment_likes); $x++)
                {
                    $video_comment_likes[$x]=explode('|@|$|', $video_comment_likes[$x]);
                    $video_comment_dislikes[$x]=explode('|@|$|', $video_comment_dislikes[$x]);
                }
                
                //gets rid of terminated accounts
                $user_ids_terminated=array();
                $temp_video_likes=array();
                $temp_video_dislikes=array();
                for($x = 0; $x < sizeof($video_likes); $x++)
                {
                    if(!in_array($video_likes[$x], $user_ids_terminated)&&!user_id_terminated($video_likes[$x]))
                        $temp_video_likes[]=$video_likes[$x];
                    else if(!in_array($video_likes[$x], $user_ids_terminated))
                    {
                        $user_ids_terminated[]=$video_likes[$x];
                        $temp_video_likes[]="";
                    }
                    
                    
                    if(!in_array($video_dislikes[$x], $user_ids_terminated)&&!user_id_terminated($video_dislikes[$x]))
                        $temp_video_dislikes[]=$video_dislikes[$x];
                    else if(!in_array($video_dislikes[$x], $user_ids_terminated))
                    {
                        $user_ids_terminated[]=$video_dislikes[$x];
                        $temp_video_dislikes[]="";
                    }
                }
                $video_likes=$temp_video_likes;
                $video_dislikes=$temp_video_dislikes;
                
                
                
                //gets correct timestamp
                $video_timestamp=$video_timestamps[$index];
                $timestamp=get_time_since($video_timestamp, $timezone);
                $timestamp_seconds=get_time_since_seconds($video_timestamp, $timezone);
                
                //gets comment timestamps
                //$comment_timestamps[$index]=explode('|%|&|', $comment_timestamps[$index]);
                $new_comment_timestamps=array();
                $comment_timestamp_seconds=array();
                for($x = 0; $x < sizeof($video_comment_timestamps); $x++)
                {
                    $new_comment_timestamps[$x]=get_time_since($video_comment_timestamps[$x], $timezone);
                    $comment_timestamp_seconds[$x]=get_time_since_seconds($video_comment_timestamps[$x], $timezone);
                }
                
                //gets badges
                $badges=get_badges($profile_id);

                $num_comment_likes=array();
                $num_comment_dislikes=array();
                $has_liked_comment=array();
                $has_disliked_comment=array();
                for($x =0; $x < sizeof($video_comment_likes); $x++)
                {
                    //gets number of comment likes
                    if($video_comment_likes[$x][0]=='')
                        $num_comment_likes[$x]=0;
                    else
                        $num_comment_likes[$x]=sizeof($video_comment_likes[$x]);

                    //gets number of commeent dislikes
                    if($video_comment_dislikes[$x][0]=='')
                        $num_comment_dislikes[$x]=0;
                    else
                        $num_comment_dislikes[$x]=sizeof($video_comment_dislikes[$x]);
                    
                    //if has liked comment
                    if(in_array($_SESSION['id'], $video_comment_likes[$x]))
                        $has_liked_comment[]=true;
                    else
                        $has_liked_comment[]=false;
                    
                    //if has disliked comment
                    if(in_array($_SESSION['id'], $video_comment_dislikes[$x]))
                        $has_disliked_comment[]=true;
                    else
                        $has_disliked_comment[]=false;
                }

                $comment_names=array();
                $comment_badges=array();
                $comment_profile_pictures=array();
                for($x = 0;$x < sizeof($video_comments_users_sent); $x++)
                {
                    //gets comment names
                    $comment_names[]=get_user_name($video_comments_users_sent[$x]);
                    
                    //gets comment profile pictures
                    $comment_profile_pictures[]=get_profile_picture($video_comments_users_sent[$x]);
                    
                    //gets comment badges
                    $comment_badges[]=get_badges($video_comments_users_sent[$x]);
                }

                //gets number of likes
                if($video_likes[0]==0)
                    $num_likes=0;
                else
                    $num_likes=sizeof($video_likes);
                
                //gets has liked
                if(in_array($_SESSION['id'], $video_likes))
                    $has_liked=true;
                else
                    $has_liked=false;
                
                //gets has disliked
                if(in_array($_SESSION['id'], $video_dislikes))
                    $has_disliked=true;
                else
                    $has_disliked=false;

                //gets number of dislikes
                if($video_dislikes[0]==0)
                    $num_dislikes=0;
                else
                    $num_dislikes=sizeof($video_dislikes);

                if($profile_id!=$_SESSION['id'])
                {
                    //checks if current user is allowed to view post
                    $user_audiences=get_audience_current_user($profile_id);
                    $bool=false;
                    
                    if(can_view($user_audiences, $video_audience))
                        $bool=true;
                }
                else
                    $bool=true;

                //gets number of comments
                if($video_comments[0]=='')
                    $num_comments=0;
                else
                    $num_comments=sizeof($video_comments);


                
                
                if($bool)
                {
                    $JSON=array();
                    $JSON['video']=$videos[$index];
                    $JSON['video_type']=$video_type;
                    $JSON['num_likes']=$num_likes;
                    $JSON['num_dislikes']=$num_dislikes;
                    $JSON['has_liked']=$has_liked;
                    $JSON['has_disliked']=$has_disliked;
                    $JSON['timestamp']=$timestamp;
                    $JSON['timestamp_seconds']=$timestamp_seconds;
                    $JSON['num_comments']=$num_comments;
                    $JSON['audience_groups']=$video_audience;
                    $JSON['comments']=$video_comments;
                    $JSON['comments_users_sent']=$video_comments_users_sent;
                    $JSON['comment_timestamps']=$new_comment_timestamps;
                    $JSON['comment_timestamp_seconds']=$comment_timestamp_seconds;
                    $JSON['comment_names']=$comment_names;
                    $JSON['num_comment_likes']=$num_comment_likes;
                    $JSON['num_comment_dislikes']=$num_comment_dislikes;
                    $JSON['has_liked_comment']=$has_liked_comment;
                    $JSON['has_disliked_comment']=$has_disliked_comment;
                    $JSON['user_name']=get_user_name($profile_id);
                    $JSON['profile_picture']=get_profile_picture($profile_id);
                    $JSON['comment_ids']=$video_comment_ids;
                    $JSON['badges']=$badges;
                    $JSON['comment_badges']=$comment_badges;
                    $JSON['comment_profile_pictures']=$comment_profile_pictures;
                    $JSON['video_preview']=get_video_preview($videos[$index], $video_type);
                    $JSON['video_url']=convert_video($videos[$index], $video_type);
                    echo json_encode($JSON);
                    exit();
                }
                else
                {
                    $JSON=array();
                    $JSON['post']='';
                    $JSON['user_id_posted']=0;
                    $JSON['like_ids']='';
                    $JSON['dislike_ids']='';
                    $JSON['num_likes']=0;
                    $JSON['num_dislikes']=0;
                    $JSON['timestamp']='';
                    $JSON['num_comments']=0;
                    $JSON['audience_groups']='';
                    $JSON['comments']='';
                    $JSON['comment_likes']='';
                    $JSON['comment_dislikes']='';
                    $JSON['comments_users_sent']='';
                    $JSON['comment_timestamps']='';
                    $JSON['comment_names']='';
                    $JSON['num_comment_likes']='';
                    $JSON['num_comment_dislikes']='';
                    $JSON['user_name']='';
                    $JSON['profile_picture']='';
                    $JSON['comment_ids']='';
                    $JSON['badges']='';
                    $JSON['comment_badges']='';
                    $JSON['comment_profile_pictures']='';
                    echo json_encode($JSON);
                    exit();
                }
            }
        }
    }
}

//changes video's audience
else if($num==2)
{
    $video_id=clean_string($_POST['video_id']);
    $groups=$_POST['groups'];

    //determines whether groups are valid
    $query=mysql_query("SELECT audience_defaults FROM public WHERE num=1 LIMIT 1");
    $query2=mysql_query("SELECT audience_group_lists FROM user_data WHERE user_id=$_SESSION[id] LIMIT 1");
    if($query&&mysql_num_rows($query)==1&&$query2&&mysql_num_rows($query2)==1)
    {
        $array=mysql_fetch_row($query);
        $array2=mysql_fetch_row($query2);

        $audience_defaults=explode('|^|*|', $array[0]);
        $audience_list=explode('|^|*|', $array2[0]);

        if(!in_array('Everyone', $groups))
        {
            $group_array=array();
            for($x = 0; $x < sizeof($groups); $x++)
            {
                if(in_array($groups[$x], $audience_defaults)||in_array($groups[$x], $audience_list))
                    $group_array[]=$groups[$x];
            }
            $groups=$group_array;
        }
        else
        {
            $groups=array();
            $groups[0]='Everyone';
        }
    }

    //if groups are valid and user selected at least one group
    if(isset($groups[0]))
    {
        $query=mysql_query("SELECT video_ids, video_audience FROM content WHERE user_id=$_SESSION[id] LIMIT 1");
        if($query&&mysql_num_rows($query)==1)
        {
            $array=mysql_fetch_row($query);
            $video_ids=explode('|^|*|', $array[0]);
            $audiences=explode('|^|*|', $array[1]);

            $index=-1;
            for($x = 0; $x < sizeof($video_ids); $x++)
            {
                if($video_id==$video_ids[$x])
                    $index=$x;
            }

            if($index!=-1)
            {
                $audiences[$index]=implode('|%|&|', $groups);

                $audiences=implode('|^|*|', $audiences);
                $query=mysql_query("UPDATE content SET video_audience='$audiences' WHERE user_id=$_SESSION[id]");
                if($query)
                    echo "Audience changed";
                else
                {
                    echo "Sometehing went wrong. We are working on fixing it";
                    send_mail_error("view_video_query.php: (2): ", mysql_error());
                }
            }
            else
                echo "Video ID invalid";
        }
        else
        {
            echo "Something went wrong. We are working on fixing it";
            send_mail_error("view_video_query.php: (2): ", mysql_error());
        }
    }
}