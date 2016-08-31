<?php
@include('init.php');
include('universal_functions.php');
$allowed="users";
include('security_checks.php');

$body=clean_string($_POST['body']);

if($body!='')
{
    $message=$body." - ".get_user_name($_SESSION['id'])." | ".$_SESSION['id'];
    if(sendAWSEmail("redlayhelp@gmail.com", "Contact", $message))
        echo "Message has been sent";
}
else
    echo "Content can't be empty";