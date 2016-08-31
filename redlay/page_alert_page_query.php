<?php
include('init.php');
include('universal_functions.php');
$allowed="pages";
include('security_checks.php');

$num=(int)($_POST['num']);

//gets alerts
if($num==1)
{
    $timezone=(int)($_POST['timezone']);
    $page=(int)($_POST['page'])*10;
    
    if($page>0)
    {
        $query=mysql_query("SELECT alert_user_ids, alert_timestamps, alert_information, alerts_read, new_alerts, alert_ids FROM page_alerts WHERE page_id=$_SESSION[page_id] LIMIT 1");
        if($query&&mysql_num_rows($query)==1)
        {
            $array=mysql_fetch_row($query);
            $user_ids=explode('|^|*|', $array[0]);
            $timestamps=explode('|^|*|', $array[1]);
            $text=explode('|^|*|', $array[2]);
            $read=explode('|^|*|', $array[3]);
            $alert_ids=explode('|^|*|', $array[5]);

            if($array[0]!='')
            {
                $total_size=sizeof($user_ids);

                if($total_size<=10)
                {
                    //adds elements in reverse order for later
                    $empty=true;
                    for($x = sizeof($user_ids)-1; $x >=0 ; $x--)
                    {
                        $temp_user_ids[]=$user_ids[$x];
                        $temp_timestamps[]=$timestamps[$x];
                        
                        
                        //gets picture link
                        $text[$x]=explode('|%|&|', $text[$x]);
                        if($text[$x][0]=="picture_comment"||$text[$x][0]=="picture_like"||$text[$x][0]=="picture_dislike"||$text[$x][0]=="liked_picture_comment"||$text[$x][0]=="disliked_picture_comment"||$text[$x][0]=="comment_same_picture")
                        {
                            $query=mysql_query("SELECT pictures, image_types FROM page_pictures WHERE page_id=$_SESSION[page_id] LIMIT 1");
                            if($query&&mysql_num_rows($query)==1)
                            {
                                $array=mysql_fetch_row($query);
                                $pictures=explode('|^|*|', $array[0]);
                                $image_types=explode('|^|*|', $array[1]);
                                
                                $index=-1;
                                for($y =0; $y < sizeof($pictures); $y++)
                                {
                                    if($pictures[$y]==$text[$x][1])
                                        $index=$y;
                                }
                                
                                if($index!=-1)
                                {
                                    if($text[$x][0]=="picture_comment"||$text[$x][0]=="picture_like"||$text[$x][0]=="picture_dislike")
                                        $text[$x][]="https://s3.amazonaws.com/bucket_name/users/$_SESSION[id]/thumbs/".$pictures[$index].".".$image_types[$index];
                                    else if($text[$x][0]=="liked_picture_comment"||$text[$x][0]=="disliked_picture_comment"||$text[$x][0]=="comment_same_picture")
                                        $text[$x][]="https://s3.amazonaws.com/bucket_name/users/".$text[$x][2]."/thumbs/".$pictures[$index].".".$image_types[$index];
                                }
                            }
                        }
                        else if($text[$x][0]=="video_like"||$text[$x][0]=="video_comment"||$text[$x][0]=="video_comment_like")
                        {
                            $query2=mysql_query("SELECT video_ids, videos, video_types FROM page_content WHERE page_id=".$text[$x][1]." LIMIT 1");
                            if($query2&&mysql_num_rows($query2)==1)
                            {
                                $array2=mysql_fetch_row($query2);
                                $video_ids=explode('|^|*|', $array2[0]);
                                $videos=explode('|^|*|', $array2[1]);
                                $video_types=explode('|^|*|', $array2[2]);
                                
                                $index=-1;
                                for($y = 0; $y < sizeof ($video_ids); $y++)
                                {
                                    if($video_ids[$y]==$text[$x][2])
                                        $index=$y;
                                }
                                
                                if($index!=-1)
                                    $text[$x][]=get_video_preview($videos[$index], $video_types[$index]);
                                else
                                    $text[$x][]="";
                            }
                        }
                        
                        $temp_text[]=$text[$x];
                        
                        $temp_read[]=$read[$x];
                        $temp_alert_ids[]=$alert_ids[$x];
                        
                    }


                    //takes the < 10 size of current array and makes it an even 10
                    for($x =0; $x < 10; $x++)
                    {
                        $array_user_ids[]=$temp_user_ids[$x];
                        $array_timestamps[]=$temp_timestamps[$x];
                        $array_text[]=$temp_text[$x];
                        $array_read[]=$temp_read[$x];
                        $array_alert_ids[]=$temp_alert_ids[$x];
                    }

                    $size=$total_size;
                }
                else
                {
                    $temp=sizeof($user_ids)-$page;
                    if($page>$total_size)
                    {
                        $temp=sizeof($user_ids)%10;
                        $empty=true;
                        $start=$temp-1;
                        $end=0;
                        $size=$total_size%10;
                    }
                    else
                    {
                        if($page!=$total_size)
                            $empty=false;
                        else
                            $empty=true;
                        $end=$temp;
                        $start=$temp+9;
                        $size=10;
                    }

                    //reverses array
                    for($x = $start; $x >= $end; $x--)
                    {
                        $array_user_ids[]=$user_ids[$x];
                        $array_timestamps[]=$timestamps[$x];
                        
                        //gets picture link
                        $text[$x]=explode('|%|&|', $text[$x]);
                        
                        if($text[$x][0]=="picture_comment"||$text[$x][0]=="picture_like"||$text[$x][0]=="picture_dislike")
                        {
                            $query=mysql_query("SELECT pictures, image_types FROM pictures WHERE user_id=$_SESSION[id] LIMIT 1");
                            if($query&&mysql_num_rows($query)==1)
                            {
                                $array=mysql_fetch_row($query);
                                $pictures=explode('|^|*|', $array[0]);
                                $image_types=explode('|^|*|', $array[1]);
                                
                                $index=-1;
                                for($y =0; $y < sizeof($pictures); $y++)
                                {
                                    if($pictures[$y]==$text[$x][1])
                                        $index=$y;
                                }
                                
                                if($index!=-1)
                                {
                                    if($text[$x][0]=="picture_comment"||$text[$x][0]=="picture_like"||$text[$x][0]=="picture_dislike")
                                        $text[$x][]="https://s3.amazonaws.com/bucket_name/users/$_SESSION[id]/thumbs/".$pictures[$index].".".$image_types[$index];
                                    else if($text[$x][0]=="liked_picture_comment"||$text[$x][0]=="disliked_picture_comment")
                                        $text[$x][]="https://s3.amazonaws.com/bucket_name/users/".$text[$x][2]."/thumbs/".$pictures[$index].".".$image_types[$index];
                                }
                            }
                        }
                        else if($text[$x][0]=="comment_same_picture"||$text[$x][0]=="liked_picture_comment"||$text[$x][0]=="disliked_picture_comment")
                        {
                            $query=mysql_query("SELECT pictures, image_types FROM pictures WHERE user_id=".$text[$x][2]." LIMIT 1");
                            if($query&&mysql_num_rows($query)==1)
                            {
                                $array=mysql_fetch_row($query);
                                $pictures=explode('|^|*|', $array[0]);
                                $image_types=explode('|^|*|', $array[1]);
                                
                                $index=-1;
                                for($y =0; $y < sizeof($pictures); $y++)
                                {
                                    if($pictures[$y]==$text[$x][1])
                                        $index=$y;
                                }
                                
                                if($index!=-1)
                                {
                                    $text[$x][]="https://s3.amazonaws.com/bucket_name/users/".$text[$x][2]."/thumbs/".$pictures[$index].".".$image_types[$index];
                                }
                            }
                        }
                        else if($text[$x][0]=="video_like"||$text[$x][0]=="video_comment"||$text[$x][0]=="video_comment_like")
                        {
                            $query2=mysql_query("SELECT video_ids, videos, video_types FROM content WHERE user_id=".$text[$x][1]." LIMIT 1");
                            if($query2&&mysql_num_rows($query2)==1)
                            {
                                $array2=mysql_fetch_row($query2);
                                $video_ids=explode('|^|*|', $array2[0]);
                                $videos=explode('|^|*|', $array2[1]);
                                $video_types=explode('|^|*|', $array2[2]);
                                
                                $index=-1;
                                for($y = 0; $y < sizeof ($video_ids); $y++)
                                {
                                    if($video_ids[$y]==$text[$x][2])
                                        $index=$y;
                                }
                                
                                if($index!=-1)
                                    $text[$x][]=get_video_preview($videos[$index], $video_types[$index]);
                                else
                                    $text[$x][]="";
                            }
                        }
                        
                        $array_text[]=$text[$x];
                        $array_read[]=$read[$x];
                        $array_alert_ids[]=$alert_ids[$x];
                    }
                }
                
                
                
                $badges=array();
                $profile_pictures=array();
                $timestamp_seconds=array();
                for($x = 0; $x < sizeof($array_text); $x++)
                {
                    //gets names
                    $names[$x]=get_user_name($array_user_ids[$x]);
                    
                    //gets timestamps
                    $temp_timestamp=$array_timestamps[$x];
                    $array_timestamps[$x]=get_time_since($array_timestamps[$x], $timezone);
                    $timestamp_seconds[$x]=get_time_since_seconds($temp_timestamp, $timezone);
                    
                    //gets badges
                    $badges[$x]=get_badges($array_user_ids[$x]);
                    
                    //gets profile pictures
                    $profile_pictures[$x]=get_profile_picture($array_user_ids[$x]);
                }
                

                $JSON=array();
                $JSON['alert_user_ids']=$array_user_ids;
                $JSON['alert_timestamps']=$array_timestamps;
                $JSON['timestamp_seconds']=$timestamp_seconds;
                $JSON['alert_information']=$array_text;
                $JSON['alerts_read']=$array_read;
                $JSON['alert_names']=$names;
                $JSON['alert_ids']=$array_alert_ids;
                $JSON['total_size']=$total_size;
                $JSON['empty']=$empty;
                $JSON['badges']=$badges;
                $JSON['profile_pictures']=$profile_pictures;
                echo json_encode($JSON);
                exit();
            }
            else
            {
                $JSON=array();
                $JSON['alert_user_ids']=array();
                $JSON['alert_timestamps']=array();
                $JSON['timestamp_seconds']=array();
                $JSON['alert_information']=array();
                $JSON['alerts_read']=array();
                $JSON['alert_names']=array();
                $JSON['alert_ids']=array();
                $JSON['total_size']=0;
                $JSON['empty']=true;
                $JSON['badges']=array();
                $JSON['profile_pictures']=array();
                echo json_encode($JSON);
                exit();
            }
        }
    }
}

