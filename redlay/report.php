<?php
@include('init.php');
include('universal_functions.php');
$allowed="all";
include('security_checks.php');

?>
<html>
    <head>
        <title>Report redlay</title>
        <?php include('required_header.php'); ?>
        <script type="text/javascript">
            function change_color()
            {
                <?php
                    if(isset($_SESSION['id']))
                        $colors=get_user_display_colors($_SESSION['id']);
                    else if(isset($_SESSION['page_id']))
                        $colors=get_page_display_colors ($_SESSION['page_id']);
                    if(isset($_SESSION['id'])||isset($_SESSION['page_id']))
                    {
                            $color=$colors[0];
                            $main_background_color=$colors[1];
                            $box_background_color=$colors[2];
                            $text_color=$colors[3];
                    }
                    else
                    {
                        $color="rgb(220, 21, 0)";
                        $main_background_color="rgb(10,10,10)";
                        $box_background_color="white";
                        $text_color="black";
                        $colors[5]="no";
                    }
                ?>
                $('.box').css('border', '5px solid <?php echo $color; ?>');
                $('body').css('background-color', '<?php echo $main_background_color; ?>');
                $('.box').css('background-color', '<?php echo $box_background_color; ?>');
                
                $('#id_input').css('outline-color', 'rgb(220,21,0)');
                $('#report_reason_input').css('outline-color', 'rgb(220,21,0)');
                $('#report_subject_input').css('outline-color', 'rgb(220,21,0)');
            }
            
            function report()
            {
                var type=$('#report_type_select').val();
                var title=$('#report_subject_input').val();
                var id=$('#id_input').val();
                var reason=$('#report_reason_input').val();
                $.post('report_email.php',
                {
                    subject: title,
                    user_type: type,
                    user_id: id,
                    report_reason: reason
                }, function(output)
                {
                    if(output=='Report sent!')
                        $('#report_error').addClass('good_errors');
                    else
                        $('#report_error').addClass('bad_errors');
                    $('#report_error').html(output).show();
                });
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
    </head>
    <body>
        <div id="top">
            <?php if(isset($_SESSION['id'])) include('top.php'); else if(isset($_SESSION['page_id'])) include('top_page.php'); else include('index_top.php'); ?>
        </div>
        <?php include('required_html.php'); ?>
        <div id="main">
            <div id="report_content" class="box">
                <p id="report_title">Report</p>
                <div id="report_body">
                    <p>You will be kept anonymous!</p>
                    <span id="report_what">What are you reporting? </span>
                    <select id="report_type_select">
                        <option value="user">User</option>
                        <option value="page">Page</option>
                    </select>
                    <br />
                    <p id="report_get_id">You will need to get their ID</p>
                    <div class="about_outside_picture" id="get_id_outside_picture"><img id="get_id_inside_picture" class="about_inside_picture" src="http://pics.redlay.com/pictures/get_id.png"/></div>
                    <span id="report_submit_id">User or Page ID: </span><input maxlength="11" class="input_box" onFocus="input_in(this);" onBlur="input_out(this);" type="text" id="id_input" placeholder="EX: 1058927" /><div id="report_profile_picture"></div>
                    <br />
                    <span id="report_subject_text">Subject: </span><input type="text" placeholder="Subject..." class="input_box" onFocus="input_in(this);" onBlur="input_out(this);" id="report_subject_input" />
                    <br />
                    <span id="report_reason">Reason: </span>
                    <textarea maxlength="500" class="input_box" onFocus="input_in(this);" onBlur="input_out(this);" id="report_reason_input" placeholder="Reason for report..."></textarea>
                    <input type="button" onClick="report();" value="Report" id="report_submit" class="red_button" />
                </div>
            </div>
            <div id="report_error"></div>
            <?php include('footer.php'); ?>
        </div>
        <script type="text/javascript" >
            $('#id_input').keyup(function(e)
            {
                var type=$('#report_type_select').val();
                var id=$('#id_input').val();
                if(id!='')
                {
                    //set HTML for profile picture
                }
                else
                    $('#report_profile_picture').html('');
            });
        </script>
    </body>
</html>