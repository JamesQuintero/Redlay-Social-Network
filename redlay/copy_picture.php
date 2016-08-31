<?php
include('init.php');
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



$picture_id=clean_string($_POST['picture_id']);
$ID=(int)($_POST['user_id']);
$type=clean_string($_POST['type']);
$description=clean_string($_POST['description']);
$audience=$_POST['audience'];

//checks if groups are valid
$is_valid=true;
if(isset($audience[0]))
{
    if(in_array('Everyone', $audience))
    {
        $audience=array();
        $audience[0]='Everyone';
    }
    else
    {
        for($x = 0; $x < sizeof($audience); $x++)
        {
            if(!is_valid_audience($audience[$x]))
                $is_valid=false;
        }
    }
}
else
{
    $audience=array();
    $audience[0]='Everyone';
}

//if selected groups are valid
if($is_valid==true)
{
    if(is_id($ID)&&user_id_exists($ID))
    {
        if(!user_id_terminated($ID))
        {
            if($type=='user')
            {
                $query=mysql_query("SELECT pictures, image_types FROM pictures WHERE user_id=$ID LIMIT 1");
                if($query&&mysql_num_rows($query)==1)
                {
                    $array=mysql_fetch_row($query);
                    $pictures=explode('|^|*|', $array[0]);
                    $image_types=explode('|^|*|', $array[1]);

                    $index=-1;
                    for($x =0; $x < sizeof($pictures); $x++)
                    {
                        if($pictures[$x]==$picture_id)
                            $index=$x;
                    }

                    if($index!=-1)
                    {
                        //path of current hosted image
                        $prev="users/$ID/photos/$picture_id.".$image_types[$index];
                        $prev_thumb="users/$ID/thumbs/$picture_id.".$image_types[$index];

                        //gets image dimensions
                        list($width, $height)=getimagesize($prev);

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


                        $type=$image_types[$index];
                        
                        $name=sha1(uniqid(rand()));

                        if(has_redlay_gold($_SESSION['id'], 'photo_quality')||($width<=800&&$height<=800))
                        {
                            $new="users/$_SESSION[id]/photos/$name.".$image_types[$index];
                            $new_thumb="users/$_SESSION[id]/thumbs/$name.".$image_types[$index];
                            
                            $s3->copyObject("bucket_name", $prev, "bucket_name", $new, S3::ACL_PUBLIC_READ);
                            $s3->copyObject("bucket_name", $prev_thumb, "bucket_name", $new_thumb, S3::ACL_PUBLIC_READ);
                        }

                        //if photo is bigger than default and current user doesn't have redlay gold
                        else
                        {
                            //if image is a jpg
                            if($type=='jpeg'||$type=='jpg')
                            {
                                $path="users/$_SESSION[id]/photos/$name.jpg";
                                $thumb_path="users/$_SESSION[id]/thumbs/$name.jpg";

                                $value=md5(uniqid(rand()));
                                $tmp_path="/tmp/$value.jpg";
                                $tmp_thumb_path="/tmp/".$value."thumbnail.jpg";
                                
                                copy("http://u.redlay.com/users/$ID/photos/$picture_id.jpg", $tmp_path);
                                copy("http://u.redlay.com/users/$ID/thumbs/$picture_id.jpg", $tmp_thumb_path);
                                
                                
                                //uploads image
                                $img=imagecreatefromjpeg($tmp_path);
                                $thumb=imagecreatetruecolor($new_width, $new_height);
                                imagecopyresampled($thumb, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
                                imagejpeg($thumb, $tmp_path, 80);
                                
                                //uploads new picture and thumbnail
                                $s3->putObjectFile($tmp_path, "bucket_name", $path, S3::ACL_PUBLIC_READ);
                                $s3->putObjectFile($tmp_thumb_path, "bucket_name", $thumb_path, S3::ACL_PUBLIC_READ);
                            }

                            //if image is a png
                            else if($type=='png')
                            {
                                $path="users/$_SESSION[id]/photos/$name.png";
                                $thumb_path="users/$_SESSION[id]/thumbs/$name.png";

                                $value=md5(uniqid(rand()));
                                $tmp_path="/tmp/$value.png";
                                $tmp_thumb_path="/tmp/".$value."thumbnail.png";
                                
                                copy("http://u.redlay.com/users/$ID/photos/$picture_id.png", $tmp_path);
                                copy("http://u.redlay.com/users/$ID/thumbs/$picture_id.png", $tmp_thumb_path);
                                
                                    //uploads image
                                $img=imagecreatefrompng($tmp_path);
                                $thumb=imagecreatetruecolor($new_width, $new_height);
                                $black=imagecolorallocate($thumb, 0,0,0);
                                imagecolortransparent($thumb, $black);
                                imagecopyresampled($thumb, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
                                imagepng($thumb, $tmp_path, 9);
                                
                                //uploads new picture and thumbnail
                                $s3->putObjectFile($tmp_path, "bucket_name", $path, S3::ACL_PUBLIC_READ);
                                $s3->putObjectFile($tmp_thumb_path, "bucket_name", $thumb_path, S3::ACL_PUBLIC_READ);
                            }
                            else if($type=='gif')
                            {
                                $path="users/$_SESSION[id]/photos/$name.gif";
                                $thumb_path="users/$_SESSION[id]/thumbs/$name.gif";

                                $value=md5(uniqid(rand()));
                                $tmp_path="/tmp/$value.gif";
                                $tmp_thumb_path="/tmp/".$value."thumbnail.gif";
                                
                                copy("http://u.redlay.com/users/$ID/photos/$picture_id.gif", $tmp_path);
                                copy("http://u.redlay.com/users/$ID/thumbs/$picture_id.gif", $tmp_thumb_path);
                                
                                //uploads image
                                $img=imagecreatefromgif($tmp_path);
                                $thumb=imagecreatetruecolor($new_width, $new_height);
                                imagecopyresampled($thumb, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
                                imagegif($thumb, $tmp_path);
                                
                                //uploads new picture and thumbnail
                                $s3->putObjectFile($tmp_path, "bucket_name", $path, S3::ACL_PUBLIC_READ);
                                $s3->putObjectFile($tmp_thumb_path, "bucket_name", $thumb_path, S3::ACL_PUBLIC_READ);
                            }
                            
                            unlink($tmp_path);
                            unlink($tmp_thumb_path);
                        }


                        $query=mysql_query("SELECT pictures, picture_descriptions, image_types, image_audiences, picture_likes, picture_dislikes, comment_ids, comments_user_sent, comment_likes, comment_dislikes, comment_timestamps, picture_comments, timestamp FROM pictures WHERE user_id=$_SESSION[id] LIMIT 1");
                        if($query&&mysql_num_rows($query)==1)
                        {

                            $array=mysql_fetch_row($query);
                            $pictures=explode('|^|*|', $array[0]);
                            $picture_descriptions=explode('|^|*|', str_replace("'", "\'", $array[1]));
                            $image_types=explode('|^|*|', $array[2]);
                            $image_audiences=explode('|^|*|', $array[3]);
                            $picture_likes=explode('|^|*|', $array[4]);
                            $picture_dislikes=explode('|^|*|', $array[5]);
                            $comment_ids=explode('|^|*|', $array[6]);
                            $comments_users_sent=explode('|^|*|', $array[7]);
                            $comment_likes=explode('|^|*|', $array[8]);
                            $comment_dislikes=explode('|^|*|', $array[9]);
                            $comment_timestamps=explode('|^|*|', $array[10]);
                            $picture_comments=explode('|^|*|', str_replace("'", "\'", $array[11]));
                            $timestamps=explode('|^|*|', $array[12]);

                            $date=get_date();
                            if($array[0]=='')
                            {
                                $pictures[0]=$name;
                                $picture_descriptions[0]=$description;
                                $image_types[0]=$type;
                                $image_audiences[0]=implode('|%|&|', $audience);
                                $picture_likes[0]='';
                                $picture_dislikes[0]='';
                                $comment_ids[0]='';
                                $comments_users_sent[0]='';
                                $comment_likes[0]='';
                                $comment_dislikes[0]='';
                                $comment_timestamps[0]='';
                                $picture_comments[0]='';
                                $timestamps[0]=$date;
                            }
                            else
                            {
                                $pictures[]=$name;
                                $picture_descriptions[]=$description;
                                $image_types[]=$type;
                                $image_audiences[]=implode('|%|&|', $audience);
                                $picture_likes[]='';
                                $picture_dislikes[]='';
                                $comment_ids[]='';
                                $comments_users_sent[]='';
                                $comment_likes[]='';
                                $comment_dislikes[]='';
                                $comment_timestamps[]='';
                                $picture_comments[]='';
                                $timestamps[]=$date;
                            }

                            $pictures=implode('|^|*|', $pictures);
                            $picture_descriptions=implode('|^|*|', $picture_descriptions);
                            $image_types=implode('|^|*|', $image_types);
                            $image_audiences=implode('|^|*|', $image_audiences);
                            $picture_likes=implode('|^|*|', $picture_likes);
                            $picture_dislikes=implode('|^|*|', $picture_dislikes);
                            $comment_ids=implode('|^|*|', $comment_ids);
                            $comments_users_sent=implode('|^|*|', $comments_users_sent);
                            $comment_likes=implode('|^|*|', $comment_likes);
                            $comment_dislikes=implode('|^|*|', $comment_dislikes);
                            $comment_timestamps=implode('|^|*|', $comment_timestamps);
                            $picture_comments=implode('|^|*|', $picture_comments);
                            $timestamps=implode('|^|*|', $timestamps);

                            $query=mysql_query("UPDATE pictures SET pictures='$pictures', picture_descriptions='$picture_descriptions', image_types='$image_types', image_audiences='$image_audiences', picture_likes='$picture_likes', picture_dislikes='$picture_dislikes', comment_ids='$comment_ids', comments_user_sent='$comments_users_sent', comment_likes='$comment_likes', comment_dislikes='$comment_dislikes', comment_timestamps='$comment_timestamps', picture_comments='$picture_comments', timestamp='$timestamps' WHERE user_id=$_SESSION[id]");
                            if($query)
                                echo "Image copied";
                            else
                            {
                                echo "Something went wrong. We are working on fixing it";
                                log_error("copy_picture.php: (4): ",mysql_error());
                            }
                        }
                        else
                        {
                            echo "Something went wrong. We are working on fixing it";
                            log_error("copy_picture.php: (2): ",mysql_error());
                        }
                    }
                    else
                        echo "Picture no longer exists";
                }
                else
                {
                    echo "Something went wrong. We are working on fixing it";
                    log_error("copy_picture.php: (1): ",mysql_error());
                }
            }
            else if($type=='page')
            {
                //copy picture from page
            }
        }
        else
            echo "User ID terminated";
    }
    else
        echo "Invalid user ID";
}
else
    echo "Invalid groups selected";
?>