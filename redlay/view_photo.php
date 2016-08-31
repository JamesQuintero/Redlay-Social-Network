<?php
@include('init.php');
include('universal_functions.php');
$allowed="all";
include('security_checks.php');

$ID=(int)($_GET['user_id']);
$picture_id=clean_string($_GET['picture_id']);
$type=clean_string($_GET['type']);

if(is_id($ID) && user_id_exists($ID))
{
    //redirects if account terminated
    if(user_id_terminated($ID))
    {
        header("Location: http://www.redlay.com/account_terminated.php");
        exit();
    }
    
    
    $picture_is_viewable=picture_is_viewable($ID, $picture_id, $type);

    if(!$picture_is_viewable||($type!='user'&&$type!='page'))
    {
        if($type=='user')
        {
            header("Location: http://www.redlay.com/profile.php?user_id=$ID");
            exit();
        }
        else if($type=='page')
        {
            header("Location: http://www.redlay.com/page.php?page_id=$ID");
            exit();
        }
    }
    else
    {
        if($type=='user')
            $index=get_picture_index($ID, $picture_id);
        else if($type=='page')
            $index=get_page_picture_index($ID, $picture_id);
    }

    if(isset($_SESSION['id'])&&$ID!=$_SESSION['id'])
    {
        //records photo views
        //record_photo_view($picture_id, $ID);
    }
}
else
{
    header("Location: http://www.redlay.com");
    exit();
}

