<?php
@include('init.php');
include('universal_functions.php');
$allowed="both";
include('security_checks.php');

?>
<!DOCTYPE html>
<html>
    <head>
        <?php include('required_header.php'); ?>
        <script type="text/javascript">
            function change_color()
            {
                <?php
                    $colors=get_user_display_colors($ID);
                     $color=$colors[0];
                     $box_background_color=$colors[1];
                     $text_color=$colors[2];
                ?>
                $('.box').css('border', '5px solid <?php echo $color; ?>');
                $('.box').css('background-color', '<?php echo $box_background_color; ?>');
                
                $('.title_color').css('color', '<?php echo $color; ?>');
                $('.text_color').css('color', '<?php echo $text_color; ?>');
                <?php $path=get_user_background_pic($ID); if(file_exists_server($path)&&$colors[5]=="yes") echo "$('body').css('background-attachment', 'fixed');"; ?>
            }
            function display_all_adds()
            {
               var timezone=get_timezone();
               $.post('all_adds_query.php',
               {
                  num:1,
                  user_id: <?php echo $ID; ?>,
                  timezone:timezone
               }, function(output)
               {
                  var adds=output.adds;
                  var add_names=output.add_names;
                  var add_profile_pictures=output.add_profile_pictures;
                  var add_num_adds=output.add_num_adds;
                  var num_adds=output.num_adds;
                  var add_dates=output.add_dates;
                  var badges=output.badges;
                  var add_title=output.add_title;
                  
                  if(num_adds!=0)
                  {
                     for(var x = 0; x < adds.length; x++)
                     {
                        var name="<div class='user_name_body' ><a class='link' href='http://www.redlay.com/profile.php?user_id="+adds[x]+"'><span class='user_name title_color' onmouseover=name_over(this); onmouseout=name_out(this); >"+add_names[x]+"</span></a></div>";
                        var profile_picture="<a class='link' href='http://www.redlay.com/profile.php?user_id="+adds[x]+"'><img class='profile_picture profile_picture_status' id='add_profile_picture_"+x+"' src='"+add_profile_pictures[x]+"'/></a>";
                        var add_date="<p style='margin: 0px;'><span class='title_color'>Added: </span><span class='text_color'>"+add_dates[x]+"</span></p>";


                        var body=get_post_format(profile_picture, name, add_date, '', '', '', '', 'add_'+x, badges[x]);
                        $('#all_adds').html(body+$('#all_adds').html());
                        
                        $('#all_adds_title').html("");
                        $('#add_profile_picture_'+x).attr({'onmouseover': "display_title(this, '"+add_num_adds[x]+" adds');", 'onmouseout': "hide_title(this);"})
                     }
                     
                     $('#all_adds_title').html(add_title+"[ "+num_adds+" ]");
                  }
                  else
                     $('#all_adds').html("<p class='text_color'>This user doesn't have any adds</p>");
                  
                  change_color();
               }, "json");
            }
            $(document).ready(function()
            {
               display_all_adds();
                change_color();
                $('#menu').hide();
                $('#footer').css('width', '910px');
                <?php
                  $path=get_user_background_pic($ID);
                  if(file_exists_server($path))
                    echo "$('body').css({'background-image': 'url(\'$path\')', 'background-position' :'center 50px'});";
                  else
                    echo "$('body').css({'background-image': 'url(\'".get_default_background_pic()."\')', 'background-position' :'center 50px'});";

                  include('required_jquery.php');
                ?>
            });
        </script>
       <script type="text/javascript">
          <?php include('required_google_analytics.js'); ?>
      </script>
    </head>
    <body>
       <?php include('required_html.php'); ?>
       <div id="top">
         <?php if(isset($_SESSION['id'])) include('top.php'); else include('top_page.php'); ?>
      </div>
       
        <div id="main">
            <div id="all_adds_box" class="content box">
                <p id="all_adds_title" class="title title_color"></p>
                <div id="all_adds">
                   
                   
                </div>
            </div>
           <?php include('footer.php'); ?>
        </div>
    </body>
</html>