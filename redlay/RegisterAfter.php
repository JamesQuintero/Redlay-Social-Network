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


//test username for validation
$firstName=clean_string($_POST['firstName']);
$lastName=clean_string($_POST['lastName']);
$email=clean_string($_POST['email']);
$password=clean_string($_POST['password']);


if(isset($_POST['firstName'])&&$firstName!='')
{
    if(isset($_POST['lastName'])&&$lastName!='')
    {
        if(isset($_POST['email'])&&$email!='')
        {
            if(isset($_POST['password'])&&$password!='')
            {
                if(strlen($firstName) < 20&&strlen($lastName) < 20)
                {
                    if((filter_var($email, FILTER_VALIDATE_EMAIL) == true)&& strlen($email) <255)
                    {
                        if(is_valid_email($email))
                        {
                            //checks if email is already in use
                            $query=mysql_query("SELECT id FROM users WHERE email='$email' LIMIT 1");
                            if(mysql_num_rows($query)==0)
                            {
                                //checks if email has already been sent
                                $query=mysql_query("SELECT passkey WHERE email='$email' LIMIT 1");
                                if(mysql_num_rows($query)==0)
                                {

                                    //creates random confirmation code
                                    $confirm_code=sha1(uniqid(rand()));

                                    //blowfish hashes password for database storage
                                    //salt consists of email and hardcoded string
                                    $password=crypt($password, '$2a$07$27'.$email.'SECRET_SALT_STRING');


                                    $result=mysql_query("INSERT INTO temp_users SET passkey='$confirm_code', firstName='$firstName', lastName='$lastName', password='$password', email='$email', timestamp='".get_date()."', email_resend='".get_date()."'");
                                    if($result)
                                    {
                                        if(sendAWSemail($email, 'Registration Confirmation', 'Click on this link to start using redlay!  http://www.redlay.com/confirmation.php?passkey='.$confirm_code))
                                            echo "Email has been sent! Email may be in spam folder";
                                        else
                                        {
                                            echo "Something went wrong";
                                            send_mail_error("Register_after.php - 1: ",mysql_error());
                                        }
                                    }
                                    else
                                    {
                                        echo "Something went wrong";
                                        send_mail_error("Register_after.php - 2: ",mysql_error());
                                    }
                                }
                                else
                                    echo "Email has already been sent. Please check your inbox or spam folder";
                            }
                            else
                                echo "Email is already in use with another account";
                        }
                        else
                            echo "Please use an actual email. We won't spam you";
                    }
                    else
                        echo "Email is invalid";
                }
                else
                {
                    echo "Something went wrong";
                    send_mail_error("Register_after.php - 6: ",mysql_error());
                }
            }
            else
                echo "Password field is empty";
        }
        else
            echo "Email field is empty";
    }
    else
        echo "Last name field is empty";
}
else
    echo "First name field is empty";
?>