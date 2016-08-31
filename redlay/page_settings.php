<?php
@include('init.php');
include('universal_functions.php');
$allowed="pages"
include('security_checks.php');

$query=mysql_query("SELECT name, created, description, location, website, blocked_users FROM page_data WHERE page_id=$_SESSION[page_id] LIMIT 1");
if($query&&mysql_num_rows($query)==1)
{
    $array=mysql_fetch_array($query);

    $name=$array[0];
    $description=$array[2];
    $blocked_users=$array[5];

    $query=mysql_query("SELECT display_colors, fan_title, information_title, post_title, background_fixed, main_video FROM page_display WHERE page_id=$_SESSION[page_id] LIMIT 1");
    if($query&&mysql_num_rows($query)==1)
    {
        $array=mysql_fetch_array($query);
        $display_colors=explode('|^|*|', $array[0]);
        $fan_title=$array[1];
        $information_title=$array[2];
        $post_title=$array[3];
        $background_fixed=$array[4];
        $main_video=$array[5];
        
        $query=mysql_query("SELECT type, type_other FROM pages WHERE id=$_SESSION[page_id] LIMIT 1");
        if($query&&mysql_num_rows($query)==1)
        {
            $array=mysql_fetch_row($query);
            $type=$array[0];
            $other_type=$array[1];
        }
    }
}

?>

