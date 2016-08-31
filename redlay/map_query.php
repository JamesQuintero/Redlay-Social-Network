<?php
@include('init.php');
include('universal_functions.php');
//$allowed="users";
//include('security_checks.php');

//allows users and pages, but can't do $allowed="all", because that'll allowed logged out users
if(!isset($_SESSION['id']))
{
    header("Location: http://www.redlay.com");
    exit();
}

$num=(int)($_POST['num']);


//gets default items for editing
if($num==1)
{
    $query=mysql_query("SELECT map_item_defaults FROM data WHERE num=1");
    if($query)
    {
        $array=mysql_fetch_row($query);
        $default=explode('|^|*|', $array[0]);
        $query=mysql_query("SELECT default_position_grid FROM user_maps WHERE user_id=$_SESSION[id] LIMIT 1");
        if($query&&mysql_num_rows($query)==1)
        {
            $array=mysql_fetch_row($query);
            $default_position_grid=explode('|^|*|', $array[0]);
            
            $temp_default=array();
            $temp_default_position_grid=array();
            $item_numbers=array();
            for($x = 0; $x < sizeof($default); $x++)
            {
                if($default[$x]!='stats'||has_redlay_gold($_SESSION['id'], 'account_stats'))
                {
                    $temp_default[]=$default[$x];
                    $temp_default_position_grid[]=$default_position_grid[$x];
                    $item_numbers[]=$x;
                }
            }
            
            $default=$temp_default;
            $default_position_grid=$temp_default_position_grid;
            
            $JSON=array();
            $JSON['default_position_grid']=$default_position_grid;
            $JSON['default_items']=$default;
            $JSON['item_numbers']=$item_numbers;
            echo json_encode($JSON);
            exit();
        }
    }
}

//gets default items for use
else if($num==2)
{
    $query=mysql_query("SELECT map_item_defaults FROM data WHERE num=1");
    if($query)
    {
        $array=mysql_fetch_row($query);
        $default=explode('|^|*|', $array[0]);

        $query=mysql_query("SELECT default_position_grid FROM user_maps WHERE user_id=$_SESSION[id] LIMIT 1");
        if($query&&mysql_num_rows($query)==1)
        {
            $array=mysql_fetch_row($query);
            $default_position_grid=explode('|^|*|', $array[0]);
            
            $temp_default=array();
            $temp_default_position_grid=array();
            for($x = 0; $x < sizeof($default); $x++)
            {
                if($default[$x]!='stats'||($default[$x]=='stats'&&has_redlay_gold($_SESSION['id'], 'account_stats')))
                {
                    $temp_default[]=$default[$x];
                    $temp_default_position_grid[]=$default_position_grid[$x];
                }
            }
            
            $default=$temp_default;
            $default_position_grid=$temp_default_position_grid;

            for($x =0; $x < sizeof($default); $x++)
            {
                if($default[$x]=="profile")
                    $links[$x]="profile.php?user_id=".$_SESSION['id'];
                else
                    $links[$x]=$default[$x].".php";
            }

            $JSON=array();
            $JSON['default_position_grid']=$default_position_grid;
            $JSON['default_items']=$default;
            $JSON['links']=$links;
            echo json_encode($JSON);
            exit();
        }
    }
}

