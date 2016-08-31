<?php
@include('init.php');
if(isset($_SESSION['id']))
{
   header("Location: http://m.redlay.com/home.php");
   exit();
}

if(!isset($_SESSION['id']))
{
    if(!isset($_SESSION['page_id']))
    {
        if(isset($_COOKIE['acc_id']))
        {
            $query=mysql_query("SELECT id FROM users WHERE account_id='$_COOKIE[acc_id]' LIMIT 1");
            if($query&&mysql_num_rows($query)==1)
            {
                $array=mysql_fetch_row($query);

                $_SESSION['id']=$array[0];
                header("Location: http://m.redlay.com/home.php");
                exit();
            }
        }
        else if(isset($_COOKIE['acc_page']))
        {
            $query=mysql_query("SELECT id FROM pages WHERE account_id='$_COOKIE[acc_page]' LIMIT 1");
            if($query&&mysql_num_rows($query)==1)
            {
                $array=mysql_fetch_row($query);

                $_SESSION['page_id']=$array[0];
                header("Location: http://m.redlay.com/page.php?page_id=".$_SESSION[page_id]);
                exit();
            }
        }
    }
    else
    {
        header("Location: http://m.redlay.com/page.php?page_id=$_SESSION[page_id]");
        exit();
    }
}
else
{
    header("Location: http://m.redlay.com/home.php");
    exit();
}
 
include('../universal_functions.php');
include('security_checks.php');

?>

