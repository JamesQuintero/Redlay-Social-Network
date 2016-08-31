<?php
@include('init.php');
include('universal_functions.php');
$allowed="all";
include('security_checks.php');

$num=(int)($_POST['num']);

//gets the pages liked
if($num==1)
{
    $ID=(int)($_POST['user_id']);

    if(is_id($ID)&&user_id_exists($ID)&&!user_id_terminated($ID))
    {
        $privacy=get_user_privacy_settings($ID);
        if($privacy[1][6]=='yes'||(isset($_SESSION['id'])&&$ID==$_SESSION['id'])||(isset($_SESSION['id'])&&user_is_friends($ID, $_SESSION['id'])))
        {
                $query=mysql_query("SELECT page_likes FROM user_data WHERE user_id=$ID LIMIT 1");
                if($query&&mysql_num_rows($query)==1)
                {
                    $array=mysql_fetch_row($query);
                    $pages=explode('|^|*|', $array[0]);

                    $query=mysql_query("SELECT page_likes FROM user_data WHERE user_id=$_SESSION[id] LIMIT 1");
                    if($query&&mysql_num_rows($query)==1)
                    {
                        $array=mysql_fetch_row($query);
                        $page_likes=explode('|^|*|', $array[0]);

                        $has_liked=array();
                        for($x = 0; $x < sizeof($pages); $x++)
                        {
                            //gets profile pictures
                            $profile_pictures[$x]=get_page_profile_picture($pages[$x]);

                            if(in_array($pages[$x], $page_likes))
                                $has_liked[$x]=true;
                            else
                                $has_liked[$x]=false;

                            $query=mysql_query("SELECT likes, name FROM page_data WHERE page_id=$pages[$x] LIMIT 1");
                            if($query&&mysql_num_rows($query)==1)
                            {
                                $array=mysql_fetch_row($query);
                                $names[$x]=$array[1];
                                $num_likes[$x]=$array[0];
                            }
                        }

                        $JSON['pages']=$pages;
                        $JSON['page_names']=$names;
                        $JSON['num_likes']=$num_likes;
                        $JSON['has_liked']=$has_liked;
                        $JSON['profile_pictures']=$profile_pictures;
                        echo json_encode($JSON);
                        exit();
                    }
                }
                else
                    log_error("profile_query.php: (1): ",mysql_error());
        }
    }
}

//gets user's videos
else if($num==2)
{
    $page=(int)($_POST['page'])*10;
    $ID=(int)($_POST['user_id']);
    
    if(is_id($ID)&&user_id_exists($ID)&&!user_id_terminated($ID))
    {
        $privacy=get_user_privacy_settings($ID);
        if($privacy[1][4]=='yes'||(isset($_SESSION['id'])&&$ID==$_SESSION['id'])||(isset($_SESSION['id'])&&user_is_friends($ID, $_SESSION['id'])))
        {
                $query=mysql_query("SELECT video_ids, videos, video_types, video_timestamps FROM content WHERE user_id=$ID LIMIT 1");
                if($query&&mysql_num_rows($query)==1)
                {
                    $array=mysql_fetch_row($query);
                    $video_ids=explode('|^|*|', $array[0]);
                    $videos=explode('|^|*|', $array[1]);
                    $video_types=explode('|^|*|', $array[2]);
                    $video_timestamps=explode('|^|*|', $array[3]);


                    $total_size=sizeof($videos);

                    if($total_size<10)
                    {
                        $empty=true;

                        //reverses because it adds backwards in the else statement below
                        $temp_video_ids=array();
                        $temp_videos=array();
                        $temp_video_types=array();
                        $temp_video_timestamps=array();

                        for($x = sizeof($video_ids)-1; $x >=0; $x--)
                        {
                            $temp_video_ids[]=$video_ids[$x];
                            $temp_videos[]=$videos[$x];
                            $temp_video_types[]=$video_types[$x];
                            $temp_video_timestamps[]=$video_timestamps[$x];
                        }

                        $video_ids=$temp_video_ids;
                        $videos=$temp_videos;
                        $video_types=$temp_video_types;
                        $video_timestamps=$temp_video_timestamps;

                    }
                    else
                    {
                        if($total_size-$page<=0)
                            $empty=true;
                        else
                            $empty=false;

                        $temp_post_ids=array();

                        $temp_video_ids=array();
                        $temp_videos=array();
                        $temp_video_types=array();
                        $temp_video_timestamps=array();

                        if($page==10)
                            $index=sizeof($video_ids)-$page+10-1;
                        else
                            $index=sizeof($video_ids)-$page+10-2;

                        while(sizeof($temp_video_ids)<=10)
                        {
                            if($video_ids[$index]!='')
                            {
                                $temp_video_ids[]=$video_ids[$index];
                                $temp_videos[]=$videos[$index];
                                $temp_video_types[]=$video_types[$index];
                                $temp_video_timestamps[]=$video_timestamps[$index];
                            }
                            else
                            {
                                $temp_video_ids[]='';
                                $temp_videos[]='';
                                $temp_video_types[]='';
                                $temp_video_timestamps[]='';
                            }

                            $index--;
                        }


                        $video_ids=$temp_video_ids;
                        $videos=$temp_videos;
                        $video_types=$temp_video_types;
                        $video_timestamps=$temp_video_timestamps;
                    }



                    //gets video previews and embeds
                    $video_previews=array();
                    $video_embeds=array();
                    for($x = 0 ;$x < sizeof($videos); $x++)
                    {
                        $video_previews[$x]=get_video_preview($videos[$x], $video_types[$x]);
                        $video_embeds[$x]=convert_video($videos[$x], $video_types[$x]);
                    }

                    $JSON=array();
                    $JSON['video_ids']=$video_ids;
                    $JSON['video_previews']=$video_previews;
                    $JSON['video_embeds']=$video_embeds;
                    $JSON['videos']=$videos;
                    $JSON['empty']=$empty;
                    $JSON['total_size']=$total_size;
                    echo json_encode($JSON);
                    exit();
                }
        }
    }
}

//gets most popular photo and post
else if($num==3)
{
    $ID=(int)($_POST['user_id']);

    if(is_id($ID)&&user_id_exists($ID)&&!user_id_terminated($ID))
    {

        $user_is_friends=user_is_friends($ID, $_SESSION['id']);

        $privacy=get_user_privacy_settings($ID);
        if($privacy[1][3]=='yes'||(isset($_SESSION['id'])&&$ID==$_SESSION['id'])||$user_is_friends=='true')
        {
            $query=mysql_query("SELECT pictures, picture_likes, picture_dislikes, picture_comments, picture_descriptions, timestamp FROM pictures WHERE user_id=$ID LIMIT 1");
            if($query&&mysql_num_rows($query)==1)
            {
                $array=mysql_fetch_row($query);
                $pictures=explode('|^|*|', $array[0]);
                $likes=explode('|^|*|', $array[1]);
                $dislikes=explode('|^|*|', $array[2]);
                $comments=explode('|^|*|', $array[3]);
                $descriptions=explode('|^|*|', str_replace("'", "\'", $array[4]));
                $timestamps=explode('|^|*|', $array[5]);

                for($x = 0; $x < sizeof($pictures); $x++)
                {
                    $likes[$x]=explode('|%|&|', $likes[$x]);
                    $dislikes[$x]=explode('|%|&|', $dislikes[$x]);
                    $comments[$x]=explode('|%|&|', $comments[$x]);
                }

                
                $high=0;
                $num_likes=0;
                $num_dislikes=0;
                $num_comments=0;

                for($x = 0; $x < sizeof($pictures); $x++)
                {
                    //gets the number of likes
                    if($likes[$x][0]!='')
                        $number=sizeof($likes[$x]);
                    else
                        $number=0;

                    //gets the number of dislikes
                    if($dislikes[$x][0]!='')
                        $number2=sizeof($dislikes[$x]);
                    else
                        $number2=0;

                    //gets the number of comments
                    if($comments[$x][0]!='')
                        $number3=sizeof($comments[$x]);
                    else
                        $number3=0;


                    //gets the picture with most number of things
                    if(($number+$number2+$number3)>$high)
                    {
                        $high=$number+$number2+$number3;
                        $picture=$pictures[$x];
                        $picture_index=$x;
                        $num_likes=$number;
                        $num_dislikes=$number2;
                        $num_comments=$number3;
                    }
                }

                $picture_total=array();
                $picture_total[0]=$num_likes;
                $picture_total[1]=$num_dislikes;
                $picture_total[2]=$num_comments;
                $picture_total[3]=$descriptions[$picture_index];
                $picture_total[4]=$timestamps[$picture_index];

                $query=mysql_query("SELECT image_types FROM pictures WHERE user_id=$ID LIMIT 1");
                if($query&&mysql_num_rows($query)==1)
                {
                    $array=mysql_fetch_row($query);
                    $image_types=explode('|^|*|', $array[0]);
                }

                $JSON=array();
                $JSON['picture_id']=$picture;
                $JSON['picture_total']=$picture_total;
                $JSON['profile_picture']="https://s3.amazonaws.com/bucket_name/users/$ID/photos/0.$image_types[0]";
            }
            else
            {
                $JSON=array();
                $JSON['picture_id']='';
                $JSON['picture_total']=array();
                $JSON['profile_picture']='';
            }
        }


        if($privacy[1][2]=='yes'||(isset($_SESSION['id'])&&$ID==$_SESSION['id'])||$user_is_friends=='true')
        {
            $query=mysql_query("SELECT post_id, updates, user_id_posted, comments, likes, dislikes, timestamp FROM updates WHERE user_id=$ID LIMIT 1");
            if($query&&mysql_num_rows($query)==1)
            {
                $array=mysql_fetch_row($query);

                $post_ids=explode('|^|*|', $array[0]);
                $posts=explode('|^|*|', $array[1]);
                $user_ids_posted=explode('|^|*|', $array[2]);
                $comments=explode('|^|*|', $array[3]);
                $likes=explode('|^|*|', $array[4]);
                $dislikes=explode('|^|*|', $array[5]);
                $timestamps=explode('|^|*|', $array[6]);

                for($x = 0; $x < sizeof($user_ids_posted); $x++)
                {
                    $comments[$x]=explode('|%|&|', $comments[$x]);
                    $likes[$x]=explode('|%|&|', $likes[$x]);
                    $dislikes[$x]=explode('|%|&|', $dislikes[$x]);
                }

                $post_id=0;
                $high=0;
                $num_likes=0;
                $num_dislikes=0;
                $num_comments=0;
                $post_index=0;
                for($x = 0; $x < sizeof($user_ids_posted); $x++)
                {
                    if($likes[$x][0]!='')
                        $number=sizeof($likes[$x]);
                    else
                        $number=0;
                    if($dislikes[$x][0]!='')
                        $number2=sizeof($dislikes[$x]);
                    else
                        $number2=0;
                    if($comments[$x][0]!='')
                        $number3=sizeof($comments[$x]);
                    else
                        $number3=0;

                    if(($number+$number2+$number3)>$high&&$user_ids_posted[$x]==$ID)
                    {
                        $high=$number+$number2+$number3;
                        $post_id=$post_ids[$x];
                        $num_likes=$number;
                        $num_dislikes=$number2;
                        $num_comments=$number3;
                        $post_index=$x;
                    }
                }

                $post_total=array();
                $post_total[0]=$post_id;
                $post_total[1]=$num_likes;
                $post_total[2]=$num_dislikes;
                $post_total[3]=$num_comments;
                $post_total[4]=$timestamps[$post_index];
                $post_total[5]=$posts[$post_index];
                $post_total[6]=get_user_name($user_ids_posted[$post_index]);

                $query=mysql_query("SELECT image_types FROM pictures WHERE user_id=$ID LIMIT 1");
                if($query&&mysql_num_rows($query)==1)
                {
                    $array=mysql_fetch_row($query);
                    $image_types=explode('|^|*|', $array[0]);
                }

                $JSON['post_total']=$post_total;
                $JSON['profile_picture']="https://s3.amazonaws.com/bucket_name/users/$ID/photos/0.$image_types[0]";
            }
        }
        else
            $JSON['post_total']=array();

        echo json_encode($JSON);
        exit();
    }
}

