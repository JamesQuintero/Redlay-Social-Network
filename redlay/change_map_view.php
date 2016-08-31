<?php
@include('init.php');
include('universal_functions.php');
$allowed="users";
include('security_checks.php');

$type=clean_string($_POST['type']);
if($type=="grid"||$type=="open")
{
    $query = mysql_query("UPDATE user_maps SET map_type='$type' WHERE user_id=$_SESSION[id]");
    if(!$query)
    {
    	echo "Something went wrong";
    	log_error("change_map_view.php: ", mysql_error());
    }
}