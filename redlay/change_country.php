<?php
@include('init.php');
include('universal_functions.php');
$allowed="users";
include('security_checks.php');

$country=clean_string($_POST['country']);
$countries=get_countries();
if(in_array($country, $countries))
{
    $query=mysql_query("UPDATE user_data SET country='$country' WHERE user_id=$_SESSION[id]");
    if($query)
        echo "Change Successful!";
    else
    {
        echo "Something went wrong";
        log_error("change_country.php: ", mysql_error());
    }
}
else
    echo "Invalid country. Fail";
