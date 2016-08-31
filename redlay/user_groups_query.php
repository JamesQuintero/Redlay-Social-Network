<?php
@include('init.php');
include('universal_functions.php');
$allowed="both";
include('security_checks.php');

$num=(int)($_POST['num']);

if($num==1)
{
    $type=clean_string($_POST['type']);
    
    $query=mysql_query("SELECT group_defaults FROM data WHERE num=1 LIMIT 1");
    $query2=mysql_query("SELECT audience_group_lists, user_friends, audience_groups FROM user_data WHERE user_id=$_SESSION[id] LIMIT 1");
    if($query&&mysql_num_rows($query)==1&&$query2&&mysql_num_rows($query2)==1)
    {
        $array=mysql_fetch_row($query);
        $array2=mysql_fetch_row($query2);
        $audience_list=explode('|^|*|', $array2[0]);
        $audience_defaults=explode('|^|*|', $array[0]);
        $adds=explode('|^|*|', $array2[1]);
        $adds_groups=explode('|^|*|', $array2[2]);
        
        for($x = 0; $x < sizeof($adds_groups); $x++)
            $adds_groups[$x]=explode('|%|&|', $adds_groups[$x]);

        $groups=array();
        if($type!='add')
            $groups[0]='Everyone';

        for($x = 0; $x < sizeof($audience_defaults); $x++)
            $groups[]=$audience_defaults[$x];

        if($array2[0]!='')
        {
            for($x =0; $x < sizeof($audience_list); $x++)
               $groups[]=$audience_list[$x];
        }
        
        //gets num adds in each group
        $num_adds=array();
        for($x = 0; $x < sizeof($groups); $x++)
        {
            $num=0;
            for($y = 0; $y < sizeof($adds_groups); $y++)
            {
                if(in_array($groups[$x], $adds_groups[$y]))
                    $num++;
            }
            
            if($groups[$x]!='Everyone')
                $num_adds[$x]=$num;
            else
                $num_adds[$x]=sizeof($adds);
        }

        $JSON=array();
        $JSON['groups']=$groups;
        $JSON['num_adds']=$num_adds;
        echo json_encode($JSON);
        exit();
    }
}

//gets current user groups
else if($num==2)
{
    $ID=(int)($_POST['user_id']);

    $query=mysql_query("SELECT group_defaults FROM data WHERE num=1 LIMIT 1");
    $query2=mysql_query("SELECT audience_group_lists, user_friends, audience_groups FROM user_data WHERE user_id=$_SESSION[id] LIMIT 1");
    if($query&&mysql_num_rows($query)==1&&$query2&&mysql_num_rows($query2)==1)
    {
        $array=mysql_fetch_row($query);
        $array2=mysql_fetch_row($query2);
        
        $audience_defaults=explode('|^|*|', $array[0]);
        $audience_list=explode('|^|*|', $array2[0]);
        $friends=explode('|^|*|', $array2[1]);
        $user_groups=explode('|^|*|', $array2[2]);

        for($x = 0; $x < sizeof($user_groups); $x++)
            $user_groups[$x]=explode('|%|&|', $user_groups[$x]);

        $index=-1;
        for($x = 0; $x < sizeof($friends); $x++)
        {
            if($friends[$x]==$ID)
                $index=$x;
        }

        if($index!=-1)
        {
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
            
            //gets num adds in each group
            $num_adds=array();
            for($x = 0; $x < sizeof($groups); $x++)
            {
                $num=0;
                for($y = 0; $y < sizeof($user_groups); $y++)
                {
                    if(in_array($groups[$x], $user_groups[$y]))
                        $num++;
                }
                
                $num_adds[$x]=$num;
            }
            
        
            $JSON=array();
            $JSON['groups']=$groups;
            $JSON['groups_in']=$groups_in;
            $JSON['num_adds']=$num_adds;
            echo json_encode($JSON);
            exit();
        }
    }
}

