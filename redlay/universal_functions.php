<?php
header('Content-type: text/html; charset=utf-8');


////////////////////USERS////////////////////////
function user_is_friends($ID, $ID2)
{
    if(is_id($ID)&&user_id_exists($ID)&&is_id($ID2)&&user_id_exists($ID2))
    {
        $query=mysql_query("SELECT user_friends FROM user_data WHERE user_id=$ID LIMIT 1");
        if($query&&mysql_num_rows($query)==1)
        {
            $array=mysql_fetch_row($query);
            $friends=explode('|^|*|', $array[0]);
            if($array[0]!='')
            {
                for($x =0; $x < sizeof($friends); $x++)
                {
                    if($friends[$x]==$ID2)
                        return "true";
                }
                return "false";
            }
            else
                return "false";
        }
        else
            return "false";
    }
    else
        return "false";
}
function is_correct_ip_address()
{
    $query=mysql_query("SELECT ip_addresses FROM users WHERE id=$_SESSION[id] LIMIT 1");
    if($query&&mysql_num_rows($query)==1)
    {
        $array=mysql_fetch_row($query);
        $ip_addresses=explode('|^|*|', $array[0]);

        $bool=false;
        for($x = 0; $x < sizeof($ip_addresses); $x++)
        {
            if($ip_addresses[$x]==$_SERVER['REMOTE_ADDR'])
                $bool=true;
        }
        return $bool;
    }
    return false;
}
function get_map_type()
{
    $query=mysql_query("SELECT map_type FROM user_maps WHERE user_id=$_SESSION[id] LIMIT 1");
    if($query&&mysql_num_rows($query)==1)
    {
        $array=mysql_fetch_row($query);
        return $array[0];
    }
}
function is_valid_month($month)
{
    //checks whether $month== every month possible
    if($month=="January"||$month=="February"||$month=="March"||$month=="April"
            ||$month=="May"||$month=="June"||$month=="July"||$month=="August"
            ||$month=="September"||$month=="October"||$month=="November"
            ||$month=="December")
        return true;
    else
        return false;
}
function get_email_settings($ID, $type)
{
    if(is_id($ID)&&user_id_exists($ID))
    {
        $query=mysql_query("SELECT email_settings FROM user_data WHERE user_id=$ID LIMIT 1");
        if($query&&mysql_num_rows($query)==1)
        {
            $array=mysql_fetch_row($query);
            $email_settings=explode('|^|*|', $array[0]);

            if($type=='accept_add_request')
                return $email_settings[0];
            else if($type=='posts_on_profile')
                return $email_settings[1];
            else if($type=="comments_on_post")
                return $email_settings[2];
            else if($type=='liked_comment')
                return $email_settings[3];
            else if($type=='disliked_comment')
                return $email_settings[4];
            else if($type=='post_like')
                return $email_settings[5];
            else if($type=='post_dislike')
                return $email_settings[6];
            else if($type=='message')
                return $email_settings[7];
            else if($type=='picture_comment')
                return $email_settings[8];
            else if($type=='photo_like')
                return $email_settings[9];
            else if($type=='photo_dislike')
                return $email_settings[10];
        }
        else
            send_mail_error("get_email_settings(): ",mysql_error());
    }
}
function send_mail_alert($ID, $information)
{
    require 'aws-sdk-for-php-master/sdk.class.php';
    
    
    //gets the email of the user to
    $query=mysql_query("SELECT email FROM users WHERE id=$ID LIMIT 1");
    if($query&&mysql_num_rows($query)==1)
    {
        $array=mysql_fetch_row($query);
        $email=$array[0];


        $user_name=get_user_name($_SESSION['id']);
//        $headers=array("From: redlay", "MIME-Version: 1.0", "Content-Type: text/html; charset=UTF-8", "Reply-to: <no-reply@redlay.com>");


        
//        $colors=get_user_display_colors($ID);
//        $email_content_top="<div style='display:block;min-height:400px;width:100%;position:absolute;background-color:rgb(30,30,30);'><table style='width:100%;'><tbody><tr><td><div style='width:100%;height:55px;position:relative;top:0px;left:0px;border-bottom:5px solid rgb(220,20,0);background-color:#1E1E1E;'><a href='http://www.redlay.com'><img src='http://www.redlay.com/pictures/redlay_title.png' style='top:0px;left:0px;position:absolute'/></a></div></td></tr><tr><td align='center'><div style='display:block;border-radius:3px;border:5px solid ".$colors[0].";margin-left:50px;margin-right:50px;position:relative;margin-top:15px;background-color:".$colors[1].";box-shadow: inset 0px 0px 3px 0px black; '>";
//        $email_content_body="<a href='http://www.redlay.com/profile.php?user_id=$_SESSION[id]'><img src='http://www.redlay.com/users/thumbs/users/$_SESSION[id]/0.jpg' style='margin-top:10px;width:100px;height:100px;border:1px solid black;'/></a>   <a style='text-decoration:none;color:$colors[0];' href='http://www.redlay.com/profile.php?user_id=$_SESSION[id]'><p style='color:$colors[0];text-decoration:underline;cursor:pointer;' >$user_name</p></a> <p class='email_message' style='color:$colors[2]'>";
//        $email_content_bottom="</p></div></td></tr>";
//        $email_content_footer="<tr><td><div style='margin-top:15px;margin-left:50px;margin-right:50px;position:relative;border:5px solid $colors[0];background-color:$colors[1];border-radius:2px;box-shadow:inset 0px 0px 3px 0px black;'><p style='color:$colors[2];text-align:center;margin-top:5px;margin-top:5px;font-weight:bold;'>redlay &#169; 2012</p><hr style='width:100%;'/>   <a href='http://www.redlay.com/about.php' style='text-decoration:none;'><span style='color:$colors[0];margin-left:15px;text-decoration:underline;font-weight:bold;margin-bottom:5px;'>About</span></a><a href='http://www.redlay.com/contact.php' style='text-decoration:none;'><span style='color:$colors[0];margin-left:15px;text-decoration:underline;font-weight:bold;margin-bottom:5px;'>Contact</span></a>    </div></td></tr></tbody></table></div>";

        if($information[0]=="accept_add_request")
        {
            $subject="New add";
            $message=$user_name." accepted your add request. http://www.redlay.com/profile_id=$_SESSION[id]";
        }
        else if($information[0]=="posts_on_profile")
        {
            $subject="Profile post";
            $message=$user_name." posted on your profile. http://www.redlay.com/view_post.php?post_id=$information[1]&&profile_id=$ID";
        }
        else if($information[0]=="comment_on_post")
        {
            $query=mysql_query("SELECT post_ids, comments FROM content WHERE user_id=$information[2] LIMIT 1");
            if($query&&mysql_num_rows($query)==1)
            {
                $array=mysql_fetch_row($query);
                $post_ids=explode('|^|*|', $array[0]);
                $comments=explode('|^|*|', $array[1]);
                
                $index=-1;
                for($x = 0; $x < sizeof($post_ids); $x++)
                {
                    if($post_ids[$x]==$information[1])
                        $index=$x;
                }
                
                if($index!=-1)
                {
                    $comments[$index]=explode('|%|&|', $comments[$index]);
                    $comment=end($comments[$index]);
                    $temp="\"$comment\".";
                }
                else
                    $temp="";
            }
            else
                $temp="";
            
            $subject="Comment on your post";
            $message=$user_name." commented on your post. $temp http://www.redlay.com/view_post.php?post_id=$information[1]&&profile_id=$information[2]";
        }
        else if($information[0]=="post_like")
        {
            $subject="Your post was liked";
            $message=$user_name." liked your post. http://www.redlay.com/view_post.php?post_id=$information[1]&&profile_id=$information[2]";
        }
        else if($information[0]=="post_dislike")
        {
            $subject="Your post was disliked";
            $message=$user_name." disliked your post. http://www.redlay.com/view_post.php?post_id=$information[1]&&profile_id=$information[2]";
        }
        else if($information[0]=="comment_like")
        {
            $subject="Your comment was liked";
            $message=$user_name." liked your comment on this post. http://www.redlay.com/view_post.php?post_id=$information[1]&&profile_id=$information[2]";
        }
        else if($information[0]=="comment_dislike")
        {
            $subject="Your comment was disliked";
            $message=$user_name." disliked your comment on this post. http://www.redlay.com/view_post.php?post_id=$information[1]&&profile_id=$information[2]";
        }
        else if($information[0]=="message")
        {
            $query=mysql_query("SELECT user_id_2, messages FROM messages WHERE user_id=$ID LIMIT 1");
            if($query&&mysql_num_rows($query)==1)
            {
                $array=mysql_fetch_row($query);
                $user_ids=explode('|^|*|', $array[0]);
                $messages=explode('|^|*|', $array[1]);
                
                $index=-1;
                for($x = 0; $x < sizeof($user_ids); $x++)
                {
                    if($user_ids[$x]==$_SESSION['id'])
                        $index=$x;
                }
                
                if($index!=-1)
                {
                    $messages[$index]=explode('|%|&|', $messages[$index]);
                    $message_sent=end($messages[$index]);
                    $temp="\"$message_sent\".";
                }
                else
                    $temp="";
            }
            else
                $temp="";
            
            $subject="You got a new message!";
            $message=$user_name." sent you a message. $temp http://www.redlay.com/messages.php";
        }
        else if($information[0]=="photo_comment")
        {
            $query=mysql_query("SELECT pictures, picture_comments FROM pictures WHERE user_id=$ID LIMIT 1");
            if($query&&mysql_num_rows($query)==1)
            {
                $array=mysql_fetch_row($query);
                $pictures=explode('|^|*|', $array[0]);
                $comments=explode('|^|*|', $array[1]);
                
                $index=-1;
                for($x = 0; $x < sizeof($pictures); $x++)
                {
                    if($pictures[$x]==$information[1])
                        $index=$x;
                }
                
                if($index!=-1)
                {
                    $comments[$index]=explode('|%|&|', $comments[$index]);
                    $temp_comment=end($comments[$index]);
                    $temp="\"$temp_comment\".";
                }
                else
                    $temp="";
            }
            else
                $temp="";
            
            $subject="Comment on your photo";
            $message=$user_name." commented on your photo. $temp http://www.redlay.com/view_photo.php?user_id=$ID&&picture_id=$information[1]&&type=user";
        }
        else if($information[0]=="photo_like")
        {
            $subject="Your photo was liked";
            $message=$user_name." liked your photo. http://www.redlay.com/view_photo.php?user_id=$ID&&picture_id=$information[1]&&type=user";
        }
        else if($information[0]=="photo_dislike")
        {
            $subject="Your photo was disliked";
            $message=$user_name." disliked your photo. http://www.redlay.com/view_photo.php?user_id=$ID&&picture_id=$information[1]&&type=user";
        }
        else if($information[0]=="like_photo_comment")
        {
            $subject="Your comment on a photo was liked";
            $message=$user_name." liked your comment on this photo. http://www.redlay.com/view_photo.php?user_id=$information[2]&&picture_id=$information[1]&&type=$information[3]";
        }
        else if($information[0]=="dislike_photo_comment")
        {
            $subject="Your comment on a photo was disliked";
            $message=$user_name." disliked your comment on this photo. http://www.redlay.com/view_photo.php?user_id=$information[2]&&picture_id=$information[1]&&type=$information[3]";
        }
        else
        {
            $subject="New alert";
            $message=$information;
        }


        $from=get_email_from();
        
        $array=array();
        $array['key']=ACCESS_KEY;
        $array['secret']=SECRET_KEY;
        $amazonSes = new AmazonSES($array);
        $amazonSes->verify_email_address($from);

        $response = $amazonSes->send_email($from,
            array('ToAddresses' => array($email)),
            array(
                'Subject.Data' => $subject,
                'Body.Text.Data' => $message,
            )
        );

        if (!$response->isOK())
        {
            send_mail_error("send_mail_alert(): ", "Something went wrong when sending alert email");
            // handle error
        }
//        if(!mail($email, $subject, $email_content_top.$email_content_body.$message.$email_content_bottom.$email_content_footer, implode("\r\n", $headers)))
//                send_mail_error("Error sending email in function send_mail_alert with variable type='$information[0]' and email='$email'");
    }
    else
        send_mail_error(mysql_error());
}
function sendAWSEmail($to, $subject, $message)
{
    require 'aws-sdk-for-php-master/sdk.class.php';
    
    $from=get_email_from();
    
    $array=array();
    $array['key']=ACCESS_KEY;
    $array['secret']=SECRET_KEY;
    $amazonSes = new AmazonSES($array);
    $amazonSes->verify_email_address($from);

    $response = $amazonSes->send_email($from,
        array('ToAddresses' => array($to)),
        array(
            'Subject.Data' => $subject,
            'Body.Text.Data' => $message,
        )
    );

    if (!$response->isOK())
    {
        send_mail_error("sendAWSEmail(): ", "Something went wrong when sending an email.");
        // handle error
        return false;
    }
    else
        return true;
}
//function terminate_account($ID)
//{
//   if(is_id($ID)&&user_id_exists($ID)&&!user_id_terminated($ID))
//   {
//      //steps of deletion
//      //delete photos from public
//      //delete posts from public
//      //delete videos from public
//      //delete photos
//      //delete adds
//      //delete photos
//      //delete files
//      //delete everything from database
//   }
//}
function num_pictures($ID)
{
    $query=mysql_query("SELECT pictures FROM pictures WHERE user_id=$ID LIMIT 1");
    if($query&&mysql_num_rows($query)==1)
    {
        $array=mysql_fetch_row($query);
        $pictures=explode('|^|*|', $array[0]);
        if($array[0]=='')
            return 0;
        else
            return sizeof($pictures);
    }
}
function completed_registration_intro($ID)
{
    $query=mysql_query("SELECT registration_intro FROM user_display WHERE user_id=$ID LIMIT 1");
    if($query&&mysql_num_rows($query)==1)
    {
        $array=mysql_fetch_row($query);
        if($array[0]=='yes')
            return true;
        else
            return false;
    }
}
function user_exists($email)
{
    $query=mysql_query("SELECT * FROM users WHERE email='$email' LIMIT 1");
    if($query&&mysql_num_rows($query)==0)
    {
        $query=mysql_query("SELECT * FROM temp_users WHERE email='$email' LIMIT 1");
        if($query&&mysql_num_rows($query)==0)
            return false;
        else
            return true;
    }
    else
        return true;
}
//ID is the user you want to check if ID2 blocked them.
function user_blocked($ID, $ID2)
{
    $query=mysql_query("SELECT blocked_users FROM user_data WHERE user_id=$ID2 LIMIT 1");
    if($query&&mysql_num_rows($query)==1)
    {
        $array=mysql_fetch_row($query);
        $blocked=explode('|^|*|', $array[0]);
        
        for($x = 0; $x < sizeof($blocked); $x++)
        {
            if($blocked[$x]==$ID)
                return true;
        }
        
        $query=mysql_query("SELECT blocked_users FROM user_data WHERE user_id=$ID LIMIT 1");
        if($query&&mysql_num_rows($query)==1)
        {
            $array=mysql_fetch_row($query);
            $blocked=explode('|^|*|', $array[0]);

            for($x = 0; $x < sizeof($blocked); $x++)
            {
                if($blocked[$x]==$ID2)
                    return true;
            }
            
            return false;
        }
    }
}
function is_unlocked($ID, $thing)
{
    $query=mysql_query("SELECT unlocked FROM user_data WHERE user_id=$ID LIMIT 1");
    if($query)
    {
        $array=mysql_fetch_row($query);
        $unlocked=explode('|^|*|', $array[0]);
        if(($thing=='colors'&&$unlocked[0]=='1')||($thing=='animated_profile_picture'&&$unlocked[1]=='1')
                ||($thing=='background_picture'&&$unlocked[2]=='1')||($thing=='profile_music'&&$unlocked[3]=='1'))
            return true;
        else
            return false;
    }
}
function pending_request($ID, $ID2)
{
    $query=mysql_query("SELECT other_user_id FROM pending_friend_requests WHERE user_id=$ID LIMIT 1");
    if($query&&mysql_num_rows($query)==1)
    {
        $array=mysql_fetch_row($query);
        $pending=explode('|^|*|', $array[0]);
        if($array[0]=='')
            return false;
        else
        {
            for($x =0; $x < sizeof($pending); $x++)
            {
                if($pending[$x]==$ID2)
                    return true;
            }
            return false;
        }
    }
}

