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

$picture_id=clean_string($_POST['picture_id']);
if(isset($_SESSION['id']))
{
    if($picture_id!='')
    {
        $query=mysql_query("SELECT pictures, picture_descriptions, timestamp, picture_likes, picture_dislikes, comments_user_sent, comment_likes, comment_dislikes, comment_timestamps, picture_comments, image_audiences, image_types FROM pictures WHERE user_id=$_SESSION[id] LIMIT 1");
        if($query&&mysql_num_rows($query)==1)
        {
            $array=mysql_fetch_row($query);
            $pictures=explode('|^|*|', $array[0]);
            $descriptions=explode('|^|*|', $array[1]);
            $timestamps=explode('|^|*|', $array[2]);
            $likes=explode('|^|*|', $array[3]);
            $dislikes=explode('|^|*|', $array[4]);
            $comments_user_sent=explode('|^|*|', $array[5]);
            $comment_likes=explode('|^|*|', $array[6]);
            $comment_dislikes=explode('|^|*|', $array[7]);
            $comment_timestamps=explode('|^|*|', $array[8]);
            $comments=explode('|^|*|', $array[9]);
            $image_audiences=explode('|^|*|', $array[10]);
            $image_types=explode('|^|*|', $array[11]);

            $index=-1;
            for($x = 0; $x < sizeof($pictures); $x++)
            {
                if($pictures[$x]==$picture_id)
                    $index=$x;
            }
            
            
            
            if($index!=-1)
            {
                $image_type=$image_types[$index];
                
                $temp_array=array();
                $temp_array2=array();
                $temp_array3=array();
                $temp_array4=array();
                $temp_array5=array();
                $temp_array6=array();
                $temp_array7=array();
                $temp_array8=array();
                $temp_array9=array();
                $temp_array10=array();
                $temp_array11=array();
                $temp_array12=array();
                $temp_array13=array();
                if(in_array($picture_id, $pictures))
                {
                    for($x = 0; $x < sizeof($timestamps); $x++)
                    {
                        if($x!=$index)
                        {
                            $temp_array[]=$descriptions[$x];
                            $temp_array2[]=$timestamps[$x];
                            $temp_array3[]=$likes[$x];
                            $temp_array4[]=$dislikes[$x];
                            $temp_array5[]=$comments_user_sent[$x];
                            $temp_array6[]=$comment_likes[$x];
                            $temp_array7[]=$comment_dislikes[$x];
                            $temp_array8[]=$comment_timestamps[$x];
                            $temp_array9[]=$comments[$x];
                            $temp_array10[]=$pictures[$x];
                            $temp_array12[]=$image_audiences[$x];
                            $temp_array13[]=$image_types[$x];
                        }
                    }
                    $descriptions=mysql_real_escape_string(implode('|^|*|', $temp_array));
                    $timestamps=implode('|^|*|', $temp_array2);
                    $likes=implode('|^|*|', $temp_array3);
                    $dislikes=implode('|^|*|', $temp_array4);
                    $comments_user_sent=implode('|^|*|', $temp_array5);
                    $comment_likes=implode('|^|*|', $temp_array6);
                    $comment_dislikes=implode('|^|*|', $temp_array7);
                    $comment_timestamps=implode('|^|*|', $temp_array8);
                    $comments=mysql_real_escape_string(implode('|^|*|', $temp_array9));
                    $pictures=implode('|^|*|', $temp_array10);
                    $image_audiences=implode('|^|*|', $temp_array12);
                    $image_types=implode('|^|*|', $temp_array13);

                    $query2=mysql_query("UPDATE pictures SET pictures='$pictures', picture_descriptions='$descriptions', timestamp='$timestamps', picture_likes='$likes', picture_dislikes='$dislikes', comments_user_sent='$comments_user_sent', comment_likes='$comment_likes', comment_dislikes='$comment_dislikes', comment_timestamps='$comment_timestamps', picture_comments='$comments', image_audiences='$image_audiences', image_types='$image_types' WHERE user_id=$_SESSION[id]");
                    if($query2)
                    {
                        $path="users/$_SESSION[id]/photos/$picture_id.$image_type";
                        $thumb_path="users/$_SESSION[id]/thumbs/$picture_id.$image_type";

                        $s3->deleteObject("bucket_name", $path);
                        $s3->deleteObject("bucket_name", $thumb_path);
//                        unlink($path);
//                        unlink($thumb_path);
                        
                        
                        
                        //deletes public's version of photo
                        $query=mysql_query("SELECT pictures_users_sent, picture_ids, original_picture_ids, picture_descriptions, picture_types, picture_timestamps FROM public WHERE num=1");
                        if($query)
                        {
                            $array=mysql_fetch_row($query);
                            $users_sent=explode('|^|*|', $array[0]);
                            $picture_ids=explode('|^|*|', $array[1]);
                            $original_picture_ids=explode('|^|*|', $array[2]);
                            $picture_descriptions=explode('|^|*|', $array[3]);
                            $picture_types=explode('|^|*|', $array[4]);
                            $picture_timestamps=explode('|^|*|', $array[5]);
                            
                            
                            $temp_users_sent=array();
                            $temp_picture_ids=array();
                            $temp_original_picture_ids=array();
                            $temp_picture_descriptions=array();
                            $temp_picture_timestamps=array();
                            $temp_picture_types=array();
                            
                            $public_id=0;
                            for($x = 0; $x < sizeof($original_picture_ids); $x++)
                            {
                                if($original_picture_ids[$x]!=$picture_id)
                                {
                                    $temp_users_sent[]=$users_sent[$x];
                                    $temp_picture_ids[]=$picture_ids[$x];
                                    $temp_original_picture_ids[]=$original_picture_ids[$x];
                                    $temp_picture_descriptions[]=$picture_descriptions[$x];
                                    $temp_picture_timestamps[]=$picture_timestamps[$x];
                                    $temp_picture_types[]=$picture_types[$x];
                                }
                                else
                                {
                                    $public_id=$picture_ids[$x];
                                    $public_type=$picture_types[$x];
                                }
                            }
                            
                            $users_sent=implode('|^|*|', $temp_users_sent);
                            $picture_ids=implode('|^|*|', $temp_picture_ids);
                            $original_picture_ids=implode('|^|*|', $temp_original_picture_ids);
                            $picture_descriptions=implode('|^|*|', $temp_picture_descriptions);
                            $picture_timestamps=implode('|^|*|', $temp_picture_timestamps);
                            $picture_types=implode('|^|*|', $temp_picture_types);
                            
                            $query=mysql_query("UPDATE public SET pictures_users_sent='$users_sent', picture_ids='$picture_ids', original_picture_ids='$original_picture_ids', picture_descriptions='$picture_descriptions', picture_types='$picture_types', picture_timestamps='$picture_timestamps' WHERE num=1");
                            if($query)
                            {
                                $path="public/photos/$public_id.".$public_type;

                                $s3->deleteObject("bucket_name", $path);
                            }
                        }
                    }
                    else
                    {
                        echo "Something went wrong ".$comments;
                        log_error("delete_photo.php: ", mysql_error());
                    }
                }
                else
                    echo "Picture does not exist";
            }
        }
        else
        {
            echo "Something went wrong ".$comments;
            log_error("delete_photo.php: ", mysql_error());
        }
    }
    else
        echo "Picture does not exist";
}
?>