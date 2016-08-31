<?php
@include('init.php');
include('universal_functions.php');
$allowed="all";
include('security_checks.php');

$num=(int)($_POST['num']);


//gets photo comments
if($num==1)
{
    $type=clean_string($_POST['type']);
    $ID=(int)($_POST['poster_id']);
    $picture_id=clean_string($_POST['picture_id']);
    $timezone=(int)($_POST['timezone']);
    
    if(is_id($ID)&&user_id_exists($ID)&&!user_id_terminated($ID))
    {
        if($type=='user')
        {
            $query=mysql_query("SELECT pictures, comment_ids, picture_comments, comments_user_sent, comment_timestamps, comment_likes, comment_dislikes FROM pictures WHERE user_id=$ID LIMIT 1");
            if($query&&mysql_num_rows($query)==1)
            {
                $array=mysql_fetch_row($query);
                $pictures=explode('|^|*|', $array[0]);
                $comment_ids=explode('|^|*|', $array[1]);
                $comments=explode('|^|*|', $array[2]);
                $comments_users_sent=explode('|^|*|', $array[3]);
                $comment_timestamps=explode('|^|*|', $array[4]);
                $comment_likes=explode('|^|*|', $array[5]);
                $comment_dislikes=explode('|^|*|', $array[6]);


                $index=-1;
                for($x = 0; $x < sizeof($pictures); $x++)
                {
                    if($picture_id==$pictures[$x])
                        $index=$x;
                }

                if($index!=-1)
                {



                    //gets rid of terminated accounts
                    $changed=false;
                    $temp_comments=array();
//                    $temp_comment_likes=array();
//                    $temp_comment_dislikes=array();
                    $temp_comments_users_sent=array();
                    $temp_comment_timestamps=array();
                    $temp_comment_ids=array();
                    for($x = 0;  $x < sizeof($pictures); $x++)
                    {
                            //explodes comment stuff
                            $comment_ids[$x]=explode('|%|&|', $comment_ids[$x]);
                            $comments[$x]=explode('|%|&|', $comments[$x]);
                            $comment_likes[$x]=explode('|%|&|', $comment_likes[$x]);
                            $comment_dislikes[$x]=explode('|%|&|', $comment_dislikes[$x]);
                            $comments_users_sent[$x]=explode('|%|&|', $comments_users_sent[$x]);
                            $comment_timestamps[$x]=explode('|%|&|', $comment_timestamps[$x]);

                            //gets rid of comments delete accounts
                            $temptemp_comments=array();
//                            $temptemp_comment_likes=array();
//                            $temptemp_comment_dislikes=array();
                            $temptemp_comments_users_sent=array();
                            $temptemp_comment_ids=array();
                            $temptemp_comment_timestamps=array();
                            for($y = 0; $y < sizeof($comment_ids[$x]); $y++)
                            {
                                if($comments_users_sent[$x][$y]!=''&&!user_id_terminated($comments_users_sent[$x][$y]))
                                {
                                    $temptemp_comments[]=$comments[$x][$y];
//                                    $temptemp_comment_likes[]=$comment_likes[$x][$y];
//                                    $temptemp_comment_dislikes[]=$comment_dislikes[$x][$y];
                                    $temptemp_comments_users_sent[]=$comments_users_sent[$x][$y];
                                    $temptemp_comment_ids[]=$comment_ids[$x][$y];
                                    $temptemp_comment_timestamps[]=$comment_timestamps[$x][$y];

                                    //gets rid of comment likes of terminated accounts
//                                    if($comment_likes[$x][$y]!='')
//                                    {
//                                        $comment_likes[$x][$y]=explode('|@|$|', $comment_likes[$x][$y]);
//                                        $temptemptemp_comment_likes=array();
//                                        for($z = 0; $z < sizeof($comment_likes[$x][$y]); $z++)
//                                        {
//                                            if(!user_id_terminated($comment_likes[$x][$y][$z]))
//                                                $temptemptemp_comment_likes[]=$comment_likes[$x][$y][$z];
//                                            else
//                                                $changed=true;
//                                        }
//                                        $temptemp_comment_likes[]=$temptemptemp_comment_likes;
//                                    }
//
//                                    //gets rid of comment dislikes of terminated accounts
//                                    if($comment_dislikes[$x][$y]!='')
//                                    {
//                                        $comment_dislikes[$x][$y]=explode('|@|$|', $comment_dislikes[$x][$y]);
//                                        $temptemptemp_comment_dislikes=array();
//                                        for($z = 0; $z < sizeof($comment_dislikes[$x][$y]); $z++)
//                                        {
//                                            if(!user_id_terminated($comment_dislikes[$x][$y][$z]))
//                                                $temptemptemp_comment_dislikes[]=$comment_dislikes[$x][$y][$z];
//                                            else
//                                                $changed=true;
//                                        }
//                                        $temptemp_comment_dislikes[]=$temptemptemp_comment_dislikes;
//                                    }
                                }
                                else
                                    $changed=true;
                            }
    //                                        
                            if($temptemp_comments[0]!=NULL)
                            {
                                $temp_comments[]=$temptemp_comments;
//                                $temp_comment_likes[]=$temptemp_comment_likes;
//                                $temp_comment_dislikes[]=$temptemp_comment_dislikes;
                                $temp_comments_users_sent[]=$temptemp_comments_users_sent;
                                $temp_comment_ids[]=$temptemp_comment_ids;
                                $temp_comment_timestamps[]=$temptemp_comment_timestamps;
                            }
                            else
                            {
                                $temp_comments[]=array();
//                                $temp_comment_likes[]=array();
//                                $temp_comment_dislikes[]=array();
                                $temp_comments_users_sent[]=array();
                                $temp_comment_ids[]=array();
                                $temp_comment_timestamps[]=array();
                            }
                    }

                    if($changed)
                    {
                        $comments=$temp_comments;
//                        $comment_likes=$temp_comment_likes;
//                        $comment_dislikes=$temp_comment_dislikes;
                        $comments_users_sent=$temp_comments_users_sent;
                        $comment_timestamps=$temp_comment_timestamps;
                        $comment_ids=$temp_comment_ids;

                        //implodes comment likes
//                        $temp_comment_likes=$comment_likes;
//                        $temp_comment_dislikes=$comment_dislikes;
                        $temp_comments_users_sent=$comments_users_sent;
                        $temp_comment_timestamps=$comment_timestamps;
                        $temp_comment_ids=$comment_ids;
                        $temp_comments=$comments;
                        for($x = 0; $x < sizeof($temp_comments_users_sent); $x++)
                        {
//                            for($y = 0; $y < sizeof($temp_comments_users_sent[$x]); $y++)
//                            {
//                                $temp_comment_likes[$x][$y]=implode('|@|$|', $temp_comment_likes[$x][$y]);
//                                $temp_comment_dislikes[$x][$y]=implode('|@|$|', $temp_comment_dislikes[$x][$y]);
//                            }

//                            $temp_comment_likes[$x]=implode('|%|&|', $temp_comment_likes[$x]);
//                            $temp_comment_dislikes[$x]=implode('|%|&|', $temp_comment_dislikes[$x]);
                            $temp_comments_users_sent[$x]=implode('|%|&|', $temp_comments_users_sent[$x]);
                            $temp_comment_timestamps[$x]=implode('|%|&|', $temp_comment_timestamps[$x]);
                            $temp_comment_ids[$x]=implode('|%|&|', $temp_comment_ids[$x]);
                            $temp_comments[$x]=implode('|%|&|', $temp_comments[$x]);
                        }
//                        $temp_comment_likes=implode('|^|*|', $temp_comment_likes);
//                        $temp_comment_dislikes=implode('|^|*|', $temp_comment_dislikes);
                        $temp_comments_users_sent=implode('|^|*|', $temp_comments_users_sent);
                        $temp_comment_timestamps=implode('|^|*|', $temp_comment_timestamps);
                        $temp_comment_ids=implode('|^|*|', $temp_comment_ids);
                        $temp_comments=implode('|^|*|', $temp_comments);

                        $query=mysql_query("UPDATE pictures SET comment_ids='$temp_comment_ids', picture_comments='".mysql_escape_string($temp_comments)."', comments_user_sent='$temp_comments_users_sent', comment_timestamps='$temp_comment_timestamps' WHERE user_id=$ID");
                        if(!$query)
                            log_error("home_query.php: (1): ", mysql_error());
                    }



                   $comment_names=array();
                   $comment_badges=array();
                    $profile_pictures=array();
                    $badges=array();
                    $comment_timestamp_seconds=array();
                    for($x = 0; $x < sizeof($comment_likes[$index]); $x++)
                    {
                        $comment_names[]=get_user_name($comments_users_sent[$index][$x]);

                        $temp_timestamp=$comment_timestamps[$index][$x];
                        $comment_timestamps[$index][$x]=get_time_since($comment_timestamps[$index][$x], $timezone);
                        $comment_timestamp_seconds[]=get_time_since_seconds($temp_timestamp, $timezone);
                        $comment_badges[$x]=get_badges($comments_users_sent[$index][$x]);

                        //gets profile pictures
                        $profile_pictures[$x]=get_profile_picture($comments_users_sent[$index][$x]);

                        //gets badges
                        $badges[$x]=get_badges($comments_users_sent[$index][$x]);
                    }

                    $num_comment_likes=array();
                    $num_comment_dislikes=array();
                    $has_liked_comment=array();
                    $has_disliked_comment=array();
                    for($x = 0; $x < sizeof($comment_likes[$index]); $x++)
                    {
                        //gets num likes
                        if($comment_likes[$index][$x][0]!='')
                            $num_comment_likes[$x]=sizeof($comment_likes[$index][$x]);
                        else
                            $num_comment_likes[$x]=0;

                        //gets num dislikes
                        if($comment_dislikes[$index][$x][0]!='')
                            $num_comment_dislikes[$x]=sizeof($comment_dislikes[$index][$x]);
                        else
                            $num_comment_dislikes[$x]=0;

                        //gets has liked
                        if(isset($_SESSION['id'])&&($comment_likes[$index][$x][0]==$_SESSION['id']||in_array($comment_likes[$index][$x], $_SESSION['id'])))
                            $has_liked_comment[$x]=true;
                        else
                            $has_liked_comment[$x]=false;

                        //gets has disliked
                        if(isset($_SESSION['id'])&&($comment_dislikes[$index][$x][0]==$_SESSION['id']||in_array($comment_dislikes[$index][$x], $_SESSION['id'])))
                            $has_disliked_comment[$x]=true;
                        else
                            $has_disliked_comment[$x]=false;


                    }

                    $JSON=array();
                    $JSON['comments']=$comments[$index];
                    $JSON['comments_user_sent']=$comments_users_sent[$index];
                    $JSON['comment_names']=$comment_names;
                    $JSON['comment_timestamps']=$comment_timestamps[$index];
                    $JSON['comment_timestamp_seconds']=$comment_timestamp_seconds;
                    $JSON['comment_likes']=$comment_likes[$index];
                    $JSON['comment_dislikes']=$comment_dislikes[$index];
                    $JSON['num_comment_likes']=$num_comment_likes;
                    $JSON['num_comment_dislikes']=$num_comment_dislikes;
                    $JSON['badges']=$badges;
                    $JSON['comment_badges']=$comment_badges;
                    $JSON['comment_ids']=$comment_ids[$index];
                    $JSON['profile_pictures']=$profile_pictures;

                    $JSON['has_liked_comment']=$has_liked_comment;
                    $JSON['has_disliked_comment']=$has_disliked_comment;
                    $JSON['num_comment_likes']=$num_comment_likes;
                    $JSON['num_comment_dislikes']=$num_comment_dislikes;
                    echo json_encode($JSON);
                    exit();
                }
            }
            else
            {
                echo "Something went wrong";
                log_error("view_photo_query.php: ", mysql_error());
            }
        }
        else
        {

        }
    }
}

