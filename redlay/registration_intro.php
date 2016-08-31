<?php
@include('init.php');
include('universal_functions.php');
if(!isset($_SESSION['id'])||completed_registration_intro($_SESSION['id']))
{
    header("Location: http://www.redlay.com");
    exit();
}

    $query=mysql_query("SELECT * FROM user_data WHERE user_id=$_SESSION[id] LIMIT 1");
    if($query&&mysql_num_rows($query)==1)
    {
        $array=mysql_fetch_array($query);
        $birthday=explode('|^|*|', $array['user_birthday']);
        $month=$birthday[0];
//        $day=$birthday[1];
//        $year=$birthday[2];
        $bio=$array['user_bio'];
        $sex=$array['user_sex'];
        $relationship=$array['user_relationship'];
        $mood=$array['user_mood'];
        $high_school=$array['high_school'];
        $college=$array['college'];
        $country=$array['country'];
        $city=$array['user_city'];
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Welcome to redlay!</title>
        <meta name="Registration Intro" content="Last modified: 3/17/13"/>
        <?php include('required_header.php'); ?>
        <script type="text/javascript">
            function change_color()
            {
                $('.box').css('border', '5px solid rgb(220,21,0)');
                $('.box').css({'background-color': 'white', 'box-shadow': 'gray'});
                $('.registration_intro_input').css('outline-color', 'rgb(220,21,0)');
                $('.registration_intro_row').css('background-color', "white");
                $('.default').css('background-color', 'rgb(255,250,205)');
                $('.other_picture').css('border', '1px solid black');
                $('#profile_picture_inside').css('border', '1px solid black');
                
                $('.title_color').css('color', 'rgb(220,20,0)');
                $('.text_color').css('color', 'rgb(30,30,30)');
            }
            
            function show_step(current_step, new_step)
            {
                if(new_step==current_step)
                {
                    for(var x = 1; x <= 3; x++)
                    {
                        if(x!=current_step)
                            $('#step_'+x).css('left', '920px');
                    }
                }
                
                var p=$('#step_'+new_step);
                var position=p.position();
                var new_current_left=position.left;
                
                var new_future_left=($('#registration_intro_content').width()/2)-($('#step_'+new_step).width()/2);
                var old_future_left=-1*(new_current_left-new_future_left);
                
                //if continue button pressed
                if(new_step>current_step)
                {
                    $('#step_'+new_step).animate({
                        left:new_future_left
                    }, 500, function(){});
                    
                    $('#step_'+current_step).animate({
                       left:old_future_left 
                    }, 500, function(){});
                    
                    
                    
                    if(new_step!=1)
                    {
                        $('#continue_button').attr('onClick', "show_step("+new_step+", "+(new_step+1)+")");
                        $('#back_button').attr('onClick', "show_step("+(new_step)+", "+(new_step-1)+");");
                    }
                }
                
                //if back button pressed
                else if(new_step<current_step)
                {
                    $('#step_'+new_step).animate({
                        left:new_future_left
                    }, 500, function(){});
                    
                    $('#step_'+current_step).animate({
                        left: '920px'
                    }, 500, function(){});
                    
                    
                    if(new_step==1)
                    {
                        $('#continue_button').attr('onClick', "show_step("+(new_step)+", "+(current_step)+")");
                        $('#back_button').attr('onClick', "show_step("+(new_step)+", "+(new_step-1)+");");
                    }
                }
                
                //if no button is pressed
                else
                {
                    $('#step_'+current_step).css('left', new_future_left);
                }
                
                //modifies buttons
                if(new_step==1)
                {
                    $('#back_button').hide();
                    $('#continue_button').show();
                    $('#finish_button').hide();
                }
                else if(new_step==2)
                {
                    $('#back_button').attr('onClick', "show_step("+new_step+", "+(new_step-1)+")");
                    $('#back_button').show();
                    $('#continue_button').hide();
                    $('#finish_button').show();
                }
            }
            function change_birthday()
            {
                if($('#birthday_month_select').val()!=''&&$('#birthday_day_select').val()!=''&&$('#birthday_year_select').val()!='')
                {
                    $.post('settings_query.php',
                    {
                       num:3,
                        month: $('#birthday_month_select').val(),
                        day: $('#birthday_day_select').val(),
                        year: $('#birthday_year_select').val(),
                        show_year: 'yes'

                    }, function(output)
                    {
                       if(output=="Change successful!")
                       {
                           display_error(output, "good_errors");
                           $('#registration_intro_row_9').attr('class', "registration_intro_row");
                       }
                       else
                           display_error(output, 'bad_errors');
                       change_color();
                    });
                }
            }
            function fill_birthdays()
            {
                $.post("registration_intro_query.php",
                {
                    num:3
                }, function(output)
                {
                    var month=output.month;
                    var day=output.day;
                    var year=output.year;
                    
                    //puts class to default is birthday is at default
                    if(month=="")
                        $('#registration_intro_row_9').attr('class', "registration_intro_row default");
                    
                    
                    //fills in days
                    var html="";
                    for(var x = 1; x <= 31; x++)
                    {
                        if(day==x)
                            var selected="selected='selected'";
                        else
                            var selected="";
                        
                        html=html+"<option value='"+x+"' "+selected+">"+x+"</option>";
                    }
                    $('#birthday_day_select').html("<option value='' <?php if($day=='') echo "selected='selected' "; ?>>Day</option>"+html);
                    
                    //fills in years
                    var html="";
                    for(var x = 1900; x <=2000; x++)
                    {
                        if(year==x)
                            var selected="selected='selected'";
                        else
                            var selected="";
                        
                        html="<option value='"+x+"' "+selected+">"+x+"</option>"+html;
                    }
                    $('#birthday_year_select').html("<option value='' <?php if($year=='') echo "selected='selected' "; ?>>Year</option>"+html);
                    
                    change_color();
                }, "json");
            }
            function change_gender()
            {
                $.post('settings_query.php',
               {
                  num:2,
                  sex: $('#gender_options').val()
               }, function(output)
               {
                  if(output=="Change successful!")
                  {
                      display_error(output, 'good_errors');
                      $('#registration_intro_row_11').attr('class', "registration_intro_row");
                  }
                  else
                      display_error(output, 'bad_errors');
                  change_color();
               });
            }
            function change_relationship()
            {
                $.post('settings_query.php',
               {
                  num:5,
                  relationship: $('#relationship_options').val()
               }, function (output)
               {
                  if(output=='Change successful!')
                  {
                     $('#registration_intro_row_10').attr('class', "registration_intro_row");
                     display_error(output, "good_errors");
                     change_color();
                  }
                  else
                     display_error(output, "bad_errors");
                 change_color();
               });
            }
            function change_mood()
            {
                $.post('settings_query.php',
               {
                  num:6,
                  mood: $('#mood_options').val()
               }, function (output)
               {
                  if(output=='Change successful!')
                  {
                     display_error(output, 'good_errors');
                     $('#registration_intro_row_5').attr('class', "registration_intro_row");
                  }
                  else
                     display_error(output, 'bad_errors');
                 
                 change_color();
               });
            }
            function finish()
            {
                $.post('finish_registration_intro.php',
                {
                    referrer_id: $('#refer_result_box').data('user_id')
                }, function (output)
                {
                    if(output=="Success")
                        exit_registration_intro();
                    else 
                        display_error(output, 'bad_errors');
                });
            }
            function exit_registration_intro()
            {
                var speed=400;
                $('#footer').animate({
                    opacity: 0,
                    marginTop: '100px'
                }, speed, function()
                {
                    $('#registration_intro_content').animate({
                        opacity: 0,
                        marginTop: '185px'
                    }, speed, function()
                    {
                        $('#top').animate({
                            opacity: 0,
                            top: '85px'
                        }, speed, function()
                        {
                            $("body").animate({
                                backgroundColor: 'white'
                              }, 400, function()
                              {
                                setTimeout(200, window.location.replace(window.location));
                              });
                        });
                    });
                });
            }
            function fill_countries()
            {
                $.post('registration_intro_query.php',
                {
                    num:2
                }, function(output)
                {
                     var countries=output.countries;
                     for(var x= 0; x < countries.length; x++)
                     {
                         if('<?php if($country!='') echo $country; else echo "none"; ?>'==countries[x])
                         {
                             var option="<option value='"+countries[x]+"' selected='selected'>"+countries[x]+"</option>";
                             $('#registration_intro_row_8').removeAttr('class').css('background-color', 'white');
                         }
                         else
                             var option="<option value='"+countries[x]+"'>"+countries[x]+"</option>";
                         $('#registration_country_options').html($('#registration_country_options').html()+option);
                     }
                }, "json");
            }
            function change_country()
            {
                $.post('settings_query.php',
                {
                    num:14,
                    country: $('#registration_country_options').val()
                }, function(output)
                {
                    if(output=="Change Successful!")
                    {
                        display_error(output, 'good_errors');
                        $('#registration_intro_row_8').attr('class', 'registration_intro_row');
                    }
                    else
                        display_error(output, 'bad_errors');
                    change_color();
                });
            }
            function change_high_school()
            {
               $.post('settings_query.php',
               {
                  num:7,
                  high_school: $('#high_school').val()
               }, function(output)
               {
                  if(output=='Change successful!')
                  {
                     display_error(output, 'good_errors');
                     $('#registration_intro_row_6').attr('class', "registration_intro_row");
                  }
                  else
                     display_error(output, 'bad_errors');
                 change_color();
               });
            }
            function change_college()
            {
                $('#settings_error').hide();
                $.post('settings_query.php',
                {
                  num:12,
                  college: $('#college').val()
                }, function(output)
                {
                  if(output=='Change successful!')
                  {
                     display_error(output, 'good_errors');
                     $('#registration_intro_row_7').attr('class', 'registration_intro_row');
                  }
                  else
                     display_error(output, 'bad_errors');
                 change_color();
                 
                });
            }
            function user_referrer(user_id)
            {
                $('#refer_result_box').animate({
                    height:0,
                    opacity:0
                }, 500, function()
                {
                    
                    var profile_picture=$('#search_result_'+user_id).data('profile_picture');
                    var name=$('#search_result_'+user_id).data('name');
                    
                    var profile_picture="<img class='profile_picture profile_picture_status' src='"+profile_picture+"' />";
                    var name="<span class='title_color'>"+name+"</span>";

                    var button="You have selected this user as your referrer. They will be rewarded with 25 points. Thanks for joining!";
                    var body=get_post_format(profile_picture, name, button, '', '', '', '', "selected_referrer", "");
                    
                    $('#refer_result_box').html(body).data('user_id', user_id).css('opacity', '1');
                    change_color();
                });
            }
            function search_refer()
            {
                $.post('registration_intro_query.php',
                {
                    num: 4,
                    first_name: $('#first_name_input').val(),
                    last_name: $('#last_name_input').val(),
                    user_id: $('#user_id_input').val()
                }, function(output)
                {
                    var num_results=output.num_results;
                    var names=output.names;
                    var profile_pictures=output.profile_pictures;
                    var user_ids=output.user_ids;
                    
                    if(num_results!=0)
                    {
                        var html="";
                        for(var x = 0; x < num_results; x++)
                        {
                            var profile_picture="<img class='profile_picture comment_profile_picture' src='"+profile_pictures[x]+"' />";
                            var name="<span class='title_color'>"+names[x]+"</span>";

                            var button="<input class='button red_button' type='button' value='This is them' onClick=user_referrer("+user_ids[x]+"); id='search_result_"+user_ids[x]+"'/>";
                            var body=get_post_format(profile_picture, name, button, '', '', '', '', "searh_referrer_result_"+x, "");
                            html+="<tr><td>"+body+"</td></tr>";
                        }
                        $('#refer_result_box').html("<table style='width:100%;margin:0 auto;'><tbody>"+html+"</tbody></table>");
                        
                        for(var x = 0; x < num_results; x++)
                        {
                            $('#search_result_'+user_ids[x]).data({'user_id': user_ids[x], "profile_picture": profile_pictures[x], "name": names[x]});
                        }
                    }
                    else
                        $('#refer_result_box').html("<p class='text_color'>Sorry, but there are no results</p>");
                    
                    
                }, "json");
            }
        </script>
        <script type="text/javascript">
            $(window).ready(function()
            {
                change_color();
                $('#menu').hide();
                $('.load_gif').hide();
                show_step(1,1);
                fill_countries();
                fill_birthdays();
                display_groups('photo_audience_box');
                initialize();
                $('#footer').css('width', '910px');
                <?php include('required_jquery.php'); ?>
            });
        </script>
        <script type="text/javascript">
            <?php include('required_google_analytics.js'); ?>
        </script>
    </head>
    <body>
        <?php
            include('required_html.php');
        ?>
        <div id="top">
            <?php include('top.php'); ?>
        </div>
        <div id="main">
            <?php include('required_side_html.php'); ?>
            <div id="registration_intro_content" class="box" >
                <p id="registration_intro_title">Getting Started</p>
                <table style="width:100%;">
                    <tbody>
                        <tr style="height:470px;">
                            <td style="vertical-align:top;">
                                <div id="step_1" class="registration_intro_step">
                                    <p id="registration_intro_description_1" style="text-align:center;">Redlay's goal is to make your life easier!</p>
                                    <table class="registration_intro_table" id="step_1_table" >
                                       <tbody>
                                          <tr id="step_1_row_1">
                                             <td >
                                                <div id="upload_profile_picture" >
                                                    <p id="registration_intro_profile_picture_title">Profile picture</p>
                                                    <div style="text-align:center;margin:0 auto;" >
                                                       <img id="profile_picture_inside" src="<?php if(file_exists_server("https://s3.amazonaws.com/redlay.users/users/$_SESSION[id]/photos/0.jpg")) echo "https://s3.amazonaws.com/redlay.users/users/$_SESSION[id]/photos/0.jpg"; else if(file_exists_server("https://s3.amazonaws.com/redlay.users/users/$_SESSION[id]/photos/0.png")) echo "https://s3.amazonaws.com/redlay.users/users/$_SESSION[id]/photos/0.png"; else echo "https://s3.amazonaws.com/redlay.users/users/$_SESSION[id]/photos/0.gif"; ?>" alt="http://pics.redlay.com/pictures/default_profile_picture.png"/>
                                                    </div>
                                                    <form action="change_profile_picture.php" method="post" enctype="multipart/form-data" style="margin-bottom:10px;">
                                                       <input id="registration_profile_picture_settings" type="file" name="image"/>
                                                       <img class="load_gif" src="http://pics.redlay.com/pictures/load.gif" id="profile_picture_gif"/>
                                                       <input type="submit" id="registration_intro_profile_picture_submit" class="button red_button" value="Change" onClick="$('#profile_picture_gif').show();" />
                                                    </form>
                                                 </div>
                                             </td>
                                          </tr>
                                       </tbody>
                                    </table>
                                </div>
                
                                <div id="step_2" class="registration_intro_step">
                                        <p style="width:500px;"> This information will be shown on your Profile</p>
                                        <table id="step_2_table">
                                            <tbody>
                                                <tr class="registration_intro_row <?php if($high_school=='') echo "default"; ?>"  id="registration_intro_row_6">
                                                    <td class="registration_intro_text_unit"><p class="registration_intro_text">High School</p></td>
                                                    <td class="registration_intro_body_unit">
                                                        <input type="text" class="input_box" id="high_school" onFocus="input_in(this);" onBlur="input_out(this);" value="<?php echo $high_school; ?>" placeholder="High School..."/>
                                                    </td>
                                                    <td class="registration_intro_button_unit">
                                                        
                                                    </td>
                                                </tr>
                                                
                                                <tr class="registration_intro_row">
                                                    <td class="registration_intro_break" colspan="3"><hr class="break" /></td>
                                                </tr>
                                                
                                                <tr class="registration_intro_row <?php if($college=='') echo "default"; ?>"  id="registration_intro_row_7">
                                                    <td class="registration_intro_text_unit"><p class="registration_intro_text">College</p></td>
                                                    <td class="registration_intro_body_unit">
                                                        <input type="text" class="input_box" id="college" onFocus="input_in(this);" onBlur="input_out(this);" value="<?php echo $college; ?>" placeholder="College..."/>
                                                    </td>
                                                    <td class="registration_intro_button_unit">
                                                        
                                                    </td>
                                                </tr>
                                                
                                                <tr class="registration_intro_row">
                                                    <td class="registration_intro_break" colspan="3"><hr class="break" /></td>
                                                </tr>
                                                
                                                <tr class="registration_intro_row" id="registration_intro_row_8">
                                                    <td class="registration_intro_text_unit"><p class="registration_intro_text">Country</p></td>
                                                    <td class="registration_intro_body_unit">
                                                        <select id="registration_country_options" onChange="change_country();">

                                                        </select>
                                                    </td>
                                                    <td class="registration_intro_button_unit">
                                                        
                                                    </td>
                                                </tr>
                                                
                                                <tr class="registration_intro_row">
                                                    <td class="registration_intro_break" colspan="3"><hr class="break" /></td>
                                                </tr>
                                                
                                                <tr class="registration_intro_row" id="registration_intro_row_9">
                                                    <td class="registration_intro_text_unit"><p class="registration_intro_text">Birthday</p></td>
                                                    <td class="registration_intro_body_unit">
                                                        <table>
                                                            <tbody>
                                                                <tr>
                                                                    <td>
                                                                        <select id="birthday_month_select" onChange="change_birthday();">
                                                                            <option value="" <?php if($month=="") echo "selected='selected'" ?>>Month</option>
                                                                            <option value='January' <?php if($month=="January") echo "selected='selected'" ?>>January</option>
                                                                            <option value='February' <?php if($month=="February") echo "selected='selected'" ?>>February</option>
                                                                            <option value='March' <?php if($month=="March") echo "selected='selected'" ?>>March</option>
                                                                            <option value='April' <?php if($month=="April") echo "selected='selected'" ?>>April</option>
                                                                            <option value='May' <?php if($month=="May") echo "selected='selected'" ?>>May</option>
                                                                            <option value='June' <?php if($month=="June") echo "selected='selected'" ?>>June</option>
                                                                            <option value='July' <?php if($month=="July") echo "selected='selected'" ?>>July</option>
                                                                            <option value='August' <?php if($month=="August") echo "selected='selected'" ?>>August</option>
                                                                            <option value='September' <?php if($month=="September") echo "selected='selected'" ?>>September</option>
                                                                            <option value='October' <?php if($month=="October") echo "selected='selected'" ?>>October</option>
                                                                            <option value='November' <?php if($month=="November") echo "selected='selected'" ?>>November</option>
                                                                            <option value='December' <?php if($month=="December") echo "selected='selected'" ?>>December</option>
                                                                        </select>
                                                                    </td>
                                                                    <td id="birthday_day_unit">
                                                                        <select id="birthday_day_select" onChange="change_birthday();">
                                                                            
                                                                        </select>
                                                                    </td>
                                                                    <td id="birthday_year_unit">
                                                                        <select id="birthday_year_select" onChange="change_birthday();">
                                                                            
                                                                        </select>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </td>
                                                    <td class="registration_intro_button_unit">
                                                        
                                                    </td>
                                                </tr>
                                                
                                                <tr class="registration_intro_row">
                                                    <td class="registration_intro_break" colspan="3"><hr class="break" /></td>
                                                </tr>
                                                
                                                <tr class="registration_intro_row" id="registration_intro_row_10">
                                                    <td class="registration_intro_text_unit"><p class="registration_intro_text">Relationship</p></td>
                                                    <td class="registration_intro_body_unit">
                                                        <select id="relationship_options" onChange="change_relationship();">
                                                            <option value="Taken" <?php if($relationship=="Taken"){echo "selected='selected'";} ?>>Taken</option>
                                                            <option value="Single and looking" <?php if($relationship=="Single and looking"){echo "selected='selected'";} ?>>Single and looking</option>
                                                            <option value="Single" <?php if($relationship=="Single"){echo "selected='selected'";} ?>>Single</option>
                                                            <option value="Unsure" <?php if($relationship=="Unsure"){echo "selected='selected'";} ?>>Unsure</option>
                                                            <option value="Forever alone" <?php if($relationship=="Forever alone"){echo "selected='selected'";} ?>>Forever alone</option>
                                                            <option value="NA" <?php if($relationship=="NA"){echo "selected='selected'";} ?>>NA</option>
                                                        </select>
                                                    </td>
                                                    <td class="registration_intro_button_unit">
                                                        
                                                    </td>
                                                </tr>
                                                
                                                <tr class="registration_intro_row">
                                                    <td class="registration_intro_break" colspan="3"><hr class="break" /></td>
                                                </tr>
                                                
                                                <tr class="registration_intro_row" id="registration_intro_row_11">
                                                    <td class="registration_intro_text_unit"><p class="registration_intro_text">Gender</p></td>
                                                    <td class="registration_intro_body_unit">
                                                        <select id="gender_options"  onChange="change_gender();">
                                                            <option value="Male" <?php if($sex=='Male'){echo "selected='selected'";} ?>>Male</option>
                                                            <option value="Female" <?php if($sex=='Female'){echo "selected='selected'";} ?>>Female</option>
                                                            <option value="Other" <?php if($sex=='Other'){echo "selected='selected'";} ?>>Other</option>
                                                        </select>
                                                    </td>
                                                    <td class="registration_intro_button_unit">
                                                        
                                                    </td>
                                                </tr>
                                                
                                                <tr class="registration_intro_row">
                                                    <td class="registration_intro_break" colspan="3"><hr class="break" /></td>
                                                </tr>
                                                
                                                <tr class="registration_intro_row" id="registration_intro_row_11">
                                                    <td class="registration_intro_text_unit"><p class="registration_intro_text">Mood</p></td>
                                                    <td class="registration_intro_body_unit">
                                                        <select id="mood_options" onChange="change_mood();">
                                                            <option value="Happy" <?php if($mood=="Happy"){echo "selected='selected'";} ?>>Happy</option>
                                                            <option value="Angry" <?php if($mood=="Angry"){echo "selected='selected'";} ?>>Angry</option>
                                                            <option value="Sad" <?php if($mood=="Sad"){echo "selected='selected'";} ?>>Sad</option>
                                                            <option value="Ambitious" <?php if($mood=="Ambitious"){echo "selected='selected'";} ?>>Ambitious</option>
                                                            <option value="Accepted" <?php if($mood=="Accepted"){echo "selected='selected'";} ?>>Accepted</option>
                                                            <option value="Bored" <?php if($mood=="Bored"){echo "selected='selected'";} ?>>Bored</option>
                                                            <option value="Ashamed" <?php if($mood=="Ashamed"){echo "selected='selected'";} ?>>Ashamed</option>
                                                            <option value="Dorky" <?php if($mood=="Dorky"){echo "selected='selected'";} ?>>Dorky</option>
                                                            <option value="Silly" <?php if($mood=="Silly"){echo "selected='selected'";} ?>>Silly</option>
                                                            <option value="Geeky" <?php if($mood=="Geeky"){echo "selected='selected'";} ?>>Geeky</option>
                                                            <option value="Naughty" <?php if($mood=="Naughty"){echo "selected='selected'";} ?>>Naughty</option>
                                                            <option value="Accomplished" <?php if($mood=="Accomplished"){echo "selected='selected'";} ?>>Accomplished</option>
                                                            <option value="Tired" <?php if($mood=="Tired"){echo "selected='selected'";} ?>>Tired</option>
                                                            <option value="Stressed" <?php if($mood=="Stressed"){echo "selected='selected'";} ?>>Stressed</option>
                                                            <option value="Indescribable" <?php if($mood=="Indescribable"){echo "selected='selected'";} ?>>Indescribable</option>
                                                            <option value="Annoyed" <?php if($mood=="Annoyed"){echo "selected='selected'";} ?>>Annoyed</option>
                                                            <option value="Relaxed" <?php if($mood=="Relaxed"){echo "selected='selected'";} ?>>Relaxed</option>
                                                            <option value="Relieved" <?php if($mood=="Relieved"){echo "selected='selected'";} ?>>Relieved</option>
                                                            <option value="Lazy" <?php if($mood=="Lazy"){echo "selected='selected'";} ?>>Lazy</option>
                                                            <option value="Calm" <?php if($mood=="Calm"){echo "selected='selected'";} ?>>Calm</option>
                                                            <option value="Alone" <?php if($mood=="Alone"){echo "selected='selected'";} ?>>Forever alone</option>
                                                            <option value="Sick" <?php if($mood=="Sick"){echo "selected='selected'";} ?>>Sick</option>
                                                            <option value="Hyper" <?php if($mood=="Hyper"){echo "selected='selected'";} ?>>Hyper</option>
                                                            <option value="Anxious" <?php if($mood=="Anxious"){echo "selected='selected'";} ?>>Anxious</option>
                                                            <option value="Drunk" <?php if($mood=="Drunk"){echo "selected='selected'";} ?>>Drunk</option>
                                                            <option value="Disappointed" <?php if($mood=="Disappointed"){echo "selected='selected'";} ?>>Disappointed</option>
                                                        </select>
                                                    </td>
                                                    <td class="registration_intro_button_unit">
                                                        
                                                    </td>
                                                </tr>
                                                
                                                
                                                
                                            </tbody>
                                        </table>
                                    </div>
                                
                            </td>
                        </tr>
                        <tr style="height:40px;">
                            <td>
                                <table style="margin:0 auto;">
                                    <tr>
                                        <td id="back_button_unit">
                                            <input class="button" id="back_button" type="button" value="Back" onClick="show_step(1, 1);"/>
                                        </td>
                                        <td id="continue_button_unit">
                                            <input class="button" id="continue_button" type="button" value="Continue" onClick="show_step(1, 2);" />
                                        </td>
                                        <td id="finish_button_unit">
                                            <input class="button" id="finish_button" type="button" value="Finish" onClick="finish();" />
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
              <?php include('footer.php'); ?>  
        </div>
        <script type="text/javascript">
            function initialize()
            {
                $('.input_box').unbind('keypress').unbind('keydown').unbind('keyup');
                $('.input_box').keyup(function(e)
                {
                    var key = (e.keyCode ? e.keyCode : e.which);
                    if(key == '13')
                    {
                        if($(this).attr('id')=='high_school')
                            change_high_school();
                        else if($(this).attr('id')=='college')
                            change_college();
                    }
                });
            }
        </script>
    </body>
</html>