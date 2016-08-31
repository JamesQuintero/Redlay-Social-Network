<?php
@include('init.php');
if(!isset($_SESSION['id']))
{
    header("Location: http://m.redlay.com");
    exit();
}

$ID=(int)($_GET['user_id']);
include('../universal_functions.php');
include('security_checks.php');

//gets the user's privacy preferences
$privacy=get_user_privacy_settings($ID);
$general=$privacy[0];
$display_non_friends=$privacy[1];
$has_gold=has_redlay_gold($ID, 'all');
$user_is_friends=user_is_friends($ID, $_SESSION['id']);


?>
<html>
    <head>
        <title><?php echo get_user_name($ID); ?></title>
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
                $('.box').css({'border': '15px solid <?php echo $color; ?>', 'background-color': '<?php echo $box_background_color; ?>'});

                $('.profile_menu_text, .table_title, .table_data, .status_update_text, .timestamp_status_update, .body_text').css('color', '<?php echo $text_color; ?>');
                $('.status_update_like, .status_update_dislike, .comment_text, .comment_like, .comment_dislike, .title_text').css('color', '<?php echo $color; ?>');
                <?php if($has_gold['badge']) echo "$('#profile_menu, #information_content').css('border-color', 'rgb(252,178,0)');"; ?>
            }
            
            //displays appropriate buttons
            $(document).ready(function()
            {
                if(<?php echo $user_is_friends; ?>==true)
                {
                    $('#profile_options_table').html("<td><input class='green_button' type='button' value='Message' onClick='display_message_box();'/></td>");
                    $('#profile_options_table').html($('#profile_options_table').html()+"<td><input class='green_button' type='button' value='Group Options' onClick='display_group_options_box();' /></td>");
                    $('#profile_options_table').html($('#profile_options_table').html()+"<td><input class='green_button' value='Delete' onClick='delete_user();' type='button' /></td>");
                }
                else if(<?php echo $_SESSION['id']; ?>!=<?php echo $ID; ?>)
                {
                    $('#profile_options_table').html("<td><input class='green_button' type='button' value='Add' onClick='display_add_menu();' /></td>");
                    $('#profile_options_table').html($('#profile_options_table').html()+"<td><input class='green_button' type='button' value='Block' onClick='block_user();'/></td>");
                }
                else if(<?php echo $_SESSION['id']; ?>==<?php echo $ID; ?>)
                {
                    $('#profile_options_table').html("<td><input class='green_button' type='button' value='Upload Photo' onClick='display_photo_upload_box();'/></td>")
                }
            });
            function display_message_box()
            {
                var body="<textarea  id='message_body' class='input_box' maxlength='1000' placeholder='Send a message...' style='width:100%;height:350px;'></textarea>";
                var confirm="<input type='button' class='green_button' onClick=message(); value='Send' />";
                display_alert("Message", body, 'message_extra_unit', 'message_gif', confirm);
                $('#message_body').attr({'onFocus': "input_in(this);", 'onBlur': "input_out(this);"});

                
                $('.alert_box_table').css('width', '770px');
                $('#message_gif').hide();
                change_color();
            }
            
            function display_photo_upload_box()
            {
                display_dim();
                $('.alert_box_inside').html("<form method='post' action='upload_picture.php' enctype='multipart/form-data' style='margin-bottom:0px;'><table class='alert_box_table' id='upload_photo_table'><tbody><tr class='alert_box_row' id='upload_photo_row_1'></tr><tr class='alert_box_row' id='upload_photo_row_2'></tr><tr class='alert_box_row' id='upload_photo_row_3'></tr><tr class='alert_box_row' id='upload_photo_row_5' ></tr><tr class='alert_box_row' id='upload_photo_row_4' ></tr></tbody></table></form>");
                    $('#upload_photo_row_1').html("<td class='upload_photo_unit alert_box_title_unit' colspan='4'><p class='alert_box_title' class='title_text'>Upload a photo</p></td>");
                    $('#upload_photo_row_2').html("<td class='upload_photo_unit' colspan='3'><input type='file' id='photo_upload_button' class='file_input' name='image' style='font-size:25px;'/></td><?php if(has_redlay_gold($_SESSION['id'], 'photo_quality')) echo "<td ><table style='width:100%;text-align:right;'><tbody><tr><td><span style='font-size:35px;' class='text_color'>Max quality</span></td><td><input type='checkbox' id='quality_checkbox' name='photo_quality' value='yes'/></td></tr></tbody></table></td>"; ?>");
                    $('#upload_photo_row_3').html("<td class='upload_photo_unit' colspan='4'><textarea name='upload_picture_description' id='upload_picture_description' class='input_box' maxlength='1000' placeholder='Describe the photo...'></textarea></td>");
                        $('#upload_picture_description').attr({'onFocus': "input_in(this);", 'onBlur': "input_out(this);"});
                    $('#upload_photo_row_4').html("<td colspan='4'><table style='width:100%;'><tbody><tr><td class='upload_photo_unit alert_box_confirmation_row_unit_left'><div class='select_box' id='photo_audience_box'></div></td><td class='upload_photo_unit alert_box_load_unit'><img class='load_gif' id='upload_photo_gif' src='load.gif'/></td><td class='upload_photo_unit alert_box_confirm_unit' ><div id='cover_upload_button' style=''></div><input type='submit' class='green_button' id='photo_upload_submit' value='Upload' /></td><td class='upload_photo_unit alert_box_cancel_unit' ><input type='button' class='gray_button' id='photo_upload_cancel' onClick=close_alert_box(); value='Close' /></td></tr></tbody></table></td>");
                        display_groups('photo_audience_box');
                        $('#upload_photo_gif').hide();
                    $('#upload_photo_row_5').html("<td colspan='4'><p id='photo_upload_message'></p><div id='upload_photo_preview'></div></td>");
                    $('#photo_upload_message').hide();

                    disable_photo_upload();
                    $('#photo_upload_submit').attr('onClick', "{$('#upload_photo_gif').show();disable_photo_upload();}");
                    
                    show_alert_box();
                change_color();
            }
            function disable_photo_upload()
            {
                $('#photo_upload_submit').css('opacity', '0.5');
                $('#photo_upload_button').attr('onChange', "undisable_photo_upload();");
                $('#cover_upload_button').css({'width': '150px', 'height': '60px'});
            }
            function undisable_photo_upload()
            {
                $('#cover_upload_button').css({'width': '0px', 'height': '0px'});
                $('#photo_upload_submit').css('opacity', '1');
            }
            
            function display_group_options_box()
            {
                
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
            
            function message()
            {
                $.post('main_access.php', 
                {
                    access:38,
                    user_id: <?php echo $ID; ?>,
                    message: $('#message_body').val()
                }, function(output)
                {
                    if(output=='Message sent!')
                    {
                        display_error(output, 'good_errors');
                        close_alert_box();
                    }
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
            
            function display_videos(page)
            {
                hide_everything();
                $('#videos').show();
                
                $.post('main_access.php',
                {
                    access:1,
                    num:2,
                    page:page,
                    user_id: <?php echo $ID; ?>
                }, function(output)
                {
                    var videos=output.videos;
                    var empty=output.empty;
                    var size=output.size;
                    var total_size=output.total_size;

                    if(page==1)
                    {
                        $('#videos').html("");
                        if(total_size>=5)
                        {
                            for(var x = 1; x < total_size/5+1; x++)
                                $('#videos').html($('#videos').html()+"<div id='video_page_"+x+"'><table id='videos_table'><tbody id='video_page_body_"+x+"'></tbody></table></div>");
                        }
                        else
                            $('#videos').html("<div id='video_page_1'><table><tbody id='video_page_body_1'></tbody></table></div>");
                        $('#videos').html($('#videos').html()+"<input class='see_more_posts see_more_videos blue_button' value='See More' type='button' id='see_more_videos'>");
                    }

                    for(var x = 0; x < size; x++)
                        $('#video_page_body_'+page).html($('#video_page_body_'+page).html()+"<tr class='video_row' id='video_row_"+page+"_"+x+"'><td id='video_unit_"+page+"_"+x+"_1'></td><td id='video_unit_"+page+"_"+x+"_2'></td></tr>");
                    
                    //displays actual videos
                    for(var x = 0; x < size; x++)
                        $('#video_unit_'+page+'_'+x+'_1').html("<iframe width='570' height='315' src='"+videos[x]+"?wmode=transparent' frameborder='0' allowfullscreen></iframe>");

                    //changes see more button if necessary
                    if(empty)
                        $('#see_more_videos').attr('onClick', '').hide();
                    else
                        $('#see_more_videos').attr('onClick', 'display_videos('+(page+1)+');');

                    //inserts form to add video
                    initialize_video_input();
                    
                    
                    $('#video_load').hide();
                }, "json");
            }
            function display_likes(page)
            {
//                hide_everything();
//                $('#likes').show();
//                
//                
//                
            }
            function display_info()
            {
                hide_everything();
                $('#info').show();
                
                $.post('get_information.php',
                {
                    user_id:<?php echo $ID; ?>
                }, function(output)
                {
                    var num_adds=output.num_adds;
                    var num_videos=output.num_videos;
                    var relationship=output.relationship;
                    var birthday=output.birthday;
                    var gender=output.gender;
                    var bio=output.bio;
                    var high_school=output.high_school;
                    var college=output.college;
                    var country=output.country;
                    var city=output.city;
                    var work=output.work;
                    var mood=output.mood;
                    
                    $('#info').html("<table style='width:100%;font-size:25px;'><tbody id='info_table_body'></tbody></table>");
                    $('#info_table_body').html($('#info_table_body').html()+"<tr><td class='info_title_unit'><span class='title_text'>Adds: </span></td><td class='info_body_unit'><span class='body_text'>"+num_adds+"</span></td></tr>");
                    $('#info_table_body').html($('#info_table_body').html()+"<tr><td class='info_title_unit'><span class='title_text'>Videos: </span></td><td class='info_body_unit'><span class='body_text'>"+num_videos+"</span></td></tr>");
                    $('#info_table_body').html($('#info_table_body').html()+"<tr><td class='info_title_unit'><span class='title_text'>Relationship: </span></td><td class='info_body_unit'><span class='body_text'>"+relationship+"</span></td></tr>");
                    $('#info_table_body').html($('#info_table_body').html()+"<tr><td class='info_title_unit'><span class='title_text'>Birthday: </span></td><td class='info_body_unit'><span class='body_text'>"+birthday+"</span></td></tr>");
                    $('#info_table_body').html($('#info_table_body').html()+"<tr><td class='info_title_unit'><span class='title_text'>Gender: </span></td><td class='info_body_unit'><span class='body_text'>"+gender+"</span></td></tr>");
                    $('#info_table_body').html($('#info_table_body').html()+"<tr><td class='info_title_unit'><span class='title_text'>Bio: </span></td><td class='info_body_unit'><span class='body_text'>"+bio +"</span></td></tr>");
                    $('#info_table_body').html($('#info_table_body').html()+"<tr><td class='info_title_unit'><span class='title_text'>High School: </span></td><td class='info_body_unit'><span class='body_text'>"+high_school+"</span></td></tr>");
                    $('#info_table_body').html($('#info_table_body').html()+"<tr><td class='info_title_unit'><span class='title_text'>College: </span></td><td class='info_body_unit'><span class='body_text'>"+college+"</span></td></tr>");
                    $('#info_table_body').html($('#info_table_body').html()+"<tr><td class='info_title_unit'><span class='title_text'>Country: </span></td><td class='info_body_unit'><span class='body_text'>"+country+"</span></td></tr>");
                    $('#info_table_body').html($('#info_table_body').html()+"<tr><td class='info_title_unit'><span class='title_text'>City: </span></td><td class='info_body_unit'><span class='body_text'>"+city+"</span></td></tr>");
                    $('#info_table_body').html($('#info_table_body').html()+"<tr><td class='info_title_unit'><span class='title_text'>Work: </span></td><td class='info_body_unit'><span class='body_text'>"+work+"</span></td></tr>");
                    $('#info_table_body').html($('#info_table_body').html()+"<tr><td class='info_title_unit'><span class='title_text'>Mood: </span></td><td class='info_body_unit'><span class='body_text'>"+mood+"</span></td></tr>");
                    
                    change_color();
                }, "json");
            }
            function delete_user()
            {
                $.post('main_access.php',
                {
                    access:21,
                    user_id: <?php echo $ID; ?>
                }, function (output)
                {
                    window.location.replace(window.location);
                });
            }
            function display_posts(page, year, month, phrase, sort)
            {
                hide_everything();
                $('#status_updates').show();
                var timezone=get_timezone();
                if(<?php if($display_non_friends[2]=='yes'||$ID==$_SESSION['id']||$user_is_friends=='true') echo "true"; else echo "false"; ?>==true)
                {
                    $.post('main_access.php',
                    {
                        access:1,
                        num:4,
                        page: page,
                        month:month,
                        year:year,
                        phrase: phrase,
                        user_id: <?php echo $ID; ?>,
                        timezone:timezone,
                        sort:sort
                    }, function (output)
                    {
                        var array=output.names;
                        var array2=output.users_sent;
                        var array3=output.users_sent_pictures
                        var array4=output.posts;
                        var array6=output.number_likes;
                        var array8=output.number_dislikes;
                        var likes_id=output.likes;
                        var dislikes_id=output.dislikes;
                        var user_colors=output.user_colors;
                        var comments=output.comments;
                        var like_names=output.post_like_names;
                        var dislike_names=output.dislike_names;
                        var post_ids=output.post_ids;
                        var empty=output.empty;
                        var size=output.size;
                        var total_size=output.total_size;
                        var time_since=output.time_since;


                        if(total_size!=0)
                        {
                            //displays the HTML template that will be used to display current and future posts
                            if(page==1)
                            {
                                $('#posts').html('');
                                for(var x = 1; x <= (total_size/10)+1; x++)
                                    $('#posts').html($('#posts').html()+"<div class='profile_post_page' id='page_"+x+"'></div>");

                                if(total_size<10)
                                    $('#posts').html("<div class='profile_post_page' id='page_1'></div>");

                                $('#posts').html($('#posts').html()+"<div id='see_more_body'></div>");
                            }

                            for(var x = 0; x < size; x++)
                            {
                                array4[x]=text_format(array4[x]);
                                    var image="<a href='http://m.redlay.com/profile.php?user_id="+array2[x]+"' class='link' ><img class='profile_picture_status profile_picture' src='http://www.redlay.com/"+array3[x]+"' id='profile_picture_status_"+post_ids[x]+"' /></a>";
                                    var name="<div class='user_name_body'><a class='user_name_link' href='http://m.redlay.com/profile.php?user_id="+array2[x]+"' ><span class='user_name' id='post_name_"+post_ids[x]+"' >"+array[x]+"</span></a></div>";
                                    var string="<p class='status_update_text text_color' >"+array4[x]+"</p>";

                                    if(comments.length>x&&comments[x][0]!='')
                                        var comment_text="<span id='comment_title_"+page+"_"+post_ids[x]+"' class='comment_text' >Comment ["+comments[x].length+"]</span>";
                                    else
                                        var comment_text="<span id='comment_title_"+page+"_"+post_ids[x]+"' class='comment_text' >Comment</span>";

                                    var comment_input="<div id='comment_text_"+page+"_"+post_ids[x]+"' class='comment_input_body'><textarea class='comment_textarea input_box' id='comment_input_"+page+"_"+post_ids[x]+"' placeholder='Comment...' maxlength='500'></textarea></div>";
                                    var comment_body="<div class='like_body' id='like_body_"+post_ids[x]+"'><hr /><p class='user_name'>Likes: </p></div><div class='dislike_body' id='dislike_body_"+post_ids[x]+"'><hr /><p class='user_name'>Dislikes: </p></div><div class='comment_body' id='comment_body_"+page+"_"+post_ids[x]+"'></div>";
                                    var timestamp="<p class='timestamp_status_update' >"+time_since[x]+"</p>";
                                    if(array2[x]==<?php if(isset($_SESSION['page_id']))echo '0'; else echo $_SESSION['id']; ?>||<?php if(isset($_SESSION['page_id']))echo '0'; else echo $_SESSION['id']; ?>==<?php echo $ID; ?>)
                                        var options="<div class='post_delete post_hide' id='post_options_"+post_ids[x]+"' onClick='show_post_options("+post_ids[x]+");'>O</div>";
                                    else
                                        var options="";
                                    var bool=false;
                                    for(var y = 0; y < likes_id[x].length; y++)
                                    {
                                        if(likes_id[x][y]==<?php if(isset($_SESSION['page_id']))echo '0'; else echo $_SESSION['id']; ?>)
                                            bool=true;
                                    }
                                    var bool2=false;
                                    for(var y = 0; y < dislikes_id[x].length; y++)
                                    {
                                        if(dislikes_id[x][y]==<?php if(isset($_SESSION['page_id']))echo '0'; else echo $_SESSION['id']; ?>)
                                            bool2=true;
                                    }
                                    var my_post=false;
                                        if(array2[x]!=<?php if(isset($_SESSION['page_id']))echo '0'; else echo $_SESSION['id']; ?>)
                                        {
                                            if(bool==true)
                                                
                                                var like="<span class='status_update_like' id='post_like_"+page+"_"+post_ids[x]+"' onClick='unlike_post("+post_ids[x]+", "+array2[x]+", "+array6[x]+", "+page+");'>Unlike ["+array6[x]+"]</span>";
                                            if(bool==false && array6[x]!=0)
                                                var like="<span class='status_update_like' id='post_like_"+page+"_"+post_ids[x]+"' onClick='like_post("+post_ids[x]+", "+array2[x]+", "+array6[x]+", "+page+");' >Like ["+array6[x]+"]</span>";
                                            else if(array6[x]==0)
                                                var like="<span class='status_update_like' id='post_like_"+page+"_"+post_ids[x]+"' onClick='like_post("+post_ids[x]+", "+array2[x]+", "+array6[x]+", "+page+");' >Like</span>";
                                        }
                                        if(array2[x]==<?php if(isset($_SESSION['page_id']))echo '0'; else echo $_SESSION['id']; ?>||<?php if(isset($_SESSION['page_id']))echo '0'; else echo $_SESSION['id']; ?>==0)
                                        {
                                            if(array6[x]==1)
                                                var like="<span class='status_update_like me' id='post_like_"+page+"_"+post_ids[x]+"'  >1 like</span>";
                                            else if(array6[x]!=0)
                                                var like="<span class='status_update_like me' id='post_like_"+page+"_"+post_ids[x]+"'  >"+array6[x]+" likes</span>";
                                            else
                                                var like="";
                                            my_post=true;
                                        }


                                        if(array2[x]!=<?php if(isset($_SESSION['page_id']))echo '0'; else echo $_SESSION['id']; ?>)
                                        {
                                            if(bool2==true)
                                                var dislike="<span class='status_update_dislike' id='post_dislike_"+page+"_"+post_ids[x]+"' onClick='undislike_post("+post_ids[x]+", "+array2[x]+", "+array8[x]+", "+page+");' >Undislike ["+array8[x]+"]</span>";
                                            if(bool2==false&&array8[x]!=0)
                                                var dislike="<span class='status_update_dislike' id='post_dislike_"+page+"_"+post_ids[x]+"' onClick='dislike_post("+post_ids[x]+", "+array2[x]+", "+array8[x]+", "+page+");' >Dislike ["+array8[x]+"]</span>";
                                            else if(array8[x]==0)
                                                var dislike="<span class='status_update_dislike' id='post_dislike_"+page+"_"+post_ids[x]+"' onClick='dislike_post("+post_ids[x]+", "+array2[x]+", "+array8[x]+", "+page+");' >Dislike</span>";
                                        }

                                        //dislike button if user is same as owner of the post
                                        if(array2[x]==<?php if(isset($_SESSION['page_id']))echo '0'; else echo $_SESSION['id']; ?>||<?php if(isset($_SESSION['page_id']))echo '0'; else echo $_SESSION['id']; ?>==0)
                                        {
                                            if(array8[x]==1)
                                                var dislike="<span class='status_update_dislike me' id='post_dislike_"+page+"_"+post_ids[x]+"' >1 dislike</span>";
                                            else if(array8[x]!=0)
                                                var dislike="<span class='status_update_dislike me' id='post_dislike_"+page+"_"+post_ids[x]+"' >"+array8[x]+" dislikes</span>";
                                            else
                                                var dislike="";
                                        }

                                    //styles like, dislike, and comment buttons
                                    var functions="<table class='post_functions_table'><tbody><tr><td class='post_functions_unit'>"+like+"</td><td class='post_functions_unit'>"+dislike+"</td><td class='post_functions_post_comment_unit'>"+comment_text+"</td></tr></tbody></table>";

                                    var body=get_post_format(image, name, string+functions, comment_input+comment_body, timestamp, options, 'post_options_'+post_ids[x], 'status_update_'+post_ids[x]);
                                    $('#page_'+page).html($('#page_'+page).html()+body);
//                                    var content=$('#page_'+page).html();
//                                    $('#page_'+page).html(content+image+options+name+string+functions+comment_input+comment_body+timestamp+post_break);



                                    //adds users to like_body
                                    for(var y= 0; y < array6[x]; y++)
                                    {
                                        var person_likes="<div class='display_like' id='display_like_"+x+"'>     <a href='http://www.redlay.com/profile.php?user_id="+likes_id[x][y]+"'><img class='profile_picture_status profile_picture like_profile_picture' src='http://www.redlay.com/users/thumbs/users/"+likes_id[x][y]+"/0.jpg' id='picture_like_"+post_ids[x]+"' /></a>      <div class='display_like_name'><a class='user_name_link' href='http://m.redlay.com/profile.php?user_id="+likes_id[x][y]+"'><p class='user_name like_user_name' id='display_like_name_"+post_ids[x]+"'>"+like_names[x][y]+"</p></a></div></div>";
                                        $('#like_body_'+post_ids[x]).html($('#like_body_'+post_ids[x]).html()+person_likes);
                                    }

                                    for(var y =0; y < array8[x]; y++)
                                    {
                                        var person_dislikes="<div class='display_dislike' id='display_dislike_"+x+"'>     <a href='http://www.redlay.com/profile.php?user_id="+dislikes_id[x][y]+"'><img class='profile_picture_status profile_picture like_profile_picture' src='http://www.redlay.com/users/thumbs/users/"+dislikes_id[x][y]+"/0.jpg' id='picture_dislike_"+post_ids[x]+"' /></a>      <div class='display_dislike_name'><a class='user_name_link' href='http://m.redlay.com/profile.php?user_id="+dislikes_id[x][y]+"'><p class='user_name dislike_user_name' id='display_dislike_name_"+post_ids[x]+"' >"+dislike_names[x][y]+"</p></a></div></div>";
                                        $('#dislike_body_'+post_ids[x]).html($('#dislike_body_'+post_ids[x]).html()+person_dislikes);
                                    }

                                    //colors of the posts depending on the user's preferences
                                    if(array2[x]==user_colors[0])
                                        $("#status_update_"+post_ids[x]).css('background-color', 'rgb(150,0,0)');
                                    else if(array2[x]==user_colors[1])
                                        $("#status_update_"+post_ids[x]).css('background-color', 'lightorange');
                                    else if(array2[x]==user_colors[2])
                                        $("#status_update_"+post_ids[x]).css('background-color', 'yellow');
                                    else if(array2[x]==user_colors[3])
                                        $("#status_update_"+post_ids[x]).css('background-color', 'rgba(0,200,0, .7)');
                                    else if(array2[x]==user_colors[4])
                                        $("#status_update_"+post_ids[x]).css('background-color', 'rgba(0,100,230,.9)');
                                    else if(array2[x]==user_colors[5])
                                        $("#status_update_"+post_ids[x]).css('background-color', 'lightpurple');
                                    else if(array2[x]==user_colors[6])
                                        $("#status_update_"+post_ids[x]).css('background-color', 'lightpink');
                            }

                            //binds data for when user presses enter to post comment
                            for(var x = 0; x < size; x++)
                            {
                                $('#comment_input_'+page+'_'+post_ids[x]).data({'post_id': post_ids[x], 'index': x, 'poster_id': array2[x], 'page': page});
                                $('#comment_title_'+page+'_'+post_ids[x]).attr({'onClick': "{show_comment("+page+", "+post_ids[x]+");}"});

                                if(comments[x]!='')
                                    $('#comment_title_'+page+'_'+post_ids[x]).data({'number': comments[x].length});
                                else
                                    $('#comment_title_'+page+'_'+post_ids[x]).data({'number': 0});
                            }


                            if(empty==false&&page==1)
                            {
                                $('#see_more_body').html($('#see_more_body').html()+"<input class='see_more_posts blue_button'  value='See More' type='button'>");
                                $('.see_more_posts').attr({'onClick': "display_posts("+(page+1)+", '"+year+"', '"+month+"', '"+phrase+"');"});
                            }
                            else if(empty==true)
                                $('.see_more_posts').hide();
                            else
                                $('.see_more_posts').attr('onClick', "display_posts("+(page+1)+", '"+year+"', '"+month+"', '"+phrase+"');");

                            display_comments(page);

                            //hides the post delete button
                            $('.post_delete').hide();

                            //hides the div that displays who liked and disliked a post
                            $('.like_body').hide();
                            $('.dislike_body').hide();

                            change_color();
                        }
                        else
                        {
                            $('#status_updates').html("<p>This user has not posted anything</p>");
                            $('#post_load').hide();
                        }
                        
                    }, "json");
                }
                else
                    $('#status_updates').html("<p class='locked'>Posts are locked</p>");
            }
            function display_comments(page)
            {
                $.post('main_access.php',
                {
                    access:2,
                    user_id: <?php echo $ID; ?>
                }, function(output)
                {
                    var comments=output.comments;
                    var comment_likes=output.number_likes;
                    var comment_likes_id=output.comment_likes_id;
                    var comment_dislikes=output.number_dislikes;
                    var comment_dislikes_id=output.comment_dislikes_id;
                    var comment_timestamps=output.comment_timestamps;
                    var comments_user_sent=output.comments_user_sent;
                    var comments_name=output.comment_names;
                    var post_ids=output.post_ids;

                    for(var x = 0; x < comments.length; x++)
                    {
                        $('#comment_body_'+page+'_'+post_ids[x]).html('');
                        if(comments[x]!='')
                        {
                            var content1='';
                            for(var y = 0; y < comments[x].length; y++)
                            {
                                comments[x][y]=text_format(comments[x][y]);
                                var name="<div class='comment_user_name_body'><a class='user_name_link' href='http://m.redlay.com/profile.php?user_id="+comments_user_sent[x][y]+"'><p class='comment_name' id='comment_name_"+post_ids[x]+"_"+y+"' >"+comments_name[x][y]+"</p></a></div>";
                                var picture="<div class='comment' id='comment_"+post_ids[x]+"_"+y+"' ><a href='http://m.redlay.com/profile.php?user_id="+comments_user_sent[x][y]+"'><img class='comment_profile_picture profile_picture' id='picture_comment_"+post_ids[x]+"_"+y+"' src='http://www.redlay.com/users/thumbs/users/"+comments_user_sent[x][y]+"/0.jpg' ></a>"
                                var comment="<p class='comment_text_body'>"+comments[x][y]+"</p>";


                                var bool=false;
                                for(var z = 0; z < comment_likes_id[x][y].length; z++)
                                {
                                    if(comment_likes_id[x][y][z]==<?php if(isset($_SESSION['page_id']))echo '0'; else echo $_SESSION['id']; ?>)
                                        bool=true;
                                }
                                var bool2=false;
                                for(var z = 0; z < comment_dislikes_id[x][y].length; z++)
                                {
                                    if(comment_dislikes_id[x][y][z]==<?php if(isset($_SESSION['page_id']))echo '0'; else echo $_SESSION['id']; ?>)
                                        bool2=true;
                                }

                                if(comment_likes[x][y]==0&&comments_user_sent[x][y]!=<?php if(isset($_SESSION['page_id']))echo '0'; else echo $_SESSION['id']; ?>)
                                    var like="<p class='comment_like' id='comment_like_"+page+"_"+x+"_"+y+"'onClick='like_comment("+post_ids[x]+", "+x+", "+y+", "+page+", "+comment_likes[x][y]+");' >Like</p>";
                                else if(bool==true)
                                    var like="<p class='comment_like' id='comment_like_"+page+"_"+x+"_"+y+"' onClick='unlike_comment("+post_ids[x]+", "+x+", "+y+", "+page+", "+comment_likes[x][y]+");' >Unlike ["+comment_likes[x][y]+"]</p>";
                                else if(comments_user_sent[x][y]==<?php if(isset($_SESSION['page_id']))echo '0'; else echo $_SESSION['id']; ?>)
                                {
                                    if(comment_likes[x][y]>1)
                                        var like="<p class='comment_like comment_like_me'>"+comment_likes[x][y]+" likes</p>";
                                    else if(comment_likes[x][y]==1)
                                        var like="<p class='comment_like comment_like_me'>1 like</p>";
                                    else
                                        var like="";
                                }
                                else
                                    var like="<p class='comment_like' id='comment_like_"+page+"_"+x+"_"+y+"' onClick='like_comment("+post_ids[x]+", "+x+", "+y+", "+page+", "+comment_likes[x][y]+");' >Like ["+comment_likes[x][y]+"]</p>";

                                if(comment_dislikes[x][y]==0&&comments_user_sent[x][y]!=<?php if(isset($_SESSION['page_id']))echo '0'; else echo $_SESSION['id']; ?>)
                                    var dislike="<p class='comment_dislike' id='comment_dislike_"+page+"_"+x+"_"+y+"' onClick='dislike_comment("+post_ids[x]+", "+x+", "+y+", "+page+", "+comment_dislikes[x][y]+");' >Dislike</p>";
                                else if(bool2==true)
                                    var dislike="<p class='comment_dislike' id='comment_dislike_"+page+"_"+x+"_"+y+"' onClick='undislike_comment("+post_ids[x]+", "+x+", "+y+", "+page+", "+comment_dislikes[x][y]+");' >Undislike ["+comment_dislikes[x][y]+"]</p>";
                                else if(comments_user_sent[x][y]==<?php if(isset($_SESSION['page_id']))echo '0'; else echo $_SESSION['id']; ?>)
                                {
                                    if(comment_dislikes[x][y]==1)
                                        var dislike="<p class='comment_dislike comment_dislike_me'>1 dislike</p>";
                                    else if (comment_dislikes[x][y]>1)
                                        var dislike="<p class='comment_dislike comment_dislike_me'>"+comment_dislikes[x][y]+" dislikes</p>";
                                    else
                                        var dislike="";
                                }
                                else
                                    var dislike="<p class='comment_dislike' id='comment_dislike_"+page+"_"+x+"_"+y+"' onClick='dislike_comment("+post_ids[x]+", "+x+", "+y+", "+page+", "+comment_dislikes[x][y]+");' >Dislike ["+comment_dislikes[x][y]+"]</p>";


                                var timestamp="<p class='comment_timestamp'>"+comment_timestamps[x][y]+"</p>";
                                var comment_break="<hr class='comment_break' /></div>";
                                if(<?php if(isset($_SESSION['page_id'])) echo "0"; else echo "1"; ?>==0)
                                {
                                    like="";
                                    dislike="";
                                }

                                var functions="<table class='post_functions_comment_table' ><tbody><tr><td class='post_functions_comment_unit'>"+like+"</td><td class='post_functions_comment_unit'>"+dislike+"</td></tr></tbody></table>";

                                content1=picture+name+comment+functions+timestamp+comment_break+content1;
                            }
                            $("#comment_body_"+page+"_"+post_ids[x]).html(content1);
                        }
                        else
                            $("#comment_body_"+page+"_"+post_ids[x]).html("There are no comments");
                    }
                    $('.comment_delete').hide();
                    $('.comment_body').hide();
                    $('.comment_textarea').hide();

                    initialize_comment_events();
                    change_color();
                }, "json");
            }
            function display_photos(page)
            {
                hide_everything();
                $('#photos').show();
                if(<?php if($display_non_friends[3]=='yes'||$ID==$_SESSION['id']||$user_is_friends=='true') echo "true"; else echo "false"; ?>==true)
                {
                    $.post('main_access.php',
                    {
                        access:1,
                        num:5,
                        number:2,
                        user_id: <?php echo $ID; ?>
                    }, function(output)
                    {
                        var images=output.images;
                        var image_widths=output.image_widths;
                        var image_heights=output.image_heights;
                        var image_id=output.image_ids;
                        var image_descriptions=output.image_descriptions;
                        var image_has_description=output.image_has_description;
                        
                            if(images.length<3)
                                var num_rows=1;
                            else
                                var num_rows=images.length/3;
                            var index=0;
                            $('#photos').html('<table id="other_pictures_table"></table>').css('height', '');
                            
                            for(var x = 0; x < num_rows; x++)
                            {
                                $('#other_pictures_table').html($('#other_pictures_table').html()+"<tr id='other_pictures_row_"+x+"' class='other_pictures_row'>");
                                for(var y = 0; y < 3; y++)
                                {
                                    $('#other_pictures_row_'+x).html($('#other_pictures_row_'+x).html()+"<td id='other_picture_unit_"+index+"' class='other_picture_unit'></td>");
                                    index++;
                                }
                            }
                            for(var x = 0; x < images.length; x++)
                            {
                                var image="<div class='image_preview_outside'><a href='http://m.redlay.com/view_photo.php?user_id=<?php echo $ID; ?>&&picture_id="+image_id[x]+"&&type=user'><img id='other_picture_"+x+"' class='other_pictures_picture' src='http://www.redlay.com/"+images[x]+"'/></a></div>";
                                $('#other_picture_unit_'+x).html(image);
                            }
                            $('.image_preview_outside').css({'height': '250px', 'width': '250px'});
                            $('.other_pictures_picture').css({'border': '2px solid <?php echo $color; ?>'});
                        
                        change_color();
                    }, "json");
                }
                else
                    $('#photos').html("<p class='locked'>Photos are locked</p>").css('height', '');
            }
            function post()
            {
                //gets the checked checkboxes and their values
                var audience_options_list=new Array();
                var num=0;
                var num2=0;
                while($('#profile_audience_box_checkbox_'+num2).length)
                {
                    if($('#profile_audience_box_checkbox_'+num2).data('checked')=='yes')
                    {
                        audience_options_list[num]=$('#profile_audience_box_checkbox_'+num2).data('group_name');
                        num++;
                    }
                    num2++;
                }


                var post=$('#post_input').val();
                $('#update_errors').hide();
                $.post('main_access.php',
                {
                    access:6,
                    user_id: <?php echo $ID; ?>,
                    updates: post,
                    audience: audience_options_list
                }, function (output)
                {
                    if(output=='Update posted!')
                        $('#errors').html(output).attr('class', 'good_errors').show();
                    else
                        $('#errors').html(output).attr('class', 'bad_errors').show();
                    $('#post_load').html("");
                    display_posts(1);
                });
            }

            function dislike_post(post_id, poster_id, num_dislikes, page)
            {
                $.post('main_access.php',
                {
                    access:10,
                    post_id: post_id,
                    profile_id: <?php echo $ID; ?>,
                    poster_id: poster_id
                }, function (output)
                {
                    num_dislikes++;
                    $('#post_dislike_'+page+'_'+post_id).html("Undislike ["+num_dislikes+"]").attr('onClick', "undislike_post("+post_id+", "+poster_id+", "+num_dislikes+", "+page+");");
                });
            }
            function undislike_post(post_id, poster_id, num_dislikes, page)
            {
                $.post('main_access.php',
                {
                    access:12,
                    post_id: post_id,
                    profile_id: <?php echo $ID; ?>
                }, function (output)
                {
                    num_dislikes--;
                    if(num_dislikes==0)
                        $('#post_dislike_'+page+'_'+post_id).html("Dislike");
                    else
                        $('#post_dislike_'+page+'_'+post_id).html("Dislike ["+num_dislikes+"]");
                    $('#post_dislike_'+post_id).attr('onClick', "dislike_post("+num_dislikes+", "+poster_id+", "+num_dislikes+", "+page+");");
                });
            }
            //ID is profile ID, ID2 is user posted ID, number is number of likes, and index is post_id
            function like_post(post_id, poster_id, num_likes, page)
            {
                $.post('main_access.php',
                {
                    access:9,
                    post_id: post_id,
                    profile_id: <?php echo $ID; ?>,
                    poster_id: poster_id
                }, function (output)
                {
                    num_likes++;
                    $('#post_like_'+page+'_'+post_id).html("Unlike ["+num_likes+"]").attr('onClick', "unlike_post("+post_id+", "+poster_id+", "+num_likes+", "+page+");");
                });
            }

            function unlike_post(post_id, poster_id, num_likes, page)
            {
                $.post('main_access.php',
                {
                    access:11,
                    post_id: post_id,
                    profile_id: <?php echo $ID; ?>
                }, function (output)
                {
                    num_likes--
                    if(num_likes==0)
                        $('#post_like_'+page+'_'+post_id).html("Like");
                    else
                        $('#post_like_'+page+'_'+post_id).html("Like ["+num_likes+"]");
                    $('#post_like_'+page+'_'+post_id).attr('onClick', "like_post("+post_id+", "+poster_id+", "+num_likes+", "+page+");");
                });
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
            function comment(post_id, index, poster_id, page)
            {
                $.post('main_access.php',
                {
                    access:13,
                    post_id: post_id,
                    comment_text: $("#comment_input_"+page+"_"+post_id).val(),
                    profile_id: <?php echo $ID; ?>,
                    poster_id: poster_id
                }, function (output)
                {
                    post_comments();
                    $('#comment_title_'+page+'_'+post_id).data('number', ($('#comment_title_'+page+'_'+post_id).data('number')+1));
                    $('#comment_title_'+page+'_'+post_id).html("Comment ["+$('#comment_title_'+page+'_'+post_id).data('number')+"]");
                });
            }
            function toggle_content_display()
            {
                if($('#content_display_box').css('display')=='none')
                    $('#content_display_box').show();
                else
                    $('#content_display_box').hide();
            }
            function hide_everything()
            {
                $('#status_updates').hide();
                $('#photos').hide();
                $('#videos').hide();
                $('#likes').hide();
                $('#info').hide();
            }

            $(document).ready(function()
            {
               display_posts(1, 'all', 'all', 'none', 1);
               change_color();
               display_groups('profile_audience_box');
               toggle_content_display();

               <?php
                        echo "$('body').css({'background-image': 'url(\'./pictures/default_background.png\')', 'background-position' :'center 50px'});";
                ?>
            });
        </script>
        <script>
            <?php include('required_google_analytics.js'); ?>
        </script>
    </head>
    <body>
        <?php include('top.php'); ?>
        <?php include('required_html.php'); ?>
        <div id="main">
                <?php
//                        $query=mysql_query("SELECT user_relationship, user_birthday, user_sex, high_school, college FROM user_data WHERE user_id=$ID LIMIT 1");
//                        $query2=mysql_query("SELECT birthday_year FROM user_display WHERE user_id=$ID LIMIT 1");
//                        if($query&&mysql_num_rows($query)==1&&$query2&&mysql_num_rows($query2)==1)
//                        {
//                            $array=mysql_fetch_row($query);
//                            $array2=mysql_fetch_row($query2);
//                            $user_relationship=$array[0];
//
//
//                            $birthday_year=$array2[0];
//                            //adds comma for user birthday
//                            $birthday=explode('|^|*|', $array[1]);
//
//                            if($birthday_year=='no')
//                                $birthday[2]='';
//                            else
//                                $birthday[1]=$birthday[1].',';
//                            
//                            $user_birthday=implode(' ', $birthday);
//                            $user_gender=$array[2];
//                            $high_school=$array[3];
//                            $college=$array[4];
//                        }
                        $query=mysql_query("SELECT user_bio FROM user_data WHERE user_id=$ID LIMIT 1");
                        if($query&&mysql_num_rows($query)==1)
                        {
                            $array=mysql_fetch_row($query);
                            $bio=$array[0];
                        }
                        $query=mysql_query("SELECT image_types FROM pictures WHERE user_id=$ID LIMIT 1");
                        if($query&&mysql_num_rows($query)==1)
                        {
                            $array=mysql_fetch_row($query);
                            $image_types=explode('|^|*|', $array[0]);
                        }
                    ?>
            <div id="information_content" class="box">
                <table style="margin:15px;margin-left:25px;">
                    <tbody>
                        <tr>
                            <td style="vertical-align:top;">
                                <img class="profile_picture" id="profile_main_picture" src="http://www.redlay.com/users/images/<?php echo $ID; ?>/0.<?php echo $image_types[0]; ?>"/>
                            </td>
                            <td style="vertical-align:top">
                                <table id="profile_information_table">
                                    <tbody>
                                        <tr class="profile_information_row">
                                            <td class="profile_information_data" ><p id="profile_name"><?php echo get_user_name($ID); ?></p></td>
                                        </tr>
                                        <tr class="profile_information_row">
                                            <td class="profile_information_data"><span class="table_data"><?php echo $bio; ?></span></td>
                                        </tr>
                    <!--                    <tr class="profile_information_row">
                                            <td class="profile_information_data"><span class="table_title">High School:</span></td>
                                            <td class="profile_information_data"><span class="table_data"><?php echo $high_school; ?></span></td>
                                        </tr>
                                        <tr class="profile_information_row">
                                            <td class="profile_information_data"><span class="table_title">College:</span></td>
                                            <td class="profile_information_data"><span class="table_data"><?php echo $college; ?></span></td>
                                        </tr>
                                        <tr class="profile_information_row">
                                            <td class="profile_information_data"><span class="table_title">Birthday:</span></td>
                                            <td class="profile_information_data"><span class="table_data"><?php echo $user_birthday; ?></span></td>
                                        </tr>
                                        <tr class="profile_information_row">
                                            <td class="profile_information_data"><span class="table_title">Relationship:</span></td>
                                            <td class="profile_information_data"><span class="table_data"><?php echo $user_relationship; ?></span></td>
                                        </tr>
                                        <tr class="profile_information_row">
                                            <td class="profile_information_data"><span class="table_title">Gender:</span></td>
                                            <td class="profile_information_data"><span class="table_data"><?php echo $user_gender; ?></span></td>
                                        </tr>-->
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </tbody>
                </table>
                
                
                
                
            </div>
            <div id="profile_menu" class="box">
                <table id="profile_menu_table">
                    <tbody>
                        <tr>
                            <td>
                                <table>
                                    <tbody>
                                        <tr id="profile_options_table">
                                            
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <hr class="break"/>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input type="button" class="display_to_button gray_button" value="Show" id="content_display_button" onClick="toggle_content_display();"/>
                                <div class="select_body_options" id="content_display_box">
                                    <table class="select_body_options_table">
                                        <tbody class="select_body_options_table_body">
                                            <tr class="select_body_options_row">
                                                <td class="select_body_option_unit">
                                                    <p class="select_body_option_text" onClick="display_posts(1, 'all', 'all', 'none', 1);toggle_content_display();">Posts</p>
                                                </td>
                                            </tr>
                                            <tr class="select_body_options_row">
                                                <td class="select_body_option_unit">
                                                    <p class="select_body_option_text" onClick="display_photos(1);toggle_content_display();">Photos</p>
                                                </td>
                                            </tr>
                                            <tr class="select_body_options_row">
                                                <td class="select_body_option_unit">
                                                    <p class="select_body_option_text" onClick="display_videos(1);toggle_content_display();">Videos</p>
                                                </td>
                                            </tr>
<!--                                            <tr class="select_body_options_row">
                                                <td class="select_body_option_unit">
                                                    <p class="select_body_option_text" onClick="display_likes(1);toggle_content_display();">Likes</p>
                                                </td>
                                            </tr>-->
                                            <tr class="select_body_options_row">
                                                <td class="select_body_option_unit">
                                                    <p class="select_body_option_text" onClick="display_info();toggle_content_display();">Info</p>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div id="posts_content" class="box">
               <div id="status_updates">
                   
                   <table id="update">
                       <tbody>
                           <tr>
                               <td colspan="2">
                                   <textarea id="post_input" name="post_input" maxlength="500" class="input_box" placeholder="What's up?" ></textarea>
                               </td>
                           </tr>
                           <tr>
                               <td>
                                   <div id="profile_audience_box" class="select_audience_box_body">

                                    </div>
                               </td>
                               <td style="text-align:right">
                                   <input type="button" value="Post" onClick="post();" id="post_button" class="red_button"/>
                               </td>
                           </tr>
                       </tbody>
                   </table>
                   
                   
                   
                   <div id="posts">

                   </div>
               </div>
                <div id="photos">
                    
                </div>
                <div id="videos">
                    
                </div>
                <div id="likes">
                    
                </div>
                <div id="info" style="padding:30px">
                    
                </div>
            </div>
            <?php include('footer.php'); ?>

            <script type="text/javascript">
                function initialize_comment_events()
                {
                    $('.comment_textarea').unbind('keypress').unbind('keydown').unbind('keyup');
                    $('.comment_textarea').keyup(function(e)
                    {
                        var key = (e.keyCode ? e.keyCode : e.which);
                        if(key == '13')
                        {
                            comment($(this).data('post_id'), $(this).data('index'), $(this).data('poster_id'), $(this).data('page'));
                            $(this).val('');
                        }
                    });
                }
            </script>
        </div>
    </body>
</html>