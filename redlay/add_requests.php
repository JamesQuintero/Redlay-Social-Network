<?php
@include('init.php');
include('universal_functions.php');
$allowed='users';
include('security_checks.php');

?>

<!DOCTYPE html>
<html>
    <head>
        <?php
            $query=mysql_query("SELECT new_friend_alerts FROM alerts WHERE user_id=$_SESSION[id] LIMIT 1");
            if($query&&mysql_num_rows($query)==1)
            {
                $array=mysql_fetch_row($query);
                $new_alerts=$array[0];
            }
        ?>
        <title><?php echo $new_alerts; ?> new add request<?php if($new_alerts!=1) echo "s"; ?>!</title>
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
                    $('.name, .footer_text').css('color', '<?php echo $color ?>');
                    $('#content, .friend_message, #company_footer, #friend_request_alert_none, .alert_box_title').css('color', '<?php echo $text_color; ?>');
                        
                    $('.title_color').css('color', '<?php echo $color; ?>');
                    $('.text_color').css('color', '<?php echo $text_color; ?>');
                }

                function display_friend_alerts()
                {
                    var timezone=get_timezone();
                    $.post('friend_request_alerts_query.php',
                    {
                        timezone:timezone
                    }, function(output)
                    {
                        var user_ids=output.other_user_ids;
                        var timestamp_seconds=output.timestamp_seconds;
                        var user_names=output.user_names;
                        var user_is_friends=output.user_is_friends;
                        var messages=output.messages;
                        var profile_pictures=output.profile_pictures;
                        var num_adds=output.num_adds;


                        if(user_ids.length>0)
                        {
                            $('#friend_request_content_body').html("<table id='friend_request_alerts_table'></table>");
                            for(var x = 0; x < user_ids.length; x++)
                            {
                                $('#friend_request_alerts_table').html($('#friend_request_alerts_table').html()+"<tr class='friend_request_alert_row' id='row_"+x+"'></tr>");
                                var name="<div class='user_name_body'><a class='link' href='http://www.redlay.com/profile.php?user_id="+user_ids[x]+"'><p class='user_name title_color' id='name_"+x+"' onmouseover=name_over(this); onmouseout=name_out(this);>"+user_names[x]+"</p></a></div>";
                                var profile_picture="<td class='profile_picture_unit'><a href='http://www.redlay.com/profile.php?user_id="+user_ids[x]+"'><img src='"+profile_pictures[x]+"' id='friend_profile_picture_"+x+"' class='friend_profile_pic profile_picture'/></a></td>";
                                if(user_is_friends[x]==true)
                                {
                                    var message="<p class='text_color' style='margin-top:0px;'>You are already adds!</p>";
                                    var buttons="<td class='buttons_unit'><input type='button' class='unfriend_button' id='unfriend_button_"+x+"' onClick='unfriend("+user_ids[x]+");' value='Unfriend' /></td></tr>";
                                }
                                else
                                {
                                    if(messages[x]!='')
                                        var message="<p class='text_color' style='margin-top:0px;'>"+messages[x]+"</p>";
                                    else
                                        var message="<p class='text_color' style='margin-top:0px;'></p>";
                                    var buttons="<td class='buttons_unit'><input id='accept_button_"+x+"' class='button green_button' value='Accept' type='button'/></td><td class='buttons_unit'><input type='button' class='button red_button' id='decline_button_"+x+"' onClick='decline_request("+user_ids[x]+");' value='Decline'/></td></tr>";
                                }
                                
                                var timestamp="<span class='text_color' id='add_timestamp_"+user_ids[x]+"_user_"+x+"'>"+timestamp_seconds+"</span>";
                                
                                $('#row_'+x).html(profile_picture+"<td class='user_name_unit'><div class='name_message_body'>"+name+message+timestamp+"</div></td>"+buttons);
                                
                                $('#accept_button_'+x).attr('onClick', "display_accept_menu("+user_ids[x]+", '"+user_names[x]+"');");
                                $('#friend_profile_picture_'+x).attr({'onmouseover': "{display_title(this, '"+num_adds[x]+" adds');}", 'onmouseout': "{hide_title(this);}"});
                            }
                            
                            for(var x = 0; x < user_ids.length; x++)
                            {
                                count_time(timestamp_seconds[x], "#add_timestamp_"+user_ids[x]+"_user_"+x);
                            }
//                            $('#friend_request_content').html($('#friend_request_content').html()+"<input id='unread_button' class='button red_button' type='button' onClick='mark_unread();' value='Mark All Unread' />");
//                            $('#unread_button').attr({'onmouseover': "{display_title(this, 'Make it like you never viewed these friend requests');}", 'onmouseout': "{hide_title(this);}"});
                        }
                        else
                            $('#friend_request_content').html("<div id='friend_request_content_body'><p id='friend_request_alert_none'>You have no add requests</p></div>");
                        $('#beginning_load_gif').hide();
                        change_color();
                    }, "json");
                }

            </script>
        <script type="text/javascript">
            $(window).ready(function()
            {
                display_friend_alerts();
                change_color();
                $('#footer').css('width', '910px');
                <?php include('required_jquery.php'); ?>
            });
        </script>
    </head>
    <body>
        <?php include('required_html.php'); ?>

        <div id="top">
            <?php include('top.php'); ?>
        </div>
        <div id="main">
            <?php include('required_side_html.php'); ?>
            <div id="friend_request_content" class="box">
                <img class="load_gif" id="beginning_load_gif" src="http://pics.redlay.com/pictures/load.gif"/>
                <div id="friend_request_content_body">
                    <p id="friend_request_alert_none">You have no add requests</p>
                </div>
            </div>
        </div>
        <?php include('footer.php'); ?>
    </body>
</html>