<!DOCTYPE html>
<html>
    <head>
        <title>Settings</title>
        <?php include('required_header.php'); ?>
        <script type="text/javascript">
            function change_color()
            {
                <?php
                        $colors=get_page_display_colors($_SESSION['page_id']);
                        $border_color=$colors[0];
                        $box_background_color=$colors[1];
                        $text_color=$colors[2];
                ?>
                $('.box').css('border', '5px solid <?php echo $border_color; ?>');
                $('.box').css('background-color', '<?php echo $box_background_color; ?>');

                $('body').css('color', '<?php echo $text_color;  ?>');
                $('#settings_title, .group_item').css('color', '<?php echo $border_color; ?>')
                $('.settings_input').css('outline-color', '<?php echo $border_color; ?>');
                $('.settings_text').css('color', '<?php echo $border_color; ?>');
                $('#change_background_picture_input').css('color', '<?php echo $border_color; ?>');
                $('.settings_menu').css('color', '<?php echo $border_color; ?>');
            }
            $(document).ready(function()
            {
                <?php $path="./users/pages/$_SESSION[page_id]/background.jpg"; if(file_exists($path)&&$colors[5]=="yes") echo "$('body').css('background-attachment', 'fixed');"; ?>

                //changes the profile picture upload button to normal if file is selected
                $('#profile_picture_settings').change(function()
                {
                    $('#submit_profile_picture').removeAttr('disabled').removeClass('settings_button_disabled').addClass('red_button settings_button');
                });

                //changes the background image upload button to normal if file is selected
                $('#change_background_fixed_input').change(function()
                {
                    if($('#change_background_fixed_input').is(":checked"))
                    {
                        $('body').css('background-attachment', 'fixed');
                        var string='yes';
                    }
                    else
                    {
                        $('body').css('background-attachment', 'scroll');
                        var string='no';
                    }
                    $.post('change_background_fixed.php',
                    {
                        value: string
                    }, function(output)
                    {
                        $('#errors').html(output);
                        if(output=='Change successful!')
                            $('#errors').addClass('good_errors').show();
                        else
                            $('#errors').addClass('bad_errors').show();
                    });
                });
            });

            function change_post_title()
            {
                $('#error').hide();
                var input=$('#change_post_title').val();
                $.post('change_post_title.php',
                {
                    text: input
                }, function (output)
                {
                    $('#error').html(output).slideDown('fast');
                });
            }

            function change_information_title()
            {
                $('#error').hide();
                var input=$('#change_information_title').val();
                $.post('change_information_title.php',
                {
                    text: input
                }, function (output)
                {
                    $('#error').html(output).slideDown('fast');
                });
            }
            function change_name()
            {
                var first=$('#change_first_name_input').val();
                var second=$('#change_last_name_input').val();
                $.post('change_name.php',
                {
                    first_name: first,
                    last_name: second
                }, function (output)
                {
                    $('#error').html(output).slideDown('fast');
                });
            }

            // Prevent "event.layerX and event.layerY are broken and deprecated in WebKit. They will be removed from the engine in the near future."
            // in latest Chrome builds.
            (function () {
                // remove layerX and layerY
                var all = $.event.props,
                    len = all.length,
                    res = [];
                while (len--) {
                    var el = all[len];
                    if (el != 'layerX' && el != 'layerY') res.push(el);
                }
                $.event.props = res;
            } ());


            function default_colors()
            {
                change_border_color('red');
                change_box_background_color('white');
                change_main_text_color('black');
            }
            function change_border_color(color)
            {
                $.post('change_color_page.php',
                {
                    num:1,
                    border_color: color
                }, function(output)
                {
                    $('.box').css('border', '5px solid rgb('+output+")");
                    $('#settings_title, .group_item, .settings_text, #change_background_picture_input, .settings_menu').css('color', 'rgb('+output+')');
                });
            }
            function change_box_background_color(color)
            {
                $.post('change_color_page.php',
                {
                    num: 3,
                    background_color: color
                }, function (output)
                {
                    $('.box').css('background-color', "rgb("+output+")");
                });
            }

            function change_main_text_color(color)
            {
                $.post('change_color_page.php',
                {
                    num: 4,
                    color: color
                }, function (output)
                {
                    $('body').css('color', "rgb("+output+")");
                });
            }

            function fill_option_boxes()
            {
                var friends=new Array();
                var colors=new Array();
                var friend_names=new Array();
                for(var x = 0; x < colors.length; x++)
                {
                    if(x==0)
                        var string='red';
                    else if(x==1)
                        string='orange';
                    else if(x==2)
                        string='yellow';
                    else if(x==3)
                        string='green';
                    else if(x==4)
                        string='blue';
                    else if(x==5)
                        string='purple';
                    else if(x==6)
                        string='pink';
                    for(var y = 0; y < friends.length; y++)
                    {
                        if(y==0)
                            $('#highlight_user_colors_options_'+string).html("<option value=''>None</option>");
                        if(colors[x]==friends[y])
                            var option="<option value="+friends[y]+" id='option_"+string+"_"+y+"' selected='selected'>"+friend_names[y]+"</option>";
                        else
                            var option="<option value="+friends[y]+" id='option_"+string+"_"+y+"'>"+friend_names[y]+"</option>";
                        $('#highlight_user_colors_options_'+string).html($('#highlight_user_colors_options_'+string).html()+option);
                    }
//                    if(x==0)
//                        var option="<option value=''>None</option><option value="+friends[x]+" id='option_"+x+"'>"+friend_names[x]+"</option>";
//                    else
//                        var option="<option value="+friends[x]+" id='option_"+x+"'>"+friend_names[x]+"</option>";
//                    $('.highlight_user_colors_options').html($('.highlight_user_colors_options').html()+option);
                }
            }
            function highlight_color_change(num)
            {
                if(num==0)
                    var string='red';
                else if(num==1)
                    var string='orange';
                else if(num==2)
                    var string='yellow';
                else if(num==3)
                    var string='green';
                else if(num==4)
                    var string='blue';
                else if(num==5)
                    var string='purple';
                else if(num==6)
                    var string='pink';
                var value=$('#highlight_user_colors_options_'+string).val();
                $('#highlight_color_picture_'+string).attr('src', "./users/images/"+value+"/0.jpg");
                $.post('change_highlight_user_color.php',
                {
                    user_id: value,
                    number: num
                }, function (output)
                {

                });
            }
            function display_blocked_users()
            {
                var users=new Array();
                var user_names=new Array();
                <?php
                    $query=mysql_query("SELECT blocked_users FROM user_data WHERE user_id=$_SESSION[id] LIMIT 1");
                    if($query&&mysql_num_rows($query)==1)
                    {
                        $array=mysql_fetch_row($query);
                        $blocked_users=explode('|^|*|', $array[0]);
                        if($array[0]!='')
                        {
                            for($x = 0; $x < sizeof($blocked_users); $x++)
                            {
                                echo "users[$x]=$blocked_users[$x];";
                                echo "user_names[$x]='".get_user_name($blocked_users[$x])."';";
                            }
                        }
                    }
                ?>
                for(var x = 0; x < users.length; x++)
                {
                    var profile_picture="<div class='blocked_user' id='blocked_user_"+x+"'><a href='http://www.redlay.com/profile.php?user_id="+users[x]+"'><img class='profile_picture_status profile_picture blocked_user_profile_picture' id='blocked_user_profile_picture_"+x+"' src='./users/images/"+users[x]+"/0.jpg'  onmouseover=picture_over(this); onmouseout=picture_out(this); ></a>";
                    var name="<div class='user_name_body'><a class='user_name_link' href='http://www.redlay.com/profile.php?user_id="+users[x]+"' ><p class='user_name' id='blocked_user_name_"+x+"' onmouseover=name_over('#blocked_user_name_"+x+"'); onmouseout=name_out('#blocked_user_name_"+x+"'); >"+user_names[x]+"</p></a></div>";
                    var unblocked_button="<input type='button' value='Unblock' id='unblock_button_"+x+"' class='red_button' onClick='unblock_user("+users[x]+");' /></div>";
                    $('#blocked_users_list').html($('#blocked_users_list').html()+profile_picture+name+unblocked_button);
                    $('.profile_picture').css('position', 'relative');
                }
            }
            function change_password()
            {
               $('#errors').hide();
               $.post('page_settings_query.php',
               {
                  num:1,
                  current_password: $('#current_password').val(),
                  new_password: $('#new_password').val(),
                  confirm_new_password: $('#confirm_new_password').val()
               }, function(output)
               {
                  if(output=='Password change successful!')
                        $('#errors').html(output).attr('class', 'good_errors').show();
                  else
                        $('#errors').html(output).attr('class', 'bad_errors').show();
               });
            }
            function unblock_user(ID)
            {
                $.post('unblock_user_user.php',
                {
                    user_id: ID
                }, function (output)
                {
                    $('#errors').html(output);
                    if(output=='User unblocked')
                        $('#errors').addClass('good_errors').show();
                    else
                        $('#errors').addClass('bad_errors').show();
                });

            }
            function display_settings_information()
            {
                $('#settings_display').hide();
                $('#settings_other').hide();
                $('#settings_images').hide();
                $('#settings_email').hide();
                $('#settings_privacy').hide();
                $('#settings_information').show();
            }
            function display_settings_display()
            {
                $('#settings_display').show();
                $('#settings_other').hide();
                $('#settings_images').hide();
                $('#settings_email').hide();
                $('#settings_privacy').hide();
                $('#settings_information').hide();
            }
            function display_settings_images()
            {
                $('#settings_display').hide();
                $('#settings_other').hide();
                $('#settings_images').show();
                $('#settings_email').hide();
                $('#settings_privacy').hide();
                $('#settings_information').hide();
            }
            function display_settings_other()
            {
                $('#settings_display').hide();
                $('#settings_other').show();
                $('#settings_images').hide();
                $('#settings_email').hide();
                $('#settings_privacy').hide();
                $('#settings_information').hide();
            }
            function show_blocked_users()
            {
                if($('#blocked_users_list').is(":visible"))
                    $('#blocked_users_list').hide();
                else
                    $('#blocked_users_list').show();
            }
            function change_description()
            {
               $('#errors').hide();
               $.post('page_settings_query.php',
               {
                  num:4,
                  description: $('#change_bio_text').val()
               },
               function(output)
               {
                  $('#errors').html(output);
                  if(output=='Change successful!')
                     $('#errors').attr('class', 'good_errors').show();
                  else
                     $('#errors').attr('class', 'bad_errors').show();
               });
            }


            function change_created_date()
            {
                $('#errors').hide();
                $.post('page_settings_query.php',
                {
                    num:3,
                    month: $('#month').val(),
                    day: $('#day').val(),
                    year: $('#year').val()
                }, function(output)
                {
                   $('#errors').html(output);
                   if(output=="Change successful!")
                       $('#errors').addClass('good_errors').show();
                   else
                       $('#errors').addClass('bad_errors').show();
                });
            }
            function terminate_account()
            {
                $.post('terminate_page.php',
                {
                    confirmation: 'yes'
                }, function(output)
                {
                    if(output=='')
                        window.location.replace('http://www.redlay.com/account_terminated.php');
                    else
                        window.location.replace('http://www.redlay.com/index.php');
                });
            }
            
            
            function select_colors()
            {
                var colors=$('#default_colors_select').val().split(',');
                
                $('#red').slider('value', colors[0]);
                $('#green').slider('value', colors[1]);
                $('#blue').slider('value', colors[2]);
            }
            function dynamic_border_color()
            {
		var red = $( "#red" ).slider( "value" );
                var green = $( "#green" ).slider( "value" );
                var blue = $( "#blue" ).slider( "value" );
                var string="rgb("+red+", "+green+", "+blue+")";
                
		$("#preview").css({"background-color": string});
                
                $('.box').css('border', '5px solid '+string);
                $('#settings_title, .group_item, .settings_text, #change_background_picture_input, .settings_menu').css('color', string);
            }
            function dynamic_background_color()
            {
		var red = $( "#red" ).slider( "value" );
                var green = $( "#green" ).slider( "value" );
                var blue = $( "#blue" ).slider( "value" );
                var string="rgb("+red+", "+green+", "+blue+")";
                
		$("#preview").css({"background-color": string});
                
                $('.box').css('background-color', string);
            }
            function dynamic_text_color()
            {
		var red = $( "#red" ).slider( "value" );
                var green = $( "#green" ).slider( "value" );
                var blue = $( "#blue" ).slider( "value" );
                var string="rgb("+red+", "+green+", "+blue+")";
                
		$("#preview").css({"background-color": string});
                
                $('body').css('color', string);
            }
            function change_border_color()
            {
                var red = $( "#red" ).slider( "value" );
                var green = $( "#green" ).slider( "value" );
                var blue = $( "#blue" ).slider( "value" );
                var string=red+", "+green+", "+blue;
                
                $.post('change_page_color.php',
                {
                    num:1,
                    border_color: string
                }, function(output)
                {
                    close_alert_box();
                });
            }
            function change_background_color()
            {
                var red = $( "#red" ).slider( "value" );
                var green = $( "#green" ).slider( "value" );
                var blue = $( "#blue" ).slider( "value" );
                var string=red+", "+green+", "+blue;
                
                $.post('change_page_color.php',
                {
                    num: 2,
                    background_color: string
                }, function (output)
                {
                    close_alert_box();
                });
            }
            function change_text_color()
            {
                var red = $( "#red" ).slider( "value" );
                var green = $( "#green" ).slider( "value" );
                var blue = $( "#blue" ).slider( "value" );
                var string=red+", "+green+", "+blue;
                
                $.post('change_page_color.php',
                {
                    num: 3,
                    text_color: string
                }, function (output)
                {
                    close_alert_box();
                });
            }
            function create_sliders(num)
            {
                if(num==1)
                {
                    $.post('page_settings_query.php',
                    {
                        num:10,
                        type: 'border_color'
                    }, function(output)
                    {
                        var colors=output.colors;
                        $( "#red, #green, #blue" ).slider({
                            orientation: "horizontal",
                            range: "min",
                            max: 255,
                            value: 127,
                            slide: dynamic_border_color,
                            change: dynamic_border_color
                        });
                        $( "#red" ).slider( "value", colors[0] );
                        $( "#green" ).slider( "value", colors[1] );
                        $( "#blue" ).slider( "value", colors[2] );
                    }, "json");
                }
                else if(num==2)
                {
                    $.post('page_settings_query.php',
                    {
                        num:10,
                        type: 'background_color'
                    }, function(output)
                    {
                        var colors=output.colors;
                        $( "#red, #green, #blue" ).slider({
                            orientation: "horizontal",
                            range: "min",
                            max: 255,
                            value: 127,
                            slide: dynamic_background_color,
                            change: dynamic_background_color
                        });
                        $( "#red" ).slider( "value", colors[0] );
                        $( "#green" ).slider( "value", colors[1] );
                        $( "#blue" ).slider( "value", colors[2] );
                    }, "json");
                }    
                else if(num==3)
                {
                    $.post('page_settings_query.php',
                    {
                        num:10,
                        type: 'text_color'
                    }, function(output)
                    {
                        var colors=output.colors;
                        $( "#red, #green, #blue" ).slider({
                            orientation: "horizontal",
                            range: "min",
                            max: 255,
                            value: 127,
                            slide: dynamic_text_color,
                            change: dynamic_text_color
                        });
                        $( "#red" ).slider( "value", colors[0] );
                        $( "#green" ).slider( "value", colors[1] );
                        $( "#blue" ).slider( "value", colors[2] );
                    }, "json");
                }
            }

            function display_color_wheel(num)
            {
                $('.alert_box').css('opacity', 1).show().draggable();
                    $('.alert_box_inside').html("<table><tbody><tr><td>        <table><tbody><tr><td><div id='red'></div></td></tr><tr><td><div id='green'></div></td></tr><tr><td><div id='blue'></div></td></tr><tr><td></td></tr></tbody></table></td>     <td><table><tbody><tr><td><p class='settings_text'>Default colors: </p></td></tr><tr><td><select id='default_colors_select' onChange='select_colors();'></select></td></tr></tbody></table></td>     </tr><tr><td><div id='preview'></div></td><td><input class='red_button' id='save_color_button' type='button' value='Change' onClick=''/><input class='gray_button' value='Cancel' onClick='{change_color();close_alert_box();}' type='button'/></td></tr></tbody></table>");
                    
                    $('#default_colors_select').html($('#default_colors_select').html()+"<option value='0,0,0'>Colors: </option>");
                    $('#default_colors_select').html($('#default_colors_select').html()+"<option value='220,20,0'>Red</option>");
                    $('#default_colors_select').html($('#default_colors_select').html()+"<option value='233,164,0'>Orange</option>");
                    $('#default_colors_select').html($('#default_colors_select').html()+"<option value='233,219,0'>Yellow</option>");
                    $('#default_colors_select').html($('#default_colors_select').html()+"<option value='50,196,39'>Green</option>");
                    $('#default_colors_select').html($('#default_colors_select').html()+"<option value='0,35,214'>Blue</option>");
                    $('#default_colors_select').html($('#default_colors_select').html()+"<option value='209,77,247'>Purple</option>");
                    $('#default_colors_select').html($('#default_colors_select').html()+"<option value='251,158,248'>Pink</option>");
                    $('#default_colors_select').html($('#default_colors_select').html()+"<option value='200,130,0'>Brown</option>");
                    $('#default_colors_select').html($('#default_colors_select').html()+"<option value='30,30,30'>Black</option>");
                    $('#default_colors_select').html($('#default_colors_select').html()+"<option value='255,255,255'>White</option>");
                    
                    if(num==1)
                        $('#save_color_button').attr('onClick', '{change_border_color();}');
                    else if(num==2)
                        $('#save_color_button').attr('onClick', '{change_background_color();}');
                    else if(num==3)
                        $('#save_color_button').attr('onClick', '{change_text_color();}');
                    
                create_sliders(num);
            }
            function change_company_website()
            {
                $.post('page_settings_query.php',
                {
                    num:9,
                    content:$('#company_website').val(),
                    type:'company_website'
                }, function(output)
                {
                    display_settings_errors(output);
                });
            }
            function change_company_founded_date()
            {
                $.post('page_settings_query.php',
                {
                    num:9,
                    content:$('#company_founded_input').val(),
                    type:'company_founded'
                }, function(output)
                {
                    display_settings_errors(output);
                });
            }
            function change_company_CEO()
            {
                var content=new Array();
                content[0]=$('#company_CEO_name').val();
                content[1]=$('#company_CEO_id').val();
                $.post('page_settings_query.php',
                {
                    num:9,
                    content:content,
                    type:'company_CEO'
                }, function(output)
                {
                    display_settings_errors(output);
                });
            }
            function change_company_CFO()
            {
                var content=new Array();
                content[0]=$('#company_CFO_name').val();
                content[1]=$('#company_CFO_id').val();
                $.post('page_settings_query.php',
                {
                    num:9,
                    content:content,
                    type:'company_CFO'
                }, function(output)
                {
                    display_settings_errors(output);
                });
            }
            function change_company_president()
            {
                var content=new Array();
                content[0]=$('#company_president_name').val();
                content[1]=$('#company_president_id').val();
                $.post('page_settings_query.php',
                {
                    num:9,
                    content:content,
                    type:'company_president'
                }, function(output)
                {
                    display_settings_errors(output);
                });
            }
            function change_company_vice_president()
            {
                var content=new Array();
                content[0]=$('#company_vice_president_name').val();
                content[1]=$('#company_vice_president_id').val();
                $.post('page_settings_query.php',
                {
                    num:9,
                    content:content,
                    type:'company_vice_president'
                }, function(output)
                {
                    display_settings_errors(output);
                });
            }
            function change_company_num_employees()
            {
                $.post('page_settings_query.php',
                {
                    num:9,
                    content:$('#company_num_employees').val(),
                    type:'company_num_employees'
                }, function(output)
                {
                    display_settings_errors(output);
                });
            }
            function change_company_headquarters()
            {
                $.post('page_settings_query.php',
                {
                    num:9,
                    content:$('#company_headquarters').val(),
                    type:'company_headquarters'
                }, function(output)
                {
                    display_settings_errors(output);
                });
            }
            function display_settings_errors(output)
            {
                $('#errors').html(output);
                    if(output!='Something went wrong. We are working on fixing it')
                        $('#errors').attr('class', 'good_errors').show();
                    else
                        $('#errors').attr('class', 'bad_errors').show();
            }
            function change_other_website()
            {
                $.post('page_settings_query.php',
                {
                    num:9,
                    content: $('#other_website_input').val(),
                    type: 'other_website'
                }, function(output)
                {
                    display_settings_errors(output);
                });
            }
            function change_other_created()
            {
                $.post('page_settings_query.php',
                {
                    num:9,
                    content:$('#other_created').val(),
                    type: 'other_created'
                }, function(output)
                {
                    display_settings_errors(output);
                });
            }
            function change_other_website()
            {
                $.post('page_settings_query.php',
                {
                    num:9,
                    content:$('#other_website_input').val(),
                    type: 'other_website'
                }, function(output)
                {
                    display_settings_errors(output);
                });
            }
            function change_place_location()
            {
                $.post('page_settings_query.php',
                {
                    num:9,
                    content:$('#place_location').val(),
                    type: 'place_location'
                }, function(output)
                {
                    display_settings_errors(output);
                }); 
            }
            function change_place_size()
            {
                $.post('page_settings_query.php',
                {
                    num:9,
                    content:$('#place_size').val(),
                    type: 'place_size'
                }, function(output)
                {
                    display_settings_errors(output);
                });
            }
            function change_place_founder()
            {
                $.post('page_settings_query.php',
                {
                    num:9,
                    content:$('#place_founder').val(),
                    type: 'place_founder'
                }, function(output)
                {
                    display_settings_errors(output);
                });
            }
            function change_place_leader()
            {
                var content=new Array();
                content[0]=$('#place_leader_name').val();
                content[1]=$('#place_leader_id').val();
                
                $.post('page_settings_query.php',
                {
                    num:9,
                    content:content,
                    type: 'place_leader'
                }, function(output)
                {
                    display_settings_errors(output);
                });
            }
            function change_product_company()
            {
                var content=new Array();
                content[0]=$('#product_company_name').val();
                content[1]=$('#product_company_id').val();
                
                $.post('page_settings_query.php',
                {
                    num:9,
                    content:content,
                    type: 'product_company'
                }, function(output)
                {
                    display_settings_errors(output);
                });
            }
            function change_product_price()
            {
                $.post('page_settings_query.php',
                {
                    num:9,
                    content:$('#product_price').val(),
                    type: 'product_price'
                }, function(output)
                {
                    display_settings_errors(output);
                });
            }
            function change_product_purchase_link()
            {
                $.post('page_settings_query.php',
                {
                    num:9,
                    content:$('#product_purchase_link').val(),
                    type: 'product_buy_link'
                }, function(output)
                {
                    display_settings_errors(output);
                });
            }
            function change_movie_type()
            {
                $.post('page_settings_query.php',
                {
                    num:9,
                    content:$('#movie_type').val(),
                    type: 'movie_type'
                }, function(output)
                {
                    display_settings_errors(output);
                });
            }
            function add_movie_studio()
            {
                var array=new Array();
                
                var num=0;
                while($('#movie_studio_name_'+num).length)
                {
                    var content=new Array();
                    content[0]=$('#movie_studio_name_'+num).val();
                    content[1]=$('#movie_studio_link_'+num).val();
                    
                    array[num]=content;
                    num++;
                }
                
                var content=new Array();
                content[0]=$('#movie_studio_add_name').val();
                content[1]=$('#movie_studio_add_id').val();
                
                array[num]=content;
                
                $.post('page_settings_query.php',
                {
                    num:9,
                    content: array,
                    type: 'movie_add_studio'
                }, function(output)
                {
                    display_settings_errors(output);
                });
            }
            function add_movie_starring()
            {
                var array=new Array();
                
                var num=0;
                while($('#movie_starring_name_'+num).length)
                {
                    var content=new Array();
                    content[0]=$('#movie_starring_name_'+num).val();
                    content[1]=$('#movie_starring_link_'+num).val();
                    
                    array[num]=content;
                    num++;
                }
                
                var content=new Array();
                content[0]=$('#movie_starring_add_name').val();
                content[1]=$('#movie_starring_add_id').val();
                
                array[num]=content;
                
                $.post('page_settings_query.php',
                {
                    num:9,
                    content: array,
                    type: 'movie_add_starring'
                }, function(output)
                {
                    display_settings_errors(output);
                });
            }
            function change_movie_rating()
            {
                $.post('page_settings_query.php',
                {
                    num:9,
                    content:$('#movie_rating').val(),
                    type: 'movie_rating'
                }, function(output)
                {
                    display_settings_errors(output);
                });
            }
            function change_tv_show_type()
            {
                $.post('page_settings_query.php',
                {
                    num:9,
                    content:$('#tv_show_type').val(),
                    type: 'tv_show_type'
                }, function(output)
                {
                    display_settings_errors(output);
                });
            }
            function add_tv_show_studio()
            {
                var array=new Array();
                
                var num=0;
                while($('#tv_show_studio_name_'+num).length)
                {
                    var content=new Array();
                    content[0]=$('#tv_show_studio_name_'+num).val();
                    content[1]=$('#tv_show_studio_link_'+num).val();
                    
                    array[num]=content;
                    num++;
                }
                
                var content=new Array();
                content[0]=$('#tv_show_studio_add_name').val();
                content[1]=$('#tv_show_studio_add_id').val();
                
                array[num]=content;
                
                $.post('page_settings_query.php',
                {
                    num:9,
                    content: array,
                    type: 'tv_show_add_studio'
                }, function(output)
                {
                    display_settings_errors(output);
                });
            }
            function add_tv_show_starring()
            {
                var array=new Array();
                
                var num=0;
                while($('#tv_show_starring_name_'+num).length)
                {
                    var content=new Array();
                    content[0]=$('#tv_show_starring_name_'+num).val();
                    content[1]=$('#tv_show_starring_link_'+num).val();
                    
                    array[num]=content;
                    num++;
                }
                
                var content=new Array();
                content[0]=$('#tv_show_starring_add_name').val();
                content[1]=$('#tv_show_starring_add_id').val();
                
                array[num]=content;
                
                $.post('page_settings_query.php',
                {
                    num:9,
                    content: array,
                    type: 'tv_show_add_starring'
                }, function(output)
                {
                    display_settings_errors(output);
                });
            }
            function change_tv_show_num_seasons()
            {
                $.post('page_settings_query.php',
                {
                    num:9,
                    content:$('#tv_show_seasons').val(),
                    type: 'tv_show_num_seasons'
                }, function(output)
                {
                    display_settings_errors(output);
                });
            }
            function change_book_type()
            {
                $.post('page_settings_query.php',
                {
                    num:9,
                    content:$('#book_type').val(),
                    type: 'book_type'
                }, function(output)
                {
                    display_settings_errors(output);
                });
            }
            function change_book_author()
            {
                var content=new Array();
                content[0]=$('#book_author_name').val();
                content[1]=$('#book_author_id').val();
                
                $.post('page_settings_query.php',
                {
                    num:9,
                    content:content,
                    type: 'book_author'
                }, function(output)
                {
                    display_settings_errors(output);
                });
            }
            function change_book_num_sold()
            {
                $.post('page_settings_query.php',
                {
                    num:9,
                    content:$('#book_num_sold').val(),
                    type: 'book_num_sold'
                }, function(output)
                {
                    display_settings_errors(output);
                });
            }
            function change_book_purchase_link()
            {
                $.post('page_settings_query.php',
                {
                    num:9,
                    content:$('#book_purchase_link').val(),
                    type: 'book_buy_link'
                }, function(output)
                {
                    display_settings_errors(output);
                });
            }
            function change_website_founders()
            {
                var array=new Array();
                
                var num=0;
                while($('#website_creator_name_'+num).length)
                {
                    var content=new Array();
                    content[0]=$('#website_creator_name_'+num).val();
                    content[1]=$('#website_creator_link'+num).val();
                    
                    array[num]=content;
                    num++;
                }
                
                var content=new Array();
                content[0]=$('#website_add_founder_name').val();
                content[1]=$('#website_add_founder_link').val();
                
                array[num]=content;
                
                $.post('page_settings_query.php',
                {
                    num:9,
                    content: array,
                    type: 'website_add_founder'
                }, function(output)
                {
                    display_settings_errors(output);
                });
            }
            function change_website_type()
            {
                $.post('page_settings_query.php',
                {
                    num:9,
                    content:$('#website_type').val(),
                    type: 'website_type'
                }, function(output)
                {
                    display_settings_errors(output);
                });
            }
            function change_charity_cause()
            {
                $.post('page_settings_query.php',
                {
                    num:9,
                    content:$('#charity_cause').val(),
                    type: 'charity_cause'
                }, function(output)
                {
                    display_settings_errors(output);
                });
            }
            function change_quote_saying_creator()
            {
                var content=new Array();
                content[0]=$('#quote_saying_creator_name').val();
                content[1]=$('#quote_saying_creator_link').val();
                
                $.post('page_settings_query.php',
                {
                    num:9,
                    content:content,
                    type: 'quote/saying_creator'
                }, function(output)
                {
                    display_settings_errors(output);
                });
            }
            function change_quote_saying_origin()
            {
                $.post('page_settings_query.php',
                {
                    num:9,
                    content:$('#quote_saying_origin').val(),
                    type: 'quote/saying_origin'
                }, function(output)
                {
                    display_settings_errors(output);
                });
            }
            function change_person_born()
            {
                $.post('page_settings_query.php',
                {
                    num:9,
                    content:$('#person_born').val(),
                    type: 'person_born'
                }, function(output)
                {
                    display_settings_errors(output);
                });
            }
            function change_person_personality()
            {
                $.post('page_settings_query.php',
                {
                    num:9,
                    content:$('#person_personalities').val(),
                    type: 'person_add_personality'
                }, function(output)
                {
                    display_settings_errors(output);
                });
            }
            function change_person_website()
            {
                $.post('page_settings_query.php',
                {
                    num:9,
                    content:$('#person_website').val(),
                    type: 'person_website'
                }, function(output)
                {
                    display_settings_errors(output);
                });
            }
            function change_actors_movies()
            {
                var array=new Array();
                
                var num=0;
                while($('#actors_movies_name_'+num).length)
                {
                    var content=new Array();
                    content[0]=$('#actors_movies_name_'+num).val();
                    content[1]=$('#actors_movies_link_'+num).val();
                    
                    array[num]=content;
                    num++;
                }
                
                var content=new Array();
                content[0]=$('#actors_movies_add_name').val();
                content[1]=$('#actors_movies_add_id').val();
                
                array[num]=content;
                
                $.post('page_settings_query.php',
                {
                    num:9,
                    content: array,
                    type: 'actor_add_movie'
                }, function(output)
                {
                    display_settings_errors(output);
                });
            }
            function change_actors_tv_shows()
            {
                var array=new Array();
                
                var num=0;
                while($('#actors_tv_shows_name_'+num).length)
                {
                    var content=new Array();
                    content[0]=$('#actors_tv_shows_name_'+num).val();
                    content[1]=$('#actors_tv_shows_link_'+num).val();
                    
                    array[num]=content;
                    num++;
                }
                
                var content=new Array();
                content[0]=$('#actors_tv_shows_add_name').val();
                content[1]=$('#actors_tv_shows_add_id').val();
                
                array[num]=content;
                
                $.post('page_settings_query.php',
                {
                    num:9,
                    content: array,
                    type: 'actor_add_tv_show'
                }, function(output)
                {
                    display_settings_errors(output);
                });
            }
            function change_actors_commercials()
            {
                var array=new Array();
                
                var num=0;
                while($('#actors_commercials_name_'+num).length)
                {
                    var content=new Array();
                    content[0]=$('#actors_commercials_name_'+num).val();
                    content[1]=$('#actors_commercials_link_'+num).val();
                    
                    array[num]=content;
                    num++;
                }
                
                var content=new Array();
                content[0]=$('#actors_commercials_add_name').val();
                content[1]=$('#actors_commercials_add_id').val();
                
                array[num]=content;
                
                $.post('page_settings_query.php',
                {
                    num:9,
                    content: array,
                    type: 'actor_add_commercial'
                }, function(output)
                {
                    display_settings_errors(output);
                });
            }
            function change_actors_others()
            {
                var array=new Array();
                
                var num=0;
                while($('#actors_others_name_'+num).length)
                {
                    var content=new Array();
                    content[0]=$('#actors_others_name_'+num).val();
                    content[1]=$('#actors_others_link_'+num).val();
                    
                    array[num]=content;
                    num++;
                }
                
                var content=new Array();
                content[0]=$('#actors_others_add_name').val();
                content[1]=$('#actors_others_add_id').val();
                
                array[num]=content;
                
                $.post('page_settings_query.php',
                {
                    num:9,
                    content: array,
                    type: 'actor_add_other'
                }, function(output)
                {
                    display_settings_errors(output);
                });
            }
            function change_actors_agency()
            {
                var content=new Array();
                content[0]=$('#actors_agency_name').val();
                content[1]=$('#actors_agency_link').val();
                
                $.post('page_settings_query.php',
                {
                    num:9,
                    content:content,
                    type: 'actor_agency'
                }, function(output)
                {
                    display_settings_errors(output);
                });
            }
            function change_singer_type()
            {
                $.post('page_settings_query.php',
                {
                    num:9,
                    content:$('#singer_type').val(),
                    type: 'singer_type'
                }, function(output)
                {
                    display_settings_errors(output);
                });
            }
            function change_singer_group()
            {
                $.post('page_settings_query.php',
                {
                    num:9,
                    content:$('#singer_group').val(),
                    type: 'singer_group'
                }, function(output)
                {
                    display_settings_errors(output);
                });
            }
            function change_singer_record_label()
            {
                var content=new Array();
                content[0]=$('#singer_record_label_name').val();
                content[1]=$('#singer_record_label_link').val();
                
                $.post('page_settings_query.php',
                {
                    num:9,
                    content:content,
                    type: 'singer_record_label'
                }, function(output)
                {
                    display_settings_errors(output);
                });
            }
            function change_author_type()
            {
                $.post('page_settings_query.php',
                {
                    num:9,
                    content:$('#author_type').val(),
                    type: 'author_type'
                }, function(output)
                {
                    display_settings_errors(output);
                });
            }
            function change_author_books()
            {
                var array=new Array();
                
                var num=0;
                while($('#authors_books_name_'+num).length)
                {
                    var content=new Array();
                    content[0]=$('#authors_books_name_'+num).val();
                    content[1]=$('#authors_books_link_'+num).val();
                    
                    array[num]=content;
                    num++;
                }
                
                var content=new Array();
                content[0]=$('#authors_books_add_name').val();
                content[1]=$('#authors_books_add_id').val();
                
                array[num]=content;
                
                $.post('page_settings_query.php',
                {
                    num:9,
                    content: array,
                    type: 'author_add_book'
                }, function(output)
                {
                    display_settings_errors(output);
                });
            }
            function change_author_num_books_sold()
            {
                $.post('page_settings_query.php',
                {
                    num:9,
                    content:$('#author_num_books_sold').val(),
                    type: 'author_num_books_sold'
                }, function(output)
                {
                    display_settings_errors(output);
                });
            }
            function change_athlete_type()
            {
                $.post('page_settings_query.php',
                {
                    num:9,
                    content:$('#athlete_type').val(),
                    type: 'athlete_type'
                }, function(output)
                {
                    display_settings_errors(output);
                });
            }
            function change_athlete_team()
            {
                var content=new Array();
                content[0]=$('#athlete_team_name').val();
                content[1]=$('#athlete_team_link').val();
                
                $.post('page_settings_query.php',
                {
                    num:9,
                    content:content,
                    type: 'athlete_team'
                }, function(output)
                {
                    display_settings_errors(output);
                });
            }
            function change_athlete_awards()
            {
                var array=new Array();
                
                var num=0;
                while($('#athletes_awards_name_'+num).length)
                {
                    array[num]=$('#athletes_awards_name_'+num).val();;
                    num++;
                }
                
                array[num]=$('#athletes_awards_add_name').val();
                
                $.post('page_settings_query.php',
                {
                    num:9,
                    content: array,
                    type: 'athlete_add_award'
                }, function(output)
                {
                    display_settings_errors(output);
                });
            }
            function change_comedian_venues()
            {
                var array=new Array();
                
                var num=0;
                while($('#comedians_venues_name_'+num).length)
                {
                    var content=new Array();
                    content[0]=$('#comedians_venues_name_'+num).val();
                    content[1]=$('#comedians_venues_link_'+num).val();
                    
                    array[num]=content;
                    num++;
                }
                
                var content=new Array();
                content[0]=$('#comedians_venues_add_name').val();
                content[1]=$('#comedians_venues_add_id').val();
                
                array[num]=content;
                
                $.post('page_settings_query.php',
                {
                    num:9,
                    content: array,
                    type: 'comedian_add_stage'
                }, function(output)
                {
                    display_settings_errors(output);
                });
            }
            function change_character_origin()
            {
                $.post('page_settings_query.php',
                {
                    num:9,
                    content:$('#character_origin').val(),
                    type: 'character_origin'
                }, function(output)
                {
                    display_settings_errors(output);
                });
            }
            function change_character_quotes()
            {
                var array=new Array();
                
                var num=0;
                while($('#character_quotes_name_'+num).length)
                {
                    var content=new Array();
                    content[0]=$('#character_quotes_name_'+num).val();
                    content[1]=$('#character_quotes_link_'+num).val();
                    
                    array[num]=content;
                    num++;
                }
                
                var content=new Array();
                content[0]=$('#character_quotes_add_name').val();
                content[1]=$('#character_quotes_add_id').val();
                
                array[num]=content;
                
                $.post('page_settings_query.php',
                {
                    num:9,
                    content: array,
                    type: 'character_add_quote'
                }, function(output)
                {
                    display_settings_errors(output);
                });
            }
            function change_government_official_place_of_work()
            {
                $.post('page_settings_query.php',
                {
                    num:9,
                    content:$('#government_official_place_of_work').val(),
                    type: 'government_official_work'
                }, function(output)
                {
                    display_settings_errors(output);
                });
            }
            function change_government_official_job()
            {
                $.post('page_settings_query.php',
                {
                    num:9,
                    content:$('#government_official_job').val(),
                    type: 'government_official_job'
                }, function(output)
                {
                    display_settings_errors(output);
                });
            }
            function change_band_members()
            {
                var array=new Array();
                
                var num=0;
                while($('#band_members_name_'+num).length)
                {
                    var content=new Array();
                    content[0]=$('#band_members_name_'+num).val();
                    content[1]=$('#band_members_link_'+num).val();
                    
                    array[num]=content;
                    num++;
                }
                
                var content=new Array();
                content[0]=$('#band_members_add_name').val();
                content[1]=$('#band_members_add_id').val();
                
                array[num]=content;
                
                $.post('page_settings_query.php',
                {
                    num:9,
                    content: array,
                    type: 'band_add_member'
                }, function(output)
                {
                    display_settings_errors(output);
                });
            }
            function change_band_type()
            {
                $.post('page_settings_query.php',
                {
                    num:9,
                    content:$('#band_type').val(),
                    type: 'band_type'
                }, function(output)
                {
                    display_settings_errors(output);
                });
            }
            function change_band_record_label()
            {
                $.post('page_settings_query.php',
                {
                    num:9,
                    content:$('#band_record_label').val(),
                    type: 'band_record_label'
                }, function(output)
                {
                    display_settings_errors(output);
                });
            }
            function change_band_songs()
            {
                var array=new Array();
                
                var num=0;
                while($('#band_songs_name_'+num).length)
                {
                    var content=new Array();
                    content[0]=$('#band_songs_name_'+num).val();
                    content[1]=$('#band_songs_link_'+num).val();
                    
                    array[num]=content;
                    num++;
                }
                
                var content=new Array();
                content[0]=$('#band_songs_add_name').val();
                content[1]=$('#band_songs_add_id').val();
                
                array[num]=content;
                
                $.post('page_settings_query.php',
                {
                    num:9,
                    content: array,
                    type: 'band_add_song'
                }, function(output)
                {
                    display_settings_errors(output);
                });
            }
            function change_director_movies()
            {
                var array=new Array();
                
                var num=0;
                while($('#director_movies_name_'+num).length)
                {
                    var content=new Array();
                    content[0]=$('#director_movies_name_'+num).val();
                    content[1]=$('#director_movies_link_'+num).val();
                    
                    array[num]=content;
                    num++;
                }
                
                var content=new Array();
                content[0]=$('#director_movies_add_name').val();
                content[1]=$('#director_movies_add_id').val();
                
                array[num]=content;
                
                $.post('page_settings_query.php',
                {
                    num:9,
                    content: array,
                    type: 'director_add_movie'
                }, function(output)
                {
                    display_settings_errors(output);
                });
            }
            function change_director_tv_shows()
            {
                var array=new Array();
                
                var num=0;
                while($('#director_tv_shows_name_'+num).length)
                {
                    var content=new Array();
                    content[0]=$('#director_tv_shows_name_'+num).val();
                    content[1]=$('#director_tv_shows_link_'+num).val();
                    
                    array[num]=content;
                    num++;
                }
                
                var content=new Array();
                content[0]=$('#director_tv_shows_add_name').val();
                content[1]=$('#director_tv_shows_add_id').val();
                
                array[num]=content;
                
                $.post('page_settings_query.php',
                {
                    num:9,
                    content: array,
                    type: 'director_add_tv_show'
                }, function(output)
                {
                    display_settings_errors(output);
                });
            }
            function change_director_commercials()
            {
                var array=new Array();
                
                var num=0;
                while($('#director_commercials_name_'+num).length)
                {
                    var content=new Array();
                    content[0]=$('#director_commercials_name_'+num).val();
                    content[1]=$('#director_commercials_link_'+num).val();
                    
                    array[num]=content;
                    num++;
                }
                
                var content=new Array();
                content[0]=$('#director_commercials_add_name').val();
                content[1]=$('#director_commercials_add_id').val();
                
                array[num]=content;
                
                $.post('page_settings_query.php',
                {
                    num:9,
                    content: array,
                    type: 'director_add_commercial'
                }, function(output)
                {
                    display_settings_errors(output);
                });
            }
            function change_director_others()
            {
                var array=new Array();
                
                var num=0;
                while($('#director_others_name_'+num).length)
                {
                    var content=new Array();
                    content[0]=$('#director_others_name_'+num).val();
                    content[1]=$('#director_others_link_'+num).val();
                    
                    array[num]=content;
                    num++;
                }
                
                var content=new Array();
                content[0]=$('#director_others_add_name').val();
                content[1]=$('#director_others_add_id').val();
                
                array[num]=content;
                
                $.post('page_settings_query.php',
                {
                    num:9,
                    content: array,
                    type: 'director_add_other'
                }, function(output)
                {
                    display_settings_errors(output);
                });
            }
            function change_producer_movies()
            {
                var array=new Array();
                
                var num=0;
                while($('#director_movies_name_'+num).length)
                {
                    var content=new Array();
                    content[0]=$('#director_movies_name_'+num).val();
                    content[1]=$('#director_movies_link_'+num).val();
                    
                    array[num]=content;
                    num++;
                }
                
                var content=new Array();
                content[0]=$('#director_movies_add_name').val();
                content[1]=$('#director_movies_add_id').val();
                
                array[num]=content;
                
                $.post('page_settings_query.php',
                {
                    num:9,
                    content: array,
                    type: 'producer_add_movie'
                }, function(output)
                {
                    display_settings_errors(output);
                });
            }
            function change_producer_tv_shows()
            {
                var array=new Array();
                
                var num=0;
                while($('#director_tv_shows_name_'+num).length)
                {
                    var content=new Array();
                    content[0]=$('#director_tv_shows_name_'+num).val();
                    content[1]=$('#director_tv_shows_link_'+num).val();
                    
                    array[num]=content;
                    num++;
                }
                
                var content=new Array();
                content[0]=$('#director_tv_shows_add_name').val();
                content[1]=$('#director_tv_shows_add_id').val();
                
                array[num]=content;
                
                $.post('page_settings_query.php',
                {
                    num:9,
                    content: array,
                    type: 'producer_add_tv_show'
                }, function(output)
                {
                    display_settings_errors(output);
                });
            }
            function change_producer_commercials()
            {
                var array=new Array();
                
                var num=0;
                while($('#director_commercials_name_'+num).length)
                {
                    var content=new Array();
                    content[0]=$('#director_commercials_name_'+num).val();
                    content[1]=$('#director_commercials_link_'+num).val();
                    
                    array[num]=content;
                    num++;
                }
                
                var content=new Array();
                content[0]=$('#director_commercials_add_name').val();
                content[1]=$('#director_commercials_add_id').val();
                
                array[num]=content;
                
                $.post('page_settings_query.php',
                {
                    num:9,
                    content: array,
                    type: 'producer_add_commercial'
                }, function(output)
                {
                    display_settings_errors(output);
                });
            }
            function change_producer_others()
            {
                var array=new Array();
                
                var num=0;
                while($('#director_others_name_'+num).length)
                {
                    var content=new Array();
                    content[0]=$('#director_others_name_'+num).val();
                    content[1]=$('#director_others_link_'+num).val();
                    
                    array[num]=content;
                    num++;
                }
                
                var content=new Array();
                content[0]=$('#director_others_add_name').val();
                content[1]=$('#director_others_add_id').val();
                
                array[num]=content;
                
                $.post('page_settings_query.php',
                {
                    num:9,
                    content: array,
                    type: 'producer_add_other'
                }, function(output)
                {
                    display_settings_errors(output);
                });
            }
            function change_public_figure_known_for()
            {
                var array=new Array();
                
                var num=0;
                while($('#public_figures_name_'+num).length)
                {
                    var content=new Array();
                    content[0]=$('#public_figures_name_'+num).val();
                    content[1]=$('#public_figures_link_'+num).val();
                    
                    array[num]=content;
                    num++;
                }
                
                var content=new Array();
                content[0]=$('#public_figures_add_name').val();
                content[1]=$('#public_figures_add_id').val();
                
                array[num]=content;
                
                $.post('page_settings_query.php',
                {
                    num:9,
                    content: array,
                    type: 'public_figure_best_known_for'
                }, function(output)
                {
                    display_settings_errors(output);
                });
            }
            <?php
                $query=mysql_query("SELECT data FROM page_data WHERE page_id=$_SESSION[page_id] LIMIT 1");
                if($query&&mysql_num_rows($query)==1)
                {
                    $array=mysql_fetch_row($query);
                    $data=explode('|^|*|', $array[0]);
                    
                    for($x = 0; $x < sizeof($data); $x++)
                    {
                        if(strstr($data[$x], '|%|&|')==true)
                        {
                            $data[$x]=explode('|%|&|', $data[$x]);
                            
                            for($y = 0; $y < sizeof($data[$x]); $y++)
                            {
                                if(strstr($data[$x][0], '|@|$|')==true)
                                    $data[$x][$y]=explode('|@|$|', $data[$x][$y]);
                            }
                        }
                        else if(strstr($data[$x], '|@|$|')==true)
                            $data[$x]=explode('|@|$|', $data[$x]);
                    }
                }
            ?>
            function display_company()
            {
                $('#information_table').html($('#information_table').html()+"<tr id='row_0'></tr><tr><td class='settings_break_unit' colspan='3'><hr class='settings_break'/></td></tr><tr id='row_1'></tr><tr><td class='settings_break_unit' colspan='3'><hr class='settings_break'/></td></tr><tr id='row_2'></tr><tr><td class='settings_break_unit' colspan='3'><hr class='settings_break'/></td></tr><tr id='row_3'></tr><tr><td class='settings_break_unit' colspan='3'><hr class='settings_break'/></td></tr><tr id='row_4'></tr><tr><td class='settings_break_unit' colspan='3'><hr class='settings_break'/></td></tr><tr id='row_5'></tr><tr><td class='settings_break_unit' colspan='3'><hr class='settings_break'/></td></tr><tr id='row_6'></tr><tr><td class='settings_break_unit' colspan='3'><hr class='settings_break'/></td></tr><tr id='row_7'></tr>");
                
                $('#row_0').html("<td class='settings_unit_left' id='row_0_left'></td><td class='settings_unit_middle' id='row_0_middle'></td><td class='settings_unit_right' id='row_0_right'></td>");
                $('#row_1').html("<td class='settings_unit_left' id='row_1_left'></td><td class='settings_unit_middle' id='row_1_middle'></td><td class='settings_unit_right' id='row_1_right'></td>");
                $('#row_2').html("<td class='settings_unit_left' id='row_2_left'></td><td class='settings_unit_middle' id='row_2_middle'></td><td class='settings_unit_right' id='row_2_right'></td>");
                $('#row_3').html("<td class='settings_unit_left' id='row_3_left'></td><td class='settings_unit_middle' id='row_3_middle'></td><td class='settings_unit_right' id='row_3_right'></td>");
                $('#row_4').html("<td class='settings_unit_left' id='row_4_left'></td><td class='settings_unit_middle' id='row_4_middle'></td><td class='settings_unit_right' id='row_4_right'></td>");
                $('#row_5').html("<td class='settings_unit_left' id='row_5_left'></td><td class='settings_unit_middle' id='row_5_middle'></td><td class='settings_unit_right' id='row_5_right'></td>");
                $('#row_6').html("<td class='settings_unit_left' id='row_6_left'></td><td class='settings_unit_middle' id='row_6_middle'></td><td class='settings_unit_right' id='row_6_right'></td>");
                $('#row_7').html("<td class='settings_unit_left' id='row_7_left'></td><td class='settings_unit_middle' id='row_7_middle'></td><td class='settings_unit_right' id='row_7_right'></td>");
                
                $('#row_0_left').html("<p class='settings_text'>Founded: </p>");
                $('#row_1_left').html("<p class='settings_text'>Website: </p>");
                $('#row_2_left').html("<p class='settings_text'>CEO: </p>");
                $('#row_6_left').html("<p class='settings_text'>Number of Employees: </p>");
                $('#row_7_left').html("<p class='settings_text'>Headquarters: </p>");
                
                $('#row_0_middle').html("<input class='input_box' id='company_founded_input' placeholder='EX: September 1, 2012' value='<?php echo $data[0]; ?>'/>");
                $('#row_1_middle').html("<input class='input_box' id='company_website' placeholder='EX: http://www.redlay.com' value='<?php echo $data[1]; ?>'/>");
                $('#row_2_middle').html("<table><tbody><tr><td><input class='input_box' id='company_CEO_name' placeholder='Name' value='<?php echo $data[2][0]; ?>'/></td></tr><tr><td><input class='input_box' id='company_CEO_id' placeholder='page id OR link' value='<?php echo $data[2][1]; ?>'/></td></tr></tbody></table>");
                $('#row_6_middle').html("<input class='input_box' id='company_num_employees' placeholder='EX: 1000' value='<?php echo $data[3]; ?>'/>");
                $('#row_7_middle').html("<input class='input_box' id='company_headquarters' placeholder='EX: Palo Alto, CA' value='<?php echo $data[4]; ?>'/>");
                
                $('#row_0_right').html("<input class='red_button' value='Change' type='button' onClick='change_company_founded_date();'/>");
                $('#row_1_right').html("<input class='red_button' value='Change' type='button' onClick='change_company_website();'/>");
                $('#row_2_right').html("<input class='red_button' value='Change' type='button' onClick='change_company_CEO();'/>");
                $('#row_3_right').html("<input class='red_button' value='Change' type='button' onClick='change_company_CFO();'/>");
                $('#row_4_right').html("<input class='red_button' value='Change' type='button' onClick='change_company_president();'/>");
                $('#row_5_right').html("<input class='red_button' value='Change' type='button' onClick='change_company_vice_president();'/>");
                $('#row_6_right').html("<input class='red_button' value='Change' type='button' onClick='change_company_num_employees();'/>");
                $('#row_7_right').html("<input class='red_button' value='Change' type='button' onClick='change_company_headquarters();'/>");
                
                $('.input_box').attr({'onFocus': "input_in(this);", 'onBlur': "input_out(this);"}).css('width', '200px');
            }
            function display_person()
            {
                $('#information_table').html($('#information_table').html()+"<tr id='row_0'></tr><tr><td class='settings_break_unit' colspan='3'><hr class='settings_break'/></td></tr><tr id='row_1'></tr><tr><td class='settings_break_unit' colspan='3'><hr class='settings_break'/></td></tr><tr id='row_2'></tr><tr><td class='settings_break_unit' colspan='3'><hr class='settings_break'/></td></tr>");
                
                $('#row_0').html("<td class='settings_unit_left' id='row_0_left'></td><td class='settings_unit_middle' id='row_0_middle'></td><td class='settings_unit_right' id='row_0_right'></td>");
                $('#row_1').html("<td class='settings_unit_left' id='row_1_left'></td><td class='settings_unit_middle' id='row_1_middle'></td><td class='settings_unit_right' id='row_1_right'></td>");
                $('#row_2').html("<td class='settings_unit_left' id='row_2_left'></td><td class='settings_unit_middle' id='row_2_middle'></td><td class='settings_unit_right' id='row_2_right'></td>");
                
                $('#row_0_left').html("<p class='settings_text'>Born: </p>");
                $('#row_1_left').html("<p class='settings_text'>Personality: </p>");
                $('#row_2_left').html("<p class='settings_text'>Website: </p>");
                
                $('#row_0_middle').html("<input class='input_box' id='person_born' placeholder='EX: June 16, 1986' value='<?php echo $data[0]; ?>'/>");
                $('#row_1_middle').html("<input class='input_box' id='person_personalities' placeholder='EX: Shy, motivated' value='<?php echo $data[2]; ?>'/>");
                $('#row_2_middle').html("<input class='input_box' id='person_website' placeholder='EX: http://mkaku.org' value='<?php echo $data[1]; ?>'/>");
                
                $('#row_0_right').html("<input class='red_button' type='button' value='Change' onClick='change_person_born();'/>");
                $('#row_1_right').html("<input class='red_button' type='button' value='Change' onClick='change_person_personality();'/>");
                $('#row_2_right').html("<input class='red_button' type='button' value='Change' onClick='change_person_website();'/>");
                
                if(<?php if($other_type=='Actor') echo "true"; else echo "false"; ?>==true)
                {
                    $('#information_table').html($('#information_table').html()+"<tr id='row_3'></tr><tr><td class='settings_break_unit' colspan='3'><hr class='settings_break'/></td></tr><tr id='row_4'></tr><tr><td class='settings_break_unit' colspan='3'><hr class='settings_break'/></td></tr><tr id='row_5'></tr><tr><td class='settings_break_unit' colspan='3'><hr class='settings_break'/></td><tr id='row_6'></tr><tr><td class='settings_break_unit' colspan='3'><hr class='settings_break'/></td></tr><tr id='row_7'></tr><tr><td class='settings_break_unit' colspan='3'><hr class='settings_break'/></td></tr>");
                    
                    $('#row_3').html("<td class='settings_unit_left' id='row_3_left'></td><td class='settings_unit_middle' id='row_3_middle'></td><td class='settings_unit_right' id='row_3_right'></td>");
                    $('#row_4').html("<td class='settings_unit_left' id='row_4_left'></td><td class='settings_unit_middle' id='row_4_middle'></td><td class='settings_unit_right' id='row_4_right'></td>");
                    $('#row_5').html("<td class='settings_unit_left' id='row_5_left'></td><td class='settings_unit_middle' id='row_5_middle'></td><td class='settings_unit_right' id='row_5_right'></td>");
                    $('#row_6').html("<td class='settings_unit_left' id='row_6_left'></td><td class='settings_unit_middle' id='row_6_middle'></td><td class='settings_unit_right' id='row_6_right'></td>");
                    $('#row_7').html("<td class='settings_unit_left' id='row_7_left'></td><td class='settings_unit_middle' id='row_7_middle'></td><td class='settings_unit_right' id='row_7_right'></td>");
                    
                    $('#row_3_left').html("<p class='settings_text' >Movies: </p>");
                    $('#row_4_left').html("<p class='settings_text' >TV Shows:</p>");
                    $('#row_5_left').html("<p class='settings_text' >Commercials:</p>");
                    $('#row_6_left').html("<p class='settings_text' >Other work:</p>");
                    $('#row_7_left').html("<p class='settings_text' >Agency:</p>");
                    
                    $('#row_3_middle').html("<div id='actors_movies_box' class='settings_box'></div>");
                    $('#row_4_middle').html("<div id='actors_tv_shows_box' class='settings_box'></div>");
                    $('#row_5_middle').html("<div id='actors_commercials_box' class='settings_box'></div>");
                    $('#row_6_middle').html("<div id='actors_others_box' class='settings_box'></div>");
                    $('#row_7_middle').html("<table><tbody><tr><td><input class='input_box' id='actors_agency_name' type='text' value='<?php echo $data[8][0]; ?>' placeholder='Agency representing you'/></td></tr><tr><td><input class='input_box' id='actors_agency_link' type='text' value='<?php echo $data[8][1]; ?>' placeholder='page id OR link'/></td></tr></tbody></table>");
                    
                    $('#row_3_right').html("<input class='red_button' type='button' onClick='change_actors_movies();' value='Change'/>");
                    $('#row_4_right').html("<input class='red_button' type='button' onClick='change_actors_tv_shows();' value='Change'/>");
                    $('#row_5_right').html("<input class='red_button' type='button' onClick='change_actors_commercials();' value='Change'/>");
                    $('#row_6_right').html("<input class='red_button' type='button' onClick='change_actors_others();' value='Change'/>");
                    $('#row_7_right').html("<input class='red_button' type='button' onClick='change_actors_agency();' value='Change'/>");
                    
                    display_actors_movies();
                    display_actors_tv_shows();
                    display_actors_commercials();
                    display_actors_others();
                }
                else if(<?php if($other_type=='Singer') echo "true"; else echo "false"; ?>==true)
                {
                    $('#information_table').html($('#information_table').html()+"<tr id='row_3'></tr><tr><td class='settings_break_unit' colspan='3'><hr class='settings_break'/></td></tr><tr id='row_4'></tr><tr><td class='settings_break_unit' colspan='3'><hr class='settings_break'/></td></tr><tr id='row_5'></tr><tr><td class='settings_break_unit' colspan='3'><hr class='settings_break'/></td>");
                    
                    $('#row_3').html("<td class='settings_unit_left' id='row_3_left'></td><td class='settings_unit_middle' id='row_3_middle'></td><td class='settings_unit_right' id='row_3_right'></td>");
                    $('#row_4').html("<td class='settings_unit_left' id='row_4_left'></td><td class='settings_unit_middle' id='row_4_middle'></td><td class='settings_unit_right' id='row_4_right'></td>");
                    $('#row_5').html("<td class='settings_unit_left' id='row_5_left'></td><td class='settings_unit_middle' id='row_5_middle'></td><td class='settings_unit_right' id='row_5_right'></td>");
                    
                    $('#row_3_left').html("<p class='settings_text' >Genre:  </p>");
                    $('#row_4_left').html("<p class='settings_text' >Group: </p>");
                    $('#row_5_left').html("<p class='settings_text' >Record Label: </p>");
                    
                    $('#row_3_middle').html("<input type='text' class='input_box' id='singer_type' value='<?php echo $data[3]; ?>' placeholder='EX: Pop, rock'/>");
                    $('#row_4_middle').html("<input type='text' class='input_box' id='singer_group' value='<?php echo $data[4]; ?>' placeholder='EX: The Beatles'/>");
                    $('#row_5_middle').html("<table><tbody><tr><td><input type='text' class='input_box' id='singer_record_label_name' value='<?php echo $data[5][0]; ?>' placeholder='Record Label'/></td></tr><tr><td><input type='text' class='input_box' id='singer_record_label_link' value='<?php echo $data[5][1]; ?>' placeholder='EX: page id OR link'/></td></tr></tbody></table>");
                    
                    $('#row_3_right').html("<input class='red_button' type='button' value='Change' onClick='change_singer_type();'/>");
                    $('#row_4_right').html("<input class='red_button' type='button' value='Change' onClick='change_singer_group();'/>");
                    $('#row_5_right').html("<input class='red_button' type='button' value='Change' onClick='change_singer_record_label();'/>");
                    
                }
                else if(<?php if($other_type=='Author') echo "true"; else echo "false"; ?>==true)
                {
                    $('#information_table').html($('#information_table').html()+"<tr id='row_3'></tr><tr><td class='settings_break_unit' colspan='3'><hr class='settings_break'/></td></tr><tr id='row_4'></tr><tr><td class='settings_break_unit' colspan='3'><hr class='settings_break'/></td></tr><tr id='row_5'></tr><tr><td class='settings_break_unit' colspan='3'><hr class='settings_break'/></td>");
                    
                    $('#row_3').html("<td class='settings_unit_left' id='row_3_left'></td><td class='settings_unit_middle' id='row_3_middle'></td><td class='settings_unit_right' id='row_3_right'></td>");
                    $('#row_4').html("<td class='settings_unit_left' id='row_4_left'></td><td class='settings_unit_middle' id='row_4_middle'></td><td class='settings_unit_right' id='row_4_right'></td>");
                    $('#row_5').html("<td class='settings_unit_left' id='row_5_left'></td><td class='settings_unit_middle' id='row_5_middle'></td><td class='settings_unit_right' id='row_5_right'></td>");
                    
                    $('#row_3_left').html("<p class='settings_text'>Type: </p>");
                    $('#row_4_left').html("<p class='settings_text'>Books: </p>");
                    $('#row_5_left').html("<p class='settings_text'>Numbers sold: </p>");
                    
                    $('#row_3_middle').html("<input class='input_box' id='author_type' value='<?php echo $data[4]; ?>' placeholder='EX: Sci-fi, horror'/>");
                    $('#row_4_middle').html("<div id='authors_books_box' class='settings_box'></div>");
                    $('#row_5_middle').html("<input class='input_box' id='author_num_books_sold' placeholder='EX: 1,000,000' value='<?php echo $data[6]; ?>'/>");
                    
                    $('#row_3_right').html("<input class='red_button' type='button' value='Change' onClick='change_author_type();'/>");
                    $('#row_4_right').html("<input class='red_button' type='button' value='Change' onClick='change_author_books();'/>");
                    $('#row_5_right').html("<input class='red_button' type='button' value='Change' onClick='change_author_num_books_sold();'/>");
                    
                    display_author_books();
                }
                else if(<?php if($other_type=='Athlete') echo "true"; else echo "false"; ?>==true)
                {
                    $('#information_table').html($('#information_table').html()+"<tr id='row_3'></tr><tr><td class='settings_break_unit' colspan='3'><hr class='settings_break'/></td></tr><tr id='row_4'></tr><tr><td class='settings_break_unit' colspan='3'><hr class='settings_break'/></td></tr><tr id='row_5'></tr><tr><td class='settings_break_unit' colspan='3'><hr class='settings_break'/></td>");
                    
                    $('#row_3').html("<td class='settings_unit_left' id='row_3_left'></td><td class='settings_unit_middle' id='row_3_middle'></td><td class='settings_unit_right' id='row_3_right'></td>");
                    $('#row_4').html("<td class='settings_unit_left' id='row_4_left'></td><td class='settings_unit_middle' id='row_4_middle'></td><td class='settings_unit_right' id='row_4_right'></td>");
                    $('#row_5').html("<td class='settings_unit_left' id='row_5_left'></td><td class='settings_unit_middle' id='row_5_middle'></td><td class='settings_unit_right' id='row_5_right'></td>");
                    
                    $('#row_3_left').html("<p class='settings_text' >Type: </p>");
                    $('#row_4_left').html("<p class='settings_text' >Team: </p>");
                    $('#row_5_left').html("<p class='settings_text' >Awards: </p>");
                    
                    $('#row_3_middle').html("<input class='input_box' id='athlete_type' placeholder='EX: Basketball' value='<?php echo $data[4]; ?>'/>");
                    $('#row_4_middle').html("<table><tbody><tr><td><input class='input_box' type='text' placeholder='Sports team you are in' value='<?php echo $data[5][0]; ?>' id='athlete_team_name'/></td></tr><tr><td><input class='input_box' type='text' value='<?php echo $data[5][1]; ?>' placeholder='page id OR link' id='athlete_team_link'/></td></tr></tbody></table>");
                    $('#row_5_middle').html("<div id='athletes_awards_box' class='settings_box'></div>"); 
                    
                    $('#row_3_right').html("<input class='red_button' type='button' value='Change' onClick='change_athlete_type();'/>");
                    $('#row_4_right').html("<input class='red_button' type='button' value='Change' onClick='change_athlete_team();'/>");
                    $('#row_5_right').html("<input class='red_button' type='button' value='Change' onClick='change_athlete_awards();'/>");
                    
                    display_athletes_awards();
                }
                else if(<?php if($other_type=='Comedian') echo "true"; else echo "false"; ?>==true)
                {
                    $('#information_table').html($('#information_table').html()+"<tr id='row_3'></tr>");
                    
                    $('#row_3').html("<td class='settings_unit_left' id='row_3_left'></td><td class='settings_unit_middle' id='row_3_middle'></td><td class='settings_unit_right' id='row_3_right'></td>");
                    
                    $('#row_3_left').html("<p class='settings_text'>Venues: </p>");
                    $('#row_3_middle').html("<div id='comedians_venues_box' class='settings_box'></div>");
                    $('#row_3_right').html("<input class='red_button' onClick='change_comedian_venues();' value='Change' type='button'/>");
                    
                    display_comedians_venues();
                }
                else if(<?php if($other_type=='Character') echo "true"; else echo "false"; ?>==true)
                {
                    $('#information_table').html($('#information_table').html()+"<tr id='row_3'></tr><tr><td class='settings_break_unit' colspan='3'><hr class='settings_break'/></td></tr><tr id='row_4'></tr>");
                    
                    $('#row_3').html("<td class='settings_unit_left' id='row_3_left'></td><td class='settings_unit_middle' id='row_3_middle'></td><td class='settings_unit_right' id='row_3_right'></td>");
                    $('#row_4').html("<td class='settings_unit_left' id='row_4_left'></td><td class='settings_unit_middle' id='row_4_middle'></td><td class='settings_unit_right' id='row_4_right'></td>");
                    
                    $('#row_3_left').html("<p class='settings_text'>Origin: </p>");
                    $('#row_4_left').html("<p class='settings_text'>Quotes: </p>");
                    
                    $('#row_3_middle').html("<input class='input_box' id='character_origin' placeholder='Link to origin' type='text' value='<?php echo $data[4]; ?>' />");
                    $('#row_4_middle').html("<div class='settings_box' id='character_quotes_box'></div>");
                    
                    $('#row_3_right').html("<input class='red_button' type='button' value='Change' onClick='change_character_origin();'/>");
                    $('#row_4_right').html("<input class='red_button' type='button' value='Change' onClick='change_character_quotes();'/>");
                    
                    display_character_quotes();
                }
                else if(<?php if($other_type=='Government Official') echo "true"; else echo "false"; ?>==true)
                {
                    $('#information_table').html($('#information_table').html()+"<tr id='row_3'></tr><tr><td class='settings_break_unit' colspan='3'><hr class='settings_break'/></td></tr><tr id='row_4'></tr>");
                    
                    $('#row_3').html("<td class='settings_unit_left' id='row_3_left'></td><td class='settings_unit_middle' id='row_3_middle'></td><td class='settings_unit_right' id='row_3_right'></td>");
                    $('#row_4').html("<td class='settings_unit_left' id='row_4_left'></td><td class='settings_unit_middle' id='row_4_middle'></td><td class='settings_unit_right' id='row_4_right'></td>");
                    
                    $('#row_3_left').html("<p class='settings_text'>Place of work: </p>");
                    $('#row_4_left').html("<p class='settings_text'>Job: </p>");
                    
                    $('#row_3_middle').html("<input class='input_box' id='government_official_place_of_work' placeholder='EX: White house' type='text' value='<?php echo $data[4]; ?>' />");
                    $('#row_4_middle').html("<input class='input_box' id='government_official_job' placeholder='EX: Secretary' type='text' value='<?php echo $data[5]; ?>' />");
                    
                    $('#row_3_right').html("<input class='red_button' type='button' value='Change' onClick='change_government_official_place_of_work();'/>");
                    $('#row_4_right').html("<input class='red_button' type='button' value='Change' onClick='change_government_official_job();'/>");
                }
                else if(<?php if($other_type=='Band') echo "true"; else echo "false"; ?>==true)
                {
                    $('#information_table').html($('#information_table').html()+"<tr id='row_3'></tr><tr><td class='settings_break_unit' colspan='3'><hr class='settings_break'/></td></tr><tr id='row_4'></tr><tr><td class='settings_break_unit' colspan='3'><hr class='settings_break'/></td></tr><tr id='row_5'></tr><tr><td class='settings_break_unit' colspan='3'><hr class='settings_break'/></td><tr id='row_6'></tr>");
                    
                    $('#row_3').html("<td class='settings_unit_left' id='row_3_left'></td><td class='settings_unit_middle' id='row_3_middle'></td><td class='settings_unit_right' id='row_3_right'></td>");
                    $('#row_4').html("<td class='settings_unit_left' id='row_4_left'></td><td class='settings_unit_middle' id='row_4_middle'></td><td class='settings_unit_right' id='row_4_right'></td>");
                    $('#row_5').html("<td class='settings_unit_left' id='row_5_left'></td><td class='settings_unit_middle' id='row_5_middle'></td><td class='settings_unit_right' id='row_5_right'></td>");
                    $('#row_6').html("<td class='settings_unit_left' id='row_6_left'></td><td class='settings_unit_middle' id='row_6_middle'></td><td class='settings_unit_right' id='row_6_right'></td>");
                    
                    $('#row_3_left').html("<p class='settings_text'>Members: </p>");
                    $('#row_4_left').html("<p class='settings_text'>Type: </p>");
                    $('#row_5_left').html("<p class='settings_text'>Record Label: </p>");
                    $('#row_6_left').html("<p class='settings_text'>Songs: </p>");
                    
                    $('#row_3_middle').html("<div id='band_members_box' class='settings_box'></div>");
                    $('#row_4_middle').html("<input type='text' class='input_box' id='band_type' placeholder='EX: Rock' value='<?php echo $data[5]; ?>'/>");
                    $('#row_5_middle').html("<input type='text' class='input_box' id='band_record_label' placeholder='Record label' value='<?php echo $data[6]; ?>'/>");
                    $('#row_6_middle').html("<div id='band_songs_box' class='settings_box'></div>");
                    
                    $('#row_3_right').html("<input class='red_button' type='button' value='Change' onClick='change_band_members();'/>");
                    $('#row_4_right').html("<input class='red_button' type='button' value='Change' onClick='change_band_type();'/>");
                    $('#row_5_right').html("<input class='red_button' type='button' value='Change' onClick='change_band_record_label();'/>");
                    $('#row_6_right').html("<input class='red_button' type='button' value='Change' onClick='change_band_songs();'/>");
                    
                    display_band_members();
                    display_band_songs();
                }
                else if(<?php if($other_type=='Director') echo "true"; else echo "false"; ?>==true)
                {
                    $('#information_table').html($('#information_table').html()+"<tr id='row_3'></tr><tr><td class='settings_break_unit' colspan='3'><hr class='settings_break'/></td></tr><tr id='row_4'></tr><tr><td class='settings_break_unit' colspan='3'><hr class='settings_break'/></td></tr><tr id='row_5'></tr><tr><td class='settings_break_unit' colspan='3'><hr class='settings_break'/></td></tr><tr id='row_6'></tr>");
                    
                    $('#row_3').html("<td class='settings_unit_left' id='row_3_left'></td><td class='settings_unit_middle' id='row_3_middle'></td><td class='settings_unit_right' id='row_3_right'></td>");
                    $('#row_4').html("<td class='settings_unit_left' id='row_4_left'></td><td class='settings_unit_middle' id='row_4_middle'></td><td class='settings_unit_right' id='row_4_right'></td>");
                    $('#row_5').html("<td class='settings_unit_left' id='row_5_left'></td><td class='settings_unit_middle' id='row_5_middle'></td><td class='settings_unit_right' id='row_5_right'></td>");
                    $('#row_6').html("<td class='settings_unit_left' id='row_6_left'></td><td class='settings_unit_middle' id='row_6_middle'></td><td class='settings_unit_right' id='row_6_right'></td>");
                    
                    $('#row_3_left').html("<p class='settings_text'>Movies: </p>");
                    $('#row_4_left').html("<p class='settings_text'>TV Shows: </p>");
                    $('#row_5_left').html("<p class='settings_text'>Commercials: </p>");
                    $('#row_6_left').html("<p class='settings_text'>Other: </p>");
                    
                    $('#row_3_middle').html("<div class='settings_box' id='director_movies_box'></div>");
                    $('#row_4_middle').html("<div class='settings_box' id='director_tv_shows_box'></div>");
                    $('#row_5_middle').html("<div class='settings_box' id='director_commercials_box'></div>");
                    $('#row_6_middle').html("<div class='settings_box' id='director_others_box'></div>");
                    
                    $('#row_3_right').html("<input class='red_button' type='button' value='Change' onClick='change_director_movies();'/>");
                    $('#row_4_right').html("<input class='red_button' type='button' value='Change' onClick='change_director_tv_shows();'/>");
                    $('#row_5_right').html("<input class='red_button' type='button' value='Change' onClick='change_director_commercials();'/>");
                    $('#row_6_right').html("<input class='red_button' type='button' value='Change' onClick='change_director_others();'/>");
                    
                    display_director_movies();
                    display_director_tv_shows();
                    display_director_commercials();
                    display_director_others();
                }
                else if(<?php if($other_type=='Producer') echo "true"; else echo "false"; ?>==true)
                {
                    $('#information_table').html($('#information_table').html()+"<tr id='row_3'></tr><tr><td class='settings_break_unit' colspan='3'><hr class='settings_break'/></td></tr><tr id='row_4'></tr><tr><td class='settings_break_unit' colspan='3'><hr class='settings_break'/></td></tr><tr id='row_5'></tr><tr><td class='settings_break_unit' colspan='3'><hr class='settings_break'/></td></tr><tr id='row_6'></tr>");
                    
                    $('#row_3').html("<td class='settings_unit_left' id='row_3_left'></td><td class='settings_unit_middle' id='row_3_middle'></td><td class='settings_unit_right' id='row_3_right'></td>");
                    $('#row_4').html("<td class='settings_unit_left' id='row_4_left'></td><td class='settings_unit_middle' id='row_4_middle'></td><td class='settings_unit_right' id='row_4_right'></td>");
                    $('#row_5').html("<td class='settings_unit_left' id='row_5_left'></td><td class='settings_unit_middle' id='row_5_middle'></td><td class='settings_unit_right' id='row_5_right'></td>");
                    $('#row_6').html("<td class='settings_unit_left' id='row_6_left'></td><td class='settings_unit_middle' id='row_6_middle'></td><td class='settings_unit_right' id='row_6_right'></td>");
                    
                    $('#row_3_left').html("<p class='settings_text'>Movies: </p>");
                    $('#row_4_left').html("<p class='settings_text'>TV Shows: </p>");
                    $('#row_5_left').html("<p class='settings_text'>Commercials: </p>");
                    $('#row_6_left').html("<p class='settings_text'>Other: </p>");
                    
                    $('#row_3_middle').html("<div class='settings_box' id='director_movies_box'></div>");
                    $('#row_4_middle').html("<div class='settings_box' id='director_tv_shows_box'></div>");
                    $('#row_5_middle').html("<div class='settings_box' id='director_commercials_box'></div>");
                    $('#row_6_middle').html("<div class='settings_box' id='director_others_box'></div>");
                    
                    $('#row_3_right').html("<input class='red_button' type='button' value='Change' onClick='change_producer_movies();'/>");
                    $('#row_4_right').html("<input class='red_button' type='button' value='Change' onClick='change_producer_tv_shows();'/>");
                    $('#row_5_right').html("<input class='red_button' type='button' value='Change' onClick='change_producer_commercials();'/>");
                    $('#row_6_right').html("<input class='red_button' type='button' value='Change' onClick='change_producer_others();'/>");
                    
                    display_director_movies();
                    display_director_tv_shows();
                    display_director_commercials();
                    display_director_others();
                    
                }
                else if(<?php if($other_type=='Public Figure') echo "true"; else echo "false"; ?>==true)
                {
                    $('#information_table').html($('#information_table').html()+"<tr id='row_3'></tr>");
                    
                    $('#row_3').html("<td class='settings_unit_left' id='row_3_left'></td><td class='settings_unit_middle' id='row_3_middle'></td><td class='settings_unit_right' id='row_3_right'></td>");
                    
                    $('#row_3_left').html("<p class='settings_text'>Known for: </p>");
                    $('#row_3_middle').html("<div class='settings_box' id='public_figures_box'></div>");
                    $('#row_3_right').html("<input class='red_button' value='Change' type='button' onClick='change_public_figure_known_for();' />");
                    
                    display_public_figure_known_for();
                }
                
                $('.input_box').attr({'onFocus': "input_in(this);", 'onBlur': "input_out(this);"}).css('width', '200px');
                $('#change_bio_text').css('width', '300px');
            }
            function display_other()
            {
                $('#information_table').html($('#information_table').html()+"<tr id='row_0'></tr><tr><td class='settings_break_unit' colspan='3'><hr class='settings_break'/></td></tr><tr id='row_1'></tr><tr><td class='settings_break_unit' colspan='3'><hr class='settings_break'/></td></tr>");
                
                $('#row_0').html("<td class='settings_unit_left' id='row_0_left'></td><td class='settings_unit_middle' id='row_0_middle'></td><td class='settings_unit_right' id='row_0_right'></td>");
                $('#row_1').html("<td class='settings_unit_left' id='row_1_left'></td><td class='settings_unit_middle' id='row_1_middle'></td><td class='settings_unit_right' id='row_1_right'></td>");
                
                
                $('#row_0_middle').html("<input class='input_box' id='other_created' placeholder='EX: September 1, 2012' value='<?php echo $data[0]; ?>'/>");
                $('#row_0_right').html("<input type='button' class='red_button' onClick='change_other_created();' value='Change'/>");
                
                $('#row_1_left').html("<p class='settings_text'>Website: </p>");
                $('#row_1_middle').html("<input class='input_box' type='text' placeholder='EX: http://www.redlay.com' id='other_website_input' value='<?php echo $data[1]; ?>'/>");
                $('#row_1_right').html("<input class='settings_button red_button' onClick='change_other_website();' value='Change' type='button' />");
                if(<?php if($other_type=='Place') echo "true"; else echo "false"; ?>==true)
                {
                    $('#row_0_left').html("<p class='settings_text'>Date formed: </p>")
                    $('#information_table').html($('#information_table').html()+"<tr id='row_2'></tr><tr><td class='settings_break_unit' colspan='3'><hr class='settings_break'/></td></tr><tr id='row_3'></tr><tr><td class='settings_break_unit' colspan='3'><hr class='settings_break'/></td></tr><tr id='row_4'></tr><tr><td class='settings_break_unit' colspan='3'><hr class='settings_break'/></td><tr id='row_5'></tr><tr><td class='settings_break_unit' colspan='3'><hr class='settings_break'/></td></tr>");
                    
                    $('#row_2').html("<td class='settings_unit_left' id='row_2_left'></td><td class='settings_unit_middle' id='row_2_middle'></td><td class='settings_unit_right' id='row_2_right'></td>");
                    $('#row_3').html("<td class='settings_unit_left' id='row_3_left'></td><td class='settings_unit_middle' id='row_3_middle'></td><td class='settings_unit_right' id='row_3_right'></td>");
                    $('#row_4').html("<td class='settings_unit_left' id='row_4_left'></td><td class='settings_unit_middle' id='row_4_middle'></td><td class='settings_unit_right' id='row_4_right'></td>");
                    $('#row_5').html("<td class='settings_unit_left' id='row_5_left'></td><td class='settings_unit_middle' id='row_5_middle'></td><td class='settings_unit_right' id='row_5_right'></td>");
                    
                    
                    $('#row_2_left').html("<p class='settings_text'>Location: </p>");
                    $('#row_3_left').html("<p class='settings_text'>Size: </p>");
                    $('#row_4_left').html("<p class='settings_text'>Founder: </p>");
                    $('#row_5_left').html("<p class='settings_text'>Current leader:</p>");
                    
                    $('#row_2_middle').html("<input class='input_box' id='place_location' placeholder='EX: Stanford, CA, U.S.' value='<?php echo $data[2]; ?>'/>");
                    $('#row_3_middle').html("<input class='input_box' id='place_size' placeholder='EX: 8,180 acres' value='<?php echo $data[3]; ?>'/>");
                    $('#row_4_middle').html("<input class='input_box' id='place_founder' placeholder='EX: Leland Stanford' value='<?php echo $data[4]; ?>'/>");
                    $('#row_5_middle').html("<table><tr><td><input class='input_box' id='place_leader_name' placeholder='name' value='<?php echo $data[5][0]; ?>'/></td></tr><td><input class='input_box' id='place_leader_id' placeholder='page id OR link' value='<?php echo $data[5][1]; ?>'/><td></td></tr></table>");
                    
                    $('#row_2_right').html("<input class='red_button' value='Change' type='button' onClick='change_place_location();'/>");
                    $('#row_3_right').html("<input class='red_button' value='Change' type='button' onClick='change_place_size();'/>");
                    $('#row_4_right').html("<input class='red_button' value='Change' type='button' onClick='change_place_founder();'/>");
                    $('#row_5_right').html("<input class='red_button' value='Change' type='button' onClick='change_place_leader();' />");
                    
                    
                }
                else if(<?php if($other_type=='Product') echo "true"; else echo "false"; ?>==true)
                {
                    $('#row_0_left').html("<p class='settings_text'>Created: </p>");
                    
                    $('#information_table').html($('#information_table').html()+"<tr id='row_2'></tr><tr><td class='settings_break_unit' colspan='3'><hr class='settings_break'/></td></tr><tr id='row_3'></tr><tr><td class='settings_break_unit' colspan='3'><hr class='settings_break'/></td></tr><tr id='row_4'></tr><tr><td class='settings_break_unit' colspan='3'><hr class='settings_break'/></td></tr>");
                    
                    $('#row_2').html("<td class='settings_unit_left' id='row_2_left'></td><td class='settings_unit_middle' id='row_2_middle'></td><td class='settings_unit_right' id='row_2_right'></td>");
                    $('#row_3').html("<td class='settings_unit_left' id='row_3_left'></td><td class='settings_unit_middle' id='row_3_middle'></td><td class='settings_unit_right' id='row_3_right'></td>");
                    $('#row_4').html("<td class='settings_unit_left' id='row_4_left'></td><td class='settings_unit_middle' id='row_4_middle'></td><td class='settings_unit_right' id='row_4_right'></td>");
                    
                    $('#row_2_left').html("<p class='settings_text'>Company: </p>");
                    $('#row_3_left').html("<p class='settings_text'>Price: </p>");
                    $('#row_4_left').html("<p class='settings_text'>Purchase link: </p>");
                    
                    $('#row_2_middle').html("<table><tr><td><input class='input_box' type='text' id='product_company_name' placeholder='name' value='<?php echo $data[2][0]; ?>'/></td></tr><tr><td><input type='text' class='input_box' id='product_company_id' value='<?php echo $data[2][1]; ?>' placeholder='page id OR link'/></td></tr></table>");
                    $('#row_3_middle').html("<input class='input_box' id='product_price' placeholder='EX: $199.99' value='<?php echo $data[3]; ?>'/>");
                    $('#row_4_middle').html("<input class='input_box' id='product_purchase_link' placeholder='Link to website purchase area' value='<?php echo $data[4]; ?>'/>");
                    
                    $('#row_2_right').html("<input class='red_button' value='Change' type='button' onClick='change_product_company();'/>");
                    $('#row_3_right').html("<input class='red_button' value='Change' type='button' onClick='change_product_price();'/>");
                    $('#row_4_right').html("<input class='red_button' value='Change' type='button' onClick='change_product_purchase_link();'/>");
                }
                else if(<?php if($other_type=='Movie') echo "true"; else echo "false";?>==true)
                {
                    $('#row_0_left').html("<p class='settings_text'>Released: </p>");
                    
                    $('#information_table').html($('#information_table').html()+"<tr id='row_2'></tr><tr><td class='settings_break_unit' colspan='3'><hr class='settings_break'/></td></tr><tr id='row_3'></tr><tr><td class='settings_break_unit' colspan='3'><hr class='settings_break'/></td></tr><tr id='row_4'></tr><tr><td class='settings_break_unit' colspan='3'><hr class='settings_break'/></td><tr id='row_5'></tr>");
                    
                    $('#row_2').html("<td class='settings_unit_left' id='row_2_left'></td><td class='settings_unit_middle' id='row_2_middle'></td><td class='settings_unit_right' id='row_2_right'></td>");
                    $('#row_3').html("<td class='settings_unit_left' id='row_3_left'></td><td class='settings_unit_middle' id='row_3_middle'></td><td class='settings_unit_right' id='row_3_right'></td>");
                    $('#row_4').html("<td class='settings_unit_left' id='row_4_left'></td><td class='settings_unit_middle' id='row_4_middle'></td><td class='settings_unit_right' id='row_4_right'></td>");
                    $('#row_5').html("<td class='settings_unit_left' id='row_5_left'></td><td class='settings_unit_middle' id='row_5_middle'></td><td class='settings_unit_right' id='row_5_right'></td>");
                    
                    $('#row_2_left').html("<p class='settings_text'>Type: </p>");
                    $('#row_3_left').html("<p class='settings_text'>Studios: </p>");
                    $('#row_4_left').html("<p class='settings_text'>Starring: </p>");
                    $('#row_5_left').html("<p class='settings_text'>Rating: </p>");
                    
                    $('#row_2_middle').html("<input type='text' class='input_box' id='movie_type' value='<?php echo $data[4]; ?>' placeholder='EX: Comedy' />");
                    $('#row_3_middle').html("<div id='movie_studios_box' class='settings_box'></div>");
                    $('#row_4_middle').html("<div id='movie_starring_box' class='settings_box'></div>");
                    $('#row_5_middle').html("<input type='text' class='input_box' id='movie_rating' value='<?php echo $data[5]; ?>' placeholder='EX: PG-13'/>");
                    
                    $('#row_2_right').html("<input class='red_button' type='button' value='Change' onClick='change_movie_type();'/>");
                    $('#row_3_right').html("<input class='red_button' type='button' value='Change' onClick='add_movie_studio();'/>");
                    $('#row_4_right').html("<input class='red_button' type='button' value='Change' onClick='add_movie_starring();'/>");
                    $('#row_5_right').html("<input class='red_button' type='button' value='Change' onClick='change_movie_rating();'/>");
                    
                    display_movie_studio_settings();
                    display_movie_starring_settings();
                    
                }
                else if(<?php if($other_type=='TV Show') echo "true"; else echo "false";?>==true)
                {
                    $('#row_0_left').html("<p class='settings_text'>Started: </p>");
                    
                    $('#information_table').html($('#information_table').html()+"<tr id='row_2'></tr><tr><td class='settings_break_unit' colspan='3'><hr class='settings_break'/></td></tr><tr id='row_3'></tr><tr><td class='settings_break_unit' colspan='3'><hr class='settings_break'/></td></tr><tr id='row_4'></tr><tr><td class='settings_break_unit' colspan='3'><hr class='settings_break'/></td><tr id='row_5'></tr>");
                    
                    $('#row_2').html("<td class='settings_unit_left' id='row_2_left'></td><td class='settings_unit_middle' id='row_2_middle'></td><td class='settings_unit_right' id='row_2_right'></td>");
                    $('#row_3').html("<td class='settings_unit_left' id='row_3_left'></td><td class='settings_unit_middle' id='row_3_middle'></td><td class='settings_unit_right' id='row_3_right'></td>");
                    $('#row_4').html("<td class='settings_unit_left' id='row_4_left'></td><td class='settings_unit_middle' id='row_4_middle'></td><td class='settings_unit_right' id='row_4_right'></td>");
                    $('#row_5').html("<td class='settings_unit_left' id='row_5_left'></td><td class='settings_unit_middle' id='row_5_middle'></td><td class='settings_unit_right' id='row_5_right'></td>");
                    
                    $('#row_2_left').html("<p class='settings_text'>Type: </p>");
                    $('#row_3_left').html("<p class='settings_text'>Studios: </p>");
                    $('#row_4_left').html("<p class='settings_text'>Starring: </p>");
                    $('#row_5_left').html("<p class='settings_text'>Seasons: </p>");
                    
                    $('#row_2_middle').html("<input type='text' class='input_box' id='tv_show_type' value='<?php echo $data[2]; ?>' placeholder='EX: Comedy' />");
                    $('#row_3_middle').html("<div id='movie_studios_box' class='settings_box'></div>");
                    $('#row_4_middle').html("<div id='movie_starring_box' class='settings_box'></div>");
                    $('#row_5_middle').html("<input type='text' class='input_box' id='tv_show_seasons' value='<?php echo $data[5]; ?>' placeholder='EX: 5'/>");
                    
                    $('#row_2_right').html("<input class='red_button' type='button' value='Change' onClick='change_tv_show_type();'/>");
                    $('#row_3_right').html("<input class='red_button' type='button' value='Change' onClick='add_tv_show_studio();'/>");
                    $('#row_4_right').html("<input class='red_button' type='button' value='Change' onClick='add_tv_show_starring();'/>");
                    $('#row_5_right').html("<input class='red_button' type='button' value='Change' onClick='change_tv_show_num_seasons();'/>");
                    
                    display_tv_show_studio_settings();
                    display_tv_show_starring_settings();
                }
                else if(<?php if($other_type=='Book') echo "true"; else echo "false";?>==true)
                {
                    $('#row_0_left').html("<p class='settings_text'>Published: </p>");
                    
                    $('#information_table').html($('#information_table').html()+"<tr id='row_2'></tr><tr><td class='settings_break_unit' colspan='3'><hr class='settings_break'/></td></tr><tr id='row_3'></tr><tr><td class='settings_break_unit' colspan='3'><hr class='settings_break'/></td></tr><tr id='row_4'></tr><tr><td class='settings_break_unit' colspan='3'><hr class='settings_break'/></td><tr id='row_5'></tr><tr><td class='settings_break_unit' colspan='3'><hr class='settings_break'/></td></tr>");
                    
                    $('#row_2').html("<td class='settings_unit_left' id='row_2_left'></td><td class='settings_unit_middle' id='row_2_middle'></td><td class='settings_unit_right' id='row_2_right'></td>");
                    $('#row_3').html("<td class='settings_unit_left' id='row_3_left'></td><td class='settings_unit_middle' id='row_3_middle'></td><td class='settings_unit_right' id='row_3_right'></td>");
                    $('#row_4').html("<td class='settings_unit_left' id='row_4_left'></td><td class='settings_unit_middle' id='row_4_middle'></td><td class='settings_unit_right' id='row_4_right'></td>");
                    $('#row_5').html("<td class='settings_unit_left' id='row_5_left'></td><td class='settings_unit_middle' id='row_5_middle'></td><td class='settings_unit_right' id='row_5_right'></td>");
                    $('#row_6').html("<td class='settings_unit_left' id='row_6_left'></td><td class='settings_unit_middle' id='row_6_middle'></td><td class='settings_unit_right' id='row_6_right'></td>");
                    
                    $('#row_2_left').html("<p class='settings_text'>Type: </p>");
                    $('#row_3_left').html("<p class='settings_text'>Author: </p>");
                    $('#row_4_left').html("<p class='settings_text'>Number sold: </p>");
                    $('#row_5_left').html("<p class='settings_text'>Purchase link: </p>");
                    
                    $('#row_2_middle').html("<input class='input_box' type='text' placeholder='EX: Sci-fi' value='<?php echo $data[2]; ?>' id='book_type' />");
                    $('#row_3_middle').html("<table><tbody><tr><td><input class='input_box' type='text' placeholder='name' value='<?php echo $data[3][0]; ?>' id='book_author_name' /></td></tr><tr><td><input class='input_box' type='text' placeholder='page id OR link' value='<?php echo $data[3][1]; ?>' id='book_author_id' /></td></tr></tbody></table>");
                    $('#row_4_middle').html("<input class='input_box' type='text' placeholder='EX: 1,000,000' value='<?php echo $data[4]; ?>' id='book_num_sold' />");
                    $('#row_5_middle').html("<input class='input_box' type='text' placeholder='Link to website purchase area' value='<?php echo $data[5]; ?>' id='book_purchase_link' />");
                    
                    $('#row_2_right').html("<input class='red_button' type='button' value='Change' onClick='change_book_type();'/>");
                    $('#row_3_right').html("<input class='red_button' type='button' value='Change' onClick='change_book_author();'/>");
                    $('#row_4_right').html("<input class='red_button' type='button' value='Change' onClick='change_book_num_sold();'/>");
                    $('#row_5_right').html("<input class='red_button' type='button' value='Change' onClick='change_book_purchase_link();'/>");
                }
                else if(<?php if($other_type=='Website') echo "true"; else echo "false"; ?>==true)
                {
                    $('#row_0_left').html("<p class='settings_text'>Launched: </p>");
                    
                    $('#information_table').html($('#information_table').html()+"<tr id='row_2'></tr><tr><td class='settings_break_unit' colspan='3'><hr class='settings_break'/></td></tr><tr id='row_3'></tr>");
                    
                    $('#row_2').html("<td class='settings_unit_left' id='row_2_left'></td><td class='settings_unit_middle' id='row_2_middle'></td><td class='settings_unit_right' id='row_2_right'></td>");
                    $('#row_3').html("<td class='settings_unit_left' id='row_3_left'></td><td class='settings_unit_middle' id='row_3_middle'></td><td class='settings_unit_right' id='row_3_right'></td>");
                    
                    $('#row_2_left').html("<p class='settings_text'>Founders: </p>");
                    $('#row_3_left').html("<p class='settings_text'>Type: </p>");
                    
                    $('#row_2_middle').html("<table><tbody><tr><td><input class='input_box' type='text' placeholder='name' value='<?php echo $data[2][0]; ?>' id='website_add_founder_name'/></td></tr><tr><td><input class='input_box' type='text' placeholder='page id OR link' value='<?php echo $data[2][1]; ?>' id='website_add_founder_link'/></td></tr></tbody></table>");
                    $('#row_3_middle').html("<input class='input_box' type='text' placeholder='EX: Social network' value='<?php echo $data[3]; ?>' id='website_type'/>");
                    
                    $('#row_2_right').html("<input type='button' class='red_button' value='Change' onClick='change_website_founders();'/>");
                    $('#row_3_right').html("<input type='button' class='red_button' value='Change' onClick='change_website_type();'/>");
                }
                else if(<?php if($other_type=='Charity') echo "true"; else echo "false";?>==true)
                {
                    $('#row_0_left').html("<p class='settings_text'>Founded: </p>");
                    $('#information_table').html($('#information_table').html()+"<tr id='row_2'></tr>");

                    $('#row_2').html("<td class='settings_unit_left' id='row_2_left'></td><td class='settings_unit_middle' id='row_2_middle'></td><td class='settings_unit_right' id='row_2_right'></td>");
                    
                    $('#row_2_left').html("<p class='settings_text'>Cause: </p>");
                    $('#row_2_middle').html("<input class='input_box' type='text' id='charity_cause' value='<?php echo $data[2]; ?>' placeholder='EX: Welfare of animals'/>");
                    
                    $('#row_2_right').html("<input class='red_button' type='button' onClick='change_charity_cause();' value='Change'/>");
                }
                else if(<?php if($other_type=='Quote/Saying') echo "true"; else echo "false";?>==true)
                {
                    $('#row_0_left').html("<p class='settings_text'>Created: </p>");
                    
                    $('#information_table').html($('#information_table').html()+"<tr id='row_2'></tr><tr><td class='settings_break_unit' colspan='3'><hr class='settings_break'/></td></tr><tr id='row_3'></tr>");
                    
                    $('#row_2').html("<td class='settings_unit_left' id='row_2_left'></td><td class='settings_unit_middle' id='row_2_middle'></td><td class='settings_unit_right' id='row_2_right'></td>");
                    $('#row_3').html("<td class='settings_unit_left' id='row_3_left'></td><td class='settings_unit_middle' id='row_3_middle'></td><td class='settings_unit_right' id='row_3_right'></td>");
                    
                    $('#row_2_left').html("<p class='settings_text'>Creator: </p>");
                    $('#row_2_middle').html("<table><tbody><tr><td><input class='input_box' id='quote_saying_creator_name' placeholder='name' value='<?php echo $data[2][0]; ?>'/></td></tr><tr><td><input class='input_box' id='quote_saying_creator_link' placeholder='page id OR link' value='<?php echo $data[2][1]; ?>'/></td></tr></tbody></table>");
                    $('#row_2_right').html("<input class='red_button' onClick='change_quote_saying_creator();' type='button' value='Change'/>");
                    
                    $('#row_3_left').html("<p class='settings_text'>Origin:</p>");
                    $('#row_3_middle').html("<input class='input_box' id='quote_saying_origin' placeholder='EX: http://www.youtube.com/watch?v=q5nVqeVhgQE' value='<?php echo $data[3]; ?>'/>");
                    $('#row_3_right').html("<input class='red_button' type='button' onClick='change_quote_saying_origin();' value='Change'/>");
                    
                }
                $('.input_box').attr({'onFocus': "input_in(this);", 'onBlur': "input_out(this);"}).css('width', '200px');
                $('#change_bio_text').css('width', '300px');
            }
            function display_movie_studio_settings()
            {
                $.post('page_query.php',
                {
                    num:5,
                    page_id: <?php echo $_SESSION['page_id']; ?>
                }, function(output)
                {
                    var data=output.data;
                    
                    $('#movie_studios_box').html("<table id='movie_studio_table'></table>");
                    if(data[2][0]!='')
                    {
                        for(var x = 0; x < data[2].length; x++)
                            $('#movie_studio_table').html($('#movie_studio_table').html()+"<tr><td><input type='text' class='input_box' id='movie_studio_name_"+x+"' placeholder='name' value='"+data[2][x][0]+"' /></td><td><input type='text' class='input_box' id='movie_studio_link_"+x+"' placeholder='page id OR link' value='"+data[2][x][1]+"' /></td></tr>");
                    }
                    else
                        $('#movie_studio_table').html("<tr><td><p style='margin:0px;font-size:14px'>There are no studios listed</p></td></tr>");
                    
                    $('#movie_studios_box').html($('#movie_studios_box').html()+"<hr /><table id='movie_studio_add_table'><tr><td><input type='text' class='input_box' id='movie_studio_add_name' placeholder='Name' /></td><td><input type='text' class='input_box' id='movie_studio_add_id' placeholder='page id OR link' /></td></tr></table>");
                     $('.input_box').attr({'onFocus': "input_in(this);", 'onBlur': "input_out(this);"}).css('width', '200px');
                     
                     $('#change_bio_text').css('width', '300px');
                }, "json");
            }
            function display_movie_starring_settings()
            {
                $.post('page_query.php',
                {
                    num:5,
                    page_id: <?php echo $_SESSION['page_id']; ?>
                }, function(output)
                {
                    var data=output.data;
                    
                    $('#movie_starring_box').html("<table id='movie_starring_table'></table>");
                    if(data[3][0]!='')
                    {
                        for(var x = 0; x < data[3].length; x++)
                            $('#movie_starring_table').html($('#movie_starring_table').html()+"<tr><td><input type='text' class='input_box' id='movie_starring_name_"+x+"' placeholder='name' value='"+data[3][x][0]+"' /></td><td><input type='text' class='input_box' id='movie_starring_link_"+x+"' placeholder='page id OR link' value='"+data[3][x][1]+"' /></td></tr>");
                    }
                    else
                        $('#movie_starring_table').html("<tr><td><p style='margin:0px;font-size:14px'>There are no actors listed</p></td></tr>");
                    
                    $('#movie_starring_box').html($('#movie_starring_box').html()+"<hr /><table id='movie_starring_add_table'><tr><td><input type='text' class='input_box' id='movie_starring_add_name' placeholder='Name' /></td><td><input type='text' class='input_box' id='movie_starring_add_id' placeholder='page id OR link' /></td></tr></table>");
                     $('.input_box').attr({'onFocus': "input_in(this);", 'onBlur': "input_out(this);"}).css('width', '200px');
                     
                     $('#change_bio_text').css('width', '300px');
                }, "json");
            }
            function display_tv_show_studio_settings()
            {
                $.post('page_query.php',
                {
                    num:5,
                    page_id: <?php echo $_SESSION['page_id']; ?>
                }, function(output)
                {
                    var data=output.data;
                    
                    $('#movie_studios_box').html("<table id='tv_show_studio_table'></table>");
                    if(data[3][0]!='')
                    {
                        for(var x = 0; x < data[3].length; x++)
                            $('#tv_show_studio_table').html($('#tv_show_studio_table').html()+"<tr><td><input type='text' class='input_box' id='tv_show_studio_name_"+x+"' placeholder='name' value='"+data[3][x][0]+"' /></td><td><input type='text' class='input_box' id='tv_show_studio_link_"+x+"' placeholder='page id OR link' value='"+data[3][x][1]+"' /></td></tr>");
                    }
                    else
                        $('#tv_show_studio_table').html("<tr><td><p style='margin:0px;font-size:14px'>There are no studios listed</p></td></tr>");
                    
                    $('#movie_studios_box').html($('#movie_studios_box').html()+"<hr /><table id='tv_show_studio_add_table'><tr><td><input type='text' class='input_box' id='tv_show_studio_add_name' placeholder='Name' /></td><td><input type='text' class='input_box' id='tv_show_studio_add_id' placeholder='page id OR link' /></td></tr></table>");
                     $('.input_box').attr({'onFocus': "input_in(this);", 'onBlur': "input_out(this);"}).css('width', '200px');
                     
                     $('#change_bio_text').css('width', '300px');
                }, "json");
            }
            function display_tv_show_starring_settings()
            {
                $.post('page_query.php',
                {
                    num:5,
                    page_id: <?php echo $_SESSION['page_id']; ?>
                }, function(output)
                {
                    var data=output.data;
                    
                    $('#movie_starring_box').html("<table id='tv_show_starring_table'></table>");
                    if(data[4][0]!='')
                    {
                        for(var x = 0; x < data[4].length; x++)
                            $('#tv_show_starring_table').html($('#tv_show_starring_table').html()+"<tr><td><input type='text' class='input_box' id='tv_show_starring_name_"+x+"' placeholder='name' value='"+data[4][x][0]+"' /></td><td><input type='text' class='input_box' id='movie_starring_link_"+x+"' placeholder='page id OR link' value='"+data[4][x][1]+"' /></td></tr>");
                    }
                    else
                        $('#tv_show_starring_table').html("<tr><td><p style='margin:0px;font-size:14px'>There are no actors listed</p></td></tr>");
                    
                    $('#movie_starring_box').html($('#movie_starring_box').html()+"<hr /><table id='tv_show_starring_add_table'><tr><td><input type='text' class='input_box' id='tv_show_starring_add_name' placeholder='Name' /></td><td><input type='text' class='input_box' id='tv_show_starring_add_id' placeholder='page id OR link' /></td></tr></table>");
                     $('.input_box').attr({'onFocus': "input_in(this);", 'onBlur': "input_out(this);"}).css('width', '200px');
                     
                     $('#change_bio_text').css('width', '300px');
                }, "json");
            }
            
            function display_actors_movies()
            {
                $.post('page_query.php',
                {
                    num:5,
                    page_id: <?php echo $_SESSION['page_id']; ?>
                }, function(output)
                {
                    var data=output.data;
                    
                    $('#actors_movies_box').html("<table id='actors_movies_table'></table>");
                    if(data[4][0]!='')
                    {
                        for(var x = 0; x < data[4].length; x++)
                            $('#actors_movies_table').html($('#actors_movies_table').html()+"<tr><td><input type='text' class='input_box' id='actors_movies_name"+x+"' placeholder='name' value='"+data[4][x][0]+"' /></td><td><input type='text' class='input_box' id='actors_movies_link_"+x+"' placeholder='page id OR link' value='"+data[4][x][1]+"' /></td></tr>");
                    }
                    else
                        $('#actors_movies_table').html("<tr><td><p style='margin:0px;font-size:14px'>There are no movies listed</p></td></tr>");
                    
                    $('#actors_movies_box').html($('#actors_movies_box').html()+"<hr /><table id='actors_movies_add_table'><tr><td><input type='text' class='input_box' id='actors_movies_add_name' placeholder='Name' /></td><td><input type='text' class='input_box' id='actors_movies_add_id' placeholder='page id OR link' /></td></tr></table>");
                     $('.input_box').attr({'onFocus': "input_in(this);", 'onBlur': "input_out(this);"}).css('width', '200px');
                     
                     $('#change_bio_text').css('width', '300px');
                }, "json");
            }
            function display_actors_tv_shows()
            {
                $.post('page_query.php',
                {
                    num:5,
                    page_id: <?php echo $_SESSION['page_id']; ?>
                }, function(output)
                {
                    var data=output.data;
                    
                    $('#actors_tv_shows_box').html("<table id='actors_tv_shows_table'></table>");
                    if(data[5][0]!='')
                    {
                        for(var x = 0; x < data[5].length; x++)
                            $('#actors_tv_shows_table').html($('#actors_tv_shows_table').html()+"<tr><td><input type='text' class='input_box' id='actors_tv_shows_name"+x+"' placeholder='name' value='"+data[5][x][0]+"' /></td><td><input type='text' class='input_box' id='actors_tv_shows_link_"+x+"' placeholder='page id OR link' value='"+data[5][x][1]+"' /></td></tr>");
                    }
                    else
                        $('#actors_tv_shows_table').html("<tr><td><p style='margin:0px;font-size:14px'>There are no TV shows listed</p></td></tr>");
                    
                    $('#actors_tv_shows_box').html($('#actors_tv_shows_box').html()+"<hr /><table id='actors_tv_shows_add_table'><tr><td><input type='text' class='input_box' id='actors_tv_shows_add_name' placeholder='Name' /></td><td><input type='text' class='input_box' id='actors_tv_shows_add_id' placeholder='page id OR link' /></td></tr></table>");
                     $('.input_box').attr({'onFocus': "input_in(this);", 'onBlur': "input_out(this);"}).css('width', '200px');
                     
                     $('#change_bio_text').css('width', '300px');
                }, "json");
            }
            function display_actors_commercials()
            {
                $.post('page_query.php',
                {
                    num:5,
                    page_id: <?php echo $_SESSION['page_id']; ?>
                }, function(output)
                {
                    var data=output.data;
                    
                    $('#actors_commercials_box').html("<table id='actors_commercials_table'></table>");
                    if(data[6][0]!='')
                    {
                        for(var x = 0; x < data[6].length; x++)
                            $('#actors_commercials_table').html($('#actors_commercials_table').html()+"<tr><td><input type='text' class='input_box' id='actors_commercials_name"+x+"' placeholder='name' value='"+data[6][x][0]+"' /></td><td><input type='text' class='input_box' id='actors_commercials_link_"+x+"' placeholder='page id OR link' value='"+data[6][x][1]+"' /></td></tr>");
                    }
                    else
                        $('#actors_commercials_table').html("<tr><td><p style='margin:0px;font-size:14px'>There are no commercials listed</p></td></tr>");
                    
                    $('#actors_commercials_box').html($('#actors_commercials_box').html()+"<hr /><table id='actors_commercials_add_table'><tr><td><input type='text' class='input_box' id='actors_commercials_add_name' placeholder='Name' /></td><td><input type='text' class='input_box' id='actors_commercials_add_id' placeholder='page id OR link' /></td></tr></table>");
                     $('.input_box').attr({'onFocus': "input_in(this);", 'onBlur': "input_out(this);"}).css('width', '200px');
                     
                     $('#change_bio_text').css('width', '300px');
                }, "json");
            }
            function display_actors_others()
            {
                $.post('page_query.php',
                {
                    num:5,
                    page_id: <?php echo $_SESSION['page_id']; ?>
                }, function(output)
                {
                    var data=output.data;
                    
                    $('#actors_others_box').html("<table id='actors_others_table'></table>");
                    if(data[7][0]!='')
                    {
                        for(var x = 0; x < data[7].length; x++)
                            $('#actors_others_table').html($('#actors_others_table').html()+"<tr><td><input type='text' class='input_box' id='actors_others_name"+x+"' placeholder='name' value='"+data[7][x][0]+"' /></td><td><input type='text' class='input_box' id='actors_others_link_"+x+"' placeholder='page id OR link' value='"+data[7][x][1]+"' /></td></tr>");
                    }
                    else
                        $('#actors_others_table').html("<tr><td><p style='margin:0px;font-size:14px'>There are no other works listed</p></td></tr>");
                    
                    $('#actors_others_box').html($('#actors_others_box').html()+"<hr /><table id='actors_others_add_table'><tr><td><input type='text' class='input_box' id='actors_others_add_name' placeholder='Name' /></td><td><input type='text' class='input_box' id='actors_others_add_id' placeholder='page id OR link' /></td></tr></table>");
                     $('.input_box').attr({'onFocus': "input_in(this);", 'onBlur': "input_out(this);"}).css('width', '200px');
                     
                     $('#change_bio_text').css('width', '300px');
                }, "json");
            }
            function display_author_books()
            {
                $.post('page_query.php',
                {
                    num:5,
                    page_id: <?php echo $_SESSION['page_id']; ?>
                }, function(output)
                {
                    var data=output.data;
                    
                    $('#authors_books_box').html("<table id='authors_books_table'></table>");
                    if(data[5][0]!='')
                    {
                        for(var x = 0; x < data[5].length; x++)
                            $('#authors_books_table').html($('#authors_books_table').html()+"<tr><td><input type='text' class='input_box' id='authors_books_name_"+x+"' placeholder='name' value='"+data[5][x][0]+"' /></td><td><input type='text' class='input_box' id='authors_books_link_"+x+"' placeholder='page id OR link' value='"+data[5][x][1]+"' /></td></tr>");
                    }
                    else
                        $('#authors_books_table').html("<tr><td><p style='margin:0px;font-size:14px'>There are no books listed</p></td></tr>");
                    
                    $('#authors_books_box').html($('#authors_books_box').html()+"<hr /><table id='authors_books_add_table'><tr><td><input type='text' class='input_box' id='authors_books_add_name' placeholder='Name' /></td><td><input type='text' class='input_box' id='authors_books_add_id' placeholder='page id OR link' /></td></tr></table>");
                     $('.input_box').attr({'onFocus': "input_in(this);", 'onBlur': "input_out(this);"}).css('width', '200px');
                     
                     $('#change_bio_text').css('width', '300px');
                }, "json");
            }
            function display_athletes_awards()
            {
                $.post('page_query.php',
                {
                    num:5,
                    page_id: <?php echo $_SESSION['page_id']; ?>
                }, function(output)
                {
                    var data=output.data;
                    
                    $('#athletes_awards_box').html("<table id='athletes_awards_table'></table>");
                    if(data[6][0]!='')
                    {
                        for(var x = 0; x < data[6].length; x++)
                            $('#athletes_awards_table').html($('#athletes_awards_table').html()+"<tr><td><input type='text' class='input_box' id='athletes_awards_name_"+x+"' placeholder='name' value='"+data[6][x][0]+"' /></td></tr>");
                    }
                    else
                        $('#athletes_awards_table').html("<tr><td><p style='margin:0px;font-size:14px'>There are no awards listed</p></td></tr>");
                    
                    $('#athletes_awards_box').html($('#athletes_awards_box').html()+"<hr /><table id='athletes_awards_add_table'><tr><td><input type='text' class='input_box' id='athletes_awards_add_name' placeholder='Name' /></td></tr></table>");
                     $('.input_box').attr({'onFocus': "input_in(this);", 'onBlur': "input_out(this);"}).css('width', '200px');
                     
                     $('#change_bio_text').css('width', '300px');
                }, "json");
            }
            function display_comedians_venues()
            {
                $.post('page_query.php',
                {
                    num:5,
                    page_id: <?php echo $_SESSION['page_id']; ?>
                }, function(output)
                {
                    var data=output.data;
                    
                    $('#comedians_venues_box').html("<table id='comedians_venues_table'></table>");
                    if(data[4][0]!='')
                    {
                        for(var x = 0; x < data[4].length; x++)
                            $('#comedians_venues_table').html($('#comedians_venues_table').html()+"<tr><td><input type='text' class='input_box' id='comedians_venues_name_"+x+"' placeholder='name' value='"+data[4][x][0]+"' /></td></tr>");
                    }
                    else
                        $('#comedians_venues_table').html("<tr><td><p style='margin:0px;font-size:14px'>There are no awards listed</p></td></tr>");
                    
                    $('#comedians_venues_box').html($('#comedians_venues_box').html()+"<hr /><table id='comedians_venues_add_table'><tr><td><input type='text' class='input_box' id='comedians_venues_add_name' placeholder='Name' /></td></tr></table>");
                     $('.input_box').attr({'onFocus': "input_in(this);", 'onBlur': "input_out(this);"}).css('width', '200px');
                     
                     $('#change_bio_text').css('width', '300px');
                }, "json");
            }
            function display_character_quotes()
            {
                $.post('page_query.php',
                {
                    num:5,
                    page_id: <?php echo $_SESSION['page_id']; ?>
                }, function(output)
                {
                    var data=output.data;
                    
                    $('#character_quotes_box').html("<table id='character_quotes_table'></table>");
                    if(data[5][0]!='')
                    {
                        for(var x = 0; x < data[5].length; x++)
                            $('#character_quotes_table').html($('#character_quotes_table').html()+"<tr><td><input type='text' class='input_box' id='character_quotes_name_"+x+"' placeholder='name' value='"+data[5][x][0]+"' /></td><td><input type='text' class='input_box' id='character_quotes_link_"+x+"' placeholder='page id OR link' value='"+data[5][x][1]+"' /></td></tr>");
                    }
                    else
                        $('#character_quotes_table').html("<tr><td><p style='margin:0px;font-size:14px'>There are no quotes listed</p></td></tr>");
                    
                    $('#character_quotes_box').html($('#character_quotes_box').html()+"<hr /><table id='character_quotes_add_table'><tr><td><input type='text' class='input_box' id='character_quotes_add_name' placeholder='Name' /></td><td><input type='text' class='input_box' id='character_quotes_add_id' placeholder='page id OR link' /></td></tr></table>");
                     $('.input_box').attr({'onFocus': "input_in(this);", 'onBlur': "input_out(this);"}).css('width', '200px');
                     
                     $('#change_bio_text').css('width', '300px');
                }, "json");
            }
            function display_band_members()
            {
                $.post('page_query.php',
                {
                    num:5,
                    page_id: <?php echo $_SESSION['page_id']; ?>
                }, function(output)
                {
                    var data=output.data;
                    
                    $('#band_members_box').html("<table id='band_members_table'></table>");
                    if(data[4][0]!='')
                    {
                        for(var x = 0; x < data[4].length; x++)
                            $('#band_members_table').html($('#band_members_table').html()+"<tr><td><input type='text' class='input_box' id='band_members_name_"+x+"' placeholder='name' value='"+data[4][x][0]+"' /></td><td><input type='text' class='input_box' id='band_members_link_"+x+"' placeholder='page id OR link' value='"+data[4][x][1]+"' /></td></tr>");
                    }
                    else
                        $('#band_members_table').html("<tr><td><p style='margin:0px;font-size:14px'>There are no members listed</p></td></tr>");
                    
                    $('#band_members_box').html($('#band_members_box').html()+"<hr /><table id='band_members_add_table'><tr><td><input type='text' class='input_box' id='band_members_add_name' placeholder='Name' /></td><td><input type='text' class='input_box' id='band_members_add_id' placeholder='page id OR link' /></td></tr></table>");
                     $('.input_box').attr({'onFocus': "input_in(this);", 'onBlur': "input_out(this);"}).css('width', '200px');
                     
                     $('#change_bio_text').css('width', '300px');
                }, "json");
            }
            function display_band_songs()
            {
                $.post('page_query.php',
                {
                    num:5,
                    page_id: <?php echo $_SESSION['page_id']; ?>
                }, function(output)
                {
                    var data=output.data;
                    
                    $('#band_songs_box').html("<table id='band_songs_table'></table>");
                    if(data[7][0]!='')
                    {
                        for(var x = 0; x < data[7].length; x++)
                            $('#band_songs_table').html($('#band_songs_table').html()+"<tr><td><input type='text' class='input_box' id='band_songs_name_"+x+"' placeholder='name' value='"+data[7][x][0]+"' /></td><td><input type='text' class='input_box' id='band_songs_link_"+x+"' placeholder='page id OR link' value='"+data[7][x][1]+"' /></td></tr>");
                    }
                    else
                        $('#band_songs_table').html("<tr><td><p style='margin:0px;font-size:14px'>There are no songs listed</p></td></tr>");
                    
                    $('#band_songs_box').html($('#band_songs_box').html()+"<hr /><table id='band_members_add_table'><tr><td><input type='text' class='input_box' id='band_songs_add_name' placeholder='Name' /></td><td><input type='text' class='input_box' id='band_songs_add_id' placeholder='page id OR link' /></td></tr></table>");
                     $('.input_box').attr({'onFocus': "input_in(this);", 'onBlur': "input_out(this);"}).css('width', '200px');
                     
                     $('#change_bio_text').css('width', '300px');
                }, "json");
            }
            function display_director_movies()
            {
                $.post('page_query.php',
                {
                    num:5,
                    page_id: <?php echo $_SESSION['page_id']; ?>
                }, function(output)
                {
                    var data=output.data;
                    
                    $('#director_movies_box').html("<table id='director_movies_table'></table>");
                    if(data[4][0]!='')
                    {
                        for(var x = 0; x < data[4].length; x++)
                            $('#director_movies_table').html($('#director_movies_table').html()+"<tr><td><input type='text' class='input_box' id='director_movies_name_"+x+"' placeholder='name' value='"+data[4][x][0]+"' /></td><td><input type='text' class='input_box' id='director_movies_link_"+x+"' placeholder='page id OR link' value='"+data[4][x][1]+"' /></td></tr>");
                    }
                    else
                        $('#director_movies_table').html("<tr><td><p style='margin:0px;font-size:14px'>There are no movies listed</p></td></tr>");
                    
                    $('#director_movies_box').html($('#director_movies_box').html()+"<hr /><table id='director_movies_add_table'><tr><td><input type='text' class='input_box' id='director_movies_add_name' placeholder='Name' /></td><td><input type='text' class='input_box' id='director_movies_add_id' placeholder='page id OR link' /></td></tr></table>");
                     $('.input_box').attr({'onFocus': "input_in(this);", 'onBlur': "input_out(this);"}).css('width', '200px');
                     
                     $('#change_bio_text').css('width', '300px');
                }, "json");
            }
            function display_director_tv_shows()
            {
                $.post('page_query.php',
                {
                    num:5,
                    page_id: <?php echo $_SESSION['page_id']; ?>
                }, function(output)
                {
                    var data=output.data;
                    
                    $('#director_tv_shows_box').html("<table id='director_tv_shows_table'></table>");
                    if(data[5][0]!='')
                    {
                        for(var x = 0; x < data[5].length; x++)
                            $('#director_tv_shows_table').html($('#director_tv_shows_table').html()+"<tr><td><input type='text' class='input_box' id='director_tv_shows_name_"+x+"' placeholder='name' value='"+data[5][x][0]+"' /></td><td><input type='text' class='input_box' id='director_tv_shows_link_"+x+"' placeholder='page id OR link' value='"+data[5][x][1]+"' /></td></tr>");
                    }
                    else
                        $('#director_tv_shows_table').html("<tr><td><p style='margin:0px;font-size:14px'>There are no TV shows listed</p></td></tr>");
                    
                    $('#director_tv_shows_box').html($('#director_tv_shows_box').html()+"<hr /><table id='director_tv_shows_add_table'><tr><td><input type='text' class='input_box' id='director_tv_shows_add_name' placeholder='Name' /></td><td><input type='text' class='input_box' id='director_tv_shows_add_id' placeholder='page id OR link' /></td></tr></table>");
                     $('.input_box').attr({'onFocus': "input_in(this);", 'onBlur': "input_out(this);"}).css('width', '200px');
                     
                     $('#change_bio_text').css('width', '300px');
                }, "json");
            }
            function display_director_commercials()
            {
                $.post('page_query.php',
                {
                    num:5,
                    page_id: <?php echo $_SESSION['page_id']; ?>
                }, function(output)
                {
                    var data=output.data;
                    
                    $('#director_commercials_box').html("<table id='director_commercials_table'></table>");
                    if(data[6][0]!='')
                    {
                        for(var x = 0; x < data[6].length; x++)
                            $('#director_commercials_table').html($('#director_commercials_table').html()+"<tr><td><input type='text' class='input_box' id='director_commercials_name_"+x+"' placeholder='name' value='"+data[6][x][0]+"' /></td><td><input type='text' class='input_box' id='director_commercials_link_"+x+"' placeholder='page id OR link' value='"+data[6][x][1]+"' /></td></tr>");
                    }
                    else
                        $('#director_commercials_table').html("<tr><td><p style='margin:0px;font-size:14px'>There are no commercials listed</p></td></tr>");
                    
                    $('#director_commercials_box').html($('#director_commercials_box').html()+"<hr /><table id='director_commercials_add_table'><tr><td><input type='text' class='input_box' id='director_commercials_add_name' placeholder='Name' /></td><td><input type='text' class='input_box' id='director_commercials_add_id' placeholder='page id OR link' /></td></tr></table>");
                     $('.input_box').attr({'onFocus': "input_in(this);", 'onBlur': "input_out(this);"}).css('width', '200px');
                     
                     $('#change_bio_text').css('width', '300px');
                }, "json");
            }
            function display_director_others()
            {
                $.post('page_query.php',
                {
                    num:5,
                    page_id: <?php echo $_SESSION['page_id']; ?>
                }, function(output)
                {
                    var data=output.data;
                    
                    $('#director_others_box').html("<table id='director_others_table'></table>");
                    if(data[7][0]!='')
                    {
                        for(var x = 0; x < data[7].length; x++)
                            $('#director_others_table').html($('#director_others_table').html()+"<tr><td><input type='text' class='input_box' id='director_others_name_"+x+"' placeholder='name' value='"+data[7][x][0]+"' /></td><td><input type='text' class='input_box' id='director_others_link_"+x+"' placeholder='page id OR link' value='"+data[7][x][1]+"' /></td></tr>");
                    }
                    else
                        $('#director_others_table').html("<tr><td><p style='margin:0px;font-size:14px'>There are no movies listed</p></td></tr>");
                    
                    $('#director_others_box').html($('#director_others_box').html()+"<hr /><table id='director_others_add_table'><tr><td><input type='text' class='input_box' id='director_others_add_name' placeholder='Name' /></td><td><input type='text' class='input_box' id='director_others_add_id' placeholder='page id OR link' /></td></tr></table>");
                     $('.input_box').attr({'onFocus': "input_in(this);", 'onBlur': "input_out(this);"}).css('width', '200px');
                     
                     $('#change_bio_text').css('width', '300px');
                }, "json");
            }
            function display_public_figure_known_for()
            {
                $.post('page_query.php',
                {
                    num:5,
                    page_id: <?php echo $_SESSION['page_id']; ?>
                }, function(output)
                {
                    var data=output.data;
                    
                    $('#public_figures_box').html("<table id='public_figures_table'></table>");
                    if(data[4][0]!='')
                    {
                        for(var x = 0; x < data[4].length; x++)
                            $('#public_figures_table').html($('#public_figures_table').html()+"<tr><td><input type='text' class='input_box' id='public_figures_name_"+x+"' placeholder='description' value='"+data[4][x][0]+"' /></td><td><input type='text' class='input_box' id='public_figures_link_"+x+"' placeholder='link' value='"+data[4][x][1]+"' /></td></tr>");
                    }
                    else
                        $('#public_figures_table').html("<tr><td><p style='margin:0px;font-size:14px'>There are no links listed</p></td></tr>");
                    
                    $('#public_figures_box').html($('#public_figures_box').html()+"<hr /><table id='public_figures_add_table'><tr><td><input type='text' class='input_box' id='public_figures_add_name' placeholder='description' /></td><td><input type='text' class='input_box' id='public_figures_add_id' placeholder='link' /></td></tr></table>");
                     $('.input_box').attr({'onFocus': "input_in(this);", 'onBlur': "input_out(this);"}).css('width', '200px');
                     
                     $('#change_bio_text').css('width', '300px');
                }, "json");
            }
        </script>
        <script type="text/javascript">
            $(document).ready(function()
            {
                <?php
                    if(!has_redlay_gold_page($_SESSION['page_id']))
                        echo "$('#background_picture_unit, #profile_colors').html('<p>There is one hidden feature. Click <a href=\'http://www.redlay.com/redlay_gold.php\'>here</a> to get it</p>');";
                    
                    if($type=='Company')
                        echo "display_company();";
                    else if($type=='Person')
                        echo "display_person();";
                    else if($type=='Other')
                        echo "display_other();";
                ?>
                $('.loading_gif_body').hide();
                $('#footer').css({'width': '680px', 'margin-left': '225px'});
                <?php $path="./users/pages/".$_SESSION['page_id']."/background.jpg"; if(file_exists($path)) echo "$('body').css({'background-image': 'url(\'$path\')', 'background-position' :'center 50px'});"; else echo "$('body').css({'background-image': 'url(\'./pictures/default_background.png\')', 'background-position' :'center 50px'});"; ?>
                display_settings_information();
                change_color();
                <?php include('required_jquery.php'); ?>
            });
        </script>
        <script type="text/javascript">

          var _gaq = _gaq || [];
          _gaq.push(['_setAccount', 'UA-33379005-1']);
          _gaq.push(['_setDomainName', 'redlay.com']);
          _gaq.push(['_trackPageview']);

          (function() {
            var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
            ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
            var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
          })();

        </script>
    </head>
    <body>
        <?php include('required_html.php'); ?>
        <div id="main" style="width:910px">
            <div id="top">
                <?php include('top_page.php'); ?>
            </div>
            <div id="settings_menu" class="box">
                <p id="settings_menu_information" class="settings_menu" onClick="display_settings_information();" onmouseover="{$(this).css('text-decoration', 'underline');}" onmouseout="{$(this).css('text-decoration', 'none');}" >Information</p>
                <p id="settings_menu_images" class="settings_menu" onClick="display_settings_images();" onmouseover="{$(this).css('text-decoration', 'underline');}" onmouseout="{$(this).css('text-decoration', 'none');}" >Images</p>
                <p id="settings_menu_display" class="settings_menu" onClick="display_settings_display();" onmouseover="{$(this).css('text-decoration', 'underline');}" onmouseout="{$(this).css('text-decoration', 'none');}" >Display</p>
                <p id="settings_menu_other" class="settings_menu" onClick="display_settings_other();" onmouseover="{$(this).css('text-decoration', 'underline');}" onmouseout="{$(this).css('text-decoration', 'none');}" >Other</p>
            </div>
                <div id="page_settings" class="box">
                    <p class="settings_title" id="settings_title">Settings</p>
                    <div id="settings_text">
                        <div id="settings_information">
                            <table class="settings_table" id="information_table">
                                <tr>
                                    <td class="settings_unit_left"><p id="current_password_text"  class="settings_text">Current Password: </p></td>
                                    <td class="settings_unit_middle"><input id="current_password" class="input_box" onFocus="input_in(this);" onBlur="input_out(this);" name="current_password" type="password" placeholder="Current Password"/></td>
                                    <td class="settings_unit_right"></td>
                                </tr>
                                <tr>
                                    <td class="settings_unit_left"><p id="new_password_text"  class="settings_text">New Password: </p></td>
                                    <td class="settings_unit_middle"><input id="new_password" class="input_box" onFocus="input_in(this);" onBlur="input_out(this);" name="new_password" type="password" placeholder="New Password"/></td>
                                    <td class="settings_unit_right"><input id="submit_new_password" class="settings_button red_button" type="button" onmouseover="{display_title(this, 'Changes your password');}" onmouseout="{hide_title(this);}" onClick="change_password();" name="submit_new_password" value="Change" /></td>
                                </tr>
                                <tr>
                                    <td class="settings_unit_left"><p id="confirm_new_password_text"  class="settings_text">Confirm New Password: </p></td>
                                    <td class="settings_unit_middle"><input id="confirm_new_password" class="input_box" onFocus="input_in(this);" onBlur="input_out(this);" name="confirm_new_password" type="password" placeholder="Confirm Password"/></td>
                                    <td class="settings_unit_right"></td>
                                </tr>
                                <!--<tr><td class="settings_break_unit" colspan="3"><hr class="settings_break"/></td></tr>-->
                                
