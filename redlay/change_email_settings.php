<?php
@include('init.php');
include('universal_functions.php');
$allowed="uesrs";
include('security_checks.php');


$checkboxes=$_POST['checkboxes'];

$bool=true;
for($x = 0; $x < sizeof($checkboxes); $x++)
{
    if($checkboxes[$x]!='1'&&$checkboxes[$x]!='0')
        $bool=false;
}

if($bool==true)
{
    $checkboxes=implode('|^|*|', $checkboxes);
    $query=mysql_query("UPDATE user_data SET email_settings='$checkboxes' WHERE user_id=$_SESSION[id]");
    if($query)
        echo "Change successful!";
    else
    {
    		echo "Something went wrong";
    		log_error("change_email_settings.php: (1): ", mysql_error());
    }
}
else
    echo "Something is wrong with the input";
