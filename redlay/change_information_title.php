<?php
@include('init.php');
include('universal_functions.php');
$allowed="users";
include('security_checks.php');

$text=clean_string($_POST['text']);

if($text!='')
{
   $query=mysql_query("UPDATE user_display SET information_title='$text' WHERE user_id=$_SESSION[id]");
   if($query)
      echo "Change successful";
   else
   {
      echo "Something went wrong. We are working on fixing it";
      log_error("change_friend_title.php: ", mysql_error());
   }
}
