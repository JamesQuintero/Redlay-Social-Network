<?php
@include('init.php');
include('universal_functions.php');
$allowed="all";
include('security_checks.php');


$num=(int)($_POST['num']);
$ID=(int)($_POST['user_id']);

if($num==1)
{
    if(is_id($ID)&&user_id_exists($ID)&&!user_id_terminated($ID))
    {
       //type is either user or page
       $type=clean_string($_POST['type']);


       if($type=='user')
       {
          $query=mysql_query("SELECT pictures, image_types FROM pictures WHERE user_id=$ID LIMIT 1");
          if($query&&mysql_num_rows($query)==1)
          {
             $array=mysql_fetch_row($query);
             $pictures=explode('|^|*|', $array[0]);
             $image_types=explode('|^|*|', $array[1]);

          }
       }
       else if($type=='page')
       {
          $query=mysql_query("SELECT pictures, image_types FROM page_pictures WHERE page_id=$ID LIMIT 1");
          if($query&&mysql_num_rows($query)==1)
          {
             $array=mysql_fetch_row($query);
             $pictures=explode('|^|*|', $array[0]);
             $image_types=explode('|^|*|', $array[1]);

          }
       }

       $image_ids=array();
       $images=array();
       $image_widths=array();
       $image_heights=array();
       for($x = 0; $x < sizeof($pictures); $x++)
       {
          $image_ids[]=$pictures[$x];

          if($type=='user')
             $images[$x]="http://u.redlay.com/users/$ID/photos/$pictures[$x].$image_types[$x]";
          else
             $images[$x]="http://pages.redlay.com/pages/$ID/photos/$pictures[$x].$image_types[$x]";

          $dimensions=getimagesize($images[$x]);
          $image_widths[]=$dimensions[0];
          $image_heights[]=$dimensions[1];
       }

       $JSON=array();
       $JSON['images']=$images;
       $JSON['image_ids']=$image_ids;
       $JSON['image_widths']=$image_widths;
       $JSON['image_heights']=$image_heights;
       echo json_encode($JSON);
       exit();
    }
}
else if($num==2)
{
    $photo_id=clean_string($_POST['photo_id']);
    if(is_id($ID)&&user_id_exists($ID)&&!user_id_terminated($ID))
    {
        $query=mysql_query("SELECT pictures, image_types FROM pictures WHERE user_id=$ID LIMIT 1");
        if($query&&mysql_num_rows($query)==1)
        {
            $array=mysql_fetch_row($query);
            $pictures=explode('|^|*|', $array[0]);
            $image_types=explode('|^|*|', $array[1]);
            
            $index=-1;
            for($x = 0; $x < sizeof($pictures); $x++)
            {
                if($pictures[$x]==$photo_id)
                    $index=$x;
            }
            
        }
    }
}