<?php
@include('init.php');
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

$photo_id=clean_string($_POST['photo_id']);
$user_id=(int)($_POST['user_id']);


if(is_id($user_id))
{
    $query=mysql_query("SELECT pictures, image_types FROM pictures WHERE user_id=$user_id LIMIT 1");
    if($query&&mysql_num_rows($query)==1)
    {
        $array=mysql_fetch_row($query);
        $pictures=explode('|^|*|', $array[0]);
        $original_image_types=explode('|^|*|', $array[1]);
        
        $index=array_search($photo_id, $pictures);
        if($index!=false)
        {
            $query=mysql_query("SELECT image_types FROM pictures WHERE user_id=$_SESSION[id] LIMIT 1");
            if($query&&mysql_num_rows($query)==1)
            {
                $array=mysql_fetch_row($query);
                $image_types=explode('|^|*|', $array[0]);

                $image_types[0]=$original_image_types[$index];
                $type=$original_image_types[$index];

                $image_types=implode('|^|*|', $image_types);
                $query=mysql_query("UPDATE pictures SET image_types='$image_types' WHERE user_id=$_SESSION[id]");


                //gets image dimensions
                $url="https://s3.amazonaws.com/bucket_name/users/$user_id/photos/".$photo_id.".".$type;
                
                if(file_exists_server($url))
                {
                    list($width, $height)=getimagesize($url);

                    if($width>=150&&$height>=150)
                    {
                        if($width>$height)
                        {
                            $new_width=800;
                            $new_height=$height/($width/800);


                            $new_thumb_height=250;
                            $new_thumb_width=$width/($height/250);
                        }
                        else if($height>$width)
                        {
                            $new_height=800;
                            $new_width=$width/($height/800);


                            $new_thumb_width=250;
                            $new_thumb_height=$height/($width/250);
                        }
                        else if($height==$width&&($height>800||$width>800))
                        {
                            $new_height=800;
                            $new_width=800;

                            $new_thumb_width=250;
                            $new_thumb_height=250;
                        }
                        else
                        {
                            $new_height=$height;
                            $new_width=$width;

                            $new_thumb_width=250;
                            $new_thumb_height=250;
                        }

                        //deletes already existing profile pictures
                        //previous might be jpg and new might be png
                        if(file_exists_server("https://s3.amazonaws.com/bucket_name/users/$_SESSION[id]/photos/0.jpg"))
                        {
                            $s3->deleteObject("bucket_name", "users/$_SESSION[id]/photos/0.jpg");
                            $s3->deleteObject("bucket_name", "users/$_SESSION[id]/thumbs/0.jpg");
                        }
                        else if(file_exists_server("https://s3.amazonaws.com/bucket_name/users/$_SESSION[id]/photos/0.png"))
                        {
                            $s3->deleteObject("bucket_name", "users/$_SESSION[id]/photos/0.png");
                            $s3->deleteObject("bucket_name", "users/$_SESSION[id]/thumbs/0.png");
                        }
                        else if(file_exists_server("https://s3.amazonaws.com/bucket_name/users/$_SESSION[id]/photos/0.gif"))
                        {
                            $s3->deleteObject("bucket_name", "users/$_SESSION[id]/photos/0.gif");
                            $s3->deleteObject("bucket_name", "users/$_SESSION[id]/thumbs/0.gif");
                        }

                        $name=0;

                            //if image is a jpg
                            if($type=='jpeg'||$type=='jpg')
                            {
                                $value=md5(uniqid(rand()));
                                $tmp_path="/tmp/$value.jpg";

                                if(copy($url, $tmp_path))
                                {

                                    $path="users/$_SESSION[id]/photos/$name.jpg";
                                    $thumb_path="users/$_SESSION[id]/thumbs/$name.jpg";


                                    //uploads image
                                    $img=imagecreatefromjpeg($tmp_path);
                                    $thumb=imagecreatetruecolor($new_width, $new_height);
                                    imagecopyresampled($thumb, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
                                    imagejpeg($thumb, $tmp_path, 80);
                                    imagejpeg($thumb, $tmp_path."jpg.jpg", 80);

                                    $s3->putBucket('bucket_name');
                                    $s3->putObjectFile($tmp_path, "bucket_name", $path, S3::ACL_PUBLIC_READ);

                                    $public_width=$new_width;
                                    $public_height=$new_height;

                                    //uploads thumb nail
                                    $img=imagecreatefromjpeg($tmp_path."jpg.jpg");
                                    $thumb=imagecreatetruecolor(250, 250);
                                    imagecopyresampled($thumb, $img, 0, 0, 0, 0, $new_thumb_width, $new_thumb_height, $new_width, $new_height);
                                    imagejpeg($thumb, $tmp_path."thumbnail.jpg", 80);

                                    $s3->putObjectFile($tmp_path."thumbnail.jpg", "bucket_name", $thumb_path, S3::ACL_PUBLIC_READ);
                                }
                            }

                            //if image is a png
                            else if($type=='png')
                            {
                                $value=md5(uniqid(rand()));
                                $tmp_path="/tmp/$value.png";

                                if(copy($url, $tmp_path))
                                {

                                    $path="users/$_SESSION[id]/photos/$name.png";
                                    $thumb_path="users/$_SESSION[id]/thumbs/$name.png";


                                    //uploads image
                                    $img=imagecreatefrompng($tmp_path);
                                    $thumb=imagecreatetruecolor($new_width, $new_height);
    //                                            $black=imagecolorallocate($thumb, 0,0,0);
    //                                            imagecolortransparent($thumb, $black);
                                    imagecopyresampled($thumb, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
                                    imagepng($thumb, $tmp_path, 9);
                                    imagepng($thumb, $tmp_path."png.png", 9);

                                    $s3->putBucket('bucket_name');
                                    $s3->putObjectFile($tmp_path, "bucket_name", $path, S3::ACL_PUBLIC_READ);

                                    $public_width=$new_width;
                                    $public_height=$new_height;


                                    //uploads thumb nail
                                    $img=imagecreatefrompng($tmp_path."png.png");
                                    $thumb=imagecreatetruecolor(250, 250);
    //                                        $black=imagecolorallocate($thumb, 0,0,0);
    //                                        imagecolortransparent($thumb, $black);
                                    imagecopyresampled($thumb, $img, 0, 0, 0, 0, $new_thumb_width, $new_thumb_height, $new_width, $new_height);
                                    imagepng($thumb, $tmp_path."thumbnail.png", 9);

                                    $s3->putObjectFile($tmp_path."thumbnail.png", "bucket_name", $thumb_path, S3::ACL_PUBLIC_READ);
                                }
                            }
                            else if($type=='gif')
                            {
                                $value=md5(uniqid(rand()));
                                $tmp_path="/tmp/$value.gif";

                                if(copy($url, $tmp_path))
                                {

                                    $path="users/$_SESSION[id]/photos/$name.gif";
                                    $thumb_path="users/$_SESSION[id]/thumbs/$name.gif";


                                    //uploads image
                                    $img=imagecreatefromgif($tmp_path);
                                    $thumb=imagecreatetruecolor($new_width, $new_height);
                                    imagecopyresampled($thumb, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
                                    imagegif($thumb, $tmp_path);
                                    imagegif($thumb, $tmp_path."gif.gif");

                                    $s3->putBucket('bucket_name');
                                    $s3->putObjectFile($tmp_path, "bucket_name", $path, S3::ACL_PUBLIC_READ);

                                    $public_width=$new_width;
                                    $public_height=$new_height;



                                    //uploads thumb nail
                                    $img=imagecreatefromgif($tmp_path."gif.gif");
                                    $thumb=imagecreatetruecolor(250, 250);
                                    imagecopyresampled($thumb, $img, 0, 0, 0, 0, $new_thumb_width, $new_thumb_height, $new_width, $new_height);
                                    imagegif($thumb, $tmp_path."thumbnail.gif");

                                    $s3->putObjectFile($tmp_path."thumbnail.gif", "bucket_name", $thumb_path, S3::ACL_PUBLIC_READ);

                                    $image_types[]='gif';
                                }
                            }

                            unlink($tmp_path);
                            unlink($tmp_path.".jpg");
                            unlink($tmp_path."jpg.jpg");
                            unlink($tmp_path."thumbnail.jpg");
                            unlink($tmp_path.".png");
                            unlink($tmp_path."png.png");
                            unlink($tmp_path."thumbnail.png");
                            unlink($tmp_path.".gif");
                            unlink($tmp_path."gif.gif");
                            unlink($tmp_path."thumbnail.gif");


                            echo "Profile picture changed";
                    }
                    else
                        echo "Photo's width or height is too small. Needs to be more than 150px.";
                }
                else
                    echo "Photo doesn't exist";
            }
            else
            {
                echo "Something went wrong. We are working on fixing it";
                log_error("change_profile_picture_existing.php: (2): ", mysql_error());
            }
        }
        else
            echo "Photo doesn't exist in database";
    }
    else
    {
        echo "Something went wrong. We are working on fixing it";
        log_error('change_profile_picture_existing.php: (1): ', mysql_error());
    }
}
else
    echo "Incorrect user id";