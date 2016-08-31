<?php
@include('init.php');
if(strstr($_SERVER['SERVER_NAME'], "www")==false)
    include('cross_domain_headers.php');

include('universal_functions.php');
$allowed="users";
include('security_checks.php');


////parameters////
$num=(int)($_POST['num']);
//$num=1;


//home everything
if($num==1)
{
            //parameters
            $ID=(int)($_POST['user_id']);
//            $page_id=(int)($_POST['page_id']);
            $page_id=-1;
            $page=(int)($_POST['page_number'])*30;
            $content_type=clean_string($_POST['content_type']);
//            $user_type=clean_string($_POST['user_type']);
            $user_type='Users';
            $group=clean_string($_POST['group']);
            $timezone=(int)($_POST['timezone']);
            $date=$_POST['date'];
            $sort=clean_string($_POST['sort']);
            
            if(has_redlay_gold($_SESSION['id'])==false)
                $date="Now";
            
            ////converts date
            if(!is_array($date))
            {
                $date=clean_string($date);
                
                //gets current unix timestamp
                $current_date=get_date();
                if($date=='Now')
                {
                    //gets the unix timestamp of right now
                    $date=$current_date;
                }
                else if($date=='Yesterday')
                {
                    //gets the unix timestamp of exactly 1 day ago
                    $day=60*60*24;
                    $date=$current_date-$day;
                }
                else if($date=='A week ago')
                {
                    //gets the unix timestamp of exactly 1 week ago
                    $date=strtotime("-1 week");
                }
                else if($date=='A month ago')
                {
                    //gets the unix timestamp of exactly 1 month ago
                    $date=strtotime("-1 month");
                }
                else if($date=="A year ago")
                {
                    //gets the unix timestamp of eactly 1 year ago
                    $date=strtotime("-1 year");
                    
                }
            }
            
            //if it's a custom date
            else
            {
                $date=strtotime($date[1]." ".$date[0]." ".$date[2]);
                if($date==false)
                {
                    echo "Invalid custom date";
                    exit();
                }
            }
    
//        $ID=-1;
//        $page_id=-1;
//        $page=30;
//        $content_type='Everything';
//        $user_type='Users';
//        $group='Everyone';
//        $timezone=0;


            //if user specifies user id
            if($ID!=-1)
            {
                $friends[0]=$ID;
                $type='user';
            }
            //if user specifies page id
            else if($page_id!=-1)
            {
                $pages[0]=$page_id;
                $type='page';
            }
            //if user doesn't specify user or page id
            else if($ID==-1&&$page_id==-1)
            {
                //if user wants users
                if($user_type=='Users')
                {
                    //if user doesn't want specific group
                    if($group!='Everyone')
                        $friends=get_users_from_group($group);
                    //gets all users in specific group
                    else
                    {
                        $friends=get_friends($_SESSION['id']);
                        $friends[]=$_SESSION['id'];
                    }
                    $type='user';
                }
                //if user wants pages
                else if($user_type=='Pages')
                {
                    $pages=get_page_likes($_SESSION['id']);
                    $type='page';
                }
                //if user wants both users and pages
                else if($user_type=='All')
                {
                    //gets all adds
                    $friends=get_friends($_SESSION['id']);
                    $friends[]=$_SESSION['id'];

                    //gets all pages
                    $pages=get_page_likes($_SESSION['id']);

                    $type='all';
                }
            }
            
            $user_ids_terminated=array();
                $users_terminated=array();
                
                $scores=array();

                $num=0;
                if($type=='user'||$type=='all')
                {
                    if($content_type=='Posts'||$content_type=='Everything')
                    {
                        for($x = 0; $x < sizeof($friends); $x++)
                        {
                            $query=mysql_query("SELECT post_ids, post_groups, posts, user_ids_posted, comments, likes, dislikes, timestamps, comment_likes, comment_dislikes, comments_user_id, comment_timestamps, comment_ids FROM content WHERE user_id=$friends[$x] LIMIT 1");
                            if($query&&mysql_num_rows($query)==1)
                            {
                                $array=mysql_fetch_row($query);
                                $post_ids=explode('|^|*|', $array[0]);
                                $audiences=explode('|^|*|', $array[1]);
                                $posts=explode('|^|*|', ($array[2]));
                                $user_ids_posted=explode('|^|*|', $array[3]);
                                $comments=explode('|^|*|', ($array[4]));
                                $likes=explode('|^|*|', $array[5]);
                                $dislikes=explode('|^|*|', $array[6]);
                                $timestamps=explode('|^|*|', $array[7]);

                                $comment_likes=explode('|^|*|', $array[8]);
                                $comment_dislikes=explode('|^|*|', $array[9]);
                                $comments_users_sent=explode('|^|*|', $array[10]);
                                $comment_timestamps=explode('|^|*|', $array[11]);
                                $comment_ids=explode('|^|*|', $array[12]);
                                
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
                                $temp_comments_users_sent=array();
                                $temp_comment_timestamps=array();
                                $temp_comment_ids=array();
                                
//                                $user_ids_terminated=array();
                                for($y = 0;  $y < sizeof($post_ids); $y++)
                                {
                                    $temp_index=array_search($user_ids_posted[$y], $user_ids_terminated);
                                    if($temp_index===false)
                                    {
                                        $user_ids_terminated[]=$user_ids_posted[$y];
                                        $temp_index=sizeof($user_ids_terminated)-1;
                                        if(user_id_terminated($user_ids_posted[$y]))
                                            $users_terminated[]=1;
                                        else
                                            $users_terminated[]=0;
                                    }
                                    
                                    if($users_terminated[$temp_index]==0)
                                    {
                                        $temp_post_ids[]=$post_ids[$y];
                                        $temp_audiences[]=$audiences[$y];
                                        $temp_posts[]=$posts[$y];
                                        $temp_user_ids_posted[]=$user_ids_posted[$y];
                                        $temp_timestamps[]=$timestamps[$y];
                                        
                                        //gets rid of likes from terminated accounts
                                        $likes[$y]=explode('|%|&|', $likes[$y]);
                                        $temptemp_likes=array();
                                        if($likes[$y][0]!='0'&&$likes[$y][0]!='')
                                        {
                                            for($z = 0; $z < sizeof($likes[$y]); $z++)
                                            {
                                                $temp_index=array_search($likes[$y][$z], $user_ids_terminated);
                                                if($temp_index===false)
                                                {
                                                    $user_ids_terminated[]=$likes[$y][$z];
                                                    $temp_index=sizeof($user_ids_terminated)-1;
                                                    if(user_id_terminated($likes[$y][$z]))
                                                        $users_terminated[]=1;
                                                    else
                                                        $users_terminated[]=0;
                                                }
                                                
                                                if($likes[$y][$z]!='0'&&$likes[$y][$z]!=''&&$users_terminated[$temp_index]==0)
                                                    $temptemp_likes[]=$likes[$y][$z];
                                                else
                                                    $changed=true;
                                            }
                                        }
                                        if($temptemp_likes[0]==NULL)
                                            $temptemp_likes=0;
                                        $temp_likes[]=$temptemp_likes;
                                        
                                        //gets rid of dislikes from terminated accounts
                                        $dislikes[$y]=explode('|%|&|', $dislikes[$y]);
                                        $temptemp_dislikes=array();
                                        if($dislikes[$y][0]!=''&&$dislikes[$y][0]!='0')
                                        {
                                            for($z = 0; $z < sizeof($dislikes[$y]); $z++)
                                            {
                                                $temp_index=array_search($dislikes[$y][$z], $user_ids_terminated);
                                                if($temp_index===false)
                                                {
                                                    $user_ids_terminated[]=$dislikes[$y][$z];
                                                    $temp_index=sizeof($user_ids_terminated)-1;
                                                    if(user_id_terminated($dislikes[$y][$z]))
                                                        $users_terminated[]=1;
                                                    else
                                                        $users_terminated[]=0;
                                                }
                                                if($dislikes[$y][$z]!='0'&&$dislikes[$y][$z]!=''&&$users_terminated[$temp_index]==0)
                                                    $temptemp_dislikes[]=$dislikes[$y][$z];
                                                else
                                                    $changed=true;
                                            }
                                        }
                                        if($temptemp_dislikes[0]==NULL)
                                            $temptemp_dislikes=0;
                                        $temp_dislikes[]=$temptemp_dislikes;
                                        
                                        //explodes comment stuff
                                        $comment_ids[$y]=explode('|%|&|', $comment_ids[$y]);
                                        $comments[$y]=explode('|%|&|', $comments[$y]);
                                        $comment_likes[$y]=explode('|%|&|', $comment_likes[$y]);
                                        $comment_dislikes[$y]=explode('|%|&|', $comment_dislikes[$y]);
                                        $comments_users_sent[$y]=explode('|%|&|', $comments_users_sent[$y]);
                                        $comment_timestamps[$y]=explode('|%|&|', $comment_timestamps[$y]);
                                        
                                        //gets rid of comments delete accounts
                                        $temptemp_comments=array();
                                        $temptemp_comments_users_sent=array();
                                        $temptemp_comment_ids=array();
                                        $temptemp_comment_timestamps=array();
                                        for($z = 0; $z < sizeof($comment_ids[$y]); $z++)
                                        {
                                            $temp_index=array_search($comments_users_sent[$y][$z], $user_ids_terminated);
                                            if($temp_index===false)
                                            {
                                                $user_ids_terminated[]=$comments_users_sent[$y][$z];
                                                $temp_index=sizeof($user_ids_terminated)-1;
                                                if(user_id_terminated($comments_users_sent[$y][$z]))
                                                    $users_terminated[]=1;
                                                else
                                                    $users_terminated[]=0;
                                            }
                                            
                                            if($comments_users_sent[$y][$z]!=''&&$users_terminated[$temp_index]==0)
                                            {
                                                $temptemp_comments[]=$comments[$y][$z];
                                                $temptemp_comments_users_sent[]=$comments_users_sent[$y][$z];
                                                $temptemp_comment_ids[]=$comment_ids[$y][$z];
                                                $temptemp_comment_timestamps[]=$comment_timestamps[$y][$z];
                                                
                                                //gets rid of comment likes of terminated accounts
                                                if($comment_likes[$y][$z]!='')
                                                {
                                                    $comment_likes[$y][$z]=explode('|@|$|', $comment_likes[$y][$z]);
                                                }

                                                //gets rid of comment dislikes of terminated accounts
                                                if($comment_dislikes[$y][$z]!='')
                                                {
                                                    $comment_dislikes[$y][$z]=explode('|@|$|', $comment_dislikes[$y][$z]);
                                                }
                                            }
                                            else if(user_id_terminated($comments_users_sent[$y][$z]))
                                                $changed=true;
                                        }

                                        if($temptemp_comments[0]!=NULL)
                                        {
                                            $temp_comments[]=$temptemp_comments;
                                            $temp_comments_users_sent[]=$temptemp_comments_users_sent;
                                            $temp_comment_ids[]=$temptemp_comment_ids;
                                            $temp_comment_timestamps[]=$temptemp_comment_timestamps;
                                        }
                                        else
                                        {
                                            $temp_comments[]=array();
                                            $temp_comments_users_sent[]=array();
                                            $temp_comment_ids[]=array();
                                            $temp_comment_timestamps[]=array();
                                        }
                                    }
                                    else
                                        $changed=true;
//                                    else
//                                    {
//                                        if(!in_array($user_ids_posted[$y], $user_ids_terminated))
//                                            $user_ids_terminated[]=$user_ids_posted[$y];
//                                        $changed=true;
//                                    }
                                }
                                
                                if($changed)
                                {
                                    $post_ids=$temp_post_ids;
                                    $audiences=$temp_audiences;
                                    $posts=$temp_posts;
                                    $user_ids_posted=$temp_user_ids_posted;
                                    $comments=$temp_comments;
                                    $likes=$temp_likes;
                                    $dislikes=$temp_dislikes;
                                    $timestamps=$temp_timestamps;
//                                    $comment_likes=$temp_comment_likes;
//                                    $comment_dislikes=$temp_comment_dislikes;
                                    $comments_users_sent=$temp_comments_users_sent;
                                    $comment_timestamps=$temp_comment_timestamps;
                                    $comment_ids=$temp_comment_ids;
                                    
                                }
                                


                                $images=array();
                                $image_descriptions=array();
                                for($y = 0; $y < sizeof($timestamps); $y++)
                                {
                                    $images[$y]='';
                                    $image_descriptions[$y]='';
                                    $user_post_kind[$y]='';
                                }

                                $user_audience=$user_audience=get_audience_current_user($friends[$x]);
                                $user_is_friends=user_is_friends($friends[$x], $_SESSION['id']);
                                
                                for($y = 0; $y < sizeof($timestamps); $y++)
                                {
                                    if($user_is_friends=='true'||$friends[$x]==$_SESSION['id'])
                                    {
                                        $audiences[$y]=explode('|%|&|', $audiences[$y]);

                                        if(can_view($user_audience, $audiences[$y])||$friends[$x]==$_SESSION['id'])
                                        {
                                            //if it's that user's post and not someone else's on that person's profile
                                            if($user_ids_posted[$y]==$friends[$x])
                                            {
                                                //if sorting by popularity
                                                if($sort=="Popularity")
                                                {
                                                        $temp_x=sizeof($likes[$y])-sizeof($dislikes[$y]);

                                                        if($temp_x>0)
                                                            $temp_y=1;
                                                        else if($temp_x==0)
                                                            $temp_y=0;
                                                        else if($temp_x<0)
                                                            $temp_y=-1;

                                                        if(abs($temp_x)>=1)
                                                            $z=abs($temp_x);
                                                        else if(abs($temp_x)<1)
                                                            $z=1;

                                                        $t=$timestamps[$y]-1360994400;

                                                        //reddit's algorithm
                                                        $f=log($z)+(($temp_y*$t)/45000);

                                                        $scores[$num]=$f;
                                                }
                                                
                                                
                                                
                                                
                                                
                                                  $array_posts[$num]=nl2br($posts[$y]);
                                                  $array_audiences[$num]=$audiences[$y];
                                                  $array_user_ids_posted[$num]=$user_ids_posted[$y];
                                                  $array_post_ids[$num]=$post_ids[$y];
                                                  $array_timestamps[$num]=$timestamps[$y];
                                                  $array_likes[$num]=$likes[$y];
                                                  $array_dislikes[$num]=$dislikes[$y];
                                                  $array_profile_ids[$num]=$friends[$x];
                                                  $array_comments[$num]=$comments[$y];
                                                  $array_comment_ids[$num]=$comment_ids[$y];
                                                  $array_type[$num]='user_post';
                                                  $array_images[$num]=$images[$y];
                                                  $array_image_descriptions[$num]=$image_descriptions[$y];
                                                  $array_image_types[$num]='';
                                                  $array_user_post_kind[$num]=$user_post_kind[$y];
                                                  $array_comment_likes[$num]=$comment_likes[$y];
                                                  $array_comment_dislikes[$num]=$comment_dislikes[$y];
                                                  $array_comments_users_sent[$num]=$comments_users_sent[$y];
                                                  $array_comment_timestamps[$num]=$comment_timestamps[$y];
                                                  $array_other[$num]='';


                                                  $num++;
                                            }
                                       }
                                    }
                                }

                            }
                        }
                    }
                    if($content_type=='Photos'||$content_type=='Everything')
                    {
                        for($x = 0; $x < sizeof($friends); $x++)
                        {
                            $query=mysql_query("SELECT pictures, picture_descriptions, image_audiences, picture_likes, picture_dislikes, picture_comments, timestamp, comment_likes, comment_dislikes, comments_user_sent, comment_timestamps, image_types, comment_ids FROM pictures WHERE user_id=$friends[$x] LIMIT 1");
                            if($query&&mysql_num_rows($query)==1)
                            {
                                $array=mysql_fetch_row($query);
                                $images=explode('|^|*|', $array[0]);
                                $image_descriptions=explode('|^|*|', $array[1]);
                                $audiences=explode('|^|*|', $array[2]);
                                $likes=explode('|^|*|', $array[3]);
                                $dislikes=explode('|^|*|', $array[4]);
                                $timestamps=explode('|^|*|', $array[6]);
                                $image_types=explode('|^|*|', $array[11]);

                                $comments=explode('|^|*|', $array[5]);
                                $comment_likes=explode('|^|*|', $array[7]);
                                $comment_dislikes=explode('|^|*|', $array[8]);
                                $comments_users_sent=explode('|^|*|', $array[9]);
                                $comment_timestamps=explode('|^|*|', $array[10]);
                                $comment_ids=explode('|^|*|', $array[12]);
                                
                                
                                //gets rid of terminated accounts
                                $changed=false;
                                $temp_likes=array();
                                $temp_dislikes=array();
                                $temp_comments=array();
                                $temp_comments_users_sent=array();
                                $temp_comment_timestamps=array();
                                $temp_comment_ids=array();
                                for($y = 0;  $y < sizeof($images); $y++)
                                {
                                        //gets rid of likes from terminated accounts
                                        $likes[$y]=explode('|%|&|', $likes[$y]);
                                        $temptemp_likes=array();
                                        if($likes[$y][0]!='0'&&$likes[$y][0]!='')
                                        {
                                            for($z = 0; $z < sizeof($likes[$y]); $z++)
                                            {
                                                $temp_index=array_search($likes[$y][$z], $user_ids_terminated);
                                                if($temp_index===false)
                                                {
                                                    $user_ids_terminated[]=$likes[$y][$z];
                                                    $temp_index=sizeof($user_ids_terminated)-1;
                                                    if(user_id_terminated($likes[$y][$z]))
                                                        $users_terminated[]=1;
                                                    else
                                                        $users_terminated[]=0;
                                                }
                                                if($likes[$y][$z]!='0'&&$likes[$y][$z]!=''&&$users_terminated[$temp_index]==0)
                                                    $temptemp_likes[]=$likes[$y][$z];
                                            }
                                        }
                                        if($temptemp_likes[0]==NULL)
                                            $temptemp_likes=0;
                                        $temp_likes[]=$temptemp_likes;
                                        
                                        //gets rid of dislikes from terminated accounts
                                        $dislikes[$y]=explode('|%|&|', $dislikes[$y]);
                                        $temptemp_dislikes=array();
                                        if($dislikes[$y][0]!=''&&$dislikes[$y][0]!='0')
                                        {
                                            for($z = 0; $z < sizeof($dislikes[$y]); $z++)
                                            {
                                                $temp_index=array_search($dislikes[$y][$z], $user_ids_terminated);
                                                if($temp_index===false)
                                                {
                                                    $user_ids_terminated[]=$dislikes[$y][$z];
                                                    $temp_index=sizeof($user_ids_terminated)-1;
                                                    if(user_id_terminated($dislikes[$y][$z]))
                                                        $users_terminated[]=1;
                                                    else
                                                        $users_terminated[]=0;
                                                }
                                                if($dislikes[$y][$z]!='0'&&$dislikes[$y][$z]!=''&&$users_terminated[$temp_index]==0)
                                                    $temptemp_dislikes[]=$dislikes[$y][$z];
                                            }
                                        }
                                        if($temptemp_dislikes[0]==NULL)
                                            $temptemp_dislikes=0;
                                        $temp_dislikes[]=$temptemp_dislikes;
                                        
                                        //explodes comment stuff
                                        $comment_ids[$y]=explode('|%|&|', $comment_ids[$y]);
                                        $comments[$y]=explode('|%|&|', $comments[$y]);
                                        $comment_likes[$y]=explode('|%|&|', $comment_likes[$y]);
                                        $comment_dislikes[$y]=explode('|%|&|', $comment_dislikes[$y]);
                                        $comments_users_sent[$y]=explode('|%|&|', $comments_users_sent[$y]);
                                        $comment_timestamps[$y]=explode('|%|&|', $comment_timestamps[$y]);
                                        
                                        //gets rid of comments delete accounts
                                        $temptemp_comments=array();
                                        $temptemp_comments_users_sent=array();
                                        $temptemp_comment_ids=array();
                                        $temptemp_comment_timestamps=array();
                                        for($z = 0; $z < sizeof($comment_ids[$y]); $z++)
                                        {
                                            $temp_index=array_search($comments_users_sent[$y][$z], $user_ids_terminated);
                                            if($temp_index===false)
                                            {
                                                $user_ids_terminated[]=$comments_users_sent[$y][$z];
                                                $temp_index=sizeof($user_ids_terminated)-1;
                                                if(user_id_terminated($comments_users_sent[$y][$z]))
                                                    $users_terminated[]=1;
                                                else
                                                    $users_terminated[]=0;
                                            }
                                            if($comments_users_sent[$y][$z]!=''&&$users_terminated[$temp_index]==0)
                                            {
                                                $temptemp_comments[]=$comments[$y][$z];
                                                $temptemp_comments_users_sent[]=$comments_users_sent[$y][$z];
                                                $temptemp_comment_ids[]=$comment_ids[$y][$z];
                                                $temptemp_comment_timestamps[]=$comment_timestamps[$y][$z];
                                                
                                            }
                                            else
                                                $changed=true;
                                        }
                                   
                                        if($temptemp_comments[0]!=NULL)
                                        {
                                            $temp_comments[]=$temptemp_comments;
                                            $temp_comments_users_sent[]=$temptemp_comments_users_sent;
                                            $temp_comment_ids[]=$temptemp_comment_ids;
                                            $temp_comment_timestamps[]=$temptemp_comment_timestamps;
                                        }
                                        else
                                        {
                                            $temp_comments[]=array();
                                            $temp_comments_users_sent[]=array();
                                            $temp_comment_ids[]=array();
                                            $temp_comment_timestamps[]=array();
                                        }
                                }
                                
                                if($changed)
                                {
                                    $comments=$temp_comments;
                                    $likes=$temp_likes;
                                    $dislikes=$temp_dislikes;
                                    $comments_users_sent=$temp_comments_users_sent;
                                    $comment_timestamps=$temp_comment_timestamps;
                                    $comment_ids=$temp_comment_ids;
                                }
                                
                                
                                for($y = 0; $y < sizeof($images); $y++)
                                {
                                    $post_ids[$y]=$friends[$x];
                                    $posts[$y]='';
                                    $user_ids_posted[$y]=$friends[$x];
                                    $user_post_kind[$y]='';
                                }
                                

//                                $total_photos_validation=0;
                                $user_audience=get_audience_current_user($friends[$x]);
                                $user_is_friends=user_is_friends($friends[$x], $_SESSION['id']);
                                
                                for($y = 0; $y < sizeof($timestamps); $y++)
                                {
                                    
                                    if($user_is_friends=="true"||$friends[$x]==$_SESSION['id'])
                                    {
                                        $audiences[$y]=explode('|%|&|', $audiences[$y]);

                                        if(can_view($user_audience, $audiences[$y])||$friends[$x]==$_SESSION['id'])
                                        {
                                            //if sorting by popularity
                                            if($sort=="Popularity")
                                            {
                                                    $temp_x=sizeof($likes[$y])-sizeof($dislikes[$y]);

                                                    if($temp_x>0)
                                                        $temp_y=1;
                                                    else if($temp_x==0)
                                                        $temp_y=0;
                                                    else if($temp_x<0)
                                                        $temp_y=-1;

                                                    if(abs($temp_x)>=1)
                                                        $z=abs($temp_x);
                                                    else if(abs($temp_x)<1)
                                                        $z=1;

                                                    $t=$timestamps[$y]-1360994400;

                                                    //reddit's algorithm
                                                    $f=log($z)+(($temp_y*$t)/45000);

                                                    $scores[$num]=$f;
                                            }
                                            
                                            
                                              $array_images[$num]=$images[$y];
                                                $array_image_descriptions[$num]=$image_descriptions[$y];
                                                $array_image_types[$num]=$image_types[$y];
                                                $array_likes[$num]=$likes[$y];
                                                $array_dislikes[$num]=$dislikes[$y];
                                                $array_comments[$num]=$comments[$y];
                                                $array_comment_ids[$num]=$comment_ids[$y];
                                                $array_timestamps[$num]=$timestamps[$y];
                                                $array_type[$num]='user_photo';
                                                $array_profile_ids[$num]=$friends[$x];
                                                $array_post_ids[$num]=$post_ids[$y];
                                                $array_posts[$num]=$posts[$y];
                                                $array_user_ids_posted[$num]=$user_ids_posted[$y];
                                                $array_user_post_kind[$num]=$user_post_kind[$y];
                                                $array_audiences[$num]=$audiences[$y];
                                                $array_comment_likes[$num]=$comment_likes[$y];
                                              $array_comment_dislikes[$num]=$comment_dislikes[$y];
                                              $array_comments_users_sent[$num]=$comments_users_sent[$y];
                                              $array_comment_timestamps[$num]=$comment_timestamps[$y];
                                              $array_other[$num]='';

                                                $num++;
                                       }
                                    }
                                }
                            }
                        }
                        
                    }
                    
                    if($content_type=="Videos"||$content_type=="Everything")
                    {
                        for($x = 0; $x < sizeof($friends); $x++)
                        {
                            //gets videos
                            $query=mysql_query("SELECT video_ids, videos, video_types, video_audience, video_likes, video_dislikes, video_comment_ids, video_comments, video_comment_likes, video_comment_dislikes, video_comments_users_sent, video_comment_timestamps, video_timestamps FROM content WHERE user_id=$friends[$x] LIMIT 1");
                            if($query&&mysql_num_rows($query)==1)
                            {
                                $array=mysql_fetch_row($query);
                                $video_ids=explode('|^|*|', $array[0]);
                                $videos=explode('|^|*|', $array[1]);
                                $video_types=explode('|^|*|', $array[2]);
                                $audiences=explode('|^|*|', $array[3]);
                                $likes=explode('|^|*|', $array[4]);
                                $dislikes=explode('|^|*|', $array[5]);
                                $comment_ids=explode('|^|*|', $array[6]);
                                $comments=explode('|^|*|', $array[7]);
                                $comment_likes=explode('|^|*|', $array[8]);
                                $comment_dislikes=explode('|^|*|', $array[9]);
                                $comments_users_sent=explode('|^|*|', $array[10]);
                                $comment_timestamps=explode('|^|*|', $array[11]);
                                $timestamps=explode('|^|*|', $array[12]);
                                
                                
                                for($y = 0; $y < sizeof($comments); $y++)
                                {
                                    $comments[$y]=explode('|%|&|', $comments[$y]);
                                    $comment_ids[$y]=explode('|%|&|', $comment_ids[$y]);
                                    $audiences[$y]=explode('|%|&|', $audiences[$y]);
                                    $likes[$y]=explode('|%|&|', $likes[$y]);
                                    $dislikes[$y]=explode('|%|&|', $dislikes[$y]);
                                    $comment_likes[$y]=explode('|%|&|', $comment_likes[$y]);
                                    $comment_dislikes[$y]=explode('|%|&|', $comment_dislikes[$y]);
                                    $comments_users_sent[$y]=explode('|%|&|', $comments_users_sent[$y]);
                                    $comment_timestamps[$y]=explode('|%|&|', $comment_timestamps[$y]);
                                    
                                    for($z = 0; $z < sizeof($comment_likes[$y]); $z++)
                                    {
                                        $comment_likes[$y][$z]=explode('|@|$|', $comment_likes[$y][$z]);
                                        $comment_dislikes[$y][$z]=explode('|@|$|', $comment_dislikes[$y][$z]);
                                    }
                                }
                                
                                
                                $prev_num=$num;

                                if($array[0]!=''&&strstr($_SERVER['SERVER_NAME'], "www")==true)
                                {
                                    for($y = 0; $y < sizeof($videos); $y++)
                                    {
                                        $array_other[$num]=array();
                                        $array_other[$num]['video_preview']=get_video_preview($videos[$y], $video_types[$y]);
                                        $array_other[$num]['video_url']=convert_video($videos[$y], $video_types[$y]);
                                        $array_other[$num]['video_id']=$video_ids[$y];


                                        $array_timestamps[$num]=$timestamps[$y];
                                        $array_type[$num]='video';

                                        $num++;
                                    }
                                }
                                
                                for($y = 0; $y < sizeof($likes); $y++)
                                {
                                    //if sorting by popularity
                                    if($sort=="Popularity")
                                    {
                                        $temp_x=sizeof($likes[$y])-sizeof($dislikes[$y]);

                                        if($temp_x>0)
                                            $temp_y=1;
                                        else if($temp_x==0)
                                            $temp_y=0;
                                        else if($temp_x<0)
                                            $temp_y=-1;

                                        if(abs($temp_x)>=1)
                                            $z=abs($temp_x);
                                        else if(abs($temp_x)<1)
                                            $z=1;

                                        $t=$timestamps[$y]-1360994400;

                                        //reddit's algorithm
                                        $f=log($z)+(($temp_y*$t)/45000);

                                        $scores[$num]=$f;
                                    }
                                    
                                      $array_posts[$prev_num]='';
                                      $array_audiences[$prev_num]='';
                                      $array_user_ids_posted[$prev_num]=$friends[$x];
                                      $array_post_ids[$prev_num]='';
                                      $array_likes[$prev_num]=$likes[$y];
                                      $array_dislikes[$prev_num]=$dislikes[$y];
                                      $array_profile_ids[$prev_num]=$friends[$x];
                                      $array_comment_ids[$prev_num]=$comment_ids[$y];
                                      $array_comments[$prev_num]=$comments[$y];
                                      $array_images[$prev_num]='';
                                      $array_image_descriptions[$prev_num]='';
                                      $array_image_types[$prev_num]='';
                                      $array_user_post_kind[$prev_num]='';
                                      $array_comment_likes[$prev_num]=$comment_likes[$y];
                                      $array_comment_dislikes[$prev_num]=$comment_dislikes[$y];
                                      $array_comments_users_sent[$prev_num]=$comments_users_sent[$y];
                                      $array_comment_timestamps[$prev_num]=$comment_timestamps[$y];
                                      
                                      $prev_num++;
                                }
                            }
                        }
                    }
                    
                    if($content_type=="Others"||$content_type=="Everything")
                    {
                        for($x = 0; $x < sizeof($friends); $x++)
                        {
                            $query=mysql_query("SELECT user_friends, friend_timestamps, user_relationship, relationship_timestamp, user_mood, mood_timestamp, page_likes, page_likes_timestamps, redlay_gold FROM user_data WHERE user_id=$friends[$x] LIMIT 1");
                            if($query&&mysql_num_rows($query)==1)
                            {
                                $array=mysql_fetch_row($query);

                                $adds=explode('|^|*|', $array[0]);
                                $add_timestamps=explode('|^|*|', $array[1]);
                                $relationship=$array[2];
                                $relationship_timestamp=$array[3];
                                $mood=$array[4];
                                $mood_timestamp=$array[5];
                                $page_likes=explode('|^|*|', $array[6]);
                                $page_likes_timestamps=explode('|^|*|', $array[7]);
                                $redlay_gold=explode('|^|*|', $array[8]);




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

                                if($array[6]!='')
                                {
                                    for($y = 0; $y < sizeof($page_likes); $y++)
                                    {
                                        $array_other[$num]=$page_likes[$y];
                                        $array_timestamps[$num]=$page_likes_timestamps[$y];
                                        $array_type[$num]='page_like';

                                        $num++;
                                    }
                                }

                                if($array[3]!=''&&($relationship!='NA'))
                                {
                                    //adds relationship
                                    $array_other[$num]=$relationship;
                                    $array_timestamps[$num]=$relationship_timestamp;
                                    $array_type[$num]='relationship';

                                    $num++;
                                }

                                if($array[5]!='')
                                {
                                    //adds mood
                                    $array_other[$num]=$mood;
                                    $array_timestamps[$num]=$mood_timestamp;
                                    $array_type[$num]='mood';

                                    $num++;
                                }

                                if($array[8]!='')
                                {
                                    $redlay_gold[2]=explode('|%|&|', $redlay_gold[2]);
                                    $newest=0;
                                    for($z = 0; $z < sizeof($redlay_gold[2]); $z++)
                                    {
                                        if($redlay_gold[2][$z]>$newest)
                                            $newest=$redlay_gold[2][$z];
                                    }
                                    
                                    //adds redlay gold
                                    $array_other[$num]='';
                                    $array_timestamps[$num]=$newest;
                                    $array_type[$num]='redlay_gold';

                                    $num++;
                                }
                                
                                for($y = $prev_num; $y < $num; $y++)
                                {
                                    //if sorting by popularity
                                    if($sort=="Popularity")
                                    {
                                        //sets to 0 because "others" don't really have a score... yet
                                            $scores[$y]=0;
                                    }
                                    
                                      $array_posts[$y]='';
                                      $array_audiences[$y]='';
                                      $array_user_ids_posted[$y]=$friends[$x];
                                      $array_post_ids[$y]='';
                                      $array_likes[$y]='';
                                      $array_dislikes[$y]='';
                                      $array_profile_ids[$y]=$friends[$x];
                                      $array_comment_ids[$y]='';
                                      $array_comments[$y]='';
                                      $array_images[$y]='';
                                      $array_image_descriptions[$y]='';
                                      $array_image_types[$y]='';
                                      $array_user_post_kind[$y]='';
                                      $array_comment_likes[$y]='';
                                      $array_comment_dislikes[$y]='';
                                      $array_comments_users_sent[$y]='';
                                      $array_comment_timestamps[$y]='';
                                }
                            }
                        }
                    }
                }
                
                
                if($sort=="Popularity")
                {
                    $array_scores=$scores;
                    sort($scores, SORT_NUMERIC);
                }   
                else
                {
                    //sorts things in chronological order
                    $array_timestamps2=$array_timestamps;
                    sort($array_timestamps, SORT_NUMERIC);
                }
                

                
                //creates temporary data of arrays for future use
                $temp_array_posts=$array_posts;
                $temp_array_audiences=$array_audiences;
                $temp_array_image_types=$array_image_types;
                $temp_array_user_ids_posted=$array_user_ids_posted;
                $temp_array_post_ids=$array_post_ids;
                $temp_array_likes=$array_likes;
                $temp_array_dislikes=$array_dislikes;
                $temp_array_profile_ids=$array_profile_ids;
                $temp_array_comment_ids=$array_comment_ids;
                $temp_array_comments=$array_comments;
                $temp_array_type=$array_type;
                $temp_array_images=$array_images;
                $temp_array_image_descriptions=$array_image_descriptions;
                $temp_array_comment_likes=$array_comment_likes;
                $temp_array_comment_dislikes=$array_comment_dislikes;
                $temp_array_comments_users_sent=$array_comments_users_sent;
                $temp_array_comment_timestamps=$array_comment_timestamps;
                $temp_array_other=$array_other;
                
                if($sort=="Popularity")
                    $temp_array_timestamps=$array_timestamps;


                $user_type=array();
                $image_types=array();

                
                $posts=array();
                $audiences=array();
                $image_types=array();
                $user_ids_posted=array();
                $post_ids=array();
                $likes=array();
                $dislikes=array();
                $profile_ids=array();
                $comment_ids=array();
                $comments=array();
                $user_type=array();
                $images=array();
                $image_descriptions=array();
                $comment_likes=array();
                $comment_dislikes=array();
                $comments_users_sent=array();
                $comment_timestamps=array();
                $other=array();
                
                if($sort=="Popularity")
                    $array_timestamps=array();
                
                
                //rearranges rest of data according to sorted timestamps compared to previously unsorted timestamps
                for($x = 0; $x < $num; $x++)
                {
                    if($array_timestamps[$x]<=$date||$sort=="Popularity")
                    {
                        if(isset($scores[$x])||$sort!="Popularity")
                        {
                            if($sort=="Popularity")
                            {
                                $number=array_search($scores[$x], $array_scores);
                                $array_scores[$number]=-99999;
                            }
                            else
                            {
                                $number=array_search($array_timestamps[$x], $array_timestamps2);
                                $array_timestamps2[$number]='';
                            }


                            $posts[$x]=$temp_array_posts[$number];
                            $audiences[$x]=$temp_array_audiences[$number];
                            $image_types[$x]=$temp_array_image_types[$number];
                            $user_ids_posted[$x]=$temp_array_user_ids_posted[$number];
                            $post_ids[$x]=$temp_array_post_ids[$number];
                            $likes[$x]=$temp_array_likes[$number];
                            $dislikes[$x]=$temp_array_dislikes[$number];
                            $profile_ids[$x]=$temp_array_profile_ids[$number];
                            $comment_ids[$x]=$temp_array_comment_ids[$number];
                            $comments[$x]=$temp_array_comments[$number];
                            $user_type[$x]=$temp_array_type[$number];
                            $images[$x]=$temp_array_images[$number];
                            $image_descriptions[$x]=$temp_array_image_descriptions[$number];
                            $comment_likes[$x]=$temp_array_comment_likes[$number];
                            $comment_dislikes[$x]=$temp_array_comment_dislikes[$number];
                            $comments_users_sent[$x]=$temp_array_comments_users_sent[$number];
                            $comment_timestamps[$x]=$temp_array_comment_timestamps[$number];
                            $other[$x]=$temp_array_other[$number];
                            
                            if($sort=="Popularity")
                                $array_timestamps[$x]=$temp_array_timestamps[$number];
                        }
                    }
                }
                
                //gets timestamps
                $timestamps=array();
                $timestamps_seconds=array();
                $comment_timestamps_seconds=array();
                for($x = 0; $x < sizeof($user_ids_posted); $x++)
                {
                    $timestamps[$x]=get_time_since($array_timestamps[$x], $timezone);
                    $timestamps_seconds[$x]=get_time_since_seconds($array_timestamps[$x], $timezone);
                    
                    for($y = 0; $y < sizeof($comment_timestamps[$x]); $y++)
                    {
                        if($comment_timestamps[$x][$y]!='')
                        {
                            $temp_timestamp=$comment_timestamps[$x][$y];
                            $comment_timestamps[$x][$y]=get_time_since($comment_timestamps[$x][$y], $timezone);
                            $comment_timestamps_seconds[$x][$y]=get_time_since_seconds($temp_timestamp, $timezone);
                        }
                        else
                        {
                            $comment_timestamps[$x][$y]='';
                            $comment_timestamps_seconds[$x][$y]='';
                        }
                    }
                }
                
                ////////////////converts a group of photos into group_photo
                if($content_type!='Photos')
                {
                    //final_10 is images
                    $longest=array();
                    $index=0;
                    for($x = 0; $x < sizeof($user_type); $x++)
                    {
                        if($user_type[$x]=='user_photo')
                        {
                            $longest[$index][]=$x;
                            
                            //if there are no more photos by the same user
                            if((isset($user_type[$x+1])&&$user_type[$x+1]!='user_photo')||$user_ids_posted[$x]!=$user_ids_posted[$x+1])
                                $index++;
                        }
                    }


                    //sets all grouped photos to 'group_photo'
                    for($x = 0; $x < sizeof($longest); $x++)
                    {
                        if(sizeof($longest[$x])>1)
                        {
                            for($y = 0; $y < sizeof($longest[$x]); $y++)
                            {
                                $user_type[$longest[$x][$y]]='group_photo';
                            }
                        }
                    }
                       
                        
                    $temp_posts=array();
                    $temp_audiences=array();
                    $temp_image_types=array();
                    $temp_user_ids_posted=array();
                    $temp_post_ids=array();
                    $temp_likes=array();
                    $temp_dislikes=array();
                    $temp_profile_ids=array();
                    $temp_comments=array();
                    $temp_comment_ids=array();
                    $temp_user_type=array();
                    $temp_images=array();
                    $temp_image_descriptions=array();
                    $temp_other=array();
                    $temp_timestamps=array();
                    $temp_timestamps_seconds=array();
                    $temp_comment_timestamps_seconds=array();
                    $temp_comment_likes=array();
                    $temp_comment_dislikes=array();
                    $temp_comments_users_sent=array();
                    $temp_comment_timestamps=array();

                    $index=0;
                    for($x = 0; $x < sizeof($user_type); $x++)
                    {
                        if($user_type[$x]=='group_photo')
                        {
                            $temp_posts[$index]=$posts[$x];
                            $temp_audiences[$index]=$audiences[$x];
                            $temp_user_ids_posted[$index]=$user_ids_posted[$x];
                            $temp_post_ids[$index]=$post_ids[$x];
                            $temp_likes[$index][]=$likes[$x];
                            $temp_dislikes[$index][]=$dislikes[$x];
                            $temp_profile_ids[$index]=$profile_ids[$x];
                            $temp_comments[$index][]=$comments[$x];
                            $temp_user_type[$index][]=$user_type[$x];
                            $temp_images[$index][]=$images[$x];
                            $temp_image_descriptions[$index][]=$image_descriptions[$x];
                            $temp_timestamps[$index][]=$timestamps[$x];
                            $temp_comment_likes[$index][]=$comment_likes[$x];
                            $temp_comment_dislikes[$index][]=$comment_dislikes[$x];
                            $temp_comments_users_sent[$index][]=$comments_users_sent[$x];
                            $temp_comment_timestamps[$index][]=$comment_timestamps[$x];
                            $temp_other[$index]=$other[$x];
                            $temp_image_types[$index][]=$image_types[$x];
                            $temp_comment_ids[$index][]=$comment_ids[$x];
                            $temp_timestamps_seconds[$index][]=$timestamps_seconds[$x];
                            $temp_comment_timestamps_seconds[$index][]=$comment_timestamps_seconds[$x];

                            if((isset($user_type[$x+1])&&$user_type[$x+1]!='group_photo')||(isset($user_ids_posted[$x+1])&&$user_ids_posted[$x+1]!=$user_ids_posted[$x]))
                                $index++;
                        }
                        else
                        {
                            $temp_posts[$index]=$posts[$x];
                            $temp_audiences[$index]=$audiences[$x];
                            $temp_user_ids_posted[$index]=$user_ids_posted[$x];
                            $temp_post_ids[$index]=$post_ids[$x];
                            $temp_likes[$index]=$likes[$x];
                            $temp_dislikes[$index]=$dislikes[$x];
                            $temp_profile_ids[$index]=$profile_ids[$x];
                            $temp_comments[$index]=$comments[$x];
                            $temp_user_type[$index]=$user_type[$x];
                            $temp_images[$index]=$images[$x];
                            $temp_image_descriptions[$index]=$image_descriptions[$x];
                            $temp_timestamps[$index]=$timestamps[$x];
                            $temp_comment_likes[$index]=$comment_likes[$x];
                            $temp_comment_dislikes[$index]=$comment_dislikes[$x];
                            $temp_comments_users_sent[$index]=$comments_users_sent[$x];
                            $temp_comment_timestamps[$index]=$comment_timestamps[$x];
                            $temp_other[$index]=$other[$x];
                            $temp_image_types[$index]=$image_types[$x];
                            $temp_comment_ids[$index]=$comment_ids[$x];
                            $temp_timestamps_seconds[$index]=$timestamps_seconds[$x];
                            $temp_comment_timestamps_seconds[$index]=$comment_timestamps_seconds[$x];
                            $index++;
                        }
                    }


                    $posts=$temp_posts;
                    $audiences=$temp_audiences;
                    $user_ids_posted=$temp_user_ids_posted;
                    $post_ids=$temp_post_ids;
                    $likes=$temp_likes;
                    $dislikes=$temp_dislikes;
                    $profile_ids=$temp_profile_ids;
                    $comments=$temp_comments;
                    $user_type=$temp_user_type;
                    $images=$temp_images;
                    $image_descriptions=$temp_image_descriptions;
                    $timestamps=$temp_timestamps;
                    $comment_likes=$temp_comment_likes;
                    $comment_dislikes=$temp_comment_dislikes;
                    $comments_users_sent=$temp_comments_users_sent;
                    $comment_timestamps=$temp_comment_timestamps;
                    $other=$temp_other;
                    $image_types=$temp_image_types;
                    $comment_ids=$temp_comment_ids;
                    $timestamps_seconds=$temp_timestamps_seconds;
                    $comment_timestamps_seconds=$temp_comment_timestamps_seconds;
                }
                
                //gets the total number of posts
                $total_size=$num;


                $array_posts=array();
                $array_audiences=array();
                $array_user_ids_posted=array();
                $array_post_ids=array();
                $array_likes=array();
                $array_dislikes=array();
                $array_profile_ids=array();
                $array_comments=array();
                $array_comment_ids=array();
                $array_type=array();
                $array_images=array();
                $array_image_types=array();
                $array_image_descriptions=array();
                $array_timestamps=array();
                $array_comment_likes=array();
                $array_comment_dislikes=array();
                $array_comments_users_sent=array();
                $array_comment_timestamps=array();
                $array_comment_timestamp_seconds=array();
                $array_other=array();

                if($total_size<=30)
                {
                    //adds elements in reverse order to be sorted in chronological order
                    $empty=true;
                    for($x = sizeof($timestamps)-1; $x >=0 ; $x--)
                    {
                        $array_posts[]=$posts[$x];
                        $array_audiences[]=$audiences[$x];
                        $array_image_types[]=$image_types[$x];
                        $array_user_ids_posted[]=$user_ids_posted[$x];
                        $array_post_ids[]=$post_ids[$x];
                        $array_likes[]=$likes[$x];
                        $array_dislikes[]=$dislikes[$x];
                        $array_profile_ids[]=$profile_ids[$x];
                        $array_comments[]=$comments[$x];
                        $array_comment_ids[]=$comment_ids[$x];
                        $array_type[]=$user_type[$x];
                        $array_images[]=$images[$x];
                        $array_image_descriptions[]=$image_descriptions[$x];
                        $array_timestamps[]=$timestamps[$x];
                        $array_timestamp_seconds[]=$timestamps_seconds[$x];
                        $array_comment_timestamp_seconds[]=$comment_timestamps_seconds[$x];
                        $array_comment_likes[]=$comment_likes[$x];
                        $array_comment_dislikes[]=$comment_dislikes[$x];
                        $array_comments_users_sent[]=$comments_users_sent[$x];
                        $array_comment_timestamps[]=$comment_timestamps[$x];
                        $array_other[]=$other[$x];
                    }

                    //takes the <10 size of current array and makes it an even 10
                    for($x =0; $x < 30; $x++)
                    {
                        $final_1[]=$array_posts[$x];
                        $final_2[]=$array_audiences[$x];
                        $final_3[]=$array_user_ids_posted[$x];
                        $final_4[]=$array_post_ids[$x];
                        $final_5[]=$array_likes[$x];
                        $final_6[]=$array_dislikes[$x];
                        $final_7[]=$array_profile_ids[$x];
                        $final_8[]=$array_comments[$x];
                        $final_9[]=$array_type[$x];
                        $final_10[]=$array_images[$x];
                        $final_11[]=$array_image_descriptions[$x];
                        $final_12[]=$array_timestamps[$x];
                        $final_13[]=$array_comment_likes[$x];
                        $final_14[]=$array_comment_dislikes[$x];
                        $final_15[]=$array_comments_users_sent[$x];
                        $final_16[]=$array_comment_timestamps[$x];
                        $final_17[]=$array_other[$x];
                        $final_18[]=$array_image_types[$x];
                        $final_19[]=$array_comment_ids[$x];
                        $final_20[]=$array_timestamp_seconds[$x];
                        $final_21[]=$array_comment_timestamp_seconds[$x];
                    }
                    $size=$num;
                }
                else
                {
                    $temp=sizeof($timestamps)-$page;
                    if($page>=$total_size)
                    {
                        $temp=sizeof($timestamps)%30;
                        $empty=true;
                        $start=$temp-1;
                        $end=0;
                        $size=$total_size%30;
                    }
                    else
                    {
                        $empty=false;
                        $end=$temp;
                        $start=$temp+29;
                        $size=30;
                    }

                    //reverses posts to be in chronological order
                    for($x = $start; $x >= $end; $x--)
                    {
                        $final_1[]=$posts[$x];
                        $final_2[]=$audiences[$x];
                        $final_3[]=$user_ids_posted[$x];
                        $final_4[]=$post_ids[$x];
                        $final_5[]=$likes[$x];
                        $final_6[]=$dislikes[$x];
                        $final_7[]=$profile_ids[$x];
                        $final_8[]=$comments[$x];
                        $final_9[]=$user_type[$x];
                        $final_10[]=$images[$x];
                        $final_11[]=$image_descriptions[$x];
                        $final_12[]=$timestamps[$x];
                        $final_13[]=$comment_likes[$x];
                        $final_14[]=$comment_dislikes[$x];
                        $final_15[]=$comments_users_sent[$x];
                        $final_16[]=$comment_timestamps[$x];
                        $final_17[]=$other[$x];
                        $final_18[]=$image_types[$x];
                        $final_19[]=$comment_ids[$x];
                        $final_20[]=$timestamps_seconds[$x];
                        $final_21[]=$comment_timestamps_seconds[$x];
                    }
                }
                
                $names=array();
                $num_likes=array();
                $num_dislikes=array();
                $num_comment_likes=array();
                $num_comment_dislikes=array();
                $has_liked=array();
                $has_disliked=array();
                $has_liked_comments=array();
                $has_disliked_comments=array();
                $comment_names=array();
                $other_names=array();
                $profile_pictures=array();
                $other_profile_pictures=array();
                $comment_profile_pictures=array();
                $comments_users_list=array();
                $badges=array();
                $comment_badges=array();
                $num_comments=array();
                for($x = 0; $x < sizeof($final_3); $x++)
                {
                    if($final_9[$x]=='user_post'||$final_9[$x]=='user_photo'||$final_9[$x]=="video")
                    {
                        //gets name
                        $names[$x]=get_user_name($final_3[$x]);
                        $other_names[$x]='';
                        
                        //gets profile picture
                        $profile_pictures[$x]=get_profile_picture($final_3[$x]);
                        
                        $other_profile_pictures[$x]='';
                        
                        //gets badges
                        $badges[$x]=get_badges($final_3[$x]);
                        
                        //gets num likes
                        if($final_5[$x][0]=='0'||$final_5[$x][0]=='')
                            $num_likes[$x]=0;
                        else
                            $num_likes[$x]=sizeof($final_5[$x]);
                        
                        //gets num dislikes
                        if($final_6[$x][0]=='0'||$final_6[$x][0]=='')
                            $num_dislikes[$x]=0;
                        else
                            $num_dislikes[$x]=sizeof($final_6[$x]);
                        
                        //gets has liked data
                        if(in_array($_SESSION['id'], $final_5[$x])||$final_5[$x][0]==$_SESSION['id'])
                            $has_liked[$x]=true;
                        else
                            $has_liked[$x]=false;
                        
                        
                        //gets has disliked data
                        if(in_array( $_SESSION['id'], $final_6[$x])||$final_6[$x][0]==$_SESSION['id'])
                            $has_disliked[$x]=true;
                        else
                            $has_disliked[$x]=false;
                        
                        //gets num comments
                        if($final_8[$x][0]!='')
                            $num_comments[$x]=sizeof($final_8[$x]);
                        else
                            $num_comments[$x]=0;
                        
                    }
//                    else if($final_9[$x]=='page_post'||$final_9[$x]=='page_photo')
//                    {
//                        $names[$x]=get_page_name($final_3[$x]);
//                        $other_names[$x]='';
//                        
//                        //gets profile picture
//                        $profile_pictures=get_page_profile_picture($final_3[$x]);
//                        
//                        $other_profile_pictures[$x]='';
//                    }
                    else if($final_9[$x]=='add')
                    {
                        $names[$x]=get_user_name($final_7[$x]);
                        $other_names[$x]=get_user_name($final_17[$x]);
                        
                        //gets other profile picture
                        $other_profile_pictures[$x]=get_profile_picture($final_17[$x]);
                        
                        //gets profile picture
                        $profile_pictures[$x]=get_profile_picture($final_7[$x]);
                        
                        //gets badges
                        $badges[$x]=get_badges($final_7[$x]);
                        
                        //sets defaults
                        $has_liked[$x]=false;
                        $has_disliked[$x]=false;
                        $num_likes[$x]=0;
                        $num_dislikes[$x]=0;
                        
                    }
                    else if($final_9[$x]=='page_like')
                    {
                        $names[$x]=get_user_name($final_7[$x]);
                        $other_names[$x]=get_page_name($final_17[$x]);
                        
                        //gets other profile picture
                        $other_profile_pictures[$x]=get_profile_picture($final_17[$x]);
                        
                        //gets profile picture
                        $profile_pictures[$x]=get_profile_picture($final_7[$x]);
                        
                        //sets defaults
                        $has_liked[$x]=false;
                        $has_disliked[$x]=false;
                        $num_likes[$x]=0;
                        $num_dislikes[$x]=0;
                    }
                    else if($final_9[$x]!='group_photo')
                    {
                        if($final_9[$x]=='redlay_gold')
                            $final_17[$x]=get_adjusted_date($final_17[$x], $timezone);
                        
                        $names[$x]=get_user_name($final_7[$x]);
                        $other_names[$x]='';
                        
                        //gets profile picture
                        $profile_pictures[$x]=get_profile_picture($final_7[$x]);
                        
                        $other_profile_pictures[$x]='';
                        $badges[$x]=get_badges($final_7[$x]);
                        
                        //sets defaults
                        $has_liked[$x]=false;
                        $has_disliked[$x]=false;
                        $num_likes[$x]=0;
                        $num_dislikes[$x]=0;
                    }
                    else
                    {
                        $has_liked[$x]=false;
                        $has_disliked[$x]=false;
                        $num_likes[$x]=0;
                        $num_dislikes[$x]=0;
                    }
                    
                    
                    if($final_13[$x]!='')
                    {
                        //gets number of likes and dislikes
                        for($y = 0; $y < sizeof($final_13[$x]); $y++)
                        {
                            //gets badges
                            $comment_badges[$x][$y]=get_badges($final_15[$x][$y]);
                            
                            
                            //gets num comment likes
                            if($final_13[$x][$y]!='')
                            {
                                if($final_13[$x][$y][0]!=''&&$final_13[$x][$y][0]!='0')
                                    $num_comment_likes[$x][$y]=sizeof($final_13[$x][$y]);
                                else
                                    $num_comment_likes[$x][$y]=0;
                            }
                            else
                                $num_comment_likes[$x][$y]=0;

                            //gets num comment dislikes
                            if($final_14[$x][$y]!='')
                            {
                                if($final_14[$x][$y][0]!=''&&$final_14[$x][$y][0]!='0')
                                    $num_comment_dislikes[$x][$y]=sizeof($final_14[$x][$y]);
                                else
                                    $num_comment_dislikes[$x][$y]=0;
                            }
                            else
                                $num_comment_dislikes[$x][$y]=0;
                            
                            //gets has liked comment
                            if($final_13[$x][$y]!='')
                            {
                                $has_liked_comment=false;
                                for($z = 0; $z < sizeof($final_13[$x][$y]); $z++)
                                {
                                    if($final_13[$x][$y][$z]==$_SESSION['id'])
                                    {
                                        $has_liked_comment=true;
                                        $z=sizeof($final_13[$x][$y]);
                                    }
                                }
                                $has_liked_comments[$x][$y]=$has_liked_comment;
                            }
                            else
                                $has_liked_comments[$x][$y]=false;
                            
                            //gets has disliked comment
                            if($final_14[$x][$y]!='')
                            {
                                $has_disliked_comment=false;
                                for($z = 0; $z < sizeof($final_14[$x][$y]); $z++)
                                {
                                    if($final_14[$x][$y][$z]==$_SESSION['id'])
                                    {
                                        $has_disliked_comment=true;
                                        $z=sizeof($final_14[$x][$y]);
                                    }
                                }
                                $has_disliked_comments[$x][$y]=$has_disliked_comment;
                            }
                            else
                                $has_disliked_comments[$x][$y]=false;
                            
                            
                            $index=-1;
                            for($z = 0; $z < sizeof($comments_users_list); $z++)
                            {
                                if($comments_users_list[$z][0]==$final_15[$x][$y])
                                {
                                    $index=$z;
                                    $z=sizeof($comments_users_list);
                                }
                            }
                            
                            if($index!=-1)
                            {
                                $comment_names[$x][$y]=$comments_users_list[$index][1];
                                $comment_profile_pictures[$x][$y]=$comments_users_list[$index][2];
                            }
                            else
                            {
                                if($comments_users_list[0][0]!='')
                                    $comments_users_list[]=array();
                                else
                                    $comments_users_list[0]=array();
                                $comments_users_list[sizeof($comments_users_list)-1][0]=$final_15[$x][$y];

                                //gets comments names
                                $comment_names[$x][$y]=get_user_name($final_15[$x][$y]);
                                $comments_users_list[sizeof($comments_users_list)-1][1]=$comment_names[$x][$y];

                                //gets comment profile pictures
                                $comment_profile_pictures[$x][$y]=get_profile_picture($final_15[$x][$y]);
                                $comments_users_list[sizeof($comments_users_list)-1][2]=$comment_profile_pictures[$x][$y];
                            }
                        }
                        
                        if(sizeof($final_13[$x])==0)
                        {
                            $num_comment_likes[$x][0]=0;
                            $num_comment_dislikes[$x][0]=0;

                            $has_liked_comments[$x][0]=false;
                            $has_disliked_comments[$x][1]=false;
                        }
                    }
                    else
                    {
                        $num_comment_likes[$x][0]=0;
                        $num_comment_dislikes[$x][0]=0;
                        
                        $has_liked_comments[$x][0]=false;
                        $has_disliked_comments[$x][1]=false;
                    }
                }

                $JSON=array();
                $JSON['posts']=$final_1;
                $JSON['audiences']=$final_2;
                $JSON['user_ids_posted']=$final_3;
                $JSON['post_ids']=$final_4;
                $JSON['profile_ids']=$final_7;
                $JSON['comments']=$final_8;
                $JSON['type']=$final_9;
                $JSON['images']=$final_10;
                $JSON['image_descriptions']=$final_11;
                $JSON['timestamps']=$final_12;
                $JSON['timestamp_seconds']=$final_20;
                $JSON['size']=$size;
                $JSON['empty']=$empty;
                $JSON['total_size']=$total_size;
                $JSON['comments_users_sent']=$final_15;
                $JSON['comment_timestamps']=$final_16;
                $JSON['comment_timestamp_seconds']=$final_21;
                $JSON['names']=$names;
                $JSON['comment_names']=$comment_names;
                $JSON['comment_profile_pictures']=$comment_profile_pictures;
                $JSON['other']=$final_17;
                $JSON['other_names']=$other_names;
                $JSON['profile_pictures']=$profile_pictures;
                $JSON['other_profile_pictures']=$other_profile_pictures;
                $JSON['image_types']=$final_18;
                $JSON['comment_ids']=$final_19;
                $JSON['badges']=$badges;
                $JSON['comment_badges']=$comment_badges;
                $JSON['has_liked']=$has_liked;
                $JSON['has_disliked']=$has_disliked;
                $JSON['has_liked_comments']=$has_liked_comments;
                $JSON['has_disliked_comments']=$has_disliked_comments;
                $JSON['num_comment_likes']=$num_comment_likes;
                $JSON['num_comment_dislikes']=$num_comment_dislikes;
                $JSON['num_likes']=$num_likes;
                $JSON['num_dislikes']=$num_dislikes;
                $JSON['num_comments']=$num_comments;
                echo json_encode($JSON);
                exit();

}