//gets the user's posts
else if($num==4)
{
    $ID=(int)($_POST['page_id']);
//    $ID=1;
    if(is_id($ID)&&page_id_exists($ID)&&!page_id_terminated($ID))
    {
        $page=(int)($_POST['page'])*10;
        $month=clean_string($_POST['month']);
        $year=clean_string($_POST['year']);
        $phrase=clean_string($_POST['phrase']);
        $timezone=(int)($_POST['timezone']);
        $sort=(int)($_POST['sort']);

//        $page=10;
//        $month="all";
//        $year="all";
//        $phrase="none";
//        $timezone=8;
//        $sort=1;

        
        if(is_valid_month($month)||$month=="all")
        {

            $query=mysql_query("SELECT post_ids, posts, comments, likes, dislikes, timestamps, comments_user_id, comment_ids, comment_likes, comment_dislikes, comment_like_types, comment_dislike_types, comment_user_types, comment_timestamps FROM page_content WHERE page_id=$ID LIMIT 1");
            if($query&&mysql_num_rows($query)==1)
            {
                $array=mysql_fetch_row($query);
                $post_ids=explode('|^|*|', $array[0]);
                $posts=explode('|^|*|', $array[1]);
                $comments=explode('|^|*|', $array[2]);
                $likes=explode('|^|*|', $array[3]);
                $dislikes=explode('|^|*|', $array[4]);
                $timestamps=explode('|^|*|', $array[5]);
                $comments_users_sent=explode('|^|*|', $array[6]);
                $comment_ids=explode('|^|*|', $array[7]);
                $comment_likes=explode('|^|*|', $array[8]);
                $comment_dislikes=explode('|^|*|', $array[9]);
                $comment_like_types=explode('|^|*|', $array[10]);
                $comment_dislike_types=explode('|^|*|', $array[11]);
                $comment_user_types=explode('|^|*|', $array[12]);
                $comment_timestamps=explode('|^|*|', $array[13]);



                //gets rid of terminated accounts
                $changed=false;
                $temp_post_ids=array();
                $temp_audiences=array();
                $temp_posts=array();
                $temp_user_ids_posted=array();
                $temp_comments=array();
                $temp_likes=array();
                $temp_dislikes=array();
                $temp_timestamps=array();
                $temp_comment_ids=array();
                $temp_comment_likes=array();
                $temp_comment_dislikes=array();
                $temp_comment_timestamps=array();
                $temp_comment_like_types=array();
                $temp_comment_dislike_types=array();
                $temp_comment_user_types=array();
                for($x = 0;  $x < sizeof($post_ids); $x++)
                {
                        $audiences[$x]=explode('|%|&|', $audiences[$x]);

                        $temp_post_ids[]=$post_ids[$x];
                        $temp_audiences[]=$audiences[$x];
                        $temp_posts[]=$posts[$x];
                        $temp_user_ids_posted[]=$user_ids_posted[$x];
                        $temp_timestamps[]=$timestamps[$x];

                        //gets rid of likes from terminated accounts
                        $likes[$x]=explode('|%|&|', $likes[$x]);
                        $temptemp_likes=array();
                        if($likes[$x][0]!='0'&&$likes[$x][0]!='')
                        {
                            for($y = 0; $y < sizeof($likes[$x]); $y++)
                            {
                                if($likes[$x][$y]!='0'&&$likes[$x][$y]!=''&&!user_id_terminated($likes[$x][$y]))
                                    $temptemp_likes[]=$likes[$x][$y];
                                else if($likes[$x][$y]!='0'&&$likes[$x][$y]!='')
                                    $changed=true;
                            }
                        }
                        if($temptemp_likes[0]==NULL)
                            $temptemp_likes=0;
                        $temp_likes[]=$temptemp_likes;

                        //gets rid of dislikes from terminated accounts
                        $dislikes[$x]=explode('|%|&|', $dislikes[$x]);
                        $temptemp_dislikes=array();
                        if($dislikes[$x][0]!=''&&$dislikes[$x][0]!='0')
                        {
                            for($y = 0; $y < sizeof($dislikes[$x]); $y++)
                            {
                                if($dislikes[$x][$y]!='0'&&$dislikes[$x][$y]!=''&&!user_id_terminated($dislikes[$x][$y]))
                                    $temptemp_dislikes[]=$dislikes[$x][$y];
                                else if($dislikes[$x][$y]!='0'&&$dislikes[$x][$y]!='')
                                    $changed=true;
                            }
                        }
                        if($temptemp_dislikes[0]==NULL)
                            $temptemp_dislikes=0;
                        $temp_dislikes[]=$temptemp_dislikes;

                        //explodes comment stuff
                        $comments[$x]=explode('|%|&|', $comments[$x]);
                        $comments_users_sent[$x]=explode('|%|&|', $comments_users_sent[$x]);
                        $comment_ids[$x]=explode('|%|&|', $comment_ids[$x]);
                        $comment_likes[$x]=explode('|%|&|', $comment_likes[$x]);
                        $comment_dislikes[$x]=explode('|%|&|', $comment_dislikes[$x]);
                        $comment_timestamps[$x]=explode('|%|&|', $comment_timestamps[$x]);
                        $comment_like_types[$x]=explode('|%|&|', $comment_like_types[$x]);
                        $comment_dislike_types[$x]=explode('|%|&|', $comment_dislike_types[$x]);
                        $comment_user_types[$x]=explode('|%|&|', $comment_user_types[$x]);

                        //gets rid of comments delete accounts
                        $temptemp_comments=array();
                        $temptemp_comments_users_sent=array();
                        $temptemp_comment_ids=array();
                        $temptemp_comment_likes=array();
                        $temptemp_comment_dislikes=array();
                        $temptemp_comment_timestamps=array();
                        $temptemp_comment_like_types=array();
                        $temptemp_comment_dislike_types=array();
                        $temptemp_comment_user_types=array();
                        for($y = 0; $y < sizeof($comments[$x]); $y++)
                        {
                            if($comments_users_sent[$x][$y]!=''&&!user_id_terminated($comments_users_sent[$x][$y]))
                            {
                                $comment_likes[$x][$y]=explode('|@|$|', $comment_likes[$x][$y]);
                                $comment_dislikes[$x][$y]=explode('|@|$|', $comment_dislikes[$x][$y]);
                                $comment_like_types[$x][$y]=explode('|@|$|', $comment_like_types[$x][$y]);
                                $comment_dislike_types[$x][$y]=explode('|@|$|', $comment_dislike_types[$x][$y]);
                                

                                $temptemp_comments[]=$comments[$x][$y];
                                $temptemp_comments_users_sent[]=$comments_users_sent[$x][$y];
                                $temptemp_comment_ids[]=$comment_ids[$x][$y];
                                $temptemp_comment_likes[]=$comment_likes[$x][$y];
                                $temptemp_comment_dislikes[]=$comment_dislikes[$x][$y];
                                $temptemp_comment_timestamps[]=$comment_timestamps[$x][$y];
                                
                                $temptemp_comment_like_types[]=$comment_like_types[$x][$y];
                                $temptemp_comment_dislike_types[]=$comment_dislike_types[$x][$y];
                                $temptemp_comment_user_types[]=$comment_user_types[$x][$y];
                            }
                            else if($comments_users_sent[$x][$y]!='')
                                $changed=true;
                        }
//                                        
                        if($temptemp_comments[0]!=NULL)
                        {
                            $temp_comments[]=$temptemp_comments;
                            $temp_comments_users_sent[]=$temptemp_comments_users_sent;
                            $temp_comment_ids[]=$temptemp_comment_ids;
                            $temp_comment_likes[]=$temptemp_comment_likes;
                            $temp_comment_dislikes[]=$temptemp_comment_dislikes;
                            $temp_comment_timestamps[]=$temptemp_comment_timestamps;
                            $temp_comment_like_types[]=$temptemp_comment_like_types;
                            $temp_comment_dislike_types[]=$temptemp_comment_dislike_types;
                            $temp_comment_user_types[]=$temptemp_comment_user_types;
                        }
                        else
                        {
                            $temp_comments[]=array();
                            $temp_comments_users_sent[]=array();
                            $temp_comment_ids[]=array();
                            $temp_comment_likes[]=array();
                            $temp_comment_dislikes[]=array();
                            $temp_comment_timestamps[]=array();
                            $temp_comment_like_types[]=array();
                            $temp_comment_dislike_types[]=array();
                            $temp_comment_user_types[]=array();
                        }
                }

                if($changed)
                {
                    $post_ids=$temp_post_ids;
                    $audiences=$temp_audiences;
                    $posts=$temp_posts;
                    $user_ids_posted=$temp_user_ids_posted;
                    $comments=$temp_comments;
                    $comments_users_sent=$temp_comments_users_sent;
                    $comment_ids=$temp_comment_ids;
                    $comment_likes=$temp_comment_likes;
                    $comment_dislikes=$temp_comment_dislikes;
                    $comment_timestamps=$temp_comment_timestamps;
                    $likes=$temp_likes;
                    $dislikes=$temp_dislikes;
                    $timestamps=$temp_timestamps;
                    $comment_like_types=$temp_comment_like_types;
                    $comment_dislike_types=$temp_comment_dislike_types;
                    $comment_user_types=$temp_comment_user_types;

//                    for($x =0; $x < sizeof($temp_audiences); $x++)
//                        $temp_audiences[$x]=implode('|%|&|', $temp_audiences[$x]);
//
//                    for($x = 0; $x < sizeof($temp_comments); $x++)
//                    {
//                        for($y = 0; $y < sizeof($temp_comment_likes[$x]); $y++)
//                        {
//                            $temp_comment_likes[$x][$y]=implode('|@|$|', $temp_comment_likes[$x][$y]);
//                            $temp_comment_dislikes[$x][$y]=implode('|@|$|', $temp_comment_dislikes[$x][$y]);
//                        }
//                        $temp_comments[$x]=implode('|%|&|', $temp_comments[$x]);
//                        $temp_comments_users_sent[$x]=implode('|%|&|', $temp_comments_users_sent[$x]);
//                        $temp_comment_ids[$x]=implode('|%|&|', $temp_comment_ids[$x]);
//                        $temp_comment_likes[$x]=implode('|%|&|', $temp_comment_likes[$x]);
//                        $temp_comment_dislikes[$x]=implode('|%|&|', $temp_comment_dislikes[$x]);
//                        $temp_comment_timestamps[$x]=implode('|%|&|', $temp_comment_timestamps[$x]);
//                    }
//
//                    $temp_comments=implode('|^|*|', $temp_comments);
//                    $temp_comments_users_sent=implode('|^|*|', $temp_comments_users_sent);
//                    $temp_comment_ids=implode('|^|*|', $temp_comment_ids);
//                    $temp_comment_likes=implode('|^|*|', $temp_comment_likes);
//                    $temp_comment_dislikes=implode('|^|*|', $temp_comment_dislikes);
//                    $temp_comment_timestamps=implode('|^|*|', $temp_comment_timestamps);
//
//                    //implodes everything for saving in database
//                    $temp_post_ids=implode('|^|*|', $temp_post_ids);
//                    $temp_audiences=implode('|^|*|', $temp_audiences);
//                    $temp_posts=implode('|^|*|', $temp_posts);
//                    $temp_user_ids_posted=implode('|^|*|', $temp_user_ids_posted);
//                    $temp_timestamps=implode('|^|*|', $temp_timestamps);
//
//                    //implodes likes
//                    $temp_likes=$likes;
//                    for($x = 0; $x < sizeof($temp_likes); $x++)
//                        $temp_likes[$x]=implode('|%|&|', $temp_likes[$x]);
//                    $temp_likes=implode('|^|*|', $temp_likes);
//
//                    //implodes dislikes
//                    $temp_dislikes=$dislikes;
//                    for($x = 0; $x < sizeof($temp_dislikes); $x++)
//                        $temp_dislikes[$x]=implode('|%|&|', $temp_dislikes[$x]);
//                    $temp_dislikes=implode('|^|*|', $temp_dislikes);
//
//
//                    $query=mysql_query("UPDATE content SET post_ids='$temp_post_ids', post_groups='$temp_audiences', posts='".mysql_escape_string($temp_posts)."', user_ids_posted='$temp_user_ids_posted', likes='$temp_likes', dislikes='$temp_dislikes', timestamps='$temp_timestamps', comments='$temp_comments', comment_ids='$temp_comment_ids', comments_user_id='$temp_comments_users_sent', comment_likes='$temp_comment_likes', comment_dislikes='$temp_comment_dislikes', comment_timestamps='$temp_comment_timestamps' WHERE user_id=$ID");
//                    if(!$query)
//                        log_error("profile_query.php: (4:2): ", mysql_error());
                }



                $temp_post_ids=array();
                $temp_posts=array();
                $temp_user_ids_posted=array();
                $temp_comments=array();
                $temp_comments_users_sent=array();
                $temp_comment_ids=array();
                $temp_comment_likes=array();
                $temp_comment_dislikes=array();
                $temp_comment_timestamps=array();
                $temp_likes=array();
                $temp_dislikes=array();
                $temp_timestamps=array();
                $temp_adjusted_timestamps=array();
                $temp_timestamp_seconds=array();
                $temp_time_since=array();
                $temp_comment_like_types=array();
                $temp_comment_dislike_types=array();
                $temp_comment_user_types=array();

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
                    if((  $month=="all"||$temp_timestamps[$x][0]==$month  )&&(  $year=="all"||$temp_timestamps[$x][2]==$year  )&&(  $phrase=='none'||strpos($posts[$x], $phrase)!==false  ))
                    {
                        $temp_post_ids[]=$post_ids[$x];
                        $temp_posts[]=$posts[$x];
                        $temp_user_ids_posted[]=$user_ids_posted[$x];
                        $temp_comments[]=$comments[$x];
                        $temp_comment_ids[]=$comment_ids[$x];
                        $temp_comments_users_sent[]=$comments_users_sent[$x];
                        $temp_comment_likes[]=$comment_likes[$x];
                        $temp_comment_dislikes[]=$comment_dislikes[$x];
                        $temp_comment_timestamps[]=$comment_timestamps[$x];
                        $temp_likes[]=$likes[$x];
                        $temp_dislikes[]=$dislikes[$x];
                        $temp_timestamps[]=$timestamps[$x];
                        $temp_adjusted_timestamps[]=$adjusted_timestamps[$x];
                        $temp_time_since[]=$time_since[$x];
                        $temp_timestamp_seconds[]=$timestamp_seconds[$x];
                        $temp_comment_like_types[]=$comment_like_types[$x];
                        $temp_comment_dislike_types[]=$comment_dislike_types[$x];
                        $temp_comment_user_types[]=$comment_user_types[$x];
                    }
                }

                $post_ids=$temp_post_ids;
                $posts=$temp_posts;
                $user_ids_posted=$temp_user_ids_posted;
                $comments=$temp_comments;
                $comment_ids=$temp_comment_ids;
                $comments_users_sent=$temp_comments_users_sent;
                $comment_likes=$temp_comment_likes;
                $comment_dislikes=$temp_comment_dislikes;
                $comment_timestamps=$temp_comment_timestamps;
                $likes=$temp_likes;
                $dislikes=$temp_dislikes;
                $timestamps=$temp_timestamps;
                $time_since=$temp_time_since;
                $timestamp_seconds=$temp_timestamp_seconds;
                $comment_like_types=$temp_comment_like_types;
                $comment_dislike_types=$temp_comment_dislike_types;
                $comment_user_types=$temp_comment_user_types;


//                    print_r($posts);
//                    print_r($post_ids);


                //sorts from oldest to newest
                //if sort != 2, sorts default (newest to oldest)
                if($sort==2)
                {
                    $temp_post_ids=array();
                    $temp_posts=array();
                    $temp_user_ids_posted=array();
                    $temp_comments=array();
                    $temp_comment_ids=array();
                    $temp_comments_users_sent=array();
                    $temp_comment_likes=array();
                    $temp_comment_dislikes=array();
                    $temp_comment_timestamps=array();
                    $temp_likes=array();
                    $temp_dislikes=array();
                    $temp_timestamps=array();
                    $temp_time_since=array();
                    $temp_timestamp_seconds=array();
                    $temp_comment_like_types=array();
                    $temp_comment_dislike_types=array();
                    $temp_comment_user_types=array();

                    for($x = sizeof($post_ids)-1; $x >= 0; $x--)
                    {
                        $temp_post_ids[]=$post_ids[$x];
                        $temp_posts[]=$posts[$x];
                        $temp_user_ids_posted[]=$user_ids_posted[$x];
                        $temp_comments[]=$comments[$x];
                        $temp_comment_ids[]=$comment_ids[$x];
                        $temp_comments_users_sent[]=$comments_users_sent[$x];
                        $temp_comment_likes[]=$comment_likes[$x];
                        $temp_comment_dislikes[]=$comment_dislikes[$x];
                        $temp_comment_timestamps[]=$comment_timestamps[$x];
                        $temp_likes[]=$likes[$x];
                        $temp_dislikes[]=$dislikes[$x];
                        $temp_timestamps[]=$timestamps[$x];
                        $temp_time_since[]=$time_since[$x];
                        $temp_timestamp_seconds[]=$timestamp_seconds[$x];
                        $temp_comment_like_types[]=$comment_like_types[$x];
                        $temp_comment_dislike_types[]=$comment_dislike_types[$x];
                        $temp_comment_user_types[]=$comment_user_types[$x];
                    }

                    $post_ids=$temp_post_ids;
                    $posts=$temp_posts;
                    $user_ids_posted=$temp_user_ids_posted;
                    $comments=$temp_comments;
                    $comment_ids=$temp_comment_ids;
                    $comments_users_sent=$temp_comments_users_sent;
                    $comment_likes=$temp_comment_likes;
                    $comment_dislikes=$temp_comment_dislikes;
                    $comment_timestamps=$temp_comment_timestamps;
                    $likes=$temp_likes;
                    $dislikes=$temp_dislikes;
                    $timestamps=$temp_timestamps;
                    $time_since=$temp_time_since;
                    $timestamp_seconds=$temp_timestamp_seconds;
                    $comment_like_types=$temp_comment_like_types;
                    $comment_dislike_types=$temp_comment_dislike_types;
                    $comment_user_types=$temp_comment_user_types;
                }


                if($array[1]!='')
                    $total_size=sizeof($posts);
                else
                    $total_size=0;


                if($total_size<10)
                {
                    $empty=true;

                    //reverses because it adds backwards in the else statement below
                    $temp_post_ids=array();
                    $temp_posts=array();
                    $temp_user_ids_posted=array();
                    $temp_comments=array();
                    $temp_comment_ids=array();
                    $temp_comments_users_sent=array();
                    $temp_comment_likes=array();
                    $temp_comment_dislikes=array();
                    $temp_comment_timestamps=array();
                    $temp_likes=array();
                    $temp_dislikes=array();
                    $temp_timestamps=array();
                    $temp_time_since=array();
                    $temp_timestamp_seconds=array();
                    $temp_comment_like_types=array();
                    $temp_comment_dislike_types=array();
                    $temp_comment_user_types=array();

                    for($x = sizeof($post_ids)-1; $x >=0; $x--)
                    {
                        $temp_post_ids[]=$post_ids[$x];
                        $temp_posts[]=$posts[$x];
                        $temp_user_ids_posted[]=$user_ids_posted[$x];
                        $temp_comments[]=$comments[$x];
                        $temp_comment_ids[]=$comment_ids[$x];
                        $temp_comments_users_sent[]=$comments_users_sent[$x];
                        $temp_comment_likes[]=$comment_likes[$x];
                        $temp_comment_dislikes[]=$comment_dislikes[$x];
                        $temp_comment_timestamps[]=$comment_timestamps[$x];
                        $temp_likes[]=$likes[$x];
                        $temp_dislikes[]=$dislikes[$x];
                        $temp_timestamps[]=$timestamps[$x];
                        $temp_time_since[]=$time_since[$x];
                        $temp_timestamp_seconds[]=$timestamp_seconds[$x];
                        $temp_comment_like_types[]=$comment_like_typesp[$x];
                        $temp_comment_dislike_types[]=$comment_dislike_types[$x];
                        $temp_comment_user_types[]=$comment_user_types[$x];
                    }

                    $post_ids=$temp_post_ids;
                    $posts=$temp_posts;
                    $user_ids_posted=$temp_user_ids_posted;
                    $comments=$temp_comments;
                    $comment_ids=$temp_comment_ids;
                    $comments_users_sent=$temp_comments_users_sent;
                    $comment_likes=$temp_comment_likes;
                    $comment_dislikes=$temp_comment_dislikes;
                    $comment_timestamps=$temp_comment_timestamps;
                    $likes=$temp_likes;
                    $dislikes=$temp_dislikes;
                    $timestamps=$temp_timestamps;
                    $time_since=$temp_time_since;
                    $timestamp_seconds=$temp_timestamp_seconds;
                    $comment_like_types=$temp_comment_like_types;
                    $comment_dislike_types=$temp_comment_dislike_types;
                    $comment_user_types=$temp_comment_user_types;

                }
                else
                {
                    if($total_size-$page<=0)
                        $empty=true;
                    else
                        $empty=false;

                    $temp_post_ids=array();
                    $temp_posts=array();
                    $temp_user_ids_posted=array();
                    $temp_comments=array();
                    $temp_comment_ids=array();
                    $temp_comments_users_sent=array();
                    $temp_comment_likes=array();
                    $temp_comment_dislikes=array();
                    $temp_comment_timestamps=array();
                    $temp_likes=array();
                    $temp_dislikes=array();
                    $temp_timestamps=array();
                    $temp_time_since=array();
                    $temp_timestamp_seconds=array();
                    $temp_comment_like_types=array();
                    $temp_comment_dislike_types=array();
                    $temp_comment_user_types=array();

                    if($page==10)
                        $index=sizeof($post_ids)-$page+10-1;
                    else
                        $index=sizeof($post_ids)-$page+10-2;

                    while(sizeof($temp_post_ids)<=10)
                    {
                        if($posts[$index]!='')
                        {
                            $temp_post_ids[]=$post_ids[$index];
                            $temp_posts[]=$posts[$index];
                            $temp_user_ids_posted[]=$user_ids_posted[$index];
                            $temp_comments[]=$comments[$index];
                            $temp_comment_ids[]=$comment_ids[$index];
                            $temp_comments_users_sent[]=$comments_users_sent[$index];
                            $temp_comment_likes[]=$comment_likes[$index];
                            $temp_comment_dislikes[]=$comment_dislikes[$index];
                            $temp_comment_timestamps[]=$comment_timestamps[$index];
                            $temp_likes[]=$likes[$index];
                            $temp_dislikes[]=$dislikes[$index];
                            $temp_timestamps[]=$timestamps[$index];
                            $temp_time_since[]=$time_since[$index];
                            $temp_timestamp_seconds[]=$timestamp_seconds[$index];
                            $temp_comment_like_types[]=$comment_like_types[$index];
                            $temp_comment_dislike_types[]=$temp_comment_dislike_types[$index];
                            $temp_comment_user_types[]=$comment_user_types[$index];
                        }
                        else
                        {
                            $temp_post_ids[]='';
                            $temp_posts[]='';
                            $temp_user_ids_posted[]='';
                            $temp_comments[]='';
                            $temp_comment_ids[]='';
                            $temp_comments_users_sent[]='';
                            $temp_comment_likes[]='';
                            $temp_comment_dislikes[]='';
                            $temp_comment_timestamps[]='';
                            $temp_likes[]='';
                            $temp_dislikes[]='';
                            $temp_timestamps[]='';
                            $temp_time_since[]='';
                            $temp_timestamp_seconds[]='';
                            $temp_comment_like_types[]='';
                            $temp_comment_dislike_types[]='';
                            $temp_comment_user_types[]='';
                        }

                        $index--;
                    }

                    $post_ids=$temp_post_ids;
                    $posts=$temp_posts;
                    $user_ids_posted=$temp_user_ids_posted;
                    $comments=$temp_comments;
                    $comment_ids=$temp_comment_ids;
                    $comments_users_sent=$temp_comments_users_sent;
                    $comment_likes=$temp_comment_likes;
                    $comment_dislikes=$temp_comment_dislikes;
                    $comment_timestamps=$temp_comment_timestamps;
                    $likes=$temp_likes;
                    $dislikes=$temp_dislikes;
                    $timestamps=$temp_timestamps;
                    $time_since=$temp_time_since;
                    $timestamp_seconds=$temp_timestamp_seconds;
                    $comment_like_types=$temp_comment_like_types;
                    $comment_dislike_types=$temp_comment_dislike_types;
                    $comment_user_types=$temp_comment_user_types;
                }


                //gets any extra stuff like names and images

                $num_likes=array();
                $num_dislikes=array();
                $has_liked=array();
                $has_disliked=array();
                $num_comments=array();

                $names=array();
                $profile_pictures=array();

                $temp_images=array();
                $badges=array();
                $comment_names=array();
                $comment_profile_pictures=array();
                $comment_badges=array();
                $comment_timestamp_seconds=array();
                $has_liked_comments=array();
                $has_disliked_comments=array();
                $num_comment_likes=array();
                $num_comment_dislikes=array();
                for($x =0; $x < sizeof($post_ids); $x++)
                {
                    //gets badges
                    $badges[$x]=get_badges($user_ids_posted[$x]);

                    //gets has liked
                    if(isset($_SESSION['id']))
                    {
                        $liked=false;
                        for($y = 0; $y < sizeof($likes[$x]); $y++)
                        {
                            if($likes[$x][$y]==$_SESSION['id'])
                            {
                                $liked=true;
                                $y=sizeof($likes[$x]);
                            }
                        }
                        $has_liked[$x]=$liked;

                        //gets has disliked
                        $disliked=false;
                        for($y = 0; $y < sizeof($dislikes[$x]); $y++)
                        {
                            if($dislikes[$x][$y]==$_SESSION['id'])
                            {
                                $disliked=true;
                                $y=sizeof($dislikes[$x]);
                            }
                        }
                        $has_disliked[$x]=$disliked;
                    }
                    else
                    {
                        $has_liked[$x]=false;
                        $has_disliked[$x]=false;
                    }

                    //gets the number of likes in posts
                    if($likes[$x][0]==''||$likes[$x][0]==0)
                        $num_likes[$x]=0;
                    else
                        $num_likes[$x]=sizeof($likes[$x]);

                    //gets the number of dislikes in posts
                    if($dislikes[$x][0]==''||$dislikes[$x][0]==0)
                        $num_dislikes[$x]=0;
                    else
                        $num_dislikes[$x]=sizeof($dislikes[$x]);

                    //gets names
                    $names[$x]=get_user_name($user_ids_posted[$x]);

                    //gets profile pictures
                    $index=-1;
                    for($y = 0; $y < sizeof($temp_images); $y++)
                    {
                        if($temp_images[$y][0]==$user_ids_posted[$x])
                        {
                            $index=$y;
                            $y=sizeof($temp_images);
                        }
                    }

                    //gets profile pictures
                    $profile_pictures[$x]=get_profile_picture($user_ids_posted[$x]);

                    //gets num comments
                    if($comments[$x][0]=='')
                        $num_comments[$x]=0;
                    else
                        $num_comments[$x]=sizeof($comments[$x]);

                    $has_liked_comments[$x]=array();
                    $has_disliked_comments[$x]=array();
                    for($y = 0; $y < sizeof($comments_users_sent[$x]); $y++)
                    {
                        //gets comment names
                        $comment_names[$x][$y]=get_user_name($comments_users_sent[$x][$y]);

                        //gets comment profile pictures
                        $comment_profile_pictures[$x][$y]=get_profile_picture($comments_users_sent[$x][$y]);

                        //gets comment badges
                        $comment_badges[$x][$y]=get_badges($comments_users_sent[$x][$y]);

                        //gets comment timestamps
                        if($comment_timestamps[$x][$y]!='')
                        {
                            $temp_timestamp=$comment_timestamps[$x][$y];
                            $comment_timestamps[$x][$y]=get_time_since($comment_timestamps[$x][$y], $timezone);
                            $comment_timestamp_seconds[$x][$y]=get_time_since_seconds($temp_timestamp, $timezone);
                        }
                        else
                        {
                            $comment_timestamps[$x][$y]='';
                            $comment_timestamp_seconds[$x][$y]='';
                        }

                        //gets if has liked comments
                        $liked=false;
                        for($z = 0; $z < sizeof($comment_likes[$x][$y]); $z++)
                        {
                            if(isset($_SESSION['id'])&&$comment_likes[$x][$y][$z]==$_SESSION['id'])
                            {
                                $liked=true;
                                $z=sizeof($comment_likes[$x][$y]);
                            }
                        }
                        $has_liked_comments[$x][$y]=$liked;

                        //gets if has disliked comments
                        $disliked=false;
                        for($z = 0; $z < sizeof($comment_dislikes[$x][$y]); $z++)
                        {
                            if(isset($_SESSION['id'])&&$comment_dislikes[$x][$y][$z]==$_SESSION['id'])
                            {
                                $disliked=true;
                                $z=sizeof($comment_dislikes[$x][$y]);
                            }
                        }
                        $has_disliked_comments[$x][$y]=$disliked;

                        //gets num comment likes
                        if($comment_likes[$x][$y][0]!=''&&$comment_likes[$x][$y][0]!='0')
                            $num_comment_likes[$x][$y]=sizeof($comment_likes[$x][$y]);
                        else
                            $num_comment_likes[$x][$y]=0;

                        //gets num comment dislikes
                        if($comment_dislikes[$x][$y][0]!=''&&$comment_dislikes[$x][$y][0]!='0')
                            $num_comment_dislikes[$x][$y]=sizeof($comment_dislikes[$x][$y]);
                        else
                            $num_comment_dislikes[$x][$y]=0;
                    }
                }

//                    print_r($posts);
//                    print_r($post_ids);
//                    echo "Total size: ".$total_size."\n";

//                    print_r($comment_likes);
//                    print_r($has_liked_comments);
//                    print_r($num_comment_likes);
//                    print_r($posts);
//                    print_r($comments);
//                    print_r($comment_timestamps);

                if($total_size>0)
                {
                    $JSON=array();
                    $JSON['names']=$names;
                    $JSON['profile_pictures']=$profile_pictures;
                    $JSON['posts']=$posts;
                    $JSON['post_ids']=$post_ids;
                    $JSON['empty']=$empty;
                    $JSON['total_size']=$total_size;
                    $JSON['timestamps']=$time_since;
                    $JSON['badges']=$badges;
                    $JSON['timestamp_seconds']=$timestamp_seconds;
                    $JSON['num_likes']=$num_likes;
                    $JSON['num_dislikes']=$num_dislikes;
                    $JSON['has_liked']=$has_liked;
                    $JSON['has_disliked']=$has_disliked;
                    $JSON['num_comments']=$num_comments;
                    $JSON['comments']=$comments;
                    $JSON['comments_users_sent']=$comments_users_sent;
                    $JSON['comment_ids']=$comment_ids;
                    $JSON['comment_likes']=$comment_likes;
                    $JSON['comment_dislikes']=$comment_dislikes;
                    $JSON['comment_timestamps']=$comment_timestamps;
                    $JSON['comment_names']=$comment_names;
                    $JSON['comment_profile_pictures']=$comment_profile_pictures;
                    $JSON['comment_badges']=$comment_badges;
                    $JSON['comment_timestamp_seconds']=$comment_timestamp_seconds;
                    $JSON['has_liked_comments']=$has_liked_comments;
                    $JSON['has_disliked_comments']=$has_disliked_comments;
                    $JSON['num_comment_likes']=$num_comment_likes;
                    $JSON['num_comment_dislikes']=$num_comment_dislikes;
                    $JSON['comment_like_types']=$comment_like_types;
                    $JSON['comment_dislike_types']=$comment_dislike_types;
                    $JSON['comment_user_types']=$comment_user_types;
                    echo json_encode($JSON);
                    exit();
                }

            }
            else
            {
                echo "Something went wrong. We are working on fixing it";
                log_error("profile_query.php: (4:1): ",mysql_error());
            }
            
            $JSON=array();
            $JSON['names']=array();
            $JSON['profile_pictures']=array();
            $JSON['posts']=array();
            $JSON['post_ids']=array();
            $JSON['empty']=true;
            $JSON['total_size']=0;
            $JSON['timestamps']=array();
            $JSON['badges']=array();
            $JSON['timestamp_seconds']=array();
            $JSON['num_likes']=array();
            $JSON['num_dislikes']=array();
            $JSON['has_liked']=array();
            $JSON['has_disliked']=array();
            $JSON['num_comments']=array();
            $JSON['comments']=array();
            $JSON['comments_users_sent']=array();
            $JSON['comment_ids']=array();
            $JSON['comment_likes']=array();
            $JSON['comment_dislikes']=array();
            $JSON['comment_timestamps']=array();
            $JSON['comment_names']=array();
            $JSON['comment_profile_pictures']=array();
            $JSON['comment_badges']=array();
            $JSON['comment_timestamp_seconds']=array();
            $JSON['has_liked_comments']=array();
            $JSON['has_disliked_comments']=array();
            $JSON['num_comment_likes']=array();
            $JSON['num_comment_dislikes']=array();
            $JSON['comment_like_types']=array();
            $JSON['comment_dislike_types']=array();
            $JSON['comment_user_types']=array();
            echo json_encode($JSON);
            exit();
        }
    }
}

