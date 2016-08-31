<?php
@include('init.php');
include('universal_functions.php');
$allowed="both";
include('security_checks.php');


$num=(int)($_POST['num']);
$ID=(int)($_POST['user_id']);

//checks to see if ID is an actual ID and it exists
if(is_id($ID)&&user_id_exists($ID)&&!user_id_terminated($ID))
{
   $privacy=get_user_privacy_settings($ID);
   if($privacy[1][2]=='yes'||$ID==$_SESSION['id']||user_is_friends($ID, $_SESSION['id']))
   {
      //gets all adds
      if($num==1)
      {
         $timezone=(int)($_POST['timezone']);
         $query=mysql_query("SELECT user_friends, friend_timestamps FROM user_data WHERE user_id=$ID LIMIT 1");
         if($query&&mysql_num_rows($query)==1)
         {
            $array=mysql_fetch_row($query);
            $adds=explode('|^|*|', $array[0]);
            $add_timestamps=explode('|^|*|', $array[1]);

            $add_names=array();
            $add_profile_pictures=array();
            $add_num_adds=array();
            $add_dates=array();
            $badges=array();
            if($array[0]!='')
               $num_adds=sizeof($adds);
            else
               $num_adds=0;

            for($x = 0; $x < sizeof($adds); $x++)
            {
               $add_names[]=get_user_name($adds[$x]);
               $add_profile_pictures[]=get_profile_picture($adds[$x]);
               $add_num_adds[]=get_num_adds($adds[$x]);
               $add_dates[]=get_time_since($add_timestamps[$x], $timezone);
               $badges[]=get_badges($adds[$x]);
            }

            $JSON=array();
            $JSON['adds']=$adds;
            $JSON['add_names']=$add_names;
            $JSON['add_profile_pictures']=$add_profile_pictures;
            $JSON['add_num_adds']=$add_num_adds;
            $JSON['num_adds']=$num_adds;
            $JSON['add_dates']=$add_dates;
            $JSON['badges']=$badges;
            $JSON['add_title']=get_add_title($ID);
            echo json_encode($JSON);
            exit();


         }
         else
         {
            echo "Something went wrong. We are working on fixing it";
            log_error("all_adds_query.php: (num:1)", mysql_error());
         }
      }
      // else if($num==2)
      // {

      // }
   }
}