function has_friend_request_alerts()
{
    $query=mysql_query("SELECT * FROM alerts WHERE user_id=$_SESSION[id] LIMIT 1");
    if($query&&mysql_num_rows($query)==1)
    {
        $array=mysql_fetch_array($query);
        if($array['new_friend_alerts']!=0)
            return 'true';
        else
            return 'false';
    }
}

function get_friend_request_alerts()
{
    $query=mysql_query("SELECT * FROM alerts WHERE user_id=$_SESSION[id] LIMIT 1");
    if($query)
    {
        if(mysql_num_rows($query)==1)
        {
            $array=mysql_fetch_array($query);
            return $array['new_friend_alerts'];
        }
    }
}
function has_messages_alerts()
{
    $query=mysql_query("SELECT new_messages FROM messages WHERE user_id=$_SESSION[id] LIMIT 1");
    if($query&&mysql_num_rows($query)==1)
    {
        $array=mysql_fetch_row($query);
        $new_messages=explode('|^|*|', $array[0]);
        
        for($x = 0; $x < sizeof($new_messages); $x++)
        {
            if($new_messages[$x]!=0)
                return true;
        }
        return false;
    }
    else
        return false;
}
function get_messages_alerts()
{
    $query=mysql_query("SELECT new_messages FROM messages WHERE user_id=$_SESSION[id] LIMIT 1");
    if($query&&mysql_num_rows($query)==1)
    {
        $array=mysql_fetch_row($query);
        $new_messages=explode('|^|*|', $array[0]);
        
        $count=0;
        for($x = 0; $x < sizeof($new_messages); $x++)
            $count+=$new_messages[$x];
        
        return $count;
        
    }
}
function has_messages_alerts_page()
{
    $query=mysql_query("SELECT new_messages FROM page_messages WHERE page_id=$_SESSION[page_id] LIMIT 1");
    if($query&&mysql_num_rows($query)==1)
    {
        $array=mysql_fetch_row($query);
        $new_messages=explode('|^|*|', $array[0]);
        
        for($x = 0; $x < sizeof($new_messages); $x++)
        {
            if($new_messages[$x]!=0)
                return true;
        }
        return false;
    }
    else
        return false;
}
function get_messages_alerts_page()
{
    $query=mysql_query("SELECT new_messages FROM page_messages WHERE page_id=$_SESSION[page_id] LIMIT 1");
    if($query&&mysql_num_rows($query)==1)
    {
        $array=mysql_fetch_row($query);
        $new_messages=explode('|^|*|', $array[0]);
        
        $count=0;
        for($x = 0; $x < sizeof($new_messages); $x++)
            $count+=$new_messages[$x];
        
        return $count;
    }
}
function has_alert_alerts()
{
    $query=mysql_query("SELECT new_alerts FROM alerts WHERE user_id=$_SESSION[id] LIMIT 1");
    if($query && mysql_num_rows($query)==1)
    {
        $array=mysql_fetch_row($query);
        if($array[0]!=0)
            return 'true';
        else
            return 'false';
    }
    else
        return 'false';
}
function get_alert_alerts()
{
    $query=mysql_query("SELECT new_alerts FROM alerts WHERE user_id=$_SESSION[id] LIMIT 1");
    if($query && mysql_num_rows($query)==1)
    {
        $array=mysql_fetch_row($query);
        return $array[0];
    }
}
function has_alert_alerts_page()
{
    $query=mysql_query("SELECT new_alerts FROM page_alerts WHERE page_id=$_SESSION[page_id] LIMIT 1");
    if($query && mysql_num_rows($query)==1)
    {
        $array=mysql_fetch_row($query);
        if($array[0]!=0)
            return true;
        else
            return false;
    }
    else
        return false;
}
function get_alert_alerts_page()
{
    $query=mysql_query("SELECT new_alerts, alert_ids, alert_user_ids, alert_information, alerts_read, alert_timestamps FROM page_alerts WHERE page_id=$_SESSION[page_id] LIMIT 1");
    if($query && mysql_num_rows($query)==1)
    {
        $array=mysql_fetch_row($query);
        $new_alerts=$array[0];
        $alert_ids=explode('|^|*|', $array[1]);
        $alert_user_ids=explode('|^|*|', $array[2]);
        $alert_information=explode('|^|*|', $array[3]);
        $alerts_read=explode('|^|*|', $array[4]);
        $alert_timestamps=explode('|^|*|', $array[5]);
        
        //gets rid of old alerts
        if(sizeof($alert_ids)>=200)
        {
            $temp_alert_ids=array();
            $temp_alert_user_ids=array();
            $temp_alert_information=array();
            $temp_alerts_read=array();
            $temp_alert_timestamps=array();
            
            for($x = 1; $x < 200; $x++)
            {
                $temp_alert_ids[]=$alert_ids[$x];
                $temp_alert_user_ids[]=$alert_user_ids[$x];
                $temp_alert_information[]=$alert_information[$x];
                $temp_alerts_read[]=$alerts_read[$x];
                $temp_alert_timestamps[]=$alert_timestamps[$x];
            }
            
            $alert_ids=implode('|^|*|', $temp_alert_ids);
            $alert_user_ids=implode('|^|*|', $temp_alert_user_ids);
            $alert_information=implode('|^|*|', $temp_alert_information);
            $alerts_read=implode('|^|*|', $temp_alerts_read);
            $alert_timestamps=implode('|^|*|', $temp_alert_timestamps);
            
            $query=mysql_query("UPDATE page_alerts SET alert_ids='$alert_ids', alert_user_ids='$alert_user_ids', alert_information='$alert_information', alerts_read='$alerts_read', alert_timestamps='$alert_timestamps' WHERE page_id=$_SESSION[page_id] ");
        }
        else
            $temp_new_alerts=$new_alerts;
        
        
        
        return $temp_new_alerts;
    }
}
function get_user_name($user_id)
{
    if(is_id($user_id)&&user_id_exists($user_id))
    {
        $query=mysql_query("SELECT firstName, lastName FROM users WHERE id=$user_id LIMIT 1");
        if($query&&mysql_num_rows($query)==1)
        {
                $array=mysql_fetch_row($query);
                $firstName=$array[0];
                $lastName=$array[1];

                $name=$firstName." ".$lastName;
                return $name;
        }
        else
            return "";
    }
    else
        return "";
}
function get_friends($user_id)
{
    $query=mysql_query("SELECT user_friends FROM user_data WHERE user_id=$user_id LIMIT 1");
    if($query)
    {
            $array=mysql_fetch_row($query);
            $friends=explode('|^|*|', $array[0]);

            if($array[0]=='')
                return array();
            return $friends;
    }
}
function get_all_friends($ID)
{
    $query=mysql_query("SELECT user_friends FROM user_data WHERE user_id=$ID LIMIT 1");
    if($query&&mysql_num_rows($query)==1)
    {
            $array=mysql_fetch_row($query);
            $friends=explode('|^|*|', $array[0]);
            if($array[0]=='')
                return array();
            return $friends;
    }
}

//checks if the ID is a useable number
function is_id($ID)
{
    if($ID!='')
    {
        $ID=preg_split('//', $ID, -1);
        $bool=true;
        for($x = 0; $x < sizeof($ID); $x++)
        {
            if(!($ID[$x]>0||$ID[$x]==0||$ID[$x]<0))
                $bool=false;
        }
        return $bool;
    }
    else
        return false;
}
function user_id_exists($ID)
{
    $query=mysql_query("SELECT id FROM users WHERE id=$ID LIMIT 1");
    if($query&&mysql_num_rows($query)==1)
        return true;
    else
        return false;
}
function get_likes($ID)
{
    $query=mysql_query("SELECT likes FROM updates WHERE user_id=$ID LIMIT 1");
    if($query);
    {
        $array=mysql_fetch_row($query);
        $likes=explode('|^|*|', $array[0]);
        for($x = 0; $x < sizeof($likes); $x++)
            $likes[$x]=explode('|%|&|', $likes[$x]);
        return $likes;
    }
}
function get_page_likes($ID)
{
    $query=mysql_query("SELECT page_likes FROM user_data WHERE user_id=$ID LIMIT 1");
    if($query)
    {
        $array=mysql_fetch_row($query);
        $likes=explode('|^|*|', $array[0]);
        if($array[0]=='')
            return array();
        else
            return $likes;
    }
}
function get_friend_title($ID)
{
    $query=mysql_query("SELECT friend_title FROM user_display WHERE user_id=$ID LIMIT 1");
    if($query&&mysql_num_rows($query)==1)
    {
        $array=mysql_fetch_row($query);
        $friend_title=$array[0];
        return $friend_title;
    }
}
function get_post_title($ID)
{
    $sql="SELECT * FROM user_display WHERE user_id=$ID LIMIT 1";
    $query=mysql_query($sql);
    if($query)
    {
        $count=mysql_num_rows($query);
        if($count==1)
        {
            $array=mysql_fetch_array($query);
            $title=$array['post_title'];
            return $title;
        }
    }
}

function get_information_title($ID)
{
    $sql="SELECT * FROM user_display WHERE user_id=$ID LIMIT 1";
    $query=mysql_query($sql);
    if($query)
    {
        $count=mysql_num_rows($query);
        if($count==1)
        {
            $array=mysql_fetch_array($query);
            $title=$array['information_title'];
            return $title;
        }
    }
}
function user_id_terminated($user_id)
{
    if($user_id!=''&&is_id($user_id)&&user_id_exists($user_id))
    {
        $query=mysql_query("SELECT closed FROM closed_accounts WHERE user_id=$user_id LIMIT 1");
        if($query&&mysql_num_rows($query)==1)
            return true;
//        {
//            $array=mysql_fetch_row($query);
//            $terminated=$array[0];
//            
//            if($terminated=='no')
//                return false;
//            else
//                return true;
//        }
        else
            return false;
    }
    else
        return false;
}
//array is the array of post ids from the user's update row, and index is the index to search for
function get_post_index($ID, $post_index)
{
    $query=mysql_query("SELECT post_id FROM updates WHERE user_id=$ID LIMIT 1");
    if($query&&mysql_num_rows($query)==1)
    {
        $array=mysql_fetch_row($query);
        $post_ids=explode('|^|*|', $array[0]);

        $index=-1;
        for($x = 0; $x < sizeof($post_ids); $x++)
        {
            if($post_ids[$x]==$post_index)
                $index=$x;
        }
        return $index;
    }
}
function get_email_from()
{
    return "no-reply@redlay.com";
}
function log_error($error, $second_error)
{
    require 'aws-sdk-for-php-master/sdk.class.php';
    
    $from=get_email_from();
        
    $array=array();
    $array['key']=ACCESS_KEY;
    $array['secret']=SECRET_KEY;
    $amazonSes = new AmazonSES($array);
    $amazonSes->verify_email_address($from);

    $response = $amazonSes->send_email($from,
        array('ToAddresses' => array('EMAIL_THAT_LOGS_BUGS')),
        array(
            'Subject.Data' => $error,
            'Body.Text.Data' => $second_error,
        )
    );

    if (!$response->isOK())
    {
        "Well I'm screwd";
    }
}
function get_countries()
{
    $query=mysql_query("SELECT countries FROM data WHERE num=1");
    if($query)
    {
        $array=mysql_fetch_row($query);
        $countries=explode('|^|*|', $array[0]);
        return $countries;
    }
}
function picture_exists($ID, $picture_id)
{
    $query=mysql_query("SELECT pictures FROM pictures WHERE user_id=$ID LIMIT 1");
    if($query)
    {
        $array=mysql_fetch_row($query);
        $names=explode('|^|*|', $array[0]);

        $bool=false;
        for($x = 0; $x < sizeof($names); $x++)
        {
            if($names[$x]==$picture_id)
                $bool=true;
        }
        return $bool;
    }
}
function get_picture_index($ID, $picture_id)
{
    $query=mysql_query("SELECT pictures FROM pictures WHERE user_id=$ID LIMIT 1");
    if($query&&mysql_num_rows($query)==1)
    {
        $array=mysql_fetch_row($query);
        $pictures=explode('|^|*|', $array[0]);
        for($x = 0; $x < sizeof($pictures); $x++)
        {
            if($pictures[$x]==$picture_id)
                return $x;
        }
        return -1;
    }
}
function get_picture_description($ID, $index)
{
    $query=mysql_query("SELECT picture_descriptions FROM pictures WHERE user_id=$ID LIMIT 1");
    if($query)
    {
        $array=mysql_fetch_row($query);
        $descriptions=explode('|^|*|', $array[0]);
        return $descriptions[$index];
    }
}
function add_alert($ID, $information)
{
    if(is_id($ID)&&user_id_exists($ID))
    {
        $query=mysql_query("SELECT alert_ids, alert_timestamps, alert_information, new_alerts, alerts_read, alert_user_ids FROM alerts WHERE user_id=$ID LIMIT 1");
        if($query&&mysql_num_rows($query)==1)
        {
            $array=mysql_fetch_row($query);

            $alert_ids=explode('|^|*|', $array[0]);
            $alert_timestamps=explode('|^|*|', $array[1]);
            $alert_information=explode('|^|*|', $array[2]);
            $new_alerts=(int)$array[3];
            $alerts_read=explode('|^|*|', $array[4]);
            $alert_user_ids=explode('|^|*|', $array[5]);


            $date=get_date();
            if($array[5]=='')
            {
                $alert_ids[0]=0;
                $alert_user_ids[0]=$_SESSION['id'];
                $alert_timestamps[0]=$date;
                $alert_information[0]=implode('|%|&|', $information);
                $new_alerts++;
                $alerts_read[0]=0;
            }
            else
            {
                $alert_ids[]=$alert_ids[sizeof($alert_ids)-1]+1;
                $alert_user_ids[]=$_SESSION['id'];
                $alert_timestamps[]=$date;
                $alert_information[]=implode('|%|&|', $information);
                $new_alerts++;
                $alerts_read[]=0;
            }

            $alert_ids=implode('|^|*|', $alert_ids);
            $alert_timestamps=implode('|^|*|', $alert_timestamps);
            $alert_information=implode('|^|*|', $alert_information);
            $alerts_read=implode('|^|*|', $alerts_read);
            $alert_user_ids=implode('|^|*|', $alert_user_ids);

            $query=mysql_query("UPDATE alerts SET alert_ids='$alert_ids', alert_timestamps='$alert_timestamps', alert_information='$alert_information', alerts_read='$alerts_read', new_alerts='$new_alerts', alert_user_ids='$alert_user_ids' WHERE user_id=$ID");
        }
    }
}
function get_user_colors($ID)
{
    $query=mysql_query("SELECT * FROM user_display WHERE user_id=$ID LIMIT 1");
    if($query&&mysql_num_rows($query)==1)
    {
        $array=mysql_fetch_array($query);
        return explode('|^|*|', $array['user_colors']);
    }
}
function get_user_display_colors($ID)
{

    $query=mysql_query("SELECT display_colors, background_fixed FROM user_display WHERE user_id=$ID LIMIT 1");
    if($query&&mysql_num_rows($query)==1)
    {
        $array=mysql_fetch_row($query);
        $colors=explode('|^|*|', $array[0]);
        
        $colors[3]=$colors[3]/100;
        
        $new_colors[0]="rgb($colors[0])";
        $new_colors[1]="rgba($colors[1],$colors[3])";
        $new_colors[2]="rgb($colors[2])";
        $new_colors[4]="none";
        $new_colors[5]=$array[1];
        return $new_colors;
    }
}
function get_page_display_colors($ID)
{
    $query=mysql_query("SELECT display_colors, background_fixed FROM page_display WHERE page_id=$ID LIMIT 1");
    if($query&&mysql_num_rows($query)==1)
    {
        $array=mysql_fetch_row($query);

        $colors=explode('|^|*|', $array[0]);
        
        $colors[3]=$colors[3]/100;
        
        $new_colors[0]="rgb($colors[0])";
        $new_colors[1]="rgba($colors[1],$colors[3])";
        $new_colors[2]="rgb($colors[2])";
        $new_colors[4]="none";
        $new_colors[5]=$array[1];
        return $new_colors;
    }
}

