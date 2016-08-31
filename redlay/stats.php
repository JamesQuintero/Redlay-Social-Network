<?php
@include('init.php');
include('universal_functions.php');
$allowed="users";
include('security_checks.php');
if(!isset($_SESSION['id'])||!has_redlay_gold($_SESSION['id'], 'account_stats'))
{
    header("Location: http://www.redlay.com");
    exit();
}

?>
<html>
    <head>
        <title>Account statistics</title>
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
                
                $('.title_color').css('color', '<?php echo $color; ?>');
                $('.text_color').css('color', '<?php echo $text_color; ?>');
                
            }
            function display_menu_item(num)
            {
                hide_everything();
                if(num==1)
                {
                    display_profile_stats();
                    $('#profile_stats').show();
                }
                else if(num==2)
                {
                    //display_photo_stats();
                    show_photo_views_menu(1);
                    $('#photo_stats').show();
                }
                else if(num==3)
                {
                    show_post_menu(1);
                    $('#post_stats').show();
                }
                else if(num==4)
                {
                    show_other_menu(1);
                    $('#other_stats').show();
                }
            }
            function show_other_menu(num)
            {
                if(num==1)
                {
                    if($('#logins_row').is(':visible'))
                        $('#logins_row').hide();
                    else
                    {
                        hide_other_menu();
                        $('#logins_load_gif').show();
                        display_logins();
                        $('#logins_row').show();
                    }
                }
                else if(num==2)
                {
                    if($('#logouts_row').is(':visible'))
                        $('#logouts_row').hide();
                    else
                    {
                        hide_other_menu();
                        $('#logouts_load_gif').show();
                        display_logouts();
                        $('#logouts_row').show();
                    }
                }    
            }
            function show_post_menu(num)
            {
                if(num==1)
                {
                    if($('#post_likes_row').is(':visible'))
                        $('#post_likes_row').hide();
                    else
                    {
                        hide_post_menu();
                        $('#post_likes_load_gif').show();
                        display_post_likes();
                        $('#post_likes_row').show();
                    }    
                }    
                else if(num==2)
                {
                    if($('#post_dislikes_row').is(':visible'))
                        $('#post_dislikes_row').hide();
                    else
                    {
                        hide_post_menu();
                        $('#post_dislikes_load_gif').show();
                        display_post_dislikes();
                        $('#post_dislikes_row').show();
                    }    
                } 
                else if(num==3)
                {
                    if($('#post_comments_row').is(':visible'))
                        $('#post_comments_row').hide();
                    else
                    {
                        hide_post_menu();
                        $('#post_comments_load_gif').show();
                        display_post_comments();
                        $('#post_comments_row').show();
                    }    
                } 
            }
            function show_photo_views_menu(num)
            {
                if(num==1)
                {
                    if($('#photo_views_row').is(":visible"))
                        $('#photo_views_row').hide();
                    else
                    {
                        hide_photo_views_menu();
                        $('#photo_views_load_gif').show();
                        display_photo_views();
                        $('#photo_views_row').show();
                    }
                }    
                else if(num==2)
                {
                    if($('#photo_likes_row').is(":visible"))
                        $('#photo_likes_row').hide();
                    else
                    {
                        hide_photo_views_menu();
                        $('#photo_likes_load_gif').show();
                        display_photo_likes();
                        $('#photo_likes_row').show();
                    }
                }    
                else if(num==3)
                {
                    if($('#photo_dislikes_row').is(":visible"))
                        $('#photo_dislikes_row').hide();
                    else
                    {
                        hide_photo_views_menu();
                        $('#photo_dislikes_load_gif').show();
                        display_photo_dislikes();
                        $('#photo_dislikes_row').show();
                    }
                }
                else if(num==4)
                {
                    if($('#photo_comments_row').is(":visible"))
                        $('#photo_comments_row').hide();
                    else
                    {
                        hide_photo_views_menu();
                        $('#photo_comments_load_gif').show();
                        display_photo_comments();
                        $('#photo_comments_row').show();
                    }
                }
            }
            function hide_other_menu()
            {
                $('#logins_row').hide();
                $('#logins_load_gif').hide();
                $('#logouts_row').hide();
                $('#logouts_load_gif').hide();
            }
            function hide_post_menu()
            {
                $('#post_likes_row').hide();
                $('#post_likes_load_gif').hide();
                $('#post_dislikes_row').hide();
                $('#post_dislikes_load_gif').hide();
                $('#post_comments_row').hide();
                $('#post_comments_load_gif').hide();
            }
            function hide_photo_views_menu()
            {
                $('#photo_views_row').hide();
                $('#photo_views_load_gif').hide();
                $('#photo_likes_row').hide();
                $('#photo_likes_load_gif').hide();
                $('#photo_dislikes_row').hide();
                $('#photo_dislikes_load_gif').hide();
                $('#photo_comments_row').hide();
                $('#photo_comments_load_gif').hide();
            }
            function hide_everything()
            {
                $('#profile_stats').hide();
                $('#photo_stats').hide();
                $('#post_stats').hide();
                $('#other_stats').hide();
            }
            function display_profile_stats()
            {
                //if nothing has been displayed yet
                if($('#profile_views').html()=="")
                {
                    var timezone=get_timezone();
                    $.post('stats_query.php',
                    {
                        num:1, 
                        timezone: timezone
                    }, function(output)
                    {
                        var profile_views_number=output.profile_views_number;
                        var profile_views_total_views=output.profile_views_total_views;
                        var profile_views_last_views=output.profile_views_last_views;
                        
                        var profiles_viewed_number=output.profiles_viewed_number;
                        var profiles_viewed_profile_pictures=output.profiles_viewed_profile_pictures;
                        var profiles_viewed_names=output.profiles_viewed_names;
                        var profiles_viewed_ids=output.profiles_viewed_ids;
                        var profiles_viewed_total_views=output.profiles_viewed_total_views;
                        var profiles_viewed_last_views=output.profiles_viewed_last_views;
                        
                        
                        //displays profile views
                        if(profile_views_number[0]!=undefined)
                        {
                            $('#profile_views_total_views_unit').html("<p class='text_color'>Total views: "+profile_views_total_views+"</p>");
                            for(var x = 0; x < profile_views_number.length; x++)
                            {
                                var name="<div class='user_name_body'><span class='title_color' onmouseover=name_over(this); onmouseout=name_out(this);>?</span></div>";
                                var profile_picture="<img class='profile_picture profile_picture_status' src='http://pics.redlay.com/pictures/anonymous_user.png' />";
                                var views="<p><span class='title_color'>Views: </span><span class='text_color'>"+profile_views_number[x]+"</span></p>";
                                var last_view="<p><span class='title_color'>Last view: </span><span class='text_color'>"+profile_views_last_views[x]+"<span></p>";

                                var body=get_post_format(profile_picture, name, views+last_view, '', '', '', '', 'profile_views_body_'+x, '');
                                $('#profile_views').html(body+$('#profile_views').html());
                            }
                        }
                        else
                            $('#profile_views').html("<p class='text_color' style='font-weight:bold;text-align:center;'>No one has viewed your profile yet</p>");
                        
                        
                        //displays profiles viewed
                        if(profiles_viewed_number[0]!=undefined)
                        {
                            $('#profiles_viewed_total_views_unit').html("<p class='text_color'>Total views: "+profiles_viewed_total_views+"</p>");
                            for(var x = 0; x < profiles_viewed_ids.length; x++)
                            {
                                var name="<div class='user_name_body'><a class='link' href='http://www.redlay.com/profile.php?user_id="+profiles_viewed_ids[x]+"'><span class='title_color' onmouseover=name_over(this); onmouseout=name_out(this);>"+profiles_viewed_names[x]+"</span></a></div>";
                                var profile_picture="<a class='link' href='http://www.redlay.com/profile.php?user_id="+profiles_viewed_ids[x]+"'><img class='profile_picture profile_picture_status' src='"+profiles_viewed_profile_pictures[x]+"' /></a></div>";
                                var views="<p><span class='title_color'>Views: </span><span class='text_color'>"+profiles_viewed_number[x]+"</span></p>";
                                var last_view="<p><span class='title_color'>Last view: </span><span class='text_color'>"+profiles_viewed_last_views[x]+"<span></p>";

                                var body=get_post_format(profile_picture, name, views+last_view, '', '', '', '', 'profiles_viewed_body_'+x, '');
                                $('#profiles_viewed').html(body+$('#profiles_viewed').html());
                            }
                        }
                        else
                            $('#profiles_viewed').html("<p class='text_color' style='font-weight:bold;text-align:center;'>You have not viewed anyone's profile yet</p>");
                        
                    }, "json");
                }
                
            }
            function display_photo_views()
            {
                //if nothing has been displayed yet
                if($('#photo_views').html()=="")
                {
                    var timezone=get_timezone();
                    $.post('stats_query.php',
                    {
                        num: 2, 
                        timezone: timezone
                    }, function(output)
                    {
                        var photo_views_numbers=output.photo_views_numbers;
                        var photo_views_total_number=output.photo_views_total_number;
                        var photo_views_photo_ids=output.photo_views_photo_ids;
                        var photo_views_photo_links=output.photo_views_photo_links;
                        var photo_views_photo_descriptions=output.photo_views_photo_descriptions;
                        var my_profile_picture=output.my_profile_picture;
                        
                        var photos_viewed_number=output.photos_viewed_number;
                        var photos_viewed_total_views=output.photos_viewed_total_views;
                        var photos_viewed_photo_ids=output.photos_viewed_photo_ids;
                        var photos_viewed_ids=output.photos_viewed_ids;
                        var photos_viewed_photo_links=output.photos_viewed_photo_links;
                        var photos_viewed_photo_names=output.photos_viewed_photo_names;
                        var photos_viewed_photo_descriptions=output.photos_viewed_photo_descriptions;
                        var photos_viewed_profile_pictures=output.photos_viewed_profile_pictures;
                        
                        
                        
                        
                        //displays profile views
                        if(photo_views_numbers[0]!=undefined)
                        {
                            $('#photo_views_total_views_unit').html("<p style='margin:0px;'><span class='title_color'>Total views: </span><span class='text_color'>"+photo_views_total_number+"</span></p>");
                            for(var x = 0; x < photo_views_numbers.length; x++)
                            {
                                photo_views_photo_descriptions[x]=text_format(photo_views_photo_descriptions[x]);
                                var image="<a class='link' href='http://www.redlay.com/view_photo.php?user_id=<?php echo $_SESSION['id']; ?>&&picture_id="+photo_views_photo_ids[x]+"&&type=user' ><img class='picture_post stats_image' src='"+photo_views_photo_links[x]+"'/></a>";
                                var views="<p style='margin:0px;'><span class='title_color'>Views: </span><span class='text_color'>"+photo_views_numbers[x]+"</span></p>";
                                var description="<span class='text_color'>"+photo_views_photo_descriptions[x]+"</span>";
                                var name="";
                                var profile_picture="<a class='link' href='http://www.redlay.com/profile.php?user_id=<?php echo $_SESSION['id']; ?>'><img class='profile_picture profile_picture_status' src='"+my_profile_picture+"'/></a>";
                                var picture_body="<table><tbody><tr><td>"+image+"</td><td>"+views+"</td></tr></tbody></table>";

                                var body=get_post_format(profile_picture, name, picture_body+description, '', '', '', '', 'photo_views_body_'+x, '');
                                $('#photo_views').html(body+$('#photo_views').html());
                            }
                        }
                        else
                            $('#photo_views').html("<p class='text_color' style='font-weight:bold;text-align:center;'>No one has viewed any of your photos yet</p>");
                        
                        
                        //displays profiles viewed
                        if(photos_viewed_total_views!=0)
                        {
                            $('#photos_viewed_total_views_unit').html("<p style='margin:0px;'><span class='title_color'>Total views: </span><span class='text_color'>"+photos_viewed_total_views+"</span></p>");
                            for(var x = 0; x < photos_viewed_number.length; x++)
                            {
                                photos_viewed_photo_descriptions[x]=text_format(photos_viewed_photo_descriptions[x]);
                                var name="<div class='user_name_body'><a class='link' href='http://www.redlay.com/profile.php?user_id="+photos_viewed_ids[x]+"'><span class='user_name title_color' onmouseover=name_over(this); onmouseout=name_out(this);>"+photos_viewed_photo_names[x]+"</span></a></div>";
                                var profile_picture="<a class='link' href='http://www.redlay.com/profile.php?user_id="+photos_viewed_ids[x]+"'><img class='profile_picture profile_picture_status' src='"+photos_viewed_profile_pictures[x]+"' /></a></div>";
                                var views="<p style='margin:0px'><span class='title_color'>Views: </span><span class='text_color'>"+photos_viewed_number[x]+"</span></p>";
                                var image="<a class='link' href='http://www.redlay.com/view_photo.php?user_id="+photos_viewed_ids[x]+"&&picture_id="+photos_viewed_photo_ids[x]+"&&type=user' ><img class='picture_post stats_image' src='"+photos_viewed_photo_links[x]+"'/></a>";
                                var description="<span class='text_color'>"+photos_viewed_photo_descriptions[x]+"</span>";
                                
                                var picture_body="<table><tbody><tr><td>"+image+"</td><td>"+views+"</td></tr></tbody></table>";

                                var body=get_post_format(profile_picture, name, picture_body+description, '', '', '', '', 'photos_viewed_body_'+x, '');
                                $('#photos_viewed').html(body+$('#photos_viewed').html());
                            }
                        }
                        else
                            $('#photos_viewed').html("<p class='text_color' style='font-weight:bold;text-align:center;'>You have not viewed anyone's photos yet</p>");
                        
                        change_color();
                        $('#photo_views_load_gif').hide();
                    }, "json");
                }
                else
                    $('#photo_views_load_gif').hide();
            }
            function display_photo_likes()
            {
                //if nothing has been displayed yet
                if($('#photo_likes').html()=="")
                {
                    var timezone=get_timezone();
                    $.post('stats_query.php',
                    {
                        num: 3, 
                        timezone: timezone
                    }, function(output)
                    {
                        
                        var photo_total_likes=output.photo_total_likes;
                        var photo_likes_photo_ids=output.photo_likes_photo_ids;
                        var photo_likes_photo_links=output.photo_likes_photo_links;
                        var photo_likes_photo_descriptions=output.photo_likes_photo_descriptions;
                        var photo_likes_user_ids=output.photo_likes_user_ids;
                        var photo_likes_profile_pictures=output.photo_likes_profile_pictures;
                        var photo_likes_names=output.photo_likes_names;
                        var photo_likes_like_date=output.photo_likes_like_date;
                        
                        
                        
                        //displays profile views
                        if(photo_total_likes!=0)
                        {
                            $('#photo_likes_total_likes_unit').html("<p class='text_color'>Total likes: "+photo_total_likes+"</p>");
                            for(var x = 0; x < photo_likes_photo_ids.length; x++)
                            {
                                if(photo_likes_photo_descriptions[x]!=undefined&&photo_likes_photo_descriptions[x]!='')
                                    photo_likes_photo_descriptions[x]=text_format(photo_likes_photo_descriptions[x]);
                                var image="<a class='link' href='http://www.redlay.com/view_photo.php?user_id="+photo_likes_user_ids[x]+"&&picture_id="+photo_likes_photo_ids[x]+"&&type=user' ><img class='picture_post stats_image' src='"+photo_likes_photo_links[x]+"'/></a>";
                                var description="<p style='margin:0px;' ><span class='text_color'>"+photo_likes_photo_descriptions[x]+"</span></p>";
                                var name="<div class='user_name_body'><a class='link' href='http://www.redlay.com/profile.php?user_id="+photo_likes_user_ids[x]+"'><span class='user_name title_color' onmouseover='name_over(this);' onmouseout='name_out(this);' >"+photo_likes_names[x]+"</span></a></div>";
                                var profile_picture="<a class='link' href='http://www.redlay.com/profile.php?user_id="+photo_likes_user_ids[x]+"'><img class='profile_picture profile_picture_status' src='"+photo_likes_profile_pictures[x]+"'/></a>";
                                var liked_date="<p style='margin:0px'><span class='title_color'>Liked: </span><span class='text_color'>"+photo_likes_like_date[x]+"</span></p>";


                                var body=get_post_format(profile_picture, name, image+description+liked_date, '', '', '', '', 'photo_likes_body_'+x, '');
                                $('#photo_likes').html(body+$('#photo_likes').html());
                            }
                        }
                        else
                            $('#photo_likes').html("<p class='text_color' style='font-weight:bold;text-align:center;'>You have not liked any photos yet.</p>");
                        
                        
                        
                        change_color();
                        $('#photo_likes_load_gif').hide();
                    }, "json");
                }
                else
                    $('#photo_likes_load_gif').hide();
            }
            function display_photo_dislikes()
            {
                //if nothing has been displayed yet
                if($('#photo_dislikes').html()=="")
                {
                    var timezone=get_timezone();
                    $.post('stats_query.php',
                    {
                        num: 4, 
                        timezone: timezone
                    }, function(output)
                    {
                        
                        var photo_total_dislikes=output.photo_total_dislikes;
                        var photo_dislikes_photo_ids=output.photo_dislikes_photo_ids;
                        var photo_dislikes_photo_links=output.photo_dislikes_photo_links;
                        var photo_dislikes_photo_descriptions=output.photo_dislikes_photo_descriptions;
                        var photo_dislikes_user_ids=output.photo_dislikes_user_ids;
                        var photo_dislikes_profile_pictures=output.photo_dislikes_profile_pictures;
                        var photo_dislikes_names=output.photo_dislikes_names;
                        var photo_dislikes_dislike_date=output.photo_dislikes_dislike_date;
                        
                        
                        
                        //displays profile views
                        if(photo_total_dislikes!=0)
                        {
                            $('#photo_dislikes_total_dislikes_unit').html("<p class='text_color'>Total dislikes: "+photo_total_dislikes+"</p>");
                            for(var x = 0; x < photo_dislikes_photo_ids.length; x++)
                            {
                                photo_dislikes_photo_descriptions[x]=text_format(photo_dislikes_photo_descriptions[x]);
                                var image="<a class='link' href='http://www.redlay.com/view_photo.php?user_id="+photo_dislikes_user_ids[x]+"&&picture_id="+photo_dislikes_photo_ids[x]+"&&type=user' ><img class='picture_post stats_image' src='"+photo_dislikes_photo_links[x]+"'/></a>";
                                var description="<p style='margin:0px;' ><span class='text_color'>"+photo_dislikes_photo_descriptions[x]+"</span></p>";
                                var name="<div class='user_name_body'><a class='link' href='http://www.redlay.com/profile.php?user_id="+photo_dislikes_user_ids[x]+"'><span class='user_name title_color' onmouseover='name_over(this);' onmouseout='name_out(this);' >"+photo_dislikes_names[x]+"</span></a></div>";
                                var profile_picture="<a class='link' href='http://www.redlay.com/profile.php?user_id="+photo_dislikes_user_ids[x]+"'><img class='profile_picture profile_picture_status' src='"+photo_dislikes_profile_pictures[x]+"'/></a>";
                                var disliked_date="<p style='margin:0px'><span class='title_color'>Disliked: </span><span class='text_color'>"+photo_dislikes_dislike_date[x]+"</span></p>";


                                var body=get_post_format(profile_picture, name, image+description+disliked_date, '', '', '', '', 'photo_dislikes_body_'+x, '');
                                $('#photo_dislikes').html(body+$('#photo_dislikes').html());
                            }
                        }
                        else
                            $('#photo_dislikes').html("<p class='text_color' style='font-weight:bold;text-align:center;'>You have not disliked any photos yet.</p>");
                        
                        
                        
                        change_color();
                        $('#photo_dislikes_load_gif').hide();
                    }, "json");
                }
                else
                    $('#photo_dislikes_load_gif').hide();
            }
            function display_photo_comments()
            {
                //if nothing has been displayed yet
                if($('#photo_comments').html()=="")
                {
                    var timezone=get_timezone();
                    $.post('stats_query.php',
                    {
                        num: 5, 
                        timezone: timezone
                    }, function(output)
                    {
                        
                        var photo_comments_number=output.photo_comments_number;
                        var photo_comments_total_comments=output.photo_comments_total_comments;
                        var photo_comments_photo_ids=output.photo_comments_photo_ids;
                        var photo_comments_ids=output.photo_comments_ids;
                        var photo_comments_photo_links=output.photo_comments_photo_links;
                        var photo_comments_photo_names=output.photo_comments_photo_names;
                        var photo_comments_photo_descriptions=output.photo_comments_photo_descriptions;
                        var photo_comments_profile_pictures=output.photo_comments_profile_pictures;
                        
                        
                        //displays profiles viewed
                        if(photo_comments_total_comments!=0)
                        {
                            $('#photo_comments_total_comments_unit').html("<p style='margin:0px;'><span class='title_color'>Total comments: </span><span class='text_color'>"+photo_comments_total_comments+"</span></p>");
                            for(var x = 0; x < photo_comments_number.length; x++)
                            {
                                photo_comments_photo_descriptions[x]=text_format(photo_comments_photo_descriptions[x]);
                                var name="<div class='user_name_body'><a class='link' href='http://www.redlay.com/profile.php?user_id="+photo_comments_ids[x]+"'><span class='user_name title_color' onmouseover=name_over(this); onmouseout=name_out(this);>"+photo_comments_photo_names[x]+"</span></a></div>";
                                var profile_picture="<a class='link' href='http://www.redlay.com/profile.php?user_id="+photo_comments_ids[x]+"'><img class='profile_picture profile_picture_status' src='"+photo_comments_profile_pictures[x]+"' /></a></div>";
                                var views="<p style='margin:0px'><span class='title_color'>Commented: </span><span class='text_color'>"+photo_comments_number[x]+"</span></p>";
                                var image="<a class='link' href='http://www.redlay.com/view_photo.php?user_id="+photo_comments_ids[x]+"&&picture_id="+photo_comments_photo_ids[x]+"&&type=user' ><img class='picture_post stats_image' src='"+photo_comments_photo_links[x]+"'/></a>";
                                var description="<span class='text_color'>"+photo_comments_photo_descriptions[x]+"</span>";
                                
                                var picture_body="<table><tbody><tr><td>"+image+"</td><td>"+views+"</td></tr></tbody></table>";

                                var body=get_post_format(profile_picture, name, picture_body+description, '', '', '', '', 'photo_comments_body_'+x, '');
                                $('#photo_comments').html(body+$('#photo_comments').html());
                            }
                        }
                        else
                            $('#photo_comments').html("<p class='text_color' style='font-weight:bold;text-align:center;'>You have not commented on anyone's photos yet</p>");
                        
                        
                        
                        change_color();
                        $('#photo_comments_load_gif').hide();
                    }, "json");
                }
                else
                    $('#photo_comments_load_gif').hide();
            }
            
            
            function display_post_likes()
            {
                //if nothing has been displayed yet
                if($('#post_likes').html()=="")
                {
                    var timezone=get_timezone();
                    $.post('stats_query.php',
                    {
                        num: 6, 
                        timezone: timezone
                    }, function(output)
                    {
                        var post_total_likes=output.post_total_likes;
                        var post_likes_post_ids=output.post_likes_post_ids;
                        var post_likes_body=output.post_likes_body;
                        var post_likes_user_ids=output.post_likes_user_ids;
                        var post_likes_profile_pictures=output.post_likes_profile_pictures;
                        var post_likes_names=output.post_likes_names;
                        var post_likes_like_date=output.post_likes_like_date;
                        
                        //displays profile views
                        if(post_total_likes!=0)
                        {
                            $('#post_likes_total_likes_unit').html("<p class='text_color'>Total likes: "+post_total_likes+"</p>");
                            for(var x = 0; x < post_likes_body.length; x++)
                            {
                                post_likes_body[x]=text_format(post_likes_body[x]);
                                var post="<span class='text_color'>"+post_likes_body[x]+"</span>";
                                var name="<div class='user_name_body'><a class='link' href='http://www.redlay.com/profile.php?user_id="+post_likes_user_ids[x]+"'><span class='user_name title_color' onmouseover='name_over(this);' onmouseout='name_out(this);' >"+post_likes_names[x]+"</span></a></div>";
                                var profile_picture="<a class='link' href='http://www.redlay.com/profile.php?user_id="+post_likes_user_ids[x]+"'><img class='profile_picture profile_picture_status' src='"+post_likes_profile_pictures[x]+"'/></a>";
                                var liked_date="<p style='margin:0px'><span class='title_color'>Liked: </span><span class='text_color'>"+post_likes_like_date[x]+"</span></p>";


                                var body=get_post_format(profile_picture, name, post+liked_date, '', '', '', '', 'post_likes_body_'+x, '');
                                $('#post_likes').html(body+$('#post_likes').html());
                                $('#post_likes_body_'+x).html("<a class='link' href='http://www.redlay.com/view_post.php?post_id="+post_likes_post_ids[x]+"&&profile_id="+post_likes_user_ids[x]+"'>"+$('#post_likes_body_'+x).html()+"</a>");
                            }
                        }
                        else
                            $('#post_likes').html("<p class='text_color' style='font-weight:bold;text-align:center;'>You have not liked any posts yet.</p>");
                        
                        
                        
                        change_color();
                        $('#post_likes_load_gif').hide();
                        
                    }, "json");
                }
                else
                    $('#post_likes_load_gif').hide();
            }
            function display_post_dislikes()
            {
                //if nothing has been displayed yet
                if($('#post_dislikes').html()=="")
                {
                    var timezone=get_timezone();
                    $.post('stats_query.php',
                    {
                        num: 7, 
                        timezone: timezone
                    }, function(output)
                    {
                        var post_total_dislikes=output.post_total_dislikes;
                        var post_dislikes_post_ids=output.post_dislikes_post_ids;
                        var post_dislikes_body=output.post_dislikes_body;
                        var post_dislikes_user_ids=output.post_dislikes_user_ids;
                        var post_dislikes_profile_pictures=output.post_dislikes_profile_pictures;
                        var post_dislikes_names=output.post_dislikes_names;
                        var post_dislikes_dislike_date=output.post_dislikes_dislike_date;
                        
                        //displays profile views
                        if(post_total_dislikes!=0)
                        {
                            $('#post_dislikes_total_dislikes_unit').html("<p class='text_color'>Total dislikes: "+post_total_dislikes+"</p>");
                            for(var x = 0; x < post_dislikes_post_ids.length; x++)
                            {
                                post_dislikes_body[x]=text_format(post_dislikes_body[x]);
                                var post="<span class='text_color'>"+post_dislikes_body[x]+"</span>";
                                var name="<div class='user_name_body'><a class='link' href='http://www.redlay.com/profile.php?user_id="+post_dislikes_user_ids[x]+"'><span class='user_name title_color' onmouseover='name_over(this);' onmouseout='name_out(this);' >"+post_dislikes_names[x]+"</span></a></div>";
                                var profile_picture="<a class='link' href='http://www.redlay.com/profile.php?user_id="+post_dislikes_user_ids[x]+"'><img class='profile_picture profile_picture_status' src='"+post_dislikes_profile_pictures[x]+"'/></a>";
                                var disliked_date="<p style='margin:0px'><span class='title_color'>Disliked: </span><span class='text_color'>"+post_dislikes_dislike_date[x]+"</span></p>";


                                var body=get_post_format(profile_picture, name, post+disliked_date, '', '', '', '', 'post_dislikes_body_'+x, '');
                                $('#post_dislikes').html(body+$('#post_dislikes').html());
                                $('#post_dislikes_body_'+x).html("<a class='link' href='http://www.redlay.com/view_post.php?post_id="+post_dislikes_post_ids[x]+"&&profile_id="+post_dislikes_user_ids[x]+"'>"+$('#post_dislikes_body_'+x).html()+"</a>");
                            }
                        }
                        else
                            $('#post_dislikes').html("<p class='text_color' style='font-weight:bold;text-align:center;'>You have not disliked any posts yet.</p>");
                        
                        
                        
                        change_color();
                        $('#post_dislikes_load_gif').hide();
                        
                    }, "json");
                }
                else
                    $('#post_dislikes_load_gif').hide();
            }
            function display_post_comments()
            {
                //if nothing has been displayed yet
                if($('#post_comments').html()=="")
                {
                    var timezone=get_timezone();
                    $.post('stats_query.php',
                    {
                        num: 8, 
                        timezone: timezone
                    }, function(output)
                    {
                        var post_comments_number=output.post_comments_number;
                        var post_comments_total_comments=output.post_comments_total_comments;
                        var post_comments_post_ids=output.post_comments_post_ids;
                        var post_comments_ids=output.post_comments_ids;
                        var post_comments_post_names=output.post_comments_post_names;
                        var post_comments_body=output.post_comments_body;
                        var post_comments_profile_pictures=output.post_comments_profile_pictures;
                        
                        
                        //displays profiles viewed
                        if(post_comments_total_comments!=0)
                        {
                            $('#post_comments_total_comments_unit').html("<p style='margin:0px;'><span class='title_color'>Total comments: </span><span class='text_color'>"+post_comments_total_comments+"</span></p>");
                            for(var x = 0; x < post_comments_number.length; x++)
                            {
                                post_comments_body[x]=text_format(post_comments_body[x]);
                                var name="<div class='user_name_body'><a class='link' href='http://www.redlay.com/profile.php?user_id="+post_comments_ids[x]+"'><span class='user_name title_color' onmouseover=name_over(this); onmouseout=name_out(this);>"+post_comments_post_names[x]+"</span></a></div>";
                                var profile_picture="<a class='link' href='http://www.redlay.com/profile.php?user_id="+post_comments_ids[x]+"'><img class='profile_picture profile_picture_status' src='"+post_comments_profile_pictures[x]+"' /></a></div>";
                                var comments="<p style='margin:0px'><span class='title_color'>Commented: </span><span class='text_color'>"+post_comments_number[x]+"</span></p>";
                                var post_body="<span class='text_color'>"+post_comments_body[x]+"</span>";
                                

                                var body=get_post_format(profile_picture, name, post_body+comments, '', '', '', '', 'post_comments_body_'+x, '');
                                $('#post_comments').html(body+$('#post_comments').html());
                                $('#post_comments_body_'+x).html("<a class='link' href='http://www.redlay.com/view_post.php?post_id="+post_comments_post_ids[x]+"&&profile_id="+post_comments_ids[x]+"'>"+$('#post_comments_body_'+x).html()+"</a>");
                            }
                        }
                        else
                            $('#post_comments').html("<p class='text_color' style='font-weight:bold;text-align:center;'>You have not commented on anyone's posts yet</p>");
                        
                        
                        
                        change_color();
                        $('#post_comments_load_gif').hide();
                    }, "json");
                }
                else
                    $('#post_comments_load_gif').hide();
            }
            function display_logins()
            {
                if($('#logins').html()=="")
                {
                    var timezone=get_timezone();
                    $.post('stats_query.php',
                    {
                        num:9,
                        timezone: timezone
                    }, function(output)
                    {
                        var dates=output.dates;
                        var ip_addresses=output.ip_addresses;
                        
                        
                        for(var x = 0; x < dates.length; x++)
                        {
                            var date="<p style='margin:0px;' ><span class='title_color'>Date: </span><span class='text_color'>"+dates[x]+"</span></p>";
                            var ip_address="<p style='margin: 0px;'><span class='title_color'>IP addr: </span><span class='text_color'>"+ip_addresses[x]+"</span></p>";
                            
                            var body="<table style='width:100%;text-align:center;'><tbody><tr><td>"+date+"</td></tr><tr><td>"+ip_address+"</td></tr><tr><td><hr class='break'/></td></tr></tbody></table>";
                            $('#logins').html(body+$('#logins').html());
                        }
                        
                        
                        change_color();
                        $('#logins_load_gif').hide();
                    }, "json");
                }
                else
                    $('#logins_load_gif').hide();
            }
            function display_logouts()
            {
                if($('#logouts').html()=="")
                {
                    var timezone=get_timezone();
                    $.post('stats_query.php',
                    {
                        num:10,
                        timezone: timezone
                    }, function(output)
                    {
                        var dates=output.dates;
                        var ip_addresses=output.ip_addresses;
                        
                        
                        for(var x = 0; x < dates.length; x++)
                        {
                            var date="<p style='margin:0px;' ><span class='title_color'>Date: </span><span class='text_color'>"+dates[x]+"</span></p>";
                            var ip_address="<p style='margin: 0px;'><span class='title_color'>IP addr: </span><span class='text_color'>"+ip_addresses[x]+"</span></p>";
                            
                            var body="<table style='width:100%;text-align:center;'><tbody><tr><td>"+date+"</td></tr><tr><td>"+ip_address+"</td></tr><tr><td><hr class='break'/></td></tr></tbody></table>";
                            $('#logouts').html(body+$('#logouts').html());
                        }
                        
                        
                        change_color();
                        $('#logouts_load_gif').hide();
                    }, "json");
                }
                else
                    $('#logouts_load_gif').hide();
            }
        </script>
        <script type="text/javascript">
            $(window).ready(function()
            {
                change_color();
                $('#menu').hide();
                $('#footer').css('width', '910px');
                display_menu_item(1);

                <?php include('required_jquery.php'); ?>
            });
        </script>
        <script type="text/javascript">
            <?php include('required_google_analytics.js'); ?>
        </script>
    </head>
    <body>
        <div id="top">
            <?php include('top.php'); ?>
        </div>
        <div id="main">
            <?php include('required_side_html.php'); ?>
            <div id="stats_content" class="content box">
                
                <table id="stats_table">
                    <tbody>
                        <tr>
                            <td style="width:100px;border-right:1px solid gray;height:500px;vertical-align:top;">
                                
                                
                                
                                <table id="stats_menu_table">
                                    <tbody>
                                        <tr>
                                            <td>
                                                <span class="stats_menu_item title_color" onClick="display_menu_item(1);" onmouseover="name_over(this);" onmouseout="name_out(this);">Profile</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span class="stats_menu_item title_color" onClick="display_menu_item(2);" onmouseover="name_over(this);" onmouseout="name_out(this);">Photos</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span class="stats_menu_item title_color" onClick="display_menu_item(3);" onmouseover="name_over(this);" onmouseout="name_out(this);">Posts</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span class="stats_menu_item title_color" onClick="display_menu_item(4);" onmouseover="name_over(this);" onmouseout="name_out(this);">Other</span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                
                                
                                
                            </td>
                            <td style="padding:10px;vertical-align:top;" >
                                
                                
                                
                                <div id="profile_stats">
                                    
                                    
                                    
                                    <table>
                                        <tbody>
                                            <tr>
                                                <td colspan="2">
                                                    <p class="title_color" style="font-size:25px;text-align:center;">Profile:</p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="width:50%;border-right:1px solid gray;vertical-align:top" >
                                                    <table >
                                                        <tbody>
                                                            <tr>
                                                                <td style="border-bottom: 1px solid gray;">
                                                                    <table>
                                                                        <tbody>
                                                                            <tr>
                                                                                <td>
                                                                                    <p class="title_color" style="text-align:center;">Profile views: </p>
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <span class="text_color">Adds who viewed your profile the most. They are kept anonymous for privacy.</span>
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td id="profile_views_total_views_unit" style="border-top:1px solid gray;">
                                                                                    
                                                                                </td>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <div id="profile_views" class="scrollable_stats"></div>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                    
                                                </td>
                                                <td style="width:50%;vertical-align:top;">
                                                    <table>
                                                        <tbody>
                                                            <tr>
                                                                <td style="border-bottom: 1px solid gray;">
                                                                    <table>
                                                                        <tbody>
                                                                            <tr>
                                                                                <td>
                                                                                    <p class="title_color" style="text-align:center;">Profiles viewed: </p>
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <span class="text_color">Profiles you viewed the most.</span>
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td id="profiles_viewed_total_views_unit" style="border-top:1px solid gray;">
                                                                                    
                                                                                </td>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <div id="profiles_viewed" class="scrollable_stats"></div>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    
                                    
                                    
                                </div>
                                <div id="photo_stats">
                                    
                                    
                                    <table>
                                        <tbody>
                                            <tr>
                                                <td id="photo_views_unit">
                                                    <table>
                                                        <tbody>
                                                            <tr>
                                                                <td>
                                                                    <span class="title_color" onmouseover="name_over(this);" onmouseout="name_out(this);" onClick="show_photo_views_menu(1);" style="cursor:pointer;">Views</span>
                                                                    <img class="load_gif" src="http://pics.redlay.com/pictures/load.gif" id="photo_views_load_gif"/>
                                                                </td>
                                                            </tr>
                                                            <tr id="photo_views_row">
                                                                <td>
                                                                    
                                                                    <table>
                                                                        <tbody>
                                                                            <tr>
                                                                                <td>
                                                                                    <p class="text_color">Number of views on your photos.</p>
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td id="photo_views_total_views_unit" style="border-top:1px solid gray;">
                                                                                    
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <div id="photo_views" class="scrollable_stats"></div>
                                                                                </td>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>
                                                                    
                                                                </td>
                                                                <td>
                                                                    
                                                                    <table>
                                                                        <tbody>
                                                                            <tr>
                                                                                <td>
                                                                                    <p class="text_color">Number of times you've viewed certain photos.</p>
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td id="photos_viewed_total_views_unit" style="border-top:1px solid gray;">
                                                                                    
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <div id="photos_viewed" class="scrollable_stats"></div>
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
                                            <tr>
                                                <td id="photo_likes_unit">
                                                    
                                                    
                                                    <table>
                                                        <tbody>
                                                            <tr>
                                                                <td>
                                                                    <span class="title_color" onmouseover="name_over(this);" onmouseout="name_out(this);" onClick="show_photo_views_menu(2);" style="cursor:pointer;">Likes</span>
                                                                    <img class="load_gif" src="http://pics.redlay.com/pictures/load.gif" id="photo_likes_load_gif"/>
                                                                </td>
                                                            </tr>
                                                            <tr id="photo_likes_row">
                                                                <td>
                                                                    
                                                                    <table>
                                                                        <tbody>
                                                                            <tr>
                                                                                <td>
                                                                                    <p class="text_color">Photos you've liked.</p>
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td id="photo_likes_total_likes_unit" style="border-top:1px solid gray;">
                                                                                    
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <div id="photo_likes" class="scrollable_stats"></div>
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
                                            <tr>
                                                <td id="photo_dislikes_unit">
                                                    
                                                    <table>
                                                        <tbody>
                                                            <tr>
                                                                <td>
                                                                    <span class="title_color" onmouseover="name_over(this);" onmouseout="name_out(this);" onClick="show_photo_views_menu(3);" style="cursor:pointer;">Dislikes</span>
                                                                    <img class="load_gif" src="http://pics.redlay.com/pictures/load.gif" id="photo_dislikes_load_gif"/>
                                                                </td>
                                                            </tr>
                                                            <tr id="photo_dislikes_row">
                                                                <td>
                                                                    
                                                                    <table>
                                                                        <tbody>
                                                                            <tr>
                                                                                <td>
                                                                                    <p class="text_color">Photos you've disliked.</p>
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td id="photo_dislikes_total_dislikes_unit" style="border-top:1px solid gray;">
                                                                                    
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <div id="photo_dislikes" class="scrollable_stats"></div>
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
                                            <tr>
                                                <td id="photo_comments_unit">
                                                    
                                                    <table>
                                                        <tbody>
                                                            <tr>
                                                                <td>
                                                                    <span class="title_color" onmouseover="name_over(this);" onmouseout="name_out(this);" onClick="show_photo_views_menu(4);" style="cursor:pointer;">Comments</span>
                                                                    <img class="load_gif" src="http://pics.redlay.com/pictures/load.gif" id="photo_comments_load_gif"/>
                                                                </td>
                                                            </tr>
                                                            <tr id="photo_comments_row">
                                                                <td>
                                                                    
                                                                    <table>
                                                                        <tbody>
                                                                            <tr>
                                                                                <td>
                                                                                    <p class="text_color">Photos you've commented on.</p>
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td id="photo_comments_total_comments_unit" style="border-top:1px solid gray;">
                                                                                    
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <div id="photo_comments" class="scrollable_stats"></div>
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
                                    
                                    
                                    
                                    
                                </div>
                                <div id="post_stats">
                                    
                                    
                                    
                                    <table>
                                        <tbody>
                                            <tr>
                                                <td id="post_likes_unit">
                                                    
                                                    
                                                    <table>
                                                        <tbody>
                                                            <tr>
                                                                <td>
                                                                    <span class="title_color" onmouseover="name_over(this);" onmouseout="name_out(this);" onClick="show_post_menu(1);" style="cursor:pointer;">Likes</span>
                                                                    <img class="load_gif" src="http://pics.redlay.com/pictures/load.gif" id="post_likes_load_gif"/>
                                                                </td>
                                                            </tr>
                                                            <tr id="post_likes_row">
                                                                <td>
                                                                    
                                                                    <table>
                                                                        <tbody>
                                                                            <tr>
                                                                                <td>
                                                                                    <p class="text_color">Posts you've liked.</p>
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td id="post_likes_total_likes_unit" style="border-top:1px solid gray;">
                                                                                    
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <div id="post_likes" class="scrollable_stats"></div>
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
                                            <tr>
                                                <td id="post_dislikes_unit">
                                                    
                                                    <table>
                                                        <tbody>
                                                            <tr>
                                                                <td>
                                                                    <span class="title_color" onmouseover="name_over(this);" onmouseout="name_out(this);" onClick="show_post_menu(2);" style="cursor:pointer;">Dislikes</span>
                                                                    <img class="load_gif" src="http://pics.redlay.com/pictures/load.gif" id="post_dislikes_load_gif"/>
                                                                </td>
                                                            </tr>
                                                            <tr id="post_dislikes_row">
                                                                <td>
                                                                    
                                                                    <table>
                                                                        <tbody>
                                                                            <tr>
                                                                                <td>
                                                                                    <p class="text_color">Posts you've disliked.</p>
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td id="post_dislikes_total_dislikes_unit" style="border-top:1px solid gray;">
                                                                                    
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <div id="post_dislikes" class="scrollable_stats"></div>
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
                                            <tr>
                                                <td id="post_comments_unit">
                                                    
                                                    <table>
                                                        <tbody>
                                                            <tr>
                                                                <td>
                                                                    <span class="title_color" onmouseover="name_over(this);" onmouseout="name_out(this);" onClick="show_post_menu(3);" style="cursor:pointer;">Comments</span>
                                                                    <img class="load_gif" src="http://pics.redlay.com/pictures/load.gif" id="post_comments_load_gif"/>
                                                                </td>
                                                            </tr>
                                                            <tr id="post_comments_row">
                                                                <td>
                                                                    
                                                                    <table>
                                                                        <tbody>
                                                                            <tr>
                                                                                <td>
                                                                                    <p class="text_color">Posts you've commented on.</p>
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td id="post_comments_total_comments_unit" style="border-top:1px solid gray;">
                                                                                    
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <div id="post_comments" class="scrollable_stats"></div>
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
                                    
                                    
                                    
                                </div>
                                <div id="other_stats">
                                    
                                    
                                    
                                    <table>
                                        <tbody>
                                            <tr>
                                                <td id="logins_unit">
                                                    
                                                    
                                                    <table>
                                                        <tbody>
                                                            <tr>
                                                                <td>
                                                                    <span class="title_color" onmouseover="name_over(this);" onmouseout="name_out(this);" onClick="show_other_menu(1);" style="cursor:pointer;">Logins</span>
                                                                    <img class="load_gif" src="http://pics.redlay.com/pictures/load.gif" id="logins_load_gif"/>
                                                                </td>
                                                            </tr>
                                                            <tr id="logins_row">
                                                                <td>
                                                                    
                                                                    <table>
                                                                        <tbody>
                                                                            <tr>
                                                                                <td>
                                                                                    <p class="text_color">Dates and IPs of your logins.</p>
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <div id="logins" class="scrollable_stats"></div>
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
                                            <tr>
                                                <td id="logouts_unit">
                                                    
                                                    <table>
                                                        <tbody>
                                                            <tr>
                                                                <td>
                                                                    <span class="title_color" onmouseover="name_over(this);" onmouseout="name_out(this);" onClick="show_other_menu(2);" style="cursor:pointer;">Logouts</span>
                                                                    <img class="load_gif" src="http://pics.redlay.com/pictures/load.gif" id="logouts_load_gif"/>
                                                                </td>
                                                            </tr>
                                                            <tr id="logouts_row">
                                                                <td>
                                                                    
                                                                    <table>
                                                                        <tbody>
                                                                            <tr>
                                                                                <td>
                                                                                    <p class="text_color">Dates and IPs of your logouts.</p>
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <div id="logouts" class="scrollable_stats"></div>
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
                                    
                                    
                                    
                                </div>
                                
                                
                                
                            </td>
                        </tr>
                    </tbody>
                </table>
                
            </div>
            <?php include('footer.php'); ?>
        </div>
    </body>
</html>
