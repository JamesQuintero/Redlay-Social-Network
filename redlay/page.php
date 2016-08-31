<?php
@include('init.php');
include('universal_functions.php');
$allowed="all";
include('security_checks.php');

//if user or page is not logged in or user does not exists
$ID=(int)($_GET['page_id']);

if(!is_id($ID)||!page_id_exists($ID)||(isset($_SESSION['id'])&&page_blocked($ID, $_SESSION['id'])))
{
    header("Location: http://www.redlay.com");
    exit();
}

if(page_id_terminated($ID))
{
    header("Location: http://www.redlay.com/account_terminated.php");
    exit();
}

//records page view
//if(isset($_SESSION['id'])&&$ID!=$_SESSION['id'])
//    record_page_view($ID);


//gets the user's privacy preferences
$has_gold=has_redlay_page_gold($ID, 'all');


$date=explode(' ', str_replace(',', '', get_adjusted_date(get_date(), 0)));
$month=$date[0];
$year=$date[2];

$name=get_page_name($ID);
?>
<!DOCTYPE html>
<html>
    <head>
        <title><?php echo $name; ?></title>
        <?php include('required_page_header.php'); ?>
        <script type="text/javascript">
            function change_color()
            {
                <?php
                    $colors=get_page_display_colors($ID);
                    $color=$colors[0];
                    $box_background_color=$colors[1];
                    $text_color=$colors[2];
                ?>
                $('.box').css('border', '5px solid <?php echo $color; ?>');
                $('.box').css({'background-color': '<?php echo $box_background_color; ?>'});
                <?php //if($has_gold) {echo "$('#redlay_gold_box, #name_box, #like_box, #profile_menu').css('border-color', 'rgb(252,178,0)');";} ?>
                    
                $('#page_left_table, #page_middle_table, #page_right_table').css('background-color', '<?php echo $box_background_color; ?>');

                $('#information_content, .locked, #status_updates, #name_box, #like_box, #gold_description, .file_input, .audience_option, #company_footer, .alert_box_description').css('color', '<?php echo $text_color; ?>');
                $('#social_update').css('outline-color', '<?php echo $color; ?>');
                $('.post_delete, .comment_delete').css({'background-color': '<?php echo $color; ?>', 'color': '<?php echo $text_color; ?>'});
                $('.comment_textarea').css('outline-color', '<?php echo $color; ?>');
                $('.other_picture').css('border', '2px solid <?php echo $color; ?>');
                $('.title_information, .alert_box_title, .friend_name, .profile_activity_text_video, .comment_name').css('color', '<?php echo $color; ?>');
                $('#bio_title_information, .popular_text, .page_user_name_body, .user_name, .user_name_activity').css('color', '<?php echo $color; ?>');
                <?php $path=get_page_background_pic($ID); if(file_exists_server($path)&&$colors[5]=="yes") echo "$('body').css('background-attachment', 'fixed');"; ?>
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
                    $('#page_middle_table').css('width', '730px');
                }
                
                if(num==1)
                {
                    $('#page_middle_table').css('width', '520px');
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
                    <?php if(isset($_SESSION['page_id'])) echo "$('#options').show();"; ?>;
                    $('#post_sort').html('');
                    display_posts(1, 'all', 'all', 1);
                    $('#post_sort_options').hide();
                    $('#profile_information_box').hide();
                    $('#docs').hide();
                    $('#likes').hide();
                    $('#account_activity_box').show();
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
                    display_extended_information();
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
            function display_likes()
            {
                $('#likes').html("<p style='text-align:center;color:<?php echo $text_color; ?>'>This feature isn't available yet. Pages will be ready soon!</p>");
            }
            
            function toggle_video_checkbox(num)
            {
                if($('#video_checkbox_'+num).attr('src')=='http://pics.redlay.com/pictures/gray_checkbox.png')
                    $('#video_checkbox_'+num).attr('src', 'http://pics.redlay.com/pictures/gray_checkbox_checked.png');
                else
                    $('#video_checkbox_'+num).attr('src', 'http://pics.redlay.com/pictures/gray_checkbox.png');
            }

            function display_post_sort(year)
            {
                var timezone=get_timezone();
                $.post('page_query.php',
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
            function search_posts()
            {
                
            }
            function delete_video(video)
            {
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
                if(<?php echo $ID; ?>== <?php if(isset($_SESSION['page_id'])) echo $_SESSION['page_id']; else echo "0"; ?>)
                    $('#options_table').html("<tr class='buttons_row' id='options_row_1'><td class='buttons_unit'><input type='button' class='button options_button green_button' id='photo_button' value='Upload Photo' onClick=display_photo_upload_box(<?php if(isset($_SESSION['id'])&&has_redlay_gold($_SESSION['id'], "photo_quality")) echo "true"; else echo "false"; ?>); /></td></tr>");

                else if(<?php if(isset($_SESSION['page_id'])) echo 'false'; else echo "true"; ?>==true)
                {
                    $('#options_table').html("<tr class='buttons_row' id='options_row_1'><td class='buttons_unit'><input type='button' class='button options_button green_button' id='message_button' value='Message' onClick='display_message_box(<?php echo $ID; ?>);' /></td></tr>");
                    $('#options_table').html($('#options_table').html()+"<tr class='buttons_row' id='options_row_2'><td class='buttons_unit'><input type='button' class='button options_button green_button' value='Group Options' onClick='show_group_options();'  /></td></tr>");
                    $('#options_table').html($('#options_table').html()+"<tr class='buttons_row' id='options_row_3'><td class='buttons_unit'><input type='button' class='button options_button green_button' id='delete_button' value='Delete' onClick='delete_user();' /></td></tr>");
                }
                else
                {
                    <?php if(isset($_SESSION['id'])&&!following_page($ID)) echo "$('#options_table').html('<tr class=\'buttons_row\' id=\'options_row_1\'><td class=\'buttons_unit\'><input type=\'button\' class=\'button options_button green_button\' id=\'add_friend_button\' value=\'Follow\' onClick=\'{follow_page($ID);}\'/></td></tr>');"; ?>
                    $('#options_table').html($('#options_table').html()+"<tr class='buttons_row' id='options_row_2'><td class='buttons_unit'><input type='button' class='button options_button green_button' id='block_button' value='Block' onClick='block_user();'/></td></tr>");
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
                    if(isset($_SESSION['page_id']))
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
           function display_posts(page, year, month, sort)
            {
                <?php 
                    if(isset($_SESSION['page_id'])&&$ID==$_SESSION['page_id'])
                    {
                        echo "$('#post_form_row_3').show();";
                        echo "$('#post_form_row_2').show();";
                    }
                    else
                    {
                        echo "$('#post_form_row_3').hide();";
                        echo "$('#post_form_row_2').hide();";
                    }
                ?>
                var timezone=get_timezone();
                $.post('page_query.php',
                {
                    num:4,
                    page: page,
                    month:month,
                    year:year,
                    page_id: <?php echo $ID; ?>,
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

                            $('#status_updates').html($('#status_updates').html()+"<div id='see_more_body'></div>");
                        }

                        for(var x = 0; x < posts.length; x++)
                        {
                            if(posts[x]!='')
                            {
                                posts[x]=convert_image(text_format(posts[x]), 'post');
                                var profile_picture="<a href='http://www.redlay.com/profile.php?user_id="+users_sent[x]+"' ><img class='profile_picture profile_picture_status' src='"+profile_pictures[x]+"' /></a>";
                                var name="<div class='user_name_body'><a class='link' href='http://www.redlay.com/profile.php?user_id="+users_sent[x]+"' ><span class='user_name' onmouseover='name_over(this);' onmouseout='name_out(this);'>"+names[x]+"</span></a></div>";
                                var post="<p class='status_update_text' >"+posts[x]+"</p>";

                                if(num_comments[x]>=1)
                                    var comment_text="<span id='comment_title_"+page+"_"+x+"' class='comment_text' >Comment ["+num_comments[x]+"]</span>";
                                else
                                    var comment_text="<span id='comment_title_"+page+"_"+x+"' class='comment_text' >Comment</span>";

                                if(<?php if((isset($_SESSION['page_id'])&&$ID==$_SESSION['page_id'])||(isset($_SESSION['id'])&&has_liked_page($_SESSION['id'], $ID))) echo "true"; else echo "false";  ?>==true)
                                    var comment_input="<div id='comment_text_"+page+"_"+x+"' class='comment_input_body'><textarea class='comment_textarea input_box' id='comment_input_"+page+"_"+x+"' placeholder='Comment...' maxlength='500' onFocus='input_in(this);' onBlur='input_out(this);'></textarea></div>";
                                else
                                    var comment_input="";

                                var comment_body="<div class='comment_body' id='comment_body_"+page+"_"+x+"'></div>";
                                var timestamp="<p class='timestamp_status_update' id='post_timestamp_"+post_ids[x]+"'>"+timestamps[x]+"</p>";
                                if(users_sent[x]==<?php if(isset($_SESSION['page_id'])) echo $_SESSION['page_id']; else echo "0"; ?>)
                                    var options="<div class='post_delete post_hide' id='post_options_"+post_ids[x]+"' onClick='show_post_options("+post_ids[x]+", <?php echo $ID; ?>);'>O</div>";
                                else
                                    var options="";


                                
                                if(users_sent[x]!=<?php if(isset($_SESSION['page_id']))echo $_SESSION['page_id']; else echo "0"; ?>)
                                {
                                    if(has_liked[x]==true)
                                        var like="<span class='status_update_like' id='post_like_"+page+"_"+x+"' onClick='unlike_post(<?php echo $ID; ?>, "+post_ids[x]+", "+users_sent[x]+", "+num_likes[x]+", "+page+", "+x+");' onmouseover=name_over(this); onmouseout=name_out(this); >Unlike ["+num_likes[x]+"]</span>";
                                    else if(num_likes[x]>=1)
                                        var like="<span class='status_update_like' id='post_like_"+page+"_"+x+"' onClick='like_post(<?php echo $ID; ?>, "+post_ids[x]+", "+users_sent[x]+", "+num_likes[x]+", "+page+", "+x+");' onmouseover=name_over(this); onmouseout=name_out(this); >Like ["+num_likes[x]+"]</span>";
                                    else
                                        var like="<span class='status_update_like' id='post_like_"+page+"_"+x+"' onClick='like_post(<?php echo $ID; ?>, "+post_ids[x]+", "+users_sent[x]+", "+num_likes[x]+", "+page+", "+x+");' onmouseover=name_over(this); onmouseout=name_out(this); >Like</span>";
                                }
                                else
                                {
                                    if(num_likes[x]==1)
                                        var like="<span class='status_update_like me' >1 like</span>";
                                    else if(num_likes[x]>=1)
                                        var like="<span class='status_update_like me' >"+num_likes[x]+" likes</span>";
                                    else
                                        var like="";
                                }


                                if(users_sent[x]!=<?php if(isset($_SESSION['page_id']))echo $_SESSION['page_id']; else echo "0"; ?>)
                                {
                                    if(has_disliked[x]==true)
                                        var dislike="<span class='status_update_dislike' id='post_dislike_"+page+"_"+x+"' onClick='undislike_post(<?php echo $ID; ?>, "+post_ids[x]+", "+users_sent[x]+", "+num_dislikes[x]+", "+page+", "+x+");' onmouseover=name_over(this); onmouseout=name_out(this); >Undislike ["+num_dislikes[x]+"]</span>";
                                   else if(num_dislikes[x]>=1)
                                        var dislike="<span class='status_update_dislike' id='post_dislike_"+page+"_"+x+"' onClick='dislike_post(<?php echo $ID; ?>, "+post_ids[x]+", "+users_sent[x]+", "+num_dislikes[x]+", "+page+", "+x+");' onmouseover=name_over(this); onmouseout=name_out(this); >Dislike ["+num_dislikes[x]+"]</span>";
                                    else
                                        var dislike="<span class='status_update_dislike' id='post_dislike_"+page+"_"+x+"' onClick='dislike_post(<?php echo $ID; ?>, "+post_ids[x]+", "+users_sent[x]+", "+num_dislikes[x]+", "+page+", "+x+");' onmouseover=name_over(this); onmouseout=name_out(this); >Dislike</span>";
                                }
                                else
                                {
                                    if(num_dislikes[x]==1)
                                        var dislike="<span class='status_update_dislike me' >1 dislike</span>";
                                    else if(num_dislikes[x]>=1)
                                        var dislike="<span class='status_update_dislike me' >"+num_dislikes[x]+" dislikes</span>";
                                    else
                                        var dislike="";
                                }


                                //styles like, dislike, and comment buttons
                                var post_functions=get_post_functions(like, dislike, comment_text);
                                var option_id="post_options_"+post_ids[x];


                                var body=get_post_format(profile_picture, name, post+post_functions, comment_input+comment_body, timestamp, options, option_id, 'post_'+page+'_'+post_ids[x], badges[x]);
                                $('#page_'+page).html($('#page_'+page).html()+body);
                                count_time(timestamp_seconds[x], '#post_timestamp_'+post_ids[x]);

                                $('#status_update_'+post_ids[x]).attr({'onmouseover': "show_close("+post_ids[x]+");", 'onmouseout': "hide_close("+post_ids[x]+");"});


                                    $('#post_options_'+post_ids[x]).attr({'onmouseover': "display_title(this, 'Display this post\'s options');", 'onmouseout': "hide_title(this);"});

//                                        $('#profile_picture_status_'+post_ids[x]).attr({'onmouseover': "display_title(this, '"+array[x]+"');", 'onmouseout': "hide_title(this);"});
                            }

                        }

                        //binds data for when user presses enter to post comment
                        for(var x = 0; x < posts.length; x++)
                        {
                            if(posts[x]!='')
                            {
                                $('#comment_input_'+page+'_'+x).data({'post_id': post_ids[x], 'index': x, 'poster_id': users_sent[x], 'page': page, 'num_comments': num_comments[x]});

                                //modifies "Comment" string
                                $('#comment_title_'+page+'_'+x).attr({'onClick': "{show_comment("+page+", "+x+");}", 'onmouseover': "{name_over(this); }", 'onmouseout': "{name_out(this); }"});

                                //adds number of comments as data
                                $('#comment_title_'+page+'_'+x).data({'number': num_comments});
                            }
                        }


                        if(empty==false&&page==1)
                        {
                            $('#see_more_body').html($('#see_more_body').html()+"<input class='button see_more_posts blue_button' value='See More' type='button'>");
                            $('.see_more_posts').attr({'onmouseover': "{display_title(this, 'See more posts');}", 'onmouseout': "{hide_title(this);}", 'onClick': "display_posts("+(page+1)+", '"+year+"', '"+month+"', '"+phrase+"', "+sort+");"});
                        }
                        else if(empty==true)
                            $('.see_more_posts').hide();
                        else
                            $('.see_more_posts').attr('onClick', "display_posts("+(page+1)+", '"+year+"', '"+month+"', '"+phrase+"', "+sort+");");





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

                                    if(comments_user_sent[x][y]==<?php if(isset($_SESSION['id']))echo $_SESSION['id']; else echo "0"; ?>||<?php echo $ID; ?>==<?php if(isset($_SESSION['page_id'])) echo $_SESSION['page_id']; else echo "0"; ?>)
                                        var option="<div class='comment_delete' id='comment_delete_"+page+"_"+x+"_"+y+"' onClick='delete_comment(<?php echo $ID; ?>, "+post_ids[x]+", "+x+", "+comment_ids[x][y]+", "+y+", "+page+");' >x</div>";
                                    else
                                        var option="";

                                    
                                    //displays likes
                                    if(comments_user_sent[x][y]!=<?php if(isset($_SESSION['id'])) echo $_SESSION['id']; else echo "0"; ?><?php if(isset($_SESSION['page_id'])) echo "&&1==0"; ?>)
                                    {
                                        if(comment_has_liked[x][y]==true)
                                            var like="<span class='comment_like' id='comment_like_"+page+"_"+x+"_"+y+"' onmouseover=name_over(this); onmouseout=name_out(this); >Unlike ["+comment_num_likes[x][y]+"]</span>";
                                        else if(comment_num_likes[x][y]>=1)
                                            var like="<span class='comment_like' id='comment_like_"+page+"_"+x+"_"+y+"' onmouseover=name_over(this); onmouseout=name_out(this); >Like ["+comment_num_likes[x][y]+"]</span>";
                                        else
                                            var like="<span class='comment_like' id='comment_like_"+page+"_"+x+"_"+y+"' onmouseover=name_over(this); onmouseout=name_out(this); >Like</span>";
                                    }
                                    else
                                    {
                                        if(comment_num_likes[x][y]==1)
                                            var like="<span class='comment_like' >1 like</span>";
                                        else if(comment_num_likes[x][y]>1)
                                            var like="<span class='comment_like' >"+comment_num_likes[x][y]+" likes</span>";
                                        else
                                            var like="";
                                    }

                                    //displays dislikes
                                    if(comments_user_sent[x][y]!=<?php if(isset($_SESSION['id'])) echo $_SESSION['id']; else echo "0"; ?><?php if(isset($_SESSION['page_id'])) echo "&&1==0"; ?>)
                                    {
                                        if(comment_has_disliked[x][y]==true)
                                            var dislike="<span class='comment_dislike' id='comment_dislike_"+page+"_"+x+"_"+y+"' onmouseover=name_over(this); onmouseout=name_out(this); >Undislike ["+comment_num_dislikes[x][y]+"]</span>";
                                        else if(comment_num_dislikes[x][y]>=1)
                                            var dislike="<span class='comment_dislike' id='comment_dislike_"+page+"_"+x+"_"+y+"' onmouseover=name_over(this); onmouseout=name_out(this); >Dislike ["+comment_num_dislikes[x][y]+"]</span>";
                                        else
                                            var dislike="<span class='comment_dislike' id='comment_dislike_"+page+"_"+x+"_"+y+"' onmouseover=name_over(this); onmouseout=name_out(this); >Dislike</span>";
                                    }
                                    else
                                    {
                                        if(comment_num_dislikes[x][y]==1)
                                            var dislike="<span class='comment_dislike' >1 dislike</span>";
                                        else if(comment_num_dislikes[x][y]>1)
                                            var dislike="<span class='comment_dislike' >"+comment_num_dislikes[x][y]+" dislikes</span>";
                                        else
                                            var dislike="";
                                    }





                                    var timestamp="<span class='comment_timestamp text_color' id='comment_timestamp_"+post_ids[x]+"_"+comment_ids[x][y]+"_"+y+"'>"+comment_timestamps[x][y]+"</span>";


                                        var functions=get_comment_functions(like, dislike);
                                    var option_id="comment_delete_"+page+"_"+x+'_'+y;

                                    //content1=picture+close+name+comment+functions+timestamp+comment_break+content1;
                                    var content=get_post_format(picture, name+comment+functions, '', '', timestamp, option, option_id, 'comment_body_'+page+'_'+x+'_'+y, comment_badges[x][y])+content;
                                }

                                $("#comment_body_"+page+"_"+x).html(content);


                                for(var y = 0; y < comments[x].length; y++)
                                {
                                    if(comment_has_liked[x][y])
                                        $('#comment_like_'+page+'_'+x+'_'+y).attr({'onClick': "unlike_comment(<?php echo $ID; ?>, "+post_ids[x]+", "+x+", "+comment_ids[x][y]+", "+y+", "+page+", "+comment_num_likes[x][y]+");"});
                                    else
                                        $('#comment_like_'+page+'_'+x+'_'+y).attr({'onClick': "like_comment(<?php echo $ID; ?>, "+post_ids[x]+", "+x+", "+comment_ids[x][y]+", "+y+", "+page+", "+comment_num_likes[x][y]+");"});

                                    if(comment_has_disliked[x][y])
                                        $('#comment_dislike_'+page+'_'+x+'_'+y).attr({'onClick': "undislike_comment(<?php echo $ID; ?>, "+post_ids[x]+", "+x+", "+comment_ids[x][y]+", "+y+", "+page+", "+comment_num_dislikes[x][y]+");"});
                                    else
                                        $('#comment_dislike_'+page+'_'+x+'_'+y).attr({'onClick': "dislike_comment(<?php echo $ID; ?>, "+post_ids[x]+", "+x+", "+comment_ids[x][y]+", "+y+", "+page+", "+comment_num_dislikes[x][y]+");"});
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
                        $('#status_updates').html("<p><?php if(isset($_SESSION['page_id'])&&$ID==$_SESSION['page_id']) echo "You have"; else echo $name." has"; ?> not posted anything</p>");
                        $('#post_load').hide();
                    }
                }, "json");
            }
            function display_fan_posts(page)
            {
                var timezone=get_timezone();
                $.post('page_query.php',
                {
                    num:16,
                    page: page,
                    page_id: <?php echo $ID; ?>
                }, function(output)
                {

                });
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
                $('#comment_title_'+page+'_'+index).attr("onClick", "hide_comment("+page+", "+index+");");
            }
            function hide_comment(page, index)
            {
                $('#comment_body_'+page+'_'+index).hide();
                $('#comment_input_'+page+'_'+index).hide();
                $('#comment_title_'+page+'_'+index).attr("onClick", "show_comment("+page+", "+index+");");
            }
            
            function display_information()
            {
                var timezone=get_timezone();
                $.post('page_query.php',
                {
                    num:15,
                    page_id: <?php echo $ID; ?>,
                    timezone:timezone
                }, function(output)
                {
                    var description=output.description;
                    var website=output.website;
                    var location=output.location;
                    var started=output.started;

                    $('#description_information').html(description);
                    $('#website_information').html(website);
                    $('#started_information').html(started);
                    $('#location_information').html(location);

                }, "json");
            }

            function display_pictures(number, page)
            {
                    $.post('page_query.php',
                    {
                        num:5,
                        number: number,
                        page_id: <?php echo $ID; ?>,
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
                                var image="<div class='image_preview_outside'><a href='http://www.redlay.com/view_photo.php?user_id=<?php echo $ID; ?>&&picture_id="+image_id[x]+"&&type=page'><img id='other_picture_"+x+"' class='other_pictures_picture' src='"+images[x]+"'/></a></div>";
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

                                $('#other_pictures').html($('#other_pictures').html()+"<div id='see_more_body'></div>");
                            }
                            
//                            if(images.length<5)
//                                var num_rows=1;
//                            else
//                                var num_rows=images.length/5;
                            if(!empty)
                                var num_rows=5;
                            else
                                var num_rows=total_size-(page*25);
                            
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
                                var image="<div class='image_preview_outside'><a href='http://www.redlay.com/view_photo.php?user_id=<?php echo $ID; ?>&&picture_id="+image_id[x]+"&&type=page'><img id='photo_"+page+"_"+x+"' class='other_pictures_picture' src='"+images[x]+"'/></a></div>";
                                $('#photos_page_table_unit_'+page+'_'+x).html(image);

//                                if(image_has_description[x]==true)
//                                    $('#other_picture_'+x).attr({'onmouseover': "display_title(this, '"+image_descriptions[x]+"');", 'onmouseout': "hide_title(this);"});
//                                else
//                                    $('#other_picture_'+x).attr({'onmouseover': "display_title(this, '<i>No Description...</i>');", 'onmouseout': " hide_title(this);"});
                                    $('#photos_'+page+'_'+x).attr({'onmouseover': "{$(this).css({'position': 'relative', 'top': '0px', 'z-index': '2', 'left': '0px'});$(this).stop().animate({width:'150px', top: '-9px',left:'-9px'}, 100, function(){});}", 'onmouseout': "$(this).stop().animate({top: '0px',width:'130px',left:'0px'}, 100, function(){$(this).css({'position': 'relative', 'top':'', 'z-index': '1','left':''});});"});
                            }
                            $('.image_preview_outside').css({'height': '130px', 'width': '130px'});
                            $('.other_pictures_picture').css({'border': '2px solid <?php echo $color; ?>', 'width': '130px'});
                            
                            
                            //modifies, creates, or deletes see_more button
                            if($('.see_more_posts').length!=0&&empty==false)
                                $('.see_more_posts').attr({'onClick': "display_pictures("+number+", "+(page+1)+");"});
                            else if(empty==false)
                            {
                                $('#see_more_body').html("<input class='button see_more_posts blue_button' id='see_more_post_button' value='See More' type='button' >");
                                $('#see_more_post_button').attr({'onmouseover': "{display_title(this, 'See more posts');}", 'onmouseout': "{hide_title(this);}", 'onClick': "{display_pictures("+number+", "+(page+1)+");}"});
                            }
                            else
                                $('#see_more_body').html('');
                        }
                        
                        
                        change_color();
                        $('#photo_load').hide();
                    }, "json");
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
                  $('#post_button').attr({'onClick': "update_status();"});

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


                    var profile_picture="<img class='profile_picture_status profile_picture' src='<?php echo get_page_profile_picture($_SESSION['page_id']);
                    ?>' id='text_format_profile_picture' />";
                    var text="<p class='status_update_text text_color' id='text_format_text' style='width:315px;'></p>";
                    var name="<div class='user_name_body'><span class='user_name' id='text_format_preview_name'><?php echo get_page_name($_SESSION['page_id']); ?></span></div>";

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
            
        </script>
        <script type="text/javascript">
            $(window).ready(function()
            {
//                display_activity(1);
                display_information();
//                $('#add_video').hide();
                initialize_video_input();
                initialize_post_preview();
//                <?php
//                    if(isset($_SESSION['page_id'])&&$ID==$_SESSION['page_id'])
//                        echo "display_groups('post_audience_selection_box');";
//                    else
//                        echo "$('#category_form_unit').html('');";
                ?>//
//                
//                display_adds();
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
                    $path=get_page_background_pic($ID);
                    if(file_exists_server($path)==true)
                        echo "$('body').css({'background-image': 'url(\'$path\')', 'background-position' :'center 50px'});";
                    else
                        echo "$('body').css({'background-image': 'url(\'".get_default_background_pic($redlay_theme)."\')', 'background-position' :'center 50px'});";
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
                    echo "<div id='top'>";
                    include('top.php');
                    echo "</div>";
                }
                else  if(isset($_SESSION['page_id']))
                {
                    echo "<div id='top'>";
                    include('top_page.php');
                    echo "</div>";
                }
                else
                    include('index_top.php');
            ?>
        <div id="main" >
            <?php if(!isset($_SESSION['page_id'])) include('required_side_html.php'); ?>
            
            <table style="position:relative;margin:0 auto;border-spacing:10px 0px;top:100px;border-spacing:0px;">
                <tbody>
                    <tr>
                        <td id="page_left" style="vertical-align:top;width:210px" >
                            <table id="page_left_table">
                                <tbody>
                                    <tr>
                                        <td>
                                            <?php
                                                if(isset($_SESSION['id']))$ID2=0; else if(isset($_SESSION['page_id'])) $ID2=$_SESSION['page_id']; else $ID2=0;
                                                if($ID==$ID2)
                                                    $string2="http://www.redlay.com/page_home.php";
                                                else
                                                    $string2="http://www.redlay.com/page.php?user_id=".$ID;
                                            ?>
                                            <a href="<?php echo $string2; ?>"><img id="profile_pic" src="<?php echo get_page_profile_picture($ID); ?>" alt="http://pics.redlay.com/pictures/default_profile_picture.png"/></a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td id="profile_menu">
                                            <table id="profile_menu_table" style="width:100%;padding:10px;">
                                                <tr class="profile_menu_row">
                                                    <td class="profile_menu_item selected" id="profile_menu_1" onClick="profile_menu_item(1);" onmouseover="{$(this).css('text-decoration', 'underline');}" onmouseout="{$(this).css('text-decoration', 'none');}">All</td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <hr class="break" />
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="profile_menu_item" id="profile_menu_2" onClick="profile_menu_item(2);" onmouseover="{$(this).css('text-decoration', 'underline');}" onmouseout="{$(this).css('text-decoration', 'none');}"> Posts</td>
                                                </tr>
                                                <tr>
                                                    <td class="profile_menu_item" id="profile_menu_3" onClick="profile_menu_item(3);" onmouseover="{$(this).css('text-decoration', 'underline'); }" onmouseout="{$(this).css('text-decoration', 'none');}">Photos</td>
                                                </tr>
                                                <tr>
                                                    <td class="profile_menu_item" id="profile_menu_4" onClick="profile_menu_item(4);" onmouseover="{$(this).css('text-decoration', 'underline'); }" onmouseout="{$(this).css('text-decoration', 'none');}">Videos</td>
                                                </tr>
                                                <tr>
                                                    <?php
                                                        if($calendar_visible==true)
                                                        {
                                                            echo "<td class='profile_menu_item' id='profile_menu_9' onClick='profile_menu_item(9);' onmouseover=name_over(this); onmouseout=name_out(this); >Calendar</td>\n";
                                                            echo "</tr><tr>";
                                                        }
                                                    ?>
        <!--                                            <td class="profile_menu_item" id="profile_menu_7" onClick="profile_menu_item(7);" onmouseover="{$(this).css('text-decoration', 'underline'); display_title(this, 'Displays all pages liked');}" onmouseout="{$(this).css('text-decoration', 'none'); hide_title(this);}">Likes</td>
                                                    <td class="profile_menu_seperator" >|</td>-->
        <!--                                            <td class="profile_menu_item" id="profile_menu_8" onclick="profile_menu_item(8);" onmouseover="{$(this).css('text-decoration', 'underline'); display_title(this, 'Displays this user\'s documents');}" onmouseout="{$(this).css('text-decoration', 'none'); hide_title(this);}">Docs</td>
                                                    <td class="profile_menu_seperator" >|</td>-->
                                                    <td class="profile_menu_item" id="profile_menu_5" onClick="profile_menu_item(5);" onmouseover="{$(this).css('text-decoration', 'underline'); }" onmouseout="{$(this).css('text-decoration', 'none');}">Info</td>
                                                    <!--<td class="profile_menu_seperator" >|</td>-->
                                                    <!--<td class="profile_menu_item" id="profile_menu_6" onClick="profile_menu_item(6);" onmouseover="{$(this).css('text-decoration', 'underline'); display_title(this, 'Displays most popular photo and post');}" onmouseout="{$(this).css('text-decoration', 'none'); hide_title(this);}">Popular</td>-->
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                    <?php
                                        if($has_gold)
                                        {
                                            //echo "<tr><td><div id='redlay_gold_box' class='box redlay_gold_box'><a class='link' href='http://www.redlay.com/redlay_gold.php' ><p id='gold_description' onmouseover=name_over(this); onmouseout=name_out(this); >Gold Member</p></a></div></td></tr>";
                                        }
                                    ?>
                                    <tr>
                                        <td id="options">
                                            <table id="options_table">

                                            </table>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td id="page_information_box">
                                            <?php $string=get_information_title($ID); ?>
                                            <div id="information_content">
                                                <table id="information_table">
                                                    <tbody>
                                                        <tr>
                                                            <td>
                                                                <span class="title_color">About</span>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <span class="text_color" id="description_information"></span>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <span class="title_color">Website</span>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <span class="text_color" id="website_information"></span>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <span class="title_color">Started</span>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <span class="text_color" id="started_information"></span>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <span class="title_color">Location</span>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <span class="text_color" id="location_information"></span>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                                
                                
                                
                                
                            </table>
                        </td>
                        <td id="page_middle" style="vertical-align:top;width:520px">
                            <table id="page_middle_table">
                                <tbody>
                                    <tr>
                                        <td id="name_box">
                                            <p id="user_name"><?php echo $name ?></p>
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
                                                                                <span id="update_title" class="profile_text">Posts</span>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>
                                                                                <table id="page_post_menu">
                                                                                    <tbody>
                                                                                        <tr>
                                                                                            <td style="border-right:1px solid gray;padding-right:5px;">
                                                                                                <span class="text_color" onmouseover="name_over(this);" onmouseout="name_out(this);" onClick="display_posts(1, 'all', 'all', 1);" style="font-size:12px;">Page</span>
                                                                                            </td>
                                                                                            <td style="padding-left:3px;">
                                                                                                <span class="text_color" onmouseover="name_over(this);" onmouseout="name_out(this);" onClick="display_fan_posts(1);" style="font-size:12px;">Fans</span>
                                                                                            </td>
                                                                                        </tr>
                                                                                    </tbody>
                                                                                </table>
                                                                            </td>
                                                                        </tr>
                                                                        <tr class="post_form_row" id="post_form_row_2">
                                                                            <td class="post_form_unit" colspan="2">
                                                                                <textarea autofocus id="social_update" class="input_box" onFocus="input_in(this);" onBlur="input_out(this);" name="social_update" maxlength="500" placeholder="What's up?" ></textarea>
                                                                            </td>
                                                                        </tr>
                                                                        <tr class="post_form_row" id="post_form_row_4" style="text-align:left;" >
                                                                            <td colspan="2">
                                                                                <div class="post_preview_box">




                                                                                    <div id="post_preview_status_update" class="status_update" style="margin:5px">


                                                                                        <table style="width:100%;">
                                                                                            <tbody>
                                                                                                <tr id="post_preview_row_1" class="post_row">
                                                                                                    <td class="post_profile_picture_unit">
                                                                                                        <img class="profile_picture_status profile_picture" src="<?php echo get_page_profile_picture($_SESSION['page_id']); ?>" id="post_preview_profile_picture" />
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
                                                                                <input class="button submit_button red_button" id="social_submit_button" onClick="{post_page();}" type="button" name="social_update_submit" value="Post" />
                                                                            </td>
                                                                        </tr>
                                                                    </table>

                                                                    <div id="post_load"><img class="load_gif" src='http://pics.redlay.com/pictures/load.gif'/></div>
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
                        <td id="page_right" style="vertical-align:top;">
                            <table id="page_right_table">
                                <tbody>
                                    <tr>
                                        <td id="account_activity_box">
                                            <p id="account_activity_title" class="title title_color">Activity</p>
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
                            page_comment(<?php echo $ID; ?>, $(this).data('poster_id'), $(this).data('post_id'), $(this).data('index'), $(this).data('page'), $(this).data('num_comments'));
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