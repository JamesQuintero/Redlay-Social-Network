<?php
    @include('init.php');
    include('universal_functions.php');
    $allowed="pages";
    include('security_checks.php');

    $query=mysql_query("SELECT new_alerts FROM page_alerts WHERE page_id=$_SESSION[page_id] LIMIT 1");
    if($query && mysql_num_rows($query)==1)
    {
        $array=mysql_fetch_row($query);
        $new_alerts=$array[0];
    }
?>


<!DOCTYPE html>
<html>
    <head>
        <title><?php echo $new_alerts; ?> new alert<?php if($new_alerts!=1) echo "s"; ?>!</title>
        <?php include('required_page_header.php'); ?>
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
                $('.box').css('background-color', '<?php echo $box_background_color; ?>');
                $('.alert_text, .alert_timestamp, #company_footer').css('color', '<?php echo $text_color; ?>');
                $('.unread_alert_text, .unread_alert_timestamp').css('color', 'rgb(30,30,30)');
                $('.user_name').css('color', '<?php echo $color; ?>');
                
                $('#alert_body').css('background-color', '<?php echo $box_background_color; ?>');
                    
                $('.title_color').css('color', "<?php echo $color; ?>");
                $('.text_color').css('color', '<?php echo $text_color; ?>');
            }
            
            
            function display_alerts(page)
            {
                $('#alert_load').show();
                var timezone=get_timezone();
                $.post('page_alert_query.php',
                {
                    num:1,
                    timezone:timezone,
                    page:page
                }, function(output)
                {
                    var alert_user_ids=output.alert_user_ids;
                    var alert_timestamps=output.alert_timestamps;
                    var alert_information=output.alert_information;
                    var alerts_read=output.alerts_read;
                    var alert_names=output.alert_names;
                    var alert_ids=output.alert_ids;
                    var total_size=output.total_size;
                    var profile_pictures=output.profile_pictures;
                    var timestamp_seconds=output.timestamp_seconds;
                    var badges=output.badges;
                    var empty=output.empty;
                    


                    if(total_size!=0)
                    {
                        //displays the HTML template that will be used to display current and future posts
                        if(page==1)
                        {
                            $('#alerts_page_body').html('');
                            for(var x = 1; x <= (total_size/10)+1; x++)
                                $('#alerts_page_body').html($('#alerts_page_body').html()+"<table class='alert_page' ><tbody id='page_"+x+"'></tbody></table>");

                            if(total_size<10)
                                $('#alerts_page_body').html("<table class='alert_page' ><tbody id='page_1'></tbody></table>");

                            $('#alerts_page_body').html($('#alerts_page_body').html()+"<div id='see_more_body'></div>");
                        }
                        
                        for(var x = 0; x < alert_user_ids.length; x++)
                        {
                            if(alert_user_ids[x]!=null)
                            {
                                var image="<a href='http://www.redlay.com/profile.php?user_id="+alert_information[x][1]+"'><img id='alert_picture_"+x+"' class='alert_profile_picture' src='"+profile_pictures[x]+"' /></a>";
                                var name="<div class='user_name_body'><a href='http://www.redlay.com/profile.php?user_id="+alert_user_ids[x]+"' class='link'><span class='user_name' id='alert_name_"+x+"' onmouseover=name_over(this); onmouseout=name_out(this);>"+alert_names[x]+"</span></a></div>";
                                if(alert_information[x][0]=='comment')
                                {
                                    var picture="";
                                    var message="commented on your post";
                                    var link="http://www.redlay.com/view_page_post.php?post_id="+alert_information[x][1]+"&&page_id="+alert_information[x][2];
                                }

                                else if(alert_information[x][0]=='like')
                                {
                                    var picture="";
                                    var message="liked your post";
                                    var link="http://www.redlay.com/view_page_post.php?post_id="+alert_information[x][1]+"&&page_id="+alert_information[x][2];
                                }

                                else if(alert_information[x][0]=='page')
                                {
                                    var picture="";
                                    var message="posted on your page";
                                    var link="http://www.redlay.com/page.php?page_id=<?php echo $_SESSION['id']; ?>";
                                }

                                else if(alert_information[x][0]=='accept_add_request')
                                {
                                    var picture="";
                                    var message="has accepted your add request";
                                    var link="http://www.redlay.com/profile.php?user_id="+alert_user_ids[x];
                                }

                                else if(alert_information[x][0]=='picture_comment')
                                {
                                    var picture="<img src='"+alert_information[x][2]+"' class='alert_image'/>";
                                    var message="commented on your picture";
                                    var link="http://www.redlay.com/view_photo.php?user_id=<?php echo $_SESSION['id']; ?>&&picture_id="+alert_information[x][1]+"&&type=user";
                                }

                                else if(alert_information[x][0]=='picture_like')
                                {
                                    var picture="<img src='"+alert_information[x][2]+"' class='alert_image' />";
                                    var message="liked your picture";
                                    var link="http://www.redlay.com/view_photo.php?user_id=<?php echo $_SESSION['id']; ?>&&picture_id="+alert_information[x][1]+"&&type=user";
                                }

                                else if(alert_information[x][0]=='picture_dislike')
                                {
                                    var picture="<img src='"+alert_information[x][2]+"' class='alert_image' />";
                                    var message="disliked your picture";
                                    var link="http://www.redlay.com/view_photo.php?user_id=<?php echo $_SESSION['id']; ?>&&picture_id="+alert_information[x][1]+"&&type=user";
                                }

                                else if(alert_information[x][0]=='comment_same_post')
                                {
                                    var picture="";
                                    var message="commented on a post you commented on";
                                    var link="http://www.redlay.com/view_post.php?post_id="+alert_information[x][1]+"&&profile_id="+alert_information[x][2];
                                }

                                else if(alert_information[x][0]=="liked_comment")
                                {
                                    var picture="";
                                    var message="liked your comment";
                                    var link="http://www.redlay.com/view_post.php?post_id="+alert_information[x][1]+"&&profile_id="+alert_information[x][2];
                                }

                                else if(alert_information[x][0]=="disliked_comment")
                                {
                                    var picture="";
                                    var message="disliked your comment";
                                    var link="http://www.redlay.com/view_post.php?post_id="+alert_information[x][1]+"&&profile_id="+alert_information[x][2];
                                }

                                else if(alert_information[x][0]=='liked_picture_comment')
                                {
                                    var picture="<img src='"+alert_information[x][3]+"' class='alert_image' />";
                                    var message="liked your comment on a picture";
                                    var link="http://www.redlay.com/view_photo.php?user_id="+alert_information[x][2]+"&&picture_id="+alert_information[x][1]+"&&type=user";
                                }

                                else if(alert_information[x][0]=='disliked_picture_comment')
                                {
                                    var picture="<img src='"+alert_information[x][3]+"' class='alert_image' />";
                                    var message="disliked your comment on a picture";
                                    var link="http://www.redlay.com/view_photo.php?user_id="+alert_information[x][2]+"&&picture_id="+alert_information[x][1]+"&&type=user";
                                }

                                else if(alert_information[x][0]=='comment_same_picture')
                                {
                                    var picture="<img src='"+alert_information[x][3]+"' class='alert_image' />";
                                    var message='commented on a picture you commented on';
                                    var link="http://www.redlay.com/view_photo.php?user_id="+alert_information[x][2]+"&&picture_id="+alert_information[x][1]+"&&type=user";
                                }
                                
                                else if(alert_information[x][0]=="video_like")
                                {
                                    var picture="<img class='alert_video' src='"+alert_information[x][3]+"' />";
                                    var message="liked your video";
                                    var link="http://www.redlay.com/view_video.php?video_id="+alert_information[x][2]+"&&user_id="+alert_information[x][1];
                                }
                                
                                else if(alert_information[x][0]=="video_comment")
                                {
                                    var picture="<img class='alert_video' src='"+alert_information[x][3]+"' />";
                                    var message="commented on your video";
                                    var link="http://www.redlay.com/view_video.php?video_id="+alert_information[x][2]+"&&user_id="+alert_information[x][1];
                                }
                                
                                else if(alert_information[x][0]=="video_comment_like")
                                {
                                    var picture="<img class='alert_video' src='"+alert_information[x][3]+"' />";
                                    var message="liked your comment on a video";
                                    var link="http://www.redlay.com/view_video.php?video_id="+alert_information[x][2]+"&&user_id="+alert_information[x][1];
                                }
                                
                                else
                                {
                                    var picture="";
                                    var message='';
                                    var link='';
                                }

                                //sets color of message and timestamp to default black color of alert has not been read
                                if(alerts_read[x]==0)
                                {
                                    var alert_text="<p class='alert_text unread_alert_text' id='unread_text_"+page+"_"+x+"'>"+message+"</p>";
                                    var timestamp="<span class='alert_timestamp unread_alert_timestamp' id='alert_timestamp_"+page+"_"+alert_user_ids[x]+"_"+x+"'>"+alert_timestamps[x]+"</span>";
                                }
                                else
                                {
                                    var alert_text="<p class='alert_text' >"+message+"</p>";
                                    var timestamp="<span class='alert_timestamp' id='alert_timestamp_"+page+"_"+alert_user_ids[x]+"_"+x+"'>"+alert_timestamps[x]+"</span>";
                                }


                                //var alert_break="<hr class='alert_break'/></div>";
    //                            $('#alert_'+x).html(image+name+alert_text+timestamp+alert_break);
                                var functions=get_post_functions("","","",timestamp);

                                //var body="<div id='alert_body_"+page+"_"+x+"' class='status_update'><table style='width:100%;'><tbody><tr id='post_row_1' class='post_row'>  <td class='post_profile_picture_unit'>"+image+"</td><td class='post_body_unit'>"+name+alert_text+"</td>  </tr><tr id='post_row_4' class='post_row'>  <td class='post_timestamp_unit' colspan='2'>"+timestamp+"</td>  </tr></tbody></table>"+alert_break+"</div>";
                                var body=get_post_format(image, name, alert_text+picture, functions, '', '', '', "alert_body_"+page+'_'+x, badges[x]);

                                if(alerts_read[x]==0)
                                    var button="<input class='button red_button' type='button' value='X' id='alert_button_"+page+"_"+x+"' />";
                                else
                                    var button="";
                                
                                $('#page_'+page).html($('#page_'+page).html()+"<tr class='alert_page_row'><td style='width:50px;'>"+button+"</td><td>"+body+"</td></tr>");
                            }
                            
                            
                            
                            

                            //sets alert's background color to lightgray if alert has not be read
                            if(alerts_read[x]==0)
                            {
                                $('#alert_body_'+page+'_'+x).attr('onClick', "alert_read("+alert_ids[x]+", '"+link+"', "+page+", "+x+", "+alert_user_ids[x]+", false);").css({'background-color': 'lightgray', 'cursor': "pointer"});
                                $('#alert_button_'+page+'_'+x).attr('onClick', "alert_read("+alert_ids[x]+", '"+link+"', "+page+", "+x+", "+alert_user_ids[x]+", true);");
                            }
                            else
                                $('#alert_body_'+page+'_'+x).attr('onClick', "window.location.replace('"+link+"');").css('cursor', 'pointer');
                        }
                        
                        for(var x = 0; x < alert_user_ids.length; x++)
                        {
                            if(alert_user_ids[x]!=null)
                            {
                                count_time(timestamp_seconds[x], '#alert_timestamp_'+page+'_'+alert_user_ids[x]+'_'+x);
                            }
                        }
                        
                        //adds or modifies see more button
                        if(empty==false&&page==1)
                        {
                            $('#see_more_body').html($('#see_more_body').html()+"<input class='button see_more_posts blue_button' value='See More' type='button'>");
                            $('.see_more_posts').attr({'onmouseover': "{display_title(this, 'See more posts');}", 'onmouseout': "{hide_title(this);}", 'onClick': "display_alerts("+(page+1)+");"});
                        }
                        else if(empty==true)
                            $('.see_more_posts').hide();
                        else
                            $('.see_more_posts').attr('onClick', "display_alerts("+(page+1)+");");
                    }
                    else
                        $('#alerts_page_body').html("<p class='no_alerts'>You do not have any alerts</p>");
                        
                    change_color();
                    $('#alert_load').hide();
                }, "json");
            }
            
            
            
            
            