//gets added items for use
else if($num==3)
{
    $query=mysql_query("SELECT added_items, added_position_grid,  added_item_types, data FROM user_maps WHERE user_id=$_SESSION[id] LIMIT 1");
    if($query&&mysql_num_rows($query)==1)
    {
        $array=mysql_fetch_row($query);
        $added_items=explode('|^|*|', $array[0]);
        $added_item_positions=explode('|^|*|', $array[1]);
        $types=explode('|^|*|', $array[2]);
        $data=explode('|^|*|', $array[3]);

        $links=array();
        $profile_pictures=array();
        $names=array();
        for($x = 0; $x < sizeof($added_items); $x++)
        {
            if($types[$x]=="user")
            {
                $links[$x]="profile.php?user_id=".$added_items[$x];
                $profile_pictures[$x]=get_profile_picture($added_items[$x]);
                $names[$x]=get_user_name($added_items[$x]);
            }
            else if($types[$x]=="page")
            {
                $profile_pictures[$x]=get_page_profile_picture($added_items[$x]);
                $links[$x]="page.php?page_id=".$added_items[$x];
                $names[$x]=get_page_name($added_items[$x]);
            }
            else if($types[$x]=='')
            {
                $data[$x]=explode('|%|&|', $data[$x]);
                
                
                $profile_pictures[$x]="https://s3.amazonaws.com/bucket_name/users/".$_SESSION[id]."/thumbs/preview_thumbs/".$data[$x][2].".jpg";
                $links[$x]=$data[$x][0];
                $names[$x]=$data[$x][0];
            }
        }


        $JSON=array();
        if(!isset($added_items[0]))
        {
            $JSON['added_items']=array();
            $JSON['added_item_positions']=array();
            $JSON['links']=array();
            $JSON['types']=array();
            $JSON['profile_pictures']=array();
            $JSON['names']=array();
        }
        else
        {
            $JSON['added_items']=$added_items;
            $JSON['added_item_positions']=$added_item_positions;
            $JSON['links']=$links;
            $JSON['profile_pictures']=$profile_pictures;
            $JSON['types']=$types;
            $JSON['names']=$names;
        }
        echo json_encode($JSON);
        exit();
    }
}

//get added items for editing
else if($num==4)
{
    $query=mysql_query("SELECT added_items, added_position_grid,  added_item_types FROM user_maps WHERE user_id=$_SESSION[id] LIMIT 1");
    if($query&&mysql_num_rows($query)==1)
    {
        $array=mysql_fetch_row($query);
        $added_items=explode('|^|*|', $array[0]);
        $added_item_positions=explode('|^|*|', $array[1]);
        $types=explode('|^|*|', $array[2]);

        $profile_pictures=array();
        $names=array();
        for($x = 0; $x < sizeof($added_items); $x++)
        {
            if($types[$x]=="user")
            {
                if(file_exists_server("https://s3.amazonaws.com/bucket_name/users/$added_items[$x]/thumbs/0.jpg"))
                    $profile_pictures[$x]="https://s3.amazonaws.com/bucket_name/users/$added_items[$x]/thumbs/0.jpg";
                else if(file_exists_server("https://s3.amazonaws.com/bucket_name/users/$added_items[$x]/thumbs/0.png"))
                    $profile_pictures[$x]="https://s3.amazonaws.com/bucket_name/users/$added_items[$x]/thumbs/0.png";
                else
                    $profile_pictures[$x]="https://s3.amazonaws.com/bucket_name/users/$added_items[$x]/thumbs/0.gif";
                
                $names[$x]=get_user_name($added_items[$x]);
            }
            else
            {
                if(file_exists_server("https://s3.amazonaws.com/bucket_name/pages/$added_items[$x]/thumbs/0.jpg"))
                    $profile_pictures[$x]="https://s3.amazonaws.com/bucket_name/pages/$added_items[$x]/thumbs/0.jpg";
                else if(file_exists_server("https://s3.amazonaws.com/bucket_name/pages/$added_items[$x]/thumbs/0.png"))
                    $profile_pictures[$x]="https://s3.amazonaws.com/bucket_name/pages/$added_items[$x]/thumbs/0.png";
                else
                    $profile_pictures[$x]="https://s3.amazonaws.com/bucket_name/pages/$added_items[$x]/thumbs/0.gif";
                
                $names[$x]=get_page_name($added_items[$x]);
            }
        }

        $JSON=array();
        if(!isset($added_items[0]))
        {
            $JSON['added_items']=array();
            $JSON['added_item_positions']=array();
            $JSON['types']=array();
            $JSON['profile_pictures']=array();
            $JSON['names']=array();
        }
        else
        {
            $JSON['added_items']=$added_items;
            $JSON['added_item_positions']=$added_item_positions;
            $JSON['types']=$types;
            $JSON['profile_pictures']=$profile_pictures;
            $JSON['names']=$names;
        }
        echo json_encode($JSON);
        exit();
    }
}