//gets all adds online
else if($num==2)
{
    $adds=get_all_friends($_SESSION['id']);
    
    $adds_online=array();
    $profile_pictures=array();
    $names=array();
    $num_adds=array();
    $no_adds=true;
    $types=array();
    for($x = 0; $x < sizeof($adds); $x++)
    {
        $query=mysql_query("SELECT timestamp FROM online WHERE user_id=$adds[$x] LIMIT 1");
        if($query&&mysql_num_rows($query)==1)
        {
            $array=mysql_fetch_row($query);
            $timestamp=explode('|^|*|', $array[0]);
            
            //determines whether add is online
            $type=$timestamp[1];
            $online=$timestamp[0];
            if($online=='online')
            {
                $no_adds=false;
                
                $adds_online[]=$adds[$x];
                $names[]=get_user_name($adds[$x]);
                $types[]=$type;
                
                //gets number of adds
                $query=mysql_query("SELECT user_friends FROM user_data WHERE user_id=$adds[$x] LIMIT 1");
                if($query&&mysql_num_rows($query)==1)
                {
                    $array=mysql_fetch_row($query);
                    $user_adds=explode('|^|*|', $array[0]);
                    
                    $num_adds[]=sizeof($user_adds);
                }
                
                $profile_pictures[]=get_profile_picture($adds[$x]);
            }
        }
    }
    
    
    $JSON=array();
    $JSON['adds']=$adds_online;
    $JSON['profile_pictures']=$profile_pictures;
    $JSON['names']=$names;
    $JSON['num_adds']=$num_adds;
    $JSON['no_adds']=$no_adds;
    $JSON['types']=$types;
    echo json_encode($JSON);
    exit();
}

