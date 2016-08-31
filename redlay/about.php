<?php
@include('init.php');
include('universal_functions.php');
$allowed="all";
include('security_checks.php');

?>
<!DOCTYPE html>
<html>
    <head>
        <title>About redlay</title>
        <?php include('required_header.php'); ?>
        <script type="text/javascript">
            function change_color()
            {
                <?php
                    if(isset($_SESSION['id']))
                    {
                        $colors = get_user_display_colors($_SESSION['id']);
                        $color = $colors[0];
                        $box_background_color = $colors[1];
                        $text_color = $colors[2];
                    }
                    else
                    {
                        $color="rgb(220,20,0)";
                        $box_background_color="rgb(255,255,255)";
                        $text_color="rgb(30,30,30)";
                    }
                ?>
                $('.box').css('border', '5px solid <?php echo $color; ?>');
                $('.box').css('background-color', '<?php echo $box_background_color; ?>');
                
                $('#about_content').css('background-color', '<?php echo $box_background_color; ?>');
                
                $('.title_color').css('color', "<?php echo $color; ?>");
                $('.text_color').css('color', '<?php echo $text_color; ?>');
            }
        </script>
        <script type="text/javascript">
            $(window).ready(function()
            {
                change_color();
                $('#footer').css('width', '910px');
                <?php include('required_jquery.php'); ?>
            });
        </script>
        <script type="text/javascript">
            <?php include('required_google_analytics.js'); ?>
        </script>
    </head>
    <body>
        <?php 
         if(isset($_SESSION['id']))
         {
            echo "<div id='top'>";
            include('top.php');
            echo "</div>";
         }
         else
           include('index_top.php');
        ?>
        
        <div id="main">
            <?php if(isset($_SESSION['id'])) include('required_side_html.php'); ?>
            <div class="content" id="about_content">
                <table style="padding:20px;">
                   <tbody>
                      <tr>
                         <td colspan="2">
                           <p class="title_color" style="font-size:25px;font-weight:bold;text-align:center;text-decoration:underline" >About</p>
                         </td>
                      </tr>
                      <tr>
                         <td colspan="2">
                            <p class="text_color" style="margin:0px;">Redlay is a social network where you can express yourself and hang out with the people you care about. We will not keep any information that you don't give us. Meaning when you delete something, it gets deleted. When you delete your account, all information gets deleted. The only thing we keep is your account ID number and that's just so if people find you afterwards, we can say your account has been terminated.  </p>
                         </td>
                      </tr>
                      <tr>
                         <td class="about_photo_unit">
                            <img class="about_picture" src="https://s3.amazonaws.com/redlay.pictures/pictures/home_page.png"/>
                         </td>
                         <td>
                            <p class="paragraph text_color">How is Redlay better?</p>
                            <p class="paragraph text_color">1) Customizable profile</p>
                            <p class="paragraph text_color">2) Account statistics</p>
                            <p class="paragraph text_color">3) Public (live feed of posts and photos)</p>
                            <p class="paragraph text_color">4) Calendar</p>
                            <p class="paragraph text_color">5) Easy to use privacy settings</p>
                            <p class="paragraph text_color">6) No ads!</p>
                            <p class="paragraph text_color" style="font-weight:bold;">7) We won't sell your information!</p>
                         </td>
                      </tr>
<!--                      <tr>
                         <td>
                            <p class="paragraph text_color">Redlay gives you the freedom to Dislike posts and photos unlike other social networks. </p>
                         </td>
                         <td class="about_photo_unit">
                            <img class="about_picture" src="http://pics.redlay.com/pictures/dislike_button.png"/>
                         </td>
                      </tr>-->
                      <tr>
                          <td>
                            <p class="paragraph text_color">You have the option of customizing your profile how you like. You can change the main text color, regular text color, background color, and the transparency of it all. Previous social networks had this ability, to some extent, but it was implemented in a way where some profiles were unreadable. </p><p>With Redlay, your profiles have a template and you will always be able to see the text. Along with changing the colors, you have the option to change your background image on your profile. So now whenever someone brings up your profile, they will see your color choices and background picture! </p>
                         </td>
                         <td class="about_photo_unit">
                            <img class="about_picture" src="https://s3.amazonaws.com/redlay.pictures/pictures/home_before.png"/>
                            <img class="about_picture" src="https://s3.amazonaws.com/redlay.pictures/pictures/home_after.png"/>
                         </td>
                      </tr>
                      <tr>
                         <td class="about_photo_unit">
                            <img class="about_picture" src="https://s3.amazonaws.com/redlay.pictures/pictures/account_stats.png"/>
                         </td>
                          <td>
                            <p class="paragraph text_color">Redlay offers you the ability to check out your account statistics. This includes seeing how many times your adds have viewed your profile, how many times you viewed your add's profiles, photos you like, disliked, and commented on, posts you like, disliked, and commented on, and your logins and logouts.</p>
                         </td>
                      </tr>
                      <tr>
                          <td>
                            <p class="paragraph text_color">With Public, you get the ability to get a live feed of all the public photos, posts, and videos being shared on Redlay. No need to wonder what other people are talking about or uploading.</p>
                         </td>
                         <td class="about_photo_unit">
                            <img class="about_picture" src="https://s3.amazonaws.com/redlay.pictures/pictures/public_page.png"/>
                         </td>
                      </tr>
                      <tr>
                         <td class="about_photo_unit">
                            <img class="about_picture" src="https://s3.amazonaws.com/redlay.pictures/pictures/calendar_page.png"/>
                         </td>
                          <td>
                            <p class="paragraph text_color">With Calendar, you get the ability to keep track of your life events. You can even show your adds what your plans are by making the calendar public.</p>
                         </td>
                      </tr>
                      <tr>
                          <td>
                            <p class="paragraph text_color">Redlay provides easy to use privacy settings that do exactly as they say. Unlike other social networks, you will not be tricked into making any information you don't want public. You have so much control that you have the ability to hide all information. You also have the option to make yourself un-searchable.</p>
                         </td>
                         <td class="about_photo_unit">
                            <img class="about_picture" src="https://s3.amazonaws.com/redlay.pictures/pictures/privacy_settings.png" />
                         </td>
                      </tr>
                       <tr>
                           <td colspan="2">
                               <hr class="break" />
                           </td>
                       </tr>
                       <tr>
                           <td colspan="2">
                               <p class="paragraph text_color" style="margin-top:15px;">Redlay is also different in the fact that we will NEVER sell your information and when you delete something off of our site, it actually gets deleted! It won't be "hidden" but will actually be erased from our servers and database. Finally!</p>
                           </td>
                       </tr>
                   </tbody>
                </table>
                <?php include('footer.php'); ?>
            </div>
        </div>
    </body>
</html>