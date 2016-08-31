<?php
@include('init.php');
include('universal_functions.php');
$allowed="all";
include('security_checks.php');

$num=(int)($_POST['num']);
$timezone=(int)($_POST['timezone']);

////gets top posts
if($num==1)
{
    $query=mysql_query("SELECT posts, posts_users_sent, post_ids, original_post_ids, post_timestamps FROM public WHERE num=1");
    if($query)
    {
        $array=mysql_fetch_row($query);
        $posts=explode('|^|*|', $array[0]);
        $posts_users_sent=explode('|^|*|', $array[1]);
        $post_ids=explode('|^|*|', $array[2]);
        $original_post_ids=explode('|^|*|', $array[3]);
        $post_timestamps=explode('|^|*|', $array[4]);


        $scores=array();
        $temp_post_users=array();
        $temp_post_ids=array();
        $temp_likes=array();
        $temp_dislikes=array();
        $temp_comment_ids=array();
        $post_likes=array();
        $post_dislikes=array();
        $post_comment_ids=array();


        //gets score for top posts
        for($loop = 0; $loop < sizeof($posts_users_sent); $loop++)
        {
            if(!in_array($posts_users_sent[$loop], $temp_post_users))
            {
                $query=mysql_query("SELECT post_ids, posts,  likes, dislikes, comment_ids FROM content WHERE user_id=$posts_users_sent[$loop] LIMIT 1");
                if($query&&mysql_num_rows($query)==1)
                {
                    $array=mysql_fetch_row($query);
                    $user_post_ids=explode('|^|*|', $array[0]);
                    $user_posts=explode('|^|*|', $array[1]);
                    $likes=explode('|^|*|', $array[2]);
                    $dislikes=explode('|^|*|', $array[3]);
                    $comment_ids=explode('|^|*|', $array[4]);


                    $index=array_search($original_post_ids[$loop], $user_post_ids);
                    if($index!=false)
                    {
                        //if post is the same
                        if(preg_replace('/[^a-zA-Z0-9_ %\[\]\.\(\)%&-]/s', '', $posts[$loop])==preg_replace('/[^a-zA-Z0-9_ %\[\]\.\(\)%&-]/s', '', $user_posts[$index]))
                        {
                            $likes[$index]=explode('|%|&|', $likes[$index]);
                            $dislikes[$index]=explode('|%|&|', $dislikes[$index]);
                            $comment_ids[$index]=explode('|%|&|', $comment_ids[$index]);

                            //adds to temporary arrays
                            $temp_post_users[]=$posts_users_sent[$loop];
                            $temp_post_ids[]=$user_post_ids;
                            $temp_likes[]=$likes;
                            $temp_dislikes[]=$dislikes;
                            $temp_comment_ids[]=$comment_ids;

                            //add to final arrays
                            $post_likes[]=$likes[$index];
                            $post_dislikes[]=$dislikes[$index];
                            $post_comment_ids[]=$comment_ids[$index];

                            $x=sizeof($likes[$index])-sizeof($dislikes[$index]);

                            if($x>0)
                                $y=1;
                            else if($x==0)
                                $y=0;
                            else if($x<0)
                                $y=-1;

                            if(abs($x)>=1)
                                $z=abs($x);
                            else if(abs($x)<1)
                                $z=1;

                            $t=$post_timestamps[$loop]-1360994400;

                            //reddit's algorithm
                            $f=log($z)+(($y*$t)/45000);

                            $scores[]=$f;
                        }
                        else
                        {
                            $post_likes[]=array();
                            $post_dislikes[]=array();
                            $post_comment_ids[]=array();
                            $scores[]=0;
                        }
                    }
                    else
                    {
                        $post_likes[]=array();
                        $post_dislikes[]=array();
                        $post_comment_ids[]=array();
                        $scores[]=0;
                    }
                }
                else
                {
                    $post_likes[]=array();
                    $post_dislikes[]=array();
                    $post_comment_ids[]=array();
                    $scores[]=0;
                }
            }
            else
            {
                $index=array_search($posts_users_sent[$loop], $temp_post_users);

                $post_ids=$temp_post_ids[$index];
                $likes=$temp_likes[$index];
                $dislikes=$temp_dislikes[$index];
                $comment_ids=$temp_comment_ids[$index];

                $index2=array_search($original_post_ids[$loop], $post_ids);

                $likes[$index2]=explode('|%|&|', $likes[$index2]);
                $dislikes[$index2]=explode('|%|&|', $dislikes[$index2]);
                $comment_ids[$index2]=explode('|%|&|', $comment_ids[$index2]);

                $post_likes[]=$likes[$index2];
                $post_dislikes[]=$dislikes[$index2];
                $post_comment_ids[]=$comment_ids[$index2];


                $x=sizeof($likes[$index2])-sizeof($dislikes[$index2]);

                if($x>0)
                    $y=1;
                else if($x==0)
                    $y=0;
                else if($x<0)
                    $y=-1;

                if(abs($x)>=1)
                    $z=abs($x);
                else if(abs($x)<1)
                    $z=1;

                $t=$post_timestamps[$loop]-1360994400;

                //reddit's algorithm
                $f=intval(log($z))+(($y*$t)/45000);

                $scores[]=$f;
            }
        }


        //sorts scores
        $array_scores=$scores;
        sort($scores, SORT_NUMERIC);


        $temp_posts=array();
        $temp_posts_users_sent=array();
        $temp_post_ids=array();
        $temp_original_post_ids=array();
        $temp_post_timestamps=array();
        $temp_comment_ids=array();
        $temp_likes=array();
        $temp_dislikes=array();


        for($x = 0; $x < sizeof($scores); $x++)
        {
            $number=array_search($scores[$x], $array_scores);
            $array_scores[$number]='';

            $temp_posts[$x]=$posts[$number];
            $temp_posts_users_sent[$x]=$posts_users_sent[$number];
            $temp_post_ids[$x]=$post_ids[$number];
            $temp_original_post_ids[$x]=$original_post_ids[$number];
            $temp_post_timestamps[$x]=$post_timestamps[$number];
            $temp_comment_ids[$x]=$post_comment_ids[$number];
            $temp_likes[$x]=$post_likes[$number];
            $temp_dislikes[$x]=$post_dislikes[$number];
        }

        $posts=$temp_posts;
        $posts_users_sent=$temp_posts_users_sent;
        $post_ids=$temp_post_ids;
        $original_post_ids=$temp_original_post_ids;
        $post_timestamps=$temp_post_timestamps;
        $comment_ids=$temp_comment_ids;
        $likes=$temp_likes;
        $dislikes=$temp_dislikes;



        if(sizeof($scores)>5)
        {
            $temp_scores=array();
            $temp_posts=array();
            $temp_posts_users_sent=array();
            $temp_post_ids=array();
            $temp_original_post_ids=array();
            $temp_post_timestamps=array();
            $temp_likes=array();
            $temp_dislikes=array();
            $temp_comment_ids=array();

            $index=sizeof($scores)-1;


            while(sizeof($temp_scores)<=5)
            {
                $temp_scores[]=$scores[$index];
                $temp_posts[]=$posts[$index];
                $temp_posts_users_sent[]=$posts_users_sent[$index];
                $temp_post_ids[]=$post_ids[$index];
                $temp_original_post_ids[]=$original_post_ids[$index];
                $temp_post_timestamps[]=$post_timestamps[$index];
                $temp_likes[]=$likes[$index];
                $temp_dislikes[]=$dislikes[$index];
                $temp_comment_ids[]=$comment_ids[$index];

                $index--;
            }

            $scores=$temp_scores;
            $posts=$temp_posts;
            $posts_users_sent=$temp_posts_users_sent;
            $post_ids=$temp_post_ids;
            $original_post_ids=$temp_original_post_ids;
            $post_timestamps=$temp_post_timestamps;
            $likes=$temp_likes;
            $dislikes=$temp_dislikes;
            $comment_ids=$temp_comment_ids;
        }


        //gets extra information
        $names=array();
        $profile_pictures=array();
        $badges=array();
        $timestamps=array();
        $timestamp_seconds=array();
        $num_likes=array();
        $num_dislikes=array();
        $has_liked=array();
        $has_disliked=array();
        $num_comments=array();
        for($x = 0; $x < sizeof($posts); $x++)
        {
            //gets names
            $names[]=get_user_name($posts_users_sent[$x]);

            //gets profile pictures
            $profile_pictures[]=get_profile_picture($posts_users_sent[$x]);

            //gets badges
            $badges[]=get_badges($posts_users_sent[$x]);

            //gets timestamps
            $timestamps[]=get_time_since($post_timestamps[$x], $timezone);
            $timestamp_seconds[]=get_time_since_seconds($post_timestamps[$x], $timezone);

            //gets num_comments
            if($comment_ids[$x][0]=='')
                $num_comments[]=0;
            else
                $num_comments[]=sizeof($comment_ids[$x]);



            //gets likes and dislikes
            if($index!=-1)
            {
                //gets num likes

                    $num_likes[]=sizeof($likes[$x]);

                //gets num dislikes

                    $num_dislikes[]=sizeof($dislikes[$x]);

                //gets has liked
                if(in_array($_SESSION['id'], $likes[$x])||$likes[$x][0]==$_SESSION[id])
                    $has_liked[]=true;
                else
                    $has_liked[]=false;

                //gets has disliked
                if(in_array($_SESSION['id'], $dislikes[$x])||$dislikes[$x][0]==$_SESSION[id])
                    $has_disliked[]=true;
                else
                    $has_disliked[]=false;
            }
            else
            {
                $num_likes[]=0;
                $num_dislikes[]=0;
                $has_liked[]=false;
                $has_disliked[]=false;
            }
        }


        $JSON=array();
        $JSON['posts']=$posts;
        $JSON['posts_users_sent']=$posts_users_sent;
        $JSON['post_ids']=$post_ids;
        $JSON['original_post_ids']=$original_post_ids;
        $JSON['names']=$names;
        $JSON['profile_pictures']=$profile_pictures;
        $JSON['timestamps']=$timestamps;
        $JSON['timestamp_seconds']=$timestamp_seconds;
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

//gets top photos
else if($num==2)
{
    $query=mysql_query("SELECT pictures_users_sent, picture_ids, original_picture_ids, picture_descriptions, picture_types, picture_timestamps FROM public WHERE num=1");
    if($query)
    {
        $array=mysql_fetch_row($query);
        $pictures_users_sent=explode('|^|*|', $array[0]);
        $picture_ids=explode('|^|*|', $array[1]);
        $original_picture_ids=explode('|^|*|', $array[2]);
        $picture_descriptions=explode('|^|*|', $array[3]);
        $picture_types=explode('|^|*|', $array[4]);
        $picture_timestamps=explode('|^|*|', $array[5]);
        
        
        $scores=array();
        $temp_picture_users=array();
        $temp_pictures=array();
        $post_likes=array();
        $post_dislikes=array();
        
        
        //gets score for top posts
        for($loop = 0; $loop < sizeof($pictures_users_sent); $loop++)
        {
            if(!in_array($pictures_users_sent[$loop], $temp_picture_users))
            {
                $query=mysql_query("SELECT pictures, picture_likes, picture_dislikes FROM pictures WHERE user_id=$pictures_users_sent[$loop] LIMIT 1");
                if($query&&mysql_num_rows($query)==1)
                {
                    $array=mysql_fetch_row($query);
                    $pictures=explode('|^|*|', $array[0]);
                    $likes=explode('|^|*|', $array[1]);
                    $dislikes=explode('|^|*|', $array[2]);
                    $index=array_search($original_picture_ids[$loop], $pictures);
                    $likes[$index]=explode('|%|&|', $likes[$index]);
                    $dislikes[$index]=explode('|%|&|', $dislikes[$index]);
                    
                    $temp_post_users[]=$posts_users_sent[$loop];
                    $temp_pictures[]=$pictures;
                    $post_likes[]=$likes;
                    $post_dislikes[]=$dislikes;
    
                    $x=sizeof($likes[$index])-sizeof($dislikes[$index]);
                    
                    if($x>0)
                        $y=1;
                    else if($x==0)
                        $y=0;
                    else if($x<0)
                        $y=-1;
                    
                    if(abs($x)>=1)
                        $z=abs($x);
                    else if(abs($x)<1)
                        $z=1;
                    
                    $t=$post_timestamps[$loop]-1360994400;
                    
                    //reddit's algorithm
                    $f=log($z)+(($y*$t)/45000);
                    
                    $scores[]=$f;
                }
                else
                    $scores[]=0;
            }
            else
            {
                $index=array_search($pictures_users_sent[$loop], $temp_picture_users);
                $pictures=$temp_pictures[$index];
                $likes=$post_likes[$index];

                $dislikes=$post_dislikes[$index];
                
                $index=array_search($original_picture_ids[$loop], $pictures);
                
                $likes[$index]=explode('|%|&|', $likes[$index]);
                $dislikes[$index]=explode('|%|&|', $dislikes[$index]);
                
                $x=sizeof($likes[$index])-sizeof($dislikes[$index]);
    
                if($x>0)
                    $y=1;
                else if($x==0)
                    $y=0;
                else if($x<0)
                    $y=-1;

                if(abs($x)>=1)
                    $z=abs($x);
                else if(abs($x)<1)
                    $z=1;

                $t=$post_timestamps[$loop]-1360994400;

                //reddit's algorithm
                $f=intval(log($z))+(($y*$t)/45000);

                $scores[]=$f;
            }
        }
        
        
        //sorts scores
        $array_scores=$scores;
        sort($scores, SORT_NUMERIC);
        
        
        $temp_picture_ids=array();
        $temp_pictures_users_sent=array();
        $temp_original_picture_ids=array();
        $temp_picture_timestamps=array();
        
        
        for($x = 0; $x < sizeof($scores); $x++)
        {
            $number=array_search($scores[$x], $array_scores);
            $array_scores[$number]='';
            
            $temp_picture_ids[$x]=$picture_ids[$number];
            $temp_pictures_users_sent[$x]=$pictures_users_sent[$number];
            $temp_original_picture_ids[$x]=$original_picture_ids[$number];
            $temp_picture_timestamps[$x]=$picture_timestamps[$number];
        }
        
        $picture_ids=$temp_picture_ids;
        $pictures_users_sent=$temp_pictures_users_sent;
        $original_picture_ids=$temp_original_picture_ids;
        $picture_timestamps=$temp_picture_timestamps;
        
        
        if(sizeof($scores)>5)
        {
            $temp_scores=array();
            $temp_picture_ids=array();
            $temp_pictures_users_sent=array();
            $temp_original_picture_ids=array();
            $temp_picture_timestamps=array();
            
            $index=sizeof($scores)-1;
            
            
            while(sizeof($temp_scores)<=5)
            {
                $temp_scores[]=$scores[$index];
                $temp_picture_ids[]=$picture_ids[$index];
                $temp_pictures_users_sent[]=$pictures_users_sent[$index];
                $temp_original_picture_ids[]=$original_picture_ids[$index];
                $temp_picture_timestamps[]=$picture_timestamps[$index];
                
                $index--;
            }
            
            $scores=$temp_scores;
            $picture_ids=$temp_picture_ids;
            $pictures_users_sent=$temp_pictures_users_sent;
            $original_picture_ids=$temp_original_picture_ids;
            $picture_timestamps=$temp_picture_timestamps;
        }
        
        //gets extra information
        $names=array();
        $profile_pictures=array();
        $badges=array();
        $timestamps=array();
        $timestamp_seconds=array();
        $num_likes=array();
        $num_dislikes=array();
        $has_liked=array();
        $has_disliked=array();
        $picture_urls=array();
        $picture_links=array();
        for($x = 0; $x < sizeof($picture_ids); $x++)
        {
            //gets picture url
            $picture_urls[]="http://u.redlay.com/public/photos/".$picture_ids[$x].".".$picture_types[$x];
            
            //gets picture links
            $picture_links[]="http://www.redlay.com/view_photo.php?user_id=".$pictures_users_sent[$x]."&&photo_id=".$original_picture_ids[$x];
            
            //gets names
            $names[]=get_user_name($pictures_users_sent[$x]);
            
            //gets profile pictures
            $profile_pictures[]=get_profile_picture($pictures_users_sent[$x]);
            
            //gets badges
            $badges[]=get_badges($pictures_users_sent[$x]);
            
            //gets timestamps
            $timestamps[]=get_time_since($picture_timestamps[$x], $timezone);
            $timestamp_seconds[]=get_time_since_seconds($picture_timestamps[$x], $timezone);
            
            //gets likes and dislikes
            $query=mysql_query("SELECT pictures, picture_likes, picture_dislikes FROM pictures WHERE user_id=$pictures_users_sent[$x] LIMIT 1");
            if($query&&mysql_num_rows($query)==1)
            {
                $array=mysql_fetch_row($query);
                $temp_picture_ids=explode('|^|*|', $array[0]);
                $temp_likes=explode('|^|*|', $array[1]);
                $temp_dislikes=explode('|^|*|', $array[2]);
                
                $index=array_search($original_picture_ids[$x], $temp_picture_ids);
                
                if($index!=-1)
                {
                    $temp_likes=explode('|%|&|', $temp_likes[$index]);
                    $temp_dislikes=explode('|%|&|', $temp_dislikes[$index]);

                    //gets num likes
                    if($temp_likes[0]==""||$temp_likes[0]=="0")
                        $num_likes[]=0;
                    else
                        $num_likes[]=sizeof($temp_likes);

                    //gets num dislikes
                    if($temp_dislikes[0]==""||$temp_dislikes[0]=="0")
                        $num_dislikes[]=0;
                    else
                        $num_dislikes[]=sizeof($temp_dislikes);

                    //gets has liked
                    if(in_array($_SESSION['id'], $temp_likes)||$temp_likes[0]==$_SESSION[id])
                        $has_liked[]=true;
                    else
                        $has_liked[]=false;

                    //gets has disliked
                    if(in_array($_SESSION['id'], $temp_dislikes)||$temp_dislikes[0]==$_SESSION[id])
                        $has_disliked[]=true;
                    else
                        $has_disliked[]=false;
                }
                else
                {
                    $num_likes[]=0;
                    $num_dislikes[]=0;
                    $has_liked[]=false;
                    $has_disliked[]=false;
                }
                
                
            }
        }
        
        
        $JSON=array();
        $JSON['pictures']=$picture_ids;
        $JSON['pictures_users_sent']=$pictures_users_sent;
        $JSON['original_picture_ids']=$original_picture_ids;
        $JSON['picture_descriptions']=$picture_descriptions;
        $JSON['picture_types']=$picture_types;
        $JSON['picture_urls']=$picture_urls;
        $JSON['picture_links']=$picture_links;
        $JSON['names']=$names;
        $JSON['profile_pictures']=$profile_pictures;
        $JSON['timestamps']=$timestamps;
        $JSON['timestamp_seconds']=$timestamp_seconds;
        $JSON['badges']=$badges;
        $JSON['num_likes']=$num_likes;
        $JSON['num_dislikes']=$num_dislikes;
        $JSON['has_liked']=$has_liked;
        $JSON['has_disliked']=$has_disliked;
        echo json_encode($JSON);
        exit();
    }
}

//gets top videos
else if($num==3)
{
    
}

//gets top everything
else if($num==4)
{
    
}