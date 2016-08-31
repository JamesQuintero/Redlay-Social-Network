<?php
@include('init.php');
include('universal_functions.php');
$allowed="users";
include("security_checks.php");

$num=(int)($_POST['num']);


//changes password
if($num==1)
{
    $current=clean_string($_POST['current_password']);
    $new=clean_string($_POST['new_password']);
    $confirm_new=clean_string($_POST['confirm_new_password']);

    if(!empty($current) && !empty($new) && !empty($confirm_new))
    {


      //need to get user email

      //blowfish hashes password for database storage
      $password=crypt($password, '$2a$07$27'.$email.'SECRET_SALT_STRING');


      $query=mysql_query("SELECT id FROM users WHERE password='$current' AND id=$_SESSION[id] LIMIT 1");
      if($query&&mysql_num_rows($query)==1)
      {
          if($new==$confirm_new)
         {
            //blowfish hashes password for database storage
            $password=crypt($password, '$2a$07$27'.$email.'SECRET_SALT_STRING');
              
            $query=mysql_query("UPDATE users SET password='$new' WHERE id=$_SESSION[id]");
            if($query)
               echo "Password change successful!";
            else
               echo "Somthing went wrong in our database. Please try again.";
         }
         else
            echo "passwords do not match";
       }
       else
       {
          echo "Something has gone wrong. We are working to fix it";
          log_error("settings_query.php - 1: ",mysql_error());
       }
    }
    else
        echo "One or more fields are empty";
}

//changes gender
else if($num==2)
{
   $sex=clean_string($_POST['sex']);
   if($sex!='blankSex')
   {
      if($sex=='Male'||$sex=='Female'||$sex=='Other')
      {
         $query = mysql_query("UPDATE user_data SET user_sex='$sex' WHERE user_id=$_SESSION[id]");
         if($query)
               echo "Change successful!";
         else
         {
               echo "We are sorry, but something went wrong. Please try again";
               log_error("settings_query.php - 2: ",mysql_error());
         }
      }
      else
         echo "Invalid gender";
   }
   else
      echo "Pick one of the options";
}

//changes birthday
else if($num==3)
{
   $month=clean_string($_POST['month']);
   $day=(int)($_POST['day']);
   $year=(int)($_POST['year']);
   $show_year=clean_string($_POST['show_year']);

   if('blankMonth'!=$month && 'blankDay'!=$day && 'blankYear'!=$year)
   {
      if($month=="January"||$month=="February"||$month=="March"||$month=="April"||$month=="May"
               ||$month=="June"||$month=="July"||$month=="August"||$month=="September"||$month=="October"||
               $month=="November"||$month=="December")
      {
         if($day>=1&&$day<=31)
         {
            if($year>=1900&&$year<=2000)
            {
               $array=array($month, $day, $year);
               $birthday=implode("|^|*|", $array);
               $query=mysql_query("UPDATE user_data SET user_birthday='$birthday' WHERE user_id=$_SESSION[id]");
               if($query)
               {
                  $query2=mysql_query("UPDATE user_display SET birthday_year='$show_year' WHERE user_id=$_SESSION[id]");
                  if($query2)
                        echo "Change successful!";
                  else
                  {
                        echo "An Error has occured. Please try again";
                        log_error("settings_query.php - 3: ",mysql_error());
                  }
               }
               else
               {
                  echo "An Error has occured. Please try again";
                  log_error("settings_query.php - 3: ",mysql_error());
               }
            }
         }
      }
   }
   else
      echo "Please fill in all of the fields";
}

//changes bio
else if($num==4)
{
   $bio=clean_string($_POST['new_bio']);

   
   $query=mysql_query("UPDATE user_data SET user_bio='$bio' WHERE user_id=$_SESSION[id]");
   if($query)
      echo "Change successful!";
   else
   {
      echo "Something went wrong. Please try again. ".$bio;
      log_error("settings_query.php - 4: ",mysql_error());
   }
}

