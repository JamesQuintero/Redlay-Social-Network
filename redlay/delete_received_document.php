<?php


// DEPRECATED FUNCTION


@include('init.php');
include('universal_functions.php');
$allowed="users";
include("security_checks.php");


// add_view('delete_received_document');
$file_id=clean_string($_POST['file_id']))));
$path='./users/docs/'.$_SESSION[id].'/received/'.$file_id.'.txt';

if(file_exists($path))
{
    $path2='./users/docs/'.$_SESSION[id].'/received';
    unlink($path);
    $directory=opendir($path2);
    $num=0;
    while($file=readdir($directory))
    {
        if($num>$file_id&&file_exists("$path2/$num.txt"))
        {
            $num2=$num-1;
            $file=rename("$path2/$num.txt", "$path2/$num2.txt");
        }
        $num++;
    }
    closedir($directory);
    
    $query=mysql_query("SELECT * FROM user_documents WHERE user_id=$_SESSION[id] LIMIT 1");
    if($query&&mysql_num_rows($query)==1)
    {
        $array=mysql_fetch_array($query);
        $user_received_from=explode('|^|*|', $array['user_received_from']);
        $file_received=explode('|^|*|', $array['file_received']);
        $timestamps_received=explode('|^|*|', $array['timestamps_received']);
        $num=0;
        for($x = 0; $x < sizeof($user_received_from); $x++)
        {
            if($x!=$file_id)
            {
                $temp_users[$num]=$user_received_from[$x];
                $temp_files[$num]=$file_received[$x];
                $temp_timestamps[$num]=$timestamps_received[$x];
                $num++;
            }
        }
        $user_received_from=implode('|^|*|', $temp_users);
        $file_received=implode('|^|*|', $temp_files);
        $timestamps_received=implode('|^|*|', $temp_timestamps);
        $query=mysql_query("UPDATE user_documents SET user_received_from='$user_received_from', file_received='$file_received', timestamps_received='$timestamps_received' WHERE user_id=$_SESSION[id] LIMIT 1");
    }
    else
        echo "Error occured while deleting file";
}
else
    echo "File does not exist";
