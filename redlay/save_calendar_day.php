<?php
@include('init.php');
include('universal_functions.php');
$allowed="users";
include('security_checks.php');

$day=(int)($_POST['day']);
$month=clean_string($_POST['month']);
$year=(int)($_POST['year']);
$data=$_POST['data'];

if($day!=''&&$month!=''&&$year!='')
{
    $query=mysql_query("SELECT information FROM calendar WHERE user_id=$_SESSION[id] LIMIT 1");
    if($query&&mysql_num_rows($query)==1)
    {
        $array=mysql_fetch_row($query);
        $information=explode('|^|*|', str_replace("'", "\'", $array[0]));
        
        $index=-1;
        for($x = 0; $x < sizeof($information); $x++)
        {
            $information[$x]=explode('|%|&|', $information[$x]);
            
            if($information[$x][0]==$year&&$information[$x][1]==$month&&$information[$x][2]==$day)
                $index=$x;
        }
        
        
        //if day already exists
        if($index!=-1)
        {
            if($data[0]!='')
            {
                for($x = 0; $x < sizeof($data); $x++)
                    $data[$x]=clean_string($data[$x]);

                $information[$index][3]=implode('|@|$|', $data);
            }
            else
            {
                $temp_information=array();
                for($x = 0; $x < sizeof($information); $x++)
                {
                    if($x!=$index)
                        $temp_information[]=$information[$x];
                }
                $information=$temp_information;
            }
            
            for($x = 0; $x < sizeof($information); $x++)
                $information[$x]=implode('|%|&|', $information[$x]);
        }
        
        //if day doesn't already exist
        else
        {
            if($data[0]!='')
            {
                $temp_data=array();
                $temp_data[0]=$year;
                $temp_data[1]=$month;
                $temp_data[2]=$day;


                for($x = 0; $x < sizeof($data); $x++)
                    $data[$x]=clean_string($data[$x]);

                $temp_data[3]=implode('|@|$|', $data);



                if($array[0]=='')
                    $information[0]=implode('|%|&|', $temp_data);
                else
                {
                    for($x = 0; $x < sizeof($information); $x++)
                        $information[$x]=implode('|%|&|', $information[$x]);
                    $information[]=implode('|%|&|', $temp_data);
                }
                
                
            }
            else
            {
                $temp_information=array();
                for($x = 0; $x < sizeof($information); $x++)
                {
                    if($x!=$index)
                        $temp_information[]=$information[$x];
                }
                $information=$temp_information;
                
                for($x = 0; $x < sizeof($information); $x++)
                    $information[$x]=implode('|%|&|', $information[$x]);
            }
        }
        
        
        $information=implode('|^|*|', $information);
        
        $query=mysql_query("UPDATE calendar SET information='$information' WHERE user_id=$_SESSION[id]");
        if($query)
            echo "Change successful!";
        else
        {
            echo "Something went wrong. We are working on fixing it";
            log_error("save_calendar_day.php: ", mysql_error());
        }
    }
}