<!DOCTYPE html>
<html>
    <head>
        <title>Welcome!</title>
        <?php include('required_header.php'); ?>
        <script type="text/javascript">
            $(document).ready(function()
            {
                $('body').css({'background-color': 'rgb(50,50,50)'});
                $('.box').css({'border-width': '5%', 'border-style': 'solid', 'border-color': 'rgb(220,20,0)', 'background-color': "white"});
                
                $('#index_table').css("width", '100%');
            });
            function login()
            {
                $.post('login.php',
                {
                    email: $('#mobile_login_email_input').val(),
                    password: $('#mobile_login_password_input').val()
                },
                function (output)
                {
                    if(output=='')
                        window.location.replace('http://m.redlay.com/index.php');
                   else if(output=='Wrong IP Address')
                       window.location.replace('http://m.redlay.com/new_ip_address.php');
                   else
                       display_error(output, 'bad_errors');
                   return false;
                });
            }
            function resend_email()
            {
                var email=$('#register_email').val();
                $.post('resend_verification_email.php',
                {
                    email:email
                }, function(output)
                {
                    if(output=='Success')
                        display_error(output, 'good_errors');
                    else
                        display_error(output, 'bad_errors');
                });
            }
            function register()
            {
                $('#errors').hide();
                
                var first_name=$('#register_first_name').val();
                var last_name=$('#register_last_name').val();
                var password=$('#register_password').val();
                var confirm_password=$('#register_confirm_password').val();
                var email=$('#register_email').val();
                var confirm_email=$('#register_confirm_email').val();
                $.post('mobile_register.php',
                {
                    first_name: first_name,
                    last_name: last_name,
                    password: password,
                    confirm_password: confirm_password,
                    email: email,
                    confirm_email: confirm_email
                }, function (output)
                {
                    if(output=='Email has been sent! Email may be in spam folder')
                        display_error(output, 'good_errors');
                    else
                        display_error(output, 'bad_errors');
                    
                    $('#index_row_8').show();
                });
            }
            function display_login_form()
            {
                $('#top_right').html("<img id='index_button' src='./pictures/register_button.png' onClick='display_register_form();' />");
                    $('#create_button').attr({'onClick': "display_create_form();"});
                    
                $('#index_button').attr('src', './pictures/register_button.png');
                
                $('#mobile_login_form').html("<table id='index_table'></table>");
                    $('#index_table').html("<tbody id='index_tbody'></tbody>");
                        $('#index_tbody').html("<tr class='index_row' id='index_row_1'></tr><tr class='index_row' id='index_row_2'></tr><tr class='index_row' id='index_row_3'></tr>");
                            $('#index_row_1').html("<td  class='index_unit'><input type='email' id='mobile_login_email_input' class='mobile_input index_input' onFocus='input_in(this);' onBlur='input_out(this);' placeholder='Email'/></td>");
                            $('#index_row_2').html("<td  class='index_unit'><input type='password' id='mobile_login_password_input' class='mobile_input index_input' onFocus='input_in(this);' onBlur='input_out(this);' placeholder='Password'/></td>");
                            $('#index_row_3').html("<td style='text-align:center;'><input class='red_button index_button' type='button' value='Login' onClick='login();'/></td>");
                            
            }
            function display_register_form()
            {
                $('#top_right').html("<img id='index_button' src='./pictures/login_button.png' onClick='display_login_form();' />");
                    $('#login_button').attr({'onClick': "display_login_form();"});
                    
                $('#index_button').attr('src', './pictures/login_button.png');
                    
                $('#mobile_login_form').html("<table id='index_table'></table>");
                    $('#index_table').html("<tbody id='index_tbody'></tbody>");
                        $('#index_tbody').html("<tr class='index_row' id='index_row_1'></tr><tr class='index_row' id='index_row_2'></tr><tr class='index_row' id='index_row_3'></tr><tr class='index_row' id='index_row_4'></tr><tr class='index_row' id='index_row_5'></tr><tr class='index_row' id='index_row_6'></tr><tr class='index_row' id='index_row_7'></tr><tr class='index_row' id='index_row_8'></tr>");
                            $('#index_row_1').html("<td  class='index_unit'><input type='text' id='register_first_name' class='mobile_input index_input' onFocus='input_in(this);' onBlur='input_out(this);' placeholder='First Name'/></td>");
                            $('#index_row_2').html("<td  class='index_unit'><input type='text' id='register_last_name' class='mobile_input index_input' onFocus='input_in(this);' onBlur='input_out(this);' placeholder='Last Name'/></td>");
                            $('#index_row_3').html("<td class='index_unit'><input type='password' id='register_password' class='mobile_input index_input' onFocus='input_in(this);' onBlur='input_out(this);' placeholder='Password'/></td>");
                            $('#index_row_4').html("<td class='index_unit'><input type='password' id='register_confirm_password' class='mobile_input index_input' onFocus='input_in(this);' onBlur='input_out(this);' placeholder='Confirm Password'/></td>");
                            $('#index_row_5').html("<td class='index_unit'><input type='email' id='register_email' class='mobile_input index_input' onFocus='input_in(this);' onBlur='input_out(this);' placeholder='Email'/></td>");
                            $('#index_row_6').html("<td class='index_unit'><input type='email' id='register_confirm_email' class='mobile_input index_input' onFocus='input_in(this);' onBlur='input_out(this);' placeholder='Confirm Email'/></td>");
                            $('#index_row_7').html("<td style='text-align:center;'><input class='red_button index_button' type='button' value='Register' onClick='register();' /></td>");
                            $('#index_row_8').html("<td style='text-align:center;'><p style='color:rgb(220,20,0);font-size:35px;margin-top:50px;margin-bottom:0px;' onClick='resend_email();'>Resend email</p></td>").hide();
            }
        </script>
        <script type="text/javascript">
            <?php include('../required_google_analytics.js'); ?>
        </script>
    </head>
    <body >
        <div id="top">
            <div class='header_background'></div>
            <table style="width: 100%;position: relative;margin-top: -140px;">
                <tbody>
                    <tr>
                        <td style="width:33%">
                            <a href="http://m.redlay.com" id="icon_link"><p id="icon" class="text">redlay</p></a>
                        </td>
                        <td style="width:33%"></td>
                        <td style="width:33%" id="top_right">
                            <img id="index_button" src="./pictures/register_button.png" onClick="display_register_form();" />
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <?php include('required_html.php'); ?>
        <div id="main">
            <div id="login_errors"></div>
            
            <table id="content" class="box">
                <tbody>
                    <tr>
                        <tr class="index_row">
                            <td  class="index_unit">
                                <div >
                                    <input type="email" id="mobile_login_email_input" class="mobile_input index_input" onFocus="input_in(this);" onBlur="input_out(this);" placeholder="Email"/>
                                </div>
                            </td>
                        </tr>
                        <tr class="index_row">
                            <td  class="index_unit">
                                <div>
                                    <input type="password" id="mobile_login_password_input" class="mobile_input index_input" onFocus="input_in(this);" onBlur="input_out(this);" placeholder="Password"/>
                                </div>
                            </td>
                        </tr>
                        <tr class="index_row">
                            <td class="index_unit">
                                <input class="red_button index_button" type="button" value="Login" onClick="login();" />
                            </td>
                        </tr>
                    </tr>
                </tbody>
            </table>
        </div>
    </body>
    <script type="text/javascript">
        $(document).ready(function()
        {
            //$('#login_password_text_box').unbind('keypress');
            $('#mobile_login_password_input').keydown(function(e)
            {
                var key = (e.keyCode ? e.keyCode : e.which);
                //enter key
                if(key == '13')
                    login();
            });
        });
    </script>
</html>