<?php
@include('init.php');
include("../universal_functions.php");
$allowed="users";
include("security_checks.php");

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
             function search()
            {
                window.location.replace('http://m.redlay.com/search_result.php?query='+$('#search_input').val());
            }
        </script>
    </head>
    <body>
        <?php include('top.php'); ?>
        <div id="main">
            <div id="content" class="box">
                <table id="index_table">
                    <tbody id="index_tbody">
                    <tr class="index_tr">
                        <td  class="index_td">
                            <input type="text" id="search_input" class="mobile_input" placeholder="Search for friend..." />
                        </td>
                    </tr>
                    <tr class="index_tr">
                        <td  class="index_td">
                            <img src="./pictures/search_button.png" id="search_button" onClick="search();"/>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </body>
</html>