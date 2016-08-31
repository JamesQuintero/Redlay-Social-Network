<?php
@include('init.php');
include('universal_functions.php');
$allowed="users";
include('security_checks.php');

$general=$_POST['general_privacy'];
$non_adds_privacy=$_POST['non_adds_privacy'];
$search_privacy=$_POST['search_privacy'];
if(sizeof($general)==4 && sizeof($non_adds_privacy)==8 && sizeof($search_privacy)==5)
{
    //indices can only be yes or no
    $bool=true;
    for($x = 0; $x < sizeof($general); $x++)
    {
        if($general[$x]!='yes'&&$general[$x]!='no')
            $bool=false;
    }

    //indices can only be yes or no
    for($x = 0; $x < sizeof($non_adds_privacy); $x++)
    {
        if($non_adds_privacy[$x]!='yes'&&$non_adds_privacy[$x]!='no')
            $bool=false;
    }

    //indices can only be yes or no
    for($x = 0; $x < sizeof($search_privacy); $x++)
    {
        if($search_privacy[$x]!='yes'&&$search_privacy[$x]!='no')
            $bool=false;
    }

    //if all indices for all arrays are valid
    if($bool)
    {
        $general=implode('|^|*|', $general);
        $non_adds_privacy=implode('|^|*|', $non_adds_privacy);
        $search_privacy=implode('|^|*|', $search_privacy);

        $query=mysql_query("UPDATE user_privacy SET general='$general', display_non_friends='$non_adds_privacy', search_options='$search_privacy' WHERE user_id=$_SESSION[id]");
        if($query)
            echo "Change successful!";
        else
        {
            echo "Change failed!";
            log_error("change_privacy_settings.php: ", mysql_error());
        }
    }
    else
        echo "Invalid options";
}
else
    echo "Invalid data";
