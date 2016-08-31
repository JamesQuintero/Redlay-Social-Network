<?php
@include('init.php');
include('universal_functions.php');
$allowed="users";
include('security_checks.php');


//echo "Got here";
//code for facebook share
require 'facebookphp/facebook.php';
$facebook = new Facebook(array(
  'appId'  => APP_ID,
  'secret' => SECRET_ID,
  'cookie' => true,
));

$userId = $facebook->getUser();

$me = null;
if ($userId) {
  try
   {
      $me = $facebook->api('/me');
      $friends=$facebook->api('/me/friends');
         facebook_methods(1, $friends);
   }
   catch (FacebookApiException $e) {
    error_log($e);
  }
}

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:fb="http://www.facebook.com/2008/fbml">
    <head>
        <title>Home</title>
        <meta name="home page" content="Last modified: 11/4/12"/>
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
                $('.box').css({'background-color': '<?php echo $box_background_color; ?>'});
                $('#facebook_share').css('border', '5px solid rgb(59, 89, 152)');
                


                $('.update_body, .timestamp_status_update, .home_user_picture_post_description, .home_view_text_title, .empty_text').css('color', '<?php echo $text_color; ?>');
                $('.update_name, #home_title, .group_photo_description').css('color', '<?php echo $color; ?>');
                $('#social_update').css('outline-color', '<?php echo $color; ?>');
                $('.status_update, #company_footer, .alert_box_text').css('color', '<?php echo $text_color; ?>');
                $('.birthday_alert_name, .alert_box_title').css('color', '<?php echo $color; ?>');
                $('.user_name, .home_menu_title').css('color', '<?php echo $color; ?>');
                $('.home_name, .comment_text, .status_update_dislike, .status_update_like, .home_name_box_title, .home_menu_option, .comment_name, .comment_like, .comment_dislike, .footer_text').css('color', '<?php echo $color; ?>');
                $('.home_other_text').css('color', '<?php echo $text_color; ?>');
                $('.post_delete, .comment_delete').css({'background-color': '<?php echo $color; ?>', 'color': '<?php echo $text_color; ?>'});

                $('.title_color').css('color', '<?php echo $color; ?>');
                $('.text_color').css('color', '<?php echo $text_color; ?>');
                $('.background_color').css('background-color', '<?php echo $box_background_color; ?>');
                $('.alert_box_inside').css('color', '<?php echo $text_color; ?>');
                <?php $path=get_user_background_pic($_SESSION['id']); if(file_exists_server($path)&&$colors[5]=="yes") echo "$('body').css('background-attachment', 'fixed');"; ?>
                $('.gold_box').css('border-color', 'rgb(252,178,0)');
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
                        display_error(output, 'good_errors')
                    else
                        display_error(output, 'bad_errors');
                });
           }
           
           function change_view()
           {
               $.post('change_home_view.php',
               {
                   num:1,
                   content: $('#select_view_button').data('val'),
                   add_id: $('#select_user_button').data('user_id')
               }, function(output)
               {
                   
               });
           }
           
           function get_view()
           {
               $.post('change_home_view.php',
                {
                    num:2
                }, function(output)
                 {
                     
                 }, "json");
           }

            function display_everything(page)
            {
                if($('#select_user_button').data('user_id')!=-1)
                    $('#social_submit_button').attr('onClick', 'post('+$('#select_user_button').data('user_id')+')');
                $('#friend_posts_load').show();
                $('#page_'+page).html('');
                
                var timezone=get_timezone();
                $.post('home_query.php',
                {
//                    num: 1,
//                    user_id: $('#select_user_button').data('user_id'),
//                    page_id: $('#select_page_button').data('page_id'),
//                    page_number: page,
//                    content_type: $('#select_view_button').data('val'),
//                    user_type: $('#select_from_button').data('type'),
//                    group: $('#select_in_button').data('group_name'),
//                    timezone: timezone
                    num: 1,
                    user_id: $('#select_user_button').data('user_id'),
                    page_id: -1,
                    page_number: page,
                    content_type: $('#select_view_button').data('val'),
                    user_type: 'Users',
                    group: $('#select_in_button').data('group_name'),
                    timezone: timezone
                    
                }, function(output)
                {
                    var post_ids=output.post_ids;
                    var posts=output.posts;
                    var user_ids_posted=output.user_ids_posted;
                    var images=output.images;
                    var image_descriptions=output.image_descriptions;
                    var comments=output.comments;
                    var comment_ids=output.comment_ids;
                    var type=output.type;
                    var size=output.size;
                    var empty=output.empty;
                    var total_size=output.total_size;
                    var names=output.names;
                    var timestamps=output.timestamps;
                    var timestamp_seconds=output.timestamp_seconds;
                    var profile_ids=output.profile_ids;
                    var comments_users_sent=output.comments_users_sent;
                    var comment_timestamps=output.comment_timestamps;
                    var comment_names=output.comment_names;
                    var others=output.other;
                    var others_names=output.other_names;
                    var profile_pictures=output.profile_pictures;
                    var other_profile_pictures=output.other_profile_pictures;
                    var image_types=output.image_types;
                    var comment_profile_pictures=output.comment_profile_pictures;
                    var badges=output.badges;
                    var comment_badges=output.comment_badges;
                    var has_liked=output.has_liked;
                    var has_disliked=output.has_disliked;
                    var num_likes=output.num_likes;
                    var num_dislikes=output.num_dislikes;
                    var num_comment_likes=output.num_comment_likes;
                    var num_comment_dislikes=output.num_comment_dislikes;
                    var has_liked_comment=output.has_liked_comments;
                    var has_disliked_comment=output.has_disliked_comments;
                    var num_comments=output.num_comments;
                    var comment_timestamp_seconds=output.comment_timestamp_seconds;



                    if(page==1)
                    {
                        $('#home_post_text').html('');
                        for(var x = 1; x <= (total_size/30)+1; x++)
                            $('#home_post_text').html($('#home_post_text').html()+"<div class='home_page_page' id='page_"+x+"'></div>");

                        if(total_size<30)
                            $('#home_post_text').html($('#home_post_text').html()+"<div class='home_page_page' id='page_1'></div>");

                        $('#home_post_text').html($('#home_post_text').html()+"<div id='see_more_body'></div>");
                    }
                    
                    if(size!=0)
                    {
                        var html="";
                        var functionality=new Array();
                        for(var x = 0; x < type.length; x++)
                        {
//                            aler(type[x]);
                            if(type[x]=='user_post')
                            {
                                posts[x]=convert_image(text_format(posts[x]), 'post');
                                //updates, user name, user profile picture, timestamps, and break
                                var post="<p class='status_update_text'>"+posts[x]+"</p>";
                                var name="<a href='http://www.redlay.com/profile.php?user_id="+user_ids_posted[x]+"' class='home_post_name_link'><span class='user_name' id='user_name_"+x+"' onmouseover=name_over(this); onmouseout=name_out(this); >"+names[x]+"</span></a>";
                                var profile_picture="<a href='http://www.redlay.com/profile.php?user_id="+user_ids_posted[x]+"'><img class='profile_picture profile_picture_status' src='"+profile_pictures[x]+"' id='home_profile_picture_"+x+"' /></a>";
                                var timestamp="<p class='timestamp_status_update' id='home_timestamp_"+page+"_timestamp_"+user_ids_posted[x]+"_"+x+"_"+post_ids[x]+"'>"+timestamps[x]+"</p>";

                                if(user_ids_posted[x]==<?php echo $_SESSION['id']; ?>)
                                    var options="<div class='post_delete post_hide' id='post_options_"+page+"_"+x+"' onClick='show_post_options("+post_ids[x]+", <?php echo $_SESSION['id']; ?>);'>O</div>";
                                else
                                    var options="";



                                //display likes
                                if(user_ids_posted[x]!=<?php echo $_SESSION['id']; ?>)
                                {
                                    if(has_liked[x]==true)
                                        var like_text="<span class='status_update_like' id='post_like_"+page+"_"+x+"' onClick='unlike_post("+profile_ids[x]+", "+post_ids[x]+", "+user_ids_posted[x]+", "+num_likes[x]+", "+page+", "+x+");' onmouseover=name_over(this); onmouseout=name_out(this); >Unlike ["+num_likes[x]+"]</span>";
                                    else if(num_likes[x]>=1)
                                        var like_text="<span class='status_update_like' id='post_like_"+page+"_"+x+"' onClick='like_post("+profile_ids[x]+", "+post_ids[x]+", "+user_ids_posted[x]+", "+num_likes[x]+", "+page+", "+x+");' onmouseover=name_over(this); onmouseout=name_out(this); >Like ["+num_likes[x]+"]</span>";
                                    else
                                        var like_text="<span class='status_update_like' id='post_like_"+page+"_"+x+"' onClick='like_post("+profile_ids[x]+", "+post_ids[x]+", "+user_ids_posted[x]+", "+num_likes[x]+", "+page+", "+x+");' onmouseover=name_over(this); onmouseout=name_out(this); >Like</span>";
                                }
                                else
                                {
                                    if(num_likes[x]==1)
                                        var like_text="<span class='status_update_like' style='cursor:default;'>1 like</span>";
                                    else if(num_likes[x]>1)
                                        var like_text="<span class='status_update_like' style='cursor:default;'>"+num_likes[x]+" likes</span>";
                                    else
                                        var like_text="";
                                }

                                //display dislikes
                                if(user_ids_posted[x]!=<?php echo $_SESSION['id']; ?>)
                                {
                                    if(has_disliked[x]==true)
                                        var dislike_text="<span class='status_update_dislike' id='post_dislike_"+page+"_"+x+"' onClick='undislike_post("+profile_ids[x]+", "+post_ids[x]+", "+user_ids_posted[x]+", "+num_dislikes[x]+", "+page+", "+x+");' onmouseover=name_over(this); onmouseout=name_out(this); >Undislike ["+num_dislikes[x]+"]</span>";
                                    else if(num_dislikes[x]>=1)
                                        var dislike_text="<span class='status_update_dislike' id='post_dislike_"+page+"_"+x+"' onClick='dislike_post("+profile_ids[x]+", "+post_ids[x]+", "+user_ids_posted[x]+", "+num_dislikes[x]+", "+page+", "+x+");' onmouseover=name_over(this); onmouseout=name_out(this); >Dislike ["+num_dislikes[x]+"]</span>";
                                    else
                                        var dislike_text="<span class='status_update_dislike' id='post_dislike_"+page+"_"+x+"' onClick='dislike_post("+profile_ids[x]+", "+post_ids[x]+", "+user_ids_posted[x]+", "+num_dislikes[x]+", "+page+", "+x+");' onmouseover=name_over(this); onmouseout=name_out(this); >Dislike</span>";
                                }
                                else
                                {
                                    if(num_dislikes[x]==1)
                                        var dislike_text="<span class='status_update_like' style='cursor:default;'>1 dislike</span>";
                                    else if(num_dislikes[x]>1)
                                        var dislike_text="<span class='status_update_like' style='cursor:default;'>"+num_dislikes[x]+" dislikes</span>";
                                    else
                                        var dislike_text="";
                                }

                                //comments and stuff
                                if(num_comments[x]>=1)
                                    var comment_text="<span id='comment_title_"+page+"_"+x+"' class='comment_text' onClick='show_comment("+page+", "+x+")' onmouseover=name_over(this); onmouseout=name_out(this); >Comment ["+num_comments[x]+"]</span>";
                                else
                                    var comment_text="<span id='comment_title_"+page+"_"+x+"' class='comment_text' onClick='show_comment("+page+", "+x+")' onmouseover=name_over(this); onmouseout=name_out(this); >Comment</span>";
                                var comment_input="<div class='comment_input_body' id='comment_input_body_"+page+"_"+x+"'><textarea class='comment_textarea input_box' id='comment_input_"+page+"_"+x+"' placeholder='Comment...' maxlength='500'></textarea></div>";
                                var comment_body="<div class='comment_body' id='comment_body_"+page+"_"+x+"'></div>";


                                //styles like, dislike, and comment buttons
                                var post_functions=get_post_functions(like_text, dislike_text, comment_text);
                                var option_id="post_options_"+page+'_'+x;
                                
                                //display everything
                                var body=get_post_format(profile_picture, name, post+post_functions, comment_input+comment_body, timestamp, options, option_id, 'post_'+page+'_'+x, badges[x]);
//                                $('#page_'+page).html($('#page_'+page).html()+body);
                                html+=body;
                                functionality[x]="none";

                                count_time(timestamp_seconds[x], '#home_timestamp_'+page+'_timestamp_'+user_ids_posted[x]+'_'+x+'_'+post_ids[x]);





                                if(user_ids_posted[x]==<?php echo $_SESSION['id']; ?>)
                                    $('#home_posts_'+page+'_'+x).attr({'onmouseover': "show_close("+page+", "+x+");", 'onmouseout': "hide_close("+page+", "+x+");"});

                                     $('#comment_input_'+page+'_'+x).attr({'onFocus': "input_in(this);", 'onBlur': "input_out(this);"});

                                    //inserts title for profile pictures
//                                    $('#home_profile_picture_'+x).attr({'onmouseover': "{display_title(this, '"+names[x]+"');}", 'onmouseout': "{hide_title(this);}"});

                                    //inserts title for comment button
                                    $('#comment_title_'+page+'_'+x).attr({'onmouseover': "{name_over(this); display_title(this, 'Comment on this post');}", 'onmouseout': "{name_out(this); hide_title(this);}"});

                                    $('#post_options_'+page+'_'+x).attr({'onmouseover': "display_title(this, 'Display this posts options');", 'onmouseout': "hide_title(this);"})

                            }
                            else if(type[x]=='user_photo')
                            {
                                if(images[x]!='')
                                {
                                    image_descriptions[x]=text_format(image_descriptions[x]);
                                    var profile_picture="<a class='profile_picture_link' href='http://www.redlay.com/profile.php?user_id="+user_ids_posted[x]+"'><img class='profile_picture profile_picture_status' src='"+profile_pictures[x]+"'/><a/>";
                                    var name="<div class='user_name_body'><a href='http://www.redlay.com/profile.php?user_id="+user_ids_posted[x]+"' class='link'><span class='user_name' id='user_name_"+page+"_"+x+"' onmouseover=name_over(this); onmouseout=name_out(this); >"+names[x]+"</span></a></div>";
                                    var image="<table><tbody><tr><td><a class='picture_post_link' href='http://www.redlay.com/view_photo.php?user_id="+user_ids_posted[x]+"&&picture_id="+images[x]+"&&type=user'><img class='picture_post' id='picture_post_picture_"+page+"_"+x+"' src='https://s3.amazonaws.com/bucket_name/users/"+user_ids_posted[x]+"/thumbs/"+images[x]+"."+image_types[x]+"' /></a></td><td style='vertical-align:top;'><span class='text_color'>"+image_descriptions[x]+"</span></td></tr></tbody></table>";
                                    var timestamp="<p class='timestamp_status_update' id='home_timestamp_"+page+"_"+images[x]+"' >"+timestamps[x]+"</p>";
                                    var description="<p class='home_user_picture_post_description' >"+image_descriptions[x]+"</p>";
                                    var picture_break="<hr />";
                                    
                                    //comments and stuff
                                    if(num_comments[x]>=1)
                                        var comment_text="<span id='comment_title_"+page+"_"+x+"' class='comment_text' onClick='show_comment("+page+", "+x+")' onmouseover=name_over(this); onmouseout=name_out(this); >Comment ["+num_comments[x]+"]</span>";
                                    else
                                        var comment_text="<span id='comment_title_"+page+"_"+x+"' class='comment_text' onClick='show_comment("+page+", "+x+")' onmouseover=name_over(this); onmouseout=name_out(this); >Comment</span>";
                                    
                                    
                                    functionality[x]=new Array();
                                    //display likes
                                    if(user_ids_posted[x]!=<?php echo $_SESSION['id']; ?>)
                                    {
                                        if(has_liked[x]==true)
                                        {    
                                            var like_text="<span class='status_update_like' id='home_photo_like_"+page+"_"+x+"'  >Unlike ["+num_likes[x]+"]</span>";
                                            functionality[x][0]="photo_unlike";
                                        }
                                        else if(num_likes[x]>=1)
                                        {
                                            var like_text="<span class='status_update_like' id='home_photo_like_"+page+"_"+x+"' >Like ["+num_likes[x]+"]</span>";
                                            functionality[x][0]="photo_like";
                                        }
                                        else
                                        {
                                            var like_text="<span class='status_update_like' id='home_photo_like_"+page+"_"+x+"'  >Like</span>";
                                            functionality[x][0]="photo_like";
                                        }
                                    }
                                    else
                                    {
                                        if(num_likes[x]==1)
                                            var like_text="<span class='status_update_like' >1 like</span>";
                                        else if(num_likes[x]>1)
                                            var like_text="<span class='status_update_like' >"+num_likes[x]+" likes</span>";
                                        else
                                            var like_text="";
                                        
                                        functionality[x][0]="none";
                                    }

                                    //display dislikes
                                    if(user_ids_posted[x]!=<?php echo $_SESSION['id']; ?>)
                                    {
                                        if(has_disliked[x]==true)
                                        {    
                                            var dislike_text="<span class='status_update_dislike' id='home_photo_dislike_"+page+"_"+x+"'  >Undislike ["+num_dislikes[x]+"]</span>";
                                            functionality[x][1]="photo_undislike";
                                        }
                                        else if(num_dislikes[x]>=1)
                                        {
                                            var dislike_text="<span class='status_update_dislike' id='home_photo_dislike_"+page+"_"+x+"' >Dislike ["+num_dislikes[x]+"]</span>";
                                            functionality[x][1]="photo_dislike";
                                        }
                                        else
                                        {
                                            var dislike_text="<span class='status_update_dislike' id='home_photo_dislike_"+page+"_"+x+"' >Dislike</span>";
                                            functionality[x][1]="photo_dislike"
                                        }
                                    }
                                    else
                                    {
                                        if(num_dislikes[x]==1)
                                            var dislike_text="<span class='status_update_dislike' >1 dislike</span>";
                                        else if(num_dislikes[x]>1)
                                            var dislike_text="<span class='status_update_dislike' >"+num_dislikes[x]+" dislikes</span>";
                                        else
                                            var dislike_text="";

                                        functionality[x][1]="none";
                                    }
                                    
                                    
                                    
                                    var post_functions=get_post_functions(like_text, dislike_text, comment_text);
                                    var comment_input="<div class='comment_input_body' id='comment_input_body_"+page+"_"+x+"'><textarea class='comment_textarea input_box' id='comment_input_"+page+"_"+x+"' placeholder='Comment...' maxlength='500' onFocus='input_in(this);' onBlur='input_out(this);'></textarea></div>";
                                    var comment_body="<div class='comment_body' id='comment_body_"+page+"_"+x+"'></div>";
                                    
                                    var body=get_post_format(profile_picture, name, image+post_functions, comment_input+comment_body, timestamp, '', '', 'post_'+page+'_'+x, badges[x]);
//                                    $('#page_'+page).html($('#page_'+page).html()+body);
                                    html+=body;
                                    
                                    count_time(timestamp_seconds[x], '#home_timestamp_'+page+'_'+images[x]);
                                    
                                    
                                    
                                    
                                }
                            }
//                            else if(type[x]=='page_post')
//                            {
//                                //updates, user name, user profile picture, timestamps, and break
//                                var update="<p class='status_update_text'>"+posts[x]+"</p>";
//                                var name="<div class='user_name_body'><a href='http://www.redlay.com/page.php?page_id="+user_ids_posted[x]+"' class='home_post_name_link'><p class='user_name' id='user_name_"+x+"' onmouseover=name_over(this); onmouseout=name_out(this); >"+names[x]+"</p></a></div>";
//                                var picture="<div class='status_update' id='home_posts_"+page+"_"+x+"'><a href='http://www.redlay.com/page.php?page_id="+user_ids_posted[x]+"'><img class='profile_picture profile_picture_status' src='"+profile_pictures[x]+"' id='home_profile_picture_"+x+"' /></a>";
//                                var timestamp="<p class='timestamp_status_update'>"+timestamps[x]+"</p>";
//                                var post_break="<hr class='break'/></div>";
//
//                                var bool=false;
//                                var bool2=false;
//                                var num1=0;
//                                var num2=0;
//
//                                //if liked or disliked and number of likes and dislikes
//                                for(var z = 0; z < likes[x].length; z++)
//                                {
//                                    if(likes[x][z]==<?php echo $_SESSION['id']; ?>)
//                                        bool=true;
//                                    if(likes[x][z]!='0')
//                                        num1++;
//                                }
//                                for(var z = 0; z < dislikes[x].length; z++)
//                                {
//                                    if(dislikes[x][z]==<?php echo $_SESSION['id']; ?>)
//                                        bool2=true;
//                                    if(dislikes[x][z]!='0')
//                                        num2++;
//                                }
//
//
//                                //display likes
//                                if(bool==true)
//                                    var like_text="<div id='post_like_"+x+"' ><p class='status_update_like' id='home_post_like_"+page+"_"+x+"' onClick='unlike_page_post("+post_ids[x]+", "+profile_ids[x]+","+user_ids_posted[x]+", "+num1+", "+page+", "+x+");' >Unlike ["+num1+"]</p></div>";
//                                else if(num1!=0)
//                                    var like_text="<div id='post_like_"+x+"' ><p class='status_update_like' id='home_post_like_"+page+"_"+x+"' onClick='like_page_post("+post_ids[x]+", "+profile_ids[x]+","+user_ids_posted[x]+", "+num1+", "+page+", "+x+");' >Like ["+num1+"]</p></div>";
//                                else
//                                    var like_text="<div id='post_like_"+x+"' ><p class='status_update_like' id='home_post_like_"+page+"_"+x+"' onClick='like_page_post("+post_ids[x]+", "+profile_ids[x]+","+user_ids_posted[x]+", "+num1+", "+page+", "+x+");' >Like</p></div>";
//
//                                //display dislikes
//                                if(bool2==true)
//                                    var dislike_text="<div id='post_dislike_"+x+"' ><p class='status_update_dislike' id='home_post_dislike_"+page+"_"+x+"' onClick='undislike_page_post("+post_ids[x]+", "+profile_ids[x]+","+user_ids_posted[x]+", "+num2+", "+page+", "+x+");' >Undislike ["+num2+"]</p></div>";
//                                else if(num2!=0)
//                                    var dislike_text="<div id='post_dislike_"+x+"' ><p class='status_update_dislike' id='home_post_dislike_"+page+"_"+x+"' onClick='dislike_page_post("+post_ids[x]+", "+profile_ids[x]+","+user_ids_posted[x]+", "+num2+", "+page+", "+x+");' >Dislike ["+num2+"]</p></div>";
//                                else
//                                    var dislike_text="<div id='post_dislike_"+x+"' ><p class='status_update_dislike' id='home_post_dislike_"+page+"_"+x+"' onClick='dislike_page_post("+post_ids[x]+", "+profile_ids[x]+","+user_ids_posted[x]+", "+num2+", "+page+", "+x+");' >Dislike</p></div>";
//
//                                //comments and stuff
//                                if(comments[x][0]!='')
//                                    var comment_text="<p id='comment_title_"+page+"_"+x+"' class='comment_text' onClick='show_comment("+page+", "+x+")' onmouseover=name_over(this); onmouseout=name_out(this); >Comment ["+comments[x].length+"]</p>";
//                                else
//                                    var comment_text="<p id='comment_title_"+page+"_"+x+"' class='comment_text' onClick='show_comment("+page+", "+x+")' onmouseover=name_over(this); onmouseout=name_out(this); >Comment</p>";
//                                var comment_input="<div class='comment_input' id='comment_input_body_"+page+"_"+x+"'><textarea class='comment_textarea input_box' id='comment_input_"+page+"_"+x+"' placeholder='Comment...' maxlength='500'></textarea></div>";
//                                var comment_body="<div class='comment_body' id='comment_body_"+page+"_"+x+"'></div>";
//
//
//                                //styles like, dislike, and comment buttons
//                                var functions="<table class='post_functions_table'><tbody><tr><td class='post_functions_unit'>"+like_text+"</td><td class='post_functions_unit'>"+dislike_text+"</td><td class='post_functions_post_comment_unit'>"+comment_text+"</td></tr></tbody></table>";
//
//                                //display everything
//                                var content=$('#page_'+page).html();
//                                $('#page_'+page).html(content+picture+name+update+functions+comment_input+comment_body+timestamp+post_break);
//
//                                     $('#comment_input_'+page+'_'+x).attr({'onFocus': "input_in(this);", 'onBlur': "input_out(this);"});
//
//                                    //inserts title for profile pictures
//                                    $('#home_profile_picture_'+x).attr({'onmouseover': "{display_title(this, '"+names[x]+"');}", 'onmouseout': "{hide_title(this);}"});
//
//                                    //inserts title for comment button
//                                    $('#comment_title_'+x).attr({'onmouseover': "{name_over(this); display_title(this, 'Comment on post');}", 'onmouseout': "{name_out(this); hide_title(this);}"});
//
//                                    //inserts title for like and dislike buttons
//                                    if(bool2==true&&dislikes[x][0]!='0')
//                                        var dislike_title='Undislike';
//                                    else
//                                        var dislike_title='Dislike';
//                                    if(bool==true&&likes[x][0]!='0')
//                                        var like_title='Unlike';
//                                    else
//                                        var like_title='Like';
//                                    $('#home_post_like_'+page+'_'+x).attr({'onmouseover': "{name_over(this); display_title(this, '"+like_title+" this post');}", 'onmouseout': "{name_out(this); hide_title(this);}"});
//                                    $('#home_post_dislike_'+page+'_'+x).attr({'onmouseover': "{name_over(this); display_title(this, '"+dislike_title+" this post');}", 'onmouseout': "{name_out(this); hide_title(this);}"});
//                            }
//                            else if(type[x]=='page_photo')
//                            {
//                                if(images[x]!='')
//                                {
//                                    var profile_picture="<div class='home_user_picture_post' id='user_post_"+page+"_"+x+"'><a class='profile_picture_link' href='http://www.redlay.com/page.php?page_id="+user_ids_posted[x]+"'><img class='profile_picture profile_picture_status' src='"+profile_pictures[x]+"'/><a/>";
//                                    var name="<div class='user_name_body'><a href='http://www.redlay.com/page.php?page_id="+user_ids_posted[x]+"' class='home_post_name_link'><p class='user_name' id='user_name_"+page+"_"+x+"' onmouseover=name_over(this); onmouseout=name_out(this); >"+names[x]+"</p></a></div>";
//                                    var image="<div class='outside_picture' id='outside_picture_"+page+"_"+x+"'><a class='picture_post_link' href='http://www.redlay.com/view_photo.php?user_id="+user_ids_posted[x]+"&&picture_id="+images[x]+"&&type=page'><img class='picture_post' id='picture_post_picture_"+page+"_"+x+"' src='pages/"+user_ids_posted[x]+"/"+images[x]+"."+image_types[x]+"' /><a/></div>";
//                                    var timestamp="<p class='timestamp_status_update'>"+timestamps[x]+"</p></div>";
//                                    var description="<p class='home_user_picture_post_description' >"+image_descriptions[x]+"</p>";
//                                    var picture_break="<hr />";
//
//
//                                    $('#page_'+page).html($('#page_'+page).html()+profile_picture+name+image+description+timestamp+picture_break);
//                                    $('#outside_picture_'+page+'_'+x).attr({'onmouseover': "$(this).css('background-color', 'lightgray');", 'onmouseout': "$(this).css('background-color', 'white');"});
//                                }
//                            }
                            else if(type[x]!=null&&type[x][0]=='group_photo')
                            {
                                var profile_picture="<a class='profile_picture_link' href='http://www.redlay.com/profile.php?user_id="+user_ids_posted[x]+"'><img class='profile_picture profile_picture_status' src='"+profile_pictures[x]+"'  /><a/>";
                                var name="<a href='http://www.redlay.com/profile.php?user_id="+user_ids_posted[x]+"' class='home_post_name_link'><span class='user_name' id='user_name_"+page+"_"+x+"' onmouseover=name_over(this); onmouseout=name_out(this); >"+names[x]+"</span></a>";
                                
                                
                                
                                if(images[x].length>=5)
                                    var length=5;
                                else
                                    var length=images[x].length;
                                
                                var z_index=5;
                                var top=0;
                                var left=0;
                                var photo_html="";
                                for(var y = 0; y < images[x].length; y++)
                                {
                                    if(y<=5)
                                    {
                                        photo_html=photo_html+"<img  class='home_image' id='home_preview_image_"+page+"_"+x+"_"+y+"' style='z-index:"+z_index+";top:"+top+"px;left:"+left+"px;cursor:pointer;' src='https://s3.amazonaws.com/bucket_name/users/"+user_ids_posted[x]+"/thumbs/"+images[x][y]+"."+image_types[x][y]+"' onClick='display_group_images("+page+", "+x+");'/>";

                                        z_index--;
                                        top+=10;
                                        left+=10;
                                    }
                                    else
                                        photo_html=photo_html+"<div id='home_preview_image_"+page+"_"+x+"_"+y+"' ></div>";
                                }
//                                $('#image_group_body_'+page+'_'+x).html(photo_html);
                                
                                
                                
                                
                                
                                var image="<div class='image_group_body' id='image_group_body_"+page+"_"+x+"'>"+photo_html+"</div>";
                                var timestamp="<p class='timestamp_status_update'>"+timestamps[x][0]+"</p>";
                                var description="<span class='home_user_picture_post_description group_photo_description' style='cursor:pointer;' onmouseover=name_over(this); onmouseout=name_out(this); onClick='display_group_images("+page+", "+x+");'>("+images[x].length+") photos</span>";
                                var picture_break="<hr class='break'/>";
                                
                                
                               /////////////fix this///////////////////
                                var body="<div id='home_posts_"+page+"_"+x+"' class='status_update'><table style='width:100%;'><tbody><tr id='post_row_1' class='post_row'>  <td class='post_profile_picture_unit' style='vertical-align:top;'>"+profile_picture+"</td><td class='post_body_unit'>"+name+image+description+"</td>  </tr><tr id='post_row_2' class='post_row'>    </tr></tbody></table>"+picture_break+"</div>";
                                
                                
                                
//                                $('#page_'+page).html($('#page_'+page).html()+body);
                                html+=body;
                                
//                                $('#page_'+page).html($('#page_'+page).html()+profile_picture+name+image+description+timestamp+picture_break);
                                $('#outside_picture_'+page+'_'+x).attr({'onmouseover': "$(this).css('background-color', 'lightgray');", 'onmouseout': "$(this).css('background-color', 'white');"});
                                
//                                    var image="<div class='outside_picture' id='outside_picture_"+page+"_"+x+"'><a class='picture_post_link' href='http://www.redlay.com/view_photo.php?user_id="+user_ids_posted[x]+"&&picture_id="+images[x]+"&&type=user'><img class='picture_post' id='picture_post_picture_"+page+"_"+x+"' src='users/"+user_ids_posted[x]+"/thumbs/"+images[x]+"."+image_types[x]+"' /><a/></div>";
//                                    var image="<p>"+images[x].length+" total images</p>";
                                
                                functionality[x]="none";
                                
                            }
                            else if(type[x]!=null)
                            {
                                if(profile_pictures[x]!=null)
                                {
                                    var name="<a href='http://www.redlay.com/profile.php?user_id="+profile_ids[x]+"' class='home_post_name_link'><span class='user_name' id='user_name_"+x+"' onmouseover=name_over(this); onmouseout=name_out(this); >"+names[x]+"</span></a>";
                                    var picture="<div class='status_update' id='home_posts_"+page+"_"+x+"'><a href='http://www.redlay.com/profile.php?user_id="+profile_ids[x]+"'><img class='profile_picture profile_picture_status' src='"+profile_pictures[x]+"' id='home_profile_picture_"+x+"' /></a>";
                                    var timestamp="<p class='timestamp_status_update' id='home_timestamp_"+page+"_"+x+"_"+type[x]+"'>"+timestamps[x]+"</p>";


                                    if(type[x]=='add')
                                        var body="<table class='added_table'><tbody><tr><td class='home_other_text_unit'><span class='home_other_text'>added </span></td><td><a href='http://www.redlay.com/profile.php?user_id="+profile_ids[x]+"'><img class='profile_picture added_profile_picture' src='"+other_profile_pictures[x]+"' id='home_profile_picture_"+x+"' /></a><div class='user_name_body other_user_name_body'><a href='http://www.redlay.com/profile.php?user_id="+others[x]+"' class='home_post_name_link'><p class='user_name' id='user_name_"+x+"' onmouseover=name_over(this); onmouseout=name_out(this); >"+others_names[x]+"</p></a></div></td></tr></tbody></table>";
                                    else if(type[x]=='video')
                                    {
                                        if(profile_ids[x]!=<?php echo $_SESSION['id']; ?>)
                                            var video_share="<input class='red_button' value='Copy' type='button' onClick='share_video("+others[x]['video_id']+", "+user_ids_posted[x]+")' />";
                                        else
                                            var video_share="";
                                        
                                        
                                        if(others[x]['video_preview']!='')
                                            var body="<table class='added_table added_video_table' ><tbody><tr><td class='home_other_text_unit' id='video_share_"+page+"_"+x+"'>"+video_share+"</td><td id='video_body_"+page+"_"+x+"'>  <img class='video_preview' src='"+others[x]['video_preview']+"' id='video_preview_"+page+"_"+x+"' /> <img class='video_play_button' id='video_play_button_"+page+"_"+x+"' src='http://pics.redlay.com/pictures/play_button.png' /> </td></tr></tbody></table>";
                                        else
                                            var body="<table class='added_table added_video_table' ><tbody><tr><td class='home_other_text_unit' id='video_share_"+page+"_"+x+"'>"+video_share+"</td><td id='video_body_"+page+"_"+x+"'>  "+others[x]['video_url']+"  </td></tr></tbody></table>";
                                    }
                                    else if(type[x]=='page_like')
                                        var body="<table class='added_table'><tbody><tr><td class='home_other_text_unit'><span class='home_other_text'>liked </span></td><td><a href='http://www.redlay.com/page.php?page_id="+profile_ids[x]+"'><img class='profile_picture added_profile_picture' src='"+other_profile_pictures[x]+"' id='home_profile_picture_"+x+"' /></a><div class='user_name_body other_user_name_body'><a href='http://www.redlay.com/page.php?page_id="+profile_ids[x]+"' class='home_post_name_link'><p class='user_name' id='user_name_"+x+"' onmouseover=name_over(this); onmouseout=name_out(this); >"+others_names[x]+"</p></a></div></td></tr></tbody></table>";
                                    else if(type[x]=='relationship')
                                        var body="<table class='added_table'><tbody><tr><td class='home_other_text_unit'><span class='home_other_text'>is now "+others[x]+"</span></td></tr></tbody></table>";
                                    else if(type[x]=='mood')
                                        var body="<table class='added_table'><tbody><tr><td class='home_other_text_unit'><span class='home_other_text'>feels "+others[x]+"</span></td></tr></tbody></table>";
                                    else if(type[x]=='redlay_gold')
                                        var body="<table class='added_table'><tbody><tr><td class='home_other_text_unit'><span class='home_other_text'>bought <input type='button' value='redlay gold!' onClick=window.open('http://www.redlay.com/redlay_gold.php'); class='red_button'  /></span></td></tr></tbody></table>";

//                                    var extra_body="<div id='home_posts_"+page+"_"+x+"' class='status_update'><table style='width:100%;'><tbody><tr id='post_row_1' class='post_row'>  <td class='post_profile_picture_unit' style='vertical-align:top;'>"+picture+"</td><td class='post_body_unit'>"+name+body+"</td>  </tr><tr id='post_row_2' class='post_row'>  <td class='post_timestamp_unit' colspan='2'><span class='timestamp_status_update'>"+timestamp+"</span></td>  </tr></tbody></table>"+post_break+"</div>";
                                    var extra_body=get_post_format(picture, name, body, '', timestamp, '', '', "home_posts_"+page+'_'+x, badges[x]);
//                                    $('#page_'+page).html($('#page_'+page).html()+extra_body);
                                    html+=extra_body;
                                    count_time(timestamp_seconds[x], '#home_timestamp_'+page+'_'+x+'_'+type[x]);
                                    
                                    if(type[x]=='video')
                                        functionality[x]="video";
                                    else
                                        functionality[x]="none";


//                                    $('#page_'+page).html($('#page_'+page).html()+picture+name+body+timestamp+post_break);
                                }
                            }
                        }
                        
                        
                        for(var x = 0; x < type.length; x++)
                        {
                            //adds video data
                            if(type[x]!=null&&type[x]=='video')
                                $('#video_body_'+page+'_'+x).data('vid_embed', others[x]['video_url']);
                        }
                        

                        //pastes html
                        $('#page_'+page).html(html);

                        for(var x = 0; x < functionality.length; x++)
                        {
                            if(functionality[x]!="none")
                            {
                                if(functionality[x]=="video")
                                {
                                    $('#video_body_'+page+'_'+x).attr('onClick', "display_actual_video('#video_body_"+page+"_"+x+"');");
                                    $('#video_preview_'+page+'_'+x).attr({'onmouseover': "video_over('#video_preview_"+page+"_"+x+"', '#video_play_button_"+page+"_"+x+"');",  'onmouseout': "video_out('#video_preview_"+page+"_"+x+"', '#video_play_button_"+page+"_"+x+"');"});
                                    $('#video_play_button_'+page+'_'+x).attr('onmouseover', "video_over('#video_preview_"+page+"_"+x+"', '#video_play_button_"+page+"_"+x+"');");
                                }
                                else
                                {
                                    //adds onClick functionality to like and dislike buttons
                                    console.log(functionality[x][0]);
                                    if(functionality[x][0]=='photo_unlike')
                                        $('#home_photo_like_'+page+'_'+x).attr({'onClick': "unlike_photo('"+images[x]+"', "+user_ids_posted[x]+", 'user', "+page+", "+x+", "+num_likes[x]+");", 'onmouseover': "name_over(this);", 'onmouseout': "name_out(this);"});
                                    else if(functionality[x][0]=='photo_like')
                                        $('#home_photo_like_'+page+'_'+x).attr({'onClick': "like_photo('"+images[x]+"', "+user_ids_posted[x]+", 'user', "+page+", "+x+", "+num_likes[x]+");", 'onmouseover': "name_over(this);", 'onmouseout': "name_out(this);"});

                                    if(functionality[x][0]=='photo_undislike')
                                        $('#home_photo_dislike_'+page+'_'+x).attr({'onClick': "undislike_photo('"+images[x]+"', "+user_ids_posted[x]+", 'user', "+page+", "+x+", "+num_dislikes[x]+");", 'onmouseover': "name_over(this);", 'onmouseout': "name_out(this);"});
                                    else if(functionality[x][0]=='photo_dislike')
                                        $('#home_photo_dislike_'+page+'_'+x).attr({'onClick': "dislike_photo('"+images[x]+"', "+user_ids_posted[x]+", 'user', "+page+", "+x+", "+num_dislikes[x]+");", 'onmouseover': "name_over(this);", 'onmouseout': "name_out(this);"});
                                }
                            }
                        }

                        ///////////displays comments/////////////////////
                        for(var x = 0; x < type.length; x++)
                        {
                            if(type[x]=='user_post'||type[x]=='user_photo'||type[x]=='page_post'||type[x]=='page_photo')
                            {
                                //binds data for when user clicks to display or post comments
                                if(type[x]=='user_post')
                                    $('#comment_input_'+page+'_'+x).data({'post_id': post_ids[x], 'profile_id': profile_ids[x], 'poster_id': user_ids_posted[x], 'page': page, 'index': x, 'type': type[x], 'num_comments': num_comments[x]});
                                else if(type[x]=='user_photo')
                                    $('#comment_input_'+page+'_'+x).data({'picture_id': images[x], 'user_id': user_ids_posted[x], 'type': type[x], 'image_type ': image_types[x], 'page': page, 'index': x, 'num_comments': num_comments[x]});
                                
                                
                                
                                $('#comment_title_'+page+'_'+x).attr({'onClick': "{show_comment("+page+", "+x+");}",'onmouseover': "{name_over(this); }", 'onmouseout': "{name_out(this); }"});

                               
                                $('#comment_title_'+page+'_'+x).data({'number': num_comments[x]});

                                //deletes previous html
                                $('#comment_body_'+page+'_'+x).html('');
                                
                                //displays comment pages
//                                if(num_comments[x]>10)
//                                {
//                                    $('#comment_body_'+page+'_'+x).html('');
//                                    
//                                    for(var y = 1; y <= (num_comments[x]/10)+1; y++)
//                                        $('#comment_body_'+page+'_'+x).html($('#comment_body_'+page+'_'+x).html()+"<div class='comment_page' id='comment_page_"+page+"_"+x+"_"+y+"'></div>");
//                                    
//                                    $("#comment_body_"+page+"_"+x).html($("#comment_body_"+page+"_"+x).html()+"<div id='see_more_comment_body'></div>");
//                                }
//                                else
//                                    $('#comment_body_'+page+'_'+x).html("<div class='comment_page' id='comment_page_"+page+"_"+x+"_1'></div>");
                                
                                

                                if(num_comments[x]>=1)
                                {
                                    for(var y = 0; y < comments[x].length; y++)
                                    {
                                        comments[x][y]=convert_image(text_format(comments[x][y]), 'comment');
                                        
                                        var string="http://www.redlay.com/profile.php?user_id="+comments_users_sent[x][y];
                                        var name="<a href='"+string+"' class='link'><span class='comment_name title_color' id='home_comment_name_"+page+"_"+x+"_"+y+"' onmouseover=name_over(this); onmouseout=name_out(this); >"+comment_names[x][y]+"</span></a>";
                                        var picture="<a href='"+string+"' class='link'><img class='profile_picture profile_picture_comment' src='"+comment_profile_pictures[x][y]+"' /></a>";
                                        var comment="<p class='comment_text_body text_color'>"+comments[x][y]+"</p>";
                                        var timestamp="<p class='comment_timestamp text_color' id='comment_timestamp_"+page+"_"+type[x]+"_"+comments_users_sent[x][y]+"_"+comment_ids[x][y]+"_"+x+"'>"+comment_timestamps[x][y]+"</p>";
                                        if(comments_users_sent[x][y]==<?php echo $_SESSION['id']; ?>)
                                            var options="<div class='post_delete' id='comment_options_"+page+"_"+x+"_"+y+"' >x</div>";
                                        else
                                            var options="";




                                        //displaying likes
                                        if(comments_users_sent[x][y]!=<?php echo $_SESSION['id']; ?>)
                                        {
                                            if(has_liked_comment[x][y]==true)
                                                var like="<span class='comment_like' id='comment_like_"+page+"_"+x+"_"+y+"' onmouseover=name_over(this); onmouseout=name_out(this); >Unlike ["+num_comment_likes[x][y]+"]</span>";
                                            else if(num_comment_likes[x][y]>=1)
                                                var like="<span class='comment_like' id='comment_like_"+page+"_"+x+"_"+y+"' onmouseover=name_over(this); onmouseout=name_out(this); >Like ["+num_comment_likes[x][y]+"]</span>";
                                            else
                                                var like="<span class='comment_like' id='comment_like_"+page+"_"+x+"_"+y+"' onmouseover=name_over(this); onmouseout=name_out(this); >Like</span>";
                                        }
                                        else
                                        {
                                            if(num_comment_likes[x][y]==1)
                                                var like="<span class='comment_like' >1 like</span>";
                                            else if(num_comment_likes[x][y]>1)
                                                var like="<span class='comment_like' >"+num_comment_likes[x][y]+" likes</span>";
                                            else
                                                var like="";
                                        }

                                        //displaying dislikes
                                        if(comments_users_sent[x][y]!=<?php echo $_SESSION['id']; ?>)
                                        {
                                            if(has_disliked_comment[x][y]==true)
                                                var dislike="<span class='comment_dislike' id='comment_dislike_"+page+"_"+x+"_"+y+"' onmouseover=name_over(this); onmouseout=name_out(this); >Undislike ["+num_comment_dislikes[x][y]+"]</span>";
                                            else if(num_comment_dislikes[x][y]>=1)
                                                var dislike="<span class='comment_dislike' id='comment_dislike_"+page+"_"+x+"_"+y+"' onmouseover=name_over(this); onmouseout=name_out(this); >Dislike ["+num_comment_dislikes[x][y]+"]</span>";
                                            else
                                                var dislike="<span class='comment_dislike' id='comment_dislike_"+page+"_"+x+"_"+y+"' onmouseover=name_over(this); onmouseout=name_out(this); >Dislike</span>";
                                        }
                                        else
                                        {
                                            if(num_comment_dislikes[x][y]==1)
                                                var dislike="<span class='comment_dislike' >1 dislike</span>";
                                            else if(num_comment_dislikes[x][y]>1)
                                                var dislike="<span class='comment_dislike' >"+num_comment_dislikes[x][y]+" dislikes</span>";
                                            else
                                                var dislike="";
                                        }
                                        
                                        //if(comments_users_sent[x][y]!=<?php echo $_SESSION['id']; ?>)
                                          var functions=get_comment_functions(like, dislike);
                                        //else
                                           //var functions="";

                                        var body=get_post_format(picture, name+comment+functions, '', '', timestamp, options, "comment_options_"+page+"_"+x+"_"+y, "comment_body_"+page+'_'+x+'_'+y, comment_badges[x][y]);
                                        $('#comment_body_'+page+'_'+x).html(body+$('#comment_body_'+page+'_'+x).html());
                                        



                                        if(type[x]=='user_post')
                                        {
                                            if(comments_users_sent[x][y]!=<?php echo $_SESSION['id']; ?>)
                                            {
                                                if(has_liked_comment[x][y]==true)
                                                    $('#comment_like_'+page+'_'+x+'_'+y).attr({'onClick': "unlike_comment("+profile_ids[x]+", "+post_ids[x]+", "+x+", "+comment_ids[x][y]+", "+y+", "+page+", "+num_comment_likes[x][y]+", 'user');"});
                                                else
                                                    $('#comment_like_'+page+'_'+x+'_'+y).attr({'onClick': "like_comment("+profile_ids[x]+", "+post_ids[x]+", "+x+", "+comment_ids[x][y]+", "+y+", "+page+", "+num_comment_likes[x][y]+", 'user');"});

                                                if(has_disliked_comment[x][y]==true)
                                                    $('#comment_dislike_'+page+'_'+x+'_'+y).attr({'onClick': "undislike_comment("+profile_ids[x]+", "+post_ids[x]+", "+x+", "+comment_ids[x][y]+", "+y+", "+page+", "+num_comment_dislikes[x][y]+", 'user');"});
                                                else
                                                    $('#comment_dislike_'+page+'_'+x+'_'+y).attr({'onClick': "dislike_comment("+profile_ids[x]+", "+post_ids[x]+", "+x+", "+comment_ids[x][y]+", "+y+", "+page+", "+num_comment_dislikes[x][y]+", 'user');"});
                                            }
                                            
                                            $('#comment_options_'+page+'_'+x+'_'+y).attr('onClick', "delete_comment("+profile_ids[x]+", "+post_ids[x]+", "+x+", "+comment_ids[x][y]+", "+y+", "+page+");");
                                        }
                                        else if(type[x]=='user_photo')
                                        {
                                            if(comments_users_sent[x][y]!=<?php echo $_SESSION['id']; ?>)
                                            {
                                                if(has_liked_comment[x][y]==true)
                                                    $('#comment_like_'+page+'_'+x+'_'+y).attr({'onClick': "unlike_photo_comment("+profile_ids[x]+", '"+images[x]+"', "+x+", "+comment_ids[x][y]+", "+y+", "+page+", "+num_comment_likes[x][y]+", 'user');"});
                                                else
                                                    $('#comment_like_'+page+'_'+x+'_'+y).attr({'onClick': "like_photo_comment("+profile_ids[x]+", '"+images[x]+"', "+x+", "+comment_ids[x][y]+", "+y+", "+page+", "+num_comment_likes[x][y]+", 'user');"});

                                                if(has_disliked_comment[x][y]==true)
                                                    $('#comment_dislike_'+page+'_'+x+'_'+y).attr({'onClick': "undislike_photo_comment("+profile_ids[x]+", '"+images[x]+"', "+x+", "+comment_ids[x][y]+", "+y+", "+page+", "+num_comment_dislikes[x][y]+", 'user');"});
                                                else
                                                    $('#comment_dislike_'+page+'_'+x+'_'+y).attr({'onClick': "dislike_photo_comment("+profile_ids[x]+", '"+images[x]+"', "+x+", "+comment_ids[x][y]+", "+y+", "+page+", "+num_comment_dislikes[x][y]+", 'user');"});
                                            }
                                            
                                            $('#comment_options_'+page+'_'+x+'_'+y).attr('onClick', "delete_photo_comment("+profile_ids[x]+", '"+images[x]+"', "+x+", "+comment_ids[x][y]+", "+y+", "+page+", 'user');");
                                        }
                                        else
                                        {
//                                            if(has_liked)
//                                                $('#home_comment_like_'+page+'_'+x+'_'+y).attr({'onClick': "unlike_page_comment("+post_ids[x]+", "+x+", "+y+", "+page+", "+profile_ids[x]+", "+num_comment_likes[x][y]+", "+comment_ids[x][y]+");"});
//                                            else
//                                                $('#home_comment_like_'+page+'_'+x+'_'+y).attr({'onClick': "like_page_comment("+post_ids[x]+", "+x+", "+y+", "+page+", "+profile_ids[x]+", "+num_comment_likes[x][y]+", "+comment_ids[x][y]+");"});
//
//                                            if(has_disliked)
//                                                $('#home_comment_dislike_'+page+'_'+x+'_'+y).attr({'onClick': "undislike_page_comment("+post_ids[x]+", "+x+", "+y+", "+page+", "+profile_ids[x]+", "+num_comment_dislikes[x][y]+", "+comment_ids[x][y]+")"});
//                                            else
//                                                $('#home_comment_dislike_'+page+'_'+x+'_'+y).attr({'onClick': "dislike_page_comment("+post_ids[x]+", "+x+", "+y+", "+page+", "+profile_ids[x]+", "+num_comment_dislikes[x][y]+", "+comment_ids[x][y]+")"});
                                        }


                                    }
                                }
                                else
                                    $("#comment_body_"+page+"_"+x).html("There are no comments");
                            }
                        }
                        
                        for(var x = 0; x < type.length; x++)
                        {
                            //displays dynamic timestamps
                            if(num_comments[x]>=1)
                            {
                                for(var y = 0; y < comments[x].length; y++)
                                {
                                    count_time(comment_timestamp_seconds[x][y], "#comment_timestamp_"+page+"_"+type[x]+"_"+comments_users_sent[x][y]+"_"+comment_ids[x][y]+"_"+x);
                                }
                            }
                        }

                        //modifies, creates, or deletes see_more button
                        if($('.see_more_posts').length!=0&&empty==false)
                            $('.see_more_posts').attr({'onClick': "display_everything("+(page+1)+");"});
                        else if(empty==false)
                        {
                            $('#see_more_body').html("<input class='see_more_posts blue_button' id='see_more_post_button' value='See More' type='button' >");
                            $('#see_more_post_button').attr({'onmouseover': "{display_title(this, 'See more posts');}", 'onmouseout': "{hide_title(this);}", 'onClick': "{display_everything("+(page+1)+");}"});
                        }
                        else
                            $('#see_more_body').html('');

                        $('.comment_body').hide();
                        $('.comment_input_body').hide();
                        $('.comment_textarea').hide();
                        
                        for(var x = 0; x < type.length; x++)
                        {
                            if(type[x]!=null&&type[x][0]=='group_photo')
                            {
                                for(var y = 0; y < images[x].length; y++)
                                    $('#home_preview_image_'+page+'_'+x+'_'+y).data({'src': "http://u.redlay.com/users/"+user_ids_posted[x]+"/thumbs/"+images[x][y]+"."+image_types[x][y], 'link': "http://www.redlay.com/view_photo.php?user_id="+user_ids_posted[x]+"&&picture_id="+images[x][y]+"&&type=user"});
                            }
                        }
                    }
                    else
                        $('#home_post_text').html("<p class='empty_text'>There is nothing to display here</p>");


                    $('#friend_posts_load').hide();
                    $('.post_delete').hide();
                    initialize_comment_events();
                    change_color();
                }, "json");
            }
            
            function display_actual_video(vid_id)
            {
                $(vid_id).html($(vid_id).data('vid_embed'));
            }
            function display_group_images(page, x)
            {
                var links=new Array();
                var srcs=new Array();
                
                var index=0;
                while($('#home_preview_image_'+page+'_'+x+'_'+index).length)
                {
                    links[index]=$('#home_preview_image_'+page+'_'+x+'_'+index).data('link');
                    srcs[index]=$('#home_preview_image_'+page+'_'+x+'_'+index).data('src');
                    index++;
                }
                
//                $('.alert_box').css('opacity', 1).show().draggable();
//                $('.alert_box_inside').html("<table ><tbody id='all_group_images_"+page+"'></tbody></table>");
                
                display_alert("("+links.length+") photos", "<div style='height:465px;overflow:scroll;'><table ><tbody id='all_group_images_"+page+"'></tbody></table></div>", 'group_photos_extra', 'group_photos_load', '');
                $('#group_photos_load').hide();
                
                for(var x = 0; x < links.length/3; x++)
                    $('#all_group_images_'+page).html($('#all_group_images_'+page).html()+"<tr id='group_images_row_"+x+"'></tr>");
                
                var index=0;
                var num=1;
                for(var x = 0; x < links.length; x++)
                {
                    $('#group_images_row_'+index).html($('#group_images_row_'+index).html()+"<td><a href='"+links[x]+"'><img class='home_group_image' src='"+srcs[x]+"'/></a></td>");
                    if(num%3==0)
                        index++;
                    num++;
                }
                change_color();
            }