//gets user's pictures
else if($num==5)
{
    $ID=(int)($_POST['page_id']);
    if(is_id($ID)&&page_id_exists($ID)&&!page_id_terminated($ID))
    {
        $number=(int)($_POST['number']);
        $page=(int)($_POST['page'])*25;

            if($number==1||$number==2)
            {
                $query=mysql_query("SELECT pictures, image_types, timestamp FROM page_pictures WHERE page_id=$ID LIMIT 1");
                if($query&&mysql_num_rows($query)==1)
                {
                    $array=mysql_fetch_row($query);
                    $pictures=explode('|^|*|', $array[0]);
                    $image_types=explode('|^|*|', $array[1]);
                    $image_timestamps=explode('|^|*|', $array[2]);
                    

//                    //gets only the viewable photos
//                    $user_audiences=get_audience_current_user($ID);
//                    $date=get_date();
//                    $image_links=array();
//                    for($x = 0; $x < sizeof($pictures); $x++)
//                    {
//                       $image_audiences[$x]=explode('|%|&|', $image_audiences[$x]);
//                       
//                       if(can_view($user_audiences, $image_audiences[$x])||(isset($_SESSION['id'])&&$ID==$_SESSION['id']))
//                       {
//                          $temp_pictures[]=$pictures[$x];
//
//                          //gets how long it's been
//                          $difference=$date-$image_timestamps[$x];
//                          
//                          //if picture is less than a day old
//                          if($difference<86400)
//                              $image_links[]="https://s3.amazonaws.com/bucket_name/users/$ID/thumbs/$pictures[$x].".$image_types[$x];
//                          else
//                              $image_links[]="http://u.redlay.com/users/$ID/thumbs/$pictures[$x].$image_types[$x]";
//
//                       }
//                    }
//                    $pictures=$temp_pictures;
                    
                    $date=get_date();
                    $image_links=array();
                    for($x = 0; $x < sizeof($pictures); $x++)
                    {
                        //gets how long it's been
                          $difference=$date-$image_timestamps[$x];
                          
                          //if picture is less than a day old
                          if($difference<86400)
                              $image_links[]="https://s3.amazonaws.com/redlay.pages/pages/$ID/thumbs/$pictures[$x].".$image_types[$x];
                          else
                              $image_links[]="http://pages.redlay.com/pages/$ID/thumbs/$pictures[$x].$image_types[$x]";
                    }

                    if($number==1)
                    {
                        $temp_images=array();
                        $temp_image_ids=array();

                        if(sizeof($image_links)>=12)
                        {
                            //gets 12 most current photos for the front of the profile
                            for($x = sizeof($image_links)-1; $x >= sizeof($image_links)-1-12; $x--)
                            {
                                $temp_images[]=$image_links[$x];
                                $temp_image_ids[]=$pictures[$x];
                            }
                        }
                        else if(sizeof($image_links)<12)
                        {
                            //gets all photos for the front of the profile
                            for($x = sizeof($image_links)-1; $x >= 0; $x--)
                            {
                                $temp_images[]=$image_links[$x];
                                $temp_image_ids[]=$pictures[$x];
                            }
                        }

                        $image_links=$temp_images;
                        $pictures=$temp_image_ids;
                    }
                    else
                    {
                        $total_size=sizeof($image_links);

                        if($total_size<25)
                        {
                            $empty=true;

                            //reverses because it adds backwards in the else statement below
                            $temp_image_links=array();
                            $temp_pictures=array();

                            for($x = sizeof($image_links)-1; $x >=0; $x--)
                            {
                                $temp_image_links[]=$image_links[$x];
                                $temp_pictures[]=$pictures[$x];
                            }

                            $image_links=$temp_image_links;
                            $pictures=$temp_pictures;

                        }
                        else
                        {
                            if($total_size-$page<=0)
                                $empty=true;
                            else
                                $empty=false;

                            $temp_image_links=array();
                            $temp_pictures=array();

                            if($page==25)
                                $index=sizeof($image_links)-$page+25-1;
                            else
                                $index=sizeof($image_links)-$page+25-2;

                            while(sizeof($temp_image_links)<=25)
                            {
                                if($pictures[$index]!='')
                                {
                                    $temp_image_links[]=$image_links[$index];
                                    $temp_pictures[]=$pictures[$index];
                                }
                                else
                                {
                                    $temp_image_links[]='';
                                    $temp_pictures[]='';
                                }

                                $index--;
                            }

                            $image_links=$temp_image_links;
                            $pictures=$temp_pictures;
                        }
                    }



                    $JSON=array();
                    $JSON['images']=$image_links;
                    $JSON['image_ids']=$pictures;
                    $JSON['total_size']=$total_size;
                    $JSON['empty']=$empty;
                    echo json_encode($JSON);
                    exit();
                }
            }
    }
}

