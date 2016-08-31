<?php
@include('init.php');
include('universal_functions.php');

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
if(isset($file))
{
    //gets image extention:
    $type=strtolower(end(explode('.', $_FILES['image']['name'])));

    $allowed=array('jpeg' ,'jpg', 'png', 'gif');
    if(in_array($type, $allowed))
    {
        $query=mysql_query("SELECT image_types, picture_likes, picture_dislikes, picture_comments, comments_user_sent, comment_likes, comment_dislikes FROM pictures WHERE user_id=$_SESSION[id] LIMIT 1");
        if($query)
        {
            $array=mysql_fetch_row($query);
            $image_types=explode('|^|*|', $array[0]);
            $likes=explode('|^|*|', $array[1]);
            $dislikes=explode('|^|*|', $array[2]);
            $comments=explode('|^|*|', str_replace("'", "\'", $array[3]));
            $comments_users_sent=explode('|^|*|', $array[4]);
            $comment_likes=explode('|^|*|', $array[5]);
            $comment_dislikes=explode('|^|*|', $array[6]);

            //gets image dimensions
            list($width, $height)=getimagesize($file);

            if($width>=150&&$height>=150)
            {
    //            if($width>$height)
    //            {
    //                $new_width=300;
    //                $new_height=$height/($width/300);
    //
    //
    //                $new_thumb_height=250;
    //                $new_thumb_width=$width/($height/250);
    //            }
    //            else if($height>$width)
    //            {
    //                $new_height=300;
    //                $new_width=$width/($height/300);
    //
    //
    //                $new_thumb_width=150;
    //                $new_thumb_height=$height/($width/150);
    //            }
    //            else if($height==$width&&($height>300||$width>300))
    //            {
    //                $new_height=300;
    //                $new_width=300;
    //
    //                $new_thumb_width=150;
    //                $new_thumb_height=150;
    //            }
    //            else
    //            {
    //                $new_height=$height;
    //                $new_width=$width;
    //
    //                $new_thumb_width=150;
    //                $new_thumb_height=150;
    //            }
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

                //if image is a jpg
                if($type=='jpeg'||$type=='jpg')
                {
                    $path="users/$_SESSION[id]/photos/0.jpg";
                    $thumb_path="users/$_SESSION[id]/thumbs/0.jpg";

                    //uploads image
                    $img=imagecreatefromjpeg($_FILES['image']['tmp_name']);
                    $thumb=imagecreatetruecolor($new_width, $new_height);
                    imagecopyresampled($thumb, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
                    imagejpeg($thumb, $_FILES['image']['tmp_name'], 80);
                    imagejpeg($thumb, $_FILES['image']['tmp_name'].".jpg", 80);

                    $s3->putObjectFile($_FILES['image']['tmp_name'], "bucket_name", $path, S3::ACL_PUBLIC_READ);


                    //uploads thumb nail
                    $img=imagecreatefromjpeg($_FILES['image']['tmp_name'].".jpg");
                    $thumb=imagecreatetruecolor(250, 250);
                    imagecopyresampled($thumb, $img, 0, 0, 0, 0, $new_thumb_width, $new_thumb_height, $new_width, $new_height);
                    imagejpeg($thumb, $_FILES['image']['tmp_name']."thumbnail.jpg", 80);

                    $s3->putObjectFile($_FILES['image']['tmp_name']."thumbnail.jpg", "bucket_name", $thumb_path, S3::ACL_PUBLIC_READ);


                    $image_types[0]='jpg';
                }

                //if image is a png
                else if($type=='png')
                {
                    $path="users/$_SESSION[id]/photos/0.png";
                    $thumb_path="users/$_SESSION[id]/thumbs/0.png";

                    $img=imagecreatefrompng($_FILES['image']['tmp_name']);
                    $thumb=imagecreatetruecolor($new_width, $new_height);
//                    $black=imagecolorallocate($thumb, 0,0,0);
//                    imagecolortransparent($thumb, $black);
                    imagecopyresampled($thumb, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
                    imagepng($thumb, $_FILES['image']['tmp_name'], 9);
                    imagepng($thumb, $_FILES['image']['tmp_name'].".png", 9);
                    
                    $s3->putObjectFile($_FILES['image']['tmp_name'], "bucket_name", $path, S3::ACL_PUBLIC_READ);
                    
                    $img=imagecreatefrompng($_FILES['image']['tmp_name'].".png");
                    $thumb=imagecreatetruecolor(250, 250);
//                    $black=imagecolorallocate($thumb, 0,0,0);
//                    imagecolortransparent($thumb, $black);
                    imagecopyresampled($thumb, $img, 0, 0, 0, 0, $new_thumb_width, $new_thumb_height, $new_width, $new_height);
                    imagepng($thumb, $_FILES['image']['tmp_name']."thumbnail.png", 9);
                    
                    $s3->putObjectFile($_FILES['image']['tmp_name']."thumbnail.png", "bucket_name", $thumb_path, S3::ACL_PUBLIC_READ);
                    
                    $image_types[0]='png';
                }
                else if($type=='gif')
                {
                    $path="users/$_SESSION[id]/photos/0.gif";
                    $thumb_path="users/$_SESSION[id]/thumbs/0.gif";

                    
                    //determines if a gif is animated or not
                    $filecontents=file_get_contents($_FILES['image']['tmp_name']);
                    $str_loc=0;
                    $count=0;
                    while ($count < 2) # There is no point in continuing after we find a 2nd frame
                    {

                        $where1=strpos($filecontents,"\x00\x21\xF9\x04",$str_loc);
                        
                        //if there are no more slides
                        if ($where1 === FALSE)
                                break;
                        else
                        {
                            $str_loc=$where1+1;
                            $where2=strpos($filecontents,"\x00\x2C",$str_loc);
                            if ($where2 === FALSE)
                                    break;
                            else
                            {
                                if ($where1+8 == $where2)
                                    $count++;
                                
                                $str_loc=$where2+1;
                            }
                        }
                    }

                    //if it's animated
                    if ($count > 1)
                    {
                        //if gif is under 1MB
                        if($_FILES['image']['size']<=1048000)
                        {
                            copy($_FILES['image']['tmp_name'], $_FILES['image']['tmp_name'].".gif");
                        }
                        else
                        {
                            echo "Animated gif must be under 1MB";
                            exit();
                        }
                        
                    }
                    
                    //else if it's a regular picture
                    else
                    {
                        //uploads image
                        $img=imagecreatefromgif($_FILES['image']['tmp_name']);
                        $thumb=imagecreatetruecolor($new_width, $new_height);
                        imagecopyresampled($thumb, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
                        imagegif($thumb, $_FILES['image']['tmp_name']);
                        imagegif($thumb, $_FILES['image']['tmp_name'].".gif");
                        
                        //uploads thumb nail
                        $img=imagecreatefromgif($_FILES['image']['tmp_name'].".gif");
                        $thumb=imagecreatetruecolor(250, 250);
                        imagecopyresampled($thumb, $img, 0, 0, 0, 0, $new_thumb_width, $new_thumb_height, $new_width, $new_height);
                        imagegif($thumb, $_FILES['image']['tmp_name']."thumbnail.gif");
                    }
                    
                    $s3->putObjectFile($_FILES['image']['tmp_name'].".gif", "bucket_name", $path, S3::ACL_PUBLIC_READ);
                    $s3->putObjectFile($_FILES['image']['tmp_name'].".gif", "bucket_name", $thumb_path, S3::ACL_PUBLIC_READ);
                    
                    $image_types[0]='gif';
                }

                unlink($_FILES['image']['tmp_name']);
                unlink($_FILES['image']['tmp_name'].".jpg");
                unlink($_FILES['image']['tmp_name']."thumbnail.jpg");
                unlink($_FILES['image']['tmp_name'].".png");
                unlink($_FILES['image']['tmp_name']."thumbnail.png");
                unlink($_FILES['image']['tmp_name'].".gif");
                unlink($_FILES['image']['tmp_name']."thumbnail.gif");



                $likes[0]='';
                $dislikes[0]='';
                $comments[0]='';
                $comments_users_sent[0]='';
                $comment_likes[0]='';
                $comment_dislikes[0]='';

                $likes=implode('|^|*|', $likes);
                $dislikes=implode('|^|*|', $dislikes);
                $comments=implode('|^|*|', $comments);
                $comments_users_sent=implode('|^|*|', $comments_users_sent);
                $comment_likes=implode('|^|*|', $comment_likes);
                $comment_dislikes=implode('|^|*|', $comment_dislikes);
                $image_types=implode('|^|*|', $image_types);

                $query=mysql_query("UPDATE pictures SET picture_likes='$likes', picture_dislikes='$dislikes', picture_comments='$comments', comments_user_sent='$comments_users_sent', comment_likes='$comment_likes', comment_dislikes='$comment_dislikes', image_types='$image_types'  WHERE user_id=$_SESSION[id]");
                if($query)
                    header("Location: http://www.redlay.com/profile.php?user_id=$_SESSION[id]");
                else
                {
                    echo "Something went wrong";
                    log_error("change_profile_picture.php: ",mysql_error());
                }
            }
            else
            {
                echo "Picture is too small. Must be 150x150 or more";
            }
        }
        else
        {
            echo "Something went wrong. We are working on fixing it";
            log_error("change_profile_picture.php: (1): ", mysql_error());
        }
    }
    else
        echo "Error! You submitted a bad file type";
}
else
    echo "Please select an image";