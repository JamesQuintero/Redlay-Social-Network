<?php
@include('init.php');
if(!isset($_SESSION['id'])&&!isset($_SESSION['page_id']))
{
    header("Location: http://m.redlay.com/index.php");
    exit();
}
include('universal_functions.php');

$num=(int)($_POST['num']);
if($num==1)
{
    $query=mysql_query("SELECT audience_defaults FROM data WHERE num=1 LIMIT 1");
    $query2=mysql_query("SELECT audience_group_lists FROM user_data WHERE user_id=$_SESSION[id] LIMIT 1");
    if($query&&mysql_num_rows($query)==1&&$query2&&mysql_num_rows($query2)==1)
    {
        $array=mysql_fetch_row($query);
        $array2=mysql_fetch_row($query2);
        $audience_list=explode('|^|*|', $array2[0]);
        $audience_defaults=explode('|^|*|', $array[0]);

        $groups=array();
        $groups[0]='Everyone';

        for($x = 0; $x < sizeof($audience_defaults); $x++)
            $groups[]=$audience_defaults[$x];

        if($audience_list[0]!='')
        {
            for($x =0; $x < sizeof($audience_list); $x++)
               $groups[]=$audience_list[$x];
        }

        $JSON=array();
        $JSON['groups']=$groups;
        echo json_encode($JSON);
    }
}
else
{
    $ID=(int)($_POST['user_id']);

    $query=mysql_query("SELECT audience_defaults FROM data WHERE num=1 LIMIT 1");
    $query2=mysql_query("SELECT audience_group_lists, user_friends, audience_groups FROM user_data WHERE user_id=$_SESSION[id] LIMIT 1");
    if($query&&mysql_num_rows($query)==1&&$query2&&mysql_num_rows($query2)==1)
    {
        $array=mysql_fetch_row($query);
        $array2=mysql_fetch_row($query2);
        $audience_list=explode('|^|*|', $array2[0]);
        $audience_defaults=explode('|^|*|', $array[0]);
        $friends=explode('|^|*|', $array2[1]);
        $user_groups=explode('|^|*|', $array2[2]);

        for($x = 0; $x < sizeof($user_groups); $x++)
            $user_groups[$x]=explode('|%|&|', $user_groups[$x]);

        for($x = 0; $x < sizeof($friends); $x++)
        {
            if($friends[$x]==$ID)
                $index=$x;
        }

        $groups_in=array();
        $groups=array();

        for($x = 0; $x < sizeof($audience_defaults); $x++)
        {
            if($audience_defaults[$x]!='Everyone')
            {
                $groups[]=$audience_defaults[$x];
                if(in_array($audience_defaults[$x], $user_groups[$index]))
                    $groups_in[]='yes';
                else
                    $groups_in[]='no';
            }
        }

        if($audience_list[0]!='')
        {
            for($x =0; $x < sizeof($audience_list); $x++)
            {
               $groups[]=$audience_list[$x];

               if(in_array($audience_list[$x], $user_groups[$index]))
                $groups_in[]='yes';
            else
                $groups_in[]='no';
            }
        }

        $JSON=array();
        $JSON['groups']=$groups;
        $JSON['groups_in']=$groups_in;
        echo json_encode($JSON);
    }
}
?>
