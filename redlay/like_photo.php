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

                if($likes[$index][0]=='')
                    $likes[$index][0]=$_SESSION['id'];
                else
                    $likes[$index][]=$_SESSION['id'];


                $likes[$index]=implode('|%|&|', $likes[$index]);
                $likes=implode('|^|*|', $likes);

                $query=mysql_query("UPDATE pictures SET picture_likes='$likes' WHERE user_id='$user_id'");
                if($query)
                {
                    
                    $information=array();
                    $information[0]='picture_like';
                    $information[1]=$picture_id;

                    //adds point
                    add_point($user_id);
                    
                    //adds alert
                    add_alert($user_id, $information);
                    
                    $emails=get_email_settings($user_id, 'photo_like');
                    if($emails==1)
                    {
                        $information[0]='photo_like';

                        send_mail_alert($user_id, $information);
                    }
                    
                    //records photo like
                    record_photo_like($picture_id, $user_id);
                }
            }
        }
    }
}
else if($type=='page')
{

}
