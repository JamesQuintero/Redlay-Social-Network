<?php
@include('init.php');
include('universal_functions.php');
$allowed="users";
include('security_checks.php');


$num=(int)($_POST['num']);
$timezone=(int)($_POST['timezone']);

include("requiredS3.php");

$file_names=get_file_names($_SESSION['id']);
$profile_file_names=$file_names[0];
$photo_file_names=$file_names[1];
$post_file_names=$file_names[2];
$other_file_names=$file_names[3];

//gets profile stats
if($num==1)
{
    //gets file stuff
    $path="users/$_SESSION[id]/files/profiles/$profile_file_names[0].txt";
    $value=md5(uniqid(rand()));
    $tmp_path="/var/www/tmp_files/$value.txt";
    $s3->getObject('bucket_name', $path, $tmp_path);
    $profile_views=file_get_contents($tmp_path);
    unlink($tmp_path);
    $profile_views=explode("\n", $profile_views);
    
    //puts stuff into arrays
    $profile_views_numbers=array();
    $profile_views_last_views=array();
    $profile_views_total_views=0;
    for($x = 0; $x < sizeof($profile_views); $x++)
    {
        $profile_views[$x]=explode(' | ', $profile_views[$x]);
        
        $profile_views_numbers[$x]=$profile_views[$x][1];
        $profile_views_total_views+=$profile_views[$x][1];
        $profile_views_last_views[$x]=get_time_since($profile_views[$x][2], $timezone);
    }
    
    //sorts by most viewed
    $profile_views_numbers2=$profile_views_numbers;
    sort($profile_views_numbers, SORT_NUMERIC);
    
    //creates temporary data of arrays for future use
    $temp_profile_views_last_views=$profile_views_last_views;
    $temp_profile_views=$profile_views;

    //rearranges rest of data according to sorted timestamps compared to previously unsorted timestamps
    for($x = 0; $x < sizeof($profile_views_numbers); $x++)
    {
        $number=-1;
        for($y = 0; $y < sizeof($profile_views_numbers2); $y++)
        {
            if($profile_views_numbers[$x]==$profile_views_numbers2[$y]&&$number==-1)
            {
                $number=$y;
                $profile_views_numbers2[$y]='';
            }
        }
        
        $profile_views_last_views[$x]=$temp_profile_views_last_views[$number];
        $profile_views[$x]=$temp_profile_views[$number];
    }
    
    
    $JSON=array();
    if($profile_views[0]!='')
    {
        $JSON['profile_views_number']=$profile_views_numbers;
        $JSON['profile_views_total_views']=$profile_views_total_views;
        $JSON['profile_views_last_views']=$profile_views_last_views;
    }
    else
    {
        $JSON['profile_views_number']=array();
        $JSON['profile_views_total_views']=0;
        $JSON['profile_views_last_views']=array();
    }
    
    //gets profiles viewed
    //gets file stuff
    $path="users/$_SESSION[id]/files/profiles/$profile_file_names[1].txt";
    $value=md5(uniqid(rand()));
    $tmp_path="/var/www/tmp_files/$value.txt";
    $s3->getObject('bucket_name', $path, $tmp_path);
    $profiles_viewed=file_get_contents($tmp_path);
    unlink($tmp_path);
    $profiles_viewed=explode("\n", $profiles_viewed);
    
    
    //puts stuff into arrays
    $profiles_viewed_profile_pictures=array();
    $profiles_viewed_names=array();
    $profiles_viewed_numbers=array();
    $profiles_viewed_ids=array();
    $profiles_viewed_last_views=array();
    $profiles_viewed_total_views=0;
    for($x = 0; $x < sizeof($profiles_viewed); $x++)
    {
        $profiles_viewed[$x]=explode(' | ', $profiles_viewed[$x]);
        
        $profiles_viewed_ids[$x]=$profiles_viewed[$x][0];
        $profiles_viewed_numbers[$x]=$profiles_viewed[$x][1];
        $profiles_viewed_total_views+=$profiles_viewed[$x][1];
    }
    
    //sorts by most viewed
    $profiles_viewed_numbers2=$profiles_viewed_numbers;
    sort($profiles_viewed_numbers, SORT_NUMERIC);
    
    //creates temporary data of arrays for future use
    $temp_profiles_viewed=$profiles_viewed;
    $temp_profiles_viewed_ids=$profiles_viewed_ids;

    //rearranges rest of data according to sorted timestamps compared to previously unsorted timestamps
    for($x = 0; $x < sizeof($profiles_viewed_numbers); $x++)
    {
        $number=-1;
        for($y = 0; $y < sizeof($profiles_viewed_numbers2); $y++)
        {
            if($profiles_viewed_numbers[$x]==$profiles_viewed_numbers2[$y]&&$number==-1)
            {
                $number=$y;
                $profiles_viewed_numbers2[$y]='';
            }
        }
        
        $profiles_viewed[$x]=$temp_profiles_viewed[$number];
        $profiles_viewed_ids[$x]=$temp_profiles_viewed_ids[$number];
    }
    
    for($x = 0; $x < sizeof($profiles_viewed); $x++)
    {
        $profiles_viewed_profile_pictures[$x]=get_profile_picture($profiles_viewed[$x][0]);
        $profiles_viewed_names[$x]=get_user_name($profiles_viewed[$x][0]);
        $profiles_viewed_last_views[$x]=get_time_since($profiles_viewed[$x][2], $timezone);
    }
    
    
    if($profiles_viewed[0]!='')
    {
        $JSON['profiles_viewed_number']=$profiles_viewed_numbers;
        $JSON['profiles_viewed_profile_pictures']=$profiles_viewed_profile_pictures;
        $JSON['profiles_viewed_names']=$profiles_viewed_names;
        $JSON['profiles_viewed_ids']=$profiles_viewed_ids;
        $JSON['profiles_viewed_total_views']=$profiles_viewed_total_views;
        $JSON['profiles_viewed_last_views']=$profiles_viewed_last_views;
    }
    else
    {
        $JSON['profiles_viewed_number']=array();
        $JSON['profiles_viewed_profile_pictures']=array();
        $JSON['profiles_viewed_names']=array();
        $JSON['profiles_viewed_ids']=array();
        $JSON['profiles_viewed_total_views']=array();
        $JSON['profiles_viewed_last_views']=array();
    }
    
    echo json_encode($JSON);
    exit();
}