//changes relationship
else if($num==5)
{
   $relationship=clean_string($_POST['relationship']);
   if($relationship!='blankRelationship')
   {
      if($relationship=='Taken'||$relationship=="Single and looking"||$relationship=="Single"||$relationship=="Unsure"||$relationship=="Forever alone"||$relationship=="NA")
      {
         $query=mysql_query("UPDATE user_data SET user_relationship='$relationship', relationship_timestamp='".get_date()."' WHERE user_id=$_SESSION[id]");
         if($query)
               echo "Change successful!";
         else
         {
               echo "Something went wrong. Please try again";
               log_error("settings_query.php - 5: ",mysql_error());
         }
      }
      else
         echo "Invalid relationship status";
   }
   else
      echo "Please select a field";
}

//changes mood
else if($num==6)
{
   $array=array();
   $array[0]="Happy";
   $array[1]="Angry";
   $array[2]="Sad";
   $array[3]="Ambitious";
   $array[4]="Accepted";
   $array[5]="Bored";
   $array[6]="Ashamed";
   $array[7]="Dorky";
   $array[8]="Silly";
   $array[9]="Geeky";
   $array[10]="Naughty";
   $array[11]="Accomplished";
   $array[12]="Tired";
   $array[13]="Stressed";
   $array[14]="Indescribable";
   $array[15]="Annoyed";
   $array[16]="Relaxed";
   $array[17]="Relieved";
   $array[18]="Lazy";
   $array[19]="Calm";
   $array[20]="Forever Alone";
   $array[21]="Sick";
   $array[22]="Hyper";
   $array[23]="Anxious";
   $array[24]="Drunk";
   $array[25]="Disappointed";
   $array[26]="Pathetic";
   $mood=clean_string($_POST['mood']);
   if($mood!='blankMood')
   {
      if(in_array($mood, $array))
      {
         $query=mysql_query("UPDATE user_data SET user_mood='$mood', mood_timestamp='".get_date()."' WHERE user_id=$_SESSION[id]");
         if($query)
               echo "Change successful!";
         else
         {
               echo "Something went wrong. Please try again";
               log_error("settings_query.php - 6: ",mysql_error());
         }
      }
      else
          echo "Invalid mood";
   }
   else
      echo "Please select a field";
}

//changes school
else if($num==7)
{
   $high_school=clean_string($_POST['high_school']);
   
   $query=mysql_query("UPDATE user_data SET high_school='$high_school' WHERE user_id=$_SESSION[id]");
   if($query)
      echo "Change successful!";
   else
   {
      echo "Something has gone wrong";
      log_error("settings_query.php - 7: ",mysql_error());
   }
}

//gets groups
else if($num==8)
{
    $num2=(int)($_POST['num2']);

    //gets all groups
    if($num2==1)
    {
        $query=mysql_query("SELECT audience_defaults FROM data WHERE num=1 LIMIT 1");
        $query2=mysql_query("SELECT audience_group_lists FROM user_data WHERE user_id=$_SESSION[id] LIMIT 1");
        if($query&&mysql_num_rows($query)==1&&$query2&&mysql_num_rows($query2)==1)
        {
            $array=mysql_fetch_row($query);
            $array2=mysql_fetch_row($query2);
            
            $group_defaults=explode('|^|*|', $array[0]);
            $group_list=explode('|^|*|', $array2[0]);

            
            $groups=array();
            for($x = 0; $x < sizeof($group_defaults); $x++)
                $groups[]=$group_defaults[$x];

            if($group_list[0]!='')
            {
                for($x = 0; $x < sizeof($group_list); $x++)
                    $groups[]=$group_list[$x];
            }

            $JSON=array();
            $JSON['groups_list']=$groups;
            echo json_encode($JSON);
            exit();
        }
    }

    //gets only added groups
    else if($num2==2)
    {
        $query=mysql_query("SELECT audience_group_lists FROM user_data WHERE user_id=$_SESSION[id] LIMIT 1");
        if($query&&mysql_num_rows($query)==1)
        {
            $array=mysql_fetch_row($query);
            $group_list=explode('|^|*|', $array[0]);

            $groups=array();

            for($x = 0; $x < sizeof($group_list); $x++)
                $groups[]=$group_list[$x];

            $JSON=array();
            $JSON['groups_list']=$groups;
            echo json_encode($JSON);
            exit();
        }
    }
}

