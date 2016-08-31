<?php
@include('init.php');
include('universal_functions.php');
$allowed="all";
include('security_checks.php');
?>
<html>
    <head>
        <title>To Do list</title>
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
                        $color = "rgb(220,20,0)";
                        $box_background_color = "white";
                        $text_color = "rgb(30,30,30)";
                    }
                ?>
                $('.box').css('border', '5px solid <?php echo $color; ?>');
                $('.box').css({'background-color': '<?php echo $box_background_color; ?>'});
                
                $('#todo_content').css('background-color', '<?php echo $box_background_color; ?>');

                $('.title_color').css('color', '<?php echo $color; ?>');
                $('.text_color').css('color', '<?php echo $text_color; ?>');
            }

            $(document).ready(function()
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
        <?php include('required_html.php'); ?>
            <?php 
            if(isset($_SESSION['id']))
            {
                echo "<div id='top'>";
                include('top.php');
                echo "</div>";
            }
            else if(isset($_SESSION['page_id']))
            {
                echo "<div id='top'>";
                include('top_page.php');
                echo "</div>";
            }
            else
                include('index_top.php');
            
            ?>
        <div id="main">
            <?php include('required_side_html.php'); ?>
            <div id="todo_content" class="content">
                <p class="text_color" style="padding:10px;">This lists what needs to be done to Redlay and what has been done to Redlay.</p>
                <hr />
                <table>
                    <tbody>
                        <tr>
                            <td style="border-right:1px solid gray;width:50%;vertical-align:top;">
                                
                                
                                <table style="width:100%;padding:10px;font-size:15px;">
                                    <tbody>
                                        <tr>
                                           <td colspan="2">
                                               <p class="title_color" style="text-align:center;">To-Do:</p>
                                           </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span class="to_do_number text_color">&#149; </span>
                                            </td>
                                            <td>
                                                <span class="to_do_number text_color">Ignore add request instead of delete</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span class="to_do_number text_color">&#149; </span>
                                            </td>
                                            <td>
                                                <span class="to_do_number text_color">Instant messaging across all pages</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span class="to_do_number text_color">&#149; </span>
                                            </td>
                                            <td>
                                                <span class="to_do_number text_color">Allow cropping of profile picture</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span class="to_do_number text_color">&#149; </span>
                                            </td>
                                            <td>
                                                <span class="to_do_number text_color">Require password if changing email</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span class="to_do_number text_color">&#149; </span>
                                            </td>
                                            <td>
                                                <span class="to_do_number text_color">Add likes, dislikes, and comments to videos in Public</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span class="to_do_number text_color">&#149; </span>
                                            </td>
                                            <td>
                                                <span class="to_do_number text_color">Add comments to posts in Public</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span class="to_do_number text_color">&#149; </span>
                                            </td>
                                            <td>
                                                <span class="to_do_number text_color">Add "Top Videos" to Public</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span class="to_do_number text_color">&#149; </span>
                                            </td>
                                            <td>
                                                <span class="to_do_number text_color">Add "Top Pictures" to Public</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span class="to_do_number text_color">&#149; </span>
                                            </td>
                                            <td>
                                                <span class="to_do_number text_color">Allow giving of gold on posts, photos, etc...</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span class="to_do_number text_color">&#149; </span>
                                            </td>
                                            <td>
                                                <span class="to_do_number text_color">Display gold expiration</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span class="to_do_number text_color">&#149; </span>
                                            </td>
                                            <td>
                                                <span class="to_do_number text_color">Fix "See More" button in messages.</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span class="to_do_number text_color">&#149; </span>
                                            </td>
                                            <td>
                                                <span class="to_do_number text_color">Allow gold purchase on any page</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span class="to_do_number text_color">&#149; </span>
                                            </td>
                                            <td>
                                                <span class="to_do_number text_color">Redlay gold - Ability to edit comments and posts (maybe free)</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span class="to_do_number text_color">&#149; </span>
                                            </td>
                                            <td>
                                                <span class="to_do_number text_color">Put time in calendar</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span class="to_do_number text_color">&#149; </span>
                                            </td>
                                            <td>
                                                <span class="to_do_number text_color">Make PNGs transparent</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span class="to_do_number text_color">&#149; </span>
                                            </td>
                                            <td>
                                                <span class="to_do_number text_color">Put in global chat rooms</span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                
                                
                                
                            </td>
                            <td style="width:50%;vertical-align:top;">
                                <table style="width:100%;padding:10px;font-size:15px">
                                    <tbody>
                                        <tr>
                                           <td>
                                               <p class="title_color" style="text-align:center;">Done:</p>
                                           </td> 
                                        </tr>
                                        <tr>
                                            <td>
                                                <span class="to_do_number text_color">&#149; Fix glitch in displaying of photos on profile (August 11, 2013)</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span class="to_do_number text_color">&#149; Allow creation of group from list (June 1, 2013)</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span class="to_do_number text_color">&#149; Allow changing of profile picture from picture (May 27, 2013)</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span class="to_do_number text_color">&#149; Change look of <a href="http://pics.redlay.com/pictures/to_do/index_page_5:19:13.png">index.php</a> (May 17, 2013)</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span class="to_do_number text_color">&#149; Add likes, dislikes, and comments to photos in Public (May 17, 2013)</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span class="to_do_number text_color">&#149; Add likes, dislikes, and comments to photos in Public (May 17, 2013)</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span class="to_do_number text_color">&#149; Ability to change profile picture from profile (May 15, 2013)</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span class="to_do_number text_color">&#149; Redesign like, dislike, and comment buttons (May 8, 2013)</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span class="to_do_number text_color">&#149; Fix comment displaying in view_post.php (May 7, 2013)</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span class="to_do_number text_color">&#149; Fix thumbnails of pictures (May 3, 2013)</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span class="to_do_number text_color">&#149; Add content popularity sorting home page (April 30, 2013)</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span class="to_do_number text_color">&#149; Add likes and dislikes to posts on Public (April 29, 2013)</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span class="to_do_number text_color">&#149; Add "Top Posts" to Public (April 29, 2013)</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span class="to_do_number text_color">&#149; Allow users to put banners on profiles (April 21, 2013)</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span class="to_do_number text_color">&#149; Put in message sound alert (April 5, 2013)</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span class="to_do_number text_color">&#149; Put likes, dislikes, and comments in for videos (April 5, 2013)</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span class="to_do_number text_color">&#149; Put in view Videos content home page (April 1, 2013)</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span class="to_do_number text_color">&#149; Put in list of your add's points (March 24, 2013)</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span class="to_do_number text_color">&#149; Put in more home feed view options (March 21, 2013)</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span class="to_do_number text_color">&#149; Give 25 points to users who refer others (March 17, 2013)</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span class="to_do_number text_color">&#149; Add points feature (March 16, 2013)</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span class="to_do_number text_color">&#149; Have messages not be deleted when unlisting users (March 15, 2013)</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span class="to_do_number text_color">&#149; Redesign buttons (March 13, 2013)</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span class="to_do_number text_color">&#149; Fix search (March 12, 2013)</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span class="to_do_number text_color">&#149; Redesign Redlay (March 11, 2013)</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span class="to_do_number text_color">&#149; Fix registration issue (March 5, 2013)</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span class="to_do_number text_color">&#149; Change profile layout (March 3, 2013)</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span class="to_do_number text_color">&#149; Put in custom themes (February 27, 2013)</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span class="to_do_number text_color">&#149; Redesign Public content info layout (February 24, 2013)</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span class="to_do_number text_color">&#149; Put in emoticons (February 20, 2013)</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span class="to_do_number text_color">&#149; Explain emailing in settings (February 16, 2013)</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span class="to_do_number text_color">&#149; Throttle logins (February 15, 2013)</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span class="to_do_number text_color">&#149; Put in dynamic changes registration_intro (February 14, 2013)</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span class="to_do_number text_color">&#149; Convert links to images in comments and view_photo and view_post (February 14, 2013)</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span class="to_do_number text_color">&#149; Allow picture linking in comments and posts</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span class="to_do_number text_color">&#149; Be able to view stuff without being logged in</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span class="to_do_number text_color">&#149; Allow new lines in messages</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span class="to_do_number text_color">&#149; Put in "See More" button in messages</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span class="to_do_number text_color">&#149; Fix error: redirection in send_all_facebook_friends.php</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span class="to_do_number text_color">&#149; Put registration on index.php</span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                    
                            </td>
                        </tr>
                    </tbody>
                </table>
                <?php include('footer.php'); ?>
            </div>
        </div>
    </body>
</html>