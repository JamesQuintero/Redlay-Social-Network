<?php
include('init.php');
include('universal_functions.php');
$allowed="users";
include('security_checks.php');


include("requiredS3.php");

$confirmation=clean_string($_POST['confirmation']);
$ID=(int)($_POST['user_id']);

if(is_id($ID))
{
    if($ID==$_SESSION['id'])
    {
        if($confirmation=='yes')
        {
            if(is_id($_SESSION['id'])&&user_id_exists($_SESSION['id'])&&!user_id_terminated($_SESSION['id']))
            {

                $query=mysql_query("DELETE FROM alerts WHERE user_id=$_SESSION[id]");
                if($query)
                {
                    $query=mysql_query("DELETE FROM calendar WHERE user_id=$_SESSION[id]");
                    if($query)
                    {
                        $query=mysql_query("DELETE FROM content WHERE user_id=$_SESSION[id]");
                        if($query)
                        {
                            $query=mysql_query("DELETE FROM facebook_invite WHERE user_id=$_SESSION[id]");
                            if($query)
                            {
                                $query=mysql_query("DELETE FROM messages WHERE user_id=$_SESSION[id]");
                                if($query)
                                {
                                    $query=mysql_query("DELETE FROM online WHERE user_id=$_SESSION[id]");
                                    if($query)
                                    {
                                        $query=mysql_query("DELETE FROM pending_friend_requests WHERE user_id=$_SESSION[id]");
                                        if($query)
                                        {
                                            $query=mysql_query("SELECT pictures, image_types FROM pictures WHERE user_id=$_SESSION[id] LIMIT 1");
                                            if($query&&mysql_num_rows($query)==1)
                                            {
                                                $array=mysql_fetch_row($query);
                                                $pictures=explode('|^|*|', $array[0]);
                                                $image_types=explode('|^|*|', $array[1]);

                                                //deletes pictures
                                                for($x = 0; $x < sizeof($pictures); $x++)
                                                {
                                                    $s3->deleteObject("bucket_name", "users/$_SESSION[id]/photos/$pictures[$x].$image_types[$x]");
                                                    $s3->deleteObject("bucket_name", "users/$_SESSION[id]/thumbs/$pictures[$x].$image_types[$x]");
                                                }

                                                $query=mysql_query("DELETE FROM pictures WHERE user_id=$_SESSION[id]");
                                                if($query)
                                                {
                                                    $query=mysql_query("DELETE FROM user_display WHERE user_id=$_SESSION[id]");
                                                    if($query)
                                                    {
                                                        $query=mysql_query("DELETE FROM user_maps WHERE user_id=$_SESSION[id]");
                                                        if($query)
                                                        {
                                                            $query=mysql_query("DELETE FROM user_privacy WHERE user_id=$_SESSION[id]");
                                                            if($query)
                                                            {
                                                                $query=mysql_query("DELETE FROM user_data WHERE user_id=$_SESSION[id]");
                                                                if($query)
                                                                {
                                                                    $query=mysql_query("INSERT INTO closed_accounts SET closed='yes', user_id=$_SESSION[id]");
                                                                    if($query)
                                                                    {
                                                                        $query=mysql_query("SELECT pictures_users_sent, picture_ids, original_picture_ids, picture_descriptions, picture_types, picture_timestamps FROM public WHERE num=1");
                                                                        if($query)
                                                                        {
                                                                            $array=mysql_fetch_row($query);
                                                                            $pictures_users_sent=explode('|^|*|', $array[0]);
                                                                            $picture_ids=explode('|^|*|', $array[1]);
                                                                            $original_picture_ids=explode('|^|*|', $array[2]);
                                                                            $picture_descriptions=explode('|^|*|', $array[3]);
                                                                            $picture_types=explode('|^|*|', $array[4]);
                                                                            $picture_timestamps=explode('|^|*|', $array[5]);

                                                                            $temp_pictures_users_sent=array();
                                                                            $temp_picture_ids=array();
                                                                            $temp_original_picture_ids=array();
                                                                            $temp_picture_descriptions=array();
                                                                            $temp_picture_types=array();
                                                                            $temp_picture_timestamps=array();

                                                                            $deleted_picture_ids=array();
                                                                            $deleted_picture_types=array();
                                                                            for($x = 0; $x < sizeof($pictures_users_sent); $x++)
                                                                            {
                                                                                if($pictures_users_sent[$x]!=$_SESSION['id'])
                                                                                {
                                                                                    $temp_pictures_users_sent[]=$pictures_users_sent[$x];
                                                                                    $temp_picture_ids[]=$picture_ids[$x];
                                                                                    $temp_original_picture_ids[]=$original_picture_ids[$x];
                                                                                    $temp_picture_descriptions[]=$picture_descriptions[$x];
                                                                                    $temp_picture_types[]=$picture_types[$x];
                                                                                    $temp_picture_timestamps[]=$picture_timestamps[$x];
                                                                                }
                                                                                else
                                                                                {
                                                                                    $deleted_picture_ids[]=$picture_ids[$x];
                                                                                    $deleted_picture_types[]=$picture_types[$x];
    ;                                                                            }
                                                                            }
                                                                            $pictures_users_sent=implode('|^|*|', $temp_pictures_users_sent);
                                                                            $picture_ids=implode('|^|*|', $temp_picture_ids);
                                                                            $original_picture_ids=implode('|^|*|', $temp_original_picture_ids);
                                                                            $picture_descriptions=implode('|^|*|', $temp_picture_descriptions);
                                                                            $picture_types=implode('|^|*|', $temp_picture_types);
                                                                            $picture_timestamps=implode('|^|*|', $temp_picture_timestamps);

                                                                            $query=mysql_query("UPDATE public SET pictures_users_sent='$pictures_users_sent', picture_ids='$picture_ids', original_picture_ids='$original_picture_ids', picture_descriptions='$picture_descriptions', picture_types='$picture_types', picture_timestamps='$picture_timestamps' WHERE num=1");
                                                                            if($query)
                                                                            {
                                                                                //deletes pictures
                                                                                for($x = 0; $x < sizeof($deleted_picture_ids); $x++)
                                                                                    $s3->deleteObject("bucket_name", "public/photos/$deleted_picture_ids[$x].$deleted_picture_types[$x]");
                                                                            }
                                                                        }


                                                                        //logs user out
                                                                        session_unset();
                                                                        session_destroy();
                                                                        session_write_close();
                                                                        setcookie(session_name(),'',0,'/');
                                                                        session_regenerate_id(true);

                                                                        //deletes cookie
                                                                        if(isset($_COOKIE['acc_id']))
                                                                            setcookie('acc_id', '0', (time()-(1)), null, null, false, true);

                                                                        echo "Account terminated";
                                                                    }
                                                                }
                                                                else
                                                                {
                                                                    echo "Something went wrong. We are working on fixing it";
                                                                    log_error("terminate_account.php: (13): ", mysql_error());
                                                                }
                                                            }
                                                            else
                                                            {
                                                                echo "Something went wrong. We are working on fixing it";
                                                                log_error("terminate_account.php: (12): ", mysql_error());
                                                            }
                                                        }
                                                        else
                                                        {
                                                            echo "Something went wrong. We are working on fixing it";
                                                            log_error("terminate_account.php: (11): ", mysql_error());
                                                        }
                                                    }
                                                    else
                                                    {
                                                        echo "Something went wrong. We are working on fixing it";
                                                        log_error("terminate_account.php: (10): ", mysql_error());
                                                    }
                                                }
                                                else
                                                {
                                                    echo "Something went wrong. We are working on fixing it";
                                                    log_error("terminate_account.php: (9): ", mysql_error());
                                                }
                                            }
                                            else
                                            {
                                                echo "Something went wrong. We are working on fixing it";
                                                log_error("terminate_account.php: (8): ", mysql_error());
                                            }
                                        }
                                        else
                                        {
                                            echo "Something went wrong. We are working on fixing it";
                                            log_error("terminate_account.php: (7): ", mysql_error());
                                        }
                                    }
                                    else
                                    {
                                        echo "Something went wrong. We are working on fixing it";
                                        log_error("terminate_account.php: (6): ", mysql_error());
                                    }
                                }
                                else
                                {
                                    echo "Something went wrong. We are working on fixing it";
                                    log_error("terminate_account.php: (5): ", mysql_error());
                                }
                            }
                            else
                            {
                                echo "Something went wrong. We are working on fixing it";
                                log_error("terminate_account.php: (4): ", mysql_error());
                            }
                        }
                        else
                        {
                            echo "Something went wrong. We are working on fixing it";
                            log_error("terminate_account.php: (3): ", mysql_error());
                        }
                    }
                    else
                    {
                        echo "Something went wrong. We are working on fixing it";
                        log_error("terminate_account.php: (2): ", mysql_error());
                    }
                }
                else
                {
                    echo "Something went wrong. We are working on fixing it";
                    log_error("terminate_account.php: (1): ", mysql_error());
                }
            }
            else
                echo "Invalid ID";
        }
    }
}