//gets photo views
else if($num==2)
{
    //gets file stuff
    $path="users/$_SESSION[id]/files/photos/$photo_file_names[0].txt";
    $value=md5(uniqid(rand()));
    $tmp_path="/var/www/tmp_files/$value.txt";
    $s3->getObject('bucket_name', $path, $tmp_path);
    $photo_views=file_get_contents($tmp_path);
    unlink($tmp_path);
    $photo_views=explode("\n", $photo_views);
    
    
    $photo_views_numbers=array();
    $photo_views_photo_ids=array();
    $photo_views_total_number=0;
    
    for($x = 0; $x < sizeof($photo_views); $x++)
    {
        $photo_views[$x]=explode(' | ', $photo_views[$x]);
        
        $photo_views_numbers[$x]=$photo_views[$x][1];
        $photo_views_total_number+=$photo_views[$x][1];
        $photo_views_photo_ids[$x]=$photo_views[$x][0];
    }
    
    //sorts by most viewed
    $photo_views_numbers2=$photo_views_numbers;
    sort($photo_views_numbers, SORT_NUMERIC);
    
    //creates temporary data of arrays for future use
    $temp_photo_views_photo_ids=$photo_views_photo_ids;
    $temp_photo_views=$photo_views;

    //rearranges rest of data according to sorted timestamps compared to previously unsorted timestamps
    for($x = 0; $x < sizeof($photo_views_numbers); $x++)
    {
        $number=-1;
        for($y = 0; $y < sizeof($photo_views_numbers); $y++)
        {
            if($photo_views_numbers[$x]==$photo_views_numbers2[$y]&&$number==-1)
            {
                $number=$y;
                $photo_views_numbers2[$y]='';
            }
        }
        
        $photo_views_photo_ids[$x]=$temp_photo_views_photo_ids[$number];
        $photo_views[$x]=$temp_photo_views[$number];
    }
    
    
    //gets photo_links
    //gets photo_descriptions
    $photo_views_photo_links=array();
    $photo_views_photo_descriptions=array();
    
    $query=mysql_query("SELECT pictures, picture_descriptions, image_types FROM pictures WHERE user_id=$_SESSION[id] LIMIT 1");
    if($query&&mysql_num_rows($query)==1)
    {
        $array=mysql_fetch_row($query);
        $pictures=explode('|^|*|', $array[0]);
        $picture_descriptions=explode('|^|*|', $array[1]);
        $image_types=explode('|^|*|', $array[2]);

        for($x = 0; $x < sizeof($photo_views); $x++)
        {
            $index=-1;
            for($y = 0; $y < sizeof($image_types); $y++)
            {
                if($pictures[$y]==$photo_views[$x][0])
                    $index=$y;
            }

            if($index!=-1)
            {
                $photo_views_photo_links[$x]="http://u.redlay.com/users/$_SESSION[id]/thumbs/".$photo_views[$x][0].".".$image_types[$index];
                $photo_views_photo_descriptions[$x]=$picture_descriptions[$index];
            }
            else
            {
                $photo_views_photo_links[$x]='';
                $photo_views_photo_descriptions[$x]='';
            }
        }
    }
    
    
    //removes any photos that don't exist
    $temp_photo_views_numbers=array();
    $temp_photo_views_total_number=0;
    $temp_photo_views_photo_ids=array();
    $temp_photo_views_photo_links=array();
    $temp_photo_views_photo_descriptions=array();
    for($x = 0; $x < sizeof($photo_views); $x++)
    {
        if($photo_views_photo_links[$x]!='')
        {
            $temp_photo_views_numbers[]=$photo_views_numbers[$x];
            $temp_photo_views_total_number+=$photo_views_numbers[$x];
            $temp_photo_views_photo_ids[]=$photo_views_photo_ids[$x];
            $temp_photo_views_photo_links[]=$photo_views_photo_links[$x];
            $temp_photo_views_photo_descriptions[]=$photo_views_photo_descriptions[$x];
        }
    }
    $photo_views_numbers=$temp_photo_views_numbers;
    $photo_views_total_number=$temp_photo_views_total_number;
    $photo_views_photo_ids=$temp_photo_views_photo_ids;
    $photo_views_photo_links=$temp_photo_views_photo_links;
    $photo_views_photo_descriptions=$temp_photo_views_photo_descriptions;
    
    
    $JSON=array();
    if($photo_views[0]!='')
    {
        $JSON['photo_views_numbers']=$photo_views_numbers;
        $JSON['photo_views_total_number']=$photo_views_total_number;
        $JSON['photo_views_photo_ids']=$photo_views_photo_ids;
        $JSON['photo_views_photo_links']=$photo_views_photo_links;
        $JSON['photo_views_photo_descriptions']=$photo_views_photo_descriptions;
        $JSON['my_profile_picture']=get_profile_picture($_SESSION['id']);
    }
    else
    {
        $JSON['photo_views_numbers']=array();
        $JSON['photo_views_total_number']=array();
        $JSON['photo_views_photo_ids']=array();
        $JSON['photo_views_photo_links']=array();
        $JSON['photo_views_photo_descriptions']=array();
        $JSON['my_profile_picture']="";
    }
    
    
    //gets photo views
    //gets file stuff
    $path="users/$_SESSION[id]/files/photos/$photo_file_names[4].txt";
    $value=md5(uniqid(rand()));
    $tmp_path="/var/www/tmp_files/$value.txt";
    $s3->getObject('bucket_name', $path, $tmp_path);
    $photos_viewed=file_get_contents($tmp_path);
    unlink($tmp_path);
    $photos_viewed=explode("\n", $photos_viewed);
    
    $photos_viewed_numbers=array();
    $photos_viewed_total_views=0;
    $photos_viewed_photo_ids=array();
    $photos_viewed_ids=array();
    for($x = 0; $x < sizeof($photos_viewed); $x++)
    {
        $photos_viewed[$x]=explode(' | ', $photos_viewed[$x]);
        
        $photos_viewed_numbers[$x]=$photos_viewed[$x][1];
        $photos_viewed_total_views+=$photos_viewed[$x][1];
        $photos_viewed_photo_ids[$x]=$photos_viewed[$x][0];
        $photos_viewed_ids[$x]=$photos_viewed[$x][3];
    }
    
    //sorts by most viewed
    $photos_viewed_numbers2=$photos_viewed_numbers;
    sort($photos_viewed_numbers, SORT_NUMERIC);
    
    //creates temporary data of arrays for future use
    $temp_photos_viewed_photo_ids=$photos_viewed_photo_ids;
    $temp_photos_viewed_ids=$photos_viewed_ids;
    $temp_photos_viewed=$photos_viewed;

    //rearranges rest of data according to sorted timestamps compared to previously unsorted timestamps
    for($x = 0; $x < sizeof($photos_viewed_numbers); $x++)
    {
        $number=-1;
        for($y = 0; $y < sizeof($photos_viewed_numbers); $y++)
        {
            if($photos_viewed_numbers[$x]==$photos_viewed_numbers2[$y]&&$number==-1)
            {
                $number=$y;
                $photos_viewed_numbers2[$y]='';
            }
        }
        
        $photos_viewed_photo_ids[$x]=$temp_photos_viewed_photo_ids[$number];
        $photos_viewed_ids[$x]=$temp_photos_viewed_ids[$number];
        $photos_viewed[$x]=$temp_photos_viewed[$number];
    }
    
    //gets photo_links
    //gets photo_descriptions
    $photos_viewed_photo_links=array();
    $photos_viewed_photo_names=array();
    $photos_viewed_photo_descriptions=array();
    $photos_viewed_profile_pictures=array();
    
    for($x = 0; $x < sizeof($photos_viewed); $x++)
    {
        $query=mysql_query("SELECT pictures, picture_descriptions, image_types FROM pictures WHERE user_id=".$photos_viewed[$x][3]." LIMIT 1");
        if($query&&mysql_num_rows($query)==1)
        {
            $array=mysql_fetch_row($query);
            $pictures=explode('|^|*|', $array[0]);
            $picture_descriptions=explode('|^|*|', $array[1]);
            $image_types=explode('|^|*|', $array[2]);


            $index=-1;
            for($y = 0; $y < sizeof($pictures); $y++)
            {
                if($pictures[$y]==$photos_viewed[$x][0])
                    $index=$y;
            }

            if($index!=-1)
            {
                $photos_viewed_photo_links[$x]="http://u.redlay.com/users/".$photos_viewed[$x][3]."/thumbs/".$photos_viewed[$x][0].".".$image_types[$index];
                $photos_viewed_photo_descriptions[$x]=$picture_descriptions[$index];
                $photos_viewed_profile_pictures[$x]=get_profile_picture($photos_viewed[$x][3]);
                $photos_viewed_photo_names[$x]=get_user_name($photos_viewed[$x][3]);
            }
            else
            {
                $photos_viewed_photo_links[$x]="";
                $photos_viewed_photo_descriptions[$x]="";
                $photos_viewed_profile_pictures[$x]="";
                $photos_viewed_photo_names[$x]="";
            }
        }
    }
    
    //removes any photos that don't exist
    $temp_photos_viewed_numbers=array();
    $temp_photos_viewed_total_views=0;
    $temp_photos_viewed_photo_links=array();
    $temp_photos_viewed_photo_ids=array();
    $temp_photos_viewed_ids=array();
    $temp_photos_viewed_photo_descriptions=array();
    $temp_photos_viewed_profile_pictures=array();
    $temp_photos_viewed_photo_names=array();
    $temp_photos_viewed=array();
    
    for($x = 0; $x < sizeof($photos_viewed); $x++)
    {
        if($photos_viewed_photo_links[$x]!='')
        {
            $temp_photos_viewed_numbers[]=$photos_viewed_numbers[$x];
            $temp_photos_viewed_total_views+=$photos_viewed_numbers[$x];
            $temp_photos_viewed_photo_links[]=$photos_viewed_photo_links[$x];
            $temp_photos_viewed_photo_ids[]=$photos_viewed_photo_ids[$x];
            $temp_photos_viewed_ids[]=$photos_viewed_ids[$x];
            $temp_photos_viewed_photo_descriptions[]=$photos_viewed_photo_descriptions[$x];
            $temp_photos_viewed_profile_pictures[]=$photos_viewed_profile_pictures[$x];
            $temp_photos_viewed_photo_names[]=$photos_viewed_photo_names[$x];
            $temp_photos_viewed[]=$photos_viewed[$x];
        }
    }
    
    $photos_viewed_numbers=$temp_photos_viewed_numbers;
    $photos_viewed_total_views=$temp_photos_viewed_total_views;
    $photos_viewed_photo_links=$temp_photos_viewed_photo_links;
    $photos_viewed_photo_ids=$temp_photos_viewed_photo_ids;
    $photos_viewed_ids=$temp_photos_viewed_ids;
    $photos_viewed_photo_descriptions=$temp_photos_viewed_photo_descriptions;
    $photos_viewed_profile_pictures=$temp_photos_viewed_profile_pictures;
    $photos_viewed_photo_names=$temp_photos_viewed_photo_names;
    $photos_viewed=$temp_photos_viewed;
    
    
    if($photos_viewed[0]!='')
    {
        $JSON['photos_viewed_number']=$photos_viewed_numbers;
        $JSON['photos_viewed_total_views']=$photos_viewed_total_views;
        $JSON['photos_viewed_photo_ids']=$photos_viewed_photo_ids;
        $JSON['photos_viewed_ids']=$photos_viewed_ids;
        $JSON['photos_viewed_photo_links']=$photos_viewed_photo_links;
        $JSON['photos_viewed_photo_names']=$photos_viewed_photo_names;
        $JSON['photos_viewed_photo_descriptions']=$photos_viewed_photo_descriptions;
        $JSON['photos_viewed_profile_pictures']=$photos_viewed_profile_pictures;
    }
    else
    {
        $JSON['photos_viewed_number']=array();
        $JSON['photos_viewed_total_views']=array();
        $JSON['photos_viewed_photo_ids']=array();
        $JSON['photos_viewed_ids']=array();
        $JSON['photos_viewed_photo_links']=array();
        $JSON['photos_viewed_photo_names']=array();
        $JSON['photos_viewed_photo_descriptions']=array();
        $JSON['photos_viewed_profile_pictures']=array();
    }
    
    echo json_encode($JSON);
    exit();
}

