<?php
@include('init.php');

//redirects to mobile version if user is using phone
//include('mobile_device_detect.php');

if(!isset($_SESSION['id']))
{
    if(!isset($_SESSION['page_id']))
    {
        if(isset($_COOKIE['acc_id']))
        {
            $query=mysql_query("SELECT id FROM users WHERE account_id='$_COOKIE[acc_id]' LIMIT 1");
            if($query&&mysql_num_rows($query)==1)
            {
                $array=mysql_fetch_row($query);

                $_SESSION['id']=$array[0];
                header("Location: http://www.redlay.com/home.php");
                exit();
            }
        }
        else if(isset($_COOKIE['acc_page']))
        {
            $query=mysql_query("SELECT id FROM pages WHERE account_id='$_COOKIE[acc_page]' LIMIT 1");
            if($query&&mysql_num_rows($query)==1)
            {
                $array=mysql_fetch_row($query);

                $_SESSION['page_id']=$array[0];
                header("Location: http://www.redlay.com/page.php?page_id=".$_SESSION[page_id]);
                exit();
            }
        }
    }
    else
    {
        header("Location: http://www.redlay.com/page.php?page_id=$_SESSION[page_id]");
        exit();
    }
}
else
{
    header("Location: http://www.redlay.com/home.php");
    exit();
}

include('universal_functions.php');


