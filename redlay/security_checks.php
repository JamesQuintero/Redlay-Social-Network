<?php
//$allowed
//user = only allows user access
//page = only allows page access
//both = allows user and page access
//all = allows all access

if($allowed=="users"||$allowed=="both"||$allowed=="all")
{
    if(isset($_COOKIE['acc_id']))
    {
        //if user is completely logged in
        if(isset($_SESSION['id']))
        {
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
        
        //sets user's session id
        else
        {
            //gets the user id and sets it
            $query=mysql_query("SELECT id FROM users WHERE account_id='$_COOKIE[acc_id]' LIMIT 1");
            if($query&&mysql_num_rows($query)==1)
            {
                $array=mysql_fetch_row($query);
                $_SESSION['id']=$array[0];
            }

            //deletes cookie if acc_id isn't valid or doesn't exist
            else
                setcookie('acc_id', '', (time()-(1)), null, null, false, true);
        }
    }

    //sets user's cookie acc_ids
    else if(isset($_SESSION['id']))
    {
        $query=mysql_query("SELECT account_id FROM users WHERE id=$_SESSION[id] LIMIT 1");
        if($query&&mysql_num_rows($query)==1)
        {
            $array=mysql_fetch_row($query);
            $account_id=$array[0];

            //sets the user to be logged in for 3 months
            setcookie('acc_id', $account_id, strtotime('+90 days'), null, null, false, true);
        }
    }
    
    //redirects if user isn't logged in and only users can access
    else if($allowed=="users")
    {
        header("Location: http://www.redlay.com");
        exit();
    }
}

if($allowed=="pages"||$allowed="all")
{
    //allows pages
    if(isset($_COOKIE['acc_page']))
    {
        //if page is completely logged in
        if(isset($_SESSION['page']))
        {
            //if registration intro hasn't been completed
            if(completed_page_registration_intro($_SESSION['page_id'])==false)
            {
                header("Location: http://www.redlay.com/page_registration_intro.php");
                exit();
            }

            //if the user's account is terminated
            if(page_id_terminated($_SESSION['page_id']))
            {
                header("Location: http://www.redlay.com/page_account_terminated.php");
                exit();
            }
        }
        
        //sets page's session id
        else
        {
            //gets the page id and sets it
            $query=mysql_query("SELECT id FROM pages WHERE account_id='$_COOKIE[acc_page]' LIMIT 1");
            if($query&&mysql_num_rows($query)==1)
            {
                $array=mysql_fetch_row($query);
                $_SESSION['page_id']=$array[0];
            }

            //deletes cookie if acc_page isn't valid or doesn't exist
            else
                setcookie('acc_page', '', (time()-(1)), null, null, false, true);
        }
    }

    //sets page's cookie acc_page
    else if(isset($_SESSION['page_id']))
    {
        $query=mysql_query("SELECT account_id FROM pages WHERE id=$_SESSION[page_id] LIMIT 1");
        if($query&&mysql_num_rows($query)==1)
        {
            $array=mysql_fetch_row($query);
            $account_id=$array[0];

            //sets the user to be logged in for 3 months
            setcookie('acc_page', $account_id, strtotime('+90 days'), null, null, false, true);
        }
    }
    
    //redirects if page isn't logged in and only pages can access
    else if($allowed=="pages")
    {
        header("Location: http://www.redlay.com");
        exit();
    }
}

if($allowed=="both")
{
    //redirects if user nor page are logged in
    if(!isset($_SESSION['id'])&&!isset($_SESSION['page_id']))
    {
        header("Location: http://www.redlay.com");
        exit();
    }
}