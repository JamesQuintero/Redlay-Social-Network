<?php
@include('init.php');
include('universal_functions.php');
$allowed="users";
include("security_checks.php");

$query=mysql_query("UPDATE user_display SET registration_intro='yes' WHERE user_id=$_SESSION[id]");
if($query)
{
    $referrer_id=(int)($_POST['referrer_id']);
    
    if(is_id($referrer_id)&&user_id_exists($referrer_id)&&$referrer_id!=$_SESSION['id'])
    {
        $query=mysql_query("SELECT points FROM user_data WHERE user_id=$referrer_id LIMIT 1");
        if($query&&mysql_num_rows($query)==1)
        {
            $array=mysql_fetch_row($query);
            $points=$array[0];
            
            $points+=25;
            
            $query=mysql_query("UPDATE user_data SET points=$points WHERE user_id=$referrer_id");
            if(!$query)
            {
                echo "Error! We are sorry for the inconvienence. Please try again.";
                log_error("finish_registration_intro.php: ".mysql_error());
            }
        }
    }
    
    echo "Success";
}
else
{
    echo "Error! We are sorry for the inconvienence. Please try again.";
    log_error("finish_registration_intro.php: ".mysql_error());
}
?>
