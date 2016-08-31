<?php
@include('init.php');
include("../universal_functions.php");
$allowed="users";
include("security_checks.php");

$num=(int)($_POST['num']);



//gets default items for use
if($num==2)
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
    $query=mysql_query("SELECT added_items, added_position_grid,  added_item_types FROM user_maps WHERE user_id=$_SESSION[id] LIMIT 1");
    if($query&&mysql_num_rows($query)==1)
    {
        $array=mysql_fetch_row($query);
        $added_items=explode('|^|*|', $array[0]);
        $added_item_positions=explode('|^|*|', $array[1]);
        $types=explode('|^|*|', $array[2]);

        $links=array();
        $profile_pictures=array();
        $names=array();
        for($x = 0; $x < sizeof($added_items); $x++)
        {
            if($types[$x]=="user")
            {
                $links[$x]="profile.php?user_id=".$added_items[$x];
                $profile_pictures[$x]="http://www.redlay.com/users/images/$added_items[$x]/0.jpg";
                $names[$x]=get_user_name($added_items[$x]);
            }
            else
            {
                $profile_pictures[$x]="http://www.redlay.com/users/pages/$added_items[$x]/0.jpg";
                $links[$x]="page.php?page_id=".$added_items[$x];
                $names[$x]=get_page_name($added_items[$x]);
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