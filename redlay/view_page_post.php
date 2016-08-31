<?php
@include('init.php');
include('universal_functions.php');
$allowed="both";
include('security_checks.php');

$post_id=(int)($_GET['post_id']);
$page_id=(int)($_GET['page_id']);

if(!is_id($page_id)||!page_id_exists($page_id))
{
    header("Location: http://www.redlay.com");
    exit();
}
else if(page_id_terminated($page_id))
{
    header("Location: http://www.redlay.com/account_terminated.php");
    exit();
}
else
{
    //checks if post exists
    $query=mysql_query("SELECT post_ids FROM content WHERE user_id=$profile_id LIMIT 1");
    if($query&&mysql_num_rows($query)==1)
    {
       $array=mysql_fetch_row($query);
       $post_ids=explode('|^|*|', $array[0]);

       $index=-1;
       for($x = 0; $x < sizeof($post_ids); $x++)
       {
          if($post_id==$post_ids[$x])
             $index=$x;
       }

       //if post doesn't exist
       if($index==-1)
       {
          header("Location: http://www.redlay.com");
          exit();
       }
    }


    //gets the user's privacy preferences
    $privacy=get_user_privacy_settings($profile_id);
    $general=$privacy[0];
    $non_adds=$privacy[1];
    if($non_adds[3]=='no'&&$profile_id!=$_SESSION['id']&&user_is_friends($profile_id, $_SESSION['id'])=='false')
        header("Location: http://www.redlay.com");
}


