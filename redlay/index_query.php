<?php
@include('init.php');
include('universal_functions.php');
if(isset($_SESSION['id'])||isset($_SESSION['page_id']))
{
    header("Location: http://www.redlay.com");
    exit();
}

$num=(int)($_POST['num']);

//gets public users
if($num==1)
{
    $timezone=(int)($_POST['timezone']);
//    $timezone=8;
    
    $query=mysql_query("SELECT user_ids, user_names, user_timestamps FROM public WHERE num=1");
    if($query&&mysql_num_rows($query)==1)
    {
        $array=mysql_fetch_row($query);
        $user_ids=explode('|^|*|', $array[0]);
        $user_names=explode('|^|*|', $array[1]);
        $user_timestamps=explode('|^|*|', $array[2]);
        
        //gets rid of terminated accounts
        $temp_user_ids=array();
        $temp_user_names=array();
        $temp_user_timestamps=array();
        for($x = 0; $x < sizeof($user_ids); $x++)
        {
            if(!user_id_terminated($user_ids[$x]))
            {
                $temp_user_ids[]=$user_ids[$x];
                $temp_user_names[]=$user_names[$x];
                $temp_user_timestamps[]=$user_timestamps[$x];
            }
        }
        $user_ids=$temp_user_ids;
        $user_names=$temp_user_names;
        $user_timestamps=$temp_user_timestamps;
        
        if($array[0]!='')
            $total_size=sizeof($user_ids);
        else
            $total_size=0;


        if($total_size<10)
        {
            //reverses because it adds backwards in the else statement below
            $temp_user_ids=array();
            $temp_user_names=array();
            $temp_user_timestamps=array();

            for($x = sizeof($user_ids)-1; $x >=0; $x--)
            {
                $temp_user_ids[]=$user_ids[$x];
                $temp_user_names[]=$user_names[$x];
                $temp_user_timestamps[]=$user_timestamps[$x];
            }

            $user_ids=$temp_user_ids;
            $user_names=$temp_user_names;
            $user_timestamps=$temp_user_timestamps;

        }
        else
        {
            $temp_user_ids=array();
            $temp_user_names=array();
            $temp_user_timestamps=array();

            $index=sizeof($user_ids)-1;

            while(sizeof($temp_user_ids)<=10)
            {
                if($user_ids[$index]!='')
                {
                    $temp_user_ids[]=$user_ids[$index];
                    $temp_user_names[]=$user_names[$index];
                    $temp_user_timestamps[]=$user_timestamps[$index];
                }
                else
                {
                    $temp_user_ids[]='';
                    $temp_user_names[]='';
                    $temp_user_timestamps[]='';
                }

                $index--;
            }

            $user_ids=$temp_user_ids;
            $user_names=$temp_user_names;
            $user_timestamps=$temp_user_timestamps;
        }
        
        
        $profile_pictures=array();
        $adjusted_timestamps=array();
        $timestamp_seconds=array();
        for($x = 0; $x < sizeof($user_ids); $x++)
        {
            //gets profile pictures
            $profile_pictures[$x]=get_profile_picture($user_ids[$x]);
            
            //gets adjusted timestamps
            $adjusted_timestamps[$x]=get_time_since($user_timestamps[$x], $timezone);
            $timestamp_seconds[$x]=get_time_since_seconds($user_timestamps[$x], $timezone);
        }
        
        $JSON=array();
        $JSON['users']=$user_ids;
        $JSON['user_names']=$user_names;
        $JSON['timestamps']=$adjusted_timestamps;
        $JSON['timestamp_seconds']=$timestamp_seconds;
        $JSON['profile_pictures']=$profile_pictures;
        echo json_encode($JSON);
        exit();
        
    }
}