//changes photo's audience
else if($num==2)
{
    if(isset($_SESSION['id']))
    {
        $picture_id=clean_string($_POST['picture_id']);
        $groups=$_POST['groups'];

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

        if(isset($groups[0]))
        {
            $query=mysql_query("SELECT pictures, image_audiences FROM pictures WHERE user_id=$_SESSION[id] LIMIT 1");
            if($query&&mysql_num_rows($query)==1)
            {
                $array=mysql_fetch_row($query);
                $pictures=explode('|^|*|', $array[0]);
                $audiences=explode('|^|*|', $array[1]);

                $index=-1;
                for($x = 0; $x < sizeof($pictures); $x++)
                {
                    if($picture_id==$pictures[$x])
                        $index=$x;
                }

                if($index!=-1)
                {
                    $audiences[$index]=implode('|%|&|', $groups);

                    $audiences=implode('|^|*|', $audiences);
                    $query=mysql_query("UPDATE pictures SET image_audiences='$audiences' WHERE user_id=$_SESSION[id]");
                    if($query)
                        echo "Audience changed";
                    else
                    {
                        echo "Sometehing went wrong. We are working on fixing it";
                        log_error("view_photo_query.php: (2): ", mysql_error());
                    }
                }
                else
                    echo "Picture ID invalid";
            }
            else
            {
                echo "Something went wrong. We are working on fixing it";
                log_error("view_photo_query.php: (2): ", mysql_error());
            }
        }
    }
}