//            function show_video_embed(div_id, embed)
//            {
//                $(div_id).html(embed);
//            }
            function show_close(page, index)
            {
                var string="#post_options_"+page+'_'+index;
                $(string).show();
            }
            function hide_close(page, index)
            {
                var string="#post_options_"+page+'_'+index;
                $(string).hide();
            }

//            function like_page_post(post_id, page_id, poster_id, number, page, index)
//            {
//                $.post('like_page_post.php',
//                {
//                    post_id: post_id,
//                    page_id: page_id,
//                    poster_id: poster_id
//                }, function (output)
//                {
//                    number++;
//                    $('#home_post_like_'+page+'_'+index).html("Unlike ["+number+"]").attr('onClick', "unlike_page_post("+post_id+", "+page_id+", "+poster_id+", "+number+", "+page+", "+index+");");
//                });
//            }
//
//            function unlike_page_post(post_id, page_id, poster_id, number, page, index)
//            {
//                $.post('unlike_page_post.php',
//                {
//                    post_id: post_id,
//                    page_id: page_id,
//                    poster_id: poster_id
//                }, function (output)
//                {
//                    number--;
//                    if(number==0)
//                        $('#home_post_like_'+page+'_'+index).html("Like");
//                    else
//                        $('#home_post_like_'+page+'_'+index).html("Like ["+number+"]");
//                    $('#home_post_like_'+page+'_'+index).attr('onClick', "like_page_post("+post_id+", "+page_id+", "+poster_id+", "+number+", "+page+", "+index+");");
//                });
//            }
//
//            function dislike_page_post(post_id, page_id, poster_id, number, page, index)
//            {
//                $.post('dislike_page_post.php',
//                {
//                    post_id: post_id,
//                    page_id: page_id,
//                    poster_id: poster_id
//                }, function (output)
//                {
//                    number++;
//                    $('#home_post_dislike_'+page+'_'+index).html("Undislike ["+number+"]").attr('onClick', "undislike_page_post("+post_id+", "+page_id+", "+poster_id+", "+number+", "+page+", "+index+");");
//                });
//            }
//            function undislike_page_post(post_id, page_id, poster_id, number, page, index)
//            {
//                $.post('undislike_page_post.php',
//                {
//                    post_id: post_id,
//                    page_id: page_id,
//                    poster_id: poster_id
//                }, function (output)
//                {
//                    number--;
//                    if(number==0)
//                        $('#home_post_dislike_'+page+'_'+index).html("Dislike");
//                    else
//                        $('#home_post_dislike_'+page+'_'+index).html("Dislike ["+index+"]");
//                    $('#home_post_dislike_'+page+'_'+index).attr('onClick', "dislike_page_post("+post_id+", "+page_id+", "+poster_id+", "+number+", "+page+", "+index+");");
//                });
//            }
//            function page_post_comment(post_id, page_id, poster_id, page, index)
//            {
//                $.post('page_post_comment.php',
//                {
//                    post_id: post_id,
//                    comment_text: $("#comment_input_"+page+"_"+index).val(),
//                    page_id: page_id,
//                    poster_id: poster_id
//                }, function (output)
//                {
//                    //post_comments();
//                    $('#comment_title_'+page+'_'+index).data('number', ($('#comment_title_'+page+'_'+index).data('number')+1));
//                    $('#comment_title_'+page+'_'+index).html("Comment ["+$('#comment_title_'+page+'_'+index).data('number')+"]");
//                });
//            }
            function show_comment(page, index)
            {
                $('#comment_body_'+page+'_'+index).show();
                $('#comment_input_body_'+page+'_'+index).show();
                $('#comment_input_'+page+'_'+index).show();
                $('#comment_title_'+page+'_'+index).attr("onClick", "hide_comment("+page+", "+index+");");
            }
            function hide_comment(page, index)
            {
                $('#comment_body_'+page+'_'+index).hide();
                $('#comment_input_body_'+page+'_'+index).hide();
                $('#comment_title_'+page+'_'+index).attr("onClick", "show_comment("+page+", "+index+");");
            }

            function change_view_options(num)
            {
                var value=$('#select_view_text_'+num).data('val');
                toggle_select_view(1);
                $('#home_title').html(value);
                $('#select_view_button').val(value).data('val', value);
                change_view();
                display_everything(1);
            }
            function change_from_options(num)
            {
                var type=$('#select_from_text_'+num).data('type');
                toggle_select_view(2);
                $('#select_from_button').val(type).data('type', type);

                if(type=='Pages'||type=='All')
                {
                    $('#select_in_button').attr({'class': 'disabled_gray_button', 'onClick': ""});
                    $('#select_from_row').hide();
                }
                else
                {
                    $('#select_in_button').attr({'class': 'gray_button', 'onClick': "toggle_select_view(3);"});
                    $('#select_from_row').show();
                }
                display_everything(1);
            }
            function change_in_options(num)
            {
                var group_name=$('#select_in_options_text_'+num).data('group_name');
                toggle_select_view(3);
                $('#select_in_button').val(group_name).data('group_name', group_name);
                display_everything(1);
            }
            function change_user_options(num)
            {
                $('#select_user_body_options').hide();
                var name=$('#select_user_options_text_'+num).data('user_name');
                $('#select_user_button').val(name).data('user_id', $('#select_user_options_text_'+num).data('user_id'));


                //disables unneeded buttons
                if($('#select_user_options_text_'+num).data('user_id')!=-1)
                {
                    $('#select_from_button').attr({'onClick': '', 'class': 'disabled_gray_button'}).css('opacity', '0.5');
                    $('#select_in_button').attr({'onClick': '', 'class': 'disabled_gray_button'}).css('opacity', '0.5');
                    $('#select_page_button').attr({'onClick': '', 'class': 'disabled_gray_button'}).css('opacity', '0.5');
                }
                else
                {
                    $('#select_from_button').attr({'onClick': 'toggle_select_view(2);', 'class': 'gray_button'}).css('opacity', '1');
                    $('#select_in_button').attr({'onClick': 'toggle_select_view(3);', 'class': 'gray_button'}).css('opacity', '1');
                    $('#select_page_button').attr({'onClick': 'toggle_select_view(5);', 'class': 'gray_button'}).css('opacity', '1');
                }
                change_view();

                display_everything(1);
            }
            function change_page_options(num)
            {
                var name=$('#select_page_options_text_'+num).data('page_name');
                toggle_select_view(5);
                $('#select_page_button').val(name).data('page_id', $('#select_page_options_text_'+num).data('page_id'));

                //disables unneeded buttons
                if($('#select_user_options_text_'+num).data('user_id')!=-1)
                {
                    $('#select_from_button').attr({'onClick': '', 'class': 'disabled_gray_button'}).css('opacity', '0.5');
                    $('#select_in_button').attr({'onClick': '', 'class': 'disabled_gray_button'}).css('opacity', '0.5');
                    $('#select_user_button').attr({'onClick': '', 'class': 'disabled_gray_button'}).css('opacity', '0.5');
                }
                else
                {
                    $('#select_from_button').attr({'onClick': 'toggle_select_view(2);', 'class': 'gray_button'}).css('opacity', '1');
                    $('#select_in_button').attr({'onClick': 'toggle_select_view(3);', 'class': 'gray_button'}).css('opacity', '1');
                    $('#select_user_button').attr({'onClick': 'toggle_select_view(4);', 'class': 'gray_button'}).css('opacity', '1');
                }

                display_everything(1);
            }
            
            function display_specific_comments(page, post_index)
            {
                $.post('home_query.php',
                {
                    
                }, function(output)
                {

                });
            }

            function toggle_select_view(num)
            {
                if(num==1)
                {
                    if($('#select_view_body_options').css('display')!='block')
                        $('#select_view_body_options').show();
                    else
                        $('#select_view_body_options').hide();

                    //hides other view options
                    $('#select_from_body_options').hide();
                    $('#select_in_body_options').hide();
                    $('#select_user_body_options').hide();
                    $('#select_page_body_options').hide();
                }
                else if(num==2)
                {
                    if($('#select_from_body_options').css('display')!='block')
                        $('#select_from_body_options').show();
                    else
                        $('#select_from_body_options').hide();

                    //hides other view options
                    $('#select_view_body_options').hide();
                    $('#select_in_body_options').hide();
                    $('#select_user_body_options').hide();
                    $('#select_page_body_options').hide();
                }
                else if(num==3)
                {
                    if($('#select_in_body_options').css('display')!='block')
                        $('#select_in_body_options').show();
                    else
                        $('#select_in_body_options').hide();

                    //hides other view options
                    $('#select_view_body_options').hide();
                    $('#select_from_body_options').hide();
                    $('#select_user_body_options').hide();
                    $('#select_page_body_options').hide();
                }
                else if(num==4)
                {
                    if($('#select_user_body_options').css('display')=='block')
                        $('#select_user_body_options').hide();
                    else
                        $('#select_user_body_options').show();

                    //hides other view options
                    $('#select_view_body_options').hide();
                    $('#select_from_body_options').hide();
                    $('#select_in_body_options').hide();
                    $('#select_page_body_options').hide();
                }
                else if(num==5)
                {
                    if($('#select_page_body_options').css('display')!='block')
                        $('#select_page_body_options').show();
                    else
                        $('#select_page_body_options').hide();

                    //hides other view options
                    $('#select_view_body_options').hide();
                    $('#select_from_body_options').hide();
                    $('#select_in_body_options').hide();
                    $('#select_user_body_options').hide();
                }
            }
            function fill_in_box()
            {
                $.post('user_groups_query.php',
                {
                    num:1
                }, function(output)
                {
                    var groups=output.groups;

                    for(var x = 0; x < groups.length; x++)
                    {
                        $('#select_in_options_table_body').html($('#select_in_options_table_body').html()+"<tr class='select_body_options_row' id='select_in_options_row_"+x+"'></tr>");
                        $('#select_in_options_row_'+x).html("<td class='select_body_options_unit' id='select_body_options_unit_"+x+"' onClick='change_in_options("+x+");'><p class='select_body_option_text' id='select_in_options_text_"+x+"'>"+groups[x]+"</p></td>");
                        $('#select_body_options_unit_'+x).attr({'onmouseover': "$('#select_in_options_row_"+x+"').css('background-color', 'rgb(200,200,200)');", 'onmouseout': "$('#select_in_options_row_"+x+"').css('background-color', '');"});
                    }

                    for(var x = 0; x < groups.length; x++)
                        $('#select_in_options_text_'+x).data({'group_name': groups[x]});


                    change_color();
                }, "json");
            }

            function fill_user_box()
            {
                $.post('home_names_query.php',
                {

                }, function(output)
                {
                    var friends=output.adds;
                    var names=output.names;
                    var profile_pictures=output.profile_pictures;
//                    var pages=output.pages;
//                    var page_names=output.page_names;

                    for(var x = 0; x < friends.length; x++)
                    {
                        $('#select_user_options_table_body').html($('#select_user_options_table_body').html()+"<tr class='select_body_options_row' id='select_user_options_row_"+x+"'></tr>");
                        if(x!=0)
                            $('#select_user_options_row_'+x).html("<td class='select_body_picture_options'><img class='select_user_profile_picture' src='"+profile_pictures[x]+"' /></td><td class='select_body_options_unit' id='select_user_body_options_unit_"+x+"' onClick='change_user_options("+x+");'><p class='select_body_option_text' id='select_user_options_text_"+x+"'>"+names[x]+"</p></td>");
                        else
                            $('#select_user_options_row_'+x).html("<td class='select_body_picture_options'></td><td class='select_body_options_unit' id='select_user_body_options_unit_"+x+"' ><p class='select_body_option_text' id='select_user_options_text_"+x+"'>"+names[x]+"</p></td>");
                        $('#select_user_options_row_'+x).attr({'onmouseover': "$('#select_user_options_row_"+x+"').css('background-color', 'rgb(200,200,200)');", 'onmouseout': "$('#select_user_options_row_"+x+"').css('background-color', '');", 'onClick': "change_user_options("+x+")"});
                    }

//                    for(var x = 0; x < pages.length; x++)
//                    {
//                        $('#select_page_options_table_body').html($('#select_page_options_table_body').html()+"<tr class='select_body_options_row' id='select_page_options_row_"+x+"'></tr>");
//                        $('#select_page_options_row_'+x).html("<td class='select_body_options_unit' id='select_page_body_options_unit_"+x+"' onClick='change_page_options("+x+");'><p class='select_body_option_text' id='select_page_options_text_"+x+"'>"+page_names[x]+"</p></td>");
//                        $('#select_page_body_options_unit_'+x).attr({'onmouseover': "$('#select_page_options_row_"+x+"').css('background-color', 'rgb(200,200,200)');", 'onmouseout': "$('#select_page_options_row_"+x+"').css('background-color', '');"});
//                    }

                    for(var x = 0; x < friends.length; x++)
                    {
                        if(x!=0)
                            $('#select_user_options_text_'+x).data({'user_id': friends[x], 'user_name': names[x]});
                        else
                            $('#select_user_options_text_0').data({'user_id': -1, 'user_name': names[x]});
                    }
//                    for(var x = 0; x < pages.length; x++)
//                    {
//                        if(x!=0)
//                            $('#select_page_options_text_'+x).data({'page_id': pages[x], 'page_name': page_names[x]});
//                        else
//                            $('#select_page_options_text_0').data({'page_id': -1, 'page_name': page_names[x]});
//                    }


                    change_color();
                }, "json");
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


                    var profile_picture="<img class='profile_picture_status profile_picture' src='http://u.redlay.com/users/<?php echo $_SESSION['id']; ?>/thumbs/0.jpg' id='text_format_profile_picture' />";
                    var text="<p class='status_update_text text_color' id='text_format_text' style='width:315px;'></p>";
                    var name="<div class='user_name_body'><span class='user_name' id='text_format_preview_name'><?php echo get_user_name($_SESSION['id']); ?></span></div>";

                    var row_1="<tr id='post_row_1' class='post_row'>  <td class='post_profile_picture_unit'>"+profile_picture+"</td><td class='post_body_unit'>"+name+text+"</td>  </tr>";

                    $('#text_format_preview_box').html("<div id='text_format_preview' class='status_update' style='margin:5px'><table style='width:100%;'><tbody>"+row_1+"</tbody></table></div>");

                            $('#text_format_info').html("<table style='width:100%;margin-top:20px;' border='1'><tbody id='text_format_info_table_body'></tbody></table>");
                                $('#text_format_info_table_body').html("<tr id='text_format_row_1'></tr><tr id='text_format_row_2'></tr><tr id='text_format_row_3'></tr><tr id='text_format_row_4'></tr><tr id='text_format_row_5'></tr><tr id='text_format_row_6'></tr><tr id='text_format_row_7'></tr>");
                                    $('#text_format_row_1').html("<td><p style='font-weight:bold;margin:5px;'>Bold:</p></td><td><p style='margin:5px;'>[b](This is bold) = <span style='font-weight:bold;'>This is bold</span></p></td>");
                                    $('#text_format_row_2').html("<td><p style='font-style:italic;margin:5px;'>Italics:</p></td><td><p style='margin:5px;'>[i](This is italics) = <span style='font-style:italic'>This is italics</span></p></td>");
                                    $('#text_format_row_3').html("<td><p style='text-decoration:underline;margin:5px;'>Underline:</p></td><td><p style='margin:5px;'>[u](This is underlined) = <span style='text-decoration:underline;'>This is underlined</span></p></td>");
                                    $('#text_format_row_4').html("<td><p style='margin:5px;'><span style='color:red;'>C</span><span style='color:orange;'>o</span><span style='color:purple;'>l</span><span style='color:green;'>o</span><span style='color:blue;'>r</span>:</p></td><td><p style='margin:5px;'>[red](This is red) = <span style='color:red;'>This is red</span></p></td>");
                                    $('#text_format_row_5').html("<td><p style='border:1px solid black;width:35px;margin:5px;'>Box:</p></td><td><p style='margin:5px;'>[box](This is boxed) = <span style='border:1px solid black;'>This is boxed</span></p></td>");
                                    $('#text_format_row_6').html("<td><p style='font-size:75%;margin:5px;'>Small:</p></td><td><p style='margin:5px;'>[s](This is small) = <span style='font-size:50%;'>This is small</span></p></td>");
                                    $('#text_format_row_7').html("<td><span class='emoticon' style='background-image: url(http://pics.redlay.com/pictures/emoticons/2.png)'></span></td><td><a href='http://www.redlay.com/emoticons.php' class='link'><p style='margin:5px;' class='title_color' onmouseover=name_over(this); onmouseout=name_out(this);>Emoticons</p></a></td>");

                initialize_text_format_test();
                change_color();
            }
            
            function display_form(num)
            {
                if(num==1)
                {
                    if($('#photo_upload_unit').css('display')=='none')
                    {
                        display_photo_upload_box();
                        $('#post_unit').hide().attr('colspan', '');
                        $('#video_unit').hide().attr('colspan', '');
                    }
                    else
                        display_form(0);
                }
                else if(num==2)
                {
                    if($('#post_unit').css('display')=='none')
                    {
                        $('#photo_upload_unit').hide().attr('colspan', '');
                        $('#post_unit').show().attr('colspan', '3').css('text-align', 'center');
                        $('#video_unit').hide().attr('colspan', '');
                    }
                    else
                        display_form(0);
                }
                else if(num==3)
                {
                    if($('#video_unit').css('display')=='none')
                    {
                        $('#photo_upload_unit').hide().attr('colspan', '');
                        $('#post_unit').hide().attr('colspan', '');
                        $('#video_unit').show().attr('colspan', '3').css('text-align', 'center');
                    }
                    else
                        display_form(0);
                }
                else if(num==0)
                {
                    $('#photo_upload_unit').hide().attr('colspan', '');
                    $('#post_unit').hide().attr('colspan', '');
                    $('#video_unit').hide().attr('colspan', '');
                }
            }
            
