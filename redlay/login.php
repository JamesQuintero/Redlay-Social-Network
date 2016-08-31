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
$password=clean_string($_POST['password']);


if((!empty($_POST['email'])) && (!empty($_POST['password'])))
{
    //uses blowfish to hash the password for verification
    //salt consists of email and hardcoded string
    $password=crypt($password, '$2a$07$27'.$email.'SECRET_SALT_STRING');
    
    //check previous logins
    $valid=false;
    $query=mysql_query("SELECT timestamps FROM login_attempts WHERE email='".$email."'");
    if($query)
    {
        if(mysql_num_rows($query)==1)
        {
            $array=mysql_fetch_row($query);
            $timestamps=explode('|^|*|', $array[0]);
            
            $time_since=get_date()-$timestamps[0];
            //throttles login attempts. Might remove if popular users are being locked out. 
            if($time_since<900&&sizeof($timestamps)==5)
                echo "Please wait ".(15-format_number($time_since/60))." minutes before trying again";
            else
            {
                $valid=true;
                $temp_timestamps=array();
                
                for($x = 0; $x < sizeof($timestamps); $x++)
                {
                    $temp_time=get_date()-$timestamps[$x];
                    
                    if($temp_time>900)
                        $temp_timestamps[]=$timestamps[$x];
                }
                
                $timestamps=implode('|^|*|', $temp_timestamps);
                $query=mysql_query("UPDATE login_attempts SET timestamp='$timestamps' WHERE email='".$email."'");
            }
        }
        else
            $valid=true;
    }
    

    if($valid)
    {
        $query=mysql_query("SELECT id, account_id FROM users WHERE email='$email' AND password='$password' LIMIT 1");
        if($query&&mysql_num_rows($query)==1)
        {
            $array=mysql_fetch_row($query);
            $ID=$array[0];
            $account_id=$array[1];

            if(!user_id_terminated($ID))
            {
                //sets the user to be logged in for 3 months
                setcookie('acc_id', $account_id, strtotime('+90 days'), null, null, false, true);
                $_SESSION['id']=$ID;
                
                //deletes previous failed logins
                $query=mysql_query("DELETE FROM login_attempts WHERE email='".$email."'");

                //records login
                record_login();

                //checks of user has redlay gold
                $_SESSION['gold']=has_redlay_gold($_SESSION['id'], 'any');
            }
            else
               echo "Your account has been terminated";
        }

        //if login failed
        else
        {
            $query=mysql_query("SELECT timestamps FROM login_attempts WHERE email='".$email."'");
            if($query)
            {
                //if no previous failed login attempts
                if(mysql_num_rows($query)==0)
                    $query=mysql_query("INSERT INTO login_attempts SET email='".$email."', timestamps='".get_date()."'");
                else
                {
                    $array=mysql_fetch_row($query);
                    $timestamps=explode('|^|*|', $array[0]);

                    //adds current attempt
                    $timestamps[]=get_date();

                    $timestamps=implode('|^|*|', $timestamps);
                    $query=mysql_query("UPDATE login_attempts SET timestamps='$timestamps' WHERE email='".$email."'");
                }
            }

            echo "Email or password are incorrect";
        }
    }
}
else
    echo "One or more fields are empty";