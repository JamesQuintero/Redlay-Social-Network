<?php
@include('init.php');
include('universal_functions.php');
$allowed="users";
include('security_checks.php');

$ID=(int)($_POST['user_id']);
$update=clean_string($_POST['updates']);
$audience=$_POST['audience'];

//checks whether the ID of the profile is valid
if(is_id($ID) && user_id_exists($ID))
{
    if($ID==$_SESSION['id']||user_is_friends($ID, $_SESSION['id']))
    {
        //checks whether the user is actually posting someting
       if($update!=''&&strlen($update)<=10000)
       {
          
            //if user selected Everyone and other groups, this changes it to just Everyone
             if(in_array('Everyone', $audience)||$ID!=$_SESSION['id']||$audience[0]=='')
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

            if($bool==true)
            {
               //checks whether users are friends
               if(user_is_friends($ID, $_SESSION['id'])=='true'||$ID==$_SESSION['id'])
               {
                   $query=mysql_query("SELECT * FROM content WHERE user_id=$ID LIMIT 1");
                   if($query&&mysql_num_rows($query)==1)
                   {
                       $array=mysql_fetch_array($query);
                       $posts=explode('|^|*|', mysql_real_escape_string($array['posts']));
                       $post_ids=explode('|^|*|', $array['post_ids']);
                       $timestamps=explode('|^|*|', $array['timestamps']);
                       $user_id_posted=explode('|^|*|', $array['user_ids_posted']);
                       $comment_ids=explode('|^|*|', $array['comment_ids']);
                       $comments=explode('|^|*|', mysql_real_escape_string($array['comments']));
                       $comment_likes=explode('|^|*|', $array['comment_likes']);
                       $comment_dislikes=explode('|^|*|', $array['comment_dislikes']);
                       $comments_user_sent=explode('|^|*|', $array['comments_user_id']);
                       $comment_timestamps=explode('|^|*|', $array['comment_timestamps']);
                       $likes=explode('|^|*|', $array['likes']);
                       $dislikes=explode('|^|*|', $array['dislikes']);
                       $audience_groups=explode('|^|*|', $array['post_groups']);

                       
                       if(get_date()-end($timestamps)>=5)
                       {
                            if($array['posts']!='')
                            {
                                if(sizeof($posts)>=1000)
                                {
                                    $temp_posts=array();
                                    $temp_post_ids=array();
                                    $temp_timestamps=array();
                                    $temp_user_id_posted=array();
                                    $temp_comment_ids=array();
                                    $temp_comments=array();
                                    $temp_comment_likes=array();
                                    $temp_comment_dislikes=array();
                                    $temp_comments_user_sent=array();
                                    $temp_comment_timestamps=array();
                                    $temp_likes=array();
                                    $temp_dislikes=array();
                                    $temp_audience_groups=array();

                                    for($x=1; $x< 1000; $x++)
                                    {
                                        $temp_posts[]=$posts[$x];
                                        $temp_post_ids[]=$post_ids[$x];
                                        $temp_timestamps[]=$timestamps[$x];
                                        $temp_user_id_posted[]=$user_id_posted[$x];
                                        $temp_comment_ids[]=$comment_ids[$x];
                                        $temp_comments[]=$comments[$x];
                                        $temp_comment_likes[]=$comment_likes[$x];
                                        $temp_comment_dislikes[]=$comment_dislikes[$x];
                                        $temp_comments_user_sent[]=$comments_user_sent[$x];
                                        $temp_comment_timestamps[]=$comment_timestamps[$x];
                                        $temp_likes[]=$likes[$x];
                                        $temp_dislikes[]=$dislikes[$x];
                                        $temp_audience_groups[]=$audience_groups[$x];
                                    }
                                    $posts=$temp_posts;
                                    $post_ids=$temp_post_ids;
                                    $timestamps=$temp_timestamps;
                                    $user_id_posted=$temp_user_id_posted;
                                    $comment_ids=$temp_comment_ids;
                                    $comments=$temp_comments;
                                    $comment_likes=$temp_comment_likes;
                                    $comment_dislikes=$temp_comment_dislikes;
                                    $comments_user_sent=$temp_comments_user_sent;
                                    $comment_timestamps=$temp_comment_timestamps;
                                    $likes=$temp_likes;
                                    $dislikes=$temp_dislikes;
                                    $audience_groups=$temp_audience_groups;
                                }
                                $posts[]=$update;
                                $new_post_id=(int)(end($post_ids))+1;
                                $post_ids[]=$new_post_id;
                                $timestamps[]=get_date();
                                $user_id_posted[]=$_SESSION['id'];
                                $comment_ids[]='';
                                $comments[]='';
                                $likes[]='';
                                $dislikes[]='';
                                $comment_likes[]='';
                                $comment_dislikes[]='';
                                $comments_user_sent[]='';
                                $comment_timestamps[]='';
                                $audience_groups[]=implode('|%|&|', $audience);
                            }
                            else
                            {
                                $posts[0]=$update;

                                $new_post_id=0;
                                $post_ids[0]=$new_post_id;
                                $timestamps[0]=get_date();
                                $user_id_posted[0]=$_SESSION['id'];
                                $comment_ids[0]='';
                                $comments[0]='';
                                $likes[0]='';
                                $dislikes[0]='';
                                $comment_likes[0]='';
                                $comment_dislikes[0]='';
                                $comments_user_sent[0]='';
                                $comment_timestamps[0]='';
                                $audience_groups[0]=implode('|%|&|', $audience);
                            }
                            $posts=implode('|^|*|', $posts);
                            $post_ids=implode('|^|*|', $post_ids);
                            $timestamps=implode('|^|*|', $timestamps);
                            $user_id_posted=implode('|^|*|', $user_id_posted);
                            $comment_ids=implode('|^|*|', $comment_ids);
                            $comments=implode('|^|*|', $comments);
                            $likes=implode('|^|*|', $likes);
                            $dislikes=implode('|^|*|', $dislikes);
                            $comment_likes=implode('|^|*|', $comment_likes);
                            $comment_dislikes=implode('|^|*|', $comment_dislikes);
                            $comments_user_sent=implode('|^|*|', $comments_user_sent);
                            $comment_timestamps=implode('|^|*|', $comment_timestamps);
                            $audience_groups=implode('|^|*|', $audience_groups);



                            $query2=mysql_query("UPDATE content SET post_ids='$post_ids', posts='$posts', comment_likes='$comment_likes', comment_dislikes='$comment_dislikes', timestamps='$timestamps', user_ids_posted='$user_id_posted', likes='$likes', dislikes='$dislikes', comment_ids='$comment_ids', comments='$comments', comments_user_id='$comments_user_sent', comment_timestamps='$comment_timestamps', post_groups='$audience_groups' WHERE user_id=$ID");
                            if($query2)
                            {
                                echo "Posted!";
                                if($_SESSION['id']!=$ID)
                                {
                                    $information=array();
                                    $information[0]='profile';

                                    add_alert($ID, $information);
                                    $emails=get_email_settings($ID, 'posts_on_profile');
                                    if($emails[1]==1)
                                    {
                                        $information=array();
                                        $information[0]='posts_on_profile';
                                        $information[1]=$new_post_id;

                                        // send_mail_alert($ID, $information);
                                    }
                                }

                                //posts to public
                                if($ID==$_SESSION['id']&&$audience[0]='Everyone')
                                {
                                    //checks whether can post to public
                                     $query=mysql_query("SELECT display_non_friends FROM user_privacy WHERE user_id=$_SESSION[id] LIMIT 1");
                                     if($query&&mysql_num_rows($query)==1)
                                     {
                                         $array=mysql_fetch_row($query);
                                         $display_non_friends=explode('|^|*|', $array[0]);

                                         //can display to public
                                         if($display_non_friends[3]=='yes')
                                         {
                                             $query=mysql_query("SELECT post_ids, posts, posts_users_sent, original_post_ids, post_timestamps FROM public WHERE num=1");
                                             if($query&&mysql_num_rows($query)==1)
                                             {
                                                 $array=mysql_fetch_row($query);
                                                 $post_ids=explode('|^|*|', $array[0]);
                                                 $posts=explode('|^|*|', mysql_real_escape_string($array[1]));
                                                 $posts_users_sent=explode('|^|*|', $array[2]);
                                                 $original_post_ids=explode('|^|*|', $array[3]);
                                                 $post_timestamps=explode('|^|*|', $array[4]);

                                                 if(sizeof($post_ids)>=500)
                                                 {
                                                     $temp_post_ids=array();
                                                     $temp_posts=array();
                                                     $temp_posts_users_sent=array();
                                                     $temp_original_post_ids=array();
                                                     $temp_post_timestamps=array();

                                                     for($x = 1; $x < sizeof($post_ids); $x++)
                                                     {
                                                         $temp_post_ids[]=$post_ids[$x];
                                                         $temp_posts[]=$posts[$x];
                                                         $temp_posts_users_sent[]=$posts_users_sent[$x];
                                                         $temp_original_post_ids[]=$original_post_ids[$x];
                                                         $temp_post_timestamps[]=$post_timestamps[$x];
                                                     }

                                                     $new_public_id=$temp_post_ids[sizeof($temp_post_ids)-1]+1;
                                                     $temp_post_ids[]=$new_public_id;
                                                     $temp_posts_users_sent[]=$_SESSION['id'];
                                                     $temp_posts[]=$update;
                                                     $temp_post_timestamps[]=get_date();
                                                     $temp_original_post_ids[]=$new_post_id;

                                                     $post_ids=implode('|^|*|', $temp_post_ids);
                                                     $posts=implode('|^|*|', $temp_posts);
                                                     $posts_users_sent=implode('|^|*|', $temp_posts_users_sent);
                                                     $original_post_ids=implode('|^|*|', $temp_original_post_ids);
                                                     $post_timestamps=implode('|^|*|', $temp_post_timestamps);
                                                 }
                                                 else
                                                 {
                                                     //if nothing has been posted before
                                                     if($array[0]=='')
                                                     {
                                                         $new_public_id=0;
                                                         $post_ids[0]=$new_public_id;
                                                         $posts_users_sent[0]=$_SESSION['id'];
                                                         $posts[0]=$update;
                                                         $post_timestamps[0]=get_date();
                                                         $original_post_ids[0]=$new_post_id;
                                                     }
                                                     else
                                                     {
                                                         $new_public_id=$post_ids[sizeof($post_ids)-1]+1;
                                                         $post_ids[]=$new_public_id;
                                                         $posts_users_sent[]=$_SESSION['id'];
                                                         $posts[]=$update;
                                                         $post_timestamps[]=get_date();
                                                         $original_post_ids[]=$new_post_id;
                                                     }

                                                     $post_ids=implode('|^|*|', $post_ids);
                                                     $posts_users_sent=implode('|^|*|', $posts_users_sent);
                                                     $posts=implode('|^|*|', $posts);
                                                     $post_timestamps=implode('|^|*|', $post_timestamps);
                                                     $original_post_ids=implode('|^|*|', $original_post_ids);
                                                 }

                                                 $query=mysql_query("UPDATE public SET posts='$posts', posts_users_sent='$posts_users_sent', post_ids='$post_ids', original_post_ids='$original_post_ids', post_timestamps='$post_timestamps' WHERE num=1");
                                                 if(!$query)
                                                 {
                                                     echo " Something went wrong when posting to Public. We are working on fixing it";
                                                     log_error("post.php: ",mysql_error());
                                                 }

                                             }
                                             else
                                             {
                                                 echo " Something went wrong when posting to Public. We are working on fixing it";
                                                 log_error("post.php: ",mysql_error());
                                             }
                                         }
                                     }
                                }
                            }
                            else
                            {
                                echo "Something went wrong when inserting your update into the database. We are working on it.";
                                log_error("update_status.php: ", mysql_error());
                            }
                       }
                       else
                           echo "Please wait a few seconds before posting again";
                   }
                   else
                   {
                       echo "Your account is not in our database. Please contact us and report the problem";
                       log_error("update_status.php", mysql_error());
                   }
               }
               else
                   echo "This user is not your friend";
           }
           else
               echo "Invalid Audience";
       }
       else
       {
           if($update=='')
            echo "You have not entered anything to post";
           else
            echo "Post must be under 10,000 characters";
       }
    }
}
else
    echo "User id is invalid";