<?php
@include('init.php');
include('../universal_functions.php');
if(!isset($_SESSION['id'])||completed_registration_intro($_SESSION['id']))
{
    header("Location: http://m.redlay.com");
    exit();
}


$query=mysql_query("SELECT * FROM user_data WHERE user_id=$_SESSION[id] LIMIT 1");
if($query&&mysql_num_rows($query)==1)
{
    $array=mysql_fetch_array($query);
    $birthday=explode('|^|*|', $array['user_birthday']);
    $month=$birthday[0];
    $day=$birthday[1];
    $year=$birthday[2];
    $bio=$array['user_bio'];
    $sex=$array['user_sex'];
    $relationship=$array['user_relationship'];
    $mood=$array['user_mood'];
    $high_school=$array['high_school'];
    $college=$array['college'];
    $country=$array['country'];
    $city=$array['user_city'];
    $query=mysql_query("SELECT birthday_year FROM user_display WHERE user_id=$_SESSION[id] LIMIT 1");
    if($query&&mysql_num_rows($query)==1)
    {
        $array2=mysql_fetch_row($query);
        $birthday_year=$array2[0];
    }
}



?>
<html>
    <head>
        <title></title>
        <?php include('required_header.php'); ?>
        <script type="text/javascript">
            function change_color()
            {
                <?php
                    $colors = get_user_display_colors($_SESSION['id']);
                    $color = $colors[0];
                    $box_background_color = $colors[1];
                    $text_color = $colors[2];
                ?>
                $('.box').css('border', '15px solid <?php echo $color; ?>');
                $('.box').css({'background-color': '<?php echo $box_background_color; ?>'});
                
                $('#company_footer').css('color', '<?php echo $text_color; ?>');
                $('.header_text').css('color', '<?php echo $color; ?>');
                
            }
            function hide_steps()
            {
                $('#step_1').hide();
                $('#step_2').hide();
                $('#step_3').hide();
            }
            function show_step_1()
            {
                hide_steps();
                $('#step_1').show();
            }
            function show_step_2()
            {
                hide_steps();
                $('#step_2').show();
            }
            function show_step_3()
            {
                hide_steps();
                $('#step_3').show();
                $('#redlay_gold_table').css({'font-size': '35px'});
            }
            function fill_days()
            {
                for(var x = 1; x <= 31; x++)
                {
                    if(x!=<?php if($day=='') echo "0"; else echo $day; ?>)
                        $('#registration_intro_day').html($('#registration_intro_day').html()+"<option value='"+x+"'>"+x+"</option>");
                    else
                        $('#registration_intro_day').html($('#registration_intro_day').html()+"<option value='"+x+"' selected='selected'>"+x+"</option>");
                }
            }
            function fill_years()
            {
                for(var x = 2002; x >= 1940; x--)
                {
                    if(x!=<?php if($year=='') echo "0"; else echo $year; ?>)
                        $('#registration_intro_year').html($('#registration_intro_year').html()+"<option value='"+x+"'>"+x+"</option>");
                    else
                        $('#registration_intro_year').html($('#registration_intro_year').html()+"<option value='"+x+"' selected='selected'>"+x+"</option>");
                }
            }
            function fill_countries()
            {
                $.post('main_access.php',
                {
                    access:34,
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
            
            function save_information()
            {
                var errors='';
                //changes birthday
                if($('#birthday_checkbox').attr('src')=='./pictures/gray_checkbox_checked.png')
                    var string="yes";
                else
                    var string="no";
                $.post('main_access.php',
                {
                    access:35,
                    num:3,
                    month: $('#registration_intro_month').val(),
                    day: $('#registration_intro_day').val(),
                    year: $('#registration_intro_year').val(),
                    show_year: string

                }, function(output)
                {
                   if(output=="Change successful!")
                   {
                       $('#row_0').removeClass('default').css('background-color', 'white');
                       $('#row_1').removeClass('default').css('background-color', 'white');
                       $('#row_2').removeClass('default').css('background-color', 'white');
                       $('#row_3').removeClass('default').css('background-color', 'white');
                   }
                   else
                       errors=errors+' | '+output;
                });


                //changes bio
                $.post('main_access.php',
                {
                    access:35,
                    num:4,
                    new_bio: $('#bio_input').val()
                },
                function(output)
                {
                   if(output=='Change successful!')
                      $('#row_4').removeClass('default').css('background-color', 'white');
                   else
                       errors=errors+' | '+output;
                });
                //changes gender
                $.post('main_access.php',
                {
                    access:35,
                    num:2,
                    sex: $('#sex_options').val()
                }, function(output)
                {
                   if(output=="Change successful!")
                         $('#row_5').removeClass('default').css('background-color', 'white');
                   else
                       errors=errors+' | '+output;
                });
                //changes relationship
                $.post('main_access.php',
                {
                    access:35,
                   num:5,
                   relationship: $('#relationship_options').val()
                }, function (output)
                {
                   if(output=='Change successful!')
                      $('#row_6').removeClass('default').css('background-color', 'white');
                   else
                       errors=errors+' | '+output;
                });     
                //changes mood
                $.post('main_access.php',
                {
                    access:35,
                    num:6,
                    mood: $('#mood_options').val()
                }, function (output)
                {
                   if(output=='Change successful!')
                      $('#row_7').removeClass('default').css('background-color', 'white');
                   else
                       errors=errors+' | '+output;
                });
                //changes high school
                $.post('main_access.php',
                {
                    access:35,
                    num:7,
                    high_school: $('#high_school').val()
                }, function(output)
                {
                   if(output=='Change successful!')
                      $('#row_8').removeClass('default').css('background-color', 'white');
                   else
                       errors=errors+' | '+output;
                });
                //changes college
                $.post('main_access.php',
                {
                    access:35,
                    num:12,
                    college: $('#college').val()
                }, function(output)
                {
                    if(output=='Change successful!')
                      $('#row_9').removeClass('default').css('background-color', 'white');
                   else
                       errors=errors+' | '+output;
                });
                //changes country
                $.post('main_access.php',
                {
                    access:35,
                    num:14,
                    country: $('#registration_country_options').val()
                }, function(output)
                {
                    if(output=='Change successful!')
                      $('#row_10').removeClass('default').css('background-color', 'white');
                   else
                       errors=errors+' | '+output;
                });
                        
                if(errors=='')
                {
                    display_error('Change successful!', 'good_errors');
                    finish();
                }
                else
                    display_error(errors, 'bad_errors');
                    
                $('#bottom_button_unit_1').html("<input class='blue_button' type='button' onClick='show_step_2();' value='Continue'/>");
            }
            function finish()
            {
                $.post('main_access.php', 
                {
                    access:37
                }, function (output)
                {
                    exit_registration_intro(); 
                });
            }
            $(document).ready(function()
            {
                $('#menu').hide();
                fill_days();
                fill_years();
                fill_countries();
                show_step_1();
                <?php include('required_jquery.php'); ?>
            });
        </script>
        <script type="text/javascript">
            <?php include('../required_google_analytics.js'); ?>
        </script>
    </head>
    <body>
        <div id="top">
            <?php include('top.php'); ?>
        </div>
        <?php include('required_html.php'); ?>
        <div id="main">
            <div id="content" class="box">
                <p style="color:<?php echo $color; ?>;font-size:35px;text-align:center;text-decoration:underline;" >Get started!</p>
                <div id="step_1" class="step">
                    <table style="width:100%;padding:25px;">
                        <tbody>
                            <tr id="row_0">
                                <td>
                                    <span class="header_text" style="font-size:25px;">Month</span>
                                </td>
                                <td style="text-align:right;">
                                    <select id="registration_intro_month"  style="font-size:25px;">
                                        <option value="0">Month:</option>
                                        <option value="January" <?php if($month=='January'){echo "selected='selected'";} ?>>January</option>
                                        <option value="February" <?php if($month=='February'){echo "selected='selected'";} ?>>February</option>
                                        <option value="March" <?php if($month=='March'){echo "selected='selected'";} ?>>March</option>
                                        <option value="April" <?php if($month=='April'){echo "selected='selected'";} ?>>April</option>
                                        <option value="May" <?php if($month=='May'){echo "selected='selected'";} ?>>May</option>
                                        <option value="June" <?php if($month=='June'){echo "selected='selected'";} ?>>June</option>
                                        <option value="July" <?php if($month=='July'){echo "selected='selected'";} ?>>July</option>
                                        <option value="August" <?php if($month=='August'){echo "selected='selected'";} ?>>August</option>
                                        <option value="September" <?php if($month=='September'){echo "selected='selected'";} ?>>September</option>
                                        <option value="October" <?php if($month=='October'){echo "selected='selected'";} ?>>October</option>
                                        <option value="November" <?php if($month=='November'){echo "selected='selected'";} ?>>November</option>
                                        <option value="December" <?php if($month=='December'){echo "selected='selected'";} ?>>December</option>
                                    </select>
                                </td>
                            </tr>
                            <tr id="row_1">
                                <td>
                                    <span class="header_text"  style="font-size:25px;">Day</span>
                                </td>
                                <td style="text-align:right;">
                                    <select id="registration_intro_day"  style="font-size:25px;">
                                        <option value="0">Day:</option>
                                    </select>
                                </td>
                            </tr>
                            <tr id="row_2">
                                <td>
                                    <span class="header_text"  style="font-size:25px;">Year</span>
                                </td>
                                <td style="text-align:right;">
                                    <select id="registration_intro_year" style="font-size:25px;">
                                        <option value="0">Year:</option>
                                    </select>
                                </td>
                            </tr>
                            <tr id="row_3">
                                <td>
                                    <span class="header_text"  style="font-size:25px;">Show on profile</span>
                                </td>
                                <td style="text-align:right;">
                                    <img class="checkbox" src="<?php  if($birthday_year=='yes') echo "./pictures/gray_checkbox_checked.png"; else echo "./pictures/gray_checkbox.png";  ?>"  onClick="toggle_checkbox('#birthday_checkbox')" id="birthday_checkbox"/>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <hr class="break"/>
                                </td>
                            </tr>
                            <tr id="row_4">
                                <td>
                                    <span class="header_text" style="font-size:25px;">Bio</span>
                                </td>
                                <td style="text-align:right;">
                                    <textarea class="input_box" id="bio_input" onFocus="input_in(this);" onblur="input_out(this);" placeholder="Type anything about you..." style="width:500px;height:200px;" ><?php echo $bio; ?></textarea>
                                </td>    
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <hr class="break"/>
                                </td>
                            </tr>
                            <tr id="row_5">
                                <td>
                                    <span class="header_text" style="font-size:25px;">Gender</span>
                                </td>
                                <td style="text-align:right;">
                                    <select id="sex_options"  style="font-size:25px;">
                                        <option value="0">Gender:</option>
                                        <option value="Male" <?php if($sex=='Male'){echo "selected='selected'";} ?>>Male</option>
                                        <option value="Female" <?php if($sex=='Female'){echo "selected='selected'";} ?>>Female</option>
                                        <option value="Other" <?php if($sex=='Other'){echo "selected='selected'";} ?>>Other</option>
                                    </select>
                                </td>    
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <hr class="break"/>
                                </td>
                            </tr>
                            <tr id="row_6">
                                <td>
                                    <span class="header_text" style="font-size:25px;">Relationship</span>
                                </td>
                                <td style="text-align:right;">
                                    <select id="relationship_options" style="font-size:25px;">
                                        <option value="0">Relationship status: </option>
                                        <option value="Taken" <?php if($relationship=="Taken"){echo "selected='selected'";} ?>>Taken</option>
                                        <option value="Single" <?php if($relationship=="Single"){echo "selected='selected'";} ?>>Single</option>
                                        <option value="Unsure" <?php if($relationship=="Unsure"){echo "selected='selected'";} ?>>Unsure</option>
                                        <option value="Forever alone" <?php if($relationship=="Forever alone"){echo "selected='selected'";} ?>>Forever alone</option>
                                        <option value="NA" <?php if($relationship=="NA"){echo "selected='selected'";} ?>>NA</option>
                                    </select>
                                </td>    
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <hr class="break"/>
                                </td>
                            </tr>
                            <tr id="row_7">
                                <td>
                                    <span class="header_text" style="font-size:25px;">Mood</span>
                                </td>
                                <td style="text-align:right;">
                                    <select id="mood_options" style="font-size:25px;">
                                        <option value="blankMood">Mood: </option>
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
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <hr class="break"/>
                                </td>
                            </tr>
                            <tr id="row_8">
                                <td>
                                    <span class="header_text" style="font-size:25px;">High School</span>
                                </td>
                                <td style="text-align:right;">
                                    <input class="input_box" type="text" value="<?php echo $high_school ?>" placeholder="High School" id="high_school" onFocus="input_in(this);" onBlur="input_out(this);"/>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <hr class="break"/>
                                </td>
                            </tr>
                            <tr id="row_9">
                                <td>
                                    <span class="header_text" style="font-size:25px;">College</span>
                                </td>
                                <td style="text-align:right;">
                                    <input class="input_box" type="text" value="<?php echo $college ?>" placeholder="College" id="college" onFocus="input_in(this);" onBlur="input_out(this);"/>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <hr class="break"/>
                                </td>
                            </tr>
                            <tr id="row_10">
                                <td>
                                    <span class="header_text" style="font-size:25px;">Country</span>
                                </td>
                                <td style="text-align:right;">
                                    <select id="registration_country_options" style="font-size:25px;">
                                        
                                    </select>
                                </td>
                            </tr>
                            
                            
                            <tr>
                                <td colspan="2" style="text-align:center;">
                                    <input class='blue_button' type='button' value='Finish' onClick='save_information();'/>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php include('footer.php'); ?>
        </div>
    </body>
</html>