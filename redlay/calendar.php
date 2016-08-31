<?php
@include('init.php');
include('universal_functions.php');
$allowed="users";
include('security_checks.php');


//if the user's account is terminated
$year=(int)($_GET['year']);
$month=clean_string($_GET['month']);

$date=explode(' ', str_replace(',', '', get_adjusted_date(get_date(), 0)));

//checks whether month and year are valid. If not, page is redirected to current month and year
if($month==''||$year==''||!is_valid_month($month)||$year<=2000||$year>=2100)
    header("Location: http://www.redlay.com/calendar.php?month=$date[0]&&year=$date[2]");

?>
<!DOCTYPE html>
<html>
    <head>
        <title><?php echo get_user_name($_SESSION['id']); ?>'s Calendar</title>
        <?php include('required_header.php'); ?>
        <script type="text/javascript">
            function change_color()
            {
                <?php
                    $colors=get_user_display_colors($_SESSION[id]);
                    $color=$colors[0];
                    $box_background_color=$colors[1];
                    $text_color=$colors[2];
                ?>
                $('.box').css('border', '5px solid <?php echo $color; ?>');
                $('.box, .calendar_day').css({'background-color': '<?php echo $box_background_color; ?>', 'box-shadow': 'gray'});
                $('#calendar_description, .day_number, .day_information_text, #company_footer, .calendar_day_text, .calendar_body_text').css('color', '<?php echo $text_color; ?>');
                $('.other_picture').css('border', '1px solid black');
                $('#profile_picture_inside').css('border', '1px solid black');
                
                $('#calendar_content').css('background-color', '<?php echo $box_background_color; ?>');
                
                $('.text_color').css('color', '<?php echo $text_color; ?>');
                $('.title_color').css('color', '<?php echo $color; ?>');
            }
            
            
            function display_calendar_information(year, month)
            {
                $.post('calendar_information_query.php',
                {
                    num:1,
                    year: year,
                    month: month,
                    user_id: <?php echo $_SESSION['id']; ?>
                }, function(output)
                {
                    var data=output.data;
                    var start_day=output.start_day;
                    var month_length=output.month_length;
                    
                    //displays days
                    for(var x = 0; x < month_length; x++)
                    {
                        var index=start_day+x;
                        var day=x+1;
                        
                        //creates cells of tables for days
                        $('#day_'+index).html("<table style='width:100%;height:100%;'><tbody><tr class='day_top_row'><td><p class='calendar_day_text' style='width:0px;padding:0px;'>"+day+"</p></td><td style='text-align:right;width:0px;padding:0px;'><input class='button gray_button edit_button' id='day_"+index+"_edit' type='button' value='Edit' /></td></tr><tr class='day_body_row'><td id='day_body_"+index+"' class='day_body' colspan='2'></td></tr></tbody></table>").addClass('calendar_day');
                        $('#day_'+index).attr({'onmouseover': "$('#day_"+index+"_edit').show();", 'onmouseout': "$('#day_"+index+"_edit').hide();"});
                        $('#day_'+index+'_edit').attr('onClick', "edit_day("+day+", '"+month+"', "+year+", "+index+");");
                        
                        //goes through array and see if there is data for specific date
                        var has_data=-1;
                        for(var y = 0; y < data.length; y++)
                        {
                            if(data[y][0]==year&&data[y][1]==month&&data[y][2]==day)
                                has_data=y;
                        }
                        
                        //displays data if there is any
                        if(has_data!=-1)
                        {
                            //add each set of data
                            $('#day_body_'+index).html("<table style='width:100%;'><tbody id='day_"+index+"_data_table'></tbody></table>");
                            for(var y = 0; y < data[has_data][3].length; y++)
                            {
                                $('#day_'+index+'_data_table').html($('#day_'+index+'_data_table').html()+"<tr><td><p class='calendar_body_text'>"+data[has_data][3][y]+"</p></td></tr><tr><td><hr style='margin:0px'></td></tr>");
                            }
                        }
                    }
                    
                    
                    //hides the edit button
                    $('.edit_button').hide();


                    change_color();
                    //hides the loading gif after page is done loading
                    $('#loading_gif_body').hide();
                }, "json");
            }
            //calendar_day is actual day while calendar_total is calendar_day+number of days skipped at beginning of month
            function edit_day(day, month, year, index)
            {
                $('#day_'+index+'_edit').attr({'class': "button edit_button red_button", 'value': "Save", 'onClick': "save_day("+day+", '"+month+"', "+year+", "+index+");"});
                $.post('calendar_information_query.php',
                {
                    num:2,
                    year: year,
                    month: month,
                    day: day
                }, function(output)
                {
                    var data=output.data;
                    
                    //add each set of data
                    $('#day_body_'+index).html("<table style='width:100%;'><tbody id='day_"+index+"_data_table'></tbody></table>");
                    
                    var temp=0;
                    if(data!=null)
                    {
                        for(var x = 0; x < data.length; x++)
                        {
                            $('#day_'+index+'_data_table').html($('#day_'+index+'_data_table').html()+"<tr><td><input class='input_box' style='width:115px' type='text' placeholder='Event' value='"+data[x]+"' id='calendar_day_data_"+index+"_"+x+"'/></td></tr><tr><td><hr style='margin:0px'></td></tr>");
                            temp++;
                        }
                    }
                    
                    $('#day_'+index+'_data_table').html($('#day_'+index+'_data_table').html()+"<tr><td><input class='input_box' style='width:115px' type='text' placeholder='Add Event' id='calendar_day_data_"+index+"_"+temp+"' /></td></tr>");
                    $("#calendar_day_data_"+index+"_"+temp).attr('onClick', 'toggle_add_input('+index+', '+temp+');');
                    $('.input_box').attr({'onFocus': "input_in(this);", 'onBlur': "input_out(this);"});
                    
                }, "json");
            }
            function toggle_add_input(index, temp)
            {
                if($("#calendar_day_data_"+index+"_"+(temp+1)).length==0)
                {
                    for(var x = 0; x <= temp; x++)
                        $("#calendar_day_data_"+index+"_"+x).attr({'onClick': '', 'value': $("#calendar_day_data_"+index+"_"+x).val()});
                    var temp_index=temp;
                    
                    temp++;
                    $('#day_'+index+'_data_table').html($('#day_'+index+'_data_table').html()+"<tr><td><input class='input_box' style='width:115px' type='text' placeholder='Add Event' id='calendar_day_data_"+index+"_"+temp+"' /></td></tr>");
                    $("#calendar_day_data_"+index+"_"+temp).attr('onClick', 'toggle_add_input('+index+', '+temp+');');
                    $('.input_box').attr({'onFocus': "input_in(this);", 'onBlur': "input_out(this);"});
                    $("#calendar_day_data_"+index+"_"+temp_index).focus();
                }
            }
            function save_day(day, month, year, index)
            {
                //creates array of information for later
                var data=new Array();
                var temp=0;
                var num=0;
                while($('#calendar_day_data_'+index+'_'+temp).length!=0)
                {
                    if($('#calendar_day_data_'+index+'_'+temp).val()!='')
                    {
                        data[num]=$('#calendar_day_data_'+index+'_'+temp).val();
                        num++;
                    }
                    
                    temp++;
                }
                if(data[0]=='')
                    data=new Array();
                
                $.post('save_calendar_day.php',
                {
                    day: day,
                    month: month,
                    year: year,
                    data: data
                }, function(output)
                {
                    if(output=='Change successful!')
                    {
                        display_day(day, month, year, index);
                        $('#day_'+index+'_edit').attr({'class': "button edit_button gray_button", 'value': "Edit", 'onClick': "edit_day("+day+", '"+month+"', "+year+", "+index+");"});
                    }
                    else
                        $('#errors').html(output).attr('class', 'bad_errors').show();
                    
                    change_color();
                });
            }
            function display_day(day, month, year, index)
            {
                $.post('calendar_information_query.php',
                {
                    num:2,
                    year: year,
                    month: month,
                    day: day
                }, function(output)
                {
                    var data=output.data;
                    
                    //add each set of data
                    $('#day_body_'+index).html("<table style='width:100%;'><tbody id='day_"+index+"_data_table'></tbody></table>");
                    for(var y = 0; y < data.length; y++)
                    {
                        $('#day_'+index+'_data_table').html($('#day_'+index+'_data_table').html()+"<tr><td><p class='calendar_body_text'>"+data[y]+"</p></td></tr><tr><td><hr style='margin:0px'></td></tr>");
                    }
                    change_color();
                }, "json");
            }
            function change_calendar_visibility()
            {
                $.post('calendar_information_query.php',
                {
                    num:3,
                    change: $('#calendar_visibility').val()
                }, function(output)
                {
                    if(output=='success')
                        display_error("Calendar visibility changed!", 'good_errors');
                    else
                        display_error(output, 'bad_errors');
                });
            }
            $(window).ready(function()
            {
                $('#loading_gif_body').show();
                change_color();
                <?php
                    if($month==''||$year=='')
                        echo "display_calendar_information(".$date[2].", '".$date[0]."');";
                    else
                        echo "display_calendar_information($year, '$month');";
                ?>
                $('#menu').hide();
                $('#footer').css('width', '910px');
                
                <?php include('required_jquery.php'); ?>
            });
        </script>
        <script type="text/javascript">
            <?php include('required_google_analytics.js'); ?>
        </script>
    </head>
    <body>
        <?php include('required_html.php'); ?>
        <div id="top">
            <?php include('top.php'); ?>
        </div>
        <div id="main">
            <?php include('required_side_html.php'); ?>
            <div id="calendar_content" class="content">
                <p id="calendar_title" class="title_color">Calendar</p>
                <table style="margin:15px;">
                    <tbody>
                        <tr>
                            <td colspan="2">
                                <p class="text_color" style="margin:0px;font-size:16px;">Plan your day! We make your life easier!</p>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <p class="text_color" style="margin:0px;font-size:16px;">Do you wish to make your calendar publicly visible?</p>
                            </td>
                            <td>
                                <?php
                                    $query=mysql_query("SELECT calendar_visible FROM user_display WHERE user_id=$_SESSION[id] LIMIT 1");
                                    if($query&&mysql_num_rows($query)==1)
                                    {
                                        $array=mysql_fetch_row($query);
                                        $calendar_visible=$array[0];
                                    }
                                ?>
                                <select onChange="change_calendar_visibility();" id="calendar_visibility">
                                    <option value="yes" <?php if($calendar_visible=='yes') echo "selected='selected'"; ?>>Yes</option>
                                    <option value="no" <?php if($calendar_visible=='no') echo "selected='selected'"; ?>>No</option>
                                </select>
                                
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div id="loading_gif_body"><img class="load_gif" src="http://pics.redlay.com/pictures/load.gif"/></div>
                <div id="change_calendar">
                    <?php
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
                    ?>
                    <div class="change_calendar_title" id="change_calendar_year_title">
                        <img src='http://pics.redlay.com/pictures/left arrow.png' class='change_calendar_arrows' onClick="window.location.replace('http://www.redlay.com/calendar.php?month=<?php echo $month; ?>&&year=<?php echo $year-1; ?>');" />
                        <span class="change_calendar_text"><?php echo $year; ?></span>
                        <img class='change_calendar_arrows' src='http://pics.redlay.com/pictures/right arrow.png' onClick="window.location.replace('http://www.redlay.com/calendar.php?month=<?php echo $month; ?>&&year=<?php echo $year+1; ?>');" />
                    </div>
                    <div class="change_calendar_title" id="change_calendar_ymonth_title">
                        <img src='http://pics.redlay.com/pictures/left arrow.png' class='change_calendar_arrows' onClick="window.location.replace('http://www.redlay.com/calendar.php?month=<?php echo $prev_month; ?>&&year=<?php echo $prev_year; ?>');" />
                        <span  class="change_calendar_text"><?php echo $month; ?></span>
                        <img class='change_calendar_arrows' src='http://pics.redlay.com/pictures/right arrow.png'  onClick="window.location.replace('http://www.redlay.com/calendar.php?month=<?php echo $next_month; ?>&&year=<?php echo $next_year; ?>');" />
                    </div>
                </div>

                
                    <table id="calendar_table">
                        <tr class="menu_week">
                            <th class="days_of_week" id="sunday_title">Sunday</th>
                            <th class="days_of_week" id="monday_title">Monday</th>
                            <th class="days_of_week" id="tuesday_title">Tuesday</th>
                            <th class="days_of_week" id="wednesday_title">Wednesday</th>
                            <th class="days_of_week" id="thursday_title">Thursday</th>
                            <th class="days_of_week" id="friday_title">Friday</th>
                            <th class="days_of_week" id="saturday_title">Saturday</th>
                        </tr>
                        <tr class="weeks">
                            <td id="day_0"></td>
                            <td id="day_1"></td>
                            <td id="day_2"></td>
                            <td id="day_3"></td>
                            <td id="day_4"></td>
                            <td id="day_5"></td>
                            <td id="day_6"></td>
                        </tr>
                        <tr class="weeks">
                            <td id="day_7"></td>
                            <td id="day_8"></td>
                            <td id="day_9"></td>
                            <td id="day_10"></td>
                            <td id="day_11"></td>
                            <td id="day_12"></td>
                            <td id="day_13"></td>
                        </tr>
                        <tr class="weeks">
                            <td id="day_14"></td>
                            <td id="day_15"></td>
                            <td id="day_16"></td>
                            <td id="day_17"></td>
                            <td id="day_18"></td>
                            <td id="day_19"></td>
                            <td id="day_20"></td>
                        </tr>
                        <tr class="weeks">
                            <td id="day_21"></td>
                            <td id="day_22"></td>
                            <td id="day_23"></td>
                            <td id="day_24"></td>
                            <td id="day_25"></td>
                            <td id="day_26"></td>
                            <td id="day_27"></td>
                        </tr>
                        <tr class="weeks">
                            <td id="day_28"></td>
                            <td id="day_29"></td>
                            <td id="day_30"></td>
                            <td id="day_31"></td>
                            <td id="day_32"></td>
                            <td id="day_33"></td>
                            <td id="day_34"></td>
                        </tr>
                        <tr class="weeks">
                            <td id="day_35"></td>
                            <td id="day_36"></td>
                            <td id="day_37"></td>
                            <td id="day_38"></td>
                            <td id="day_39"></td>
                            <td id="day_40"></td>
                            <td id="day_41"></td>
                        </tr>
                    </table>
                
                <?php include('footer.php'); ?>
            </div>
        </div>
    </body>
</html>