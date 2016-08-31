<?php
@include('init.php');
include('universal_functions.php');
$allowed="users";
include('security_checks.php');


?>
<!DOCTYPE html>
<html>
    <head>
        <title>Messages</title>
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
                $('.box').css('border', '5px solid <?php echo $color; ?>');
                $('.box').css('background-color', '<?php echo $box_background_color; ?>');
                $('#message_alert_none, .message, #company_footer, .message_timestamp').css('color', '<?php echo $text_color; ?>');
                $('.user_name_message, .user_name, .home_name, .user_name_sent').css('color', '<?php echo $color ?>');
                
                $('#message_box_content').css('background-color', '<?php echo $box_background_color; ?>');
                
                $('.title_color').css('color', '<?php echo $color; ?>');
                $('.text_color').css('color', '<?php echo $text_color; ?>');
            }
            
            function display_messages(ID, page)
            {
                $('#messages_gif').show();
                $('#message_button').attr('onClick', '{message('+ID+');}').show();
                var timezone=get_timezone();
                
                
                $.post('message_query.php',
                {
                    num:2,
                    user_id: ID,
                    timezone: timezone,
                    page: page
                }, function(output)
                {
                    var messages=output.messages;
                    var message_spaces=output.message_spaces;
                    var message_timestamps=output.message_timestamps;
                    var message_timestamps_seconds=output.message_timestamps_seconds;
                    var message_user_sent=output.message_user_sent;
                    var message_names=output.message_names;
                    var profile_pictures=output.profile_pictures;
                    var empty=output.empty;
                    var total_size=output.total_size;
                    
                    
                    if(total_size!=0)
                    {
                        if(page==1)
                        {
                            $('#message_content').html('');
                            for(var x = 1; x <= (total_size/10)+1; x++)
                                $('#message_content').html($('#message_content').html()+"<div id='message_page_"+x+"'></div>");

                            if(total_size<10)
                                $('#message_content').html("<div id='message_page_1'></div>");

                            $('#message_content').html($('#message_content').html()+"<div id='see_more_body'></div>");
                        }
                        
                        var html="";
                        for(var x = 0; x < messages.length; x++)
                        {
                            if(message_user_sent[0]!='')
                            {
                                message_spaces[x]=text_format(convert_image(message_spaces[x], 'post'));
                                var name="<div class='user_name_body'><a href='http://www.redlay.com/profile.php?user_id="+message_user_sent[x]+"' class='message_name_link'><span class='user_name_sent' onmouseover=name_over(this); onmouseout=name_out(this); >"+message_names[x]+"</span></a></div>";
                                var picture="<div class='message_messages'><a href='http://www.redlay.com/profile.php?user_id="+message_user_sent[x]+"'><img class='profile_picture_message profile_picture' src='"+profile_pictures[x]+"' /></a>";
                                var message="<span class='message'>"+message_spaces[x]+"</span>";
                                var timestamp="<span class='message_timestamp' id='message_timestamp_"+ID+"_messages_"+message_user_sent[x]+"_"+x+"'>"+message_timestamps[x]+"</span>";


                                var body=get_post_format(picture, name, message, '', timestamp, '', ID+'_message_'+x)
                                html=html+body
                            }
                        }
                        
                        $('#message_page_'+page).html(html);

                        //counts time
                        for(var x = 0; x < messages.length; x++)
                            count_time(message_timestamps_seconds[x], '#message_timestamp_'+ID+'_messages_'+message_user_sent[x]+'_'+x);
                            
                        //adds see more button
                        if(empty==false&&page==1)
                        {
                            $('#see_more_body').html($('#see_more_body').html()+"<input class='button see_more_posts blue_button' value='See More' type='button'>");
                            $('.see_more_posts').attr({'onmouseover': "{display_title(this, 'See more messages');}", 'onmouseout': "{hide_title(this);}", 'onClick': "display_messages("+ID+", "+(page+1)+");"});
                        }
                        else if(empty==true)
                            $('.see_more_posts').hide();
                        else
                            $('.see_more_posts').attr('onClick', "display_messages("+ID+", "+(page+1)+");");
                    }
                    else
                        $('#message_content').html("<p id='message_alert_none'>You have no messages</p>");
                        
                    change_color();
                    $('#messages_gif').hide();
                }, "json");
            }
            
            function display_adds()
            {
                //displays appropriate HTML
                $('#adds_select_box').html("<input class='button gray_button' value='Adds' type='button' id='adds_list_button' >");
                $('#adds_select_box').html($('#adds_select_box').html()+"<div id='adds_select_box_box_inside' class='select_body_options'></div>");
                $('#adds_select_box_box_inside').html("<table class='select_body_options_table'><tbody id='adds_select_box_body' class='select_body_options_table_body'></tbody></table>");
                $('#adds_select_box_box_inside').hide();
                $('#adds_list_button').attr({'onClick': "toggle_group_display('adds_select_box');"});


                $.post('message_query.php',
                {
                    num:5,
                    user_id: <?php echo $_SESSION['id']; ?>
                }, function(output)
                {
                    var adds=output.adds;
                    var names=output.names;
                    var profile_pictures=output.profile_pictures;
                    var is_added=output.is_added;

                    for(var x = 0; x < adds.length; x++)
                    {
                        if(adds[x]!=0)
                        {
                            $('#adds_select_box_body').html($('#adds_select_box_body').html()+"<tr class='select_body_options_row' id='adds_select_box_audience_row_"+x+"'></tr>");
                            $('#adds_select_box_audience_row_'+x).html("<td class='audience_selection_checkbox_unit' id='adds_select_box_audience_selection_checkbox_"+x+"'></td><td class='audience_selection_checkbox_unit' id='adds_select_box_audience_selection_profile_picture_"+x+"'></td><td class='select_body_option_unit' id='adds_select_box_audience_selection_text_"+x+"'></td>");
                                if(is_added[x]==true)
                                    $('#adds_select_box_audience_selection_checkbox_'+x).html("<img class='checkbox' src='http://pics.redlay.com/pictures/gray_checkbox_checked.png' id='add_checkbox_"+x+"'/>");
                                else
                                    $('#adds_select_box_audience_selection_checkbox_'+x).html("<img class='checkbox' src='http://pics.redlay.com/pictures/gray_checkbox.png' id='add_checkbox_"+x+"'/>");


                                $('#add_checkbox_'+x).attr('onClick', "{toggle_regular_checkbox(this);toggle_user("+x+", "+adds[x]+");}");
                                $('#adds_select_box_audience_selection_profile_picture_'+x).html("<img class='add_list_profile_picture' id='adds_select_box_profile_picture_"+x+"' src='"+profile_pictures[x]+"'/>");
                                $('#adds_select_box_audience_selection_text_'+x).html("<p class='select_body_option_text'>"+names[x]+"</p>");
                        }
                    }
                }, "json");
            }
            
            function toggle_user(index, user_id)
            {
                //user gets added
                if($('#add_checkbox_'+index).attr('src')=='http://pics.redlay.com/pictures/gray_checkbox_checked.png')
                {
                    $.post('message_query.php',
                    {
                        num:3,
                        user_id: user_id
                    }, function(output)
                    {
                         display_names();
                         $('#message_content').html("<p id='message_alert_none'>You have no messages</p>");
                    });
                }
                else
                {
                    $.post('message_query.php',
                    {
                        num:4,
                        user_id: user_id
                    }, function(output)
                    {
                         display_names();
                         $('#message_content').html("<p id='message_alert_none'>You have no messages</p>");
                    });
                }    
            }

            function display_names()
            {
                //puts in form to add new user
                $('#name_table_body').html("<tr><td colspan='2'><div class='select_body' id='adds_select_box'>   </div></td></tr>");
                $('#add_adds_button').attr('onClick', "toggle_group_display('adds_message_list_body')");
                display_adds();
                
                $.post('message_query.php',
                {
                    num:1
                }, function(output)
                {
                    var names=output.names;
                    var user_ids=output.user_ids;
                    var new_messages=output.new_messages
                    var profile_pictures=output.profile_pictures;
                    
                    
                    if(names!='none')
                    {
                        for(var x =0; x < names.length; x++)
                        {
                            if(new_messages[x]>0)
                                var number="("+new_messages[x]+")";
                            else
                                var number='';
                            
                            $('#name_table_body').html($("#name_table_body").html()+"<tr id='name_row_"+x+"' class='message_name_row'></tr>");
                            var profile_picture="<td class='home_profile_picture_unit'><img class='home_name_profile_picture' src='"+profile_pictures[x]+"' /></td>";
                            var name="<td class='message_name_unit'><p onClick='display_messages("+user_ids[x]+", 1)' class='home_name' onmouseover=name_over(this); onmouseout=name_out(this); id='name_"+x+"'>"+names[x]+" "+number+"</p></td>";
                            $('#name_row_'+x).html(profile_picture+name);
                            $('#name_'+x).attr({'onClick': "display_messages("+user_ids[x]+", 1);$('#name_"+x+"').html('"+names[x]+"');"});
                        }
                    }
                    else
                    {
                        $('#name_table_body').html($('#name_table_body').html()+"<tr><td><p>You have not messaged anyone yet. Click on the adds button to add people to your message list!</p></td></tr>");
                    }
                    
                    change_color();
                }, "json");
            }
            function display_group_chat_names()
            {
                $.post('message_query.php',
                {
                    num:6
                }, function(output)
                {
                    var chat_ids=output.chat_ids;
                    var chat_names=output.chat_names;
                    var new_messages=output.new_messages;
                    
                    if(chat_ids.length>=1&&chat_ids[0]!='')
                    {
                        for(var x = 0; x < chat_ids.length; x++)
                        {
                            var chat_name="<span class='text_color'>"+chat_names[x]+"</span>";
                            var new_chat_messages="<";
                        }
                    }
                    else
                        $('#group_chat_table_body').html("<tr><td><p class='text_color'>You don't have any group chats</p></td></tr>");
                    
                }, "json");
            }
            
            function add_user()
            {
                $.post('message_query.php',
                {
                    num:3,
                    user_id: $('#add_user_input').val()
                }, function(output)
                {
                    
                });
            }
            function new_group_chat()
            {
                
            }
            
            $(window).ready(function()
            {
                $('#messages_gif').hide();
                $('#message_button').hide();
                display_names();
                initialize_input_event();
                $('#menu').hide();
                $('#footer').css('width', '920px');
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
            
            <div class="content" style="min-height:430px;" id="message_box_content">
                
                
                <table style="position:relative;">
                    <tbody>
                        <tr>
                            <td style="vertical-align:top;">
                                <div id="message_names">
                                    <p id="message_names_title" class="title_color">Names</p>
                                    <div style="overflow: scroll;height: 370px;border: 1px solid gray;box-shadow: inset 0px 0px 1px gray;">
                                        <table>
                                            <tbody id="name_table_body">

                                            </tbody>
                                        </table>
                                    </div>
                                    <p class="title_color" style="text-align:center;margin:5px;margin-top:15px;">Group chats</p>
                                    <div style="overflow: scroll;height: 370px;border: 1px solid gray;box-shadow: inset 0px 0px 1px gray;">
                                        <?php 
                                            if(has_redlay_gold($_SESSION['id'], "group_chats"))
                                            {
                                                echo "<input class='button red_button' value='Create chat' onClick='new_group_chat();' type='button' />";
                                            }
                                        ?>
                                        <table id="group_chat_table">
                                            <tbody id="group_chat_table_body">

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </td>
                            <td style="vertical-align:top;">
                                <div id="messages_content_body">
                                    <img class="load_gif" src="http://pics.redlay.com/pictures/load.gif" id="messages_gif"/>
                                    <table id="message_form">
                                        <tr>
                                            <td>
                                                <textarea autofocus id="message_body" class="input_box" onFocus="input_in(this);" onBlur="input_out(this);" placeholder='Reply...' maxlength='1000'></textarea>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="text-align:right">
                                                <input class="button red_button" type="button" id="message_button" value="Send" />
                                            </td>
                                        </tr>
                                    </table>

                                    <div id="message_content">
                                        <p id="message_alert_none">You have no messages</p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                
                
                
                <?php include('footer.php'); ?>
            </div>
        </div>
        <script type="text/javascript">
            function initialize_input_event()
            {
                $('#add_user_input').unbind('keypress').unbind('keydown').unbind('keyup');
                $('#add_user_input').keyup(function(e)
                {
                    var key = (e.keyCode ? e.keyCode : e.which);
                    //enter key
                    if(key == '13')
                    {
                        add_user();
                        $(this).val('');
                    }
                });
            }
        </script>
    </body>
</html>