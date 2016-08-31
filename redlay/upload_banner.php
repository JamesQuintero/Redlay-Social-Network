<?php
@include('init.php');
if(strstr($_SERVER['SERVER_NAME'], "www")==false)
    include('cross_domain_headers.php');

include('universal_functions.php');
$allowed="users";
include('security_checks.php');

//gets all the necessary AWS schtuff
if (!class_exists('S3'))
    require_once('S3.php');
if (!defined('awsAccessKey'))
    define('awsAccessKey', ACCESS_KEY);
if (!defined('awsSecretKey'))
    define('awsSecretKey', SECRET_KEY);

//creates S3 item with schtuff
$s3 = new S3(awsAccessKey, awsSecretKey);


//checks if there actually is a photo selected
if($_FILES['image']['size']!=0)
{
    //if the file is less than or equal to 10MB
    if($_FILES['image']['size']<=10240000)
    {
        //gets image extention:
        $type=strtolower(end(explode('.', $_FILES['image']['name'])));

        $allowed=array('jpeg' ,'jpg', 'png', 'gif');
        if(in_array($type, $allowed))
        {
            //gets image dimensions
            list($width, $height)=getimagesize($_FILES['image']['tmp_name']);

            if($width>=400&&$height>=150)
            {
                $new_width=925;
                $new_height=$height/($width/925);
                
                $banner_dimensions=array();
                $banner_dimensions[0]=$new_height;
                $banner_dimensions[1]=$new_width;
                $banner_dimensions[2]=0;
                $banner_dimensions=implode('|^|*|', $banner_dimensions);
                $query=mysql_query("UPDATE user_display SET banner_data='$banner_dimensions' WHERE user_id=$_SESSION[id]");

                    //if image is a jpg
                    if($type=='jpeg'||$type=='jpg')
                    {
                        $path="users/$_SESSION[id]/photos/banner.jpg";

                        //uploads image
                        $img=imagecreatefromjpeg($_FILES['image']['tmp_name']);
                        $thumb=imagecreatetruecolor($new_width, $new_height);
                        imagecopyresampled($thumb, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
                        imagejpeg($thumb, $_FILES['image']['tmp_name'], 80);
                        
                        $s3->putBucket('bucket_name');
                        $s3->putObjectFile($_FILES['image']['tmp_name'], "bucket_name", $path, S3::ACL_PUBLIC_READ);
                        $type="jpg";
                    }

                    //if image is a png
                    else if($type=='png')
                    {
                        $path="users/$_SESSION[id]/photos/banner.png";
                        
                        //uploads image
                        $img=imagecreatefrompng($_FILES['image']['tmp_name']);
                        $thumb=imagecreatetruecolor($new_width, $new_height);
                        imagecopyresampled($thumb, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
                        imagepng($thumb, $_FILES['image']['tmp_name'], 9);

                        $s3->putBucket('bucket_name');
                        $s3->putObjectFile($_FILES['image']['tmp_name'], "bucket_name", $path, S3::ACL_PUBLIC_READ);

                    }
                    else if($type=='gif')
                    {
                        $path="users/$_SESSION[id]/photos/banner.gif";

                        //uploads image
                        $img=imagecreatefromgif($_FILES['image']['tmp_name']);
                        $thumb=imagecreatetruecolor($new_width, $new_height);
                        imagecopyresampled($thumb, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
                        imagegif($thumb, $_FILES['image']['tmp_name']);

                        $s3->putBucket('bucket_name');
                        $s3->putObjectFile($_FILES['image']['tmp_name'], "bucket_name", $path, S3::ACL_PUBLIC_READ);

                    }

                    unlink($_FILES['image']['tmp_name']);

                    $success='true';
                    if(strstr($_SERVER['SERVER_NAME'], "www")==false)
                        header("Location: http://m.redlay.com/home.php");
            }
            else
            {
                $message="Photo's width needs to be at least 400px and height at least 150px";
                $success='false';
            }
        }
        else
        {
            $message="That is not an image!";
            $success='false';
        }
    }
    else
    {
        $message="Photo file is too big. 10MB is the max.";
        $success='false';
    }
}
else
{
    $message="No Photo Selected!";
    $success='false';
}
?>

<script type="text/javascript">
    console.log("<?php echo $message; ?>");
//    window.parent.document.getElementById('upload_photo_gif').style.display="none";
    if(<?php echo $success ?>==true)
    {
        window.parent.document.getElementById('banner_container_because_F_javascript').innerHTML="<img id='banner' src='<?php echo "https://s3.amazonaws.com/bucket_name/users/$_SESSION[id]/photos/banner.".$type; ?>' style='top:0px' />";
        parent.initialize_banner();
    }
    else
    {
        console.log("<?php echo $message; ?>");
        parent.display_error("<?php echo $message; ?>", 'bad_errors');
    }
</script>