?>
<!DOCTYPE html>
<html>
    <head>
        <title><?php if($type=='user') echo get_picture_description($ID, $index); else echo get_page_picture_description($ID, $index); ?></title>
        <?php include('required_header.php'); ?>
        <script type="text/javascript" >
            function change_color()
            {
                <?php
                    if($_SESSION['page_id']!=null)
                        $colors=get_page_display_colors($_SESSION['page_id']);
                    else if(isset($_SESSION['id']))
                        $colors=get_user_display_colors($_SESSION['id']);
                    else
                    {
                        $colors=array();
                        $colors[0]="rgb(220,20,0)";
                        $colors[1]="white";
                        $colors[2]="rgb(30,30,30)";
                    }
                    $color=$colors[0];
                    $box_background_color=$colors[1];
                    $text_color=$colors[2];
                ?>
                $('#picture_description_description').css('color', '<?php echo $text_color; ?>');
                $('.box').css('background-color', '<?php echo $box_background_color; ?>');
                $('.box').css('border', '5px solid <?php echo $color; ?>');
                $('#picture_description_name, #picture_like_text, #picture_dislike_text').css('color', '<?php echo $color; ?>');
                $('#delete_photo, .comment_name').css('color', '<?php echo $color; ?>');
                $('#slide_show').css('color', '<?php echo $color; ?>');
                $('.picture_comment').css('outline-color', '<?php echo $color; ?>');
                $('.comment_text_body, .timestamp_status_update, #company_footer').css('color', '<?php echo $text_color; ?>');
                $('.comment_delete').css({'background-color': '<?php echo $color; ?>', 'color': '<?php echo $text_color; ?>'});
                $('body').css('background-attachment', 'fixed');
                $('.comment_like, .comment_dislike, .alert_box_title').css('color', '<?php echo $color; ?>');
                $('.alert_box_text, #comment_body').css('color', '<?php echo $text_color; ?>');
                
                $('#picture_main').css('background-color', '<?php echo $box_background_color; ?>');
                    
                $('.title_color').css('color', '<?php echo $color; ?>');
                $('.text_color').css('color', '<?php echo $text_color; ?>');
            }
            
            $(window).ready(function()
            {
                if(<?php if(isset($_SESSION['id'])) echo "true"; else echo "false"; ?>==true&&<?php echo $ID; ?>!=<?php if(isset($_SESSION['id'])) echo $_SESSION['id']; else echo "0"; ?>)
                {
                    var content=$('#picture_main').html();
                    $('#options_unit').html("<table style='border-spacing:0px;'><tbody><tr><td><input class='button red_button' type='button' value='Copy' id='copy_picture_button' /></td><td><input class='button red_button' type='button' value='Make Profile Picture' id='make_profile_picture_button' /></td></tr></tbody></table>");
                    $('#copy_picture_button').attr('onClick', "display_copy_picture_menu('<?php echo $picture_id ?>', <?php echo $ID; ?>, '<?php echo $type; ?>');");
                    $('#copy_picture_button').attr({'onmouseover': "display_title(this, 'Copy photo to your account');", 'onmouseout': "hide_title(this);"});
                    
                    $('#make_profile_picture_button').attr('onClick', "make_profile_picture();");
                    $('#make_profile_picture_button').attr({'onmouseover': "display_title(this, 'Make this photo your profile picture');", 'onmouseout': "hide_title(this);"});
                }
                else if(<?php if(isset($_SESSION['id'])) echo "true"; else echo "false"; ?>==false)
                    $('#options_unit').html("");
            });
            function delete_photo()
            {
                if(<?php if(isset($_SESSION['id'])) echo $_SESSION['id']; else echo "0"; ?>==<?php echo $ID; ?>)
                {
                    $.post('delete_photo.php',
                    {
                        picture_id: '<?php echo $picture_id; ?>'
                    }, function(output)
                    {
                        window.location.replace("http://www.redlay.com/profile.php?user_id=<?php echo $_SESSION[id] ?>");
                    });
                }
            }
            
            function display_comments()
            {
                var timezone=get_timezone();
                $.post('view_photo_query.php',
                {
                    num:1,
                    type: '<?php echo $type; ?>',
                    poster_id: <?php echo $ID; ?>,
                    picture_id: '<?php echo $picture_id; ?>',
                    timezone:timezone
                }, function(output)
                {
                    var comments=output.comments;
                    var comment_ids=output.comment_ids;
                    var comment_names=output.comment_names;
                    var comments_user_sent=output.comments_user_sent;
                    var comment_timestamps=output.comment_timestamps;
                    var comment_timestamp_seconds=output.comment_timestamp_seconds;
                    var profile_pictures=output.profile_pictures;
                    
                    var has_liked_comment=output.has_liked_comment;
                    var has_disliked_comment=output.has_disliked_comment;
                    var num_comment_likes=output.num_comment_likes;
                    var num_comment_dislikes=output.num_comment_dislikes;
                    var badges=output.badges;

                    if(comments[0]!=undefined&&comments[0]!='')
                    {
                            <?php if(isset($_SESSION['page_id'])) echo "$('#picture_comments').html('');"; ?>

                            for(var x = 0; x < comments.length; x++)
                            {
                                comments[x]=convert_image(text_format(comments[x]), 'comment');
                                
                                var picture="<a href='http://www.redlay.com/profile.php?user_id="+comments_user_sent[x]+"'><img class='profile_picture profile_picture_comment' src='"+profile_pictures[x]+"'/></a>";
                                var name="<div style='display:inline-block'><a href='http://www.redlay.com/profile.php?user_id="+comments_user_sent[x]+"' class='link'><span class='user_name title_color' onmouseover=name_over(this); onmouseout=name_out(this); >"+comment_names[x]+"</span></a></div>";
                                var comment="<span id='picture_comment_"+x+"' class='comment_text_body text_color'>"+comments[x]+"</span>";
                                var timestamp="<span class='comment_timestamp text_color' id='photo_comment_timestamp_"+comment_ids[x]+"'>"+comment_timestamps[x]+"</span>";

                                if(comments_user_sent[x]==<?php if(isset($_SESSION['id']))echo $_SESSION['id']; else echo "0"; ?>)
                                    var close="<div class='comment_delete' id='comment_delete_0_0_"+x+"' >x</div>";
                                else
                                    var close="";

                                //displaying likes
                                if(comments_user_sent[x]!=<?php if(isset($_SESSION['id'])) echo $_SESSION['id']; else echo "0";?>)
                                {
                                    if(has_liked_comment[x]==true)
                                        var like="<div class='left_function' id='comment_like_body_0_0_"+x+"' ><span class='comment_like' id='comment_like_0_0_"+x+"' >Unlike ["+num_comment_likes[x]+"]</span></div>";
                                    else if(num_comment_likes[x]>=1)
                                        var like="<div class='left_function' id='comment_like_body_0_0_"+x+"' ><span class='comment_like' id='comment_like_0_0_"+x+"' >Like ["+num_comment_likes[x]+"]</span></div>";
                                    else
                                        var like="<div class='left_function' id='comment_like_body_0_0_"+x+"' ><span class='comment_like' id='comment_like_0_0_"+x+"' >Like</span></div>";
                                }
                                else
                                {
                                    if(num_comment_likes[x]==1)
                                        var like="<div class='left_function_disabled' ><span class='comment_like' >1 like</span></div>";
                                    else if(num_comment_likes[x]>1)
                                        var like="<div class='left_function_disabled' ><span class='comment_like' >"+num_comment_likes[x]+" likes</span></div>";
                                    else
                                        var like="";
                                }

                                //displaying dislikes
                                if(comments_user_sent[x]!=<?php if(isset($_SESSION['id'])) echo $_SESSION['id']; else echo "0"; ?>)
                                {
                                    if(like=='')
                                        var function_class='single_function';
                                    else
                                        var function_class='right_function';
                                    
                                    if(has_disliked_comment[x]==true)
                                        var dislike="<div class='"+function_class+"' id='comment_dislike_body_0_0_"+x+"' ><span class='comment_dislike' id='comment_dislike_0_0_"+x+"' >Undislike ["+num_comment_dislikes[x]+"]</span></div>";
                                    else if(num_comment_dislikes>=1)
                                        var dislike="<div class='"+function_class+"' id='comment_dislike_body_0_0_"+x+"' ><span class='comment_dislike' id='comment_dislike_0_0_"+x+"' >Dislike ["+num_comment_dislikes[x]+"]</span></div>";
                                    else
                                        var dislike="<div class='"+function_class+"' id='comment_dislike_body_0_0_"+x+"' ><span class='comment_dislike' id='comment_dislike_0_0_"+x+"' >Dislike</span></div>";
                                }
                                else
                                {
                                    if(like=='')
                                        var function_class='single_function_disabled';
                                    else
                                        var function_class='right_function_disabled';
                                    
                                    if(num_comment_dislikes[x]==1)
                                        var dislike="<div class='"+function_class+"' ><span class='comment_dislike' >1 dislike</span></div>";
                                    else if(num_comment_dislikes[x]>1)
                                        var dislike="<div class='"+function_class+"' ><span class='comment_dislike' >"+num_comment_dislikes[x]+" dislikes</span></div>";
                                    else
                                        var dislike="";
                                }

                                var functions=get_comment_functions(like, dislike, timestamp);


                                var body=get_post_format(picture, name, comment, functions, '', close, 'comment_delete_0_0_'+x, "comment_body_0_0_"+x, badges[x]);
                                $('#comment_body_0_0').html(body+$('#comment_body_0_0').html());
                               
                                $('#comment_delete_0_0_'+x).attr('onClick', "delete_photo_comment(<?php echo $ID; ?>, '<?php echo $picture_id; ?>', 0, "+comment_ids[x]+", "+x+", 0, '<?php echo $type; ?>');");
    //                                $('#comment_body').html(picture+close+name+comment+functions+timestamp+comment_break+$('#comment_body').html());


                                if(has_liked_comment[x]==true)
                                    $("#comment_like_body_0_0_"+x).attr({'onClick': "unlike_photo_comment(<?php echo $ID; ?>, '<?php echo $picture_id; ?>', 0, "+comment_ids[x]+", "+x+", 0, "+num_comment_likes[x]+");"});
                                else
                                    $("#comment_like_body_0_0_"+x).attr({'onClick': "like_photo_comment(<?php echo $ID; ?>, '<?php echo $picture_id; ?>', 0, "+comment_ids[x]+", "+x+", 0, "+num_comment_likes[x]+");"});

                                if(has_disliked_comment[x]==true)
                                    $("#comment_dislike_body_0_0_"+x).attr({'onClick': "undislike_photo_comment(<?php echo $ID; ?>, '<?php echo $picture_id; ?>', 0, "+comment_ids[x]+", "+x+", 0, "+num_comment_dislikes[x]+")"});
                                else
                                    $("#comment_dislike_body_0_0_"+x).attr({'onClick': "dislike_photo_comment(<?php echo $ID; ?>, '<?php echo $picture_id; ?>', 0, "+comment_ids[x]+", "+x+", 0, "+num_comment_dislikes[x]+")"});
                            }
                            
                            for(var x = 0; x < comments.length; x++)
                                count_time(comment_timestamp_seconds[x], '#photo_comment_timestamp_'+comment_ids[x]);
                            
                        
                    }
                    else
                        $('#comment_body_0_0').html("<span class='text_color'>There are no comments here! You should post one!</span>");
                        
                    $('#comment_body_0_0').css({'margin-left': '0px', 'margin-right': '0px'});
                        
                    $('.comment_delete').hide();
                    
                    $('.post_functions_table').css('width', 'auto');

                    change_color();
                }, "json");
            }
            $(window).ready(function()
            {
                //hides arrows if no previos or next photo

                <?php
                if($type=="user")
                {
                    $next_photo=get_next_photo($ID, $picture_id);
                    $previous_photo=get_previous_photo($ID, $picture_id);
                }
                else if($type=="page")
                {
                    $next_photo=get_next_page_photo($ID, $picture_id);
                    $previous_photo=get_previous_page_photo($ID, $picture_id);
                }
                ?>
            });
            
            function arrow_over(num)
            {
                if(num==1)
                {
                    $('#left_arrow').css('background-color', 'whitesmoke').stop().animate({
                        opacity:1
                    }, 100, function()
                    {});
                }
                else if(num==2)
                {
                    $('#left_arrow').css('background-color', 'white').stop().animate({
                        opacity:.5
                    }, 100, function()
                    {});
                }
                else if(num==3)
                {
                    $('#right_arrow').css('background-color', 'whitesmoke').stop().animate({
                        opacity:1
                    }, 100, function()
                    {});
                }
                else
                {
                    $('#right_arrow').css('background-color', 'white').stop().animate({
                        opacity:.5
                    }, 100, function()
                    {});
                }
            }
            
            function arrow_down(num)
            {
                if(num==1)
                {
                    $('#left_arrow').css('box-shadow','inset 0px 0px 3px gray');
                }
                else if(num==2)
                {
                    $('#left_arrow').css('box-shadow','1px 1px 1px gray');
                }
                else if(num==3)
                {
                    $('#right_arrow').css('box-shadow','inset 0px 0px 3px gray');
                }
                else
                {
                    $('#right_arrow').css('box-shadow','1px 1px 1px gray');
                }
            }


            function change_audience_options()
            {
                //gets the checked checkboxes and their values
                var audience_options_list=new Array();
                var num=0;
                var num2=0;
                while($('#photo_groups_box_checkbox_'+num2).length)
                {
                    if($('#photo_groups_box_checkbox_'+num2).data('checked')=='yes')
                    {
                        audience_options_list[num]=$('#photo_groups_box_checkbox_'+num2).data('group_name');
                        num++;
                    }
                    num2++;
                }

                $.post('view_photo_query.php',
                {
                    num:2,
                    picture_id: '<?php echo $picture_id; ?>',
                    groups: audience_options_list
                }, function(output)
                {
                    if(output=="Audience changed")
                        $('#errors').html(output).attr('class', 'good_errors').show();
                    else
                        $('#errors').html(output).attr('class', 'bad_errors').show();
                });
            }
            function display_options_menu()
            {
                var group_selection="<tr><td><div class='select_box' id='photo_groups_box'></div></td><td style='text-align:right;'><input type='button' class='button red_button' value='Change' onClick='change_audience_options();'/></td></tr>";
                var delete_photo="<tr><td><p class='alert_box_text text_color' style='margin:0px;font-size:14px;'>Permanently delete photo</p></td><td style='text-align:right'><input type='button' class='button red_button' onclick='delete_photo();' value='Delete' /></td></tr>";
                var make_profile_picture="<tr><td><span class='text_color' style='font-size:14px;'>Make this your profile picture</span></td><td style='text-align:right;'><input class='button red_button' type='button' value='Change' onClick='make_profile_picture();' /></td></tr>";
                
                var table_body="<table style='width:100%;'><tbody>"+group_selection+delete_photo+make_profile_picture+"</tbody></table>";
                
                display_alert("Options", table_body, 'options_extra_unit', 'message_gif', "");
                display_current_photo_groups('photo_groups_box', '<?php echo $picture_id; ?>');


                $('#message_gif').hide();
                change_color();
            }
            function make_profile_picture()
            {
                $.post('change_profile_picture_existing.php',
                {
                    photo_id: '<?php echo $picture_id; ?>',
                    user_id: <?php echo $ID; ?>
                }, function(output)
                {
                    if(output=="Profile picture changed")
                        display_error(output, 'good_errors');
                    else
                        display_error(output, 'bad_errors');
                });
            }
            
            function display_report_menu()
            {
                var body="";
                var confirm="<input class='button red_button' type='button' value='Report' id='report_photo_button'/>";
                display_alert("Report", body, 'report_extra_unit', 'report_gif', confirm);
                
                $('#report_photo_button').attr('onClick', "report_photo('<?php echo $picture_id; ?>', <?php echo $ID; ?>);");


                $('#report_gif').hide();
                change_color();
            }
            function display_photo_timestamp()
            {
                var timezone=get_timezone();
                $.post('view_photo_query.php',
                {
                    num:3,
                    type: '<?php echo $type; ?>',
                    timezone: timezone,
                    index: <?php echo $index; ?>,
                    user_id: <?php echo $ID; ?>
                }, function(output)
                {
                    var timestamp=output.timestamp;
                    var timestamp_seconds=output.timestamp_seconds;
                    
                    $('#picture_timestamp').html(timestamp);
                    count_time(timestamp_seconds, "#picture_timestamp");
                }, "json");
            }
            function display_photo_information()
            {
                $.post('view_photo_query.php',
                {
                    num: 4,
                    type: '<?php echo $type; ?>',
                    user_id: <?php echo $ID; ?>,
                    picture_id: '<?php echo $picture_id; ?>'
                }, function(output)
                {
                    var has_liked=output.has_liked;
                    var has_disliked=output.has_disliked
                    var num_likes=output.num_likes;
                    var num_dislikes=output.num_dislikes;
                    var badges=output.badges;
                    var description=text_format(output.picture_description);
                    
                    
                    
                    if(<?php if((!isset($_SESSION['id'])||($type=='user'&&$_SESSION['id']==$ID))||$type=='page'&&$_SESSION['page_id']==$ID) echo "true"; else echo "false"; ?>)
                    {
                        if(num_likes==1)
                            var like="<div class='left_function_disabled'><span class='comment_like' >1 like</span></div>";
                        else if(num_likes>1)
                            var like="<div class='left_function_disabled'><span class='comment_like' >"+num_likes+" likes</span></div>";
                        else
                            var like="";
                        
                        if(like=="")
                            var function_class='single_function_disabled';
                        else
                            var function_class='right_function_disabled';
                        
                        if(num_dislikes==1)
                            var dislike="<div class='"+function_class+"'><span class='comment_like' >1 dislike</span></div>";
                        else if(num_dislikes>1)
                            var dislike="<div class='"+function_class+"'><span class='comment_like' >"+num_dislikes+" dislikes</span></div>";
                        else
                            var dislike="";
                    }
                    else
                    {
                        if(has_liked)
                            var like="<div class='left_function' id='home_photo_like_body_0_0' ><span class='comment_like' id='home_photo_like_0_0' >Unlike ["+num_likes+"]</span></div>";
                        else if(num_likes>=1)
                            var like="<div class='left_function' id='home_photo_like_body_0_0' ><span class='comment_like' id='home_photo_like_0_0' >Like ["+num_likes+"]</span></div>";
                        else
                            var like="<div class='left_function' id='home_photo_like_body_0_0' ><span class='comment_like' id='home_photo_like_0_0' >Like</span></div>";

                        if(like=="")
                            var function_class='single_function';
                        else
                            var function_class='right_function';

                        if(has_disliked)
                            var dislike="<div class='"+function_class+"' id='home_photo_dislike_body_0_0' ><span class='comment_dislike' id='home_photo_dislike_0_0' >Undislike ["+num_dislikes+"]</span></div>";
                        else if(num_dislikes>=1)
                            var dislike="<div class='"+function_class+"' id='home_photo_dislike_body_0_0' ><span class='comment_like' id='home_photo_dislike_0_0' >Dislike ["+num_likes+"]</span></div>";
                        else
                            var dislike="<div class='"+function_class+"' id='home_photo_dislike_body_0_0' ><span class='comment_like' id='home_photo_dislike_0_0' >Dislike</span></div>";
                    }
                    
                    
                    var functions=get_post_functions(like, dislike, '', '');
                    
                    var profile_picture="<a class='link' href='http://www.redlay.com/profile.php?user_id=<?php echo $ID; ?>'><img class='profile_picture profile_picture_status' src='<?php echo get_profile_picture($ID); ?>'/></a>";
                    var name="<div class='user_name_body'><a class='link' href='http://www.redlay.com/profile.php?user_id=<?php echo $ID; ?>'><span class='user_name title_color' onmouseover='name_over(this);' onmouseout='name_out(this);'><?php echo get_user_name($ID); ?></span></a></div>";
                    var description="<p class='text_color' style='margin:0px;'>"+description+"</p>";

                    var body=get_post_format(profile_picture, name, description, functions, '', '', '', '', badges);
                    $('#picture_information').html(body);
                    
                    if(<?php 
                    if(isset($_SESSION['id']))
                    {
                        if($type=='user'&&$_SESSION['id']==$ID)
                            echo "true";
                        else
                            echo "false";
                    }
                    else if(isset($_SESSION['page_id']))
                    {
                        if($type=='page'&&$_SESSION['page_id']==$ID)
                            echo "true";
                        else
                            echo "false";
                    }
                    else
                        echo "false";
                    ?>==false)
                    {
                        if(has_liked)
                            $('#home_photo_like_body_0_0').attr('onClick', "unlike_photo('<?php echo $picture_id; ?>', <?php echo $ID; ?>, '<?php echo $type; ?>', 0, 0, "+num_likes+")");
                        else
                            $('#home_photo_like_body_0_0').attr('onClick', "like_photo('<?php echo $picture_id; ?>', <?php echo $ID; ?>, '<?php echo $type; ?>', 0, 0, "+num_likes+")");

                        if(has_disliked)
                            $('#home_photo_dislike_body_0_0').attr('onClick', "undislike_photo('<?php echo $picture_id; ?>', <?php echo $ID; ?>, '<?php echo $type; ?>', 0, 0, "+num_dislikes+")");
                        else
                            $('#home_photo_dislike_body_0_0').attr('onClick', "dislike_photo('<?php echo $picture_id; ?>', <?php echo $ID; ?>, '<?php echo $type; ?>', 0, 0, "+num_dislikes+")");
                    }
                    
                }, "json");
                
                
            }
            function display_related_photos()
            {
                var timezone=get_timezone();
                $.post('view_photo_query.php',
                {
                    num:5,
                    user_id: <?php echo $ID; ?>,
                    timezone: timezone
                }, function(output)
                {
                    var pictures=output.pictures;
                    var picture_ids=output.picture_ids;
                    var profile_picture=output.profile_picture;
                    var name=output.name;
                    var num_likes=output.num_likes;
                    var num_dislikes=output.num_dislikes;
                    var timestamps=output.timestamps;
                    
                    var html="";
                    for(var x = 0; x < pictures.length; x++)
                    {
                        if(pictures[x]!=null)
                        {
                            var body="<a href='http://www.redlay.com/view_photo.php?user_id=<?php echo $ID; ?>&&picture_id="+picture_ids[x]+"&&type=user' class='link' ><img class='picture_post' src='"+pictures[x]+"' style='width:150px;'/></a>";
                            var profile_pic="<a class='link'><img class='profile_picture profile_picture_comment' src='"+profile_picture+"'/></a>";
                            var user_name="<div class='user_name_body' ><a class='link' href='http://www.redlay.com/profile.php?user_id=<?php echo $ID; ?>'><span class='user_name title_color' >"+name+"</span></a></div>";
                            var timestamp="<span class='text_color' style='font-size:14px;'>"+timestamps[x]+"</span>";
                            
                            var num_adds='';
                            var badge_body="";
                            var description="";
                            var buttons="";
                            
                            html+="<tr><td><table><tbody><tr><td> "+body+" </td></tr><tr><td> "+timestamp+" </td></tr></tbody></table></td></tr>";
                            
                        }
                    }
                    $('#related_photos').html("<p class='text_color' >More Photos from <?php echo get_user_name($ID); ?></p><table style='margin: 0 auto;'><tbody>"+html+"</tbody></table>");
                }, "json");
            }
        </script>
        
        <script type="text/javascript">
            $(window).ready(function()
            {
                display_photo_timestamp();
                display_photo_information();
                initialize_comment_events();
                display_comments();
                display_related_photos();
                change_color();
                $('#menu').hide();
                $('#footer').css('width', '910px');
                <?php include('required_jquery.php'); ?>
            });
        </script>
        <script type="text/javascript">
            <?php include('required_google_analytics.php'); ?>
        </script>
    </head>
    <body>
        <?php include('required_html.php'); ?>
        
                <?php 
                    if(isset($_SESSION['page_id']))
                    {
                        echo "<div id='top'>";
                        include('top_page.php'); 
                        echo "</div>";
                    }
                    else if(isset($_SESSION['id']))
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
            <div id="picture_main" class="content">
                <?php
                
                if($type=='user')
                {
                    $query=mysql_query("SELECT timestamp FROM pictures WHERE user_id=$ID LIMIT 1");
                    if($query&&mysql_num_rows($query)==1)
                    {
                        $array=mysql_fetch_row($query);
                        $timestamp=explode('|^|*|', $array[0]);
                    }

                    if(file_exists_server("http://u.redlay.com/users/$ID/photos/$picture_id.jpg"))
                    {
                        $path="http://u.redlay.com/users/$ID/photos/$picture_id.jpg";
                        $file_type='jpg';
                    }
                    else if(file_exists_server("http://u.redlay.com/users/$ID/photos/$picture_id.png"))
                    {
                        $path="http://u.redlay.com/users/$ID/photos/$picture_id.png";
                        $file_type='png';
                    }
                    else if(file_exists_server("http://u.redlay.com/users/$ID/photos/$picture_id.gif"))
                    {
                        $path="http://u.redlay.com/users/$ID/photos/$picture_id.gif";
                        $file_type='gif';
                    }
                }
                else if($type=='page')
                {
                    $query=mysql_query("SELECT timestamp FROM page_pictures WHERE page_id=$ID LIMIT 1");
                    if($query&&mysql_num_rows($query)==1)
                    {
                        $array=mysql_fetch_row($query);
                        $timestamp=explode('|^|*|', $array['timestamp']);
                    }

                    if(file_exists_server("http://p.redlay.com/pages/$ID/$picture_id.jpg"))
                    {
                        $path="http://p.redlay.com/pages/$ID/$picture_id.jpg";
                        $file_type='jpg';
                    }
                    else if(file_exists_server("http://p.redlay.com/pages/$ID/$picture_id.png"))
                    {
                        $path="http://p.redlay.com/pages/$ID/$picture_id.png";
                        $file_type='png';
                    }
                    else if(file_exists_server("http://p.redlay.com/pages/$ID/$picture_id.gif"))
                    {
                        $path="http://p.redlay.com/pages/$ID/$picture_id.gif";
                        $file_type='gif';
                    }
                }

                ?>
                
                <table style="width:100%;padding:20px;">
                    <tbody>
                        <tr class="picture_body">
                            <td>
                                <table id="left_arrow" onmouseover="arrow_over(1)" onmouseout="arrow_over(2);" onmousedown="arrow_down(1);" onmouseup="arrow_down(2);" onClick="window.location.replace('http://www.redlay.com/view_photo.php?user_id=<?php echo $ID; ?>&&picture_id=<?php if($type=='user') echo get_previous_photo($ID, $picture_id); else echo get_previous_page_photo($ID, $picture_id); ?>&&type=<?php echo $type; ?>');" >
                                    <tbody>
                                        <tr>
                                            <td>
                                                <p id="left_arrow_text"><</p>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                            <td>
                                <?php list($width, $height)=getimagesize('http://u.redlay.com/users/'.$ID.'/photos/'.$picture_id.'.'.$file_type); if($width>800||$height>800) echo "<a href='http://u.redlay.com/users/$ID/photos/$picture_id.$file_type'>"; ?><img id="picture" src="<?php echo $path; ?>" /><?php if($width>800||$height>800) echo "</a>"; ?>
                            </td>
                            <td>
                                <table id="right_arrow" onmouseover="arrow_over(3)" onmouseout="arrow_over(4);" onmousedown="arrow_down(3);" onmouseup="arrow_down(4);" onClick="window.location.replace('http://www.redlay.com/view_photo.php?user_id=<?php echo $ID; ?>&&picture_id=<?php if($type=='user') echo get_next_photo($ID, $picture_id); else echo get_next_page_photo($ID, $picture_id); ?>&&type=<?php echo $type; ?>');" >
                                    <tbody>
                                        <tr>
                                            <td>
                                                <p id="right_arrow_text">></p>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3">
                                    <table>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <div id="picture_information">


                                                    </div>
                                                </td>
                                                <td style="vertical-align:top">
                                                    <table id="photo_information_table">
                                                        <tbody>
                                                            <tr class="view_photo_row">
                                                                <td><p class="timestamp_status_update" id="picture_timestamp"></p></td>
                                                            </tr>
                                                            <tr class="view_photo_row">
                                                                <td id="options_unit"><p id='delete_photo' onClick='display_options_menu();' onmouseover="{name_over(this);}" onmouseout="name_out(this);">Options</p></td>
                                                            </tr>
                                <!--                            <tr class="view_photo_row">
                                                                <td><a class="link" href="http://www.redlay.com/picture_slide_show.php?user_id=<?php echo $ID; ?>&&photo_id=<?php echo $picture_id; ?>&&type=<?php echo $type; ?>"><p id="slide_show" onmouseover="name_over(this);" onmouseout="name_out(this);">Slide show></p></a></td>
                                                            </tr>-->
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3">
                                <table>
                                    <tbody>
                                        <tr>
                                            <td style="width:675px;margin:0 auto;border-right:1px solid gray;vertical-align:top;">
                                                <div id="comment">
                                                    <div id="picture_comments">
                                                        <textarea class='picture_comment comment_textarea input_box' onFocus="input_in(this);" onBlur="input_out(this);" id="comment_picture" placeholder='Comment...' maxlength='500' ></textarea>
                                <!--                        <input type='button' class='button picture_comment_submit red_button' value='Post' onClick='picture_comment();'/>-->
                                                    </div>
                                                    <div id="comment_body_0_0" class="comment_body">

                                                    </div>
                                                </div>
                                            </td>
                                            <td style="vertical-align:top;">
                                                <div id="related_photos" style="text-align:center;">

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
            <script type="text/javascript">
                function initialize_comment_events()
                {
                    $('.comment_textarea').unbind('keypress').unbind('keydown').unbind('keyup');
                    $('.comment_textarea').keyup(function(e)
                    {
                        var key = (e.keyCode ? e.keyCode : e.which);
                        if(key == '13')
                        {
                            comment_photo('<?php echo $picture_id; ?>', <?php echo $ID; ?>, 0, 0, 0, 'comment_picture');
                            $(this).val('');
                        }
                    });
                }
            </script>
            <?php include('footer.php'); ?>
        </div>
    </body>
</html>