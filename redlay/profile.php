<?php
@include('init.php');
include('universal_functions.php');
$allowed="all";
include('security_checks.php');

//if user or page is not logged in or user does not exists
$ID=(int)($_GET['user_id']);

if(!is_id($ID)||!user_id_exists($ID)||user_blocked($ID, $_SESSION['id']))
{
    header("Location: http://www.redlay.com");
    exit();
}

if(user_id_terminated($ID))
{
    header("Location: http://www.redlay.com/account_terminated.php");
    exit();
}

//records profile view
if(isset($_SESSION['id'])&&$ID!=$_SESSION['id'])
    record_profile_view($ID);


//gets the user's privacy preferences
$privacy=get_user_privacy_settings($ID);
$general=$privacy[0];
$display_non_friends=$privacy[1];
$has_gold=has_redlay_gold($ID);
if(isset($_SESSION['id']))
    $user_is_friends=user_is_friends($ID, $_SESSION['id']);
else
    $user_is_friends="false";

$calendar_visible=get_calendar_visibility($ID);
$date=explode(' ', str_replace(',', '', get_adjusted_date(get_date(), 0)));
$month=$date[0];
$year=$date[2];
?>

<!DOCTYPE html>
<html>
    <head>
        <?php
            //gets the user's name
            $name=get_user_name($ID);
            
            //gets the number of alerts for title
            $number=  get_friend_request_alerts() + get_messages_alerts() + get_alert_alerts();
        ?>
        <?php
            //overrides previous number if user has new messages
            if(has_messages_alerts())
            {
                if(get_messages_alerts()==1)
                    $string=get_messages_alerts()." new message!";
                else
                    $string=get_messages_alerts()." new messages!";
            }

            //does or doesn't display the number if the user has no new messages
            if($number!=0)
                $string=$name." "."(".$number.")";
            else
                $string=$name;
        ?>
        <title><?php echo $string; ?></title>
        <script type="text/javascript">
            startTime = (new Date).getTime();
        </script>
        <?php include('required_header.php'); ?>
        <script type="text/javascript">
            function change_color()
            {
                <?php
                        $colors=get_user_display_colors($ID);
                        $color=$colors[0];
                        $box_background_color=$colors[1];
                        $text_color=$colors[2];
                ?>
                $('.box').css('border', '5px solid <?php echo $color; ?>');
                $('.box').css({'background-color': '<?php echo $box_background_color; ?>'});
                
                $('#profile_left_table, #profile_middle_table, #profile_right_table').css('background-color', '<?php echo $box_background_color; ?>');
                <?php //if($has_gold) {echo "$('#redlay_gold_box, #name_box, #profile_menu').css('border-color', 'rgb(252,178,0)');";} ?>

                $('#information_content, .locked, #status_updates, #name_box, #gold_description, .file_input, .audience_option, #company_footer, .alert_box_description').css('color', '<?php echo $text_color; ?>');
                $('#social_update').css('outline-color', '<?php echo $color; ?>');
                $('.post_delete, .comment_delete').css({'background-color': '<?php echo $color; ?>', 'color': '<?php echo $text_color; ?>'});
                $('.comment_textarea').css('outline-color', '<?php echo $color; ?>');
                $('.other_picture').css('border', '2px solid <?php echo $color; ?>');
                $('.title_information, .alert_box_title, .friend_name, .profile_activity_text_video, .comment_name').css('color', '<?php echo $color; ?>');
                $('#bio_title_information, .popular_text, .page_user_name_body, .user_name, .user_name_activity').css('color', '<?php echo $color; ?>');
                <?php $path=get_user_background_pic($ID); if(file_exists_server($path)&&$colors[5]=="yes") echo "$('html').css('background-attachment', 'fixed');"; ?>
                $('.timestamp_status_update, .alert_box_text, .status_update_text, .most_popular_text, .document_info_title, .document_info_body, .empty_text, .profile_information_text, .popular_picture_text, .profile_activity_text').css('color', '<?php echo $text_color; ?>');
                $('.user_name_link, .status_update_dislike, .status_update_like, .footer_text, .friend_name, .profile_text, .comment_text, #start_picture_slide_show, .workspace_text, .comment_like, .comment_dislike, .user_name_activity').css('color', '<?php echo $color; ?>');

                
                $('.title_color').css('color', '<?php echo $color; ?>');
                $('.text_color').css('color', '<?php echo $text_color; ?>');
            }
            
            //num is the indicator to determine what to do
            //num2 is an extra number for display a specific number of items
            function profile_menu_item(num)
            {
                $('.profile_menu_item').css('font-weight', 'normal');
                $('#profile_menu_'+num).css('font-weight', 'bold');
                
                if(num!=1)
                {
                    $('#information_box').hide();
                    $('#common_friends').hide();
                    $('#status_update_box').hide();
                    $('#other_pictures').hide();
                    $('#workspace').hide();
                    $('#pictures').html('');
                    $('#options').hide();
                    $('#most_popular').hide();
                    $('#profile_information_box').hide();
                    $('#user_videos').hide();
                    $('#likes').hide();
                    $('#docs').hide();
                    $('#account_activity_box').hide();
                    $('#profile_calendar').hide();
                    $('#profile_middle_table').css('width', '730px');
                }
                
                if(num==1)
                {
                    $('#profile_middle_table').css('width', '520px');
                    $('#favorite_posts').hide();
                    $('#user_videos').hide();
                    $('#information_box').show();
                    $('#common_friends').show();
                    $('#status_update_box').css('width', '510px').show();
                    $('#status_update_box').css({'border-collapse': 'seperate', "width": ""});
                        $('#post_form_unit').css({'border-right': "", 'border-bottom': ""});
                        $('#post_sort_unit').css({'border-bottom': ""});
                    $('#update_title').css('width', '510px');
                    $('#other_pictures').css({'width': '510px', 'height': '200px'});
                    display_pictures(1, 1);
                    $('#other_pictures').show();
                    $('#most_popular').hide();
                    $('#profile_calendar').hide();
                    <?php if(isset($_SESSION['id'])) echo "$('#options').show();"; ?>;
                    $('#post_sort').html('');
                        display_posts(1, 'all', 'all', 'none', 1);
                    $('#post_sort_options').hide();
                    $('#profile_information_box').hide();
                    $('#docs').hide();
                    $('#likes').hide();
                    $('#account_activity_box').show();
                    console.log("got here 23");
                }
                else if(num==2)
                {
                    $('#status_update_box').css('width', '730px').show();
                    $('#status_udpate_box').css({'border-collapse': 'collapse', 'width': "100%"});
                        $('#post_form_unit').css({'border-right': "1px solid gray", 'border-bottom': "1px solid gray"});
                        $('#post_sort_unit').css({'border-bottom': "1px solid gray"});
                    $('#update_title').css('width', '730px');
                    display_post_sort('all');
                    $('#post_sort_options').show();
                    initialize_post_search();
                }
                else if(num==3)
                {
                    display_pictures(2, 1);
                    $('#other_pictures').css({'width': '730px', 'height': 'auto'}).show();
                }
                else if(num==4)
                {
                    $('#user_videos').show();
                    display_videos(1);
                }
                //information_box
                else if(num==5)
                {
                    $('#profile_information_box').show();
                    <?php if($display_non_friends[0]=='yes'||(isset($_SESSION['id'])&&$ID==$_SESSION['id'])||$user_is_friends=='true') echo "display_extended_information();"; else echo "$('#profile_information_box').html(\"<p class='locked' id='information_lock'>Information is locked</p>\");"; ?>
                    change_color();
                }
//                else if(num==6)
//                {
//                    display_most_popular();
//                    $('#most_popular').show();
//                }
//                else if(num==7)
//                {
//                    $('#likes').html('');
//                    display_likes();
//                    $('#likes').show();
//                }
//                else if(num==8)
//                {
//                    $('#docs').html('').show();
//                    display_documents();
//                }
                else if(num==9)
                {
                    $('#profile_calendar').show();
                    display_calendar_information(<?php echo $year; ?>, '<?php echo $month; ?>');
                }
                change_color();
            }
            function display_calendar_information(year, month)
            {
                $.post('calendar_information_query.php',
                {
                    num:1,
                    year: year,
                    month: month,
                    user_id: <?php echo $ID; ?>
                }, function(output)
                {
                    var data=output.data;
                    var start_day=output.start_day;
                    var month_length=output.month_length;
                    var prev_month=output.prev_month;
                    var next_month=output.next_month;
                    
                    //resets HTML
                    $('#calendar_table').html("<tr class='menu_week'><th class='days_of_week' id='sunday_title'>Sunday</th><th class='days_of_week' id='monday_title'>Monday</th><th class='days_of_week' id='tuesday_title'>Tuesday</th><th class='days_of_week' id='wednesday_title'>Wednesday</th><th class='days_of_week' id='thursday_title'>Thursday</th><th class='days_of_week' id='friday_title'>Friday</th><th class='days_of_week' id='saturday_title'>Saturday</th></tr><tr class='weeks'><td id='day_0'></td><td id='day_1'></td><td id='day_2'></td><td id='day_3'></td><td id='day_4'></td><td id='day_5'></td><td id='day_6'></td></tr><tr class='weeks'><td id='day_7'></td><td id='day_8'></td><td id='day_9'></td><td id='day_10'></td><td id='day_11'></td><td id='day_12'></td><td id='day_13'></td></tr><tr class='weeks'><td id='day_14'></td><td id='day_15'></td><td id='day_16'></td><td id='day_17'></td><td id='day_18'></td><td id='day_19'></td><td id='day_20'></td></tr><tr class='weeks'><td id='day_21'></td><td id='day_22'></td><td id='day_23'></td><td id='day_24'></td><td id='day_25'></td><td id='day_26'></td><td id='day_27'></td></tr><tr class='weeks'><td id='day_28'></td><td id='day_29'></td><td id='day_30'></td><td id='day_31'></td><td id='day_32'></td><td id='day_33'></td><td id='day_34'></td></tr><tr class='weeks'><td id='day_35'></td><td id='day_36'></td><td id='day_37'></td><td id='day_38'></td><td id='day_39'></td><td id='day_40'></td><td id='day_41'></td></tr>");
                    
                    //displays days
                    for(var x = 0; x < month_length; x++)
                    {
                        
                        var index=start_day+x;
                        var day=x+1;
                        
                        
                        //creates cells of tables for days
                        $('#day_'+index).html("<table style='width:100%;height:100%;'><tbody><tr class='day_top_row'><td><p class='calendar_day_text' style='width:0px;padding:0px;' >"+day+"</p></td><td style='text-align:right;width:0px;padding:0px;'></td></tr><tr class='day_body_row'><td id='day_body_"+index+"' class='day_body' colspan='2'></td></tr></tbody></table>").addClass('calendar_day');

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
                                $('#day_'+index+'_data_table').html($('#day_'+index+'_data_table').html()+"<tr><td><p class='calendar_body_text'>"+data[has_data][3][y]+"</p></td></tr><tr><td><hr style='margin:0px'></td></tr>");
                        }
                    }
                    
                    $('#year_left_arrow').attr('onClick', "{display_calendar_information("+(year-1)+", '"+month+"');$('#calendar_year_text').html('"+(year-1)+"');}");
                    $('#year_right_arrow').attr('onClick', "{display_calendar_information("+(year+1)+", '"+month+"');$('#calendar_year_text').html('"+(year+1)+"');}");
                    
                    $('#month_left_arrow').attr('onClick', "{display_calendar_information("+year+", '"+prev_month+"');$('#calendar_month_text').html('"+prev_month+"');}");
                    $('#month_right_arrow').attr('onClick', "{display_calendar_information("+year+", '"+next_month+"');$('#calendar_month_text').html('"+next_month+"');}");
                    
                    
                    //hides the edit button
                    $('.edit_button').hide();


                    change_color();
                    //hides the loading gif after page is done loading
                    $('#loading_gif_body').hide();
                }, "json");
            }
            function display_activity(page)
            {
                if(<?php if((isset($_SESSION['id'])&&$ID==$_SESSION['id'])||$display_non_friends[7]=='yes'||$user_is_friends=='true') echo "true"; else echo "false"; ?>==true)
                {
                    var timezone=get_timezone();
                    $.post('profile_query.php',
                    {
                        num: 11,
                        user_id: <?php echo $ID; ?>,
                        page: page,
                        timezone: timezone
                    }, function(output)
                    {
                        var type=output.type;
                        var size=output.size;
                        var empty=output.empty;
                        var total_size=output.total_size;
                        var name=output.name;
                        var timestamps=output.timestamps;
                        var timestamp_seconds=output.timestamp_seconds;
                        var others=output.other;
                        var others_names=output.other_names;
                        var profile_picture=output.profile_picture
                        var profile_pictures_other=output.profile_pictures_other;

                        if(page==1)
                        {
                            $('#account_activity').html('');
                            for(var x = 1; x <= (total_size/15)+1; x++)
                                $('#account_activity').html($('#account_activity').html()+"<div class='home_page_page' id='activity_page_"+x+"'></div>");

                            if(total_size<15)
                                $('#account_activity').html("<div class='home_page_page' id='activity_page_1'></div>");

                            $('#account_activity').html($('#account_activity').html()+"<div id='activity_see_more_body'></div>");
                        }
                        if(size!=0)
                        {
                            for(var x = 0; x < size; x++)
                            {
                                var user_name="<div class='activity_user_name_body'><a href='http://www.redlay.com/profile.php?user_id=<?php echo $ID; ?>' style='text-decoration:none'><p class='user_name_activity profile_activity_text' id='user_name_"+x+"' onmouseover=name_over(this); onmouseout=name_out(this); >"+name+"</p></a></div>";
                                var picture="<div class='status_update' id='home_posts_"+page+"_"+x+"'><a href='http://www.redlay.com/profile.php?user_id=<?php echo $ID; ?>'><img class='profile_picture profile_picture_activity' src='"+profile_picture+"' id='home_profile_picture_"+x+"' /></a>";
                                var timestamp="<p class='timestamp_status_update' id='activity_timestamp_"+x+"'>"+timestamps[x]+"</p>";
                                var post_break="<hr class='break'/></div>";


                                if(type[x]=='add')
                                    var body="<table class='added_table_activity'><tbody><tr><td class='home_other_text_unit'><span class='profile_activity_text'>added </span></td><td><a href='http://www.redlay.com/profile.php?user_id="+others[x]+"'><img class='profile_picture added_profile_picture' src='"+profile_pictures_other[x]+"' id='activity_profile_picture_"+x+"' /></a></td></tr></tbody></table>";
                                else if(type[x]=='video')
                                    var body="<table class='added_table_activity'><tbody><tr><td class='home_other_text_unit'><span class='profile_activity_text'>shared a</span></td><td class='home_other_text_unit'><span style='cursor:pointer;' id='display_video_"+x+"' class='profile_activity_text_video' onmouseover=name_over(this); onmouseout=name_out(this); >video</span></td></tr><tr><td colspan='2'><img id='display_video_preview_"+x+"' class='activity_video_preview' src='"+others[x][1]+"' /></td></tr></tbody></table>";
                                else if(type[x]=='page_like')
                                    var body="<table class='added_table_activity'><tbody><tr><td class='home_other_text_unit'><span class='profile_activity_text'>liked </span></td><td><a href='http://www.redlay.com/page.php?page_id="+others[x]+"'><img class='profile_picture added_profile_picture' src='"+profile_pictures_other[x]+"' id='activity_profile_picture_"+x+"' /></a></td></tr></tbody></table>";
                                else if(type[x]=='relationship')
                                    var body="<table class='added_table_activity'><tbody><tr><td class='home_other_text_unit'><span class='profile_activity_text'>is now "+others[x]+"</span></td></tr></tbody></table>";
                                else if(type[x]=='mood')
                                    var body="<table class='added_table_activity'><tbody><tr><td class='home_other_text_unit'><span class='profile_activity_text'>feels "+others[x]+"</span></td></tr></tbody></table>";
                                else if(type[x]=='redlay_gold')
                                    var body="<table class='added_table_activity'><tbody><tr><td class='home_other_text_unit'><span class='profile_activity_text'>bought </span><input type='button' value='redlay gold!' onClick=window.open('http://www.redlay.com/redlay_gold.php'); class='button red_button'  /></td></tr></tbody></table>";

                                $('#activity_page_'+page).html($('#activity_page_'+page).html()+picture+user_name+body+timestamp+post_break);
                                count_time(timestamp_seconds[x], "#activity_timestamp_"+x);
                                    if(type[x]=='add'||type[x]=='page_like')
                                        $('#activity_profile_picture_'+x).attr({'onmouseover': "display_title(this, '"+others_names[x]+"');", 'onmouseout': "hide_title(this);"});
                                    $('.user_name_activity').css('width', '');
                            }
                            for(var x = 0; x < size; x++)
                            {
                               if(type[x]=='video')
                               {
                                  $('#display_video_'+x).attr('onClick', "display_video_activity("+x+");").css('cursor', 'pointer').data('video_url', others[x][0]);
                                  $('#display_video_preview_'+x).attr('onClick', "display_video_activity("+x+");").css('cursor', 'pointer').data('video_url', others[x][0]);;
                               }
                            }
                        }
                        else
                            $('#account_activity').html("<?php if(isset($_SESSION['id'])&&$ID==$_SESSION['id']) echo "You have"; else echo $name." has"; ?> not done anything yet");
                            
                        if(empty==false&&page==1)
                        {
                            $('#activity_see_more_body').html($('#activity_see_more_body').html()+"<input class='see_more_posts button' id='activity_see_more_button' value='See More' type='button'>");
                            $('#activity_see_more_button').attr({'onmouseover': "{display_title(this, 'See more posts');}", 'onmouseout': "{hide_title(this);}", 'onClick': "display_activity("+(page+1)+");"});
                        }
                        else if(empty==true)
                            $('#activity_see_more_button').hide();
                        else
                            $('#activity_see_more_button').attr('onClick', "display_activity("+(page+1)+");");

                        change_color();
                    }, "json");
                }
                else
                    $('#account_activity_box').html("<p class='locked'>Activity is locked</p>");
            }
            function display_video_activity(index)
            {
               var video_url=$('#display_video_'+index).data('video_url');
               
               var title="";
               var body=video_url;
               var extra_id="";
               var load_id="display_video_load_gif";
               var confirm="";
               display_alert(title, body, extra_id, load_id, confirm)
               $('#display_video_load_gif').hide();
               $('#current_video').attr('width', '460');
               $('#current_video').attr('height', '260');
            }
            
            function display_videos(page)
            {
                if(<?php if((isset($_SESSION['id'])&&$ID==$_SESSION['id'])||$display_non_friends[4]=='yes'||$user_is_friends=='true') echo "true"; else echo "false"; ?>==true)
                {
                    <?php if(!isset($_SESSION['id'])||(isset($_SESSION['id'])&&$ID!=$_SESSION['id'])) echo "$('#add_video_form').html('');"; ?>
                    $('#video_load').show();
                    $.post('profile_query.php',
                    {
                        num:2,
                        page: page,
                        user_id: <?php echo $ID; ?>
                    }, function(output)
                    {
                        var video_ids=output.video_ids;
                        var video_types=output.video_types;
                        var video_embeds=output.video_embeds;
                        var video_previews=output.video_previews;
                        var video_timestamps=output.video_timestamps;
                        var empty=output.empty;
                        var size=output.size;
                        var total_size=output.total_size;

//                        $('#delete_video').attr('onClick', 'delete_videos('+total_size+')');
                        if(video_ids[0]!='')
                        {
                            if(page==1)
                            {
                                $('#videos').html("");
                                if(total_size>=10)
                                {
                                    for(var x = 1; x < total_size/10+1; x++)
                                        $('#videos').html($('#videos').html()+"<div id='video_page_"+x+"'><table id='videos_table'><tbody id='video_page_body_"+x+"'></tbody></table></div>");
                                }
                                else
                                    $('#videos').html("<div id='video_page_1'><table><tbody id='video_page_body_1'></tbody></table></div>");
                                $('#videos').html($('#videos').html()+"<input class='see_more_posts see_more_videos button' value='See More' type='button' id='see_more_videos'>");
                                $('#see_more_videos').attr({'onmouseover': "display_title(this, 'Display more videos');", 'onmouseout': "hide_title(this);", 'onClick': "display_videos("+(page+1)+");"});
                            }

                            for(var x = 0; x < video_ids.length; x++)
                            {
                                if(video_ids[x]!='')
                                {
                                    if(video_previews[x]=='')
                                        $('#video_page_body_'+page).html($('#video_page_body_'+page).html()+"<tr><td><table style='position:relative;'><tbody><tr class='video_row' id='video_row_"+page+"_"+x+"'><td id='video_unit_"+page+"_"+x+"_1'>"+video_embeds[x]+"</td><td id='video_unit_"+page+"_"+x+"_2'></td></tr></tbody></table></td></tr>");
                                    else
                                        $('#video_page_body_'+page).html($('#video_page_body_'+page).html()+"<tr><td><table style='position:relative;'><tbody><tr class='video_row' id='video_row_"+page+"_"+x+"'><td id='video_unit_"+page+"_"+x+"_1'>   <img class='video_preview' src='"+video_previews[x]+"' id='video_preview_"+page+"_"+x+"' /> <img class='video_play_button' id='video_play_button_"+page+"_"+x+"' src='http://pics.redlay.com/pictures/play_button.png' />   </td><td id='video_unit_"+page+"_"+x+"_2'></td></tr></tbody></table></td></tr>");

                                    $('#video_unit_'+page+'_'+x+'_1').attr('onClick', "display_actual_video('#video_unit_"+page+"_"+x+"_1');");
                                    $('#video_preview_'+page+'_'+x).attr({'onmouseover': "video_over('#video_preview_"+page+"_"+x+"', '#video_play_button_"+page+"_"+x+"');",  'onmouseout': "video_out('#video_preview_"+page+"_"+x+"', '#video_play_button_"+page+"_"+x+"');"});
                                    $('#video_play_button_'+page+'_'+x).attr('onmouseover', "video_over('#video_preview_"+page+"_"+x+"', '#video_play_button_"+page+"_"+x+"');");

                                    $('#video_unit_'+page+'_'+x+'_2').html("<table><tbody><tr><td id='share_video_unit_"+page+"_"+x+"'>    </td></tr><tr><td id='delete_video_unit_"+page+"_"+x+"'>    </td></tr><tr></tr></tbody></table>");

                                    if(<?php if((isset($_SESSION['id'])&&$ID!=$_SESSION['id'])) echo "true"; else echo "false"; ?>==true)
                                        $('#share_video_unit_'+page+'_'+x).html("<input class='button red_button' type='button' value='Share' onClick='share_video("+video_ids[x]+", <?php if(isset($_SESSION['id'])) echo $_SESSION['id']; else echo "0"; ?>);'/>");
                                    else if(<?php if(isset($_SESSION['id'])) echo "true"; else echo "false"; ?>==true)
                                        $('#delete_video_unit_'+page+'_'+x).html("<input class='button red_button' type='button' value='Delete' onClick='delete_video("+video_ids[x]+");' />");
                                    else
                                        $('#delete_video_unit_'+page+'_'+x).html("");
                                }
                            }
                            
                            for(var x = 0; x < video_ids.length; x++)
                                $('#video_unit_'+page+'_'+x+'_1').data('vid_embed', video_embeds[x]);

                            if(empty)
                                $('#see_more_videos').attr('onClick', '').hide();
                            else
                                $('#see_more_videos').attr('onClick', 'display_videos('+(page+1)+');');

                            initialize_video_input();
                        }
                        else
                            $('#videos').html("<p style='color:<?php echo $text_color; ?>;text-align:center;'><?php if(isset($_SESSION['id'])&&$ID==$_SESSION['id']) echo "You have"; else echo $name." has"; ?> not shared videos yet</p>");
                        
                        $('#video_load').hide();
                    }, "json");
                }
                else
                    $('#user_videos').html("<p class='locked'>Videos locked</p>");
            }
            function toggle_video_checkbox(num)
            {
                if($('#video_checkbox_'+num).attr('src')=='http://pics.redlay.com/pictures/gray_checkbox.png')
                    $('#video_checkbox_'+num).attr('src', 'http://pics.redlay.com/pictures/gray_checkbox_checked.png');
                else
                    $('#video_checkbox_'+num).attr('src', 'http://pics.redlay.com/pictures/gray_checkbox.png');
            }