////adds group
//else if($num==9)
//{
//   $group=clean_string($_POST['group']);
//
//   //if group can be added
//   if($group!=''&&strlen($group)<=20)
//   {
//       if(!is_valid_audience($group))
//       {
//           $query=mysql_query("SELECT audience_group_lists FROM user_data WHERE user_id=$_SESSION[id] LIMIT 1");
//           if($query&&mysql_num_rows($query)==1)
//           {
//               $array=mysql_fetch_row($query);
//               $groups=explode('|^|*|', $array[0]);
//
//               if($array[0]=='')
//                   $groups[0]=$group;
//               else
//                   $groups[]=$group;
//
//               $groups=implode('|^|*|', $groups);
//               $query=mysql_query("UPDATE user_data SET audience_group_lists='$groups' WHERE user_id=$_SESSION[id]");
//               if($query)
//                   echo "Group added";
//               else
//               {
//                   echo "Something went wrong. We are working to fix it";
//                   log_error("settings_query.php - 9: ",mysql_error());
//               }
//           }
//           else
//           {
//               echo "Something went wrong. We are working to fix it";
//               log_error("settings_query.php - 9: ",mysql_error());
//           }
//       }
//       else
//           echo "This group already exists";
//   }
//   else
//       echo "Invalid Group";
//}

//deletes group
else if($num==10)
{
   $group=clean_string($_POST['group']);
   if(is_valid_audience($group))
   {
       $query=mysql_query("SELECT audience_group_lists FROM user_data WHERE user_id=$_SESSION[id] LIMIT 1");
       if($query&&mysql_num_rows($query)==1)
       {
           $array=mysql_fetch_row($query);
           $groups=explode('|^|*|', $array[0]);

           $temp_groups=array();
           for($x = 0; $x < sizeof($groups); $x++)
           {
               if($groups[$x]!=$group)
                   $temp_groups[]=$groups[$x];
           }
           $groups=implode('|^|*|', $temp_groups);

           $query=mysql_query("UPDATE user_data SET audience_group_lists='$groups' WHERE user_id=$_SESSION[id]");
           if($query)
               echo "Group deleted";
           else
           {
               echo "Something went wrong. We are working to fix it";
               log_error("settings_query.php - 10: ",mysql_error());
           }
       }
       else
       {
           echo "Something went wrong. We are working to fix it";
           log_error("settings_query.php - 10: ",mysql_error());
       }
   }
   else
       echo "You cannot delete this group";
}

//gets users in spefied group
else if($num==11)
{
    $group=clean_string($_POST['group']);
    if($group!=''&&is_valid_audience($group))
    {
        $query=mysql_query("SELECT user_friends, audience_groups FROM user_data WHERE user_id=$_SESSION[id] LIMIT 1");
        if($query&&mysql_num_rows($query)==1)
        {
            $array=mysql_fetch_row($query);
            $friends=explode('|^|*|', $array[0]);
            $groups=explode('|^|*|', $array[1]);


            $user_ids=array();
            $images=array();
            $names=array();
            for($x = 0; $x < sizeof($groups); $x++)
            {
                $groups[$x]=explode('|%|&|', $groups[$x]);
                if(in_array($group, $groups[$x]))
                {
                    $user_ids[]=$friends[$x];
                    $images[]=get_profile_picture($friends[$x]);
                    $names[]=get_user_name($friends[$x]);
                }
            }

            $JSON=array();
            $JSON['users']=$user_ids;
            $JSON['images']=$images;
            $JSON['names']=$names;
            echo json_encode($JSON);
            exit();
        }
    }
}

