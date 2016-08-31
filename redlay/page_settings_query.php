<?php
@include('init.php');
include('universal_functions.php');
$allowed="pages";
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
        //blowfish hashes password for database storage
        $current=crypt($password, '$2a$07$27'.$email.'SECRET_SALT_STRING');


        $query=mysql_query("SELECT id FROM pages WHERE password='$current' AND id=$_SESSION[page_id] LIMIT 1");
        if($query&&mysql_num_rows($query)==1)
        {
            if($new==$confirm_new)
           {
              //blowfish hashes password for database storage
                $new=crypt($password, '$2a$07$27'.$email.'SECRET_SALT_STRING');

              $query=mysql_query("UPDATE pages SET password='$new' WHERE id=$_SESSION[page_id]");
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
            send_mail_error("page_settings_query.php - 1: ",mysql_error());
         }
    }
    else
        echo "One or more fields are empty";
}

//changes created date
else if($num==3)
{
   $created=clean_string($_POST['created']);

   if($created!='')
   {
       $query=mysql_query("UPDATE page_data SET created='$created' WHERE page_id=$_SESSION[page_id]");
       if($query)
            echo "Change successful!";
       else
       {
          echo "An Error has occured. Please try again";
          send_mail_error("page_settings_query.php - 3: ",mysql_error());
       }
   }
   else
      echo "Please fill in all of the fields";
}

//changes description
else if($num==4)
{
   $description=clean_string($_POST['description']);

   $query=mysql_query("UPDATE page_data SET description='$description' WHERE page_id=$_SESSION[page_id]");
   if($query)
      echo "Change successful!";
   else
   {
      echo "Something went wrong. We are working on fixing it";
      send_mail_error("page_settings_query.php - 4: ",mysql_error());
   }
}

//changes location
else if($num==5)
{
    $location=clean_string($_POST['location']);


    $query=mysql_query("UPDATE page_data SET location='$location' WHERE page_id=$_SESSION[page_id]");
    if($query)
        echo "Change successful!";
    else
    {
        echo "Something went wrong. We are working on fixing it";
      send_mail_error("page_settings_query.php - 5: ",mysql_error());
    }
}

//changes website
else if($num==6)
{
    $website=clean_string($_POST['website']);

    
    $query=mysql_query("UPDATE page_data SET website='$website' WHERE page_id=$_SESSION[page_id]");
    if($query)
        echo "Change successful!";
    else
    {
        echo "Something went wrong. We are working on fixing it";
      send_mail_error("page_settings_query.php - 6: ",mysql_error());
    }
}

//changes name
else if($num==7)
{
    $name=clean_string($_POST['name']);

    if($name!='')
    {
        $query=mysql_query("UPDATE page_data SET name='$name' WHERE page_id=$_SESSION[page_id]");
        if($query)
            echo "Change successful!";
        else
        {
            echo "Something went wrong. We are working on fixing it";
          send_mail_error("page_settings_query.php - 7: ",mysql_error());
        }
    }
    else
        echo "Name cannot be empty";
}

//changes main video
else if($num==8)
{
    $url=clean_string($_POST['video']);

    $first=strpos($url, '&');
    if($first!=false)
        $url=substr($url, 0, $first);
    $url=str_replace('watch?v=', 'v/', $url);

    if($url!=''&&(strstr($url, 'http://youtube.com')!=false||strstr($url, 'http://www.youtube.com')!=false||strstr($url, 'www.youtube.com')!=false||strstr($url, 'youtube.com')!=false))
    {
        $query=mysql_query("UPDATE page_display SET main_video='$url' WHERE page_id=$_SESSION[page_id]");
        if($query)
            echo "Change successful!";
        else
        {
            echo "Something went wrong. We are working on fixing it";
          send_mail_error("page_settings_query.php - 7: ",mysql_error());
        }
    }
}

////change page view settings
//else if($num==10)
//{
//    
//    if product
//    //toggle user review displays
//    //toggle price display
//    
//    if movie
//    //toggle user reviews
//    //toggle page/critique reviews
//    
//    if any
//    //toggle main video display
//}

