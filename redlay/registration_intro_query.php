<?php
include('init.php');
include('universal_functions.php');

$num=(int)($_POST['num']);

//gets pictures
if($num==1)
{
    
}

//gets countries
else if($num==2)
{
    $query=mysql_query("SELECT countries FROM data WHERE num=1 LIMIT 1");
    if($query&&mysql_num_rows($query)==1)
    {
        $array=mysql_fetch_row($query);
        $countries=explode('|^|*|', $array[0]);

        $JSON=array();
        $JSON['countries']=$countries;
        echo json_encode($JSON);
        exit();
    }
}

//gets birthday
else if($num==3)
{
    $query=mysql_query("SELECT user_birthday FROM user_data WHERE user_id=$_SESSION[id] LIMIT 1");
    if($query&&mysql_num_rows($query)==1)
    {
        $array=mysql_fetch_row($query);
        $birthday=explode('|^|*|', $array[0]);
        
        if($array[0]!='')
        {
            $JSON=array();
            $JSON['month']=$birthday[0];
            $JSON['day']=$birthday[1];
            $JSON['year']=$birthday[2];
            echo json_encode($JSON);
            exit();
        }
        else
        {
            $JSON=array();
            $JSON['month']="";
            $JSON['day']='';
            $JSON['year']='';
            echo json_encode($JSON);
            exit();
        }
    }
}

//finds referrer
else if($num==4)
{
    $first_name=clean_string($_POST['first_name']);
    $last_name=clean_string($_POST['last_name']);
    $ID=(int)($_POST['user_id']);
    
    //if searching by name
    if($first_name!=""||$last_name!="")
    {
        //if only searching by first name
        if($first_name!=""&&$last_name=="")
        {
            $query=mysql_query("SELECT id, lastName FROM users WHERE firstName='$first_name'");
            if($query)
            {
                $names=array();
                $profile_pictures=array();
                $user_ids=array();
                $array=array();
                for($x = 0; $x < mysql_num_rows($query); $x++)
                {
                    //gets every user with the matching names
                    $array[$x]=mysql_fetch_row($query);

                    //checks if user id isn't terminated
                    if(!user_id_terminated($array[$x][0]))
                        $user_ids[]=$array[$x][0];
                    
                    //gets name
                    $names[]=$first_name." ".$array[$x][1];
                    
                    //gets profile picture
                    $profile_pictures[]=get_profile_picture($array[$x][0]);
                }
                
                $JSON=array();
                $JSON['num_results']=mysql_num_rows($query);
                $JSON['names']=$names;
                $JSON['profile_pictures']=$profile_pictures;
                $JSON['user_ids']=$user_ids;
                echo json_encode($JSON);
                exit();
            }
            else
            {
                echo "Something went wrong. We are working on fixing it";
                log_error("registration_intro_query.php: (num==4): (1): ", mysql_error());
            }
        }
        
        //if only searching by last name
        else if($first_name==""&&$last_name!="")
        {
            $query=mysql_query("SELECT id, firstName FROM users WHERE lastName='$last_name'");
            if($query)
            {
                $names=array();
                $profile_pictures=array();
                $user_ids=array();
                $array=array();
                for($x = 0; $x < mysql_num_rows($query); $x++)
                {
                    //gets every user with the matching names
                    $array[$x]=mysql_fetch_row($query);

                    //checks if user id isn't terminated
                    if(!user_id_terminated($array[$x][0]))
                        $user_ids[]=$array[$x][0];
                    
                    //gets name
                    $names[]=$array[$x][1]." ".$last_name;
                    
                    //gets profile picture
                    $profile_pictures[]=get_profile_picture($array[$x][0]);
                }
                
                $JSON=array();
                $JSON['num_results']=mysql_num_rows($query);
                $JSON['names']=$names;
                $JSON['profile_pictures']=$profile_pictures;
                $JSON['user_ids']=$user_ids;
                echo json_encode($JSON);
                exit();
            }
            else
            {
                echo "Something went wrong. We are working on fixing it";
                log_error("registration_intro_query.php: (num==4): (2): ", mysql_error());
            }
        }
        
        //if searching by both
        else if($first_name!=""&&$last_name!="")
        {
            $query=mysql_query("SELECT id FROM users WHERE firstName='$first_name' AND lastName='$last_name'");
            if($query)
            {
                $names=array();
                $profile_pictures=array();
                $user_ids=array();
                $array=array();
                for($x = 0; $x < mysql_num_rows($query); $x++)
                {
                    //gets every user with the matching names
                    $array[$x]=mysql_fetch_row($query);

                    //checks if user id isn't terminated
                    if(!user_id_terminated($array[$x][0]))
                        $user_ids[]=$array[$x][0];
                    
                    //gets name
                    $names[]=$first_name." ".$last_name;
                    
                    //gets profile picture
                    $profile_pictures[]=get_profile_picture($array[$x][0]);
                }
                
                $JSON=array();
                $JSON['num_results']=mysql_num_rows($query);
                $JSON['names']=$names;
                $JSON['profile_pictures']=$profile_pictures;
                $JSON['user_ids']=$user_ids;
                echo json_encode($JSON);
                exit();
            }
            else
            {
                echo "Something went wrong. We are working on fixing it";
                log_error("registration_intro_query.php: (num==4): (3): ", mysql_error());
            }
        }
    }
    
    //if searching by user_id
    else 
    {
        if(is_id($ID)&&user_id_exists($ID)&&!user_id_terminated($ID))
        {
            $query=mysql_query("SELECT firstName, lastName FROM users WHERE id=$ID LIMIT 1");
            if(mysql_num_rows($query)==1)
            {
                $array=mysql_fetch_row($query);
                $first_name=$array[0];
                $last_name=$array[1];
                
                $JSON=array();
                $JSON['num_results']=1;
                
                $names=array();
                $names[]=$first_name." ".$last_name;
                $JSON['names']=$names;
                
                $profile_pictures=array();
                $profile_pictures[]=get_profile_picture($ID);
                $JSON['profile_pictures']=$profile_pictures;
                
                $user_ids=array();
                $user_ids[]=$ID;
                $JSON['user_ids']=$user_ids;
                echo json_encode($JSON);
                exit();
            }
            else
            {
                $JSON=array();
                $JSON['num_results']=0;
                $JSON['names']="";
                $JSON['profile_picture']="";
                $JSON['user_ids']=array();
                echo json_encode($JSON);
                exit();
            }
        }
    }
}