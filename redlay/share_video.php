<?php
@include('init.php');
include('universal_functions.php');
$allowed="users";
include('security_checks.php');

$video=(int)($_POST['video']);
$ID=(int)($_POST['user_id']);

if(is_id($ID)&&user_id_exists($ID))
{
    if($video>=0)
    {
        $query=mysql_query("SELECT video_ids, videos, video_types FROM content WHERE user_id=$ID LIMIT 1");
        if($query&&mysql_num_rows($query)==1)
        {
            $array=mysql_fetch_row($query);
            $video_ids=explode('|^|*|', $array[0]);
            $videos=explode('|^|*|', $array[1]);
            $video_types=explode('|^|*|', $array[2]);
            
            $index=-1;
            for($x = 0; $x < sizeof($video_ids); $x++)
            {
                if($video==$video_ids[$x])
                    $index=$x;
            }
            
            if($index!=-1)
            {
                $query=mysql_query("SELECT video_ids, videos, video_types, video_audience, video_likes, video_dislikes, video_comment_ids, video_comments, video_comment_likes, video_comment_dislikes, video_comments_users_sent, video_comment_timestamps, video_timestamps FROM content WHERE user_id=$_SESSION[id] LIMIT 1");
                if($query&&mysql_num_rows($query)==1)
                {
                    $array=mysql_fetch_row($query);
                    $current_video_ids=explode('|^|*|', $array[0]);
                    $current_videos=explode('|^|*|', $array[1]);
                    $current_video_types=explode('|^|*|', $array[2]);
                    $current_video_audience=explode('|^|*|', $array[3]);
                    $current_video_likes=explode('|^|*|', $array[4]);
                    $current_video_dislikes=explode('|^|*|', $array[5]);
                    $current_video_comment_ids=explode('|^|*|', $array[6]);
                    $current_video_comments=explode('|^|*|', $array[7]);
                    $current_video_comment_likes=explode('|^|*|', $array[8]);
                    $current_video_comment_dislikes=explode('|^|*|', $array[9]);
                    $current_video_comments_users_sent=explode('|^|*|', $array[10]);
                    $current_video_comment_timestamps=explode('|^|*|', $array[11]);
                    $current_video_timestamps=explode('|^|*|', $array[12]);
                    
                    if($array[0]!='')
                    {
                        $current_video_ids[]=end($current_video_ids)+1;
                        $current_videos[]=$videos[$index];
                        $current_video_types[]=$video_types[$index];
                        $current_video_audience[]="Everyone";
                        $current_video_likes[]="";
                        $current_video_dislikes[]="";
                        $current_video_comment_ids[]="";
                        $current_video_comments[]="";
                        $current_video_comment_likes[]="";
                        $current_video_comment_dislikes[]="";
                        $current_video_comments_users_sent[]="";
                        $current_video_comment_timestamps[]="";
                        $current_video_timestamps[]=get_date();
                    }
                    else
                    {
                        $current_video_ids[0]=0;
                        $current_videos[0]=$videos[$index];
                        $current_video_types[0]=$video_types[$index];
                        $current_video_audience[0]="Everyone";
                        $current_video_likes[0]="";
                        $current_video_dislikes[0]="";
                        $current_video_comment_ids[0]="";
                        $current_video_comments[0]="";
                        $current_video_comment_likes[0]="";
                        $current_video_comment_dislikes[0]="";
                        $current_video_comments_users_sent[0]="";
                        $current_video_comment_timestamps[0]="";
                        $current_video_timestamps[0]=get_date();
                    }
                    
                    $video_ids=implode('|^|*|', $current_video_ids);
                    $videos=implode('|^|*|', $current_videos);
                    $video_types=implode('|^|*|', $current_video_types);
                    $video_audience=implode('|^|*|', $current_video_audience);
                    $video_likes=implode('|^|*|', $current_video_likes);
                    $video_dislikes=implode('|^|*|', $current_video_dislikes);
                    $video_comment_ids=implode('|^|*|', $current_video_comment_ids);
                    $video_comments=implode('|^|*|', $current_video_comments);
                    $video_comment_likes=implode('|^|*|', $current_video_comment_likes);
                    $video_comment_dislikes=implode('|^|*|', $current_video_comment_dislikes);
                    $video_comments_users_sent=implode('|^|*|', $current_video_comments_users_sent);
                    $video_comment_timestamps=implode('|^|*|', $current_video_comment_timestamps);
                    $video_timestamps=implode('|^|*|', $current_video_timestamps);
                    $query=mysql_query("UPDATE content SET video_ids='$video_ids', videos='$videos', video_types='$video_types', video_audience='$video_audience', video_likes='$video_likes', video_dislikes='$video_dislikes', video_comment_ids='$video_comment_ids', video_comments='$video_comments', video_comment_likes='$video_comment_likes', video_comment_dislikes='$video_comment_dislikes', video_comments_users_sent='$video_comments_users_sent', video_comment_timestamps='$video_comment_timestamps', video_timestamps='$video_timestamps' WHERE user_id=$_SESSION[id]");
                    if($query)
                        echo "Video shared";
                    else
                    {
                        echo "Something went wrong";
                        log_error("share_video.php: ", mysql_error());
                    }
                }
                else
                    echo "Something went wrong 2";
            }
            else
                echo "Video doesn't exist";
        }
    }
    else
        echo "Invalid video id";
}
else 
    echo "Invalid user ID";