//gets current photo groups
else if($num==3)
{
    $picture_id=clean_string($_POST['picture_id']);

    $query=mysql_query("SELECT group_defaults FROM data WHERE num=1 LIMIT 1");
    $query2=mysql_query("SELECT audience_group_lists, audience_groups FROM user_data WHERE user_id=$_SESSION[id] LIMIT 1");
    $query3=mysql_query("SELECT pictures, image_audiences FROM pictures WHERE user_id=$_SESSION[id] LIMIT 1");
    if($query&&mysql_num_rows($query)==1&&$query2&&mysql_num_rows($query2)==1&&$query3&&mysql_num_rows($query3)==1)
    {
        $array=mysql_fetch_row($query);
        $array2=mysql_fetch_row($query2);
        $array3=mysql_fetch_row($query3);

        $audience_list=explode('|^|*|', $array2[0]);
        $add_groups=explode('|^|*|', $array2[1]);
        $audience_defaults=explode('|^|*|', $array[0]);
        $pictures=explode('|^|*|', $array3[0]);
        $image_audiences=explode('|^|*|', $array3[1]);

        for($x = 0; $x < sizeof($image_audiences); $x++)
            $image_audiences[$x]=explode('|%|&|', $image_audiences[$x]);

        $index=-1;
        for($x = 0; $x < sizeof($pictures); $x++)
        {
            if($pictures[$x]==$picture_id)
                $index=$x;
        }

        if($index!=-1)
        {
            $groups_in=array();
            $groups=array();

            $groups[0]='Everyone';
            if($image_audiences[$index][0]=='Everyone')
                $groups_in[0]='yes';
            else
                $groups_in[0]='no';

            for($x = 0; $x < sizeof($audience_defaults); $x++)
            {
                $groups[]=$audience_defaults[$x];
                if(in_array($audience_defaults[$x], $image_audiences[$index]))
                    $groups_in[]='yes';
                else
                    $groups_in[]='no';
            }

            if($audience_list[0]!='')
            {
                for($x =0; $x < sizeof($audience_list); $x++)
                {
                   $groups[]=$audience_list[$x];

                   if(in_array($audience_list[$x], $image_audiences[$index]))
                        $groups_in[]='yes';
                    else
                        $groups_in[]='no';
                }
            }
            
            //gets num adds in each group
            $num_adds=array();
            for($x = 0; $x < sizeof($groups); $x++)
            {
                $add_groups[$x]=explode('|%|&|', $add_groups[$x]);
                $num=0;
                for($y = 0; $y < sizeof($add_groups); $y++)
                {
                    if(in_array($groups[$x], $add_groups[$y]))
                        $num++;
                }
                
                if($groups[$x]!='Everyone')
                    $num_adds[$x]=$num;
                else
                    $num_adds[$x]=sizeof($add_groups);
            }

            $JSON=array();
            $JSON['groups']=$groups;
            $JSON['groups_in']=$groups_in;
            $JSON['num_adds']=$num_adds;
            echo json_encode($JSON);
        }
    }
}

