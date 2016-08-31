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
    //salt will be truncated if over 22 characters
    $password=crypt($password, '$2a$07$27'.$email.'SECRET_SALT_STRING');
    
    //check previous logins
    $valid=false;
    $query=mysql_query("SELECT timestamps FROM page_login_attempts WHERE ip_address='".$_SERVER[HTTP_X_FORWARDED_FOR]."'");
    if($query)
    {
        if(mysql_num_rows($query)==1)
        {
            $array=mysql_fetch_row($query);
            $timestamps=explode('|^|*|', $array[0]);
            
            $time_since=get_date()-$timestamps[0];
            if($time_since<900&&sizeof($timestamps)==5)
                echo "Please wait ".format_number($time_since/60)." minutes before trying again";
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
                $query=mysql_query("UPDATE page_login_attempts SET timestamps='$timestamps' WHERE ip_address='".$_SERVER[HTTP_X_FORWARDED_FOR]."'");
            }
        }
        else
            $valid=true;
    }
    

    if($valid)
    {
        $query=mysql_query("SELECT id, account_id FROM pages WHERE email='$email' AND password='$password' LIMIT 1");
        if($query&&mysql_num_rows($query)==1)
        {
            $array=mysql_fetch_row($query);
            $ID=$array[0];
            $account_id=$array[1];

            if(!page_id_terminated($ID))
            {
                //sets the user to be logged in for a month
                setcookie('acc_page', $account_id, strtotime('+90 days'), null, null, false, true);
                $_SESSION['page_id']=$ID;

                //deletes previous failed logins
                $query=mysql_query("DELETE FROM page_login_attempts WHERE ip_address='".$_SERVER[HTTP_X_FORWARDED_FOR]."'");
                
                //records login
                record_page_login();

                echo "Logged in";
                //checks of user has redlay gold
//                $_SESSION['gold']=has_redlay_gold($_SESSION['id'], 'any');
            }
            else
               echo "Your account has been terminated";
        }

        //if login failed
        else
        {
            $query=mysql_query("SELECT timestamps FROM page_login_attempts WHERE ip_address='".$_SERVER[HTTP_X_FORWARDED_FOR]."'");
            if($query)
            {
                //if no previous failed login attempts
                if(mysql_num_rows($query)==0)
                    $query=mysql_query("INSERT INTO page_login_attempts SET ip_address='".$_SERVER[HTTP_X_FORWARDED_FOR]."', timestamps='".get_date()."'");
                else
                {
                    $array=mysql_fetch_row($query);
                    $timestamps=explode('|^|*|', $array[0]);

                    //adds current attempt
                    $timestamps[]=get_date();

                    $timestamps=implode('|^|*|', $timestamps);
                    $query=mysql_query("UPDATE page_login_attempts SET timestamps='$timestamps' WHERE ip_address='".$_SERVER[HTTP_X_FORWARDED_FOR]."'");
                }
            }

            echo "Email or password are incorrect";
        }
    }
}
else
    echo "One or more fields are empty";