//Change college
else if($num==12)
{
   $college=clean_string($_POST['college']);

   $query=mysql_query("UPDATE user_data SET college='$college' WHERE user_id=$_SESSION[id]");
   if($query)
      echo "Change successful!";
   else
   {
      echo "Something has gone wrong";
      log_error("settings_query.php - 12: ",mysql_error());
   }
}

//gets colors
else if($num==13)
{
    $type=clean_string($_POST['type']);
    
    $query=mysql_query("SELECT display_colors FROM user_display WHERE user_id=$_SESSION[id] LIMIT 1");
    if($query&&mysql_num_rows($query)==1)
    {
        $array=mysql_fetch_row($query);
        $colors=explode('|^|*|', $array[0]);
        
        if($type=='border_color')
        {
            $JSON=array();
            $JSON['colors']=explode(',', $colors[0]);
            $JSON['opacity']=(int)($colors[3]);
        }
        else if($type=='background_color'||$type=='opacity')
        {
            $JSON=array();
            $JSON['colors']=explode(',', $colors[1]);
            $JSON['opacity']=(int)($colors[3]);
        }
        else if($type=='text_color')
        {
            $JSON=array();
            $JSON['colors']=explode(',', $colors[2]);
            $JSON['opacity']=(int)($colors[3]);
        }
        
        echo json_encode($JSON);
        exit();
    }
}

//changes country
else if($num==14)
{
    $country=clean_string($_POST['country']);
    $countries=get_countries();
    if(in_array($country, $countries))
    {
        $query=mysql_query("UPDATE user_data SET country='$country' WHERE user_id=$_SESSION[id]");
        if($query)
            echo "Change Successful!";
        else
        {
            echo "Something went wrong";
            log_error("settings_query.php: - 14:  ", log_error());
        }
    }
    else
        echo "Invalid country.";
}

//gets blocked users
else if($num==15)
{
    $timezone=(int)($_POST['timezone']);
    
    $query=mysql_query("SELECT blocked_users, blocked_user_timestamps FROM user_data WHERE user_id=$_SESSION[id] LIMIT 1");
    if($query&&mysql_num_rows($query)==1)
    {
        $array=mysql_fetch_row($query);
        $blocked_users=explode('|^|*|', $array[0]);
        $blocked_user_timestamps=explode('|^|*|', $array[1]);
        
        if($array[0]!='')
        {
            $names=array();
            $profile_pictures=array();
            for($x = 0; $x < sizeof($blocked_users); $x++)
            {
                //gets name
                $names[]=get_user_name($blocked_users[$x]);

                //gets profile pictures
                $profile_pictures[]=get_profile_picture($blocked_users[$x]);
                
                $blocked_user_timestamps[$x]=get_time_since($blocked_user_timestamps[$x], $timezone);
            }

            $JSON=array();
            $JSON['blocked_users']=$blocked_users;
            $JSON['profile_pictures']=$profile_pictures;
            $JSON['blocked_user_timestamps']=$blocked_user_timestamps;
            $JSON['names']=$names;
            echo json_encode($JSON);
            exit();
        }
        else
        {
            $JSON=array();
            $JSON['blocked_users']=array();
            $JSON['profile_pictures']=array();
            $JSON['blocked_user_timestamps']=array();
            $JSON['names']=array();
            echo json_encode($JSON);
            exit();
        }
    }
}

//deletes background image
else if($num==16)
{
    include("requiredS3.php");
    
    if(file_exists_server("https://s3.amazonaws.com/bucket_name/users/$_SESSION[id]/photos/background.jpg"))
        $s3->deleteObject("bucket_name", "users/$_SESSION[id]/photos/background.jpg");
    else if(file_exists_server("https://s3.amazonaws.com/bucket_name/users/$_SESSION[id]/photos/background.png"))
        $s3->deleteObject("bucket_name", "users/$_SESSION[id]/photos/background.png");
    else if(file_exists_server("https://s3.amazonaws.com/bucket_name/users/$_SESSION[id]/photos/background.gif"))
        $s3->deleteObject("bucket_name", "users/$_SESSION[id]/photos/background.gif");
}

