<?php
@include('init.php');
include('universal_functions.php');
$allowed="users";
include('security_checks.php');

$video_id=(int)($_GET['video_id']);
$user_id=(int)($_GET['user_id']);

if(!is_id($user_id)||!user_id_exists($user_id))
{
    header("Location: http://www.redlay.com");
    exit();
}
else if(user_id_terminated($user_id))
{
    header("Location: http://www.redlay.com/account_terminated.php");
    exit();
}
else
{
    //checks if video exists
    $query=mysql_query("SELECT video_ids FROM content WHERE user_id=$user_id LIMIT 1");
    if($query&&mysql_num_rows($query)==1)
    {
        $array=mysql_fetch_row($query);
        $video_ids=explode('|^|*|', $array[0]);
        
        $index=array_search($video_id, $video_ids);
        if($index==false)
        {
            header("Location: http://www.redlay.com");
            exit();
        }
    }


    //gets the user's privacy preferences
    $privacy=get_user_privacy_settings($user_id);
    $general=$privacy[0];
    $non_adds=$privacy[1];
    if($non_adds[5]=='no'&&$user_id!=$_SESSION['id']&&user_is_friends($user_id, $_SESSION['id'])=='false')
        header("Location: http://www.redlay.com");
}


?>
<!DOCTYPE html>
<html>
    <head>
        <title>Video</title>
        <?php include('required_header.php'); ?>
        <script type="text/javascript">
            function change_color()
            {
                <?php
                    $colors=get_user_display_colors($user_id);
                    $color=$colors[0];
                    $box_background_color=$colors[1];
                    $text_color=$colors[2];
                ?>
                        
                $('.box').css('border', '5px solid <?php echo $color; ?>');
                $('.box').css('background-color', '<?php echo $box_background_color; ?>');
                
                
                $('.text_color').css('color', "<?php echo $text_color; ?>");
                $('.title_color').css('color', "<?php echo $color; ?>");
                
                $('.status_update').css('border', 'none');
            }
            
            function display_video()
            {
                var timezone=get_timezone();
                $.post('view_video_query.php',
                {
                    num:1,
                    user_id: <?php echo $user_id; ?>,
                    video_id: <?php echo $video_id ?>,
                    timezone:timezone
                }, function(output)
                {
                    console.log("Got here");
                    //video information
                    var video=output.video;
                    var video_preview=output.video_preview;
                    var video_url=output.video_url;
                    var num_likes=output.num_likes;
                    var num_dislikes=output.num_dislikes;
                    var has_liked=output.has_liked;
                    var has_disliked=output.has_disliked;
                    var timestamp=output.timestamp;
                    var timestamp_seconds=output.timestamp_seconds;
                    var audience_groups=output.audience_groups;
                    var user_name=output.user_name;
                    var profile_picture=output.profile_picture;
                    var badges=output.badges;
                    
                    
                    //comment information
                    var num_comments=output.num_comments;
                    var comments=output.comments;
                    var comments_users_sent=output.comments_users_sent;
                    var comment_timestamps=output.comment_timestamps;
                    var comment_timestamp_seconds=output.comment_timestamp_seconds;
                    var comment_names=output.comment_names;
                    var num_comment_likes=output.num_comment_likes;
                    var num_comment_dislikes=output.num_comment_dislikes;
                    var has_liked_comment=output.has_liked_comment;
                    var has_disliked_comment=output.has_disliked_comment;
                    var comment_ids=output.comment_ids;
                    var comment_badges=output.comment_badges;
                    var comment_profile_pictures=output.comment_profile_pictures;
                    
                    
                    if(<?php echo $user_id; ?>!=<?php echo $_SESSION['id']; ?>)
                        var video_share="<input class='button red_button' value='Copy' type='button' style='position:relative;left:325px;' onClick='share_video(<?php echo $video_id; ?>, <?php echo $user_id; ?>)' />";
                    else
                        var video_share="";
                    
                    if(video_preview)
                        var body="<table class='added_table added_video_table' ><tbody><tr><td id='video_body_0_0'>  <img class='video_preview' id='video_preview_0_0' src='"+video_preview+"' /> <img class='video_play_button' id='video_play_button_0_0' src='http://pics.redlay.com/pictures/play_button.png' /> </td></tr><tr><td class='home_other_text_unit' id='video_share'>"+video_share+"</td></tr></tbody></table>";
                    else
                        var body="<table class='added_table added_video_table' ><tbody><tr><td class='home_other_text_unit' id='video_share'>"+video_share+"</td></tr><tr><td id='video_body'>  "+video_url+"  </td></tr></tbody></table>";
                    
                    var picture="<a href='http://www.redlay.com/profile.php?user_id=<?php echo $user_id; ?>'><img class='profile_picture_status profile_picture' src='"+profile_picture+"'></a>";
                    var name="<a class='user_name_link' href='http://www.redlay.com/profile.php?user_id=<?php echo $user_id; ?>' ><span class='user_name title_color' onmouseover=name_over(this); onmouseout=name_out(this);>"+user_name+"</span></a>";

                    
                    if(<?php if($general[2]=="yes"||$user_id==$_SESSION['id']||user_is_friends($user_id, $_SESSION['id'])=='true') echo "true"; else echo "false"; ?>==true)
                        var comment_input="<div id='comment_text' class='comment_input_body'><textarea class='comment_textarea input_box' onFocus='input_in(this);' onBlur='input_out(this);' id='comment_input_0_0' placeholder='Comment...' maxlength='500'></textarea></div>";
                    else
                        var comment_input="";
                    
                    var comment_body="<div class='comment_body' id='comment_body_0_0'></div>";
                    
                    if(<?php if(isset($_SESSION['id'])) echo $_SESSION['id']; else echo '0'; ?>==<?php echo $user_id; ?>)
                        var options="<div class='post_delete post_hide' id='post_options_<?php echo $video_id; ?>' onClick='show_post_options(<?php echo $video_id; ?>, <?php  echo $user_id; ?>);'>O</div>";
                    else
                        var options="";

                    //display likes
                    if(<?php echo $user_id; ?>!=<?php echo $_SESSION['id']; ?>)
                    {
                        if(has_liked==true)
                            var like_text="<span class='status_update_like title_color' id='video_like_0_0' onmouseover=name_over(this); onClick='unlike_video(<?php echo $user_id; ?>, <?php echo $video_id; ?>, "+num_likes+", 0, 0);' onmouseout=name_out(this); >Unlike ["+num_likes+"]</span>";
                        else if(num_likes>=1)
                            var like_text="<span class='status_update_like title_color' id='video_like_0_0' onmouseover=name_over(this); onClick='like_video(<?php echo $user_id; ?>, <?php echo $video_id; ?>, "+num_likes+", 0, 0);' onmouseout=name_out(this); >Like ["+num_likes+"]</span>";
                        else
                            var like_text="<span class='status_update_like title_color' id='video_like_0_0' onmouseover=name_over(this); onClick='like_video(<?php echo $user_id; ?>, <?php echo $video_id; ?>, "+num_likes+", 0, 0);' onmouseout=name_out(this); >Like</span>";
                    }
                    else
                    {
                        if(num_likes==1)
                            var like_text="<span class='status_update_like title_color' style='cursor:default;'>1 like</span>";
                        else if(num_likes>1)
                            var like_text="<span class='status_update_like title_color' style='cursor:default;'>"+num_likes[x]+" likes</span>";
                        else
                            var like_text="";
                    }

                    //display dislikes
                    if(<?php echo $user_id; ?>!=<?php echo $_SESSION['id']; ?>)
                    {
                        if(has_disliked==true)
                            var dislike_text="<span class='status_update_dislike title_color' id='video_dislike_0_0' onmouseover=name_over(this); onClick='undislike_video(<?php echo $user_id; ?>, <?php echo $video_id; ?>, "+num_dislikes+", 0, 0);' onmouseout=name_out(this); >Undislike ["+num_dislikes+"]</span>";
                        else if(num_dislikes>=1)
                            var dislike_text="<span class='status_update_dislike title_color' id='video_dislike_0_0' onmouseover=name_over(this); onClick='dislike_video(<?php echo $user_id; ?>, <?php echo $video_id; ?>, "+num_dislikes+", 0, 0);' onmouseout=name_out(this); >Dislike ["+num_dislikes+"]</span>";
                        else
                            var dislike_text="<span class='status_update_dislike title_color' id='video_dislike_0_0' onmouseover=name_over(this); onClick='dislike_video(<?php echo $user_id; ?>, <?php echo $video_id; ?>, "+num_dislikes+", 0, 0);' onmouseout=name_out(this); >Dislike</span>";
                    }
                    else
                    {
                        if(num_dislikes==1)
                            var dislike_text="<span class='status_update_like title_color' style='cursor:default;'>1 dislike</span>";
                        else if(num_dislikes>1)
                            var dislike_text="<span class='status_update_like title_color' style='cursor:default;'>"+num_dislikes[x]+" dislikes</span>";
                        else
                            var dislike_text="";
                    }

                    if(num_comments==0)
                        var comment_text="<span id='comment_title' class='comment_text title_color' onmouseover=name_over(this); onmouseout=name_out(this); onClick='toggle_comment_displays();'>Comment</span>";
                    else
                        var comment_text="<span id='comment_title' class='comment_text title_color' onmouseover=name_over(this); onmouseout=name_out(this); onClick='toggle_comment_displays();'>Comment ["+num_comments+"]</span>";


                    var timestamp="<span class='timestamp_status_update' id='post_timestamp'>"+timestamp+"</span>";
                    var post_functions=get_post_functions(like_text, dislike_text, comment_text, timestamp);



                    var body=get_post_format(picture, name, body,post_functions, comment_input+comment_body, options, 'post_options_<?php echo $video_id; ?>', 'post_<?php echo $video_id; ?>', badges)
                    count_time(timestamp_seconds, '#post_timestamp');

                    $('#post').html(body);
                    $('#comment_text').hide();
                    $('#comment_body_0_0').hide();
                    
                    
                    $('#video_body_0_0').data('vid_embed', video_url);
                    
                    $('#video_body_0_0').attr('onClick', "display_actual_video('#video_body_0_0');");
                    $('#video_preview_0_0').attr({'onmouseover': "video_over('#video_preview_0_0', '#video_play_button_0_0');",  'onmouseout': "video_out('#video_preview_0_0', '#video_play_button_0_0');"});
                    $('#video_play_button_0_0').attr('onmouseover', "video_over('#video_preview_0_0', '#video_play_button_0_0');");









                    /////////posts commenmts////////////////

                    $('#comment_body_0_0').html('');
                    var body='';
                    for(var x = 0; x < comments.length; x++)
                    {
                        comments[x]=convert_image(text_format(comments[x]), 'comment');
                        if(comments[x]!='')
                        {
                            var name="<a class='user_name_link' href='http://www.redlay.com/profile.php?user_id="+comments_users_sent[x]+"'><span class='comment_name title_color' id='comment_name_"+x+"' onmouseover=name_over(this); onmouseout=name_out(this); >"+comment_names[x]+"</span></a>";
                            var picture="<a href='http://www.redlay.com/profile.php?user_id="+comments_users_sent[x]+"'><img class='comment_profile_picture profile_picture' id='picture_comment_"+x+"' src='"+comment_profile_pictures[x]+"' ></a>";
                            var comment="<span class='comment_text_body'>"+comments[x]+"</span>";

                            if(comments_users_sent[x]==<?php echo $_SESSION['id']; ?>)
                                var options="<div class='comment_delete' id='comment_delete_"+x+"' onClick='delete_video_comment(<?php echo $user_id; ?>, <?php echo $video_id; ?>, "+comment_ids[x]+", 0, 0, "+x+");' >x</div>";
                            else
                                var options="";


                            //displaying likes
                            if(comments_users_sent[x]!=<?php echo $_SESSION['id']; ?>)
                            {
                                if(has_liked_comment[x]==true)
                                    var like="<span class='comment_like title_color' id='comment_like_0_0_"+x+"' onmouseover=name_over(this); onmouseout=name_out(this); >Unlike ["+num_comment_likes[x]+"]</span>";
                                else if(num_comment_likes[x]>=1)
                                    var like="<span class='comment_like title_color' id='comment_like_0_0_"+x+"' onmouseover=name_over(this); onmouseout=name_out(this); >Like ["+num_comment_likes[x]+"]</span>";
                                else
                                    var like="<span class='comment_like title_color' id='comment_like_0_0_"+x+"' onmouseover=name_over(this); onmouseout=name_out(this); >Like</span>";
                            }
                            else
                            {
                                if(num_comment_likes[x]==1)
                                    var like="<span class='comment_like title_color' >1 like</span>";
                                else if(num_comment_likes[x]>1)
                                    var like="<span class='comment_like title_color' >"+num_comment_likes[x]+" likes</span>";
                                else
                                    var like="";
                            }

                            //displaying dislikes
                            if(comments_users_sent[x]!=<?php echo $_SESSION['id']; ?>)
                            {
                                if(has_disliked_comment[x]==true)
                                    var dislike="<span class='comment_dislike title_color' id='comment_dislike_0_0_"+x+"' onmouseover=name_over(this); onmouseout=name_out(this); >Undislike ["+num_comment_dislikes[x]+"]</span>";
                                else if(num_comment_dislikes[x]>=1)
                                    var dislike="<span class='comment_dislike title_color' id='comment_dislike_0_0_"+x+"' onmouseover=name_over(this); onmouseout=name_out(this); >Dislike ["+num_comment_dislikes[x]+"]</span>";
                                else
                                    var dislike="<span class='comment_dislike title_color' id='comment_dislike_0_0_"+x+"' onmouseover=name_over(this); onmouseout=name_out(this); >Dislike</span>";
                            }
                            else
                            {
                                if(num_comment_dislikes[x]==1)
                                    var dislike="<span class='comment_dislike title_color' >1 dislike</span>";
                                else if(num_comment_dislikes[x]>1)
                                    var dislike="<span class='comment_dislike title_color' >"+num_comment_dislikes[x]+" dislikes</span>";
                                else
                                    var dislike="";
                            }
                            var timestamp="<span class='comment_timestamp' id='post_comment_timestamp_"+comment_ids[x]+"'>"+comment_timestamps[x]+"</span>";
                              var functions=get_comment_functions(like, dislike, timestamp);

                            body=get_post_format(picture, name, comment, functions, '', options, "comment_delete_"+x, "comment_body_0_0_"+x, comment_badges[x])+body;
                            

                        }
                    }
                    
                    $("#comment_body_0_0").html(body);
                    
                    if($('#comment_body_0_0').html()=='')
                        $('#comment_body_0_0').html("There are no comments");
                    else
                        $('.comment_delete').attr({'onmouseover': "display_title(this, 'Delete this comment');", 'onmouseout': "hide_title(this);"}).hide();
                    
                    
                    for(var x = 0; x < comments.length; x++)
                    {
                        if(comments_users_sent[x]!=<?php echo $_SESSION['id']; ?>)
                        {
                            if(has_liked_comment[x]==true)
                                $('#comment_like_0_0_'+x).attr({'onClick': "unlike_video_comment(<?php echo $user_id; ?>, <?php echo $video_id; ?>, 0, "+comment_ids[x]+", "+x+", 0, "+num_comment_likes[x]+");"});
                            else
                                $('#comment_like_0_0_'+x).attr({'onClick': "like_video_comment(<?php echo $user_id; ?>, <?php echo $video_id; ?>, 0, "+comment_ids[x]+", "+x+", 0, "+num_comment_likes[x]+");"});

                            if(has_disliked_comment[x]==true)
                                $('#comment_dislike_0_0_'+x).attr({'onClick': "undislike_video_comment(<?php echo $user_id; ?>, <?php echo $video_id; ?>, 0, "+comment_ids[x]+", "+x+", 0, "+num_comment_dislikes[x]+");"});
                            else
                                $('#comment_dislike_0_0_'+x).attr({'onClick': "dislike_video_comment(<?php echo $user_id; ?>, <?php echo $video_id; ?>, 0, "+comment_ids[x]+", "+x+", 0, "+num_comment_dislikes[x]+");"});
                        }

                        $('#comment_delete_'+x).attr('onClick', "delete_video_comment(<?php echo $user_id; ?>, <?php echo $video_id; ?>, 0, "+comment_ids[x]+", "+x+", 0);");
                    }
                    
                    
                    
                    
                    for(var x = 0; x < comments.length; x++)
                        count_time(comment_timestamp_seconds[x], '#post_comment_timestamp_'+comment_ids[x]);



                    /////////end of posting comments////////
                    change_color();
                    $('.post_body').css('min-height', '0px');
                    initialize_comment_events();
                }, "json");
                
            }
            function toggle_comment_displays()
            {
                if($('.comment_input_body').css('display')=='block')
                {
                    $('.comment_input_body').hide();
                    $('#comment_body_0_0').hide();
                }
                else
                {
                    $('.comment_input_body').show();
                    $('#comment_body_0_0').show();
                }
            }
        </script>
        <script type="text/javascript">
            $(window).ready(function()
            {
                display_video();
                change_color();
                $('.post_hide').hide();
                $('#menu').hide();
                $('#footer').css('width', '910px');

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
        <?php include('required_html.php'); ?>
        <div id="main">
            <?php include('required_side_html.php'); ?>
            <div class="content box" style="width: 910px;">
                <div id="post" style="margin: 10px">

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
                        comment_video(<?php echo $user_id; ?>, <?php echo $video_id; ?>, 0, 0, $(this).data('num_comments'));
                        $(this).val('');
                    }
                });
            }
        </script>
    </body>
</html>