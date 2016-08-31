<?php
@include('init.php');
include('universal_functions.php');
$allowed = "all";
include('security_checks.php');

if(isset($_SESSION['id'])||isset($_SESSION['page_id']))
{
    header("Location: http://www.redlay.com");
    exit();
}

?>

<!DOCTYPE html>
<html>
    <head>
        <title>Page Create</title>
        <?php include('required_header.php'); ?>
        <script type="text/javascript">
            function change_color()
            {
                <?php
                    if (isset($_SESSION['id']))
                    {
                        $colors = get_user_display_colors($_SESSION['id']);
                        $color = $colors[0];
                        $box_background_color = $colors[1];
                        $text_color = $colors[2];
                    } else {
                        $color = "rgb(220,20,0)";
                        $box_background_color = "white";
                        $text_color = "rgb(30,30,30)";
                    }
                ?>
                        
                $('.box').css('border', '5px solid <?php echo $color; ?>');
                $('.box').css({'background-color': '<?php echo $box_background_color; ?>'});
                
                $('.title_color').css('color', '<?php echo $color; ?>');
                $('.text_color').css('color', '<?php echo $text_color; ?>');
            }
            
            function page_login()
            {
                $.post('page_login.php',
                {
                    email: $('#page_login_email_input').val(),
                    password: $('#page_login_password_input').val()
                },
                function (output)
                {
                    console.log(output);
                    if(output=='Logged in')
                        window.location.replace(window.location);
                    else
                       display_error(output, 'bad_errors');
                });
            }

            $(document).ready(function()
            {
                $('#footer').css('width', '900px');
                change_color();
                <?php include('required_jquery.php'); ?>
            });
        </script>
        <script type="text/javascript">
            <?php include('required_google_analytics.js'); ?>
        </script>
    </head>
    <body>
        <?php include('required_html.php'); ?>
        <?php include('index_top.php'); ?>
        <div id="main">
            <?php include('required_side_html.php'); ?>
            <div class="content box">
                <table style="width:100%">
                    <tr>
                        <td colspan="2" style="text-align:center;">
                            <div id="browser_error" style="width:100%;margin:0 auto;"></div>
                        </td>
                    </tr>
                    <tr>
                        <td style="width:50%;border-right:1px solid gray;vertical-align:top;">
                            <table style="margin:0 auto;">
                                <tbody>
                                    <tr>
                                        <td>
                                            <p class="title title_color">Page Login</p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <input autofocus="" id="page_login_email_input" class="input_box" type="email" placeholder="Email" onfocus="{input_in(this);}" onblur="input_out(this);" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <input id="page_login_password_input" type="password" class="input_box" placeholder="Password" onfocus="{input_in(this); }" onblur="{input_out(this);}" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="text-align:center;">
                                            <input id="page_login_button" type="button" value="Log In" class="button red_button" onclick="page_login();"  />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <a class="link" href="http://www.redlay.com/recover_password.php?type=page"><p id="forgot_password_page" class="forgot_password title_color" onmouseover="name_over(this);" onmouseout="name_out(this);">Forgot password?</p></a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                </table>
            </div>
            <?php include('footer.php'); ?>
        </div>
    </body>
    <script type="text/javascript">
        $(document).ready(function()
        {
            //$('#login_password_text_box').unbind('keypress');
            $('#page_login_password_input').keydown(function(e)
            {
                var key = (e.keyCode ? e.keyCode : e.which);
                if(key == '13')
                    page_login();
            });
        });
    </script>
</html>