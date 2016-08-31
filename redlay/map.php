<?php
@include('init.php');
include('universal_functions.php');
$allowed="users";
include('security_checks.php');
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Map</title>
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
                $('.box').css('border', '5px solid <?php echo $color; ?>');
                $('.preview_box').css({"border": "1px solid <?php echo $color; ?>", "background-color": "<?php echo $box_background_color; ?>"});
                $('.box').css({'background-color': '<?php echo $box_background_color; ?>'});
                $('.preivew_profile_friend_profile_picture').css('background-color', '<?php echo $text_color; ?>');
                $('.preview_information_text').css('color', '<?php echo $text_color; ?>');
                $('.preview_photo_table_unit').css('background-color', '<?php echo $text_color; ?>');
                $('.preview_profile_post_picture').css('background-color', '<?php echo $text_color; ?>');
                $('.preview_profile_post_body, #company_footer').css('color', '<?php echo $text_color; ?>');
                $('.map_item_title').css('color', '<?php echo $color; ?>');
                $('.map_item').css('background-color', '<?php echo $box_background_color; ?>');
            }

            
            function display_grid_slots()
            {
                //clears contents for reload or startup
                $('#map_slots').html('');

                //displays slots of map is grid
                var index=0;
                $('#map_slots').html("<table id='map_table'></table>");
                for(var x = 0; x < 3; x++)
                {
                    $('#map_table').html($('#map_table').html()+"<tr class='map_slot_row' id='map_slot_row_"+x+"'></tr>");
                    for(var y = 0; y < 6; y++)
                    {
                        $('#map_slot_row_'+x).html($('#map_slot_row_'+x).html()+"<td id='map_item_slot_"+index+"' class='map_item_slot' ></td>");
                        index++;
                    }
                }
                for(var x = 0; x < 18; x++)
                {
                    $('#map_item_slot_'+x).data('slot_number', x);
                }
            }

            //displays item information when clicked on
            function display_new_map_interface()
            {
                $.post('map_query.php',
                {
                    num: 5,
                    type:'user'
//                    type: $('#add_map_type').val()
                }, function(output)
                {
                    //clears old list
                    $('#add_map_list').html('');

                    //displays list of friends or pages so user can create new map_item
                    var list=output.friends;
                    var list_names=output.friend_names;
                    for(var x = 0; x < list.length; x++)
                        $('#add_map_list').html($('#add_map_list').html()+"<option value='"+list[x]+"'>"+list_names[x]+"</option>");

                    display_temp_map();
                }, "json");
            }
            function display_temp_map()
            {
                $('#add_map_body').html("<div id='new_map_item' class='map_item map_preview_item'><img class='map_item_image' id='add_map_picture' src='' /></div>");

                $('#add_map_picture').attr("src", "https://s3.amazonaws.com/redlay.users/users/"+$('#add_map_list').val()+"/thumbs/0.jpg");
                    
                    
                $('#new_map_item').data({'data': $('#add_map_list').val(), 'type': 'added', 'added_type': 'user'});

                
                $('#new_map_item').draggable({
                    containment: '#map_content',
                    cursor: 'pointer',
                    revert:true,
                    drag: function(event, ui){$(this).css({'z-index': 999, 'border': '1px solid black'});},
                    stop: function(event, ui){$(this).css({'z-index': 1, 'border': ''})}
                });
            }
            function display_website_map()
            {
                $.post('get_website_previews.php',
                {
                    website:$('#website_input').val()
                }, function(output)
                {
                    var previews=output.previews;
                    var website=output.website;
                    var heights=output.heights;
                    var widths=output.widths;
                    
                    if(previews[0]!='')
                    {
                        $('#website_map_body').html("<div id='new_website_map_item' class='map_item map_preview_item'></div>");
                        
                        //puts in all previews at once
                        for(var x = 0; x < previews.length; x++)
                        {
                            $('#new_website_map_item').html($('#new_website_map_item').html()+"<img class='add_website_map_preview' id='map_website_preview_"+x+"' src='"+previews[x]+"' />");
                            
                            if(heights[x]>widths[x])
                                $('#map_website_preview_'+x).css('width', '100px');
                            else
                                $('#map_website_preview_'+x).css('height', '100px');
                        }
                        
                        //attaches data
                        for(var x = 0;x < previews.length; x++)
                            $('#map_website_preview_'+x).data({'preview': previews[x]});

                        var data=new Array();
                        data[0]=website;
                        data[1]=previews[0];

                        //put in arrow functions with passing next and previous previews as parameter
                        $('#new_website_map_item').data({'data': data, 'type': 'website', 'added_type': 'none'});


                        $('#right_preview_arrow').attr('onClick', "change_map_preview(1);");
                        change_map_preview(0);
                    }
                    else
                    {
                        var data=new Array();
                        data[0]="http://www.redlay.com";
                        data[1]="http://www.redlay.com/favicon.ico";
                    }    


                    $('#new_website_map_item').draggable({
                        containment: '#map_content',
                        cursor: 'pointer',
                        revert:true,
                        drag: function(event, ui){$(this).css({'z-index': 999, 'border': '1px solid black'});},
                        stop: function(event, ui){$(this).css({'z-index': 1, 'border': ''})}
                    });
                    
                }, "json");
            }
            function change_map_preview(index)
            {
                if(index>0)
                    $('#map_website_preview_'+(index-1)).hide();
                if($('#map_website_preview_'+(index+1)).length)
                    $('#map_website_preview_'+(index+1)).hide();
                
                $('#map_website_preview_'+index).show();

                var data=$('#new_website_map_item').data('data');
                data[1]=$('#map_website_preview_'+index).data('preview');

                //put in arrow functions with passing next and previous previews as parameter
                $('#new_website_map_item').data({'data': data, 'type': 'website', 'added_type': 'none'});
                
                
                if(index>0)
                    $('#left_preview_arrow').attr('onClick', "change_map_preview("+(index-1)+");");
                if($('#map_website_preview_'+(index+1)).length)
                    $('#right_preview_arrow').attr('onClick', "change_map_preview("+(index+1)+");");
            }










            ////////////////////GRID////////////////////////////

            //displays the default map items to modify
            function display_grid()
            {
                $.post('map_query.php',
                {
                    num: 1
                }, function(output)
                {
                    var default_items=output.default_items;
                    var default_position_grid=output.default_position_grid;
                    var item_numbers=output.item_numbers

                    for(var x =0; x < default_items.length; x++)
                    {
                        $('#map_item_slot_'+default_position_grid[x]).html("<div class='map_item' id='map_grid_item_"+default_position_grid[x]+"'></div>");
//                        $('#map_grid_item_'+default_position_grid[x]).html("<div id='map_item_hidden_"+default_position_grid[x]+"' class='map_hidden_layer' ></div>");
                        $('#map_grid_item_'+default_position_grid[x]).html($('#map_grid_item_'+default_position_grid[x]).html()+"<img src='http://pics.redlay.com/pictures/"+default_items[x]+".png' id='map_item_image_"+default_position_grid[x]+"' style='width:100px;' />");

                        $('#map_grid_item_'+default_position_grid[x]).attr({'onClick': 'remove_added_options();', 'onmouseover': "display_title(this, '"+default_items[x]+"');", 'onmouseout': "hide_title(this);"});
                        make_item_draggable(default_position_grid[x], item_numbers[x], 'default', 'N/A');
                    }
                    display_added_grid();

                    change_color();
                }, "json");
            }

            

            //displays the user_added map items
            function display_added_grid()
            {
                $.post('map_query.php',
                {
                    num: 3
                }, function(output)
                {
                    var added_items=output.added_items;
                    var added_position_grid=output.added_item_positions;
                    var types=output.types;
                    var profile_pictures=output.profile_pictures;
                    var added_item_names=output.names;

                    for(var x =0; x < added_items.length; x++)
                    {
                        
                        $('#map_item_slot_'+added_position_grid[x]).html("<div class='map_item ' id='map_grid_item_"+added_position_grid[x]+"' ></div>");
//                        $('#map_grid_item_'+added_position_grid[x]).html("<div id='map_item_hidden_"+added_position_grid[x]+"' class='map_hidden_layer' ></div>");
                        $('#map_grid_item_'+added_position_grid[x]).html($('#map_grid_item_'+added_position_grid[x]).html()+"<img src='"+profile_pictures[x]+"' id='map_item_image_"+added_position_grid[x]+"' class='map_item_image' />");

                        if(types[x]!='')
                            make_item_draggable(added_position_grid[x], added_items[x], 'added', types[x]);
                        else
                        {
                            var data=new Array();
                            data[0]=added_item_names[x];
                            data[1]=profile_pictures[x];
                            
                            make_item_draggable(added_position_grid[x], data, 'website', 'none');
                        }

                        $('#map_grid_item_'+added_position_grid[x]).attr({'onClick': "display_added_options("+x+")", 'onmouseover': "display_title(this, '"+added_item_names[x]+"');", 'onmouseout': "hide_title(this);"});
                    }
                    

                    change_color();
                }, "json");
            }
            

            //id is the index of the map_item in database
            //map_index is the slot number the map_item is located in
            //type is either default {profile, home, etc}, or added {pages, profiles, etc}
            //added_type is either user or page
            function make_item_draggable(map_element_id, map_index, type, added_type)
            {
                //in added methods, map_index is the id of the user or page of the item instead of the index
                    //in default methods, map_index is the actual index


                    $('#map_grid_item_'+map_element_id).data({'data': map_index, 'type': type, 'added_type': added_type});


                    $('#map_grid_item_'+map_element_id).draggable({
                        containment: '#map_table',
                        cursor: 'pointer',
                        revert:true,
                        drag: function(event, ui){$(this).css({'z-index': 999, 'border': '1px solid black'});},
                        stop: function(event, ui){$(this).css({'z-index': 1, 'border': ''})}
                    });
            }

            function make_maps_droppable()
            {
                //loops through each grid map_slot
                for(var x =0; x < 18; x++)
                {

                    //makes the ones with no current map_item in them the ones that can receive new ones
                    if($('#map_item_slot_'+x).html()=='')
                    {
                        
                        //when item is dropped in, method map_item_stop() is called
                        $('#map_item_slot_'+x).droppable({
                            drop: function(event, ui)
                            {
                                //tells the map_item not to go back to original spot
                                ui.draggable.draggable('option', 'revert', false );

                                //gets the index of the map_item
                                var data=ui.draggable.data('data');

                                //either default or added
                                var map_type=ui.draggable.data('type');

                                //gets index of map_slot
                                var temp=$(this).attr('id');
                                var slot_number=temp.substring(temp.length-2, temp.length);
                                if(slot_number.substring(0, 1)=="_")
                                    slot_number=slot_number.substring(1, 2);

                                var added_type=ui.draggable.data('added_type');

                                //alert(data+' | '+map_type+' | '+slot_number+' | '+added_type);

                                $.post('map_set_item_location.php',
                                {
                                    data: data,
                                    slot_number: slot_number,
                                    type: map_type,
                                    added_type: added_type
                                }, function(output)
                                {
                                   if(output!='success')
                                      display_error(output, 'bad_errors');
                                   
                                    display_stuff();
                                });
                            }
                        });
                    }
                }
            }
            
            
            function display_added_options(added_item_index)
            {
                $('#map_menu').html("<input class='red_button' type='button' value='Delete' onClick='delete_added_item("+added_item_index+")'/>");
            }
            function remove_added_options()
            {
                $('#map_menu').html('');
            }

            function delete_added_item(added_item_index)
            {
                $.post('map_query.php',
                {
                    num:10,
                    added_item_index: added_item_index
                }, function(output)
                {
                    display_stuff();
                });
            }

            function display_stuff()
            {
                remove_added_options();
                display_grid_slots();
                display_grid();
                display_added_grid();
                make_maps_droppable();
                display_new_map_interface();
            }

            $(document).ready(function()
            {
                display_stuff();
                initialize_website_input();
                $('#menu').hide();
                $('#footer').css('width', '910px');

                <?php include('required_jquery.php'); ?>
            });
        </script>
    </head>
    <body>
        <?php include('required_html.php'); ?>
        <div id="top">
            <?php include('top.php'); ?>
        </div>
        <div id="main">
            <?php include('required_side_html.php'); ?>
            <div id="map_content" class="box">


                <div id="map_menu" ></div>
                <div id="map">
                    <div id="map_slots">
                        
                    </div>
                    <div id="map_body">
                        <div id="map_defaults">

                        </div>
                        <div id="map_adds">

                        </div>
                    </div>
                </div>



                <div id="map_add_feature" class="map">
                    <div id="add_map_input">
                        <table>
                            <tbody>
                                <tr>
                                    <td>
                                        
                                        <table>
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <select id="add_map_list" onChange="display_temp_map();">

                                                        </select>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div id="add_map_body" >
                                                            
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        
                                        
                                    </td>
                                    <td>
                                        <p class="title_color">Drag adds onto the map!</p>
                                    </td>