//gets photo likes
else if($num==3)
{
    //gets profile views
    //gets file stuff
    $path="users/$_SESSION[id]/files/photos/$photo_file_names[3].txt";
    $value=md5(uniqid(rand()));
    $tmp_path="/var/www/tmp_files/$value.txt";
    $s3->getObject('bucket_name', $path, $tmp_path);
    $photo_likes=file_get_contents($tmp_path);
    unlink($tmp_path);
    $photo_likes=explode("\n", $photo_likes);
    
    $photo_total_likes=0;
    $photo_likes_photo_ids=array();
    $photo_likes_photo_links=array();
    $photo_likes_photo_descriptions=array();
    $photo_likes_user_ids=array();
    $photo_likes_profile_pictures=array();
    $photo_likes_names=array();
    $photo_likes_like_date=array();
    for($x = 0; $x < sizeof($photo_likes); $x++)
    {
        $photo_likes[$x]=explode(' | ', $photo_likes[$x]);
        
        $photo_likes_photo_ids[$x]=$photo_likes[$x][0];
        $photo_likes_user_ids[$x]=$photo_likes[$x][1];
        $photo_likes_names[$x]=get_user_name($photo_likes[$x][1]);
        $photo_likes_like_date[$x]=get_time_since($photo_likes[$x][2], $timezone);
        $photo_total_likes++;
        
        //gets photo_links
        //gets photo_descriptions
        $query=mysql_query("SELECT pictures, picture_descriptions, image_types FROM pictures WHERE user_id=".$photo_likes[$x][1]." LIMIT 1");
        if($query&&mysql_num_rows($query)==1)
        {
            $array=mysql_fetch_row($query);
            $pictures=explode('|^|*|', $array[0]);
            $picture_descriptions=explode('|^|*|', $array[1]);
            $image_types=explode('|^|*|', $array[2]);
            
            $index=-1;
            for($y = 0; $y < sizeof($pictures); $y++)
            {
                if($pictures[$y]==$photo_likes[$x][0])
                    $index=$y;
            }
            
            if($index!=-1)
            {
                $photo_likes_photo_links[$x]="http://u.redlay.com/users/".$photo_likes[$x][1]."/thumbs/".$photo_likes[$x][0].".".$image_types[$index];
                $photo_likes_photo_descriptions[$x]=$picture_descriptions[$index];
                $photo_likes_profile_pictures[$x]=get_profile_picture($photo_likes[$x][1]);
            }
        }
    }
    
    
    if($photo_likes[0][0]!='')
    {
        $JSON['photo_total_likes']=$photo_total_likes;
        $JSON['photo_likes_photo_ids']=$photo_likes_photo_ids;
        $JSON['photo_likes_photo_links']=$photo_likes_photo_links;
        $JSON['photo_likes_photo_descriptions']=$photo_likes_photo_descriptions;
        $JSON['photo_likes_user_ids']=$photo_likes_user_ids;
        $JSON['photo_likes_profile_pictures']=$photo_likes_profile_pictures;
        $JSON['photo_likes_names']=$photo_likes_names;
        $JSON['photo_likes_like_date']=$photo_likes_like_date;
        
    }
    else
    {
        $JSON['photo_total_likes']=0;
        $JSON['photo_likes_photo_ids']=array();
        $JSON['photo_likes_photo_links']=array();
        $JSON['photo_likes_photo_descriptions']=array();
        $JSON['photo_likes_user_ids']=array();
        $JSON['photo_likes_profile_pictures']=array();
        $JSON['photo_likes_names']=array();
        $JSON['photo_likes_like_date']=array();
    }
    
    echo json_encode($JSON);
    exit();
}

