<?php
@include('init.php');
include('universal_functions.php');
$allowed="all";
include('security_checks.php');

if(isset($_SESSION['id'])||issset($_SESSION['page_id']))
{
    header("Location: http://www.redlay.com");
    exit();
}

$name=clean_string($_POST['name']);
$email=clean_string($_POST['email']);
$type=clean_string($_POST['type']);
$other_type=clean_string($_POST['other_type']);
$password=clean_string($_POST['password']);
$confirm_password=clean_string($_POST['confirm_password']);


if($name!=''&&$email!=''&&$type!=''&&$password!=''&&$confirm_password!='')
{   
    //putting in list of other
    if(($type=='Company'||$type=='Person'||$type=='Other')&&($other_type=='Place'||$other_type=='Product'||
            $other_type=='Movie'||$other_type=='TV Show'||$other_type=='Book'||$other_type=='Website'||$other_type=='Charity'||$other_type=='Quote/Saying'))
    {
        //check if email is already in use
        if(!page_email_exists($email))
        {
            //query returns whether email is pending
            $query=mysql_query("SELECT passkey FROM temp_pages WHERE email='$email' LIMIT 1");
            if($query&&mysql_num_rows($query)==0)
            {
                //checks if passwords match each other
                if($password==$confirm_password&&$password2==$confirm_password2)
                {
                    //validates email
                    if(filter_var($email, FILTER_VALIDATE_EMAIL)==true)
                    {
                        //encryptes up a code and the passwords
                        $confirm_code=sha1(uniqid(rand()));

                        //blowfish hashes password for database storage
                        $password=crypt($password, '$2a$07$27'.$email.'cad37e8a5fc1');

                        if($type!='Other')
                            $other_type='';
                        
                        $query=mysql_query("INSERT INTO temp_pages SET passkey='$confirm_code', name='$name', email='$email', type='$type', type_other='$other_type' password='$password', timestamp='".get_date()."'");
                        if($query)
                        {
                            if(mail($email, "Page Registration Confirmation", 'Your Confirmation link. Click on this link to activate your account http://www.redlay.com/page_confirmation.php?passkey='.$confirm_code, "From: redlay"))
                                echo "Email has been sent! Email may be in spam folder";
                            else
                            {
                                echo "Something went wrong";
                                send_mail_error("register_page.php: ", "Email failed to send to ".$email);
                            }
                        }
                        else
                        {
                            echo "Something went wrong";
                            send_mail_error("register_page.php: ", mysql_error());
                        }
                    }
                    else
                        echo "Email is invalid";
                }
                else
                    echo "Passwords do not match";
            }
            else
                echo "Email is already pending for verification";
        }
        else
            echo "Email is already being used by another page account";
    }
    else
        echo "Invalid page type";
}
else
    echo "One or more fields are empty";