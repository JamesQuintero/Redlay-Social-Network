<?php
@include('init.php');
include('universal_functions.php');
$allowed = "users";
include('security_checks.php');
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Redlay themes</title>
        <?php include('required_header.php'); ?>
        <script type="text/javascript">
            function change_color()
            {
                <?php
                    if (isset($_SESSION['id'])) {
                        $colors = get_user_display_colors($_SESSION['id']);
                        $color = $colors[0];
                        $box_background_color = $colors[1];
                        $text_color = $colors[2];
                    } else {
                        $color = "rgb(220,20,0);";
                        $box_background_color = "white";
                        $text_color = "rgb(30,30,30)";
                    }
                ?>
                $('.box').css('border', '5px solid <?php echo $color; ?>');
                $('.box').css({'background-color': '<?php echo $box_background_color; ?>'});

                <?php $path = get_user_background_pic($_SESSION['id']);
                if (file_exists_server($path) && $colors[5] == "yes") echo "$('body').css('background-attachment', 'fixed');"; ?>

                $('.title_color').css('color', '<?php echo $color; ?>');
                $('.text_color').css('color', '<?php echo $text_color; ?>');
            }

            $(document).ready(function()
            {
                $('#footer').css('width', '910px');
                <?php
                    $path = get_user_background_pic($_SESSION['id']);
                    if (file_exists_server($path) == true)
                        echo "$('body').css({'background-image': 'url(\'$path\')', 'background-position' :'center 50px'});";
                    else
                        echo "$('body').css({'background-image': 'url(\'" . get_default_background_pic($redlay_theme) . "\')', 'background-position' :'center 50px'});";
                ?>
                <?php include('required_jquery.php'); ?>
            });
        </script>
        <script type="text/javascript">
            <?php include('required_google_analytics.js'); ?>
        </script>
    </head>
    <body>
        <?php include('required_html.php'); ?>
        <?php
            if(isset($_SESSION['id']))
            {
                echo "<div id='top'>";
                include('top.php');
                echo "</div>";
            }
        ?>
        <div id="main">
            <?php include('required_side_html.php'); ?>
            <div id="themes_content" class="content box">
                <table>
                    <tbody>
                        <tr>
                            <td colspan="2">
                                <p class="title title_color">Themes</p>
                            </td>
                        </tr>
                        <tr>
                            <td class="theme_picture">
                                
                            </td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <?php include('footer.php'); ?>
        </div>
    </body>
</html>