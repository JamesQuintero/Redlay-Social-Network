<div id="top">
    <div class='header_background'></div>
<!--    <a href="http://m.redlay.com" id="icon_link"><p id="icon" class="text">redlay</p></a>-->
    <table id="alerts">
        <tbody>
            <tr>
                <td style="text-align:center;height:40px;">
                    <p id="add_request_alert_numbers" class="alert_numbers" <?php if(has_friend_request_alerts()!='true') echo "style='display:none;' ";?>><?php if(has_friend_request_alerts()=='true') echo get_friend_request_alerts(); ?></p>
                </td>
                <td style="text-align:center;">
                    <p id="messages_alert_numbers" class="alert_numbers" <?php if(has_messages_alerts()!='true')echo "style='display:none;' "; ?>><?php if(has_messages_alerts()=='true') echo get_messages_alerts(); ?></p>
                </td>
                <td style="text-align:center;">
                    <p id="alert_alert_numbers" class="alert_numbers" <?php if(has_alert_alerts()!='true') echo "style='display:none;' "; ?>><?php if(has_alert_alerts()=='true')echo get_alert_alerts(); ?></p>
                </td>
            </tr>
            <tr>
                <td style="text-align:center;">
                    <img id="add_request_alert" class="alert" src="./pictures/add_request.png" alt="Photo unavailable" onClick="window.location.replace('http://m.redlay.com/add_requests.php')" />
                </td>
                <td style="text-align:center;">
                    <img id="messages_alert" class="alert" src="./pictures/messages_alert.png" onClick="window.location.replace('http://m.redlay.com/messages.php')" alt="Photo unavailable" />
                </td>
                <td style="text-align:center;">
                    <img id="alert_alert" class="alert" src="./pictures/alert.png" alt="Photo unavailable" onClick="window.location.replace('http://m.redlay.com/alert_page.php');" />
                </td>
            </tr>
        </tbody>
    </table>
       
       
       
    <img src="./pictures/map_button.png" id="menu_button" onClick="show_menu();"/>
    <div id="top_map">
        <div id="map_top_slots">

        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function()
    {
        $('#top_map').hide();
        display_top_grid_slots();
        display_top_grid();
        <?php if($_SERVER['REQUEST_URI']!='/registration_intro.php') echo "alert_time_events();"; ?>
    });
    function alert_time_events()
    {
        online();
        check_alerts();
        setTimeout(function()
        {
            check_alerts();
        }, 5000);
    }
    function check_alerts()
    {
        $.post('main_access.php',
        {
            access:31
        }, function(output)
        {
            var new_friends=output.new_friends;
            var new_messages=output.new_messages;
            var new_alerts=output.new_alerts;
            
            $('#add_request_alert_numbers').html(new_friends);
            if(new_friends!=0)
                $('#add_request_alert_numbers').show();
            else
                $('#add_request_alert_numbers').hide();
            
            $('#messages_alert_numbers').html(new_messages);
            if(new_messages!=0)
                $('#messages_alert_numbers').show();
            else
                $('#messages_alert_numbers').hide();
            
            $('#alert_alert_numbers').html(new_alerts);
            if(new_alerts!=0)
                $('#alert_alert_numbers').show();
            else
                $('#alert_alert_numbers').hide();
        }, "json");
    }
    function show_menu()
    {
        if($('#top_map').css('display')=='none')
        {
            $('#top_map').show().stop().animate({
                height: '510px'
            }, 350, function()
            {

            });
        }
        else
        {
            $('#top_map').stop().animate({
                height: '0px'
            }, 350, function()
            {
                $('#top_map').hide();
            });
        }
    }

    //displays the slots for grid
    function display_top_grid_slots()
    {
        //clears contents for reload or startup
        $('#map_top_slots').html('');

        //displays slots of map is grid
        var index=0;
        var index2=0;
        $('#map_top_slots').html("<table id='map_top_table'><tr class='map_top_slot_row' id='map_top_slot_row_0'></tr><tr class='map_top_slot_row' id='map_top_slot_row_1'></tr><tr class='map_top_slot_row' id='map_top_slot_row_2'></tr></table>");
        for(var x = 0; x < 3; x++)
        {
            for(var y = 0; y < 6; y++)
            {
                $('#map_top_slot_row_'+x).html($('#map_top_slot_row_'+x).html()+"<td id='map_top_item_slot_"+index+"' class='map_top_item_slot' ></td>");
                index++;
            }
        }
    }
    //displays regular default map_items without modification
    function display_top_grid()
    {
        $.post('map_query.php',
        {
            num: 2
        }, function(output)
        {
            var default_items=output.default_items;
            var default_position_grid=output.default_position_grid;
            var links=output.links;

            for(var x =0; x < default_items.length; x++)
            {
                $('#map_top_item_slot_'+default_position_grid[x]).html("<div class='map_top_item' id='map_top_grid_item_"+default_position_grid[x]+"' onClick=window.location.replace('http://m.redlay.com/"+links[x]+"'); ></div>");

                $('#map_top_grid_item_'+default_position_grid[x]).html("<div id='map_top_item_hidden_"+default_position_grid[x]+"' class='map_top_hidden_layer' ></div>");
                $('#map_top_grid_item_'+default_position_grid[x]).html($('#map_top_grid_item_'+default_position_grid[x]).html()+"<img src='./pictures/"+default_items[x]+".png' id='map_top_item_image_"+default_position_grid[x]+"' class='map_item_image' />");

                //adds the item's title
                $('#map_top_grid_item_'+default_position_grid[x]).html($('#map_top_grid_item_'+default_position_grid[x]).html()+"<br class='map_top_break' />");
            }
            display_added_top_grid();
            change_color();
        }, "json");
    }
    //displays the user_added map items
    function display_added_top_grid()
    {
        $.post('map_query.php',
        {
            num: 3
        }, function(output)
        {
            var added_items=output.added_items;
            var added_position_grid=output.added_item_positions;
            var links=output.links;
            var types=output.types;
            var profile_pictures=output.profile_pictures;
            var added_item_names=output.names;

            for(var x =0; x < added_items.length; x++)
            {
                $('#map_top_item_slot_'+added_position_grid[x]).html("<div class='map_top_item' id='map_top_grid_item_"+added_position_grid[x]+"' onClick=window.location.replace('http://m.redlay.com/"+links[x]+"');></div>");

                $('#map_top_grid_item_'+added_position_grid[x]).html("<div id='map_top_item_hidden_"+added_position_grid[x]+"' class='map_top_hidden_layer' ></div>");
                $('#map_top_grid_item_'+added_position_grid[x]).html($('#map_top_grid_item_'+added_position_grid[x]).html()+"<img src='"+profile_pictures[x]+"' id='map_top_item_image_"+added_position_grid[x]+"' class='map_top_item_image' />");

                //adds the item's title
                $('#map_top_grid_item_'+added_position_grid[x]).html($('#map_top_grid_item_'+added_position_grid[x]).html()+"<br class='map_top_break' />");
            }
            change_color();
        }, "json");
    }
</script>