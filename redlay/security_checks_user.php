<?php

if(isset($_COOKIE['acc_id']))
{
    if(isset($_SESSION['id']))
    {
        //if cookie isn't set yet user is logged in
        if(!isset($_COOKIE['acc_id']))
        {
            $query=mysql_query("SELECT account_id FROM users WHERE id=$_SESSION[id] LIMIT 1");
            if($query&&mysql_num_rows($query)==1)
            {
                $array=mysql_fetch_row($query);
                $account_id=$array[0];

                setcookie('acc_id', $account_id, time()+3600*24*30, null, null, false, true);
            }
        }
        
        //if registration intro hasn't been completed
        if(completed_registration_intro($_SESSION['id'])==false)
        {
            header("Location: http://www.redlay.com/registration_intro.php");
            exit();
        }

        //if the user's account is terminated
        if(user_id_terminated($_SESSION['id']))
        {
            header("Location: http://www.redlay.com/account_terminated.php");
            exit();
        }

    }
    else if(isset($_COOKIE['acc_id']))
    {
        //sets the user to be logged in for a month
        $query=mysql_query("SELECT id FROM users WHERE account_id='$_COOKIE[acc_id]' LIMIT 1");
        if($query&&mysql_num_rows($query)==1)
        {
            $array=mysql_fetch_row($query);
            $_SESSION['id']=$array[0];
        }

        //deletes cookie if acc_id isn't valid or doesn't exist
        else
            setcookie('acc_id', '0', (time()-(1)), null, null, false, true);
    }
    else
    {
        header("Location: http://www.redlay.com");
        exit();
    }
}

//sets user's session ID
else
{
    if(isset($_SESSION['id']))
    {
        $query=mysql_query("SELECT account_id FROM users WHERE id=$_SESSION[id] LIMIT 1");
        if($query&&mysql_num_rows($query)==1)
        {
            $array=mysql_fetch_row($query);
            $account_id=$array[0];
            
            //sets the user to be logged in for a month
            setcookie('acc_id', $account_id, strtotime('+30 days'), null, null, false, true);
        }
    }
}