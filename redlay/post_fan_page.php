<?php
@include('init.php');
include('universal_functions.php');
$allowed="users";
include('security_checks.php');

$ID=$_SESSION['page_id'];
$update=clean_string($_POST['post']);

//checks whether the ID of the profile is valid
if(is_id($ID) && page_id_exists($ID))
{
    if($ID==$_SESSION['page_id'])
    {
        //checks whether the user is actually posting someting
       if($update!=''&&strlen($update)<=10000)
       {
           $query=mysql_query("SELECT * FROM page_fan_content WHERE page_id=$ID LIMIT 1");
           if($query&&mysql_num_rows($query)==1)
           {
               $array=mysql_fetch_array($query);
               $posts=explode('|^|*|', mysql_real_escape_string($array['posts']));
               $post_ids=explode('|^|*|', $array['post_ids']);
               $timestamps=explode('|^|*|', $array['timestamps']);
               $comment_ids=explode('|^|*|', $array['comment_ids']);
               $comments=explode('|^|*|', mysql_real_escape_string($array['comments']));
               $comment_likes=explode('|^|*|', $array['comment_likes']);
               $comment_dislikes=explode('|^|*|', $array['comment_dislikes']);
               $comment_like_types=explode('|^|*|', $array['comment_like_types']);
               $comment_dislike_types=explode('|^|*|', $array['comment_dislike_types']);
               $comments_user_sent=explode('|^|*|', $array['comments_user_id']);
               $comment_user_types=explode('|^|*|', $array['comment_user_types']);
               $comment_timestamps=explode('|^|*|', $array['comment_timestamps']);
               $likes=explode('|^|*|', $array['likes']);
               $dislikes=explode('|^|*|', $array['dislikes']);

               //prevents mass posting
               if(get_date()-end($timestamps)>=20)
               {
                    if($array['posts']!='')
                    {
                        if(sizeof($posts)>=1000)
                        {
                            $temp_posts=array();
                            $temp_post_ids=array();
                            $temp_timestamps=array();
                            $temp_comment_ids=array();
                            $temp_comments=array();
                            $temp_comment_likes=array();
                            $temp_comment_like_types=array();
                            $temp_comment_dislike_types=array();
                            $temp_comment_user_types=array();
                            $temp_comment_dislikes=array();
                            $temp_comments_user_sent=array();
                            $temp_comment_timestamps=array();
                            $temp_likes=array();
                            $temp_dislikes=array();

                            for($x=1; $x< 1000; $x++)
                            {
                                $temp_posts[]=$posts[$x];
                                $temp_post_ids[]=$post_ids[$x];
                                $temp_timestamps[]=$timestamps[$x];
                                $temp_comment_ids[]=$comment_ids[$x];
                                $temp_comments[]=$comments[$x];
                                $temp_comment_likes[]=$comment_likes[$x];
                                $temp_comment_dislikes[]=$comment_dislikes[$x];
                                $temp_comments_user_sent[]=$comments_user_sent[$x];
                                $temp_comment_timestamps[]=$comment_timestamps[$x];
                                $temp_likes[]=$likes[$x];
                                $temp_dislikes[]=$dislikes[$x];
                                $temp_comment_like_types[]=$comment_like_types[$x];
                                $temp_comment_dislike_types[]=$comment_dislike_types[$x];
                                $temp_comment_user_types[]=$comment_user_types[$x];
                            }
                            $posts=$temp_posts;
                            $post_ids=$temp_post_ids;
                            $timestamps=$temp_timestamps;
                            $comment_ids=$temp_comment_ids;
                            $comments=$temp_comments;
                            $comment_likes=$temp_comment_likes;
                            $comment_dislikes=$temp_comment_dislikes;
                            $comments_user_sent=$temp_comments_user_sent;
                            $comment_timestamps=$temp_comment_timestamps;
                            $likes=$temp_likes;
                            $dislikes=$temp_dislikes;
                            $comment_like_types=$temp_comment_like_types;
                            $comment_dislike_types=$temp_comment_dislike_types;
                            $comment_user_types=$temp_comment_user_types;
                        }
                        $posts[]=$update;
                        $new_post_id=(int)(end($post_ids))+1;
                        $post_ids[]=$new_post_id;
                        $timestamps[]=get_date();
                        $comment_ids[]='';
                        $comments[]='';
                        $likes[]='';
                        $dislikes[]='';
                        $comment_likes[]='';
                        $comment_dislikes[]='';
                        $comments_user_sent[]='';
                        $comment_timestamps[]='';
                        $comment_like_types[]='';
                        $comment_dislike_types[]='';
                        $comment_user_types[]='';
                        
                    }
                    else
                    {
                        $posts[0]=$update;

                        $new_post_id=0;
                        $post_ids[0]=$new_post_id;
                        $timestamps[0]=get_date();
                        $comment_ids[0]='';
                        $comments[0]='';
                        $likes[0]='';
                        $dislikes[0]='';
                        $comment_likes[0]='';
                        $comment_dislikes[0]='';
                        $comments_user_sent[0]='';
                        $comment_timestamps[0]='';
                        $comment_like_types[0]='';
                        $comment_dislike_types[0]='';
                        $comment_user_types[0]='';
                        
                        
                    }
                    $posts=implode('|^|*|', $posts);
                    $post_ids=implode('|^|*|', $post_ids);
                    $timestamps=implode('|^|*|', $timestamps);
                    $comment_ids=implode('|^|*|', $comment_ids);
                    $comments=implode('|^|*|', $comments);
                    $likes=implode('|^|*|', $likes);
                    $dislikes=implode('|^|*|', $dislikes);
                    $comment_likes=implode('|^|*|', $comment_likes);
                    $comment_dislikes=implode('|^|*|', $comment_dislikes);
                    $comment_like_types=implode('|^|*|', $comment_like_types);
                    $comment_dislike_types=implode('|^|*|', $comment_dislike_types);
                    $comments_user_sent=implode('|^|*|', $comments_user_sent);
                    $comment_user_types=implode('|^|*|', $comment_user_types);
                    $comment_timestamps=implode('|^|*|', $comment_timestamps);



                    $query2=mysql_query("UPDATE page_fan_content SET post_ids='$post_ids', posts='$posts', comment_likes='$comment_likes', comment_dislikes='$comment_dislikes', comment_like_types='$comment_like_types', comment_dislike_types='$comment_dislike_types', timestamps='$timestamps', likes='$likes', dislikes='$dislikes', comment_ids='$comment_ids', comments='$comments', comments_user_id='$comments_user_sent', comment_user_types='$comment_user_types', comment_timestamps='$comment_timestamps' WHERE page_id=$ID");
                    if($query2)
                        echo "Update posted";
                    else
                    {
                        echo "Something went wrong. We are working on it.";
                        log_error("post_page.php: (2): ", mysql_error());
                    }
               }
               else
                   echo "Please wait a few seconds before posting again";
           }
           else
           {
               echo "Something went wrong. We are working on fixing it";
               log_error("post_page.php: (1): ", mysql_error());
           }
       }
       else
       {
           if($update=='')
               echo "You have not entered anything";
           else
               echo "Post must be under 10,000 characters";
       }
    }
    else
        echo "You aren't allowed to post here";
}
else
    echo "Page id is invalid";
?>
