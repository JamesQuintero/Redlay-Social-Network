<?php
@include('init.php');
include('universal_functions.php');
$allowed="users";
include('security_checks.php');
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Redlay Gold</title>
        <?php include('required_header.php'); ?>
        <script type="text/javascript">
            function change_color()
            {
                <?php
//                    if(isset($_SESSION['id']))
//                        $colors=get_user_display_colors($_SESSION['id']);
//                    else
//                        $colors=get_page_display_colors($_SESSION['page_id']);
//                    
//                    $color=$colors[0];
//                    $box_background_color=$colors[1];
//                    $text_color=$colors[2];
                  $color="rgb(220,20,0)";
                  $box_background_color="rgb(256,256,256)";
                  $text_color="rgb(30,30,30)";
                ?>
                $('.box').css('border', '5px solid <?php echo $color; ?>');
                $('.box').css({'background-color': '<?php echo $box_background_color; ?>'});

                $('.redlay_gold_description, #company_footer').css('color', '<?php echo $text_color; ?>');
                    
                $('.title_color').css('color', '<?php echo $color; ?>');
                $('.text_color').css('color', '<?php echo $text_color; ?>');
                $('.purchase_successful').css('color', 'green');
            }
            
            function toggle_add_view()
            {
                
            }

            $(document).ready(function()
            {
                change_color();
                $('#footer').css('width', '910px');
                <?php
                    include('required_jquery.php');
                ?>
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
            <?php include('required_side_html.php'); ?>
            <div class="box" id="gold_content">
                
                <p id="gold_title" style="text-align:center;text-decoration:underline">Redlay Gold:</p>
                <?php
                    if(isset($_SESSION['id']))
                        include('redlay_gold_html.php');
                    else
                        include('redlay_gold_page_html.php');
                ?>
                
            </div>
            <?php include('footer.php'); ?>
        </div>
    </body>
</html>