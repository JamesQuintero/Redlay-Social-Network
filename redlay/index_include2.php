
<div id="LoginInformation">
    <form name="login_form">
        <p id="profile_text">Profile:</p>
        <p id="login_email">Email: </p><input id="login_email_text_box" type="email" name="email"/>
        <p id="login_password"> Password: </p><input id="login_password_text_box" type="password" name="login_password"/>
        <input id="login_submitButton" type="button" name="login_submit" value="Log In" class="green_button_index" onClick="login();" onmouseover="{submit_button_over('#login_submitButton'); green_button_over('#login_submitButton');}" onmouseout="{submit_button_out('#login_submitButton'); green_button_out('#login_submitButton');}"/>
    </form>
    <p id="forgot_password_login" class="forgot_password" onClick="window.location.replace('http://www.redlay.com/recover_password.php');">Forgot password?</p>
</div>
<div class="errors" id="login_errors">
</div>