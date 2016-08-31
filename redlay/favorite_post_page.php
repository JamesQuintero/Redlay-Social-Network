<?php
@include('init.php');
include("universal_functions.php");
$allowed="users";
include("security_checks.php");

////DEPRECATED FEATURE

$num=clean_string($_POST['number']))))));;
$ID=clean_string($_POST['user_id']))))));;
$post=clean_string($_POST['post']))))));;
$profile_id=clean_string($_POST['profile']))))));;
$query=mysql_query("SELECT * FROM page_updates WHERE page_id=$_SESSION[page_id] LIMIT 1");
if($query&&mysql_num_rows($query)==1)
{
    $array=mysql_fetch_array($query);
    $favorite=explode('|^|*|', $array['favorite_posts']);
    $favorite_users=explode('|^|*|', $array['favorite_posts_users']);
    $favorite_users_profile=explode('|^|*|', $array['favorite_posts_users_profile']);
    $favorite_post_index=explode('|^|*|', $array['favorite_posts_index']);

    if($array['favorite_posts']=='')
    {
        $favorite[0]=$post;
        $favorite_users[0]=$ID;
        $favorite_users_profile[0]=$profile_id;
        $favorite_post_index[0]=$num;
    }
    else
    {
        $favorite[]=$post;
        $favorite_users[]=$ID;
        $favorite_users_profile[]=$profile_id;
        $favorite_post_index[]=$num;
    }
    $favorite=implode('|^|*|', $favorite);
    $favorite_users=implode('|^|*|', $favorite_users);
    $favorite_users_profile=implode('|^|*|', $favorite_users_profile);
    $favorite_post_index=implode('|^|*|', $favorite_post_index);

    $query=mysql_query("UPDATE page_updates SET favorite_posts='$favorite', favorite_posts_users='$favorite_users', favorite_posts_users_profile='$favorite_users_profile', favorite_posts_index='$favorite_post_index' WHERE page_id=$_SESSION[page_id]");
    if($query)
        echo "Post favorited!";
    else
    {
        echo "Post favorite failed!";
        log_error("favorite_post_page.php: (2): ", mysql_error());
    }
}
else
    {
        echo "Something went wrong";
        log_error("favorite_post_page.php: (1): ", mysql_error());
    }
?>
