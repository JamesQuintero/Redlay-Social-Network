<?php
@include('init.php');
include('universal_functions.php');
$allowed="users";
include('security_checks.php');

$user_id=(int)($_POST['user_id']);
$picture_id=clean_string($_POST['picture_id']);
$type=clean_string($_POST['type']);


if($type=='user')
{
    $query=mysql_query("SELECT pictures, picture_likes FROM pictures WHERE user_id=$user_id LIMIT 1");
    if($query && mysql_num_rows($query)==1)
    {
        $array=mysql_fetch_row($query);
        $pictures=explode('|^|*|', $array[0]);
        $likes=explode('|^|*|', $array[1]);

        $index=-1;
        for($x = 0; $x < sizeof($pictures); $x++)
        {
            if($pictures[$x]==$picture_id)
                $index=$x;
        }

        if($index!=-1)
        {
            $likes[$index]=explode('|%|&|', $likes[$index]);
            $temp_likes=array();
            for($x = 0; $x < sizeof($likes[$index]); $x++)
            {
                if($_SESSION['id']!=$likes[$index][$x])
                    $temp_likes[]=$likes[$index][$x];
            }
            
            //removes point
            remove_point($user_id);


            $likes[$index]=implode('|%|&|', $temp_likes);
            $likes=implode('|^|*|', $likes);

            $query=mysql_query("UPDATE pictures SET picture_likes='$likes' WHERE user_id='$user_id'");
        }
    }
}
else if($type=='page')
{

}