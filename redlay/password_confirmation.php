<?php
@include('init.php');
include('universal_functions.php');
$allowed="all";
include('security_checks.php');

if(isset($_SESSION['id'])||isset($_SESSION['page_id']))
{
    header("Location: http://www.redlay.com");
    exit();
}

$passkey=clean_string($_GET['passkey']);

$query=mysql_query("SELECT email, type FROM password_recovery WHERE passkey='$passkey'");
if($query&&mysql_num_rows($query)==1)
{
    $array=mysql_fetch_row($query);
    $email=$array[0];
    $type=$array[1];
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
        <title>Password Recovery</title>
        <?php include('required_header.php'); ?>
        <script type="text/javascript">
            function change_color()
            {
                $('.box').css({'border': '5px solid rgb(220,21,0)', 'background-color': 'white'});
                
                $('.text_color').css('color', 'rgb(30,30,30)');
                $('.title_color').css('color', 'rgb(220,20,0)');
            }
            function change_password()
            {
                $.post('change_recovery_password.php',
                {
                    num:1,
                    passkey: '<?php echo $passkey; ?>',
                    password: $('#password_1').val(),
                    password2: $('#password_2').val()
                }, function (output)
                {
                    if(output=='Change Successful!')
                        display_error(output, 'good_errors');
                    else
                        display_error(output, 'bad_errors');
                });
            }
            function wrong_account()
            {
                $.post('change_recovery_password.php',
                {
                    num:2,
                    passkey: '<?php echo $passkey; ?>'
                }, function(output)
                {
                    window.location.replace(window.location);
                });
            }
        </script>
        <script type="text/javascript">
            $(window).ready(function()
            {
                change_color();
                $('#footer').css('width', '910px');
                <?php include('required_jquery.php'); ?>
            });
        </script>
        <script type="text/javascript">
            <?php include('required_google_analytics.js'); ?>
        </script>
    </head>
    <body>
        <?php
            include('required_html.php');
        ?>
        <div id="main">
            <?php include('required_side_html.php'); ?>
            <?php
                include('index_top.php');
            ?>
            <div id="password_recovery_content" class="content box">
                <p id="password_recovery_information">To change your password for the <?php if($type=='user') echo "account"; else echo "page"; ?> connected to <span style="text-decoration:underline;"><?php echo $email; ?></span>, insert your new password into the boxes below. <a id="wrong_account" onClick="wrong_account();" style="color:rgb(220,20,0);cursor:pointer;" onmouseover="name_over(this);" onmouseout="name_out(this);">Not your account?</a></p>

                <table id="email_recovery_table">
                    <tbody>
                        <tr>
                            <td>
                                <input type="password" id="password_1" class="input_box password_recovery_input" onFocus="input_in(this);" onBlur="input_out(this);" placeholder="Password"/>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input type="password" id="password_2" class="input_box password_recovery_input" onFocus="input_in(this);" onBlur="input_out(this);" placeholder="Confirm password"/>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input type="button" id="password_recovery_submit" class="red_button" value="Change" onClick="change_password();" />
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <?php include('footer.php'); ?>
    </body>
</html>

