$('#menu').hide();
<?php
    if($redlay_theme=="black") 
    {
        $is_profile=strstr($_SERVER['REQUEST_URI'], 'profile.php');
        if(($is_profile==false||get_user_background_pic($ID)==""))
            echo "$('body').css({'background-image': 'url(\'".get_default_background_pic($redlay_theme)."\')', 'background-position' :'center 50px'});";
        
        echo "$('#top_map').css('background-color' ,'rgb(30,30,30)');";
    }
    else if($redlay_theme=="white")
    {
        $is_profile=strstr($_SERVER['REQUEST_URI'], 'profile.php');
        if(($is_profile==false||get_user_background_pic($ID)==""))
        {
            echo "$('html').css({'height': '100%', 'background-color': 'whitesmoke'});";
            echo "$('#window_background').css('box-shadow', 'inset 0px 0px 150px gray');";
        }
        echo "$('#top_map').css('background-color' ,'whitesmoke');";
    }
    else if($redlay_theme=="aluminum")
    {
        $is_profile=strstr($_SERVER['REQUEST_URI'], 'profile.php');
        if(($is_profile==false||get_user_background_pic($ID)==""))
        {
            echo "$('html').css({'height': '100%', 'background-image': 'url(\'http://pics.redlay.com/pictures/themes/aluminum/aluminum_background.jpg\')', 'background-attachment': 'fixed'});";
            echo "$('#window_background').css('box-shadow', 'inset 0px 0px 150px gray');";
        }
        
        echo "$('#top_map').css('background-image', 'url(\'http://pics.redlay.com/pictures/themes/aluminum/aluminum_background.jpg\')');";
    }
    else if($redlay_theme=="neon")
    {
        $is_profile=strstr($_SERVER['REQUEST_URI'], 'profile.php');
        if(($is_profile==false||get_user_background_pic($ID)==""))
            echo "$('html').css({'height': '100%', 'background-image': 'url(\'http://pics.redlay.com/pictures/themes/neon/neon_background.jpg\')', 'background-attachment': 'fixed'});";

        echo "$('#window_background').css('box-shadow', 'inset 0px 0px 150px black');";
        echo "$('#top_map').css('background-image', 'url(\'http://pics.redlay.com/pictures/themes/neon/neon_background.jpg\')');";
    }
    else if($redlay_theme=="beach")
    {
        $is_profile=strstr($_SERVER['REQUEST_URI'], 'profile.php');
        if(($is_profile==false||get_user_background_pic($ID)==""))
            echo "$('html').css({'height': '100%', 'background-image': 'url(\'http://pics.redlay.com/pictures/themes/beach/beach_background.jpg\')', 'background-attachment': 'fixed', 'background-size': '100% 100%', 'background-position': '50% 100%'});";
        echo "$('#top_map').css('background-image', 'url(\'http://pics.redlay.com/pictures/themes/beach/beach_background.jpg\')');";
    }
    else if($redlay_theme=="fire and ice")
    {
        $is_profile=strstr($_SERVER['REQUEST_URI'], 'profile.php');
        if(($is_profile==false||get_user_background_pic($ID)==""))
            echo "$('html').css({'height': '100%', 'background-image': 'url(\'http://pics.redlay.com/pictures/themes/fire_and_ice/fire_and_ice_background.jpg\')', 'background-attachment': 'fixed', 'background-size': '100% 100%', 'background-position': '50% 100%'});";
        echo "$('#top_map').css('background-image', 'url(\'http://pics.redlay.com/pictures/themes/fire_and_ice/fire_and_ice_background.jpg\')');";
    }
    else if($redlay_theme=="custom")
    {
        $query=mysql_query("SELECT x_cord, y_cord, width, height FROM themes WHERE user_id=$_SESSION[id] LIMIT 1");
        if($query&&mysql_num_rows($query))
        {
            $array=mysql_fetch_row($query);
            $x_cord=$array[0];
            $y_cord=$array[1];
            $width=$array[2];
            $height=$array[3];
            
            $is_profile=strstr($_SERVER['REQUEST_URI'], 'profile.php');
            if($is_profile==false||get_user_background_pic($ID)=="")
                echo "$('html').css({'height': '100%', 'background-image': 'url(\'https://s3.amazonaws.com/redlay.users/users/$_SESSION[id]/themes/background.png\')', 'background-attachment': 'fixed', 'background-size': '$width% $height%', 'background-position': '50% 50%'});";
            echo "$('#top_map').css('background-image', 'url(\'https://s3.amazonaws.com/redlay.users/users/$_SESSION[id]/themes/background.png\')');";
        }
    }

?>

//centers window
var Document_width=($(window).width())/2;
var Profile_width=(935)/2;
if(Document_width-Profile_width>=0)
{
    //$('#main').css('margin-left', Document_width-Profile_width);
    $('#top').css('left', Document_width-Profile_width);
    $('#dynamic_message_box').css('left', Document_width-Profile_width);
    $('.alert_box').css('left', Document_width-250);
    $('#errors').css('left', Document_width-160);
}
$(window).resize(function()
{
    var Document_width=($(window).width())/2;    
    var Profile_width=(935)/2;
    var alert_box_width=($('.alert_box').width())/2;
    if(Document_width-Profile_width>=0)
    {
        //$('#main').css('margin-left', Document_width-Profile_width);
        $('#top').css('left', Document_width-Profile_width);
        $('#dynamic_message_box').css('left', Document_width-Profile_width);
        $('.alert_box').css('left', Document_width-alert_box_width);
        $('#errors').css('left', Document_width-160);
    }
    else
    {
        //$('#main').css('margin-left', '0px');
        $('#top').css('left', '0px');
        $('#dynamic_message_box').css('left', '0px');
        $('.alert_box').css('left', '217px');
        $('#errors').css('left', '300px');
    }
});

//puts user offline
window.onbeforeunload=function()
{
    $.ajax({
        type:'POST',
        url: 'online.php',
        data:{
            'num':3
        },
        async:false
    });
};

$(window).unload( function ()
{
    $.ajax({
        type:'POST',
        url: 'online.php',
        data:{
            'num':3
        },
        async:false
    });
});