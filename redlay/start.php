<?php
@include('init.php');
include('universal_functions.php');
$allowed="users";
include('security_checks.php');

if(!has_redlay_gold($_SESSION['id'], 'any'))
{
    header("Location: http://www.redlay.com/profile.php?user_id=".$_SESSION['id']);
    exit();
}

?>
<!DOCTYPE html>
<html>
    <head>
        <title>Use your Redlay gold!</title>
        <?php include('required_header.php'); ?>
        <script type="text/javascript">
            function change_color()
            {
                <?php
                    $colors = get_user_display_colors($_SESSION['id']);
                    $color = $colors[0];
                    $box_background_color = $colors[1];
                    $text_color = $colors[2];
                ?>
                $('.box').css('border', '5px solid <?php echo $color; ?>');
                $('.box').css({'background-color': '<?php echo $box_background_color; ?>'});
                
                $('.title_color').css('color', '<?php echo $color; ?>');
                $('.text_color').css('color', '<?php echo $text_color; ?>');
            }
            
            $(document).ready(function()
            {
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
        <?php include('required_html.php'); ?>
        <div id="top">
            <?php include('top.php'); ?>
        </div>
        <div id="main">
            <div class="content box">
                <p style="text-align:center;font-size:20px;" class="title_color">Thank you for your purchase!</p>
                <p class="text_color" style="margin: 0 auto; width:500px;margin-bottom:30px;">A receipt for your purchase has been emailed to you. Log in to <a href="http://www.paypal.com">Paypal</a> to review the details of this purchase.</p>
                <p class="text_color" style="margin:0 auto;width:500px;margin-bottom:30px;">If you purchase Site Customization, click <a class="link" href="http://www.redlay.com/settings.php"><span class="title_color" onmouseover="name_over(this);" onmouseout="name_out(this);">here</span></a> to go to the settings page</p>
                <p class="text_color" style="margin:0 auto;width:500px">If you purchase Account Stats, click on the "Map" button at the top and you'll see a new item</p>
            </div>
            <?php include('footer.php'); ?>
        </div>
    </body>
</html>