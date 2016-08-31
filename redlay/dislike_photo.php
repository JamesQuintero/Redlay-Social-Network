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
    if($user_id!=$_SESSION['id'])
    {
        $query=mysql_query("SELECT pictures, picture_dislikes FROM pictures WHERE user_id=$user_id LIMIT 1");
        if($query && mysql_num_rows($query)==1)
        {
            $array=mysql_fetch_row($query);
            $pictures=explode('|^|*|', $array[0]);
            $dislikes=explode('|^|*|', $array[1]);

            $index=-1;
            for($x = 0; $x < sizeof($pictures); $x++)
            {
                if($pictures[$x]==$picture_id)
                    $index=$x;
            }

            if($index!=-1)
            {
                $dislikes[$index]=explode('|%|&|', $dislikes[$index]);

                if($dislikes[$index][0]=='')
                    $dislikes[$index][0]=$_SESSION['id'];
                else
                    $dislikes[$index][]=$_SESSION['id'];


                $dislikes[$index]=implode('|%|&|', $dislikes[$index]);
                $dislikes=implode('|^|*|', $dislikes);

                $query=mysql_query("UPDATE pictures SET picture_dislikes='$dislikes' WHERE user_id='$user_id'");
                if($query)
                {
                    //records photo like
                    record_photo_dislike($picture_id, $user_id);
                }
            }
        }
    }
}
else if($type=='page')
{

}