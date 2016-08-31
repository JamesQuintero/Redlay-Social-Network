<?php
@include('init.php');
include('../universal_functions.php');
$allowed="users";
include('security_checks.php');
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Calendar</title>
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
            <div class="content box" id="calendar_content">
                
            </div>
            <?php include('footer.php'); ?>
        </div>
    </body>
</html>