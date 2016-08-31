<?php
@include('init.php');
include('../universal_functions.php');
$allowed="users";
include('security_checks.php');

?>

<!DOCTYPE html>
<html>
    <head>
        <title>Home</title>
        <?php include('required_header.php'); ?>
        <script type="text/javascript">
            function change_color()
            {
                $('.alert_box').css('position', 'fixed');
                <?php
                        $colors=get_user_display_colors($_SESSION['id']);
                        $color=$colors[0];
                        $box_background_color=$colors[1];
                        $text_color=$colors[2];
                ?>
                $('.box').css({'border': '15px solid <?php echo $color; ?>', 'background-color': '<?php echo $box_background_color; ?>'});

                $('.user_name, .post_functions_unit, .comment_text, .comment_name, .comment_like, .comment_dislike, .group_photo_description').css({'color': '<?php echo $color; ?>'});
                $('.comment_text_body, .comment_timestamp, .home_text').css('color', '<?php echo $text_color; ?>');

                $('.title_color').css('color', '<?php echo $color; ?>');
                $('.text_color').css('color', '<?php echo $text_color; ?>');
            }
            function display_everything(page)
            {
                $('#page_'+page).html('');
                
                var user_id=$('#select_user_button').data('user_id');
                var page_id=$('#select_page_button').data('page_id');
                var content_type=$('#home_content_display_button').data('content_type');
                var user_type=$('#home_from_display_button').data('user_type');
                var timezone=get_timezone();
                $.post('main_access.php',
                {
                    access:3,
                    num: 1,
                    user_id: user_id,
//                    page_id: page_id,
                    page_id: -1,
                    page_number: page,
                    content_type: content_type,
//                    user_type: user_type,
                    user_type: 'Users',
                    group: 'Everyone',
                    timezone:timezone
                }, function(output)
                {
                    var post_ids=output.post_ids;
                    var posts=output.posts;
                    var audiences=output.audiences;
                    var user_ids_posted=output.user_ids_posted;
                    var images=output.images;
                    var image_descriptions=output.image_descriptions;
                    var likes=output.likes;
                    var dislikes=output.dislikes;
                    var comments=output.comments;
                    var type=output.type;
                    var size=output.size;
                    var empty=output.empty;
                    var total_size=output.total_size;
                    var names=output.names;
                    var timestamps=output.timestamps;
                    var profile_ids=output.profile_ids;
                    var comment_likes=output.comment_likes;
                    var comment_dislikes=output.comment_dislikes;
                    var comments_users_sent=output.comments_users_sent;
                    var comment_timestamps=output.comment_timestamps;
                    var num_comment_likes=output.num_comment_likes;
                    var num_comment_dislikes=output.num_comment_dislikes;
                    var comment_names=output.comment_names;
                    var others=output.other;
                    var others_names=output.other_names;
                    var profile_pictures=output.profile_pictures;
                    var other_profile_pictures=output.other_profile_pictures;
                    var image_types=output.image_types;



                    if(page==1)
                    {
                        $('#home_post_text').html('');
                        for(var x = 1; x <= (total_size/15)+1; x++)
                            $('#home_post_text').html($('#home_post_text').html()+"<div class='home_page_page' id='page_"+x+"'></div>");

                        if(total_size<30)
                            $('#home_post_text').html($('#home_post_text').html()+"<div class='home_page_page' id='page_1'></div>");

                        $('#home_post_text').html($('#home_post_text').html()+"<div id='see_more_body'></div>");
                    }
                    if(size!=0)
                    {
                        for(var x = 0; x < size; x++)
                        {
                            if(type[x]=='user_post')
                            {
                                posts[x]=text_format(posts[x]);
                                
                                //updates, user name, user profile picture, timestamps, and break
                                var update="<p class='status_update_text home_text'>"+posts[x]+"</p>";
                                var name="<div class='user_name_body'><a href='http://m.redlay.com/profile.php?user_id="+user_ids_posted[x]+"' class='name_link'><p class='user_name' id='user_name_"+x+"' >"+names[x]+"</p></a></div>";
                                var picture="<a href='http://m.redlay.com/profile.php?user_id="+user_ids_posted[x]+"' class='link'><img class='profile_picture profile_picture_status' src='http://www.redlay.com/users/thumbs/users/"+user_ids_posted[x]+"/0.jpg' id='home_profile_picture_"+x+"' /></a>";
                                var timestamp="<p class='timestamp_status_update home_text'>"+timestamps[x]+"</p>";

                                var bool=false;
                                var bool2=false;
                                var num1=0;
                                var num2=0;

                                //if liked or disliked and number of likes and dislikes
                                for(var z = 0; z < likes[x].length; z++)
                                {
                                    if(likes[x][z]==<?php echo $_SESSION['id']; ?>)
                                        bool=true;
                                    if(likes[x][z]!='0')
                                        num1++;
                                }
                                for(var z = 0; z < dislikes[x].length; z++)
                                {
                                    if(dislikes[x][z]==<?php echo $_SESSION['id']; ?>)
                                        bool2=true;
                                    if(dislikes[x][z]!='0')
                                        num2++;
                                }


                                //display likes
                                if(bool==true&&likes[x][0]!='0')
                                    var like_text="<div id='post_like_"+x+"' ><p class='status_update_like' id='home_post_like_"+page+"_"+x+"' onClick='unlike_post("+post_ids[x]+", "+profile_ids[x]+","+user_ids_posted[x]+", "+num1+", "+page+", "+x+");' >Unlike ["+num1+"]</p></div>";
                                else if(num1!=0&&user_ids_posted[x]!=<?php echo $_SESSION['id']; ?>)
                                    var like_text="<div id='post_like_"+x+"' ><p class='status_update_like' id='home_post_like_"+page+"_"+x+"' onClick='like_post("+post_ids[x]+", "+profile_ids[x]+","+user_ids_posted[x]+", "+num1+", "+page+", "+x+");' >Like ["+num1+"]</p></div>";
                                else if(profile_ids[x]!=<?php echo $_SESSION['id']; ?>)
                                    var like_text="<div id='post_like_"+x+"' ><p class='status_update_like' id='home_post_like_"+page+"_"+x+"' onClick='like_post("+post_ids[x]+", "+profile_ids[x]+","+user_ids_posted[x]+", "+num1+", "+page+", "+x+");' >Like</p></div>";
                                else
                                {
                                    if(num1==1)
                                        var like_text="<p class='status_update_like me' >1 like</p>";
                                    else if(num1>1)
                                        var like_text="<p class='status_update_like me' >"+num1+" likes</p>";
                                    else
                                        var like_text="";
                                }

                                //display dislikes
                                if(bool2==true&&dislikes[x][0]!='0')
                                    var dislike_text="<div id='post_dislike_"+x+"' ><p class='status_update_dislike' id='home_post_dislike_"+page+"_"+x+"' onClick='undislike_post("+post_ids[x]+", "+profile_ids[x]+","+user_ids_posted[x]+", "+num2+", "+page+", "+x+");' >Undislike ["+num2+"]</p></div>";
                                else if(num2!=0&&user_ids_posted[x]!=<?php echo $_SESSION['id']; ?>)
                                    var dislike_text="<div id='post_dislike_"+x+"' ><p class='status_update_dislike' id='home_post_dislike_"+page+"_"+x+"' onClick='dislike_post("+post_ids[x]+", "+profile_ids[x]+","+user_ids_posted[x]+", "+num2+", "+page+", "+x+");' >Dislike ["+num2+"]</p></div>";
                                else if(profile_ids[x]!=<?php echo $_SESSION['id']; ?>)
                                    var dislike_text="<div id='post_dislike_"+x+"' ><p class='status_update_dislike' id='home_post_dislike_"+page+"_"+x+"' onClick='dislike_post("+post_ids[x]+", "+profile_ids[x]+","+user_ids_posted[x]+", "+num2+", "+page+", "+x+");' >Dislike</p></div>";
                                else
                                {
                                     if(num2==1)
                                        var dislike_text="<p class='status_update_dislike me' >1 dislike</p>";
                                    else if(num2>1)
                                        var dislike_text="<p class='status_update_dislike me' >"+num2+" dislikes</p>";
                                    else
                                        var dislike_text="";
                                }

                                //comments and stuff
                                if(comments[x][0]!='')
                                    var comment_text="<p id='comment_title_"+page+"_"+x+"' class='comment_text' onClick='show_comment("+page+", "+x+")' >Comment ["+comments[x].length+"]</p>";
                                else
                                    var comment_text="<p id='comment_title_"+page+"_"+x+"' class='comment_text' onClick='show_comment("+page+", "+x+")' >Comment</p>";
                                var comment_input="<div class='comment_input_body' id='comment_input_body_"+page+"_"+x+"'><textarea class='comment_textarea input_box' id='comment_input_"+page+"_"+x+"' placeholder='Comment...' maxlength='500'></textarea></div>";
                                var comment_body="<div class='comment_body' id='comment_body_"+page+"_"+x+"'></div>";


                                //styles like, dislike, and comment buttons
                                var functions="<table class='post_functions_table'><tbody><tr><td class='post_functions_unit'>"+like_text+"</td><td class='post_functions_seperator text_color'>|</td><td class='post_functions_unit'>"+dislike_text+"</td><td class='post_functions_seperator text_color'>|</td><td class='post_functions_post_comment_unit'>"+comment_text+"</td></tr></tbody></table>";

                                var body=get_post_format(picture, name, update+functions, comment_input+comment_body, timestamp, '', '', 'home_posts_'+page+'_'+x)
                                //display everything
//                                var content=$('#page_'+page).html();
                                $('#page_'+page).html($('#page_'+page).html()+body);

                            }
                            else if(type[x]=='user_photo')
                            {
                                if(images[x]!='')
                                {
                                    var profile_picture="<div class='home_user_picture_post' id='user_post_"+page+"_"+x+"'><a class='profile_picture_link' href='http://m.redlay.com/profile.php?user_id="+user_ids_posted[x]+"'><img class='profile_picture profile_picture_status' src='http://www.redlay.com/users/thumbs/users/"+user_ids_posted[x]+"/0.jpg'/><a/>";
                                    var name="<div class='user_name_body'><a href='http://m.redlay.com/profile.php?user_id="+user_ids_posted[x]+"' class='name_link'><p class='user_name' id='user_name_"+page+"_"+x+"' >"+names[x]+"</p></a></div>";
                                    var image="<div class='outside_picture' id='outside_picture_"+page+"_"+x+"'><a class='picture_post_link' href='http://m.redlay.com/view_photo.php?user_id="+user_ids_posted[x]+"&&picture_id="+images[x]+"&&type=user'><div class='image_overflow'><img class='picture_post' id='picture_post_picture_"+page+"_"+x+"' src='http://www.redlay.com/users/thumbs/users/"+user_ids_posted[x]+"/"+images[x]+"."+image_types[x]+"' /></div><a/></div>";
                                    var timestamp="<p class='timestamp_status_update home_text'>"+timestamps[x]+"</p></div>";
                                    var description="<p class='home_user_picture_post_description home_text' >"+image_descriptions[x]+"</p>";
                                    var picture_break="<hr class='break'/>";


                                    $('#page_'+page).html($('#page_'+page).html()+profile_picture+name+image+description+timestamp+picture_break);
                                }
                            }
//                            else if(type[x]=='page_post')
//                            {
//                                posts[x]=text_format(posts[x]);
//                                //updates, user name, user profile picture, timestamps, and break
//                                var update="<p class='status_update_text'>"+posts[x]+"</p>";
//                                var name="<div class='user_name_body'><a href='http://m.redlay.com/page.php?page_id="+user_ids_posted[x]+"' class='home_post_name_link'><p class='user_name' id='user_name_"+x+"' >"+names[x]+"</p></a></div>";
//                                var picture="<div class='status_update' id='home_posts_"+page+"_"+x+"'><a href='http://m.redlay.com/page.php?page_id="+user_ids_posted[x]+"'><img class='profile_picture profile_picture_status' src='http://www.redlay.com/users/thumbs/pages/"+user_ids_posted[x]+"/0.jpg' id='home_profile_picture_"+x+"' /></a>";
//                                var timestamp="<p class='timestamp_status_update home_text'>"+timestamps[x]+"</p>";
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
//
//
//                                //comments and stuff
//                                if(comments[x][0]!='')
//                                    var comment_text="<p id='comment_title_"+page+"_"+x+"' class='comment_text' onClick='show_comment("+page+", "+x+");' >Comment ["+comments[x].length+"]</p>";
//                                else
//                                    var comment_text="<p id='comment_title_"+page+"_"+x+"' class='comment_text' onClick='show_comment("+page+", "+x+");' >Comment</p>";
//                                var comment_input="<div class='comment_input_body' id='comment_input_body_"+page+"_"+x+"'><textarea class='comment_textarea input_box' id='comment_input_"+page+"_"+x+"' placeholder='Comment...' maxlength='500'></textarea></div>";
//                                var comment_body="<div class='comment_body' id='comment_body_"+page+"_"+x+"'></div>";
//
//
//                                //styles like, dislike, and comment buttons
//                                var functions="<table class='post_functions_table'><tbody><tr><td class='post_functions_unit'>"+like_text+"</td><td class='post_functions_unit'>"+dislike_text+"</td><td class='post_functions_post_comment_unit'>"+comment_text+"</td></tr></tbody></table>";
//
//                                //display everything
//                                var content=$('#page_'+page).html();
//                                $('#page_'+page).html(content+picture+name+update+functions+comment_input+comment_body+timestamp+post_break);
//                            }
//                            else if(type[x]=='page_photo')
//                            {
//                                if(images[x]!='')
//                                {
//                                    var profile_picture="<div class='home_user_picture_post' id='user_post_"+page+"_"+x+"'><a class='profile_picture_link' href='http://m.redlay.com/page.php?page_id="+user_ids_posted[x]+"'><img class='profile_picture profile_picture_status' src='http://www.redlay.com/users/thumbs/pages/"+user_ids_posted[x]+"/0.jpg'/><a/>";
//                                    var name="<div class='user_name_body'><a href='http://m.redlay.com/page.php?page_id="+user_ids_posted[x]+"' class='home_post_name_link'><p class='user_name' id='user_name_"+page+"_"+x+"' >"+names[x]+"</p></a></div>";
//                                    var image="<div class='outside_picture' id='outside_picture_"+page+"_"+x+"'><a class='picture_post_link' href='http://m.redlay.com/view_photo.php?user_id="+user_ids_posted[x]+"&&picture_id="+images[x]+"&&type=page'><img class='picture_post' id='picture_post_picture_"+page+"_"+x+"' src='http://www.redlay.com/users/thumbs/pages/"+user_ids_posted[x]+"/"+images[x]+".jpg' /><a/></div>";
//                                    var timestamp="<p class='timestamp_status_update home_text'>"+timestamps[x]+"</p></div>";
//                                    var description="<p class='home_user_picture_post_description' >"+image_descriptions[x]+"</p>";
//                                    var picture_break="<hr class='break'/>";
//
//
//                                    $('#page_'+page).html($('#page_'+page).html()+profile_picture+name+image+description+timestamp+picture_break);
//                                }
//                            }
                            else if(type[x]!=null&&type[x][0]=='group_photo')
                            {
                                var profile_picture="<div class='home_user_picture_post' id='user_post_"+page+"_"+x+"'><a class='profile_picture_link' href='http://m.redlay.com/profile.php?user_id="+user_ids_posted[x]+"'><img class='profile_picture profile_picture_status' src='http://www.redlay.com/"+profile_pictures[x]+"'/><a/>";
                                var name="<div class='user_name_body'><a href='http://m.redlay.com/profile.php?user_id="+user_ids_posted[x]+"' class='home_post_name_link'><p class='user_name' id='user_name_"+page+"_"+x+"' >"+names[x]+"</p></a></div>";
                                var image="<div class='image_group_body' id='image_group_body_"+page+"_"+x+"'></div>";
                                var timestamp="<p class='timestamp_status_update home_text'>"+timestamps[x][0]+"</p></div>";
                                var description="<span class='home_user_picture_post_description group_photo_description' style='cursor:pointer;' onClick='display_group_images("+page+", "+x+");'>("+images[x].length+") photos</span>";
                                var picture_break="<hr />";
                                $('#page_'+page).html($('#page_'+page).html()+profile_picture+name+image+description+timestamp+picture_break);
                                $('#outside_picture_'+page+'_'+x).attr({'onmouseover': "$(this).css('background-color', 'lightgray');", 'onmouseout': "$(this).css('background-color', 'white');"});
                                
//                                    var image="<div class='outside_picture' id='outside_picture_"+page+"_"+x+"'><a class='picture_post_link' href='http://m.redlay.com/view_photo.php?user_id="+user_ids_posted[x]+"&&picture_id="+images[x]+"&&type=user'><img class='picture_post' id='picture_post_picture_"+page+"_"+x+"' src='./users/thumbs/users/"+user_ids_posted[x]+"/"+images[x]+"."+image_types[x]+"' /><a/></div>";
//                                    var image="<p>"+images[x].length+" total images</p>";
                                if(images[x].length>=5)
                                    var length=5;
                                else
                                    var length=images[x].length;
                                
                                var z_index=5;
                                var top=0;
                                var left=0;
                                for(var y = 0; y < images[x].length; y++)
                                {
                                    if(y<=5)
                                    {
                                        $('#image_group_body_'+page+'_'+x).html($('#image_group_body_'+page+'_'+x).html()+"<img class='home_image' id='home_preview_image_"+page+"_"+x+"_"+y+"' style='z-index:"+z_index+";top:"+top+"px;left:"+left+"px;' src='http://www.redlay.com/users/thumbs/users/"+user_ids_posted[x]+"/"+images[x][y]+"."+image_types[x][y]+"'/>");

                                        z_index--;
                                        top+=10;
                                        left+=10;
                                    }
                                    else
                                        $('#image_group_body_'+page+'_'+x).html($('#image_group_body_'+page+'_'+x).html()+"<img id='home_preview_image_"+page+"_"+x+"_"+y+"' />");
                                }
                                
                                
                            }
                            else if(type[x]!=null)
                            {
                                var name="<div class='user_name_body'><a href='http://m.redlay.com/profile.php?user_id="+profile_ids[x]+"' class='home_post_name_link'><p class='user_name' id='user_name_"+x+"' >"+names[x]+"</p></a></div>";
                                var picture="<div class='status_update' id='home_posts_"+page+"_"+x+"'><a href='http://m.redlay.com/profile.php?user_id="+profile_ids[x]+"'><img class='profile_picture profile_picture_status' src='http://www.redlay.com/"+profile_pictures[x]+"' id='home_profile_picture_"+x+"' /></a>";
                                var timestamp="<p class='timestamp_status_update home_text'>"+timestamps[x]+"</p>";
                                var post_break="<hr class='break'/></div>";


                                if(type[x]=='add')
                                    var body="<table class='added_table added_add_table'><tbody><tr><td class='home_other_text_unit'><span class='home_other_text text_color'>added </span></td><td><a href='http://m.redlay.com/profile.php?user_id="+others[x]+"'><img class='profile_picture_status added_profile_picture' src='http://www.redlay.com/"+other_profile_pictures[x]+"' id='home_profile_picture_"+x+"' /></a><div class='user_name_body other_user_name_body'><a href='http://m.redlay.com/profile.php?user_id="+others[x]+"' class='home_post_name_link'><p class='user_name' id='user_name_"+x+"' >"+others_names[x]+"</p></a></div></td></tr></tbody></table>";
//                                else if(type[x]=='video')
//                                    var body="<table class='added_table added_video_table' ><tbody><tr><td class='home_other_text_unit'><span class='home_other_text'></span></td><td>  <object width='570' height='320'><param name='movie' value='"+others[x]+"?wmode=transparent&version=3&amp;hl=en_US'><param name='wmode' value='transparent' /></param><param name='allowFullScreen' value='true'></param><param name='allowscriptaccess' value='always'></param><embed src='"+others[x]+"?wmode=transparent&version=3&amp;hl=en_US' type='application/x-shockwave-flash' width='570' height='320' allowscriptaccess='always' allowfullscreen='true'></embed></object>  </td></tr></tbody></table>";
                                else if(type[x]=='video')
                                    var body="<table class='added_table added_video_table' ><tbody><tr><td class='home_other_text_unit'><span class='home_other_text text_color'></span></td><td>  <iframe width='570' height='315' src='"+others[x]+"?wmode=transparent' frameborder='0' allowfullscreen></iframe>  </td></tr></tbody></table>";
                                else if(type[x]=='page_like')
                                    var body="<table class='added_table page_like_table'><tbody><tr><td class='home_other_text_unit'><span class='home_other_text text_color'>liked </span></td><td><a href='http://m.redlay.com/page.php?page_id="+others[x]+"'><img class='profile_picture_status added_profile_picture' src='http://www.redlay.com/"+other_profile_pictures[x]+"' id='home_profile_picture_"+x+"' /></a><div class='user_name_body other_user_name_body'><a href='http://m.redlay.com/page.php?page_id="+others[x]+"' class='home_post_name_link'><p class='user_name' id='user_name_"+x+"' >"+others_names[x]+"</p></a></div></td></tr></tbody></table>";
                                else if(type[x]=='relationship')
                                    var body="<table class='added_table relationship_table'><tbody><tr><td class='home_other_text_unit'><span class='home_other_text text_color'>is now "+others[x]+"</span></td></tr></tbody></table>";
                                else if(type[x]=='mood')
                                    var body="<table class='added_table mood_table'><tbody><tr><td class='home_other_text_unit'><span class='home_other_text text_color'>feels "+others[x]+"</span></td></tr></tbody></table>";
                                else if(type[x]=='redlay_gold')
                                    var body="<table class='added_table redlay_gold_table'><tbody><tr><td class='home_other_text_unit'><span class='home_other_text text_color'>bought <input type='button' value='redlay gold!' onClick=window.open('http://m.redlay.com/redlay_gold.php'); class='red_button'  /></span></td></tr></tbody></table>";

                                
                                $('#page_'+page).html($('#page_'+page).html()+picture+name+body+timestamp+post_break);
                            }
                            
                            
                            
                        }

                        ///////////displays comments/////////////////////
                        for(var x = 0; x < size; x++)
                        {
                            if(type[x]=='user_post'||type[x]=='user_photo'||type[x]=='page_post'||type[x]=='page_photo')
                            {
                                //binds data for when user clicks to display or post comments
                                $('#comment_input_'+page+'_'+x).data({'post_id': post_ids[x], 'profile_id': profile_ids[x], 'poster_id': user_ids_posted[x], 'page': page, 'index': x, 'type': type[x]}).attr({'onFocus': "input_in(this);", 'onBlur': "input_out(this);"});
                                $('#comment_title_'+page+'_'+x).attr({'onClick': "{show_comment("+page+", "+x+");}"});

                                if(comments[x]!='')
                                    $('#comment_title_'+page+'_'+x).data({'number': comments[x].length});
                                else
                                    $('#comment_title_'+page+'_'+x).data({'number': 0});

                                //deletes previous html
                                $('#comment_body_'+page+'_'+x).html('');

                                if(comments[x]!='')
                                {
                                    for(var y = 0; y < comments[x].length; y++)
                                    {
                                        var string="http://www.redlay.com/profile.php?user_id="+comments_users_sent[x][y];
                                        var name="<div class='comment_user_name_body'><a href='"+string+"' class='home_post_name_link'><p class='comment_name' id='home_comment_name_"+page+"_"+x+"_"+y+"' >"+comment_names[x][y]+"</p></a></div>";
                                        var picture="<div id='comment_"+page+"_"+x+"_"+y+"' class='comment'><a href='"+string+"'><img class='comment_profile_picture profile_picture' id='comment_profile_picture_"+page+"_"+x+"_"+y+"' src='http://www.redlay.com/users/thumbs/users/"+comments_users_sent[x][y]+"/0.jpg' /></a>";
                                        var comment="<p class='comment_text_body'>"+comments[x][y]+"</p>";
                                        var timestamp="<p class='comment_timestamp'>"+comment_timestamps[x][y]+"</p>";
                                        var comment_break="<hr class='comment_break' /></div>";

                                        var bool=false;
                                        var bool2=false;

                                        //seeing if already liked
                                        for(var z = 0; z < comment_likes[x][y].length; z++)
                                        {
                                            if(comment_likes[x][y][z]==<?php echo $_SESSION['id']; ?>)
                                                bool=true;
                                        }
                                        //seeing if already disliked
                                        for(var z = 0; z < comment_dislikes[x][y].length; z++)
                                        {
                                            if(comment_dislikes[x][y][z]==<?php echo $_SESSION['id']; ?>)
                                                bool2=true;
                                        }

                                        //displaying likes
                                        if(comments_users_sent[x][y]!=<?php echo $_SESSION['id']; ?>)
                                        {
                                            if(bool==true)
                                                var like="<div id='home_comment_like_body_"+page+"_"+x+"_"+y+"'><p class='comment_like' id='home_comment_like_"+page+"_"+x+"_"+y+"' >Unlike ["+num_comment_likes[x][y]+"]</p></div>";
                                            else if(num_comment_likes[x][y]==0&&comments_users_sent[x][y]!='')
                                                var like="<div id='home_comment_like_body_"+page+"_"+x+"_"+y+"'><p class='comment_like' id='home_comment_like_"+page+"_"+x+"_"+y+"' >Like</p></div>";
                                            else if(comments_users_sent[x][y]!='')
                                                var like="<div id='home_comment_like_body_"+page+"_"+x+"_"+y+"'><p class='comment_like' id='home_comment_like_"+page+"_"+x+"_"+y+"' >Like ["+num_comment_likes[x][y]+"]</p></div>";
                                            else
                                                var like="";
                                        }
                                        else
                                        {
                                            if(num_comment_likes[x][y]==1)
                                                var like="<div id='home_comment_like_body_"+page+"_"+x+"_"+y+"'><p class='comment_like comment_like_me' >1 like</p></div>";
                                            else if(num_comment_likes[x][y]>1)
                                                var like="<div id='home_comment_like_body_"+page+"_"+x+"_"+y+"'><p class='comment_like comment_like_me' >"+num_comment_likes[x][y]+" likes</p></div>";
                                            else
                                                var like="";
                                        }

                                        //displaying dislikes
                                        if(comments_users_sent[x][y]!=<?php echo $_SESSION['id']; ?>)
                                        {
                                            if(bool2==true)
                                                var dislike="<div id='home_comment_dislike_body_"+page+"_"+x+"_"+y+"'><p class='comment_dislike' id='home_comment_dislike_"+page+"_"+x+"_"+y+"' onmouseover=name_over(this); onmouseout=name_out(this); >Undislike ["+num_comment_dislikes[x][y]+"]</p></div>";
                                            else if(num_comment_dislikes[x][y]==0&&comments_users_sent[x][y]!='')
                                                var dislike="<div id='home_comment_dislike_body_"+page+"_"+x+"_"+y+"'><p class='comment_dislike' id='home_comment_dislike_"+page+"_"+x+"_"+y+"' onmouseover=name_over(this); onmouseout=name_out(this); >Dislike</p></div>";
                                            else if(comments_users_sent[x][y]!='')
                                                var dislike="<div id='home_comment_dislike_body_"+page+"_"+x+"_"+y+"'><p class='comment_dislike' id='home_comment_dislike_"+page+"_"+x+"_"+y+"' onmouseover=name_over(this); onmouseout=name_out(this); >Dislike ["+num_comment_dislikes[x][y]+"]</p></div>";
                                            else
                                                var dislike="";
                                        }
                                        else
                                        {
                                            if(num_comment_dislikes[x][y]==1)
                                                var dislike="<div id='home_comment_dislike_body_"+page+"_"+x+"_"+y+"'><p class='comment_dislike comment_dislike_me' >1 dislike</p></div>";
                                            else if(num_comment_dislikes[x][y]>1)
                                                var dislike="<div id='home_comment_dislike_body_"+page+"_"+x+"_"+y+"'><p class='comment_dislike comment_dislike_me' >"+num_comment_dislikes[x][y]+" dislikes</p></div>";
                                            else
                                                var dislike="";
                                        }

                                        var functions="<table class='post_functions_comment_table' ><tbody><tr><td class='post_functions_comment_unit'>"+like+"</td><td class='post_functions_comment_unit'>"+dislike+"</td></tr></tbody></table>";
                                        if(comments_users_sent[x][y]!='')
                                            $("#comment_body_"+page+"_"+x).html(picture+name+comment+functions+timestamp+comment_break+$("#comment_body_"+page+"_"+x).html());

                                        if(type[x]=='user_post'||type[x]=='user_photo')
                                        {
                                            if(bool)
                                                $('#home_comment_like_'+page+'_'+x+'_'+y).attr({'onClick': "unlike_comment("+post_ids[x]+", "+x+", "+y+", "+page+", "+profile_ids[x]+", "+num_comment_likes[x][y]+");"});
                                            else
                                                $('#home_comment_like_'+page+'_'+x+'_'+y).attr({'onClick': "like_comment("+post_ids[x]+", "+x+", "+y+", "+page+", "+profile_ids[x]+", "+num_comment_likes[x][y]+");"});

                                            if(bool2)
                                                $('#home_comment_dislike_'+page+'_'+x+'_'+y).attr({'onClick': "undislike_comment("+post_ids[x]+", "+x+", "+y+", "+page+", "+profile_ids[x]+", "+num_comment_dislikes[x][y]+")"});
                                            else
                                                $('#home_comment_dislike_'+page+'_'+x+'_'+y).attr({'onClick': "dislike_comment("+post_ids[x]+", "+x+", "+y+", "+page+", "+profile_ids[x]+", "+num_comment_dislikes[x][y]+")"});
                                        }
                                        else
                                        {
                                            if(bool)
                                                $('#home_comment_like_'+page+'_'+x+'_'+y).attr({'onClick': "unlike_page_comment("+post_ids[x]+", "+x+", "+y+", "+page+", "+profile_ids[x]+", "+num_comment_likes[x][y]+");"});
                                            else
                                                $('#home_comment_like_'+page+'_'+x+'_'+y).attr({'onClick': "like_page_comment("+post_ids[x]+", "+x+", "+y+", "+page+", "+profile_ids[x]+", "+num_comment_likes[x][y]+");"});

                                            if(bool2)
                                                $('#home_comment_dislike_'+page+'_'+x+'_'+y).attr({'onClick': "undislike_page_comment("+post_ids[x]+", "+x+", "+y+", "+page+", "+profile_ids[x]+", "+num_comment_dislikes[x][y]+")"});
                                            else
                                                $('#home_comment_dislike_'+page+'_'+x+'_'+y).attr({'onClick': "dislike_page_comment("+post_ids[x]+", "+x+", "+y+", "+page+", "+profile_ids[x]+", "+num_comment_dislikes[x][y]+")"});
                                        }


                                    }
                                }
                                else
                                    $("#comment_body_"+page+"_"+x).html("There are no comments");
                            }
                        }

                        //modifies, creates, or deletes see_more button
                        if($('.see_more_posts').length!=0&&empty==false)
                            $('.see_more_posts').attr({'onClick': "display_everything("+(page+1)+");"});
                        else if(empty==false)
                        {
                            $('#see_more_body').html("<img class='see_more_button' id='see_more_post_button' src='./pictures/see_more_button.png' >");
                            $('#see_more_post_button').attr({'onClick': "{display_everything("+(page+1)+");}"});
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
                                    $('#home_preview_image_'+page+'_'+x+'_'+y).data({'src': "http://www.redlay.com/users/thumbs/users/"+user_ids_posted[x]+"/"+images[x][y]+"."+image_types[x][y], 'link': "http://m.redlay.com/view_photo.php?user_id="+user_ids_posted[x]+"&&picture_id="+images[x][y]+"&&type=user"});
                            }
                        }
                    }
                    else
                        $('#home_post_text').html("<p class='empty_text'>There is nothing to display here</p>");


                    initialize_comment_events();
                    change_color();
                }, "json");
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
                
                
                //465
                display_alert("("+links.length+") photos", "<div id='group_picture_alert_box_div' style='height:680px;overflow:auto;-webkit-overflow-scrolling: touch;'><table ><tbody id='all_group_images_"+page+"'></tbody></table></div>", 'group_photos_extra', 'group_photos_load', '');
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
                
                show_alert_box();
                
