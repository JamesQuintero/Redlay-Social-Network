<?php 
@include('init.php');
if(isset($_SESSION['id'])||isset($_SESSION['page_id']))
{
    header("Location: http://m.redlay.com");
    exit();
}
include('../universal_functions.php');

//test username for validation
$firstName=clean_string($_POST['first_name']);
$lastName=clean_string($_POST['last_name']);
$password=clean_string($_POST['password']);
$confirmPassword=clean_string($_POST['confirm_password']);
$email=clean_string($_POST['email']);
$confirmEmail=clean_string($_POST['confirm_email']);


    //tests if names contain weird characters
if(!empty($_POST['first_name']) && !empty($_POST['last_name']) && !empty($_POST['email']) && !empty($_POST['password']) && !empty($_POST['confirm_password']) && !empty($_POST['confirm_email']))
{
    if(strlen($firstName) < 20&&strlen($lastName) < 20)
    {
        //asks if email already exists and makes sure it's legit
        if(user_exists($email)== false)
        {
            if((filter_var($email, FILTER_VALIDATE_EMAIL) == true) && (filter_var($confirmEmail, FILTER_VALIDATE_EMAIL) == true)&& strlen($email) <255 && strlen($confirmEmail) <255)
            {
                //tests to see if emails and passwords match up
                if(($password==$confirmPassword)&&($email==$confirmEmail))
                {
                    //creates random confirmation code
                    $confirm_code=sha1(uniqid(rand()));
                    
                    //blowfish hashes password for database storage
                    $password=crypt($password, '$2a$07$27'.$email.'cad37e8a5fc1');


                    $result=mysql_query("INSERT INTO temp_users SET passkey='$confirm_code', firstName='$firstName', lastName='$lastName', password='$password', email='$email', timestamp='".get_date()."'");
                    if($result)
                    {
                        if(mail($email, 'Registration Confirmation', 'Click on this link to start using redlay!  http://m.redlay.com/confirmation.php?passkey='.$confirm_code, 'From: redlay'))
                            echo "Email has been sent! Email may be in spam folder";
                        else
                        {
                            echo "Something went wrong";
                            log_error("Register_after.php - 1: ",mysql_error());
                        }
                    }
                    else
                    {
                        echo "Something went wrong";
                        log_error("Register_after.php - 2: ",mysql_error());
                    }
                }
                else
                {
                    echo "Something went wrong";
                    log_error("Register_after.php - 3: ",mysql_error());
                }
            }
            else
            {
                echo "Something went wrong";
                log_error("Register_after.php - 4: ",mysql_error());
            }
        }
        else
            echo "Email is already in use with another account";
    }
    else
    {
        echo "Something went wrong";
        log_error("Register_after.php - 6: ",mysql_error());
    }
}
else
    echo "One or more fields are empty";