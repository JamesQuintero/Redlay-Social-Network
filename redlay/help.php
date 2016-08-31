<?php
@include('init.php');
include('universal_functions.php');
$allowed="all";
include('security_checks.php');

?>
<!DOCTYPE html>
<html>
    <head>
        <title>Help</title>
        <meta name="help page" content="Last modified: 3/16/13"/>
        <?php include('required_header.php'); ?>
        <script type="text/javascript">
            function change_color()
            {
                <?php
                   if(isset($_SESSION['id']))
                   {
                        $colors=get_user_display_colors($_SESSION['id']);
                        $color=$colors[0];
                        $box_background_color=$colors[1];
                        $text_color=$colors[2];
                   }
                   else
                   {
                       $color="rgb(220,20,0)";
                       $box_background_color="rgb(256,256,256)";
                       $text_color="rgb(30,30,30)";
                   }
                ?>
                $('.box').css('border', '5px solid <?php echo $color; ?>');
                $('.box').css('background-color', '<?php echo $box_background_color; ?>');
            }
            function show_item(num)
            {
                if($('#help_list_item_'+num).css('display')=='none')
                    $('#help_list_item_'+num).show();
                else
                    $('#help_list_item_'+num).hide();
            }
        </script>
        <script type="text/javascript">
            $(window).ready(function()
            {
                change_color();
                $('#menu').hide();
                $('#footer').css('width', '910px');
                $('.help_list_item').hide();
                <?php include('required_jquery.php'); ?>
            });
        </script>
        <script type="text/javascript">
            include('required_google_analytics.js');
        </script>
    </head>
    <body>
        <?php
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
                include('index_top.php');
         ?>
        <div id="main">
            <div id="help_content" class="content box">
                <table>
                    
                </table>
            </div>
            <?php include('footer.php'); ?>
        </div>
    </body>
</html>