//gets groups the specified user is in
else if($num==6)
{
    $ID=(int)($_POST['user_id']);

    if(is_id($ID)&&user_id_exists($ID)&&!user_id_terminated($ID))
    {
        $query=mysql_query("SELECT group_defaults FROM data WHERE num=1");
        $query2=mysql_query("SELECT user_friends, audience_groups, audience_group_lists FROM user_data WHERE user_id=$_SESSION[id] LIMIT 1");
        if($query&&mysql_num_rows($query)==1)
        {
            $array=mysql_fetch_row($query);
            $array2=mysql_fetch_row($query2);
            $default_groups=explode('|^|*|', $array[0]);

            $friends=explode('|^|*|', $array2[0]);
            $users_groups=explode('|^|*|', $array2[1]);
            $groups_list=explode('|^|*|', $array2[2]);


            $index=-1;
            for($x = 0; $x < sizeof($friends); $x++)
            {
                if($friends[$x]==$ID)
                    $index=$x;
            }

            $groups=$default_groups;
            if($array2[2]!='')
            {
                for($x = 0; $x < sizeof($groups_list); $x++)
                    $groups[]=$groups_list[$x];
            }

            $JSON=array();
            $JSON['groups_list']=$groups;
            $JSON['user_groups']=explode('|%|&|',$users_groups[$index]);
            echo json_encode($JSON);
            exit();
        }
    }
}

