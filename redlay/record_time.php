<?php
@include('init.php');
include('universal_functions.php');
$allowed="all";
include('security_checks.php');


$page=clean_string($_POST['page']);
$time=(double)($_POST['time']);

sleep(3);

//checks if it's a valid site page
//checks if time is less than 15 seconds
if(is_valid_page($page)&&$time<=15000)
{
    $file_name="../$page.txt";

    $contents=file_get_contents($file_name);

    if($contents!="")
        $contents=explode("\n", $contents);
    else
        $contents=array();
    
    
    $contents[]=$time." | ".get_date();
    

    //add to S3 master file if temp file has over 500 entries
    if(sizeof($contents)>=500)
    {
        //gets all the necessary AWS schtuff
        if (!class_exists('S3'))
            require_once('S3.php');
        if (!defined('awsAccessKey'))
            define('awsAccessKey', ACCESS_KEY);
        if (!defined('awsSecretKey'))
            define('awsSecretKey', SECRET_KEY);

        //creates S3 item with schtuff
        $s3 = new S3(awsAccessKey, awsSecretKey);
        
        
        $path="stats/$page.txt";
        $value=md5(uniqid(rand()));
        $tmp_path="/var/www/tmp_files/$value.txt";
        
        $s3->getObject('files_bucket_name', $path, $tmp_path);
        
        $master_contents=file_get_contents($tmp_path);
        
        $master_contents=explode("\n", $master_contents);
        for($x = 0; $x < sizeof($contents); $x++)
            $master_contents[]=$contents[$x];
        
        $master_contents=implode("\n", $master_contents);
        
        file_put_contents($tmp_path, $master_contents);
        $s3->putObjectFile($tmp_path, "files_bucket_name", $path, S3::ACL_PUBLIC_READ);
        file_put_contents($file_name, "");
        unlink($tmp_path);
        
    }
    else
    {
        //implodes content
        $contents=implode("\n", $contents);
        
        //puts contents in a temp file
        file_put_contents($file_name, $contents);
    }
}