?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta name="description" content=" Redlay is a social network where you can express yourself and hang out with the people you care about. You get many features from other social networking sites, but without the complexity and privacy issues." />
        <meta name="keywords" content="social network,account,online,easier,redlay,red,lay,red lay" />
        <title>Redlay</title>
        <script type="text/javascript">
            startTime = (new Date).getTime();
        </script>
        <?php include('required_header.php'); ?>
        <link rel="shortcut icon" type="image/x-icon" href="favicon.ico" />
        <script type="text/javascript">
            function change_color()
            {
                $('.box').css({'border': '5px solid rgb(220,21,0)', 'background-color': 'white'});
                $('.page_login_text').css('color', 'rgb(220,21,0)');
                $('#page_login_email_input').css('outline-color', '#dc1500');
                $('#page_login_password_1_input').css('outline-color', '#dc1500');
                $('#page_login_password_2_input').css('outline-color', '#dc1500');
                $('#login_submitButton').css('box-shadow', '0px 0px 0px gray');
                $('#page_login_submit').css('box-shadow', '0px 0px 0px gray');
                $('body').css({"background-image": "url('<?php echo get_default_background_pic($redlay_theme); ?>')", "background-position" :"center 50px"<?php if($redlay_theme=="white") echo ", 'background-color': 'whitesmoke'"; ?>});
                $('.page_register_text').css('color', 'black');
                
                $('.title_color').css('color', 'rgb(220,20,0)');
                $('.text_color').css('color', 'rgb(30,30,30)');
                
                $('.post_body').css('min-height', '0px');
            }
            function animate_logins(num)
            {
                if(num==1)
                {
                    $('#index_main_body').stop().animate({
                        marginLeft: '-300px'
                    }, 500, function()
                    {});
                    $('#page_login_body').stop().animate({
                        marginLeft: '250px'
                    }, 500, function()
                    {});
                    $('.index_arrow').attr('onClick', 'animate_logins(2);');
                    $(".index_arrow_image").css({"-webkit-transform":"rotate(0deg)", "-moz-transform": "rotate(0deg)"});
                }
                else if(num==2)
                {
//                    $('#index_main_body').stop().animate({
//                        marginLeft: '250px'
//                    }, 500, function()
//                    {});
//                    $('#page_login_body').stop().animate({
//                        marginLeft: '840px'
//                    }, 500, function()
//                    {});
//                    $('.index_arrow').attr('onClick', 'animate_logins(1);');
//                    $(".index_arrow_image").css({"-webkit-transform":"rotate(180deg)", "-moz-transform": "rotate(180deg)"});
                }
            }
            
            function register()
            {
                $.post('RegisterAfter.php',
                {
                    firstName: $('#first_name_input').val(),
                    lastName: $('#last_name_input').val(),
                    password: $('#password_input').val(),
                    email: $('#email_input').val()
                }, function (output)
                {
                    if(output=='Email has been sent! Email may be in spam folder')
                        display_error(output, "good_errors");
                    else
                        display_error(output, 'bad_errors');
                });
            }
            function resend_email()
            {
                if($('#resent_email_input').val()!='')
                {
                    $.post('resend_verification_email.php',
                    {
                        email: $('#resend_email_input').val()
                    }, function(output)
                    {
                        if(output=="Success")
                        {
                            display_error("Email sent!", 'good_errors');
                            close_alert_box();
                        }
                        else
                            display_error(output, "bad_errors");
                    });
                }
                else
                    display_error("Email field is empty", 'bad_errors');
            }
            
            
            function display_resend_email_menu()
            {
                var title="Resend email";
                var body="<input type='email' class='input_box' id='resend_email_input' placeholder='Email' style='position:relative;margin:0 auto;' onFocus=input_in(this); onBlur=input_out(this); />";
                var extra_id='';
                var load_id='resend_load_gif';
                var confirm="<input class='button red_button' type='button' id='resend_verification_button' onClick='resend_email();' value='Send' />";
                display_alert(title, body, extra_id, load_id, confirm);
                
                $('#resend_load_gif').hide();
            }
            function login_form(num)
            {
                if(num==1)
                {
                    $('#login_arrow_body').attr('onClick', "login_form(2)");
                    $('#login_arrow_text').html('<');
                    
                    $('#page_login_box').stop().animate({
                        left:580
                    }, 100, function()
                    {

                    });
                    $('#login_box').stop().animate({
                        left:0
                    }, 250, function()
                    {
                        
                    });
                }
                else if(num==2)
                {
                    $('#login_arrow_body').attr('onClick', "login_form(1)");
                    $('#login_arrow_text').html('>');
                    
                    $('#login_box').stop().animate({
                        left:-530
                    }, 100, function()
                    {

                    });
                    $('#page_login_box').stop().animate({
                        left:0
                    }, 250, function()
                    {
                        
                    });
                }
            }
            $(document).ready(function()
            {
                <?php 
                    if( strstr($_SERVER['HTTP_USER_AGENT'],'Android') ||
                        strstr($_SERVER['HTTP_USER_AGENT'],'iPhone') ||
                        strstr($_SERVER['HTTP_USER_AGENT'],'iPod') ||
                        strstr($_SERVER['HTTP_USER_AGENT'],'BlackBerry')
                        )

                    {
                           echo "$('.version_selection').show();";
                           
                    }
                    else
                    {
                        echo "$('.version_selection').hide();";
                        echo "$('#desktop_version').show();";
                    }
                ?>
                        
                <?php 
                    if(is_internet_explorer())
                        echo "$('#browser_error').html('<p style=\'text-align:center;\'>Oh no! You are using internet explorer! Switch to <a class=\'link\' href=\'http://www.google.com/chrome\'><span style=\'cursor:pointer;color:blue;text-decoration:underline;\' >Chrome</span></a>! Redlay might not work as you would expect with Internet Explorer.</p>');";  ?>
                //display_public_users();
                change_color();
                $('#resend_form_row').hide();
                $('#menu').hide();
                setTimeout(function()
                {
                    $('#login_form_body').css('position', 'absolute');
                    $('#login_form_body').css('position', 'relative');
                }, 100);
                <?php include('required_jquery.php'); ?>
                    
                var page_load=(new Date).getTime() - startTime;
                record_page_load_time('index', page_load);

            });
        </script>
        <script type="text/javascript">
            <?php include('required_google_analytics.js'); ?>
        </script>
    </head>
    <body>
        <?php include('required_html.php'); ?>