//            function like_page(page_id, index)
//            {
//                $.post('like_page.php',
//                {
//                    page_id: page_id
//                }, function (output)
//                {
//                    $('#status_errors').html(output);
//                    $('#page_like_button_'+index).attr({'value':'Unlike', 'onClick': "unlike_page("+page_id+", "+index+");", 'id': 'page_unlike_button_'+index}).attr('class', 'page_like_button red_button');
//                });
//            }
//            function unlike_page(page_id, index)
//            {
//                $.post('unlike_page.php',
//                {
//                    page_id: page_id
//                }, function (output)
//                {
//                    $('#status_errors').html(output);
//                    $('#page_unlike_button_'+index).attr({'value':'Like', 'onClick': "like_page("+page_id+", "+index+");", 'id': 'page_like_button_'+index}).attr('class', 'page_like_button green_button');
//                });
//            }
//            function display_most_popular()
//            {
//                $.post('profile_query.php',
//                {
//                    num:3,
//                    user_id: <?php echo $ID; ?>
//                }, function(output)
//                {
//                    $('#most_popular').html('');
//                    var picture_id=output.picture_id;
//                    var picture_total=output.picture_total;
//                    var post_total=output.post_total;
//
//                    if(<?php if($display_non_friends[3]=='yes'||(isset($_SESSION['id'])&&$ID==$_SESSION['id'])||$user_is_friends=='true') echo "true"; else echo "false"; ?>==true)
//                    {
//                        //displays most popular photo
//                        var picture="<div id='most_popular_picture' class='outside_picture'><a href='http://www.redlay.com/view_photo.php?user_id=<?php echo $ID; ?>&&picture_id="+picture_id+"&&type=user' ><img id='most_popular_picture_picture' src='users/<?php echo $ID; ?>/thumbs/"+picture_id+".jpg' class='picture_post' /></a></div>";
//                        var picture_likes="<p id='popular_picture_likes' class='popular_picture_text'><span class='popular_text'>Likes:</span> "+picture_total[0]+"</p>";
//                        var picture_dislikes="<p id='popular_picture_dislikes' class='popular_picture_text'><span class='popular_text'>Dislikes:</span> "+picture_total[1]+"</p>";
//                        var picture_comments="<p id='popular_picture_comments' class='popular_picture_text'><span class='popular_text'>Comments:</span> "+picture_total[2]+"</p></div>";
//                        var picture_description="<div id='most_popular_picture_information'><p id='popular_picture_description' class='popular_picture_text'>"+picture_total[3]+"</p><hr />";
//                        var picture_timestamp="<p id='popular_picture_timestamp' class='popular_picture_text'>"+picture_total[4]+"</p><hr />";
//                        $('#most_popular').html(picture+picture_description+picture_timestamp+picture_likes+picture_dislikes+picture_comments);
//                        $('#most_popular_picture_picture').attr({'onmouseover': "$('#most_popular_picture').css('background-color', 'lightgray');", 'onmouseout': "$('#most_popular_picture').css('background-color', 'white');"});
//                    }
//                    else
//                        $('#most_popular').html("<p class='locked'>Photos are locked</p>");
//
//                    if(<?php if($display_non_friends[2]=='yes'||(isset($_SESSION['id'])&&$ID==$_SESSION['id'])||$user_is_friends=='true') echo "true"; else echo "false"; ?>==true)
//                    {
//                        //displays most popular post
//                        var post_picture="<div id='most_popular_post'><div id='most_popular_post_post' class='status_update' ><a href='http://www.redlay.com/profile.php?user_id=<?php echo $ID; ?>' ><img class='profile_picture_status profile_picture' src='users/<?php echo $ID; ?>/thumbs/0.jpg' id='most_popular_post_picture' onmouseover=display_title(this, '"+post_total[6]+"'); onmouseout=hide_title(this); /></a>";
//                        var post_name="<div class='user_name_body'><a class='user_name_link' href='http://www.redlay.com/profile.php?user_id=<?php echo $ID; ?>' ><p class='user_name' id='most_popular_post_name' onmouseover=name_over(this); onmouseout=name_out(this); >"+post_total[6]+"</p></a></div>";
//                        var post_body="<p class='status_update_text' id='most_popular_post_body' >"+post_total[5]+"</p></div>";
//
//                        var post_timestamp="<div id='most_popular_post_information'><p class='timestamp_status_update' id='most_popular_post_timestamp' >"+post_total[4]+"</p><hr />";
//                        var post_likes="<p id='popular_post_likes' class='popular_post_text'><span class='popular_text'>Likes:</span> <span class='most_popular_text' >"+post_total[1]+"</span></p>";
//                        var post_dislikes="<p id='popular_post_dislikes' class='popular_post_text'><span class='popular_text'>Dislikes:</span> <span class='most_popular_text' >"+post_total[2]+"</span></p>";
//                        var post_comments="<p id='popular_post_comments' class='popular_post_text'><span class='popular_text'>Comments:</span> <span class='most_popular_text' >"+post_total[3]+"</span></p></div></div>";
//
//                        $('#most_popular').html($('#most_popular').html()+post_picture+post_name+post_body+post_timestamp+post_likes+post_dislikes+post_comments);
//                        $('#most_popular_post_post').attr({'onClick': "window.location.replace('http://www.redlay.com/view_post.php?post_id="+post_total[0]+"&&profile_id=<?php echo $ID; ?>');"});
//                    }
//                    else
//                        $('#most_popular').html($('#most_popular').html()+"<p class='locked'>Posts are locked</p>");
//                    change_color();
//                }, "json");
//            }
            function display_post_sort(year)
            {
                if(<?php if($display_non_friends[2]=='yes'||(isset($_SESSION['id'])&&$ID==$_SESSION['id'])||$user_is_friends=='true') echo "true"; else echo "false"; ?>==true)
                {
                    var timezone=get_timezone();
                    $.post('profile_query.php',
                    {
                        num:13,
                        user_id: <?php echo $ID; ?>,
                        timezone:timezone
                    }, function(output)
                    {
                        var years=output.years;
                        var months=output.months;
                        
                        if(year=='all')
                        {
                            $('#post_sort_years').html("<option value='all'>Year: </option>");
                            $('#post_sort_months').html("<option value='all'>Month: </option>");
                            for(var x = 0; x < years.length; x++)
                                $('#post_sort_years').html($('#post_sort_years').html()+"<option value='"+years[x]+"'>"+years[x]+"</option>");
                        }
                        else
                        {
                            var index=-1;
                            for(var x = 0; x < years.length; x++)
                            {
                                if(years[x]==year)
                                    index=x;
                            }
                            
                            if(index!=-1)
                            {
                                for(var y = 0; y < months[index].length; y++)
                                    $('#post_sort_months').html($('#post_sort_months').html()+"<option value='"+months[index][y]+"'>"+months[index][y]+"</option>");
                            }
                            
                        }
                            
                        
                        
                        
                    }, "json");
                }
            }
            function search_posts()
            {
                
            }
            function delete_video(video)
            {
                //gets the checked checkboxes and their values
//                var videos=new Array();
//                var num=0;
//                total_size--;
//                while($('#video_checkbox_'+total_size).length)
//                {
//                    if($('#video_checkbox_'+total_size).attr('src')=='http://pics.redlay.com/pictures/gray_checkbox_checked.png')
//                    {
//                        videos[num]=total_size;
//                        num++;
//                    }
//                    total_size--;
//                }
                
                $.post('remove_video.php',
                {
                    video: video
                }, function (output)
                {
                    if(output=='Video Deleted!')
                        display_videos(1);
                    else
                        display_error(output, 'bad_errors');
                });
            }
            $(window).ready(function()
            {
                $('#unFriend_button').hide();
                $('#add_friend_button').hide();
                $('#pending_friend_request_button').hide();
                if(<?php echo $ID; ?>== <?php if(isset($_SESSION['page_id']))echo '0'; else if(isset($_SESSION['id'])) echo $_SESSION['id']; else echo "0"; ?>)
                    $('#options_table').html("<tr class='buttons_row' id='options_row_1'><td class='buttons_unit'><input type='button' class='options_button button green_button' id='photo_button' value='Upload Photo' onClick=display_photo_upload_box(<?php if(isset($_SESSION['id'])&&has_redlay_gold($_SESSION['id'], "photo_quality")) echo "true"; else echo "false"; ?>); /></td></tr>");

                else if(<?php if(isset($_SESSION['page_id'])) echo 'false'; else echo $user_is_friends; ?>==true)
                {
                    $('#options_table').html("<tr class='buttons_row' id='options_row_1'><td class='buttons_unit'><input type='button' class='options_button button green_button' id='message_button' value='Message' onClick='display_message_box(<?php echo $ID; ?>);' /></td></tr>");
                    $('#options_table').html($('#options_table').html()+"<tr class='buttons_row' id='options_row_2'><td class='buttons_unit'><input type='button' class='options_button button green_button' value='Group Options' onClick='show_group_options();'  /></td></tr>");
                    $('#options_table').html($('#options_table').html()+"<tr class='buttons_row' id='options_row_3'><td class='buttons_unit'><input type='button' class='options_button button green_button' id='delete_button' value='Delete' onClick='delete_user();' /></td></tr>");
                }
                else if(<?php if(isset($_SESSION['page_id']))echo 'false'; else if(isset($_SESSION['id'])&&pending_request($ID, $_SESSION['id'])) echo 'true'; else echo 'false'; ?> ==true)
                    $('#options_table').html("<tr class='buttons_row' id='options_row_1'><td class='buttons_unit'><input type='button' class='options_button button green_button_disabled' id='pending_friend_request_button' value='Pending Request' /></td></tr>");
                else if(<?php if(isset($_SESSION['page_id'])) echo "true"; else echo "false"; ?>==true)
                    $('#options_table').html("<tr class='buttons_row' id='options_row_1'><td class='buttons_unit'><input type='button' value='Block' class='options_button button green_button' id='block_button' onClick=block_user(); /></td></tr>");

                else
                {
                    <?php if($general[0]=='yes'||(isset($_SESSION['id'])&&$ID==$_SESSION['id'])) echo "$('#options_table').html('<tr class=\'buttons_row\' id=\'options_row_1\'><td class=\'buttons_unit\'><input type=\'button\' class=\'options_button button green_button\' id=\'add_friend_button\' value=\'Add\' onClick=\'{display_add_menu($ID);}\'/></td></tr>');"; ?>
                    $('#options_table').html($('#options_table').html()+"<tr class='buttons_row' id='options_row_2'><td class='buttons_unit'><input type='button' class='options_button button green_button' id='block_button' value='Block' onClick='block_user();'/></td></tr>");
                }
                change_color();
            });
            function save_user_groups()
            {
                //gets the checked checkboxes and their values
                var groups_list=new Array();
                var num=0;
                var num2=0;
                while($('#audience_options_box_checkbox_'+num2).length)
                {
                    if($('#audience_options_box_checkbox_'+num2).data('checked')=='yes')
                    {
                        groups_list[num]=$('#audience_options_box_checkbox_'+num2).data('group_name');
                        num++;
                    }
                    num2++;
                }
                

                $.post('profile_query.php',
                {
                    num:7,
                    user_id: <?php echo $ID; ?>,
                    groups:groups_list
                }, function(output)
                {
                    if(output=="User groups modified")
                    {
                        display_error(output, 'good_errors');
                        close_alert_box();
                    }
                    else
                        display_error(output, 'bad_errors');
                });
            }

            function show_group_options()
            {
                var body="<table class='alert_box_body_table'><tbody><tr><td><hr class='alert_box_line'/></td></tr><tr><td><div class='select_box' id='audience_options_box'></div></td></tr><tr><td><hr class='alert_box_line'/></td></tr></tbody></table>";
                var confirm="<input type='button' class='button red_button' id='change_groups' onClick=save_user_groups(); value='Save' />";
                display_alert("Groups user is in", body, 'groups_extra_unit', 'groups_gif', confirm);
                display_current_groups('audience_options_box', <?php echo $ID; ?>);


                $('#groups_gif').hide();
                change_color();
            }



            function display_group_options()
            {
                if($('#audience_category_table').length)
                {
                    //hides or shows
                    if($('#alert_box_row_5').css('display')=='table-row')
                    {
                        $('#alert_box_row_5').hide();
                        $('#alert_box_row_4').hide();
                    }
                    else
                    {
                        $('#alert_box_row_5').show();
                        $('#alert_box_row_4').show();
                    }
                }
                else
                {
                    $('#alert_box_row_4').html("<td class='alert_box_unit' colspan='3'><hr /></td>");
                    $('#alert_box_row_5').html("<td class='alert_box_unit' colspan='3'><table id='audience_category_table' ></table></td>");


                    var audience_list=new Array();
                    <?php
                    if(isset($_SESSION['id']))
                    {
                        $query=mysql_query("SELECT audience_defaults FROM public WHERE num=1 LIMIT 1");
                        $query2=mysql_query("SELECT audience_group_lists FROM user_data WHERE user_id=$_SESSION[id] LIMIT 1");
                        if($query&&mysql_num_rows($query)==1&&$query2&&mysql_num_rows($query2)==1)
                        {
                            $array=mysql_fetch_row($query);
                            $array2=mysql_fetch_row($query2);
                            $audience_list=explode('|^|*|', $array2[0]);
                            $audience_defaults=explode('|^|*|', $array[0]);

                            $num=0;
                            for($x = 0; $x < sizeof($audience_defaults); $x++)
                            {
                                echo "audience_list[$num]='$audience_defaults[$x]';";
                                $num++;
                            }
                            if($audience_list[0]!='')
                            {
                                for($x =0; $x < sizeof($audience_list); $x++)
                                {
                                    echo "audience_list[$num]='$audience_list[$x]';";
                                    $num++;
                                }
                            }
                        }
                    }
                    ?>
                    //adds rows to table
                    if(audience_list.length>=3)
                    {
                        for(var x = 0; x < (audience_list.length/3); x++)
                            $('#audience_category_table').html($('#audience_category_table').html()+"<tr class='audience_category_row' id='audience_category_row_"+x+"'></tr>");
                    }
                    else
                        $('#audience_category_table').html("<tr class='audience_category_row' id='audience_category_row_0'></tr>");

                    //adds actual list items
                    var num=0;
                    for(var x =0; x < audience_list.length; x++)
                    {
                        $('#audience_category_row_'+num).html($('#audience_category_row_'+num).html()+"<td id='option_"+x+"'><input type='checkbox' id='checkbox_"+x+"'/><span class='audience_option' id='checkbox_name_"+x+"' >"+audience_list[x]+"</span></td>");
                        if(x%3==0&&x!=0)
                            num++;
                    }
                    for(var x =0; x < audience_list.length; x++)
                        $('#checkbox_'+x).data("audience_name", audience_list[x]);
                }
            }
            
            function block_user()
            {
                $.post('block_user.php',
                {
                    user_id: <?php echo $ID; ?>
                }, function (output)
                {
                    if(output=='User blocked')
                        display_error(output, 'good_errors');
                    else
                        display_error(output, 'bad_errors');
                });
            }
            function delete_user()
            {
                $.post('unfriend.php',
                {
                    user_id: <?php echo $ID; ?>
                }, function (output)
                {
                    window.location.replace(window.location);
                });
            }
            function display_adds()
            {
                if(<?php if($display_non_friends[1]=='yes'||(isset($_SESSION['id'])&&$ID==$_SESSION['id'])||$user_is_friends=='true') echo "true"; else echo "false"; ?>==true)
                {
                    $.post('profile_query.php',
                    {
                        num: 12,
                        user_id: <?php echo $ID; ?>
                    }, function(output)
                    {
                        var adds=output.adds;
                        var profile_pictures=output.profile_pictures;
                        var names=output.names;
                        var num_adds=output.num_adds;


                        if(adds[0]!='')
                        {
                            for(var x = 0; x < adds.length; x++)
                            {
                                $('#friends_main_body').html($("#friends_main_body").html()+"<tr class='add_row' id='add_row_"+x+"'></tr>");

                                var image="<div class='friends' id='friend_"+x+"'><a class='friend_profile_picture_link' href='http://www.redlay.com/profile.php?user_id="+adds[x]+"'><img class='friend_profile_picture profile_picture' src='"+profile_pictures[x]+"' id='friend_profile_picture_"+x+"' /></a>";
                                var name="<a class='friend_name_link' href='http://www.redlay.com/profile.php?user_id="+adds[x]+"'><p class='friend_name' id='friend_name_"+x+"' onmouseover=name_over(this); onmouseout=name_out(this);>"+names[x]+"</p></a>";
                                var number_of_adds="<p class='adds_num_adds text_color'>Adds: "+num_adds[x]+"</p>";


                                $('#add_row_'+x).html("<td class='add_profile_picture_unit'>"+image+"</td><td style='vertical-align:top;'><div class='add_information_body'>"+name+number_of_adds+"</div></td>");


                                $('#friend_profile_picture_'+x).attr({'onmouseover': "display_title(this, '"+names[x]+"');", 'onmouseout': "hide_title(this);"});
                            }
                        }
                        else
                            $('#friends_main').html("<p style='color:<?php echo $text_color; ?>'><?php if(isset($_SESSION['id'])&&$ID==$_SESSION['id']) echo "You have"; else echo $name." has"; ?> not added anyone yet</p>");

                    },"json");
                }
                else
                    $('#user_friends_box').html("<p class='locked' id='friends_locked'>Adds are locked</p>");
            }

           function favorite_post(id, num)
           {
               var update=$('#status_update_text_'+num).html();
                $.post('favorite_post.php',
                {
                    user_id: id,
                    number: num,
                    post: update,
                    profile: <?php echo $ID; ?>
                }, function(output)
                {
                    $('#errors').html(update);
                });
           }
           function change_audience_options(post_id)
           {
               //gets the checked checkboxes and their values
                var audience_options_list=new Array();
                var num=0;
                var num2=0;
                while($('#post_groups_box_checkbox_'+num2).length)
                {
                    if($('#post_groups_box_checkbox_'+num2).data('checked')=='yes')
                    {
                        audience_options_list[num]=$('#post_groups_box_checkbox_'+num2).data('group_name');
                        num++;
                    }
                    num2++;
                }

                $.post('view_post_query.php',
                {
                    num:2,
                    post_id: post_id,
                    groups: audience_options_list
                }, function(output)
                {
                    if(output=="Audience changed")
                        display_error(output, 'good_errors');
                    else
                        display_error(output, 'bad_errors');
                });
           }
           function display_posts(page, year, month, phrase, sort)
            {
                if(<?php if($display_non_friends[2]=='yes'||(isset($_SESSION['id'])&&$ID==$_SESSION['id'])||$user_is_friends=='true') echo "true"; else echo "false"; ?>==true)
                {
                    var date = new Date()
                    var timezone = date.getTimezoneOffset();
                    $.post('profile_query.php',
                    {
                        num:4,
                        page: page,
                        month:month,
                        year:year,
                        phrase: phrase,
                        user_id: <?php echo $ID; ?>,
                        timezone: timezone,
                        sort:sort
                    }, function (output)
                    {
                        var names=output.names;
                        var users_sent=output.users_sent;
                        var profile_pictures=output.profile_pictures
                        var posts=output.posts;
                        var num_comments=output.num_comments;
                        var post_ids=output.post_ids;
                        var empty=output.empty;
                        var size=output.size;
                        var total_size=output.total_size;
                        var timestamps=output.timestamps;
                        var timestamp_seconds=output.timestamp_seconds;
                        var badges=output.badges;
                        
                        var has_liked=output.has_liked;
                        var has_disliked=output.has_disliked;
                        var num_likes=output.num_likes;
                        var num_dislikes=output.num_dislikes;
                        
                        var comment_ids=output.comment_ids;
                        var comments=output.comments;
                        var comment_timestamps=output.comment_timestamps;
                        var comment_timestamp_seconds=output.comment_timestamp_seconds;
                        var comments_user_sent=output.comments_users_sent;
                        var comment_names=output.comment_names;
                        var comment_profile_pictures=output.comment_profile_pictures;
                        var comment_badges=output.comment_badges;

                        var comment_has_liked=output.has_liked_comments;
                        var comment_has_disliked=output.has_disliked_comments;
                        var comment_num_likes=output.num_comment_likes;
                        var comment_num_dislikes=output.num_comment_dislikes;


                        if(total_size!=0)
                        {
                            //displays the HTML template that will be used to display current and future posts
                            if(page==1)
                            {
                                $('#status_updates').html('');
                                for(var x = 1; x <= (total_size/10)+1; x++)
                                    $('#status_updates').html($('#status_updates').html()+"<div class='profile_post_page' id='page_"+x+"'></div>");

                                if(total_size<10)
                                    $('#status_updates').html("<div class='profile_post_page' id='page_1'></div>");

                                $('#status_updates').html($('#status_updates').html()+"<div id='posts_see_more_body'></div>");
                            }

                            var functionality=new Array();
                            var html="";
                            for(var x = 0; x < posts.length; x++)
                            {
                                if(posts[x]!='')
                                {
                                    posts[x]=text_format(convert_image(posts[x], 'post'));
                                    var profile_picture="<a href='http://www.redlay.com/profile.php?user_id="+users_sent[x]+"' ><img class='profile_picture profile_picture_status' src='"+profile_pictures[x]+"' /></a>";
                                    var name="<div class='user_name_body'><a class='link' href='http://www.redlay.com/profile.php?user_id="+users_sent[x]+"' ><span class='user_name' onmouseover='name_over(this);' onmouseout='name_out(this);'>"+names[x]+"</span></a></div>";
                                    var post="<p class='status_update_text' >"+posts[x]+"</p>";


                                    if(<?php if($general[2]=='yes'||(isset($_SESSION['id'])&&$ID==$_SESSION['id'])||(isset($_SESSION['id'])&&user_is_friends($_SESSION['id'], $ID)=='true')) echo "true"; else echo "false"; ?>==true)
                                        var comment_input="<div id='comment_text_"+page+"_"+x+"' class='comment_input_body'><textarea class='comment_textarea input_box' id='comment_input_"+page+"_"+x+"' placeholder='Comment...' maxlength='500' onFocus='input_in(this);' onBlur='input_out(this);'></textarea></div>";
                                    else
                                        var comment_input="";

                                    var comment_body="<div class='comment_body' id='comment_body_"+page+"_"+x+"'></div>";
                                    var timestamp="<p class='timestamp_status_update' id='post_timestamp_"+post_ids[x]+"'>"+timestamps[x]+"</p>";
                                    if(users_sent[x]==<?php if(isset($_SESSION['id'])) echo $_SESSION['id']; else echo "0"; ?>||<?php if($_SESSION['id']==$ID) echo "true"; else echo "false"; ?>==true)
                                        var options="<div class='post_delete post_hide' id='post_options_"+post_ids[x]+"' onClick='show_post_options("+post_ids[x]+", <?php echo $ID; ?>);'>O</div>";
                                    else
                                        var options="";


                                    if(<?php if(isset($_SESSION['id'])&&($general[2]=="yes"||(isset($_SESSION['id'])&&$ID==$_SESSION['id'])||(isset($_SESSION['id'])&&user_is_friends($_SESSION['id'], $ID)=='true'))) echo "true"; else echo "false"; ?>==true)
                                    {
                                        functionality[x]=new Array();
                                        if(users_sent[x]!=<?php if(isset($_SESSION['id']))echo $_SESSION['id']; else echo "0"; ?>)
                                        {
                                            if(has_liked[x]==true)
                                            {
                                                var like="<div class='left_function' id='post_like_body_"+page+"_"+x+"' ><span class='status_update_like' id='post_like_"+page+"_"+x+"' >Unlike ["+num_likes[x]+"]</span></div>";
                                                functionality[x][0]="unlike";
                                            }
                                            else if(num_likes[x]>=1)
                                            {
                                                var like="<div class='left_function' id='post_like_body_"+page+"_"+x+"' ><span class='status_update_like' id='post_like_"+page+"_"+x+"' >Like ["+num_likes[x]+"]</span></div>";
                                                functionality[x][0]="like";
                                            }
                                            else
                                            {
                                                var like="<div class='left_function' id='post_like_body_"+page+"_"+x+"' ><span class='status_update_like' id='post_like_"+page+"_"+x+"' >Like</span></div>";
                                               functionality[x][0]="like";
                                            }
                                        }
                                        else
                                        {
                                            if(num_likes[x]==1)
                                                var like="<div class='left_function_disabled' ><span class='status_update_like me' >1 like</span></div>";
                                            else if(num_likes[x]>=1)
                                                var like="<div class='left_function_disabled' ><span class='status_update_like me' >"+num_likes[x]+" likes</span></div>";
                                            else
                                                var like="";
                                            functionality[x][0]="none";
                                        }


                                        if(users_sent[x]!=<?php if(isset($_SESSION['id']))echo $_SESSION['id']; else echo "0"; ?>)
                                        {
                                            if(like!="")
                                                var function_class='middle_function';
                                            else
                                                var function_class='left_function';
                                            
                                            if(has_disliked[x]==true)
                                            {
                                                var dislike="<div class='"+function_class+"' id='post_dislike_body_"+page+"_"+x+"' ><span class='status_update_dislike' id='post_dislike_"+page+"_"+x+"' >Undislike ["+num_dislikes[x]+"]</span></div>";
                                                functionality[x][1]="undislike";
                                            }
                                            else if(num_dislikes[x]>=1)
                                            {
                                                var dislike="<div class='"+function_class+"' id='post_dislike_body_"+page+"_"+x+"' ><span class='status_update_dislike' id='post_dislike_"+page+"_"+x+"' >Dislike ["+num_dislikes[x]+"]</span></div>";
                                                functionality[x][1]="dislike";
                                            }
                                            else
                                            {
                                                var dislike="<div class='"+function_class+"' id='post_dislike_body_"+page+"_"+x+"' ><span class='status_update_dislike' id='post_dislike_"+page+"_"+x+"' >Dislike</span></div>";
                                                functionality[x][1]="dislike";
                                            }
                                        }
                                        else
                                        {
                                            if(like!="")
                                                var function_class='middle_function_disabled';
                                            else
                                                var function_class='left_function_disabled';
                                            
                                            if(num_dislikes[x]==1)
                                                var dislike="<div class='"+function_class+"'  id='post_dislike_body_"+page+"_"+x+"'><span class='status_update_dislike me' >1 dislike</span></div>";
                                            else if(num_dislikes[x]>=1)
                                                var dislike="<div class='"+function_class+"'  id='post_dislike_body_"+page+"_"+x+"'><span class='status_update_dislike me' >"+num_dislikes[x]+" dislikes</span></div>";
                                            else
                                                var dislike="";
                                        }
                                    }
                                    else
                                    {
                                        var like="";
                                        var dislike="";
                                    }
                                    
                                    if(like==''&&dislike=='')
                                        var comment_class='single_function';
                                    else
                                        var comment_class='right_function';
                                        
                                    if(num_comments[x]>=1)
                                        var comment_text="<div class='"+comment_class+"' id='comment_title_body_"+page+"_"+x+"'><span id='comment_title_"+page+"_"+x+"' class='comment_text' >Comment ["+num_comments[x]+"]</span></div>";
                                    else
                                        var comment_text="<div class='"+comment_class+"' id='comment_title_body_"+page+"_"+x+"'><span id='comment_title_"+page+"_"+x+"' class='comment_text' >Comment</span></div>";


                                    //styles like, dislike, and comment buttons
                                    var post_functions=get_post_functions(like, dislike, comment_text, timestamp);
                                    var option_id="post_options_"+post_ids[x];


                                    var body=get_post_format(profile_picture, name, post,post_functions, comment_input+comment_body, options, option_id, 'post_'+page+'_'+post_ids[x], badges[x]);
                                    html+=body;

//                                        $('#profile_picture_status_'+post_ids[x]).attr({'onmouseover': "display_title(this, '"+array[x]+"');", 'onmouseout': "hide_title(this);"});
                                }

                            }
                            
                            $('#page_'+page).html(html);

                            //binds data for when user presses enter to post comment
                            for(var x = 0; x < posts.length; x++)
                            {
                                if(posts[x]!='')
                                {
                                    count_time(timestamp_seconds[x], '#post_timestamp_'+post_ids[x]);
                                    $('#status_update_'+post_ids[x]).attr({'onmouseover': "show_close("+post_ids[x]+");", 'onmouseout': "hide_close("+post_ids[x]+");"});
                                    $('#post_options_'+post_ids[x]).attr({'onmouseover': "display_title(this, 'Display this post\'s options');", 'onmouseout': "hide_title(this);"});
                                    
                                    
                                    if(functionality[x][0]=="unlike")
                                        $('#post_like_body_'+page+'_'+x).attr('onClick', "unlike_post(<?php echo $ID; ?>, "+post_ids[x]+", "+users_sent[x]+", "+num_likes[x]+", "+page+", "+x+");");
                                    else if(functionality[x][0]=='like')
                                        $('#post_like_body_'+page+'_'+x).attr('onClick', "like_post(<?php echo $ID; ?>, "+post_ids[x]+", "+users_sent[x]+", "+num_likes[x]+", "+page+", "+x+");");
                                    
                                    if(functionality[x][1]=="undislike")
                                        $('#post_dislike_body_'+page+'_'+x).attr('onClick', "undislike_post(<?php echo $ID; ?>, "+post_ids[x]+", "+users_sent[x]+", "+num_dislikes[x]+", "+page+", "+x+");");
                                    else if(functionality[x][1]=='dislike')
                                        $('#post_dislike_body_'+page+'_'+x).attr('onClick', "dislike_post(<?php echo $ID; ?>, "+post_ids[x]+", "+users_sent[x]+", "+num_dislikes[x]+", "+page+", "+x+");");
                                        
                                    
                                    $('#comment_input_'+page+'_'+x).data({'post_id': post_ids[x], 'index': x, 'poster_id': users_sent[x], 'page': page, 'num_comments': num_comments[x]});

                                    //modifies "Comment" string
                                    $('#comment_title_body_'+page+'_'+x).attr({'onClick': "{show_comment("+page+", "+x+");}"});

                                    //adds number of comments as data
                                    $('#comment_title_'+page+'_'+x).data({'number': num_comments});
                                }
                            }


                            if(empty==false&&page==1)
                            {
                                $('#posts_see_more_body').html($('#posts_see_more_body').html()+"<input class='see_more_posts button' id='posts_see_more_button' value='See More' type='button'>");
                                $('#posts_see_more_button').attr({'onmouseover': "{display_title(this, 'See more posts');}", 'onmouseout': "{hide_title(this);}", 'onClick': "display_posts("+(page+1)+", '"+year+"', '"+month+"', '"+phrase+"', "+sort+");"});
                            }
                            else if(empty==true)
                                $('#posts_see_more_button').hide();
                            else
                                $('#posts_see_more_button').attr('onClick', "display_posts("+(page+1)+", '"+year+"', '"+month+"', '"+phrase+"', "+sort+");");
                            
                            
                            
                            
                            
                            /////////////////////displays comments////////////////////////
                            for(var x = 0; x < comments.length; x++)
                            {
                                $('#comment_body_'+page+'_'+x).html('');
                                if(comments[x]!='')
                                {
                                    var content='';
                                    for(var y = 0; y < comments[x].length; y++)
                                    {
                                        comments[x][y]=convert_image(text_format(comments[x][y]), 'comment');

                                        var name="<a class='user_name_link' href='http://www.redlay.com/profile.php?user_id="+comments_user_sent[x][y]+"'><span class='comment_name title_color' id='comment_name_"+page+"_"+x+"_"+y+"' onmouseover=name_over(this); onmouseout=name_out(this); >"+comment_names[x][y]+"</span></a>";
                                        var picture="<a href='http://www.redlay.com/profile.php?user_id="+comments_user_sent[x][y]+"'><img class='comment_profile_picture profile_picture' id='picture_comment_"+page+"_"+x+"_"+y+"' src='"+comment_profile_pictures[x][y]+"' ></a>"
                                        var comment="<p class='comment_text_body text_color'>"+comments[x][y]+"</p>";

                                        if(comments_user_sent[x][y]==<?php if(isset($_SESSION['id']))echo $_SESSION['id']; else echo "0"; ?>)
                                            var option="<div class='comment_delete' id='comment_delete_"+page+"_"+x+"_"+y+"' onClick='delete_comment(<?php echo $ID; ?>, "+post_ids[x]+", "+x+", "+comment_ids[x][y]+", "+y+", "+page+");' >x</div>";
                                        else
                                            var option="";

                                        if(<?php if(isset($_SESSION['id'])&&($general[2]=="yes"||(isset($_SESSION['id'])&&$ID==$_SESSION['id'])||(isset($_SESSION['id'])&&user_is_friends($_SESSION['id'], $ID)=='true'))) echo "true"; else echo "false"; ?>==true||comments_user_sent[x][y]==<?php if(isset($_SESSION['id'])) echo $_SESSION['id']; else echo "0"; ?>)
                                        {
                                            //displays likes
                                            if(comments_user_sent[x][y]!=<?php if(isset($_SESSION['id'])) echo $_SESSION['id']; else echo "0"; ?><?php if(isset($_SESSION['page_id'])) echo "&&1==0"; ?>)
                                            {
                                                if(comment_has_liked[x][y]==true)
                                                    var like="<div class='left_function' id='comment_like_body_"+page+"_"+x+"_"+y+"' ><span class='comment_like' id='comment_like_"+page+"_"+x+"_"+y+"'  >Unlike ["+comment_num_likes[x][y]+"]</span></div>";
                                                else if(comment_num_likes[x][y]>=1)
                                                    var like="<div class='left_function' id='comment_like_body_"+page+"_"+x+"_"+y+"' ><span class='comment_like' id='comment_like_"+page+"_"+x+"_"+y+"' >Like ["+comment_num_likes[x][y]+"]</span></div>";
                                                else
                                                    var like="<div class='left_function' id='comment_like_body_"+page+"_"+x+"_"+y+"' ><span class='comment_like' id='comment_like_"+page+"_"+x+"_"+y+"' >Like</span></div>";
                                            }
                                            else
                                            {
                                                if(comment_num_likes[x][y]==1)
                                                    var like="<div class='left_function_disabled' ><span class='comment_like' >1 like</span></div>";
                                                else if(comment_num_likes[x][y]>1)
                                                    var like="<div class='left_function_disabled' ><span class='comment_like' >"+comment_num_likes[x][y]+" likes</span></div>";
                                                else
                                                    var like="";
                                            }

                                            //displays dislikes
                                            if(comments_user_sent[x][y]!=<?php if(isset($_SESSION['id'])) echo $_SESSION['id']; else echo "0"; ?><?php if(isset($_SESSION['page_id'])) echo "&&1==0"; ?>)
                                            {
                                                if(like=="")
                                                    var function_class='left_function';
                                                else
                                                    var function_class='middle_function';
                                                
                                                if(comment_has_disliked[x][y]==true)
                                                    var dislike="<div class='"+function_class+"' id='comment_dislike_body_"+page+"_"+x+"_"+y+"'><span class='comment_dislike' id='comment_dislike_"+page+"_"+x+"_"+y+"' >Undislike ["+comment_num_dislikes[x][y]+"]</span></div>";
                                                else if(comment_num_dislikes[x][y]>=1)
                                                    var dislike="<div class='"+function_class+"' id='comment_dislike_body_"+page+"_"+x+"_"+y+"'><span class='comment_dislike' id='comment_dislike_"+page+"_"+x+"_"+y+"' >Dislike ["+comment_num_dislikes[x][y]+"]</span></div>";
                                                else
                                                    var dislike="<div class='"+function_class+"' id='comment_dislike_body_"+page+"_"+x+"_"+y+"'><span class='comment_dislike' id='comment_dislike_"+page+"_"+x+"_"+y+"' >Dislike</span></div>";
                                            }
                                            else
                                            {
                                                if(like=="")
                                                    var function_class='left_function_disabled';
                                                else
                                                    var function_class='middle_function_disabled';
                                                
                                                if(comment_num_dislikes[x][y]==1)
                                                    var dislike="<div class='"+function_class+"' ><span class='comment_dislike' >1 dislike</span></div>";
                                                else if(comment_num_dislikes[x][y]>1)
                                                    var dislike="<div class='"+function_class+"' ><span class='comment_dislike' >"+comment_num_dislikes[x][y]+" dislikes</span></div>";
                                                else
                                                    var dislike="";
                                            }
                                        }





                                        var timestamp="<span class='comment_timestamp text_color' id='comment_timestamp_"+post_ids[x]+"_"+comment_ids[x][y]+"_"+y+"'>"+comment_timestamps[x][y]+"</span>";


                                        var functions=get_comment_functions(like, dislike, timestamp);
                                        var option_id="comment_delete_"+page+"_"+x+'_'+y;

                                        //content1=picture+close+name+comment+functions+timestamp+comment_break+content1;
                                        var content=get_post_format(picture, name,comment,functions, '', option, option_id, 'comment_body_'+page+'_'+x+'_'+y, comment_badges[x][y])+content;
                                    }

                                    $("#comment_body_"+page+"_"+x).html(content);


                                    for(var y = 0; y < comments[x].length; y++)
                                    {
                                        if(comment_has_liked[x][y])
                                            $('#comment_like_body_'+page+'_'+x+'_'+y).attr({'onClick': "unlike_comment(<?php echo $ID; ?>, "+post_ids[x]+", "+x+", "+comment_ids[x][y]+", "+y+", "+page+", "+comment_num_likes[x][y]+");"});
                                        else
                                            $('#comment_like_body_'+page+'_'+x+'_'+y).attr({'onClick': "like_comment(<?php echo $ID; ?>, "+post_ids[x]+", "+x+", "+comment_ids[x][y]+", "+y+", "+page+", "+comment_num_likes[x][y]+");"});

                                        if(comment_has_disliked[x][y])
                                            $('#comment_dislike_body_'+page+'_'+x+'_'+y).attr({'onClick': "undislike_comment(<?php echo $ID; ?>, "+post_ids[x]+", "+x+", "+comment_ids[x][y]+", "+y+", "+page+", "+comment_num_dislikes[x][y]+");"});
                                        else
                                            $('#comment_dislike_body_'+page+'_'+x+'_'+y).attr({'onClick': "dislike_comment(<?php echo $ID; ?>, "+post_ids[x]+", "+x+", "+comment_ids[x][y]+", "+y+", "+page+", "+comment_num_dislikes[x][y]+");"});
                                    }


                                }
                                else
                                    $("#comment_body_"+page+"_"+x).html("There are no comments");
                            }


                            for(var x = 0; x < comments.length; x++)
                            {
                                for(var y = 0; y< comments[x].length; y++)
                                {
                                    count_time(comment_timestamp_seconds[x][y], "#comment_timestamp_"+post_ids[x]+"_"+comment_ids[x][y]+"_"+y);
                                }
                            }

                            $('.comment_delete').hide();
                            $('.comment_body').hide();
                            $('.comment_textarea').hide();
                            //hides the load gif
                                $('#post_load').hide();
                            initialize_comment_events();
                            
                            

                            //hides the post delete button
                            $('.post_delete').hide();

                            //hides the div that displays who liked and disliked a post
                            $('.like_body').hide();
                            $('.dislike_body').hide();

                            change_color();
                        }
                        else
                        {
                            $('#status_updates').html("<p><?php if(isset($_SESSION['id'])&&$ID==$_SESSION['id']) echo "You have"; else echo $name." has"; ?> not posted anything</p>");
                            $('#post_load').hide();
                        }
                        var page_load=(new Date).getTime() - startTime;
                        record_page_load_time('profile', page_load);
                        
//                        //only do this for me
//                        if(<?php echo $_SESSION['id']; ?>==1)
//                        {
//                            setTimeout(function(){
//                                window.location.reload(window.location);
//                            }, 3000);
//                        }
                        
                    }, "json");
                }
                else
                {
                    $('#status_update_box').html("<p class='locked'>Posts are locked</p>");
                }
            }
            
            function show_close(index)
            {
                var string="#post_options_"+index;
                $(string).show();
            }
            function hide_close(index)
            {
                var string="#post_options_"+index;
                $(string).hide();
            }
            function show_comment(page, index)
            {
                $('#comment_body_'+page+'_'+index).show();
                $('#comment_input_'+page+'_'+index).show();
                $('#comment_title_body_'+page+'_'+index).attr("onClick", "hide_comment("+page+", "+index+");");
            }
            function hide_comment(page, index)
            {
                $('#comment_body_'+page+'_'+index).hide();
                $('#comment_input_'+page+'_'+index).hide();
                $('#comment_title_body_'+page+'_'+index).attr("onClick", "show_comment("+page+", "+index+");");
            }
            
            function display_information()
            {
                if(<?php if($display_non_friends[0]=='yes'||(isset($_SESSION['id'])&&$ID==$_SESSION['id'])||$user_is_friends=='true') echo "true"; else echo "false"; ?>==true)
                {
                    var timezone=get_timezone();
                    $.post('profile_query.php',
                    {
                        num:15,
                        user_id: <?php echo $ID; ?>,
                        timezone:timezone
                    }, function(output)
                    {
                        var birthday=output.birthday;
                        var relationship=output.relationship;
                        var gender=output.gender;
                        var bio=output.bio;
                        var high_school=output.high_school;
                        var college=output.college;
                        var mood=output.mood;
                        var date_joined=output.date_joined;

                        $('#birthday_information').html(birthday);
                        $('#relationship_information').html(relationship);
                        $('#sex_information').html(gender);
                        $('#bio_information').html(bio);
                        $('#high_school_information').html(high_school);
                        $('#college_information').html(college);
                        $('#mood_information').html(mood);
                        $('#date_joined_information').html(date_joined);

                    }, "json");
                }
                else
                    $('#information_content').html("<p class='locked' id='information_lock'>Information is locked</p>");
            }
            
            function display_pictures(number, page)
            {
                if(<?php if($display_non_friends[3]=='yes'||(isset($_SESSION['id'])&&$ID==$_SESSION['id'])||$user_is_friends=='true') echo "true"; else echo "false"; ?>==true)
                {
                    $.post('profile_query.php',
                    {
                        num:5,
                        number: number,
                        user_id: <?php echo $ID; ?>,
                        page: page
                    }, function(output)
                    {
                        var images=output.images;
                        var image_id=output.image_ids;
                        var total_size=output.total_size;
                        var empty=output.empty;
                        
                        if(number==1)
                        {
                            $('#other_pictures').html("<table id='other_pictures_table'><tr id='other_pictures_row_0' class='other_pictures_row'><tr id='other_pictures_row_1' class='other_pictures_row'></table>").css('height', '200px');
                            //$('#other_pictures').html($('#other_pictures').html()+"<a class='link' href='http://www.redlay.com/picture_slide_show.php?user_id=<?php echo $ID; ?>&&type=user&&photo_id=0'><span id='start_picture_slide_show'>Slide Show></span>");
                            $('#start_picture_slide_show').attr({'onmouseover': "{name_over(this); display_title(this, 'Starts a slideshow with all the pictures');}", "onmouseout": "{name_out(this); hide_title(this);}"});
                            var index=0;
                            for(var x = 0; x < 2; x++)
                            {
                                for(var y = 0; y < 6; y++)
                                {
                                    $('#other_pictures_row_'+x).html($('#other_pictures_row_'+x).html()+"<td id='other_picture_unit_"+index+"' class='other_picture_unit'></td>");
                                    index++;
                                }
                            }
                            for(var x = 0; x < images.length; x++)
                            {
                                var image="<div class='image_preview_outside'><a href='http://www.redlay.com/view_photo.php?user_id=<?php echo $ID; ?>&&picture_id="+image_id[x]+"&&type=user'><img id='other_picture_"+x+"' class='other_pictures_picture' src='"+images[x]+"'/></a></div>";
                                $('#other_picture_unit_'+x).html(image);

//                                if(image_has_description[x]==true)
//                                    $('#picture_'+x).attr({'onmouseover': "display_title(this, '"+image_descriptions[x]+"');", 'onmouseout': "hide_title(this);"});
//                                else
//                                    $('#picture_'+x).attr({'onmouseover': "display_title(this, '<i>No Description...</i>');", 'onmouseout': " hide_title(this);"});
                                    $('#other_picture_'+x).attr({'onmouseover': "{$(this).css({'position': 'relative', 'top': '0px', 'z-index': '2', 'left': '0px'});$(this).stop().animate({width:'90px', top: '-7px',left:'-7px'}, 100, function(){});}", 'onmouseout': "$(this).stop().animate({top: '0px',width:'75px',left:'0px'}, 100, function(){$(this).css({'position': 'relative', 'top':'', 'z-index': '1', 'left': ''});});"});

                            }
                            $('.other_pictures_picture').css({'border': '2px solid <?php echo $color; ?>', 'width': '75px'});

                        }
                        else if(number==2)
                        {
                            if(page==1)
                            {
                                $('#other_pictures').html('');
                                for(var x = 1; x <= (total_size/25)+1; x++)
                                    $('#other_pictures').html($('#other_pictures').html()+"<div class='profile_photo_page' id='photos_page_"+x+"'></div>");

                                if(total_size<25)
                                    $('#other_pictures').html($('#other_pictures').html()+"<div class='profile_photo_page' id='photos_page_1'></div>");

                                $('#other_pictures').html($('#other_pictures').html()+"<div id='see_more_photos_body'></div>");
                            }
                            
//                            if(images.length<5)
//                                var num_rows=1;
//                            else
//                                var num_rows=images.length/5;
                            if(!empty)
                                var num_rows=5;
                            else
                            {
                                var num_rows=Math.abs(((total_size-(page*25))/5));
                                console.log("Total size: "+total_size);
                                console.log("page: "+page);
                                console.log("num rows: "+num_rows);
                            }
                            
                            
                            var index=0;
                            $('#photos_page_'+page).html("<table id='photos_page_table_"+page+"' ><tbody id='photos_page_table_body_"+page+"'></tbody></table>").css('height', '');
                            $('#photos_page_table_'+page).css('width', '710px');
                            
                            for(var x = 0; x < num_rows; x++)
                            {
                                $('#photos_page_table_body_'+page).html($('#photos_page_table_body_'+page).html()+"<tr id='photos_page_table_row_"+page+"_"+x+"' class='other_pictures_row'>");
                                for(var y = 0; y < 5; y++)
                                {
                                    $('#photos_page_table_row_'+page+'_'+x).html($('#photos_page_table_row_'+page+'_'+x).html()+"<td id='photos_page_table_unit_"+page+"_"+index+"' class='other_picture_unit'></td>");
                                    index++;
                                }
                            }
                            
                            for(var x = 0; x < images.length; x++)
                            {
                                if(images[x]!=''&&images[x]!=undefined)
                                {
                                    
                                    var image="<div class='image_preview_outside'><a href='http://www.redlay.com/view_photo.php?user_id=<?php echo $ID; ?>&&picture_id="+image_id[x]+"&&type=user'><img id='photo_"+page+"_"+x+"' class='other_pictures_picture' src='"+images[x]+"' /></a></div>";
                                    $('#photos_page_table_unit_'+page+'_'+x).html(image);
                           

//                                if(image_has_description[x]==true)
//                                    $('#other_picture_'+x).attr({'onmouseover': "display_title(this, '"+image_descriptions[x]+"');", 'onmouseout': "hide_title(this);"});
//                                else
//                                    $('#other_picture_'+x).attr({'onmouseover': "display_title(this, '<i>No Description...</i>');", 'onmouseout': " hide_title(this);"});
                                    $('#photo_'+page+'_'+x).attr({'onmouseover': "{$(this).css({'position': 'relative', 'top': '0px', 'z-index': '2', 'left': '0px'});$(this).stop().animate({width:'150px', top: '-9px',left:'-9px'}, 100, function(){});}", 'onmouseout': "$(this).stop().animate({top: '0px',width:'130px',left:'0px'}, 100, function(){$(this).css({'position': 'relative', 'top':'', 'z-index': '1','left':''});});"});
                                }
                            }
                            $('.image_preview_outside').css({'height': '130px', 'width': '130px'});
                            $('.other_pictures_picture').css({'border': '2px solid <?php echo $color; ?>', 'width': '130px'});
                            
                            
                            //modifies, creates, or deletes see_more button
                            if($('#see_more_photos_button').length!=0&&empty==false)
                                $('#see_more_photos_button').attr({'onClick': "display_pictures("+number+", "+(page+1)+");"});
                            else if(empty==false)
                            {
                                $('#see_more_photos_body').html("<input class='see_more_posts button' id='see_more_photos_button' value='See More' type='button' >");
                                $('#see_more_photos_button').attr({'onmouseover': "{display_title(this, 'See more posts');}", 'onmouseout': "{hide_title(this);}", 'onClick': "{display_pictures("+number+", "+(page+1)+");}"});
                            }
                            else
                                $('#see_more_photos_body').html('');
                        }
                        
                        
                        change_color();
                        $('#photo_load').hide();
                    }, "json");
                }
                else
                    $('#other_pictures').html("<p class='locked'>Photos are locked</p>").css('height', '');
            }
            function display_extended_information()
            {
               var timezone=get_timezone();
                $.post('profile_query.php',
                {
                    num:10,
                    user_id: <?php echo $ID; ?>,
                    timezone:timezone
                }, function(output)
                {
                    var name=output.name;
                    var num_adds=output.num_friends;
                    var num_videos=output.num_videos;
                    var relationship_status=output.relationship_status;
                    var birthday=output.birthday;
                    var gender=output.gender;
                    var bio=output.bio;
                    var high_school=output.high_school;
                    var college=output.college;
                    var mood=output.mood;
                    var num_page_likes=output.num_page_likes;
                    var num_updates=output.num_updates;
                    var num_post_likes=output.num_post_likes;
                    var num_post_dislikes=output.num_post_dislikes;
                    var num_pictures=output.num_pictures;
                    var date_joined=output.date_joined;


                    var user_name="<tr><td><span class='profile_information_text_title'>Name: </span></td><td><span class='profile_information_text'>"+name+"</span></td></tr>";
                    var num_adds="<tr><td><span class='profile_information_text_title'>Adds: </span></td><td><span class='profile_information_text'>"+num_adds+"</span></td></tr>";
                    var num_videos="<tr><td><span class='profile_information_text_title'>Videos: </span></td><td><span class='profile_information_text'>"+num_videos+"</span></td></tr>";
                    var relationship_status="<tr><td><span class='profile_information_text_title'>Relationship: <span></td><td><span class='profile_information_text'>"+relationship_status+"</span></td></tr>";
                    var birthday="<tr><td><span class='profile_information_text_title'>Born on: </span></td><td><span class='profile_information_text'>"+birthday+"</span></td></tr>";
                    var gender="<tr><td><span class='profile_information_text_title'>Gender: </span></td><td><span class='profile_information_text'>"+gender+"</span></td></tr>";
                    var bio="<tr><td><span class='profile_information_text_title'>Bio: </span></td><td><span class='profile_information_text'>"+bio+"</span></td></tr>";
                    var high_school="<tr><td><span class='profile_information_text_title'>High School: </span></td><td><span class='profile_information_text'>"+high_school+"</span></td></tr>";
                    var college="<tr><td><span class='profile_information_text_title'>College: </span></td><td><span class='profile_information_text'>"+college+"</span></td></tr>";
                    var mood="<tr><td><span class='profile_information_text_title'>Mood: </span></td><td><span class='profile_information_text'>"+mood+"</span></td></tr>";
                    //var num_page_likes="<tr><td><span class='profile_information_text_title'>Pages liked: </span></td><td><span class='profile_information_text'>"+num_page_likes+"</span></td></tr>";
                    var num_updates="<tr><td><span class='profile_information_text_title'>Total_posts:</span></td><td><span class='profile_information_text'>"+num_updates+"</span></td></tr>";
                    var num_post_likes="<tr><td><span class='profile_information_text_title'>Total post likes:</span></td><td><span class='profile_information_text'>"+num_post_likes+"</span></td></tr>";
                    var num_post_dislikes="<tr><td><span class='profile_information_text_title'>Total post dislikes:</span></td><td><span class='profile_information_text'>"+num_post_dislikes+"</span></td></tr>";
                    var num_pictures="<tr><td><span class='profile_information_text_title'>Total pictures:</span></td><td><span class='profile_information_text'>"+num_pictures+"</span></td></tr>";
                    var joined="<tr><td><span class='profile_information_text_title'>Date joined:</span></td><td><span class='profile_information_text'>"+date_joined+"</span></td></tr>";

                    var begin_table="<table style='padding:20px;'><tbody><tr><td colspan='2'><span class='title title_color'>Extended info</span></td></tr>";
                    var end_table="</tbody></table>";
                   $('#profile_information_box').html(begin_table+user_name+num_adds+num_videos+relationship_status+birthday+gender+bio+high_school+college+mood+num_updates+num_post_likes+num_post_dislikes+num_pictures+joined+end_table);
                   change_color();
                }, "json");
            }
            
            function show_post_audience_box()
            {
               $('.alert_box').css('opacity', 1).show().draggable();
               $('.alert_box_inside').html("<table id='add_friend_table'><tr class='alert_box_row' id='add_friend_row_1'></tr><tr class='alert_box_row' id='add_friend_row_2'></tr><tr class='alert_box_row' id='add_friend_row_3'></tr><tr class='alert_box_row' id='add_friend_row_4'></tr><tr class='alert_box_row' id='add_friend_row_5'></tr><tr class='alert_box_row' id='add_friend_row_6'></tr></table>");
                  $('#add_friend_row_1').html("<td class='alert_box_title_unit' colspan='4'><span class='alert_box_title'>Audiences: </span></td>");
                  $('#add_friend_row_2').html("<td colspan='4'><span class='alert_box_description'>These are the groups that will be able to view this post</span></td>");
                  $('#add_friend_row_3').html("<td colspan='4'><hr class='alert_box_line'/></td>");
                  $('#add_friend_row_4').html("<td colspan='4'><table id='audience_category_table'></table></td>");
                  $('#add_friend_row_5').html("<td colspan='4'><hr class='alert_box_line'/></td>");
                  fill_audience_category_table();
                  $('#post_button').attr({'onClick': "post(<?php echo $ID; ?>);"});

                  change_color();
            }
            function fill_audience_category_table()
            {
               var audience_list=new Array();
               <?php
                if(isset($_SESSION['id']))
                {
                    $query=mysql_query("SELECT audience_defaults FROM public WHERE num=1 LIMIT 1");
                    $query2=mysql_query("SELECT audience_group_lists FROM user_data WHERE user_id=$_SESSION[id] LIMIT 1");
                    if($query&&mysql_num_rows($query)==1&&$query2&&mysql_num_rows($query2)==1)
                    {
                       $array=mysql_fetch_row($query);
                       $array2=mysql_fetch_row($query2);
                       $audience_list=explode('|^|*|', $array2[0]);
                       $audience_defaults=explode('|^|*|', $array[0]);

                       echo "audience_list[0]='Everyone';";
                       $num=1;
                       for($x = 0; $x < sizeof($audience_defaults); $x++)
                       {
                             echo "audience_list[$num]='$audience_defaults[$x]';";
                             $num++;
                       }
                       if($audience_list[0]!='')
                       {
                             for($x =0; $x < sizeof($audience_list); $x++)
                             {
                                echo "audience_list[$num]='$audience_list[$x]';";
                                $num++;
                             }
                       }
                    }
                }
               ?>
               //adds rows to table
               if(audience_list.length>=4)
               {
                  for(var x = 0; x < (audience_list.length/3); x++)
                        $('#audience_category_table').html($('#audience_category_table').html()+"<tr class='audience_category_row' id='audience_category_row_"+x+"'></tr>");
               }
               else
                  $('#audience_category_table').html("<tr class='audience_category_row' id='audience_category_row_0'></tr>");

               //adds actual list items
               var num=0;
               for(var x =0; x < audience_list.length; x++)
               {
                  $('#audience_category_row_'+num).html($('#audience_category_row_'+num).html()+"<td id='option_"+x+"'><input type='checkbox' id='checkbox_"+x+"'/><span class='audience_option' id='checkbox_name_"+x+"' >"+audience_list[x]+"</span></td>");
                  if(x%3==0&&x!=0)
                        num++;
               }
               for(var x =0; x < audience_list.length; x++)
                  $('#checkbox_'+x).data("audience_name", audience_list[x]);
               $('#add_friend_row_6').html("<td class='alert_box_confirmation_row_unit_left'></td><td class='alert_box_load_unit' ></td><td class='alert_box_confirmation_unit'><input type='button' class='button green_button' id='post_button' value='Post' /></td><td class='alert_box_cancel_unit'><input type='button' class='button gray_button' id='cancel_post_buton' value='Cancel' onClick=close_alert_box(); /></td>");
            }

            function process_new_video()
            {
                $.post('profile_query.php',
                {
                    num: 9,
                    video_url: $('#add_video_input').val()
                }, function(output)
                {
                    var video=output.video;
                    
                    if(video!='')
                    {
                        $('#video_preview').html(video);
                        $('#add_video').css('height', '290px');
                    }
                    else
                        $('#video_preview').html("<p class='text_color'>Invalid video</p>");
                }, "json");
            }
            function animate_share_video_form()
            {
                if($('#add_video').css('display')=='block')
                {
                    $('#add_video').animate({
                        height: '0px'
                    }, 1000, function()
                    {
                        $('#add_video').hide();
                    });
                }
                else
                {
                    $('#add_video').show();
                    $('#add_video').animate({
                        height: '110px'
                    }, 1000, function()
                    {

                    });
                }
            }
            function safe_search()
            {
                if($('#post_chronological_sort').val()!='')
                    var sort=$('#post_chronological_sort').val();
                else 
                    var sort='0';
                
                var month=$('#post_sort_months').val();
                var year=$('#post_sort_years').val();
                
                if($('#search_posts_input').val()=='')
                    var phrase='none';
                else
                    var phrase=$('#search_posts_input').val();
                
                display_posts(1, month, year, phrase, sort);
            }
            
            function display_post_preview()
            {
                if($('#post_form_row_4').css('display')=='none')
                {
                    $('#post_form_row_4').show();
                    $('.post_preview_box').css('height', '0px');
                    $('.post_preview_box').animate(
                    {
                        height:100
                    }, 250, function()
                    {
                        $('.post_preview_box').css({'min-height': '100px', 'height': ''});
                    });
                }
                
            }
            function change_post_preview()
            {
                $('#post_preview_text').html(text_format($('#social_update').val()));
            }
            function change_text_format_test()
            {
                $('#text_format_text').html(text_format($('#text_format_input').val()));
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


                    var profile_picture="<img class='profile_picture_status profile_picture' src='<?php echo get_profile_picture($_SESSION['id']);
                    ?>' id='text_format_profile_picture' />";
                    var text="<p class='status_update_text text_color' id='text_format_text' style='width:315px;'></p>";
                    var name="<div class='user_name_body'><span class='user_name' id='text_format_preview_name'><?php echo get_user_name($_SESSION['id']); ?></span></div>";

                    var row_1="<tr id='post_row_1' class='post_row'>  <td class='post_profile_picture_unit'>"+profile_picture+"</td><td class='post_body_unit'>"+name+text+"</td>  </tr>";

                    $('#text_format_preview_box').html("<div id='text_format_preview' class='status_update' style='margin:5px'><table style='width:100%;'><tbody>"+row_1+"</tbody></table></div>");

                            $('#text_format_info').html("<table style='width:100%;margin-top:20px;' class='text_color' border='1'><tbody id='text_format_info_table_body'></tbody></table>");
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
            function add_video()
            {
                $.post('add_user_video.php',
                {
                    video: $('#add_video_input').val()
                }, function (output)
                {
                    if(output=='Video posted!')
                        display_error(output, 'good_errors');
                    else
                        display_error(output, 'bad_errors');
                });
            }
            function display_banner_change_form()
            {
                $('#banner_upload_button').show().animate({
                    opacity:1
                }, 150, function(){});
                
                $('#button_container_because_F_IE').html("<input class='button red_button' id='banner_change_button' type='submit' value='Upload' />");
                initialize_banner();
            }
            
            function initialize_banner()
            {
                $( "#banner" ).draggable({
                    axis: "y",
                    stop:function(event, ui){
                        var position=$('#banner').position();
                        var top=position.top;
                        var height=$('#banner').height();
                        
                        //can't have empty space at top
                        if(top>0)
                        {
                            $('#banner').css('top', '0px');
                            top=0;
                        }
                        
                        //can't have empty space at bottom
                        if(top+height<200)
                        {
                            top=(height-200)*-1;
                            $('#banner').css('top', top+"px");
                        }
                        
                        change_banner_position(top);
                    }
                });
            }
            function change_banner_position(top)
            {
                $.post('change_banner_position.php',
                {
                    top: top
                }, function(output)
                {
                    if(output!="")
                        display_error(output, 'bad_errors');
                });
            }
            
        </script>
        <script type="text/javascript">
            $(window).ready(function()
            {
                <?php 
                if(!isset($_SESSION['id'])||(isset($_SESSION['id'])&&!user_is_friends($ID, $_SESSION['id'])))
                {
                    echo "$('#post_form_row_2').html('');";
                    echo "$('#post_form_row_3').html('');";
                    echo "$('#options').html('');";
                    echo "$('#options').hide();";
                }
                ?>
                display_activity(1);
                display_information();
                $('#add_video').hide();
                initialize_video_input();
                initialize_post_preview();
                $('#change_profile_picture_button_body').hide();
                <?php
                    if(isset($_SESSION['id'])&&$ID==$_SESSION['id'])
                    {
                        echo "$('#change_profile_picture_button_body').show();";
                        echo "display_groups('post_audience_selection_box');";
                    }
                    else
                        echo "$('#category_form_unit').html('');";
                ?>
                
                display_adds();
                $('#most_popular').hide();
                $('#profile_information_box').hide();
                $('#user_videos').hide();
                $('#likes').hide();
                $('#post_form_row_4').hide();
                change_color();
                $('.post_hide').hide();
                $('#hide_more_information').hide();
                profile_menu_item(1);
                <?php
                $path=get_user_background_pic($ID);
                if((file_exists_server($path)==true)&&($general[1]=='yes'||(isset($_SESSION['id'])&&$ID==$_SESSION['id'])||$user_is_friends=='true'))
                    echo "$('html').css({'background-image': 'url(\'$path\')', 'background-position' :'center 50px'});";
                else
                    echo "$('html').css({'background-image': 'url(\'".get_default_background_pic($redlay_theme)."\')', 'background-position' :'center 50px'});";
                ?>
                $('#banner_upload_button').css('opacity', '0').hide();
                <?php
                    $banner=get_user_banner($ID);
                    if($banner==""&&$ID!=$_SESSION['id'])
                    {
                        echo "$('#banner_container').css({'border': 'none', 'height': '0px'}).hide();";
                        echo "$('#banner').hide();";
                    }
                    
                    if($ID==$_SESSION['id'])
                    {
                        echo "$('#banner_change_button').show();";
                    }
                    else
                    {
                        echo "$('#banner_change_button').hide();";
                    }
                        
                ?>
                <?php include('required_jquery.php'); ?>
             });
            </script>
            <script type="text/javascript">
                <?php include('required_google_analytics.js'); ?>
            </script>
    </head>
    <body>
        <?php include('required_html.php'); ?>
       
            <?php 
            if(isset($_SESSION['id'])) 
            {
                echo " <div id='top'>";
                include('top.php');
                echo "</div>";
            }
            else
                include('index_top.php');
            ?>
        <div id="main" >
            <?php include('required_side_html.php'); ?>
            <table style="position:relative;margin:0 auto;border-spacing:10px 0px;top:100px;border-spacing:0px;">
                <tbody>
                    <tr>
                        <td colspan="3">
                            <div id="banner_container">
                                <?php
                                    $query=mysql_query("SELECT banner_data FROM user_display WHERE user_id=$ID LIMIT 1");
                                    if($query&&mysql_num_rows($query)==1)
                                    {
                                        $array=mysql_fetch_row($query);
                                        $banner_data=explode('|^|*|', $array[0]);
                                    }
                                ?>
                                <div id="banner_container_because_F_javascript">
                                    <img id="banner" src="<?php echo get_user_banner($ID); ?>" <?php echo "style='top:$banner_data[2]px'"; ?>/>
                                </div>
                                <div id="banner_form">
                                    <form method='post' action='upload_banner.php' enctype='multipart/form-data' target='banner_upload_iframe'>
                                        <table>
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <input type="file" name="image" id="banner_upload_button" />
                                                    </td>
                                                    <td>
                                                        <div id="button_container_because_F_IE">
                                                            <input class="button red_button" id="banner_change_button" type="button" value="Change banner" onClick="display_banner_change_form();"/>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td id="profile_left" style="vertical-align:top;width:210px" >
                            <table id="profile_left_table">
                                <tbody>
                                    <tr>
                                        <td>
                                            <div style="position:relative;">
                                                <?php
                                                    if(isset($_SESSION['page_id']))$ID2=0; else if(isset($_SESSION['id'])) $ID2=$_SESSION['id']; else $ID2=0;
                                                    if($ID==$ID2)
                                                        $string2="http://www.redlay.com/home.php";
                                                    else
                                                        $string2="http://www.redlay.com/profile.php?user_id=".$ID;
                                                ?>
                                                <a href="<?php echo $string2; ?>"><img id="profile_pic" src="<?php if(file_exists_server("https://s3.amazonaws.com/bucket_name/users/$ID/photos/0.jpg")) echo "https://s3.amazonaws.com/bucket_name/users/$ID/photos/0.jpg"; else if(file_exists_server("https://s3.amazonaws.com/bucket_name/users/$ID/photos/0.png")) echo "https://s3.amazonaws.com/bucket_name/users/$ID/photos/0.png"; else if(file_exists_server("https://s3.amazonaws.com/bucket_name/users/$ID/photos/0.gif")) echo "https://s3.amazonaws.com/bucket_name/users/$ID/photos/0.gif"; ?>" alt="http://pics.redlay.com/pictures/default_profile_picture.png"/></a>
                                                <div style="position:absolute;bottom:0px;right:0px;" id="change_profile_picture_button_body">
                                                    <input class="button red_button" type="button" value="Change Profile Picture" id="change_profile_picture_button" onclick="display_profile_picture_menu();"/>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
<!--                                    <tr>
                                        <td id="profile_points_unit">
                                            <table>
                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            <img class="icon" src="http://pics.redlay.com/pictures/points.png" />
                                                        </td>
                                                        <td>
                                                            <span class="text_color"><?php echo number_format(get_points($ID)); ?> points</span>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>-->
                                    <?php
                                        if($has_gold)
                                        {
                                            echo "<tr><td><div id='redlay_gold_box' ><a class='link' href='http://www.redlay.com/redlay_gold.php' ><p id='gold_description' onmouseover=name_over(this); onmouseout=name_out(this); >Gold Member</p></a></div></td></tr>";
                                        }
                                    ?>
                                    <tr>
                                        <td id="profile_menu">
                                            <table id="profile_menu_table" style="width:100%;padding:10px;">
                                                <tr class="profile_menu_row">
                                                    <td class="profile_menu_item selected text_color" id="profile_menu_1" onClick="profile_menu_item(1);" onmouseover="{$(this).css('text-decoration', 'underline');}" onmouseout="{$(this).css('text-decoration', 'none');}">All</td>
                                                </tr>
                                                <tr><td><hr class="break"/></td></tr>
                                                <tr>
                                                    <td class="profile_menu_item text_color" id="profile_menu_2" onClick="profile_menu_item(2);" onmouseover="{$(this).css('text-decoration', 'underline');}" onmouseout="{$(this).css('text-decoration', 'none');}"> Posts</td>
                                                </tr>
                                                <tr>
                                                    <td class="profile_menu_item text_color" id="profile_menu_3" onClick="profile_menu_item(3);" onmouseover="{$(this).css('text-decoration', 'underline');}" onmouseout="{$(this).css('text-decoration', 'none');}">Photos</td>
                                                </tr>
                                                <tr>
                                                    <td class="profile_menu_item text_color" id="profile_menu_4" onClick="profile_menu_item(4);" onmouseover="{$(this).css('text-decoration', 'underline');}" onmouseout="{$(this).css('text-decoration', 'none');}">Videos</td>
                                                </tr>
                                                <tr>
                                                    <?php
                                                        if($calendar_visible==true)
                                                        {
                                                            echo "<td class='profile_menu_item text_color' id='profile_menu_9' onClick='profile_menu_item(9);' onmouseover=name_over(this); onmouseout=name_out(this); >Calendar</td>\n";
                                                            echo "</tr><tr>";
                                                        }
                                                    ?>
        <!--                                            <td class="profile_menu_item" id="profile_menu_7" onClick="profile_menu_item(7);" onmouseover="{$(this).css('text-decoration', 'underline'); display_title(this, 'Displays all pages liked');}" onmouseout="{$(this).css('text-decoration', 'none'); hide_title(this);}">Likes</td>
                                                    <td class="profile_menu_seperator" >|</td>-->
        <!--                                            <td class="profile_menu_item" id="profile_menu_8" onclick="profile_menu_item(8);" onmouseover="{$(this).css('text-decoration', 'underline'); display_title(this, 'Displays this user\'s documents');}" onmouseout="{$(this).css('text-decoration', 'none'); hide_title(this);}">Docs</td>
                                                    <td class="profile_menu_seperator" >|</td>-->
                                                    <td class="profile_menu_item text_color" id="profile_menu_5" onClick="profile_menu_item(5);" onmouseover="{$(this).css('text-decoration', 'underline');}" onmouseout="{$(this).css('text-decoration', 'none');}">Info</td>
                                                    <!--<td class="profile_menu_seperator" >|</td>-->
                                                    <!--<td class="profile_menu_item" id="profile_menu_6" onClick="profile_menu_item(6);" onmouseover="{$(this).css('text-decoration', 'underline'); display_title(this, 'Displays most popular photo and post');}" onmouseout="{$(this).css('text-decoration', 'none'); hide_title(this);}">Popular</td>-->
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td id="options">
                                            <table id="options_table">

                                            </table>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td id="user_friends_box">
                                            <div style="height:350px;overflow:auto;">
                                                <a id="friends_title" class="link" href="http://www.redlay.com/all_adds.php?user_id=<?php echo $ID; ?>">
                                                    <?php if($display_non_friends[1]=='yes'||(isset($_SESSION['id'])&&$ID==$_SESSION['id'])||$user_is_friends) echo "<p class='profile_text' id='friends_title_text' >Adds [".sizeof(get_friends($ID))."]</p>" ?>
                                                </a>
                                                <table id="friends_main">
                                                    <tbody id="friends_main_body">

                                                    </tbody>
                                                </table>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                                
                                
                                
                                
                            </table>
                        </td>
                        <td id="profile_middle" style="vertical-align:top;width:520px">
                            <table id="profile_middle_table">
                                <tbody>
                                    <tr>
                                        <td id="name_box">
                                            <p id="user_name"><?php echo $name ?></p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td id="information_box">
                                            <div id="information_content">
                                                <table id="information_table">
                                                    <tbody>
                                                        <tr class="information_row" id="information_row_1">
                                                            <td class="info_item_left" ><span class="title_information" id="birthday_title_information">Birthday: </span></td>
                                                            <td class="info_item_right" ><span class="text_information" id="birthday_information"> </span></td>
                                                        </tr>
                                                        <tr class="information_row" id="information_row_2">
                                                            <td class="info_item_left" ><span class="title_information" id="relationship_title_information">Relationship: </span></td>
                                                            <td class="info_item_right" ><span class="text_information" id="relationship_information"> </span></td>
                                                        </tr>
                                                        <tr class="information_row" id="information_row_3">
                                                            <td class="info_item_left" ><span class="title_information" id="sex_title_information">Gender: </span></td>
                                                            <td class="info_item_right" ><span class="text_information" id="sex_information" > </span></td>
                                                        </tr>
                                                        <tr class="information_row" id="information_row_4">
                                                            <td class="info_item_left" ><span class="title_information" id="bio_title_information">Bio: </span></td>
                                                            <td class="info_item_right" ><span id="bio_information" class="text_information"> </span></td>
                                                        </tr>
                                                        <tr class="information_row" id="information_row_5">
                                                            <td class="info_item_left" ><span class="title_information" id="high_school_title_information">High School: </span></td>
                                                            <td class="info_item_right" ><span class="text_information" id="high_school_information"> </span></td>
                                                        </tr>
                                                        <tr class="information_row" id="information_row_6">
                                                            <td class="info_item_left" ><span class="title_information" id="college_title_information">College: </span></td>
                                                            <td class="info_item_right" ><span class="text_information" id="college_information"> </span></td>
                                                        </tr>
                                                        <tr class="information_row" id="information_row_7">
                                                            <td class="info_item_left" ><span class="title_information" id="mood_title_information">Mood: </span></td>
                                                            <td class="info_item_right" ><span class="text_information" id="mood_information"> </span></td>
                                                        </tr>
                                                        <tr class="information_row" id="information_row_8">
                                                            <td class="info_item_left" ><span class="title_information" id="date_joined_title_information">Date joined: </span></td>
                                                            <td class="info_item_right" ><span class="text_information" id="date_joined_information"> </span></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td id="other_pictures">
                                            <div id="photo_load"><img class="load_gif" src='http://pics.redlay.com/pictures/load.gif'/></div>
                                            <table id="other_pictures_table">
                                                <tr id="other_pictures_row_0" class="other_pictures_row">
                                                    <td id="other_picture_unit_0" class="other_picture_unit"></td>
                                                    <td id="other_picture_unit_1" class="other_picture_unit"></td>
                                                    <td id="other_picture_unit_2" class="other_picture_unit"></td>
                                                    <td id="other_picture_unit_3" class="other_picture_unit"></td>
                                                    <td id="other_picture_unit_4" class="other_picture_unit"></td>
                                                    <td id="other_picture_unit_5" class="other_picture_unit"></td>
                                                </tr>
                                                <tr id="other_pictures_row_1" class="other_pictures_row">
                                                    <td id="other_picture_unit_6" class="other_picture_unit"></td>
                                                    <td id="other_picture_unit_7" class="other_picture_unit"></td>
                                                    <td id="other_picture_unit_8" class="other_picture_unit"></td>
                                                    <td id="other_picture_unit_9" class="other_picture_unit"></td>
                                                    <td id="other_picture_unit_10" class="other_picture_unit"></td>
                                                    <td id="other_picture_unit_11" class="other_picture_unit"></td>
                                                </tr>
                                            </table>
                                            <span id='start_picture_slide_show' onmouseover="name_over(this);" onmouseout="name_out(this);" onClick="window.location.replace('http://www.redlay.com/picture_slide_show.php?user_id=<?php echo $ID; ?>&&type=user&&num=0');">Slide Show></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td id="status_update_box">
                                            <table style="width:100%;">
                                                <tbody>
                                                    <tr>
                                                        <td id="post_form_unit">
                                                            <div id="status_update_form">
                                                                <table id="update">

                                                                    <tr class="post_form_row" id="post_form_row_1">
                                                                        <td class="post_form_unit" colspan="2">
                                                                            <span id="update_title" class="profile_text"><?php echo get_post_title($ID); ?></span>
                                                                        </td>
                                                                    </tr>
                                                                    <tr class="post_form_row" id="post_form_row_2">
                                                                        <td class="post_form_unit" colspan="2">
                                                                            <textarea autofocus id="social_update" class="input_box" onFocus="input_in(this);" onBlur="input_out(this);" name="social_update" maxlength="500" placeholder="What's up?" ></textarea>
                                                                        </td>
                                                                    </tr>
                                                                    <tr class="post_form_row" id="post_form_row_4" style="text-align:left;">
                                                                        <td colspan="2">
                                                                            <div class="post_preview_box">




                                                                                <div id="post_preview_status_update" class="status_update" style="margin:5px">


                                                                                    <table style="width:100%;">
                                                                                        <tbody>
                                                                                            <tr id="post_preview_row_1" class="post_row">
                                                                                                <td class="post_profile_picture_unit">
                                                                                                    <img class="profile_picture_status profile_picture" src="<?php echo get_profile_picture($_SESSION['id']); ?>" id="post_preview_profile_picture" />
                                                                                                </td>
                                                                                                <td class="post_body_unit">
                                                                                                     <div class="user_name_body">
                                                                                                        <span class="user_name" id="post_preview_name" ><?php echo $name; ?></span>
                                                                                                    </div>
                                                                                                    <p class="status_update_text" id="post_preview_text" style="width:315px"></p>
                                                                                                </td>
                                                                                            </tr>
                                                                                        </tbody>
                                                                                    </table>

                                                                                </div>





                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                    <tr class="post_form_row" id="post_form_row_3">
                                                                        <td class="post_form_unit" id="category_form_unit">
                                                                                <table style="width:100%;">
                                                                                    <tr>
                                                                                        <td style="text-align:left;width:50%;">
                                                                                            <div class="audience_selection_box" id="post_audience_selection_box">

                                                                                            </div>
                                                                                        </td>
                                                                                        <td style="text-align:right;width:50%;">
                                                                                            <span style="cursor:pointer;" class="title_color" onmouseover="name_over(this);" onmouseout="name_out(this);" onClick="display_text_format();">Text Format</span>
                                                                                        </td>
                                                                                    </tr>
                                                                                </table>
                                                                        </td>
                                                                        <td class="post_form_unit" id="post_button_form_unit">
                                                                            <input class="submit_button button red_button" id="social_submit_button" onClick="{post(<?php echo $ID; ?>);}" type="button" name="social_update_submit" value="Post" />
                                                                        </td>
                                                                    </tr>
                                                                </table>

                                                                <div id="post_load"><img class="load_gif" src='http://pics.redlay.com/pictures/load.gif'/></div>
                                                            </div>
                                                        </td>
                                                        <td id="post_sort_unit">
                                                            <div id="post_sort_options">
                                                                <table>
                                                                    <tbody>
                                                                        <tr>
                                                                            <td colspan="2" style="text-align:center;">
                                                                                <span class="profile_text" style="width:100%">Sort:</span>
                                                                            </td>
                                                                        </tr>


                                                                        <tr>
                                                                            <td colspan="2">
                                                                                <select id="post_chronological_sort" onChange="safe_search();">
                                                                                    <option value="1">Newest to Oldest</option>
                                                                                    <option value="2">Oldest to Newest</option>
                                                                                </select>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>

                                                                        </tr>


                                                                        <tr>
                                                                            <td>
                                                                                <select id='post_sort_months' onChange="display_posts(1,  $('#post_sort_years').val(), $(this).val(), 'none', 1);" onChange="safe_search();"></select>
                                                                            </td>
                                                                            <td>
                                                                                <select id='post_sort_years' onChange="{display_posts(1, $(this).val(), $('#post_sort_months').val(), 'none', 1); display_post_sort($(this).val());}" onChange="safe_search();"></select>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td colspan='2'>
                                                                                <input class="input_box" placeholder="Search by word or phrase" onFocus="input_in(this);" onBlur="input_out(this);" type="text" id="search_posts_input"/>
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="2">
                                                            <div id="status_updates">

                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr id="user_videos_row">
                                        <td id="user_videos">
                                                <?php if(isset($_SESSION['id'])&&$ID==$_SESSION['id']) echo "<input type='button' class='button gray_button' id='share_video_button' onClick='animate_share_video_form();' value='Share Video'/>"; ?>
                                                <div id="add_video_form">
                                                    <div id='add_video'>
                                                        <p id='add_video_text' class='settings_text'>Share a video:</p>
                                                        <input type='text' id='add_video_input' class="input_box" onFocus="input_in(this);" onBlur="input_out(this);" placeholder='EX: http://www.youtube.com/watch?v=myMC3IETlpo' maxlength='255'/>
                                                        <div id="video_preview">

                                                        </div>
                                                        <input type='button' id='add_video_submit' class='button red_button' value='Add' onClick='add_video();'/>
                                                    </div>
                                                </div>
                                                <?php if(isset($_SESSION['id'])&&$ID==$_SESSION['id']) echo "<hr />"; ?>
                                                <div id="video_load"><img class="load_gif" src='http://pics.redlay.com/pictures/load.gif'/></div>
                                                <div id="videos"></div>
                                        </td>
                                    </tr>
                                    <tr id="profile_information_box_row">
                                        <td id="profile_information_box">
                                            
                                        </td>
                                    </tr>
                                    <tr id="most_popular_row">
                                        <td id="most_popular">
                                            
                                        </td>
                                    </tr>
                                    <tr id="likes_row">
                                        <td id="likes">
                                            
                                        </td>
                                    </tr>
                                    <tr id="docs_row">
                                        <td id="docs">
                                            
                                        </td>
                                    </tr>
                                    <tr id="profile_calendar_row">
                                        <td id="profile_calendar">
                                                <p id="calendar_title" class="title_color">Calendar</p>
                                                <?php if(isset($_SESSION['id'])&&$ID==$_SESSION['id']&&$calendar_visible==false) echo "<p class='text_color'>This isn't visible to anyone but you</p>" ?>
                                                <div id="change_calendar">
                                                    <div class="change_calendar_title" id="change_calendar_year_title">
                                                        <img src='http://pics.redlay.com/pictures/left arrow.png' class='change_calendar_arrows' id="year_left_arrow" />
                                                        <span class="change_calendar_text" id="calendar_year_text" ><?php echo $year; ?></span>
                                                        <img class='change_calendar_arrows' src='http://pics.redlay.com/pictures/right arrow.png'id="year_right_arrow" />
                                                    </div>
                                                    <div class="change_calendar_title" id="change_calendar_ymonth_title">
                                                        <img src='http://pics.redlay.com/pictures/left arrow.png' class='change_calendar_arrows' id="month_left_arrow"/>
                                                        <span  class="change_calendar_text" id="calendar_month_text"><?php echo $month; ?></span>
                                                        <img class='change_calendar_arrows' src='http://pics.redlay.com/pictures/right arrow.png'  id="month_right_arrow"/>
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
                        <td id="profile_right" style="vertical-align:top;">
                            <table id="profile_right_table">
                                <tbody>
                                    <tr>
                                        <td id="account_activity_box">
                                            <p id="account_activity_title" class="profile_text">Activity</p>
                                            <div id="account_activity">

                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                </tbody>
            </table>
            <iframe name="banner_upload_iframe" style="display:none"></iframe>
            <iframe name="photo_upload_iframe" style="display:none"></iframe>
            <script type="text/javascript">
                $(document).ready(function()
                {
                    //$('#login_password_text_box').unbind('keypress');
                    $('#login_password_text_box').keydown(function(e)
                    {
                        var key = (e.keyCode ? e.keyCode : e.which);
                        if(key == '13')
                            login();
                    });
                });
                
                function initialize_comment_events()
                {
                    $('.comment_textarea').unbind('keypress').unbind('keydown').unbind('keyup');
                    $('.comment_textarea').keyup(function(e)
                    {
                        var key = (e.keyCode ? e.keyCode : e.which);
                        if(key == '13')
                        {
                            comment(<?php echo $ID; ?>, $(this).data('poster_id'), $(this).data('post_id'), $(this).data('index'), $(this).data('page'), $(this).data('num_comments'));
                            $(this).val('');
                        }
                    });
                }
                function initialize_video_input()
                {
                    $('#add_video_input').unbind('keypress').unbind('keydown').unbind('keyup');
                    $('#add_video_input').keydown(function(e)
                    {
//                        var key = (e.keyCode ? e.keyCode : e.which);
//                        if(key == '13')
                          setTimeout(function(){process_new_video();}, 1000);
                            
                    });
                }
                function initialize_post_search()
                {
                    $('#search_posts_input').unbind('keypress').unbind('keydown').unbind('keyup');
                    $('#search_posts_input').keyup(function(e)
                    {
                        var key = (e.keyCode ? e.keyCode : e.which);
                        if(key == '13')
                            safe_search();
                    });
                }
                function initialize_post_preview()
                {
                    $('#social_update').unbind('keypress').unbind('keydown').unbind('keyup');
                    $('#social_update').keydown(function(e)
                    {
                        display_post_preview(); 
                        change_post_preview();
                    });
                    $('#social_update').keyup(function(e)
                    {
                        change_post_preview();
                    });
                }
                function initialize_text_format_test()
                {
                    $('#text_format_input').unbind('keypress').unbind('keydown').unbind('keyup');
                    $('#text_format_input').keydown(function(e)
                    {
                        change_text_format_test();
                    });
                    $('#text_format_input').keyup(function(e)
                    {
                        change_text_format_test();
                    });
                }
            </script>
        </div>
    </body>
</html>