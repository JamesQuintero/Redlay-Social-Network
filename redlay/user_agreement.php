<?php
@include('init.php');
include('universal_functions.php');
$allowed="all";
include('security_checks.php');


?>
<html>
    <head>
        <title>User Agreement</title>
        <?php include('required_header.php'); ?>
        <script type="text/javascript">
            function change_color()
            {
                <?php
                    if(isset($_SESSION['id']))
                    {
                        $colors = get_user_display_colors($_SESSION['id']);
                        $color = $colors[0];
                        $box_background_color = $colors[1];
                        $text_color = $colors[2];
                    }
                    else
                    {
                        $color="rgb(220,20,0)";
                        $box_background_color="rgb(255,255,255)";
                        $text_color="rgb(30,30,30)";
                    }
                ?>
                $('.box').css('border', '5px solid <?php echo $color; ?>');
                $('.box').css({'background-color': '<?php echo $box_background_color; ?>'});
                
                $('#todo_content').css('background-color', '<?php echo $box_background_color; ?>');
                
                $('.title_color').css('color', "<?php echo $color; ?>");
                $('.text_color').css('color', '<?php echo $text_color; ?>');
            }
            function login()
            {
                $.post('login.php',
                {
                    email: $('#login_email_text_box').val(),
                    password: $('#login_password_text_box').val()
                },
                function (output)
                {
                    if(output=='')
                        index_exit();
                    else
                       display_error(output, 'bad_errors');
                });
            }
            $(document).ready(function()
            {
                $('#menu').hide();
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
            <div id="terms_box" class="content">
                <div style="padding:30px;">
                    <p class="text_color">Last revised January 28, 2013</p>
                    <p class="text_color">The following User Agreement ("Agreement") governs the use of www.redlay.com ("site") as provided by Redlay. By registering on www.redlay.com, you agree to these terms.</p>
                    <p class="text_color">Redlay reserves the right to terminate and/or suspend access to the site without notice. </p>
                    <p class="text_color">Redlay is not responsible, in any way, for any illegal activities you perform on or by using the site. </p>
                    <p class="title_color" style="font-weight:bold;">Registration:</p>
                    <p class="text_color">Redlay requires that you register and/or set up an account to use the site. You must be 13 years or older to create an account. You must provide an email, first name, last name, and a password. You agree and represent that all registration information provided by you is accurate.</p>
                    <p class="title_color" style="font-weight:bold;">Usage: </p>
                    <p class="text_color">You are responsible for all usage or activity on your account including, but not limited to, use of the account by any person, with or without authorization, or who has access to any computer on which your account resides or is accessible.</p>
                    <p class="text_color">You agree to not upload pornography and/or copyrighted material. </p>
                    <p class="text_color">You agree to not interfere, attack, disrupt the site's hardware, servers, or users in any way.</p>
                    <p class="title_color" style="font-weight:bold;">Deletions:</>
                    <p class="text_color">Redlay reserves the right to delete any material provided for display or placed on the site without notice.</p>
                    <p class="title_color" style="font-weight:bold;">Additional rules:</p>
                    <p class="text_color">Redlay reserves the right to post additional rules of usage that apply to specific parts of the site. Your continued use of www.redlay.com constitutes your agreement to comply with these additional rules.</p>
                </div>
                <?php include('footer.php'); ?>
            </div>
        </div>
    </body>
</html>