<?php
//exit();
@include('init.php');
include('universal_functions.php');
$allowed="users";
include('security_checks.php');

include("requiredS3.php");

$url=clean_string($_POST['url']);
$description=clean_string($_POST['description']);
$max_quality=clean_string($_POST['max_quality']);
$groups=$_POST['groups'];


if(!empty ($groups)&&!in_array('Everyone', $groups))
{
    //checks if all groups are valid
    $bool=true;
    for($x = 0; $x < sizeof($groups); $x++)
    {
        if(!is_valid_audience($groups[$x]))
           $bool=false;
    }
}
else
{
    $groups=array();
    $groups[0]='Everyone';
    $bool=true;
}


$privacy=get_user_privacy_settings($_SESSION['id']);
if($bool)
{
    //fixes URL
    $temp=explode('/', $url);
    $end=end($temp);
    $end=explode('?', $end);
    $end=$end[0];
    $temp[sizeof($temp)-1]=$end;
 
    $url=implode('/', $temp);
    
    //checks if there actually is a photo selected
    if(file_exists_server($url))
    {
        //if user has redlay gold, the max photo size of 10MB
//        if((has_redlay_gold($_SESSION['id'], 'photo_quality')&&($_FILES['image']['size']<=10240000||!isset($_POST['photo_quality'])))||!has_redlay_gold($_SESSION['id'], 'photo_quality'))
//        {
        //if the file is less than or equal to 10MB
        if($_FILES['image']['size']<=10240000)
        {
            //checks if description is right length
            if(strlen($description)<=1000)
            {
                $query=mysql_query("SELECT pictures, picture_descriptions, picture_comments, timestamp, picture_likes, picture_dislikes, image_audiences, comment_likes, comment_dislikes, comment_timestamps, comments_user_sent, comment_ids, image_types FROM pictures WHERE user_id=$_SESSION[id] LIMIT 1");
                if($query&&mysql_num_rows($query)==1)
                {
                    $array=mysql_fetch_array($query);
                    $pictures=explode('|^|*|', $array[0]);
                    $descriptions=explode('|^|*|', mysql_real_escape_string($array[1]));
                    $comments=explode('|^|*|', mysql_real_escape_string($array[2]));
                    $timestamps=explode('|^|*|', $array[3]);
                    $likes=explode('|^|*|', $array[4]);
                    $dislikes=explode('|^|*|', $array[5]);
                    $image_audiences=explode('|^|*|', $array[6]);

                    $comment_likes=explode('|^|*|', $array[7]);
                    $comment_dislikes=explode('|^|*|', $array[8]);
                    $comment_timestamps=explode('|^|*|', $array[9]);
                    $comments_users_sent=explode('|^|*|', $array[10]);
                    $comment_ids=explode('|^|*|', $array[11]);
                    $image_types=explode('|^|*|', $array[12]);

                    $date=get_date();
                    $name=sha1(uniqid(rand()));

                    //adds timestamp
                    if($array[3]=='')
                    {
                        $timestamps[0]=$date;
                        $descriptions[0]=$description;
                        $comments[0]='';
                        $comment_likes[0]='';
                        $comment_dislikes[0]='';
                        $comment_timestamps[0]='';
                        $comments_users_sent[0]='';
                        $likes[0]='';
                        $dislikes[0]='';
                        $pictures[0]=$name;
                        $image_audiences[0]=implode('|%|&|', $groups);
                        $comment_ids[0]='';
                    }
                    else
                    {
                        $timestamps[]=$date;
                        $descriptions[]=$description;
                        $comments[]='';
                        $comment_likes[]='';
                        $comment_dislikes[]='';
                        $comment_timestamps[]='';
                        $comments_users_sent[]='';
                        $likes[]='';
                        $dislikes[]='';
                        $pictures[]=$name;
                        $image_audiences[]=implode('|%|&|', $groups);
                        $comment_ids[]='';
                    }
                    $timestamps=implode('|^|*|', $timestamps);
                    $comments=implode('|^|*|', $comments);
                    $comment_likes=implode('|^|*|', $comment_likes);
                    $comment_dislikes=implode('|^|*|', $comment_dislikes);
                    $comment_timestamps=implode('|^|*|', $comment_timestamps);
                    $comments_users_sent=implode('|^|*|', $comments_users_sent);
                    $descriptions=implode('|^|*|', $descriptions);
                    $likes=implode('|^|*|', $likes);
                    $dislikes=implode('|^|*|', $dislikes);
                    $pictures=implode('|^|*|', $pictures);
                    $image_audiences=implode('|^|*|', $image_audiences);
                    $comment_ids=implode('|^|*|', $comment_ids);

                    //gets image extention:
                    $type=strtolower(end(explode('.', $url)));

                    $allowed=array('jpeg' ,'jpg', 'png', 'gif');
                    if(in_array($type, $allowed))
                    {
                        //gets image dimensions
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

//                            if($width>10&&$height>10)
//                            {
                                $success=false;
                                //if image is a jpg
                                if($type=='jpeg'||$type=='jpg')
                                {
                                    $value=md5(uniqid(rand()));
                                    $tmp_path="/tmp/$value.jpg";
                                    
                                    if(copy($url, $tmp_path))
                                    {
                                        $success=true;
                                        
                                        $path="users/$_SESSION[id]/photos/$name.jpg";
                                        $thumb_path="users/$_SESSION[id]/thumbs/$name.jpg";

//                                        if($max_quality=='true'&&has_redlay_gold($_SESSION['id'], 'photo_quality'))
//                                        {
                                        if($max_quality=='true')
                                        {
                                            $img=imagecreatefromjpeg($tmp_path);
                                            $thumb=imagecreatetruecolor($width, $height);
                                            imagecopyresampled($thumb, $img, 0, 0, 0, 0, $width, $height, $width, $height);
                                            imagejpeg($thumb, $tmp_path, 80);
                                            imagejpeg($thumb, $tmp_path."jpg.jpg", 80);

                                            $s3->putBucket('bucket_name');
                                            $s3->putObjectFile($tmp_path, "bucket_name", $path, S3::ACL_PUBLIC_READ);

                                            $public_width=$width;
                                            $public_height=$height;
                                        }
                                        else
                                        {
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
                                        }

                                        //uploads thumb nail
                                        $img=imagecreatefromjpeg($tmp_path."jpg.jpg");
                                        $thumb=imagecreatetruecolor(250, 250);
                                        imagecopyresampled($thumb, $img, 0, 0, 0, 0, $new_thumb_width, $new_thumb_height, $new_width, $new_height);
                                        imagejpeg($thumb, $tmp_path."thumbnail.jpg", 80);

                                        $s3->putObjectFile($tmp_path."thumbnail.jpg", "bucket_name", $thumb_path, S3::ACL_PUBLIC_READ);


                                        $image_types[]='jpg';
                                    }
                                }

                                //if image is a png
                                else if($type=='png')
                                {
                                    $value=md5(uniqid(rand()));
                                    $tmp_path="/tmp/$value.png";
                                    
                                    if(copy($url, $tmp_path))
                                    {
                                        $success=true;
                                        
                                        $path="users/$_SESSION[id]/photos/$name.png";
                                        $thumb_path="users/$_SESSION[id]/thumbs/$name.png";

//                                        if($max_quality=='true'&&has_redlay_gold($_SESSION['id'], 'photo_quality'))
//                                        {
                                        if($max_quality=='true')
                                        {
                                            $img=imagecreatefrompng($tmp_path);
                                            $thumb=imagecreatetruecolor($width, $height);
//                                            $black=imagecolorallocate($thumb, 0,0,0);
//                                            imagecolortransparent($thumb, $black);
                                            imagecopyresampled($thumb, $img, 0, 0, 0, 0, $width, $height, $width, $height);
                                            imagepng($thumb, $tmp_path, 9);
                                            imagepng($thumb, $tmp_path."png.png", 9);

                                            $s3->putBucket('bucket_name');
                                            $s3->putObjectFile($tmp_path, "bucket_name", $path, S3::ACL_PUBLIC_READ);

                                            $public_width=$width;
                                            $public_height=$height;
                                        }
                                        else
                                        {
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
                                        }

                                        //uploads thumb nail
                                        $img=imagecreatefrompng($tmp_path."png.png");
                                        $thumb=imagecreatetruecolor(250, 250);
//                                        $black=imagecolorallocate($thumb, 0,0,0);
//                                        imagecolortransparent($thumb, $black);
                                        imagecopyresampled($thumb, $img, 0, 0, 0, 0, $new_thumb_width, $new_thumb_height, $new_width, $new_height);
                                        imagepng($thumb, $tmp_path."thumbnail.png", 9);

                                        $s3->putObjectFile($tmp_path."thumbnail.png", "bucket_name", $thumb_path, S3::ACL_PUBLIC_READ);

                                        $image_types[]='png';
                                    }
                                }
                                else if($type=='gif')
                                {
                                    $value=md5(uniqid(rand()));
                                    $tmp_path="/tmp/$value.gif";
                                    
                                    if(copy($url, $tmp_path))
                                    {
                                        $success=true;
                                        
                                        $path="users/$_SESSION[id]/photos/$name.gif";
                                        $thumb_path="users/$_SESSION[id]/thumbs/$name.gif";

//                                        if($max_quality=='true'&&has_redlay_gold($_SESSION['id'], 'photo_quality'))
//                                        {
                                        if($max_quality=='true')
                                        {
                                            $img=imagecreatefromgif($tmp_path);
                                            $thumb=imagecreatetruecolor($width, $height);
                                            imagecopyresampled($thumb, $img, 0, 0, 0, 0, $width, $height, $width, $height);
                                            imagegif($thumb, $tmp_path);
                                            imagegif($thumb, $tmp_path."gif.gif");

                                            $s3->putBucket('bucket_name');
                                            $s3->putObjectFile($tmp_path, "bucket_name", $path, S3::ACL_PUBLIC_READ);

                                            $public_width=$width;
                                            $public_height=$height;
                                        }
                                        else
                                        {
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
                                        }

                                        //uploads thumb nail
                                        $img=imagecreatefromgif($tmp_path."gif.gif");
                                        $thumb=imagecreatetruecolor(250, 250);
                                        imagecopyresampled($thumb, $img, 0, 0, 0, 0, $new_thumb_width, $new_thumb_height, $new_width, $new_height);
                                        imagegif($thumb, $tmp_path."thumbnail.gif");

                                        $s3->putObjectFile($tmp_path."thumbnail.gif", "bucket_name", $thumb_path, S3::ACL_PUBLIC_READ);

                                        $image_types[]='gif';
                                    }
                                }
                                
                                $image_types=implode('|^|*|', $image_types);


                                if($success)
                                {
                                    $query2=mysql_query("UPDATE pictures SET image_types='$image_types', pictures='$pictures', timestamp='$timestamps', picture_descriptions='$descriptions', picture_comments='$comments', picture_likes='$likes', picture_dislikes='$dislikes', image_audiences='$image_audiences', comments_user_sent='$comments_users_sent', comment_likes='$comment_likes', comment_dislikes='$comment_dislikes', comment_timestamps='$comment_timestamps', comment_ids='$comment_ids' WHERE user_id=$_SESSION[id]");
                                    if($query2)
                                    {    
                                        if($privacy[1][3]=='yes'&&$groups[0]=='Everyone')
                                        {
                                            /////////////posts to public//////////////////
                                            $query=mysql_query("SELECT picture_ids, pictures_users_sent, picture_descriptions, picture_timestamps, original_picture_ids, picture_types FROM public WHERE num=1");
                                            if($query&&mysql_num_rows($query)==1)
                                            {
                                                $array=mysql_fetch_row($query);
                                                $picture_ids=explode('|^|*|', $array[0]);
                                                $pictures_users_sent=explode('|^|*|', $array[1]);
                                                $picture_descriptions=explode('|^|*|', mysql_real_escape_string($array[2]));
                                                $picture_timestamps=explode('|^|*|', $array[3]);
                                                $original_picture_ids=explode('|^|*|', $array[4]);
                                                $picture_types=explode('|^|*|', $array[5]);

                                                if(sizeof($picture_ids)>=500)
                                                {
                                                    $temp_picture_ids=array();
                                                    $temp_pictures_users_sent=array();
                                                    $temp_picture_descriptions=array();
                                                    $temp_picture_timestamps=array();
                                                    $temp_original_picture_ids=array();
                                                    $temp_picture_types=array();

                                                    for($x = 1; $x < sizeof($picture_ids); $x++)
                                                    {
                                                        $temp_picture_ids[]=$picture_ids[$x];
                                                        $temp_pictures_users_sent[]=$pictures_users_sent[$x];
                                                        $temp_picture_descriptions[]=$picture_descriptions[$x];
                                                        $temp_picture_timestamps[]=$picture_timestamps[$x];
                                                        $temp_original_picture_ids[]=$original_picture_ids[$x];
                                                        $temp_picture_types[]=$picture_types[$x];
                                                    }

                                                    $new_id=$temp_picture_ids[sizeof($temp_picture_ids)-1]+1;
                                                    $temp_picture_ids[]=$new_id;
                                                    $temp_pictures_users_sent[]=$_SESSION['id'];
                                                    $temp_picture_descriptions[]=$description;
                                                    $temp_picture_timestamps[]=get_date();
                                                    $temp_original_picture_ids[]=$name;
                                                    $temp_picture_types[]=$type;

                                                    $picture_ids=implode('|^|*|', $temp_picture_ids);
                                                    $pictures_users_sent=implode('|^|*|', $temp_pictures_users_sent);
                                                    $picture_descriptions=implode('|^|*|', $temp_picture_descriptions);
                                                    $picture_timestamps=implode('|^|*|', $temp_picture_timestamps);
                                                    $original_picture_ids=implode('|^|*|', $temp_original_picture_ids);
                                                    $picture_types=implode('|^|*|', $temp_picture_types);
                                                }
                                                else
                                                {
                                                    if($array[0]=='')
                                                    {
                                                        $picture_ids[0]=0;
                                                        $new_id=0;
                                                        $pictures_users_sent[0]=$_SESSION['id'];
                                                        $picture_descriptions[0]=$description;
                                                        $picture_timestamps[0]=get_date();
                                                        $original_picture_ids[0]=$name;
                                                        $picture_types[0]=$type;
                                                    }
                                                    else
                                                    {
                                                        $new_id=$picture_ids[sizeof($picture_ids)-1]+1;
                                                        $picture_ids[]=$new_id;
                                                        $pictures_users_sent[]=$_SESSION['id'];
                                                        $picture_descriptions[]=$description;
                                                        $picture_timestamps[]=get_date();
                                                        $original_picture_ids[]=$name;
                                                        $picture_types[]=$type;
                                                    }

                                                    $picture_ids=implode('|^|*|', $picture_ids);
                                                    $pictures_users_sent=implode('|^|*|', $pictures_users_sent);
                                                    $picture_descriptions=implode('|^|*|', $picture_descriptions);
                                                    $picture_timestamps=implode('|^|*|', $picture_timestamps);
                                                    $original_picture_ids=implode('|^|*|', $original_picture_ids);
                                                    $picture_types=implode('|^|*|', $picture_types);
                                                }


                                                $container_height=260;
                                                $container_width=475;

                                                if($public_width/$public_height>=1.83)
                                                {
                                                    $public_image_width=$public_width/($public_height/260);
                                                    $public_image_height=260;
                                                }
                                                else
                                                {
                                                    $public_image_width=475;
                                                    $public_image_height=$public_height/($public_width/475);
                                                }
                                                
//                                                if($public_width>$public_height)
//                                                {
//                                                    if($public_width/$public_height>=1.5)
//                                                    {
//                                                        $ratio=$public_width/400;
//                                                        $new_thumb_height=$public_height/$ratio;
//                                                        $new_thumb_width=400;
//                                                    }
//                                                    else
//                                                    {
//                                                        $ratio=$public_height/260;
//                                                        $new_thumb_height=260;
//                                                        $new_thumb_width=$public_width/$ratio;
//                                                    }
//
//    //                                                $new_thumb_width=400;
//    //                                                $new_thumb_height=$public_height/($public_width/400);
//                                                }
//                                                else if($public_height>$public_width)
//                                                {
//                                                    
//                                                    $ratio=$public_height/260;
//                                                    $new_thumb_width=$public_width/$ratio;
//                                                    $new_thumb_height=260;
//
//
//    //                                                $new_thumb_height=260;
//    //                                                $new_thumb_width=$public_width/($public_height/260);
//                                                }
//                                                else if($public_height==$public_width)
//                                                {
//                                                    $ratio=$public_width/400;
//                                                    $new_thumb_height=$public_height/$ratio;
//                                                    $new_thumb_width=400;
//                                                }


                                                //if image is a jpg
                                                if($type=='jpeg'||$type=='jpg')
                                                {
                                                    $path="public/photos/$new_id.jpg";

                                                    //uploads thumb nail
                                                    $img=imagecreatefromjpeg($tmp_path."jpg.jpg");
                                                    $thumb=imagecreatetruecolor($container_width, $container_height);
                                                    imagecopyresampled($thumb, $img, 0, 0, 0, 0, $public_image_width, $public_image_height, $public_width, $public_height);
                                                    imagejpeg($thumb, $tmp_path."public.jpg", 80);

                                                    $s3->putObjectFile($tmp_path."public.jpg", "bucket_name", $path, S3::ACL_PUBLIC_READ);

                                                }

                                                //if image is a png
                                                else if($type=='png')
                                                {
                                                    $path="public/photos/$new_id.png";
                                                    //uploads thumb nail

                                                    $img=imagecreatefrompng($tmp_path."png.png");
                                                    $thumb=imagecreatetruecolor($container_width, $container_height);
//                                                    $black=imagecolorallocate($thumb, 0,0,0);
//                                                    imagecolortransparent($thumb, $black);
                                                    imagecopyresampled($thumb, $img, 0, 0, 0, 0, $public_image_width, $public_image_height, $public_width, $public_height);
                                                    imagepng($thumb, $tmp_path."public.png", 9);

                                                    $s3->putObjectFile($tmp_path."public.png", "bucket_name", $path, S3::ACL_PUBLIC_READ);

                                                }
                                                else if($type=='gif')
                                                {
                                                    $path="public/photos/$new_id.gif";

                                                    //uploads thumb nail
                                                    $img=imagecreatefromgif($tmp_path."gif.gif");
                                                    $thumb=imagecreatetruecolor($container_width, $container_height);
                                                    imagecopyresampled($thumb, $img, 0, 0, 0, 0, $public_image_width, $public_image_height, $public_width, $public_height);
                                                    imagegif($thumb, $tmp_path."public.gif");

                                                    $s3->putObjectFile($tmp_path."public.gif", "bucket_name", $path, S3::ACL_PUBLIC_READ);

                                                }

                                                $query2=mysql_query("UPDATE public SET picture_ids='$picture_ids', pictures_users_sent='$pictures_users_sent', picture_descriptions='$picture_descriptions', picture_timestamps='$picture_timestamps', original_picture_ids='$original_picture_ids', picture_types='$picture_types' WHERE num=1");

                                            }
                                        }

                                        unlink($tmp_path);
                                        unlink($tmp_path.".jpg");
                                        unlink($tmp_path."jpg.jpg");
                                        unlink($tmp_path."thumbnail.jpg");
                                        unlink($tmp_path."public.jpg");
                                        unlink($tmp_path.".png");
                                        unlink($tmp_path."png.png");
                                        unlink($tmp_path."thumbnail.png");
                                        unlink($tmp_path."public.png");
                                        unlink($tmp_path.".gif");
                                        unlink($tmp_path."gif.gif");
                                        unlink($tmp_path."thumbnail.gif");
                                        unlink($tmp_path."public.gif");


//                                        if(strstr($_SERVER['SERVER_NAME'], "www")==false)
//                                            header("Location: http://m.redlay.com/home.php");
                                        $message="Photo uploaded";
                                    }
                                }
                                else
                                    $message="Something went wrong when copying the photo";

//                            }
                        }
                        else
                            $message="Photo's width or height is too small. Needs to be more than 150px.";
                    }
                    else
                        $message="That is not an image!";
                }
                else
                {
                    $message="Something went wrong. We are working to fix it";
                    log_error("upload_picture_url.php: ", mysql_error());
                }

            }
            else
                $message="Description is too long. Please keep it under 1,000 characters";
        }
        else
            $message="Photo file is too big. 10MB is the max.";
    }
    else
        $message="Photo doesn't exist";
}
else
    $message="Invalid audience selected";

$JSON=array();
$JSON['current_user']=$_SESSION['id'];
$JSON['photo_id']=$name;
if($type=="jpeg")
    $type="jpg";

$JSON['type']=$type;
$JSON['width']=$public_width;
$JSON['height']=$public_height;
$JSON['errors']=$message;
echo json_encode($JSON);
exit();