//gets photo timestamp
else if($num==3)
{
    $type=clean_string($_POST['type']);
    $timezone=(int)($_POST['timezone']);
    $pic_index=(int)($_POST['index']);
    $ID=(int)($_POST['user_id']);
    
    if(is_id($ID)&&user_id_exists($ID)&&!user_id_terminated($ID))
    {
        if($type=='user')
        {
            $query=mysql_query("SELECT timestamp FROM pictures WHERE user_id=$ID LIMIT 1");
            if($query&&mysql_num_rows($query)==1)
            {
                $array=mysql_fetch_row($query);
                $timestamps=explode('|^|*|', $array[0]);
            }
        }
        else if($type=='page')
        {
            $query=mysql_query("SELECT timestamp FROM page_pictures WHERE user_id=$ID LIMIT 1");
            if($query&&mysql_num_rows($query)==1)
            {
                $array=mysql_fetch_row($query);
                $timestamps=explode('|^|*|', $array[0]);
            }
        }

        $time=get_time_since($timestamps[$pic_index], $timezone);
        $timestamp_seconds=get_time_since_seconds($timestamps[$pic_index], $timezone);
        
        
        $JSON=array();
        $JSON['timestamp']=$time;
        $JSON['timestamp_seconds']=$timestamp_seconds;
        echo json_encode($JSON);
        exit();
    }
}

