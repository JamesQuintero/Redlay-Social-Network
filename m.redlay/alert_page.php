<?php
@include('init.php');
include('../universal_functions.php');
$allowed="users"
include('security_checks.php');

$query=mysql_query("SELECT new_alerts FROM alerts WHERE user_id=$_SESSION[id] LIMIT 1");
if($query&&mysql_num_rows($query)==1)
{
    $array=mysql_fetch_row($query);
    $alerts=$array[0];
}
?>

<!DOCTYPE html>
<html>
    <head>
        <title><?php echo $alerts; ?> new alerts</title>
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
                $('.box').css('border', '15px solid <?php echo $color; ?>');
                $('.box').css({'background-color': '<?php echo $box_background_color; ?>'});
                
                
                $('.title_color').css('color', '<?php echo $color; ?>');
                $('.text_color').css('color', '<?php echo $text_color; ?>');
            }
            
            
            function display_alerts(page)
            {
                var timezone=get_timezone();
                $.post('main_access.php',
                {
                    access:39,
                    num:1,
                    timezone:timezone,
                    page: page
                }, function(output)
                {
                    var alert_user_ids=output.alert_user_ids;
                    var alert_timestamps=output.alert_timestamps;
                    var alert_information=output.alert_information;
                    var alerts_read=output.alerts_read;
                    var alert_names=output.alert_names;
                    var alert_ids=output.alert_ids;
                    var total_size=output.total_size;
                    var empty=output.empty;


                    if(total_size!=0)
                    {
                        //displays the HTML template that will be used to display current and future posts
                        if(page==1)
                        {
                            $('#alerts_table_body').html('');
                            for(var x = 1; x <= (total_size/10)+1; x++)
                                $('#alerts_table_body').html($('#alerts_table_body').html()+"<table class='alert_page' ><tbody id='page_"+x+"'></tbody></table>");

                            if(total_size<10)
                                $('#alerts_table_body').html("<table class='alert_page' ><tbody id='page_1'></tbody></table>");

                            $('#alerts_table_body').html($('#alerts_table_body').html()+"<div id='see_more_body'></div>");
                        }
                        
                        
                        $('#alerts_table_body').html('');
                        for(var x = 0; x < alert_user_ids.length; x++)
                        {
                            var image="<a href='http://www.redlay.com/profile.php?user_id="+alert_information[x][1]+"'><img id='alert_picture_"+x+"' class='profile_picture profile_picture_status' src='http://www.redlay.com/users/thumbs/users/"+alert_user_ids[x]+"/0.jpg' /></a>";
                            var name="<div class='user_name_body'><a href='http://www.redlay.com/profile.php?user_id="+alert_user_ids[x]+"' class='user_name_link'><p class='user_name' id='alert_name_"+x+"' onmouseover=name_over(this); onmouseout=name_out(this);>"+alert_names[x]+"</p></a></div>";
                            if(alert_information[x][0]=='comment')
                            {
                                var message="commented on your post";
                                var link="http://www.redlay.com/view_post.php?post_id="+alert_information[x][1]+"&&profile_id="+alert_information[x][2];
                            }

                            else if(alert_information[x][0]=='like')
                            {
                                var message="liked your post";
                                var link="http://www.redlay.com/view_post.php?post_id="+alert_information[x][1]+"&&profile_id="+alert_information[x][2];
                            }

                            else if(alert_information[x][0]=='profile')
                            {
                                var message="posted on your profile";
                                var link="http://www.redlay.com/profile.php?user_id=<?php echo $_SESSION['id']; ?>";
                            }

                            else if(alert_information[x][0]=='dislike')
                            {
                                var message="disliked your post";
                                var link="http://www.redlay.com/view_post.php?post_id="+alert_information[x][1]+"&&profile_id="+alert_information[x][2];
                            }

                            else if(alert_information[x][0]=='accept_add_request')
                            {
                                var message="has accepted your add request";
                                var link="http://www.redlay.com/profile.php?user_id="+alert_user_ids[x];
                            }

                            else if(alert_information[x][0]=='picture_comment')
                            {
                                var message="commented on your picture";
                                var link="http://www.redlay.com/view_photo.php?user_id=<?php echo $_SESSION['id']; ?>&&picture_id="+alert_information[x][1]+"&&type=user";
                            }

                            else if(alert_information[x][0]=='picture_like')
                            {
                                var message="liked your picture";
                                var link="http://www.redlay.com/view_photo.php?user_id=<?php echo $_SESSION['id']; ?>&&picture_id="+alert_information[x][1]+"&&type=user";
                            }

                            else if(alert_information[x][0]=='picture_dislike')
                            {
                                var message="disliked your picture";
                                var link="http://www.redlay.com/view_photo.php?user_id=<?php echo $_SESSION['id']; ?>&&picture_id="+alert_information[x][1]+"&&type=user";
                            }

                            else if(alert_information[x][0]=='comment_same_post')
                            {
                                var message="commented on a post you commented on";
                                var link="http://www.redlay.com/view_post.php?post_id="+alert_information[x][1]+"&&profile_id="+alert_information[x][2];
                            }

                            else if(alert_information[x][0]=="liked_comment")
                            {
                                var message="liked your comment";
                                var link="http://www.redlay.com/view_post.php?post_id="+alert_information[x][1]+"&&profile_id="+alert_information[x][2];
                            }

                            else if(alert_information[x][0]=="disliked_comment")
                            {
                                var message="disliked your comment";
                                var link="http://www.redlay.com/view_post.php?post_id="+alert_information[x][1]+"&&profile_id="+alert_information[x][2];
                            }

                                else if(alert_information[x][0]=='liked_picture_comment')
                                {
                                    var message="liked your comment on a picture";
                                    var link="http://www.redlay.com/view_photo.php?user_id="+alert_information[x][2]+"&&picture_id="+alert_information[x][1]+"&&type=user";
                                }

                                else if(alert_information[x][0]=='disliked_picture_comment')
                                {
                                    var message="disliked your comment on a picture";
                                    var link="http://www.redlay.com/view_photo.php?user_id="+alert_information[x][2]+"&&picture_id="+alert_information[x][1]+"&&type=user";
                                }

                            else if(alert_information[x][0]=='comment_same_picture')
                            {
                                var message='commented on a picture you commented on';
                                var link="http://www.redlay.com/view_photo.php?user_id="+alert_information[x][2]+"&&picture_id="+alert_information[x][1]+"&&type=user";
                            }
                            else
                            {
                                var message='';
                                var link='';
                            }

                            //sets color of message and timestamp to default black color of alert has not been read
                            if(alerts_read[x]==0)
                            {
                                var alert_text="<p class='status_update_text alert_text unread_alert_text' >"+message+"</p>";
                                var timestamp="<p class='alert_timestamp unread_alert_timestamp'>"+alert_timestamps[x]+"</p>";
                            }
                            else
                            {
                                var alert_text="<p class='alert_text' >"+message+"</p>";
                                var timestamp="<p class='alert_timestamp'>"+alert_timestamps[x]+"</p>";
                            }
                            
                            var body="<tr><td>"+get_post_format(image, name, message, '', timestamp, '', '', 'alert_'+x)+"</td></tr>"+body;


                            //sets alert's background color to lightgray if alert has not be read
                            if(alerts_read[x]==0)
                                $('#alert_body_'+page+'_'+x).attr('onClick', "alert_read("+alert_ids[x]+", '"+link+"');").css('background-color', 'lightgray');
                            else
                                $('#alert_body_'+page+'_'+x).attr('onClick', "window.location.replace('"+link+"')");
                        }
                        
                        $('#page_'+page).html(body);
                        
                        for(var x =0; x < alert_user_ids.length; x++)
                        {
                            $('#alert_'+x).html("<a class='link' href='"+link+"'>"+$('#alert_'+x).html()+"</a>");
                            $('#delete_checkbox_'+x).data('alert_id', alert_ids[x]);
                        }
                        
                        
                    }
                    else
                        $('#alerts_content').html("<p class='no_alerts text_color'>You do not have any alerts</p>");
                    change_color();
                    $('#post_load').hide();
                }, "json");
            }
            
            
            
            
            $(document).ready(function()
            {
                $('#menu').hide();
                display_alerts(1);
                <?php include('required_jquery.php'); ?>
                <?php echo "$('body').css({'background-image': 'url(\'./pictures/default_background.png\')', 'background-position' :'center 50px'});"; ?>
                change_color();
            });
        </script>
        <script type="text/javascript">
            <?php include('../required_google_analytics.js'); ?>
        </script>
    </head>
    <body>
        <?php include('required_html.php'); ?>
        <?php include('top.php'); ?>
        <div id="main">
            <div id="alerts_content" class="box">
                <table id="alerts_table" style="padding:50px;">
                    <tbody id="alerts_table_body">
                        
                    </tbody>
                </table>
            </div>
            <?php include('footer.php'); ?>
        </div>
    </body>
</html>