//gets current post groups
else if($num==4)
{
    $post_id=(int)($_POST['post_id']);

    $query=mysql_query("SELECT group_defaults FROM data WHERE num=1 LIMIT 1");
    $query2=mysql_query("SELECT audience_group_lists, audience_groups FROM user_data WHERE user_id=$_SESSION[id] LIMIT 1");
    $query3=mysql_query("SELECT post_ids, post_groups FROM content WHERE user_id=$_SESSION[id] LIMIT 1");
    if($query&&mysql_num_rows($query)==1&&$query2&&mysql_num_rows($query2)==1&&$query3&&mysql_num_rows($query3)==1)
    {
        $array=mysql_fetch_row($query);
        $array2=mysql_fetch_row($query2);
        $array3=mysql_fetch_row($query3);

        $audience_list=explode('|^|*|', $array2[0]);
        $add_groups=explode('|^|*|', $array2[1]);
        $audience_defaults=explode('|^|*|', $array[0]);
        $post_ids=explode('|^|*|', $array3[0]);
        $audiences=explode('|^|*|', $array3[1]);

        for($x = 0; $x < sizeof($audiences); $x++)
            $audiences[$x]=explode('|%|&|', $audiences[$x]);

        $index=-1;
        for($x = 0; $x < sizeof($post_ids); $x++)
        {
            if($post_ids[$x]==$post_id)
                $index=$x;
        }

        if($index!=-1)
        {
            $groups_in=array();
            $groups=array();

            $groups[0]='Everyone';
            if($audiences[$index][0]=='Everyone')
                $groups_in[0]='yes';
            else
                $groups_in[0]='no';

            for($x = 0; $x < sizeof($audience_defaults); $x++)
            {
                $groups[]=$audience_defaults[$x];
                if(in_array($audience_defaults[$x], $audiences[$index]))
                    $groups_in[]='yes';
                else
                    $groups_in[]='no';
            }

            if($audience_list[0]!='')
            {
                for($x =0; $x < sizeof($audience_list); $x++)
                {
                   $groups[]=$audience_list[$x];

                   if(in_array($audience_list[$x], $audiences[$index]))
                        $groups_in[]='yes';
                    else
                        $groups_in[]='no';
                }
            }
            
            //gets num adds in each group
            $num_adds=array();
            for($x = 0; $x < sizeof($groups); $x++)
            {
                $add_groups[$x]=explode('|%|&|', $add_groups[$x]);
                $num=0;
                for($y = 0; $y < sizeof($add_groups); $y++)
                {
                    if(in_array($groups[$x], $add_groups[$y]))
                        $num++;
                }
                
                if($groups[$x]!='Everyone')
                    $num_adds[$x]=$num;
                else
                    $num_adds[$x]=sizeof($add_groups);
            }

            $JSON=array();
            $JSON['groups']=$groups;
            $JSON['groups_in']=$groups_in;
            $JSON['num_adds']=$num_adds;
            echo json_encode($JSON);
        }
    }
}

//adds new group
else if($num==5)
{
    $new_group=clean_string($_POST['new_group']);
    
    if($new_group!=''&&strlen($new_group)<=50)
    {
        $query=mysql_query("SELECT audience_group_lists FROM user_data WHERE user_id=$_SESSION[id] LIMIT 1");
        if($query&&mysql_num_rows($query)==1)
        {
            $array=mysql_fetch_row($query);
            $groups=explode('|^|*|', $array[0]);
            
            if($array[0]=='')
                $groups=$new_group;
            else
            {
                $groups[]=$new_group;
                $groups=implode('|^|*|', $groups);
            }
            
            $query=mysql_query("UPDATE user_data SET audience_group_lists='$groups' WHERE user_id=$_SESSION[id]");
            if($query)
                echo "Group added";
            else
            {
                echo "Something went wrong";
                log_error("user_groups_query.php: (5): ", mysql_error());
            }
        }
    }
}