//gets photo information
else if($num==4)
{
    $picture_id=clean_string($_POST['picture_id']);
    $user_id=(int)($_POST['user_id']);
    $type=clean_string($_POST['type']);
    
    if(is_id($user_id)&&user_id_exists($user_id)&&!user_id_terminated($user_id))
    {
        if($type=='user')
        {
            $query=mysql_query("SELECT pictures, picture_likes, picture_dislikes, picture_descriptions FROM pictures WHERE user_id=$user_id LIMIT 1");
            if($query&&mysql_num_rows($query)==1)
            {
                $array=mysql_fetch_row($query);
                $pictures=explode('|^|*|', $array[0]);
                $picture_likes=explode('|^|*|', $array[1]);
                $picture_dislikes=explode('|^|*|', $array[2]);
                $picture_descriptions=explode('|^|*|', $array[3]);

                $index=-1;
                for($x = 0; $x < sizeof($pictures); $x++)
                {
                    if($picture_id==$pictures[$x])
                        $index=$x;
                }

                if($index!=-1)
                {
                    
                    //deletes terminated accounts
                    $changed=false;
                    $temp_picture_likes=array();
                    $temp_picture_dislikes=array();
                    for($x = 0; $x < sizeof($pictures); $x++)
                    {
                        $picture_likes[$x]=explode('|%|&|', $picture_likes[$x]);
                        $picture_dislikes[$x]=explode('|%|&|', $picture_dislikes[$x]);
                        
                        //deletes terminated accounts
                        $temptemp_picture_likes=array();
                        for($y = 0; $y < sizeof($picture_likes[$x]); $y++)
                        {
                            if(!user_id_terminated($picture_likes[$x][$y]))
                                $temptemp_picture_likes[]=$picture_likes[$x][$y];
                            else
                                $changed=true;
                        }
                        $temp_picture_likes[]=$temptemp_picture_likes;
                        
                        //deletes terminated accounts
                        $temptemp_picture_dislikes=array();
                        for($y = 0; $y < sizeof($picture_dislikes[$x]); $y++)
                        {
                            if(!user_id_terminated($picture_dislikes[$x][$y]))
                                $temptemp_picture_dislikes[]=$picture_dislikes[$x][$y];
                            else
                                $changed=true;
                        }
                        $temp_picture_dislikes[]=$temptemp_picture_dislikes;
                    }
                    
                    if($changed)
                    {
                        $picture_likes=$temp_picture_likes;
                        $picture_dislikes=$temp_picture_dislikes;
                    }
                    

                    $has_liked=false;
                    $has_disliked=false;
                    for($x = 0; $x < sizeof($picture_likes[$index]); $x++)
                    {
                        if($picture_likes[$index][$x]==$_SESSION['id'])
                            $has_liked=true;
                    }

                    for($x = 0; $x < sizeof($picture_dislikes[$index]); $x++)
                    {
                        if($picture_dislikes[$index][$x]==$_SESSION['id'])
                            $has_disliked=true;
                    }

                    if($picture_likes[$index][0]=='')
                        $num_likes=0;
                    else
                        $num_likes=sizeof($picture_likes[$index]);

                    if($picture_dislikes[$index][0]=='')
                        $num_dislikes=0;
                    else
                        $num_dislikes=sizeof($picture_dislikes[$index]);

                    $JSON=array();
                    $JSON['has_liked']=$has_liked;
                    $JSON['has_disliked']=$has_disliked;
                    $JSON['num_likes']=$num_likes;
                    $JSON['num_dislikes']=$num_dislikes;
                    $JSON['badges']=get_badges($user_id);
                    $JSON['picture_description']=$picture_descriptions[$index];
                    echo json_encode($JSON);
                    exit();
                }
            }

        }
        else if($type=='page')
        {

        }
    }
}

