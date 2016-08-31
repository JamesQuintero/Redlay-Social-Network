<?php
@include('init.php');
if(isset($_SESSION['id'])||isset($_SESSION['page_id']))
{
    header("Location: http://www.redlay.com/index.php");
    exit();
}
include('universal_functions.php');

$email=clean_string($_POST['email']);
if(filter_var($email, FILTER_VALIDATE_EMAIL) == true&&strlen($email)<=255)
{
    $query=mysql_query("SELECT passkey, email_resend FROM temp_users WHERE email='$email'");
    if($query&&mysql_num_rows($query)==1)
    {
        $array=mysql_fetch_row($query);
        $confirm_code=$array[0];
        $email_resend=$array[1];
        
        $time=get_date()-(int)($email_resend);
        if($time>60||$array[1]=='')
        {
            if(sendAWSEmail($email, 'Registration Confirmation', 'Your Confirmation link. Click on this link to activate your account http://www.redlay.com/confirmation.php?passkey='.$confirm_code))
            {
                $query=mysql_query("UPDATE temp_users SET email_resend='".get_date()."' WHERE email='$email'");
                echo "Success";
            }
            else
            {
                echo "Something went wrong when sending the email";
                log_error("resend_verification_email.php: ", "email failed to send");
            }
        }
        else
            echo "Please wait about minute before trying again";
    }
    else
        echo "Email has never been submitted for verification.";
}
else
    echo "There is a problem with the email you submitted";