<?php
@include('init.php');
include('../universal_functions.php');
$allowed="users";
include('security_checks.php');


?>

<!DOCTYPE html>
<html>
    <head>
        <title></title>
        <?php include('required_header.php'); ?>
        <script type="text/javascript">
            function change_color()
            {
                <?php
                        $colors=get_user_display_colors($_SESSION['id']);
                        $color=$colors[0];
                        $box_background_color=$colors[1];
                        $text_color=$colors[2];
                ?>
                $('.box').css('border', '15px solid <?php echo $color; ?>');
                $('.box').css({'background-color': '<?php echo $box_background_color; ?>'});
                
                $('.title_color').css('color', '<?php echo $color; ?>');
                $('.text_color').css('color', '<?php echo $text_color; ?>');
                
            }
            
            function display_adds()
            {
                //displays appropriate HTML
                $('#adds_select_box').html($('#adds_select_box').html()+"<div id='adds_select_box_box_inside' class='select_body_options' style='width:100%;'></div>");
                $('#adds_select_box_box_inside').html("<table class='select_body_options_table'><tbody id='adds_select_box_body' class='select_body_options_table_body'></tbody></table>");
                $('#adds_select_box_box_inside').hide();
                $('#adds_list_button').attr({'onClick': "toggle_group_display('adds_select_box');"});

                $.post('main_access.php',
                {
                    access:40,
                    num:5,
                    user_id: <?php echo $_SESSION['id']; ?>
                }, function(output)
                {
                    var adds=output.adds;
                    var names=output.names;
                    var profile_pictures=output.profile_pictures;

                    for(var x = 0; x < adds.length; x++)
                    {
                        $('#adds_select_box_body').html($('#adds_select_box_body').html()+"<tr class='select_body_options_row' id='adds_select_box_audience_row_"+x+"' ></tr>");
                            $('#adds_select_box_audience_row_'+x).attr('onClick', "display_messages("+adds[x]+");toggle_group_display('adds_select_box');");
                        $('#adds_select_box_audience_row_'+x).html("<td class='audience_selection_checkbox_unit' id='adds_select_box_audience_selection_checkbox_"+x+"'></td><td class='audience_selection_checkbox_unit' id='adds_select_box_audience_selection_profile_picture_"+x+"'></td><td class='select_body_option_unit' id='adds_select_box_audience_selection_text_"+x+"'></td>");

                            
                        $('#adds_select_box_audience_selection_profile_picture_'+x).html("<img class='add_list_profile_picture' id='adds_select_box_profile_picture_"+x+"' src='http://www.redlay.com/"+profile_pictures[x]+"'/>");
                        $('#adds_select_box_audience_selection_text_'+x).html("<p class='select_body_option_text'>"+names[x]+"</p>");
                    }
                    $('#adds_select_box_box_inside').css('width', $('#adds_select_box').width());
                }, "json");
            }
            
            function message(ID)
            {
                $.post('message_user.php',
                {
                    user_id: ID,
                    message: $('#message_body').val()
                }, function(output)
                {
                    if(output=="Message sent!")
                    {
                        display_error(output, 'good_errors');
                        display_messages(ID);
                    }
                    else
                        display_error(output, 'bad_errors');
                });
            }
            
            function display_messages(ID)
            {
                //displays the send button
                $('#send_button_unit').html("<input class='red_button' type='button' value='Send' id='send_button'/>");
                $('#send_button').attr('onClick', 'message('+ID+');');
                
                
                $.post('main_access.php',
                {
                    access:40,
                    num:2,
                    user_id: ID,
                    timezone: get_timezone()
                }, function(output)
                {
                    var messages=output.messages;
                    var message_timestamps=output.message_timestamps;
                    var message_user_sent=output.message_user_sent;
                    var message_names=output.message_names;
                    
                    if(message_user_sent[0]!='')
                    {
                        $('#message_content').html('');
                        for(var x = 0; x < messages.length; x++)
                        {
                            messages[x]=text_format(messages[x]);
                            var name="<a href='http://www.redlay.com/profile.php?user_id="+message_user_sent[x]+"' class='message_name_link'><p id='user_name_sent_"+x+"' class='user_name_sent' onmouseover=name_over(this); onmouseout=name_out(this); >"+message_names[x]+"</p></a>";
                            var picture="<div class='message_messages'><a href='http://www.redlay.com/profile.php?user_id="+message_user_sent[x]+"'><img id='profile_picture_"+x+"' class='profile_picture_message profile_picture' src='http://www.redlay.com/users/thumbs/users/"+message_user_sent[x]+"/0.jpg' /></a>";
                            var message="<p class='message'>"+messages[x]+"</p>";
                            var timestamp="<p class='message_timestamp' >"+message_timestamps[x]+"</p>";
                            var message_break="<hr class='message_break' /></div>";

                            var content=$('#message_content').html();
                            $('#message_content').html(picture+name+message+timestamp+message_break+content);
                        }
                    }
                    else
                        $('#message_content').html("<p id='message_alert_none'>You have no messages</p>");
                    change_color();
                }, "json");
            }

            function display_text_format()
            {
                var title="Text Format";
                var extra_id="text_format_extra";
                var load_id="text_format_load";
                var confirm="";

                var body="<table id='text_format_table' style='width:100%;'><tbody id='text_format_table_body'></tbody></table>";


                display_alert(title, body, extra_id, load_id, confirm);
                $('#text_format_load').hide();

                $('#text_format_table_body').html("<tr id='text_format_body_row_1'></tr><tr id='text_format_body_row_2'></tr>");
                    $('#text_format_body_row_1').html("<td id='text_format_input_unit'></td>");
                        $('#text_format_input_unit').html("<textarea class='input_box' placeholder='Try it!' id='text_format_input' style='width:100%;height:100px;' onFocus='input_in(this);' onBlur='input_out(this);'></textarea>");
                            $('text_format_input').attr({'onFocus': "input_in(this);", 'onBlur': "input_out(this);"});
                    $('#text_format_body_row_2').html("<td id='text_format_output_unit'></td>");
                        $('#text_format_output_unit').html("<div class='post_preview_box' id='text_format_preview_box'></div><hr class='break'/><div id='text_format_info'></div>");


                    var profile_picture="<img class='profile_picture_status profile_picture' src='./users/thumbs/users/<?php echo $_SESSION['id']; ?>/0.jpg' id='text_format_profile_picture' />";
                    var text="<p class='status_update_text text_color' id='text_format_text' style='width:315px;'></p>";
                    var name="<div class='user_name_body'><span class='user_name' id='text_format_preview_name'><?php echo get_user_name($_SESSION['id']); ?></span></div>";

                    var row_1="<tr id='post_row_1' class='post_row'>  <td class='post_profile_picture_unit'>"+profile_picture+"</td><td class='post_body_unit'>"+name+text+"</td>  </tr>";

                    $('#text_format_preview_box').html("<div id='text_format_preview' class='status_update' style='margin:5px'><table style='width:100%;'><tbody>"+row_1+"</tbody></table></div>");

                            $('#text_format_info').html("<table style='width:100%;margin-top:20px;' border='1'><tbody id='text_format_info_table_body'></tbody></table>");
                                $('#text_format_info_table_body').html("<tr id='text_format_row_1'></tr><tr id='text_format_row_2'></tr><tr id='text_format_row_3'></tr><tr id='text_format_row_4'></tr><tr id='text_format_row_5'></tr><tr id='text_format_row_6'></tr>");
                                    $('#text_format_row_1').html("<td><p style='font-weight:bold;margin:5px;'>Bold:</p></td><td><p style='margin:5px;'>[b](This is bold) = <span style='font-weight:bold;'>This is bold</span></p></td>");
                                    $('#text_format_row_2').html("<td><p style='font-style:italic;margin:5px;'>Italics:</p></td><td><p style='margin:5px;'>[i](This is italics) = <span style='font-style:italic'>This is italics</span></p></td>");
                                    $('#text_format_row_3').html("<td><p style='text-decoration:underline;margin:5px;'>Underline:</p></td><td><p style='margin:5px;'>[u](This is underlined) = <span style='text-decoration:underline;'>This is underlined</span></p></td>");
                                    $('#text_format_row_4').html("<td><p style='margin:5px;'><span style='color:red;'>C</span><span style='color:orange;'>o</span><span style='color:purple;'>l</span><span style='color:green;'>o</span><span style='color:blue;'>r</span>:</p></td><td><p style='margin:5px;'>[red](This is red) = <span style='color:red;'>This is red</span></p></td>");
                                    $('#text_format_row_5').html("<td><p style='border:1px solid black;width:35px;margin:5px;'>Box:</p></td><td><p style='margin:5px;'>[box](This is boxed) = <span style='border:1px solid black;'>This is boxed</span></p></td>");
                                    $('#text_format_row_6').html("<td><p style='font-size:75%;margin:5px;'>Small:</p></td><td><p style='margin:5px;'>[s](This is small) = <span style='font-size:50%;'>This is small</span></p></td>");

                initialize_text_format_test();
                change_color();
            }

            $(document).ready(function()
            {
                $('#menu').hide();
                $('#footer').css('width', '100%');
                display_adds();
                <?php include('required_jquery.php'); ?>
                $('body').css({'background-image': 'url(\'./pictures/default_background.png\')', 'background-position' :'center 50px'});
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
                <table id="messages_table" style="width:100%;" class="box">
                    <tbody>
                        <tr>
                            <td>
                                <p class="title title_color" >Messages</p>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div id="adds_select_box">
                                    <input class="gray_button" type="button" value="Adds" style="width:100%;" id="adds_list_button"/>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div style="border:1px solid gray;font-size:35px;">
                                    <div id="message_form">
                                        <table style="width:100%;">
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <textarea placeholder="Reply..." class="textarea input_box" onFocus="input_in(this);" onBlur="input_out(this);" style="height:150px;"></textarea>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td >
                                                        <table style="float:right">
                                                            <tbody>
                                                                <tr>
                                                                    <td>
                                                                        <span class="title_color" onmouseover="name_over(this);" onmouseout="name_out(this);" onClick="display_text_format();" style="font-size:35px;">Text format</span>
                                                                    </td>
                                                                    <td id="send_button_unit">
                                                                        
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div id="message_content">
                                        
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            <?php include('footer.php'); ?>
        </div>
    </body>
</html>