//gets related photos
else if($num==5)
{
    $user_id=(int)($_POST['user_id']);
    $timezone=(int)($_POST['timezone']);
    
    if(is_id($user_id)&&user_id_exists($user_id)&&!user_id_terminated($user_id))
    {
        $query=mysql_query("SELECT pictures, image_types, picture_likes, picture_dislikes, timestamp FROM pictures WHERE user_id=$user_id LIMIT 1");
        if($query&&mysql_num_rows($query)==1)
        {
            $array=mysql_fetch_row($query);
            $pictures=explode('|^|*|', $array[0]);
            $image_types=explode('|^|*|', $array[1]);
            $picture_likes=explode('|^|*|', $array[2]);
            $picture_dislikes=explode('|^|*|', $array[3]);
            $timestamps=explode('|^|*|', $array[4]);
            
            $rand_array=array();
            for($x = 0; $x < 4; $x++)
            {
                //creates random number
                $rand=mt_rand(0, sizeof($pictures));
                
                //checks whether random number already exists
                if(!in_array($rand, $rand_array))
                    $rand_array[]=$rand;
                
                //if random number already does exist
                else
                {
                    //create new random numbers until it doesn't already exist
                    while(in_array($rand, $rand_array))
                        $rand=mt_rand(0, sizeof($pictures));
                    
                    //add new unique random number
                    $rand_array[]=$rand;
                }
            }
            
            $temp_pictures=array();
            $temp_image_types=array();
            $temp_picture_likes=array();
            $temp_picture_dislikes=array();
            $temp_timestamps=array();
            
            for($x = 0; $x < sizeof($rand_array); $x++)
            {
                $index=$rand_array[$x];
                $temp_pictures[]=$pictures[$index];
                $temp_image_types[]=$image_types[$index];
                $temp_picture_likes[]=$picture_likes[$index];
                $temp_picture_dislikes[]=$picture_dislikes[$index];
                $temp_timestamps[]=$timestamps[$index];
            }
            
            //gets number of likes and dislikes
            $num_likes=array();
            $num_dislikes=array();
            $profile_pictures=array();
            $pictures=array();
            $timestamps=array();
            for($x = 0; $x < sizeof($temp_picture_likes); $x++)
            {
                if($temp_picture_likes[$x]=="")
                    $num_likes[]=0;
                else
                {
                    $temp_picture_likes[$x]=explode('|%|&|', $temp_picture_likes[$x]);
                    $num_likes[]=sizeof($temp_picture_likes[$x]);
                }
                
                if($temp_picture_dislikes[$x]=="")
                    $num_dislikes[]=0;
                else
                {
                    $temp_picture_dislikes[$x]=explode('|%|&|', $temp_picture_dislikes[$x]);
                    $num_dislikes[]=sizeof($temp_picture_dislikes[$x]);
                }
                
                //gets timestamps
                $timestamps[$x]=get_time_since($temp_timestamps[$x], $timezone);
                
                //gets picture srcs
                $date=get_date();
                if($date-$timestamps[$x]>=86400)
                    $pictures[$x]="http://u.redlay.com/users/$user_id/thumbs/$temp_pictures[$x].$temp_image_types[$x]";
                else
                    $pictures[$x]="https://s3.amazonaws.com/bucket_name/users/$user_id/thumbs/$temp_pictures[$x].$temp_image_types[$x]";
            }
            
            $JSON=array();
            $JSON['pictures']=$pictures;
            $JSON['picture_ids']=$temp_pictures;
            $JSON['profile_picture']=get_profile_picture($user_id);
            $JSON['name']=get_user_name($user_id);
            $JSON['image_types']=$temp_image_types;
            $JSON['num_likes']=$num_likes;
            $JSON['num_dislikes']=$num_dislikes;
            $JSON['timestamps']=$timestamps;
            echo json_encode($JSON);
            exit();
        }
    }
}