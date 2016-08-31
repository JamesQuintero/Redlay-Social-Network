<?php
include('init.php');
include('universal_functions.php');
$allowed="users";
include('security_checks.php');

$num=(int)($_POST['num']);
$content=clean_string($_POST['content']);
$add_id=(int)($_POST['add_id']);
$sort=clean_string($_POST['sort']);


//changes home view
if($num==1)
{
    if((user_is_friends($add_id, $_SESSION['id'])||$add_id==-1)&&($content=='Everything'||$content=='Posts'||$content=='Photos'||$content=="Videos"||$content=='Others')&&($sort=="Recent"||$sort=="Popularity"))
    {
        $home_view=array();
        $home_view[0]=$content;
        $home_view[1]=$add_id;
        $home_view[2]=$sort;
        $home_view=implode('|^|*|', $home_view);
        
        $query=mysql_query("UPDATE user_display SET home_view='$home_view' WHERE user_id=$_SESSION[id]");
    }
}

//gets home view
else if($num==2)
{
    $query=mysql_query("SELECT home_view FROM user_display WHERE user_id=$_SESSION[id] LIMIT 1");
    if($query&&mysql_num_rows($query)==1)
    {
        $array=mysql_fetch_row($query);
        $home_view=explode('|^|*|', $array[0]);
        
        $JSON=array();
        $JSON['content_view']=$home_view[0];
        $JSON['add_id']=$home_view[1];
        if(isset($home_view[2]))
            $JSON['sort']=$home_view[2];
        else
        {
            $JSON['sort']="Recent";
            
            $home_view[]="Recent";
            $home_view=implode('|^|*|', $home_view);
            $query=mysql_query("UPDATE user_display SET home_view='$home_view' WHERE user_id=$_SESSION[id]");
        }
        
        if($home_view[1]!=-1)
            $JSON['add_name']=get_user_name($home_view[1]);
        else
            $JSON['add_name']='';
        
        echo json_encode($JSON);
        exit();
    }
}