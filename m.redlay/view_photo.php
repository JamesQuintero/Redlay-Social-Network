<?php
@include('init.php');
include('../universal_functions.php');
$allowed="users";
include("security_checks.php");

$ID=(int)($_GET['user_id']);
$picture_id=clean_string($_GET['picture_id']);
$type=clean_string($_GET['type']);


$picture_is_viewable=picture_is_viewable($ID, $picture_id, $type);

if(!$picture_is_viewable&&($type=='user'||$type=='page'))
{
    if($type=='user')
    {
        header("Location: http://m.redlay.com/profile.php?user_id=$ID");
        exit();
    }
    else if($type=='page')
    {
        header("Location: http://m.redlay.com/page.php?page_id=$ID");
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
?>
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
                    else
                        $colors=get_user_display_colors($_SESSION['id']);
                    $color=$colors[0];
                    $box_background_color=$colors[1];
                    $text_color=$colors[2];
                ?>
                $('#picture_description_description').css('color', '<?php echo $text_color; ?>');
                $('.box').css('background-color', '<?php echo $box_background_color; ?>');
                $('.box').css('border', '15px solid <?php echo $color; ?>');
                $('#picture_description_name, #picture_like_text, #picture_dislike_text').css('color', '<?php echo $color; ?>');
                $('#delete_photo, .comment_name').css('color', '<?php echo $color; ?>');
                $('#slide_show').css('color', '<?php echo $color; ?>');
                $('.picture_comment').css('outline-color', '<?php echo $color; ?>');
                $('.comment_text_body, .timestamp_status_update, #company_footer').css('color', '<?php echo $text_color; ?>');
                $('.photo_comment_delete').css({'background-color': '<?php echo $color; ?>', 'color': '<?php echo $text_color; ?>'});
                $('body').css('background-attachment', 'fixed');

                $('.comment_like, .comment_dislike').css('color', '<?php echo $color; ?>');
            }
            
            $(window).ready(function()
            {
                if((<?php echo $ID; ?>!=<?php if(isset($_SESSION['id'])) echo $_SESSION['id']; else echo "0"; ?>||'<?php echo $picture_id; ?>'=='0')||(<?php echo "'".$type."'"; ?>!='user'&&<?php if(!isset($_SESSION['page_id']))echo '0'; else echo '1'; ?>==0&&<?php echo $ID; ?>==<?php if(!isset($_SESSION['page_id'])) echo $_SESSION['id']; else echo '0'; ?>))
                {
                    var content=$('#picture_main').html();
                    $('#delete_photo').html('');
                }
            });
            function delete_photo_menu()
            {
                $('.alert_box').css('opacity', 1).show().draggable();
                $('.alert_box_inside').html("<table id='delete_photo_table'><tr id='delete_photo_row_1'></tr><tr id='delete_photo_row_2'></tr><tr id='delete_photo_row_3'></tr></table>");
                    $('#delete_photo_row_1').html("<td colspan='3'><p class='alert_box_title'>Delete Photo:</p></td>");
                    $('#delete_photo_row_2').html("<td colspan='3'><p class='alert_box_body_text'>Are you sure you want to permanently delete this photo? You will be removing all trace.</p></td>");
                    $('#delete_photo_row_3').html("<td></td><td id='confirm_button_unit' ><input type='button' value='Delete' class='red_button' id='confirm_delete_button' onClick='delete_photo();'/></td><td id='cancel_button_unit'><input class='cancel_button' type='button' id='cancel_delete_photo_button' value='Cancel' onClick='close_alert_box();' /></td>")

                change_color();
            }
            function delete_photo()
            {
                $.post('main_access.php',
                {
                    access:20,
                    picture_id: '<?php echo $picture_id; ?>'
                }, function(output)
                {
                    window.location.replace("http://m.redlay.com/profile.php?user_id=<?php echo $_SESSION[id] ?>");
                });
            }
            function delete_comment(index)
            {
                $.post('main_access.php',
                {
                    access:22,
                    user_id: <?php echo $ID; ?>,
                    type: '<?php echo $type; ?>',
                    photo_id: '<?php echo $picture_id; ?>',
                    comment_index: index
                }, function(output)
                {
                    //display error
                });
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
                     if($next_photo!='0'&&$next_photo==false)
                        echo "$('#right_arrow').hide();";
                     if($previous_photo==false)
                        echo "$('#left_arrow').hide();";
                ?>
            });

            function like_photo(num_likes)
            {
                $.post('main_access.php',
                {
                    access:23,
                    user_id: <?php echo $ID; ?>,
                    picture_id: '<?php echo $picture_id; ?>',
                    type: '<?php echo $type; ?>'
                }, function(output)
                {
                    num_likes++;
                    $('#picture_like_text').html("Unlike ["+num_likes+']').attr('onClick', 'unlike_photo('+num_likes+');');
                });
            }
            function dislike_photo(num_dislikes)
            {
                $.post('main_access.php',
                {
                    access:24,
                    user_id: <?php echo $ID; ?>,
                    picture_id: '<?php echo $picture_id; ?>',
                    type: '<?php echo $type; ?>'
                }, function(output)
                {
                    num_dislikes++;
                    $('#picture_dislike_text').html("Undislike ["+num_dislikes+']').attr('onClick', 'undislike_photo('+num_dislikes+');');
                });
            }

            function unlike_photo(num_likes)
            {
                $.post('main_access.php',
                {
                    access:25,
                    user_id: <?php echo $ID; ?>,
                    picture_id: '<?php echo $picture_id; ?>',
                    type: '<?php echo $type; ?>'
                }, function(output)
                {
                    num_likes--;
                    if(num_likes==0)
                        $('#picture_like_text').html("Like").attr('onClick', "like_photo("+num_likes+");");
                    else
                        $('#picture_like_text').html("Like ["+num_likes+']').attr('onClick', "like_photo("+num_likes+");");
                });
            }
            function undislike_photo(num_dislikes)
            {
                $.post('main_access.php',
                {
                    access:26,
                    user_id: <?php echo $ID; ?>,
                    picture_id: '<?php echo $picture_id; ?>',
                    type: '<?php echo $type; ?>'
                }, function(output)
                {
                    num_dislikes--;
                    if(num_dislikes==0)
                        $('#picture_dislike_text').html("Dislike").attr('onClick', "dislike_photo("+num_dislikes+");");
                    else
                        $('#picture_dislike_text').html("Dislike ["+num_dislikes+']').attr('onClick', "dislike_photo("+num_dislikes+");");
                });
            }

            function comment()
            {
//                alert(<?php echo $ID ?>+' | <?php echo $picture_id; ?> | <?php echo $type; ?>')
                var comment_text=$('#comment_picture').val();
                $.post('main_access.php',
                {
                    access:27,
                    user_id: <?php echo $ID; ?>,
                    picture_id: '<?php echo $picture_id; ?>',
                    comment: comment_text,
                    type: '<?php echo $type; ?>'
                }, function (output)
                {
                    window.location.replace(window.location);
                });
            }

            function display_comments()
            {
                $.post('main_access.php',
                {
                    access:28,
                    num:1,
                    type: '<?php echo $type; ?>',
                    poster_id: <?php echo $ID; ?>,
                    index: <?php echo $index; ?>
                }, function(output)
                {
                    var comments=output.comments;
                    var comment_names=output.comment_names;
                    var comments_user_sent=output.comments_user_sent;
                    var comment_timestamps=output.comment_timestamps;
                    var comment_likes=output.comment_likes;
                    var comment_dislikes=output.comment_dislikes;
                    var num_comment_likes=output.num_comment_likes;
                    var num_comment_dislikes=output.num_comment_dislikes;


                    if(comments[0]!='')
                    {
                        <?php if(isset($_SESSION['page_id'])) echo "$('#picture_comments').html('');"; ?>

                        for(var x = 0; x < comments.length; x++)
                        {
                            var picture="<div id='picture_comment_name_"+x+"' class='picture_comment_body'  ><a href='http://m.redlay.com/profile.php?user_id="+comments_user_sent[x]+"'><img class='comment_profile_picture profile_picture' src='http://www.redlay.com/users/thumbs/users/"+comments_user_sent[x]+"/0.jpg'/></a>";
                            var name="<div class='user_name comment_user_name_body'><a href='http://m.redlay.com/profile.php?user_id="+comments_user_sent[x]+"' style='text-decoration:none'><p class='comment_name' >"+comment_names[x]+"</p></a></div>";
                            var comment="<p id='picture_comment_"+x+"' class='comment_text_body'>"+comments[x]+"</p>";
                            var timestamp="<p class='comment_timestamp'>"+comment_timestamps[x]+"</p>";
                            var comment_break="<hr class='comment_break picture_comment_break' /></div>";

                            var bool=false;
                                var bool2=false;

                                //seeing if already liked
                                for(var z = 0; z < comment_likes[x].length; z++)
                                {
                                    if(comment_likes[x][z]==<?php echo $_SESSION['id']; ?>)
                                        bool=true;

                                     if(comment_dislikes[x][z]==<?php echo $_SESSION['id']; ?>)
                                        bool2=true;
                                }

                                //displaying likes
                                if(comments_user_sent[x]!=<?php echo $_SESSION['id']; ?>)
                                {
                                    if(bool==true)
                                        var like="<div id='photo_comment_like_body_"+x+"'><p class='comment_like' id='photo_comment_like_"+x+"' >Unlike ["+num_comment_likes[x]+"]</p></div>";
                                    else if(num_comment_likes[x]==0&&comments_user_sent[x]!='')
                                        var like="<div id='photo_comment_like_body_"+x+"'><p class='comment_like' id='photo_comment_like_"+x+"' >Like</p></div>";
                                    else if(comments_user_sent[x]!='')
                                        var like="<div id='photo_comment_like_body_"+x+"'><p class='comment_like' id='photo_comment_like_"+x+"' >Like ["+num_comment_likes[x]+"]</p></div>";
                                    else
                                        var like="";
                                }
                                else
                                {
                                    if(num_comment_likes[x]==1)
                                        var like="<div id='photo_comment_like_body_"+x+"'><p class='comment_like comment_like_me' >1 like</p></div>";
                                    else if(num_comment_likes[x]>1)
                                        var like="<div id='photo_comment_like_body_"+x+"'><p class='comment_like comment_like_me' >"+num_comment_likes[x]+" likes</p></div>";
                                    else
                                        var like="";
                                }

                                //displaying dislikes
                                if(comments_user_sent[x]!=<?php echo $_SESSION['id']; ?>)
                                {
                                    if(bool2==true)
                                        var dislike="<div id='photo_comment_dislike_body_"+x+"'><p class='comment_dislike' id='photo_comment_dislike_"+x+"' >Undislike ["+num_comment_dislikes[x]+"]</p></div>";
                                    else if(num_comment_dislikes[x]==0&&comments_user_sent[x]!='')
                                        var dislike="<div id='photo_comment_dislike_body_"+x+"'><p class='comment_dislike' id='photo_comment_dislike_"+x+"' >Dislike</p></div>";
                                    else if(comments_user_sent[x]!='')
                                        var dislike="<div id='photo_comment_dislike_body_"+x+"'><p class='comment_dislike' id='photo_comment_dislike_"+x+"' >Dislike ["+num_comment_dislikes[x]+"]</p></div>";
                                    else
                                        var dislike="";
                                }
                                else
                                {
                                    if(num_comment_dislikes[x]==1)
                                        var dislike="<div id='photo_comment_dislike_body_"+x+"'><p class='comment_dislike comment_dislike_me' >1 dislike</p></div>";
                                    else if(num_comment_dislikes[x]>1)
                                        var dislike="<div id='photo_comment_dislike_body_"+x+"'><p class='comment_dislike comment_dislike_me' >"+num_comment_dislikes[x]+" dislikes</p></div>";
                                    else
                                        var dislike="";
                                }


                                var functions="<table class='post_functions_comment_table' ><tbody><tr><td class='post_functions_comment_unit'>"+like+"</td><td class='post_functions_comment_unit'>"+dislike+"</td></tr></tbody></table>";



                                    $('#comment_body').html(picture+name+comment+functions+timestamp+comment_break+$('#comment_body').html());







                                        if(bool)
                                            $('#photo_comment_like_'+x).attr({'onClick': "unlike_comment("+x+", "+num_comment_likes[x]+");"});
                                        else
                                            $('#photo_comment_like_'+x).attr({'onClick': "like_comment("+x+", "+num_comment_likes[x]+");"});

                                        if(bool2)
                                            $('#photo_comment_dislike_'+x).attr({'onClick': "undislike_comment("+x+", "+num_comment_dislikes[x]+")"});
                                        else
                                            $('#photo_comment_dislike_'+x).attr({'onClick': "dislike_comment("+x+", "+num_comment_dislikes[x]+")"});


                        }
                    }
                    else
                        $('#comment_body').html("<p style='font-size:25px;padding:10px;margin:0px;'>There are no comments here! You should post one!</p>").css('padding-top', '0px');
                    $('.photo_comment_delete').hide();

                    change_color();
                }, "json");
            }

            $(window).ready(function()
            {
                var likes=false;
                var dislikes=false;
                var likes_size=0;
                var dislikes_size=0;
                <?php
                    $query=mysql_query("SELECT picture_likes, picture_dislikes FROM pictures WHERE user_id='$ID' LIMIT 1");
                    if($query&&mysql_num_rows($query)==1)
                    {
                        $array=mysql_fetch_row($query);
                        $likes=explode('|^|*|', $array[0]);
                        $dislikes=explode('|^|*|', $array[1]);
                        for($x = 0; $x < sizeof($likes); $x++)
                            $new_likes[$x]=explode('|%|&|', $likes[$x]);

                        for($x = 0; $x < sizeof($dislikes); $x++)
                            $new_dislikes[$x]=explode('|%|&|', $dislikes[$x]);

                        $bool=false;
                        for($x = 0; $x < sizeof($new_likes[$index]); $x++)
                        {
                            if($new_likes[$index][$x]==$_SESSION['id'])
                                echo "likes=true;";
                        }

                        for($x = 0; $x < sizeof($new_dislikes[$index]); $x++)
                        {
                            if($new_dislikes[$index][$x]==$_SESSION['id'])
                                echo "dislikes=true;";
                        }
                        if($likes[$index]=='')
                            echo "likes_size=0;";
                        else
                            echo "likes_size='".sizeof($new_likes[$index])."';";
                        if($dislikes[$index]=='')
                            echo "dislikes_size=0;";
                        else
                            echo "dislikes_size='".sizeof($new_dislikes[$index])."';";
                    }
                ?>
                if((<?php if(isset($_SESSION['id'])) echo $_SESSION['id'];else echo "0"; ?>==<?php echo $ID; ?>)||<?php if(isset($_SESSION['page_id']))echo $_SESSION['page_id']; else echo "0"; ?>!=0)
                {
                    if(likes_size==1)
                        $('#picture_like_text').html('1 Like').attr('onClick', '');
                    else if(likes_size>=2)
                        $('#picture_like_text').html(likes_size+' Likes').attr('onClick', '');
                }
                else if(likes_size>0&&likes==false)
                    $('#picture_like_text').html('Like ['+likes_size+']').attr({'onClick': "like_photo("+likes_size+");", 'onmouseover': "name_out(this);", 'onmouseout': "name_out(this);"});
                else if(likes_size>0&&likes==true)
                    $('#picture_like_text').html('Unlike ['+likes_size+']').attr({'onClick': "unlike_photo("+likes_size+");", 'onmouseover': "name_out(this);", 'onmouseout': "name_out(this);"});
                else if(likes_size==0)
                    $('#picture_like_text').html('Like').attr({'onClick': "like_photo("+likes_size+");", 'onmouseover': "name_out(this);", 'onmouseout': "name_out(this);"});

                if((<?php if(isset($_SESSION['id'])) echo $_SESSION['id'];else echo "0"; ?>==<?php echo $ID; ?>)||<?php if(isset($_SESSION['page_id']))echo $_SESSION['page_id']; else echo "0"; ?>!=0)
                {
                    if(dislikes_size==1)
                        $('#picture_dislike_text').html('1 Dislike').attr('onClick', '');
                    else if(likes_size>=2)
                        $('#picture_dislike_text').html(dislikes_size+' Dislike').attr('onClick', '');
                }
                else if(dislikes_size>0&&dislikes==false)
                    $('#picture_dislike_text').html('Dislike ['+dislikes_size+']').attr({'onClick': "dislike_photo("+dislikes_size+");", 'onmouseover': "name_out(this);", 'onmouseout': "name_out(this);"});
                else if(dislikes_size>0&&dislikes==true)
                    $('#picture_dislike_text').html('Undislike ['+dislikes_size+']').attr({'onClick': "undislike_photo("+dislikes_size+");", 'onmouseover': "name_out(this);", 'onmouseout': "name_out(this);"});
                else if(dislikes_size==0)
                    $('#picture_dislike_text').html('Dislike').attr({'onClick': "dislike_photo("+dislikes_size+");", 'onmouseover': "name_out(this);", 'onmouseout': "name_out(this);"});
            });
            function display_photo_timestamp()
            {
                var timezone=get_timezone();
                $.post('main_access.php',
                {
                    access:29,
                    num:3,
                    type: '<?php echo $type; ?>',
                    timezone: timezone,
                    index: <?php echo $index; ?>,
                    user_id: <?php echo $ID; ?>
                }, function(output)
                {
                    var timestamp=output.timestamp;
                    
                    $('#picture_timestamp').html(timestamp);
                }, "json");
            }
            $(window).ready(function()
            {
                display_photo_timestamp();
                initialize_comment_events();
                <?php echo "$('body').css({'background-image': 'url(\'./pictures/default_background.png\')', 'background-position' :'center 50px'});"; ?>
                display_comments();
                change_color();
            });
        </script>
    </head>
    <body>
        <?php include('required_html.php'); ?>
        <div id="top">
                <?php if(isset($_SESSION['page_id']))include('top_page.php'); else include('top.php'); ?>
        </div>
        <div id="main">
            <div id="picture_main" class="box">
                <?php
                if($type=='user')
                {
                    $header_response = get_headers("http://www.redlay.com/users/images/$ID/$picture_id.jpg", 1);
                    if ( strpos( $header_response[0], "404" ) !== true )
                        $path="http://www.redlay.com/users/images/$ID/$picture_id.jpg";
                    else
                    {
                        $header_response = get_headers("http://www.redlay.com/users/images/$ID/$picture_id.png", 1);
                        if ( strpos( $header_response[0], "404" ) !== true )
                            $path="http://www.redlay.com/users/images/$ID/$picture_id.png";
                        else
                        {
                            $header_response = get_headers("http://www.redlay.com/users/images/$ID/$picture_id.gif", 1);
                        if ( strpos( $header_response[0], "404" ) !== true )
                            $path="http://www.redlay.com/users/images/$ID/$picture_id.gif";
                        }
                    }
                }
                else if($type=='page')
                {
                    $header_response = get_headers("http://www.redlay.com/users/pages/$ID/$picture_id.jpg", 1);
                    if ( strpos( $header_response[0], "404" ) !== true )
                        $path="http://www.redlay.com/users/pages/$ID/$picture_id.jpg";
                    else
                    {
                        $header_response = get_headers("http://www.redlay.com/users/pages/$ID/$picture_id.png", 1);
                        if ( strpos( $header_response[0], "404" ) !== true )
                            $path="http://www.redlay.com/users/pages/$ID/$picture_id.png";
                        else
                        {
                            $header_response = get_headers("http://www.redlay.com/users/pages/$ID/$picture_id.gif", 1);
                        if ( strpos( $header_response[0], "404" ) !== true )
                            $path="http://www.redlay.com/users/pages/$ID/$picture_id.gif";
                        }
                    }
                }
                ?>
                <div id="picture_body">
                    <div id="left_arrow" onClick="window.location.replace('http://m.redlay.com/view_photo.php?user_id=<?php echo $ID; ?>&&picture_id=<?php if($type=='user') echo get_previous_photo($ID, $picture_id); else echo get_previous_page_photo($ID, $picture_id); ?>&&type=<?php echo $type; ?>');" ><p id="left_arrow_text"><</p></div>
                    <img id="picture" src="<?php echo $path; ?>" />
                    <div id="right_arrow" onClick="window.location.replace('http://m.redlay.com/view_photo.php?user_id=<?php echo $ID; ?>&&picture_id=<?php if($type=='user') echo get_next_photo($ID, $picture_id); else echo get_next_page_photo($ID, $picture_id); ?>&&type=<?php echo $type; ?>');" ><p id="right_arrow_text">></p></div>
                </div>
                <div id="picture_bottom">
                    <table id="photo_information_table">
                        <tbody>
                            <tr class="view_photo_row">
                                <td><p class="timestamp_status_update" id="picture_timestamp"></p></td>
                            </tr>
                        </tbody>
                    </table>
                    <a href="<?php if($type=='page')echo "http://m.redlay.com/page.php?page_id=$ID"; else echo "http://m.redlay.com/profile.php?user_id=$ID"; ?>"><img class="profile_picture" id="profile_picture_photo" src="<?php if($type=='page') echo 'http://www.redlay.com/users/thumbs/pages/'.$ID.'/0.jpg'; else echo 'http://www.redlay.com/users/thumbs/users/'.$ID.'/0.jpg'; ?>"/></a>
                    <div id="picture_information">
                        <div class="picture_description">
                            <div class="user_name">
                                <a href="<?php if($_SESSION['page_id']!=null)echo "http://m.redlay.com/page.php?page_id=$ID"; else echo "http://m.redlay.com/profile.php?user_id=$ID"; ?>" id="picture_description_name_link">
                                    <span id="picture_description_name" ><?php if($type=='page') echo get_page_name($ID); else echo get_user_name($ID); ?></span>
                                </a>
                            </div>
                            <p id="picture_description_description"><?php if($type=='user') echo get_picture_description($ID, $index); else echo get_page_picture_description($ID, $index); ?></p>
                        </div>
                    </div>
                    <table class="post_functions_photo_table">
                        <tbody>
                            <tr>
                                <td class="post_functions_unit">
                                    <p id="picture_like_text" onClick="like_photo(1);" >    </p>
                                </td>
                                <td class="post_functions_unit">
                                    <p id="picture_dislike_text" onClick="dislike_photo(1);" >    </p>
                                </td>
                                <td class="post_functions_post_comment_unit"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <hr class="break"/>

                <div id="comment">
                    <div id="picture_comments">
                        <textarea class='comment_textarea input_box' onFocus="input_in(this);" onBlur="input_out(this);"  id="comment_picture" placeholder='Comment...' maxlength='500' ></textarea>
                    </div>
                    <div id="comment_body">

                    </div>
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
                            comment();
                            $(this).val('');
                        }
                    });
                }
            </script>
            <?php include('footer.php'); ?>
        </div>
    </body>
</html>