<!--        <div style="width:100%;height:100%;background-color:white;position:absolute;top:0px;left:0px;z-index:99999;display:none" class="version_selection">
            <table style="text-align:center;position:absolute;width:100%" >
                <tbody>
                    <tr>
                        <td><p style="font-size:50px">It seems you are on a mobile device:</p></td>
                    </tr>
                    <tr>
                        <td>
                            <input type="button" class="button red_button" value="Mobile Version" style="font-size:50px;border-radius:10px;" onClick="{window.location.replace('http://m.redlay.com');}"/>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="button" class="button red_button" value="Desktop Version" style="font-size:50px;border-radius:10px;" onClick="{$('.version_selection').hide();$('#desktop_version').show();$('#top').css('position', 'relative');$('.header_background').css('position', 'absolute');}"/>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>-->
        <?php include('index_top.php'); ?>
        <div id="desktop_version" style="display:block;">
            <div id="main">
                <div id="index_main" >
                    <table style="margin:0 auto;">
                        <tbody>
                            <tr>
                                <td>
                                    <div id="browser_error"></div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    
                                    <table style="margin:20px;border-bottom:1px solid gray" >
                                        <tbody>
                                            <tr>
                                                <td style="width:50%;vertical-align:top;border-right:1px solid gray;" >
                                                    
                                                    
                                                    <div style="position:relative;padding:10px;" id="description_box" >
                                                        <p class="text_color" style="text-align:left;margin:5px;margin-bottom:10px;">Redlay is a social network where you can express yourself and hang out with the people you care about.</p>
<!--                                                        <table>
                                                            <tbody>
                                                                <tr>
                                                                    <td colspan="3">
                                                                        <p style="margin:0px;">How is Redlay better?</p>
                                                                        <p style="margin:0px;"><span style="color:rgb(220,20,0);">1)</span> Customizable profile</p>
                                                                        <p style="margin:0px;"><span style="color:rgb(220,20,0);">2)</span> Dislike button</p>
                                                                        <p style="margin:0px;"><span style="color:rgb(220,20,0);">3)</span> Full control privacy settings</p>
                                                                        <p style="margin:0px;"><span style="color:rgb(220,20,0);">4)</span> Public (live feed of posts and photos)</p>
                                                                        <p style="margin:0px;"><span style="color:rgb(220,20,0);">5)</span> No ads</p>
                                                                        <p style="margin:0px;font-weight:bold;"><span style="color:rgb(220,20,0);">6)</span> We won't sell your information!</p>
                                                                        <a class="link" href="http://www.redlay.com/about.php"><p style="margin:0px;color:rgb(220,20,0);" >and more...</p></a>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>-->
                                                    </div>
                                                    <div style="position:relative;padding:10px;" id="picture_box">
