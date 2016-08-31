<?php
@include('init.php');
include('universal_functions.php');
$allowed="users";
include("security_checks.php");

$post_id=(int)($_POST['post_id']);
$page_id=(int)($_POST['page_id']);

if(is_id($page_id) && page_id_exists($page_id) && $post_id>=0)
{
    $query=mysql_query("SELECT post_ids, dislikes FROM page_updates WHERE page_id=$page_id LIMIT 1");
    if($query&&mysql_num_rows($query)==1)
    {
        $array=mysql_fetch_row($query);
        $post_ids=explode('|^|*|', $array[0]);
        $dislikes=explode('|^|*|', $array[1]);

        for($x = 0; $x < sizeof($post_ids); $x++)
        {
            if($post_ids[$x]==$post_id)
                $index=$x;
        }

        $dislikes[$index]=explode('|%|&|', $dislikes[$index]);

        if($dislikes[$index][0]=='0')
            $dislikes[$index][0]=$_SESSION['id'];
        else
            $dislikes[$index][]=$_SESSION['id'];

        $dislikes[$index]=implode('|%|&|', $dislikes[$index]);

        $dislikes=implode('|^|*|', $dislikes);

        $query=mysql_query("UPDATE page_updates SET dislikes='$dislikes' WHERE page_id=$page_id");
        if($query)
        {

        }
        else
        {
            echo "Something went wrong";
            log_error("dislike_page_post.php: ",mysql_error());
        }
    }
    else
    {
        echo "Something went wrong";
        log_error("dislike_page_post.php: ",mysql_error());
    }
}