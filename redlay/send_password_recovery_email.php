<?php
@include('init.php');
include('universal_functions.php');
$allowed="all";
include('security_checks.php');

if(isset($_SESSION['id'])||isset($_SESSION['page_id']))
{
    header("Location: http://www.redlay.com");
    exit();
}


$email=clean_string($_POST['email']);
$type=clean_string($_POST['type']);
$passkey=sha1(uniqid(rand()));
if($email!=''&&($type=='user'||$type=='page'))
{
    if(filter_var($email, FILTER_VALIDATE_EMAIL) == true)
    {
        if($type=='user')
        {
            //checks if user actually exists
            $query=mysql_query("SELECT id FROM users WHERE email='$email'");
            if($query&&mysql_num_rows($query)==1)
            {
                //if user is already in database
                $exists=false;
                $valid=false;
                $difference=0;
                $query=mysql_query("SELECT timestamp FROM password_recovery WHERE email='$email' AND type='$type' LIMIT 1");
                if($query&&mysql_num_rows($query)==1)
                {
                    $exists=true;
                    $array=mysql_fetch_row($query);
                    $timestamp=$array[0];

                    $date=get_date();
                    $difference=$date-$timestamp;
                    if($difference>=60)
                    {
                        $valid=true;
                        $query=mysql_query("UPDATE password_recovery SET timestamp=".get_date()." WHERE email='$email' AND type='$type'");
                    }
                }

                //if user isn't in database
                if($exists==false)
                    $query=mysql_query("INSERT INTO password_recovery SET passkey='$passkey', email='$email', type='$type', timestamp=".get_date());

                if($exists==false||$valid==true)
                {
                    //sends user an email of the passkey
                    if(sendAWSEmail($email, 'Password Recovery', "Follow the link to reset your password. http://www.redlay.com/password_confirmation.php?passkey=$passkey \n - redlay"))
                        echo "Email sent!";
                    else
                    {
                        echo "Email failed to send";
                        log_error("send_password_recovery_email.php: ", "AWS email failed to send");
                    }
                }
                else if($valid==false)
                    echo "Please wait ".(60-$difference)." seconds before trying again";
            }
            
            //resends verification email
            else
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
                            echo "Email sent!";
                        }
                        else
                        {
                            echo "Something went wrong when sending the email";
                            log_error("send_password_recovery_email.php: (2): ", "AWS email failed to send");
                        }
                    }
                    else
                        echo "Please wait about minute before trying again";
                }
                else
                    echo "User doesn't exist";
            }
        }
        else if($type=='page')
        {
            //checks if page actually exists
            $query=mysql_query("SELECT id FROM pages WHERE email='$email'");
            if($query&&mysql_num_rows($query)==1)
            {
                //if user is already in database
                $exists=false;
                $valid=false;
                $difference=0;
                $query=mysql_query("SELECT timestamp FROM password_recovery WHERE email='$email' AND type='$type' LIMIT 1");
                if($query&&mysql_num_rows($query)==1)
                {
                    $exists=true;
                    $array=mysql_fetch_row($query);
                    $timestamp=$array[0];

                    $date=get_date();
                    $difference=$date-$timestamp;
                    if($difference>=60)
                    {
                        $valid=true;
                        $query=mysql_query("UPDATE password_recovery SET timestamp=".get_date()." WHERE email='$email' AND type='$type'");
                    }
                    //$query=mysql_query("DELETE FROM password_recovery WHERE email='$email' AND type='$type'");
                }

                //if user isn't in database
                if($exists==false)
                    $query=mysql_query("INSERT INTO password_recovery SET passkey='$passkey', email='$email', type='$type', timestamp=".get_date());

                if($exists==false||$valid==true)
                {
                    //sends user an email of the passkey
                    if(sendAWSEmail($email, 'Password Recovery', "Follow the link to reset your password. http://www.redlay.com/password_confirmation.php?passkey=$passkey \n - redlay"))
                        echo "Email sent!";
                    else
                    {
                        echo "Email failed to send";
                        log_error("send_password_recovery_email.php: (3): ", "AWS email failed to send");
                    }
                }
                else if($valid==false)
                    echo "Please wait ".(60-$difference)." seconds before trying again";
            }
            else
                echo "User doesn't exist";
        }
    }
    else
        echo "Please enter a valid email";
}
else
    echo "Email field is empty";