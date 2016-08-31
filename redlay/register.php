<?php
@include('init.php');
if(isset($_SESSION['id'])||isset($_SESSION['page_id']))
{
    header("Location: http://www.redlay.com");
    exit();
}
include('universal_functions.php');
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Register</title>
        <?php include('required_header.php'); ?>
        <script type="text/javascript">
            function change_color()
            {
                 $('body').css('background-color', 'rgb(30,30,30)');
                $('.box').css({'border': '5px solid rgb(220,21,0)', 'background-color': "white"});
                $('.register_input').css('outline-color', 'rgb(220,21,0)');
                $('#page_register_button').css('top', '28px');
                $('.bad_errors').css('color', 'white');
            }
            
            function register()
            {
                if($('#agree_checkbox').attr('src')=="http://pics.redlay.com/pictures/gray_checkbox_checked.png")
                {
                    $.post('RegisterAfter.php',
                    {
                        firstName: $('#first_name_input').val(),
                        lastName: $('#last_name_input').val(),
                        password: $('#password_input').val(),
                        email: $('#email_input').val()
                    }, function (output)
                    {
                        if(output=='Email has been sent! Email may be in spam folder')
                            display_error(output, "good_errors");
                        else
                            display_error(output, 'bad_errors');
                    });
                }
                else
                    display_error("You must agree to the user agreement before creating an account", 'bad_errors');
            }
            function resend_email()
            {
                $.post('resend_verification_email.php',
                {
                    email: $('#verification_email_resend').val()
                }, function(output)
                {
                    if(output=="Success")
                        display_error("Email sent!", 'good_errors')
                    else
                        display_error(output, "bad_errors");
                });
            }
            
            function toggle_password()
            {
                if($('#password_plain_text_checkbox').is(":checked"))
                {
                    var temp=$('#password_input').val();
                    $('#password_unit').html("<input class='index_input input_box' id='password_input'  type='text' placeholder='Password' onFocus='input_in(this);' />");
                        $('#password_input').attr('onBlur', "{$(this).css({'border': '1px solid gray', 'box-shadow': 'inset 0px 0px 5px 0px gray'});}").val(temp);
                }
                else
                {
                    var temp=$('#password_input').val();
                    $('#password_unit').html("<input class='index_input input_box' id='password_input'  type='password' placeholder='Password' onFocus='input_in(this);' />");
                        $('#password_input').attr('onBlur', "{$(this).css({'border': '1px solid gray', 'box-shadow': 'inset 0px 0px 5px 0px gray'});}").val(temp);
                }
            }
            $(document).ready(function()
            {
                $('#resend_email_information').hide();
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
        <?php
            include('required_html.php');
            include('index_top.php');
        ?>
        <div id="main">
            <div id="register_content" class="content box">
                <table id ="register_table">
                    <tr class="register_row" id="register_row_1">
                        <td colspan="3" align="center"><p id="register_title">Create</p></td>
                    </tr>
                    <tr class="register_row" id="register_row_2">
                        <td class="register_title_unit"><span class="register_text">First Name: </span></td>
                        <td class="register_input_unit"><input class="index_input input_box" id="first_name_input" type="text" maxlength ="20" autocomplete="off" placeholder="First Name" onFocus="{input_in(this);}" onBlur="{$(this).css({'border': '1px solid gray', 'box-shadow': 'inset 0px 0px 5px 0px gray'});}"/></td>
                    </tr>
                    <tr class="register_row" id="register_row_3">
                        <td class="register_title_unit"><span class="register_text">Last Name: </span></td>
                        <td class="register_input_unit"><input class="index_input input_box" id="last_name_input" type="text" maxlength ="20" autocomplete="off" placeholder="Last Name" onFocus="{input_in(this);}" onBlur="{$(this).css({'border': '1px solid gray', 'box-shadow': 'inset 0px 0px 5px 0px gray'});}"/></td>
                    </tr>
                    <tr class="register_row" id="register_row_4">
                        <td class="register_title_unit"><span class="register_text">Password: </span></td>
                        <td class="register_input_unit" id="password_unit"><input class="index_input input_box" id="password_input"  type="password" placeholder="Password" onFocus="{input_in(this);}" onBlur="{$(this).css({'border': '1px solid gray', 'box-shadow': 'inset 0px 0px 5px 0px gray'});}"/></td>
                        <td><div><input type="checkbox" id="password_plain_text_checkbox" onClick="toggle_password();"/><span class="text_color">Show text</span></div></td>
                    </tr>
                    <tr class="register_row" id="register_row_6">
                        <td class="register_title_unit"><span class="register_text">Email: </span></td>
                        <td class="register_input_unit"><input class="index_input input_box" id="email_input" type="email" maxlength="255" autocomplete="off" placeholder="Email" onFocus="{input_in(this);}" onBlur="{$(this).css({'border': '1px solid gray', 'box-shadow': 'inset 0px 0px 5px 0px gray'});}"/></td>
                    </tr>
                    <tr class="register_row" id="register_row_6">
                        <td class="register_input_unit"><p style="margin:0px;">I agree to the </p><p style="margin:0px;"><span style="cursor:pointer;color:rgb(220,20,0);" onmouseover="name_over(this);" onmouseout="name_out(this);" onClick="window.open('http://www.redlay.com/user_agreement.php');">User Agreement</span></p></td>
                        <td class="register_title_unit" style="text-align:center;"><img class="checkbox" src="http://pics.redlay.com/pictures/gray_checkbox.png" id="agree_checkbox" onClick="toggle_regular_checkbox('#agree_checkbox');"/></td>
                    </tr>
                    <tr class="register_row" id="register_row_8">
                        <td colspan="3" ><hr /></td>
                    </tr>
                    <tr class="register_row" id="register_row_8">
                        <td id="register_button" colspan="3" align="center"><input class="red_button" id="register_submit" type="button" value="Create" onClick="register();" /></td>
                    </tr>
                    <tr class="register_row" id="register_row_9">
                        <td colspan="3" align="center"><p id="register_no_email_recieved" style="cursor:pointer;text-align:center;" onClick="$('#resend_email_information').show(); $(this).hide();" onmouseover="name_over(this);" onmouseout="name_out(this);">Didn't receive email?</p></td>
                    </tr>
                </table>
                
                <table id="resend_email_information">
                    <tr class="register_row">
                        <td class="register_title_unit">
                            <span class="register_text">Email: </span>
                        </td>
                        <td>
                            <input class="index_input" id="verification_email_resend" type="email" placeholder="Email..." onFocus="input_in(this);" onBlur="{$(this).css({'border': '1px solid gray', 'box-shadow': 'inset 0px 0px 5px 0px gray'});}"/>
                        </td>
                    </tr>
                    <tr class="register_row">
                        <td colspan="2" align="center"><input id="verification_email_resend_submit" onClick="resend_email();" type="button" value="Resend" class="red_button" /></td>
                    </tr>
                </table>
            </div>
            <?php include('footer.php'); ?>
        </div>
    </body>
</html>