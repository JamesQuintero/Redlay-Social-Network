<?php
@include('init.php');
if(isset($_SESSION['id'])||isset($_SESSION['page_id']))
{
    header("Location: http://m.redlay.com");
    exit();
}
include('../universal_functions.php');

$email=clean_string($_POST['email']);
if(filter_var($email, FILTER_VALIDATE_EMAIL) == true&&strlen($email)<=255)
{
    $query=mysql_query("SELECT passkey FROM temp_users WHERE email='$email'");
    if($query&&mysql_num_rows($query)==1)
    {
        $array=mysql_fetch_row($query);
        $confirm_code=$array[0];
        if(mail($email, 'Registration Confirmation', 'Your Confirmation link. Click on this link to activate your account http://m.redlay.com/confirmation.php?passkey='.$confirm_code, 'From: redlay'))
            echo "Success";
        else
            echo "Something went wrong when sending the email";
    }
    else
        echo "Email has never been submitted for verification.";
}
else
    echo "There is a problem with the email you submitted";
?>
