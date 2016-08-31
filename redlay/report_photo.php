<?php
@include('init.php');
include('universal_functions.php');
$allowed="users";
include('security_checks.php');

//$photo_id=clean_string($_POST['photo_id']);
//$ID=(int)($_POST['user_id']);
$photo_id=clean_string($_POST['photo_id']);
$ID=(int)($_POST['user_id']);

if(is_id($ID)&&user_id_exists($ID)&&!user_id_terminated($ID))
{
    if($ID!=$_SESSION['id'])
    {
        $query=mysql_query("SELECT pictures, image_types FROM pictures WHERE user_id=$ID LIMIT 1");
        if($query&&mysql_num_rows($query)==1)
        {
            $array=mysql_fetch_row($query);
            $pictures=explode('|^|*|', $array[0]);
            $image_types=explode('|^|*|', $array[1]);

            $index=-1;
            for($x = 0; $x < sizeof($pictures); $x++)
            {
                if($pictures[$x]==$photo_id)
                    $index=$x;
            }

            //if photo exists
            if($index!=-1)
            {
                $information=array();
                $information[0]=$photo_id;
                $information[1]=$image_types[$index];
                $information=implode('|^|*|', $information);

                $query=mysql_query("SELECT reported_by FROM report WHERE user_id=$ID AND information='$information'");
                if($query&&mysql_num_rows($query)==0)
                {
                    $query=mysql_query("INSERT INTO report SET user_id=$ID, type='photo', information='$information', reported_by='$_SESSION[id]', timestamp='".get_date()."'");
                    if($query)
                        echo "Photo reported";
                    else
                    {
                        echo "Something went wrong. We are working on fixing it";
                        log_error("report_photo.php: (3): ", mysql_error());
                    }
                }
                else if($query)
                    echo "User has already been reported for this";
                else
                {
                    echo "Something went wrong. We are working on fixing it";
                    log_error("report_photo.php: (2): ", mysql_error());
                }
            }
            else
                echo "Invalid photo ID";
        }
        else
        {
            echo "Something went wrong. We are working on fixing it";
            log_error("report_photo.php: (1): ", mysql_error());
        }
    }
    else
        echo "You can't report yourself. That would be stupid.";
}
else
    echo "Invalid user ID";