//gets photo dislikes
else if($num==4)
{
    //gets profile views
    //gets file stuff
    $path="users/$_SESSION[id]/files/photos/$photo_file_names[2].txt";
    $value=md5(uniqid(rand()));
    $tmp_path="/var/www/tmp_files/$value.txt";
    $s3->getObject('bucket_name', $path, $tmp_path);
    $photo_dislikes=file_get_contents($tmp_path);
    unlink($tmp_path);
    $photo_dislikes=explode("\n", $photo_dislikes);
    
    $photo_total_dislikes=0;
    $photo_dislikes_photo_ids=array();
    $photo_dislikes_photo_links=array();
    $photo_dislikes_photo_descriptions=array();
    $photo_dislikes_user_ids=array();
    $photo_dislikes_profile_pictures=array();
    $photo_dislikes_names=array();
    $photo_dislikes_dislike_date=array();
    
    
    for($x = 0; $x < sizeof($photo_dislikes); $x++)
    {
        $photo_dislikes[$x]=explode(' | ', $photo_dislikes[$x]);
        
        $photo_dislikes_photo_ids[$x]=$photo_dislikes[$x][0];
        $photo_dislikes_user_ids[$x]=$photo_dislikes[$x][1];
        $photo_dislikes_names[$x]=get_user_name($photo_dislikes[$x][1]);
        $photo_dislikes_dislike_date[$x]=get_time_since($photo_dislikes[$x][2], $timezone);
        $photo_total_dislikes++;
        
        //gets photo_links
        //gets photo_descriptions
        $query=mysql_query("SELECT pictures, picture_descriptions, image_types FROM pictures WHERE user_id=".$photo_dislikes[$x][1]." LIMIT 1");
        if($query&&mysql_num_rows($query)==1)
        {
            $array=mysql_fetch_row($query);
            $pictures=explode('|^|*|', $array[0]);
            $picture_descriptions=explode('|^|*|', $array[1]);
            $image_types=explode('|^|*|', $array[2]);
            
            $index=-1;
            for($y = 0; $y < sizeof($pictures); $y++)
            {
                if($pictures[$y]==$photo_dislikes[$x][0])
                    $index=$y;
            }
            
            if($index!=-1)
            {
                $photo_dislikes_photo_links[$x]="http://u.redlay.com/users/".$photo_dislikes[$x][1]."/thumbs/".$photo_dislikes[$x][0].".".$image_types[$index];
                $photo_dislikes_photo_descriptions[$x]=$picture_descriptions[$index];
                $photo_dislikes_profile_pictures[$x]=get_profile_picture($photo_dislikes[$x][1]);
            }
        }
    }
    
    
    if($photo_dislikes[0][0]!='')
    {
        $JSON['photo_total_dislikes']=$photo_total_dislikes;
        $JSON['photo_dislikes_photo_ids']=$photo_dislikes_photo_ids;
        $JSON['photo_dislikes_photo_links']=$photo_dislikes_photo_links;
        $JSON['photo_dislikes_photo_descriptions']=$photo_dislikes_photo_descriptions;
        $JSON['photo_dislikes_user_ids']=$photo_dislikes_user_ids;
        $JSON['photo_dislikes_profile_pictures']=$photo_dislikes_profile_pictures;
        $JSON['photo_dislikes_names']=$photo_dislikes_names;
        $JSON['photo_dislikes_dislike_date']=$photo_dislikes_dislike_date;
        
    }
    else
    {
        $JSON['photo_total_dislikes']=0;
        $JSON['photo_dislikes_photo_ids']=array();
        $JSON['photo_dislikes_photo_links']=array();
        $JSON['photo_dislikes_photo_descriptions']=array();
        $JSON['photo_dislikes_user_ids']=array();
        $JSON['photo_dislikes_profile_pictures']=array();
        $JSON['photo_dislikes_names']=array();
        $JSON['photo_dislikes_dislike_date']=array();
    }
    
    echo json_encode($JSON);
    exit();
}

