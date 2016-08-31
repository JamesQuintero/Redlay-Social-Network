<?php
@include('init.php');
if(!isset($_SESSION['id']))
{
    header("Location: http://m.redlay.com");
    exit();
}
?>
<html>
    <head>
        <title>Home</title>
        <link rel="stylesheet" type="text/css" href="mobile_main.css" />
        <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
        <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js"></script>
        <link href='http://fonts.googleapis.com/css?family=Chivo' rel='stylesheet' type='text/css' />
        <script type="text/javascript" src="all_jQuery.js"></script>
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
                
            </div>
        </div>
    </body>
</html>