else if($num==9)
{
    $content=$_POST['content'];
    
    $type=clean_string($_POST['type']);
    
    $query=mysql_query("SELECT type, type_other FROM pages WHERE id=$_SESSION[page_id] LIMIT 1");
    if($query&&mysql_num_rows($query)==1)
    {
        $array=mysql_fetch_row($query);
        $page_type=$array[0];
        $other_type=$array[1];
        
        //gets types
        //number is index of data array
        //action type is how to store data in data array
        if(($type=='company_founded'&&$page_type=='Company')||($type=='person_born'&&$page_type=='Person'))
        {
            $number=0;
            $action_type='change';
        }
        else if(($type=='company_website'&&$page_type=='Company')||($type=='person_website'&&$page_type=='Person'))
        {
            $number=1;
            $action_type='change';
        }
        else if($type=='company_CEO'&&$page_type=='Company')
        {
            $number=2;
            $action_type='change_with_link';
        }
        else if(($type=='singer_group'&&$page_type=='Person'&&$other_type=='Singer'))
        {
            $number=4;
            $action_type='change_with_link';
        }
        else if(($type=='company_num_employees'&&$page_type=='Company')||($type=='author_num_books_sold'&&$page_type=='Person'&&$other_type=='Author'))
        {
            $number=6;
            $action_type='change';
        }
        else if($type=='company_headquarters'&&$page_type=='Company')
        {
            $number=7;
            $action_type='change';
        }
        else if($type=='person_add_personality'&&$page_type=='Person')
        {
            $number=2;
            $action_type='change';
        }
            
//        else if($type=='person_home'&&$page_type=='Person')
//        {
//            $number=3;
//            $action_type='change';
//        }
        else if($type=='actor_add_movie'&&$page_type=='Person'&&$other_type=='Actor')
        {
            $number=4;
            $action_type='add_with_link';
        }
        else if($type=='actor_add_tv_show'&&$page_type=='Person'&&$other_type=='Actor')
        {
            $number=5;
            $action_type='add_with_link';
        }
        else if($type=='actor_add_commercial'&&$page_type=='Person'&&$other_type=='Actor')
        {
            $number=6;
            $action_type='add_with_link';
        }
        else if($type=='actor_add_other'&&$page_type=='Person'&&$other_type=='Actor')
        {
            $number=7;
            $action_type='add_with_link';
        }
        else if($type=='actor_agency'&&$page_type=='Person'&&$other_type=='Actor')
        {
            $number=8;
            $action_type='change_with_link';
        }
        else if($type=='singer_type'&&$page_type=='Person'&&$other_type=='Singer')
        {
            $number=3;
            $action_type='change';
        }
        else if($type=='athlete_type'&&$page_type=='Person'&&$other_type=='Athlete')
        {
            $number=4;
            $action_type='change';
        }
        else if($type=='singer_record_label'&&$page_type=='Person'&&$other_type=='Singer')
        {
            $number=5;
            $action_type='change_with_link';
        }
        else if($type=='author_type'&&$page_type=='Person'&&$other_type=='Author')
        {
            $number=4;
            $action_type='change';
        }
        else if($type=='author_add_book'&&$page_type=='Person'&&$other_type=='Author')
        {
            $number=5;
            $action_type='add_with_link';
        }
        else if($type=='athlete_team'&&$page_type=='Person'&&$other_type=='Athlete')
        {
            $number=5;
            $action_type='change_with_link';
        }
        else if($type=='athlete_add_award'&&$page_type=='Person'&&$other_type=='Athlete')
        {
            $number=6;
            $action_type='add';
        }
        else if($type=='comedian_add_stage'&&$page_type=='Person'&&$other_type=='Comedian')
        {
            $number=4;
            $action_type='add_with_link';
        }
        else if($type=='character_origin'&&$page_type=='Person'&&$other_type=='Character')
        {
            $number=4;
            $action_type='change';
        }
        else if($type=='character_add_quote'&&$page_type=='Person'&&$other_type=='Character')
        {
            $number=5;
            $action_type='add_with_link';
        }
        else if($type=='government_official_work'&&$page_type=='Person'&&$other_type=='Government Official')
        {
            $number=4;
            $action_type='change';
        }
        else if($type=='government_official_job'&&$page_type=='Person'&&$other_type=='Government Official')
        {
            $number=5;
            $action_type='change';
        }
        else if($type=='band_add_member'&&$page_type=='Person'&&$other_type=='Band')
        {
            $number=4;
            $action_type='add_with_link';
        }
        else if($type=='band_type'&&$page_type=='Person'&&$other_type=='Band')
        {
            $number=5;
            $action_type='change';
        }
        else if($type=='band_record_label'&&$page_type=='Person'&&$other_type=='Band')
        {
            $number=6;
            $action_type='change';
        }
        else if($type=='band_add_song'&&$page_type=='Person'&&$other_type=='Band')
        {
            $number=7;
            $action_type='add_with_link';
        }
        else if($type=='director_add_movie'&&$page_type=='Person'&&$other_type=='Director')
        {
            $number=4;
            $action_type="add_with_link";
        }
        else if($type=='director_add_tv_show'&&$page_type=='Person'&&$other_type=='Director')
        {
            $number=5;
            $action_type='add_with_link';
        }
        else if($type=='director_add_commercial'&&$page_type=='Person'&&$other_type=='Director')
        {
            $number=6;
            $action_type='add_with_link';
        }
        else if($type=='director_add_other'&&$page_type=='Person'&&$other_type=='Director')
        {
            $number=7;
            $action_type='add_with_link';
        }
        else if($type=='producer_add_movie'&&$page_type=='Person'&&$other_type=='Producer')
        {
            $number=4;
            $action_type='add_with_link';
        }
        else if($type=='producer_add_tv_show'&&$page_type=='Person'&&$other_type=='Producer')
        {
            $number=5;
            $action_type='add_with_link';
        }
        else if($type=='producer_add_commercial'&&$page_type=='Person'&&$other_type=='Producer')
        {
            $number=6;
            $action_type='add_with_link';
        }
        else if($type=='producer_add_other'&&$page_type=='Person'&&$other_type=='Producer')
        {
            $number=7;
            $action_type='add_with_link';
        }
        else if($type=='public_figure_best_known_for'&&$page_type=='Person'&&$other_type=='Public Figure')
        {
            $number=4;
            $action_type='add_with_link';
        }
        else if($type=='other_created'&&$page_type=='Other')
        {
            $number=0;
            $action_type='change';
        }
        else if($type=='other_website'&&$page_type=='Other')
        {
            $number=1;
            $action_type='change';
        }
        else if($type=='place_location'&&$page_type=='Other'&&$other_type=='Place')
        {
            $number=2;
            $action_type='change';
        }
        else if($type=='place_size'&&$page_type=='Other'&&$other_type=='Place')
        {
            $number=3;
            $action_type='change';
        }
        else if($type=='place_founder'&&$page_type=='Other'&&$other_type=='Place')
        {
            $number=4;
            $action_type='change';
        }
        else if($type=='place_leader'&&$page_type=='Other'&&$other_type=='Place')
        {
            $number=5;
            $action_type='change_with_link';
        }
        else if($type=='product_company'&&$page_type=='Other'&&$other_type=='Product')
        {
            $number=2;
            $action_type='change_with_link';
        }
        else if($type=='product_price'&&$page_type=='Other'&&$other_type=='Product')
        {
            $number=3;
            $action_type='change';
        }
        else if($type=='product_buy_link'&&$page_type=='Other'&&$other_type=='Product')
        {
            $number=4;
            $action_type='change';
        }
        else if($type=='movie_add_studio'&&$page_type=='Other'&&$other_type=='Movie')
        {
            $number=2;
            $action_type='add_with_link';
        }
        else if($type=='movie_add_starring'&&$page_type=='Other'&&$other_type=='Movie')
        {
            $number=3;
            $action_type='add_with_link';
        }
        else if($type=='movie_type'&&$page_type=='Other'&&$other_type=='Movie')
        {
            $number=4;
            $action_type='change';
        }
        else if($type=='movie_rating'&&$page_type=='Other'&&$other_type=='Movie')
        {
            $number=5;
            $action_type='change';
        }
        else if($type=='tv_show_type'&&$page_type=='Other'&&$other_type=='TV Show')
        {
            $number=2;
            $action_type='change';
        }
        else if($type=='tv_show_add_studio'&&$page_type=='Other'&&$other_type=='TV Show')
        {
            $number=3;
            $action_type='add_with_link';
        }
        else if($type=='tv_show_add_starring'&&$page_type=='Other'&&$other_type=='TV Show')
        {
            $number=4;
            $action_type='add_with_link';
        }
        else if($type=='tv_show_num_seasons'&&$page_type=='Other'&&$other_type=='TV Show')
        {
            $number=5;
            $action_type='change';
        }
        else if($type=='book_type'&&$page_type=='Other'&&$other_type=='Book')
        {
            $number=2;
            $action_type='change';
        }
        else if($type=='book_author'&&$page_type=='Other'&&$other_type=='Book')
        {
            $number=3;
            $action_type='change_with_link';
        }
        else if($type=='book_num_sold'&&$page_type=='Other'&&$other_type=='Book')
        {
            $number=4;
            $action_type='change';
        }
        else if($type=='book_buy_link'&&$page_type=='Other'&&$other_type=='Book')
        {
            $number=5;
            $action_type='change';
        }
        else if($type=='website_add_founder'&&$page_type=='Other'&&$other_type=='Website')
        {
            $number=2;
            $action_type='add_with_link';
        }
        else if($type=='website_type'&&$page_type=='Other'&&$other_type=='Website')
        {
            $number=3;
            $action_type='change';
        }
        else if($type=='charity_cause'&&$page_type=='Other'&&$other_type=='Charity')
        {
            $number=2;
            $action_type='change';
        }
        else if($type=='quote/saying_creator'&&$page_type=='Other'&&$other_type=='Quote/Saying')
        {
            $number=2;
            $action_type='change_with_link';
        }
        else if($type=='quote/saying_origin'&&$page_type=='Other'&&$other_type=='Quote/Saying')
        {
            $number=3;
            $action_type='change';
        }
        else
            exit();

   //     if($type=='company_founded'||$type=='person_born'||$type=='other_created')
   //         $number=0;
   //     else if($type=='company_website'||$type=='person_website'||$type=='other_website')
   //         $number=1;
   //     else if($type=='company_CEO'||$type=='person_add_personality'||$type=='person_delete_personality'||$type=='place_location'||$type=='product_company'||
   //             $type=='movie_add_studio'||$type=='movie_delete_studio'||$type=='tv_show_type'||$type=='book_type'||$type=='website_add_founder'||$type=='website_delete_founder'||
   //             $type=='charity_cause'||$type=='quote/saying person')
   //         $number=2;
   //     else if($type=='company_CFO'||$type=='person_home'||$type=='place_size'||$type=='product_price'||$type=='movie_add_starring'||$type=='movie_delete_starring'||
   //             $type=='tv_show_add_studio'||$type=='tv_show_delete_studio'||$type=='book_author'||$type=='website_type')
   //         $number=3;
   //     else if($type=='company_president'||$type=='actor_add_movie'||$type=='actor_delete_movie'||$type=='singer_type'||$type=='athlete_type'||$type=='author_type'||
   //             $type=='comedian_add_stage'||$type=='comedian_delete_stage'||$type=='character_origin'||$type=='government_official_work'||$type=='band_add_member'||
   //             $type=='band_delete_member'||$type=='producer_add_movie'||$type=='producer_delete_movie'||$type=='public_figure_best_known_for'||$type=='place_founder'||
   //             $type=='product_buy_link'||$type=='movie_type'||$type=='tv_show_add_starring'||$type=='tv_show_delete_starring'||$type=='book_num_sold')
   //         $number=4;
   //     else if($type=='company_vice_president'||$type=='singer_group'||$type=='actor_add_tv_show'||$type=='actor_delete_tv_show'||$type=='author_add_book'||
   //             $type=='author_delete_book'||$type=='athlete_team'||$type=='character_add_quote'||$type=='character_delete_quote'||$type=='government_official_job'||
   //             $type=='band_type'||$type=='director_add_movie'||$type=='director_delete_movie'||$type=='producer_add_tv_show'||$type=='producer_delete_tv_show'||
   //             $type=='movie_rating'||$type=='tv_show_num_seasons'||$type=='book_num_languages')
   //         $number=5;
   //     else if($type=='company_num_employees'||$type=='author_num_books_sold'||$type=='actor_add_commercial'||$type=='actor_delete_commercial'||$type=='singer_record_label'||
   //             $type=='athlete_add_award'||$type=='athlete_delete_award'||$type=='band_record_label'||$type=='director_add_tv_show'||$type=='director_delete_tv_show'||
   //             $type=='producer_add_commercial'||$type=='producer_delete_commercial'||$type=='book_buy_link')
   //         $number=6;
   //     else if($type=='company_headquarters'||$type=='actor_add_other'||$type=='actor_delete_other'||$type=='band_add_song'||$type=='band_delete_song'||$type=='director_add_commercial'||
   //             $type=='director_delete_commercial')
   //         $number=7;
   //     else if($type=='actor_agency')
   //         $number=8;


        $query=mysql_query("SELECT data FROM page_data WHERE page_id=$_SESSION[page_id] LIMIT 1");
        if($query&&mysql_num_rows($query)==1)
        {
            $array=mysql_fetch_row($query);
            $data=explode('|^|*|', $array[0]);
            

           if($action_type=='change_with_link')
           {
               for($x = 0; $x < sizeof($content); $x++)
                   $content[$x]=clean_string($content[$x]);

               
               $data[$number]=implode('|@|$|', $content);
               
               $message="Change successful!";
           }
           else if($action_type=='change')
           { 
               $data[$number]=clean_string($content);
               
               $message="Change successful!";
           }
           else if($action_type=='add_with_link')
           {
               $data[$number]=array();
               
               for($x = 0; $x < sizeof($content); $x++)
               {
                   for($y = 0; $y < sizeof($content[$x]); $y++)
                       $content[$x][$y]=clean_string($content[$x][$y]);
                   
                   if($content[$x][0]!='')
                    {
                        if($data[$number][0]=='')
                            $data[$number][0]=implode('|@|$|', $content[$x]);
                        else
                            $data[$number][]=implode('|@|$|', $content[$x]);
                    }
               }
               
               $data[$number]=implode('|%|&|', $data[$number]);

                $message="Items changed!";
           }
           else if($action_type=='add')
           {
               $data[$number]=array();
               
               for($x = 0; $x < sizeof($content); $x++)
               {
                   $content[$x]=clean_string($content[$x]);
                   if($content[$x]!='')
                    {
                        if($data[$number][0]=='')
                            $data[$number][0]=$content[$x];
                        else
                            $data[$number][]=$content[$x];
                    }
               }
               
               $data[$number]=implode('|%|&|', $data[$number]);

                $message="Items changed!";
           }
//           else if($action_type=='delete')
//           {
//               $content=(int)($content);
//
//               if($content>=0)
//               {
//                   $data[$number]=explode('|%|&|', $data[$number]);
//
//                   $temp_items=array();
//                   for($x = 0; $x < sizeof($data[$number]); $x++)
//                   {
//                       if($x!=$content)
//                           $temp_items[]=$data[$number];
//                   }
//                   $data[$number]=implode('|%|&|', $data[$number]);
//                   
//                   $message="Item deleted";
//               }
//           }

           $data=implode('|^|*|', $data);
           $query=mysql_query("UPDATE page_data SET data='$data' WHERE page_id=$_SESSION[page_id]");
           if($query)
               echo $message;
           else
           {
               echo "Something went wrong. We are working on fixing it";
               send_mail_error("page_settings_query.php: (9), (number:$number): ", mysql_error());
           }
        }
    }
}

else if($num==10)
{
    $type=clean_string($_POST['type']);
    
    $query=mysql_query("SELECT display_colors FROM page_display WHERE page_id=$_SESSION[page_id] LIMIT 1");
    if($query&&mysql_num_rows($query)==1)
    {
        $array=mysql_fetch_row($query);
        $colors=explode('|^|*|', $array[0]);
        
        if($type=='border_color')
        {
            $JSON=array();
            $JSON['colors']=explode(',', $colors[0]);
        }
        else if($type=='background_color')
        {
            $JSON=array();
            $JSON['colors']=explode(',', $colors[1]);
        }
        else if($type=='text_color')
        {
            $JSON=array();
            $JSON['colors']=explode(',', $colors[2]);
        }
        echo json_encode($JSON);
        exit();
    }
}