//saves user's new groups
else if($num==7)
{
    $ID=(int)($_POST['user_id']);
    $groups=$_POST['groups'];

    if(is_id($ID)&&user_id_exists($ID)&&!user_id_terminated($ID))
    {
        $bool=true;
        for($x = 0; $x < sizeof($groups); $x++)
        {
            if(!is_valid_audience($groups[$x]))
                $bool=false;
        }
        if($bool)
        {
            $query=mysql_query("SELECT user_friends, audience_groups FROM user_data WHERE user_id=$_SESSION[id] LIMIT 1");
            if($query&&mysql_num_rows($query)==1)
            {
                $array=mysql_fetch_row($query);

                $friends=explode('|^|*|', $array[0]);
                $user_groups=explode('|^|*|', $array[1]);


                for($x = 0; $x < sizeof($friends); $x++)
                {
                    if($friends[$x]==$ID)
                        $user_groups[$x]=implode('|%|&|', $groups);
                }
                $user_groups=implode('|^|*|', $user_groups);

                $query=mysql_query("UPDATE user_data SET audience_groups='$user_groups' WHERE user_id=$_SESSION[id]");
                if($query)
                    echo "User groups modified";
                else
                {
                    echo "Something went wrong. We are working to fix it";
                log_error("profile_query.php", mysql_error());
                }
            }
            else
            {
                echo "Something went wrong. We are working to fix it";
                log_error("profile_query.php", mysql_error());
            }
        }
        else
            echo "Invalid group";
    }
    else
        echo "User ID is invalid";
}