//function get_user_document_names($ID)
//{
//    $query=mysql_query("SELECT * FROM user_documents WHERE user_id=$ID LIMIT 1");
//    if($query&&mysql_num_rowS($query)==1)
//    {
//        $array=mysql_fetch_array($query);
//        $names=explode('|^|*|', $array['document_names']);
//    }
//    return $names;
//}
//function get_received_document_names($ID)
//{
//    $query=mysql_query("SELECT * FROM user_documents WHERE user_id=$ID LIMIT 1");
//    if($query&&mysql_num_rowS($query)==1)
//    {
//        $array=mysql_fetch_array($query);
//        $names=explode('|^|*|', $array['file_received']);
//    }
//    return $names;
//}
//function get_document_contents()
//{
//    $contents=array();
//    $path="users/docs/$_SESSION[id]";
//    $directory=opendir($path);
//    while($file=readdir($directory))
//    {
//        if(substr($file, 0, 1)!=".")
//        {
//            $contents[]=str_replace("'", "\'", file_get_contents($path."/archive/".$file));
//        }
//    }
//    closedir($directory);
//    return $contents;
//}
//function get_file_name($num)
//{
//    $query=mysql_query("SELECT * FROM user_documents WHERE user_id=$_SESSION[id] LIMIT 1");
//    if($query&&mysql_num_rows($query)==1)
//    {
//        $array=mysql_fetch_array($query);
//        $names=explode('|^|*|', $array['document_names']);
//        return $names[$num];
//    }
//}
//function get_received_file_name($num)
//{
//    $query=mysql_query("SELECT * FROM user_documents WHERE user_id=$_SESSION[id] LIMIT 1");
//    if($query&&mysql_num_rows($query)==1)
//    {
//        $array=mysql_fetch_array($query);
//        $name=explode('|^|*|', $array['file_received']);
//        if($name[$num]=='')
//            return '';
//        else
//            return $name[$num];
//    }
//}
//function has_document_alerts()
//{
//    $query=mysql_query("SELECT * FROM user_documents WHERE user_id=$_SESSION[id] LIMIT 1");
//    if($query&&mysql_num_rows($query)==1)
//    {
//        $array=mysql_fetch_array($query);
//        if($array['new_document_alerts']==0)
//            return 'false';
//        else
//            return 'true';
//    }
//    else
//        return 'false';
//}
//function get_document_alerts()
//{
//    $query=mysql_query("SELECT * FROM user_documents WHERE user_id=$_SESSION[id] LIMIT 1");
//    if($query&&mysql_num_rows($query)==1)
//    {
//        $array=mysql_fetch_array($query);
//        return $array['new_document_alerts'];
//    }
//}
function get_background_repeat($ID)
{
    $query=mysql_query("SELECT * FROM user_display WHERE user_id=$ID LIMIT 1");
    if($query&&mysql_num_rows($query)==1)
    {
        $array=mysql_fetch_array($query);
        $num=$array['background_repeat'];
        if($num==1)
            return "true";
        else
            return "false";
    }
}
function get_user_privacy_settings($ID)
{
    $query=mysql_query("SELECT general, display_non_friends, search_options FROM user_privacy WHERE user_id=$ID LIMIT 1");
    if($query&& mysql_num_rows($query)==1)
    {
        $array=mysql_fetch_row($query);
        $general=explode('|^|*|', $array[0]);
        $display_non_friends=explode('|^|*|', $array[1]);
        $search_settings=explode('|^|*|', $array[2]);

        $privacy=array();
        $privacy[0]=$general;
        $privacy[1]=$display_non_friends;
        $privacy[2]=$search_settings;
        return $privacy;
    }
}
function get_all_user_information($ID)
{
    $privacy=get_user_privacy_settings($ID);
    if($privacy[1][0]=='yes'||$ID==$_SESSION['id'])
    {
        $query=mysql_query("SELECT * FROM user_data WHERE user_id=$ID LIMIT 1");
        $query2=mysql_query("SELECT * FROM user_display WHERE user_id=$ID LIMIT 1");
        $query3=mysql_query("SELECT * FROM users WHERE id=$ID LIMIT 1");
        $query4=mysql_query("SELECT * FROM user_privacy WHERE user_id=$ID LIMIT 1");
        $query5=mysql_query("SELECT * FROM updates WHERE user_id=$ID LIMIT 1");
        $query6=mysql_query("SELECT * FROM pictures WHERE user_id=$ID LIMIT 1");
        if($query&&mysql_num_rows($query)==1&&$query2&&mysql_num_rows($query2)==1&&$query3&&mysql_num_rows($query3)==1&&$query4&&mysql_num_rows($query4)==1&&$query5&&mysql_num_rows($query5)==1&&$query6&&mysql_num_rows($query6)==1)
        {
            $array=mysql_fetch_array($query);
            $array2=mysql_fetch_array($query2);
            $array3=mysql_fetch_array($query3);
            $array4=mysql_fetch_array($query4);
            $array5=mysql_fetch_array($query5);
            $array6=mysql_fetch_array($query6);
            $privacy=explode('|^|*|', $array4['display_non_friends']);

            $list=array();
            //name
            $list[0]=$array3['firstName']." ".$array3['lastName'];
            //number of friends
            $list[1]=sizeof(explode('|^|*|', $array['user_friends']));
            //number of videos
            $list[2]=sizeof(explode('|^|*|', $array['user_videos']));
            //relationship status
            $list[3]=$array['user_relationship'];

            //birthdays
            $birthday_year=$array2['birthday_year'];
            $birthday=explode('|^|*|', $array['user_birthday']);
            if($birthday_year=='yes')
                $list[4]=$birthday[0]." ".$birthday[1]." ".$birthday[2];
            else
                $list[4]=$birthday[0]." ".$birthday[1];
            //gender
            $list[5]=$array['user_sex'];
            //bio
            $list[6]=str_replace("'", "\'", $array['user_bio']);
            //school
            $list[7]=$array['user_school'];
            //mood
            $list[8]=$array['user_mood'];
            //number of pages they liked
            $list[9]=sizeof(explode('|^|*|', $array['page_likes']));
            //number of updates
            $list[10]=sizeof(explode('|^|*|', $array5['updates']));
            //number of likes
            $list[11]=0;
            if($array5['likes']!='')
            {
                $likes=explode('|^|*|', $array5['likes']);
                $count=0;
                for($x = 0; $x < sizeof($likes); $x++)
                {
                    if($likes[$x]!='0')
                        $count=$count+sizeof(explode('|%|&|', $likes[$x]));
                }
                $list[11]=$count;
            }
            //number of dislikes
            $list[12]=0;
            if($array5['dislikes']!='')
            {
                $dislikes=explode('|^|*|', $array5['dislikes']);
                $count=0;
                for($x = 0; $x < sizeof($dislikes); $x++)
                {
                    if($dislikes[$x]!='0')
                        $count=$count+sizeof(explode('|%|&|', $dislikes[$x]));
                }
                $list[12]=$count;
            }
            $list[13]=sizeof($array6['pictures']);
            $list[14]=$array3['timestamps'];
            return $list;
        }
        else
            return array();
    }
    else
        return array();
}
function get_user_picture($ID, $index)
{
    $query=mysql_query("SELECT pictures FROM pictures WHERE user_id=$ID LIMIT 1");
    if($query)
    {
        $array=mysql_fetch_row($query);
        $names=explode('|^|*|', $array[0]);
        return $names[$index];
    }
}
function get_post_years($ID)
{
    $query=mysql_query("SELECT timestamp FROM updates WHERE user_id=$ID LIMIT 1");
    if($query)
    {
        $array=mysql_fetch_row($query);

        if($array[0]!='')
        {
            //gets the timestamps and explodes them into an array. year is at index 2
            $post_timestamps=explode('|^|*|', $array[0]);
            for($x = 0; $x < sizeof($post_timestamps); $x++)
                $post_timestamps[$x]=explode(' ', $post_timestamps[$x]);

            //finds the highest and lowest year out of all timestamps
            $high=$post_timestamps[0][2];
            $low=$post_timestamps[0][2];
            for($x = 0; $x < sizeof($post_timestamps); $x++)
            {
                if($post_timestamps[$x][2]>$high)
                    $high=$post_timestamps[$x][2];
                else if($post_timestamps[$x][2]<$low)
                    $low=$post_timestamps[$x][2];
            }
            //adds a year to years in case high is the same as low
            $years[0]=$post_timestamps[0][2];
            //adds the year
            for($x = 0; $x < $high-$low; $x++)
                $years[$x]=$low+$x;

            //returns the years of the posts
            return $years;
        }
        else
        {
            return array();
        }
    }
}
function get_post_months($ID)
{
    $query=mysql_query("SELECT timestamp FROM updates WHERE user_id=$ID LIMIT 1");
    if($query&&mysql_num_rows($query)==1)
    {
        $array=mysql_fetch_row($query);
        $post_timestamps=explode('|^|*|', $array[0]);
        for($x = 0; $x < sizeof($post_timestamps); $x++)
            $post_timestamps[$x]=explode(' ', str_replace(',', '', $post_timestamps[$x]));
        //starts highest off at January and lowest at December so something has to be higher or lower than them
        $high=1;
        $low=12;
        for($x = 0; $x < sizeof($post_timestamps); $x++)
        {
            //gets timestamps ready for sorting
            $post_timestamps[$x][0]=str_replace('January', 1, $post_timestamps[$x][0]);
            $post_timestamps[$x][0]=str_replace('February', 2, $post_timestamps[$x][0]);
            $post_timestamps[$x][0]=str_replace('March', 3, $post_timestamps[$x][0]);
            $post_timestamps[$x][0]=str_replace('April', 4, $post_timestamps[$x][0]);
            $post_timestamps[$x][0]=str_replace('May', 5, $post_timestamps[$x][0]);
            $post_timestamps[$x][0]=str_replace('June', 6, $post_timestamps[$x][0]);
            $post_timestamps[$x][0]=str_replace('July', 7, $post_timestamps[$x][0]);
            $post_timestamps[$x][0]=str_replace('August', 8, $post_timestamps[$x][0]);
            $post_timestamps[$x][0]=str_replace('September', 9, $post_timestamps[$x][0]);
            $post_timestamps[$x][0]=str_replace('October', 10, $post_timestamps[$x][0]);
            $post_timestamps[$x][0]=str_replace('November', 11, $post_timestamps[$x][0]);
            $post_timestamps[$x][0]=str_replace('December', 12, $post_timestamps[$x][0]);

            if($post_timestamps[$x][0]>$high)
                $high=$post_timestamps[$x][0];
            if($post_timestamps[$x][0]<$low)
                $low=$post_timestamps[$x][0];
        }
        //adds a default month
        $months[0]='January';

        //creating a temporary low since the original changes
        $temp_low=$low;
        for($x = 0; $x <= $high-$temp_low; $x++)
        {
            //puts the months back to normal
            if($low==1) $months[$x]='January';
            else if($low==2) $months[$x]='Feburary';
            else if($low==3) $months[$x]='March';
            else if($low==4) $months[$x]='April';
            else if($low==5) $months[$x]='May';
            else if($low==6) $months[$x]='June';
            else if($low==7) $months[$x]='July';
            else if($low==8) $months[$x]='August';
            else if($low==9) $months[$x]='September';
            else if($low==10) $months[$x]='October';
            else if($low==11) $months[$x]='November';
            else if($low==12) $months[$x]='December';
            $low++;
        }

        //self explanatory
        return $months;
    }
}
//returns the friend if today is their birthday
function get_friend_birthdays()
{
    $friends=get_friends($_SESSION['id']);
    $total=array();
    $num=0;
    for($x = 0; $x < sizeof($friends); $x++)
    {
        //selects the user from database
        $query=mysql_query("SELECT user_id FROM user_data WHERE user_id=$friends[$x]");
        if($query&&mysql_num_rows($query)==1)
        {
            //selects user's birthday from database
            $query2=mysql_query("SELECT user_birthday FROM user_data WHERE user_id=$friends[$x]");
            if($query2&&mysql_num_rows($query2)==1)
            {
                //gets the user and their birthday from query
                $array=mysql_fetch_row($query);
                $array2=mysql_fetch_row($query2);
                $users=$array[0];
                $birthdays=explode('|^|*|', $array2[0]);

                //gets the date to compare birthdays
                $date=explode(' ', str_replace(",", "", get_date()));

                //if it is the user's birthday, add themd to the list
                if($date[0]==$birthdays[0]&&$date[1]==$birthdays[1])
                {
                    $total[$num][0]=$users;
                    $age=$date[2]-$birthdays[2];
                    $total[$num][1]=$age;
                    $num++;
                }
            }
        }
    }
    return $total;

}

function has_redlay_gold($ID)
{
    if(is_id($ID)&&user_id_exists($ID))
    {
        $query=mysql_query("SELECT redlay_gold FROM user_data WHERE user_id=$ID LIMIT 1");
        if($query&&mysql_num_rows($query)==1)
        {
            $array=mysql_fetch_row($query);
            $gold=explode('|^|*|', $array[0]);

            //if there's something there
            if($array[0]!="")
            {
                //if gold hasn't expired
                $current_date=get_date();
                if($gold[1]<=$current_date||$gold[1]=="forever")
                    return true;
                else
                {
                    $query=mysql_query("UPDATE user_data SET redlay_gold='' WHERE user_id=$ID");
                    return false;
                }
            }
            else
                return false;
        }
        else
            return false;
    }
    else
        return false;
}

function has_redlay_page_gold($ID, $type)
{
    if(is_id($ID)&&page_id_exists($ID))
    {
        $query=mysql_query("SELECT redlay_gold FROM page_data WHERE page_id=$ID LIMIT 1");
        if($query&&mysql_num_rows($query)==1)
        {
            $array=mysql_fetch_row($query);
            $gold=explode('|^|*|', $array[0]);

           $features=explode('|%|&|', $gold[0]);


                //returns whether user has specific gold
                if($type=='any')
                {
                     if($gold[0]!='')
                        return true;
                     else
                        return false;
                }

                //if user has all redlay gold
                else if($type=='all')
                {
                   $has_all=true;
                    if(!in_array('site_customization', $features))
                         $has_all=false;
                    if(!in_array('photo_quality', $features))
                         $has_all=false;
                    if(!in_array('account_stats', $features))
                         $has_all=false;
                    if(!in_array('new_feature_test', $features))
                         $has_all=false;

                    return $has_all;
                }

                //checks if user has purchased any redlay gold
                else
                {
                    if(in_array($type, $features))
                       return true;
                    else
                       return false;
                }
        }
    }
    else
        return false;
}


function is_blocked_by_page($page_id)
{
    $query=mysql_query("SELECT blocked_users, blocked_types FROM user_blocks WHERE page_id=$page_id LIMIT 1");
    if($query&&mysql_num_rows($query)==1)
    {
        $array=mysql_fetch_row($query);
        $blocked_users=explode('|^|*|', $array[0]);
        $blocked_types=explode('|^|*|', $array[1]);


        if(isset($_SESSION['id']))
        {
            for($x = 0; $x < sizeof($blocked_users); $x++)
            {
                if($blocked_users[$x]==$_SESSION['id']&&$blocked_types[$x]=='user')
                    return true;
            }
            return false;
        }
        else if(isset($_SESSION['page_id']))
        {
            for($x = 0; $x < sizeof($blocked_users); $x++)
            {
                if($blocked_users[$x]==$_SESSION['page_id']&&$blocked_types[$x]=='page')
                    return true;
            }
            return false;
        }

        return false;
    }
}
function is_blocked_by_user($ID)
{
    $query=mysql_query("SELECT blocked_users, blocked_types FROM user_blocks WHERE user_id=$ID LIMIT 1");
    if($query&&mysql_num_rows($query)==1)
    {
        $array=mysql_fetch_row($query);
        $blocked_users=explode('|^|*|', $array[0]);
        $blocked_types=explode('|^|*|', $array[1]);


        if(isset($_SESSION['id']))
        {
            for($x = 0; $x < sizeof($blocked_users); $x++)
            {
                if($blocked_users[$x]==$_SESSION['id']&&$blocked_types[$x]=='user')
                    return true;
            }
            return false;
        }
        
        return false;
    }
}