//gets days and years for home view custom date
else if($num==3)
{
    $query=mysql_query("SELECT timestamps FROM users WHERE id=$_SESSION[id] LIMIT 1");
    if($query&&mysql_num_rows($query)==1)
    {
        $array=mysql_fetch_row($query);
        $timestamp=$array[0];
        
        //gets string version of timestamp
        $new_timestamp=get_adjusted_date($timestamp, 0);
        
        //breaks up string date
        //[0]=May
        //[1]=23,
        //[2]=2012
        $break=explode(' ', $new_timestamp);
        
        //gets 2012 from break array
        $join_year=(int)$break[2];
        
        //gets current date - March 21, 2013
        $now=get_regular_date(get_date());
        
        //breaks up string date
        //[0]=March
        //[1]=21,
        //[2]=2013
        $break=explode(' ', $now);
        
        //gets 2013 from break array
        $current_year=(int)($break[2]);
        
        
        $JSON=array();
        $JSON['current_year']=$current_year;
        $JSON['join_year']=$join_year;
        echo json_encode($JSON);
        exit();
    }
}

//gets add's points
else if($num==4)
{
    $query=mysql_query("SELECT user_friends FROM user_data WHERE user_id=$_SESSION[id] LIMIT 1");
    if($query&&mysql_num_rows($query)==1)
    {
        $array=mysql_fetch_row($query);
        $adds=explode('|^|*|', $array[0]);
        
        $adds[]=$_SESSION['id'];
        
        //gets the points for everyone in the $adds array
        $points=array();
        for($x = 0; $x < sizeof($adds); $x++)
        {
            $query=mysql_query("SELECT points FROM user_data WHERE user_id=$adds[$x] LIMIT 1");
            if($query&&mysql_num_rows($query)==1)
            {
                $array=mysql_fetch_row($query);
                $p=$array[0];
                
                $points[]=$p;
            }
        }
        
        
        //sorts things in chronological order
        $array_points=$points;
        sort($points, SORT_NUMERIC);

        //creates temporary data of arrays for future use
        $temp_adds=$adds;

        $array_adds=array();
        //rearranges rest of data according to sorted timestamps compared to previously unsorted timestamps
        for($x = 0; $x < sizeof($temp_adds); $x++)
        {
            $number=array_search($points[$x], $array_points);
            $array_points[$number]='';

            $array_adds[$x]=$temp_adds[$number];
        }
        $adds=$array_adds;
        
        //gets extra stuff
        $names=array();
        $profile_pictures=array();
        for($x = 0; $x < sizeof($adds); $x++)
        {
            //gets names
            $names[]=get_user_name($adds[$x]);
            
            //gets profile pictures
            $profile_pictures[]=get_profile_picture($adds[$x]);
        }
        
        $JSON=array();
        $JSON['adds']=$adds;
        $JSON['names']=$names;
        $JSON['profile_pictures']=$profile_pictures;
        $JSON['points']=$points;
        echo json_encode($JSON);
        exit();
    }
}