//deletes group
else if($num==6)
{
    $group=clean_string($_POST['group']);
//    $group='Tech';
    
    if($group!='Everyone'&&$group!='Friends'&&$group!='Close Friends'&&$group!='Family')
    {
        $query=mysql_query("SELECT audience_groups, audience_group_lists FROM user_data WHERE user_id=$_SESSION[id] LIMIT 1");
        if($query&&mysql_num_rows($query)==1)
        {
            $array=mysql_fetch_row($query);
            $add_groups=explode('|^|*|', $array[0]);
            $audience_groups_list=explode('|^|*|', $array[1]);

            //removes group from current list of groups
            if($audience_groups_list[0]==$group&&sizeof($audience_groups_list)==1)
                $audience_groups_list='';
            else
            {
                $temp_audiences=array();
                for($x = 0; $x < sizeof($audience_groups_list); $x++)
                {
                    if($group!=$audience_groups_list[$x])
                        $temp_audiences[]=$audience_groups_list[$x];
                }
                $audience_groups_list=implode('|^|*|', $temp_audiences);
            }

            //removes group from add's groups
            for($x = 0; $x < sizeof($add_groups); $x++)
            {
                $add_groups[$x]=explode('|%|&|', $add_groups[$x]);

                //if list of groups for add contains the deleting group
                if(in_array($group, $add_groups[$x]))
                {
                    if($add_groups[$x][0]==$group&&sizeof($add_groups[$x])==1)
                        $add_groups[$x]='Friends';
                    else
                    {
                        //deletes group from certain add's groups
                        $temp_add_groups=array();
                        for($y = 0; $y < sizeof($add_groups[$x]); $y++)
                        {
                            if($add_groups[$x][$y]!=$group)
                                $temp_add_groups[]=$add_groups[$x][$y];
                        }

                        $add_groups[$x]=implode('|%|&|', $temp_add_groups);
                    }
                }
                else
                    $add_groups[$x]=implode('|%|&|', $add_groups[$x]);
            }

            $add_groups=implode('|^|*|', $add_groups);
            $query=mysql_query("UPDATE user_data SET audience_groups='$add_groups', audience_group_lists='$audience_groups_list' WHERE user_id=$_SESSION[id]");
            if($query)
            {

                //gets post audiences
                $query=mysql_query("SELECT post_groups FROM content WHERE user_id=$_SESSION[id] LIMIT 1");
                if($query&&mysql_num_rows($query)==1)
                {
                    $array=mysql_fetch_row($query);
                    $audiences=explode('|^|*|', $array[0]);

                    for($x = 0; $x < sizeof($audiences); $x++)
                    {
                        $audiences[$x]=explode('|%|&|', $audiences[$x]);

                        //replaces deleting group
                        if(in_array($group, $audiences[$x]))
                        {
                            if($audiences[$x][0]==$group&&sizeof($audiences[$x])==1)
                                $audiences[$x]='Friends';
                            else    
                            {
                                $temp_audience=array();
                                for($y = 0; $y < sizeof($audiences[$x]); $y++)
                                {
                                    if($group!=$audiences[$x][$y])
                                        $temp_audience[]=$audiences[$x][$y];
                                }
                                $audiences[$x]=implode('|%|&|', $temp_audience);
                            }
                        }
                        else
                            $audiences[$x]=implode('|%|&|', $audiences[$x]);
                    }

                    $audiences=implode('|^|*|', $audiences);
                    $query=mysql_query("UPDATE content SET post_groups='$audiences' WHERE user_id=$_SESSION[id] LIMIT 1");
                    if($query)
                    {

                        //gets photo audiences
                        $query=mysql_query("SELECT image_audiences FROM pictures WHERE user_id=$_SESSION[id] LIMIT 1");
                        if($query&&mysql_num_rows($query)==1)
                        {
                            $array=mysql_fetch_row($query);
                            $image_audiences=explode('|^|*|', $array[0]);

                            for($x = 0; $x < sizeof($image_audiences); $x++)
                            {
                                $image_audiences[$x]=explode('|%|&|', $image_audiences[$x]);

                                if(in_array($group, $image_audiences[$x]))
                                {
                                    if($audiences[$x][0]==$group&&sizeof($audiences[$x])==1)
                                        $audiences[$x]='Friends';
                                    else    
                                    {
                                        $temp_image_audience=array();
                                        for($y = 0; $y < sizeof($image_audiences[$x]); $y++)
                                        {
                                            if($group!=$image_audiences[$x][$y])
                                                $temp_image_audiences[]=$image_audiences[$x][$y];
                                        }

                                        $image_audiences[$x]=implode('|%|&|', $temp_image_audiences);
                                    }
                                }
                                else
                                    $image_audiences[$x]=implode('|%|&|', $image_audiences[$x]);
                            }

                            $image_audiences=implode('|^|*|', $image_audiences);

                            $query=mysql_query("UPDATE pictures SET image_audiences='$image_audiences' WHERE user_id=$_SESSION[id]");
                            if($query)
                                echo "Group deleted";
                            else
                                echo "Something went wrong";
                        }
                    }

                }
            }
            else
            {
                echo "Something went wrong";
                log_error("user_groups_query.php: (6): ", mysql_error());
            }
        }
    }
}