function picture_is_viewable($ID, $picture_id, $type)
{
    //if it's a user's photo
    if($type=='user')
    {
        //if the picture exists
        if(picture_exists($ID, $picture_id))
        {
            //if ID is a valid user id
            if(is_id($ID)&&user_id_exists($ID))
            {
                //if $_SESSION[id] isn't blocked by ID
                if(!is_blocked_by_user($ID))
                {
                    //if current user is signed in and users aren't friends
                    $privacy=get_user_privacy_settings($ID);
                    if(isset($_SESSION['id'])&&user_is_friends($ID, $_SESSION['id'])=='true')
                    {
                        //if current user isn't viewing their own photo
                            if($ID!=$_SESSION['id'])
                            {
                                //check if $_SESSION[id] is in allowed group
                                    $query=mysql_query("SELECT pictures, image_audiences FROM pictures WHERE user_id=$ID LIMIT 1");
                                    if($query&&mysql_num_rows($query)==1)
                                    {
                                        $array=mysql_fetch_row($query);
                                        $images=explode('|^|*|', $array[0]);
                                        $image_audiences=explode('|^|*|', $array[1]);

                                        $index=-1;
                                        for($x = 0; $x < sizeof($images); $x++)
                                        {
                                            if($images[$x]==$picture_id)
                                                $index=$x;
                                        }

                                        $image_audiences[$index]=explode('|%|&|', $image_audiences[$index]);
                                        if(!in_array('Everyone', $image_audiences[$index]))
                                        {
                                            $groups=get_audience_current_user($ID);
                                            for($x = 0; $x < sizeof($groups); $x++)
                                            {
                                                if(in_array($groups[$x], $image_audiences[$index]))
                                                    return true;
                                            }
                                            return false;
                                        }
                                        else
                                            return true;
                                    }
                                    else
                                        return false;
                            }
                            else
                                return true;
                    }
                    //if ID allows public viewing of image
                    else if($privacy[1][4]=='yes')
                        return true;
                    else
                        return false;
                }
                else
                    return false;
            }
            else
                return false;
        }
        else
            return false;
    }
    else
        return false;
}
function get_next_photo($ID, $picture_id)
{
    $query=mysql_query("SELECT pictures, image_audiences FROM pictures WHERE user_id=$ID LIMIT 1");
    if($query&&mysql_num_rows($query)==1)
    {
        $array=mysql_fetch_row($query);
        $pictures=explode('|^|*|', $array[0]);
        $image_audiences=explode('|^|*|', $array[1]);
        
        $index=-1;
        for($x = 0; $x < sizeof($pictures); $x++)
        {
            if($pictures[$x]==$picture_id)
                $index=$x;
        }

        if($pictures[sizeof($pictures)-1]==$picture_id)
            return '0';
        else
        {
            $image_audiences[$index+1]=explode('|%|&|', $image_audiences[$index+1]);
            if($ID!=$_SESSION['id'])
            {
                $groups=get_audience_current_user($ID);

                $bool=false;
                if($image_audiences[$index+1][0]!='Everyone')
                {
                    for($x = 0; $x < sizeof($image_audiences[$index+1]); $x++)
                    {
                        if(in_array($image_audiences[$index+1][$x], $groups))
                            return $pictures[$index+1];
                    }

                    if($bool)
                        return $pictures[$index+1];
                    else
                        return false;
                }
                else
                    return $pictures[$index+1];
            }
            else
                return $pictures[$index+1];
        }
    }
}
function get_previous_photo($ID, $picture_id)
{
    $query=mysql_query("SELECT pictures, image_audiences FROM pictures WHERE user_id=$ID LIMIT 1");
    if($query&&mysql_num_rows($query)==1)
    {
        $array=mysql_fetch_row($query);
        $pictures=explode('|^|*|', $array[0]);
        $image_audiences=explode('|^|*|', $array[1]);

        $index=-1;
        for($x = 0; $x < sizeof($pictures); $x++)
        {
            if($pictures[$x]==$picture_id)
                $index=$x;
        }

        if($picture_id=='0')
            return $pictures[sizeof($pictures)-1];
        else
        {
            $image_audiences[$index-1]=explode('|%|&|', $image_audiences[$index-1]);
            if($ID!=$_SESSION['id'])
            {
                $groups=get_audience_current_user($ID);

                if($image_audiences[$index-1][0]!='Everyone')
                {
                    for($x = 0; $x < sizeof($image_audiences[$index-1]); $x++)
                    {
                        if(in_array($image_audiences[$index-1][$x], $groups))
                            return $pictures[$index-1];
                    }

                        return false;
                }
                else
                    return $pictures[$index-1];
            }
            else
            {
                return $pictures[$index-1];
            }
        }
    }
}


























/////////////////////////////////PAGE FUNCTIONS/////////////////////////////////////////
function get_page_name($ID)
{
    $query=mysql_query("SELECT name FROM page_data WHERE page_id=$ID LIMIT 1");
    if($query)
    {
        $array=mysql_fetch_row($query);
        return $array[0];
    }
}
function completed_page_registration_intro($ID)
{
    $query=mysql_query("SELECT registration_intro FROM page_display WHERE page_id=$ID LIMIT 1");
    if($query&&mysql_num_rows($query)==1)
    {
        $array=mysql_fetch_row($query);
        if($array[0]=='no')
            return false;
        else
            return true;
    }
    return false;
}
//function is_page_terminated($ID)
//{
//    $query=mysql_query("SELECT closed FROM pages WHERE page_id=$ID LIMIT 1");
//    if($query&&mysql_num_rows($query)==1)
//    {
//        $array=mysql_fetch_row($query);
//        $closed=$array[0];
//        if($array[0]!=2)
//            return false;
//        else
//            return true;
//    }
//    return true;
//}
function page_email_exists($email)
{
    $query=mysql_query("SELECT id FROM pages WHERE email='$email' LIMIT 1");
    if($query)
    {
        if(mysql_num_rows($query)>=1)
            return true;
        else
            return false;
    }
    return false;
}
function page_id_exists($ID)
{
    $query=mysql_query("SELECT id FROM pages WHERE id=$ID LIMIT 1");
    if($query&&mysql_num_rows($query)==1)
        return true;
    else
        return false;
}
function page_id_terminated($ID)
{
    if(is_id($ID)&&page_id_exists($ID))
    {
        $query=mysql_query("SELECT closed FROM closed_pages WHERE page_id=$ID LIMIT 1");
        if($query&&mysql_num_rows($query)==1)
            return true;
        else 
            return false;
    }
    else
        return false;
}
function add_page_hack_count($ID)
{
    $query=mysql_query("SELECT hack_count FROM pages WHERE id=$ID LIMIT 1");
    if($query&&mysql_num_rows($query)==1)
    {
        $array=mysql_fetch_row($query);
        $hack_count=$array[0];
        $hack_count++;
        $query=mysql_query("UPDATE pages SET hack_count=$hack_count WHERE id=$ID");
        if($query)
        {
            if($hack_count==3)
                terminate_page_account();
        }
    }
}
function page_blocked($ID, $ID2)
{
    $query=mysql_query("SELECT blocked_users FROM page_data WHERE page_id=$ID LIMIT 1");
    if($query&&mysql_num_rows($query)==1)
    {
        $array=mysql_fetch_row($query);
        $blocked_users=explode('|^|*|', $array[0]);
        for($x = 0; $x < sizeof($blocked_users); $x++)
        {
            if($blocked_users[$x]==$ID2)
                return true;
        }
        return false;
    }
}
function get_page_type($ID)
{
    $query=mysql_query("SELECT type FROM pages WHERE id=$ID LIMIT 1");
    if($query)
    {
        $array=mysql_fetch_row($query);
        return $array[0];
    }
}
function get_main_video($ID)
{
    $query=mysql_query("SELECT * FROM page_display WHERE page_id=$ID LIMIT 1");
    if($query&&mysql_num_rows($query)==1)
    {
        $array=mysql_fetch_array($query);
        $video=$array['main_video'];
        return $video;
    }
}
function get_products($ID)
{
    $query=mysql_query("SELECT * FROM page_data WHERE page_id=$ID LIMIT 1");
    if($query&&mysql_num_rows($query)==1)
    {
        $array=mysql_fetch_array($query);
        $names=explode('|^|*|', $array['product_name']);
        $image_numbers=explode('|^|*|', $array['product_number']);
        $purchase_links=explode('|^|*|', $array['product_purchase_link']);
        $prices=explode('|^|*|', $array['product_price']);
        $links=explode('|^|*|', $array['product_link']);
        $category=explode('|^|*|', $array['product_category']);
        $list[0]=$names;
        $list[1]=$category;
        $list[2]=$prices;
        $list[3]=$purchase_links;
        $list[4]=$image_numbers;
        $list[5]=$links;
        return $list;
    }
}
function get_product_categories($ID)
{
    $query=mysql_query("SELECT * FROM page_data WHERE page_id=$ID LIMIT 1");
    if($query&&mysql_num_rows($query)==1)
    {
        $array=mysql_fetch_array($query);
        $categories=explode('|^|*|', $array['product_categories']);
        if($array['product_categories']=='')
            return array();
        else
            return $categories;
    }
}
function get_page_messages_alerts()
{
    $query=mysql_query("SELECT new_messages FROM page_messages WHERE page_id=$_SESSION[page_id] LIMIT 1");
    if($query&&mysql_num_rows($query)==1)
    {
        $array=mysql_fetch_row($query);
        return $array[0];
    }
    return 0;
}
function has_page_message_alerts()
{
    $query=mysql_query("SELECT new_messages FROM page_messages WHERE page_id=$_SESSION[page_id] LIMIT 1");
    if($query&&mysql_num_rows($query)==1)
    {
       $array=mysql_fetch_row($query);
       if($array[0]==0)
           return false;
       else
           return true;
    }
    return false;
}
function get_messages($ID)
{
    $query=mysql_query("SELECT * FROM page_messages WHERE page_id=$ID LIMIT 1");
    if($query&&mysql_num_rows($query)==1)
    {
        $array=mysql_fetch_array($query);
        $messages=explode('|^|*|', str_replace("'", "\'", $array['messages']));
        $message_timestamps=explode('|^|*|', $array['timestamps']);
        $pages_sent=explode('|^|*|', $array['page_sent']);
        $page_ids=explode('|^|*|', $array['page_id_2']);
        for($x = 0; $x < sizeof($messages); $x++)
        {
            $messages[$x]=explode('|%|&|', $messages[$x]);
            $message_timestamps[$x]=explode('|%|&|', $message_timestamps[$x]);
            $pages_sent[$x]=explode('|%|&|', $pages_sent[$x]);
        }
        $array[0]=$messages;
        $array[1]=$message_timestamps;
        $array[2]=$pages_sent;
        $array[3]=$page_ids;
        return $array;
    }
}
function page_picture_exists($ID, $picture_id)
{
    $query=mysql_query("SELECT pictures FROM page_pictures WHERE page_id=$ID LIMIT 1");
    if($query)
    {
        $array=mysql_fetch_row($query);
        $names=explode('|^|*|', $array[0]);
        $bool=false;
        for($x = 0; $x < sizeof($names); $x++)
        {
            if($names[$x]==$picture_id)
                $bool=true;
        }
        return $bool;
    }
}
function get_message_names($ID)
{
    $query=mysql_query("SELECT * FROM page_messages WHERE page_id=$ID LIMIT 1");
    if($query&&mysql_num_rows($query)==1)
    {
        $array=mysql_fetch_array($query);
        $names=explode('|^|*|', $array['page_id_2']);
        if($array['page_id_2']=='')
            return array();
        else
            return $names;
    }
}
function get_page_picture_index($ID, $picture_id)
{
    $query=mysql_query("SELECT pictures FROM page_pictures WHERE page_id=$ID LIMIT 1");
    if($query)
    {
        $array=mysql_fetch_row($query);
        $names=explode('|^|*|', $array[0]);
        $num=0;
        while($names[$num]!=$picture_id)
            $num++;
        if($names[$num]==$picture_id)
            return $num;
        else
            return -1;
    }
}
function get_page_picture_description($ID, $index)
{
    $query=mysql_query("SELECT picture_descriptions FROM page_pictures WHERE page_id=$ID LIMIT 1");
    if($query)
    {
        $array=mysql_fetch_row($query);
        $descriptions=explode('|^|*|', $array[0]);
        return str_replace("'", "\'", $descriptions[$index]);
    }
}
function get_page_website($ID)
{
    $query=mysql_query("SELECT * FROM page_data WHERE page_id=$ID LIMIT 1");
    if($query&&mysql_num_rows($query)==1)
    {
        $array=mysql_fetch_array($query);
        $website=$array['website'];
        if($array['website']=='')
            return '';
        else
            return $website;
    }
}
function get_website_dimentions($ID)
{
    $query=mysql_query("SELECT * FROM page_data WHERE page_id=$ID LIMIT 1");
    if($query&&mysql_num_rows($query)==1)
    {
        $array=mysql_fetch_array($query);
        $dimentions=explode('|^|*|', $array['website_dimentions']);
        if($array['website_dimentions']=='')
            return array();
        else
            return $dimentions;
    }
}
function get_fan_stuff($ID)
{
    $query=mysql_query("SELECT * FROM page_data WHERE page_id=$ID LIMIT 1");
    if($query&&mysql_num_rows($query)==1)
    {
        $array=mysql_fetch_array($query);
        $array2[0]=explode('|^|*|', $array['fan_videos']);
        $array2[1]=explode('|^|*|', $array['fan_video_sent']);
        $array2[2]=explode('|^|*|', $array['fan_video_timestamps']);
        $array2[3]=explode('|^|*|', $array['fan_picture_timestamps']);
        $array2[4]=explode('|^|*|', $array['fan_picture_sent']);
        $temp=$array['fan_number_pictures'];

        if($temp<=100)
            $array2[5]=$temp;
        else
            $array2[5]=100;
        $array2[6]=$array['fan_number_pictures'];
        return $array2;
    }
}
function get_all_information($ID)
{
    $query=mysql_query("SELECT * FROM page_data WHERE page_id=$ID LIMIT 1");
    if($query&&mysql_num_rows($query)==1)
    {
        $array=mysql_fetch_array($query);
        $list[0]=$array['name'];
        if($array['videos']!='')
            $list[1]=sizeof(explode('|^|*|', $array['videos']));
        else
            $list[1]=0;
        $list[2]=$array['likes'];
        $list[3]=explode('|^|*|', $array['created']);
        $list[3][1]=$list[3][1].",";
        $list[3]=implode(' ', $list[3]);
        $list[4]=$array['website'];
        $list[5]=$array['product_number'];
        if($array['fan_videos']!='')
            $list[6]=sizeof(explode('|^|*|', $array['fan_videos']));
        else
            $list[6]=0;
        $list[7]=$array['fan_number_pictures'];
        $query=mysql_query("SELECT * FROM page_updates WHERE page_id=$ID LIMIT 1");
        if($query&&mysql_num_rows($query)==1)
        {
            $array=mysql_fetch_array($query);
            if($array['posts']!='')
                $list[8]=sizeof(explode('|^|*|', $array['posts']));
            else
                $list[8]=0;
            
                $list[9]=0;
            if($array['likes']!='')
            {
                $likes=explode('|^|*|', $array['likes']);
                $count=0;
                for($x = 0; $x < sizeof($likes); $x++)
                {
                    if($likes[$x]!='0')
                    {
                        $likes[$x]=explode('|%|&|', $likes[$x]);
                        $count=$count+sizeof($likes[$x]);
                    }
                }
                $list[10]=$count;
            }
            else
            {
                $list[10]=0;
            }
            if($array['dislikes']!='')
            {
                $dislikes=explode('|^|*|', $array['dislikes']);
                $count=0;
                for($x = 0; $x < sizeof($dislikes); $x++)
                {
                    if($dislikes[$x]!='0')
                    {
                        $dislikes[$x]=explode('|%|&|', $dislikes[$x]);
                        $count=$count+sizeof($dislikes[$x]);
                    }
                }
                $list[11]=$count;
            }
            else
            {
                $list[11]=0;
            }
            $query=mysql_query("SELECT * FROM pages WHERE id=$ID LIMIT 1");
            if($query&&mysql_num_rows($query)==1)
            {
                $array=mysql_fetch_array($query);
                $list[12]=$array['type'];
                $list[13]=$array['timestamp'];
                return $list;
            }
        }
    }
}
function is_unlocked_page($ID, $thing)
{
    $query=mysql_query("SELECT unlocked FROM page_data WHERE page_id=$ID LIMIT 1");
    if($query)
    {
        $array=mysql_fetch_row($query);
        $unlocked=explode('|^|*|', $array[0]);
        if(($thing=='colors'&&$unlocked[0]=='1')||($thing=='animated_profile_picture'&&$unlocked[1]=='1')
                ||($thing=='background_picture'&&$unlocked[2]=='1')||($thing=='profile_music'&&$unlocked[3]=='1'))
            return true;
        else
            return false;
    }
}

//array is the array of post ids from the user's update row, and index is the index to search for
function get_page_post_index($ID, $post_index)
{
    $query=mysql_query("SELECT post_ids FROM page_updates WHERE page_id=$ID LIMIT 1");
    if($query&&mysql_num_rows($query)==1)
    {
        $array=mysql_fetch_row($query);
        $post_ids=explode('|^|*|', $array[0]);

        $index=-1;
        for($x = 0; $x < sizeof($post_ids); $x++)
        {
            if($post_ids[$x]==$post_index)
                $index=$x;
        }
        return $index;
    }
}

