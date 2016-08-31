<?php
@include('init.php');
include('universal_functions.php');
$allowed="users";
include('security_checks.php');

$query=mysql_query("SELECT * FROM user_maps WHERE user_id=$_SESSION[id] LIMIT 1");
if($query&&mysql_num_rows($query)==1)
{
    $array=mysql_fetch_array($query);
    $map_items=explode('|^|*|', $array['items']);
    $map_type=$array['map_type'];
    $position_grid=explode('|^|*|', $array['position_grid']);
    $position_open=explode('|^|*|', $array['position_open']);
    $background_pictures=explode('|^|*|', $array['background_pictures']);

    $JSON=array();
    $JSON['map_items']=$map_items;
    $JSON['map_type']=$map_type;
    $JSON['position_grid']=$position_grid;
    $JSON['position_open']=$position_open;
    $JSON['background_pictures']=$background_pictures;
    echo json_encode($JSON);
    exit();
}
else
    log_error("map_information_query.php: ", mysql_error());