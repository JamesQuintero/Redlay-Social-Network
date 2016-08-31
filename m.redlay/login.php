<?php
@include('init.php');
if(isset($_SESSION['id'])||isset($_SESSION['page_id']))
{
    header("Location: http://m.redlay.com");
    exit();
}
include('../universal_functions.php');


$email=clean_string($_POST['email']);
$password=clean_string($_POST['password']);

if((!empty($_POST['email'])) && (!empty($_POST['password'])))
{
    //uses blowfish to hash the password for verification
    //salt will be truncated if over 22 characters
    $password=crypt($password, '$2a$07$27'.$email.'cad37e8a5fc1');

    $query=mysql_query("SELECT id, ip_addresses, account_id FROM users WHERE email='$email' AND password='$password' LIMIT 1");
    if($query&&mysql_num_rows($query)==1)
    {
        $array=mysql_fetch_row($query);
        $ID=$array[0];
        $ip_addresses=explode('|^|*|', $array[1]);
        $account_id=$array[2];

        if(!user_id_terminated($ID))
        {
            //sets the user to be logged in for a month
            setcookie('acc_id', $account_id, time()+3600*24*30, null, null, false, true);
            $_SESSION['id']=$ID;


        }
        else
           echo "Your account has been terminated";

    }
    else
        echo "Email or password are incorrect";
}
else
    echo "One or more fields are empty";