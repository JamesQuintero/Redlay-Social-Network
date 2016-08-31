<?php
@include('init.php');
include('universal_functions.php');
$allowed="users";
include('security_checks.php');

if(!has_redlay_gold($_SESSION['id']))
{
    header("Location: http://www.redlay.com");
    exit();
}

//gets all the necessary AWS schtuff
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
            $path="users/$_SESSION[id]/themes/header_background.png";

            //uploads thumb nail
            $img=imagecreatefromjpeg($_FILES['image']['tmp_name']);
            $thumb=imagecreatetruecolor($width, $height);
            imagecopyresampled($thumb, $img, 0, 0, 0, 0, $width, $height, $width, $height);
            imagepng($thumb, $_FILES['image']['tmp_name'].".png", 9);

            $s3->putObjectFile($_FILES['image']['tmp_name'].".png", "bucket_name", $path, S3::ACL_PUBLIC_READ);
        }
        else if($type=="png")
        {
            $path="users/$_SESSION[id]/themes/header_background.png";
            
            //uploads thumb nail
            $img=imagecreatefrompng($_FILES['image']['tmp_name']);
            $thumb=imagecreatetruecolor($width, $height);
//            $black=imagecolorallocate($thumb, 0,0,0);
//            imagecolortransparent($thumb, $black);
            imagecopyresampled($thumb, $img, 0, 0, 0, 0, $width, $height, $width, $height);
            imagepng($thumb, $_FILES['image']['tmp_name'].".png", 9);

            $s3->putObjectFile($_FILES['image']['tmp_name'].".png", "bucket_name", $path, S3::ACL_PUBLIC_READ);
        }
        else if($type=='gif')
        {
            $path="users/$_SESSION[id]/themes/header_background.png";
            
            //uploads thumb nail
            $img=imagecreatefromgif($_FILES['image']['tmp_name']);
            $thumb=imagecreatetruecolor($width, $height);
            imagecopyresampled($thumb, $img, 0, 0, 0, 0, $width, $height, $width, $height);
            imagepng($thumb, $_FILES['image']['tmp_name'].".png");

            $s3->putObjectFile($_FILES['image']['tmp_name'].".png", "bucket_name", $path, S3::ACL_PUBLIC_READ);
        }
        
        unlink($_FILES['image']['tmp_name']);
        unlink($_FILES['image']['tmp_name'].".png");
        
        $message="Change successful";
    }
    else
        $message="not valid picture format";
}
else
    $message="No picture";
?>

<script type="text/javascript">
//    window.parent.document.getElementById('upload_photo_gif').style.display="none";
//    if(<?php echo $success ?>==true)
//    {
//        window.parent.document.getElementById('upload_photo_preview').innerHTML="<div id='picture_preview_body'><div class='draggable_thumbnail_selector'></div><img id='upload_photo_preview_image' src='https://s3.amazonaws.com/bucket_name/users/<?php echo $_SESSION['id'] ?>/photos/<?php echo $name ?>.<?php if($type=='jpeg') echo "jpg";echo $type; ?>'/></div><div id='thumbnail_info_body'><div id='thumbnail_preview_body'><div id='thumbnail_preview_window'><img id='thumbnail_image_preview' src='https://s3.amazonaws.com/bucket_name/users/<?php echo $_SESSION['id'] ?>/photos/<?php echo $name ?>.<?php echo $type; ?>' /></div></div><div id='thumbnail_info'></div></div>";
//        var description=window.parent.document.getElementById('upload_picture_description');
//        description.value="";
//        description.style.display="none";
//        window.parent.document.getElementById('upload_photo_row_2').innerHTML="<td class='upload_photo_unit' colspan='3'><input type='file' id='photo_upload_button' class='file_input' name='image' ></td><?php echo "<td style='width: 120px;'><table><tbody><tr><td><span>Max quality</span></td><td><input type='checkbox' id='quality_checkbox' name='photo_quality' value='yes'/></td></tr></tbody></table></td>"; ?>";
//
//        var photo_upload_message=window.parent.document.getElementById('photo_upload_message');
//        photo_upload_message.style.display="block";
//        window.parent.document.getElementById('upload_photo_preview').style.display="block";
//        photo_upload_message.innerHTML="Uploaded: ";
//
//        parent.disable_photo_upload();
//        parent.display_error("Photo uploaded", 'good_errors');
//        parent.initialize_thumbnail_selection('<?php echo $name; ?>', <?php echo $width ?>, <?php echo $height; ?>);
//    }
//    else
//    {
//        errors.innerHTML="<?php echo $message ?>";
//        parent.display_error("Something went wrong", 'bad_errors');
//    }
    
    parent.display_error('<?php echo $message; ?>', '<?php if($message="Change successful") echo "good_errors"; else echo "bad_errors"; ?>');
    window.parent.document.getElementById('header_background_load_gif').style.display="none";
    parent.update_header_background();
</script>