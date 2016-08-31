<div id="top">
    <div class="header_background"></div>
    <!--<a href="http://www.redlay.com"><img id="icon"  src="http://pics.redlay.com/pictures/redlay_title.png"/></a>-->
    <a href="http://www.redlay.com"><img id="icon"  src="http://pics.redlay.com/pictures/redlay_title_white_arial.png"/></a>
    <table id="login_box" style="left:365px;position:relative;top:3px;">
        <tbody>
            <tr>
                <td>
                    <input autofocus="" id="login_email_text_box" class="input_box" type="email" name="email" placeholder="Email" onfocus="{input_in(this);input_focus(this);}" onblur="{input_out(this);input_blur(this, 'Email');}" />
                </td>
                <td>
                    <input id="login_password_text_box" type="password" class="input_box" name="login_password" placeholder="Password" onfocus="{input_in(this);input_focus(this);}" onblur="{input_out(this);input_blur(this, 'Password');}" />
                </td>
                <td>
                    <input id="login_submitButton" type="button" name="login_submit" value="Log In" class="button red_button" onclick="login();"  />
                </td>
                <td>
                    <a class="link" href="http://www.redlay.com/recover_password.php?type=user"><p id="forgot_password_login" class="forgot_password" onmouseover="name_over(this);" onmouseout="name_out(this);" style="color:white;">Forgot password?</p></a>
                </td>
                <td>
                    <a href="http://www.redlay.com/page_index.php">
                        <div id="login_arrow_body">
                            <img src="http://pics.redlay.com/pictures/pages_icon.png" style="height: 22px;margin: 0 auto;position: relative;padding: 5px;vertical-align: middle;">
                        </div>
                    </a>
                </td>
            </tr>
        </tbody>
    </table>
    <!--<input class="button" id="page_register_button" type="button" value="Page Register" onmouseover="{display_title(this, 'Create a new Page');}" onmouseout="{hide_title(this);}" onClick="window.location.replace('http://www.redlay.com/page_register.php');"/>-->
    <!--<a class="link" href="http://www.redlay.com/register.php"><input class="button" id="registerButton" type="button" value="Create" onmouseover="{display_title(this, 'Create a new account');}" onmouseout="{hide_title(this);}" /></a>-->
<!--            <input class="button" id="page_login" type="button" value="Page Login" onmouseover="{display_title(this, 'Login as a Page');}" onmouseout="{hide_title(this);}" onClick="display_page_login();" />-->
    <!--<a class="link" href="http://www.redlay.com"><input class="button" id="login" type="button" value="Login" onmouseover="{display_title(this, 'Login to your account');}" onmouseout="{hide_title(this);}" /></a>-->
<!--            <a href="http://www.redlay.com/test_account_start.php"><input class="button" id="testButton" type="button" value="Test Account" onmouseover="{display_title(this, 'Try a free account!');}" onmouseout="{hide_title(this);}" /></a>-->
</div>
<!--<div id="top">
    <div class="header_background" ></div>
    <a href="http://www.redlay.com"><img id="icon"  src="http://pics.redlay.com/pictures/redlay_title.png"/></a>
    <div id="login_form_body" >
        <table style="position:relative;">
            <tbody>
                <tr>
                    <td>
                        <table id="login_box" >
                            <tbody>
                                <tr>
                                    <td style="padding-left:10px;">
                                        <input autofocus="" id="login_email_text_box" class="input_box" type="email" name="email" placeholder="Email" onfocus="{input_in(this);}" onblur="input_out(this);" />
                                    </td>
                                    <td>
                                        <input id="login_password_text_box" type="password" class="input_box" name="login_password" placeholder="Password" onfocus="{input_in(this); }" onblur="{input_out(this);}" />
                                    </td>
                                    <td>
                                        <input id="login_submitButton" type="button" name="login_submit" value="Log In" class="button red_button" onclick="login();"  />
                                    </td>
                                    <td style="padding-right:10px;">
                                        <a class="link" href="http://www.redlay.com/recover_password.php?type=user"><p id="forgot_password_login" class="forgot_password title_color" onmouseover="name_over(this);" onmouseout="name_out(this);">Forgot password?</p></a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                    <td>
                        <table id="page_login_box" >
                            <tbody>
                                <tr>
                                    <td style="padding-left:10px;">
                                        <input autofocus="" id="login_email_text_box" class="input_box" type="email" name="email" placeholder="Email" onfocus="{input_in(this);}" onblur="input_out(this);" />
                                    </td>
                                    <td>
                                        <input id="login_password_text_box" type="password" class="input_box" name="login_password" placeholder="Password" onfocus="{input_in(this); }" onblur="{input_out(this);}" />
                                    </td>
                                    <td>
                                        <input id="login_submitButton" type="button" name="login_submit" value="Log In" class="button red_button" onclick="page_login();"  />
                                    </td>
                                    <td style="padding-right:10px;">
                                        <a class="link" href="http://www.redlay.com/recover_password.php?type=page"><p id="forgot_password_login" class="forgot_password title_color" onmouseover="name_over(this);" onmouseout="name_out(this);">Forgot password?</p></a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>
        
        <div id="login_arrow_body" onClick="login_form(2);">
            <span id="login_arrow_text" style="color: gray;font-family: arial;text-align: center;padding-left: 10px;padding-right: 10px;vertical-align: middle;top: 6px;position: relative;"><</span>
        </div>
    </div>
    <input class="button" id="page_register_button" type="button" value="Page Register" onmouseover="{display_title(this, 'Create a new Page');}" onmouseout="{hide_title(this);}" onClick="window.location.replace('http://www.redlay.com/page_register.php');"/>
    <a class="link" href="http://www.redlay.com/register.php"><input class="button" id="registerButton" type="button" value="Create" onmouseover="{display_title(this, 'Create a new account');}" onmouseout="{hide_title(this);}" /></a>
            <input class="button" id="page_login" type="button" value="Page Login" onmouseover="{display_title(this, 'Login as a Page');}" onmouseout="{hide_title(this);}" onClick="display_page_login();" />
    <a class="link" href="http://www.redlay.com"><input class="button" id="login" type="button" value="Login" onmouseover="{display_title(this, 'Login to your account');}" onmouseout="{hide_title(this);}" /></a>
            <a href="http://www.redlay.com/test_account_start.php"><input class="button" id="testButton" type="button" value="Test Account" onmouseover="{display_title(this, 'Try a free account!');}" onmouseout="{hide_title(this);}" /></a>
</div>-->