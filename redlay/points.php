<?php
#assign licenseFirst = "/* ">
#assign licensePrefix = " * ">
#assign licenseLast = " */">
#include "../Licenses/license-default.txt">
@include('init.php');

include('universal_functions.php');
$allowed = "users";
include('security_checks.php');
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Title</title>
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
            }
            else
            {
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

        $(document).ready(function()
        {
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
            if(isset($_SESSION['id']))
            {
                echo "<div id='top'>";
                include('top.php');
                echo "</div>";
            }
            else if(isset($_SESSION['page_id']))
            {
                echo "<div id='top'>";
                include('top_page.php');
                echo "</div>";
            }
            else
                include('index_top.php');
        ?>
        <div id="main">
            <?php if (!isset($_SESSION['page_id'])) include('required_side_html.php'); ?>
            <div id="points_content" class="content box">
                <p class="title title_color" style="margin-bottom:0px;">Points</p>
                <table style="padding:20px;padding-top:0px;">
                        <tbody>
                            <tr>
                                <td>
                                    <p class="text_color paragraph" >Redlay has a points system that allows you to be rewarded for the things you do on this site. For example, you get 1 point every time an add likes one of your posts. This may not seem like a lot, but it can definitely add up the hundreds or thousands! </p>
                                    <p class="text_color paragraph">Ways to get points:</p>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <p class="text_color paragraph">+1 for every like on your posts</p>
                                    <p class="text_color paragraph">+1 for every like on your photos</p>
                                    <p class="text_color paragraph">+1 for every like on your comments</p>
                                    <p class="text_color paragraph">+25 for every person you refer</p>
                                </td>
                            </tr>
                        </tbody>
                </table>
            </div>
            <?php include('footer.php'); ?>
        </div>
    </body>
</html>