<?php
@include('init.php');
include('universal_functions.php');
$allowed="users";
include("security_checks.php");

// add_view('get_user_receieved_document');

//DEPRECATED FEATURE


$num=clean_string($_GET['num']))));
$file_id=clean_string($_POST['file_id']))));
$path='./users/docs/'.$_SESSION[id].'/received/'.$file_id.'.txt';
if(file_exists($path))
{
    if($num==1)
        echo file_get_contents($path);
    else if($num==2)
    {
        $query=mysql_query("SELECT * FROM user_documents WHERE user_id=$_SESSION[id] LIMIT 1");
        if($query&&mysql_num_rows($query)==1)
        {
            $array=mysql_fetch_array($query);
            $names=explode('|^|*|', $array['file_received']);
            echo $names[$file_id];
        }
        else
        {
            echo "Something went wrong";
            log_error("get_user_receieved_document.php: (1): ", mysql_error());
        }
    }
    else if($num==3)
    {
        $query=mysql_query("SELECT * FROM user_documents WHERE user_id=$_SESSION[id] LIMIT 1");
        if($query&&mysql_num_rows($query)==1)
        {
            $array=mysql_fetch_array($query);
            $names=explode('|^|*|', $array['user_received_from']);
            echo get_user_name($names[$file_id]);
        }
        else
        {
            echo "Something went wrong";
            log_error("get_user_receieved_document.php: (2): ", mysql_error());
        }
    }
}
else
    echo "File does not exist!";