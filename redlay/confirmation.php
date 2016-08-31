<?php
@include('init.php');
include('universal_functions.php');

if(isset($_SESSION['id'])||isset($_SESSION['page_id']))
{
    header("Location: http://www.redlay.com");
    exit();
}


$passkey=clean_string($_GET['passkey']);

////gets all the necessary AWS stuff
require 'aws-sdk-for-php-master/sdk.class.php';

$array=array();
$array['key']=ACCESS_KEY;
$array['secret']=SECRET_KEY;
$s3=new AmazonS3($array);

//gets all the necessary AWS schtuff
if (!class_exists('S3'))
    require_once('S3.php');
if (!defined('awsAccessKey'))
    define('awsAccessKey', ACCESS_KEY);
if (!defined('awsSecretKey'))
    define('awsSecretKey', SECRET_KEY);

//creates S3 item with schtuff
$awsS3 = new S3(awsAccessKey, awsSecretKey);

echo "Creating account...";

if(strlen($passkey)==40)
{
    $query=mysql_query("SELECT firstName, lastName, password, email FROM temp_users WHERE passkey='$passkey' LIMIT 1");
    if($query&&mysql_num_rows($query)==1)
    {
            $array=mysql_fetch_row($query);

            $first_name=$array[0];
            $last_name=$array[1];
            $password=$array[2];
            $email=$array[3];
            
//                    //sends email saying someone signed up
//                    $message=$first_name." ".$last_name." signed up";
//                    $from=get_email_from();
//                    $to="";
//                    $subject="Sign up";
//
//                    $temp_array=array();
//                    $temp_array['key']=ACCESS_KEY;
//                    $temp_array['secret']=SECRET_KEY;
//                    $amazonSes = new AmazonSES($temp_array);
//                    $amazonSes->verify_email_address($from);
//
//                    $amazonSes->send_email($from,
//                        array('ToAddresses' => array($to)),
//                        array(
//                            'Subject.Data' => $subject,
//                            'Body.Text.Data' => $message,
//                        )
//                    );

            $timestamp=get_date();

            //gives completely unique and random account id
            $bool=false;
            while($bool==false)
            {
                //gets a random SHA512 hash of random hash for the account id
                $temp_hash=sha1(uniqid(rand()));
                $temp_salt=sha1(uniqid(rand()));
                $account_id=crypt($temp_hash, '$6$rounds=5000$'.$temp_salt.'$');

                $query=mysql_query("SELECT id FROM users WHERE account_id='$account_id' LIMIT 1");
                if($query&&mysql_num_rows($query)==0)
                    $bool=true;
            }

            //inserts the user into the users table
            $query=mysql_query("INSERT INTO users SET firstName= '$first_name', lastName= '$last_name', password= '$password', email='$email', ip_addresses='$_SERVER[HTTP_X_FORWARDED_FOR]', timestamps='$timestamp', account_id='$account_id'");
            if(!$query)
            {
                echo "Error something has gone wrong when trying to create your account";
                log_error("confirmation.php: (1): ",mysql_error());
            }
            
            //sets login cookie with value of the user's account id for a month
            setcookie('acc_id', $account_id, (time()+(86400*31)), null, null, false, true);

            $query=mysql_query("SELECT id FROM users WHERE email='$email'");
            $array=mysql_fetch_row($query);
            $_SESSION['id']=$array[0];

            //remove from temporary table
            $query=mysql_query("DELETE FROM temp_users WHERE passkey='$passkey'");
            if(!$query)
            {
                echo "Error something has gone wrong when trying to create your account";
                log_error("confirmation.php: (2): ",mysql_error());
            }
            $query=mysql_query("INSERT INTO alerts SET user_id=$_SESSION[id]");
            if(!$query)
            {
                echo "Error something has gone wrong when trying to create your account";
                log_error("confirmation.php: (3): ",mysql_error());
            }
            $query=mysql_query("SELECT user_id_2, users_listed, messages, user_sent, timestamps FROM messages WHERE user_id=1 LIMIT 1");
            if($query)
            {
                $array=mysql_fetch_row($query);
                $other_users=explode('|^|*|', $array[0]);
                $users_listed=explode('|^|*|', $array[1]);
                $messages=explode('|^|*|', mysql_real_escape_string($array[2]));
                $users_sent=explode('|^|*|', $array[3]);
                $timestamps=explode('|^|*|', $array[4]);

                $other_users[]=$_SESSION['id'];
                $users_listed[]=$_SESSION['id'];
                $messages[]="Hi, I am the creator of Redlay. Thanks for joining! How did you find out about the site?";
                $users_sent[]=1;
                $date=get_date();
                $timestamps[]=$date;

                $other_users=implode('|^|*|', $other_users);
                $users_listed=implode('|^|*|', $users_listed);
                $messages=implode('|^|*|', $messages);
                $users_sent=implode('|^|*|', $users_sent);
                $timestamps=implode('|^|*|', $timestamps);

                $query=mysql_query("UPDATE messages SET user_id_2='$other_users', users_listed='$users_listed', messages='$messages', user_sent='$users_sent', timestamps='$timestamps' WHERE user_id=1");
                if($query)
                {
                    $new_message="Hi, I am the creator of Redlay. Thanks for joining! How did you find out about the site?";
                    $query=mysql_query("INSERT INTO messages SET user_id_2=1, users_listed=1, messages='$new_message', new_messages=1, user_sent=1, timestamps=".$date.", user_id=$_SESSION[id]");
                    if(!$query)
                    {
                        echo "Error something has gone wrong when trying to create your account";
                        log_error("confirmation.php: (6): ",mysql_error());
                    }
                }
                else
                {
                    $query=mysql_query("INSERT INTO messages SET user_id=$_SESSION[id]");
                    if(!$query)
                    {
                        echo "Error something has gone wrong when trying to create your account";
                        log_error("confirmation.php: (9): ",mysql_error());
                    }
                }
            }
            else
            {
                $query=mysql_query("INSERT INTO messages SET user_id=$_SESSION[id]");
                if(!$query)
                {
                    echo "Error something has gone wrong when trying to create your account";
                    log_error("confirmation.php: (5): ",mysql_error());
                }
            }
                            
            $query=mysql_query("INSERT INTO pending_friend_requests SET user_id=$_SESSION[id]");
            if(!$query)
            {
                echo "Error something has gone wrong when trying to create your account";
                log_error("confirmation.php: (6): ",mysql_error());
            }
            $query=mysql_query("INSERT INTO pictures SET user_id=$_SESSION[id], pictures='0', image_types='jpg', picture_descriptions='Profile Picture', timestamp='$timestamp', image_audiences='Everyone'");
            if(!$query)
            {
                echo "Error something has gone wrong when trying to create your account";
                log_error("confirmation.php: (7): ",mysql_error());
            }
            $query=mysql_query("INSERT INTO content SET user_id=$_SESSION[id]");
            if(!$query)
            {
                echo "Error something has gone wrong when trying to create your account";
                log_error("confirmation.php: (8): ",mysql_error());
            }

            $query=mysql_query("SELECT user_friends, friend_timestamps, audience_groups FROM user_data WHERE user_id=1 LIMIT 1");
            if($query)
            {
                $array=mysql_fetch_row($query);
                $adds=explode('|^|*|', $array[0]);
                $add_timestamps=explode('|^|*|', $array[1]);
                $groups=explode('|^|*|', $array[2]);

                $adds[]=$_SESSION['id'];
                $add_timestamps[]=$date;
                $groups[]="Other";

                $adds=implode('|^|*|', $adds);
                $add_timestamps=implode('|^|*|', $add_timestamps);
                $groups=implode('|^|*|', $groups);

                $query=mysql_query("UPDATE user_data SET user_friends='$adds', friend_timestamps='$add_timestamps', audience_groups='$groups' WHERE user_id=1");
                if($query)
                {
                    $query=mysql_query("INSERT INTO user_data SET user_id=$_SESSION[id], user_friends='1', friend_timestamps='$date', audience_groups='Friends', user_relationship='NA', user_mood='Happy', email_settings='1|^|*|1|^|*|1|^|*|1|^|*|1|^|*|1|^|*|1|^|*|1|^|*|1|^|*|1|^|*|1|^|*|1', user_sex='Other' ");
                    if(!$query)
                    {
                        echo "Error something has gone wrong when trying to create your account";
                        log_error("confirmation.php: (10): ",mysql_error());
                    }
                }
                else
                {
                    $query=mysql_query("INSERT INTO user_data SET user_relationship='NA', user_mood='Happy', email_settings='1|^|*|1|^|*|1|^|*|1|^|*|1|^|*|1|^|*|1|^|*|1|^|*|1|^|*|1|^|*|1|^|*|1', user_sex='Other' ");
                    if(!$query)
                    {
                        echo "Error something has gone wrong when trying to create your account";
                        log_error("confirmation.php: (11): ",mysql_error());
                    }
                }
            }
            else
            {
                $query=mysql_query("INSERT INTO user_data SET user_relationship='NA', user_mood='Happy', email_settings='1|^|*|1|^|*|1|^|*|1|^|*|1|^|*|1|^|*|1|^|*|1|^|*|1|^|*|1|^|*|1|^|*|1', user_sex='Other' ");
                if(!$query)
                {
                    echo "Error something has gone wrong when trying to create your account";
                    log_error("confirmation.php: (12): ",mysql_error());
                }
            }
            $display_colors="220,20,0|^|*|256,256,256|^|*|30,30,30|^|*|100";
            $query=mysql_query("INSERT INTO user_display SET display_colors='$display_colors', background_fixed='no', friend_title='Adds', post_title='Posts', information_title='Information', birthday_year='yes', user_colors='|^|*||^|*||^|*||^|*||^|*||^|*|', registration_intro='no', home_view='Everything|^|*|-1', calendar_visible='no', user_id=$_SESSION[id]");
            if(!$query)
            {
                echo "Error something has gone wrong when trying to create your account";
                log_error("confirmation.php: (13): ",mysql_error());
            }
            $query=mysql_query("INSERT INTO user_privacy SET user_id=$_SESSION[id], general='yes|^|*|yes|^|*|yes|^|*|yes', display_non_friends='yes|^|*|yes|^|*|yes|^|*|yes|^|*|yes|^|*|yes|^|*|yes|^|*|yes', search_options='yes|^|*|yes|^|*|yes|^|*|yes|^|*|yes'");
            if(!$query)
            {
                echo "Error something has gone wrong when trying to create your account";
                log_error("confirmation.php: (14): ",mysql_error());
            }
            $query=mysql_query("INSERT INTO calendar SET user_id=$_SESSION[id]");
            if(!$query)
            {
                echo "Error something has gone wrong when trying to create your account";
                log_error("confirmation.php: (15): ",mysql_error());
            }
            $query=mysql_query("INSERT INTO user_maps SET map_type='grid', default_position_grid='0|^|*|1|^|*|2|^|*|3|^|*|4|^|*|5|^|*|6', user_id=$_SESSION[id]");
            if(!$query)
            {
                echo "Error something has gone wrong when trying to create your account";
                log_error("confirmation.php: (16): ",mysql_error());
            }
            $query=mysql_query("INSERT INTO online SET timestamp=".get_date().", user_id=$_SESSION[id]");
            if(!$query)
            {
                echo "Error something has gone wrong when trying to create your account";
                log_error("confirmation.php: (17): ",mysql_error());
            }
            $query=mysql_query("SELECT num_users FROM data WHERE num=1");
            if($query)
            {
                $array=mysql_fetch_row($query);
                $num_users=$array[0];
                $num_users++;
                $query=mysql_query("UPDATE data SET num_users=$num_users WHERE num=1");
                if(!$query)
                {
                    echo "Error something has gone wrong when trying to create your account";
                    log_error("confirmation.php: (18): ",mysql_error());
                }
            }
            else
            {
                echo "Error something has gone wrong when trying to create your account";
                log_error("confirmation.php: (19): ",mysql_error());
            }
            
            $query=mysql_query("SELECT user_ids, user_names, user_timestamps FROM public WHERE num=1");
            if($query)
            {
                $array=mysql_fetch_row($query);
                $user_ids=explode('|^|*|', $array[0]);
                $user_names=explode('|^|*|', $array[1]);
                $user_timestamps=explode('|^|*|', $array[2]);

                if(sizeof($user_ids)==200)
                {
                    $temp_user_ids=array();
                    $temp_user_names=array();
                    $temp_user_timestamps=array();

                    for($x = 1; $x < sizeof($user_ids); $x++)
                    {
                        $temp_user_ids[]=$user_ids[$x];
                        $temp_user_names[]=$user_names[$x];
                        $temp_user_timestamps[]=$user_timestamps[$x];
                    }

                    $user_ids=$temp_user_ids;
                    $user_names=$temp_user_names;
                    $user_timestamps=$temp_user_timestamps;
                }

                $user_ids[]=$_SESSION['id'];
                $user_names[]=$first_name." ".$last_name;
                $user_timestamps[]=get_date();

                $user_ids=implode('|^|*|', $user_ids);
                $user_names=implode('|^|*|', $user_names);
                $user_timestamps=implode('|^|*|', $user_timestamps);

                $query=mysql_query("UPDATE public SET user_ids='$user_ids', user_names='$user_names', user_timestamps='$user_timestamps' WHERE num=1");
                if(!$query)
                {
                    echo "Error something has gone wrong when trying to create your account";
                    log_error("confirmation.php: (20): ",mysql_error());
                }
            }
            else
            {
                echo "Error something has gone wrong when trying to create your account";
                log_error("confirmation.php: (21): ",mysql_error());
            }
            
            $responses=array();

            $new=array();
            $new['body']="";
            $new['contentType']="text/plain";
            $new['acl']=AmazonS3::ACL_PRIVATE;

            $responses[]=$s3->create_object('bucket_name', "users/$_SESSION[id]/files/other/login.txt", $new);
            $responses[]=$s3->create_object('bucket_name', "users/$_SESSION[id]/files/other/logout.txt", $new);
            $responses[]=$s3->create_object('bucket_name', "users/$_SESSION[id]/files/photos/photo_views.txt", $new);
            $responses[]=$s3->create_object('bucket_name', "users/$_SESSION[id]/files/photos/photos_commented_on.txt", $new);
            $responses[]=$s3->create_object('bucket_name', "users/$_SESSION[id]/files/photos/photos_disliked.txt", $new);
            $responses[]=$s3->create_object('bucket_name', "users/$_SESSION[id]/files/photos/photos_liked.txt", $new);
            $responses[]=$s3->create_object('bucket_name', "users/$_SESSION[id]/files/photos/photos_viewed.txt", $new);
            $responses[]=$s3->create_object('bucket_name', "users/$_SESSION[id]/files/posts/posts_commented_on.txt", $new);
            $responses[]=$s3->create_object('bucket_name', "users/$_SESSION[id]/files/posts/posts_disliked.txt", $new);
            $responses[]=$s3->create_object('bucket_name', "users/$_SESSION[id]/files/posts/posts_liked.txt", $new);
            $responses[]=$s3->create_object('bucket_name', "users/$_SESSION[id]/files/profiles/profile_views.txt", $new);
            $responses[]=$s3->create_object('bucket_name', "users/$_SESSION[id]/files/profiles/profiles_viewed.txt", $new);


            //default profile picture's current file location
            $file="http://pics.redlay.com/pictures/default_profile_picture.png";

            //temp png location
            $temp_path="/tmp/".md5(uniqid(rand())).".png";

            //copies to temp png location
            copy($file, $temp_path);

            //temp jpg location
            $new_temp_path="/tmp/".md5(uniqid(rand())).".jpg";

            //converts png to jpg
            $img=imagecreatefrompng($temp_path);
            $thumb=imagecreatetruecolor(250, 250);
            imagecopyresampled($thumb, $img, 0, 0, 0, 0, 250, 250, 250, 250);
            imagejpeg($thumb, $new_temp_path, 80);

            //uploads default profile picture from temporary location
            $awsS3->putObjectFile($new_temp_path, "bucket_name", "users/$_SESSION[id]/photos/0.jpg", S3::ACL_PUBLIC_READ);
            $awsS3->putObjectFile($new_temp_path, "bucket_name", "users/$_SESSION[id]/thumbs/0.jpg", S3::ACL_PUBLIC_READ);

            //adds a temp file and deletes to create the themes folder
            $s3->create_object('bucket_name', "users/1/themes/temp.txt", $new);

            //deletes temperary files
            unlink($temp_path);
            unlink($new_temp_path);
            imagedestroy($thumb);

            header("Location: http://www.redlay.com/thankyou.php");
            exit();     
                    
    }
    else
        echo "Confirmation code doesn't exist! The account was either just created or the confirmation code never existed at all.";
}
else
    echo "Invalid confirmation code";