//gets user birthday
else if($num==17)
{
    $query=mysql_query("SELECT user_birthday FROM user_data WHERE user_id=$_SESSION[id] LIMIT 1");
    if($query&&mysql_num_rows($query)==1)
    {
        $array=mysql_fetch_row($query);
        $birthday=explode('|^|*|', $array[0]);
        
        $JSON=array();
        $JSON['year']=$birthday[2];
        echo json_encode($JSON);
        exit();
    }
}

//sets up email change
else if($num==18)
{
    $new_email=clean_string($_POST['new_email']);
    
    if((filter_var($new_email, FILTER_VALIDATE_EMAIL) == true)&& strlen($new_email) <255)
    {
        $query=mysql_query("SELECT id FROM users WHERE email='".$new_email."' LIMIT 1");
        if($query&&mysql_num_rows($query)==0)
        {
            $query=mysql_query("SELECT user_id, timestamp FROM email_change WHERE new_email='".$new_email."' LIMIT 1");
            if($query&&mysql_num_rows($query)==0)
            {
                //creates random confirmation code
                $passkey=sha1(uniqid(rand()));

                //gets name
                $query=mysql_query("SELECT firstName, lastName FROM users WHERE id=$_SESSION[id] LIMIT 1");
                if($query&&mysql_num_rows($query)==1)
                {
                    $array=mysql_fetch_row($query);
                    $first_name=$array[0];
                    $last_name=$array[1];

                    //sends email
                    if(sendAWSEmail($new_email, "Email confirmation", "This email is going to be the new email for the Redlay account associated to $first_name $last_name with user id $_SESSION[id]. Click the link to confirm if this is you. http://www.redlay.com/change_email.php?passkey=$passkey&&confirmation=true. If this is not you, click here. http://www.redlay.com/change_email.php?$passkey&&confirmation=false"))
                    $query=mysql_query("INSERT INTO email_change SET passkey='$passkey', new_email='$new_email', timestamp='".get_date()."', user_id=$_SESSION[id] ");
                    if($query)
                        echo "Confirmation email has been sent to specified email. Changes will occur once it is confirmed by clicking of the sent link";
                    else
                    {
                        echo "Something went wrong. We are working on fixing it";
                        log_error("settings_query.php: (18:2): ", mysql_errro());
                    }
                }
                else
                {
                    echo "Something went wrong. We are working on fixing it";
                    log_error("settings_query.php: (18:1): ", mysql_error());
                }
            }
            else
            {
                $array=mysql_fetch_row($query);
                $user_id=$array[0];
                $timestamp=$array[1];
                
                $date=get_date();
                if($date-$timestamp>=180)
                {
                    if($user_id==$_SESSION['id'])
                    {
                        if(sendAWSEmail($new_email, "Email confirmation", "This email is going to be the new email for the Redlay account associated to $first_name $last_name with user id $_SESSION[id]. Click the link to confirm if this is you. http://www.redlay.com/change_email.php?passkey=$passkey&&confirmation=true. If this is not you, click here. http://www.redlay.com/change_email.php?passkey=$passkey&&confirmation=false"))
                        {
                            echo "Another email has been sent to specified email address";
                            $query=mysql_query("UPDATE email_change SET timestamp='".get_date()."' WHERE user_id=$_SESSION[id]");
                        }
                        else
                            echo "Invalid email";
                    }
                    else
                        echo "Someone is already trying to change their email to this";
                }
                else
                    echo "Please wait a couple minutes before trying again";
            }
        }
        else
            echo "Email is already in use. Please choose a different one";
    }
    else
        echo "Invalid email";
}