<!--                                <tr>
                                    <td class="settings_unit_left"><p id="month_birthday_title"  class="settings_text"><?php echo get_created_title($type, $other_type); ?>: </p></td>
                                    <td class="settings_unit_middle"></td>
                                    <td class="settings_unit_right"><input class="settings_button red_button"  id="submit_birthday" type="button" value="Change" onClick="change_created_date();" onmouseover="{display_title(this, 'Change your birthday');}" onmouseout="{hide_title(this);}"/></td>
                                </tr>-->
                                
                                <tr><td class="settings_break_unit" colspan="3"><hr class="settings_break"/></td></tr>
                                <tr>
                                    <td class="settings_unit_left"><p id="bio_text"  class="settings_text">Description: </p></td>
                                    <td class="settings_unit_middle"><textarea id="change_bio_text" class="settings_input input_box" onFocus="input_in(this);" onBlur="input_out(this);" maxlength="1200" ><?php echo $description; ?></textarea></td>
                                    <td class="settings_unit_right"><input class="settings_button red_button" type="button" onClick="change_description();" id="submit_bio_button" value="Change" onmouseover="{display_title(this, 'Change your description');}" onmouseout="{hide_title(this);}" /></td>
                                </tr>
                                <tr><td class="settings_break_unit" colspan="3"><hr class="settings_break"/></td></tr>
                            </table>
                        </div>
                        <div id="settings_images">
                            <table class="settings_table">
                                <tbody>
                                    <tr>
                                        <td id="profile_picture_unit">
                                            <form action="change_page_profile_picture.php" method="post" enctype="multipart/form-data">
                                                <table class="settings_table">
                                                    <tbody>
                                                        <tr>
                                                            <td class="settings_unit_left"><p id="profile_picture_text"  class="settings_text">Profile picture: </p></td>
                                                            <td class="settings_unit_middle">
                                                                <input class="file_button" id="profile_picture_settings" type="file" name="image"/>
                                                                <div id="loading_gif_profile_picture" class="loading_gif_body"><img class="load_gif" src="load.gif"/></div>
                                                            </td>
                                                            <td class="settings_unit_right">
                                                                <input class="settings_button_disabled" id="submit_profile_picture" onClick="$('#loading_gif_profile_picture').show();" type="submit" name="submit_profile_picture" value="Upload" disabled="disabled"  onmouseover="{display_title(this, 'Change your profile picture');}" onmouseout="{hide_title(this);}" />
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </form>
                                        </td>
                                    </tr>

                                    <tr><td class="settings_break_unit" colspan="3"><hr class="settings_break"/></td></tr>

                                    <tr>
                                        <td id="background_picture_unit">
                                            <form action="change_background_picture.php" method="post" enctype="multipart/form-data">
                                                <table class="settings_table">
                                                    <tbody>
                                                        <tr class="background_image_row" id="background_image_row_1">
                                                            <td class="background_image_unit" id="background_image_unit_1"><p id="change_background_picture_text" class="settings_text">Background Image: </p></td>
                                                            <td class="background_image_unit" id="background_image_unit_1"><input type="file" class="file_button" <?php  ?> id="change_background_picture_input" name="image"/></td>
                                                            <td class="background_image_unit" id="background_image_unit_1"><input type="submit"  onClick="$('#loading_gif_background_picture').show();" id="background_picture_submit" class="red_button" name="submit_background_picture" value="Upload" onmouseover="{display_title(this, 'Change profile background image');}" onmouseout="{hide_title(this);}" /></td>
                                                        </tr>
                                                        <tr class="background_image_row" id="background_image_row_2">
                                                            <td class="settings_unit_left"></td>
                                                            <td class="settings_unit_middle" id="change_background_picture_fixed"><span id="change_background_fixed_text" class="settings_text">Fixed background: </span><input type="checkbox" value="yes" <?php if($background_fixed=='yes') echo "checked=='checked'"; ?> name="change_background_fixed_checkbox" id="change_background_fixed_input" /><div id="loading_gif_background_picture" class="loading_gif_body"><img class="load_gif" src="load.gif"/></div></td>
                                                            <td class="settings_unit_right"></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </form>
                                        </td>
                                    </tr>


                                </tbody>
                            </table>
                        </div>
                        <div id="settings_display">
                            <div id="profile_colors">
                                <table class="settings_table" id="">
                                    <tbody id="color_table_body">
                                        <tr >
                                            <td class="settings_unit_left"><p class="settings_text">Border color:</p></td>
                                            <td class="settings_unit_middle"><input type="button" class="red_button" id="border_color_button" value="Change" onClick="display_color_wheel(1);" onmouseover="{display_title(this, 'Changes the border colors around the boxes');}" onmouseout="{ hide_title(this);}" /></td>
                                            <td class="settings_unit_right"></td>
                                        </tr>
                                        <tr >
                                            <td class="settings_unit_left"><p class="settings_text">Background color:</p></td>
                                            <td class="settings_unit_middle"><input type="button" class="red_button" id="border_color_button" value="Change" onClick="display_color_wheel(2);" onmouseover="{display_title(this, 'Changes the background color of the boxes');}" onmouseout="{hide_title(this);}" /></td>
                                            <td class="settings_unit_right"></td>
                                        </tr>
                                        <tr >
                                            <td class="settings_unit_left"><p class="settings_text">Text color:</p></td>
                                            <td class="settings_unit_middle"><input type="button" class="red_button" id="border_color_button" value="Change" onClick="display_color_wheel(3);" onmouseover="{display_title(this, 'Changes the text color');}" onmouseout="{hide_title(this);}" /></td>
                                            <td class="settings_unit_right"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <hr class="settings_break"/>
                            <div id="highlight_users_posts">
                                <p id="high_users_posts_title" class="settings_text">User's Post Highlight Colors</p>
                                <div id="highlight_user_colors">
                                    <div id="highlight_color_body_red" class="highlight_color_body">
                                        <div id="highlight_color_picture_red_body" class="highlight_user_picture"><img id="highlight_color_picture_red" class="highlight_color_picture" src="<?php if($user_highlighted_colors[0]!='') echo "./users/images/".$user_highlighted_colors[0]."/0.jpg"; ?>"/></div>
                                        <div id="highlight_color_color_red" class="highlight_color_color"></div>
                                        <select id="highlight_user_colors_options_red" class="highlight_user_colors_options" onchange="highlight_color_change(0);">

                                        </select>
                                    </div>
                                    <div id="highlight_color_body_orange" class="highlight_color_body">
                                        <div id="highlight_color_picture_orange_body" class="highlight_user_picture"><img id="highlight_color_picture_orange" class="highlight_color_picture" src="<?php if($user_highlighted_colors[1]!='') echo "./users/images/".$user_highlighted_colors[1]."/0.jpg"; ?>"/></div>
                                        <div id="highlight_color_color_orange" class="highlight_color_color"></div>
                                        <select id="highlight_user_colors_options_orange" class="highlight_user_colors_options" onchange="highlight_color_change(1);">

                                        </select>
                                    </div>
                                    <div id="highlight_color_body_yellow" class="highlight_color_body">
                                        <div id="highlight_color_picture_yellow_body" class="highlight_user_picture"><img id="highlight_color_picture_yellow" class="highlight_color_picture" src="<?php if($user_highlighted_colors[2]!='') echo "./users/images/".$user_highlighted_colors[2]."/0.jpg"; ?>"/></div>
                                        <div id="highlight_color_color_yellow" class="highlight_color_color"></div>
                                        <select id="highlight_user_colors_options_yellow" class="highlight_user_colors_options" onchange="highlight_color_change(2);">

                                        </select>
                                    </div>
                                    <div id="highlight_color_body_green" class="highlight_color_body">
                                        <div id="highlight_color_picture_green_body" class="highlight_user_picture"><img id="highlight_color_picture_green" class="highlight_color_picture" src="<?php if($user_highlighted_colors[3]!='') echo "./users/images/".$user_highlighted_colors[3]."/0.jpg"; ?>"/></div>
                                        <div id="highlight_color_color_green" class="highlight_color_color"></div>
                                        <select id="highlight_user_colors_options_green" class="highlight_user_colors_options" onchange="highlight_color_change(3);">

                                        </select>
                                    </div>
                                    <div id="highlight_color_body_blue" class="highlight_color_body">
                                        <div id="highlight_color_picture_blue_body" class="highlight_user_picture"><img id="highlight_color_picture_blue" class="highlight_color_picture" src="<?php if($user_highlighted_colors[4]!='') echo "./users/images/".$user_highlighted_colors[4]."/0.jpg"; ?>"/></div>
                                        <div id="highlight_color_color_blue" class="highlight_color_color"></div>
                                        <select id="highlight_user_colors_options_blue" class="highlight_user_colors_options" onchange="highlight_color_change(4);">

                                        </select>
                                    </div>
                                    <div id="highlight_color_body_purple" class="highlight_color_body">
                                        <div id="highlight_color_picture_purple_body" class="highlight_user_picture"><img id="highlight_color_picture_purple" class="highlight_color_picture" src="<?php if($user_highlighted_colors[5]!='') echo "./users/images/".$user_highlighted_colors[5]."/0.jpg"; ?>"/></div>
                                        <div id="highlight_color_color_purple" class="highlight_color_color"></div>
                                        <select id="highlight_user_colors_options_purple" class="highlight_user_colors_options" onchange="highlight_color_change(5);">

                                        </select>
                                    </div>
                                    <div id="highlight_color_body_pink" class="highlight_color_body">
                                        <div id="highlight_color_picture_pink_body" class="highlight_user_picture"><img id="highlight_color_picture_pink" class="highlight_color_picture" src="<?php if($user_highlighted_colors[6]!='') echo "./users/images/".$user_highlighted_colors[6]."/0.jpg"; ?>"/></div>
                                        <div id="highlight_color_color_pink" class="highlight_color_color"></div>
                                        <select id="highlight_user_colors_options_pink" class="highlight_user_colors_options" onchange="highlight_color_change(6);">

                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="settings_other">
                            <table class="settings_table">
                                <tr id="terminate_account_settings">
                                    <td class="settings_unit_left"><p id="terminate_account_text"  class="settings_text">Terminate Account: </p></td>
                                    <td class="settings_unit_middle"></td>
                                    <td class="settings_unit_right"><input type="button" class="settings_button red_button" id="terminate_account_button" value="Terminate" onClick="terminate_account_confirmation();" onmouseover="{button_over(this); display_title(this, 'WARNING! Will PERMANENTLY disable account!');}" onmouseout="{button_out(this); hide_title(this);}" onmousedown="red_button_down(this);" onmouseup="red_button_up(this);" /></td>
                                </tr>
                                <tr><td class="settings_break_unit" colspan="3"><hr class="settings_break"/></td></tr>
                                <tr id="change_post_title_settings">
                                    <td class="settings_unit_left"><p id="change_post_title_text" class="settings_text">Posts title: </p></td>
                                    <td class="settings_unit_middle"><input type="text" id="change_post_title" class="input_box" onFocus="input_in(this);" onBlur="input_out(this);" value="<?php echo $post_title; ?>" maxlength="10"/></td>
                                    <td class="settings_unit_right"><input type="button" id="change_post_title_submit" onClick="change_post_title();" value="Change" class="settings_button red_button" onmouseover="{button_over(this); display_title(this, 'Change post title');}" onmouseout="{button_out(this); hide_title(this);}" onmousedown="red_button_down(this);" onmouseup="red_button_up(this);" /></td>
                                </tr>
                                <tr><td class="settings_break_unit" colspan="3"><hr class="settings_break"/></td></tr>
                                <tr id="change_information_title_settings">
                                    <td class="settings_unit_left"><p id="change_information_title_text" class="settings_text">Information title: </p></td>
                                    <td class="settings_unit_middle"><input type="text" id="change_information_title" class="input_box" onFocus="input_in(this);" onBlur="input_out(this);" value="<?php echo $information_title; ?>" maxlength="20"/></td>
                                    <td class="settings_unit_right"><input type="button" id="change_information_title_submit" onClick="change_information_title();" value="Change" class="settings_button red_button" onmouseover="{button_over(this); display_title(this, 'Change information title');}" onmouseout="{button_out(this); hide_title(this);}" onmousedown="red_button_down(this);" onmouseup="red_button_up(this);" /></td>
                                </tr>
                                <tr><td class="settings_break_unit" colspan="3"><hr class="settings_break"/></td></tr>
                                <tr id="change_first_name">
                                    <td class="settings_unit_left"><p id="change_first_name_text" class="settings_text">Name: </p></td>
                                    <td class="settings_unit_middle"><input type="text" id="change_first_name_input" class="input_box" onFocus="input_in(this);" onBlur="input_out(this);" value="<?php echo $name; ?>" maxlength="20" placeholder="First: "/></td>
                                    <td class="settings_unit_right"><input type="button" id="change_name_submit" value="Change" onClick="change_name();" class="settings_button red_button" /></td>
                                </tr>
                                <tr><td class="settings_break_unit" colspan="3"><hr class="settings_break"/></td></tr>
                                <tr id="blocked_users">
                                    <td class="settings_unit_left"><p id="blocked_users_text" class="settings_text" >Blocked Users:</p></td>
                                    <td class="settings_unit_middle"><div id="blocked_users_list"><p onClick="show_blocked_users();" style="cursor:pointer" onmouseover="{name_over(this); display_title(this, 'Shows all current blocked users');}" onmouseout="{name_out(this); hide_title(this);}" >Show list</p></div></td>
                                </tr>
                                <tr><td class="settings_break_unit" colspan="3"><hr class="settings_break"/></td></tr>
                                <tr id="audiences">
                                    <td class="settings_unit_left"><p class="settings_text" >Groups:</p></td>
                                    <td class="settings_unit_middle"><table id="group_list"><tbody id="group_list_body"></tbody></table></td>
                                    <td class="settings_unit_right"></td>
                                </tr>
                            </table>
                        </div>
                     </div>
                </div>
            <?php include('footer.php'); ?>
        </div>
    </body>

    <script type="text/javascript">
        function initialize()
        {
            $('#add_group_input').keydown(function(e)
            {
                var key = (e.keyCode ? e.keyCode : e.which);
                if(e.keyCode == '13')
                    add_group();
            });
        }
    </script>

</html>