//gets photo comments
else if($num==5)
{
    //gets photo views
    //gets file stuff
    $path="users/$_SESSION[id]/files/photos/$photo_file_names[1].txt";
    $value=md5(uniqid(rand()));
    $tmp_path="/var/www/tmp_files/$value.txt";
    $s3->getObject('bucket_name', $path, $tmp_path);
    $photo_comments=file_get_contents($tmp_path);
    unlink($tmp_path);
    $photo_comments=explode("\n", $photo_comments);
    
    $photo_comments_number=array();
    $photo_comments_total_comments=0;
    $photo_comments_photo_ids=array();
    $photo_comments_ids=array();
    $photo_comments_photo_links=array();
    $photo_comments_photo_names=array();
    $photo_comments_photo_descriptions=array();
    $photo_comments_profile_pictures=array();
    for($x = 0; $x < sizeof($photo_comments); $x++)
    {
        $photo_comments[$x]=explode(' | ', $photo_comments[$x]);
        
        $photo_comments_number[$x]=$photo_comments[$x][1];
        $photo_comments_total_comments+=$photo_comments[$x][1];
        $photo_comments_photo_ids[$x]=$photo_comments[$x][0];
        $photo_comments_ids[$x]=$photo_comments[$x][2];
        $photo_comments_profile_pictures[$x]=get_profile_picture($photo_comments[$x][2]);
        $photo_comments_photo_names[$x]=get_user_name($photo_comments[$x][2]);
        
        //gets photo_links
        //gets photo_descriptions
        $query=mysql_query("SELECT pictures, picture_descriptions, image_types FROM pictures WHERE user_id=".$photo_comments[$x][2]." LIMIT 1");
        if($query&&mysql_num_rows($query)==1)
        {
            $array=mysql_fetch_row($query);
            $pictures=explode('|^|*|', $array[0]);
            $picture_descriptions=explode('|^|*|', $array[1]);
            $image_types=explode('|^|*|', $array[2]);
            
            $index=-1;
            for($y = 0; $y < sizeof($pictures); $y++)
            {
                if($pictures[$y]==$photo_comments[$x][0])
                    $index=$y;
            }
            
            if($index!=-1)
            {
                $photo_comments_photo_links[$x]="http://u.redlay.com/users/".$photo_comments[$x][2]."/thumbs/".$photo_comments[$x][0].".".$image_types[$index];
                $photo_comments_photo_descriptions[$x]=$picture_descriptions[$index];
            }
        }
    }
    
    
    if($photo_comments[0]!='')
    {
        $JSON['photo_comments_number']=$photo_comments_number;
        $JSON['photo_comments_total_comments']=$photo_comments_total_comments;
        $JSON['photo_comments_photo_ids']=$photo_comments_photo_ids;
        $JSON['photo_comments_ids']=$photo_comments_ids;
        $JSON['photo_comments_photo_links']=$photo_comments_photo_links;
        $JSON['photo_comments_photo_names']=$photo_comments_photo_names;
        $JSON['photo_comments_photo_descriptions']=$photo_comments_photo_descriptions;
        $JSON['photo_comments_profile_pictures']=$photo_comments_profile_pictures;
    }
    else
    {
        $JSON['photo_comments_number']=array();
        $JSON['photo_comments_total_views']=array();
        $JSON['photo_comments_photo_ids']=array();
        $JSON['photo_comments_ids']=array();
        $JSON['photo_comments_photo_links']=array();
        $JSON['photo_comments_photo_names']=array();
        $JSON['photo_comments_photo_descriptions']=array();
        $JSON['photo_comments_profile_pictures']=array();
    }
    
    echo json_encode($JSON);
    exit();
}

