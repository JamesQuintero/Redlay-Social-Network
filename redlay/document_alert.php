<?php
@include('init.php');
include('universal_functions.php');
$allowed="users";
include("security_checks.php");
// add_view('document_alert');

//gets the number of alerts and sets the new ones to 0
$query=mysql_query("SELECT * FROM user_documents WHERE user_id=$_SESSION[id] LIMIT 1");
if($query&&mysql_num_rows($query)==1)
{
    $array=mysql_fetch_array($query);
    $new_alerts=$array['new_document_alerts'];
    $users_sent=explode('|^|*|', $array['user_received_from']);
    $file_received=explode('|^|*|', $array['file_received']);
    $messages=explode('|^|*|', str_replace("'", "\'", $array['messages_received']));
    $timestamps=explode('|^|*|', $array['timestamps_received']);


    $query=mysql_query("UPDATE user_documents SET new_document_alerts=0 WHERE user_id=$_SESSION[id] LIMIT 1");
}
?>

<!DOCTYPE html>
<html>
    <head>
        <title><?php echo $new_alerts; ?> new alerts!</title>
        <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.min.js"></script>
        <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js"></script>
        <link href='http://fonts.googleapis.com/css?family=Chivo' rel='stylesheet' type='text/css' />
        <link rel="stylesheet" type="text/css" href="MainCSS.css" />
        <link rel="stylesheet" type="text/css" href="document_alerts.css" />
        <script type="text/javascript" src="all_jQuery.js"></script>
        <script type="text/javascript" >
            function change_color()
            {
                <?php
                        $colors=get_user_display_colors($_SESSION['id']);
                        $color=$colors[0];
                        $main_background_color=$colors[1];
                        $box_background_color=$colors[2];
                        $text_color=$colors[3];
                ?>
                $('.box').css('border', '5px solid <?php echo $color; ?>');
                $('body').css('background-color', '<?php echo $main_background_color; ?>');
                $('.box').css('background-color', '<?php echo $box_background_color; ?>');
                <?php $path="./users/images/$_SESSION[id]/background.jpg"; if(file_exists($path)&&$colors[5]=="yes") echo "$('body').css('background-attachment', 'fixed');"; ?>
                $('.alert_name_text').css('color', '<?php echo $color; ?>');
            }
            $(window).ready(function()
            {
                $('#friend_request_alert_numbers').hide();
                $('#messages_alert_numbers').hide();
                $('#alert_alert_numbers').hide();
                $('#document_alert_numbers').hide();

                if(<?php echo has_friend_request_alerts(); ?>==true)
                    $('#friend_request_alert_numbers').show();
                if(<?php echo has_messages_alerts(); ?>==true)
                    $('#messages_alert_numbers').show();
                if(<?php echo has_alert_alerts(); ?>==true)
                    $('#alert_alert_numbers').show();
                if(<?php echo has_document_alerts(); ?>==true)
                    $('#document_alert_numbers').show();
            });
            function display_alerts()
            {
                var users_sent=new Array();
                var timestamps=new Array();
                var user_names=new Array();
                <?php
                    if($array['file_received']!='')
                    {
                        for($x = 0; $x < sizeof($file_received); $x++)
                        {
                            echo "users_sent[$x]='$users_sent[$x]';";
                            echo "user_names[$x]='".get_user_name($users_sent[$x])."';";
                            echo "timestamps[$x]='$timestamps[$x]';";
                        }
                    }
                ?>
                if(users_sent.length!=0)
                {
                    $('#content').html('');
                    for(var x = 0; x < users_sent.length; x++)
                    {
                        var profile_picture="<div id='document_alert_"+x+"' class='document_alert' onmouseover=alert_over('#document_alert_"+x+"'); onmouseout=alert_out('#document_alert_"+x+"'); onClick=window.location.replace('http://localhost/view_document.php?file_id="+x+"');><a href='http://localhost/profile.php?user_id="+users_sent[x]+"'><img class='profile_picture_alerts' id='picture_alert_"+x+"' onmouseover=picture_over('#picture_alert_"+x+"'); onmouseout=picture_out('#picture_alert_"+x+"'); src='./users/images/"+users_sent[x]+"/0.jpg'/></a>";
                        var names="<div class='alert_name'><a href='http://localhost/profile.php?user_id="+users_sent[x]+"' class='alert_name_link'><span id='alert_name_"+x+"' onmouseover=name_over('#alert_name_"+x+"'); onmouseout=name_out('#alert_name_"+x+"'); class='alert_name_text'>"+user_names[x]+"</span></a></div>";
                        var text="<p class='document_alert_text'> sent you a document!</p><img class='document_alert_document' src='blank_document.png'/>";
                        var timestamp="<p class='document_alert_timestamp'>"+timestamps[x]+"</p><hr class='document_alert_break'/></div>";
                        $('#content').html(profile_picture+names+text+timestamp+$('#content').html());
                    }
                }
            }
            function picture_over(string)
            {
                $(string).css('box-shadow', '0 0 3px 3px <?php echo $color; ?>');
            }
            function alert_over(string)
            {
                $(string).css('background-color', 'lightblue');
            }
            function alert_out(string)
            {
                $(string).css('background-color', '<?php echo $box_background_color; ?>');
            }
            $(window).ready(function()
            {
                display_alerts();
                change_color();
                $('#menu').hide();
                $('#footer').css('width', '910px');
                <?php $path="./users/images/".$_SESSION[id]."/background.jpg"; if(file_exists($path)==true) echo "$('body').css({'background-image': 'url(\'$path\')', 'background-position' :'center 50px'});"; ?>
                var Document_width=($(window).width())/2;
                var Profile_width=(935)/2;
                if(Document_width-Profile_width>=0)
                {
                    $('#main').css('margin-left', Document_width-Profile_width);
                    $('#footer').css('margin-left', Document_width-Profile_width);
                    $('#top').css('left', Document_width-Profile_width);
                }
                $(window).resize(function()
                {
                    var Document_width=($(window).width())/2;
                    var Profile_width=(935)/2;
                    if(Document_width-Profile_width>=0)
                    {
                        $('#main').css('margin-left', Document_width-Profile_width);
                        $('#footer').css('margin-left', Document_width-Profile_width);
                        $('#top').css('left', Document_width-Profile_width);
                    }
                    else
                    {
                        $('#main').css('margin-left', '0px');
                        $('#footer').css('margin-left', '0px');
                        $('#top').css('left', '0px');
                    }
                });
            });
        </script>
    </head>
    <body>
        <div id="top">
                <?php include('top.php'); ?>
            </div>
        <div id="main">
            <div id="content" class="box">
                <p id="document_empty_message">You have no documents sent to you</p>
            </div>
        </div>
        <?php include('footer.php'); ?>
    </body>
</html>