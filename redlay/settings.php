<?php
@include('init.php');
include('universal_functions.php');
$allowed="users";
include('security_checks.php');

?>
<!DOCTYPE html>
<html>
    <head>
        <title>Settings</title>
        <script type="text/javascript">
            startTime = (new Date).getTime();
        </script>
        <?php include('required_header.php'); ?>
        <script type="text/javascript">
            function change_color()
            {
                <?php
                        $colors=get_user_display_colors($_SESSION['id']);
                        $border_color=$colors[0];
                        $box_background_color=$colors[1];
                        $text_color=$colors[2];
                ?>
                $('.box').css('border', '5px solid <?php echo $border_color; ?>');
                $('.box').css('background-color', '<?php echo $box_background_color; ?>');
                
                $('#settings_left_table, #settings_middle_table').css('background-color', '<?php echo $box_background_color; ?>');

                $('body').css('color', '<?php echo $text_color;  ?>');
                $('#settings_title, .group_item').css('color', '<?php echo $border_color; ?>')
                $('.settings_input').css('outline-color', '<?php echo $border_color; ?>');
                $('.settings_text').css('color', '<?php echo $border_color; ?>');
                $('#change_background_picture_input').css('color', '<?php echo $border_color; ?>');
                $('.settings_menu').css('color', '<?php echo $border_color; ?>');
                
                $('.title_color').css('color', '<?php echo $border_color; ?>');
                $('.text_color').css('color', '<?php echo $text_color; ?>');
            }
            function select_colors()
            {
                var colors=$('#default_colors_select').val().split(',');
                
                $('#red').slider('value', colors[0]);
                $('#green').slider('value', colors[1]);
                $('#blue').slider('value', colors[2]);
            }
            function select_opacity()
            {
                var opacity=$('#default_opacity_select').val();
                
                $('#opacity').slider('value', opacity);
            }
            function dynamic_border_color()
            {
		        var red = $( "#red" ).slider( "value" );
                var green = $( "#green" ).slider( "value" );
                var blue = $( "#blue" ).slider( "value" );
                var opacity=$('#red').data('opacity');
                var string="rgba("+red+", "+green+", "+blue+", "+(opacity/100)+")";
                
		        $("#preview").css({"background-color": string});
                
                $('.box').css('border', '5px solid '+string);
                $('#settings_title, .group_item, .settings_text, #change_background_picture_input, .settings_menu').css('color', string);
            }
            function dynamic_background_color()
            {
                var red = $( "#red" ).slider( "value" );
                var green = $( "#green" ).slider( "value" );
                var blue = $( "#blue" ).slider( "value" );
                var opacity=$('#red').data('opacity');
                var string="rgba("+red+", "+green+", "+blue+", "+(opacity/100)+")";
                
		        $("#preview").css({"background-color": string});
                
                $('.box').css('background-color', string);
                $('#settings_left_table').css('background-color', string);
                $('#settings_middle_table').css('background-color', string);
            }
            function dynamic_text_color()
            {
		        var red = $( "#red" ).slider( "value" );
                var green = $( "#green" ).slider( "value" );
                var blue = $( "#blue" ).slider( "value" );
                var opacity=$('#red').data('opacity');
                var string="rgba("+red+", "+green+", "+blue+", "+(opacity/100)+")";
                
		        $("#preview").css({"background-color": string});
                
                $('body').css('color', string);
            }
            function dynamic_opacity()
            {
                var red = $('#opacity').data('red');
                var green = $('#opacity').data('green');
                var blue = $('#opacity').data('blue');
                
                var opacity=$('#opacity').slider("value");
                var string="rgba("+red+", "+green+", "+blue+", "+(opacity/100)+")";
                
		        $("#preview").css({"background-color": string});
                
                $('.box').css('background-color', string);
            }
            
            function change_border_color()
            {
                var red = $( "#red" ).slider( "value" );
                var green = $( "#green" ).slider( "value" );
                var blue = $( "#blue" ).slider( "value" );
                var string=red+", "+green+", "+blue;
                
                $.post('change_color.php',
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
                
                $.post('change_color.php',
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
                
                $.post('change_color.php',
                {
                    num: 3,
                    text_color: string
                }, function (output)
                {
                    close_alert_box();
                });
            }
            function change_opacity()
            {
                var opacity = $( "#opacity" ).slider( "value" );
                
                $.post('change_color.php',
                {
                    num: 4,
                    opacity: opacity
                }, function (output)
                {
                    close_alert_box();
                });
            }
            
            function create_sliders(num)
            {
                if(num==1)
                {
                    $.post('settings_query.php',
                    {
                        num:13,
                        type: 'border_color'
                    }, function(output)
                    {
                        var colors=output.colors;
                        var opacity=output.opacity;
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
                        
                        $('#red').data('opacity', opacity);
                    }, "json");
                }
                else if(num==2)
                {
                    $.post('settings_query.php',
                    {
                        num:13,
                        type: 'background_color'
                    }, function(output)
                    {
                        var colors=output.colors;
                        var opacity=output.opacity;
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
                        
                        $('#red').data('opacity', opacity);
                    }, "json");
                }    
                else if(num==3)
                {
                    $.post('settings_query.php',
                    {
                        num:13,
                        type: 'text_color'
                    }, function(output)
                    {
                        var colors=output.colors;
                        var opacity=output.opacity;
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
                        
                        $('#red').data('opacity', opacity);
                    }, "json");
                }
                else if(num==4)
                {
                    $.post('settings_query.php',
                    {
                        num:13,
                        type: 'opacity'
                    }, function(output)
                    {
                        var colors=output.colors;
                        var opacity=output.opacity;
                        $( "#opacity" ).slider({
                            orientation: "horizontal",
                            range: "min",
                            max: 100,
                            value: 100,
                            slide: dynamic_opacity,
                            change: dynamic_opacity
                        });
                        $( "#opacity" ).slider( "value", opacity );
                        
                        //sets colors as data for future use
                        $('#opacity').data('red', colors[0]);
                        $('#opacity').data('green', colors[1]);
                        $('#opacity').data('blue', colors[2]);
                    }, "json");
                }
            }
            $(document).ready(function()
            {
                <?php $path=get_user_background_pic($_SESSION['id']); if(file_exists_server($path)&&$colors[5]=="yes") echo "$('body').css('background-attachment', 'fixed');"; ?>

                //changes the profile picture upload button to normal if file is selected
                $('#profile_picture_settings').change(function()
                {
                    $('#submit_profile_picture').removeAttr('disabled').removeClass('settings_button_disabled').addClass('button red_button settings_button');
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
                        if(output=='Change successful!')
                            display_error(output, 'good_errors');
                        else
                            display_error(output, 'bad_errors');
                    });
                });
            });

            function change_friend_title()
            {
                var input=$('#change_friend_title').val();
                $.post('change_friend_title.php',
                {
                    text: input
                }, function (output)
                {
                    if(output=='Change successful')
                       display_error(output, 'good_errors');
                    else
                       display_error(output, 'bad_errors');
                });
            }

            function change_post_title()
            {
                var input=$('#change_post_title').val();
                $.post('change_post_title.php',
                {
                    text: input
                }, function (output)
                {
                    if(output=='Change successful')
                       display_error(output, 'good_errors');
                    else
                       display_error(output, 'bad_errors');
                });
            }

            function change_information_title()
            {
                var input=$('#change_information_title').val();
                $.post('change_information_title.php',
                {
                    text: input
                }, function (output)
                {
                    if(output=='Change successful')
                       display_error(output, 'good_errors');
                    else
                       display_error(output, 'bad_errors');
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
                    if(output=='Change successful')
                       display_error(output, 'good_errors');
                    else
                       display_error(output, 'bad_errors');
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
            
            function fill_option_boxes()
            {
                var friends=new Array();
                var colors=new Array();
                var friend_names=new Array();
                <?php
                    $friends=get_friends($_SESSION['id']);
                    $colors=get_user_colors($_SESSION['id']);
                    for($x = 0; $x < sizeof($friends); $x++)
                    {
                        echo "friends[$x]='".$friends[$x]."';";
                        echo "friend_names[$x]='".get_user_name($friends[$x])."';";
                    }
                    for($x = 0; $x < sizeof($colors); $x++)
                        echo "colors[$x]='".$colors[$x]."';";
                ?>
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
                $('#highlight_color_picture_'+string).attr('src', "users/"+value+"/0.jpg");
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
                var timezone=get_timezone();
                $.post('settings_query.php',
                {
                    num:15,
                    timezone:timezone
                }, function(output)
                {
                    var blocked_users=output.blocked_users;
                    var profile_pictures=output.profile_pictures;
                    var blocked_user_timestamps=output.blocked_user_timestamps;
                    var names=output.names;
                    
                    if(blocked_users[0]!=undefined)
                    {
                        $('#blocked_users_table_body').html("");
                        for(var x = 0; x < blocked_users.length; x++)
                        {
                            var profile_picture="<a href='http://www.redlay.com/profile.php?user_id="+blocked_users[x]+"'><img class='blocked_profile_picture' src='"+profile_pictures[x]+"'  /></a>";
                            var name="<div class='user_name_body'><a class='link' href='http://www.redlay.com/profile.php?user_id="+blocked_users[x]+"'><span class='title_color' onmouseover=name_over(this); onmouseout=name_out(this);>"+names[x]+"</span></a></div>";
                            var timestamp="<p class='text_color' style='margin:0px;font-size:14px;'>Blocked "+blocked_user_timestamps[x]+"</p><p><input class='button red_button' type='button' value='Unblock' onClick='unblock("+blocked_users[x]+")'/></p>";

                            var body=get_post_format(profile_picture, name+timestamp, '', '', '', '', '', '');
                            $('#blocked_users_table_body').html($('#blocked_users_table_body').html()+"<tr><td>"+body+"</td></tr>");
                        }
                    }
                    else
                        $('#blocked_users_middle_unit').html("<p class='text_color' style='margin:0px; '>You have not blocked anybody</p>");
                }, "json");
            }
            function unblock(ID)
            {
                $.post('unblock_user.php',
                {
                    user_id: ID
                }, function (output)
                {
                    if(output=='User unblocked')
                    {
                        display_blocked_users();
                        display_error(output, 'good_errors');
                    }
                    else
                        display_error(output, 'bad_errors');
                });
            }
            function change_password()
            {
               $.post('settings_query.php', 
               {
                  num:1,
                  current_password: $('#current_password').val(),
                  new_password: $('#new_password').val(),
                  confirm_new_password: $('#confirm_new_password').val()
               }, function(output)
               {
                  if(output=='Password change successful!')
                      display_error(output, 'good_errors');
                  else
                      display_error(output, 'bad_errors');
               });
            }
            function unblock_user(ID)
            {
                $.post('unblock_user_user.php',
                {
                    user_id: ID
                }, function (output)
                {
                    if(output=='User unblocked')
                        display_error(output, 'good_errors');
                    else
                        display_error(output, 'bad_errors');
                });

            }
            function change_box_border_opacity()
            {
                $.post('change_box_border_opacity.php',
                {
                    opacity: $('#change_box_border_opacity').val()
                }, function (output)
                {
                    
                });
            }
            function change_box_background_opacity()
            {
                $.post('change_box_background_opacity.php',
                {
                    opacity: $('#change_box_background_opacity').val()
                }, function (output)
                {

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
            function display_settings_email()
            {
                $('#settings_display').hide();
                $('#settings_other').hide();
                $('#settings_images').hide();
                $('#settings_email').show();
                $('#settings_privacy').hide();
                $('#settings_information').hide();
            }
            function display_settings_privacy()
            {
                $('#settings_display').hide();
                $('#settings_other').hide();
                $('#settings_images').hide();
                $('#settings_email').hide();
                $('#settings_information').hide();
                $('#settings_privacy').show();
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
            function change_email_settings()
            {
                var array=new Array();
                if($('#email_settings_0').length&&$('#email_settings_0').data('checked')=='yes') array[0]="1"; else array[0]="0";
                if($('#email_settings_1').length&&$('#email_settings_1').data('checked')=='yes') array[1]="1"; else array[1]="0";
                if($('#email_settings_2').length&&$('#email_settings_2').data('checked')=='yes') array[2]="1"; else array[2]="0";
                if($('#email_settings_3').length&&$('#email_settings_3').data('checked')=='yes') array[3]="1"; else array[3]="0";
                if($('#email_settings_4').length&&$('#email_settings_4').data('checked')=='yes') array[4]="1"; else array[4]="0";
                if($('#email_settings_5').length&&$('#email_settings_5').data('checked')=='yes') array[5]="1"; else array[5]="0";
                if($('#email_settings_6').length&&$('#email_settings_6').data('checked')=='yes') array[6]="1"; else array[6]="0";
                if($('#email_settings_7').length&&$('#email_settings_7').data('checked')=='yes') array[7]="1"; else array[7]="0";
                if($('#email_settings_8').length&&$('#email_settings_8').data('checked')=='yes') array[8]="1"; else array[8]="0";
                if($('#email_settings_9').length&&$('#email_settings_9').data('checked')=='yes') array[9]="1"; else array[9]="0";
                if($('#email_settings_10').length&&$('#email_settings_10').data('checked')=='yes') array[10]="1"; else array[10]="0";
                if($('#email_settings_11').length&&$('#email_settings_11').data('checked')=='yes') array[11]="1"; else array[11]="0";
                $.post('change_email_settings.php',
                {
                    checkboxes:array
                }, function(output)
                {
                    if(output=="Change successful!")
                        display_error(output, 'good_errors');
                    else
                        display_error(output, 'bad_errors');
                });
            }
            function change_privacy_settings()
            {
                var general_privacy=new Array();
                var non_adds_privacy=new Array();
                var search_privacy=new Array();
                general_privacy[0]=$('#privacy_settings_1').data('checked');
                general_privacy[1]=$('#privacy_settings_2').data('checked');
                general_privacy[2]=$('#privacy_settings_16').data('checked');
                general_privacy[3]=$('#privacy_settings_17').data('checked');
                non_adds_privacy[0]=$('#privacy_settings_3').data('checked');
                non_adds_privacy[1]=$('#privacy_settings_4').data('checked');
                non_adds_privacy[2]=$('#privacy_settings_5').data('checked');
                non_adds_privacy[3]=$('#privacy_settings_6').data('checked');
                non_adds_privacy[4]=$('#privacy_settings_7').data('checked');
                non_adds_privacy[5]=$('#privacy_settings_8').data('checked');
                non_adds_privacy[6]=$('#privacy_settings_9').data('checked');
                non_adds_privacy[7]=$('#privacy_settings_15').data('checked');
                search_privacy[0]=$('#privacy_settings_10').data('checked');
                search_privacy[1]=$('#privacy_settings_11').data('checked');
                search_privacy[2]=$('#privacy_settings_12').data('checked');
                search_privacy[3]=$('#privacy_settings_13').data('checked');
                search_privacy[4]=$('#privacy_settings_14').data('checked');

                $.post('change_privacy_settings.php',
                {
                    general_privacy: general_privacy,
                    non_adds_privacy: non_adds_privacy,
                    search_privacy:search_privacy
                }, function(output)
                {
                    if(output=="Change successful!")
                        display_error(output, 'good_errors');
                    else
                        display_error(output, 'bad_errors');
                });
            }
            function terminate_account_confirmation()
            {
               var title="Terminate account";
               var extra_id="";
               var load_id="terminate_account_load";
               var confirm="<input class='button red_button' type='button' value='Delete' onClick='terminate_account();'/>";
               var body="<p class='text_color' style='width:500px;'>All posts, photos, videos, and any information you ever uploaded or shared will be deleted PERMANENTLY. Please stop any recurring payments with paypal. Are you sure you want to delete your account? This can not be undone.</p>";
               display_alert(title, body, extra_id, load_id, confirm);
               $('#terminate_account_load').hide();
               change_color();
            }
            function terminate_account()
            {
                $.post('terminate_account.php',
                {
                    confirmation: 'yes',
                    user_id: <?php echo $_SESSION['id']; ?>
                }, function(output)
                {
                    if(output=='Account terminated')
                        window.location.replace('http://www.redlay.com/');
                    else
                        display_error(output, 'bad_errors');
                });
            }
            
            //displays all people in a specific group
            function show_group(group)
            {
                $('.alert_box').css('opacity', 1).show().draggable();
                $('.alert_box_inside').html("<table id='group_table'><tr class='alert_box_row' id='row_1'></tr><tr class='alert_box_row' id='row_2'></tr></table>");

                $.post('settings_query.php',
                {
                    num:11,
                    group:group
                }, function(output)
                {
                    var users=output.users;
                    var images=output.images;
                    var names=output.names;

                    $('#row_1').html("<td class='alert_box_title_container'><span class='alert_box_title'>"+group+"</span></td>");
                    $('#row_2').html("<table id='group_table'></table>");

                    if(users.length!=0)
                    {
                        for(var x = 0; x < users.length/5; x++)
                        {
                            $('#group_table').html($('#group_table').html()+"<tr id='group_row_"+x+"' class='group_row'></tr>");
                        }

                        var num=0;
                        for(var x = 0; x < users.length; x++)
                        {
                            $('#group_row_'+num).html($('#group_row_'+num).html()+"<td class='group_list_item'><a href='http://www.redlay.com/profile.php?user_id="+users[x]+"'><img class='group_list_profile_picture' id='group_list_image_"+x+"' src='"+images[x]+"'/></a></td>");

                            $('#group_list_image_'+x).attr({'onmouseover': "{display_title(this, '"+names[x]+"');}", 'onmouseout': "{hide_title(this);}"});

                            
                            if(x%5==0&&x!=0)
                                num++;
                        }
                    }
                    else
                        $('.alert_box_inside').html("You do not have any people in this group");

                }, "json");
            }


            function delete_group(group)
            {
                $.post('settings_query.php',
                {
                    num:10,
                    group: group
                }, function(output)
                {
                     if(output=='Group deleted')
                     {
                         display_error(output, 'good_errors');
                         fill_group_list();
                     }
                      else
                         display_error(output, 'bad_errors');
                });
            }

            

            <?php
                $query=mysql_query("SELECT * FROM user_data WHERE user_id=$_SESSION[id] LIMIT 1");
                $query2=mysql_query("SELECT general, display_non_friends, search_options FROM user_privacy WHERE user_id=$_SESSION[id] LIMIT 1");
                if($query&&mysql_num_rows($query)==1&&$query2&&mysql_num_rows($query2)==1)
                {
                    $array=mysql_fetch_array($query);
                    $array2=mysql_fetch_row($query2);

                    $birthday=explode('|^|*|', $array['user_birthday']);
                    $name=explode(' ', get_user_name($_SESSION['id']));
                    $month=$birthday[0];
                    $day=$birthday[1];
                    $year=$birthday[2];
                    $sex=$array['user_sex'];
                    $relationship=$array['user_relationship'];
                    $mood=$array['user_mood'];
                    $high_school=$array['high_school'];
                    $college=$array['college'];
                    $bio=$array['user_bio'];
                    $emails=explode('|^|*|', $array['email_settings']);
                    $email_settings=get_email_settings($_SESSION['id']);
                    $query=mysql_query("SELECT * FROM user_display WHERE user_id=$_SESSION[id] LIMIT 1");
                    if($query&&mysql_num_rows($query)==1)
                    {
                        $array=mysql_fetch_array($query);
                        $friends_title=$array['friend_title'];
                        $post_title=$array['post_title'];
                        $information_title=$array['information_title'];
                        $user_highlighted_colors=explode('|^|*|', $array['user_colors']);
                        $box_border_opacity=$array['box_border_opacity'];
                        $box_background_opacity=$array['box_background_opacity'];
                        $display_colors=explode('|^|*|', $array['display_colors']);
                        $background_fixed=$array['background_fixed'];
                        $birthday_year=$array['birthday_year'];
                    }
                    $first_name=$name[0];
                    $last_name=$name[1];

                    $general_privacy=explode('|^|*|', $array2[0]);
                    $display_non_friends_privacy=explode('|^|*|', $array2[1]);
                    $search_options_privacy=explode('|^|*|', $array2[2]);
                }
            ?>

            function display_color_wheel(num)
            {
                $('.alert_box').css('opacity', 1).show().draggable();
                if(num>=1&&num<=3)
                {
                    $('.alert_box_inside').html("<table><tbody><tr><td>        <table><tbody><tr><td><div id='red'></div></td></tr><tr><td><div id='green'></div></td></tr><tr><td><div id='blue'></div></td></tr><tr><td></td></tr></tbody></table></td>     <td><table><tbody><tr><td><p class='settings_text'>Default colors: </p></td></tr><tr><td><select id='default_colors_select' onChange='select_colors();'></select></td></tr></tbody></table></td>     </tr><tr><td><div id='preview'></div></td><td><input class='button red_button' id='save_color_button' type='button' value='Change' onClick=''/><input class='button gray_button' value='Cancel' onClick='{change_color();close_alert_box();}' type='button'/></td></tr></tbody></table>");
                    
                    
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
                }
                else
                {
                    $('.alert_box_inside').html("<table><tbody><tr><td>        <table><tbody><tr><td><div id='opacity'></div></td></tr><tr><td></td></tr></tbody></table></td>     <td><table><tbody><tr><td><p class='settings_text'>Default opacities: </p></td></tr><tr><td><select id='default_opacity_select' onChange='select_opacity();'></select></td></tr></tbody></table></td>     </tr><tr><td><div id='preview'></div></td><td><input class='button red_button' id='save_opacity_button' type='button' value='Change' onClick=''/><input class='button gray_button' value='Cancel' onClick='{change_color();close_alert_box();}' type='button'/></td></tr></tbody></table>");
                    
                    $('#default_opacity_select').html($('#default_opacity_select').html()+"<option value='-1'>Opacity: </option>");
                    $('#default_opacity_select').html($('#default_opacity_select').html()+"<option value='100'>100 </option>");
                    $('#default_opacity_select').html($('#default_opacity_select').html()+"<option value='90'>90 </option>");
                    $('#default_opacity_select').html($('#default_opacity_select').html()+"<option value='80'>80 </option>");
                    $('#default_opacity_select').html($('#default_opacity_select').html()+"<option value='70'>70 </option>");
                    $('#default_opacity_select').html($('#default_opacity_select').html()+"<option value='60'>60 </option>");
                    $('#default_opacity_select').html($('#default_opacity_select').html()+"<option value='50'>50 </option>");
                    $('#default_opacity_select').html($('#default_opacity_select').html()+"<option value='40'>40 </option>");
                    $('#default_opacity_select').html($('#default_opacity_select').html()+"<option value='30'>30 </option>");
                    $('#default_opacity_select').html($('#default_opacity_select').html()+"<option value='20'>20 </option>");
                    $('#default_opacity_select').html($('#default_opacity_select').html()+"<option value='10'>10 </option>");
                    $('#default_opacity_select').html($('#default_opacity_select').html()+"<option value='0'>0 </option>");
                }
                
                
                
                if(num==1)
                    $('#save_color_button').attr('onClick', '{change_border_color();}');
                else if(num==2)
                    $('#save_color_button').attr('onClick', '{change_background_color();}');
                else if(num==3)
                    $('#save_color_button').attr('onClick', '{change_text_color();}');
                else if(num==4)
                    $('#save_opacity_button').attr('onClick', '{change_opacity();}');
                    
                create_sliders(num);
            }

            function toggle_email_checkboxes(num)
            {
                if($('#email_settings_'+num).data('checked')=='yes')
                {
                    $('#email_settings_'+num).data('checked', 'no');
                    $('#email_settings_checkbox_'+num).attr({'src': 'http://pics.redlay.com/pictures/gray_checkbox.png'});
                }
                else
                {
                    $('#email_settings_'+num).data('checked', 'yes');
                    $('#email_settings_checkbox_'+num).attr({'src': 'http://pics.redlay.com/pictures/gray_checkbox_checked.png'});
                }
            }

            function toggle_privacy_checkboxes(num)
            {
                if($('#privacy_settings_'+num).data('checked')=='yes')
                {
                    $('#privacy_settings_'+num).data('checked', 'no');
                    $('#privacy_settings_checkbox_'+num).attr({'src': 'http://pics.redlay.com/pictures/gray_checkbox.png'});
                }
                else
                {
                    $('#privacy_settings_'+num).data('checked', 'yes');
                    $('#privacy_settings_checkbox_'+num).attr({'src': 'http://pics.redlay.com/pictures/gray_checkbox_checked.png'});
                }
            }

            function display_email_checkboxes()
            {
                var checked="<img class='checkbox' src='http://pics.redlay.com/pictures/gray_checkbox_checked.png' />";
                var unchecked="<img class='checkbox' src='http://pics.redlay.com/pictures/gray_checkbox.png' />";
                if(<?php echo $emails[0]; ?>==1)
                    $('#email_settings_0').html("<img class='checkbox' src='http://pics.redlay.com/pictures/gray_checkbox_checked.png' id='email_settings_checkbox_0'/>").data('checked', 'yes').attr('onClick', 'toggle_email_checkboxes(0)');
                else
                    $('#email_settings_0').html("<img class='checkbox' src='http://pics.redlay.com/pictures/gray_checkbox.png' id='email_settings_checkbox_0'/>").data('checked', 'no').attr('onClick', 'toggle_email_checkboxes(0)');

                if(<?php echo $emails[1]; ?>==1)
                    $('#email_settings_1').html("<img class='checkbox' src='http://pics.redlay.com/pictures/gray_checkbox_checked.png' id='email_settings_checkbox_1'/>").data('checked', 'yes').attr('onClick', 'toggle_email_checkboxes(1)');
                else
                    $('#email_settings_1').html("<img class='checkbox' src='http://pics.redlay.com/pictures/gray_checkbox.png' id='email_settings_checkbox_1'/>").data('checked', 'no').attr('onClick', 'toggle_email_checkboxes(1)');

                if(<?php echo $emails[2]; ?>==1)
                    $('#email_settings_2').html("<img class='checkbox' src='http://pics.redlay.com/pictures/gray_checkbox_checked.png' id='email_settings_checkbox_2'/>").data('checked', 'yes').attr('onClick', 'toggle_email_checkboxes(2)');
                else
                    $('#email_settings_2').html("<img class='checkbox' src='http://pics.redlay.com/pictures/gray_checkbox.png' id='email_settings_checkbox_2'/>").data('checked', 'no').attr('onClick', 'toggle_email_checkboxes(2)');

                if(<?php echo $emails[3]; ?>==1)
                    $('#email_settings_3').html("<img class='checkbox' src='http://pics.redlay.com/pictures/gray_checkbox_checked.png' id='email_settings_checkbox_3'/>").data('checked', 'yes').attr('onClick', 'toggle_email_checkboxes(3)');
                else
                    $('#email_settings_3').html("<img class='checkbox' src='http://pics.redlay.com/pictures/gray_checkbox.png' id='email_settings_checkbox_3'/>").data('checked', 'no').attr('onClick', 'toggle_email_checkboxes(3)');

                if(<?php echo $emails[4]; ?>==1)
                    $('#email_settings_4').html("<img class='checkbox' src='http://pics.redlay.com/pictures/gray_checkbox_checked.png' id='email_settings_checkbox_4'/>").data('checked', 'yes').attr('onClick', 'toggle_email_checkboxes(4)');
                else
                    $('#email_settings_4').html("<img class='checkbox' src='http://pics.redlay.com/pictures/gray_checkbox.png' id='email_settings_checkbox_4'/>").data('checked', 'no').attr('onClick', 'toggle_email_checkboxes(4)');

                if(<?php echo $emails[5]; ?>==1)
                    $('#email_settings_5').html("<img class='checkbox' src='http://pics.redlay.com/pictures/gray_checkbox_checked.png' id='email_settings_checkbox_5'/>").data('checked', 'yes').attr('onClick', 'toggle_email_checkboxes(5)');
                else
                    $('#email_settings_5').html("<img class='checkbox' src='http://pics.redlay.com/pictures/gray_checkbox.png' id='email_settings_checkbox_5'/>").data('checked', 'no').attr('onClick', 'toggle_email_checkboxes(5)');

                if(<?php echo $emails[6]; ?>==1)
                    $('#email_settings_6').html("<img class='checkbox' src='http://pics.redlay.com/pictures/gray_checkbox_checked.png' id='email_settings_checkbox_6'/>").data('checked', 'yes').attr('onClick', 'toggle_email_checkboxes(6)');
                else
                    $('#email_settings_6').html("<img class='checkbox' src='http://pics.redlay.com/pictures/gray_checkbox.png' id='email_settings_checkbox_6'/>").data('checked', 'no').attr('onClick', 'toggle_email_checkboxes(6)');

                if(<?php echo $emails[7]; ?>==1)
                    $('#email_settings_7').html("<img class='checkbox' src='http://pics.redlay.com/pictures/gray_checkbox_checked.png' id='email_settings_checkbox_7'/>").data('checked', 'yes').attr('onClick', 'toggle_email_checkboxes(7)');
                else
                    $('#email_settings_7').html("<img class='checkbox' src='http://pics.redlay.com/pictures/gray_checkbox.png' id='email_settings_checkbox_7'/>").data('checked', 'no').attr('onClick', 'toggle_email_checkboxes(7)');

                if(<?php echo $emails[8]; ?>==1)
                    $('#email_settings_8').html("<img class='checkbox' src='http://pics.redlay.com/pictures/gray_checkbox_checked.png' id='email_settings_checkbox_8'/>").data('checked', 'yes').attr('onClick', 'toggle_email_checkboxes(8)');
                else
                    $('#email_settings_8').html("<img class='checkbox' src='http://pics.redlay.com/pictures/gray_checkbox.png' id='email_settings_checkbox_8'/>").data('checked', 'no').attr('onClick', 'toggle_email_checkboxes(8)');
                
                if(<?php echo $emails[9]; ?>==1)
                    $('#email_settings_9').html("<img class='checkbox' src='http://pics.redlay.com/pictures/gray_checkbox_checked.png' id='email_settings_checkbox_9'/>").data('checked', 'yes').attr('onClick', 'toggle_email_checkboxes(9)');
                else
                    $('#email_settings_9').html("<img class='checkbox' src='http://pics.redlay.com/pictures/gray_checkbox.png' id='email_settings_checkbox_9'/>").data('checked', 'no').attr('onClick', 'toggle_email_checkboxes(9)');
                
                if(<?php echo $emails[10]; ?>==1)
                    $('#email_settings_10').html("<img class='checkbox' src='http://pics.redlay.com/pictures/gray_checkbox_checked.png' id='email_settings_checkbox_10'/>").data('checked', 'yes').attr('onClick', 'toggle_email_checkboxes(10)');
                else
                    $('#email_settings_10').html("<img class='checkbox' src='http://pics.redlay.com/pictures/gray_checkbox.png' id='email_settings_checkbox_10'/>").data('checked', 'no').attr('onClick', 'toggle_email_checkboxes(10)');
                
                if(<?php echo $emails[11]; ?>==1)
                    $('#email_settings_11').html("<img class='checkbox' src='http://pics.redlay.com/pictures/gray_checkbox_checked.png' id='email_settings_checkbox_11'/>").data('checked', 'yes').attr('onClick', 'toggle_email_checkboxes(11)');
                else
                    $('#email_settings_11').html("<img class='checkbox' src='http://pics.redlay.com/pictures/gray_checkbox.png' id='email_settings_checkbox_11'/>").data('checked', 'no').attr('onClick', 'toggle_email_checkboxes(11)');
            }
            function display_privacy_checkboxes()
            {
                if('<?php echo $general_privacy[0]; ?>'=='yes')
                    $('#privacy_settings_1').html("<img class='checkbox' src='http://pics.redlay.com/pictures/gray_checkbox_checked.png' id='privacy_settings_checkbox_1'/>").data('checked', 'yes').attr('onClick', 'toggle_privacy_checkboxes(1)');
                else
                    $('#privacy_settings_1').html("<img class='checkbox' src='http://pics.redlay.com/pictures/gray_checkbox.png' id='privacy_settings_checkbox_1'/>").data('checked', 'no').attr('onClick', 'toggle_privacy_checkboxes(1)');

                if('<?php echo $general_privacy[1]; ?>'=='yes')
                    $('#privacy_settings_2').html("<img class='checkbox' src='http://pics.redlay.com/pictures/gray_checkbox_checked.png' id='privacy_settings_checkbox_2'/>").data('checked', 'yes').attr('onClick', 'toggle_privacy_checkboxes(2)');
                else
                    $('#privacy_settings_2').html("<img class='checkbox' src='http://pics.redlay.com/pictures/gray_checkbox.png' id='privacy_settings_checkbox_2'/>").data('checked', 'no').attr('onClick', 'toggle_privacy_checkboxes(2)');

                if('<?php echo $general_privacy[2]; ?>'=='yes')
                    $('#privacy_settings_16').html("<img class='checkbox' src='http://pics.redlay.com/pictures/gray_checkbox_checked.png' id='privacy_settings_checkbox_16'/>").data('checked', 'yes').attr('onClick', 'toggle_privacy_checkboxes(16)');
                else
                    $('#privacy_settings_16').html("<img class='checkbox' src='http://pics.redlay.com/pictures/gray_checkbox.png' id='privacy_settings_checkbox_16'/>").data('checked', 'no').attr('onClick', 'toggle_privacy_checkboxes(16)');
                
                if('<?php echo $general_privacy[3]; ?>'=='yes')
                    $('#privacy_settings_17').html("<img class='checkbox' src='http://pics.redlay.com/pictures/gray_checkbox_checked.png' id='privacy_settings_checkbox_17'/>").data('checked', 'yes').attr('onClick', 'toggle_privacy_checkboxes(17);');
                else
                    $('#privacy_settings_17').html("<img class='checkbox' src='http://pics.redlay.com/pictures/gray_checkbox.png' id='privacy_settings_checkbox_17'/>").data('checked', 'no').attr('onClick', 'toggle_privacy_checkboxes(17);');

                
                


                if('<?php echo $display_non_friends_privacy[0]; ?>'=='yes')
                    $('#privacy_settings_3').html("<img class='checkbox' src='http://pics.redlay.com/pictures/gray_checkbox_checked.png' id='privacy_settings_checkbox_3'/>").data('checked', 'yes').attr('onClick', 'toggle_privacy_checkboxes(3)');
                else
                    $('#privacy_settings_3').html("<img class='checkbox' src='http://pics.redlay.com/pictures/gray_checkbox.png' id='privacy_settings_checkbox_3'/>").data('checked', 'no').attr('onClick', 'toggle_privacy_checkboxes(3)');

                if('<?php echo $display_non_friends_privacy[1]; ?>'=='yes')
                    $('#privacy_settings_4').html("<img class='checkbox' src='http://pics.redlay.com/pictures/gray_checkbox_checked.png' id='privacy_settings_checkbox_4'/>").data('checked', 'yes').attr('onClick', 'toggle_privacy_checkboxes(4)');
                else
                    $('#privacy_settings_4').html("<img class='checkbox' src='http://pics.redlay.com/pictures/gray_checkbox.png' id='privacy_settings_checkbox_4'/>").data('checked', 'no').attr('onClick', 'toggle_privacy_checkboxes(4)');

                if('<?php echo $display_non_friends_privacy[2]; ?>'=='yes')
                    $('#privacy_settings_5').html("<img class='checkbox' src='http://pics.redlay.com/pictures/gray_checkbox_checked.png' id='privacy_settings_checkbox_5'/>").data('checked', 'yes').attr('onClick', 'toggle_privacy_checkboxes(5)');
                else
                    $('#privacy_settings_5').html("<img class='checkbox' src='http://pics.redlay.com/pictures/gray_checkbox.png' id='privacy_settings_checkbox_5'/>").data('checked', 'no').attr('onClick', 'toggle_privacy_checkboxes(5)');

                if('<?php echo $display_non_friends_privacy[3]; ?>'=='yes')
                    $('#privacy_settings_6').html("<img class='checkbox' src='http://pics.redlay.com/pictures/gray_checkbox_checked.png' id='privacy_settings_checkbox_6'/>").data('checked', 'yes').attr('onClick', 'toggle_privacy_checkboxes(6)');
                else
                    $('#privacy_settings_6').html("<img class='checkbox' src='http://pics.redlay.com/pictures/gray_checkbox.png' id='privacy_settings_checkbox_6'/>").data('checked', 'no').attr('onClick', 'toggle_privacy_checkboxes(6)');

                if('<?php echo $display_non_friends_privacy[4]; ?>'=='yes')
                    $('#privacy_settings_7').html("<img class='checkbox' src='http://pics.redlay.com/pictures/gray_checkbox_checked.png' id='privacy_settings_checkbox_7'/>").data('checked', 'yes').attr('onClick', 'toggle_privacy_checkboxes(7)');
                else
                    $('#privacy_settings_7').html("<img class='checkbox' src='http://pics.redlay.com/pictures/gray_checkbox.png' id='privacy_settings_checkbox_7'/>").data('checked', 'no').attr('onClick', 'toggle_privacy_checkboxes(7)');

                if('<?php echo $display_non_friends_privacy[5]; ?>'=='yes')
                    $('#privacy_settings_8').html("<img class='checkbox' src='http://pics.redlay.com/pictures/gray_checkbox_checked.png' id='privacy_settings_checkbox_8'/>").data('checked', 'yes').attr('onClick', 'toggle_privacy_checkboxes(8)');
                else
                    $('#privacy_settings_8').html("<img class='checkbox' src='http://pics.redlay.com/pictures/gray_checkbox.png' id='privacy_settings_checkbox_8'/>").data('checked', 'no').attr('onClick', 'toggle_privacy_checkboxes(8)');

                if('<?php echo $display_non_friends_privacy[6]; ?>'=='yes')
                    $('#privacy_settings_9').html("<img class='checkbox' src='http://pics.redlay.com/pictures/gray_checkbox_checked.png' id='privacy_settings_checkbox_9'/>").data('checked', 'yes').attr('onClick', 'toggle_privacy_checkboxes(9)');
                else
                    $('#privacy_settings_9').html("<img class='checkbox' src='http://pics.redlay.com/pictures/gray_checkbox.png' id='privacy_settings_checkbox_9'/>").data('checked', 'no').attr('onClick', 'toggle_privacy_checkboxes(9)');

                if('<?php echo $display_non_friends_privacy[7]; ?>'=='yes')
                    $('#privacy_settings_15').html("<img class='checkbox' src='http://pics.redlay.com/pictures/gray_checkbox_checked.png' id='privacy_settings_checkbox_15'/>").data('checked', 'yes').attr('onClick', 'toggle_privacy_checkboxes(15);');
                else
                    $('#privacy_settings_15').html("<img class='checkbox' src='http://pics.redlay.com/pictures/gray_checkbox.png' id='privacy_settings_checkbox_15'/>").data('checked', 'no').attr('onClick', 'toggle_privacy_checkboxes(15);');
                
                
                

                if('<?php echo $search_options_privacy[0]; ?>'=='yes')
                    $('#privacy_settings_10').html("<img class='checkbox' src='http://pics.redlay.com/pictures/gray_checkbox_checked.png' id='privacy_settings_checkbox_10'/>").data('checked', 'yes').attr('onClick', 'toggle_privacy_checkboxes(10)');
                else
                    $('#privacy_settings_10').html("<img class='checkbox' src='http://pics.redlay.com/pictures/gray_checkbox.png' id='privacy_settings_checkbox_10'/>").data('checked', 'no').attr('onClick', 'toggle_privacy_checkboxes(10)');

                if('<?php echo $search_options_privacy[1]; ?>'=='yes')
                    $('#privacy_settings_11').html("<img class='checkbox' src='http://pics.redlay.com/pictures/gray_checkbox_checked.png' id='privacy_settings_checkbox_11'/>").data('checked', 'yes').attr('onClick', 'toggle_privacy_checkboxes(11)');
                else
                    $('#privacy_settings_11').html("<img class='checkbox' src='http://pics.redlay.com/pictures/gray_checkbox.png' id='privacy_settings_checkbox_11'/>").data('checked', 'no').attr('onClick', 'toggle_privacy_checkboxes(11)');

                if('<?php echo $search_options_privacy[2]; ?>'=='yes')
                    $('#privacy_settings_12').html("<img class='checkbox' src='http://pics.redlay.com/pictures/gray_checkbox_checked.png' id='privacy_settings_checkbox_12'/>").data('checked', 'yes').attr('onClick', 'toggle_privacy_checkboxes(12)');
                else
                    $('#privacy_settings_12').html("<img class='checkbox' src='http://pics.redlay.com/pictures/gray_checkbox.png' id='privacy_settings_checkbox_12'/>").data('checked', 'no').attr('onClick', 'toggle_privacy_checkboxes(12)');

                if('<?php echo $search_options_privacy[3]; ?>'=='yes')
                    $('#privacy_settings_13').html("<img class='checkbox' src='http://pics.redlay.com/pictures/gray_checkbox_checked.png' id='privacy_settings_checkbox_13'/>").data('checked', 'yes').attr('onClick', 'toggle_privacy_checkboxes(13)');
                else
                    $('#privacy_settings_13').html("<img class='checkbox' src='http://pics.redlay.com/pictures/gray_checkbox.png' id='privacy_settings_checkbox_13'/>").data('checked', 'no').attr('onClick', 'toggle_privacy_checkboxes(13)');

                if('<?php echo $search_options_privacy[4]; ?>'=='yes')
                    $('#privacy_settings_14').html("<img class='checkbox' src='http://pics.redlay.com/pictures/gray_checkbox_checked.png' id='privacy_settings_checkbox_14'/>").data('checked', 'yes').attr('onClick', 'toggle_privacy_checkboxes(14)');
                else
                    $('#privacy_settings_14').html("<img class='checkbox' src='http://pics.redlay.com/pictures/gray_checkbox.png' id='privacy_settings_checkbox_14'/>").data('checked', 'no').attr('onClick', 'toggle_privacy_checkboxes(14)');

            }
            
            function add_group()
            {
                var new_group=$('#new_group').val();
                $.post('user_groups_query.php',
                {
                    num:5,
                    new_group: new_group
                }, function(output)
                {
                    if(output=='Group added')
                    {
                        display_error(output, 'good_errors');
                        display_user_groups();
                    }
                    else
                        display_error(output, 'bad_errors');
                });
            }
            
            function display_delete_group_warning(group)
            {
                var title="Warning";
                var body="<p class='text_color' style='font-size:14px' >Any posts or photos whose audience includes '"+group+"' will automatically have that part of their audience set to 'Friends'. Any adds in '"+group+"' will also automatically be placed in the 'Friends' group.</p>";
                var extra_id='';
                var load_id='delete_group_warning_load';
                var confirm="<input class='button red_button' value='Continue' type='button' id='confirm_delete_group' />";
                
                display_alert(title, body, extra_id, load_id, confirm);
                $('#delete_group_warning_load').hide();
                
                $('.alert_box').css('width', '500px');
                
                $('#confirm_delete_group').attr('onClick', "delete_group('"+group+"')");
            }
            
            function delete_group(group)
            {
                close_alert_box();
                $.post('user_groups_query.php',
                {
                    num:6,
                    group:group
                }, function(output)
                {
                    if(output=='Group deleted')
                    {
                        display_error(output, 'good_errors');
                        display_user_groups();
                    }
                    else
                        display_error(output, 'bad_errors');
                });
            }
            
            function display_user_groups()
            {
                $.post('user_groups_query.php',
                {
                    num:1
                }, function(output)
                {
                    var groups=output.groups;
                    
                    $('#group_list_body').html("");
                    for(var x =0; x < groups.length; x++)
                    {
                        if(groups[x]!='Everyone')
                        {
                            if(groups[x]!='Friends'&&groups[x]!='Close Friends'&&groups[x]!='Family')
                                var row=row+"<tr id='groups_row_"+x+"'><td><p class='text_color' style='margin-top:5px;margin-bottom:5px;font-size:14px;' id='group_name_"+x+"'>"+groups[x]+"</p></td><td><input class='button red_button' type='button' value='Delete' id='delete_group_button_"+x+"' /></td></tr>";
                            else
                                var row=row+"<tr id='groups_row_"+x+"'><td><p class='text_color' style='margin-top:5px;margin-bottom:5px;font-size:14px;' id='group_name_"+x+"'>"+groups[x]+"</p></td><td></td></tr>";
                        }
                    }
                    
                    var row=row+"<tr id='add_groups_row'><td ><input type='text' placeholder='New group...' class='input_box' maxlength='50' id='new_group' onFocus='input_in(this);' onBlur='input_out(this);' /></td><td></td></tr>";
                    $('#group_list_body').html(row);
                    
                    for(var x = 0; x < groups.length; x++)
                    {
                        $('#group_name_'+x).attr("display_users_in_group('"+groups[x]+"');");
                        $('#delete_group_button_'+x).attr('onClick', "display_delete_group_warning('"+groups[x]+"');");
                    }
                    
                    initialize();
                    
                }, "json");
            }
            function display_users_in_group()
            {
                
            }
            function delete_background_image()
            {
                $.post('settings_query.php',
                {
                    num:16
                }, function(output)
                {
                    window.location.replace(window.location);
                });
            }
            function fill_birthdays()
            {
                $.post('settings_query.php',
                {
                    num:17
                }, function(output)
                {
                    var year=output.year;
                    
                    $('#year').html("<option value='blankYear'>Year:</option>");
                    var html="";
                    for(var x = 1900; x < 2000; x++)
                    {
                        if(year==x)
                            var selected="selected='selected'";
                        else
                            var selected="";
                        html="<option value='"+x+"' "+selected+">"+x+"</option>"+html;
                    }
                    $('#year').html($('#year').html()+html);
                }, "json");
            }
            function explain_email(num)
            {
                var extra_id='';
                var load_id='explain_email';
                var confirm="";
                if(num==1)
                {
                    var title="User is added";
                    var body="<p style='margin:0px;padding:10px;'>You will get an email when you add request someone and they accept.</p>";
                    display_alert(title, body, extra_id, load_id, confirm);
                }
                else if(num==2)
                {
                    var title="An add posts on my profile";
                    var body="<p style='margin:0px;padding:10px;'>You will get an email when an add posts on your profile.</p>";
                    display_alert(title, body, extra_id, load_id, confirm);
                }
                else if(num==3)
                {
                    var title="An add comments on my post";
                    var body="<p style='margin:0px;padding:10px;'>You will get an email whenever an add comments on one of your posts.</p>";
                    display_alert(title, body, extra_id, load_id, confirm);
                }
                else if(num==4)
                {
                    var title="An add likes my comment";
                    var body="<p style='margin:0px;padding:10px;'>You will get an email whenever an add likes any of your comments.</p>";
                    display_alert(title, body, extra_id, load_id, confirm);
                }
                else if(num==5)
                {
                    var title="An add likes my post";
                    var body="<p style='margin:0px;padding:10px;'>You will get an email whenever an add likes any of your posts.</p>";
                    display_alert(title, body, extra_id, load_id, confirm);
                }
                else if(num==6)
                {
                    var title="An add messages me";
                    var body="<p style='margin:0px;padding:10px;'>You will get an email when an add messages you. (And you're online).</p>";
                    display_alert(title, body, extra_id, load_id, confirm);
                }
                else if(num==7)
                {
                    var title="An add comments on my photos";
                    var body="<p style='margin:0px;padding:10px;'>You will get an email whenever an add comments on any of your photos.</p>";
                    display_alert(title, body, extra_id, load_id, confirm);
                }
                else if(num==8)
                {
                    var title="An add likes my photos";
                    var body="<p style='margin:0px;padding:10px;'>You will get an email whenever an add likes on any of your photos.</p>";
                    display_alert(title, body, extra_id, load_id, confirm);
                }
                else if(num==9)
                {
                    var title="Anything non-adds do";
                    var body="<p style='margin:0px;padding:10px;'>You will get an email whenever anyone you haven't added does something to you. This may include sending an add request, liking comments or posts, commenting on posts, etc...</p>";
                    display_alert(title, body, extra_id, load_id, confirm);
                }
                $('.alert_box').css('width', '500px');
                $('#explain_email').hide();
            }
        </script>
        <script type="text/javascript">
            $(document).ready(function()
            {
                fill_group_list();
                fill_birthdays();
                display_blocked_users();
                display_user_groups();
                display_email_checkboxes();
                display_privacy_checkboxes();
                $('.loading_gif_body').hide();
                display_settings_information();
                change_color();
                <?php include('required_jquery.php'); ?>
                    
                var page_load=(new Date).getTime() - startTime;
                record_page_load_time('settings', page_load);

            });
        </script>
        <script type="text/javascript">
            <?php include('required_google_analytics.js'); ?>
        </script>
    </head>
    <body>
        <?php include('required_html.php'); ?>
        <div id="main">
            <div id="top">
                <?php include('top.php'); ?>
            </div>
            <?php include('required_side_html.php'); ?>
            <table style="margin:0 auto;margin-top:100px;border-spacing:0px;">
                <tbody>
                    <tr>
                        <td style="vertical-align:top" id="settings_left">
                            <table id="settings_left_table">
                                <tbody>
                                    <tr>
                                        <td>
                                            <table id="settings_menu">
                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            <table style="cursor:pointer;" onClick="display_settings_information();">
                                                                <tbody>
                                                                    <tr>
                                                                        <td style="width:15px;" >
                                                                            <img class="icon" src="https://s3.amazonaws.com/redlay.pictures/pictures/settings_icons/info.png" />
                                                                        </td>
                                                                        <td>
                                                                            <span id="settings_menu_information" class="settings_menu title_color" onmouseover="name_over(this);" onmouseout="name_out(this);">Info</span>
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <table style="cursor:pointer;" onClick="display_settings_images();">
                                                                <tbody>
                                                                    <tr>
                                                                        <td style="width:15px;" >
                                                                            <img class="icon" src="https://s3.amazonaws.com/redlay.pictures/pictures/settings_icons/customize.png" />
                                                                        </td>
                                                                        <td>
                                                                            <span id="settings_menu_images" class="settings_menu title_color" onmouseover="name_over(this);" onmouseout="name_out(this);">Customize</span>
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <table style="cursor:pointer;" onClick="display_settings_email();">
                                                                <tbody>
                                                                    <tr>
                                                                        <td style="width:15px;" >
                                                                            <img class="icon" src="https://s3.amazonaws.com/redlay.pictures/pictures/settings_icons/email.png" />
                                                                        </td>
                                                                        <td>
                                                                            <span id="settings_menu_email" class="settings_menu title_color" onmouseover="name_over(this);" onmouseout="name_out(this);">Email</span>
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <table style="cursor:pointer;" onClick="display_settings_privacy();">
                                                                <tbody>
                                                                    <tr>
                                                                        <td style="width:15px;" >
                                                                            <img class="icon" src="https://s3.amazonaws.com/redlay.pictures/pictures/settings_icons/privacy.png" />
                                                                        </td>
                                                                        <td>
                                                                            <span id="settings_menu_privacy" class="settings_menu title_color" onmouseover="name_over(this);" onmouseout="name_out(this);">Privacy</span>
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <table style="cursor:pointer;" onClick="display_settings_other();">
                                                                <tbody>
                                                                    <tr>
                                                                        <td style="width:15px;" >
                                                                            <img class="icon" src="https://s3.amazonaws.com/redlay.pictures/pictures/settings_icons/other.png" />
                                                                        </td>
                                                                        <td>
                                                                            <span id="settings_menu_other" class="settings_menu title_color" onmouseover="name_over(this);" onmouseout="name_out(this);">Other</span>
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                        <td style="vertical-align:top" id="settings_middle">
                            <table id="settings_middle_table">
                                <tbody>
                                    <tr>
                                        <td>
                                            <div id="settings" style="margin-left:15px;">
                                                <p class="settings_title" id="settings_title">Settings</p>
                                                <div id="settings_text">
                                                    <div id="settings_information">
                                                        <table class="settings_table">
                                                            <tr>
                                                                <td class="settings_unit_left"><p id="current_password_text"  class="settings_text"></p></td>
                                                                <td class="settings_unit_middle"><input id="current_password" class="input_box" onFocus="input_in(this);" onBlur="input_out(this);" name="current_password" type="password" placeholder="Current Password"/></td>
                                                                <td class="settings_unit_right"></td>
                                                            </tr>
                                                            <tr>
                                                                <td class="settings_unit_left"><p id="new_password_text"  class="settings_text">Change password: </p></td>
                                                                <td class="settings_unit_middle"><input id="new_password" class="input_box" onFocus="input_in(this);" onBlur="input_out(this);" name="new_password" type="password" placeholder="New Password"/></td>
                                                                <td class="settings_unit_right"></td>
                                                            </tr>
                                                            <tr>
                                                                <td class="settings_unit_left"><p id="confirm_new_password_text"  class="settings_text"> </p></td>
                                                                <td class="settings_unit_middle"><input id="confirm_new_password" class="input_box" onFocus="input_in(this);" onBlur="input_out(this);" name="confirm_new_password" type="password" placeholder="Confirm Password"/></td>
                                                                <td class="settings_unit_right"></td>
                                                            </tr>
                                                            <tr><td class="settings_break_unit" colspan="3"><hr class="settings_break"/></td></tr>
                                                            <tr>
                                                                <td class="settings_unit_left"><p id="sex_text" class="settings_text">Change email: </p></td>
                                                                <td class="settings_unit_middle">
                                                                    <input class="input_box" placeholder="New Email" id="email_input" onFocus="input_in(this);" onBlur="input_out(this);" />
                                                                </td>
                                                                <td class="settings_unit_right"></td>
                                                            </tr>
                                                            <tr><td class="settings_break_unit" colspan="3"><hr class="settings_break"/></td></tr>
                                                            <tr>
                                                                <td class="settings_unit_left"><p id="sex_text" class="settings_text">Gender: </p></td>
                                                                <td class="settings_unit_middle">
                                                                    <select id="sex_options" name="sex" onChange="change_sex();">
                                                                        <option value="blankSex">Gender:</option>
                                                                        <option value="Male" <?php if($sex=='Male'){echo "selected='selected'";} ?>>Male</option>
                                                                        <option value="Female" <?php if($sex=='Female'){echo "selected='selected'";} ?>>Female</option>
                                                                        <option value="Other" <?php if($sex=='Other'){echo "selected='selected'";} ?>>Other</option>
                                                                    </select>
                                                                </td>
                                                                <td class="settings_unit_right"></td>
                                                            </tr>
                                                            <tr><td class="settings_break_unit" colspan="3"><hr class="settings_break"/></td></tr>
                                                            <tr>
                                                                <td class="settings_unit_leftt"><p id="month_birthday_title"  class="settings_text">Month: </p></td>
                                                                <td class="settings_unit_middle">
                                                                    <select id="month" name="month" onChange="change_birthday();">
                                                                        <option value="blankMonth">Month:</option>
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
                                                                <td class="settings_unit_right"></td>
                                                            </tr>
                                                            <tr>
                                                                <td class="settings_unit_left"><p id="day_birthday_title"  class="settings_text">Day: </p></td>
                                                                <td class="settings_unit_middle">
                                                                    <select id="day" name="day" onChange="change_birthday();">
                                                                        <option value="blankDay">Day:</option>
                                                                        <option value="1" <?php if($day==1){echo "selected='selected'";} ?>>1</option>
                                                                        <option value="2" <?php if($day==2){echo "selected='selected'";} ?>>2</option>
                                                                        <option value="3" <?php if($day==3){echo "selected='selected'";} ?>>3</option>
                                                                        <option value="4" <?php if($day==4){echo "selected='selected'";} ?>>4</option>
                                                                        <option value="5" <?php if($day==5){echo "selected='selected'";} ?>>5</option>
                                                                        <option value="6" <?php if($day==6){echo "selected='selected'";} ?>>6</option>
                                                                        <option value="7" <?php if($day==7){echo "selected='selected'";} ?>>7</option>
                                                                        <option value="8" <?php if($day==8){echo "selected='selected'";} ?>>8</option>
                                                                        <option value="9" <?php if($day==9){echo "selected='selected'";} ?>>9</option>
                                                                        <option value="10" <?php if($day==10){echo "selected='selected'";} ?>>10</option>
                                                                        <option value="11" <?php if($day==11){echo "selected='selected'";} ?>>11</option>
                                                                        <option value="12" <?php if($day==12){echo "selected='selected'";} ?>>12</option>
                                                                        <option value="13" <?php if($day==13){echo "selected='selected'";} ?>>13</option>
                                                                        <option value="14" <?php if($day==14){echo "selected='selected'";} ?>>14</option>
                                                                        <option value="15" <?php if($day==15){echo "selected='selected'";} ?>>15</option>
                                                                        <option value="16" <?php if($day==16){echo "selected='selected'";} ?>>16</option>
                                                                        <option value="17" <?php if($day==17){echo "selected='selected'";} ?>>17</option>
                                                                        <option value="18" <?php if($day==18){echo "selected='selected'";} ?>>18</option>
                                                                        <option value="19" <?php if($day==19){echo "selected='selected'";} ?>>19</option>
                                                                        <option value="20" <?php if($day==20){echo "selected='selected'";} ?>>20</option>
                                                                        <option value="21" <?php if($day==21){echo "selected='selected'";} ?>>21</option>
                                                                        <option value="22" <?php if($day==22){echo "selected='selected'";} ?>>22</option>
                                                                        <option value="23" <?php if($day==23){echo "selected='selected'";} ?>>23</option>
                                                                        <option value="24" <?php if($day==24){echo "selected='selected'";} ?>>24</option>
                                                                        <option value="25" <?php if($day==25){echo "selected='selected'";} ?>>25</option>
                                                                        <option value="26" <?php if($day==26){echo "selected='selected'";} ?>>26</option>
                                                                        <option value="27" <?php if($day==27){echo "selected='selected'";} ?>>27</option>
                                                                        <option value="28" <?php if($day==28){echo "selected='selected'";} ?>>28</option>
                                                                        <option value="29" <?php if($day==29){echo "selected='selected'";} ?>>29</option>
                                                                        <option value="30" <?php if($day==30){echo "selected='selected'";} ?>>30</option>
                                                                        <option value="31" <?php if($day==31){echo "selected='selected'";} ?>>31</option>
                                                                    </select>
                                                                </td>
                                                                <td class="settings_unit_right"></td>
                                                            </tr>
                                                            <tr>
                                                                <td class="settings_unit_left"><p id="year_birthday_title"  class="settings_text">Year: </p></td>
                                                                <td class="settings_unit_middle">
                                                                    <select id="year" name="year" onChange="change_birthday();">
                                                                        <!--
                                                                            puts birthday years in here
                                                                        -->
                                                                    </select>
                                                                </td>
                                                                <td class="settings_unit_right"></td>
                                                            </tr>
                                                            <tr>
                                                                <td class="settings_unit_left"><p id="show_birthday_text" class="settings_text">Show year on profile</p></td>
                                                                <td class="settings_unit_middle"><input type="checkbox" id="show_birthday_checkbox" onClick="change_birthday();" <?php if($birthday_year=="yes") echo "checked=checked"; ?>/></td>
                                                                <td class="settings_unit_right"></td>
                                                            </tr>
                                                            <tr><td class="settings_break_unit" colspan="3"><hr class="settings_break"/></td></tr>


                                                            <tr>
                                                                <td class="settings_unit_left"><p id="bio_text"  class="settings_text">About: </p></td>
                                                                <td class="settings_unit_middle"><textarea id="change_bio_text" class="settings_input input_box" onFocus="input_in(this);" onBlur="input_out(this);" maxlength="1200" ><?php echo $bio; ?></textarea></td>
                                                                <td class="settings_unit_right"><input class="button settings_button red_button" type="button" onClick="submit_bio();" id="submit_bio_button" value="Change" /></td>
                                                            </tr>
                                                            <tr><td class="settings_break_unit" colspan="3"><hr class="settings_break"/></td></tr>


                                                            <tr>
                                                                <td class="settings_unit_left"><p id="relationship_text"  class="settings_text">Relationship status: </p></td>
                                                                <td class="settings_unit_middle">
                                                                    <select id="relationship_options" name="relationship_options" onchange="change_relationship();">
                                                                        <option value="blankRelationship">Relationship status: </option>
                                                                        <option value="Taken" <?php if($relationship=="Taken"){echo "selected='selected'";} ?>>Taken</option>
                                                                        <option value="Single and looking" <?php if($relationship=="Single and looking"){echo "selected='selected'";} ?>>Single and looking</option>
                                                                        <option value="Single" <?php if($relationship=="Single"){echo "selected='selected'";} ?>>Single</option>
                                                                        <option value="Unsure" <?php if($relationship=="Unsure"){echo "selected='selected'";} ?>>Unsure</option>
                                                                        <option value="Forever alone" <?php if($relationship=="Forever alone"){echo "selected='selected'";} ?>>Forever alone</option>
                                                                        <option value="NA" <?php if($relationship=="NA"){echo "selected='selected'";} ?>>NA</option>
                                                                    </select>
                                                                </td>
                                                                <td class="settings_unit_right"></td>
                                                            </tr>
                                                            <tr><td class="settings_break_unit" colspan="3"><hr class="settings_break"/></td></tr>


                                                            <tr>
                                                                <td class="settings_unit_left"><p id="mood_text"  class="settings_text">Mood: </p></td>
                                                                <td class="settings_unit_middle">
                                                                    <select id="mood_options" name="mood_options" onChange="change_mood();">
                                                                        <option value="blankMood">Mood: </option>
                                                                        <option value="Happy" <?php if($mood=="Happy"){echo "selected='selected'";} ?>>Happy</option>
                                                                        <option value="Angry" <?php if($mood=="Angry"){echo "selected='selected'";} ?>>Angry</option>
                                                                        <option value="Sad" <?php if($mood=="Sad"){echo "selected='selected'";} ?>>Sad</option>
                                                                        <option value="Ambitious" <?php if($mood=="Ambitious"){echo "selected='selected'";} ?>>Ambitious</option>
                                                                        <option value="Accepted" <?php if($mood=="Accepted"){echo "selected='selected'";} ?>>Accepted</option>
                                                                        <option value="Bored" <?php if($mood=="Bored"){echo "selected='selected'";} ?>>Bored</option>
                                                                        <option value="Ashamed" <?php if($mood=="Ashamed"){echo "selected='selected'";} ?>>Ashamed</option>
                                                                        <option value="Pathetic" <?php if($mood=="Pathetic"){echo "selected='selected'";} ?>>Pathetic</option>
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
                                                                        <option value="Forever Alone" <?php if($mood=="Forever Alone"){echo "selected='selected'";} ?>>Forever alone</option>
                                                                        <option value="Sick" <?php if($mood=="Sick"){echo "selected='selected'";} ?>>Sick</option>
                                                                        <option value="Hyper" <?php if($mood=="Hyper"){echo "selected='selected'";} ?>>Hyper</option>
                                                                        <option value="Anxious" <?php if($mood=="Anxious"){echo "selected='selected'";} ?>>Anxious</option>
                                                                        <option value="Drunk" <?php if($mood=="Drunk"){echo "selected='selected'";} ?>>Drunk</option>
                                                                        <option value="Disappointed" <?php if($mood=="Disappointed"){echo "selected='selected'";} ?>>Disappointed</option>
                                                                    </select>
                                                                </td>
                                                                <td class="settings_unit_right"></td>
                                                            </tr>
                                                            <tr><td class="settings_break_unit" colspan="3"><hr class="settings_break"/></td></tr>


                                                            <tr>
                                                                <td class="settings_unit_left"><p id="school_text"  class="settings_text">High School: </p></td>
                                                                <td class="settings_unit_middle"><input type="text" onFocus="input_in(this);" onBlur="input_out(this);" id="high_school" class="input_box" placeholder="High School" value="<?php echo $high_school; ?>" maxlength="40"/></td>
                                                                <td class="settings_unit_right"><input class="button red_button" type="button" value="Change" onClick="change_high_school();" /></td>
                                                            </tr>
                                                            <tr><td class="settings_break_unit" colspan="3"><hr class="settings_break"/></td></tr>

                                                            <tr>
                                                                <td class="settings_unit_left"><p id="school_text"  class="settings_text">College: </p></td>
                                                                <td class="settings_unit_middle"><input type="text" onFocus="input_in(this);" onBlur="input_out(this);" id="college" class="input_box" placeholder="College" value="<?php echo $college; ?>" maxlength="40"/></td>
                                                                <td class="settings_unit_right"><input class="button red_button" type="button" value="Change" onClick="change_college();" /></td>
                                                            </tr>
                                                            <tr><td class="settings_break_unit" colspan="3"><hr class="settings_break"/></td></tr>

                                                        </table>
                                                    </div>
                                                    <div id="settings_images">
                                                        <table class="settings_table">
                                                            <tbody>
                                                                <tr>
                                                                    <td id="profile_picture_unit" colspan="3">
                                                                        <form action="change_profile_picture.php" method="post" enctype="multipart/form-data">
                                                                            <table class="settings_table">
                                                                                <tbody>
                                                                                    <tr>
                                                                                        <td class="settings_unit_left"><p id="profile_picture_text"  class="settings_text">Profile picture: </p></td>
                                                                                        <td class="settings_unit_middle">
                                                                                            <input class="file_button" id="profile_picture_settings" type="file" name="image"/>
                                                                                            <div id="loading_gif_profile_picture" class="loading_gif_body"><img class="load_gif" src="http://pics.redlay.com/pictures/load.gif"/></div>
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
                                                                    <td id="background_picture_unit" colspan="3">

                                                                        <?php if(!file_exists_server(get_user_background_pic($_SESSION['id']))) echo "<form action='change_background_picture.php' method='post' enctype='multipart/form-data'>"; ?>
                                                                            <table class="settings_table">
                                                                                <tbody>
                                                                                    <tr class="background_image_row" id="background_image_row_1">
                                                                                        <td class="background_image_unit" id="background_image_unit_1"><p id="change_background_picture_text" class="settings_text">Background Image: </p></td>
                                                                                        <?php
                                                                                            if(!file_exists_server(get_user_background_pic($_SESSION['id'])))
                                                                                            {
                                                                                                echo "<td class='background_image_unit' id='background_image_unit_1'><input type='file' class='file_button' id='change_background_picture_input' name='image'/></td>";
                                                                                                echo "<td class='background_image_unit' id='background_image_unit_1'><input type='submit'  onClick=\"$('#loading_gif_background_picture').show();\" id='background_picture_submit' class='button red_button' name='submit_background_picture' value='Upload' /></td>";
                                                                                            }
                                                                                            else
                                                                                            {
                                                                                                echo "<td class='background_image_unit' id='background_image_unit_1'></td>";
                                                                                                echo "<td class='background_image_unit' id='background_image_unit_1'><input class='button red_button' type='button' value='Delete' onClick='delete_background_image();' id='background_picture_submit' /></td>";
                                                                                            } 
                                                                                        ?>
                                                                                    </tr>
                                                                                    <?php
                                                                                        if(!file_exists_server(get_user_background_pic($_SESSION['id'])))
                                                                                        {
                                                                                            echo "<tr class='background_image_row' id='background_image_row_2'>";
                                                                                            echo "<td class='settings_unit_left'></td>";
                                                                                            if($background_fixed=='yes')
                                                                                                $checked="checked='checked'";
                                                                                            else
                                                                                                $checked="";
                                                                                            echo "<td class='settings_unit_middle' id='change_background_picture_fixed'><span id='change_background_fixed_text' class='settings_text'>Fixed background: </span><input type='checkbox' value='yes' $checked name='change_background_fixed_checkbox' id='change_background_fixed_input' /><div id='loading_gif_background_picture' class='loading_gif_body'><img class='load_gif' src='http://pics.redlay.com/pictures/load.gif' /></div></td>";
                                                                                            echo "<td class='settings_unit_right'></td>";
                                                                                            echo "</tr>";
                                                                                        }
                                                                                    ?>
                                                                                </tbody>
                                                                            </table>
                                                                        <?php if(!file_exists_server(get_user_background_pic($_SESSION['id']))) echo "</form>"; ?>
                                                                    </td>
                                                                </tr>

                                                                <tr><td class="settings_break_unit" colspan="3"><hr class="settings_break"/></td></tr>

                                                                <tr class="change_color_row">
                                                                    <td class="settings_unit_left"><p class="settings_text">Main color:</p></td>
                                                                    <td class="settings_unit_middle"></td>
                                                                    <td class="settings_unit_right"><input type="button" class="button red_button" id="border_color_button" value="Change" onClick="display_color_wheel(1);" /></td>
                                                                </tr>
                                                                <tr class="change_color_row">
                                                                    <td class="settings_unit_left"><p class="settings_text">Background color:</p></td>
                                                                    <td class="settings_unit_middle"></td>
                                                                    <td class="settings_unit_right"><input type="button" class="button red_button" id="border_color_button" value="Change" onClick="display_color_wheel(2);"  /></td>
                                                                </tr>
                                                                <tr class="change_color_row">
                                                                    <td class="settings_unit_left"><p class="settings_text">Text color:</p></td>
                                                                    <td class="settings_unit_middle"></td>
                                                                    <td class="settings_unit_right"><input type="button" class="button red_button" id="border_color_button" value="Change" onClick="display_color_wheel(3);" /></td>
                                                                </tr>
                                                                <tr  class="change_color_row">
                                                                    <td class="settings_unit_left"><p class="settings_text">Opacity:</p></td>
                                                                    <td class="settings_unit_middle"></td>
                                                                    <td class="settings_unit_right"><input type="button" class="button red_button" id="border_color_button" value="Change" onClick="display_color_wheel(4);" /></td>
                                                                </tr>

                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <div id="settings_email">
                                                        <table class="settings_table">
                                                            <tr>
                                                                <td class="settings_unit_left"><p id="settings_email_title" class="settings_text">Email me when...</p></td>
                                                            </tr>
                                                            <tr>
                                                                <td class="settings_unit_left"><span class="settings_email_text" onClick="explain_email(1);" onmouseover="name_over(this);" onmouseout="name_out(this);" style="cursor:pointer;" >User is added: </span></td>
                                                                <td class="settings_unit_middle" id="email_settings_0"> </td>
                                                                <td class="settings_unit_right"></td>
                                                            </tr>
                                                            <tr>
                                                                <td class="settings_unit_left"><span class="settings_email_text" onClick="explain_email(2);" onmouseover="name_over(this);" onmouseout="name_out(this);" style="cursor:pointer;" >An add posts on my profile: </span></td>
                                                                <td class="settings_unit_middle" id="email_settings_1"> </td>
                                                                <td class="settings_unit_right"></td>
                                                            </tr>
                                                            <tr>
                                                                <td class="settings_unit_left"><span class="settings_email_text" onClick="explain_email(3);" onmouseover="name_over(this);" onmouseout="name_out(this);" style="cursor:pointer;" >An add comments on my post: </span></td>
                                                                <td class="settings_unit_middle" id="email_settings_2"> </td>
                                                                <td class="settings_unit_right"></td>
                                                            </tr>
                                                            <tr>
                                                                <td class="settings_unit_left"><span class="settings_email_text" onClick="explain_email(4);" onmouseover="name_over(this);" onmouseout="name_out(this);" style="cursor:pointer;" >An add likes my comment: </span></td>
                                                                <td class="settings_unit_middle" id="email_settings_3"> </td>
                                                                <td class="settings_unit_right"></td>
                                                            </tr>
                                                            <tr>
                                                                <td class="settings_unit_left"><span class="settings_email_text" onClick="explain_email(5);" onmouseover="name_over(this);" onmouseout="name_out(this);" style="cursor:pointer;" >An add likes my post: </span></td>
                                                                <td class="settings_unit_middle" id="email_settings_5"> </td>
                                                                <td class="settings_unit_right"></td>
                                                            </tr>
                                                            <tr>
                                                                <td class="settings_unit_left"><span class="settings_email_text" onClick="explain_email(6);" onmouseover="name_over(this);" onmouseout="name_out(this);" style="cursor:pointer;" >An add messages me: </span></td>
                                                                <td class="settings_unit_middle" id="email_settings_7"> </td>
                                                                <td class="settings_unit_right"></td>
                                                            </tr>
                                                            <tr>
                                                                <td class="settings_unit_left"><span class="settings_email_text" onClick="explain_email(7);" onmouseover="name_over(this);" onmouseout="name_out(this);" style="cursor:pointer;" >An add comments on my photos: </span></td>
                                                                <td class="settings_unit_middle" id="email_settings_8"> </td>
                                                                <td class="settings_unit_right"></td>
                                                            </tr>
                                                            <tr>
                                                                <td class="settings_unit_left"><span class="settings_email_text" onClick="explain_email(8);" onmouseover="name_over(this);" onmouseout="name_out(this);" style="cursor:pointer;" >An add likes my photos: </span></td>
                                                                <td class="settings_unit_middle" id="email_settings_9"> </td>
                                                                <td class="settings_unit_right"></td>
                                                            </tr>
                                                            <tr>
                                                                <td class="settings_unit_left"><span class="settings_email_text" onClick="explain_email(9);" onmouseover="name_over(this);" onmouseout="name_out(this);" style="cursor:pointer;" >Anything non-adds do: </span></td>
                                                                <td class="settings_unit_middle" id="email_settings_11"> </td>
                                                                <td class="settings_unit_right"></td>
                                                            </tr>
                                                            <tr>
                                                                <td class="settings_unit_left"></td>
                                                                <td class="settings_unit_middle"></td>
                                                                <td class="settings_unit_right"><input id="settings_email_submit" type="button" class="button settings_button red_button" value="Change" onClick="change_email_settings();" onmouseover="{display_title(this, 'Change email settings');}" onmouseout="{hide_title(this);}"/></td>
                                                            </tr>
                                                        </table>


                                                    </div>
                                                    <div id="settings_privacy">
                                                        <table class="settings_table">

                                                            <tr>
                                                                <td class="settings_unit_left"><span class="settings_text">General: </span></td>
                                                                <td class="settings_unit_middle"></td>
                                                                <td class="settings_unit_right"></td>
                                                            </tr>
                                                            <tr>
                                                                <td class="settings_unit_left"><span class="settings_email_text">Users can send add request: </span></td>
                                                                <td class="settings_unit_middle" id="privacy_settings_1"></td>
                                                                <td class="settings_unit_right"></td>
                                                            </tr>
                                                            <tr>
                                                                <td class="settings_unit_left"><span class="settings_email_text">Non-adds can see my background image: </span></td>
                                                                <td class="settings_unit_middle" id="privacy_settings_2"></td>
                                                                <td class="settings_unit_right"></td>
                                                            </tr>
                                                            <tr>
                                                                <td class="settings_unit_left"><span class="settings_email_text">Non-adds can like, dislike, and comment on posts: </span></td>
                                                                <td class="settings_unit_middle" id="privacy_settings_16"></td>
                                                                <td class="settings_unit_right"></td>
                                                            </tr>
                                                            <tr>
                                                                <td class="settings_unit_left"><span class="settings_email_text">Non-adds can like, dislike, and comment on photos: </span></td>
                                                                <td class="settings_unit_middle" id="privacy_settings_17"></td>
                                                                <td class="settings_unit_right"></td>
                                                            </tr>
                                                            <tr><td class="settings_break_unit" colspan="3"><hr class="settings_break"/></td></tr>
                                                            <tr>
                                                                <td class="settings_unit_left"><span class="settings_text">Non-adds can see my: </span></td>
                                                                <td class="settings_unit_middle"></td>
                                                                <td class="settings_unit_right"></td>
                                                            </tr>
                                                            <tr>
                                                                <td class="settings_unit_left"><span class="settings_email_text">Information: </span></td>
                                                                <td class="settings_unit_middle" id="privacy_settings_3"></td>
                                                                <td class="settings_unit_right"></td>
                                                            </tr>
                                                            <tr>
                                                                <td class="settings_unit_left"><span class="settings_email_text">Activity: </span></td>
                                                                <td class="settings_unit_middle" id="privacy_settings_15"></td>
                                                                <td class="settings_unit_right"></td>
                                                            </tr>
                                                            <tr>
                                                                <td class="settings_unit_left"><span class="settings_email_text">Adds: </span></td>
                                                                <td class="settings_unit_middle" id="privacy_settings_4"></td>
                                                                <td class="settings_unit_right"></td>
                                                            </tr>
                                                            <tr>
                                                                <td class="settings_unit_left"><span class="settings_email_text">Posts: </span></td>
                                                                <td class="settings_unit_middle" id="privacy_settings_5"></td>
                                                                <td class="settings_unit_right"></td>
                                                            </tr>
                                                            <tr>
                                                                <td class="settings_unit_left"><span class="settings_email_text">Photos: </span></td>
                                                                <td class="settings_unit_middle" id="privacy_settings_6"></td>
                                                                <td class="settings_unit_right"></td>
                                                            </tr>
                                                            <tr>
                                                                <td class="settings_unit_left"><span class="settings_email_text">Videos: </span></td>
                                                                <td class="settings_unit_middle" id="privacy_settings_7"></td>
                                                                <td class="settings_unit_right"></td>
                                                            </tr>
                                                            <tr>
                                                                <td class="settings_unit_left"><span class="settings_email_text">Docs: </span></td>
                                                                <td class="settings_unit_middle" id="privacy_settings_8"></td>
                                                                <td class="settings_unit_right"></td>
                                                            </tr>
                                                            <tr>
                                                                <td class="settings_unit_left"><span class="settings_email_text">Likes: </span></td>
                                                                <td class="settings_unit_middle" id="privacy_settings_9"></td>
                                                                <td class="settings_unit_right"></td>
                                                            </tr>

                                                            <tr><td class="settings_break_unit" colspan="3"><hr class="settings_break"/></td></tr>



                                                            <tr>
                                                                <td class="settings_unit_left"><span class="settings_text">I can be searched by: </span></td>
                                                                <td class="settings_unit_middle"></td>
                                                                <td class="settings_unit_right"></td>
                                                            </tr>
                                                            <tr>
                                                                <td class="settings_unit_left"><span class="settings_email_text">First Name: </span></td>
                                                                <td class="settings_unit_middle" id="privacy_settings_10"></td>
                                                                <td class="settings_unit_right"></td>
                                                            </tr>
                                                            <tr>
                                                                <td class="settings_unit_left"><span class="settings_email_text">Last Name: </span></td>
                                                                <td class="settings_unit_middle" id="privacy_settings_11"></td>
                                                                <td class="settings_unit_right"></td>
                                                            </tr>
                                                            <tr>
                                                                <td class="settings_unit_left"><span class="settings_email_text">High School: </span></td>
                                                                <td class="settings_unit_middle" id="privacy_settings_12"></td>
                                                                <td class="settings_unit_right"></td>
                                                            </tr>
                                                            <tr>
                                                                <td class="settings_unit_left"><span class="settings_email_text">College: </span></td>
                                                                <td class="settings_unit_middle" id="privacy_settings_13"></td>
                                                                <td class="settings_unit_right"></td>
                                                            </tr>
                                                            <tr>
                                                                <td class="settings_unit_left"><span class="settings_email_text">City: </span></td>
                                                                <td class="settings_unit_middle" id="privacy_settings_14"></td>
                                                                <td class="settings_unit_right"></td>
                                                            </tr>
                                                            <tr>
                                                                <td class="settings_unit_left"></td>
                                                                <td class="settings_unit_middle"></td>
                                                                <td class="settings_unit_right"><input id="settings_privacy_submit" type="button" class="button settings_button red_button" value="Change" onClick="change_privacy_settings();" onmouseover="{display_title(this, 'Change email settings');}" onmouseout="{hide_title(this);}" /></td>
                                                            </tr>




                                                        </table>


                                                    </div>
                                                    <div id="settings_other">
                                                        <table class="settings_table">
                                                            <tr id="terminate_account_settings">
                                                                <td class="settings_unit_left"><p id="terminate_account_text"  class="settings_text">Terminate Account: </p></td>
                                                                <td class="settings_unit_middle"></td>
                                                                <td class="settings_unit_right"><input type="button" class="button settings_button red_button" id="terminate_account_button" value="Terminate" onClick="terminate_account_confirmation();" onmouseover="{display_title(this, 'WARNING! Will PERMANENTLY delete account!');}" onmouseout="{hide_title(this);}" /></td>
                                                            </tr>
                                                            <tr >
                                                                <td class="settings_break_unit" colspan="3"><hr class="settings_break"/></td>
                                                            </tr>
                                                            <tr id="change_first_name">
                                                                <td class="settings_unit_left"><p id="change_first_name_text" class="settings_text">First name: </p></td>
                                                                <td class="settings_unit_middle"><input type="text" id="change_first_name_input" class="input_box" onFocus="input_in(this);" onBlur="input_out(this);" value="<?php echo $first_name; ?>" maxlength="20" placeholder="First: "/></td>
                                                                <td class="settings_unit_right"></td>
                                                            </tr>
                                                            <tr id="change_last_name">
                                                                <td class="settings_unit_left"><p id="change_last_name_text" class="settings_text">Last name: </p></td>
                                                                <td class="settings_unit_middle"><input type="text" id="change_last_name_input" class="input_box" onFocus="input_in(this);" onBlur="input_out(this);" value="<?php echo $last_name; ?>" maxlength="20"placeholder="Last: "/></td>
                                                            </tr>
                                                            <tr><td class="settings_break_unit" colspan="3"><hr class="settings_break"/></td></tr>
                                                            <tr id="blocked_users">
                                                                <td class="settings_unit_left"><p id="blocked_users_text" class="settings_text" >Blocked Users:</p></td>
                                                                <td class="settings_unit_middle" id="blocked_users_middle_unit">
                                                                    <table id="blocked_users_table">
                                                                        <tbody id="blocked_users_table_body">

                                                                        </tbody>
                                                                    </table>
                                                                </td>
                                                            </tr>
                                                            <tr><td class="settings_break_unit" colspan="3"><hr class="settings_break"/></td></tr>
                                                            <tr id="audiences">
                                                                <td class="settings_unit_left"><p class="settings_text" >Groups:</p></td>
                                                                <td class="settings_unit_middle">
                                                                    <table id="group_list">
                                                                        <tbody id="group_list_body">

                                                                        </tbody>
                                                                    </table>
                                                                </td>
                                                                <td class="settings_unit_right"></td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                 </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <?php include('footer.php'); ?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </body>

    <script type="text/javascript">
        function initialize()
        {
            
            $('.input_box').unbind('keypress').unbind('keydown').unbind('keyup');
            $('.input_box').keyup(function(e)
            {
                var key = (e.keyCode ? e.keyCode : e.which);
                if(key == '13')
                {
                    if($(this).attr('id')=='confirm_new_password')
                        change_password();
                    else if($(this).attr('id')=='email_input')
                        change_email();
                    else if($(this).attr('id')=='high_school')
                        change_high_school();
                    else if($(this).attr('id')=='college')
                        change_college();
                    else if($(this).attr('id')=='change_first_name_input'||$(this).attr('id')=='change_last_name_input')
                        change_name();
                    else if($(this).attr('id')=='new_group')
                        add_group();
                }
            });
        }
    </script>
</html>