<?php
@include('init.php');
include('universal_functions.php');
$allowed="users";
include('security_checks.php');

$XPos=(int)($_POST['xPos']);
$YPos=(int)($_POST['yPos']);
$id=(int)($_POST['id']);
$type=clean_string($_POST['type']);
$map_type=clean_string($_POST['added_type']);

if($type=="default")
{
    $query=mysql_query("SELECT default_position_open FROM user_maps WHERE user_id=$_SESSION[id] LIMIT 1");
    if($query&&mysql_num_rows($query)==1)
    {
        $array=mysql_fetch_row($query);
        $default_position_open=explode('|^|*|', $array[0]);

        $default_position_open[$id]=explode('|%|&|');
        $default_position_open[$id][0]=$XPos;
        $default_position_open[$id][1]=$YPos;

        $default_position_open[$id]=implode('|%|&|', $default_position_open[$id]);

        $default_position_open=implode('|^|*|', $default_position_open);

        $query=mysql_query("UPDATE user_maps SET default_position_open='$default_position_open' WHERE user_id=$_SESSION[id]");
        if(!$query)
        {
            echo "Something went wrong";
            log_error("change_map_location.php: (1): ", mysql_error());
        }
    }
}
else if($type=="added")
{
    $query=mysql_query("SELECT added_items, added_item_types, added_position_open FROM user_maps WHERE user_id=$_SESSION[id] LIMIT 1");
    if($query&&mysql_num_rows($query)==1)
    {
        $array=mysql_fetch_row($query);
        $added_items=explode('|^|*|', $array[0]);
        $added_item_types=explode('|^|*|', $array[1]);
        $added_position_open=explode('|^|*|', $array[2]);

        $index=-1;
        for($x =0; $x < sizeof($added_items); $x++)
        {
            if($added_items[$x]==$id&&$added_item_types[$x]==$map_type)
                $index=$x;
        }

        $positions=array();
        $positions[0]=$XPos;
        $positions[1]=$YPos;
        $position=implode('|%|&|', $positions);
        if($index==-1)
        {
            $added_items[]=$id;
            $added_item_types[]=$map_type;
            $added_position_open[]=$position;
        }
        else
        {
            $added_items[$index]=$id;
            $added_item_types[$index]=$map_type;
            $added_position_open[$index]=$position;
        }

        $added_items=implode('|^|*|', $added_items);
        $added_item_types=implode('|^|*|', $added_item_types);
        $added_position_open=implode('|^|*|', $added_position_open);

        $query=mysql_query("UPDATE user_maps SET added_items='$added_items', added_item_types='$added_item_types', added_position_open='$added_position_open' WHERE user_id=$_SESSION[id]");
        if(!$query)
        {
            echo "Something went wrong";
            log_error("change_map_location.php: (1): ", mysql_error());
        }
    }
}