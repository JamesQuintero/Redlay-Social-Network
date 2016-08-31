<?php
include('init.php');
include('universal_functions.php');
$allowed="users";
include('security_checks.php');

$num=(int)($_POST['num']);
if($num==1)
{
    $post_id=(int)($_POST['post_id']);
    $profile_id=(int)($_POST['profile_id']);
    $timezone=(int)($_POST['timezone']);

    if(is_id($profile_id)&&user_id_exists($profile_id)&&!user_id_terminated($profile_id))
    {
        $query=mysql_query("SELECT post_ids, post_groups, posts, user_ids_posted, comment_ids, comments, comment_likes, comment_dislikes, comments_user_id, comment_timestamps, likes, dislikes, timestamps FROM content WHERE user_id=$profile_id LIMIT 1");
        if($query&&mysql_num_rows($query)==1)
        {
            $array=mysql_fetch_array($query);
            $post_ids=explode('|^|*|', $array[0]);
            $post_groups=explode('|^|*|', $array[1]);
            $posts=explode('|^|*|', $array[2]);
            $user_ids_posted=explode('|^|*|', $array[3]);
            $comment_ids=explode('|^|*|', $array[4]);
            $comments=explode('|^|*|', $array[5]);
            $comment_likes=explode('|^|*|', $array[6]);
            $comment_dislikes=explode('|^|*|', $array[7]);
            $comments_users_sent=explode('|^|*|', $array[8]);
            $comment_timestamps=explode('|^|*|', $array[9]);
            $likes=explode('|^|*|', $array[10]);
            $dislikes=explode('|^|*|', $array[11]);
            $timestamps=explode('|^|*|', $array[12]);

            $index=-1;
            for($x = 0; $x < sizeof($post_ids); $x++)
            {
                if($post_ids[$x]==$post_id)
                    $index=$x;
            }
            
            
            if($index!=-1)
            {
                //explodes everything
                
                //gets rid of terminated accounts
                $changed=false;
                $temp_comments=array();
                $temp_comment_likes=array();
                $temp_comment_dislikes=array();
                $temp_comments_users_sent=array();
                $temp_comment_timestamps=array();
                $temp_comment_ids=array();
                for($x = 0;  $x < sizeof($post_ids); $x++)
                {
                        //gets rid of likes from terminated accounts
                        $likes[$x]=explode('|%|&|', $likes[$x]);
                        
                        $temptemp_likes=array();
                        if($likes[$x][0]!='0'&&$likes[$x][0]!='')
                        {
                            for($y = 0; $y < sizeof($likes[$x]); $y++)
                            {
                                if($likes[$x][$y]!='0'&&$likes[$x][$y]!=''&&!user_id_terminated($likes[$x][$y]))
                                    $temptemp_likes[]=$likes[$x][$y];
                                else
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
                                else
                                    $changed=true;
                            }
                        }
                        if($temptemp_dislikes[0]==NULL)
                            $temptemp_dislikes=0;
                        $temp_dislikes[]=$temptemp_dislikes;

                        //explodes comment stuff
                        $comment_ids[$x]=explode('|%|&|', $comment_ids[$x]);
                        $comments[$x]=explode('|%|&|', $comments[$x]);
                        $comment_likes[$x]=explode('|%|&|', $comment_likes[$x]);
                        $comment_dislikes[$x]=explode('|%|&|', $comment_dislikes[$x]);
                        $comments_users_sent[$x]=explode('|%|&|', $comments_users_sent[$x]);
                        $comment_timestamps[$x]=explode('|%|&|', $comment_timestamps[$x]);

                        //gets rid of comments delete accounts
                        $temptemp_comments=array();
                        $temptemp_comments_users_sent=array();
                        $temptemp_comment_ids=array();
                        $temptemp_comment_timestamps=array();
                        for($y = 0; $y < sizeof($comment_ids[$x]); $y++)
                        {
                            if($comments_users_sent[$x][$y]!=''&&!user_id_terminated($comments_users_sent[$x][$y]))
                            {
                                $temptemp_comments[]=$comments[$x][$y];
                                $temptemp_comments_users_sent[]=$comments_users_sent[$x][$y];
                                $temptemp_comment_ids[]=$comment_ids[$x][$y];
                                $temptemp_comment_timestamps[]=$comment_timestamps[$x][$y];

                                //gets rid of comment likes of terminated accounts
                                if($comment_likes[$x][$y]!='')
                                    $comment_likes[$x][$y]=explode('|@|$|', $comment_likes[$x][$y]);

                                //gets rid of comment dislikes of terminated accounts
                                if($comment_dislikes[$x][$y]!='')
                                    $comment_dislikes[$x][$y]=explode('|@|$|', $comment_dislikes[$x][$y]);
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
                
                
                
                
                //gets correct timestamp
                $timestamp=get_time_since($timestamps[$index], $timezone);
                $timestamp_seconds=get_time_since_seconds($timestamps[$index], $timezone);
                
                //gets comment timestamps
                $new_comment_timestamps=array();
                $comment_timestamp_seconds=array();
                for($x = 0; $x < sizeof($comment_timestamps[$index]); $x++)
                {
                    $new_comment_timestamps[$x]=get_time_since($comment_timestamps[$index][$x], $timezone);
                    $comment_timestamp_seconds[$x]=get_time_since_seconds($comment_timestamps[$index][$x], $timezone);
                }
                
                //gets badges
                $badges=get_badges($user_ids_posted[$index]);
                
                //explodes everything
                $post_groups[$index]=explode('|%|&|', $post_groups[$index]);

                $num_comment_likes=array();
                $num_comment_dislikes=array();
                $has_liked_comment=array();
                $has_disliked_comment=array();
                for($x =0; $x < sizeof($comment_likes[$index]); $x++)
                {
                    //gets number of comment likes
                    if($comment_likes[$index][$x][0]=='')
                        $num_comment_likes[]=0;
                    else
                        $num_comment_likes[]=sizeof($comment_likes[$index][$x]);

                    //gets number of commeent dislikes
                    if($comment_dislikes[$index][$x][0]=='')
                        $num_comment_dislikes[]=0;
                    else
                        $num_comment_dislikes[]=sizeof($comment_dislikes[$index][$x]);
                    
                    //gets if has liked comment
                    if(in_array($_SESSION['id'], $comment_likes[$index][$x]))
                        $has_liked_comment[]=true;
                    else
                        $has_liked_comment[]=false;
                    
                    //gets if has liked comment
                    if(in_array($_SESSION['id'], $comment_dislikes[$index][$x]))
                        $has_disliked_comment[]=true;
                    else
                        $has_disliked_comment[]=false;
                }

                $comment_names=array();
                $comment_badges=array();
                $comment_profile_pictures=array();
                for($x = 0;$x < sizeof($comments_users_sent[$index]); $x++)
                {
                    //gets comment names
                    $comment_names[]=get_user_name($comments_users_sent[$index][$x]);
                    
                    //gets comment profile pictures
                    $comment_profile_pictures[]=get_profile_picture($comments_users_sent[$index][$x]);
                    
                    //gets comment badges
                    $comment_badges[]=get_badges($comments_users_sent[$index][$x]);
                }

                //gets number of likes
                if($likes[$index][0]==0)
                    $num_likes=0;
                else
                    $num_likes=sizeof($likes[$index]);

                //gets number of dislikes
                if($dislikes[$index][0]==0)
                    $num_dislikes=0;
                else
                    $num_dislikes=sizeof($dislikes[$index]);
                
                //gets if has liked
                if(in_array($_SESSION['id'], $likes[$index]))
                    $has_liked=true;
                else
                    $has_liked=false;
                
                //gets if has liked
                if(in_array($_SESSION['id'], $dislikes[$index]))
                    $has_disliked=true;
                else
                    $has_disliked=false;

                
                
                if($user_ids_posted[$index]!=$_SESSION['id'])
                {
                    //checks if current user is allowed to view post
                    $user_audiences=get_audience_current_user($user_ids_posted[$index]);
                    $bool=false;
                    for($x = 0; $x < sizeof($user_audiences); $x++)
                    {
                        if(can_view($user_audiences, $post_groups[$index])&&$bool==false)
                            $bool=true;
                    }
                }
                else
                    $bool=true;

                //gets number of comments
                if($comments[$index][0]=='')
                    $num_comments=0;
                else
                    $num_comments=sizeof($comments[$index]);



                if($bool)
                {
                    $JSON=array();
                    $JSON['post']=$posts[$index];
                    $JSON['user_id_posted']=$user_ids_posted[$index];
                    $JSON['num_likes']=$num_likes;
                    $JSON['num_dislikes']=$num_dislikes;
                    $JSON['has_liked']=$has_liked;
                    $JSON['has_disliked']=$has_disliked;
                    $JSON['timestamp']=$timestamp;
                    $JSON['timestamp_seconds']=$timestamp_seconds;
                    $JSON['num_comments']=$num_comments;
                    $JSON['audience_groups']=$post_groups[$index];
                    $JSON['comments']=$comments[$index];
                    $JSON['comments_users_sent']=$comments_users_sent[$index];
                    $JSON['comment_timestamps']=$new_comment_timestamps;
                    $JSON['comment_timestamp_seconds']=$comment_timestamp_seconds;
                    $JSON['comment_names']=$comment_names;
                    $JSON['num_comment_likes']=$num_comment_likes;
                    $JSON['num_comment_dislikes']=$num_comment_dislikes;
                    $JSON['has_liked_comment']=$has_liked_comment;
                    $JSON['has_disliked_comment']=$has_disliked_comment;
                    $JSON['user_name']=get_user_name($user_ids_posted[$index]);
                    $JSON['profile_picture']=get_profile_picture($user_ids_posted[$index]);
                    $JSON['comment_ids']=$comment_ids[$index];
                    $JSON['badges']=$badges;
                    $JSON['comment_badges']=$comment_badges;
                    $JSON['comment_profile_pictures']=$comment_profile_pictures;
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
                    $JSON['comment_profile_pictures']=array();
                    echo json_encode($JSON);
                    exit();
                }
            }
        }
    }
}

//changes post's audience
else if($num==2)
{
    $post_id=clean_string($_POST['post_id']);
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
        $query=mysql_query("SELECT post_ids, post_groups FROM content WHERE user_id=$_SESSION[id] LIMIT 1");
        if($query&&mysql_num_rows($query)==1)
        {
            $array=mysql_fetch_row($query);
            $post_ids=explode('|^|*|', $array[0]);
            $audiences=explode('|^|*|', $array[1]);

            $index=-1;
            for($x = 0; $x < sizeof($post_ids); $x++)
            {
                if($post_id==$post_ids[$x])
                    $index=$x;
            }

            if($index!=-1)
            {
                $audiences[$index]=implode('|%|&|', $groups);

                $audiences=implode('|^|*|', $audiences);
                $query=mysql_query("UPDATE content SET post_groups='$audiences' WHERE user_id=$_SESSION[id]");
                if($query)
                    echo "Audience changed";
                else
                {
                    echo "Sometehing went wrong. We are working on fixing it";
                    send_mail_error("view_post_query.php: (2): ", mysql_error());
                }
            }
            else
                echo "Post ID invalid";
        }
        else
        {
            echo "Something went wrong. We are working on fixing it";
            send_mail_error("view_post_query.php: (2): ", mysql_error());
        }
    }
}