//gets public photos
else if($num==2)
{
    $query=mysql_query("SELECT pictures_users_sent, picture_ids, original_picture_ids, picture_descriptions, picture_types, picture_timestamps WHERE num=1");
    if($query&&mysql_num_rows($query)==1)
    {
        $array=mysql_fetch_row($query);
        $pictures_users_sent=explode('|^|*|', $array[0]);
        $picture_ids=explode('|^|*|', $array[1]);
        $original_picture_ids=explode('|^|*|', $array[2]);
        $picture_descriptions=explode('|^|*|', $array[3]);
        $picture_types=explode('|^|*|', $array[4]);
        $picture_timestamps=explode('|^|*|', $array[5]);
        
        //gets rid of terminated accounts
        $temp_pictures_users_sent=array();
        $temp_picture_ids=array();
        $temp_original_picture_ids=array();
        $temp_picture_descriptions=array();
        $temp_picture_types=array();
        $temp_picture_timestamps=array();
        
        for($x = 0; $x < sizeof($user_ids); $x++)
        {
            if(!user_id_terminated($user_ids[$x]))
            {
                $temp_pictures_users_sent[]=$pictures_users_sent[$x];
                $temp_picture_ids[]=$picture_ids[$x];
                $temp_original_picture_ids[]=$original_picture_ids[$x];
                $temp_picture_descriptions[]=$picture_descriptions[$x];
                $temp_picture_types[]=$picture_types[$x];
                $temp_picture_timestamps[]=$picture_timestamps[$x];
            }
        }
        
        $pictures_users_sent=$temp_pictures_users_sent;
        $picture_ids=$temp_picture_ids;
        $original_picture_ids=$temp_original_picture_ids;
        $picture_descriptions=$temp_picture_descriptions;
        $picture_types=$temp_picture_types;
        $picture_timestamps=$temp_picture_timestamps;
        
        
        
        if($array[0]!='')
            $total_size=sizeof($pictures_users_sent);
        else
            $total_size=0;


        if($total_size<10)
        {
            //reverses because it adds backwards in the else statement below
            $temp_pictures_users_sent=array();
            $temp_picture_ids=array();
            $temp_original_picture_ids=array();
            $temp_picture_descriptions=array();
            $temp_picture_types=array();
            $temp_picture_timestamps=array();

            for($x = sizeof($pictures_users_sent)-1; $x >=0; $x--)
            {
                $temp_pictures_users_sent[]=$pictures_users_sent[$x];
                $temp_picture_ids[]=$picture_ids[$x];
                $temp_original_picture_ids[]=$original_picture_ids[$x];
                $temp_picture_descriptions[]=$picture_descriptions[$x];
                $temp_picture_types[]=$picture_types[$x];
                $temp_picture_timestamps[]=$picture_timestamps[$x];
            }

            $pictures_users_sent=$temp_pictures_users_sent;
            $picture_ids=$temp_picture_ids;
            $original_picture_ids=$temp_original_picture_ids;
            $picture_descriptions=$temp_picture_descriptions;
            $picture_types=$temp_picture_types;
            $picture_timestamps=$temp_picture_timestamps;

        }
        else
        {
            $temp_pictures_users_sent=array();
            $temp_picture_ids=array();
            $temp_original_picture_ids=array();
            $temp_picture_descriptions=array();
            $temp_picture_types=array();
            $temp_picture_timestamps=array();

            $index=sizeof($pictures_users_sent)-1;

            while(sizeof($temp_pictures_users_sent)<=10)
            {
                if($picture_ids[$index]!='')
                {
                    $temp_user_ids[]=$user_ids[$index];
                    
                    $temp_pictures_users_sent[]=$pictures_users_sent[$index];
                    $temp_picture_ids[]=$picture_ids[$index];
                    $temp_original_picture_ids[]=$original_picture_ids[$index];
                    $temp_picture_descriptions[]=$picture_descriptions[$index];
                    $temp_picture_types[]=$picture_types[$index];
                    $temp_picture_timestamps[]=$picture_timestamps[$index];
                }
                else
                {
                    $temp_pictures_users_sent[]='';
                    $temp_picture_ids[]='';
                    $temp_original_picture_ids[]='';
                    $temp_picture_descriptions[]='';
                    $temp_picture_types[]='';
                    $temp_picture_timestamps[]='';
                }

                $index--;
            }

            $pictures_users_sent=$temp_pictures_users_sent;
            $picture_ids=$temp_picture_ids;
            $original_picture_ids=$temp_original_picture_ids;
            $picture_descriptions=$temp_picture_descriptions;
            $picture_types=$temp_picture_types;
            $picture_timestamps=$temp_picture_timestamps;
        }
        
        
        $profile_pictures=array();
        $adjusted_timestamps=array();
        $timestamp_seconds=array();
        for($x = 0; $x < sizeof($user_ids); $x++)
        {
            //gets profile pictures
            $profile_pictures[$x]=get_profile_picture($user_ids[$x]);
            
            //gets adjusted timestamps
            $adjusted_timestamps[$x]=get_time_since($user_timestamps[$x], $timezone);
            $timestamp_seconds[$x]=get_time_since_seconds($user_timestamps[$x], $timezone);
        }
        
        $JSON=array();
        $JSON['pictures_users_sent']=$pictures_users_sent;
        $JSON['picture_ids']=$picture_ids;
        $JSON['original_picture_ids']=$original_picture_ids;
        $JSON['picture_descriptions']=$picture_descriptions;
        $JSON['picture_types']=$picture_types;
        $JSON['picture_timestamps']=$picture_timestamps;
        //maybe some more things go here
        echo json_encode($JSON);
        exit();
    }
}