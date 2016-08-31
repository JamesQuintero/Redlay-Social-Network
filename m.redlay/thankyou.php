<?php
@include('init.php');
if(!isset($_SESSION['id'])&&!isset($_SESSION['page_id']))
{
    header("Location:http://m.redlay.com");
    exit();
}
include('../universal_functions.php');
include('security_checks.php');
?>
<html>
    <head>
        <title></title>
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

            // Prevent "event.layerX and event.layerY are broken and deprecated in WebKit. They will be removed from the engine in the near future."
            // in latest Chrome builds.
            (function () {
                // remove layerX and layerY
                var all = $.event.props,
                len = all.length,
                res = [];
                while (len--) {
                    var el = all[len];
                    if (el != 'layerX' && el != 'layerY') res.push(el);
                }
                $.event.props = res;
            } ());

            $(document).ready(function()
            {
                $('#menu').hide();
                $('#footer').css('width', '910px');
                <?php include('required_jquery.php'); ?>
            });
        </script>
        <script type="text/javascript">
            <?php include('../required_google_analytics.js'); ?>
        </script>
    </head>
    <body>
        <?php include('required_html.php'); ?>
        <div id="top">
            <?php include('top.php'); ?>
        </div>
        <div id="main">
            <div>
                
            </div>
            <?php include('footer.php'); ?>
        </div>
    </body>
</html>