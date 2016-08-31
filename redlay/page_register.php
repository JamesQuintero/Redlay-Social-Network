<?php
@include('init.php');
include('universal_functions.php');
$allowed="all";
include('security_checks.php');

if(isset($_SESSION['id'])||isset($_SESSION['page_id']))
{
    header("Location: http://www.redlay.com");
    exit();
}

?>
<!DOCTYPE html>

<html>
    <head>
        <title>Page Register</title>
        <?php include('required_header.php'); ?>
        <script type="text/javascript">
            function change_color()
            {
                $('body').css('background-color', 'rgb(30,30,30)');
                $('.page_register_input').css('outline-color', 'red');
                $('.box').css({'border': '5px solid rgb(220,21,0)', 'background-color': "white"});
                $('.page_register_text').css('color', 'black');
            }
            
            function register()
            {
                $.post('register_page.php',
                {
                    name: $('#register_name_input').val(),
                    type: $('#register_type_input').val(),
                    email: $('#register_email_input').val(),
                    password: $('#register_password_input').val(),
                    confirm_password: $('#register_confirm_password_input').val()
                }, function(output)
                {
                    $('#errors').html(output)
                    if(output=='Email has been sent! Email may be in spam folder')
                        $('#errors').addClass('good_errors').show();
                    else
                        $('#errors').addClass('bad_errors').show();
                });
            }
            function toggle_password_displays()
            {
                var password1=$('#register_password_input').val();
                var password_confirm_1=$('#register_confirm_password_input').val();

                if($('#register_password_input').attr('type')=="password")
                {
                    $('#toggle_password_displays').attr({'onmouseover': "display_title(this, 'Hide passwords');", 'onmouseout': "hide_title(this);"});
                    $('#password_1').html("<input type='text' class='index_input input_box' id='register_password_input' value='"+password1+"' placeholder='Password' onFocus='input_in(this);' />");
                    $('#register_password_input').attr('onBlur', "$(this).css({'border': '1px solid gray', 'box-shadow': 'inset 0px 0px 5px 0px gray'})");
                    $('#password_confirm_1').html("<input type='text' class='index_input input_box' id='register_confirm_password_input' value='"+password_confirm_1+"' placeholder='Confirm password' onFocus='input_in(this);' />");
                    $('#register_confirm_password_input').attr('onBlur', "$(this).css({'border': '1px solid gray', 'box-shadow': 'inset 0px 0px 5px 0px gray'})");
                }
                else
                {
                    $('#toggle_password_displays').attr({'onmouseover': "display_title(this, 'Show passwords in normal text');", 'onmouseout': "hide_title(this);"});
                    $('#password_1').html("<input type='password' class='index_input input_box' id='register_password_input' value='"+password1+"' onFocus='input_in(this);'  placeholder='Password' />");
                    $('#register_password_input').attr('onBlur', "$(this).css({'border': '1px solid gray', 'box-shadow': 'inset 0px 0px 5px 0px gray'})");
                    $('#password_confirm_1').html("<input type='password' class='index_input input_box' id='register_confirm_password_input' value='"+password_confirm_1+"' onFocus='input_in(this);' placeholder='Confirm password' />");
                    $('#register_confirm_password_input').attr('onBlur', "$(this).css({'border': '1px solid gray', 'box-shadow': 'inset 0px 0px 5px 0px gray'})");
                }
                $('.page_register_input').css('outline-color', 'red');
            }
            function toggle_other_options()
            {
                if($('#register_type_input').val()=='Company'||$('#register_type_input').val()=='Person') 
                    $('#other_register_row').hide(); 
                else 
                    $('#other_register_row').show();
            }
            $(document).ready(function()
            {
                toggle_other_options();
                $('#footer').css('width', '910px');
                <?php include('required_jquery.php'); ?>
                change_color();
            });
        </script>
        <script type="text/javascript">
            <?php include('required_google_analytics.js'); ?>
        </script>
    </head>
    <body>
        <?php include('required_html.php'); ?>
        <?php include('index_top.php'); ?>
        <div id="main">
            <div id="register_content" class="content box">
                <table id="RegistrationInformation">
                    <tr class="register_row">
                        <td colspan="2" align="center"><p class="register_text" id="register_title">Page Register</p></td>
                    </tr>
                    <tr class="register_row">
                        <td class="register_title_unit"><span class="page_register_text">Name:</span></td>
                        <td class="register_title_unit"><input type="text" class="index_input input_box" onFocus="input_in(this);" onBlur="$(this).css({'border': '1px solid gray', 'box-shadow': 'inset 0px 0px 5px 0px gray'})"  id="register_name_input" maxlength="255" placeholder="Name"/></td>
                    </tr>
                    <tr class="register_row">
                        <td class="register_title_unit"><span class="page_register_text">Type:</span></td>
                        <td class="register_title_unit">
                            <select id="register_type_input" class="page_register_input" onChange="toggle_other_options();">
                                <option id="company_option" value="Company" >Company</option>
                                <option id="person_option" value="Person" >Person</option>
                                <option id="other_option" value="Other" >Other</option>
                            </select>
                        </td>
                    </tr>
                    <tr class="register_row" id="other_register_row">
                        <td class="register_title_unit"><span class="page_register_text">Others type:</span></td>
                        <td class="register_title_unit">
                            <select id="register_other_type_input" class="page_register_input">
                                <option value="Place" >Place</option>
                                <option value="Product" >Product</option>
                                <option value="Movie" >Movie</option>
                                <option value="TV show" >TV Show</option>
                                <option value="Book" >Book</option>
                                <option value="Website" >Website</option>
                                <option value="Charity" >Charity</option>
                                <option value="Quote/Saying">Quote/Saying</option>
                            </select>
                        </td>
                    </tr>
                    <tr class="register_row">
                        <td class="register_title_unit"><span class="page_register_text">Email:</span></td>
                        <td class="register_title_unit"><input type="email" class="index_input input_box" onFocus="input_in(this);" onBlur="$(this).css({'border': '1px solid gray', 'box-shadow': 'inset 0px 0px 5px 0px gray'})" id="register_email_input" maxlength="255" placeholder="Email"/></td>
                    </tr>
                    <tr class="register_row">
                        <td class="register_title_unit"><span class="page_register_text">Password:</span></td>
                        <td class="register_title_unit" id="password_1"><input type="password" class="index_input input_box" placeholder="Password" onFocus="input_in(this);" onBlur="$(this).css({'border': '1px solid gray', 'box-shadow': 'inset 0px 0px 5px 0px gray'})" id="register_password_input"/></td>
                    </tr>
                    <tr class="register_row">
                        <td class="register_title_unit"><span class="page_register_text">Confirm Password:</span></td>
                        <td class="register_title_unit" id="password_confirm_1"><input type="password" class="index_input input_box" placeholder="Confirm password" onFocus="input_in(this);" onBlur="$(this).css({'border': '1px solid gray', 'box-shadow': 'inset 0px 0px 5px 0px gray'})" id="register_confirm_password_input"/></td>
                    </tr>
                    <tr class="register_row">
                        <td id="register_button" colspan="2" align="center"><input type="button" id="page_register_submit" class="green_button" value="Register" onClick="register();"/></td>
                    </tr>
                </table>
                <div id="toggle_passwords"><input type="checkbox" onClick="toggle_password_displays();" id="toggle_password_displays" onmouseover="display_title(this, 'Show passwords in normal text');" onmouseout="hide_title(this);"/></div>
            </div>
            <?php include('footer.php'); ?>
        </div>
    </body>
</html>