function is_valid_audience($audience)
{
    $query=mysql_query("SELECT group_defaults FROM data WHERE num=1");
    $query2=mysql_query("SELECT audience_group_lists FROM user_data WHERE user_id=$_SESSION[id] LIMIT 1");
    if($query&&$query2)
    {
        $array=mysql_fetch_row($query);
        $array2=mysql_fetch_row($query2);
        
        $default_audiences=explode('|^|*|', $array[0]);
        $added_audiences=explode('|^|*|', $array2[0]);

        //checks if default such as Everyone, Friends, Family, Close Friends, etc.
        for($x = 0; $x < sizeof($default_audiences); $x++)
        {
            if($default_audiences[$x]==$audience||$audience=='Everyone')
                return true;
        }
        
        //checks if it's a category the user has added; colleges, book club, etc.
        for($x =0; $x < sizeof($added_audiences); $x++)
        {
            if($added_audiences[$x]==$audience)
                return true;
        }
        return false;

    }
}

//gets the current user's audience settings from user ID
function get_audience_current_user($ID)
{
   $query=mysql_query("SELECT user_friends, audience_groups FROM user_data WHERE user_id=$ID LIMIT 1");
   if($query&&mysql_num_rows($query)==1)
   {
      $array=mysql_fetch_row($query);
      
      $friends=explode('|^|*|', $array[0]);
      $audiences=explode('|^|*|', $array[1]);
      
      for($x = 0; $x < sizeof($friends); $x++)
      {
          $audiences[$x]=explode('|%|&|', $audiences[$x]);
         if($friends[$x]==$_SESSION['id'])
            return $audiences[$x];
      }
      return '';
   }
}

function can_view($user_audience, $post_audience)
{
    if(in_array('Everyone', $post_audience)||$post_audience=="Everyone")
         return true;
    else
    {
        for($x = 0; $x < sizeof($user_audience); $x++)
       {
            if(in_array($user_audience[$x], $post_audience)||$user_audience[$x]==$post_audience)
                 return true;
       }
       return false;
    }
}
function get_created_title($type, $other_type)
{
    if($type=='Company') 
        echo "Founded: "; 
    else if($type=='Person') 
        echo "Born: "; 
    else
    {
        if($other_type=='Place') 
            echo "Date formed: "; 
        else if($other_type=='Product') 
            echo "Date Made: "; 
        else if($other_type=='Movie') 
            echo "Released: "; 
        else if($other_type=='TV Show') 
            echo "Debut: "; 
        else if($other_type=='Book') 
            echo "Published"; 
        else if($other_type=='Website') 
            echo "Launched: "; 
        else if($other_type=='Charity') 
            echo "Started:"; 
        else if($other_type=='Quote/Saying') 
            echo "Created: ";
    }
}

















///////////////////////UNIVERSAL//////////////////////////
function get_date()
{
    $date=time()+25200;
    return $date;
}
function get_regular_date($timestamp)
{
    return date('F j, Y g:i:s A', $timestamp);
}
function get_adjusted_date($timestamp, $timezone)
{
    return date('F j, Y g:i A', ($timestamp-($timezone*60)));
}
function get_current_time($timezone)
{
    $date=get_date();
    $new_timezone=($timezone*60);
    $total=$date-$new_timezone;
    
    $final=(time()+25200)-($timezone*60);
    return (int)($final);
}
function get_time_since_seconds($timestamp, $timezone)
{
    $time=$timestamp-($timezone*60);
    $new_time=(int)(get_current_time($timezone)-$time);
    if(((int)$new_time)<0)
        return 0;
    else
        return $new_time;
}
function get_time_since($timestamp, $timezone)
{
    $time=(int)($timestamp-($timezone*60));
    $new_time=get_current_time($timezone)-$time;
    
    if($new_time>=3600)
    {
        
        if($new_time>2678400)
        {
            $ago=get_adjusted_date($time, $timezone);
            
            $time=explode(' ', $ago);
            $ago=$time[0]." ".$time[1]." ".$time[2];
        }
        else if($new_time>604800&&$new_time<2678400)
            $ago=number_format((int)($new_time/86400),0)." days ago";
        else if($new_time>=86400&&$new_time<604800)
        {
            $num_days=number_format((int)($new_time/86400),0);
            $num_hours=number_format(((int)($new_time%86400)/3600));
            
            if($num_days!=1)
                $days=$num_days." days";
            else
                $days="1 day";
                
            if($num_hours!=0)
            {
                if($num_hours!=1)
                    $hours=$num_hours." hours ago";
                else
                    $hours="1 hour ago";
            }
            else
            {
                $minutes=number_format(((int)($new_time%3600)/60));
                $hours=$minutes." minutes ago";
            }
            
            $ago=$days." ".$hours;
        }
        else if($new_time>=7200)
            $ago=number_format((int)($new_time/3600),0)." hours ".number_format(((int)($new_time%3600)/60))." minutes ago";
        else if($new_time>=3600&&$new_time<7200)
            $ago="1 hour and ".number_format(((int)($new_time%3600)/60))." minutes ago";
        else
            $ago="1 hour ago";
    }
    else
    {
        if($new_time>=120)
            $ago=number_format((int)($new_time/60),0)." minutes ago";
        else if($new_time>=60&&$new_time<120)
            $ago="1 minute ago";
        else if($new_time<60)
            $ago=$new_time." seconds ago";
    }
    return $ago;
}
function get_doc_icon($num, $file_extention)
{
    //if num==1, return thumbnail
    //if num==2, return regular image
    if($num==1)
    {
        //if word
        if($file_extention=="doc"||$file_extention=="docx"||$file_extention=="docm"||$file_extention=="dotx"||$file_extention=="dotm")
            return "http://pics.redlay.com/pictures/doc icon thumbnails/word.png";

        //if excel
        else if($file_extention=="xls"||$file_extention=="xlsx"||$file_extention=="xlsm"||$file_extention=="xltx"||$file_extention=="xltm"||$file_extention=="xlsb"||$file_extention=="xlam"||$file_extention=="xll")
            return "http://pics.redlay.com/pictures/doc icon thumbnails/excel.png";

        //if powerpoint
        else if($file_extention=="ppt"||$file_extention=="pptx"||$file_extention=="pptm"||$file_extention=="potx"||$file_extention=="potm"||$file_extention=="ppam"||$file_extention=="ppsx"||$file_extention=="ppsm")
            return "http://pics.redlay.com/pictures/doc icon thumbnails/powerpoint.png";

        //if audio
        else if($file_extention=="aif"||$file_extention=="m4a"||$file_extention=="mp3"||$file_extention=="mpa"||$file_extention=="wav"||$file_extention=="wma")
            return "http://pics.redlay.com/pictures/doc icon thumbnails/music.png";

        //if video
        else if($file_extention=="asf"||$file_extention=="avi"||$file_extention=="flv"||$file_extention=="mov"||$file_extention=="mp4"||$file_extention=="mpg"||$file_extention=="swf"||$file_extention=="vob"||$file_extention=="wmv")
            return "http://pics.redlay.com/pictures/doc icon thumbnails/video.png";

        //if executable
        else if($file_extention=="app"||$file_extention=="bat"||$file_extention=="cgi"||$file_extention=="com"||$file_extention=="exe"||$file_extention=="gadget"||$file_extention=="pif"||$file_extention=="wsf")
            return "http://pics.redlay.com/pictures/doc icon thumbnails/exe.png";

        //if comand
        else if($file_extention=="cmd")
            return "http://pics.redlay.com/pictures/doc icon thumbnails/cmd.png";

        //if html
        else if($file_extention=="html"||$file_extention=="htm"||$file_extention=="xhtml"||$$file_extention=="asp"||$file_extention=="aspx")
            return "http://pics.redlay.com/pictures/doc icon thumbnails/html.png";

        //if php
        else if($file_extention=="php")
            return "http://pics.redlay.com/pictures/doc icon thumbnails/php.png";

        //if css
        else if($file_extention=="css")
            return "http://pics.redlay.com/pictures/doc icon thumbnails/css.png";

        //if javascript
        else if($file_extention=="js"||$file_extention=="jsp")
            return "http://pics.redlay.com/pictures/doc icon thumbnails/javascript.png";

        //if java
        else if($file_extention=="jar"||$file_extention=="java"||$file_extention=="class")
            return "http://pics.redlay.com/pictures/doc icon thumbnails/java.png";

        //if c++
        else if($file_extention=="cpp"||$file_extention=="vcxproj"||$file_extention=="c"||$file_extention=="h")
            return "http://pics.redlay.com/pictures/doc icon thumbnails/c++.png";

        //if flash
        else if($file_extention=="fla")
            return "http://pics.redlay.com/pictures/doc icon thumbnails/flash.png";

        //if python
        else if($file_extention=="py")
            return "http://pics.redlay.com/pictures/doc icon thumbnails/python.png";

        //if sql
        else if($file_extention=="sql")
            return "http://pics.redlay.com/pictures/doc icon thumbnails/sql.png";

        //if ruby
        else if($file_extention=="ruby")
            return "http://pics.redlay.com/pictures/doc icon thumbnails/ruby.png";

        //if torrent
        else if($file_extention=="torrent")
            return "http://pics.redlay.com/pictures/doc icon thumbnails/torrent.png";

        //if image
        else if($file_extention=="png"||$file_extention=="jpg"||$file_extention=="jpeg"||$file_extention=="gif"||$file_extention=="bmp"||$file_extention=="webp")
            return "http://pics.redlay.com/pictures/doc icon thumbnails/image.png";

        //if photoshop
        else if($file_extention=="psd")
            return "http://pics.redlay.com/pictures/doc icon thumbnails/photoshop.png";

        //if xml
        else if($file_extention=="xml")
            return "http://pics.redlay.com/pictures/doc icon thumbnails/xml.png";

        //if pdf
        else if($file_extention=="pdf")
            return "http://pics.redlay.com/pictures/doc icon thumbnails/pdf.png";

        //if text
        else if($file_extention=="text"||$file_extention=="txt")
            return "http://pics.redlay.com/pictures/doc icon thumbnails/text.png";

        //if none of the above
        else
            return "http://pics.redlay.com/pictures/doc icon thumbnails/other.png";
    }
    else if($num==2)
    {
        //if word
        if($file_extention=="doc"||$file_extention=="docx"||$file_extention=="docm"||$file_extention=="dotx"||$file_extention=="dotm")
            return "http://pics.redlay.com/pictures/doc icons/word.png";

        //if excel
        else if($file_extention=="xls"||$file_extention=="xlsx"||$file_extention=="xlsm"||$file_extention=="xltx"||$file_extention=="xltm"||$file_extention=="xlsb"||$file_extention=="xlam"||$file_extention=="xll")
            return "http://pics.redlay.com/pictures/doc icons/excel.png";

        //if powerpoint
        else if($file_extention=="ppt"||$file_extention=="pptx"||$file_extention=="pptm"||$file_extention=="potx"||$file_extention=="potm"||$file_extention=="ppam"||$file_extention=="ppsx"||$file_extention=="ppsm")
            return "http://pics.redlay.com/pictures/doc icons/powerpoint.png";

        //if audio
        else if($file_extention=="aif"||$file_extention=="m4a"||$file_extention=="mp3"||$file_extention=="mpa"||$file_extention=="wav"||$file_extention=="wma")
            return "http://pics.redlay.com/pictures/doc icons/music.png";

        //if video
        else if($file_extention=="asf"||$file_extention=="avi"||$file_extention=="flv"||$file_extention=="mov"||$file_extention=="mp4"||$file_extention=="mpg"||$file_extention=="swf"||$file_extention=="vob"||$file_extention=="wmv")
            return "http://pics.redlay.com/pictures/doc icons/video.png";

        //if executable
        else if($file_extention=="app"||$file_extention=="bat"||$file_extention=="cgi"||$file_extention=="com"||$file_extention=="exe"||$file_extention=="gadget"||$file_extention=="pif"||$file_extention=="wsf")
            return "http://pics.redlay.com/pictures/doc icons/exe.png";

        //if comand
        else if($file_extention=="cmd")
            return "http://pics.redlay.com/pictures/doc icons/cmd.png";

        //if html
        else if($file_extention=="html"||$file_extention=="htm"||$file_extention=="xhtml"||$$file_extention=="asp"||$file_extention=="aspx")
            return "http://pics.redlay.com/pictures/doc icons/html.png";

        //if php
        else if($file_extention=="php")
            return "http://pics.redlay.com/pictures/doc icons/php.png";

        //if css
        else if($file_extention=="css")
            return "http://pics.redlay.com/pictures/doc icons/css.png";

        //if javascript
        else if($file_extention=="js"||$file_extention=="jsp")
            return "http://pics.redlay.com/pictures/doc icons/javascript.png";

        //if java
        else if($file_extention=="jar"||$file_extention=="java"||$file_extention=="class")
            return "http://pics.redlay.com/pictures/doc icons/java.png";

        //if c++
        else if($file_extention=="cpp"||$file_extention=="vcxproj"||$file_extention=="c"||$file_extention=="h")
            return "http://pics.redlay.com/pictures/doc icons/c++.png";

        //if flash
        else if($file_extention=="fla")
            return "http://pics.redlay.com/pictures/doc icons/flash.png";

        //if python
        else if($file_extention=="py")
            return "http://pics.redlay.com/pictures/doc icons/python.png";

        //if sql
        else if($file_extention=="sql")
            return "http://pics.redlay.com/pictures/doc icons/sql.png";

        //if ruby
        else if($file_extention=="ruby")
            return "http://pics.redlay.com/pictures/doc icons/ruby.png";

        //if torrent
        else if($file_extention=="torrent")
            return "http://pics.redlay.com/pictures/doc icons/torrent.png";

        //if image
        else if($file_extention=="png"||$file_extention=="jpg"||$file_extention=="jpeg"||$file_extention=="gif"||$file_extention=="bmp"||$file_extention=="webp")
            return "http://pics.redlay.com/pictures/doc icons/image.png";

        //if photoshop
        else if($file_extention=="psd")
            return "http://pics.redlay.com/pictures/doc icons/photoshop.png";

        //if xml
        else if($file_extention=="xml")
            return "http://pics.redlay.com/pictures/doc icons/xml.png";

        //if pdf
        else if($file_extention=="pdf")
            return "http://pics.redlay.com/pictures/doc icons/pdf.png";

        //if text
        else if($file_extention=="text"||$file_extention=="txt")
            return "http://pics.redlay.com/pictures/doc icons/text.png";

        //if none of the above
        else
            return "http://pics.redlay.com/pictures/doc icons/other.png";
    }
    else if($num==3)
    {
        //if word
        if($file_extention=="doc"||$file_extention=="docx"||$file_extention=="docm"||$file_extention=="dotx"||$file_extention=="dotm")
            return "word";

        //if excel
        else if($file_extention=="xls"||$file_extention=="xlsx"||$file_extention=="xlsm"||$file_extention=="xltx"||$file_extention=="xltm"||$file_extention=="xlsb"||$file_extention=="xlam"||$file_extention=="xll")
            return "excel";

        //if powerpoint
        else if($file_extention=="ppt"||$file_extention=="pptx"||$file_extention=="pptm"||$file_extention=="potx"||$file_extention=="potm"||$file_extention=="ppam"||$file_extention=="ppsx"||$file_extention=="ppsm")
            return "powerpoint";

        //if audio
        else if($file_extention=="aif"||$file_extention=="m4a"||$file_extention=="mp3"||$file_extention=="mpa"||$file_extention=="wav"||$file_extention=="wma")
            return "audio";

        //if video
        else if($file_extention=="asf"||$file_extention=="avi"||$file_extention=="flv"||$file_extention=="mov"||$file_extention=="mp4"||$file_extention=="mpg"||$file_extention=="swf"||$file_extention=="vob"||$file_extention=="wmv")
            return "video";

        //if executable
        else if($file_extention=="app"||$file_extention=="bat"||$file_extention=="cgi"||$file_extention=="com"||$file_extention=="exe"||$file_extention=="gadget"||$file_extention=="pif"||$file_extention=="wsf")
            return "executable";

        //if comand
        else if($file_extention=="cmd")
            return "command";

        //if html
        else if($file_extention=="html"||$file_extention=="htm"||$file_extention=="xhtml"||$$file_extention=="asp"||$file_extention=="aspx")
            return "html";

        //if php
        else if($file_extention=="php")
            return "php";

        //if css
        else if($file_extention=="css")
            return "css";

        //if javascript
        else if($file_extention=="js"||$file_extention=="jsp")
            return "javascript";

        //if java
        else if($file_extention=="jar"||$file_extention=="java"||$file_extention=="class")
            return "java";

        //if c++
        else if($file_extention=="cpp"||$file_extention=="vcxproj"||$file_extention=="c"||$file_extention=="h")
            return "c++";

        //if flash
        else if($file_extention=="fla")
            return "flash";

        //if python
        else if($file_extention=="py")
            return "python";

        //if sql
        else if($file_extention=="sql")
            return "sql";

        //if ruby
        else if($file_extention=="ruby")
            return "ruby";

        //if torrent
        else if($file_extention=="torrent")
            return "torrent";

        //if image
        else if($file_extention=="png"||$file_extention=="jpg"||$file_extention=="jpeg"||$file_extention=="gif"||$file_extention=="bmp"||$file_extention=="webp")
            return "image";

        //if photoshop
        else if($file_extention=="psd")
            return "photoshop";

        //if xml
        else if($file_extention=="xml")
            return "xml";

        //if pdf
        else if($file_extention=="pdf")
            return "pdf";

        //if text
        else if($file_extention=="text"||$file_extention=="txt")
            return "text";

        //if none of the above
        else
            return "other";
    }
}
function get_size($size)
{
    //gets total space taken
    if($size/1000000000>=1)
        return number_format($size/1000000000, 2)."GB";
    else if($size/1000000>=1)
        return number_format($size/1000000, 2)."MB";
    else if($size/1000>=1)
        return number_format($size/1000, 2)."KB";
    else
        return $size."B";
}
function get_users_from_group($group)
{
    $query=mysql_query("SELECT user_friends, audience_groups FROM user_data WHERE user_id=$_SESSION[id] LIMIT 1");
    if($query&&mysql_num_rows($query)==1)
    {
        $array=mysql_fetch_row($query);
        $friends=explode('|^|*|', $array[0]);
        $groups=explode('|^|*|', $array[1]);

        $array_friends=array();
        for($x = 0; $x < sizeof($groups); $x++)
        {
            $groups[$x]=explode('|%|&|', $groups[$x]);
            for($y = 0;$y < sizeof($groups[$x]); $y++)
            {
                if($group==$groups[$x][$y])
                    $array_friends[]=$friends[$x];
            }
        }
        return $array_friends;
    }
}
function clean_string($string)
{
    return str_replace('|@|$|', '', str_replace('|%|&|', '', str_replace('|^|*|', '', trim(mysql_real_escape_string(htmlentities(stripslashes($string), ENT_COMPAT, 'UTF-8'))))));
}
function is_internet_explorer()
{
    if (isset($_SERVER['HTTP_USER_AGENT']) && 
    (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false))
        return true;
    else
        return false;
}
function process_video($video)
{
    //initializes variables incase video isn't valid
    $type='';
    
    
    //if youtube
    if((strstr($video, 'youtube.com/watch?')==true||strstr($video, 'youtube.com/v/')==true))
    {
        //if regular video
        if(strstr($video, 'youtube.com/v/')==false)
        {
            if(strpos($video, 'v=')!=false)
            {
                //original: youtube.com/watch?annotation_id=annotation_370587&v=X_QNBwvBV4Y
                //after: X_QNBwvBV4Y
                $video=substr($video, (strpos($video, 'v=')+2), 11);

                $valid_video=true;
                $type='youtube';
            }
            else
                $valid_video=false;
        }
        
        //if embedded video
        else
        {
            $video=substr($video, (strpos($video, 'v/')+2), 11);
            
            $valid_video=true;
            $type='youtube';
        }
    }
    
    //if vimeo
    else if(strstr($video, 'vimeo.com/')==true)
    {
        //if regular video
        if(strstr($video, 'vimeo.com/video/')==false)
        {
            //original: http://vimeo.com/42480177
            //after: 42480177
            $video=substr($video, (strpos($video, '.com/')+5));
            
            //cleans out parameters
            $temp_video=explode('?', $video);
            $video=$temp_video[0];

            $valid_video=true;
            $type='vimeo';
        }
        
        //if embedded video
        else
        {
            //original: http://play.vimeo.com/video/42480177?badge=0
            //after: 42480177
            $video=substr($video, (strpos($video, '.com/video/')+11));
            
            //cleans out parameters
            $temp_video=explode('?', $video);
            $video=$temp_video[0];

            $valid_video=true;
            $type='vimeo';
        }
    }
    else
        $valid_video=false;
    
    $final=array();
    $final[0]=$valid_video;
    $final[1]=$type;
    $final[2]=$video;
    
    
    return $final;
    
}
function convert_video($video, $type)
{
    if($type=='youtube')
    {
        return "<iframe width='375' height='211' id='current_video' src='http://www.youtube.com/embed/".$video."?wmode=transparent' frameborder='0' allowfullscreen></iframe>";
    }
    else if($type=='vimeo')
    {
        return "<iframe id='current_video' src='http://player.vimeo.com/video/".$video."' width='375' height='211' frameborder='0' webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>";
    }
}
function get_video_preview($video, $type)
{
    if($type=='youtube')
    {
        return "http://i3.ytimg.com/vi/".$video."/mqdefault.jpg";
    }
    else if($type=='vimeo')
    {
        return "";
    }
}
function get_badges($ID)
{
    $badges=array();
    $badges['gold']=has_redlay_gold($ID, 'any');
    return $badges;
}
function get_profile_picture($ID)
{
    if(is_id($ID)&&user_id_exists($ID))
    {
        $query=mysql_query("SELECT image_types FROM pictures WHERE user_id=$ID LIMIT 1");
        if($query&&mysql_num_rows($query)==1)
        {
            $array=mysql_fetch_row($query);
            $image_types=explode('|^|*|', $array[0]);

            return get_pic_thumbnail_src($ID, '0', $image_types[0]);
        }
        else
            return "";
    }
    else
        return "";
}
function get_page_profile_picture($ID)
{
    if(is_id($ID)&&page_id_exists($ID))
    {
        $query=mysql_query("SELECT image_types FROM page_pictures WHERE page_id=$ID LIMIT 1");
        if($query&&mysql_num_rows($query)==1)
        {
            $array=mysql_fetch_row($query);
            $image_types=explode('|^|*|', $array[0]);

            return get_page_pic_thumbnail_src($ID, '0', $image_types[0]);
        }
        else
            return "";
    }
    else
        return "";
}