//                touchScroll('group_picture_alert_box_div');
                change_color();
            }
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
            function like_comment(post_id, post_index, comment_index, page, profile_id, num_likes)
            {
                $.post('main_access.php',
                {
                    access:4,
                    post_id: post_id,
                    comment_index: comment_index,
                    profile_id: profile_id
                }, function(output)
                {
                    num_likes++;
                    $('#home_comment_like_'+page+'_'+post_index+'_'+comment_index).html("Unlike ["+num_likes+']');
                    $('#home_comment_like_'+page+'_'+post_index+'_'+comment_index).attr('onClick', "unlike_comment("+post_id+", "+post_index+", "+comment_index+", "+page+", "+profile_id+", "+num_likes+");");
                });
            }
            function dislike_comment(post_id, post_index, comment_index,page, profile_id, num_dislikes)
            {
                $.post('main_access.php',
                {
                    access:5,
                    post_id: post_id,
                    comment_index: comment_index,
                    profile_id: profile_id
                }, function(output)
                {
                    num_dislikes++;
                    $('#home_comment_dislike_'+page+'_'+post_index+'_'+comment_index).html("Undislike ["+num_dislikes+']');
                    $('#home_comment_dislike_'+page+'_'+post_index+'_'+comment_index).attr('onClick', "undislike_comment("+post_id+", "+post_index+", "+comment_index+", "+page+", "+profile_id+", "+num_dislikes+");");
                });
            }
            function unlike_comment(post_id, post_index, comment_index,page, profile_id, num_likes)
            {
                $.post('main_access.php',
                {
                    access:6,
                    post_id: post_id,
                    comment_index: comment_index,
                    profile_id: profile_id
                }, function(output)
                {
                    num_likes--;

                    $('#home_comment_like_'+page+'_'+post_index+'_'+comment_index).attr('onClick', "like_comment("+post_id+", "+post_index+", "+comment_index+", "+page+", "+profile_id+", "+num_likes+");");
                    if(num_likes==0)
                        $('#home_comment_like_'+page+'_'+post_index+'_'+comment_index).html("Like");
                    else
                        $('#home_comment_like_'+page+'_'+post_index+'_'+comment_index).html("Like ["+num_likes+"]");
                });
            }
            function undislike_comment(post_id, post_index, comment_index,page, profile_id, num_dislikes)
            {
                $.post('main_access.php',
                {
                    access:7,
                    post_id: post_id,
                    comment_index: comment_index,
                    profile_id: profile_id
                }, function(output)
                {
                    num_dislikes--;

                    $('#home_comment_dislike_'+page+'_'+post_index+'_'+comment_index).attr('onClick', "dislike_comment("+post_id+", "+post_index+", "+comment_index+", "+page+", "+profile_id+", "+num_dislikes+");");
                    if(num_dislikes==0)
                        $('#home_comment_dislike_'+page+'_'+post_index+'_'+comment_index).html("Dislike");
                    else
                        $('#home_comment_dislike_'+page+'_'+post_index+'_'+comment_index).html("Dislike ["+num_dislikes+"]");
                });
            }

            function like_page_post(post_id, page_id, poster_id, number, page, index)
            {
                $.post('main_access.php',
                {
                    access:14,
                    post_id: post_id,
                    page_id: page_id,
                    poster_id: poster_id
                }, function (output)
                {
                    number++;
                    $('#home_post_like_'+page+'_'+index).html("Unlike ["+number+"]").attr('onClick', "unlike_page_post("+post_id+", "+page_id+", "+poster_id+", "+number+", "+page+", "+index+");");
                });
            }

            function unlike_page_post(post_id, page_id, poster_id, number, page, index)
            {
                $.post('main_access.php',
                {
                    access:15,
                    post_id: post_id,
                    page_id: page_id,
                    poster_id: poster_id
                }, function (output)
                {
                    number--;
                    if(number==0)
                        $('#home_post_like_'+page+'_'+index).html("Like");
                    else
                        $('#home_post_like_'+page+'_'+index).html("Like ["+number+"]");
                    $('#home_post_like_'+page+'_'+index).attr('onClick', "like_page_post("+post_id+", "+page_id+", "+poster_id+", "+number+", "+page+", "+index+");");
                });
            }

            function dislike_page_post(post_id, page_id, poster_id, number, page, index)
            {
                $.post('main_access.php',
                {
                    access:16,
                    post_id: post_id,
                    page_id: page_id,
                    poster_id: poster_id
                }, function (output)
                {
                    number++;
                    $('#home_post_dislike_'+page+'_'+index).html("Undislike ["+number+"]").attr('onClick', "undislike_page_post("+post_id+", "+page_id+", "+poster_id+", "+number+", "+page+", "+index+");");
                });
            }
            function undislike_page_post(post_id, page_id, poster_id, number, page, index)
            {
                $.post('main_access.php',
                {
                    access:17,
                    post_id: post_id,
                    page_id: page_id,
                    poster_id: poster_id
                }, function (output)
                {
                    number--;
                    if(number==0)
                        $('#home_post_dislike_'+page+'_'+index).html("Dislike");
                    else
                        $('#home_post_dislike_'+page+'_'+index).html("Dislike ["+index+"]");
                    $('#home_post_dislike_'+page+'_'+index).attr('onClick', "dislike_page_post("+post_id+", "+page_id+", "+poster_id+", "+number+", "+page+", "+index+");");
                });
            }

            function dislike_post(post_id, profile_id, poster_id, number, page, index)
            {
                $.post('main_access.php',
                {
                    access:10,
                    post_id: post_id,
                    profile_id: profile_id,
                    poster_id: poster_id
                }, function (output)
                {
                    number++;
                    $('#home_post_dislike_'+page+'_'+index).html("Undislike ["+number+"]").attr('onClick', "undislike_post("+post_id+", "+profile_id+", "+poster_id+", "+number+", "+page+", "+index+");");
                });
            }
            function undislike_post(post_id, profile_id, poster_id, number, page, index)
            {
                $.post('main_access.php',
                {
                    access:12,
                    post_id: post_id,
                    profile_id: profile_id
                }, function (output)
                {
                    number--;
                    if(number==0)
                        $('#home_post_dislike_'+page+'_'+index).html("Dislike");
                    else
                        $('#home_post_dislike_'+page+'_'+index).html("Dislike ["+index+"]");
                    $('#home_post_dislike_'+page+'_'+index).attr('onClick', "dislike_post("+post_id+", "+profile_id+", "+poster_id+", "+number+", "+page+", "+index+");");
                });
            }
            //ID is profile ID, ID2 is user posted ID, number is number of likes, and index is post_id
            function like_post(post_id, profile_id, poster_id, number, page, index)
            {
                $.post('main_access.php',
                {
                    access:9,
                    post_id: post_id,
                    profile_id: profile_id,
                    poster_id: poster_id
                }, function (output)
                {
                    number++;
                    $('#home_post_like_'+page+'_'+index).html("Unlike ["+number+"]").attr('onClick', "unlike_post("+post_id+", "+profile_id+", "+poster_id+", "+number+", "+page+", "+index+");");
                });
            }

            function unlike_post(post_id, profile_id, poster_id, number, page, index)
            {
                $.post('main_access.php',
                {
                    access:11,
                    post_id: post_id,
                    profile_id: profile_id
                }, function (output)
                {
                    number--;
                    if(number==0)
                        $('#home_post_like_'+page+'_'+index).html("Like");
                    else
                        $('#home_post_like_'+page+'_'+index).html("Like ["+number+"]");
                    $('#home_post_like_'+page+'_'+index).attr('onClick', "like_post("+post_id+", "+profile_id+", "+poster_id+", "+number+", "+page+", "+index+");");
                });
            }

            function comment(post_id, profile_id, poster_id, page, index)
            {
                var text=$("#comment_input_"+page+"_"+index).val();
                $.post('main_access.php',
                {
                    access:13,
                    post_id: post_id,
                    comment_text: text,
                    profile_id: profile_id,
                    poster_id: poster_id
                }, function (output)
                {
                    
                    $('#comment_title_'+page+'_'+index).data('number', ($('#comment_title_'+page+'_'+index).data('number')+1));
                    $('#comment_title_'+page+'_'+index).html("Comment ["+$('#comment_title_'+page+'_'+index).data('number')+"]");
                    
                    //displays new comment
                    var new_index=0;
                    while($('#comment_'+page+'_'+index+'_'+new_index).length)
                        new_index++;

                    var string="http://m.redlay.com/profile.php?user_id=<?php echo $_SESSION['id']; ?>";
                    var name="<div class='comment_user_name_body'><a href='http://m.redlay.com/profile.php?user_id=<?php echo $_SESSION['id']; ?>' class='home_post_name_link'><p class='comment_name' id='home_comment_name_"+page+"_"+index+"_"+new_index+"' ><?php echo get_user_name($_SESSION['id']); ?></p></a></div>";
                    var picture="<div id='comment_"+page+"_"+index+"_"+new_index+"' class='comment'><a href='"+string+"'><img class='comment_profile_picture profile_picture' id='comment_profile_picture_"+page+"_"+index+"_"+new_index+"' src='http://www.redlay.com/users/thumbs/users/<?php echo $_SESSION['id']; ?>/0.jpg' /></a>";
                    var comment="<p class='comment_text_body'>"+text+"</p>";
                    var timestamp="<p class='comment_timestamp'>1 second ago</p>";
                    var comment_break="<hr class='comment_break' /></div>";


                    var functions="<table class='post_functions_comment_table' ><tbody><tr><td class='post_functions_comment_unit'></td><td class='post_functions_comment_unit'></td></tr></tbody></table>";

                    if($('#comment_'+page+'_'+index+'_0').length)
                        $("#comment_body_"+page+"_"+index).html(picture+name+comment+functions+timestamp+comment_break+$("#comment_body_"+page+"_"+index).html());
                    else
                        $("#comment_body_"+page+"_"+index).html(picture+name+comment+functions+timestamp+comment_break);
                    
                    change_color();
                });
            }

            function page_post_comment(post_id, page_id, poster_id, page, index)
            {
                $.post('main_access.php',
                {
                    access:18,
                    post_id: post_id,
                    comment_text: $("#comment_input_"+page+"_"+index).val(),
                    page_id: page_id,
                    poster_id: poster_id
                }, function (output)
                {
                    //post_comments();
                    $('#comment_title_'+page+'_'+index).data('number', ($('#comment_title_'+page+'_'+index).data('number')+1));
                    $('#comment_title_'+page+'_'+index).html("Comment ["+$('#comment_title_'+page+'_'+index).data('number')+"]");
                });
            }

            function post()
            {
                //gets the checked checkboxes and their values
                var audience_options_list=new Array();
                var num=0;
                var num2=0;
                while($('#home_audience_box_checkbox_'+num2).length)
                {
                    if($('#home_audience_box_checkbox_'+num2).data('checked')=='yes')
                    {
                        audience_options_list[num]=$('#home_audience_box_checkbox_'+num2).data('group_name');
                        num++;
                    }
                    num2++;
                }


                var post=$('#home_post_input').val();
                $('#update_errors').hide();
                $.post('main_access.php',
                {
                    access:8,
                    user_id: <?php echo $_SESSION['id']; ?>,
                    updates: post,
                    audience: audience_options_list
                }, function (output)
                {
                    if(output=='Update posted!')
                        $('#errors').html(output).attr('class', 'good_errors').show();
                    else
                        $('#errors').html(output).attr('class', 'bad_errors').show();
                    $('#post_load').html("");
                    display_everything(1);
                });
            }
            function toggle_view_display()
            {
                change_view();
                if($('#home_content_box').css('display')=='none')
                    $('#home_content_box').show();
                else
                    $('#home_content_box').hide();
                $('#home_from_box').hide();
                $('#home_adds_box').hide();
                $('#home_pages_box').hide();
            }
            function toggle_from_display()
            {
                if($('#home_from_box').css('display')=='none')
                    $('#home_from_box').show();
                else
                    $('#home_from_box').hide();
                $('#home_content_box').hide();
                $('#home_adds_box').hide();
                $('#home_pages_box').hide();
            }
            function toggle_adds_display()
            {
                change_view();
                if($('#home_adds_box').css('display')=='none')
                    $('#home_adds_box').show();
                else
                    $('#home_adds_box').hide();
                $('#home_content_box').hide();
                $('#home_from_box').hide();
                $('#home_pages_box').hide();
            }
            function toggle_pages_display()
            {
                if($('#home_pages_box').css('display')=='none')
                    $('#home_pages_box').show();
                else
                    $('#home_pages_box').hide();
                
                $('#home_content_box').hide();
                $('#home_from_box').hide();
                $('#home_adds_box').hide();
            }
            function change_user_options(num)
            {
                $('#select_user_body_options').hide();
                var name=$('#select_user_options_text_'+num).data('user_name');
                $('#select_user_button').val(name).data('user_id', $('#select_user_options_text_'+num).data('user_id'));

                change_view();
                display_everything(1);
            }
            function change_page_options(num)
            {
                var name=$('#select_page_options_text_'+num).data('page_name');
                toggle_select_view(5);
                $('#select_page_button').val(name).data('page_id', $('#select_page_options_text_'+num).data('page_id'));

                display_everything(1);
            }
            function fill_user_box()
            {
                $.post('main_access.php',
                {
                    access:19
                }, function(output)
                {
                    var adds=output.adds;
                    var names=output.names;
                    var profile_pictures=output.profile_pictures;
                    var pages=output.pages;
                    var page_names=output.page_names;

                    for(var x = 0; x < adds.length; x++)
                    {
                        $('#select_user_options_table_body').html($('#select_user_options_table_body').html()+"<tr class='select_body_options_row' id='select_user_options_row_"+x+"'></tr>");
                        if(x!=0)
                            $('#select_user_options_row_'+x).html("<td class='select_body_picture_options'><img class='select_user_profile_picture' src='http://www.redlay.com/"+profile_pictures[x]+"' /></td><td class='select_body_options_unit' id='select_user_body_options_unit_"+x+"' onClick='change_user_options("+x+");'><p class='select_body_option_text' id='select_user_options_text_"+x+"'>"+names[x]+"</p></td>");
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

                    for(var x = 0; x < adds.length; x++)
                    {
                        if(x!=0)
                            $('#select_user_options_text_'+x).data({'user_id': adds[x], 'user_name': names[x]});
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
                $('#post_preview_text').html(text_format($('#home_post_input').val()));
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
                            $('#text_format_preview_box').html("<div id='text_format_preview' class='status_update' style='margin:5px'></div>");
                                $('#text_format_preview').html("<img class='profile_picture_status profile_picture' src='./users/thumbs/users/<?php echo $_SESSION['id']; ?>/0.jpg' id='text_format_profile_picture' />");
                                $('#text_format_preview').html($('#text_format_preview').html()+"<div class='user_name_body'><span class='user_name' id='text_format_preview_name'><?php echo get_user_name($_SESSION['id']); ?></span></div>");
                                $('#text_format_preview').html($('#text_format_preview').html()+"<p class='status_update_text text_color' id='text_format_text' style='width:315px;'></p>");
                            $('#text_format_info').html("<table style='width:100%;margin-top:20px;' border='1'><tbody id='text_format_info_table_body'></tbody></table>");
                                $('#text_format_info_table_body').html("<tr id='text_format_row_1'></tr><tr id='text_format_row_2'></tr><tr id='text_format_row_3'></tr><tr id='text_format_row_4'></tr><tr id='text_format_row_5'></tr><tr id='text_format_row_6'></tr>");
                                    $('#text_format_row_1').html("<td><p style='font-weight:bold;'>Bold:</p></td><td><p>[b](This is bold) = <span style='font-weight:bold;'>This is bold</span></p></td>");
                                    $('#text_format_row_2').html("<td><p style='font-style:italic;'>Italics:</p></td><td><p>[i](This is italics) = <span style='font-style:italic'>This is italics</span></p></td>");
                                    $('#text_format_row_3').html("<td><p style='text-decoration:underline;'>Underline:</p></td><td><p>[u](This is underlined) = <span style='text-decoration:underline;'>This is underlined</span></p></td>");
                                    $('#text_format_row_4').html("<td><p ><span style='color:red;'>C</span><span style='color:orange;'>o</span><span style='color:purple;'>l</span><span style='color:green;'>o</span><span style='color:blue;'>r</span>:</p></td><td><p>[red](This is red) = <span style='color:red;'>This is red</span></p></td>");
                                    $('#text_format_row_5').html("<td><p style='border:1px solid black;width:35px;'>Box:</p></td><td><p>[box](This is boxed) = <span style='border:1px solid black;'>This is boxed</span></p></td>");
                                    $('#text_format_row_6').html("<td><p style='font-size:75%;'>Small:</p></td><td><p>[s](This is small) = <span style='font-size:50%;'>This is small</span></p></td>");
                
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
            
            function change_view()
           {
               $.post('change_home_view.php',
               {
                   num:1,
                   content: $('#home_content_display_button').data('content_type'),
                   add_id: $('#select_user_button').data('user_id')
               }, function(output)
               {
                   
               });
           }
            
            function initialize_view()
            {
                $.post('main_access.php',
                {
                    access:41,
                    num:2
                }, function(output)
                {
                    var content=output.content_view;
                    var add_id=output.add_id;
                    var add_name=output.add_name;
                    
                    $('#home_content_display_button').data('content_type', content);
                    
                    if(content!='Everything')
                        $('#home_content_display_button').attr('value', content);
                    
                    $('#select_user_button').data({'user_id': add_id, 'user_name': add_name});
                    
                    if(add_id!=-1)
                        $('#select_user_button').attr('value', add_name);


    //                $('#select_from_text_0').data('type', 'All');
    //                $('#select_from_text_1').data('type', 'Users');
    //                $('#select_from_text_2').data('type', 'Pages');
    //                $('#select_from_button').data('type', 'All');
                    $('#select_in_button').data('group_name', 'Everyone');
    //                $('#select_page_button').data({'page_id': -1, 'page_name': ''});
    
                    display_everything(1);
                }, "json");
            }
            
            $(document).ready(function()
            {

                <?php
//                    $path="http://www.redlay.com/users/images/$_SESSION[id]/background.jpg";
//                    $header_response = get_headers($path, 1);
//                    if ( strpos( $header_response[0], "404" ) !== true )
//                        echo "$('body').css({'background-image': 'url(\'$path\')', 'background-position' :'center 0px'});";
//                    else
                        echo "$('body').css({'background-image': 'url(\'./pictures/default_background.png\')', 'background-position' :'center 50px'});";
                ?>

                $('#menu').hide();
                display_form(0);
                display_groups('home_audience_box');
                initialize_post_preview();
                
                $('#home_content_box').hide();
                $('#home_from_box').hide();
                $('#home_adds_box').hide();
                $('#home_pages_box').hide();
                $('#post_form_row_4').hide();
                
                fill_user_box();
                $('#home_content_display_button').data('content_type', 'Everything');
                $('#home_from_display_button').data('user_type', 'All');
                $('#select_user_button').data({'user_id': -1, 'user_name': ''});
                $('#select_page_button').data({'page_id': -1, 'page_name': ''});
                
                initialize_view();
                //display_everything(1);
                change_color();
                <?php include('required_jquery.php'); ?>
            });
        </script>
        <script type="text/javascript">
            <?php include('../required_google_analytics.js'); ?>
        </script>
    </head>
    <body>
        <?php include('top.php'); ?>
        <?php include('required_html.php'); ?>
        <div id="main">
            <div id="home_post_box" class="box">
                <table id="home_menu_table">
                    <tr>
                        <td colspan="7">
                            <div id="home_post">
                                <table style="width:100%">
                                    <tbody>
                                        <tr>
                                            <td style="width:100%;">
                                                <table id="home_menu_table">
                                                    <tbody id="home_menu_table_body">
                                                        <tr>
                                                            <td class="home_menu_unit" id="home_menu_unit_left" onClick="display_form(1);" >
                                                                <p class="home_menu_text title_color">Photo</p>
                                                            </td>
                                                            <td class="home_menu_unit" id="home_menu_unit_middle" onClick="display_form(2);" >
                                                                <p class="home_menu_text title_color">Post</p>
                                                            </td>
                                                            <td class="home_menu_unit" id="home_menu_unit_right" onClick="display_form(3);">
                                                                <p class="home_menu_text title_color">Video</p>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td id="post_unit">
                                                <table style="width:100%;">
                                                    <tbody>
                                                        <tr>
                                                            <td colspan="2">
                                                                <textarea id="home_post_input" name="post_input" maxlength="500" class="textarea input_box" onFocus="input_in(this);" onBlur="input_out(this);" placeholder="What's up?" ></textarea>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <div id="home_audience_box" class="select_audience_box_body">

                                                                </div>
                                                            </td>
                                                            <td style="text-align:right;">
                                                                <input id="home_post_button" class="red_button" value="Post" type="button" onClick="post();"/>
                                                            </td>
                                                        </tr>

                                                        <tr class="post_form_row" id="post_form_row_4" style="text-align:left;">
                                                            <td colspan="2">
                                                                <div class="post_preview_box">
                                                                    <div id="post_preview_status_update" class="status_update" style="margin:25px">
                                                                        <img class="profile_picture_status profile_picture" src="http://www.redlay.com/users/thumbs/users/<?php echo $_SESSION[id]; ?>/0.jpg" id="post_preview_profile_picture" />
                                                                        <div class="user_name_body">
                                                                            <span class="user_name title_color" id="post_preview_name" ><?php echo get_user_name($_SESSION['id']); ?></span>
                                                                        </div>
                                                                        <p class="status_update_text text_color" id="post_preview_text" style="width:500px"></p>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                        </tr>

                                                        <tr>
                                                            <td colspan="2">
                                                                <hr class="break"/>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </td>
                                            <td id="photo_upload_unit">
                                            </td>
                                            <td id="video_unit">
                                            </td>
                                        </td>
                                    </tr>
                                        
                                        
                                        
                                        
                                        <tr>
                                            <td colspan="2">
                                                <table style="width:100%;">
                                                    <tbody>
                                                        <tr>
                                                            <td class="home_menu_display_item">
                                                                <input id="home_content_display_button" class="gray_button" type="button" value="View" onClick="toggle_view_display();"/>
                                                                <div id="home_content_box" class="select_body_options">
                                                                    <table class="select_body_options_table">
                                                                        <tbody class="select_body_options_table_body">
                                                                            <tr class="select_body_options_row">
                                                                                <td class="select_body_option_unit">
                                                                                    <p class="select_body_option_text" onClick="$('#home_content_display_button').data('content_type', 'Everything');display_everything(1);">Everything</p>
                                                                                </td>
                                                                            </tr>
                                                                            <tr class="select_body_options_row">
                                                                                <td class="select_body_option_unit">
                                                                                    <p class="select_body_option_text" onClick="$('#home_content_display_button').data('content_type', 'Posts');display_everything(1);">Posts</p>
                                                                                </td>
                                                                            </tr>
                                                                            <tr class="select_body_options_row">
                                                                                <td class="select_body_option_unit">
                                                                                    <p class="select_body_option_text" onClick="$('#home_content_display_button').data('content_type', 'Photos');display_everything(1);">Photos</p>
                                                                                </td>
                                                                            </tr>
                                                                            <tr class="select_body_options_row">
                                                                                <td class="select_body_option_unit">
                                                                                    <p class="select_body_option_text" onClick="$('#home_content_display_button').data('content_type', 'Others');display_everything(1);">Others</p>
                                                                                </td>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <span class="home_menu_seperator text_color">|</span>
                                                            </td>
                                                            <td class="home_menu_display_item" style="text-align:right">
                                                                <input id="select_user_button" type="button" class="gray_button" value="Adds" onClick="toggle_adds_display();"/>
                                                                <div id="home_adds_box" class="select_body_options">
                                                                    <table class="select_body_options_table">
                                                                        <tbody class="select_body_options_table_body" id="select_user_options_table_body">

                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
            <div id="home_content" class="box">
                <div id="home_post_text">

                </div>
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
                        if($(this).data('type')=='user_post'||$(this).data('type')=='user_photo')
                            comment($(this).data('post_id'), $(this).data('profile_id'), $(this).data('poster_id'), $(this).data('page'), $(this).data('index'));
                        else
                            page_post_comment($(this).data('post_id'), $(this).data('profile_id'), $(this).data('poster_id'), $(this).data('page'), $(this).data('index'));
                        $(this).val('');
                    }
                });
            }
            function initialize_post_preview()
            {
                $('#home_post_input').unbind('keypress').unbind('keydown').unbind('keyup');
                $('#home_post_input').keydown(function(e)
                {
                    display_post_preview(); 
                    change_post_preview();
                });
                $('#home_post_input').keyup(function(e)
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
    </body>
</html>