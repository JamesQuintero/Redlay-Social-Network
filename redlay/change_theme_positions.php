<?php
@include('init.php');
include('universal_functions.php');
$allowed="users";
include('security_checks.php');

// if(!has_redlay_gold($_SESSION['id']))
// {
//     header("Location: http://www.redlay.com");
//     exit();
// }


$type=clean_string($_POST['type']);
$x_cord=(int)($_POST['x_cord']);
$y_cord=(int)($_POST['y_cord']);
$width=(int)($_POST['width']);
$height=(int)($_POST['height']);

// usleep(250);

if($x_cord>=0&&$x_cord<=100)
{
    if($y_cord>=0&&$y_cord<=100)
    {
        if($width>=0&&$width<=100)
        {
            if($height>=0&&$height<=100)
            {
                $query=mysql_query("UPDATE themes SET x_cord=$x_cord, y_cord=$y_cord, width=$width, height=$height WHERE user_id=$_SESSION[id]");
                if($query)
                    echo "Change successful";
                else
                {
                    echo "Something went wrong. We are working on fixing it";
                    log_error("change_theme_positions.php: (1): ", mysql_error());
                }
            }
        }
    }
}