function get_file_names($ID)
{
    $ID=(int)($ID);
    
    $query=mysql_query("SELECT file_names FROM user_data WHERE user_id=$ID LIMIT 1");
    if($query&&mysql_num_rows($query)==1)
    {
        $array=mysql_fetch_row($query);
        $file_names=explode('|^|*|', $array[0]);
        for($x = 0; $x < sizeof($file_names); $x++)
        {
            $file_names[$x]=explode('|%|&|', $file_names[$x]);
        }
        return $file_names;
    }
}

//gets last time current user have viewed a certain photo
function get_last_time_photo_viewed($photo_id, $user_id)
{
    include("requiredS3.php");
    
    $file_names=get_file_names($_SESSION['id']);
    $photo_file_names=$file_names[1];
    
    //gets file stuff
    $path="users/$_SESSION[id]/files/photos/$photo_file_names[4].txt";
    $value=md5(uniqid(rand()));
    $tmp_path="/var/www/tmp_files/$value.txt";
    $s3->getObject('bucket_name', $path, $tmp_path);
    $contents=file_get_contents($tmp_path);
    $contents=explode("\n", $contents);
    unlink($tmp_path);
    
    
    for($x = 0; $x < sizeof($contents); $x++)
    {
        $contents[$x]=explode(" | ", $contents[$x]);
        
        //adds a view
        if($contents[$x][0]==$photo_id&&$contents[$x][3]==$user_id)
            return $contents[$x][2];
    }
    
    return "";
}

//records login
function record_login()
{
    //gets all the necessary AWS schtuff
    if (!class_exists('S3'))
        require_once('S3.php');
    if (!defined('awsAccessKey'))
        define('awsAccessKey', ACCESS_KEY);
    if (!defined('awsSecretKey'))
        define('awsSecretKey', SECRET_KEY);

    //creates S3 item with schtuff
    $s3 = new S3(awsAccessKey, awsSecretKey);
    
    $file_names=get_file_names($_SESSION['id']);
    $other_file_names=$file_names[3];
    
    //gets file stuff
    $path="users/$_SESSION[id]/files/other/$other_file_names[0].txt";
    $value=md5(uniqid(rand()));
    $tmp_path="/var/www/tmp_files/$value.txt";
    $s3->getObject('bucket_name', $path, $tmp_path);
    $contents=file_get_contents($tmp_path);
    $contents=explode("\n", $contents);
    
    
    $contents[]=get_date().' | '.$_SERVER['HTTP_X_FORWARDED_FOR'];
    
    
    $contents=implode("\n", $contents);
    file_put_contents($tmp_path, $contents);
    $s3->putObjectFile($tmp_path, "bucket_name", $path, S3::ACL_PUBLIC_READ);
    unlink($tmp_path);
}
//records page login
function record_page_login()
{
//    //gets all the necessary AWS schtuff
//    if (!class_exists('S3'))
//        require_once('S3.php');
//    if (!defined('awsAccessKey'))
//        define('awsAccessKey', ACCESS_KEY);
//    if (!defined('awsSecretKey'))
//        define('awsSecretKey', SECRET_KEY);
//
//    //creates S3 item with schtuff
//    $s3 = new S3(awsAccessKey, awsSecretKey);
//    
//    //gets file stuff
//    $path="pages/$_SESSION[page_id]/files/other/login.txt";
//    $value=md5(uniqid(rand()));
//    $tmp_path="/var/www/tmp_files/$value.txt";
//    $s3->getObject('pages_bucket_name', $path, $tmp_path);
//    $contents=file_get_contents($tmp_path);
//    $contents=explode("\n", $contents);
//    
//    
//    $contents[]=get_date().' | '.$_SERVER['HTTP_X_FORWARDED_FOR'];
//    
//    
//    $contents=implode("\n", $contents);
//    file_put_contents($tmp_path, $contents);
//    $s3->putObjectFile($tmp_path, "pages_bucket_name", $path, S3::ACL_PUBLIC_READ);
//    unlink($tmp_path);
}

//records logout
function record_logout()
{
    //gets all the necessary AWS schtuff
    if (!class_exists('S3'))
        require_once('S3.php');
    if (!defined('awsAccessKey'))
        define('awsAccessKey', ACCESS_KEY);
    if (!defined('awsSecretKey'))
        define('awsSecretKey', SECRET_KEY);

    //creates S3 item with schtuff
    $s3 = new S3(awsAccessKey, awsSecretKey);
    
    $file_names=get_file_names($_SESSION['id']);
    $other_file_names=$file_names[3];
    
    //gets file stuff
    $path="users/$_SESSION[id]/files/other/$other_file_names[1].txt";
    $value=md5(uniqid(rand()));
    $tmp_path="/var/www/tmp_files/$value.txt";
    $s3->getObject('bucket_name', $path, $tmp_path);
    $contents=file_get_contents($tmp_path);
    $contents=explode("\n", $contents);
    
    
    $contents[]=get_date().' | '.$_SERVER['HTTP_X_FORWARDED_FOR'];
    
    
    $contents=implode("\n", $contents);
    file_put_contents($tmp_path, $contents);
    $s3->putObjectFile($tmp_path, "bucket_name", $path, S3::ACL_PUBLIC_READ);
    unlink($tmp_path);
}

//records logout
function record_page_logout()
{
    //gets all the necessary AWS schtuff
    if (!class_exists('S3'))
        require_once('S3.php');
    if (!defined('awsAccessKey'))
        define('awsAccessKey', ACCESS_KEY);
    if (!defined('awsSecretKey'))
        define('awsSecretKey', SECRET_KEY);

    //creates S3 item with schtuff
    $s3 = new S3(awsAccessKey, awsSecretKey);
    
    
    //gets file stuff
    $path="pages/$_SESSION[page_id]/files/other/logout.txt";
    $value=md5(uniqid(rand()));
    $tmp_path="/var/www/tmp_files/$value.txt";
    $s3->getObject('pages_bucket_name', $path, $tmp_path);
    $contents=file_get_contents($tmp_path);
    $contents=explode("\n", $contents);
    
    
    $contents[]=get_date().' | '.$_SERVER['HTTP_X_FORWARDED_FOR'];
    
    
    $contents=implode("\n", $contents);
    file_put_contents($tmp_path, $contents);
    $s3->putObjectFile($tmp_path, "pages_bucket_name", $path, S3::ACL_PUBLIC_READ);
    unlink($tmp_path);
}

function record_photo_view($photo_id, $user_id)
{
    //gets all the necessary AWS schtuff
    if (!class_exists('S3'))
        require_once('S3.php');
    if (!defined('awsAccessKey'))
        define('awsAccessKey', ACCESS_KEY);
    if (!defined('awsSecretKey'))
        define('awsSecretKey', SECRET_KEY);

    //creates S3 item with schtuff
    $s3 = new S3(awsAccessKey, awsSecretKey);
    
    $file_names=get_file_names($_SESSION['id']);
    $photo_file_names=$file_names[1];
    
    //gets file stuff
    $path="users/$_SESSION[id]/files/photos/$photo_file_names[0].txt";
    $value=md5(uniqid(rand()));
    $tmp_path="/var/www/tmp_files/$value.txt";
    $s3->getObject('bucket_name', $path, $tmp_path);
    $contents=file_get_contents($tmp_path);
    $contents=explode("\n", $contents);


    $exists=false;
    for($x = 0; $x < sizeof($contents); $x++)
    {
        $contents[$x]=explode(" | ", $contents[$x]);

        //adds a view
        $date=get_last_time_photo_viewed($photo_id, $user_id);
        if($contents[$x][0]==$photo_id&&get_date()-$date>=60)
        {
            $counter=(int)$contents[$x][1];
            $counter++;
            $contents[$x][1]=$counter;
            $exists=true;
        }
        else if($contents[$x][0]==$photo_id)
            $exists=true;

        $contents[$x]=implode(" | ",$contents[$x]);
    }

    if($exists==false)
    {
        $new=$photo_id." | 1";
        if($contents[0]=='')
            $contents[0]=$new;
        else
            $contents[]=$new;
    }

    $contents=implode("\n", $contents);
    file_put_contents($tmp_path, $contents);
    $s3->putObjectFile($tmp_path, "bucket_name", $path, S3::ACL_PUBLIC_READ);
    unlink($tmp_path);
    
    
        
    //gets file stuff
    $path="users/$_SESSION[id]/files/photos/$photo_file_names[4].txt";
    $value=md5(uniqid(rand()));
    $tmp_path="/var/www/tmp_files/$value.txt";
    $s3->getObject('bucket_name', $path, $tmp_path);
    $contents=file_get_contents($tmp_path);
    $contents=explode("\n", $contents);


    $exists=false;
    for($x = 0; $x < sizeof($contents); $x++)
    {
        $contents[$x]=explode(" | ", $contents[$x]);

        //adds a view
        if($contents[$x][0]==$photo_id&&$contents[$x][3]==$user_id&&(get_date()-$contents[$x][2]>=60))
        {
            $counter=(int)$contents[$x][1];
            $counter++;
            $contents[$x][1]=$counter;
            $contents[$x][2]=get_date();
            $contents[$x][3]=$user_id;
            $exists=true;
        }
        else if($contents[$x][0]==$photo_id&&$contents[$x][3]==$user_id)
            $exists=true;

        $contents[$x]=implode(" | ",$contents[$x]);
    }

    if($exists==false)
    {
        $new=$photo_id." | 1 | ".get_date()." | ".$user_id;
        if($contents[0]=='')
            $contents[0]=$new;
        else
            $contents[]=$new;
    }

    $contents=implode("\n", $contents);
    file_put_contents($tmp_path, $contents);
    $s3->putObjectFile($tmp_path, "bucket_name", $path, S3::ACL_PUBLIC_READ);
    unlink($tmp_path);
}

