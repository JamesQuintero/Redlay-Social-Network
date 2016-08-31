<?php
@include('init.php');
include('../alert_functions.php');
include("../universal_functions.php");
$allowed="users";
include("security_checks.php");


//$ID=strip_tags(stripslashes(mysql_real_escape_string($_POST['user_id'])));
$search=clean_string($_POST['query']))));


//return all results
$name=explode(" ", $search);

//searches and gets results
$query=mysql_query("SELECT id FROM users WHERE firstName='$name[0]' AND lastName='$name[1]' LIMIT 100");
if($query&&mysql_num_rows($query)>=1)
{
    $results=array();
    for($x = 0; $x < mysql_num_rows($query); $x++)
    {
        //gets all results using for loop
        $array[$x]=mysql_fetch_array($query);
        $results[$x]=$array[$x][0];
    }
}
else
    $results=array();

//displays the results
for($x = 0; $x < sizeof($results); $x++)
{
    $query2=mysql_query("SELECT search_options FROM user_privacy WHERE user_id='$results[$x]' LIMIT 1");
    if($query2&&mysql_num_rows($query2)==1)
    {
        $array2=mysql_fetch_array($query2);
        $search_array=explode('|^|*|', $array2[0]);
        if($search_array[2]=='no')
            $results[$x]=0;

        $images[$x]='http://www.redlay.com/users/images/'.$results[$x].'/0.jpg';
        $names[$x]=get_user_name($results[$x]);
        
    }
    else
        log_error(mysql_error());
}
$JSON['results']=$results;
$JSON['profile_images']=$images;
$JSON['result_names']=$names;
echo json_encode($JSON);
exit();