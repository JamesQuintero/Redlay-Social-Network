<?php
@include('init.php');
include('universal_functions.php');
$allowed="users";
include('security_checks.php');

//gets all the necessary AWS stuff
if (!class_exists('S3'))
    require_once('S3.php');
if (!defined('awsAccessKey'))
    define('awsAccessKey', ACCESS_KEY);
if (!defined('awsSecretKey'))
    define('awsSecretKey', SECRET_KEY);

//creates S3 item with schtuff
$s3 = new S3(awsAccessKey, awsSecretKey);

$file=$_FILES['image']['tmp_name'];
//if the user specified a file
if($_FILES['image']['size']>0)
{
    //gets image extention:
    $type=strtolower(end(explode('.', $_FILES['image']['name'])));

    $allowed=array('jpeg' ,'jpg', 'png', 'gif');
    if(in_array($type, $allowed))
    {
        //gets image dimensions
        list($width, $height)=getimagesize($file);
        
        if($type=="jpeg"||$type=='jpg')
        {
            $path="users/$_SESSION[id]/photos/background.jpg";

            //uploads thumb nail
            $img=imagecreatefromjpeg($_FILES['image']['tmp_name']);
            $thumb=imagecreatetruecolor($width, $height);
            imagecopyresampled($thumb, $img, 0, 0, 0, 0, $width, $height, $width, $height);
            imagejpeg($thumb, $_FILES['image']['tmp_name'].".jpg", 80);

            $s3->putObjectFile($_FILES['image']['tmp_name'].".jpg", "redlay.users", $path, S3::ACL_PUBLIC_READ);
        }
        else if($type=="png")
        {
            $path="users/$_SESSION[id]/photos/background.png";
            
            //uploads thumb nail
            $img=imagecreatefrompng($_FILES['image']['tmp_name']);
            $thumb=imagecreatetruecolor($width, $height);
//            $black=imagecolorallocate($thumb, 0,0,0);
//            imagecolortransparent($thumb, $black);
            imagecopyresampled($thumb, $img, 0, 0, 0, 0, $width, $height, $width, $height);
            imagepng($thumb, $_FILES['image']['tmp_name'].".png", 9);

            $s3->putObjectFile($_FILES['image']['tmp_name'].".png", "redlay.users", $path, S3::ACL_PUBLIC_READ);
        }
        else if($type=='gif')
        {
            //uploads thumb nail
            $img=imagecreatefromgif($_FILES['image']['tmp_name']);
            $thumb=imagecreatetruecolor($width, $height);
            imagecopyresampled($thumb, $img, 0, 0, 0, 0, $width, $height, $width, $height);
            imagegif($thumb, $_FILES['image']['tmp_name'].".gif");

            $s3->putObjectFile($_FILES['image']['tmp_name'].".gif", "redlay.users", $path, S3::ACL_PUBLIC_READ);
        }
        
        unlink($_FILES['image']['tmp_name']);
        unlink($_FILES['image']['tmp_name'].".jpg");
        unlink($_FILES['image']['tmp_name'].".png");
        unlink($_FILES['image']['tmp_name'].".gif");
        
        header("Location: http://www.redlay.com/profile.php?user_id=".$_SESSION[id]);
    }
}
