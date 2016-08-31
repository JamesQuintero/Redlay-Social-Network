<?php
@include('init.php');
include('universal_functions.php');
$allowed="users";
include('security_checks.php');

$friends=get_all_friends($_SESSION['id']);

$friends_friends=array();
$all_friends=array();
$common_friends=array();
$total_friends=array();
$num=0;

for($x = 0; $x < sizeof($friends); $x++)
{
    $temp_friends=get_all_friends($friends[$x]);
    for($y = 0; $y < sizeof($temp_friends); $y++)
    {
        $bool=false;
        for($z = 0; $z < sizeof($all_friends); $z++)
        {
            if($all_friends[$z]==$temp_friends[$y])
                $bool=true;
        }
        if($bool==false&&user_is_friends($_SESSION['id'], $temp_friends[$y])=="false")
            $all_friends[]=$temp_friends[$y];
    }
}
$count=array();
for($x = 0; $x < sizeof($all_friends); $x++)
{
    $temp=get_all_friends($all_friends[$x]);
    $temp_num=0;
    for($y = 0; $y < sizeof($temp); $y++)
    {
        for($z = 0; $z < sizeof($friends); $z++)
        {
            if($friends[$z]==$temp[$y]&&$temp[$y]!=$_SESSION['id'])
                 $temp_num++;
        }
    }
    $count[$x]=$temp_num;
}

$number=rand(0, sizeof($all_friends)-1);
$number2=rand(0, sizeof($all_friends)-1);

$JSON=array();
$JSON['friend']=$all_friends[$number];
$JSON['friend2']=$all_friends[$number2];
$JSON['friend_name']=get_user_name($all_Friends[$number]);
$JSON['friend_anme2']=get_user_name($all_Friends[$number2]);
$JSON['num_common_friends']=$count[$number];
$JSON['num_common_friends2']=$count[$number2];
echo json_encode($JSON);