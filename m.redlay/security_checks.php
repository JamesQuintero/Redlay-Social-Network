<!-- <?php
if(isset($_SESSION['id']))
{
   if(completed_registration_intro($_SESSION['id'])==false)
    {
        header("Location: http://m.redlay.com/registration_intro.php");
        exit();
    }

    //if the user's account is terminated
    if(user_id_terminated($_SESSION['id']))
    {
        header("Location: http://m.redlay.com/account_terminated.php");
        exit();
    }

//    if(!is_correct_ip_address())
//    {
//        header("Location: http://m.redlay.com/new_ip_address.php");
//        exit();
//    }
}
else if(isset($_SESSION['page_id']))
{
    if(!completed_page_registration_intro($_SESSION['page_id'])==false)
    {
        header("Location: http://m.redlay.com/page_registration_intro.php");
        exit();
    }

    //if the user's account is terminated
    if(page_id_terminated($_SESSION['page_id']))
    {
        header("Location: http://m.redlay.com/account_terminated.php");
        exit();
    }
}
?>
 -->




 <?php
//$allowed
//user = only allows user access
//all = allows all access

if($allowed=="users"||$allowed=="all")
{
    if(isset($_COOKIE['acc_id']))
    {
        //if user is completely logged in
        if(isset($_SESSION['id']))
        {
            //if registration intro hasn't been completed
            if(completed_registration_intro($_SESSION['id'])==false)
            {
                header("Location: http://m.redlay.com/registration_intro.php");
                exit();
            }

            //if the user's account is terminated
            if(user_id_terminated($_SESSION['id']))
            {
                header("Location: http://m.redlay.com/account_terminated.php");
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
        header("Location: http://m.redlay.com");
        exit();
    }
}