//gets user's documents
//else if($num==8)
//{
//    $ID=(int)($_POST['user_id']);
//
//    $privacy=get_user_privacy_settings($ID);
//    if($privacy[1][5]=='yes'||(isset($_SESSION['id'])&&$ID==$_SESSION['id'])||(isset($_SESSION['id'])&&user_is_friends($ID, $_SESSION['id'])))
//    {
//        if(is_id($ID)&&user_id_exists($ID))
//        {
//            $query=mysql_query("SELECT doc_ids, document_names, file_ext, doc_viewability, doc_audiences, num_downloads FROM user_documents WHERE user_id=$ID LIMIT 1");
//            if($query&&mysql_num_rows($query)==1)
//            {
//                $array=mysql_fetch_row($query);
//                $doc_ids=explode('|^|*|', $array[0]);
//                $document_names=explode('|^|*|', $array[1]);
//                $file_exts=explode('|^|*|', $array[2]);
//                $doc_viewability=explode('|^|*|', $array[3]);
//                $doc_audiences=explode('|^|*|', $array[4]);
//                $num_downloads=explode('|^|*|', $array[5]);
//
//
//                $user_groups=get_audience_current_user($ID);
//
//                if($array[0]!='')
//                {
//                        $temp_doc_ids=array();
//                        $temp_document_names=array();
//                        $temp_file_exts=array();
//                        $temp_num_downloads=array();
//                        $temp_sizes=array();
//                        $temp_doc_icons=array();
//                        $temp_doc_types=array();
//                        for($x = 0; $x < sizeof($doc_ids); $x++)
//                        {
//                            $doc_audiences[$x]=explode('|%|&|', $doc_audiences[$x]);
//                            if($doc_viewability[$x]=='public'||(isset($_SESSION['id'])&&$ID==$_SESSION['id']))
//                            {
//                                for($y = 0; $y < sizeof($user_groups); $y++)
//                                {
//                                    if(in_array($user_groups[$y], $doc_audiences[$x])||in_array('Everyone', $doc_audiences[$x])||(isset($_SESSION['id'])&&$ID==$_SESSION['id']))
//                                    {
//                                        $temp_doc_ids[]=$doc_ids[$x];
//                                        $temp_document_names[]=$document_names[$x];
//                                        $temp_file_exts[]=$file_exts[$x];
//                                        $temp_num_downloads[]=$num_downloads[$x];
//                                        $temp_sizes[]=get_size(filesize("users/docs/$ID/archive/$doc_ids[$x].$file_exts[$x]"));
//                                        $temp_doc_icons[]=get_doc_icon(2, $file_exts[$x]);
//                                        $temp_doc_types[]=get_doc_icon(3, strtolower($file_exts[$x]));
//                                    }
//                                }
//                            }
//                        }
//                        $doc_ids=$temp_doc_ids;
//                        $document_names=$temp_document_names;
//                        $file_exts=$temp_file_exts;
//                        $num_downloads=$temp_num_downloads;
//                        $sizes=$temp_sizes;
//                        $doc_icons=$temp_doc_icons;
//                        $doc_types=$temp_doc_types;
//
//                    $JSON=array();
//                    $JSON['doc_ids']=$doc_ids;
//                    $JSON['document_names']=$document_names;
//                    $JSON['file_exts']=$file_exts;
//                    $JSON['doc_types']=$doc_types;
//                    $JSON['num_downloads']=$num_downloads;
//                    $JSON['doc_icons']=$doc_icons;
//                    $JSON['doc_sizes']=$sizes;
//                    echo json_encode($JSON);
//                    exit();
//                }
//                else
//                {
//                    $JSON=array();
//                    $JSON['doc_ids']=array();
//                    $JSON['document_names']=array();
//                    $JSON['file_exts']=array();
//                    $JSON['doc_types']=array();
//                    $JSON['num_downloads']=array();
//                    $JSON['doc_icons']=array();
//                    $JSON['doc_sizes']=array();
//                    echo json_encode($JSON);
//                    exit();
//                }
//            }
//        }
//    }
//}

//returns filtered youtube link
else if($num==9)
{
    $video=clean_string($_POST['video_url']);

    if($video!='')
    {
        //cleans video URL
        $final=process_video($video);

        if($final[0]==true)
        {
            $video=convert_video($final[2], $final[1]);

            $JSON=array();
            $JSON['video']=$video;
            echo json_encode($JSON);
            exit();
        }
        else
        {
            $JSON=array();
            $JSON['video']='';
            echo json_encode($JSON);
            exit();
        }
    }
}

//gets user's extended informations
else if($num==10)
{
    $ID=(int)($_POST['user_id']);
    if(is_id($ID)&&user_id_exists($ID)&&!user_id_terminated($ID))
    {
        $timezone=(int)($_POST['timezone']);


        $privacy=get_user_privacy_settings($ID);
        if($privacy[1][0]=='yes'||(isset($_SESSION['id'])&&$ID==$_SESSION['id']))
        {
            $query=mysql_query("SELECT * FROM user_data WHERE user_id=$ID LIMIT 1");
            $query2=mysql_query("SELECT * FROM user_display WHERE user_id=$ID LIMIT 1");
            $query3=mysql_query("SELECT * FROM users WHERE id=$ID LIMIT 1");
            $query5=mysql_query("SELECT * FROM content WHERE user_id=$ID LIMIT 1");
            $query6=mysql_query("SELECT pictures FROM pictures WHERE user_id=$ID LIMIT 1");
            if($query&&mysql_num_rows($query)==1&&$query2&&mysql_num_rows($query2)==1&&$query3&&mysql_num_rows($query3)==1&&$query5&&mysql_num_rows($query5)==1&&$query6&&mysql_num_rows($query6)==1)
            {
                $array=mysql_fetch_array($query);
                $array2=mysql_fetch_array($query2);
                $array3=mysql_fetch_array($query3);
                $array5=mysql_fetch_array($query5);
                $array6=mysql_fetch_array($query6);


                $JSON=array();
                $JSON['name']=$array3['firstName']." ".$array3['lastName'];
                if($array['user_friends'])
                    $JSON['num_friends']=sizeof(explode('|^|*|', $array['user_friends']));
                else
                    $JSON['num_friends']=0;
                
                if($array['user_videos']!='')
                    $JSON['num_videos']=sizeof(explode('|^|*|', $array['user_videos']));
                else
                    $JSON['num_videos']=0;
                $JSON['relationship_status']=$array['user_relationship'];


                $birthday_year=$array2['birthday_year'];
                $birthday=explode('|^|*|', $array['user_birthday']);
                if($birthday_year=='yes')
                    $JSON['birthday']=$birthday[0]." ".$birthday[1]." ".$birthday[2];
                else
                    $JSON['birthday']=$birthday[0]." ".$birthday[1];
                $JSON['gender']=$array['user_sex'];
                $JSON['bio']=$array['user_bio'];
                $JSON['high_school']=$array['high_school'];
                $JSON['college']=$array['college'];
                $JSON['mood']=$array['user_mood'];
                $JSON['num_page_likes']=sizeof(explode('|^|*|', $array['page_likes']));
                if($array5['posts']!='')
                    $JSON['num_updates']=sizeof(explode('|^|*|', $array5['posts']));
                else
                    $JSON['num_updates']=0;
                $JSON['num_post_likes']=0;
                if($array5['likes']!='')
                {
                    $likes=explode('|^|*|', $array5['likes']);
                    $count=0;
                    for($x = 0; $x < sizeof($likes); $x++)
                    {
                        if($likes[$x]!='0')
                            $count=$count+sizeof(explode('|%|&|', $likes[$x]));
                    }
                    $JSON['num_post_likes']=$count;
                }
                $JSON['num_post_dislikes']=0;
                if($array5['dislikes']!='')
                {
                    $dislikes=explode('|^|*|', $array5['dislikes']);
                    $count=0;
                    for($x = 0; $x < sizeof($dislikes); $x++)
                    {
                        if($dislikes[$x]!='0')
                            $count=$count+sizeof(explode('|%|&|', $dislikes[$x]));
                    }
                    $JSON['num_post_dislikes']=$count;
                }
                $JSON['num_pictures']=sizeof(explode('|^|*|', $array6[0]));
                $JSON['date_joined']=get_time_since($array3['timestamps'], $timezone);
                
                echo json_encode($JSON);
                exit();
            }
        }
    }
}

