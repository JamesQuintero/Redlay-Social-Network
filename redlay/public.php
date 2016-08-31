<?php
@include('init.php');
include('universal_functions.php');
$allowed="all";
include('security_checks.php');
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Public</title>
        <?php include('required_header.php'); ?>
        <link rel="stylesheet" type="text/css" href="public.css" />
        <script type="text/javascript">
            function change_color()
            {
                <?php
                    if(isset($_SESSION['id']))
                    {
                        $colors=get_user_display_colors($_SESSION['id']);
                        $color=$colors[0];
                        $box_background_color=$colors[1];
                        $text_color=$colors[2];
                    }
                    else
                    {
                        $color="rgb(220,20,0)";
                        $box_background_color="white";
                        $text_color="rgb(30,30,30)";
                    }
                ?>
                $('.box').css('border', '5px solid <?php echo $color; ?>');
                $('.box').css('background-color', '<?php echo $box_background_color; ?>');
                
                $('#public_content').css('background-color', '<?php echo $box_background_color; ?>');
                
                $('#video_url_submit').css('outline-color', '<?php echo $color; ?>');
                
                $('.public_text, #company_footer, .text_color').css('color', '<?php echo $text_color; ?>');
                $('.title_color, .comment_like, .comment_dislike').css('color', '<?php echo $color; ?>');
            }
            function show_comments(type, page, index, post_id, user_id)
            {
                $('#comment_body_'+page+'_'+index).show();
                $('#comment_input_body_'+page+'_'+index).show();
                $('#comment_input_'+page+'_'+index).show();
                $('#comment_title_body_'+page+'_'+index).attr("onClick", "hide_comment('"+type+"', "+page+", "+index+", "+post_id+", "+user_id+");");
                
                
                
                if($('#comment_body_'+page+'_'+index).html()=="")
                {
                    //puts in loading gif
                    $('#comment_body_'+page+'_'+index).html("<img class='load_gif' src='http://pics.redlay.com/pictures/load.gif' />");
                    
                    if(type=="post")
                        var num=6;
                    else if(type=="photo")
                        var num=5;
                    else
                        var num=7;

                    var data=new Array();
                    data[0]=post_id;
                    data[1]=user_id;
                    data[2]=get_timezone();


                    $.post("public_query.php",
                    {
                        num:num,
                        data:data
                    }, function(output)
                    {
                        var comments=output.comments;
                        var comment_ids=output.comment_ids;
                        var comments_users_sent=output.comments_users_sent;
                        var comment_num_likes=output.comment_num_likes;
                        var comment_num_dislikes=output.comment_num_dislikes;
                        var comments_has_liked=output.comments_has_liked;
                        var comments_has_disliked=output.comments_has_disliked;
                        var comment_timestamps=output.comment_timestamps;
                        var comment_timestamp_seconds=output.comment_timestamp_seconds;
                        var comment_profile_pictures=output.comment_profile_pictures;
                        var comment_names=output.comment_names;
                        var comment_badges=output.comment_badges;
                        
                        
                        
                        var html="";
                        for(var x = 0; x < comments.length; x++)
                        {
                            $('#comment_input_'+page+'_'+index).data({'post_id': post_id, 'profile_id': user_id, 'type': type, 'poster_id': user_id, 'page': page, 'index': index, 'num_comments': comments.length});
                            $('#comment_title_'+page+'_'+index).data({'number': comments.length});
                            

                            if(comments[x].length>=1&&comments[x][0]!='')
                            {
                                comments[x]=convert_image(text_format(comments[x]), 'comment');
                                
                                var string="http://www.redlay.com/profile.php?user_id="+comments_users_sent[x];
                                var name="<a href='"+string+"' class='link'><span class='comment_name title_color' id='home_comment_name_"+page+"_"+index+"_"+x+"' onmouseover=name_over(this); onmouseout=name_out(this); >"+comment_names[x]+"</span></a>";
                                var picture="<a href='"+string+"' class='link'><img class='profile_picture comment_profile_picture' src='"+comment_profile_pictures[x]+"' /></a>";
                                var comment="<p class='comment_text_body text_color'>"+comments[x]+"</p>";
                                var timestamp="<p class='comment_timestamp text_color' id='comment_timestamp_"+page+"_"+comments_users_sent[x]+"_"+comment_ids[x]+"_"+x+"'>"+comment_timestamps[x]+"</p>";
                                if(comments_users_sent[x]==<?php echo $_SESSION['id']; ?>)
                                    var options="<div class='post_delete' id='comment_options_"+page+"_"+index+"_"+x+"' >x</div>";
                                else
                                    var options="";




                                //displaying likes
                                if(comments_users_sent[x]!=<?php echo $_SESSION['id']; ?>)
                                {
                                    if(comments_has_liked[x]==true)
                                        var like="<div class='left_function' id='comment_like_body_"+page+"_"+index+"_"+x+"' ><span class='comment_like' id='comment_like_"+page+"_"+index+"_"+x+"' >Unlike ["+comment_num_likes[x]+"]</span></div>";
                                    else if(comment_num_likes[x]>=1)
                                        var like="<div class='left_function' id='comment_like_body_"+page+"_"+index+"_"+x+"' ><span class='comment_like' id='comment_like_"+page+"_"+index+"_"+x+"' >Like ["+comment_num_likes[x]+"]</span></div>";
                                    else
                                        var like="<div class='left_function' id='comment_like_body_"+page+"_"+index+"_"+x+"' ><span class='comment_like' id='comment_like_"+page+"_"+index+"_"+x+"' >Like</span></div>";
                                }
                                else
                                {
                                    if(comment_num_likes[x]==1)
                                        var like="<div class='left_function_disabled' ><span class='comment_like' >1 like</span></div>";
                                    else if(comment_num_likes[x]>1)
                                        var like="<div class='left_function_disabled' ><span class='comment_like' >"+comment_num_likes[x]+" likes</span></div>";
                                    else
                                        var like="";
                                }

                                //displaying dislikes
                                if(comments_users_sent[x]!=<?php echo $_SESSION['id']; ?>)
                                {
                                    if(like=="")
                                        var function_class='single_function';
                                    else
                                        var function_class='right_function';
                                    
                                    if(comments_has_disliked[x]==true)
                                        var dislike="<div class='"+function_class+"' id='comment_dislike_body_"+page+"_"+index+"_"+x+"'><span class='comment_dislike' id='comment_dislike_"+page+"_"+index+"_"+x+"' >Undislike ["+comment_num_likes[x]+"]</span>";
                                    else if(comment_num_dislikes[x]>=1)
                                        var dislike="<div class='"+function_class+"' id='comment_dislike_body_"+page+"_"+index+"_"+x+"'><span class='comment_dislike' id='comment_dislike_"+page+"_"+index+"_"+x+"' >Dislike ["+comment_num_likes[x]+"]</span>";
                                    else
                                        var dislike="<div class='"+function_class+"' id='comment_dislike_body_"+page+"_"+index+"_"+x+"'><span class='comment_dislike' id='comment_dislike_"+page+"_"+index+"_"+x+"' >Dislike</span>";
                                }
                                else
                                {
                                    if(like=="")
                                        var function_class='single_function_disabled';
                                    else
                                        var function_class='right_function_disabled';
                                    
                                    if(comment_num_dislikes[x]==1)
                                        var dislike="<div class='"+function_class+"' ><span class='comment_dislike' >1 dislike</span></div>";
                                    else if(comment_num_dislikes[x]>1)
                                        var dislike="<div class='"+function_class+"' ><span class='comment_dislike' >"+comment_num_dislikes[x]+" dislikes</span></div>";
                                    else
                                        var dislike="";
                                }
                                
                                  var functions=get_comment_functions(like, dislike, timestamp);

                                var body=get_post_format(picture, name+comment, functions, '', '', options, "comment_options_"+page+"_"+index+"_"+x, "comment_body_"+page+'_'+index+'_'+x, comment_badges[x]);
                                html=body+html;
                            }
                            else
                                html="There are no comments";
                        }
                        $('#comment_body_'+page+'_'+index).html(html);
                        
                        $('.comment_textarea').attr({'onFocus': "input_in(this)", "onBlur": "input_out(this)"});
                        
                        for(var x = 0; x < comments.length; x++)
                        {
                            //displays dynamic timestamps
                            if(comments[x].length>=1)
                            {
                                if(comments_users_sent[x]!=<?php echo $_SESSION['id']; ?>)
                                {
                                    if(comments_has_liked[x]==true)
                                        $('#comment_like_body_'+page+'_'+index+'_'+x).attr({'onClick': "unlike_comment("+user_id+", "+post_id+", "+index+", "+comment_ids[x]+", "+x+", "+page+", "+comment_num_likes[x]+");"});
                                    else
                                        $('#comment_like_body_'+page+'_'+index+'_'+x).attr({'onClick': "like_comment("+user_id+", "+post_id+", "+index+", "+comment_ids[x]+", "+x+", "+page+", "+comment_num_likes[x]+");"});

                                    if(comments_has_liked[x]==true)
                                        $('#comment_dislike_body_'+page+'_'+index+'_'+x).attr({'onClick': "undislike_comment("+user_id+", "+post_id+", "+index+", "+comment_ids[x]+", "+x+", "+page+", "+comment_num_likes[x]+");"});
                                    else
                                        $('#comment_dislike_body_'+page+'_'+index+'_'+x).attr({'onClick': "dislike_comment("+user_id+", "+post_id+", "+index+", "+comment_ids[x]+", "+x+", "+page+", "+comment_num_likes[x]+");"});
                                }

                                $('#comment_options_'+page+'_'+index+'_'+x).attr('onClick', "delete_comment("+user_id+", "+post_id+", "+index+", "+comment_ids[x]+", "+x+", "+page+");");


                                count_time(comment_timestamp_seconds[x], "#comment_timestamp_"+page+"_"+comments_users_sent[x]+"_"+comment_ids[x]+"_"+x);

                            }
                        }
                        
                        
                        initialize_comment_events();
                        change_color();
                    }, "json");
                }
            }
            function hide_comment(type, page, index, post_id, user_id)
            {
                $('#comment_body_'+page+'_'+index).hide();
                $('#comment_input_body_'+page+'_'+index).hide();
                $('#comment_title_body_'+page+'_'+index).attr("onClick", "show_comments('"+type+"', "+page+", "+index+", "+post_id+", "+user_id+");");
            }
            function display_videos(page)
            {
                hide_everything();
                $('#videos').show();
                $('#refresh_button').attr('onClick', "display_videos("+page+");");
                
                var timezone=get_timezone();
                $.post('public_query.php',
                {
                    num:3,
                    page:page,
                    timezone: timezone
                }, function(output)
                {
                    var video_ids=output.video_ids;
                    var videos=output.videos;
                    var video_embeds=output.video_embeds;
                    var video_types=output.video_types;
                    var video_previews=output.video_previews
                    var videos_users_sent=output.videos_users_sent;
                    var video_timestamps=output.video_timestamps;
                    var timestamp_seconds=output.timestamp_seconds;
                    var empty=output.empty;
                    var total_size=output.total_size;
                    var profile_pictures=output.profile_pictures;
                    var user_names=output.user_names;
                    var badges=output.badges;
                    var num_adds=output.num_adds;
                    
                    
                    
                    if(total_size>0)
                    {
                        if(page==1)
                        {
                            $('#video_content').html("");
                            if(total_size>=10)
                            {
                                for(var x = 1; x < total_size/10+1; x++)
                                    $('#video_content').html($('#video_content').html()+"<div id='video_page_"+x+"'><table id='public_videos_table'><tbody id='video_page_body_"+x+"'></tbody></table></div>");
                            }
                            else
                                $('#video_content').html("<div id='video_page_1'><table><tbody id='video_page_body_1'></tbody></table></div>");
                            
                            
                            
                        }

                        for(var x = 0; x < videos.length; x++)
                        {
                            var profile_picture="<a class='link' href='http://www.redlay.com/profile.php?user_id="+videos_users_sent[x]+"'><img class='profile_picture profile_picture_status' src='"+profile_pictures[x]+"' /></a>";
                            
                            if(video_previews[x]!='')
                                var video_body="<div id='video_unit_"+page+"_"+x+"'><img class='video_preview' src='"+video_previews[x]+"' id='video_preview_"+page+"_"+x+"' /> <img class='video_play_button' id='video_play_button_"+page+"_"+x+"' src='http://pics.redlay.com/pictures/play_button.png' /></div>";
                            else
                                var video_body=video_embeds[x];
                            
                            var name="<div class='user_name_body'><a class='link' href='http://www.redlay.com/profile.php?user_id="+videos_users_sent[x]+"'><p class='user_name title_color' >"+user_names[x]+"</p></a></div>";
                            
                            if(videos_users_sent[x]!=<?php if(isset($_SESSION['id'])) echo $_SESSION['id']; else echo "0"; ?>)
                                var buttons="<table><tbody><tr><td><input class='button red_button' type='button' value='Copy' id='copy_button_"+page+"_"+video_ids[x]+"' /></td></tr></tbody></table>";
                            else
                                var buttons="<table><tbody><tr><td><input class='button red_button' type='button' value='Delete' id='delete_button_"+page+"_"+video_ids[x]+"' /></td></tr></tbody></table>";
                            
                            var num_adds_body="<span class='text_color' style='opacity:0.8'>"+num_adds[x]+" adds</span>";
                            
                            var description="";
                            var timestamp="<p class='timestamp_status_update text_color' id='public_video_timestamp_"+x+"'>"+video_timestamps[x]+"</p>";
                            var body=get_public_format(video_body, profile_picture, name, num_adds_body, buttons, description, timestamp, 'public_video_'+page+'_'+x, badges[x]);
                            
                            $('#video_page_body_'+page).html("<tr><td>"+body+"</td></tr>"+$('#video_page_body_'+page).html());
                            count_time(timestamp_seconds[x], '#public_video_timestamp_'+x);
                            
                            $('#video_unit_'+page+'_'+x).attr('onClick', "display_actual_video('#video_unit_"+page+"_"+x+"');").css('position','relative');
                            $('#video_preview_'+page+'_'+x).attr({'onmouseover': "video_over('#video_preview_"+page+"_"+x+"', '#video_play_button_"+page+"_"+x+"');",  'onmouseout': "video_out('#video_preview_"+page+"_"+x+"', '#video_play_button_"+page+"_"+x+"');"});
                            $('#video_play_button_'+page+'_'+x).attr('onmouseover', "video_over('#video_preview_"+page+"_"+x+"', '#video_play_button_"+page+"_"+x+"');");
                        }


                        if($('#videos_see_more').length)
                        {
                            if(!empty)
                                $('#videos_see_more').attr('onClick', "display_videos("+(page+1)+");");
                        }
                        else
                        {
                            if(!empty)
                                $('#videos').html($('#videos').html()+"<input class='see_more_posts button' id='videos_see_more' value='See more' onClick='display_videos("+(page+1)+");' type='button'/>");
                        }

                        if(empty)
                                $('#videos_see_more').hide().attr('onClick', '');
                            
                        for(var x = 0; x < videos.length; x++)
                            $('#video_unit_'+page+'_'+x).data('vid_embed', video_embeds[x]);
                    }
                    else
                        $('#videos_content').html("<p class='text_color'>There is nothing to display here</p>");
                    
                    
                    
                    change_color();
                },"json");
            }
            function display_photos(page)
            {
                hide_everything();
                $('#photos').show();
                $('#refresh_button').attr('onClick', "display_photos("+page+");");
                
                var timezone=get_timezone();
                $.post('public_query.php',
                {
                    num:1,
                    page:page,
                    timezone: timezone
                }, function(output)
                {
                    var photo_ids=output.photo_ids;
                    var original_picture_ids=output.original_picture_ids;
                    var photo_descriptions=output.photo_descriptions;
                    var photos_users_sent=output.photos_users_sent;
                    var photos_users_sent_names=output.photos_users_sent_names;
                    var image_types=output.image_types;
                    var profile_pictures=output.profile_pictures;
                    var timestamps=output.timestamps;
                    var timestamp_seconds=output.timestamp_seconds;
                    var is_empty=output.is_empty;
                    var total_size=output.total_size;
                    var badges=output.badges;
                    var picture_types=output.picture_types;
                    var num_adds=output.num_adds;
                    
                    
                    var num_likes=output.num_likes;
                    var num_dislikes=output.num_dislikes;
                    var has_liked=output.has_liked;
                    var has_disliked=output.has_disliked;
                    var num_comments=output.num_comments;
                    
                    
                    if(total_size>0)
                    {
                        $('#photo_load_gif').show();
                        if(page==1)
                        {
                            $('#photos_content').html("");
                            
                            var html="";
                            for(var x = 1; x <= (total_size/10)+1; x++)
                            {
                                $('#photos_content').html($('#photos_content').html()+"<div id='photo_page_body_"+x+"'></div>");
                            }

                            if(total_size<10)
                                $('#photos_content').html($('#photos_content').html()+"<div id='photo_page_body_"+x+"'></div>");

                        }
                        
                        
                        var html="";
                        var functionality=new Array();
                        for(var x = 0; x < photo_ids.length; x++)
                        {
                            var profile_picture="<a class='link' href='http://www.redlay.com/profile.php?user_id="+photos_users_sent[x]+"'><img class='profile_picture profile_picture_status' src='"+profile_pictures[x]+"'/></a>";
                            var image="<a class='link' href='http://www.redlay.com/view_photo.php?user_id="+photos_users_sent[x]+"&&picture_id="+original_picture_ids[x]+"&&type=user'><img class='public_image' src='http://u.redlay.com/public/photos/"+photo_ids[x]+"."+picture_types[x]+"' /></a>";
                            var name="<div class='user_name_body'><a class='link' href='http://www.redlay.com/profile.php?user_id="+photos_users_sent[x]+"'><p class='user_name title_color text' onmouseover=name_over(this); onmouseout=name_out(this); >"+photos_users_sent_names[x]+"</p></a></div>";
                            var description="<p class='text_color text' style='margin:0px;'>"+photo_descriptions[x]+"</p>";
                            var timestamp="<p class='timestamp_status_update text_color text' id='photo_timestamp_"+photo_ids[x]+"'>"+timestamps[x]+"</p>";
                            
                            
                            if(photos_users_sent[x]!=<?php if(isset($_SESSION['id'])) echo $_SESSION['id']; else echo "0"; ?>)
                                var buttons="<table><tbody><tr><td><input class='button red_button' type='button' value='Copy' id='copy_button_"+page+"_"+photo_ids[x]+"' /></td></tr></tbody></table>";
                            else
                                var buttons="<table><tbody><tr><td><input class='button red_button' type='button' value='Delete' id='delete_button_"+page+"_"+photo_ids[x]+"' /></td></tr></tbody></table>";
                            
                            var num_adds_body="<span class='text_color text' style='opacity:0.8'>"+num_adds[x]+" adds</span>";
                            
                            //gets badges
                            if(badges[x]!=undefined&&badges[x]!='')
                            {
                                if(badges[x]['gold']==true)
                                    var badge_body="<tr><td><a class='link' href='http://www.redlay.com/redlay_gold.php'><div class='badge gold_badge' ><p class='badge_text' >Gold</p></div></a></td></tr>";
                                else
                                    var badge_body="";
                            }
                            else
                                var badge_body='';
                            
                            
                            functionality[x]=new Array();
                            
                            //display likes
                            if(photos_users_sent[x]!=<?php echo $_SESSION['id']; ?>)
                            {
                                if(has_liked[x]==true)
                                {
                                    var like_text="<div class='left_function' id='home_photo_like_body_"+page+"_"+x+"'><span class='status_update_like title_color' id='home_photo_like_"+page+"_"+x+"'>Unlike ["+num_likes[x]+"]</span></div>";
                                    functionality[x][0]="photo_unlike";
                                }
                                else if(num_likes[x]>=1)
                                {
                                    var like_text="<div class='left_function' id='home_photo_like_body_"+page+"_"+x+"'><span class='status_update_like title_color' id='home_photo_like_"+page+"_"+x+"' >Like ["+num_likes[x]+"]</span></div>";
                                    functionality[x][0]="photo_like";
                                }
                                else
                                {
                                    var like_text="<div class='left_function' id='home_photo_like_body_"+page+"_"+x+"'><span class='status_update_like title_color' id='home_photo_like_"+page+"_"+x+"' >Like</span></div>";
                                    functionality[x][0]="photo_like";
                                }
                            }
                            else
                            {
                                if(num_likes[x]==1)
                                    var like_text="<div class='left_function_disabled' ><span class='status_update_like title_color' style='cursor:default;'>1 like</span></div>";
                                else if(num_likes[x]>1)
                                    var like_text="<div class='left_function_disabled' ><span class='status_update_like title_color' style='cursor:default;'>"+num_likes[x]+" likes</span></div>";
                                else
                                    var like_text="";
                                
                                functionality[x][0]="";
                            }

                            //display dislikes
                            if(photos_users_sent[x]!=<?php echo $_SESSION['id']; ?>)
                            {
                                if(like_text=='')
                                    var function_class="left_function";
                                else
                                    var function_class='middle_function';

                                if(has_disliked[x]==true)
                                {
                                    var dislike_text="<div class='"+function_class+"' id='home_photo_dislike_body_"+page+"_"+x+"'><span class='status_update_dislike title_color' id='home_photo_dislike_"+page+"_"+x+"' >Undislike ["+num_dislikes[x]+"]</span></div>";
                                    functionality[x][1]="photo_undislike";
                                }
                                else if(num_dislikes[x]>=1)
                                {
                                    var dislike_text="<div class='"+function_class+"' id='home_photo_dislike_body_"+page+"_"+x+"'><span class='status_update_dislike title_color' id='home_photo_dislike_"+page+"_"+x+"' >Dislike ["+num_dislikes[x]+"]</span></div>";
                                    functionality[x][1]="photo_dislike";
                                }
                                else
                                {
                                    var dislike_text="<div class='"+function_class+"' id='home_photo_dislike_body_"+page+"_"+x+"'><span class='status_update_dislike title_color' id='home_photo_dislike_"+page+"_"+x+"' >Dislike</span></div>";
                                    functionality[x][1]="photo_dislike";
                                }
                            }
                            else
                            {
                                if(like_text=='')
                                    var function_class="left_function_disabled";
                                else
                                    var function_class='middle_function_disabled';

                                if(num_dislikes[x]==1)
                                    var dislike_text="<div class='"+function_class+"' ><span class='status_update_like title_color' style='cursor:default;'>1 dislike</span></div>";
                                else if(num_dislikes[x]>1)
                                    var dislike_text="<div class='"+function_class+"' ><span class='status_update_like title_color' style='cursor:default;'>"+num_dislikes[x]+" dislikes</span></div>";
                                else
                                    var dislike_text="";
                                functionality[x][1]="";
                            }

                            if(like_text==''&&dislike_text=='')
                                var function_class="single_function";
                            else
                                var function_class='right_function';

                            //comments and stuff
                            if(num_comments[x]>=1)
                                var comment_text="<div class='"+function_class+"' id='comment_title_body_"+page+"_"+x+"' ><span id='comment_title_"+page+"_"+x+"' class='comment_text title_color' >Comment ["+num_comments[x]+"]</span></div>";
                            else
                                var comment_text="<div class='"+function_class+"' id='comment_title_body_"+page+"_"+x+"' ><span id='comment_title_"+page+"_"+x+"' class='comment_text title_color' >Comment</span></div>";

                            var comment_input="<div class='comment_input_body' id='comment_input_body_"+page+"_"+x+"'><textarea class='comment_textarea input_box' id='comment_input_"+page+"_"+x+"' placeholder='Comment...' maxlength='500'></textarea></div>";
                            var comment_body="<div class='comment_body' id='comment_body_"+page+"_"+x+"'></div>";

                            var functions=get_post_functions(like_text,dislike_text, comment_text,'');
                            
                            
                            
                            
//                            var body=get_public_format(image, profile_picture, name, num_adds_body, buttons, description, timestamp, 'public_photo_'+x, badges[x]);
                            var body="<table style='width:100%;border-spacing:0px;' id='public_photo_"+x+"'><tbody><tr style='display:inline-table;height:100%;width:100%;position:relative;border-spacing:0px;'><td style='width:400px;'>"+image+"</td> <td style='vertical-align:top;'><table style='height:100%;border-spacing:0px;'><tbody><tr><td style='vertical-align:top;height:50px'><table style='border-spacing:0px;' ><tbody><tr><td>"+profile_picture+"</td><td><table style='height:50px;border-spacing:0px;'><tbody><tr><td style='height:20px;'>"+name+"</td></tr><tr><td style='vertical-align:bottom;'>"+num_adds_body+"</td></tr></tbody></table></td><tr><td><table style='width:100%;border-spacing:0px;'><tbody>"+badge_body+"</tbody></table></td><td></td></tr></tr></tbody></table></td></tr><tr><td style='vertical-align:top;'>"+description+"</td></tr><tr><td style='height:35px;'>"+buttons+"</td></tr><tr><td style='height:20px;'>"+timestamp+"</td></tr>  </tbody></table></td></tr><tr><td colspan='2'>   "+functions+comment_input+comment_body+"   </td></tr></tbody></table><hr class='break'/>";

                            
                            html="<tr ><td>"+body+"</td></tr>"+html;
                        }
                        $('#photo_page_body_'+page).html("<table><tbody id='photos_table_body_"+page+"'>"+html+"</tbody></table>");
                        
                        for(var x = 0; x < photo_ids.length; x++)
                        {
                            //adds onClick functionality to like and dislike buttons
                            if(functionality[x][0]=='photo_unlike')
                                $('#home_photo_like_body_'+page+'_'+x).attr({'onClick': "unlike_photo('"+original_picture_ids[x]+"', "+photos_users_sent[x]+", 'user', "+page+", "+x+", "+num_likes[x]+");"});
                            else if(functionality[x][0]=='photo_like')
                                $('#home_photo_like_body_'+page+'_'+x).attr({'onClick': "like_photo('"+original_picture_ids[x]+"', "+photos_users_sent[x]+", 'user', "+page+", "+x+", "+num_likes[x]+");"});

                            if(functionality[x][1]=='photo_undislike')
                                $('#home_photo_dislike_body_'+page+'_'+x).attr({'onClick': "undislike_photo('"+original_picture_ids[x]+"', "+photos_users_sent[x]+", 'user', "+page+", "+x+", "+num_dislikes[x]+");"});
                            else if(functionality[x][1]=='photo_dislike')
                                $('#home_photo_dislike_body_'+page+'_'+x).attr({'onClick': "dislike_photo('"+original_picture_ids[x]+"', "+photos_users_sent[x]+", 'user', "+page+", "+x+", "+num_dislikes[x]+");"});
                        }
                        
                        
                        for(var x = 0; x <photo_ids.length; x++)
                        {
                            count_time(timestamp_seconds[x], '#photo_timestamp_'+photo_ids[x]);
                            
                            $('#comment_input_'+page+'_'+x).data({'picture_id': original_picture_ids[x], 'user_id': photos_users_sent[x], 'type': 'photo', 'image_type ': picture_types[x], 'page': page, 'index': x, 'num_comments': num_comments[x]});
                            $('#comment_title_body_'+page+'_'+x).attr('onClick', "show_comments('post', "+page+", "+x+", '"+original_picture_ids[x]+"', "+photos_users_sent[x]+");");
                            
                            //puts in functionality into add button
                            if($('#add_button_photo_'+page+'_'+photo_ids[x]).length)
                                $('#add_button_photo_'+page+'_'+photo_ids[x]).attr('onClick', "display_add_menu("+photos_users_sent[x]+");");
                            
                            //puts in functionality into copy button
                            if($('#copy_button_'+page+'_'+photo_ids[x]).length)
                                $('#copy_button_'+page+'_'+photo_ids[x]).attr('onClick', "display_copy_picture_menu('"+original_picture_ids[x]+"', "+photos_users_sent[x]+", 'user');");
                            
                            //puts in functionality into delete button
                            else
                                $('#delete_button_'+page+'_'+photo_ids[x]).attr('onClick', "delete_picture_public('"+photos_users_sent[x]+"');");
                        }
                        


                        if($('#photos_see_more').length)
                        {
                            if(!is_empty)
                                $('#photos_see_more').attr('onClick', "display_photos("+(page+1)+");");
                        }
                        else
                        {
                            if(!is_empty)
                                $('#photos').html($('#photos').html()+"<input class='see_more_posts button' id='photos_see_more' value='See more' onClick='display_photos("+(page+1)+");' type='button'/>");
                        }

                        if(is_empty)
                                $('#photos_see_more').hide().attr('onClick', '');
                            
                        $('.comment_textarea').attr({'onFocus': "input_in(this)", "onBlur": "input_out(this)"});
                        $('.comment_body').hide();
                        $('.comment_input_body').hide();
                        $('.comment_textarea').hide();
                    }
                    else
                        $('#photos_content').html("<p class='text_color'>There is nothing to display here</p>");
                        
                    $('#photo_load_gif').hide();
                    
                    change_color();
                },"json");
            }
            function display_top_posts()
            {
                var timezone=get_timezone();
                $.post("top_content.php",
                {
                    num:1,
                    timezone:timezone
                }, function(output)
                {
                    var posts=output.posts;
                    var posts_users_sent=output.posts_users_sent;
                    var post_ids=output.post_ids;
                    var original_post_ids=output.original_post_ids;
                    var names=output.names;
                    var profile_pictures=output.profile_pictures;
                    var timestamps=output.timestamps;
                    var timestamp_seconds=output.timestamp_seconds;
                    var badges=output.badges;
                    
                    var num_likes=output.num_likes;
                    var num_dislikes=output.num_dislikes;
                    var has_liked=output.has_liked;
                    var has_disliked=output.has_disliked;
                    var num_comments=output.num_comments;
                    
                    $('#top_posts_body').html("<p style='margin:0px;'>Top posts</p>");
                    var page='1000000';
                    
                    var html="";
                    for(var x = 0; x < posts.length; x++)
                    {
                        posts[x]=text_format(convert_image(posts[x], 'post'));
                        var profile_picture="<a href='http://www.redlay.com/profile.php?user_id="+posts_users_sent[x]+"'><img class='profile_picture profile_picture_status' src='"+profile_pictures[x]+"' /></a>";
                        var name="<div class='user_name_body' ><a class='link' href='http://www.redlay.com/profile.php?user_id="+posts_users_sent[x]+"'><span class='user_name title_color' onmouseover=name_over(this); onmouseout=name_out(this); >"+names[x]+"</span></a></div>";
                        var post="<span class='text_color'>"+posts[x]+"</span>";
                        var timestamp="<span class='text_color' id='top_post_timestamp_"+x+"' style='font-size:14px;'>"+timestamps[x]+"</span>";
                        
                        
                        
                        
                        
                        //display likes
                        if(posts_users_sent[x]!=<?php echo $_SESSION['id']; ?>)
                        {
                            if(has_liked[x]==true)
                                var like_text="<div class='left_function' id='post_like_body_"+page+"_"+x+"'><span class='status_update_like title_color' id='post_like_"+page+"_"+x+"'>Unlike ["+num_likes[x]+"]</span></div>";
                            else if(num_likes[x]>=1)
                                var like_text="<div class='left_function' id='post_like_body_"+page+"_"+x+"'><span class='status_update_like title_color' id='post_like_"+page+"_"+x+"' >Like ["+num_likes[x]+"]</span></div>";
                            else
                                var like_text="<div class='left_function' id='post_like_body_"+page+"_"+x+"'><span class='status_update_like title_color' id='post_like_"+page+"_"+x+"' >Like</span></div>";
                        }
                        else
                        {
                            if(num_likes[x]==1)
                                var like_text="<div class='left_function_disabled' ><span class='status_update_like title_color' style='cursor:default;'>1 like</span></div>";
                            else if(num_likes[x]>1)
                                var like_text="<div class='left_function_disabled' ><span class='status_update_like title_color' style='cursor:default;'>"+num_likes[x]+" likes</span></div>";
                            else
                                var like_text="";
                        }

                        //display dislikes
                        if(posts_users_sent[x]!=<?php echo $_SESSION['id']; ?>)
                        {
                            if(like_text=='')
                                var function_class="left_function";
                            else
                                var function_class='middle_function';
                            
                            if(has_disliked[x]==true)
                                var dislike_text="<div class='"+function_class+"' id='post_dislike_body_"+page+"_"+x+"'><span class='status_update_dislike title_color' id='post_dislike_"+page+"_"+x+"' >Undislike ["+num_dislikes[x]+"]</span></div>";
                            else if(num_dislikes[x]>=1)
                                var dislike_text="<div class='"+function_class+"' id='post_dislike_body_"+page+"_"+x+"'><span class='status_update_dislike title_color' id='post_dislike_"+page+"_"+x+"' >Dislike ["+num_dislikes[x]+"]</span></div>";
                            else
                                var dislike_text="<div class='"+function_class+"' id='post_dislike_body_"+page+"_"+x+"'><span class='status_update_dislike title_color' id='post_dislike_"+page+"_"+x+"' >Dislike</span></div>";
                        }
                        else
                        {
                            if(like_text=='')
                                var function_class="left_function_disabled";
                            else
                                var function_class='middle_function_disabled';
                            
                            if(num_dislikes[x]==1)
                                var dislike_text="<div class='"+function_class+"' ><span class='status_update_like title_color' style='cursor:default;'>1 dislike</span></div>";
                            else if(num_dislikes[x]>1)
                                var dislike_text="<div class='"+function_class+"' ><span class='status_update_like title_color' style='cursor:default;'>"+num_dislikes[x]+" dislikes</span></div>";
                            else
                                var dislike_text="";
                        }
                        
                        if(like_text==''&&dislike_text=='')
                            var function_class="single_function";
                        else
                            var function_class='right_function';
                        
                        //comments and stuff
                        if(num_comments[x]>=1)
                            var comment_text="<div class='"+function_class+"' id='comment_title_body_"+page+"_"+x+"' ><span id='comment_title_"+page+"_"+x+"' class='comment_text title_color' >Comment ["+num_comments[x]+"]</span></div>";
                        else
                            var comment_text="<div class='"+function_class+"' id='comment_title_body_"+page+"_"+x+"' ><span id='comment_title_"+page+"_"+x+"' class='comment_text title_color' >Comment</span></div>";
                        
                        var comment_input="<div class='comment_input_body' id='comment_input_body_"+page+"_"+x+"'><textarea class='comment_textarea input_box' id='comment_input_"+page+"_"+x+"' placeholder='Comment...' maxlength='500'></textarea></div>";
                        var comment_body="<div class='comment_body' id='comment_body_"+page+"_"+x+"'></div>";

                        var functions=get_post_functions(like_text,dislike_text, comment_text,timestamp);

                        var html=html+get_post_format(profile_picture, name, post, functions, comment_input+comment_body, '', '', "top_post_"+x, badges[x]);
                    }
                    
                    $('#top_posts_body').html($('#top_posts_body').html()+html);
                    
                    for(var x = 0; x < posts.length; x++)
                    {
                        count_time(timestamp_seconds[x], '#top_post_timestamp_'+x);
                        
                        $('#comment_input_'+page+'_'+x).data({'post_id': post_ids[x], 'profile_id': posts_users_sent[x], 'poster_id': posts_users_sent[x], 'page': page, 'index': x, 'type': 'post', 'num_comments': num_comments[x]});
                        $('#comment_title_body_'+page+'_'+x).attr('onClick', "show_comments('post', "+page+", "+x+", "+original_post_ids[x]+", "+posts_users_sent[x]+");");
                        
                        //display likes
                        if(posts_users_sent[x]!=<?php echo $_SESSION['id']; ?>)
                        {
                            var page='1000000';
                            if(has_liked[x]==true)
                                $('#post_like_'+page+'_'+x).attr('onClick', "unlike_post("+posts_users_sent[x]+", "+original_post_ids[x]+", "+posts_users_sent[x]+", "+num_likes[x]+", "+page+", "+x+")");
                            else if(num_likes[x]>=1)
                                $('#post_like_'+page+'_'+x).attr('onClick', "like_post("+posts_users_sent[x]+", "+original_post_ids[x]+", "+posts_users_sent[x]+", "+num_likes[x]+", "+page+", "+x+")");
                            else
                                $('#post_like_'+page+'_'+x).attr('onClick', "like_post("+posts_users_sent[x]+", "+original_post_ids[x]+", "+posts_users_sent[x]+", "+num_likes[x]+", "+page+", "+x+")");
                        
                        }
                        //display dislikes
                        if(posts_users_sent[x]!=<?php echo $_SESSION['id']; ?>)
                        {
                            if(has_disliked[x]==true)
                                $('#post_dislike_'+page+'_'+x).attr('onClick', "undislike_post("+posts_users_sent[x]+", "+original_post_ids[x]+", "+posts_users_sent[x]+", "+num_dislikes[x]+", "+page+", "+x+")");
                            else if(num_dislikes[x]>=1)
                                $('#post_dislike_'+page+'_'+x).attr('onClick', "dislike_post("+posts_users_sent[x]+", "+original_post_ids[x]+", "+posts_users_sent[x]+", "+num_dislikes[x]+", "+page+", "+x+")");
                            else
                                $('#post_dislike_'+page+'_'+x).attr('onClick', "dislike_post("+posts_users_sent[x]+", "+original_post_ids[x]+", "+posts_users_sent[x]+", "+num_dislikes[x]+", "+page+", "+x+")");
                        }
                    }
                    
                    $('.comment_textarea').attr({'onFocus': "input_in(this)", "onBlur": "input_out(this)"});
                    $('.comment_body').hide();
                    $('.comment_input_body').hide();
                    $('.comment_textarea').hide();

                    change_color();
                    initialize_comment_events();
                    
                }, "json");
            }
            function display_posts(page)
            {
                hide_everything();
                $('#posts').show();
                $('#refresh_button').attr('onClick', "display_posts("+page+");");
                
                display_top_posts();
                
                var timezone=get_timezone();
                $.post('public_query.php',
                {
                    num:2,
                    page:page,
                    timezone:timezone
                }, function(output)
                {
                    var posts=output.posts;
                    var names=output.names;
                    var profile_pictures=output.profile_pictures;
                    var post_ids=output.post_ids;
                    var original_post_ids=output.original_post_ids;
                    var posts_users_sent=output.posts_users_sent;
                    var timestamps=output.timestamps;
                    var timestamp_seconds=output.timestamp_seconds;
                    var total_size=output.total_size;
                    var is_empty=output.is_empty;
                    var badges=output.badges;
                    
                    var num_likes=output.num_likes;
                    var num_dislikes=output.num_dislikes;
                    var has_liked=output.has_liked;
                    var has_disliked=output.has_disliked;
                    
                    if(total_size>0)
                    {
                        if(page==1)
                        {
                            $('#posts_content').html("");
                            for(var x = 1; x <= (total_size/10)+1; x++)
                                $('#posts_content').html($('#posts_content').html()+"<div id='posts_page_body_"+x+"'></div>");

                            if(total_size<10)
                                $('#posts_content').html("<div id='posts_page_body_1'></div>");
                        }
                        
                        var html="";
                        for(var x = 0; x < posts.length; x++)
                        {
                            posts[x]=text_format(convert_image(posts[x], 'post'));
                            var profile_picture="<a href='http://www.redlay.com/profile.php?user_id="+posts_users_sent[x]+"'><img class='profile_picture profile_picture_status' src='"+profile_pictures[x]+"' /></a>";
                            var name="<div class='user_name_body' ><a class='link' href='http://www.redlay.com/profile.php?user_id="+posts_users_sent[x]+"'><span class='user_name title_color' onmouseover=name_over(this); onmouseout=name_out(this); >"+names[x]+"</span></a></div>";
                            var post="<span class='text_color'>"+posts[x]+"</span>";
                            var timestamp="<span class='text_color' id='post_timestamp_"+x+"' style='font-size:14px;'>"+timestamps[x]+"</span>";
                            
                            
                            
                            
                            
                            
                            
                            //display likes
                            if(posts_users_sent[x]!=<?php echo $_SESSION['id']; ?>)
                            {
                                if(has_liked[x]==true)
                                    var like_text="<div class='left_function' id='post_like_body_"+page+"_"+x+"' ><span class='status_update_like title_color' id='post_like_"+page+"_"+x+"' >Unlike ["+num_likes[x]+"]</span></div>";
                                else if(num_likes[x]>=1)
                                    var like_text="<div class='left_function' id='post_like_body_"+page+"_"+x+"' ><span class='status_update_like title_color' id='post_like_"+page+"_"+x+"' >Like ["+num_likes[x]+"]</span></div>";
                                else
                                    var like_text="<div class='left_function' id='post_like_body_"+page+"_"+x+"' ><span class='status_update_like title_color' id='post_like_"+page+"_"+x+"' >Like</span></div>";
                            }
                            else
                            {
                                if(num_likes[x]==1)
                                    var like_text="<div class='left_function' ><span class='status_update_like title_color' style='cursor:default;'>1 like</span></div>";
                                else if(num_likes[x]>1)
                                    var like_text="<div class='left_function' ><span class='status_update_like title_color' style='cursor:default;'>"+num_likes[x]+" likes</span></div>";
                                else
                                    var like_text="";
                            }

                            //display dislikes
                            if(posts_users_sent[x]!=<?php echo $_SESSION['id']; ?>)
                            {
                                if(like_text=="")
                                    var function_class='left_function';
                                else
                                    var function_class='middle_function';
                                
                                if(has_disliked[x]==true)
                                    var dislike_text="<div class='"+function_class+"' id='post_dislike_body_"+page+"_"+x+"'><span class='status_update_dislike title_color' id='post_dislike_"+page+"_"+x+"' >Undislike ["+num_dislikes[x]+"]</span></div>";
                                else if(num_dislikes[x]>=1)
                                    var dislike_text="<div class='"+function_class+"' id='post_dislike_body_"+page+"_"+x+"'><span class='status_update_dislike title_color' id='post_dislike_"+page+"_"+x+"' >Dislike ["+num_dislikes[x]+"]</span></div>";
                                else
                                    var dislike_text="<div class='"+function_class+"' id='post_dislike_body_"+page+"_"+x+"'><span class='status_update_dislike title_color' id='post_dislike_"+page+"_"+x+"' >Dislike</span></div>";
                            }
                            else
                            {
                                if(like_text=="")
                                    var function_class='left_function_disabled';
                                else
                                    var function_class='middle_function_disabled';
                                
                                if(num_dislikes[x]==1)
                                    var dislike_text="<div class='"+function_class+"' ><span class='status_update_like title_color' style='cursor:default;'>1 dislike</span></div>";
                                else if(num_dislikes[x]>1)
                                    var dislike_text="<div class='"+function_class+"' ><span class='status_update_like title_color' style='cursor:default;'>"+num_dislikes[x]+" dislikes</span></div>";
                                else
                                    var dislike_text="";
                            }

                            
                            
                            
                            
                            
                            
                            
                            
                            
                            
                            var functions=get_post_functions(like_text,dislike_text,'',timestamp);
                            
                            html=get_post_format(profile_picture, name, post, functions, '', '', "post_"+page+'_'+x, badges[x])+html;
                        }
                        
                        $('#posts_page_body_'+page).html(html);
                        
                        for(var x = 0; x < posts.length; x++)
                        {
                            count_time(timestamp_seconds[x], '#post_timestamp_'+x);

                            //display likes
                            if(posts_users_sent[x]!=<?php echo $_SESSION['id']; ?>)
                            {
                                if(has_liked[x]==true)
                                    $('#post_like_'+page+'_'+x).attr('onClick', "unlike_post("+posts_users_sent[x]+", "+original_post_ids[x]+", "+posts_users_sent[x]+", "+num_likes[x]+", "+page+", "+x+")");
                                else if(num_likes[x]>=1)
                                    $('#post_like_'+page+'_'+x).attr('onClick', "like_post("+posts_users_sent[x]+", "+original_post_ids[x]+", "+posts_users_sent[x]+", "+num_likes[x]+", "+page+", "+x+")");
                                else
                                    $('#post_like_'+page+'_'+x).attr('onClick', "like_post("+posts_users_sent[x]+", "+original_post_ids[x]+", "+posts_users_sent[x]+", "+num_likes[x]+", "+page+", "+x+")");

                            }
                            //display dislikes
                            if(posts_users_sent[x]!=<?php echo $_SESSION['id']; ?>)
                            {
                                if(has_disliked[x]==true)
                                    $('#post_dislike_'+page+'_'+x).attr('onClick', "undislike_post("+posts_users_sent[x]+", "+original_post_ids[x]+", "+posts_users_sent[x]+", "+num_dislikes[x]+", "+page+", "+x+")");
                                else if(num_dislikes[x]>=1)
                                    $('#post_dislike_'+page+'_'+x).attr('onClick', "dislike_post("+posts_users_sent[x]+", "+original_post_ids[x]+", "+posts_users_sent[x]+", "+num_dislikes[x]+", "+page+", "+x+")");
                                else
                                    $('#post_dislike_'+page+'_'+x).attr('onClick', "dislike_post("+posts_users_sent[x]+", "+original_post_ids[x]+", "+posts_users_sent[x]+", "+num_dislikes[x]+", "+page+", "+x+")");
                            }
                        }


                        if($('#posts_see_more').length)
                        {
                            if(!is_empty)
                                $('#posts_see_more').attr('onClick', "display_posts("+(page+1)+");");
                        }
                        else
                        {
                            if(!is_empty)
                                $('#posts').html($('#posts').html()+"<input class='see_more_posts button' id='posts_see_more' value='See more' onClick='display_posts("+(page+1)+");' type='button'/>");
                        }

                        if(is_empty)
                                $('#posts_see_more').hide().attr('onClick', '');
                    }
                    else
                        $('#posts_content').html("<p class='text_color'>There is nothing to display here</p>");
                    
                    initialize_comment_events();
                    change_color();
                },"json");
            }
            function display_users(page)
            {
                hide_everything();
                $('#users').show();
                $('#refresh_button').attr('onClick', "display_users("+page+");");
                
                var timezone=get_timezone();
                $.post('public_query.php',
                {
                    num:4,
                    timezone:timezone,
                    page:page
                }, function(output)
                {
                    var user_ids=output.user_ids;
                    var user_names=output.user_names;
                    var user_timestamps=output.user_timestamps;
                    var timestamp_seconds=output.timestamp_seconds;
                    var profile_pictures=output.profile_pictures;
                    var total_size=output.total_size;
                    var empty=output.empty;
                    
                    //1==hasn't added
                    //2==pending add
                    //3==already added
                    var add_status=output.add_status
                    
                    if(page==1)
                    {
                        $('#users').html("");
                        for(var x = 1; x <= (total_size/10)+1; x++)
                            $('#users').html($('#users').html()+"<div id='users_page_body_"+x+"'></div>");

                        if(total_size<10)
                            $('#users').html("<div id='users_page_body_1'></div>");
                    } 
                    
                    for(var x = 0; x < user_ids.length; x++)
                    {
                        if(user_ids[x]!='')
                        {
                            var profile_picture="<a class='link' href='http://www.redlay.com/profile.php?user_id="+user_ids[x]+"'><img class='profile_picture profile_picture_status' src='"+profile_pictures[x]+"' /></a>";
                            var name="<div class='user_name_body'><a class='link' href='http://www.redlay.com/profile.php?user_id="+user_ids[x]+"'><p class='user_name title_color' onmouseover=name_over(this); onmouseout=name_out(this); >"+user_names[x]+"</p></a></div>";
                            var timestamp="<p class='timestamp_status_update text_color' id='user_timestamp_"+page+"_"+user_ids[x]+"'>"+user_timestamps[x]+"</p>";

                            if(add_status[x]==1&&user_ids[x]!=<?php if(isset($_SESSION['id'])) echo $_SESSION['id']; else echo "0"; ?>&&<?php if(isset($_SESSION['id'])) echo "true"; else echo "false"; ?>==true)
                                var add_button="<input class='button red_button' type='button' value='Add' onClick='display_add_menu("+user_ids[x]+");' />";
                            else if(<?php if(isset($_SESSION['id'])) echo "true"; else echo "false"; ?>==true&&add_status[x]==2)
                                var add_button="<input class='button red_button_disabled' type='button' value='Pending' />";
                            else
                                var add_button="";

                            var body=get_post_format(profile_picture, name, add_button, '', timestamp, '', '', "new_user_"+x, '');
                            $('#users_page_body_'+page).html($('#users_page_body_'+page).html()+body);
                        }
                    }
                    
                    for(var x = 0; x < user_ids.length; x++)
                    {
                        if(user_ids[x]!='')
                        {
                            count_time(timestamp_seconds[x], '#user_timestamp_'+page+'_'+user_ids[x]);
                        }
                    }
                    
                    if($('#users_see_more').length)
                    {
                        if(!empty)
                            $('#users_see_more').attr('onClick', "display_users("+(page+1)+");");
                    }
                    else
                    {
                        if(!empty)
                            $('#users').html($('#users').html()+"<input class='see_more_posts button' id='users_see_more' value='See more' onClick='display_users("+(page+1)+");' type='button'/>");
                    }

                    if(empty)
                            $('#users_see_more').hide().attr('onClick', '');
                    
                    change_color();
                }, "json");
            }
            function hide_everything()
            {
                $('#photos').hide();
                $('#posts').hide();
                $('#videos').hide();
                $('#users').hide();
            }

        </script>
        <script type="text/javascript">
        $(window).ready(function()
        {
            hide_everything();
            display_photos(1);
            change_color();
            $('#footer').css('width', '910px');
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
            else
                include('index_top.php');
            ?>
        
        <div id="main">
            <?php include('required_side_html.php'); ?>
            <div id="public_content" class="content">
                <table style="margin:0 auto;width:910px">
                    <tbody>
                        <tr>
                            <td colspan="2">
                                <p id="public_title_box_title">Public</p>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <hr style="margin:0px"/>
                            </td>
                        </tr>
                        <tr>
                            <td style="width:170px;vertical-align:top" >
                                <table id="public_menu_table">
                                    <tr class="public_menu_row">
                                        <td class="public_menu_item" onClick="display_videos(1);" onmouseover="name_over(this);" onmouseout="name_out(this);" >Videos</td>
                                    </tr>
                                    <tr class="public_menu_row">
                                        <td class="public_menu_item" onClick="display_photos(1);" onmouseover="name_over(this);" onmouseout="name_out(this);" >Photos</td>
                                    </tr>
                                    <tr class="public_menu_row">
                                        <td class="public_menu_item" onClick="display_posts(1);" onmouseover="name_over(this);" onmouseout="name_out(this);" >Posts</td>
                                    </tr>
                                    <tr class="public_menu_row">
                                        <td class="public_menu_item" onClick="display_users(1);" onmouseover="name_over(this);" onmouseout="name_out(this);" >Users</td>
                                    </tr>
                                </table>
                            </td>
                            <td style="border-left:1px solid gray;">
                                <input type="button" class="button red_button" value="Refresh" onClick="" id="refresh_button"/>
                                <hr class="break"/>
                                
                                <div id="photos">
                                    <img class='load_gif' id='photo_load_gif' src='http://pics.redlay.com/pictures/load.gif'/>
                                    <div id="photos_content">
                                        
                                    </div>
                                </div>
                                
                                <div id="videos">
                                    <div id="video_content">
                                        
                                        
                                    </div>
                                </div>
                                
                                <div id="posts">
                                    <div id="top_posts_body" style="margin-left:100px;position:relative;margin-bottom:20px;border:1px solid gray;width:500px;">
                                        
                                        
                                    </div>
                                    <div id="posts_content">
                                        
                                        
                                    </div>
                                </div>
                                
                                <div id="users">
                                    
                                    
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <?php include('footer.php'); ?>
        </div>
        <script type="text/javascript">
            function initialize_comment_events()
            {
                $('.comment_textarea').unbind('keypress').unbind('keydown').unbind('keyup');
                $('.comment_textarea').keyup(function(e)
                {
                    var key = (e.keyCode ? e.keyCode : e.which);
                    if(key == '13')
                    {

                        if($(this).data('type')=='post')
                        {
                            console.log("Profile_id: "+$(this).data('profile_id'));
                            console.log("poster_id: "+$(this).data('poster_id'));
                            console.log("Post_id: "+$(this).data('post_id'));
                            console.log("Index: "+$(this).data('index'));
                            console.log("Page: "+$(this).data('page'));
                            console.log("Num_comments: "+$(this).data('num_comments'));
                            console.log($(this).data('profile_id')+" | "+$(this).data('poster_id')+" | "+$(this).data('post_id')+" | "+$(this).data('index')+" | "+$(this).data('page')+" | "+$(this).data('num_comments'));
                            comment($(this).data('profile_id'), $(this).data('poster_id'), $(this).data('post_id'), $(this).data('index'), $(this).data('page'), $(this).data('num_comments'));
                        }
                        else
                        {
                            console.log("Profile_id: "+$(this).data('profile_id'));
                            console.log("poster_id: "+$(this).data('poster_id'));
                            console.log("Post_id: "+$(this).data('post_id'));
                            console.log("Index: "+$(this).data('index'));
                            console.log("Page: "+$(this).data('page'));
                            console.log("Num_comments: "+$(this).data('num_comments'));
                            console.log($(this).data('profile_id')+" | "+$(this).data('poster_id')+" | "+$(this).data('post_id')+" | "+$(this).data('index')+" | "+$(this).data('page')+" | "+$(this).data('num_comments'));
                        }
                    }
                });
            }
        </script>
    </body>
</html>