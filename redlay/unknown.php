<?php
@include('init.php');
include('universal_functions.php');
$allowed="all";
include("security_checks.php");

?>
<html>
    <head>
        <title>You're lost...</title>
        <?php include('required_header.php'); ?>
        <script type="text/javascript">
            function change_color()
            {
                $('.box').css('border', '5px solid rgb(220, 21, 0)');
                $('.box').css({'background-color': 'white', 'box-shadow': 'gray'});
                $('.contact_input').css('outline-color', 'rgb(220,21,0)');
            }
            
        </script>
        <script type="text/javascript">
            $(window).ready(function()
            {
                change_color();
                $('#menu').hide();
                $('#footer').css('width', '910px');
                $('#unknown_body').css({'margin-left': '20px', 'margin-right': '20px'});
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
            <?php if(isset($_SESSION['id'])) include('top.php'); else if(isset($_SESSION['page_id'])) include('top_page.php'); else echo "<div class='header_background'></div><a href='http://www.redlay.com' id='icon_link'><p id='icon' class='text'>redlay</p></a>"; ?>
        </div>
        <div id="main">
            <div id="unknown_content" class="box">
                <p id="unknown_body">The page you are looking for either does not exist, or is temporary unavailable.</p>
            </div>
            <?php include('footer.php'); ?>
        </div>
    </body>
</html>