<?php
@include('init.php');
include('universal_functions.php');
$allowed="users";
include("security_checks.php");


//DEPRECATED FEATURE
exit();

$query=mysql_query("SELECT document_names FROM user_documents WHERE user_id=$_SESSION[id] LIMIT 1");
if($query&&mysql_num_rows($query)==1)
{
    $array=mysql_fetch_row($query);
    $num=sizeof(explode('|^|*|', $array[0]));
}
// add_view('view_document');

?>

<!DOCTYPE html>
<html>
    <head>
        <title><?php echo $num; ?> received documents</title>
        <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.min.js"></script>
        <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js"></script>
        <link href='http://fonts.googleapis.com/css?family=Chivo' rel='stylesheet' type='text/css' />
        <link rel="stylesheet" type="text/css" href="MainCSS.css" />
        <link rel="stylesheet" type="text/css" href="documents.css" />
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

                $('#readonly_input').css('outline-color', '<?php echo $color; ?>');
                $('.document_title').css('color', '<?php echo $color; ?>');
                $('.document_info_title').css('color', '<?php echo $color; ?>');
                $('#delete_document_button').css('top', '-30px');
            }
            function display_received_documents()
            {
                var names=new Array();
                var timestamps=new Array();
                <?php
                    $names=get_received_document_names($_SESSION['id']);
                    if($names[0]!='')
                    {
                        for($x = 0; $x < sizeof($names); $x++)
                        {
                            echo "names[$x]='$names[$x]';";
                            echo "timestamps[$x]='".date("F j, Y g:i A", filemtime('./users/docs/'.$_SESSION[id].'/received/'.$x.'.txt'))."';";
                        }
                    }
                ?>
                var top=0;
                var left=0;
                $('#existing_documents_title').html("Received documents ["+names.length+"]");
                for(var x = 0; x < names.length; x++)
                {
                    if(x!=0&&x%2==0)
                    {
                        top=-246;
                        left=left+210;
                    }
                    else if(x%2!=0&&x!=1)
                    {
                        top=0;
                    }
                    var document="<div id='existing_document_"+x+"' class='existing_document' onmouseover='existing_document_over("+x+", 1);' onmouseout='existing_document_over("+x+", 2);' onClick=change_document("+x+");><img id='existing_document_icon_"+x+"' class='existing_document_icon' src='blank_document.png'/>";
                    var name="<p id='existing_document_name_"+x+"' class='existing_document_name'>"+names[x]+"</p>";
                    var date="<p id='existing_document_timestamp_"+x+"' class='existing_document_timestamp'>"+timestamps[x]+"</p></div>";

                    $('#existing_documents').html($('#existing_documents').html()+document+name+date);
                    $('#existing_document_'+x).css({'margin-top': top, 'margin-left': left});
                }
            }
            function change_document(num)
            {
                //gets the document contents
                $.post('get_user_received_document.php?num=1',
                {
                    file_id: num
                }, function (output)
                {
                    $('#readonly_input').html(output);
                });
                
                //gets the document's title
                $.post('get_user_received_document.php?num=2',
                {
                    file_id: num
                }, function(output)
                {
                    $('#document_title').html("<span class='document_info_title'>Title: </span>"+output);
                });
                $('#document_menu').html("<input type='button' class='green_button' value='Save' id='new_document_submit' onclick='save_document("+num+");'/><input type='button' value='Delete' class='green_button' id='delete_document_button' onClick='delete_document("+num+");' />");
                $('#new_document_submit').attr({'onmouseover': "{submit_button_over('#new_document_submit'); green_button_over('#new_document_submit');}", 'onmouseout': "{submit_button_out('#new_document_submit'); green_button_out('#new_document_submit');}"});
                $('#delete_document_button').attr({'onmouseover': "{submit_button_over('#delete_document_submit'); green_button_over('#delete_document_submit');}", 'onmouseout': "{submit_button_out('#delete_document_submit'); green_button_out('#delete_document_submit');}"});

                //gets the user who sent the document
                $.post('get_user_received_document.php?num=3',
                {
                    file_id: num
                }, function (output)
                {
                    $('#document_user_sent').html("<span class='document_info_from'>From: </span>"+output);
                });
                change_color();
            }
            function delete_document(num)
            {
                $.post('delete_received_document.php',
                {
                    file_id: num
                }, function (output)
                {
                    window.location.replace(window.location);
                });
            }
            function save_document(num)
            {
                if($('#new_document_title').length==0)
                    var title=$('#document_info_title').html();
                else
                    var title=$('#new_document_title').val();
                $.post('save_received_document.php',
                {
                    file_id: num
                }, function (output)
                {
                    window.location.replace(window.location);
                });
            }
            function existing_document_over(num, number)
            {
                if(number==1)
                    $('#existing_document_'+num).css('background-color', 'lightblue');
                else
                    $('#existing_document_'+num).css('background-color', 'white');
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
        </script>
        <script type="text/javascript" >
            $(window).ready(function()
            {
                display_received_documents();
                change_document(0);
                change_color();
                $('#menu').hide();
                var Document_width=($(window).width())/2;
                var Profile_width=(935)/2;
                if(Document_width-Profile_width>=0)
                {
                    $('#main').css('left', Document_width-Profile_width);
                    $('#top').css('left', Document_width-Profile_width);
                }
                $(window).resize(function()
                {
                    var Document_width=($(window).width())/2;
                    var Profile_width=(935)/2;
                    if(Document_width-Profile_width>=0)
                    {
                        $('#main').css('left', Document_width-Profile_width);
                        $('#top').css('left', Document_width-Profile_width);
                    }
                    else
                    {
                        $('#main').css('left', '0px');
                        $('#top').css('left', '0px');
                    }
                });
            });
        </script>
    </head>
    <body>
        <div id="main">
            <p id="document_error" class="errors"></p>
            <div id="content" class="box">
                <p id="existing_documents_title" class="document_title">Received documents</p>
                <div id="existing_documents">

                </div>
                <p id="create_new_document_title" class="document_title">Create a new document</p>
                <div id="document_menu">
                </div>
                <div id="new_document">
                    <div id="document_title"></div>
                    <div id="document_user_sent"></div>
                    <textarea readonly id="readonly_input" maxlength="10000" class="document_input"></textarea>
                </div>
            </div>


            <div id="top">
                <?php include('top.php'); ?>
            </div>
        </div>
    </body>
</html>