//gets post likes
else if($num==6)
{
    //gets profile views
    //gets file stuff
    $path="users/$_SESSION[id]/files/posts/$post_file_names[2].txt";
    $value=md5(uniqid(rand()));
    $tmp_path="/var/www/tmp_files/$value.txt";
    $s3->getObject('bucket_name', $path, $tmp_path);
    $post_likes=file_get_contents($tmp_path);
    unlink($tmp_path);
    $post_likes=explode("\n", $post_likes);
    
    

    $post_total_likes=0;
    $post_likes_post_ids=array();
    $post_likes_body=array();
    $post_likes_user_ids=array();
    $post_likes_profile_pictures=array();
    $post_likes_names=array();
    $post_likes_like_date=array();
    for($x = 0; $x < sizeof($post_likes); $x++)
    {
        $post_likes[$x]=explode(' | ', $post_likes[$x]);
        
        $post_likes_post_ids[$x]=$post_likes[$x][0];
        $post_likes_user_ids[$x]=$post_likes[$x][1];
        $post_likes_names[$x]=get_user_name($post_likes[$x][1]);
        $post_likes_like_date[$x]=get_time_since($post_likes[$x][2], $timezone);
        $post_total_likes++;
        
        //gets photo_links
        //gets photo_descriptions
        $query=mysql_query("SELECT post_ids, posts FROM content WHERE user_id=".$post_likes[$x][1]." LIMIT 1");
        if($query&&mysql_num_rows($query)==1)
        {
            $array=mysql_fetch_row($query);
            $post_ids=explode('|^|*|', $array[0]);
            $posts=explode('|^|*|', $array[1]);
            
            $index=-1;
            for($y = 0; $y < sizeof($post_ids); $y++)
            {
                if($post_ids[$y]==$post_likes[$x][0])
                    $index=$y;
            }
            
            if($index!=-1)
            {
                $post_likes_body[$x]=$posts[$index];
                $post_likes_profile_pictures[$x]=get_profile_picture($post_likes[$x][1]);
            }
        }
    }
    
    
    $JSON=array();
    if($post_likes[0][0]!='')
    {
        $JSON['post_total_likes']=$post_total_likes;
        $JSON['post_likes_post_ids']=$post_likes_post_ids;
        $JSON['post_likes_body']=$post_likes_body;
        $JSON['post_likes_user_ids']=$post_likes_user_ids;
        $JSON['post_likes_profile_pictures']=$post_likes_profile_pictures;
        $JSON['post_likes_names']=$post_likes_names;
        $JSON['post_likes_like_date']=$post_likes_like_date;
    }
    else
    {
        $JSON['post_total_likes']=0;
        $JSON['post_likes_post_ids']=array();
        $JSON['post_likes_body']=array();
        $JSON['post_likes_user_ids']=array();
        $JSON['post_likes_profile_pictures']=array();
        $JSON['post_likes_names']=array();
        $JSON['post_likes_like_date']=array();
    }
    
    echo json_encode($JSON);
    exit();
}