<!--                                                        <img id="index_picture_1" class="index_picture" src="http://pics.redlay.com/pictures/index_picture_3.png" style="position:relative;margin:0 auto;width:100%;height:auto;"/>-->
                                                        <img id="index_picture_1" class="index_picture" src="http://pics.redlay.com/pictures/index_picture_4.png" style="position:relative;margin:0 auto;width:100%;height:auto;border:1px solid rgb(150,150,150);box-shadow:1px 1px 3px gray;"/>
                                                    </div>
                                                    
                                                    
                                                </td>
                                                <td style="width:50%;" >
                                                    
                                                    <table style="width:100%;">
                                                        <tbody>
                                                            <tr>
                                                                <td style="position:relative;" id="register_box">
                                                                    <table id ="register_table" style="margin-left:0px;margin:0 auto;">
                                                                        <tr class="register_row" id="register_row_1">
                                                                            <td align="center"><p id="register_title" style="margin-top:0px;text-decoration:none;">Create</p></td>
                                                                        </tr>
                                                                        <tr class="register_row" id="register_row_2" style="text-align:center;">
                                                                            <td class="register_input_unit"><input style="border-radius:3px;" class="index_input" id="first_name_input" type="text" maxlength ="20" autocomplete="off" placeholder="First Name" onFocus="{input_in(this);}" onBlur="{$(this).css({'border': '1px solid gray', 'box-shadow': 'inset 0px 0px 5px 0px gray'});input_blur(this, 'First Name');}"/></td>
                                                                        </tr>
                                                                        <tr class="register_row" id="register_row_3" style="text-align:center;">
                                                                            <td class="register_input_unit"><input style="border-radius:3px;" class="index_input" id="last_name_input" type="text" maxlength ="20" autocomplete="off" placeholder="Last Name" onFocus="{input_in(this);input_focus(this);}" onBlur="{$(this).css({'border': '1px solid gray', 'box-shadow': 'inset 0px 0px 5px 0px gray'});input_blur(this, 'Last Name');}"/></td>
                                                                        </tr>
                                                                        <tr class="register_row" id="register_row_4" style="text-align:center;">
                                                                            <td class="register_input_unit" id="password_unit"><input style="border-radius:3px;" class="index_input" id="password_input"  type="password" placeholder="Password" onFocus="{input_in(this);input_focus_password(this);}" onBlur="{$(this).css({'border': '1px solid gray', 'box-shadow': 'inset 0px 0px 5px 0px gray'});input_blur_password(this, 'Password');}"/></td>
                                                                        </tr>
                                                                        <tr class="register_row" id="register_row_6" style="text-align:center;">
                                                                            <td class="register_input_unit"><input style="border-radius:3px;" class="index_input " id="email_input" type="email" maxlength="255" autocomplete="off" placeholder="Email" onFocus="{input_in(this);input_focus(this);}" onBlur="{$(this).css({'border': '1px solid gray', 'box-shadow': 'inset 0px 0px 5px 0px gray'});input_blur(this, 'Email');}"/></td>
                                                                        </tr>
                                                                        <tr class="register_row" id="register_row_6">
                                                                            <td class="register_title_unit" style="text-align:center;"><p style="margin:0px;font-size:14px;" >By clicking create, you agree to the </p><p style="margin-top:0px;font-size:14px;"><span style="cursor:pointer;color:rgb(220,20,0);" onmouseover="name_over(this);" onmouseout="name_out(this);" onClick="window.open('http://www.redlay.com/user_agreement.php');">User Agreement</span></p></td>
                                                                        </tr>
                                                                        <tr class="register_row" id="register_row_8">
                                                                            <td colspan="2" ><hr /></td>
                                                                        </tr>
                                                                        <tr class="register_row" id="register_row_8">
                                                                            <td id="register_button" colspan="3" align="center"><input style="font-size:20px;padding-top:10px;padding-bottom:10px;" class="button red_button" id="register_submit" type="button" value="Create" onClick="register();" /></td>
                                                                        </tr>
                                                                        <tr class="register_row" id="register_row_9">
                                                                            <td id="register_button" colspan="3" align="center"><p class="title_color" style="margin:0px;cursor:pointer;" onClick="display_resend_email_menu();" onmouseover="name_over(this);" onmouseout="name_out(this);" >Resend email</p></td>
                                                                        </tr>
        <!--                                                                <tr class="register_row" id="register_row_9">
                                                                            <td colspan="2" align="center"><p id="register_no_email_recieved" style="cursor:pointer;text-align:center;margin:0px;" onClick="$('#resend_email_information').show(); $(this).hide();" onmouseover="name_over(this);" onmouseout="name_out(this);">Didn't receive email?</p></td>
                                                                        </tr>--> 
                                                                    </table>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <div style="position:relative;height:275px;overflow:auto;border:1px solid gray;box-shadow:inset 0px 0px 10px rgb(220,220,220);display:none;" id="public_box">
                                                                        
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                    
                                                    
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    
                                </td>
                            </tr><tr><td><table style="margin:0 auto;width:100%;position:relative;border-spacing:15px;padding:20px;" >
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <table style="border-spacing:0px;">
                                                        <tbody>
                                                            <tr>
                                                                <td>
                                                                    <p class="text_color" style="margin:0px;text-align:center;font-size:12px;">Before</p>
                                                                    <img class="index_about_picture" src="https://s3.amazonaws.com/redlay.pictures/pictures/index/grant_customize_profile_before.png"/>
                                                                </td>
                                                                <td>
                                                                    <p class="text_color" style="margin:0px;text-align:center;font-size:12px;">After</p>
                                                                    <img class="index_about_picture" src="https://s3.amazonaws.com/redlay.pictures/pictures/index/grant_customize_profile_after.png"/>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                                <td style="vertical-align:top;">
                                                    <p class="title_color" style="margin:0px;">Customizable profile</p>
                                                    <p class="paragraph text_color" style="font-size:14px;margin:0px;">You have the option of customizing your profile how you like! You can change the main text color, regular text color, background color, and the transparency of it all. You also have the ability to upload a background image</p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="2">
                                                    <hr class="break" />
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <img class="index_about_picture" src="https://s3.amazonaws.com/redlay.pictures/pictures/index/photo_in_post.png" style="max-width:none;width:350px;"/>
                                                </td>
                                                <td style="vertical-align:top;">
                                                    <p class="title_color" style="margin:0px;">Photos/gifs in comments</p>
                                                    <p class="text_color" style="font-size:14px;margin:0px;">You have the option of sharing a relevent photo or gif in the comments or posts! </p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="2">
                                                    <hr class="break" />
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <table style="border-spacing:0px;">
                                                        <tbody>
                                                            <tr>
                                                                <td>
                                                                    <p class="text_color" style="margin:0px;text-align:center;font-size:12px;">Light</p>
                                                                    <img class="index_about_picture" src="https://s3.amazonaws.com/redlay.pictures/pictures/index/site_themes_1.png"/>
                                                                </td>
                                                                <td>
                                                                    <p class="text_color" style="margin:0px;text-align:center;font-size:12px;">Beach</p>
                                                                    <img class="index_about_picture" src="https://s3.amazonaws.com/redlay.pictures/pictures/index/site_themes_2.png"/>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                                <td style="vertical-align:top;">
                                                    <p class="title_color" style="margin:0px;">Site Themes</p>
                                                    <p class="text_color" style="font-size:14px;margin:0px;">You have the option of having a theme while you browse Redlay. Themes: light, dark, aluminum, neon, beach, and custom theme.</p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="2">
                                                    <hr class="break" />
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <img class="index_about_picture" src="https://s3.amazonaws.com/redlay.pictures/pictures/index/dislike_button.png"/>
                                                </td>
                                                <td style="vertical-align:top;">
                                                    <p class="title_color" style="margin:0px;">Dislike Button</p>
                                                    <p class="text_color" style="font-size:14px;margin:0px;">Dislike button! Enough said.</p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="2">
                                                    <hr class="break" />
                                                </td>
                                            </tr>