//            function display_photo_upload_box()
//            {
//                //$('.alert_box').css('opacity', 1).show().draggable();
//                display_dim();
//                $('.alert_box_inside').html("<form method='post' action='upload_picture.php' enctype='multipart/form-data' target='photo_upload_iframe'><table class='alert_box_table' id='upload_photo_table'><tbody><tr class='alert_box_row' id='upload_photo_row_1'></tr><tr class='alert_box_row' id='upload_photo_row_2'></tr><tr class='alert_box_row' id='upload_photo_row_3'></tr><tr class='alert_box_row' id='upload_photo_row_5' ></tr><tr class='alert_box_row' id='upload_photo_row_4' ></tr></tbody></table></form>");
//                    $('#upload_photo_row_1').html("<td class='upload_photo_unit alert_box_title_unit' colspan='4'><p class='alert_box_title' class='text'>Upload a photo</p></td>");
//                    $('#upload_photo_row_2').html("<td class='upload_photo_unit' colspan='3'><input type='file' id='photo_upload_button' class='file_input' name='image'/></td><?php if(has_redlay_gold($_SESSION['id'], 'photo_quality')) echo "<td style='width: 120px;'><table><tbody><tr><td><span>Max quality</span></td><td><input type='checkbox' id='quality_checkbox' name='photo_quality' value='yes'/></td></tr></tbody></table></td>"; ?>");
//                    $('#upload_photo_row_3').html("<td class='upload_photo_unit' colspan='4'><textarea name='upload_picture_description' id='upload_picture_description' class='input_box' maxlength='1000' placeholder='Describe the photo...'></textarea></td>");
//                        $('#upload_picture_description').attr({'onFocus': "input_in(this);", 'onBlur': "input_out(this);"});
//                    $('#upload_photo_row_4').html("<td colspan='4'><table style='width:100%;'><tbody><tr><td class='upload_photo_unit alert_box_confirmation_row_unit_left'><div class='select_box' id='photo_audience_box'></div></td><td class='upload_photo_unit alert_box_load_unit'><img class='load_gif' id='upload_photo_gif' src='http://pics.redlay.com/pictures/load.gif'/></td><td class='upload_photo_unit alert_box_confirm_unit' ><input type='submit' class='red_button' id='photo_upload_submit' value='Upload' /></td><td class='upload_photo_unit alert_box_cancel_unit' ><input type='button' class='gray_button' id='photo_upload_cancel' onClick=close_alert_box(); value='Close' /></td></tr></tbody></table></td>");
//                        display_groups('photo_audience_box');
//                        $('#upload_photo_gif').hide();
//                    $('#upload_photo_row_5').html("<td colspan='4'><p id='photo_upload_message'></p><div id='upload_photo_preview'></div></td>");
//                    $('#photo_upload_message').hide();
//
//                    disable_photo_upload();
//                    $('#photo_upload_submit').attr('onClick', "{$('#upload_photo_gif').show();disable_photo_upload();}");
//                    
//                    show_alert_box();
//                change_color();
//            }
            
            function home_menu_over(id)
            {
                $(id).css('box-shadow', '0px 2px 3px gray');
                $(id).css('background-color', 'whitesmoke');
            }
            function home_menu_out(id)
            {
                $(id).css('box-shadow', '0px 3px 5px gray');
                $(id).css('background-color', '');
            }
            function home_menu_down(id)
            {
                $(id).css('box-shadow', 'inset 0px 3px 5px gray');
            }
            function home_menu_up(id)
            {
                $(id).css('box-shadow', '0px 3px 5px gray');
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
            
            function display_adds_online_loop()
            {
                var prev_adds=new Array();
                
                            $.post('home_query.php', 
                            {
                                num:2
                            }, function(output)
                            {
                                var adds=output.adds;
                                var profile_pictures=output.profile_pictures;
                                var names=output.names;
                                var num_adds=output.num_adds;
                                var no_adds=output.no_adds;
                                var types=output.types;
                                
                                
                                if(prev_adds==undefined||!is_same(prev_adds, adds)||no_adds==true)
                                {
                                    if(no_adds==false)
                                    {
                                        $('#online_adds').html("<p class='title_color' style='margin-top:5px;margin-bottom:10px;text-align:center;'>Online</p><table><tbody id='online_adds_table_body'></tbody></table>");
                                        for(var x = 0; x < adds.length; x++)
                                        {
                                            var profile_picture="<a href='http://www.redlay.com/profile.php?user_id="+adds[x]+"'><img class='profile_picture profile_picture_comment' src='"+profile_pictures[x]+"' /></a>";
                                            var name="<div class='user_name_body'><a class='link' href='http://www.redlay.com/profile.php?user_id="+adds[x]+"'><span class='user_name title_color' onmouseover=name_over(this); onmouseout=name_out(this);>"+names[x]+"</span></a></div>";
//                                            var description="<p class='text_color' style='margin:0px;'>"+num_adds[x]+" adds</p>";
                                            var message_button="<input class='red_button' value='Message' type='button' onClick='display_message_box("+adds[x]+");' style='padding-top:4px;padding-bottom:4px;padding-left:12px;padding-right:12px;' />";
//                                            var source="<p class='text_color' style='margin:0px;font-size:14px;'>"+types[x]+"</p>";

//                                            var body=get_post_format(profile_picture, name, description+message_button+source, '', '', '', '');


                                            var body= "<div class='status_update' id='' onmouseover=$('#').show(); onmouseout=$('#').hide();><table style='width:100%;'><tbody><tr id='post_row_1' class='post_row'>  <td style='width:50px;'><table><tbody><tr><td>"+profile_picture+"</td></tr></tbody></table></td><td class='post_body_unit'><table><tbody><tr><td>"+name+"</td></tr><tr><td>"+message_button+"</td></tr><tr><td></td></tr></tbody></table></td>  </tr><tr id='post_row_2' class='post_row'>  <td colspan='2' class='post_functions_unit'></td>  </tr><tr id='post_row_3' class='post_row'>    </tr></tbody></table><hr class='break' /></div>";

                                            $('#online_adds_table_body').html($('#online_adds_table_body').html()+"<tr><td>"+body+"</td></tr>");
                                        }
                                        prev_adds=adds;
                                    }
                                    else
                                        $('#online_adds').html("<p class='text_color' style='text-align:center;'>You have no adds online</p>");
                                 }
                                 
                                 change_color();
                            }, "json");
                
                
                setInterval(function()
                {
                            $.post('home_query.php', 
                            {
                                num:2
                            }, function(output)
                            {
                                var adds=output.adds;
                                var profile_pictures=output.profile_pictures;
                                var names=output.names;
                                var num_adds=output.num_adds;
                                var no_adds=output.no_adds;
                                var types=output.types;
                                
                                if(no_adds)
                                {
                                    $('#online_adds').html("<p class='text_color' style='text-align:center;'>You have no adds online</p>");
                                    prev_adds=undefined;
                                }
                                else
                                {
                                    if(prev_adds==undefined||!is_same(prev_adds, adds))
                                    {
                                        $('#online_adds').html("<p class='title_color' style='margin-top:5px;margin-bottom:10px;text-align:center;'>Online</p><table><tbody id='online_adds_table_body'></tbody></table>");
                                        for(var x = 0; x < adds.length; x++)
                                        {
//                                            var profile_picture="<a href='http://www.redlay.com/profile.php?user_id="+adds[x]+"'><img class='profile_picture profile_picture_status' src='"+profile_pictures[x]+"' /></a>";
//                                            var name="<div class='user_name_body'><a class='link' href='http://www.redlay.com/profile.php?user_id="+adds[x]+"'><span class='user_name title_color' onmouseover=name_over(this); onmouseout=name_out(this);>"+names[x]+"</span></a></div>";
//                                            var description="<p class='text_color' style='margin:0px;'>"+num_adds[x]+" adds</p>";
//                                            var message_button="<input class='green_button' value='Message' type='button' onClick='display_message_box("+adds[x]+");'/>";
//                                            var source="<p class='text_color' style='margin:0px;font-size:14px;'>"+types[x]+"</p>";
//
//                                            var body=get_post_format(profile_picture, name, description+message_button+source, '', '', '', '');

                                            var profile_picture="<a href='http://www.redlay.com/profile.php?user_id="+adds[x]+"'><img class='profile_picture profile_picture_comment' src='"+profile_pictures[x]+"' /></a>";
                                            var name="<div class='user_name_body'><a class='link' href='http://www.redlay.com/profile.php?user_id="+adds[x]+"'><span class='user_name title_color' onmouseover=name_over(this); onmouseout=name_out(this);>"+names[x]+"</span></a></div>";
//                                            var description="<p class='text_color' style='margin:0px;'>"+num_adds[x]+" adds</p>";
                                            var message_button="<input class='red_button' value='Message' type='button' onClick='display_message_box("+adds[x]+");' style='padding-top:4px;padding-bottom:4px;padding-left:12px;padding-right:12px;' />";
//                                            var source="<p class='text_color' style='margin:0px;font-size:14px;'>"+types[x]+"</p>";

//                                            var body=get_post_format(profile_picture, name, description+message_button+source, '', '', '', '');


                                            var body= "<div class='status_update' id='' onmouseover=$('#').show(); onmouseout=$('#').hide();><table style='width:100%;'><tbody><tr id='post_row_1' class='post_row'>  <td style='width:50px;'><table><tbody><tr><td>"+profile_picture+"</td></tr></tbody></table></td><td class='post_body_unit'><table><tbody><tr><td>"+name+"</td></tr><tr><td>"+message_button+"</td></tr><tr><td></td></tr></tbody></table></td>  </tr><tr id='post_row_2' class='post_row'>  <td colspan='2' class='post_functions_unit'></td>  </tr><tr id='post_row_3' class='post_row'>    </tr></tbody></table><hr class='break' /></div>";

                                            $('#online_adds_table_body').html($('#online_adds_table_body').html()+"<tr><td>"+body+"</td></tr>");
                                        }
                                        prev_adds=adds;
                                    }
                                }
                                change_color();
                            }, "json");
                }, 3000);
            }
            
            function initialize_view()
            {
                $.post('change_home_view.php',
                {
                    num:2
                }, function(output)
                {
                    var content=output.content_view;
                    var add_id=output.add_id;
                    var add_name=output.add_name;
                    
                    $('#select_view_button').data('val', content);
                    
                    if(content!='Everything')
                        $('#select_view_button').attr('value', content);
                    
                    $('#select_user_button').data({'user_id': add_id, 'user_name': add_name});
                    
                    if(add_id!=-1)
                        $('#select_user_button').attr('value', add_name);
                    
                    $('#select_view_text_0').data('val', 'Everything');
                    $('#select_view_text_1').data('val', 'Posts');
                    $('#select_view_text_2').data('val', 'Photos');
                    $('#select_view_text_3').data('val', 'Others');


    //                $('#select_from_text_0').data('type', 'All');
    //                $('#select_from_text_1').data('type', 'Users');
    //                $('#select_from_text_2').data('type', 'Pages');
    //                $('#select_from_button').data('type', 'All');
                    $('#select_in_button').data('group_name', 'Everyone');
    //                $('#select_page_button').data({'page_id': -1, 'page_name': ''});
    
                    display_everything(1);
                }, "json");
            }

            $(window).ready(function()
            {
                display_adds_online_loop();
                display_form(0);
                display_groups('post_audience_selection_box');
                $('.select_body_options').hide();
                $('#select_from_row').hide();
                $('.alert_box').hide();
                $('#post_form_row_4').hide();
                fill_in_box();
                fill_user_box();
                initialize_post_preview();
                initialize_video_input();
                
                initialize_view();
                //display_everything(1);


                change_color();
                <?php $path=get_user_background_pic($_SESSION['id']); if(file_exists_server($path)) echo "$('body').css({'background-image': 'url(\'$path\')', 'background-position' :'center 50px'});";  else echo "$('body').css({'background-image': 'url(\'".get_default_background_pic($redlay_theme)."\')', 'background-position' :'center 50px'});"; ?>
                $('#menu').hide();
                <?php include('required_jquery.php'); ?>
            });
            
            
            
        </script>
        <script type="text/javascript">
            <?php include('required_google_analytics.js'); ?>
        </script>
    </head>
    <body >
        <?php include('facebook_html.php'); ?>
        <?php include('required_html.php'); ?>
        <div id="top">
            <?php include('top.php'); ?>
        </div>
        <div id="main" >
            <?php include('required_side_html.php'); ?>
            <table id="home_table" >
                <tbody id="home_table_body">
                    <tr id="home_body_row">
                        <td id="home_left">
                                    <a href="http://www.redlay.com/profile.php?user_id=<?php echo $_SESSION['id']; ?>"><img id="profile_pic" src="<?php if(file_exists_server("https://s3.amazonaws.com/bucket_name/users/$_SESSION[id]/photos/0.jpg")) echo "https://s3.amazonaws.com/bucket_name/users/$_SESSION[id]/photos/0.jpg"; else if(file_exists_server("https://s3.amazonaws.com/bucket_name/users/$_SESSION[id]/photos/0.png")) echo "https://s3.amazonaws.com/bucket_name/users/$_SESSION[id]/photos/0.png"; else if(file_exists_server("https://s3.amazonaws.com/bucket_name/users/$_SESSION[id]/photos/0.gif")) echo "https://s3.amazonaws.com/bucket_name/users/$_SESSION[id]/photos/0.gif"; ?>" alt="http://pics.redlay.com/pictures/default_profile_picture.png"/></a>






                                        <table id="view_users" class="box">
                                            <tbody>
                                                <tr>
                                                    <td colspan="2"><p class="title_color" style="text-align:center;font-size:20px;font-weight:bold;">View</p></td>
                                                </tr>
                                                
                                                <tr>
                                                    <td class="home_view_text_title">View</td>
                                                    <td>
                                                        <div class="select_body" id="select_view_body">
                                                            <input class="gray_button" value="Everything" type="button" id="select_view_button" onClick="toggle_select_view(1);"/>
                                                            <div class="select_body_options" id="select_view_body_options">
                                                                <table class="select_body_options_table">
                                                                    <tbody class="select_body_options_table_body" id="select_view_options_table_body">
                                                                        <tr class="select_body_options_row" id="select_view_row_0">
                                                                            <td class="select_body_option_unit" onClick="change_view_options(0);">
                                                                                <p class="select_body_option_text" id="select_view_text_0" onmouseover="$('#select_view_row_0').css('background-color', 'rgb(200,200,200)');" onmouseout="$('#select_view_row_0').css('background-color', '');" >Everything</p>
                                                                            </td>
                                                                        </tr>
                                                                        <tr class="select_body_options_row" id="select_view_row_1">
                                                                            <td class="select_body_option_unit" onClick="change_view_options(1);">
                                                                                <p class="select_body_option_text" id="select_view_text_1" onmouseover="$('#select_view_row_1').css('background-color', 'rgb(200,200,200)');" onmouseout="$('#select_view_row_1').css('background-color', '');" >Posts</p>
                                                                            </td>
                                                                        </tr>
                                                                        <tr class="select_body_options_row" id="select_view_row_2">
                                                                            <td class="select_body_option_unit" onClick="change_view_options(2);">
                                                                                <p class="select_body_option_text" id="select_view_text_2" onmouseover="$('#select_view_row_2').css('background-color', 'rgb(200,200,200)');" onmouseout="$('#select_view_row_2').css('background-color', '');" >Photos</p>
                                                                            </td>
                                                                        </tr>
                                                                        <tr class="select_body_options_row" id="select_view_row_3">
                                                                            <td class="select_body_option_unit" onClick="change_view_options(3);">
                                                                                <p class="select_body_option_text" id="select_view_text_3" onmouseover="$('#select_view_row_3').css('background-color', 'rgb(200,200,200)');" onmouseout="$('#select_view_row_3').css('background-color', '');" >Others</p>
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
<!--                                                        <tr>
                                                    <td class="home_view_text_title">From</td>
                                                    <td>
                                                        <div class="select_body" id="select_from_body">
                                                            <input class="gray_button" value="All" type="button" id="select_from_button" onClick="toggle_select_view(2);"/>
                                                            <div class="select_body_options" id="select_from_body_options">
                                                                <table class="select_body_options_table">
                                                                    <tbody class="select_body_options_table_body" id="select_from_options_table_body">

                                                                        <tr class="select_body_options_row" id="select_from_row_0">
                                                                            <td class="select_body_option_unit" onClick="change_from_options(0);">
                                                                                <p class="select_body_option_text" id="select_from_text_0" onmouseover="$('#select_from_row_0').css('background-color', 'rgb(200,200,200)');" onmouseout="$('#select_from_row_0').css('background-color', '');" >All</p>
                                                                            </td>
                                                                        </tr>
                                                                        <tr class="select_body_options_row" id="select_from_row_1">
                                                                            <td class="select_body_option_unit" onClick="change_from_options(1);">
                                                                                <p class="select_body_option_text" id="select_from_text_1" onmouseover="$('#select_from_row_1').css('background-color', 'rgb(200,200,200)');" onmouseout="$('#select_from_row_1').css('background-color', '');" >Users</p>
                                                                            </td>
                                                                        </tr>
                                                                        <tr class="select_body_options_row" id="select_from_row_2">
                                                                            <td class="select_body_option_unit" onClick="change_from_options(2);">
                                                                                <p class="select_body_option_text" id="select_from_text_2" onmouseover="$('#select_from_row_2').css('background-color', 'rgb(200,200,200)');" onmouseout="$('#select_from_row_2').css('background-color', '');" >Pages</p>
                                                                            </td>
                                                                        </tr>

                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>-->
                                                <tr id="select_from_row">
                                                    <td class="home_view_text_title">In</td>
                                                    <td>
                                                        <div class="select_body" id="select_in_body">
                                                            <input class="gray_button" value="Everyone" type="button" id="select_in_button" onClick="toggle_select_view(3);"/>
                                                            <div class="select_body_options" id="select_in_body_options">
                                                                <table class="select_body_options_table">
                                                                    <tbody class="select_body_options_table_body" id="select_in_options_table_body">
                                                                        <!--html will be posted here -->
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="home_menu_break_unit" colspan="2"><hr class="home_menu_break" /></td>
                                                </tr>
                                                <tr>
                                                    <td class="home_view_text_title">Or</td>
                                                    <td>
                                                        <div class="select_body" id="select_user_body">
                                                            <input class="gray_button" value="All Adds" value="Users" type="button" id="select_user_button" onClick="toggle_select_view(4);"/>
                                                            <div class="select_body_options" id="select_user_body_options">
                                                                <table class="select_body_options_table">
                                                                    <tbody class="select_body_options_table_body" id="select_user_options_table_body">
                                                                        <!--html will be posted here -->
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
<!--                                                        <tr>
                                                    <td class="home_menu_break_unit" colspan="2"><hr class="home_menu_break" /></td>
                                                </tr>
                                                <tr>
                                                    <td class="home_view_text_title">Or</td>
                                                    <td>
                                                        <div class="select_body" id="select_page_body">
                                                            <input class="gray_button" value="All Pages" value="Pages" type="button" id="select_page_button" onClick="toggle_select_view(5);"/>
                                                            <div class="select_body_options" id="select_page_body_options">
                                                                <table class="select_body_options_table">
                                                                    <tbody class="select_body_options_table_body" id="select_page_options_table_body">
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>-->
                                            </tbody>
                                        </table>

                        </td>
                        <td id="home_middle">
                                    <div id="posts" class="box">
                                        <p id="home_title" class="settings_title">Everything</p>
                                                <div id="status_update_form">
                                                    <table id="home_menu_table">
                                                        <tbody>
                                                            <tr style="text-align:center;">
                                                                <td class="home_menu_unit" onClick="display_form(1);" onmouseover="home_menu_over(this);" onmouseout="home_menu_out(this);" id="home_menu_unit_left" onmousedown="home_menu_down(this);" onmouseup="home_menu_up(this);">
                                                                    <p class="title_color" >Photo</p>
                                                                </td>
                                                                <td  class="home_menu_unit" onClick="display_form(2);" onmouseover="home_menu_over(this);" onmouseout="home_menu_out(this);" id="home_menu_unit_middle" onmousedown="home_menu_down(this);" onmouseup="home_menu_up(this);">
                                                                    <p class="title_color" >Post</p>
                                                                </td>
                                                                <td class="home_menu_unit" onClick="display_form(3);" onmouseover="home_menu_over(this);" onmouseout="home_menu_out(this);" id="home_menu_unit_right" onmousedown="home_menu_down(this);" onmouseup="home_menu_up(this);">
                                                                    <p class="title_color" >Video</p>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td id="photo_upload_unit">
                                                                    <p>Photo upload</p>
                                                                </td>
                                                                <td id="post_unit">
                                                                                        <table id="update">

                                                                                            <tr class="post_form_row" id="post_form_row_1">
                                                                                                <td class="post_form_unit" colspan="2"><span id="update_title" class="profile_text"><?php echo get_post_title($ID); ?></span></td>
                                                                                            </tr>
                                                                                            <tr class="post_form_row" id="post_form_row_2">
                                                                                                <td class="post_form_unit" colspan="2"><textarea autofocus id="social_update" class="input_box" onFocus="input_in(this);" onBlur="input_out(this);" name="social_update" maxlength="1000" placeholder="What's up?" ></textarea></td>
                                                                                            </tr>

                                                                                            <tr class="post_form_row" id="post_form_row_4" style="text-align:left;">
                                                                                                <td colspan="2">
                                                                                                    <div class="post_preview_box">
                                                                                                        <div id="post_preview_status_update" class="status_update" style="margin:5px">
                                                                                                            <table style="width:100%;">
                                                                                                                <tbody>
                                                                                                                    <tr id="post_preview_row_1" class="post_row">
                                                                                                                        <td class="post_profile_picture_unit">
                                                                                                                            <img class="profile_picture_status profile_picture" src="http://u.redlay.com/users/<?php echo $_SESSION[id]; ?>/thumbs/0.jpg" id="post_preview_profile_picture" />
                                                                                                                        </td>
                                                                                                                        <td class="post_body_unit">
                                                                                                                             <div class="user_name_body">
                                                                                                                                <span class="user_name" id="post_preview_name" ><?php echo get_user_name($_SESSION['id']); ?></span>
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
                                                                                                <td class="post_form_unit" id="post_button_form_unit"><input class="submit_button red_button" id="social_submit_button" onClick="{post(<?php echo $_SESSION['id'] ?>);}" type="button" name="social_update_submit" value="Post" onmouseover="{display_title(this, 'Post on your profile');}" onmouseout="{hide_title(this);}"/></td>
                                                                                            </tr>
                                                                                        </table>
                                                                </td>
                                                                <td id="video_unit">
                                                                        <div id="add_video_form">
                                                                            <div id='add_video' style="height:auto;">
                                                                                <p id='add_video_text' class='settings_text'>Share a video:</p>
                                                                                <input type='text' style="width:90%;" id='add_video_input' class="input_box" onFocus="input_in(this);" onBlur="input_out(this);" placeholder='Link to a Youtube or Vimeo video' maxlength='255'/>
                                                                                <div id="video_preview" style="width:auto;margin-left:0px;">

                                                                                </div>
                                                                                <input type='button' id='add_video_submit' class='red_button' value='Add' onClick='add_video();'/>
                                                                            </div>
                                                                        </div>
                                                                        
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                    
                                                    
                                                    
                                                    
                                                    
                                                    
                                                    
                                                    
                                                    
                                                    
                                                    
                                                    
                                                </div>
                                        <img id="friend_posts_load" class="load_gif" src="http://pics.redlay.com/pictures/load.gif"/>
                                        <hr />
                                        <div id="home_post_text">

                                        </div>
                                    </div>
                                    <?php include('footer.php'); ?>
                        </td>
                        <td id="home_right" style="width:200px;">
                                <div id="facebook_share" class="box" style="border:5px solid blue;width:200px;">
                                    <table style="width:200px;text-align:center;">
                                        <tbody>
                                            <?php if ($me): ?>
                                                <tr>
                                                    <td>
                                                       <table style="width:200px;">
                                                          <tbody>
                                                             <tr>
                                                                <td style="text-align:center;">
                                                                   <img src="http://graph.facebook.com/<?php echo $me['id']; ?>/picture" style="border:1px solid gray"/>
                                                                </td>
                                                             </tr>
                                                             <tr>
                                                                <td style="text-align:center;">
                                                                   <p style="color:blue;font-weight:bold;margin:0px;text-shadow:-1px -1px 0 white, 1px -1px 0 white, -1px 1px 0 white, 1px 1px 0 white">Welcome <?php echo $me['first_name']; ?>! </p>
                                                                </td>
                                                             </tr>
                                                          </tbody>
                                                       </table>
                                                        
                                                        
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <input class="blue_button" type="button" value="Tell your friends" onClick="facebook_post();"/>
                                                    </td>
                                                </tr>
<!--                                                        <tr>
                                                    <td>
                                                        <input class="blue_button" type="button" onclick="importFacebookStuff();" value="Import facebook posts" />
                                                    </td>
                                                </tr>-->
<!--                                                        <tr>
                                                    <td>
                                                        <input class="blue_button" type="button" onclick="sendRequestViaMultiFriendSelector();" value="Invite your friends" />
                                                    </td>
                                                </tr>
                                                <tr>
                                                   <td>
                                                      <input class="blue_button" type="button" onClick="sentRequestAllFriendsMenu();" value="Invite ALL your friends" />
                                                   </td>
                                                </tr>-->
                                            <?php else: ?>
                                                <tr>
                                                    <td style="text-align:center;">
                                                        <span style="color:blue;">Invite your friends!</span>
                                                        <img src="http://pics.redlay.com/pictures/facebook_login_button.png" onClick="facebook_login();" style="cursor:pointer;"/>
                                                    </td>
                                                </tr>
                                            <?php endif ?>
                                        </tbody>
                                    </table>
                                </div>
                                <?php 
                                    if(!has_redlay_gold($_SESSION['id'], 'all'))
                                    {
                                       echo "<div class='box gold_box redlay_gold_box' id='redlay_gold_box' style='margin-top:15px;'>";
                                       echo "<p class='text_color' style='font-weight:bold;text-align:center;margin-top:15px;margin-bottom:5px;' >Want extra features?</p><a class='link' href='http://www.redlay.com/redlay_gold.php' ><p class='text_color' style='font-weight:bold;text-align:center;margin-top:5px;margin-bottom:15px;' onmouseover='name_over(this);' onmouseout='name_out(this);'>Get Redlay GOLD!</p></a>";
                                       echo "</div>";
                                    }
                                ?>    

                                <div class="box" id="online_adds">
                                    <p class="title_color" style="text-align:center;">Online</p>
                                    <table>
                                        <tbody id="online_adds_table_body">

                                        </tbody>
                                    </table>
                                </div>
<!--                                        <div class="box" id="adds_photos">
                                    <p class="title_color" style="text-align:center;">Photos</p>
                                    <table>
                                        <tbody id="online_adds_table_body">

                                        </tbody>
                                    </table>
                                </div>-->


                        </td>
                    </tr>
                </tbody>
            </table>
            
        </div>
        <iframe name="photo_upload_iframe" style="display:none"></iframe>
        <script type="text/javascript">
            function initialize_comment_events()
            {
                $('.comment_textarea').unbind('keypress').unbind('keydown').unbind('keyup');
                $('.comment_textarea').keyup(function(e)
                {
                    var key = (e.keyCode ? e.keyCode : e.which);
                    if(key == '13')
                    {
//                        alert($(this).data('type'));
                        if($(this).data('type')=='user_post')
                            comment($(this).data('profile_id'), $(this).data('poster_id'), $(this).data('post_id'), $(this).data('index'), $(this).data('page'), $(this).data('num_comments'));
                        else if($(this).data('type')=='user_photo')
//                            alert('picture_id: '+$(this).data('picture_id')+" | user_id"+$(this).data('user_id')+" | index: "+$(this).data('index')+" | num_comments: "+$(this).data('num_comments'));
                            comment_photo($(this).data('picture_id'), $(this).data('user_id'), $(this).data('page'), $(this).data('index'), $(this).data('num_comments'), $(this).attr('id'));
                        //else
                            //page_post_comment($(this).data('post_id'), $(this).data('profile_id'), $(this).data('poster_id'), $(this).data('page'), $(this).data('index'));
                        //$(this).val('');
                    }
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
            function initialize_video_input()
            {
                $('#add_video_input').unbind('keypress').unbind('keydown').unbind('keyup');
                $('#add_video_input').keydown(function(e)
                {
                      setTimeout(function(){process_new_video();}, 100);
                });
            }
        </script>
    </body>
        <script type="text/javascript">
            function facebook_post()
            {
                FB.ui(
                  {
                    method: 'feed',
                    name: 'Redlay',
                    link: 'http://www.redlay.com',
                    picture: 'http://www.redlay.com/favicon.ico',
                    caption: 'Add me on redlay!',
                    description: 'Redlay is a social network where you can express yourself and hang out with the people you care about'
                  },
                  function(response) {
                    
                  }
                );
            }

            function facebook_login()
            {
                FB.login(function(response) {
                    if (response.authResponse)
                    {
                        $.post('facebook_methods.php',
                        {
                            num:3
                        }, function(output)
                        {});

                        window.location.replace(window.location);

                    } else {
                    // cancelled
                    }
                });
            }


            function sendRequestViaMultiFriendSelector()
            {
                FB.ui({method: 'apprequests',
                  message: 'Come check out this new social network!',
                  picture: "http://www.redlay.com/favicon.ico"
                }, function (response)
                {
                    if(response && response.hasOwnProperty('to')) {
                        for(i = 0; i < response.to.length; i++) {
                            console.log("" + i + " ID: " + response.to[i]);
                        }
                    }
                });
            }

//            function requestCallback(response)
//            {
//               var ids = response["to"];
//               $.post('facebook_methods.php',
//               {
//                  num:1,
//                  sent:ids
//               }, function(output)
//               {});
//            }
            function sentRequestAllFriendsMenu()
            {
                
               var title="Send Requests";
               var body="<p class='text_color'>Do you wish to send Redlay requests to all of your friends? You, unfortunately, can only send 50 at a time. So just keep clicking \"Send Requests\" when pages pop up 50 friends at a time!</p>";
               var extra_id="send_all_requests_extra";
               var load_id="send_all_requests_load_id";
               var confirm="<a href='http://www.redlay.com/send_all_facebook_friends.php' ><input type='button' class='red_button' value='Send' /></a>";
               display_alert(title, body, extra_id, load_id, confirm)
               $('#send_all_requests_load_id').hide();
               $('.alert_box').css('width', '500px');
            }
            
            function importFacebookStuff()
            {
                
            }
            
            function importFacebookStuff2()
            {
                
            }
        </script>
</html>