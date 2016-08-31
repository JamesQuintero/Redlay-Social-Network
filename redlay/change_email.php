<?php
@include('init.php');
include('universal_functions.php');
$allowed="all";
include('security_checks.php');

$passkey=clean_string($_GET['passkey']);
$confirmation=clean_string($_GET['confirmation']);

if($confirmation=="true")
{
    if(!isset($_SESSION['id']))
    {
        header("Location: http://www.redlay.com");
        exit();
    }
    
    $query=mysql_query("SELECT user_id, new_email FROM email_change WHERE passkey='$passkey' LIMIT 1");
    if($query&&mysql_num_rows($query)==1)
    {
        $array=mysql_fetch_row($query);
        $user_id=$array[0];

        if($user_id==$_SESSION['id'])
        {
            $new_email=$array[1];
            $query=mysql_query("UPDATE users SET email='$new_email' WHERE id=$_SESSION[id]");
            if($query)
                $query=mysql_query("DELETE FROM email_change WHERE user_id=$_SESSION[id]");
            else
            {
                log_error("change_email.php: (2): ", mysql_error());
                header("Location: http://www.redlay.com");
                exit();
            }
        }
        else
        {
            header("Location: http://www.redlay.com");
            exit();
        }
    }
    else
    {
        if(!$query||mysql_num_rows($query)!=0)
            log_error("change_email.php: (1): ", mysql_error());
        header("Location: http://www.redlay.com");
        exit();
    }
}

//if user didn't try to change their email
else if($confirmation=="false")
{
    $query=mysql_query("DELETE FROM email_change WHERE passkey='$passkey'");
    if($query)
    {
        header("Location: http://www.redlay.com");
        exit();
    }
    else
    {
        log_error("change_email.php: (3): ", mysql_error());
        header("Location: http://www.redlay.com");
        exit();
    }
}
else
{
    header("Location: http://www.redlay.com");
    exit();
}

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
                
                
                $('.text_color').css('color', '<?php echo $text_color; ?>');
                $('.title_color').css('color', '<?php echo $color; ?>');
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
        <div id="top">
            <?php include('top.php'); ?>
        </div>
        <div id="main">
            <div class="content box">
                <p class="text_color" style="text-align:center;">Your email has been changed to <?php echo $new_email; ?></p>
            </div>
            <?php include('footer.php'); ?>
        </div>
    </body>
</html>