//gets the list of adds or pages
else if($num==5)
{
    $type=clean_string($_POST['type']);
    if($type=="user")
    {
        $query=mysql_query("SELECT user_friends FROM user_data WHERE user_id=$_SESSION[id] LIMIT 1");
        if($query&&mysql_num_rows($query)==1)
        {
            $array=mysql_fetch_row($query);
            $friends=explode('|^|*|', $array[0]);

            for($x = 0;$x < sizeof($friends); $x++)
                $friend_names[$x]=get_user_name($friends[$x]);

            $JSON=array();
            $JSON['friends']=$friends;
            $JSON['friend_names']=$friend_names;
            echo json_encode($JSON);
            exit();
        }
    }
    else if($type=="page")
    {
        $query=mysql_query("SELECT page_likes FROM user_data WHERE user_id=$_SESSION[id] LIMIT 1");
        if($query&&mysql_num_rows($query)==1)
        {
            $array=mysql_fetch_row($query);
            $pages=explode('|^|*|', $array[0]);

            for($x = 0;$x < sizeof($pages); $x++)
                $page_names[$x]=get_page_name($pages[$x]);

            $JSON=array();
            $JSON['friends']=$pages;
            $JSON['friend_names']=$page_names;
            echo json_encode($JSON);
            exit();
        }
    }
}

//deletes added item
else if($num==10)
{
    //gets all the necessary AWS schtuff
    if (!class_exists('S3'))
        require_once('S3.php');
    if (!defined('awsAccessKey'))
        define('awsAccessKey', ACCES_KEY);
    if (!defined('awsSecretKey'))
        define('awsSecretKey', SECRET_KEY);

    //creates S3 item with schtuff
    $s3 = new S3(awsAccessKey, awsSecretKey);
    
    $added_item_index=(int)($_POST['added_item_index']);

    $query=mysql_query("SELECT added_items, added_item_types, added_position_grid, data FROM user_maps WHERE user_id=$_SESSION[id] LIMIT 1");
    if($query&&mysql_num_rows($query)==1)
    {
        $array=mysql_fetch_row($query);
        $added_items=explode('|^|*|', $array[0]);
        $added_item_types=explode('|^|*|', $array[1]);
        $added_position_grid=explode('|^|*|', $array[2]);
        $data=explode('|^|*|', $array[3]);

        
            $temp_added_items=array();
            $temp_added_item_types=array();
            $temp_added_position_grid=array();
            $temp_data=array();
            for($x = 0; $x < sizeof($added_items); $x++)
            {
                if($x!=$added_item_index)
                {
                    $temp_added_items[]=$added_items[$x];
                    $temp_added_item_types[]=$added_item_types[$x];
                    $temp_added_position_grid[]=$added_position_grid[$x];
                    $temp_data[]=$data[$x];
                }
                else
                {
                    if($data[$x]!='')
                    {
                        $data[$x]=explode('|%|&|', $data[$x]);
                        $item_id=$data[$x][2];
                        
                        $s3->deleteObject("bucket_name", "users/$_SESSION[id]/thumbs/preview_thumbs/$item_id.jpg");
                    }
                }
            }

        $added_items=implode('|^|*|', $temp_added_items);
        $added_item_types=implode('|^|*|', $temp_added_item_types);
        $added_position_grid=implode('|^|*|', $temp_added_position_grid);
        $data=implode('|^|*|', $temp_data);
        
        $query=mysql_query("UPDATE user_maps SET added_items='$added_items', added_item_types='$added_item_types', added_position_grid='$added_position_grid', data='$data' WHERE user_id=$_SESSION[id]");
        if($query)
            echo "Item deleted";
    }
}