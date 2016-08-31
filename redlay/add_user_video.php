<?php
include('init.php');
include('universal_functions.php');
$allowed="users";
include('security_checks.php');


$video=clean_string($_POST['video']);
$audience=$_POST['audience'];

$valid_video=false;
if($video!='')
{
    //if user selected Everyone and other groups, this changes it to just Everyone
     if(in_array('Everyone', $audience)||$audience[0]=='')
     {
           $audience=array();
           $audience[0]='Everyone';
     }

    //checks whether all audience groups are valid
    $bool=true;
    for($x = 0; $x < sizeof($audience); $x++)
    {
       if(!is_valid_audience($audience[$x]))
          $bool=false;
    }

    //if audience is valid
    if($bool==true)
    {
        //if youtube
        if((strstr($video, 'youtube.com/watch?')==true||strstr($video, 'youtube.com/v/')==true))
        {
            //if regular video
            if(strstr($video, 'youtube.com/v/')==false)
            {
                if(strpos($video, 'v=')!=false)
                {
                    //original: youtube.com/watch?annotation_id=annotation_370587&v=X_QNBwvBV4Y
                    //after: X_QNBwvBV4Y
                    $video=substr($video, (strpos($video, 'v=')+2), 11);

                    $valid_video=true;
                    $type='youtube';
                }
                else
                    $valid_video=false;
            }

            //if embedded video
            else
            {
                $video=substr($video, (strpos($video, 'v/')+2), 11);

                $valid_video=true;
                $type='youtube';
            }
        }

        //if vimeo
        else if(strstr($video, 'vimeo.com/')==true)
        {
            //if regular video
            if(strstr($video, 'vimeo.com/video/')==false)
            {
                //original: http://vimeo.com/42480177
                //after: 42480177
                $video=substr($video, (strpos($video, '.com/')+5));

                //cleans out parameters
                $temp_video=explode('?', $video);
                $video=$temp_video[0];

                $valid_video=true;
                $type='vimeo';
            }

            //if embedded video
            else
            {
                //original: http://play.vimeo.com/video/42480177?badge=0
                //after: 42480177?badge=0
                $video=substr($video, (strpos($video, '.com/video/')+11));

                //cleans out parameters
                $temp_video=explode('?', $video);
                $video=$temp_video[0];

                $valid_video=true;
                $type='vimeo';
            }
        }
        else
            $valid_video=false;









        if($valid_video)
        {
            $query=mysql_query("SELECT video_ids, videos, video_types, video_audience, video_likes, video_dislikes, video_comment_ids, video_comments, video_comment_likes, video_comment_dislikes, video_comments_users_sent, video_comment_timestamps, video_timestamps FROM content WHERE user_id=$_SESSION[id] LIMIT 1");
            if($query&&mysql_num_rows($query)==1)
            {
                $array=mysql_fetch_row($query);
                $video_ids=explode('|^|*|', $array[0]);
                $videos=explode('|^|*|', $array[1]);
                $video_types=explode('|^|*|', $array[2]);
                $video_audiences=explode('|^|*|', $array[3]);
                $video_likes=explode('|^|*|', $array[4]);
                $video_dislikes=explode('|^|*|', $array[5]);
                $video_comment_ids=explode('|^|*|', $array[6]);
                $video_comments=explode('|^|*|', $array[7]);
                $video_comment_likes=explode('|^|*|', $array[8]);
                $video_comment_dislikes=explode('|^|*|', $array[9]);
                $video_comment_timestamps=explode('|^|*|', $array[10]);
                $video_timestamps=explode('|^|*|', $array[11]);

                if($array[0]=='')
                {
                    $video_id=0;
                    $video_ids[0]=0;
                    $videos[0]=$video;
                    $video_types[0]=$type;
                    $video_audiences[0]=implode('|%|&|', $audience);
                    $video_likes[0]='';
                    $video_dislikes[0]="";
                    $video_comment_ids[0]="";
                    $video_comments[0]="";
                    $video_comment_likes[0]="";
                    $video_comment_dislikes[0]="";
                    $video_comment_timestamps[0]="";
                    $video_timestamps[0]=get_date();
                }
                else
                {
                    $video_id=end($video_ids)+1;
                    $video_ids[]=$video_id;
                    $videos[]=$video;
                    $video_types[]=$type;
                    $video_audiences[]=implode('|%|&|', $audience);
                    $video_likes[]='';
                    $video_dislikes[]="";
                    $video_comment_ids[]="";
                    $video_comments[]="";
                    $video_comment_likes[]="";
                    $video_comment_dislikes[]="";
                    $video_comment_timestamps[]="";
                    $video_timestamps[]=get_date();
                }

                $video_ids=implode('|^|*|', $video_ids);
                $videos=implode('|^|*|', $videos);
                $video_types=implode('|^|*|', $video_types);
                $video_audiences=implode('|^|*|', $video_audiences);
                $video_likes=implode('|^|*|', $video_likes);
                $video_dislikes=implode('|^|*|', $video_dislikes);
                $video_comment_ids=implode('|^|*|', $video_comment_ids);
                $video_comments=implode('|^|*|', $video_comments);
                $video_comment_likes=implode('|^|*|', $video_comment_likes);
                $video_comment_dislikes=implode('|^|*|', $video_comment_dislikes);
                $video_comment_timestamps=implode('|^|*|', $video_comment_timestamps);
                $video_timestamps=implode('|^|*|', $video_timestamps);
                $query=mysql_query("UPDATE content SET video_ids='$video_ids', videos='$videos', video_types='$video_types',video_audience='$video_audiences', video_likes='$video_likes', video_dislikes='$video_dislikes', video_comment_ids='$video_comment_ids', video_comments='$video_comments', video_comment_likes='$video_comment_likes', video_comment_dislikes='$video_comment_dislikes', video_comment_timestamps='$video_comment_timestamps', video_timestamps='$video_timestamps' WHERE user_id=$_SESSION[id]");
                if($query)
                {
                    echo "Video posted!";

                    $privacy=get_user_privacy_settings($_SESSION['id']);
                    if($privacy[1][3]=='yes')
                    {
                        $query=mysql_query("SELECT video_ids, videos, video_types, videos_users_sent, video_timestamps FROM public WHERE num=1");
                        if($query)
                        {
                            $array=mysql_fetch_row($query);
                            $video_ids=explode('|^|*|', $array[0]);
                            $videos=explode('|^|*|', $array[1]);
                            $video_types=explode('|^|*|', $array[2]);
                            $videos_users_sent=explode('|^|*|', $array[3]);
                            $video_timestamps=explode('|^|*|', $array[4]);


                            if($array[0]!='')
                            {
                                $video_ids[]=$video_id;
                                $videos[]=$video;
                                $video_types[]=$type;
                                $videos_users_sent[]=$_SESSION['id'];
                                $video_timestamps[]=get_date();
                            }
                            else
                            {
                                $video_ids[0]=$video_id;
                                $videos[0]=$video;
                                $video_types[0]=$type;
                                $videos_users_sent[0]=$_SESSION['id'];
                                $video_timestamps[0]=get_date();
                            }

                            $video_ids=implode('|^|*|', $video_ids);
                            $videos=implode('|^|*|', $videos);
                            $video_types=implode('|^|*|', $video_types);
                            $videos_users_sent=implode('|^|*|', $videos_users_sent);
                            $video_timestamps=implode('|^|*|', $video_timestamps);
                            $query=mysql_query("UPDATE public SET video_ids='$video_ids', videos='$videos', video_types='$video_types', videos_users_sent='$videos_users_sent', video_timestamps='$video_timestamps' WHERE num=1");
                        }
                    }
                }
                else
                {
                    echo "Something went wrong";
                    log_error("add_user_video.php", mysql_error());
                }
            }
            else
            {
                echo "Something went wrong!";
                log_error("add_user_video.php", mysql_error());
            }
        }
        else
            echo "Not valid video";
    }
    else
        echo "Invalid audience";
}
else
    echo "Field is empty";