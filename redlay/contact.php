<?php
@include('init.php');
include('universal_functions.php');
$allowed="all";
include('security_checks.php');
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Contact Redlay</title>
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
                $('.box').css('border', '5px solid <?php echo $color; ?>');
                $('.box').css('background-color', '<?php echo $box_background_color; ?>');
                <?php echo "$('body').css('background-attachment', 'fixed');"; ?>
                $('.contact_input').css('outline-color', '<?php echo $color; ?>');
                
                $('.text_color').css('color', '<?php echo $text_color; ?>');
                $('.title_color').css('color', "<?php echo $color; ?>");
            }
            
            function send_content()
            {
                $.post('contact_email.php',
                {
                    body: $('#contact_input').val()
                }, function (output)
                {
                    if(output=='Message has been sent')
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
            <?php
                include('top.php');
            ?>
        </div>
        <?php include('required_html.php'); ?>
        <div id="main">
            <?php include('required_side_html.php'); ?>
            <div id="contact_content" class="content box">
                <p id="contact_title" class="title_color">Contact</p>
                <table style="width:500px;margin:0 auto;position:relative;">
                    <tbody>
                        <tr>
                            <td>
                                <p class="text_color">You can contact us about anything you want. You can report a glitch or give a suggestion about a new feature you want. Feel free!</p>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <textarea id="contact_input" style="width:500px;height:200px;" class="input_box" placeholder="What would you like to tell us?" maxlength="5000" onFocus="input_in(this);" onBlur="input_out(this);" ></textarea>
                            </td>
                        </tr>
                        <tr>
                            <td style="text-align:center;">
                                <input type="button" value="Send" onClick="send_content();" class="red_button" />
                            </td>
                        </tr>
                    </tbody>
                </table>
                </div>
            </div>
            <?php include('footer.php'); ?>
        </div>
    </body>
</html>