<?php
@include('init.php');
include('universal_functions.php');
$allowed="users";
include('security_checks.php');

$num=clean_string($_GET['num']);
$file_id=clean_string($_POST['file_id']);
$path='./users/docs/'.$_SESSION[id].'/archive/'.$file_id.'.txt';
if(file_exists_server($path))
{
    if($num==1)
        echo file_get_contents($path);
    else if($num==2)
    {
        $query=mysql_query("SELECT * FROM user_documents WHERE user_id=$_SESSION[id] LIMIT 1");
        if($query&&mysql_num_rows($query)==1)
        {
            $array=mysql_fetch_array($query);
            $names=explode('|^|*|', $array['document_names']);
            echo $names[$file_id];
        }
        else
        {
            echo "Something went wrong";
            log_error("get_user_document.php: ", mysql_error());
        }
    }
}
else
    echo "File does not exist!";