//sets specified alert as read
else if($num==2)
{
    $alert_id=(int)($_POST['alert_id']);

    $query=mysql_query("SELECT alert_ids, alerts_read, new_alerts FROM alerts WHERE user_id=$_SESSION[id] LIMIT 1");
    if($query&&mysql_num_rows($query)==1)
    {
        $array=mysql_fetch_row($query);

        $alert_ids=explode('|^|*|', $array[0]);
        $alerts_read=explode('|^|*|', $array[1]);
        $new_alerts=$array[2];

        $index=-1;
        for($x = 0; $x < sizeof($alert_ids); $x++)
        {
            if($alert_ids[$x]==$alert_id)
                $index=$x;
        }

        if($index!=-1)
        {
            if($alerts_read[$index]==0)
            {
                $alerts_read[$index]=1;
                $new_alerts--;

                $alerts_read=implode('|^|*|', $alerts_read);

                $query=mysql_query("UPDATE alerts SET alerts_read='$alerts_read', new_alerts=$new_alerts WHERE user_id=$_SESSION[id]");
            }
        }
    }
}

//deletes all alerts
else if($num==3)
{
      $query=mysql_query("UPDATE page_alerts SET alert_ids='', alert_user_ids='', alert_timestamps='', alert_information='', alerts_read='', new_alerts='' WHERE user_id=$_SESSION[id] LIMIT 1");
      if($query)
         echo "Alerts deleted";
      else
         echo "Something went wrong";
}