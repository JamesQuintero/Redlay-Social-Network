<?php
@include('init.php');
include('universal_functions.php');
$allowed="users";
include('security_checks.php');

$slot_number=(int)($_POST['slot_number']);
$data=$_POST['data'];
$type=clean_string($_POST['type']);
$added_type=clean_string($_POST['added_type']);

if($slot_number>=0&&$slot_number<=17)
{
    if($type=='default')
    {
        $data=(int)($data);
        $query=mysql_query("SELECT default_position_grid, added_position_grid FROM user_maps WHERE user_id=$_SESSION[id] LIMIT 1");
        if($query&&mysql_num_rows($query)==1)
        {
            $array=mysql_fetch_row($query);
            $default_position_grid=explode('|^|*|', $array[0]);
            $added_position_grid=explode('|^|*|', $array[1]);

            $bool=false;
            //determines whether something is already there
            if($array[0]!='')
            {
               if(in_array($slot_number, $default_position_grid))
                  $bool=true;
            }
            
            if($array[1]!='')
            {
               if(in_array($slot_number, $added_position_grid))
                  $bool=true;
            }

            //if nothing is there
            if($bool==false)
            {
                $default_position_grid[$data]=$slot_number;
                $default_position_grid=implode('|^|*|', $default_position_grid);
                $query=mysql_query("UPDATE user_maps SET default_position_grid='$default_position_grid' WHERE user_id=$_SESSION[id]");
                if($query)
                   echo "success";
            }
            else
               echo "Something is already there";
        }
        else
        {
           echo "Something went wrong. We are working on fixing it";
           send_mail_error("map_set_item_location: (type:website): ", mysql_error());
        }
    }
    else if($type=="added")
    {
        $data=(int)($data);
        $query=mysql_query("SELECT added_items, added_item_types, added_position_grid, default_position_grid FROM user_maps WHERE user_id=$_SESSION[id] LIMIT 1");
        if($query&&mysql_num_rows($query)==1)
        {
            $array=mysql_fetch_row($query);
            $added_items=explode('|^|*|', $array[0]);
            $added_item_types=explode('|^|*|', $array[1]);
            $added_position_grid=explode('|^|*|', $array[2]);
            $default_position_grid=explode('|^|*|', $array[3]);

            if(!in_array($slot_number, $default_position_grid)&&!in_array($slot_number, $added_position_grid))
            {
                $temp_added_items=array();
                $temp_added_item_types=array();
                $temp_added_position_grid=array();

                for($x = 0; $x < sizeof($added_items); $x++)
                {
                    if($data!=$added_items[$x]||$added_type!=$added_item_types[$x])
                    {
                        $temp_added_items[]=$added_items[$x];
                        $temp_added_item_types[]=$added_item_types[$x];
                        $temp_added_position_grid[]=$added_position_grid[$x];
                    }
                }

                $added_items=$temp_added_items;
                $added_item_types=$temp_added_item_types;
                $added_position_grid=$temp_added_position_grid;

                if($array[0]=='')
                {
                    $added_items[0]=$data;
                    $added_item_types[0]=$added_type;
                    $added_position_grid[0]=$slot_number;
                }
                else
                {
                    $added_items[]=$data;
                    $added_item_types[]=$added_type;
                    $added_position_grid[]=$slot_number;
                }

                $added_items=implode('|^|*|', $added_items);
                $added_item_types=implode('|^|*|', $added_item_types);
                $added_position_grid=implode('|^|*|', $added_position_grid);

                $query=mysql_query("UPDATE user_maps SET added_items='$added_items', added_item_types='$added_item_types', added_position_grid='$added_position_grid' WHERE user_id=$_SESSION[id]");
                if($query)
                   echo "success";
            }


        }
        else
        {
           echo "Something went wrong. We are working on fixing it";
           send_mail_error("map_set_item_location: (type:website): ", mysql_error());
        }
    }
    else if($type=='website')
    {
        for($x = 0; $x < sizeof($data); $x++)
            $data[$x]=clean_string($data[$x]);
        
        
        
        //checks whether thumbnail has been saved
        
        $query=mysql_query("SELECT added_items, added_item_types, added_position_grid, default_position_grid, data FROM user_maps WHERE user_id=$_SESSION[id] LIMIT 1");
        if($query&&mysql_num_rows($query)==1)
        {
            $array=mysql_fetch_row($query);
            $added_items=explode('|^|*|', $array[0]);
            $added_item_types=explode('|^|*|', $array[1]);
            $added_position_grid=explode('|^|*|', $array[2]);
            $default_position_grid=explode('|^|*|', $array[3]);
            $map_data=explode('|^|*|', $array[4]);
            
            if(!in_array($slot_number, $added_position_grid)&&!in_array($slot_number, $default_position_grid))
            {
                
                $temp_added_items=array();
                $temp_added_item_types=array();
                $temp_added_position_grid=array();
                $temp_map_data=array();

                //gets item_id and index of item
                $item_id=-1;
                $index=-1;
                for($x = 0; $x < sizeof($added_items); $x++)
                {
                    $map_data[$x]=explode('|%|&|', $map_data[$x]);
                    
                    if($data[0]==$map_data[$x][0])
                    {
                        $item_id=$map_data[$x][2];
                        $index=$x;
                    }
                }
                
                
                //if item already exists, just change the slot number
                if($index!=-1)
                {
                    $added_position_grid[$index]=$slot_number;
                    
                    $added_position_grid=implode('|^|*|', $added_position_grid);
                    $query=mysql_query("UPDATE user_maps SET added_position_grid='$added_position_grid' WHERE user_id=$_SESSION[id]");
                    if($query)
                   echo "success";
                }
                
                //if item doesn't already exist
                else
                {
                    //gets id the thumbnail will have
                    
                    $largest=0;
                    for($x = 0; $x < sizeof($map_data); $x++)
                    {
                        if($map_data[$x][2]>$largest)
                            $largest=$map_data[$x][2];
                    }
                    $item_id=$largest+1;
                    
                    
                    $thumb_path="./users/users/$_SESSION[id]/thumbs/preview_thumbs/".$item_id.".jpg";
                    copy($data[1], $thumb_path);

                    //if there are currently no added items
                    if($array[4]=='')
                    {
                        $added_items[0]='';
                        $added_item_types[0]='';
                        $added_position_grid[0]=$slot_number;

                        $data[2]=$item_id;
                        $map_data[0]=$data;
                    }
                    else
                    {
                        //makes map_data array same size as others
                        $temp_map_data=array();
                        for($x = 0; $x < sizeof($added_items); $x++)
                        {
                            if(isset($map_data[$x]))
                                $temp_map_data[]=$map_data[$x];
                            else
                                $temp_map_data[]='';
                        }
                        $map_data=$temp_map_data;
                        
                        
                        
                        $added_items[]='';
                        $added_item_types[]='';
                        $added_position_grid[]=$slot_number;

                        $data[2]=$item_id;
                        $map_data[]=$data;
                    }

                    for($x = 0; $x < sizeof($map_data); $x++)
                        $map_data[$x]=implode('|%|&|', $map_data[$x]);

                    $added_items=implode('|^|*|', $added_items);
                    $added_item_types=implode('|^|*|', $added_item_types);
                    $added_position_grid=implode('|^|*|', $added_position_grid);
                    $map_data=implode('|^|*|', $map_data);

                    $query=mysql_query("UPDATE user_maps SET added_items='$added_items', added_item_types='$added_item_types', added_position_grid='$added_position_grid', data='$map_data' WHERE user_id=$_SESSION[id]");
                    if($query)
                     echo "success";
                    else
                    {
                        echo "Something went wrong";
                        log_error("map_set_item_location.php: (1): ", mysql_error());
                    }
                }
                
            }
            else
               echo "There is something already there";
        }
        else
        {
           echo "Something went wrong. We are working on fixing it";
           log_error("map_set_item_location.php: (2): ", mysql_error());
        }
    }
    else
       echo "Invalid map type";
}
else
   echo "Invalid slot number";