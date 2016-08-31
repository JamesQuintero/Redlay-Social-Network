<?php
@include('init.php');
if(isset($_SESSION['id'])||isset($_SESSION['page_id']))
{
    header("Location: http://www.redlay.com/index.php");
    exit();
}
include('universal_functions.php');
include('security_checks.php');

$type=clean_string($_GET['type']);
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Password Recovery</title>
        <?php include('required_header.php'); ?>
        <script type="text/javascript">
            function change_color()
            {
                $('.box').css('border', '5px solid rgb(220,20,0)');
                $('body').css('background-color', '<?php if($redlay_theme=="black") echo "rgb(30,30,30)"; else echo "whitesmoke"; ?>');
                $('.box').css({'background-color': 'rgb(256,256,256)'});
                
                $('body').css({"background-position" :"center 50px"});
            }
            function get_password()
            {
                $.post('send_password_recovery_email.php',
                {
                    email: $('#password_recovery_email_input').val(),
                    type: '<?php echo $type; ?>'
                }, function (output)
                {
                    if(output=="Email sent!")
                        display_error(output, 'good_errors');
                    else
                        display_error(output, 'bad_errors');
                });
            }
        </script>
        <script type="text/javascript">
            $(window).ready(function()
            {
                change_color();
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
        <?php include('index_top.php'); ?>
        <?php include('required_html.php'); ?>
        <div id="main">
            <?php include('required_side_html.php'); ?>
            <div id="password_recovery_content" class="content box">
                <p id="password_recovery_information">To obtain control of your account, enter in the email address connected to the it and click submit. We will send you an email with a link to change your password.</p>
                <table id="email_recovery_table">
                    <tbody>
                        <tr>
                            <td>
                                <input type="email" id="password_recovery_email_input" class="index_input input_box" placeholder="Email..." onFocus="input_in(this);" onBlur="input_out(this);"/>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="">
                                <input type="button" id="password_recovery_submit" class="red_button" value="Send" onClick="get_password();" />
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <?php include('footer.php'); ?>
        </div>
    </body>
</html>
