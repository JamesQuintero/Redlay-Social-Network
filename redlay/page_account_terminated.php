<?php
@include('init.php');
include('universal_functions.php');
$allowed="both";
include('security_checks.php');


?>
<!DOCTYPE html>
<html>
    <head>
        <title>Page Terminated</title>
        <?php
            include('required_header.php');
        ?>
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
                $('.box').css({'background-color': '<?php echo $box_background_color; ?>'});
                
                $('.text_color').css('color', '<?php echo $text_color; ?>');
            }
            $(window).ready(function()
            {
                change_color();
                $('#menu').hide();
                
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
                if($_SESSION['id']!=null)
                    include('top.php');
                else if(isset($_SESSION['page_id']))
                    include('top_page.php');
                else
                    include('index_top.php');
            ?>
        </div>
        <div id="main">
            <div id="account_terminated_content" class="content box">
                <p class="text_color" style="text-align:center;">Page Has Been Terminated.</p>
            </div>
        </div>
    </body>
</html>
