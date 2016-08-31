<?php
@include('init.php');
include('universal_functions.php');
$allowed="users";
include('security_checks.php');

$video=(int)($_POST['video']);

$query=mysql_query("SELECT video_ids, videos, video_types, video_timestamps FROM content WHERE user_id=$_SESSION[id] LIMIT 1");
if($query&&mysql_num_rows($query)==1)
{
    $array=mysql_fetch_row($query);
    $video_ids=explode('|^|*|', $array[0]);
    $videos=explode('|^|*|', $array[1]);
    $video_types=explode('|^|*|', $array[2]);
    $video_timestamps=explode('|^|*|', $array[3]);

    $temp_video_ids=array();
    $temp_videos=array();
    $temp_video_types=array();
    $temp_video_timestamps=array();
    for($x = 0; $x < sizeof($videos); $x++)
    {
        if($video!=$video_ids[$x])
        {
            $temp_video_ids[]=$video_ids[$x];
            $temp_videos[]=$videos[$x];
            $temp_video_types[]=$video_types[$x];
            $temp_video_timestamps[]=$video_timestamps[$x];
        }
    }

    $video_ids=implode('|^|*|', $temp_video_ids);
    $videos=implode('|^|*|', $temp_videos);
    $video_types=implode('|^|*|', $temp_video_types);
    $video_timestamps=implode('|^|*|', $temp_video_timestamps);
    $query=mysql_query("UPDATE content SET video_ids='$video_ids', videos='$videos', video_types='$video_types', video_timestamps='$video_timestamps' WHERE user_id=$_SESSION[id]");
    if($query)
        echo "Video Deleted!";
    else
    {
        echo "Video removal fail!";
        log_error("remove_video.php: ",mysql_error());
    }
}
else
{
    echo "Something went wrong";
    log_error("remove_video.php: ",mysql_error());
}