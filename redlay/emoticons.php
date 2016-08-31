<?php
@include('init.php');
include('universal_functions.php');
$allowed = "users";
include('security_checks.php');
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Emoticons</title>
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
                        $color = "rgb(220,20,0);";
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
                change_color();
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
                else
                    include('index_top.php');
                
            ?>
        <div id="main">
            <?php include('required_side_html.php'); ?>
            <div class="content box" id="emoticons_content">
                
                
                <table id="emoticons_table" border="0" style="width:150px;margin:0 auto;">
                    <tbody>
                        <tr>
                            <td colspan="2">
                                <p class="title title_color" style="text-align:center;">Emoticons</p>
                            </td>
                        </tr>
                        <tr>
                            <td style="border:1px solid gray;width:25px;">
                                <img src="http://pics.redlay.com/pictures/emoticons/2.png" style="height:25px;width:25px;"/>
                            </td>
                            <td style="text-align:center">
                                <span class="text_color">:)</span>
                            </td>
                        </tr>
                        <tr>
                            <td style="border:1px solid gray;width:25px;">
                                <img src="http://pics.redlay.com/pictures/emoticons/1.png" style="height:25px;width:25px;"/>
                            </td>
                            <td style="text-align:center">
                                <span class="text_color">:D</span>
                            </td>
                        </tr>
                        <tr>
                            <td style="border:1px solid gray;width:25px;">
                                <img src="http://pics.redlay.com/pictures/emoticons/3.png" style="height:25px;width:25px;"/>
                            </td>
                            <td style="text-align:center">
                                <span class="text_color">-_-</span>
                            </td>
                        </tr>
                        <tr>
                            <td style="border:1px solid gray;width:25px;">
                                <img src="http://pics.redlay.com/pictures/emoticons/4.png" style="height:25px;width:25px;"/>
                            </td>
                            <td style="text-align:center">
                                <span class="text_color">O_O</span>
                            </td>
                        </tr>
                        <tr>
                            <td style="border:1px solid gray;width:25px;">
                                <img src="http://pics.redlay.com/pictures/emoticons/5.png" style="height:25px;width:25px;"/>
                            </td>
                            <td style="text-align:center">
                                <span class="text_color">:'(</span>
                            </td>
                        </tr>
                        <tr>
                            <td style="border:1px solid gray;width:25px;">
                                <img src="http://pics.redlay.com/pictures/emoticons/6.png" style="height:25px;width:25px;"/>
                            </td>
                            <td style="text-align:center">
                                <span class="text_color">(cool)</span>
                            </td>
                        </tr>
                        <tr>
                            <td style="border:1px solid gray;width:25px;">
                                <img src="http://pics.redlay.com/pictures/emoticons/7.png" style="height:25px;width:25px;"/>
                            </td>
                            <td style="text-align:center">
                                <span class="text_color">3:) or (devil)</span>
                            </td>
                        </tr>
                        <tr>
                            <td style="border:1px solid gray;width:25px;">
                                <img src="http://pics.redlay.com/pictures/emoticons/8.png" style="height:25px;width:25px;"/>
                            </td>
                            <td style="text-align:center">
                                <span class="text_color">XD</span>
                            </td>
                        </tr>
                        <tr>
                            <td style="border:1px solid gray;width:25px;">
                                <img src="http://pics.redlay.com/pictures/emoticons/9.png" style="height:25px;width:25px;"/>
                            </td>
                            <td style="text-align:center">
                                <span class="text_color"><3</span>
                            </td>
                        </tr>
                        <tr>
                            <td style="border:1px solid gray;width:25px;">
                                <img src="http://pics.redlay.com/pictures/emoticons/10.png" style="height:25px;width:25px;"/>
                            </td>
                            <td style="text-align:center">
                                <span class="text_color">:P</span>
                            </td>
                        </tr>
                        <tr>
                            <td style="border:1px solid gray;width:25px;">
                                <img src="http://pics.redlay.com/pictures/emoticons/11.png" style="height:25px;width:25px;"/>
                            </td>
                            <td style="text-align:center">
                                <span class="text_color">(swear)</span>
                            </td>
                        </tr>
                        <tr>
                            <td style="border:1px solid gray;width:25px;">
                                <img src="http://pics.redlay.com/pictures/emoticons/12.png" style="height:25px;width:25px;"/>
                            </td>
                            <td style="text-align:center">
                                <span class="text_color">>:( or >:|</span>
                            </td>
                        </tr>
                        <tr>
                            <td style="border:1px solid gray;width:25px;">
                                <img src="http://pics.redlay.com/pictures/emoticons/13.png" style="height:25px;width:25px;"/>
                            </td>
                            <td style="text-align:center">
                                <span class="text_color">;)</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
                
                
            </div>
            <?php include('footer.php'); ?>
        </div>
    </body>
</html>