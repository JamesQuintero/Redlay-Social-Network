<?php
//changes redlay.com to www.redlay.com
if(strstr($_SERVER['SERVER_NAME'], "redlay.com")==true)
{
    if(strstr($_SERVER['SERVER_NAME'], "www")==false&&strstr($_SERVER["SERVER_NAME"],'m.redlay')==false)
    {
         header("Location: http://www.redlay.com".$_SERVER['REQUEST_URI']);
         exit();
    }
}
else
    exit();


//if(!ob_start("ob_gzhandler"))
ob_start();
ini_set('session.cookie_httponly', true);
session_start();
session_cache_limiter();


$host="AMAZON_RDS"; //localhost
$username="DATABASE_USERNAME"; //database username
$password="RDS_PASSWORD"; //password for user to database
$db_name="DATABASE_NAME"; //name of database

//opens connection to mysql server

$dbc= mysql_connect("$host","$username", "$password" );
if(!$dbc)
{
    $from='no-reply@redlay.com';
    
    $array=array();
    $array['key']=ACCESS_KEY;
    $array['secret']=SECRET_KEY;
    $amazonSes = new AmazonSES($array);
    $amazonSes->verify_email_address($from);

    $amazonSes->send_email($from,
        array('ToAddresses' => array('EMAIL_ADDRESS')),
        array(
            'Subject.Data' => "Redlay down!",
            'Body.Text.Data' => mysql_error(),
        )
    );
    
    die("We are sorry, but it looks like redlay.com is down right now. ");
}

//select database
$db_selected = mysql_select_db("$db_name", $dbc);
if(!$db_selected)
{
    $from='no-reply@redlay.com';
    
    $array=array();
    $array['key']=ACCESS_KEY;
    $array['secret']=SECRET_KEY;
    $amazonSes = new AmazonSES($array);
    $amazonSes->verify_email_address($from);

    $amazonSes->send_email($from,
        array('ToAddresses' => array('EMAIL_ADDRESS')),
        array(
            'Subject.Data' => "Redlay down!",
            'Body.Text.Data' => mysql_error(),
        )
    );
    
    die("We are sorry, but it looks like redlay.com is down right now. ");
}
    
//gets redlay theme
if(isset($_SESSION['id']))
{
    $query=mysql_query("SELECT theme FROM themes WHERE user_id=$_SESSION[id] LIMIT 1");
    if($query&&mysql_num_rows($query)==1)
    {
        $array=mysql_fetch_row($query);
        $theme=$array[0];

        if($theme=="custom")
        {
            $query=mysql_query("SELECT redlay_gold FROM user_data WHERE user_id=$_SESSION[id] LIMIT 1");
            if($query&&mysql_num_rows($query)==1)
            {
                $array=mysql_fetch_row($query);
                
                if($array[0]=='')
                    $theme="white";
            }
        }
        $redlay_theme=$theme;
    }
    else
        $redlay_theme="white";
}
else 
    $redlay_theme="white";