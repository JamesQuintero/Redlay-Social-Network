<?php
@include('init.php');
include('universal_functions.php');
//$allowed="users";
//include('security_checks.php');
if(!isset($_SESSION['id']))
{
    header("Location: http://www.redlay.com");
    exit();
}


$theme=clean_string($_POST['theme']);

if(is_valid_theme($theme))
{
    $query=mysql_query("SELECT theme, bought_themes FROM themes WHERE user_id=$_SESSION[id] LIMIT 1");
    if($query)
    {
        //if first time changing theme
        if(mysql_num_rows($query)==0)
            $query=mysql_query("INSERT INTO themes SET user_id=$_SESSION[id], theme='$theme' ");
        else
        {
            $valid=false;
            $gold_theme=gold_redlay_theme($theme);
            
            //if theme is a default theme
            if($gold_theme==false)
                $valid=true;
            
            //if theme has to be bought
            else if($gold_theme==true)
            {
                $array=mysql_fetch_row($query);
                $bought_themes=explode('|^|*|', $array[1]);
                
                if(in_array($theme, $bought_themes))
                    $valid=true;
                else
                    $valid=false;
            }
            
            //if theme is for gold members
            if($valid)
                $query=mysql_query("UPDATE themes SET theme='$theme' WHERE user_id=$_SESSION[id]");
            else
                echo "Sorry, but you have to purchase this";
        }
    }
    else
    {
        echo "Something went wrong. We are working on fixing it";
        log_error("redlay_theme.php: ", mysql_error());
    }
}
else
    echo "Theme is invalid";