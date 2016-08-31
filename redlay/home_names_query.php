<?php
@include('init.php');
include('universal_functions.php');
$allowed="users";
include('security_checks.php');

/////////redo///////////


$adds=get_all_friends($_SESSION['id']);
$adds[]=$_SESSION['id'];

$names=array();
$profile_pictures=array();
for($x = 0; $x < sizeof($adds); $x++)
{
    $names[]=get_user_name($adds[$x]);
    
    //gets profile pictures
    $profile_pictures[]=get_profile_picture($adds[$x]);
}


//sorts add's names
$temp_names=$names;
sort($names, SORT_STRING);

//puts add ids in order by add's names
$temp_adds=array();
$temp_profile_pictures=array();
$index=0;
for($x =0; $x < sizeof($names); $x++)
{
    for($y =0; $y < sizeof($temp_names); $y++)
    {
        if($temp_names[$y]==$names[$x])
            $index=$y;
    }
    
    $temp_adds[$x]=$adds[$index];
    $temp_profile_pictures[$x]=$profile_pictures[$index];
}
$adds=$temp_adds;
$profile_pictures=$temp_profile_pictures;




//adds user_id of -1
//adds user_name of "All Adds"
$temp_adds=array();
$temp_names=array();
$temp_profile_pictures=array();

$temp_adds[0]=0;
$temp_names[0]="All ".get_friend_title($_SESSION['id']);
$temp_profile_pictures[0]="";

for($x =0; $x < sizeof($names); $x++)
{
    $temp_names[]=$names[$x];
    $temp_adds[]=$adds[$x];
    $temp_profile_pictures[]=$profile_pictures[$x];
}
$adds=$temp_adds;
$names=$temp_names;
$profile_pictures=$temp_profile_pictures;


$JSON=array();

if(isset($temp_adds[0]))
{
    $JSON['adds']=$adds;
    $JSON['names']=$names;
    $JSON['profile_pictures']=$profile_pictures;
}
else
{
    $JSON['adds']=array();
    $JSON['names']=array();
    $JSON['profile_pictures']=$profile_pictures;
}
echo json_encode($JSON);
exit();