<!--                                            <tr>
                                                <td>
                                                    <img class="index_about_picture" src="https://s3.amazonaws.com/redlay.pictures/pictures/index/redlay_points.png"/>
                                                </td>
                                                <td style="vertical-align:top;">
                                                    <p class="title_color" style="margin:0px;">Points</p>
                                                    <p class="text_color" style="font-size:14px;margin:0px;">The amount of points you have shows how popular you are! You get a point when you get a like on anything. The more points you have, the higher you rank!</p>
                                                </td>
                                            </tr>-->
                                            <tr>
                                                <td>
                                                    <img class="index_about_picture" src="https://s3.amazonaws.com/redlay.pictures/pictures/index/set_photo_thumbnail.png"/>
                                                </td>=
                                                <td style="vertical-align:top;">
                                                    <p class="title_color" style="margin:0px;">Custom photo thumbnail</p>
                                                    <p class="text_color" style="font-size:14px;margin:0px;">You have the ability to set the thumbnail for the photos you upload! This is a great feature if the focus of the photo is in a specific part.</p>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    
                    
                    
                    
                    
                    
                    <?php include('footer.php'); ?>

                    <!--<div class="fb-like" id="facebook_like" data-href="https://www.facebook.com/pages/Redlay/350603014953791" data-send="true" data-layout="button_count" data-width="450" data-show-faces="true" data-font="lucida grande"></div>
                    <!--<div id="arrow_right" class="index_arrow" onClick="animate_logins(1);" onmouseover="index_arrow_functions(this, 1);" onmouseout="index_arrow_functions(this, 2);" onmousedown="index_arrow_functions(this, 3);" onmouseup="index_arrow_functions(this, 4);"><img src="http://pics.redlay.com/pictures/index_right_arrow.png" class="index_arrow_image"/></div>-->
                </div>
            </div>
        </div>
    </body>
    <script type="text/javascript">
        $(document).ready(function()
        {
            //$('#login_password_text_box').unbind('keypress');
            $('#login_password_text_box').keydown(function(e)
            {
                var key = (e.keyCode ? e.keyCode : e.which);
                if(key == '13')
                    login();
            });
            //register
            $('#email_input').keydown(function(e)
            {
                var key = (e.keyCode ? e.keyCode : e.which);
                if(key == '13')
                    register();
            });
            $('#page_login_password_1_input').keypress(function(e)
            {
                var key = (e.keyCode ? e.keyCode : e.which);
                if(key == '13')
                    page_login();
            });
        });
    </script>
</html>