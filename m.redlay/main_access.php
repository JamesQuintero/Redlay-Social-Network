<?php

//uses main site scripts for mobile site features. 

@include('init.php');
if(!isset($_SESSION['id'])&&!isset($_SESSION['page_id']))
{
    header("Location: http://m.redlay.com/");
    exit();
}
else
{
    $access=(int)($_POST['access']);
    //gets profile stuff
    //   posts
    //   photos
    //   adds
    //   activity
    //   etc.
    if($access==1)
        include('../profile_query.php');
    
    //gets comments
    else if($access==2)
        include('../user_post_comment_query.php');
    
    //gets everything for home page
    else if($access==3)
        include('../home_query.php');
    
    //likes comment
    else if($access==4)
        include('../like_comment.php');
    
    //dislike comment
    else if($access==5)
        include('../dislike_comment.php');
    
    //unlikes comment
    else if($access==6)
        include('../unlike_comment.php');
    
    //undislikes comment
    else if($access==7)
        include('../undislike_comment.php');
    
    //posts
    else if($access==8)
        include('../post.php');
    
    //likes post
    else if($access==9)
        include('../like_post.php');
    
    //dislikes post
    else if($access==10)
        include('../dislike_post.php');
    
    //unlikes post
    else if($access==11)
        include('../unlike_post.php');
    
    //undislikes_post
    else if($access==12)
        include('../undislike_post.php');
    
    //comments
    else if($access==13)
        include('../comment.php');
    
    //likes page's post
    else if($access==14)
        include('../like_page_post.php');
    
    //unlikes page's post
    else if($access==15)
        include('../unlike_page_post.php');
    
    //dislikes page's post
    else if($access==16)
        include('../dislike_page_post.php');
    
    //undislikes page's post
    else if($access==17)
        include('../undislike_page_post.php');
    
    //comments page
    else if($access==18)
        include('../page_post_comment.php');
    
    
    
    
    //gets groups for home page
    else if($access==19)
        include('../home_names_query.php');
    
    //deletes photo
    else if($access==20)
        include('../delete_photo.php');
    
    //deletes user
    else if($access==21)
        include('../unfriend.php');
    
    //deletes photo's comment
    else if($access==22)
        include('../delete_photo_comment.php');
    
    //likes photo
    else if($access==23)
        include('../like_photo.php');
    
    //dislikes photo
    else if($access==24)
        include('../dislike_photo.php');
    
    //unlikes photo
    else if($access==25)
        include('../unlike_photo.php');
    
    //undislike photo
    else if($access==26)
        include('../undislike_photo.php');
    
    //comments on photo
    else if($access==27)
        include('../comment_picture.php');
    
    //gets photo comments
    else if($access==28)
        include('../view_photo_query.php');
    
    //gets photo's timestamp
    else if($access==29)
        include('../view_photo_query.php');
    
    //gets add requests
    else if($access==30)
        include('../friend_request_alerts_query.php');
    
    //gets alerts
    else if($access==31)
        include('../alert_query.php');
    
    //adds user
    else if($access==32)
        include('../add.php');
    
    //gets user's groups
    else if($access==33)
        include('../user_groups_query.php');
    
    //gets registration intro query
    else if($access==34)
        include('../registration_intro_query.php');
    
    //changes settings
    else if($access==35)
        include('../settings_query.php');
    
    //changes country
    else if($access==36)
        include('../change_country.php');
    
    //finishes registration intro
    else if($access==37)
        include('../finish_registration_intro.php');
    
    //messages user
    else if($access==38)
        include('../message_user.php');
    
    //gets alerts for alert page
    else if($access==39)
        include('../alert_page_query.php');
    
    //gets messages
    else if($access==40)
        include('../message_query.php');
    
    //gets/changes home view
    else if($access==41)
        include('../change_home_view.php');
    
}