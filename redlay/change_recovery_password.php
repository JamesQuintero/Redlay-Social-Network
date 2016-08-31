<?php
@include('init.php');
include('universal_functions.php');
$allowed="all";
include('security_checks.php');

//if user is logged in
if(isset($_SESSION['id'])||isset($_SESSION['page_id']))
{
    header("Location: http://www.redlay.com");
    exit();
}

$num=(int)($_POST['num']);

if($num==1)
{
    $password=clean_string($_POST['password']);
    $password2=clean_string($_POST['password2']);
    $passkey=clean_string($_POST['passkey']);

    if($password!=''&&$password2!='')
    {
        if($password==$password2)
        {
            //blowfish hashes password for database storage
            //salt consists of email and hardcoded string
            $password=crypt($password, '$2a$07$27'.$email.'SECRET_SALT_STRING');


            $query=mysql_query("SELECT email, type FROM password_recovery WHERE passkey='$passkey' LIMIT 1");
            if($query&&mysql_num_rows($query)==1)
            {
                $array=mysql_fetch_row($query);
                $email=$array[0];
                $type=$array[1];

                if($type=='user')
                {
                    $query=mysql_query("UPDATE users SET password='$password' WHERE email='$email'");
                    if($query)
                    {
                        $query=mysql_query("DELETE FROM password_recovery WHERE passkey='$passkey'");
                        if($query)
                            echo "Change Successful!";
                        else
                        {
                            echo "Something went wrong. We are working on fixing it";
                            log_error("change_recovery_password.php: (2): ", mysql_error());
                        }
                    }
                    else
                    {
                        echo "Something went wrong. We are working on fixing it";
                        log_error("change_recovery_password.php: (1): ", mysql_error());
                    }
                }
                else if($type=='page')
                {
                    $query=mysql_query("UPDATE pages SET password='$password' WHERE email='$email'");
                    if($query)
                    {
                        $query=mysql_query("DELETE FROM password_recovery WHERE passkey='$passkey'");
                        if($query)
                            echo "Change Successful!";
                        else
                        {
                            echo "Something went wrong. We are working on fixing it";
                            log_error("change_recovery_password.php: (4): ", mysql_error());
                        }
                    }
                    else
                    {
                        echo "Something went wrong. We are working on fixing it";
                        log_error("change_recovery_password.php: (3): ", mysql_error());
                    }
                }
            }
            else
                echo "Passkey doesn't exist";
        }
        else
            echo "Passwords do not match";
    }
    else
        echo "Password fields are empty";
}
else if($num==2)
{
    $passkey=clean_string($_POST['passkey']);
    
    $query=mysql_query("DELETE FROM password_recovery WHERE passkey='$passkey'");
    if(!$query)
        log_error("change_recovery_password.php: (4): ", mysql_error());
}