//records when commenting on photo
function record_photo_comment($photo_id, $user_id)
{
    //gets all the necessary AWS schtuff
    if (!class_exists('S3'))
        require_once('S3.php');
    if (!defined('awsAccessKey'))
        define('awsAccessKey', ACCESS_KEY);
    if (!defined('awsSecretKey'))
        define('awsSecretKey', SECRET_KEY);

    //creates S3 item with schtuff
    $s3 = new S3(awsAccessKey, awsSecretKey);
    
    $file_names=get_file_names($_SESSION['id']);
    $photo_file_names=$file_names[1];
    
    //gets file stuff
    $path="users/$_SESSION[id]/files/photos/$photo_file_names[1].txt";
    $value=md5(uniqid(rand()));
    $tmp_path="/var/www/tmp_files/$value.txt";
    $s3->getObject('bucket_name', $path, $tmp_path);
    $contents=file_get_contents($tmp_path);
    $contents=explode("\n", $contents);

    $exists=false;
    for($x = 0; $x < sizeof($contents); $x++)
    {
        $contents[$x]=explode(" | ", $contents[$x]);

        //adds a view
        if($contents[$x][0]==$photo_id&&$contents[$x][2]==$user_id)
        {
            $counter=(int)$contents[$x][1];
            $counter++;
            $contents[$x][1]=$counter;
            $contents[$x][2]=$user_id;
            $exists=true;
        }
        else if($contents[$x][0]==$photo_id&&$contents[$x][2]==$user_id)
            $exists=true;

        $contents[$x]=implode(" | ",$contents[$x]);
    }

    if($exists==false)
    {
        $new=$photo_id." | 1 | ".$user_id;
        if($contents[0]=='')
            $contents[0]=$new;
        else
            $contents[]=$new;
    }

    $contents=implode("\n", $contents);
    file_put_contents($tmp_path, $contents);
    $s3->putObjectFile($tmp_path, "bucket_name", $path, S3::ACL_PUBLIC_READ);
    unlink($tmp_path);
}

//records when liking a photo
function record_photo_like($photo_id, $user_id)
{
    //gets all the necessary AWS schtuff
    if (!class_exists('S3'))
        require_once('S3.php');
    if (!defined('awsAccessKey'))
        define('awsAccessKey', ACCESS_KEY);
    if (!defined('awsSecretKey'))
        define('awsSecretKey', SECRET_KEY);

    //creates S3 item with schtuff
    $s3 = new S3(awsAccessKey, awsSecretKey);
    
    $file_names=get_file_names($_SESSION['id']);
    $photo_file_names=$file_names[1];
    
    //gets file stuff
    $path="users/$_SESSION[id]/files/photos/$photo_file_names[3].txt";
    $value=md5(uniqid(rand()));
    $tmp_path="/var/www/tmp_files/$value.txt";
    $s3->getObject('bucket_name', $path, $tmp_path);
    $contents=file_get_contents($tmp_path);
    $contents=explode("\n", $contents);


    $exists=false;
    for($x = 0; $x < sizeof($contents); $x++)
    {
        $contents[$x]=explode(" | ", $contents[$x]);

        //adds a view
        if($contents[$x][0]==$photo_id&&$contents[$x][1]==$user_id)
        {
            $contents[$x][2]=get_date();
            $exists=true;
        }

        $contents[$x]=implode(" | ",$contents[$x]);
    }

    if($exists==false)
    {
        $new=$photo_id." | ".$user_id." | ".get_date();
        if($contents[0]=='')
            $contents[0]=$new;
        else
            $contents[]=$new;
    }

    $contents=implode("\n", $contents);
    file_put_contents($tmp_path, $contents);
    $s3->putObjectFile($tmp_path, "bucket_name", $path, S3::ACL_PUBLIC_READ);
    unlink($tmp_path);
}

//records when disliking a photo
function record_photo_dislike($photo_id, $user_id)
{
    //gets all the necessary AWS schtuff
    if (!class_exists('S3'))
        require_once('S3.php');
    if (!defined('awsAccessKey'))
        define('awsAccessKey', ACCESS_KEY);
    if (!defined('awsSecretKey'))
        define('awsSecretKey', SECRET_KEY);

    //creates S3 item with schtuff
    $s3 = new S3(awsAccessKey, awsSecretKey);
    
    $file_names=get_file_names($_SESSION['id']);
    $photo_file_names=$file_names[1];
    
    //gets file stuff
    $path="users/$_SESSION[id]/files/photos/$photo_file_names[2].txt";
    $value=md5(uniqid(rand()));
    $tmp_path="/var/www/tmp_files/$value.txt";
    $s3->getObject('bucket_name', $path, $tmp_path);
    $contents=file_get_contents($tmp_path);
    $contents=explode("\n", $contents);


    $exists=false;
    for($x = 0; $x < sizeof($contents); $x++)
    {
        $contents[$x]=explode(" | ", $contents[$x]);

        //adds a view
        if($contents[$x][0]==$photo_id&&$contents[$x][1]==$user_id)
        {
            $contents[$x][2]=get_date();
            $exists=true;
        }

        $contents[$x]=implode(" | ",$contents[$x]);
    }

    if($exists==false)
    {
        $new=$photo_id." | ".$user_id." | ".get_date();
        if($contents[0]=='')
            $contents[0]=$new;
        else
            $contents[]=$new;
    }

    $contents=implode("\n", $contents);
    file_put_contents($tmp_path, $contents);
    $s3->putObjectFile($tmp_path, "bucket_name", $path, S3::ACL_PUBLIC_READ);
    unlink($tmp_path);
}

//records when commenting on photo
function record_post_comment($post_id, $user_id)
{
    //gets all the necessary AWS schtuff
    if (!class_exists('S3'))
        require_once('S3.php');
    if (!defined('awsAccessKey'))
        define('awsAccessKey', ACCESS_KEY);
    if (!defined('awsSecretKey'))
        define('awsSecretKey', SECRET_KEY);

    //creates S3 item with schtuff
    $s3 = new S3(awsAccessKey, awsSecretKey);
    
    $file_names=get_file_names($_SESSION['id']);
    $post_file_names=$file_names[2];
    
    //gets file stuff
    $path="users/$_SESSION[id]/files/posts/$post_file_names[0].txt";
    $value=md5(uniqid(rand()));
    $tmp_path="/var/www/tmp_files/$value.txt";
    $s3->getObject('bucket_name', $path, $tmp_path);
    $contents=file_get_contents($tmp_path);
    $contents=explode("\n", $contents);


    $exists=false;
    for($x = 0; $x < sizeof($contents); $x++)
    {
        $contents[$x]=explode(" | ", $contents[$x]);

        //adds a view
        if($contents[$x][0]==$post_id&&$contents[$x][2]==$user_id)
        {
            $counter=(int)$contents[$x][1];
            $counter++;
            $contents[$x][1]=$counter;
            $exists=true;
        }
        else if($contents[$x][0]==$post_id)
            $exists=true;

        $contents[$x]=implode(" | ",$contents[$x]);
    }

    if($exists==false)
    {
        $new=$post_id." | 1 | ".$user_id;
        if($contents[0]=='')
            $contents[0]=$new;
        else
            $contents[]=$new;
    }

    $contents=implode("\n", $contents);
    file_put_contents($tmp_path, $contents);
    $s3->putObjectFile($tmp_path, "bucket_name", $path, S3::ACL_PUBLIC_READ);
    unlink($tmp_path);
}

//records when liking a photo
function record_post_like($post_id, $user_id)
{
    //gets all the necessary AWS schtuff
    if (!class_exists('S3'))
        require_once('S3.php');
    if (!defined('awsAccessKey'))
        define('awsAccessKey', ACCESS_KEY);
    if (!defined('awsSecretKey'))
        define('awsSecretKey', SECRET_KEY);

    //creates S3 item with schtuff
    $s3 = new S3(awsAccessKey, awsSecretKey);
    
    $file_names=get_file_names($_SESSION['id']);
    $post_file_names=$file_names[2];
    
    //gets file stuff
    $path="users/$_SESSION[id]/files/posts/$post_file_names[2].txt";
    $value=md5(uniqid(rand()));
    $tmp_path="/var/www/tmp_files/$value.txt";
    $s3->getObject('bucket_name', $path, $tmp_path);
    $contents=file_get_contents($tmp_path);
    $contents=explode("\n", $contents);


    $exists=false;
    for($x = 0; $x < sizeof($contents); $x++)
    {
        $contents[$x]=explode(" | ", $contents[$x]);

        //adds a view
        if($contents[$x][0]==$post_id&&$contents[$x][1]==$user_id)
        {
            $contents[$x][2]=get_date();
            $exists=true;
        }

        $contents[$x]=implode(" | ",$contents[$x]);
    }

    if($exists==false)
    {
        $new=$post_id." | ".$user_id." | ".get_date();
        if($contents[0]=='')
            $contents[0]=$new;
        else
            $contents[]=$new;
    }

    $contents=implode("\n", $contents);
    file_put_contents($tmp_path, $contents);
    $s3->putObjectFile($tmp_path, "bucket_name", $path, S3::ACL_PUBLIC_READ);
    unlink($tmp_path);
}

//records when disliking a photo
function record_post_dislike($post_id, $user_id)
{
    //gets all the necessary AWS schtuff
    if (!class_exists('S3'))
        require_once('S3.php');
    if (!defined('awsAccessKey'))
        define('awsAccessKey', ACCESS_KEY);
    if (!defined('awsSecretKey'))
        define('awsSecretKey', SECRET_KEY);

    //creates S3 item with schtuff
    $s3 = new S3(awsAccessKey, awsSecretKey);
    
    $file_names=get_file_names($_SESSION['id']);
    $post_file_names=$file_names[2];
    
    //gets file stuff
    $path="users/$_SESSION[id]/files/posts/$post_file_names[1].txt";
    $value=md5(uniqid(rand()));
    $tmp_path="/var/www/tmp_files/$value.txt";
    $s3->getObject('bucket_name', $path, $tmp_path);
    $contents=file_get_contents($tmp_path);
    $contents=explode("\n", $contents);


    $exists=false;
    for($x = 0; $x < sizeof($contents); $x++)
    {
        $contents[$x]=explode(" | ", $contents[$x]);

        //adds a view
        if($contents[$x][0]==$post_id&&$contents[$x][1]==$user_id)
        {
            $contents[$x][2]=get_date();
            $exists=true;
        }

        $contents[$x]=implode(" | ",$contents[$x]);
    }

    if($exists==false)
    {
        $new=$post_id." | ".$user_id." | ".get_date();
        if($contents[0]=='')
            $contents[0]=$new;
        else
            $contents[]=$new;
    }

    $contents=implode("\n", $contents);
    file_put_contents($tmp_path, $contents);
    $s3->putObjectFile($tmp_path, "bucket_name", $path, S3::ACL_PUBLIC_READ);
    unlink($tmp_path);
}

//records when viewing profile
function record_profile_view($ID)
{
    //gets all the necessary AWS schtuff
    if (!class_exists('S3'))
        require_once('S3.php');
    if (!defined('awsAccessKey'))
        define('awsAccessKey', ACCESS_KEY);
    if (!defined('awsSecretKey'))
        define('awsSecretKey', SECRET_KEY);

    //creates S3 item with schtuff
    $s3 = new S3(awsAccessKey, awsSecretKey);
    
    $file_names=get_file_names($_SESSION['id']);
    $profile_file_names=$file_names[0];
    
    //records new view for other user's profile
    //0 = ID
    //1 = view number
    //2 = timestamp of last view
    //
        //gets file stuff
        $path="users/$ID/files/profiles/$profile_file_names[0].txt";
        
        //gets a random number string for temp path
        $value=md5(uniqid(rand()));
        
        //initializes a temp path
        $tmp_path="/var/www/tmp_files/$value.txt";
        
        //moves profile_views.txt to temp path
        $s3->getObject('bucket_name', $path, $tmp_path);
        
        //get content of temp file
        $contents=file_get_contents($tmp_path);
        
        //delete temp file
        unlink($tmp_path);
        
        //explode contents
        $contents=explode("\n", $contents);


        
        $exists=false;
        
        //searches through list of users
        for($x = 0; $x < sizeof($contents); $x++)
        {
            //explodes row
            $contents[$x]=explode(" | ", $contents[$x]);

            //adds a to ID
            if($contents[$x][0]==$_SESSION['id']&&get_date()-$contents[$x][2]>=60)
            {
                $counter=(int)$contents[$x][1];
                $counter++;
                $contents[$x][1]=$counter;
                $contents[$x][2]=get_date();
                $exists=true;
            }
            else if($contents[$x][0]==$_SESSION['id'])
                $exists=true;

            //implodes row
            $contents[$x]=implode(" | ",$contents[$x]);
        }

        //if record didn't exist, add it
        if($exists==false)
        {
            //adds ID to row and gives it a view of 1
            $new=$_SESSION[id]." | 1 | ".get_date();
            if($contents[0]=='')
                $contents[0]=$new;
            else
                $contents[]=$new;
        }

        //implodes content
        $contents=implode("\n", $contents);
        
        //puts contents in a temp file
        file_put_contents($tmp_path, $contents);
        
        //puts uploads that temp file to S3
        $s3->putObjectFile($tmp_path, "bucket_name", $path, S3::ACL_PUBLIC_READ);
        
        //deletes test file
        unlink($tmp_path);
        
    //records view for the number of times current user has viewed this photo
    //0 = ID
    //1 = view number
    //2 = timestamp of last view
        //gets file stuff
        $path="users/$_SESSION[id]/files/profiles/$profile_file_names[1].txt";
        
        //gets random number string for temp file name
        $value=md5(uniqid(rand()));
        
        //gets path for temp file
        $tmp_path="/var/www/tmp_files/$value.txt";
        
        //gets file
        $s3->getObject('bucket_name', $path, $tmp_path);
        
        //gets contents of temp file
        $contents=file_get_contents($tmp_path);
        
        //deletes temp file
        unlink($tmp_path);
        
        //explodes content
        $contents=explode("\n", $contents);

        //iterates through content
        $exists=false;
        for($x = 0; $x < sizeof($contents); $x++)
        {
            //explodes row
            $contents[$x]=explode(" | ", $contents[$x]);

            //adds a view
            if($contents[$x][0]==$ID&&(get_date()-$contents[$x][2]>=60))
            {
                $counter=(int)$contents[$x][1];
                $counter++;
                $contents[$x][1]=$counter;
                $contents[$x][2]=get_date();
                $exists=true;
            }
            else if($contents[$x][0]==$ID)
                $exists=true;

            //implodes row
            $contents[$x]=implode(" | ",$contents[$x]);
        }

        //if haven't viewed profile before
        if($exists==false)
        {
            //create new row for profile
            $new=$ID." | 1 | ".get_date();
            if($contents[0]=='')
                $contents[0]=$new;
            else
                $contents[]=$new;
        }

        //implodes contents
        $contents=implode("\n", $contents);
        
        //creates temp file with contents
        file_put_contents($tmp_path, $contents);
        
        //uploads temp file to S3
        $s3->putObjectFile($tmp_path, "bucket_name", $path, S3::ACL_PUBLIC_READ);
        
        //deletes temp file
        unlink($tmp_path);
}
function record_redlay_gold_purchase($user_id, $type)
{
    $path="../redlay_gold.txt";
    $contents=file_get_contents($path);
    $contents=explode("\n", $contents);

    //adds purchase to array
    $contents[]=$type." | ".$user_id." | ".get_date();

    $contents=implode("\n", $contents);
    file_put_contents($path, $contents);
}
function facebook_methods($num, $friends)
{
   //stores user's facebook friends
   if($num==1)
   {
      
      
//      $query=mysql_query("SELECT friends FROM facebook_invite WHERE user_id=$_SESSION[id] LIMIT 1");
//      if($query)
//      {
         //insert into table
//         if(mysql_num_rows($query)==0)
//         {
//            $friends=$friends['data'];
//            
//            $friend_ids=array();
//            for($x = 0; $x < sizeof($friends); $x++)
//               $friend_ids[$x]=(int)($friends[$x]['id']);
//
//            $friend_ids=implode('|^|*|', $friend_ids);
//            $query=mysql_query("INSERT INTO facebook_invite SET friends='$friend_ids', user_id=$_SESSION[id]");
//         }
//         //re-inserts list of friends
//         else
//         {
//            $array=mysql_fetch_row($query);
//            $friends_list=explode('|^|*|', $array[0]);
//            
//            $friends=$friends['data'];
//            for($x = 0; $x < sizeof($friends); $x++)
//            {
//               if(!in_array($friends[$x]['id'], $friends_list))
//                   $friends_list[]=$friends[$x]['id'];
//            }
//            
//            $friends_list=implode('|^|*|', $friends_list);
//            $query=mysql_query("UPDATE facebook_invite SET friends='$friends_list' WHERE user_id=$_SESSION[id]");
//         }
//      }
            $friends=$friends['data'];
            $friends_list=array();
            for($x = 0; $x < sizeof($friends); $x++)
            {
               if(!in_array($friends[$x]['id'], $friends_list))
                   $friends_list[]=$friends[$x]['id'];
            }
            
            $friends_list=implode('|^|*|', $friends_list);
            $query=mysql_query("UPDATE facebook_invite SET friends='$friends_list' WHERE user_id=$_SESSION[id]");
   }
}
function get_num_adds($ID)
{
   if(is_id($ID)&&user_id_exists($ID))
   {
      $query=mysql_query("SELECT user_friends FROM user_data WHERE user_id=$ID LIMIT 1");
      if($query&&mysql_num_rows($query)==1)
      {
         $array=mysql_fetch_row($query);
         $adds=explode('|^|*|', $array[0]);

         if($array[0]!='')
            return sizeof($adds);
         else
            return 0;
      }
   }
}
//gets the number of times a page has been liked
function get_num_likes($ID)
{
    if(is_id($ID)&&page_id_exists($ID)&&!page_id_terminated($ID))
    {
        $query=mysql_query("SELECT likes FROM page_data WHERE page_id=$ID LIMIT 1");
        if($query&&mysql_num_rows($query)==1)
        {
            $array=mysql_fetch_row($query);
            return $array[0];
        }
    }
}
function get_add_title($ID)
{
   $query=mysql_query("SELECT friend_title FROM user_display WHERE user_id=$ID LIMIT 1");
   if($query&&mysql_num_rows($query)==1)
   {
      $array=mysql_fetch_row($query);
      $add_title=$array[0];
      return $add_title;
   }
   
}
function format_post($post)
{
   $array=explode(' ', $post);
   $temp_array=array();
   for($x = 0; $x < sizeof($array); $x++)
   {
      //enters in new line if part of string is over 26 characters
      if(strlen($array[$x])>=26)
      {
         while(strlen($array[$x])>=26)
         {
            $temp=substr($array[$x], 0, 26);
            $temp_array[]=$temp;
            $array[$x]=substr($array[$x], 26);
         }
      }
      else
         $temp_array[]=$array[$x];
   }
   return implode(' ', $temp_array);
}
function get_calendar_visibility($ID)
{
    if(is_id($ID)&&user_id_exists($ID)&&!user_id_terminated($ID))
    {
        $query=mysql_query("SELECT calendar_visible FROM user_display WHERE user_id=$ID LIMIT 1");
        if($query&&mysql_num_rows($query)==1)
        {
            $array=mysql_fetch_row($query);
            
            if($array[0]=='yes')
                return true;
            else
                return false;
        }
    }
}
function get_user_banner($ID)
{
    if(file_exists_server("https://s3.amazonaws.com/bucket_name/users/$ID/photos/banner.jpg"))
        return "https://s3.amazonaws.com/bucket_name/users/$ID/photos/banner.jpg";
    else if(file_exists_server("https://s3.amazonaws.com/bucket_name/users/$ID/photos/banner.png"))
        return "https://s3.amazonaws.com/bucket_name/users/$ID/photos/banner.png";
    else if(file_exists_server("https://s3.amazonaws.com/bucket_name/users/$ID/photos/banner.gif"))
        return "https://s3.amazonaws.com/bucket_name/users/$ID/photos/banner.gif";
    else
        return "";
}
//gets user's background picture
function get_user_background_pic($ID)
{
    if(file_exists_server("https://s3.amazonaws.com/bucket_name/users/$ID/photos/background.jpg"))
        return "https://s3.amazonaws.com/bucket_name/users/$ID/photos/background.jpg";
    else if(file_exists_server("https://s3.amazonaws.com/bucket_name/users/$ID/photos/background.png"))
        return "https://s3.amazonaws.com/bucket_name/users/$ID/photos/background.png";
    else if(file_exists_server("https://s3.amazonaws.com/bucket_name/users/$ID/photos/background.gif"))
        return "https://s3.amazonaws.com/bucket_name/users/$ID/photos/background.gif";
    else
        return "";
}
//gets user's background picture
function get_page_background_pic($ID)
{
    if(file_exists_server("https://s3.amazonaws.com/pages_bucket_name/pages/$ID/photos/background.jpg"))
        return "https://s3.amazonaws.com/pages_bucket_name/pages/$ID/photos/background.jpg";
    else if(file_exists_server("https://s3.amazonaws.com/pages_bucket_name/pages/$ID/photos/background.png"))
        return "https://s3.amazonaws.com/pages_bucket_name/pages/$ID/photos/background.png";
    else if(file_exists_server("https://s3.amazonaws.com/pages_bucket_name/pages/$ID/photos/background.png"))
        return "https://s3.amazonaws.com/pages_bucket_name/pages/$ID/photos/background.gif";
    else
        return "";
}

