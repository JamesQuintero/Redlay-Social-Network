<?php
@include('init.php');
if(isset($_SESSION['id']))
{
    header("Location: http://m.redlay.com");
    exit();
}
include('../universal_functions.php');
include('security_checks.php');


include('../confirmation_code.php');
if($success)
    header("Location: http://m.redlay.com/thankyou.php");
?>