<!--                                    <td>
                                       <p class="text"> or add a website</p> 
                                    </td>
                                    <td>
                                        
                                        <table>
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <input class="input_box" type="text" placeholder="EX: http://www.redlay.com" id="website_input" onFocus="input_in(this);" onBlur="input_out(this);"/>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div id="website_map_body">
                                                            
                                                        </div>
                                                    </td>
                                                    <td rowspan="2" >
                                                        <table>
                                                            <tbody>
                                                                <tr>
                                                                    <td id="left_arrow_unit" class="preview_arrow_unit">
                                                                        <div class="preview_arrow_body" id="left_preview_arrow">
                                                                            <p class="preview_arrow_text"><</p>
                                                                        </div>
                                                                    </td>
                                                                    <td id="right_arrow_unit" class="preview_arrow_unit">
                                                                        <div class="preview_arrow_body" id="right_preview_arrow">
                                                                            <p class="preview_arrow_text">></p>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        
                                    </td>-->
                                </tr>
                            </tbody>
                        </table>
                        
                        
                        
                        
                        
<!--                        <select id="add_map_type" onChange="display_new_map_interface();">
                            <option value="user">Adds</option>
                            <option value="page">Pages</option>
                        </select>-->
                    </div>
                </div>
            </div>
            <?php include('footer.php'); ?>
        </div>
    </body>
    <script type="text/javascript">
        function initialize_website_input()
        {
            $('#website_input').keydown(function(e)
            {
                var key = (e.keyCode ? e.keyCode : e.which);
                if(key == '13')
                    display_website_map(0);
            });
        }
    </script>
</html>