function get_default_background_pic($redlay_theme)
{
    if($redlay_theme=="black")
        return "http://pics.redlay.com/pictures/themes/dark/default_background.jpg";
    else
        return "";
}

//returns source of picture thumbnail
function get_pic_thumbnail_src($ID, $photo_id, $image_type)
{
    return "https://s3.amazonaws.com/bucket_name/users/$ID/thumbs/$photo_id.".$image_type;
}

//returns source of picture
function get_pic_src($ID, $photo_id, $image_type)
{
    return "http://u.redlay.com/users/$ID/photos/$photo_id.".$image_type;
}

//returns source of picture thumbnail
function get_page_pic_thumbnail_src($ID, $photo_id, $image_type)
{
    return "https://s3.amazonaws.com/pages_bucket_name/pages/$ID/thumbs/$photo_id.".$image_type;
}

//returns source of picture
function get_page_pic_src($ID, $photo_id, $image_type)
{
    return "http://u.redlay.com/users/$ID/photos/$photo_id.".$image_type;
}

//checks if a file exists on a different server
//(used for EC2 and S3 communication)
function file_exists_server($file_url)
{
    $AgetHeaders = @get_headers($file_url);
    if (preg_match("|200|", $AgetHeaders[0])) 
        return true;
    else
        return false;
}

////new name: users/$_SESSION[id]/photos/19fiusdh239fjo82389.jpg
//function upload_picture($new_name, $temp_name, $bucket, $type, $new_width, $new_height, $width, $height)
//{
//    //gets all the necessary AWS schtuff
//    if (!class_exists('S3'))
//        require_once('S3.php');
//    if (!defined('awsAccessKey'))
//        define('awsAccessKey', ACCESS_KEY);
//    if (!defined('awsSecretKey'))
//        define('awsSecretKey', SECRET_KEY);
//
//    //creates S3 item with schtuff
//    $s3 = new S3(awsAccessKey, awsSecretKey);
//
//    if($type=='jpg')
//    {
//        $img=imagecreatefromjpeg($temp_name);
//        $thumb=imagecreatetruecolor($new_width, $new_height);
//        imagecopyresampled($thumb, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
//        imagejpeg($thumb, $temp_name);
//    }
//    
//    
//    $s3->putObjectFile($temp_name, $bucket, $new_name, S3::ACL_PUBLIC_READ);
//    
//    unlink($temp_name);
//}

function is_online($ID)
{
    if(is_id($ID)&&user_id_exists($ID)&&!user_id_terminated($ID))
    {
        $query=mysql_query("SELECT timestamp FROM online WHERE user_id=$ID LIMIT 1");
        if($query&&mysql_num_rows($query)==1)
        {
            $array=mysql_fetch_row($query);
            $timestamp=explode('|^|*|', $array[0]);
            if($timestamp[0]=='online')
                return true;
            else
                return false;
        }
    }
}
function format_number($number)
{
    $temp=$number;
    
    while($number>=1000)
        $number-=1000;
    
    while($number>=100)
        $number-=100;
    
    while($number>=10)
        $number-=10;
    
    while($number>=1)
        $number-=1;
    
    return $temp-$number;   
}
//checks if email isn't a temporary email
function is_valid_email($email)
{
    if(strstr($email, "mailinator")==true) return false;
    else if(strstr($email, "guerrillamail")==true) return false;
    else if(strstr($email, 'dispostable')==true)return false;
    else if(strstr($email, 'disposemail')==true)return false;
    else if(strstr($email, 'yopmail')==true)return false;
    else if(strstr($email, 'getairmail')==true)return false;
    else if(strstr($email, 'fakeinbox')==true)return false;
    else if(strstr($email, '10minutemail')==true)return false;
    else if(strstr($email, '20minutemail')==true)return false;
    else if(strstr($email, 'deadaddress')==true)return false;
    else if(strstr($email, 'emailsensei')==true)return false;
    else if(strstr($email, 'emailthe')==true)return false;
    else if(strstr($email, 'incognitomail')==true)return false;
    else if(strstr($email, 'koszmail')==true)return false;
    else if(strstr($email, 'mailcatch')==true)return false;
    else if(strstr($email, 'mailnesia')==true)return false;
    else if(strstr($email, 'mytrashmail')==true)return false;
    else if(strstr($email, 'noclickemail')==true)return false;
    else if(strstr($email, 'spamspot')==true)return false;
    else if(strstr($email, 'spamavert')==true)return false;
    else if(strstr($email, 'spamfree24')==true)return false;
    else if(strstr($email, 'tempemail')==true)return false;
    else if(strstr($email, 'trashmail')==true)return false;
    else if(strstr($email, 'easytrashmail')==true)return false;
    else if(strstr($email, 'easytrashemail')==true)return false;
    else if(strstr($email, 'jetable')==true)return false;
    else if(strstr($email, 'mailexpire')==true)return false;
    else if(strstr($email, 'emailexpire')==true)return false;
    else if(strstr($email, 'meltmail')==true)return false;
    else if(strstr($email, 'spambox')==true)return false;
    else if(strstr($email, 'tempomail')==true)return false;
    else if(strstr($email, 'tempoemail')==true)return false;
    else if(strstr($email, '33mail')==true)return false;
    else if(strstr($email, 'e4ward')==true)return false;
    else if(strstr($email, 'gishpuppy')==true)return false;
    else if(strstr($email, 'inboxalias')==true)return false;
    else if(strstr($email, 'mailnull')==true)return false;
    else if(strstr($email, 'spamex')==true)return false;
    else if(strstr($email, 'spamgourmet')==true)return false;
    else if(strstr($email, 'dudmail')==true)return false;
    else if(strstr($email, 'mintemail')==true)return false;
    else if(strstr($email, 'spambog')==true)return false;
    else if(strstr($email, 'flitzmail')==true)return false;
    else if(strstr($email, 'eyepaste')==true)return false;
    else if(strstr($email, '12minutemail')==true)return false;
    else if(strstr($email, 'onewaymail')==true)return false;
    else if(strstr($email, 'disposableinbox')==true)return false;
    else if(strstr($email, 'freemail')==true)return false;
    else if(strstr($email, 'koszmail')==true)return false;
    else if(strstr($email, '0wnd')==true)return false;
    else if(strstr($email, '2prong')==true)return false;
    else if(strstr($email, 'binkmail')==true)return false;
    else if(strstr($email, 'amilegit')==true)return false;
    else if(strstr($email, 'bobmail')==true)return false;
    else if(strstr($email, 'brefmail')==true)return false;
    else if(strstr($email, 'bumpymail')==true)return false;
    else if(strstr($email, 'dandikmail')==true)return false;
    else if(strstr($email, 'despam')==true)return false;
    else if(strstr($email, 'dodgeit')==true)return false;
    else if(strstr($email, 'dump-email')==true)return false;
    else if(strstr($email, 'email60')==true)return false;
    else if(strstr($email, 'emailias')==true)return false;
    else if(strstr($email, 'emailinfive')==true)return false;
    else if(strstr($email, 'emailmiser')==true)return false;
    else if(strstr($email, 'emailtemporario')==true)return false;
    else if(strstr($email, 'emailwarden')==true)return false;
    else if(strstr($email, 'ephemail')==true)return false;
    else if(strstr($email, 'explodemail')==true)return false;
    else if(strstr($email, 'fakeinbox')==true)return false;
    else if(strstr($email, 'fakeinformation')==true)return false;
    else if(strstr($email, 'filzmail')==true)return false;
    else if(strstr($email, 'fixmail')==true)return false;
    else if(strstr($email, 'get1mail')==true)return false;
    else if(strstr($email, 'getonemail')==true)return false;
    else if(strstr($email, 'haltospam')==true)return false;
    else if(strstr($email, 'ieatspam')==true)return false;
    else if(strstr($email, 'ihateyoualot')==true)return false;
    else if(strstr($email, 'imails')==true)return false;
    else if(strstr($email, 'inboxclean')==true)return false;
    else if(strstr($email, 'ipoo')==true)return false;
    else if(strstr($email, 'mail4trash')==true)return false;
    else if(strstr($email, 'mailbidon')==true)return false;
    else if(strstr($email, 'maileater')==true)return false;
    else if(strstr($email, 'mailexpire')==true)return false;
    else if(strstr($email, 'mailin8r')==true)return false;
    else if(strstr($email, 'mailinator2')==true)return false;
    else if(strstr($email, 'mailincubator')==true)return false;
    else if(strstr($email, 'mailme')==true)return false;
    else if(strstr($email, 'mailnull')==true)return false;
    else if(strstr($email, 'mailzilla')==true)return false;
    else if(strstr($email, 'meltmail')==true)return false;
    else if(strstr($email, 'nobulk')==true)return false;
    else if(strstr($email, 'nowaymail')==true)return false;
    else if(strstr($email, 'pookmail')==true)return false;
    else if(strstr($email, 'proxymail')==true)return false;
    else if(strstr($email, 'putthisinyourspamdatabase')==true)return false;
    else if(strstr($email, 'quickinbox')==true)return false;
    else if(strstr($email, 'safetymail')==true)return false;
    else if(strstr($email, 'snakemail')==true)return false;
    else if(strstr($email, 'sharklasers')==true)return false;
    else 
        return true;
}

//if theme is valid
function is_valid_theme($theme)
{
    if($theme=="black")
        return true;
    else if($theme=="white")
        return true;
    else if($theme=="aluminum")
        return true;
    else if($theme=="neon")
        return true;
    else if($theme=="beach")
        return true;
    else if($theme=="custom")
        return true;
    else
        return false;
}

//if theme has to be bought
function gold_redlay_theme($theme)
{
    return false;
}
function has_liked_page($user_id, $page_id)
{
    if(is_id($user_id)&&is_id($page_id)&&user_id_exists($user_id)&&page_id_exists($page_id)
            &&!user_id_terminated($user_id)&&!page_id_terminated($page_id))
    {
        $query=mysql_query("SELECT page_liked FROM user_data WHERE user_id=$user_id LIMIT 1");
        if($query&&mysql_num_rows($query)==1)
        {
            $array=mysql_fetch_row($query);
            $pages_liked=explode('|^|*|', $array[0]);
            
            if(in_array($page_id, $pages_liked))
                return true;
            else
                return false;
        }
    }
}
function get_points($user_id)
{
    if(is_id($user_id)&&user_id_exists($user_id)&&!user_id_terminated($user_id))
    {
        $query=mysql_query("SELECT points FROM user_data WHERE user_id=$user_id LIMIT 1");
        if($query&&mysql_num_rows($query)==1)
        {
            $array=mysql_fetch_row($query);
            $points=$array[0];
            
            return $points;
        }
    }
}
function add_point($ID)
{
    if(is_id($ID)&&user_id_exists($ID)&&!user_id_terminated($ID))
    {
        $query=mysql_query("SELECT points FROM user_data WHERE user_id=$ID LIMIT 1");
        if($query&&mysql_num_rows($query)==1)
        {
            $array=mysql_fetch_row($query);
            $points=(int)($array[0]);
            
            $points++;
            
            $query=mysql_query("UPDATE user_data SET points=$points WHERE user_id=$ID");
            if(!$query)
                send_mail_error("universal_functions.php: add_point(): ", mysql_error());
        }
    }
}
function remove_point($ID)
{
    if(is_id($ID)&&user_id_exists($ID)&&!user_id_terminated($ID))
    {
        $query=mysql_query("SELECT points FROM user_data WHERE user_id=$ID LIMIT 1");
        if($query&&mysql_num_rows($query)==1)
        {
            $array=mysql_fetch_row($query);
            $points=(int)($array[0]);
            
            $points--;
            
            $query=mysql_query("UPDATE user_data SET points=$points WHERE user_id=$ID");
            if(!$query)
                send_mail_error("universal_functions.php: add_point(): ", mysql_error());
        }
    }
}
function following_page($ID)
{
    
}
function is_valid_page($page)
{
    if($page=="home"||
       $page=="profile"||
       $page=="index"||
       $page=="settings")
        return true;
    else
        return false;
}