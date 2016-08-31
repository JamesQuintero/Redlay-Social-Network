<?php

header("Location: http://www.redlay.com");
exit();


//DEPRECATED FEATURE



@include('init.php');
include('universal_functions.php');
$allowed="all";
include('security_checks.php');

$ID=(int)($_GET['user_id']);
$type=clean_string($_GET['type']);
$photo_id=clean_string($_GET['photo_id']);

//redirect if any info is wrong
if(!is_id($ID)||!user_id_exists($ID)||user_id_terminated($ID)||($type!="page"&&$type!="user"))
{
    header("Location: http://www.redlay.com");
    exit();
}

//gets photo index
$query=mysql_query("SELECT pictures FROM pictures WHERE user_id=$ID LIMIT 1");
if($query&&mysql_num_rows($query)==1)
{
   $array=mysql_fetch_row($query);
   $pictures=explode('|^|*|', $array[0]);
   
   $index=-1;
   for($x = 0; $x < sizeof($pictures); $x++)
   {     
      if($photo_id==$pictures[$x])
         $index=$x;
   }
   
   //if picture doesn't exist
   if($index==-1)
   {
      header("Location: http://www.redlay.com");
      exit();
   }
}

//checks to see if current user is allowed to view ANY photos
if(isset($_SESSION['id']))
{
    if($ID!=$_SESSION['id']&&user_is_friends($ID, $_SESSION['id'])=="false")
    {
        $privacy=get_user_privacy_settings($ID);
        if($privacy[1][4]=='no')
        {
            header("Location: http://www.redlay.com");
            exit();
        }
        else
        {
            $query=mysql_query("SELECT image_audiences FROM pictures WHERE user_id=$ID LIMIT 1");
            if($query&&mysql_num_rows($query)==1)
            {
                $array=mysql_fetch_row($query);
                $image_audiences=explode('|^|*|', $array[0]);

                $bool=false;
                for($x = 0; $x < sizeof($image_audiences); $x++)
                {
                    $image_audiences[$index]=explode('|%|&|', $image_audiences[$index]);
                    if(!in_array('Everyone', $image_audiences[$index]))
                    {
                        $groups=get_audience_current_user($ID);
                        for($x = 0; $x < sizeof($groups); $x++)
                        {
                            if(in_array($groups[$x], $image_audiences[$index]))
                                $bool=true;
                        }
                        $bool=false;;
                    }
                    else
                        $bool=true;
                }

                if($bool==false)
                {
                    header("Location: http://www.redlay.com");
                    exit();
                }
            }
        }
    }
}
else
{
    $privacy=get_user_privacy_settings($ID);
    if($privacy[1][4]=='no')
    {
        header("Location: http://www.redlay.com");
        exit();
    }
}
?>
<html>
    <head>
        <title>Photo Slide Show</title>
        <?php include('required_header.php'); ?>
        <script type="text/javascript">
            function change_color()
            {
                <?php
                        if(isset($_SESSION['id']))
                        {
                            $colors=get_user_display_colors($_SESSION['id']);
                            $color=$colors[0];
                            $box_background_color=$colors[1];
                            $text_color=$colors[2];
                        }
                        else
                        {
                            $color="rgb(220,20,0)";
                            $box_background_color="white";
                            $text_color="rgb(30,30,30)";
                        }
                ?>
                $('.box').css('border', '5px solid <?php echo $color; ?>');
                $('.box').css({'background-color': '<?php echo $box_background_color; ?>'});
                
                
                $('.title_color').css('color', '<?php echo $color; ?>');
                $('.text_color').css('color', '<?php echo $text_color; ?>');
            }
            
            
            $(window).ready(function()
            {
                $.post('user_photo_query.php',
                {
                    num:1,
                    user_id: <?php echo $ID; ?>,
                    type: '<?php echo $type; ?>'
                }, function(output)
                {
                    var images=output.images;
                    var image_ids=output.image_ids;
                    var image_widths=output.image_widths;
                    var image_heights=output.image_heights;
                    
                    var html="";
                    for(var x = 0; x < images.length; x++)
                    {
//                       var picture_slide_show_body="<div id='picture_slide_show_body_"+x+"' class='slide_show_picture_body'></div>";
//                        $('#picture_slide_show_body').html(picture_slide_show_body+$('#picture_slide_show_body').html());
//                        
                        html="<div id='picture_slide_show_body_"+x+"' class='slide_show_picture_body'><a href='http://www.redlay.com/view_photo.php?user_id=<?php echo $ID; ?>&&picture_id="+image_ids[x]+"&&type=<?php echo $type; ?>'><img src='"+images[x]+"' class='slide_show_photo' id='image_"+x+"'/></a></div>"+html;
                    }
                    $('#picture_slide_show_body').html(html);
                    //displays the specified image without animation
                    var specific_image="<a href='http://www.redlay.com/view_photo.php?user_id=<?php echo $ID; ?>&&picture_id="+image_ids[<?php echo $index; ?>]+"&&type=<?php echo $type; ?>'><img src='"+images[<?php echo $index; ?>]+"' class='slide_show_photo' id='image_<?php echo $index; ?>'/></a>";
                    $('#picture_slide_show_body_<?php echo $index; ?>').html(specific_image);
                    for(var x = 0; x < images.length; x++)
                    {
                        //0 is width and 1 is height
                        $('#image_'+x).css({'max-height': '510px', 'max-width': '910px'});
                        $('#picture_slide_show_body_'+x).css({'left' : '910px', 'z-index': '999', "width": image_widths[x], "height": image_heights[x]});
                    }



                    //setTimeout is to give the picture time to load so the width will be received properly
                    //setTimeout(function()
                    //{
                        //moves the picture to the proper location
                        $('#image_<?php echo $index; ?>').css({'max-width': '910px', 'max-height': '510px'});
                        var left_length=((910/2)-(image_widths[<?php echo $index; ?>]/2))+"px";
                        //setTimeout(function()
                        //{
                            $('#picture_slide_show_body_<?php echo $index; ?>').css({'left': left_length, "border": "1px solid black"});
                        //}, 100);
                    //}, 500);
//                    
                    change_color();
                    
                    $('#picture_slide_show_gif').hide();

                    //starts at end
                    if(<?php echo $index ?>==images.length-1)
                        photo_interval(0, images);
                       
                    //starts at next image
                    else
                        photo_interval(<?php echo $index+1; ?>, images, image_ids);
                }, "json");
            });
            function photo_interval(num, images, image_ids)
            {    
                setTimeout(function()
                {
                    //changes the photo
                    change_photo(num, images, image_ids);

                    //if at the last photo, go back to first
                    if(num==images.length-1)
                        num=0;
                    else
                        num++;

                    //repeat cycle with recursion after 5 seconds
                    photo_interval(num, images, image_ids);
                }, 5000);
            }
            function change_photo(num, images, image_ids)
            {
                //flickering because picture is animted to the left is caused by adding the picture to the html of the picture_body element
                //by getting the current pictures, and replacing them with the new one attached
                if(num<images.length)
                {
                    //makes every pictue, besides the new one, have a z-index of 1 so it can be overlapped if necessary
                    for(var x = 0; x < images.length; x++)
                    {
                        if(x!=num)
                            $('#picture_slide_show_body_'+x).css({'z-index': '1'});
                    }
                    
                    //makes the new picture's z-index 999 so it can overlap if necessary
                    $('#picture_slide_show_body_'+num).css('z-index', '999');
                    
                    //starts after 1/10 of a second to give the z-index time
                    setTimeout(function()
                    {
                        //calculates where new photo will end up
                        //animates new photo to move to that location
                        var left_length=(910/2)-($('#image_'+num).width()/2)+"px";
                        $('#picture_slide_show_body_'+num).animate({
                            left: left_length
                        }, 700, function()
                        {
                            
                        });

                        //gets the old photo
                        if(num==0)
                            var temp=images.length-1;
                        else
                            var temp=num-1;
                        
                        
                        //gets the coordinate of where the old photo will move
                        var temp_distance="-"+((910/2)+($('#image_'+temp).width()/2)+"px");

                        //moves old photo out of the way then resets it
                        $('#picture_slide_show_body_'+temp).animate({
                            left: temp_distance
                        }, 700, function()
                        {
                            setTimeout(function()
                            {
                                $('#picture_slide_show_body_'+temp).css({'left': '910px', 'z-index': '1'});
                            }, 100);
                        });

                    }, 100);
                }
            }
            function add_next_picture(current_photo_id)
            {
                $.post('user_photo_query.php',
                {
                    num:2,
                    photo_id: current_photo_id,
                    user_id: <?php echo $ID; ?>,
                    type: '<?php echo $type; ?>'
                }, function(output)
                {
                    var images=output.images;
                    var image_ids=output.image_ids;
                    var image_widths=output.image_widths;
                    var image_heights=output.image_heights;
                    
                }, "json");
            }
            $(document).ready(function()
            {
                $('#footer').css('width', '910px');
                <?php include('required_jquery.php'); ?>
                change_color();
            });
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
            else 
                include('index_top.php');
            ?>
        <div id="main">
            <?php include('required_side_html.php'); ?>
            <div id="photo_slide_show_content" class="content box" >
                <img class="load_gif" src="http://pics.redlay.com/pictures/load.gif" id="picture_slide_show_gif"/>
                <div id="picture_slide_show_body">
                    
                </div>
                <div id="photo_info">

                </div>
            </div>
        </div>
        <?php include('footer.php'); ?>
    </body>
</html>