//gets account activity
////////////////////////NEED MODIFYING//////////////////////
else if($num==11)
{
    $ID=(int)($_POST['page_id']);
    
    if(is_id($ID)&&page_id_exists($ID)&&!page_id_terminated($ID))
    {
        
            $page=(int)($_POST['page'])*15;
            $timezone=(int)($_POST['timezone']);

            $num=0;
            $query=mysql_query("SELECT user_friends, friend_timestamps, user_relationship, relationship_timestamp, user_mood, mood_timestamp, page_likes, page_likes_timestamps, redlay_gold FROM user_data WHERE user_id=$ID LIMIT 1");
            if($query&&mysql_num_rows($query)==1)
            {
                $array=mysql_fetch_row($query);

                $adds=explode('|^|*|', $array[0]);
                $add_timestamps=explode('|^|*|', $array[1]);

                $query2=mysql_query("SELECT videos, video_types, video_timestamps FROM content WHERE user_id=$ID LIMIT 1");
                if($query2&&mysql_num_rows($query2))
                {
                    $array2=mysql_fetch_row($query2);
                    $videos=explode('|^|*|', $array2[0]);
                    $video_types=explode('|^|*|', $array2[1]);
                    $video_timestamps=explode('|^|*|', $array2[2]);
                }

                $relationship=$array[2];
                $relationship_timestamp=$array[3];
                $mood=$array[4];
                $mood_timestamp=$array[5];
                $page_likes=explode('|^|*|', $array[6]);
                $page_likes_timestamps=explode('|^|*|', $array[7]);
                $redlay_gold=explode('|^|*|', $array[8]);

                $redlay_gold_purchased=explode('|%|&|', $redlay_gold[2]);


                $array_timestamps=array();
                $array_type=array();
                $array_other=array();
                $prev_num=$num;

                if($array[0]!='')
                {
                    //adds adds to list
                    for($y = 0; $y < sizeof($adds); $y++)
                    {
                        $array_timestamps[$num]=$add_timestamps[$y];
                        $array_type[$num]='add';
                        $array_other[$num]=$adds[$y];

                        $num++;
                    }
                }

                if($array2[0]!='')
                {
                    //adds others to list
                    for($y = 0; $y < sizeof($videos); $y++)
                    {
                       $array_video_other=array();
                       $array_video_other[0]=convert_video($videos[$y], $video_types[$y]);
                       $array_video_other[1]=get_video_preview($videos[$y], $video_types[$y]);
                        $array_other[$num]=$array_video_other;
                        $array_timestamps[$num]=$video_timestamps[$y];
                        $array_type[$num]='video';

                        $num++;
                    }
                }

                if($array[8]!='')
                {
                    for($y = 0; $y < sizeof($page_likes); $y++)
                    {
                        $array_other[$num]=$page_likes[$y];
                        $array_timestamps[$num]=$page_likes_timestamps[$y];
                        $array_type[$num]='page_like';

                        $num++;
                    }
                }

                if($array[5]!=''&&($relationship!='NA'))
                {
                    //adds relationship
                    $array_other[$num]=$relationship;
                    $array_timestamps[$num]=$relationship_timestamp;
                    $array_type[$num]='relationship';

                    $num++;
                }

                if($array[7]!='')
                {
                    //adds mood
                    $array_other[$num]=$mood;
                    $array_timestamps[$num]=$mood_timestamp;
                    $array_type[$num]='mood';

                    $num++;
                }

                if($array[10]!='')
                {
                    //gets latest redlay gold purchase
                    $latest_purchase=0;
                    for($x = 0; $x < sizeof($redlay_gold_purchased); $x++)
                    {
                        if($latest_purchase<$redlay_gold_purchased[$x])
                            $latest_purchase=$redlay_gold_purchased[$x];
                    }

                    //adds redlay gold
                    $array_other[$num]='timestamp';
                    $array_timestamps[$num]=$latest_purchase;
                    $array_type[$num]='redlay_gold';

                    $num++;
                }




                        //sorts things in chronological order
                        $array_timestamps2=$array_timestamps;
                        sort($array_timestamps, SORT_NUMERIC);


                        //creates temporary data of arrays for future use
                        $temp_array_type=$array_type;
                        $temp_array_other=$array_other;


                        $user_type=array();

                        //rearranges rest of data according to sorted timestamps compared to previously unsorted timestamps
                        for($x = 0; $x < $num; $x++)
                        {
                            for($y = 0; $y < $num; $y++)
                            {
                                if($array_timestamps[$x]==$array_timestamps2[$y])
                                {
                                    $number=$y;
                                    $array_timestamps2[$y]='';
                                }
                            }
                            $user_type[$x]=$temp_array_type[$number];
                            $other[$x]=$temp_array_other[$number];
                        }

                        //translates timestamps back to normal after sorting
                        $timestamp_seconds=array();
                        for($x = 0; $x < sizeof($array_timestamps); $x++)
                        {
                            $timestamps[$x]=get_time_since($array_timestamps[$x], $timezone);
                            $timestamp_seconds[$x]=get_time_since_seconds($array_timestamps[$x], $timezone);
                        }


                        //gets the total number of posts
                        $total_size=$num;


                        $array_type=array();
                        $array_other=array();
                        $array_timestamps=array();
                        $array_timestamp_seconds=array();

                        if($total_size<=15)
                        {
                            //adds elements in reverse order to be sorted in chronological order
                            $empty=true;
                            for($x = sizeof($timestamps)-1; $x >=0 ; $x--)
                            {
                                $array_type[]=$user_type[$x];
                                $array_other[]=$other[$x];
                                $array_timestamps[]=$timestamps[$x];
                                $array_timestamp_seconds[]=$timestamp_seconds[$x];
                            }

                            //takes the <10 size of current array and makes it an even 10
                            for($x =0; $x < 15; $x++)
                            {
                                $final_9[]=$array_type[$x];
                                $final_12[]=$array_timestamps[$x];
                                $final_17[]=$array_other[$x];
                                $final_18[]=$array_timestamp_seconds[$x];
                            }
                            $size=$num;
                        }
                        else
                        {
                            $temp=sizeof($timestamps)-$page;
                            if($page>=$total_size)
                            {
                                $temp=sizeof($timestamps)%15;
                                $empty=true;
                                $start=$temp-1;
                                $end=0;
                                $size=$total_size%15;
                            }
                            else
                            {
                                $empty=false;
                                $end=$temp;
                                $start=$temp+14;
                                $size=15;
                            }

                            //reverses posts to be in chronological order
                            for($x = $start; $x >= $end; $x--)
                            {
                                $final_9[]=$user_type[$x];
                                $final_12[]=$timestamps[$x];
                                $final_17[]=$other[$x];
                                $final_18[]=$timestamp_seconds[$x];
                            }
                        }

                        $names=array();
                        $other_names=array();
                        $profile_pictures_other=array();
                        $timestamp_seconds=array();
                        for($x = 0; $x < sizeof($final_9); $x++)
                        {
                            if($final_9[$x]=='user_post'||$final_9[$x]=='user_photo'||$final_9[$x]=='page_post'||$final_9[$x]=='page_photo')
                            {
                                $other_names[$x]='';
                                $profile_pictures_other[$x]='';
                            }
                            else if($final_9[$x]=='add')
                            {
                                $other_names[$x]=get_user_name($final_17[$x]);

                                //gets profile picture of other
                                $profile_pictures_other[$x]=get_profile_picture($final_17[$x]);

                            }
                            else if($final_9[$x]=='page_like')
                            {
                                $other_names[$x]=get_page_name($final_17[$x]);

                                //gets profile picture of other
                                $profile_pictures_other[$x]=get_profile_picture($final_17[$x]);

                            }
                            else
                            {
                                $other_names[$x]='';
                                $profile_pictures_other[$x]='';
                            }


                        }

                        //gets profile picture
                        $profile_picture=get_profile_picture($ID);

                        $JSON=array();
                        $JSON['type']=$final_9;
                        $JSON['timestamps']=$final_12;
                        $JSON['timestamp_seconds']=$final_18;
                        $JSON['size']=$size;
                        $JSON['empty']=$empty;
                        $JSON['total_size']=$total_size;
                        $JSON['name']=get_user_name($ID);
                        $JSON['other']=$final_17;
                        $JSON['other_names']=$other_names;
                        $JSON['profile_picture']=$profile_picture;
                        $JSON['profile_pictures_other']=$profile_pictures_other;
                        echo json_encode($JSON);
                        exit();
        }
    }
}

