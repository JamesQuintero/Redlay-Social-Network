<?php
@include('init.php');
include('universal_functions.php');
$allowed = "users";
include('security_checks.php');

// if(!has_redlay_gold($_SESSION['id']))
// {
//     header("Location: http://www.redlay.com");
//     exit();
// }

?>
<html>
    <head>
        <title>Custom Theme</title>
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
            function input_up(id)
            {
                var number=$(id).val();
                
                if(number<100)
                {
                    number++;
                    $(id).val(number);
                }
            }
            function input_down(id)
            {
                var number=$(id).val();
                
                if(number>0)
                {
                    number--;
                    $(id).val(number);
                }
            }
//            function save_background_width()
//            {
//                $.post('change_theme_positions.php',
//                {
//                    type: 'background',
//                    width: $('#background_width_input').val()
//                }, function(output)
//                {
//                    if(output=="Change successful")
//                        display_error(output, 'good_errors');
//                    else
//                        display_error(output, 'bad_errors');
//                });
//            }
//            function save_background_height()
//            {
//                $.post('change_theme_positions.php',
//                {
//                    type: 'background',
//                    height: $('#background_height_input').val()
//                }, function(output)
//                {
//                    if(output=="Change successful")
//                        display_error(output, 'good_errors');
//                    else
//                        display_error(output, 'bad_errors');
//                });
//            }
//            function save_header_background_x()
//            {
//                $.post('change_theme_positions.php',
//                {
//                    type: 'header',
//                    x_cord: $('#header_background_left_right_input').val()
//                }, function(output)
//                {
//                    if(output=="Change successful")
//                        display_error(output, 'good_errors');
//                    else
//                        display_error(output, 'bad_errors');
//                });
//            }
//            function save_header_background_y()
//            {
//                $.post('change_theme_positions.php',
//                {
//                    type: 'header',
//                    y_cord: $('#header_background_left_right_input').val()
//                }, function(output)
//                {
//                    if(output=="Change successful")
//                        display_error(output, 'good_errors');
//                    else
//                        display_error(output, 'bad_errors');
//                });
//            }
            function save_custom_theme_stuff()
            {
                $.post('change_theme_positions.php',
                {
                    x_cord: $('#header_background_left_right_input').val(),
                    y_cord: $('#header_background_up_down_input').val(),
                    width: $('#background_width_input').val(),
                    height: $('#background_height_input').val()
                }, function(output)
                {
                    if(output=='Change successful')
                        display_error(output, 'good_errors');
                    else
                        display_error(output, 'bad_errors');
                });
            }
            function update_header_background()
            {
                window.location.replace(window.location);
//                $('.header_background').css('background-image', 'url(https://s3.amazonaws.com/bucket_name/users/<?php echo $_SESSION['id']; ?>/themes/header_background.png)');
            }
            function update_background()
            {
                window.location.replace(window.location);
//                $('html').css('background-image', "url(https://s3.amazonaws.com/bucket_name/users/<?php echo $_SESSION['id']; ?>/themes/background.png)");
            }
            $(document).ready(function()
            {
                $('#background_load_gif').hide();
                $('#header_background_load_gif').hide();
                
                $('#footer').css('width', '910px');
                if(<?php if(file_exists_server("https://s3.amazonaws.com/bucket_name/users/$_SESSION[id]/themes/background.jpg")) echo "true"; else echo "false"; ?>==true)
                {
                    $('#background_form_unit').html("<input class='button red_button' type='button' value='Delete' />");
                }
                
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
            <?php include('top.php'); ?>
        </div>
        <div id="main">
            <?php include('required_side_html.php'); ?>
            <div id="custom_theme_content" class="content box" >
                <table style="width:100%;padding:20px;">
                    <tbody>
                        <tr>
                            <td>
                                <p class="title title_color" style="text-align:center;">Custom theme</p>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                
                                <table>
                                    <tbody>
                                        <tr>
                                            <td class="custom_background_text_unit">
                                                <span class="text_color">Background: </span>
                                            </td>
                                            <td id="background_form_unit">
                                                <form method='post' action='change_theme_background.php' enctype='multipart/form-data' target='theme_background_upload' id='custom_theme_background_form' style="margin:0px;">
                                                    <input type='file' id='background_change_button' onChange="{$('#custom_background_submit').click();$('#background_load_gif').show()}" name="image" />
                                                    <img class="load_gif" id="background_load_gif" src="http://pics.redlay.com/pictures/load.gif"/>
                                                    <input type='submit' id='custom_background_submit' class="button red_button" style="display:none;" />
                                                </form>
                                                
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="custom_background_text_unit">
                                                <span class="text_color">Header: </span>
                                            </td>
                                            <td id="header_background_form_unit">
                                                <form method='post' action='change_theme_header_background.php' enctype='multipart/form-data' target='theme_background_upload' id='custom_theme_header_background_form' style="margin:0px;">
                                                    <input type='file' id='header_background_change_button' onChange="{$('#custom_header_background_submit').click();$('#header_background_load_gif').show();}" name="image" />
                                                    <img class="load_gif" id="header_background_load_gif" src="http://pics.redlay.com/pictures/load.gif"/>
                                                    <input type='submit' id='custom_header_background_submit' class="button red_button" style="display:none;" />
                                                </form>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">
                                                <hr class="break"/>
                                            </td>
                                        </tr>
                                        
                                        <?php
                                            $query=mysql_query("SELECT x_cord, y_cord, width, height FROM themes WHERE user_id=$_SESSION[id] LIMIT 1");
                                            if($query&&mysql_num_rows($query)==1)
                                            {
                                                $array=mysql_fetch_row($query);
                                                $x_cord=$array[0];
                                                $y_cord=$array[1];
                                                $width=$array[2];
                                                $height=$array[3];
                                            }
                                        ?>
                                        <tr>
                                            <td class="custom_background_text_unit">
                                                <p class="text_color" style="margin:0px;">Header background position</p>
                                                <p class="text_color" style="margin:0px;text-align:center;">(Up Down)</p>
                                            </td>
                                            <td>
                                                <input class="input_box" id="header_background_up_down_input" placeholder="0-100" style="width:50px;" value="<?php echo $y_cord; ?>" onFocus="input_in(this);" onBlur="input_out(this);" /><span class="text_color">%</span>
                                            </td>
                                        </tr>
<!--                                        <tr>
                                            <td class="custom_background_text_unit">
                                                <span class="text_color">Header background (Left Right)</span>
                                            </td>
                                            <td>
                                                <input class="input_box" id="header_background_left_right_input" placeholder="0-100" style="width:50px;" value="<?php echo $x_cord ?>" onFocus="input_in(this);" onBlur="input_out(this);" /><span class="text_color">%</span>
                                            </td>
                                        </tr>-->
                                        <tr>
                                            <td colspan="2">
                                                <hr class="break"/>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="custom_background_text_unit">
                                                <span class="text_color">Background width</span>
                                            </td>
                                            <td>
                                                <input class="input_box" id="background_width_input" placeholder="0-100" style="width:50px;" value="<?php echo $width; ?>" onFocus="input_in(this);" onBlur="input_out(this);" /><span class="text_color">%</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="custom_background_text_unit">
                                                <span class="text_color">Background height</span>
                                            </td>
                                            <td>
                                                <input class="input_box" id="background_height_input" placeholder="0-100" style="width:50px;" value="<?php echo $height; ?>" onFocus="input_in(this);" onBlur="input_out(this);" /><span class="text_color">%</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">
                                                <input class="button red_button" type="button" value="Save" onClick="{save_custom_theme_stuff();}"/>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                
                                
                                
                                
                                
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <?php include('footer.php'); ?>
            
            <iframe name="theme_background_upload" style="display:none"></iframe>
            <script type="text/javascript">
                $(document).ready(function()
                {
                    $('#header_background_up_down_input').unbind('keypress').unbind('keydown').unbind('keyup');
                    $('#header_background_up_down_input').keydown(function(e)
                    {
                        var key = (e.keyCode ? e.keyCode : e.which);
                        //up arrow
                        if(key == '38')
                        {
                            //incrememts number in input field
                            input_up("#header_background_up_down_input");
                            
                            //moves header down
                            var vertical=$('#header_background_up_down_input').val();
                            $('.header_background').css('background-position-y', vertical+"%");
                        }
                        //down arrow
                        else if(key=="40")
                        {
                            //incrememts number in input field
                            input_down("#header_background_up_down_input");
                            
                            //moves header up
                            var vertical=$('#header_background_up_down_input').val();
                            $('.header_background').css('background-position-y', vertical+"%");
                        }
                    });
                    
                    $('#header_background_left_right_input').unbind('keypress').unbind('keydown').unbind('keyup');
                    $('#header_background_left_right_input').keydown(function(e)
                    {
                        var key = (e.keyCode ? e.keyCode : e.which);
                        //up arrow
                        if(key == '38')
                        {
                            //increments number in input field
                            input_up("#header_background_left_right_input");
                            
                            //moves header right
                            var horizontal=$('#header_background_left_right_input').val();
                            var vertical=$('#header_background_up_down_input').val();
                            $('.header_background').css('background-position', horizontal+"% "+vertical+"%");
                        }
                        //down arrow
                        else if(key=="40")
                        {
                            //increments number in input field
                            input_down("#header_background_left_right_input");
                            
                            //moves header left
                            var horizontal=$('#header_background_left_right_input').val();
                            var vertical=$('#header_background_up_down_input').val();
                            $('.header_background').css('background-position', horizontal+"% "+vertical+"%");
                        }
                    });
                    
                    $('#background_width_input').unbind('keypress').unbind('keydown').unbind('keyup');
                    $('#background_width_input').keydown(function(e)
                    {
                        var key = (e.keyCode ? e.keyCode : e.which);
                        //up arrow
                        if(key == '38')
                        {
                            //increments number in input field
                            input_up("#background_width_input");
                            
                            //widens background
                            var width=$('#background_width_input').val();
                            var height=$('#background_height_input').val();
                            $('html').css('background-size', width+"% "+height+"%");
                        }
                        //down arrow
                        else if(key=="40")
                        {
                            //increments number in input field
                            input_down("#background_width_input");
                            
                            //shrinks background
                            var width=$('#background_width_input').val();
                            var height=$('#background_height_input').val();
                            $('html').css('background-size', width+"% "+height+"%");
                        }
                    });
                    
                    $('#background_height_input').unbind('keypress').unbind('keydown').unbind('keyup');
                    $('#background_height_input').keydown(function(e)
                    {
                        var key = (e.keyCode ? e.keyCode : e.which);
                        //up arrow
                        if(key == '38')
                        {
                            //increments number in input field
                            input_up("#background_height_input");
                            
                            //widens background
                            var width=$('#background_width_input').val();
                            var height=$('#background_height_input').val();
                            $('html').css('background-size', width+"% "+height+"%");
                        }
                        //down arrow
                        else if(key=="40")
                        {
                            //increments number in input field
                            input_down("#background_height_input");
                            
                            //widens background
                            var width=$('#background_width_input').val();
                            var height=$('#background_height_input').val();
                            $('html').css('background-size', width+"% "+height+"%");
                        }
                    });
                    
                });
            </script>
        </div>
    </body>
</html>