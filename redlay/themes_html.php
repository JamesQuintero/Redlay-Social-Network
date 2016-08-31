<?php
if($redlay_theme=="black") 
{
    echo "$('.header_background').css({'background-color': 'rgb(30,30,30);'});";
}
else if($redlay_theme=="white")
{
    echo "$('.header_background').css('background-color', 'whitesmoke');";
    if(isset($_SESSION['id']))
    {
        if(strstr($_SERVER['REQUEST_URI'], 'profile.php')==false&&strstr($_SERVER['REQUEST_URI'], 'view_post.php')==false)
        {
            $background=get_user_background_pic($_SESSION['id']);
            if($background=="0")
            {
                echo "$('html').css({'height': '100%', 'background-color': 'whitesmoke'});";
                echo "$('#window_background').css('box-shadow', 'inset 0px 0px 150px gray');";
            }
            else
            {
                echo "$('html').css({'height': '100%'});";
                echo "$('#window_background').css('box-shadow', 'inset 0px 0px 150px black');";
            }
        }
        else
        {
            if(strstr($_SERVER['REQUEST_URI'], 'profile.php')==true)
                $background=get_user_background_pic($ID);
            else if(strstr($_SERVER['REQUEST_URI'], 'view_post.php')==true)
                $background=get_user_background_pic($profile_id);
                
            if($background=="0")
            {
                echo "$('html').css({'height': '100%', 'background-color': 'whitesmoke'});";
                echo "$('#window_background').css('box-shadow', 'inset 0px 0px 150px gray');";
            }
            else
            {
                echo "$('html').css({'height': '100%'});";
                echo "$('#window_background').css('box-shadow', 'inset 0px 0px 150px black');";
            }
        }
    }
    else
    {
        echo "$('html').css({'height': '100%', 'background-color': 'whitesmoke'});";
        echo "$('#window_background').css('box-shadow', 'inset 0px 0px 150px gray');";
    }
    
    echo "$('#search_box_submit_button').attr('class', 'white_button');";
    echo "$('#homeButton').attr('class', 'white_button');";
    echo "$('#profile_button').attr('class', 'white_button');";
    echo "$('#menu_button').attr('class', 'white_button');";
    echo "$('#top_map').css('background-color' ,'whitesmoke');";
}
else if($redlay_theme=="aluminum")
{
    echo "$('.header_background').css('background-image', 'url(\'http://pics.redlay.com/pictures/themes/aluminum/aluminum_background.jpg\')');";
    
    if(isset($_SESSION['id']))
    {
        if(strstr($_SERVER['REQUEST_URI'], 'profile.php')==false&&strstr($_SERVER['REQUEST_URI'], 'view_post.php')==false)
        {
            $background=get_user_background_pic($_SESSION['id']);
            if($background=="0")
            {
                echo "$('html').css({'height': '100%', 'background-image': 'url(\'http://pics.redlay.com/pictures/themes/aluminum/aluminum_background.jpg\')', 'background-attachment': 'fixed'});";
                echo "$('#window_background').css('box-shadow', 'inset 0px 0px 150px gray');";
            }
            else
            {
                echo "$('html').css({'height': '100%'});";
                echo "$('#window_background').css('box-shadow', 'inset 0px 0px 150px black');";
            }
        }
        else
        {
            if(strstr($_SERVER['REQUEST_URI'], 'profile.php')==true)
                $background=get_user_background_pic($ID);
            else if(strstr($_SERVER['REQUEST_URI'], 'view_post.php')==true)
                $background=get_user_background_pic($profile_id);
            else
                $background=get_user_background_pic($_SESSION['id']);
                
            if($background=="0")
            {
                echo "$('html').css({'height': '100%', 'background-image': 'url(\'http://pics.redlay.com/pictures/themes/aluminum/aluminum_background.jpg\')', 'background-attachment': 'fixed'});";
                echo "$('#window_background').css('box-shadow', 'inset 0px 0px 150px gray');";
            }
            else
            {
                echo "$('html').css({'height': '100%'});";
                echo "$('#window_background').css('box-shadow', 'inset 0px 0px 150px black');";
            }
        }
    }
    else
    {
        echo "$('html').css({'height': '100%', 'background-image': 'url(\'http://pics.redlay.com/pictures/themes/aluminum/aluminum_background.jpg\')', 'background-attachment': 'fixed'});";
        echo "$('#window_background').css('box-shadow', 'inset 0px 0px 150px gray');";
    }
    
    echo "$('#search_box_submit_button').attr('class', 'white_button');";
    echo "$('#search_box_submit_button').css('background-image', 'url(\'http://pics.redlay.com/pictures/themes/aluminum/aluminum_background.jpg\')');";
    echo "$('#homeButton').attr('class', 'white_button');";
    echo "$('#homeButton').css('background-image', 'url(\'http://pics.redlay.com/pictures/themes/aluminum/aluminum_background.jpg\')');";
    echo "$('#profile_button').attr('class', 'white_button');";
    echo "$('#profile_button').css('background-image', 'url(\'http://pics.redlay.com/pictures/themes/aluminum/aluminum_background.jpg\')');";
    echo "$('#menu_button').attr('class', 'white_button');";
    echo "$('#menu_button').css('background-image', 'url(\'http://pics.redlay.com/pictures/themes/aluminum/aluminum_background.jpg\')');";
    echo "$('#top_map').css('background-image', 'url(\'http://pics.redlay.com/pictures/themes/aluminum/aluminum_background.jpg\')');";
}
else if($redlay_theme=="neon")
{
    //header-http://www.wallsave.com/wallpapers/1600x1200/neon-colors/362744/neon-colors-colorful-multicolor-by-acgfly-362744.jpg
    //html-http://up.programosy.pl/foto/22924-1920x1200-glowcolor_by_operian.jpg
    
    echo "$('.header_background').css({'background-image': 'url(\'http://pics.redlay.com/pictures/themes/neon/neon_header_background.jpg\')', 'background-position': '50% 50%', 'background-size': '100%'});";
    
    if(isset($_SESSION['id']))
    {
        if(strstr($_SERVER['REQUEST_URI'], 'profile.php')==false&&strstr($_SERVER['REQUEST_URI'], 'view_post.php')==false)
        {
            $background=get_user_background_pic($_SESSION['id']);
            if($background=="0")
                echo "$('html').css({'height': '100%', 'background-image': 'url(\'http://pics.redlay.com/pictures/themes/neon/neon_background.jpg\')', 'background-attachment': 'fixed'});";
            else
                echo "$('html').css({'height': '100%'});";
        }
        else
        {
            if(strstr($_SERVER['REQUEST_URI'], 'profile.php')==true)
                $background=get_user_background_pic($ID);
            else if(strstr($_SERVER['REQUEST_URI'], 'view_post.php')==true)
                $background=get_user_background_pic($profile_id);
                
            if($background=="0")
                echo "$('html').css({'height': '100%', 'background-image': 'url(\'http://pics.redlay.com/pictures/themes/neon/neon_background.jpg\')', 'background-attachment': 'fixed'});";
            else
                echo "$('html').css({'height': '100%'});";
        }
    }
    else
        echo "$('html').css({'height': '100%', 'background-image': 'url(\'http://pics.redlay.com/pictures/themes/neon/neon_background.jpg\')', 'background-attachment': 'fixed'});";
    
    echo "$('#search_box_submit_button').attr('class', 'button');";
    echo "$('#search_box_submit_button').css({'background-image': 'url(\'http://pics.redlay.com/pictures/themes/neon/neon_header_background2.jpg\')', 'background-size': '80px 1065px', 'background-position': '50% 50%'});";
    echo "$('#homeButton').attr('class', 'button');";
    echo "$('#homeButton').css({'background-image': 'url(\'http://pics.redlay.com/pictures/themes/neon/neon_header_background2.jpg\')', 'background-size': '80px 1065px', 'background-position': '50% 50%'});";
    echo "$('#profile_button').attr('class', 'button');";
    echo "$('#profile_button').css({'background-image': 'url(\'http://pics.redlay.com/pictures/themes/neon/neon_header_background2.jpg\')', 'background-size': '80px 1065px', 'background-position': '50% 50%'});";
    echo "$('#menu_button').attr('class', 'button');";
    echo "$('#menu_button').css({'background-image': 'url(\'http://pics.redlay.com/pictures/themes/neon/neon_header_background2.jpg\')', 'background-size': '80px 1065px', 'background-position': '50% 50%'});";
    echo "$('#window_background').css('box-shadow', 'inset 0px 0px 150px black');";
    echo "$('#top_map').css('background-image', 'url(\'http://pics.redlay.com/pictures/themes/neon/neon_background.jpg\')');";
}
else if($redlay_theme=="beach")
{
    //header=http://www.mrwallpaper.com/wallpapers/Beautiful-Beach-1920x1200.jpg
    //html=http://www.mrwallpaper.com/wallpapers/Boat-Tropical-Beach-1680x1050.jpg
    
    echo "$('.header_background').css({'background-image': 'url(\'http://pics.redlay.com/pictures/themes/beach/beach_header_background.jpg\')', 'background-position': '50% 30%', 'background-size': '100%'});";
    
    if(isset($_SESSION['id']))
    {
        if(strstr($_SERVER['REQUEST_URI'], 'profile.php')==false&&strstr($_SERVER['REQUEST_URI'], 'view_post.php')==false)
        {
            $background=get_user_background_pic($_SESSION['id']);
            if($background=="0")
                echo "$('html').css({'height': '100%', 'background-image': 'url(\'http://pics.redlay.com/pictures/themes/beach/beach_background.jpg\')', 'background-attachment': 'fixed', 'background-size': '100% 100%', 'background-position': '50% 100%'});";
            else
                echo "$('html').css({'height': '100%'});";
        }
        else
        {
            if(strstr($_SERVER['REQUEST_URI'], 'profile.php')==true)
                $background=get_user_background_pic($ID);
            else if(strstr($_SERVER['REQUEST_URI'], 'view_post.php')==true)
                $background=get_user_background_pic($profile_id);
                
            if($background=="0")
                echo "$('html').css({'height': '100%', 'background-image': 'url(\'http://pics.redlay.com/pictures/themes/beach/beach_background.jpg\')', 'background-attachment': 'fixed', 'background-size': '100% 100%', 'background-position': '50% 100%'});";
            else
                echo "$('html').css({'height': '100%'});";
        }
    }
    else
        echo "$('html').css({'height': '100%', 'background-image': 'url(\'http://pics.redlay.com/pictures/themes/beach/beach_background.jpg\')', 'background-attachment': 'fixed', 'background-size': '100% 100%', 'background-position': '50% 100%'});";
    
    echo "$('#search_box_submit_button').attr('class', 'white_button');";
    echo "$('#homeButton').attr('class', 'white_button');";
    echo "$('#profile_button').attr('class', 'white_button');";
    echo "$('#menu_button').attr('class', 'white_button');";
    echo "$('#top_map').css('background-image', 'url(\'http://pics.redlay.com/pictures/themes/beach/beach_background.jpg\')');";
}
?>