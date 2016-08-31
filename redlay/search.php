<?php
@include('init.php');
include('universal_functions.php');
$allowed="users";
include('security_checks.php');
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Search redlay</title>
        <?php include('required_header.php'); ?>
        <script type="text/javascript">
            function change_color()
            {
                <?php
                if(isset($_SESSION['page_id']))
                    $colors=get_page_display_colors($_SESSION['page_id']);
                else
                    $colors=get_user_display_colors($_SESSION['id']);
                
                $color=$colors[0];
                $box_background_color=$colors[1];
                $text_color=$colors[2];
                ?>
                $('.box').css('border', '5px solid <?php echo $color; ?>');
                $('.box').css('background-color', '<?php echo $box_background_color; ?>');

                $('#search_results, #case_sensitive, #search_description, #company_footer').css('color', '<?php echo $text_color; ?>');
                $('.search_title, .search_description_name').css('color', '<?php echo $color; ?>');
                
                $('.title_color').css('color', '<?php echo $color; ?>');
                $('.text_color').css('color', '<?php echo $text_color; ?>');
            }
            
            function add_friend(ID)
            {
               //gets the checked checkboxes and their values
               var audience_options_list=new Array();
               var num=0;
               var num2=0;
               while($('#add_groups_unit_body_checkbox_'+num2).length)
               {
                  if($('#add_groups_unit_body_checkbox_'+num2).data('checked')=='yes')
                  {
                        audience_options_list[num]=$('#add_groups_unit_body_checkbox_'+num2).data('group_name');
                        num++;
                  }
                  num2++;
               }
               
                $.post('add_friend.php',
                {
                   user_id: ID,
                   message: $('#add_user_message_input').val(),
                   audience: audience_options_list
                }, function (output)
                {
                    if(output=="Add request sent!")
                    {
                        display_error(output, 'good_errors');
                        close_alert_box();
                        $('#options').html('').hide();
                    }
                    else
                        display_error(output, 'bad_errors');
                });
            }


            function search_database(num)
            {
                $('#search_load').show();
                //gets all the search paramters
                var parameters=new Array();
                if(num==1)
                {
                    parameters[0]=$('#menu_1_input').val();
                    parameters[1]=$('#menu_2_input').val();
                    parameters[2]=$('#menu_3_input').val();
                    parameters[3]=$('#menu_4_input').val();
                    parameters[4]=$('#menu_5_input').val();
                }
                else
                    parameters[0]=$('#menu_2_input').val();

                
                $.post('search_query.php',
                {
                    num: num,
                    parameters: parameters
                }, function(output)
                {
                    ///displays search results
                    var ids=output.ids;
                    var names=output.names;
                    var is_friends=output.is_friends;
                    var has_liked=output.has_liked;
                    var pending_friends=output.pending_friends;
                    var add_requests_sent=output.add_request_sent;
                    var descriptions=output.user_descriptions;
                    var num_adds=output.num_adds;
                    var num_likes=output.num_likes;
                    var page_descriptions=output.page_descriptions;
                    var profile_pictures=output.profile_pictures;
                    var badges=output.badges;

                    if(ids.length!=0)
                    {
                        for(var x = 0; x < ids.length; x++)
                        {
                            $('#search_table_body').html($('#search_table_body').html()+"<tr class='search_row'><td id='search_unit_"+x+"'></td><td id='search_unit_button_"+x+"'></td></tr>");

                            if(num==1)
                            {
                                var profile_picture="<a href='http://www.redlay.com/profile.php?user_id="+ids[x]+"'><img class='profile_picture profile_picture_status' src='"+profile_pictures[x]+"' /></a>";
                                var name="<div class='user_name_body'><a class='link' href='http://www.redlay.com/profile.php?user_id="+ids[x]+"'><span class='user_name title_color' onmouseover=name_over(this); onmouseout=name_out(this); >"+names[x]+"</span></a></div>";
                                var description="<p class='text_color'>"+descriptions[x]+"</p>";
                                var adds="<span class='text_color'>Adds: "+num_adds[x]+"</span>";
                                

                                var body=get_post_format(profile_picture, name, description+adds, '', '', '', '', "searh_result_"+x, badges[x])
                                $('#search_unit_'+x).html(body);
                                
                                if(ids[x]!=<?php echo $_SESSION['id']; ?>)
                                {
                                    if(pending_friends[x]=='true')
                                    {
                                        if(add_requests_sent[x]==ids[x])
                                        {
                                            $('#search_unit_button_'+x).html("<input type='button' class='green_button' value='Accept' id='accept_button_"+x+"' />");
                                            $('#accept_button_'+x).attr('onClick', "display_accept_menu("+ids[x]+", '"+names[x]+"')");
                                        }
                                        else
                                            $('#search_unit_button_'+x).html("<input type='button' class='red_button_disabled' value='Pending' />");
                                    }   
                                    else
                                        $('#search_unit_button_'+x).html("<input type='button' class='button red_button' value='Add' onClick='display_add_menu("+ids[x]+");'/>");
                                }
                            }
                            else
                            {
                                $('#search_description_unit_'+x).html("<div class='search_description_body'><a href='http://www.redlay.com/page.php?page_id="+ids[x]+"'  class='search_link'><p class='search_description_name' id='search_description_name_"+x+"'>"+names[x]+"</p></a><p class='search_description' id='search_description_"+x+"'>"+page_descriptions[x]+"</p><p class='search_num' id='search_num_"+x+"'>Likes: "+num_likes[x]+"</p></div>");
                                $('#search_description_name_'+x).attr({'onmouseover': "name_over(this);", 'onmouseout': "name_out(this);"});
                                $('#search_profile_picture_'+x).attr({'src': profile_pictures[x] });
                                $('#profile_picture_link_'+x).attr({'href': "http://www.redlay.com/page.php?page_id="+ids[x]});
                                
                                if(has_liked[x]=='false')
                                {
                                    $('#search_buttons_'+x).html("<input type='button' class='green_button' id='like_button_"+x+"' value='Like' onClick='like_page("+ids[x]+", "+x+");' />");
                                        $('#like_button_'+x).attr({'onmouseover': "{display_title(this, 'Like this page');}", 'onmouseout': "hide_title(this);"});
                                }
                            }
                        }
                        $('.profile_picture_status').css({'width': "100px", 'height': "100px", 'top': '0px'});
                    }
                    else
                        $('#search_results').html("<p class='text_color'>None found</p>");
                    $('#search_load').hide();
                    change_color();
                }, "json");
            }

            function method(num, page_type)
            {
                if(num==1)
                {
                    $('#search_items_unit').html("<table><tbody id='search_items_unit_table_body'><tr><td><input type='text' class='input_box' id='menu_1_input' placeholder='First Name' onFocus='input_in(this);' onBlur='input_out(this);' /></td><td><input type='text' class='input_box' id='menu_2_input' placeholder='Last Name' onFocus='input_in(this);' onBlur='input_out(this);' /></td></tr></tbody></table>");
                    $('#search_items_unit_table_body').html($('#search_items_unit_table_body').html()+"<tr><td><input type='text' class='input_box' id='menu_3_input' placeholder='High School' onFocus='input_in(this);' onBlur='input_out(this);' /></td></tr>");
                    $('#search_items_unit_table_body').html($('#search_items_unit_table_body').html()+"<tr><td><input type='text' class='input_box' id='menu_4_input' placeholder='College' onFocus='input_in(this);' onBlur='input_out(this);'/></td></tr>");
                    $('#search_items_unit_table_body').html($('#search_items_unit_table_body').html()+"<tr><td><input type='text' class='input_box' id='menu_5_input' placeholder='City' onFocus='input_in(this);' onBlur='input_out(this);'/></td></tr>");

                    $('#search_options_submit').attr('onClick', 'search_database(1);');
                }
                else if(num==2)
                {
                    if(page_type==0)
                    {
                        $('#search_items_unit').html("<select onChange='method(2, $(this).val());' id='page_type_options'></select>");
                        $('#page_type_options').html("<option value='0'>Page Type:</option>");
                        $('#page_type_options').html($('#page_type_options').html()+"<option value='1'>Company</option>");
                        $('#page_type_options').html($('#page_type_options').html()+"<option value='2'>Person</option>");
                        $('#page_type_options').html($('#page_type_options').html()+"<option value='3'>Other</option>");
                        $('#search_options_submit').attr('onClick', '');
                    }
                    else if(page_type==1)
                    {
                        $('#search_items_unit_2').html("<table><tbody id='search_items_unit_table_body_2'><tr><td><input type='text' class='input_box' id='menu_2_input' placeholder='Company Name'/></td></tr></tbody></table>");
                        
                        $('#search_options_submit').attr('onClick', 'search_database(2);');
                    }
                    else if(page_type==2)
                    {
                        $('#search_items_unit_2').html("<table><tbody id='search_items_unit_table_body_2'><tr><td><input type='text' class='input_box' id='menu_2_input' placeholder='First Name'/></td></tr></tbody></table>");
                        $('#search_items_unit_table_body_2').html($('#search_items_unit_table_body_2').html()+"<tr><td><input type='text' class='input_box' id='menu_3_input' placeholder='Last Name'/></td></tr>");
                        $('#search_options_submit').attr('onClick', 'search_database(3);');
                    }
                    else if(page_type==3)
                    {
                        $('#search_items_unit_2').html("<table><tbody id='search_items_unit_table_body_2'><tr><td><input type='text' class='input_box' id='menu_2_input' placeholder='Name'/></td></tr></tbody></table>");
                        $('#search_options_submit').attr('onClick', 'search_database(4);');
                    }
                }
                else if(num==0)
                {
                    $('#search_items_unit').html("");
                    $('#search_items_unit_2').html("");
                    $('#search_items_unit_3').html("");
                }
                change_color();
            }
        </script>
        <script type="text/javascript">
            $(document).ready(function()
            {
                $('#search_load').hide();
                method(1, 0);
                change_color();
                $('#menu').hide();
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
        <div id="top">
           <?php if(!isset($_SESSION['page_id']))include('top.php'); else include('top_page.php'); ?>
        </div>
        <div id="main">
            <?php include('required_side_html.php'); ?>
            <div id="search_content" class="content box">
                <div id="search_title">
                    <p class="search_title">Search Results:</p>
                </div>
                <p id="search_description">You do not have to fill out all of the fields.</p>
                <table id="search_options_menu">
                    <tbody id="search_options_menu_body">
                        <tr id="search_options_menu_row">
                            <td class="search_options_menu_unit" id="search_items_unit">
                                <table>
                                    <tbody id="search_items_unit_table_body">
                                        
                                    </tbody>
                                </table>
                            </td>
                            <td class="search_options_menu_unit" id="search_items_unit_2">
                                <table>
                                    <tbody id="search_items_unit_table_body_2">
                                        
                                    </tbody>
                                </table>
                            </td>
                            <td class="search_options_menu_unit" id="search_items_unit_3">
                                <table>
                                    <tbody id="search_items_unit_table_body_3">
                                        
                                    </tbody>
                                </table>
                            </td>
                            <td class="search_options_menu_unit" >
                                <input type="button" value="Search" id="search_options_submit" class="button red_button" onmouseover="display_title(this, 'Search using the selected criteria');" onmouseout="hide_title(this);" onClick="search_database(1);"/>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <hr/>
                <div id="search_load"><img class="load_gif" src='http://pics.redlay.com/pictures/load.gif'/></div>
                <div id="search_results">
                    <table>
                        <tbody id="search_table_body">
                             
                        </tbody>
                    </table>
                </div>
            </div>
            <?php include('footer.php'); ?>
        </div>
    </body>
</html>