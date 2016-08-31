<?php
@include('init.php');
include('universal_functions.php');
$allowed="users";
include('security_checks.php');


$month=clean_string($_POST['month']);
$year=(int)($_POST['year']);
$num=(int)($_POST['num']);
$ID=(int)($_POST['user_id']);

if($num==1)
{
    if(is_id($ID)&&user_id_exists($ID)&&!user_id_terminated($ID)&&($ID==$_SESSION['id']||user_is_friends($ID, $_SESSION['id'])=='true'))
    {
        //sees if user can see calendar
        $calendar_visible=get_calendar_visibility($ID);
        if($calendar_visible||$ID==$_SESSION['id'])
        {
            $num_years=$year-2000;        

            $leap_years=0;
            for($y = 0; $y < $num_years; $y++)
            {
                if($y%4==0)
                    $leap_years++;
            }

            $start_day=(($num_years+($leap_years)+6)%7);



            if($month=="January")
            {
                $month_num=1;
                $month_length=31;
            }
            else if($month=="February")
            {
                $month_num=2;
                if($year%4==0)
                    $month_length=29;
                else
                    $month_length=28;
            }
            else if($month=="March")
            {
                $month_num=3;
                $month_length=31;
            }
            else if($month=="April")
            {
                $month_num=4;
                $month_length=30;
            }
            else if($month=="May")
            {
                $month_num=5;
                $month_length=31;
            }
            else if($month=="June")
            {
                $month_num=6;
                $month_length=30;
            }
            else if($month=="July")
            {
                $month_num=7;
                $month_length=31;
            }
            else if($month=="August")
            {
                $month_num=8;
                $month_length=31;
            }
            else if($month=="September")
            {
                $month_num=9;
                $month_length=30;
            }
            else if($month=="October")
            {
                 $month_num=10;
                 $month_length=31;
            }
            else if($month=="November")
            {
                $month_num=11;
                $month_length=30;
            }
            else if($month=="December")
            {
                $month_num=12;
                $month_length=31;
            }



            for($x = 1; $x < $month_num; $x++)
            {
                //January
                if($x==1)
                    $start_day+=31;
                //February
                else if($x==2)
                {
                    if($year%4==0)
                        $start_day+=29;
                    else
                        $start_day+=28;
                }
                //March
                else if($x==3)
                    $start_day+=31;
                //April
                else if($x==4)
                    $start_day+=30;
                //May
                else if($x==5)
                    $start_day+=31;
                //June
                else if($x==6)
                    $start_day+=30;
                //July
                else if($x==7)
                    $start_day+=31;
                //August
                else if($x==8)
                    $start_day+=31;
                //September
                else if($x==9)
                    $start_day+=30;
                //October
                else if($x==10)
                    $start_day+=31;
                //November
                else if($x==11)
                    $start_day+=30;
                //December
                else if($x==12)
                    $start_day+=31;
            }
                $start_day=$start_day%7;

            $query=mysql_query("SELECT information FROM calendar WHERE user_id=$ID LIMIT 1");
            if($query&&mysql_num_rows($query)==1)
            {
                $array=mysql_fetch_row($query);
                $data=explode('|^|*|', $array[0]);

                for($x = 0; $x < sizeof($data); $x++)
                {
                    $data[$x]=explode('|%|&|', $data[$x]);

                    //0 = year
                    //1 = month
                    //2 = day
                    //3 = data
                    $data[$x][3]=explode('|@|$|', $data[$x][3]);
                }


                //gets:
                //prev_month
                //next_month
                //prev_year
                //next_year
                
                $date=explode(' ', str_replace(',', '', get_adjusted_date(get_date(), 0)));
                if($month=="January")
                {
                    $prev_month="December";
                    $next_month="February";
                    $prev_year=$year-1;
                    $next_year=$year;
                }
                else if($month=="February")
                {
                    $prev_month="January";
                    $next_month="March";
                    $prev_year=$year;
                    $next_year=$year;
                }
                else if($month=="March")
                {
                    $prev_month="February";
                    $next_month="April";
                    $prev_year=$year;
                    $next_year=$year;
                }
                else if($month=="April")
                {
                    $prev_month="March";
                    $next_month="May";
                    $prev_year=$year;
                    $next_year=$year;
                }
                else if($month=="May")
                {
                    $prev_month="April";
                    $next_month="June";
                    $prev_year=$year;
                    $next_year=$year;
                }
                else if($month=="June")
                {
                    $prev_month="May";
                    $next_month="July";
                    $prev_year=$year;
                    $next_year=$year;
                }
                else if($month=="July")
                {
                    $prev_month="June";
                    $next_month="August";
                    $prev_year=$year;
                    $next_year=$year;
                }
                else if($month=="August")
                {
                    $prev_month="July";
                    $next_month="September";
                    $prev_year=$year;
                    $next_year=$year;
                }
                else if($month=="September")
                {
                    $prev_month="August";
                    $next_month="October";
                    $prev_year=$year;
                    $next_year=$year;
                }
                else if($month=="October")
                {
                    $prev_month="September";
                    $next_month="November";
                    $prev_year=$year;
                    $next_year=$year;
                }
                else if($month=="November")
                {
                    $prev_month="October";
                    $next_month="December";
                    $prev_year=$year;
                    $next_year=$year;
                }
                else if($month=="December")
                {
                    $prev_month="November";
                    $next_month="January";
                    $prev_year=$year;
                    $next_year=$year+1;
                }
                else
                {
                    $prev_month=$date[0];
                    $next_month=$date[0];
                    $prev_year=$date[2];
                    $next_year=$date[2];
                }
                
                
                
                $JSON=array();
                $JSON['data']=$data;
                $JSON['month_length']=$month_length;
                $JSON['start_day']=$start_day;
                $JSON['prev_month']=$prev_month;
                $JSON['next_month']=$next_month;
                echo json_encode($JSON);
                exit();
            }
        }
    }
}
else if($num==2)
{
    $day=(int)($_POST['day']);
    
    $query=mysql_query("SELECT information FROM calendar WHERE user_id=$_SESSION[id] LIMIT 1");
    if($query&&mysql_num_rows($query)==1)
    {
        $array=mysql_fetch_row($query);
        $data=explode('|^|*|', $array[0]);
        
        $JSON=array();
        
        for($x = 0; $x < sizeof($data); $x++)
        {
            $data[$x]=explode('|%|&|', $data[$x]);
            
            if($data[$x][0]==$year&&$data[$x][1]==$month&&$data[$x][2]==$day)
                
            $JSON['data']=explode('|@|$|', $data[$x][3]);
        }
        echo json_encode($JSON);
        exit();
    }
}

//changes calendar public viewability
else if($num==3)
{
    $visible=clean_string($_POST['change']);
    if($visible=='yes'||$visible=='no')
    {
        $query=mysql_query("UPDATE user_display SET calendar_visible='$visible' WHERE user_id=$_SESSION[id]");
        if($query)
            echo "success";
        else
        {
            echo "Something went wrong. We are working on fixiting it";
            send_mail_error("calendar_information_query.php: (3): ", mysql_error());
        }
    }
    else
        echo "Not valid option";
}
