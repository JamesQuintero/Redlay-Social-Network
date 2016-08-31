<?php
@include('init.php');
include('universal_functions.php');
$allowed="users";
include("security_checks.php");

?>
<html>
    <head>
        <title>Thank You!</title>
        <meta name="Thank you page" content="Last modified: 3/5/13"/>
        <?php include('required_header.php'); ?>
        <script type="text/javascript">
            function change_color()
            {
                $('.box').css('border', '5px solid rgb(220, 21, 0)');
                $('.box').css('background-color', 'white');
                
            }
            
        </script>
        <script type="text/javascript">
            $(window).ready(function()
            {
                change_color();
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
        <div id="top">
            <?php if(isset($_SESSION['id']))include('top.php'); else if(isset($_SESSION['page_id'])) include('top_page.php');  ?>
        </div>
        <?php include('required_html.php'); ?>
        <div id="main">
            <?php include('required_side_html.php'); ?>
            <div id="thank_you_content" class="content box" >
                <p id="thank_you_text" style="text-align:center;">Thank You! You may now begin using your account!</p>
            </div>
            <?php include('footer.php'); ?>
        </div>
    </body>
</html>