//            function toggle_delete_checkbox(num)
//            {
//                if($('#delete_checkbox_'+num).attr('src')=='http://pics.redlay.com/pictures/gray_checkbox.png')
//                    $('#delete_checkbox_'+num).attr('src', 'http://pics.redlay.com/pictures/gray_checkbox_checked.png');
//                else
//                    $('#delete_checkbox_'+num).attr('src', 'http://pics.redlay.com/pictures/gray_checkbox.png');
//            }
//            function delete_alerts()
//            {
//                //gets the checked checkboxes and their values
//               var alerts=new Array();
//               var num=0;
//               var num2=0;
//               while($('#delete_checkbox_'+num2).length)
//               {
//                  if($('#delete_checkbox_'+num2).attr('src')=='http://pics.redlay.com/pictures/gray_checkbox_checked.png')
//                  {
//                        alerts[num]=$('#delete_checkbox_'+num2).data('alert_id');
//                        num++;
//                  }
//                  num2++;
//               }
//
//
//                $.post('alert_page_query.php',
//                {
//                    num:3,
//                    alerts: alerts
//                }, function(output)
//                {
//                    if(output=='Alerts deleted')
//                        display_alerts(1);
//                    else
//                        display_error(output, "bad_errors");
//                });
//            }
            function delete_all_alerts()
            {
                $.post('alert_page_query.php',
                {
                    num:3
                }, function(output)
                {
                    display_alerts(1);
                });
            }
            function alert_read(alert_id, link, page, index, user_id, button)
            {
                $.post('alert_page_query.php',
                {
                    num:2,
                    alert_id: alert_id
                }, function(output)
                {
                    if(button==false)
                        window.location.replace(link);
                    else
                    {
                        $('#alert_body_'+page+'_'+index).css('background', '');
                        $('#unread_text_'+page+'_'+index).attr('class', "alert_text");
                        $('#alert_timestamp_'+page+'_'+user_id+'_'+index).attr('class', "alert_timestamp");
                        $('#alert_button_'+page+'_'+index).hide();
                        change_color();
                    }
                });
            }
        </script>
        <script type="text/javascript">
            $(window).ready(function()
            {
                display_alerts(1);
                change_color();
                $('#menu').hide();
                $('#footer').css('width', '910px');
                <?php include('required_jquery.php'); ?>
            });
        </script>
    </head>
    <body>
        <?php include('required_html.php'); ?>
        <div id="top">
                <?php include('top.php'); ?>
        </div>
        <div id="main">
            <?php include('required_side_html.php'); ?>
            <div id="alert_body" class="content">
                <p class="title title_color" id="alerts_title">Alerts</p>
                <div style="width:500px;text-align:center;position:relative;margin:0 auto;">
                    <p class="text_color">Gray alerts are new alerts.</p>
                </div>
                <hr />
                <input class="button red_button" type="button" value="Delete all" onClick="delete_all_alerts();" style="margin-left:15px;position:relative;"/>
                <div id="alert_load"><img class="load_gif" src='http://pics.redlay.com/pictures/load.gif'/></div>
                <div id="alerts_page_body" style="padding:15px;">
                    
                    
                    
                </div>
                <?php include('footer.php'); ?>
            </div>
        </div>
    </body>
</html>
