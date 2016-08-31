<?php
@include('init.php');
include('../universal_functions.php');
$allowed="users";
include('security_checks.php');

//gets the number of add requests
$query=mysql_query("SELECT new_friend_alerts FROM alerts WHERE user_id=$_SESSION[id] LIMIT 1");
if($query&&mysql_num_rows($query)==1)
{
    $array=mysql_fetch_row($query);
    $num_add_requests=$array[0];
}
?>

<!DOCTYPE html>
<html>
    <head>
        <title><?php echo $num_add_requests; ?> new add <?php if($num_add_requests==1) echo "request"; else echo "requests"; ?></title>
        <?php include('required_header.php'); ?>
        <script type="text/javascript">
            function change_color()
            {
                <?php
                        $colors=get_user_display_colors($_SESSION['id']);
                        $color=$colors[0];
                        $box_background_color=$colors[1];
                        $text_color=$colors[2];
                ?>
                $('.box').css('border', '15px solid <?php echo $color; ?>');
                $('.box').css({'background-color': '<?php echo $box_background_color; ?>'});
                <?php echo "$('body').css('background-attachment', 'fixed');"; ?>
                $('.name, .alert_box_title').css('color', '<?php echo $color; ?>');
                $('.friend_message').css('color', '<?php echo $text_color; ?>');
                
            }
            
            function display_adds()
            {
                $.post('main_access.php',
                {
                    access:30
                }, function(output)
                {
                    var user_ids=output.other_user_ids;
                    var timestamps=output.timestamps;
                    var user_names=output.user_names;
                    var user_is_friends=output.user_is_friends;
                    var messages=output.messages;


                    if(user_ids.length>0)
                    {
                        for(var x = 0; x < user_ids.length; x++)
                        {
                            $('#add_request_table_body').html($('#add_request_table_body').html()+"<tr class='friend_request_alert_row' id='row_"+x+"'></tr>");
                            
                            
                            var name="<div class='user_name_body friend_user_name_body'><a style='text-decoration:none;' href='http://m.redlay.com/profile.php?user_id="+user_ids[x]+"'><span class='name' id='name_"+x+"' >"+user_names[x]+"</span></a></div>";
                            var profile_picture="<td class='profile_picture_unit'><a href='http://m.redlay.com/profile.php?user_id="+user_ids[x]+"'><img src='http://www.redlay.com/users/images/"+user_ids[x]+"/0.jpg' id='friend_profile_picture_"+x+"' class='friend_profile_pic profile_picture'/></a></td>";

                            var message="<span class='friend_message'>"+messages[x]+"</span>";
                            var buttons="<td class='buttons_unit'><input id='accept_button_"+x+"' class='accept_button green_button' value='Accept' type='button'/></td><td class='buttons_unit'><input type='button' class='decline_button red_button' id='decline_button_"+x+"' onClick='decline_request("+user_ids[x]+");' value='Decline'/></td></tr>";

                            $('#row_'+x).html(profile_picture+"<td class='user_name_unit'><div class='name_message_body' style='font-size:35px;'>"+name+message+"</div></td>"+buttons);
                            $('#accept_button_'+x).attr({'onClick': "display_add_box("+user_ids[x]+", '"+user_names[x]+"');"});
                        }
                    }
                    else
                        $('#add_request_content').html("<div id='friend_request_content_body'><p id='friend_request_alert_none' style='text-align:center;'>You have no add requests</p></div>");
                    $('#beginning_load_gif').hide();
                }, "json");
            }
            
            function display_add_box(ID, name)
            { 
                var title="Add "+name+" to any group";
                var body="<div id='groups_category_box'></div>";
                var extra_id="user_groups";
                var load_id="load_gif";
                var confirm="<input class='green_button'  type='button' value='Add' onClick='accept_request("+ID+");' />";
                display_alert(title, body, extra_id, load_id, confirm);
                
                display_groups('groups_category_box', 'add');
                $('#load_gif').hide();
                
                change_color();
            }
            function accept_request(ID)
            {
                //gets the checked checkboxes and their values
                var audience_options_list=new Array();
                var num=0;
                var num2=0;
                while($('#groups_category_box_checkbox_'+num2).length)
                {
                    if($('#groups_category_box_checkbox_'+num2).data('checked')=='yes')
                    {
                        audience_options_list[num]=$('#groups_category_box_checkbox_'+num2).data('group_name');
                        num++;
                    }
                    num2++;
                }


                $.post('main_access.php',
                {
                    access:32,
                    user_id: ID,
                    audience_options_list: audience_options_list
                }, function (output)
                {
                    if(output=="User added!")
                        window.location.replace(window.location);
                    else
                        $('#friend_request_alert_errors').html(output).addClass('bad_errors').show();
                });
            }
            function decline_request()
            {
                
            }
            
            
            
            
            function display_groups(item_id, type)
            {
                //displays appropriate HTML
                if(type!='add')
                    $('#'+item_id).html("<input type='button' class='gray_button' id='show_user_groups_button' value='Display to' /><div id='"+item_id+"_box_inside' class='select_body_options'></div>");
                else
                    $('#'+item_id).html("<input type='button' class='gray_button' id='show_user_groups_button' value='Add to' /><div id='"+item_id+"_box_inside' class='select_body_options'></div>");
                $('#'+item_id+'_box_inside').html("<table class='select_body_options_table' ><tbody id='"+item_id+"_body' class='select_body_options_table_body'></tbody></table>");
                $('#'+item_id+'_box_inside').hide();
                $('#show_user_groups_button').attr({'onClick': "toggle_group_display('"+item_id+"');"});

                //gets the groups
                $.post('main_access.php',
                {
                    access:33,
                    num:1,
                    type: type
                }, function(output)
                {
                   var groups=output.groups;
                   var type=output.type;

                    for(var x = 0; x < groups.length; x++)
                   {
                        $('#'+item_id+'_body').html($('#'+item_id+'_body').html()+"<tr class='select_body_options_row' id='"+item_id+"_audience_row_"+x+"'></tr>");
                        $('#'+item_id+'_audience_row_'+x).html("<td class='audience_selection_checkbox_unit' id='"+item_id+"_audience_selection_checkbox_"+x+"'></td><td class='select_body_option_unit' id='"+item_id+"_audience_selection_text_"+x+"'></td>");

                        if(item_id!='photo_audience_box')
                            $('#'+item_id+'_audience_selection_checkbox_'+x).html("<img class='checkbox' id='"+item_id+"_checkbox_"+x+"'/>");
                        else
                            $('#'+item_id+'_audience_selection_checkbox_'+x).html("<input type='checkbox' class='checkbox' id='"+item_id+"_checkbox_"+x+"' name='groups[]' value='"+groups[x]+"'/>");
                            $('#'+item_id+'_audience_selection_text_'+x).html("<p class='select_body_option_text'>"+groups[x]+"</p>");
                   }

                   for(x =0; x < groups.length; x++)
                   {
                      $('#'+item_id+'_checkbox_'+x).data("group_name", groups[x]);


                      if(item_id!='photo_audience_box')
                      {
                          $('#'+item_id+'_checkbox_'+x).attr('onClick', "toggle_checkbox('#"+item_id+"_checkbox_"+x+"');");
                          if(groups[x]=='Everyone')
                              $('#'+item_id+'_checkbox_'+x).data('checked', 'yes').attr('src', './pictures/gray_checkbox_checked.png');
                          else
                              $('#'+item_id+'_checkbox_'+x).data('checked', 'no').attr('src', './pictures/gray_checkbox.png');
                      }
                   }

                }, "json");
            }
            
            
            
            
            

            $(document).ready(function()
            {
                $('#menu').hide();
                display_adds();
                <?php include('required_jquery.php'); ?>
                <?php echo "$('body').css({'background-image': 'url(\'./pictures/default_background.png\')', 'background-position' :'center 50px'});"; ?>
            });
        </script>
    </head>
    <body>
        <?php include('required_html.php'); ?>
        <?php include('top.php'); ?>
        
        <div id="main">
            <div id="add_request_content" class="box">
                <table id="add_request_table" style="padding:20px;">
                    <tbody id="add_request_table_body">
                        
                    </tbody>
                </table>
            </div>
            <?php include('footer.php'); ?>
        </div>
    </body>
</html>