//gets post dislikes
else if($num==7)
{
    //gets profile views
    //gets file stuff
    $path="users/$_SESSION[id]/files/posts/$post_file_names[1].txt";
    $value=md5(uniqid(rand()));
    $tmp_path="/var/www/tmp_files/$value.txt";
    $s3->getObject('bucket_name', $path, $tmp_path);
    $post_dislikes=file_get_contents($tmp_path);
    unlink($tmp_path);
    $post_dislikes=explode("\n", $post_dislikes);
    
    

    $post_total_dislikes=0;
    $post_dislikes_post_ids=array();
    $post_dislikes_body=array();
    $post_dislikes_user_ids=array();
    $post_dislikes_profile_pictures=array();
    $post_dislikes_names=array();
    $post_dislikes_dislike_date=array();
    for($x = 0; $x < sizeof($post_dislikes); $x++)
    {
        $post_dislikes[$x]=explode(' | ', $post_dislikes[$x]);
        
        $post_dislikes_post_ids[$x]=$post_dislikes[$x][0];
        $post_dislikes_user_ids[$x]=$post_dislikes[$x][1];
        $post_dislikes_names[$x]=get_user_name($post_dislikes[$x][1]);
        $post_dislikes_dislike_date[$x]=get_time_since($post_dislikes[$x][2], $timezone);
        $post_total_dislikes++;
        
        //gets photo_links
        //gets photo_descriptions
        $query=mysql_query("SELECT post_ids, posts FROM content WHERE user_id=".$post_dislikes[$x][1]." LIMIT 1");
        if($query&&mysql_num_rows($query)==1)
        {
            $array=mysql_fetch_row($query);
            $post_ids=explode('|^|*|', $array[0]);
            $posts=explode('|^|*|', $array[1]);
            
            $index=-1;
            for($y = 0; $y < sizeof($post_ids); $y++)
            {
                if($post_ids[$y]==$post_dislikes[$x][0])
                    $index=$y;
            }
            
            if($index!=-1)
            {
                $post_dislikes_body[$x]=$posts[$index];
                $post_dislikes_profile_pictures[$x]=get_profile_picture($post_dislikes[$x][1]);
            }
        }
    }
    
    
    $JSON=array();
    if($post_dislikes[0][0]!='')
    {
        $JSON['post_total_dislikes']=$post_total_dislikes;
        $JSON['post_dislikes_post_ids']=$post_dislikes_post_ids;
        $JSON['post_dislikes_body']=$post_dislikes_body;
        $JSON['post_dislikes_user_ids']=$post_dislikes_user_ids;
        $JSON['post_dislikes_profile_pictures']=$post_dislikes_profile_pictures;
        $JSON['post_dislikes_names']=$post_dislikes_names;
        $JSON['post_dislikes_dislike_date']=$post_dislikes_dislike_date;
    }
    else
    {
        $JSON['post_total_dislikes']=0;
        $JSON['post_dislikes_post_ids']=array();
        $JSON['post_dislikes_body']=array();
        $JSON['post_dislikes_user_ids']=array();
        $JSON['post_dislikes_profile_pictures']=array();
        $JSON['post_dislikes_names']=array();
        $JSON['post_dislikes_dislike_date']=array();
    }
    
    echo json_encode($JSON);
    exit();
}

