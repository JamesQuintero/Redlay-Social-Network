<?php
@include('init.php');
include('universal_functions.php');
$allowed="users";
include('security_checks.php');

$value=clean_string($_POST['value']);
if($value=="yes"||$value=="no")
{
    $query=mysql_query("UPDATE user_display SET background_fixed='$value' WHERE user_id=$_SESSION[id]");
    if($query)
        echo "Change successful!";
    else
    {
        echo "Something went wrong. We are working to fix it";
        log_error("change_background_fixed.php: ", mysql_error());
    }
}