?>
<!DOCTYPE html>
<html>
    <head>
        <title>Post</title>
        <?php include('required_header.php'); ?>
        <script type="text/javascript">
            function change_color()
            {
                <?php
                    $colors=get_user_display_colors($profile_id);
                    $color=$colors[0];
                    $box_background_color=$colors[1];
                    $text_color=$colors[2];
                ?>
                        
                $('.box').css('border', '5px solid <?php echo $color; ?>');
                $('.box').css('background-color', '<?php echo $box_background_color; ?>');
                
                $('#view_post_body').css('background-color', '<?php echo $box_background_color; ?>');

                $('#post_text, .status_update_text, #company_footer, .timestamp_status_update, .comment_timestamp, .comment_text_body').css({'color': '<?php echo $text_color; ?>'});
                $('.status_update_like, .status_update_dislike, .comment_text, .user_name, .comment_name, .comment_like, .comment_dislike').css('color', '<?php echo $color; ?>');

                $('.post_delete, .comment_delete').css({'background-color': '<?php echo $color; ?>', 'color': '<?php echo $text_color; ?>'});
                $('.comment_textarea').css({'outline-color': '<?php echo $color; ?>', 'width': '785px'});
                
                $('.status_update').css('border', 'none');
                $('.text_color').css('color', "<?php echo $text_color; ?>");
                $('.title_color').css('color', "<?php echo $color; ?>");
            }
            
            function display_post()
            {
                var timezone=get_timezone();
                $.post('view_post_query.php',
                {
                    num:1,
                    profile_id: <?php echo $profile_id; ?>,
                    post_id: <?php echo $post_id ?>,
                    timezone:timezone
                }, function(output)
                {
                    //post information
                    var post=output.post;
                    var poster_id=output.user_id_posted;
                    var user_name=output.user_name;
                    var like_ids=output.like_ids;
                    var dislike_ids=output.dislike_ids;
                    var num_likes=output.num_likes;
                    var num_dislikes=output.num_dislikes;
                    var num_comments=output.num_comments;
                    var timestamp=output.timestamp;
                    var timestamp_seconds=output.timestamp_seconds;
                    var profile_picture=output.profile_picture;
                    var badges=output.badges;
                    var comment_badges=output.comment_badges;
                    
                    //comment information
                    var comment_likes=output.comment_likes;
                    var comment_dislikes=output.comment_dislikes;
                    var comments=output.comments;
                    var comment_timestamps=output.comment_timestamps;
                    var comment_timestamp_seconds=output.comment_timestamp_seconds;
                    var comments_user_sent=output.comments_users_sent;
                    var comments_name=output.comment_names;
                    var num_comment_likes=output.num_comment_likes;
                    var num_comment_dislikes=output.num_comment_dislikes;
                    var comment_ids=output.comment_ids;
                    var comment_profile_pictures=output.comment_profile_pictures;
                    
                    post=convert_image(text_format(post), 'post');
                    
                    var picture="<a href='http://www.redlay.com/profile.php?user_id="+poster_id+"'><img class='profile_picture_status profile_picture' src='"+profile_picture+"'></a>";
                    var name="<a class='user_name_link' href='http://www.redlay.com/profile.php?user_id="+poster_id+"' ><span class='user_name' onmouseover=name_over(this); onmouseout=name_out(this);>"+user_name+"</span></a>";
                    var post_text="<p class='status_update_text text_color'>"+post+"</p>";
                    
                    if(<?php if($general[2]=="yes"||$profile_id==$_SESSION['id']||user_is_friends($profile_id, $_SESSION['id'])=='true') echo "true"; else echo "false"; ?>==true)
                        var comment_input="<div id='comment_text' class='comment_input_body'><textarea class='comment_textarea input_box' onFocus='input_in(this);' onBlur='input_out(this);' id='comment_input_0_0' placeholder='Comment...' maxlength='500'></textarea></div>";
                    else
                        var comment_input="";
                    
                    var comment_body="<div class='comment_body' id='comment_body_0_0'></div>";
                    
                    if(<?php if(isset($_SESSION['id'])) echo $_SESSION['id']; else echo '0'; ?>==<?php echo $profile_id; ?>)
                        var options="<div class='post_delete post_hide' id='post_options_<?php echo $post_id; ?>' onClick='show_post_options(<?php echo $post_id; ?>, <?php  echo $profile_id; ?>);'>O</div>";
                    else
                        var options="";

                    if(<?php if($general[2]=="yes"||$profile_id==$_SESSION['id']||user_is_friends($_SESSION['id'], $ID)) echo "true"; else echo "false"; ?>==true)
                    {
                        //checks whether current user has liked post or not
                        if(num_likes>=1)
                        {
                            var has_liked=false;
                            for(var x = 0; x < like_ids.length; x++)
                            {
                                if(like_ids[x]==<?php echo $_SESSION['id']; ?>)
                                    has_liked=true;
                            }
                        }

                        //checks whether current user has disliked post or not
                        if(num_dislikes>=1)
                        {
                            var has_disliked=false;
                            for(var x = 0; x < dislike_ids.length; x++)
                            {
                                if(dislike_ids[x]==<?php echo $_SESSION['id']; ?>)
                                    has_disliked=true;
                            }
                        }

                        //if 0 likes and owner of post is not current user
                        //if people liked post and current user has not liked it
                        //if current user has liked it
                        //if owner of post is current user
                        if(num_likes==0&&<?php echo $_SESSION['id'] ?>!=poster_id)
                            var like="<p class='status_update_like' onClick='like_post(<?php echo $profile_id ?>, <?php echo $post_id ?>, "+poster_id+", "+num_likes+", 0, 0);' onmouseover=name_over(this); onmouseout=name_out(this); id='post_like_0_0' >Like</p>";
                        else if(num_likes>=1&&has_liked==false&&<?php echo $_SESSION['id'] ?>!=poster_id)
                            var like="<p class='status_update_like' onClick='like_post(<?php echo $profile_id ?>, <?php echo $post_id ?>, "+poster_id+", "+num_likes+", 0, 0);' onmouseover=name_over(this); onmouseout=name_out(this); id='post_like_0_0' >Like ["+num_likes+"]</p>";
                        else if(has_liked==true)
                            var like="<p class='status_update_like' onClick='unlike_post(<?php echo $profile_id ?>, <?php echo $post_id ?>, "+poster_id+", "+num_likes+", 0, 0);' onmouseover=name_over(this); onmouseout=name_out(this); id='post_like_0_0' >Unlike ["+num_likes+"]</p>";
                        else
                        {
                            if(num_likes==0)
                                var like="";
                            else if(num_likes==1)
                                var like="<p class='status_update_like' >1 like</p>";
                            else
                                var like="<p class='status_update_like' >"+num_likes+" likes</p>";
                        }

                        //if 0 dislikes and owner of post is not current user
                        //if people disliked post and current user has not disliked it
                        //if current user has disliked it
                        //if owner of post is current user
                        if(num_dislikes==0&&<?php echo $_SESSION['id'] ?>!=poster_id)
                            var dislike="<p class='status_update_dislike' onClick='dislike_post(<?php echo $profile_id ?>, <?php echo $post_id ?>, "+poster_id+", "+num_dislikes+", 0, 0);' onmouseover=name_over(this); onmouseout=name_out(this); id='post_dislike_0_0' >Dislike</p>";
                        else if(num_dislikes>=1&&has_disliked==false&&<?php echo $_SESSION['id'] ?>!=poster_id)
                            var dislike="<p class='status_update_dislike' onClick='dislike_post(<?php echo $profile_id ?>, <?php echo $post_id ?>, "+poster_id+", "+num_dislikes+", 0, 0);' onmouseover=name_over(this); onmouseout=name_out(this); id='post_dislike_0_0' >Dislike ["+num_dislikes+"]</p>";
                        else if(has_disliked==true)
                            var dislike="<p class='status_update_dislike' onClick='undislike_post(<?php echo $profile_id ?>, <?php echo $post_id ?>, "+poster_id+", "+num_dislikes+", 0, 0);' onmouseover=name_over(this); onmouseout=name_out(this); id='post_dislike_0_0' >Undislike ["+num_dislikes+"]</p>";
                        else
                        {
                            if(num_dislikes==0)
                                var dislike="";
                            else if(num_dislikes==1)
                                var dislike="<p class='status_update_dislike' >1 dislike</p>";
                            else
                                var dislike="<p class='status_update_dislike' >"+num_dislikes+" dislikes</p>";
                        }
                    }
                    else
                    {
                                        if(num_likes[x]==1)
                                            var like="<span class='status_update_like me' >1 like</span>";
                                        else if(num_likes[x]!=0)
                                            var like="<span class='status_update_like me' >"+num_likes[x]+" likes</span>";
                                        else
                                            var like="";
                                        
                                        
                                        if(num_dislikes[x]==1)
                                            var dislike="<span class='status_update_dislike me' >1 dislike</span>";
                                        else if(num_dislikes[x]!=0)
                                            var dislike="<span class='status_update_dislike me' >"+num_dislikes[x]+" dislikes</span>";
                                        else
                                            var dislike="";
                    }

                    if(num_comments==0)
                        var comment_text="<span id='comment_title' class='comment_text' onmouseover=name_over(this); onmouseout=name_out(this); onClick='toggle_comment_displays();'>Comment</span>";
                    else
                        var comment_text="<span id='comment_title' class='comment_text' onmouseover=name_over(this); onmouseout=name_out(this); onClick='toggle_comment_displays();'>Comment ["+num_comments+"]</span>";






                    var timestamp="<span class='timestamp_status_update' id='post_timestamp'>"+timestamp+"</span>";
                    var post_functions=get_post_functions(like, dislike, comment_text, timestamp)


//                    var body="<div class='status_update'>"+options+"<table style='width:100%;'><tbody><tr id='post_row_1' class='post_row'>  <td class='post_profile_picture_unit'>"+profile_picture+"</td><td class='post_body_unit'>"+name+post_text+post_functions+"</td>  </tr><tr id='post_row_2' class='post_row'>  <td colspan='2' class='post_functions_unit'></td>  </tr><tr id='post_row_3' class='post_row'>  <td colspan='2' class='post_comments_unit'>"+comment_input+comment_body+"</td>  </tr><tr id='post_row_4' class='post_row'>  <td class='post_timestamp_unit' colspan='2'>"+timestamp+"</td>  </tr></tbody></table></div>";
                    var body=get_post_format(picture, name, post_text,post_functions, comment_input+comment_body, options, 'post_options_<?php echo $post_id; ?>', 'post_<?php echo $post_id; ?>', badges)
                    count_time(timestamp_seconds, '#post_timestamp');

                    $('#post').html(body);
//                    $('#post').html(profile_picture+name+post_text+functions+timestamp+comment_input+comment_body);
                    $('#comment_title').data({'num_comments': num_comments, 'poster_id': poster_id});
                    $('#comment_text').hide();
                    $('#comment_body_0_0').hide();









                    /////////posts commenmts////////////////

                    $('#comment_body_0_0').html('');
                    var body='';
                    for(var x = 0; x < comments.length; x++)
                    {
                        comments[x]=convert_image(text_format(comments[x]), 'comment');
                        if(comments[x]!='')
                        {
                            var name="<a class='user_name_link' href='http://www.redlay.com/profile.php?user_id="+comments_user_sent[x]+"'><span class='comment_name' id='comment_name_"+x+"' onmouseover=name_over(this); onmouseout=name_out(this); >"+comments_name[x]+"</span></a>";
                            var picture="<a href='http://www.redlay.com/profile.php?user_id="+comments_user_sent[x]+"'><img class='comment_profile_picture profile_picture' id='picture_comment_"+x+"' src='"+comment_profile_pictures[x]+"' ></a>";
                            var comment="<span class='comment_text_body'>"+comments[x]+"</span>";

                            if(comments_user_sent[x]==<?php echo $_SESSION['id']; ?>)
                                var close="<div class='comment_delete' id='comment_delete_"+x+"' onClick='delete_comment(<?php echo $profile_id; ?>, <?php echo $post_id; ?>, 0, "+comment_ids[x]+", "+x+", 0);' >x</div>";
                            else
                                var close="";

                            var bool=false;
                            for(var y = 0; y < comment_likes[x].length; y++)
                            {
                                if(comment_likes[x][y]==<?php if(isset($_SESSION['page_id']))echo '0'; else echo $_SESSION['id']; ?>)
                                    bool=true;
                            }
                            var bool2=false;
                            for(var y = 0; y < comment_dislikes[x].length; y++)
                            {
                                if(comment_dislikes[x][y]==<?php if(isset($_SESSION['page_id']))echo '0'; else echo $_SESSION['id']; ?>)
                                    bool2=true;
                            }

                            if(num_comment_likes[x]==0&&comments_user_sent[x]!=<?php if(isset($_SESSION['page_id']))echo '0'; else echo $_SESSION['id']; ?>)
                                var like="<span class='comment_like' id='comment_like_0_0_"+x+"' onClick='like_comment(<?php echo $profile_id; ?>, <?php echo $post_id; ?>, 0, "+comment_ids[x]+", "+x+", 0, "+num_comment_likes[x]+");' onmouseover=name_over(this); onmouseout=name_out(this); >Like</span>";
                            else if(bool==true)
                                var like="<span class='comment_like' id='comment_like_0_0_"+x+"' onClick='unlike_comment(<?php echo $profile_id; ?>, <?php echo $post_id; ?>, 0, "+comment_ids[x]+", "+x+", 0, "+num_comment_likes[x]+");' onmouseover=name_over(this); onmouseout=name_out(this); >Unlike ["+num_comment_likes[x]+"]</span>";
                            else if(comments_user_sent[x]==<?php if(isset($_SESSION['page_id']))echo '0'; else echo $_SESSION['id']; ?>)
                            {
                                if(num_comment_likes[x]>1)
                                    var like="<span class='comment_like comment_like_me'>"+num_comment_likes[x]+" likes</span>";
                                else if(num_comment_likes[x]==1)
                                    var like="<span class='comment_like comment_like_me'>1 like</span>";
                                else
                                    var like="";
                            }
                            else
                                var like="<span class='comment_like' id='comment_like_"+x+"' onClick='like_comment(<?php echo $profile_id; ?>, <?php echo $post_id; ?>, 0, "+comment_ids[x]+", "+x+", 0, "+num_comment_likes[x]+");' onmouseover=name_over(this); onmouseout=name_out(this); >Like ["+num_comment_likes[x]+"]</span>";

                            

                            if(num_comment_dislikes[x]==0&&comments_user_sent[x]!=<?php if(isset($_SESSION['page_id']))echo '0'; else echo $_SESSION['id']; ?>)
                                var dislike="<span class='comment_dislike' id='comment_dislike_0_0_"+x+"' onClick='dislike_comment(<?php echo $profile_id; ?>, <?php echo $post_id; ?>, 0, "+comment_ids[x]+", "+x+", 0, "+num_comment_dislikes[x]+");' onmouseover=name_over(this); onmouseout=name_out(this); >Dislike</span>";
                            else if(bool2==true)
                                var dislike="<span class='comment_dislike' id='comment_dislike_0_0_"+x+"' onClick='undislike_comment(<?php echo $profile_id; ?>, <?php echo $post_id; ?>, 0, "+comment_ids[x]+", "+x+", 0, "+num_comment_dislikes[x]+");' onmouseover=name_over(this); onmouseout=name_out(this); >Undislike ["+num_comment_dislikes[x]+"]</span>";
                            else if(comments_user_sent[x]==<?php if(isset($_SESSION['page_id']))echo '0'; else echo $_SESSION['id']; ?>)
                            {
                                if(num_comment_dislikes[x]==1)
                                    var dislike="<span class='comment_dislike comment_dislike_me'>1 dislike</span>";
                                else if (num_comment_dislikes[x]>1)
                                    var dislike="<span class='comment_dislike comment_dislike_me'>"+num_comment_dislikes[x]+" dislikes</span>";
                                else
                                    var dislike="";
                            }
                            else
                                var dislike="<span class='comment_dislike' id='comment_dislike_"+x+"' onClick='dislike_comment(<?php echo $profile_id; ?>, <?php echo $post_id; ?>, 0, "+comment_ids[x]+", "+x+", 0, "+num_comment_dislikes[x]+");' onmouseover=name_over(this); onmouseout=name_out(this); >Dislike ["+num_comment_dislikes[x]+"]</span>";


                            var timestamp="<span class='comment_timestamp' id='post_comment_timestamp_"+comment_ids[x]+"'>"+comment_timestamps[x]+"</span>";
                            if(<?php if(isset($_SESSION['page_id'])) echo "0"; else echo "1"; ?>==0)
                            {
                                like="";
                                dislike="";
                            }
                            
                            
                                var functions=get_comment_functions(like, dislike);

                            var body=get_post_format(picture, name, comment+functions, '', timestamp, close, 'comment_delete_'+x, "comment_body_0_0_"+x, comment_badges[x])+body;
                        }
                    }
                    
                    $("#comment_body_0_0").html(body);
                    
                    if($('#comment_body_0_0').html()=='')
                        $('#comment_body_0_0').html("There are no comments");
                    else
                        $('.comment_delete').attr({'onmouseover': "display_title(this, 'Delete this comment');", 'onmouseout': "hide_title(this);"}).hide();
                    
                    
                    for(var x = 0; x < comments.length; x++)
                        count_time(comment_timestamp_seconds[x], '#post_comment_timestamp_'+comment_ids[x]);



                    /////////end of posting comments////////
                    change_color();
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
                display_post();
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
            <div class="content" style="width: 910px;" id="view_post_body">
                <div id="post" style="margin: 10px">

                </div>
                <?php include('footer.php'); ?>
            </div>
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
                        comment(<?php echo $profile_id; ?>, $('#comment_title').data('poster_id'), <?php echo $post_id; ?>, 0, 0, $('#comment_title').data('num_comments'));
                        $(this).val('');
                    }
                });
            }
        </script>
    </body>
</html>