//gets post comments
else if($num==8)
{
    //gets photo views
    //gets file stuff
    $path="users/$_SESSION[id]/files/posts/$post_file_names[0].txt";
    $value=md5(uniqid(rand()));
    $tmp_path="/var/www/tmp_files/$value.txt";
    $s3->getObject('bucket_name', $path, $tmp_path);
    $post_comments=file_get_contents($tmp_path);
    unlink($tmp_path);
    $post_comments=explode("\n", $post_comments);
    
    $post_comments_number=array();
    $post_comments_total_comments=0;
    $post_comments_post_ids=array();
    $post_comments_ids=array();
    $post_comments_post_names=array();
    $post_comments_body=array();
    $post_comments_profile_pictures=array();
    for($x = 0; $x < sizeof($post_comments); $x++)
    {
        $post_comments[$x]=explode(' | ', $post_comments[$x]);
        
        $post_comments_number[$x]=$post_comments[$x][1];
        $post_comments_total_comments+=$post_comments[$x][1];
        $post_comments_post_ids[$x]=$post_comments[$x][0];
        $post_comments_ids[$x]=$post_comments[$x][2];
        $post_comments_profile_pictures[$x]=get_profile_picture($post_comments[$x][2]);
        $post_comments_post_names[$x]=get_user_name($post_comments[$x][2]);
        
        //gets photo_links
        //gets photo_descriptions
        $query=mysql_query("SELECT post_ids, posts FROM content WHERE user_id=".$post_comments[$x][2]." LIMIT 1");
        if($query&&mysql_num_rows($query)==1)
        {
            $array=mysql_fetch_row($query);
            $post_ids=explode('|^|*|', $array[0]);
            $posts=explode('|^|*|', $array[1]);
            
            $index=-1;
            for($y = 0; $y < sizeof($post_ids); $y++)
            {
                if($post_ids[$y]==$post_comments[$x][0])
                    $index=$y;
            }
            
            if($index!=-1)
                $post_comments_body[$x]=$posts[$index];
        }
    }
    
    
    if($post_comments[0]!='')
    {
        $JSON['post_comments_number']=$post_comments_number;
        $JSON['post_comments_total_comments']=$post_comments_total_comments;
        $JSON['post_comments_post_ids']=$post_comments_post_ids;
        $JSON['post_comments_ids']=$post_comments_ids;
        $JSON['post_comments_post_names']=$post_comments_post_names;
        $JSON['post_comments_body']=$post_comments_body;
        $JSON['post_comments_profile_pictures']=$post_comments_profile_pictures;
    }
    else
    {
        $JSON['post_comments_number']=array();
        $JSON['post_comments_total_comments']=array();
        $JSON['post_comments_post_ids']=array();
        $JSON['post_comments_ids']=array();
        $JSON['post_comments_post_names']=array();
        $JSON['post_comments_body']=array();
        $JSON['post_comments_profile_pictures']=array();
    }
    
    echo json_encode($JSON);
    exit();
}

//gets logins
else if($num==9)
{
    //gets photo views
    //gets file stuff
    $path="users/$_SESSION[id]/files/other/$other_file_names[0].txt";
    $value=md5(uniqid(rand()));
    $tmp_path="/var/www/tmp_files/$value.txt";
    $s3->getObject('bucket_name', $path, $tmp_path);
    $logins=file_get_contents($tmp_path);
    unlink($tmp_path);
    $logins=explode("\n", $logins);
    
    $dates=array();
    $ip_addresses=array();
    for($x = 0; $x < sizeof($logins); $x++)
    {
        $logins[$x]=explode(' | ', $logins[$x]);
        
        $dates[$x]=get_time_since($logins[$x][0], $timezone);
        $ip_addresses[$x]=$logins[$x][1];
    }
    
    $JSON=array();
    if($logins[0]!='')
    {
        $JSON['dates']=$dates;
        $JSON['ip_addresses']=$ip_addresses;
    }
    else
    {
        $JSON['dates']=array();
        $JSON['ip_addresses']=array();
    }
    
    echo json_encode($JSON);
    exit();
}

//gets logout
else if($num==10)
{
    //gets photo views
    //gets file stuff
    $path="users/$_SESSION[id]/files/other/$other_file_names[1].txt";
    $value=md5(uniqid(rand()));
    $tmp_path="/var/www/tmp_files/$value.txt";
    $s3->getObject('bucket_name', $path, $tmp_path);
    $logouts=file_get_contents($tmp_path);
    unlink($tmp_path);
    $logouts=explode("\n", $logouts);
    
    $dates=array();
    $ip_addresses=array();
    for($x = 0; $x < sizeof($logouts); $x++)
    {
        $logouts[$x]=explode(' | ', $logouts[$x]);
        
        $dates[$x]=get_time_since($logouts[$x][0], $timezone);
        $ip_addresses[$x]=$logouts[$x][1];
    }
    
    $JSON=array();
    if($logouts[0]!='')
    {
        $JSON['dates']=$dates;
        $JSON['ip_addresses']=$ip_addresses;
    }
    else
    {
        $JSON['dates']=array();
        $JSON['ip_addresses']=array();
    }
    
    echo json_encode($JSON);
    exit();
}