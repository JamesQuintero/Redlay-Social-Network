<?php
@include('init.php');
include('../universal_functions.php');
$allowed="users";
include("security_checks.php");

$ID=(int)($_POST['user_id']);

if(is_id($ID)&&user_id_exists($ID))
{
    $privacy=get_privacy_settings($ID);
    if($privacy[0]=="yes")
    {
        $query=mysql_query("SELECT user_friends, user_videos, user_relationship, relationship_partner, user_birthday, user_sex, user_bio, high_school, college, country, city, work, user_mood FROM user_data WHERE user_id=$ID LIMIT 1");
        if($query&&mysql_num_rows($query)==1)
        {
            $array=mysql_fetch_row($query);
            $adds=explode('|^|*|', $array[0]);
            $videos=explode('|^|*|', $array[1]);
            $relationship=$array[2];
            $relationship_partner=$array[3];
            $birthday=explode('|^|*|', $array[4]);
            $gender=$array[5];
            $bio=$array[6];
            $high_school=$array[7];
            $college=$array[8];
            $country=$array[9];
            $city=$array[10];
            $work=$array[11];
            $mood=$array[12];
            
            $relationship_array=array();
            $relationship_array[0]=$relationship;
            $relationship_array[1]=$relationship_partner;
            
            $birthday[1]=$birthday[1].",";
            $birthday=implode(' ', $birthday);
            
            
            $JSON=array();
            $JSON['num_adds']=sizeof($adds);
            $JSON['num_videos']=sizeof($videos);
            $JSON['relationship']=$relationship_array;
            $JSON['birthday']=$birthday;
            $JSON['gender']=$gender;
            $JSON['bio']=$bio;
            $JSON['high_school']=$high_school;
            $JSON['college']=$college;
            $JSON['country']=$country;
            $JSON['city']=$city;
            $JSON['work']=$work;
            $JSON['mood']=$mood;
            echo json_encode($JSON);
            exit();
        }
    }
}