//gets user's adds
else if($num==12)
{
    $ID=(int)($_POST['user_id']);

    if(is_id($ID)&&user_id_exists($ID)&&!user_id_terminated($ID))
    {
        $privacy=get_user_privacy_settings($ID);
        if($privacy[1][1]=='yes'||(isset($_SESSION['id'])&&$ID==$_SESSION['id'])||(isset($_SESSION['id'])&&user_is_friends($ID, $_SESSION['id'])))
        {
            $query=mysql_query("SELECT user_friends, friend_timestamps, audience_groups FROM user_data WHERE user_id=$ID LIMIT 1");
            if($query&&mysql_num_rows($query)==1)
            {
                $array=mysql_fetch_row($query);
                $adds=explode('|^|*|', $array[0]);
                $add_timestamps=explode('|^|*|', $array[1]);
                $add_groups=explode('|^|*|', $array[2]);
                
                
                $changed=false;
                $temp_adds=array();
                $temp_add_timestamps=array();
                $temp_add_groups=array();
                for($x = 0; $x < sizeof($adds); $x++)
                {
                    if(!user_id_terminated($adds[$x]))
                    {
                        $temp_adds[]=$adds[$x];
                        $temp_add_timestamps[]=$add_timestamps[$x];
                        $temp_add_groups[]=$add_groups[$x];
                    }
                    else
                        $changed=true;
                }
                
                if($changed)
                {
                    $adds=$temp_adds;
                    $add_timestamps=$temp_add_timestamps;
                    $add_groups=$temp_add_groups;
                    
                    $temp_adds=implode('|^|*|', $temp_adds);
                    $temp_add_timestamps=implode('|^|*|', $temp_add_timestamps);
                    $temp_add_groups=implode('|^|*|', $temp_add_groups);
                    
                    $query=mysql_query("UPDATE user_data SET user_friends='$temp_adds', friend_timestamps='$temp_add_timestamps', audience_groups='$temp_add_groups' WHERE user_id=$ID");
                    if(!$query)
                        log_error("profile_query.php: (12:0): ",mysql_error());
                }

                if($array[0]!='')
                {
                    $profile_pictures=array();
                    $names=array();
                    $num_adds=array();
                    for($x = 0; $x < sizeof($adds); $x++)
                    {
                        //gets profile pictures
                        $profile_pictures[$x]=get_profile_picture($adds[$x]);

                        //gets names
                        $names[$x]=get_user_name($adds[$x]);

                        //gets number of adds
                        $query=mysql_query("SELECT user_friends FROM user_data WHERE user_id=$adds[$x] LIMIT 1");
                        if($query&&mysql_num_rows($query)==1)
                        {
                            $array=mysql_fetch_row($query);
                            $num_adds[$x]=sizeof(explode('|^|*|', $array[0]));
                        }
                        else
                            log_error("profile_query.php: (12:1): ",mysql_error());
                    }

                    $JSON=array();
                    $JSON['adds']=$adds;
                    $JSON['profile_pictures']=$profile_pictures;
                    $JSON['names']=$names;
                    $JSON['num_adds']=$num_adds;
                    echo json_encode($JSON);
                    exit();
                }
                else
                {
                    $JSON=array();
                    $JSON['adds']=array();
                    $JSON['profile_pictures']=array();
                    $JSON['names']=array();
                    $JSON['num_adds']=array();
                    echo json_encode($JSON);
                    exit();
                }
            }
        }
    }
}

//gets availible months and years of posts
else if($num==13)
{
    $ID=(int)($_POST['user_id']);
    if(is_id($ID)&&user_id_exists($ID)&&!user_id_terminated($ID))
    {
        $timezone=(int)($_POST['timezone']);
        $query=mysql_query("SELECT timestamps FROM content WHERE user_id=$ID LIMIT 1");
        if($query&&mysql_num_rows($query)==1)
        {
            $array=mysql_fetch_row($query);
            $timestamps=explode('|^|*|', $array[0]);

            $years=array();
            $months=array();
            for($x = 0; $x < sizeof($timestamps); $x++)
            {
                $timestamps[$x]=explode(' ', str_replace(",", "", get_adjusted_date($timestamps[$x], $timezone)));

                $index=-1;
                for($y = 0; $y < sizeof($years); $y++)
                {
                    //asks if year is already stored
                    if($years[$y]==$timestamps[$x][2])
                    {
                        $index=$y;

                        //checks if month exists for specific year
                        $months_index=-1;
                        for($z = 0; $z < sizeof($months[$index]); $z++)
                        {
                            if($months[$index][$z]==$timestamps[$x][0])
                                $months_index=$z;
                        }

                        //adds month if doesn't exist for specific year
                        if($months_index==-1)
                            $months[$index][]=$timestamps[$x][0];
                    }


                }

                if($index==-1)
                {
                    $years[]=$timestamps[$x][2];
                    $months[]=array();
                    $months[sizeof($months)-1][0]=$timestamps[$x][0];
                }
            }
                $JSON=array();
                $JSON['years']=$years;
                $JSON['months']=$months;
                echo json_encode($JSON);
                exit();
        }
    }
}

//sets image thumbnail
else if($num==14)
{
    //gets all the necessary AWS schtuff
    if (!class_exists('S3'))
        require_once('S3.php');
    if (!defined('awsAccessKey'))
        define('awsAccessKey', ACCESS_KEY);
    if (!defined('awsSecretKey'))
        define('awsSecretKey', SECREY_KEY);

    //creates S3 item with schtuff
    $s3 = new S3(awsAccessKey, awsSecretKey);
    
    
    $image_id=clean_string($_POST['image_id']);
    $top=(int)($_POST['top']);
    $left=(int)($_POST['left']);
    $preview_width=(int)($_POST['width']);
    $preview_height=(int)($_POST['height']);
//    $image_id="2495c6d86a1a457691f7543d37647219c1afbe09";
//    $top=0;
//    $left=150;
//    $preview_width=450;
//    $preview_height=300;
    
    $query=mysql_query("SELECT pictures, image_types FROM pictures WHERE user_id=$_SESSION[id] LIMIT 1");
    if($query&&mysql_num_rows($query)==1)
    {
        $array=mysql_fetch_row($query);
        $pictures=explode('|^|*|', $array[0]);
        $image_types=explode('|^|*|', $array[1]);
        
        $index=-1;
        for($x = 0; $x < sizeof($pictures); $x++)
        {
            if($pictures[$x]==$image_id)
                $index=$x;
        }
        
        if($index!=-1)
        {
            $file="http://u.redlay.com/users/$_SESSION[id]/photos/$image_id.".$image_types[$index];

            //gets image dimensions
            list($width, $height)=getimagesize($file);

            if($width>$height)
            {
                $new_thumb_height=250;
                $new_thumb_width=$width/($height/250);
            }
            else if($height>$width)
            {
                $new_thumb_width=250;
                $new_thumb_height=$height/($width/250);
            }
            else if($height==$width&&($height>800||$width>800))
            {
                $new_thumb_width=250;
                $new_thumb_height=250;
            }
            else
            {
                $new_thumb_width=250;
                $new_thumb_height=250;
            }
            
            $left=($width/$preview_width)*$left;
            $top=($height/$preview_height)*$top;

            //if image is a jpg
            if($image_types[$index]=='jpeg'||$image_types[$index]=='jpg')
            {
                $new_path="users/$_SESSION[id]/thumbs/$image_id.jpg";
                $value=md5(uniqid(rand()));
                $temp_path="/tmp/$value.jpg";

                //copies photo to temperary path
                copy($file, $temp_path);

                //creates thumbnail from temp photo path
                $img=imagecreatefromjpeg($temp_path);
                $thumb=imagecreatetruecolor(250, 250);
                imagecopyresampled($thumb, $img, 0, 0, $left, $top, $new_thumb_width, $new_thumb_height, $width, $height);
                imagejpeg($thumb, $temp_path, 80);

                //updates newly created thumbnail
                $s3->putObjectFile($temp_path, "bucket_name", $new_path, S3::ACL_PUBLIC_READ);

                //deletes temp photo
                imagedestroy($thumb);
                unlink($temp_path);
                echo "Thumbnail set";
            }

            //if image is a png
            else if($image_types[$index]=='png')
            {
                $thumb_path="users/$_SESSION[id]/thumbs/$image_id.png";
                $value=md5(uniqid(rand()));
                $temp_path="/tmp/$value.png";

                //copies photo to temperary path
                copy($file, $temp_path);

                //uploads thumb nail
                $img=imagecreatefrompng($temp_path);
                $thumb=imagecreatetruecolor(250, 250);
                $black=imagecolorallocate($thumb, 0,0,0);
                imagecolortransparent($thumb, $black);
                imagecopyresampled($thumb, $img, 0, 0, $left, $top, $new_thumb_width, $new_thumb_height, $width, $height);
                imagepng($thumb, $temp_path, 80);

                //updates newly created thumbnail
                $s3->putObjectFile($temp_path, "bucket_name", $new_path, S3::ACL_PUBLIC_READ);

                //deletes temp photo
                imagedestroy($thumb);
                unlink($temp_path);
                echo "Thumbnail set";

            }
            else if($image_types[$index]=='gif')
            {
                $thumb_path="users/$_SESSION[id]/thumbs/$image_id.gif";
                $value=md5(uniqid(rand()));
                $temp_path="/tmp/$value.gif";

                //copies photo to temperary path
                copy($file, $temp_path);

                //uploads thumb nail
                $img=imagecreatefromgif($temp_path);
                $thumb=imagecreatetruecolor(250, 250);
                imagecopyresampled($thumb, $img, 0, 0, $left, $top, $new_thumb_width, $new_thumb_height, $width, $height);
                imagegif($thumb, $temp_path);

                //updates newly created thumbnail
                $s3->putObjectFile($temp_path, "bucket_name", $new_path, S3::ACL_PUBLIC_READ);

                //deletes temp photo
                imagedestroy($thumb);
                unlink($temp_path);
                echo "Thumbnail set";
            }
            else
            {
                echo "Something went wrong. We are working on fixing it";
                log_error("profile_query.php: ", "Image type: ".$image_types[$index]);
            }
        }
    }
}

//gets page's information
else if($num==15)
{
    $ID=(int)($_POST['page_id']);
    if(is_id($ID)&&page_id_exists($ID)&&!page_id_terminated($ID))
    {
        $timezone=(int)($_POST['timezone']);

        $query=mysql_query("SELECT created, description, location, website FROM page_data WHERE page_id=$ID LIMIT 1");
        if($query&&mysql_num_rows($query)==1)
        {
            $array=mysql_fetch_row($query);
            $started=explode('|^|*|', $array[0]);
            $description=$array[1];
            $location=$array[2];
            $website=$array[3];
            
            $JSON=array();
            $JSON['started']=implode(' ',$started);
            $JSON['description']=$description;
            $JSON['location']=$location;
            $JSON['website']=$website;
            echo json_encode($JSON);
            exit();
        }
    }
}
//gets fan's posts
else if($num==16)
{
    $page=(int)($_POST['page']);
    $page_id=(int)($_POST['page_id']);
    
    //unifinished   
}