<?php
@include('init.php');
include("../universal_functions.php");
$allowed="users";
include("security_checks.php");

?>
<html>
    <head>
        <title>Home</title>
        <?php include("required_header.php"); ?>
        <script type="text/javascript">
            $(document).ready(function()
            {
                $('#menu').hide();
                $('body').css({'background-color': 'rgb(10,10,10)'});
                $('.box').css({'border-width': '5%', 'border-style': 'solid', 'border-color': 'rgb(220,21,0)', 'background-color': "white"});
            });
        </script>
    </head>
    <body>
        <?php include('top.php'); ?>
        <div id="main">
            <div id="content" class="box">
                <p>Nothing here, yet</p>
            </div>
        </div>
    </body>
</html>