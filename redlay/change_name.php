<?php
@include('init.php');
include('universal_functions.php');
$allowed="users";
include('security_checks.php');

$first_name=clean_string($_POST['first_name']);
$last_name=clean_string($_POST['last_name']);

if($first_name!=''&&$last_name!='')
{
    $query=mysql_query("UPDATE users SET firstName='$first_name', lastName='$last_name' WHERE id=$_SESSION[id]");
    if($query)
       echo "Change successful";
    else
    {
       echo "Something went wrong. We are working on fixing it";
       log_error("change_name.php: ", mysql_error());
    }
}
else
    echo "One or more fields are emtpy";
