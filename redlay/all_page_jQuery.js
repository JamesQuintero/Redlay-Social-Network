function login()
{
    $.post('login.php',
    {
        email: $('#login_email_text_box').val(),
        password: $('#login_password_text_box').val()
    },
    function (output)
    {
        if(output=='')
            window.location.replace(window.location);
        else
           display_error(output, 'bad_errors');
    });
}
function hide_text(ID)
{
    $(ID).css('text','');
}
function show_text(ID)
{
    $(ID).css().text("What's up?");
}
function get_timezone()
{
    var date = new Date()
    var timezone = date.getTimezoneOffset();
    return timezone;
}
function online()
{
    $.post('online.php',
    {
        num:1
    }, function(output)
    {});
}
function offline()
{
    $.ajax({
        type:'POST',
        url: 'online.php',
        data:{
            'num':3
        },
        async:false
    });
}
function is_same(array, array2)
{
    var same=true;
   
    if(array.length==array2.length)
    {
        for(var x = 0; x < array.length; x++)
        {
            if(array[x]!=array2[x])
                same=false;
        }    
    }
    else
        same=false;
    
    return same;
}
function display_alert(title, body, extra_id, load_id, confirm)
{
    display_dim();
    $('.alert_box_inside').html("<table class='alert_box_table' ><tbody><tr class='alert_box_row' id='alert_box_row_1'></tr><tr class='alert_box_row' id='alert_box_row_2'></tr><tr class='alert_box_row' id='alert_box_row_3'></tr></tbody></table>");
    $('#alert_box_row_1').html("<td class='alert_box_title_unit' colspan='4'><p class='alert_box_title' class='title_color text'>"+title+"</p></td>");
    $('#alert_box_row_2').html("<td class='alert_box_body_unit' colspan='4'>"+body+"</td>");
    $('#alert_box_row_3').html("<td colspan='4'><table style='width:100%'><tbody><tr><td class='alert_box_confirmation_row_unit_left' id='"+extra_id+"'></td><td class='alert_box_load_unit'><img class='load_gif' id='"+load_id+"' src='http://pics.redlay.com/pictures/load.gif'/></td><td class='alert_box_confirm_unit' >"+confirm+"</td><td class='alert_box_cancel_unit' ><input type='button' class='button gray_button' id='message_cancel' onClick=close_alert_box(); value='Cancel' /></tr></tbody></table></td>");

    show_alert_box();
}
function display_error(error, type)
{
    $('#errors').css('opacity', 0).html(error).attr('class', type).show();
    $('#errors').stop().animate({
        opacity :1
    }, 500, function()
    {
        setTimeout(function()
        {
            $('#errors').stop().animate({
                opacity:0
            }, 500, function()
            {
                $('#errors').html('').hide();
            });
        }, 3500);
    });
}

function get_post_format(profile_picture, name, body, functions, second_row, options, option_id, body_id, badges)
{
    //gets badges
    if(badges!=undefined&&badges!='')
    {
        if(badges['gold']==true)
            var badge_body="<tr><td><a class='link' href='http://www.redlay.com/redlay_gold.php'><div class='badge gold_badge' style='cursor:pointer;' ><p class='badge_text' >Gold</p></div></a></td></tr>";
        else
            var badge_body="";
    }
    else
        var badge_body='';
    
    if(second_row!='')
        var extra_row="<tr id='post_row_2' class='post_row'>  <td colspan='2'>"+second_row+"</td>  </tr>";
    else
        var extra_row="";
    return "<div class='status_update' id='"+body_id+"' onmouseover=$('#"+option_id+"').show(); onmouseout=$('#"+option_id+"').hide();>"+options+"<table style='width:100%;'><tbody><tr id='post_row_1' class='post_row'>  <td class='post_profile_picture_unit'><table><tbody><tr><td>"+profile_picture+"</td></tr><tr><td><table style='width:100%;border-spacing:0px;'><tbody>"+badge_body+"</tbody></table></td></tr></tbody></table></td><td class='post_body_unit'><table style='width:100%;'><tbody><tr><td >"+name+"</td></tr><tr><td style='padding:0px' ><div class='post_body'>"+body+"</div></td></tr><tr><td>"+functions+"</td></tr></tbody></table></td>  </tr>"+extra_row+"</tbody></table></div>";
}

function get_public_format(body, profile_picture, name, num_adds, buttons, description, timestamp, body_id, badges)
{
    //gets badges
    if(badges!=undefined&&badges!='')
    {
        if(badges['gold']==true)
            var badge_body="<tr><td><a class='link' href='http://www.redlay.com/redlay_gold.php'><div class='badge gold_badge' ><p class='badge_text' >Gold</p></div></a></td></tr>";
        else
            var badge_body="";
    }
    else
        var badge_body='';
    
    var line_break="<hr class='break'/>";
//    return "<table style='width:100%;' id='"+body_id+"'><tbody><tr><td style='width:400px;'>"+body+"</td> <td style='vertical-align:top;'><table><tbody><tr><td><table><tbody><tr><td>"+profile_picture+"</td><td><table style='height:70px;'><tbody><tr><td>"+name+"</td></tr><tr><td>"+badge_body+"</td></tr></tbody></table></td></tr></tbody></table></td></tr> <tr><td>"+button+"</td></tr><tr><td>"+description+"</td></tr><tr><td>"+timestamp+"</td></tr>  </tbody></table></td></tr></tbody></table>"+line_break;
return "<table style='width:100%;' id='"+body_id+"'><tbody><tr><td style='width:400px;'>"+body+"</td> <td style='vertical-align:top;'><table style='height:100%;display:inline-block;'><tbody><tr><td style='vertical-align:top;height:100px;'><table><tbody><tr><td>"+profile_picture+"</td><td><table style='height:70px;'><tbody><tr><td style='height:20px;'>"+name+"</td></tr><tr><td style='vertical-align:bottom;'>"+num_adds+"</td></tr></tbody></table></td><tr><td><table style='width:100%;'><tbody>"+badge_body+"</tbody></table></td><td></td></tr></tr></tbody></table></td></tr><tr><td style='vertical-align:top;'>"+description+"</td></tr><tr><td style='height:35px;'>"+buttons+"</td></tr><tr><td style='height:20px;'>"+timestamp+"</td></tr>  </tbody></table></td></tr></tbody></table>"+line_break;
}

function display_groups(item_id, type)
{
    //displays appropriate HTML
    if(type!='add')
        $('#'+item_id).html("<input type='button' class='button gray_button' id='"+item_id+"_show_user_groups_button' value='Display to' /><div id='"+item_id+"_box_inside' class='select_body_options'></div>");
    else
        $('#'+item_id).html("<input type='button' class='button gray_button' id='"+item_id+"_show_user_groups_button' value='Add to' /><div id='"+item_id+"_box_inside' class='select_body_options'></div>");
    $('#'+item_id+'_box_inside').html("<table class='select_body_options_table' ><tbody id='"+item_id+"_body' class='select_body_options_table_body'></tbody></table>");
    $('#'+item_id+'_box_inside').hide();
    $('#'+item_id+'_show_user_groups_button').attr({'onClick': "toggle_group_display('"+item_id+"');"});

    //gets the groups
    $.post('user_groups_query.php',
    {
        num:1,
        type: type
    }, function(output)
    {
       var groups=output.groups;
       var num_adds=output.num_adds;
       
        for(var x = 0; x < groups.length; x++)
       {
            $('#'+item_id+'_body').html($('#'+item_id+'_body').html()+"<tr class='select_body_options_row' id='"+item_id+"_audience_row_"+x+"'></tr>");
            $('#'+item_id+'_audience_row_'+x).html("<td class='audience_selection_checkbox_unit' id='"+item_id+"_audience_selection_checkbox_"+x+"'></td><td class='select_body_option_unit' id='"+item_id+"_audience_selection_text_"+x+"'></td><td class='select_body_option_unit' id='"+item_id+"_audience_num_adds_text_"+x+"'></td>");

            if(item_id!='photo_audience_box')
                $('#'+item_id+'_audience_selection_checkbox_'+x).html("<img class='checkbox' id='"+item_id+"_checkbox_"+x+"'/>");
            else
                $('#'+item_id+'_audience_selection_checkbox_'+x).html("<input type='checkbox' class='checkbox' id='"+item_id+"_checkbox_"+x+"' name='groups[]' value='"+groups[x]+"'/>");
            $('#'+item_id+'_audience_selection_text_'+x).html("<p class='select_body_option_text' id='name_"+item_id+"_"+x+"'>"+groups[x]+"</p>");
            $('#'+item_id+'_audience_num_adds_text_'+x).html("<span>("+num_adds[x]+")</span>");
       }

       for(x =0; x < groups.length; x++)
       {
          $('#'+item_id+'_checkbox_'+x).data("group_name", groups[x]);

          
          if(item_id!='photo_audience_box')
          {
              $('#'+item_id+'_checkbox_'+x).attr('onClick', "toggle_checkbox('"+item_id+"', "+x+");");
              $('#name_'+item_id+'_'+x).attr('onClick', "toggle_checkbox('"+item_id+"', "+x+");");
              if(groups[x]=='Everyone')    
                  $('#'+item_id+'_checkbox_'+x).data('checked', 'yes').attr('src', 'http://pics.redlay.com/pictures/gray_checkbox_checked.png');
              else
                  $('#'+item_id+'_checkbox_'+x).data('checked', 'no').attr('src', 'http://pics.redlay.com/pictures/gray_checkbox.png');
          }
       }
      
    }, "json");
}
function display_current_groups(item_id, user_id)
{
    //displays appropriate HTML
    $('#'+item_id).html("<input type='button' class='button gray_button' id='show_user_groups_button' value='Display to' /><div id='"+item_id+"_box_inside' class='select_body_options'></div>");
    $('#'+item_id+'_box_inside').html("<table class='select_body_options_table' ><tbody id='"+item_id+"_body' class='select_body_options_table_body'></tbody></table>");
    $('#'+item_id+'_box_inside').hide();
    $('#show_user_groups_button').attr({'onClick': "toggle_group_display('"+item_id+"');"});

    //gets the groups
    $.post('user_groups_query.php',
    {
        num:2,
        user_id: user_id
    }, function(output)
    {
       var groups=output.groups;
       var groups_in=output.groups_in;
       var num_adds=output.num_adds;

        for(var x = 0; x < groups.length; x++)
       {
            $('#'+item_id+'_body').html($('#'+item_id+'_body').html()+"<tr class='select_body_options_row' id='"+item_id+"_audience_row_"+x+"'></tr>");
            $('#'+item_id+'_audience_row_'+x).html("<td class='audience_selection_checkbox_unit' id='"+item_id+"_audience_selection_checkbox_"+x+"'></td><td class='select_body_option_unit' id='"+item_id+"_audience_selection_text_"+x+"'></td><td class='select_body_option_unit' id='"+item_id+"_audience_num_adds_text_"+x+"'></td>");

            if(item_id!='photo_audience_box')
                $('#'+item_id+'_audience_selection_checkbox_'+x).html("<img class='checkbox' id='"+item_id+"_checkbox_"+x+"'/>");
            else
                $('#'+item_id+'_audience_selection_checkbox_'+x).html("<input type='checkbox' class='checkbox' id='"+item_id+"_checkbox_"+x+"' name='groups[]' value='"+groups[x]+"'/>");
                $('#'+item_id+'_audience_selection_text_'+x).html("<p class='select_body_option_text' id='name_"+item_id+"_"+x+"'>"+groups[x]+"</p>");
                $('#'+item_id+'_audience_num_adds_text_'+x).html("<span>("+num_adds[x]+")</span>");
       }

       for(x =0; x < groups.length; x++)
       {
          $('#'+item_id+'_checkbox_'+x).data("group_name", groups[x]);


          if(item_id!='photo_audience_box')
          {
              $('#'+item_id+'_checkbox_'+x).attr('onClick', "toggle_checkbox('"+item_id+"', "+x+");");
              $('#name_'+item_id+'_'+x).attr('onClick', "toggle_checkbox('"+item_id+"', "+x+");");
              var bool=false;

              if(groups_in[x]=='yes')
                  $('#'+item_id+'_checkbox_'+x).data('checked', 'yes').attr('src', 'http://pics.redlay.com/pictures/gray_checkbox_checked.png');
              else
                  $('#'+item_id+'_checkbox_'+x).data('checked', 'no').attr('src', 'http://pics.redlay.com/pictures/gray_checkbox.png');
          }
       }

    }, "json");
}

function display_current_photo_groups(item_id, picture_id)
{
    //displays appropriate HTML
    $('#'+item_id).html("<input type='button' class='button gray_button' id='show_user_groups_button' value='Display to' /><div id='"+item_id+"_box_inside' class='select_body_options'></div>");
    $('#'+item_id+'_box_inside').html("<table class='select_body_options_table' ><tbody id='"+item_id+"_body' class='select_body_options_table_body'></tbody></table>");
    $('#'+item_id+'_box_inside').hide();
    $('#show_user_groups_button').attr({'onClick': "toggle_group_display('"+item_id+"');"});

    //gets the groups
    $.post('user_groups_query.php',
    {
        num:3,
        picture_id: picture_id
    }, function(output)
    {
       var groups=output.groups;
       var groups_in=output.groups_in;
       var num_adds=output.num_adds;

        for(var x = 0; x < groups.length; x++)
       {
            $('#'+item_id+'_body').html($('#'+item_id+'_body').html()+"<tr class='select_body_options_row' id='"+item_id+"_audience_row_"+x+"'></tr>");
            $('#'+item_id+'_audience_row_'+x).html("<td class='audience_selection_checkbox_unit' id='"+item_id+"_audience_selection_checkbox_"+x+"'></td><td class='select_body_option_unit' id='"+item_id+"_audience_selection_text_"+x+"'></td><td class='select_body_option_unit' id='"+item_id+"_audience_num_adds_text_"+x+"'></td>");

            if(item_id!='photo_audience_box')
                $('#'+item_id+'_audience_selection_checkbox_'+x).html("<img class='checkbox' id='"+item_id+"_checkbox_"+x+"'/>");
            else
                $('#'+item_id+'_audience_selection_checkbox_'+x).html("<input type='checkbox' class='checkbox' id='"+item_id+"_checkbox_"+x+"' name='groups[]' value='"+groups[x]+"'/>");
            
            $('#'+item_id+'_audience_selection_text_'+x).html("<p class='select_body_option_text' id='name_"+item_id+"_"+x+"'>"+groups[x]+"</p>");
            $('#'+item_id+'_audience_num_adds_text_'+x).html("<span>("+num_adds[x]+")</span>");
       }

       for(x =0; x < groups.length; x++)
       {
          $('#'+item_id+'_checkbox_'+x).data("group_name", groups[x]);


          if(item_id!='photo_audience_box')
          {
              $('#'+item_id+'_checkbox_'+x).attr('onClick', "toggle_checkbox('"+item_id+"', "+x+");");
              $('#name_'+item_id+'_'+x).attr('onClick', "toggle_checkbox('"+item_id+"', "+x+");");
              var bool=false;

              if(groups_in[x]=='yes')
                  $('#'+item_id+'_checkbox_'+x).data('checked', 'yes').attr('src', 'http://pics.redlay.com/pictures/gray_checkbox_checked.png');
              else
                  $('#'+item_id+'_checkbox_'+x).data('checked', 'no').attr('src', 'http://pics.redlay.com/pictures/gray_checkbox.png');
          }
       }

    }, "json");
}

function display_current_post_groups(item_id, post_id)
{
    //displays appropriate HTML
    $('#'+item_id).html("<input type='button' class='button gray_button' id='show_user_groups_button' value='Display to' /><div id='"+item_id+"_box_inside' class='select_body_options'></div>");
    $('#'+item_id+'_box_inside').html("<table class='select_body_options_table' ><tbody id='"+item_id+"_body' class='select_body_options_table_body'></tbody></table>");
    $('#'+item_id+'_box_inside').hide();
    $('#show_user_groups_button').attr({'onClick': "toggle_group_display('"+item_id+"');"});

    //gets the groups
    $.post('user_groups_query.php',
    {
        num:4,
        post_id: post_id
    }, function(output)
    {
       var groups=output.groups;
       var groups_in=output.groups_in;
       var num_adds=output.num_adds;

        for(var x = 0; x < groups.length; x++)
       {
            $('#'+item_id+'_body').html($('#'+item_id+'_body').html()+"<tr class='select_body_options_row' id='"+item_id+"_audience_row_"+x+"'></tr>");
            $('#'+item_id+'_audience_row_'+x).html("<td class='audience_selection_checkbox_unit' id='"+item_id+"_audience_selection_checkbox_"+x+"'></td><td class='select_body_option_unit' id='"+item_id+"_audience_selection_text_"+x+"'></td><td class='select_body_option_unit' id='"+item_id+"_audience_num_adds_text_"+x+"'></td>");

            if(item_id!='photo_audience_box')
                $('#'+item_id+'_audience_selection_checkbox_'+x).html("<img class='checkbox' id='"+item_id+"_checkbox_"+x+"'/>");
            else
                $('#'+item_id+'_audience_selection_checkbox_'+x).html("<input type='checkbox' class='checkbox' id='"+item_id+"_checkbox_"+x+"' name='groups[]' value='"+groups[x]+"'/>");
            
            $('#'+item_id+'_audience_selection_text_'+x).html("<p class='select_body_option_text' id='name_"+item_id+"_"+x+"'>"+groups[x]+"</p>");
            $('#'+item_id+'_audience_num_adds_text_'+x).html("<span>("+num_adds[x]+")</span>");
       }

       for(x =0; x < groups.length; x++)
       {
          $('#'+item_id+'_checkbox_'+x).data("group_name", groups[x]);


          if(item_id!='photo_audience_box')
          {
              $('#'+item_id+'_checkbox_'+x).attr('onClick', "toggle_checkbox('"+item_id+"', "+x+");");
                $('#name_'+item_id+'_'+x).attr('onClick', "toggle_checkbox('"+item_id+"', "+x+");");
              var bool=false;

              if(groups_in[x]=='yes')
                  $('#'+item_id+'_checkbox_'+x).data('checked', 'yes').attr('src', 'http://pics.redlay.com/pictures/gray_checkbox_checked.png');
              else
                  $('#'+item_id+'_checkbox_'+x).data('checked', 'no').attr('src', 'http://pics.redlay.com/pictures/gray_checkbox.png');
          }
       }

    }, "json");
}

function show_alert_box()
{
    setTimeout(function()
    {
        var Document_width=($(window).width())/2;
        
        $('.alert_box').css({'margin-top': (-1*($('.alert_box').height()/2))});
        $('.alert_box').css('display', 'block').animate({opacity: 1}, 350, function(){}).draggable();
        $('.alert_box').css('left', Document_width-250);
    }, 200);
}
function display_dim()
{
    $('#dim').css({'opacity': '0'}).show();
    $('#dim').animate({opacity:.3}, 350, function(){});
}
//function toggle_checkbox(item_id, index)
//{
//    var checked=$('#'+item_id+'_checkbox_'+index).data('checked');
//    if(checked=='yes')
//        $('#'+item_id+'_checkbox_'+index).attr('src', 'http://pics.redlay.com/pictures/gray_checkbox.png').data('checked', 'no');
//    else
//        $('#'+item_id+'_checkbox_'+index).attr('src', 'http://pics.redlay.com/pictures/gray_checkbox_checked.png').data('checked', 'yes');
//}

function toggle_group_display(item_id)
{
    if($('#'+item_id+'_box_inside').css('display')!='block')
        $('#'+item_id+'_box_inside').show();
    else
        $('#'+item_id+'_box_inside').hide();
}

//id is id of element and string is message
function display_title(id, string)
{
    $(id).mousemove(function(e)
    {
        if($('.tool_tip').css('opacity')<'0.8')
        {
            $('.tool_tip').html("<p class='tool_tip_text'>"+string+"</p>").css({'left': (e.pageX-50), 'top': (e.pageY+20)});
            $('.tool_tip').stop().animate({
                opacity: 1
            }, 100, function()
            {});
        }
        else
            $('.tool_tip').css({'left': (e.pageX-50), 'top': (e.pageY+20)});
    });
}

//id is id of element and string is message
function display_title_up(id, string)
{
    $(id).mousemove(function(e)
    {
        if($('.tool_tip').css('opacity')<'0.8')
        {
            $('.tool_tip').html("<p class='tool_tip_text'>"+string+"</p>").css({'left': (e.pageX-50), 'top': (e.pageY-50)});
            $('.tool_tip').stop().animate({
                opacity: 1
            }, 100, function()
            {});
        }
        else
            $('.tool_tip').css({'left': (e.pageX-50), 'top': (e.pageY-50)});
    });
}
function close_alert_box()
{
    $('.alert_box').animate({
        opacity: 0
    }, 500, function()
    {
        $('.alert_box_inside').html('');
        $('.alert_box').hide();
    });
    $('#dim').animate({opacity:0}, 350, function(){
        $('#dim').hide();
    });
    
}
//id is id of element
//type is either user or page
//user_id is id of either user or page
function display_title_picture(id, type, user_id)
{
    $(id).mousemove(function(e)
    {
        if($('.tool_tip').css('opacity')<'0.8')
        {
            if(type=='user')
                var src="http://u.redlay.com/users/"+user_id+"/photos/0.jpg";
            else
                var src="http://u.redlay.com/pages/"+user_id+"/0.jpg";

            $('.tool_tip').html("<img class='tool_tip_image' src='"+src+"' />").css({'left': (e.pageX-50), 'top': (e.pageY+20)});
            $('.tool_tip').stop().animate({
                opacity: 1
            }, 100, function()
            {

            });
        }
        else
            $('.tool_tip').css({'left': (e.pageX-50), 'top': (e.pageY+20)});
    });
}
function hide_title(id)
{
    $('.tool_tip').stop().animate({
        opacity: '0'
    }, 100, function()
    {
        $('.tool_tip').html('').css({'left': '0px', 'top': '0px', 'opacity': '0'});
    });
}

function input_in(ID)
{
    $(ID).css({'box-shadow': 'inset 0px 0px 4px 0px rgb(220,21,0)', 'border-color': 'rgb(220,21,0)'});
}
function input_out(ID)
{
    $(ID).css({'box-shadow': 'none', 'border-color': 'gray'});
}
function toggle_checkbox(item_id, index)
{
    var checked=$('#'+item_id+'_checkbox_'+index).data('checked');
    if(checked=='yes')
        $('#'+item_id+'_checkbox_'+index).attr('src', 'http://pics.redlay.com/pictures/gray_checkbox.png').data('checked', 'no');
    else
        $('#'+item_id+'_checkbox_'+index).attr('src', 'http://pics.redlay.com/pictures/gray_checkbox_checked.png').data('checked', 'yes');
}


function name_over(string)
{
    $(string).css({'text-decoration': 'underline'});
}
function name_out(string)
{
    $(string).css('text-decoration', 'none');
}

function show_menu()
{
    if($('#menu').css('display')=='none')
        $('#menu').show();
    else
        $('#menu').hide();
}
function menu_text_over(string, num)
{
    if(num==1)
        $(string).addClass('menu_text_over');
    else
        $(string).removeClass('menu_text_over');
}
function show_post_options(post_id, user_id)
{
   var begin_body="<table style='width:100%;'><tbody><tr>";
    var group_selection="<td><div class='select_box' id='post_groups_box'></div></td>";
    var group_selection_save_button="<td style='text-align:right;'><input type='button' class='button red_button' value='Change' onClick='change_audience_options("+post_id+");'/></td></tr>";
    var delete_photo="<td><p class='alert_box_text'>Delete post</p></td><td style='text-align:right'><input type='button' class='button red_button' onclick='delete_post("+post_id+", "+user_id+");' value='Delete' /></td>";
    var end_body="</tbody></table>";
    var confirm="";
    display_alert("Options", begin_body+group_selection+group_selection_save_button+delete_photo+end_body, 'options_extra_unit', 'message_gif', confirm);
    display_current_post_groups('post_groups_box', post_id);


    $('#message_gif').hide();
    change_color();
}
function toggle_regular_checkbox(id)
{
    var src=$(id).attr('src');
    if(src=='http://pics.redlay.com/pictures/gray_checkbox.png')
        $(id).attr('src', 'http://pics.redlay.com/pictures/gray_checkbox_checked.png');
    else
        $(id).attr('src', 'http://pics.redlay.com/pictures/gray_checkbox.png');
}
function text_format(text)
{
    var final_text=text;
    
    //continue until everything is converted
    while(1==1)
    {
        if(final_text.toLowerCase().indexOf('[b](')!=-1)
        {
            var prev_index=final_text.toLowerCase().indexOf('[b](');
            
            //gets "and this is going to be bold) whatever comes after"
            var start=prev_index+4;
            var temp=final_text.substring(start);
            
            
            if(temp.indexOf(')')!=-1)
            {
                //gets ")" of "and this is going to be bold)"
                var end=start+temp.indexOf(')');
                
                
                var body_text=final_text.substring(start, end);
                
                var front=final_text.substring(0,prev_index);
                var middle="<span style='font-weight:bold;'>"+body_text+"</span>";
                var back=final_text.substring(end+1);
                
                //removes "[b](and this is going to be bold)" and replaces it with <span style='font-weight:bold;'>and this is going to be bold</span>
                final_text=front+middle+back;
            }
            else
            {
                //gets "and this is going to be bold"
                var front=final_text.substring(0,prev_index);
                var body_text=final_text.substring(start);
                
                //removes "[b](and this is going to be bold)" and replaces it with <span style='font-weight:bold;'>and this is going to be bold</span>
                final_text=front+"<span style='font-weight:bold;'>"+body_text+"</span>";
            }
        }
        else if(final_text.toLowerCase().indexOf('[i](')!=-1)
        {
            var prev_index=final_text.toLowerCase().indexOf('[i](');
            
            //gets "and this is going to be bold) whatever comes after"
            var start=prev_index+4;
            var temp=final_text.substring(start);
            
            
            if(temp.indexOf(')')!=-1)
            {
                //gets ")" of "and this is going to be bold)"
                var end=start+temp.indexOf(')');
                
                
                var body_text=final_text.substring(start, end);
                
                var front=final_text.substring(0,prev_index);
                var middle="<span style='font-style:italic;'>"+body_text+"</span>";
                var back=final_text.substring(end+1);
                
                //removes "[b](and this is going to be bold)" and replaces it with <span style='font-weight:bold;'>and this is going to be bold</span>
                final_text=front+middle+back;
            }
            else
            {
                //gets "and this is going to be bold"
                var front=final_text.substring(0,prev_index);
                var body_text=final_text.substring(start);
                
                //removes "[b](and this is going to be bold)" and replaces it with <span style='font-weight:bold;'>and this is going to be bold</span>
                final_text=front+"<span style='font-style:italic;'>"+body_text+"</span>";
            }
        }
        
        else if(final_text.toLowerCase().indexOf('[u](')!=-1)
        {
            var prev_index=final_text.toLowerCase().indexOf('[u](');
            
            //gets "and this is going to be bold) whatever comes after"
            var start=prev_index+4;
            var temp=final_text.substring(start);
            
            
            if(temp.indexOf(')')!=-1)
            {
                //gets ")" of "and this is going to be bold)"
                var end=start+temp.indexOf(')');
                
                
                var body_text=final_text.substring(start, end);
                
                var front=final_text.substring(0,prev_index);
                var middle="<span style='text-decoration:underline;'>"+body_text+"</span>";
                var back=final_text.substring(end+1);
                
                //removes "[b](and this is going to be bold)" and replaces it with <span style='font-weight:bold;'>and this is going to be bold</span>
                final_text=front+middle+back;
            }
            else
            {
                //gets "and this is going to be bold"
                var front=final_text.substring(0,prev_index);
                var body_text=final_text.substring(start);
                
                //removes "[b](and this is going to be bold)" and replaces it with <span style='font-weight:bold;'>and this is going to be bold</span>
                final_text=front+"<span style='text-decoration:underline;'>"+body_text+"</span>";
            }
        }
            
        else if(final_text.toLowerCase().indexOf('[s](')!=-1)
        {
            var prev_index=final_text.toLowerCase().indexOf('[s](');
            
            //gets "and this is going to be bold) whatever comes after"
            var start=prev_index+4;
            var temp=final_text.substring(start);
            
            
            if(temp.indexOf(')')!=-1)
            {
                //gets ")" of "and this is going to be bold)"
                var end=start+temp.indexOf(')');
                
                
                var body_text=final_text.substring(start, end);
                
                var front=final_text.substring(0,prev_index);
                var middle="<span style='font-size:75%;'>"+body_text+"</span>";
                var back=final_text.substring(end+1);
                
                //removes "[b](and this is going to be bold)" and replaces it with <span style='font-weight:bold;'>and this is going to be bold</span>
                final_text=front+middle+back;
            }
            else
            {
                //gets "and this is going to be bold"
                var front=final_text.substring(0,prev_index);
                var body_text=final_text.substring(start);
                
                //removes "[b](and this is going to be bold)" and replaces it with <span style='font-weight:bold;'>and this is going to be bold</span>
                final_text=front+"<span style='font-size:75%;'>"+body_text+"</span>";
            }
        }
        
        else if(final_text.toLowerCase().indexOf('[box](')!=-1)
        {
            var prev_index=final_text.toLowerCase().indexOf('[box](');
            
            //gets "and this is going to be bold) whatever comes after"
            var start=prev_index+6;
            var temp=final_text.substring(start);
            
            
            if(temp.indexOf(')')!=-1)
            {
                //gets ")" of "and this is going to be bold)"
                var end=start+temp.indexOf(')');
                
                
                var body_text=final_text.substring(start, end);
                
                var front=final_text.substring(0,prev_index);
                var middle="<span style='border:1px solid black;'>"+body_text+"</span>";
                var back=final_text.substring(end+1);
                
                //removes "[b](and this is going to be bold)" and replaces it with <span style='font-weight:bold;'>and this is going to be bold</span>
                final_text=front+middle+back;
            }
            else
            {
                //gets "and this is going to be bold"
                var front=final_text.substring(0,prev_index);
                var body_text=final_text.substring(start);
                
                //removes "[b](and this is going to be bold)" and replaces it with <span style='font-weight:bold;'>and this is going to be bold</span>
                final_text=front+"<span style='border:1px solid black;'>"+body_text+"</span>";
            }
        }
        
        else if(final_text.toLowerCase().indexOf('[red](')!=-1)
        {
            var prev_index=final_text.toLowerCase().indexOf('[red](');

            //gets "and this is going to be bold) whatever comes after"
            var start=prev_index+6;
            var temp=final_text.substring(start);


            if(temp.indexOf(')')!=-1)
            {
                //gets ")" of "and this is going to be bold)"
                var end=start+temp.indexOf(')');


                var body_text=final_text.substring(start, end);

                var front=final_text.substring(0,prev_index);
                var middle="<span style='color:red;'>"+body_text+"</span>";
                var back=final_text.substring(end+1);

                //removes "[b](and this is going to be bold)" and replaces it with <span style='font-weight:bold;'>and this is going to be bold</span>
                final_text=front+middle+back;
            }
            else
            {
                //gets "and this is going to be bold"
                var front=final_text.substring(0,prev_index);
                var body_text=final_text.substring(start);

                //removes "[b](and this is going to be bold)" and replaces it with <span style='font-weight:bold;'>and this is going to be bold</span>
                final_text=front+"<span style='color:red;'>"+body_text+"</span>";
            }
        }
        else if(final_text.toLowerCase().indexOf('[orange](')!=-1)
        {
            var prev_index=final_text.toLowerCase().indexOf('[orange](');

            //gets "and this is going to be bold) whatever comes after"
            var start=prev_index+9;
            var temp=final_text.substring(start);


            if(temp.indexOf(')')!=-1)
            {
                //gets ")" of "and this is going to be bold)"
                var end=start+temp.indexOf(')');


                var body_text=final_text.substring(start, end);

                var front=final_text.substring(0,prev_index);
                var middle="<span style='color:orange;'>"+body_text+"</span>";
                var back=final_text.substring(end+1);

                //removes "[b](and this is going to be bold)" and replaces it with <span style='font-weight:bold;'>and this is going to be bold</span>
                final_text=front+middle+back;
            }
            else
            {
                //gets "and this is going to be bold"
                var front=final_text.substring(0,prev_index);
                var body_text=final_text.substring(start);

                //removes "[b](and this is going to be bold)" and replaces it with <span style='font-weight:bold;'>and this is going to be bold</span>
                final_text=front+"<span style='color:orange;'>"+body_text+"</span>";
            }
        }   
        else if(final_text.toLowerCase().indexOf('[yellow](')!=-1)
        {
            var prev_index=final_text.toLowerCase().indexOf('[yellow](');

            //gets "and this is going to be bold) whatever comes after"
            var start=prev_index+9;
            var temp=final_text.substring(start);


            if(temp.indexOf(')')!=-1)
            {
                //gets ")" of "and this is going to be bold)"
                var end=start+temp.indexOf(')');


                var body_text=final_text.substring(start, end);

                var front=final_text.substring(0,prev_index);
                var middle="<span style='color:yellow;'>"+body_text+"</span>";
                var back=final_text.substring(end+1);

                //removes "[b](and this is going to be bold)" and replaces it with <span style='font-weight:bold;'>and this is going to be bold</span>
                final_text=front+middle+back;
            }
            else
            {
                //gets "and this is going to be bold"
                var front=final_text.substring(0,prev_index);
                var body_text=final_text.substring(start);

                //removes "[b](and this is going to be bold)" and replaces it with <span style='font-weight:bold;'>and this is going to be bold</span>
                final_text=front+"<span style='color:yellow;'>"+body_text+"</span>";
            }
        }
        else if(final_text.toLowerCase().indexOf('[green](')!=-1)
        {
            var prev_index=final_text.toLowerCase().indexOf('[green](');

            //gets "and this is going to be bold) whatever comes after"
            var start=prev_index+8;
            var temp=final_text.substring(start);


            if(temp.indexOf(')')!=-1)
            {
                //gets ")" of "and this is going to be bold)"
                var end=start+temp.indexOf(')');


                var body_text=final_text.substring(start, end);

                var front=final_text.substring(0,prev_index);
                var middle="<span style='color:green;'>"+body_text+"</span>";
                var back=final_text.substring(end+1);

                //removes "[b](and this is going to be bold)" and replaces it with <span style='font-weight:bold;'>and this is going to be bold</span>
                final_text=front+middle+back;
            }
            else
            {
                //gets "and this is going to be bold"
                var front=final_text.substring(0,prev_index);
                var body_text=final_text.substring(start);

                //removes "[b](and this is going to be bold)" and replaces it with <span style='font-weight:bold;'>and this is going to be bold</span>
                final_text=front+"<span style='color:green;'>"+body_text+"</span>";
            }
        }
        else if(final_text.toLowerCase().indexOf('[blue](')!=-1)
        {
            var prev_index=final_text.toLowerCase().indexOf('[blue](');

            //gets "and this is going to be bold) whatever comes after"
            var start=prev_index+7;
            var temp=final_text.substring(start);


            if(temp.indexOf(')')!=-1)
            {
                //gets ")" of "and this is going to be bold)"
                var end=start+temp.indexOf(')');


                var body_text=final_text.substring(start, end);

                var front=final_text.substring(0,prev_index);
                var middle="<span style='color:blue;'>"+body_text+"</span>";
                var back=final_text.substring(end+1);

                //removes "[b](and this is going to be bold)" and replaces it with <span style='font-weight:bold;'>and this is going to be bold</span>
                final_text=front+middle+back;
            }
            else
            {
                //gets "and this is going to be bold"
                var front=final_text.substring(0,prev_index);
                var body_text=final_text.substring(start);

                //removes "[b](and this is going to be bold)" and replaces it with <span style='font-weight:bold;'>and this is going to be bold</span>
                final_text=front+"<span style='color:blue;'>"+body_text+"</span>";
            }
        }
        else if(final_text.toLowerCase().indexOf('[purple](')!=-1)
        {
            var prev_index=final_text.toLowerCase().indexOf('[purple](');

            //gets "and this is going to be bold) whatever comes after"
            var start=prev_index+9;
            var temp=final_text.substring(start);


            if(temp.indexOf(')')!=-1)
            {
                //gets ")" of "and this is going to be bold)"
                var end=start+temp.indexOf(')');


                var body_text=final_text.substring(start, end);

                var front=final_text.substring(0,prev_index);
                var middle="<span style='color:purple;'>"+body_text+"</span>";
                var back=final_text.substring(end+1);

                //removes "[b](and this is going to be bold)" and replaces it with <span style='font-weight:bold;'>and this is going to be bold</span>
                final_text=front+middle+back;
            }
            else
            {
                //gets "and this is going to be bold"
                var front=final_text.substring(0,prev_index);
                var body_text=final_text.substring(start);

                //removes "[b](and this is going to be bold)" and replaces it with <span style='font-weight:bold;'>and this is going to be bold</span>
                final_text=front+"<span style='color:purple;'>"+body_text+"</span>";
            }
        }
        else if(final_text.toLowerCase().indexOf('[pink](')!=-1)
        {
            var prev_index=final_text.toLowerCase().indexOf('[pink](');

            //gets "and this is going to be bold) whatever comes after"
            var start=prev_index+7;
            var temp=final_text.substring(start);


            if(temp.indexOf(')')!=-1)
            {
                //gets ")" of "and this is going to be bold)"
                var end=start+temp.indexOf(')');


                var body_text=final_text.substring(start, end);

                var front=final_text.substring(0,prev_index);
                var middle="<span style='color:pink;'>"+body_text+"</span>";
                var back=final_text.substring(end+1);

                //removes "[b](and this is going to be bold)" and replaces it with <span style='font-weight:bold;'>and this is going to be bold</span>
                final_text=front+middle+back;
            }
            else
            {
                //gets "and this is going to be bold"
                var front=final_text.substring(0,prev_index);
                var body_text=final_text.substring(start);

                //removes "[b](and this is going to be bold)" and replaces it with <span style='font-weight:bold;'>and this is going to be bold</span>
                final_text=front+"<span style='color:pink;'>"+body_text+"</span>";
            }
        }
        else if(final_text.toLowerCase().indexOf('[brown](')!=-1)
        {
            var prev_index=final_text.toLowerCase().indexOf('[brown](');

            //gets "and this is going to be bold) whatever comes after"
            var start=prev_index+8;
            var temp=final_text.substring(start);


            if(temp.indexOf(')')!=-1)
            {
                //gets ")" of "and this is going to be bold)"
                var end=start+temp.indexOf(')');


                var body_text=final_text.substring(start, end);

                var front=final_text.substring(0,prev_index);
                var middle="<span style='color:brown;'>"+body_text+"</span>";
                var back=final_text.substring(end+1);

                //removes "[b](and this is going to be bold)" and replaces it with <span style='font-weight:bold;'>and this is going to be bold</span>
                final_text=front+middle+back;
            }
            else
            {
                //gets "and this is going to be bold"
                var front=final_text.substring(0,prev_index);
                var body_text=final_text.substring(start);

                //removes "[b](and this is going to be bold)" and replaces it with <span style='font-weight:bold;'>and this is going to be bold</span>
                final_text=front+"<span style='color:brown;'>"+body_text+"</span>";
            }
        }
        else
            break;
    }
    
    while(final_text.indexOf('3:)')!=-1)
        final_text=final_text.replace("3:)", "<span class='emoticon' style='background-image: url(http://pics.redlay.com/pictures/emoticons/7.png)'></span>");
    while(final_text.indexOf('(devil)')!=-1)
        final_text=final_text.replace("(devil)", "<span class='emoticon' style='background-image: url(http://pics.redlay.com/pictures/emoticons/7.png)'></span>");
    
    while(final_text.indexOf(':D')!=-1)
        final_text=final_text.replace(":D", "<span class='emoticon' style='background-image: url(http://pics.redlay.com/pictures/emoticons/1.png)'></span>");
    while(final_text.indexOf(':-D')!=-1)
        final_text=final_text.replace(":-D", "<span class='emoticon' style='background-image: url(http://pics.redlay.com/pictures/emoticons/1.png)'></span>");
    
    while(final_text.indexOf(':)')!=-1)
        final_text=final_text.replace(":)", "<span class='emoticon' style='background-image: url(http://pics.redlay.com/pictures/emoticons/2.png)'></span>");
    while(final_text.indexOf(':-)')!=-1)
        final_text=final_text.replace(":-)", "<span class='emoticon' style='background-image: url(http://pics.redlay.com/pictures/emoticons/2.png)'></span>");
    while(final_text.indexOf('(:')!=-1)
        final_text=final_text.replace("(:", "<span class='emoticon' style='background-image: url(http://pics.redlay.com/pictures/emoticons/2.png)'></span>");
    while(final_text.indexOf('(-:')!=-1)
        final_text=final_text.replace("(-:", "<span class='emoticon' style='background-image: url(http://pics.redlay.com/pictures/emoticons/2.png)'></span>");
   
    while(final_text.indexOf('-_-')!=-1)
        final_text=final_text.replace("-_-", "<span class='emoticon' style='background-image: url(http://pics.redlay.com/pictures/emoticons/3.png)'></span>");
    while(final_text.indexOf('(-_-)')!=-1)
        final_text=final_text.replace("(-_-)", "<span class='emoticon' style='background-image: url(http://pics.redlay.com/pictures/emoticons/3.png)'></span>");
    
    while(final_text.indexOf('*_*')!=-1)
        final_text=final_text.replace("*_*", "<span class='emoticon' style='background-image: url(http://pics.redlay.com/pictures/emoticons/4.png)'></span>");
    while(final_text.indexOf('*.*')!=-1)
        final_text=final_text.replace("*.*", "<span class='emoticon' style='background-image: url(http://pics.redlay.com/pictures/emoticons/4.png)'></span>");
    while(final_text.indexOf('O_O')!=-1)
        final_text=final_text.replace("O_O", "<span class='emoticon' style='background-image: url(http://pics.redlay.com/pictures/emoticons/4.png)'></span>");
    while(final_text.indexOf('O.O')!=-1)
        final_text=final_text.replace("O.O", "<span class='emoticon' style='background-image: url(http://pics.redlay.com/pictures/emoticons/4.png)'></span>");
    while(final_text.indexOf('0.0')!=-1)
        final_text=final_text.replace("0.0", "<span class='emoticon' style='background-image: url(http://pics.redlay.com/pictures/emoticons/4.png)'></span>");
    while(final_text.indexOf('0_0')!=-1)
        final_text=final_text.replace("0_0", "<span class='emoticon' style='background-image: url(http://pics.redlay.com/pictures/emoticons/4.png)'></span>");
    while(final_text.indexOf('(*_*)')!=-1)
        final_text=final_text.replace("(*_*)", "<span class='emoticon' style='background-image: url(http://pics.redlay.com/pictures/emoticons/4.png)'></span>");
    while(final_text.indexOf('(*.*)')!=-1)
        final_text=final_text.replace("(*.*)", "<span class='emoticon' style='background-image: url(http://pics.redlay.com/pictures/emoticons/4.png)'></span>");
    while(final_text.indexOf('(O_O)')!=-1)
        final_text=final_text.replace("(O_O)", "<span class='emoticon' style='background-image: url(http://pics.redlay.com/pictures/emoticons/4.png)'></span>");
    while(final_text.indexOf('(O.O)')!=-1)
        final_text=final_text.replace("(O.O)", "<span class='emoticon' style='background-image: url(http://pics.redlay.com/pictures/emoticons/4.png)'></span>");
    while(final_text.indexOf('(0.0)')!=-1)
        final_text=final_text.replace("(0.0)", "<span class='emoticon' style='background-image: url(http://pics.redlay.com/pictures/emoticons/4.png)'></span>");
    while(final_text.indexOf('(0_0)')!=-1)
        final_text=final_text.replace("(0_0)", "<span class='emoticon' style='background-image: url(http://pics.redlay.com/pictures/emoticons/4.png)'></span>");
    
    while(final_text.indexOf(":'(")!=-1)
        final_text=final_text.replace(":'(", "<span class='emoticon' style='background-image: url(http://pics.redlay.com/pictures/emoticons/5.png)'></span>");
    while(final_text.indexOf(")':")!=-1)
        final_text=final_text.replace(")':", "<span class='emoticon' style='background-image: url(http://pics.redlay.com/pictures/emoticons/5.png)'></span>");
    
    while(final_text.indexOf('(cool)')!=-1)
        final_text=final_text.replace("(cool)", "<span class='emoticon' style='background-image: url(http://pics.redlay.com/pictures/emoticons/6.png)'></span>");
    
    while(final_text.indexOf('XD')!=-1)
        final_text=final_text.replace("XD", "<span class='emoticon' style='background-image: url(http://pics.redlay.com/pictures/emoticons/8.png)'></span>");
    
    while(final_text.indexOf('&lt;3')!=-1)
        final_text=final_text.replace("&lt;3", "<span class='emoticon' style='background-image: url(http://pics.redlay.com/pictures/emoticons/9.png)'></span>");
    while(final_text.indexOf('<3')!=-1)
        final_text=final_text.replace("<3", "<span class='emoticon' style='background-image: url(http://pics.redlay.com/pictures/emoticons/9.png)'></span>");
    
    while(final_text.indexOf(":P")!=-1)
        final_text=final_text.replace(":P", "<span class='emoticon' style='background-image: url(http://pics.redlay.com/pictures/emoticons/10.png)'></span>");
    while(final_text.indexOf(':-P')!=-1)
        final_text=final_text.replace(":-P", "<span class='emoticon' style='background-image: url(http://pics.redlay.com/pictures/emoticons/10.png)'></span>");
    while(final_text.indexOf('d:')!=-1)
        final_text=final_text.replace("d:", "<span class='emoticon' style='background-image: url(http://pics.redlay.com/pictures/emoticons/10.png)'></span>");
    while(final_text.indexOf('d-:')!=-1)
        final_text=final_text.replace("d-:", "<span class='emoticon' style='background-image: url(http://pics.redlay.com/pictures/emoticons/10.png)'></span>");
    
    while(final_text.indexOf('(swear)')!=-1)
        final_text=final_text.replace("(swear)", "<span class='emoticon' style='background-image: url(http://pics.redlay.com/pictures/emoticons/11.png)'></span>");
    
    while(final_text.indexOf('&gt;:(')!=-1)
        final_text=final_text.replace("&gt;:(", "<span class='emoticon' style='background-image: url(http://pics.redlay.com/pictures/emoticons/12.png)'></span>");
    while(final_text.indexOf('&gt;:|')!=-1)
        final_text=final_text.replace("&gt;:|", "<span class='emoticon' style='background-image: url(http://pics.redlay.com/pictures/emoticons/12.png)'></span>");
    while(final_text.indexOf('>:(')!=-1)
        final_text=final_text.replace(">:(", "<span class='emoticon' style='background-image: url(http://pics.redlay.com/pictures/emoticons/12.png)'></span>");
    while(final_text.indexOf('>:|')!=-1)
        final_text=final_text.replace(">:|", "<span class='emoticon' style='background-image: url(http://pics.redlay.com/pictures/emoticons/12.png)'></span>");
    
    while(final_text.indexOf(';)')!=-1)
        final_text=final_text.replace(";)", "<span class='emoticon' style='background-image: url(http://pics.redlay.com/pictures/emoticons/13.png)'></span>");
    while(final_text.indexOf('(;')!=-1)
        final_text=final_text.replace("(;", "<span class='emoticon' style='background-image: url(http://pics.redlay.com/pictures/emoticons/13.png)'></span>");
    
    
    return final_text;
}
function convert_image(text, type)
{
    //text EX: http://i.imgur.com/4v8spSg.jpg
    var final_text=text;
    
    //if there's a link
    if(final_text.toLowerCase().indexOf('http://')!=-1||final_text.toLowerCase().indexOf('https://')!=-1)
    {
        //gets index of beginning occurance
        var http=final_text.toLowerCase().indexOf('http://');
        var https=final_text.toLowerCase().indexOf('https://');
        
        if(http!=-1&&https==-1)
            var prev_index=http;
        else if(https!=-1&&http==-1)
            var prev_index=https;
        else
        {
            if(http<https)
                var prev_index=http;
            else
                var prev_index=https;
        }

        //gets "http://i.imgur.com/4v8spSg.jpg ..."
        var temp=final_text.substring(prev_index);
        temp=temp.replace("#", '');
        
        //gets the end of the url
        if(temp.indexOf(' ')!=-1||temp.indexOf("<br />")!=-1)
        {
            //gets " " of "http://i.imgur.com/4v8spSg.jpg ..."
            var space=temp.indexOf(' ');
            var line=temp.indexOf('<br />')
            var end=temp.indexOf(' ')+prev_index;
            
            if(space!=-1&&line==-1)
                var end=space+prev_index;
            else if(line!=-1&&space==-1)
                var end=line+prev_index;
            else
            {
                if(space<line)
                    var end=space+prev_index;
                else
                    var end=line+prev_index;
            }

            //gets the full url: "http://i.imgur.com/4v8spSg.jpg"
            var body_text=final_text.substring(prev_index, end);
            
            
            //gets "/name.jpg"
            var body_split=body_text.split('/');
            var extension=body_split[body_split.length-1];

            //gets "jpg"
            body_split=extension.split('.');
            extension=body_split[body_split.length-1];
            var before_end=body_split[body_split.length-2];

            //if it's an image
            if( (extension.toLowerCase()=='jpg'||extension.toLowerCase()=='png'||extension.toLowerCase()=='gif') || ( (extension==''||extension==','||extension=='!'||extension=='?') && (before_end.toLowerCase()=='jpg'|| before_end.toLowerCase()=='png' || before_end.toLowerCase()=='gif' ) ) )
            {
                //gets the stuff before the image
                var front=final_text.substring(0,prev_index);

                //gets the image
                if(type=='comment')
                    var middle="<br /><div class='comment_picture_div' ><a class='link' href='"+body_text+"'><img class='comment_picture' src='"+body_text+"'  /></a></div>";
                else if(type=='post')
                    var middle="<br /><div class='post_picture_div' ><a class='link' href='"+body_text+"'><img class='post_picture' src='"+body_text+"'  /></a></div>";

                //gets the stuff after the image
                var back=final_text.substring(end);

                //removes "http://i.imgur.com/4v8spSg.jpg" and replaces it with "<img src='http://i.imgur.com/4v8spSg.jpg' />"
                final_text=front+back+middle;
            }
        }
        else
        {
            //gets the full url: "http://i.imgur.com/4v8spSg.jpg"
            var body_text=final_text.substring(prev_index);
            
            //gets "/name.jpg"
            var body_split=body_text.split('/');
            var extension=body_split[body_split.length-1];

            //gets "jpg"
            body_split=extension.split('.');
            extension=body_split[body_split.length-1];
            
            //if it's an image
            if( (extension.toLowerCase()=='jpg'||extension.toLowerCase()=='png'||extension.toLowerCase()=='gif') || ( (extension==''||extension==','||extension=='!'||extension=='?') && (before_end.toLowerCase()=='jpg'|| before_end.toLowerCase()=='png' || before_end.toLowerCase()=='gif' ) ) )
            {
                //gets the stuff before the image
                var front=final_text.substring(0,prev_index);

                //gets the image and everything afterwards
                var body_text=final_text.substring(prev_index);

                //removes "[b](and this is going to be bold)" and replaces it with <span style='font-weight:bold;'>and this is going to be bold</span>
                if(type=='comment')
                    final_text=front+"<br /><div class='comment_picture_div' ><a class='link' href='"+body_text+"'><img class='comment_picture' src='"+body_text+"'  /></a></div>";
                else if(type=='post')
                    final_text=front+"<br /><div class='post_picture_div' ><a class='link' href='"+body_text+"'><img class='post_picture' src='"+body_text+"'  /></a></div>";
            }
        }
    }
    return final_text;
}
function video_over(vid_id, button_id)
{
    $(vid_id).stop().animate({
        opacity:.85
    }, 250, function()
    {});
    
    $(button_id).stop().animate({
        opacity:.85
    }, 250, function()
    {});
}
function video_out(vid_id, button_id)
{
    $(vid_id).stop().animate({
        opacity:.75
    }, 250, function()
    {});
    
    $(button_id).stop().animate({
        opacity:.65
    }, 250, function()
    {});
}
function play_button_over(vid_id, button_id)
{
    video_over(vid_id, button_id);
}
function share_video(vid_id, user_id)
{
    $.post('share_video.php',
    {
        video: vid_id,
        user_id: user_id
    }, function(output)
    {
        if(output=='Video shared')
            display_error(output, 'good_errors');
        else
            display_error(output, 'bad_errors');
    });
}
function like_comment(profile_id, post_id, post_index, comment_id, comment_index, page, num_likes)
{
    $.post('like_comment.php',
    {
        post_id: post_id,
        comment_id: comment_id,
        profile_id: profile_id
    }, function(output)
    {
        if(output=='Comment liked')
        {
            num_likes++;
            $('#comment_like_'+page+'_'+post_index+'_'+comment_index).html("Unlike ["+num_likes+']');
            $('#comment_like_'+page+'_'+post_index+'_'+comment_index).attr('onClick', "unlike_comment("+profile_id+", "+post_id+", "+post_index+", "+comment_id+", "+comment_index+", "+page+", "+num_likes+");");
        }
        else
            display_error(output, 'bad_errors');
    });
}
function dislike_comment(profile_id, post_id, post_index, comment_id, comment_index, page, num_dislikes)
{
    $.post('dislike_comment.php',
    {
        post_id: post_id,
        comment_id: comment_id,
        profile_id: profile_id
    }, function(output)
    {
        if(output=='Comment disliked')
        {
            num_dislikes++;
            $('#comment_dislike_'+page+'_'+post_index+'_'+comment_index).html("Undislike ["+num_dislikes+']');
            $('#comment_dislike_'+page+'_'+post_index+'_'+comment_index).attr('onClick', "undislike_comment("+profile_id+", "+post_id+", "+post_index+", "+comment_id+", "+comment_index+", "+page+", "+num_dislikes+");");
        }
        else
            display_error(output, 'bad_errors');
    });
}
function unlike_comment(profile_id, post_id, post_index, comment_id, comment_index, page, num_likes)
{
    $.post('unlike_comment.php',
    {
        post_id: post_id,
        comment_id: comment_id,
        profile_id: profile_id
    }, function(output)
    {
        num_likes--;

        $('#comment_like_'+page+'_'+post_index+'_'+comment_index).attr('onClick', "like_comment("+profile_id+", "+post_id+", "+post_index+", "+comment_id+", "+comment_index+", "+page+", "+num_likes+");");
        if(num_likes==0)
            $('#comment_like_'+page+'_'+post_index+'_'+comment_index).html("Like");
        else
            $('#comment_like_'+page+'_'+post_index+'_'+comment_index).html("Like ["+num_likes+"]");
    });
}
function undislike_comment(profile_id, post_id, post_index, comment_id, comment_index, page, num_dislikes)
{
    $.post('undislike_comment.php',
    {
        post_id: post_id,
        comment_id: comment_id,
        profile_id: profile_id
    }, function(output)
    {
        num_dislikes--;

        $('#comment_dislike_'+page+'_'+post_index+'_'+comment_index).attr('onClick', "dislike_comment("+profile_id+", "+post_id+", "+post_index+", "+comment_id+", "+comment_index+", "+page+", "+num_dislikes+");");
        if(num_dislikes==0)
            $('#comment_dislike_'+page+'_'+post_index+'_'+comment_index).html("Dislike");
        else
            $('#comment_dislike_'+page+'_'+post_index+'_'+comment_index).html("Dislike ["+num_dislikes+"]");
    });
}
function like_video_comment(profile_id, video_id, video_index, comment_id, comment_index, page, num_likes)
{
    $.post('like_video_comment.php',
    {
        video_id: video_id,
        comment_id: comment_id,
        profile_id: profile_id
    }, function(output)
    {
        if(output=='Comment liked')
        {
            num_likes++;
            $('#comment_like_'+page+'_'+video_index+'_'+comment_index).html("Unlike ["+num_likes+']');
            $('#comment_like_'+page+'_'+video_index+'_'+comment_index).attr('onClick', "unlike_video_comment("+profile_id+", "+video_id+", "+video_index+", "+comment_id+", "+comment_index+", "+page+", "+num_likes+");");
        }
        else
            display_error(output, 'bad_errors');
    });
}
function dislike_video_comment(profile_id, video_id, video_index, comment_id, comment_index, page, num_dislikes)
{
    $.post('dislike_video_comment.php',
    {
        video_id: video_id,
        comment_id: comment_id,
        profile_id: profile_id
    }, function(output)
    {
        if(output=='Comment disliked')
        {
            num_dislikes++;
            $('#comment_dislike_'+page+'_'+video_index+'_'+comment_index).html("Undislike ["+num_dislikes+']');
            $('#comment_dislike_'+page+'_'+video_index+'_'+comment_index).attr('onClick', "undislike_video_comment("+profile_id+", "+video_id+", "+video_index+", "+comment_id+", "+comment_index+", "+page+", "+num_dislikes+");");
        }
        else
            display_error(output, 'bad_errors');
    });
}
function unlike_video_comment(profile_id, video_id, video_index, comment_id, comment_index, page, num_likes)
{
    $.post('unlike_video_comment.php',
    {
        video_id: video_id,
        comment_id: comment_id,
        profile_id: profile_id
    }, function(output)
    {
        num_likes--;

        $('#comment_like_'+page+'_'+video_index+'_'+comment_index).attr('onClick', "like_comment("+profile_id+", "+video_id+", "+video_index+", "+comment_id+", "+comment_index+", "+page+", "+num_likes+");");
        if(num_likes==0)
            $('#comment_like_'+page+'_'+video_index+'_'+comment_index).html("Like");
        else
            $('#comment_like_'+page+'_'+video_index+'_'+comment_index).html("Like ["+num_likes+"]");
    });
}
function undislike_video_comment(profile_id, video_id, video_index, comment_id, comment_index, page, num_dislikes)
{
    $.post('undislike_video_comment.php',
    {
        video_id: video_id,
        comment_id: comment_id,
        profile_id: profile_id
    }, function(output)
    {
        num_dislikes--;

        $('#comment_dislike_'+page+'_'+video_index+'_'+comment_index).attr('onClick', "dislike_comment("+profile_id+", "+video_id+", "+video_index+", "+comment_id+", "+comment_index+", "+page+", "+num_dislikes+");");
        if(num_dislikes==0)
            $('#comment_dislike_'+page+'_'+video_index+'_'+comment_index).html("Dislike");
        else
            $('#comment_dislike_'+page+'_'+video_index+'_'+comment_index).html("Dislike ["+num_dislikes+"]");
    });
}

function like_post(profile_id, post_id, poster_id, num_likes, page, post_index)
{
    $.post('like_post.php',
    {
        post_id: post_id,
        profile_id: profile_id,
        poster_id: poster_id
    }, function (output)
    {
        if(output=='Post liked')
        {
            num_likes++;
            $('#post_like_'+page+'_'+post_index).html("Unlike ["+num_likes+"]").attr('onClick', "unlike_post("+profile_id+", "+post_id+", "+poster_id+", "+num_likes+", "+page+", "+post_index+");");
        }
        else
            display_error(output, 'bad_errors');
    });
}
function unlike_post(profile_id, post_id, poster_id, num_likes, page, post_index)
{
    $.post('unlike_post.php',
    {
        post_id: post_id,
        profile_id: profile_id
    }, function (output)
    {
        num_likes--
        if(num_likes==0)
            $('#post_like_'+page+'_'+post_index).html("Like");
        else
            $('#post_like_'+page+'_'+post_index).html("Like ["+num_likes+"]");
        $('#post_like_'+page+'_'+post_index).attr('onClick', "like_post("+profile_id+", "+post_id+", "+poster_id+", "+num_likes+", "+page+", "+post_index+");");
    });
}
function dislike_post(profile_id, post_id, poster_id, num_dislikes, page, post_index)
{
    $.post('dislike_post.php',
    {
        post_id: post_id,
        profile_id: profile_id,
        poster_id: poster_id
    }, function (output)
    {
        if(output=='Post disliked')
        {
            num_dislikes++;
            $('#post_dislike_'+page+'_'+post_index).html("Undislike ["+num_dislikes+"]").attr('onClick', "undislike_post("+profile_id+", "+post_id+", "+poster_id+", "+num_dislikes+", "+page+", "+post_index+");");
        }
        else
            display_error(output, 'bad_errors');
    });
}
function undislike_post(profile_id, post_id, poster_id, num_dislikes, page, post_index)
{
    $.post('undislike_post.php',
    {
        post_id: post_id,
        profile_id: profile_id
    }, function (output)
    {
        num_dislikes--;
        if(num_dislikes==0)
            $('#post_dislike_'+page+'_'+post_index).html("Dislike");
        else
            $('#post_dislike_'+page+'_'+post_index).html("Dislike ["+num_dislikes+"]");
        $('#post_dislike_'+page+'_'+post_index).attr('onClick', "dislike_post("+profile_id+", "+post_id+", "+poster_id+", "+num_dislikes+", "+page+", "+post_index+");");
    });
}
function like_video(user_id, video_id, num_likes, page, index)
{
    $.post('like_video.php',
    {
        video_id: video_id,
        user_id: user_id
    }, function(output)
    {
        if(output=='Video liked')
        {
            num_likes++;
            $('#video_like_'+page+'_'+index).html("Unlike ["+num_likes+']');
            $('#video_like_'+page+'_'+index).attr('onClick', "unlike_video("+user_id+", "+video_id+", "+num_likes+", "+page+", "+index+");");
        }
        else
            display_error(output, "bad_errors");
    });
}
function dislike_video(user_id, video_id, num_dislikes, page, index)
{
    $.post('dislike_video.php',
    {
        video_id: video_id,
        user_id: user_id
    }, function(output)
    {
        if(output=='Video disliked')
        {
            num_dislikes++;
            $('#video_dislike_'+page+'_'+index).html("Undislike ["+num_dislikes+']');
            $('#video_dislike_'+page+'_'+index).attr('onClick', "undislike_video("+user_id+", "+video_id+", "+num_dislikes+", "+page+", "+index+");");
        }
        else
            display_error(output, "bad_errors");
    });
}
function unlike_video(user_id, video_id, num_likes, page, index)
{
    $.post('unlike_video.php',
    {
        video_id: video_id,
        user_id: user_id
    }, function(output)
    {
        num_likes--;
        if(num_likes==0)
            $('#video_like_'+page+'_'+index).html("Like");
        else
            $('#video_like_'+page+'_'+index).html("Like ["+num_likes+"]");
        
        $('#video_like_'+page+'_'+index).attr('onClick', "like_video("+user_id+", "+video_id+", "+num_likes+", "+page+", "+index+");");
        
    });
}
function undislike_video(user_id, video_id, num_dislikes, page, index)
{
    $.post('undislike_video.php',
    {
        video_id: video_id,
        user_id: user_id
    }, function(output)
    {
        num_dislikes--;
        if(num_dislikes==0)
            $('#video_dislike_'+page+'_'+index).html("Dislike");
        else
            $('#video_dislike_'+page+'_'+index).html("Dislike ["+num_dislikes+"]");
        
        $('#video_dislike_'+page+'_'+index).attr('onClick', "dislike_video("+user_id+", "+video_id+", "+num_dislikes+", "+page+", "+index+");");
        
    });
}

function delete_comment(profile_id, post_id, post_index, comment_id, comment_index, page)
{
    $.post('delete_comment.php',
    {
        post_id: post_id,
        comment_id: comment_id,
        profile_id: profile_id
    }, function(output)
    {
        if(output=='success')
            $('#comment_body_'+page+'_'+post_index+'_'+comment_index).hide();
        else
            display_error(output, 'bad_errors');
    });
}

function delete_video_comment(user_id, video_id, comment_id, page, index, comment_index)
{
    $.post('delete_comment_video.php',
    {
        video_id: video_id,
        comment_id: comment_id,
        user_id: user_id
    }, function(output)
    {
        if(output=='success')
            $('#comment_body_'+page+'_'+index+'_'+comment_index).hide();
        else
            display_error(output, 'bad_errors');
    });
}

function delete_post(post_id, user_id)
{
      $.post('delete_post.php',
      {
         post_id: post_id,
         user_id: user_id
      }, function (output)
      {
         if(output=="Success")
            window.location.replace(window.location);
         else
            display_error(output, 'bad_errors');
         close_alert_box();
      });
}
function comment(profile_id, poster_id, post_id, post_index, page, num_comments)
{
    var text=$("#comment_input_"+page+"_"+post_index).val();
    text = text.replace(/(\n)/gm, "");
    if(text!='')
    {
        $("#comment_input_"+page+"_"+post_index).val('');
        $.post('comment.php',
        {
            post_id: post_id,
            comment_text: text,
            profile_id: profile_id,
            poster_id: poster_id
        }, function (output)
        {
            var current_profile_picture=output.current_profile_picture;
            var current_name=output.current_name;
            var new_comment_id=output.new_comment_id;
            var current_user=output.current_user;
            var badges=output.badges;


            num_comments++;
            $('#comment_title_'+page+'_'+post_index).html("Comment ["+num_comments+"]");

            //displays new comment
            var new_index=0;
            while($('#comment_body_'+page+'_'+post_index+'_'+new_index).length)
                new_index++;

            text=convert_image(text_format(text), 'comment');
            
            var string="http://www.redlay.com/profile.php?user_id="+current_user;
            var name="<div class='comment_user_name_body'><a href='"+string+"' class='link' ><p class='comment_name title_color' id='comment_name_"+page+"_"+post_index+"_"+new_index+"' onmouseover=name_over(this); onmouseout=name_out(this); >"+current_name+"</p></a></div>";
            var picture="<img class='comment_profile_picture profile_picture' id='comment_profile_picture_"+page+"_"+post_index+"_"+new_index+"' src='"+current_profile_picture+"' /></a>";
            var comment="<p class='comment_text_body text_color'>"+text+"</p>";
            var timestamp="<p class='comment_timestamp text_color' id='temp_comment_timestamp_"+new_index+"'>1 second ago</p>";
            var options="<div class='comment_delete' id='comment_delete_"+page+"_"+post_index+"_"+new_index+"' onClick='delete_comment("+profile_id+", "+post_id+", "+post_index+", "+new_comment_id+", "+new_index+", "+page+");' >x</div>";
            var options_id="comment_delete_"+page+'_'+post_index+'_'+new_index;



            var comment_id="comment_body_"+page+'_'+post_index+'_'+new_index;

            var body=get_post_format(picture, name, comment, '', timestamp, options, options_id, comment_id, badges);
            if($('#comment_body_'+page+'_'+post_index+'_0').length)
                $("#comment_body_"+page+"_"+post_index).html(body+$("#comment_body_"+page+"_"+post_index).html());
            else
                $("#comment_body_"+page+"_"+post_index).html(body);
            count_time(1, '#temp_comment_timestamp_'+new_index);

            change_color();
        }, "json");
    }
    else
        display_error("Your comment seems to be empty!", 'bad_errors');
}
function comment_video(user_id, video_id, index, page, num_comments)
{
    var text=$("#comment_input_"+page+"_"+index).val();
    text = text.replace(/(\n)/gm, "");
    if(text!='')
    {
        $("#comment_input_"+page+"_"+index).val('');
        $.post('comment_video.php',
        {
            video_id: video_id,
            comment_text: text,
            user_id: user_id
        }, function (output)
        {
            var current_profile_picture=output.current_profile_picture;
            var current_name=output.current_name;
            var new_comment_id=output.new_comment_id;
            var current_user=output.current_user;
            var badges=output.badges;


            num_comments++;
            $('#comment_title_'+page+'_'+index).html("Comment ["+num_comments+"]");

            //displays new comment
            var new_index=0;
            while($('#comment_body_'+page+'_'+index+'_'+new_index).length)
                new_index++;

            text=convert_image(text_format(text), 'comment');
            
            var string="http://www.redlay.com/profile.php?user_id="+current_user;
            var name="<div class='comment_user_name_body'><a href='"+string+"' class='link' ><p class='comment_name title_color' id='comment_name_"+page+"_"+index+"_"+new_index+"' onmouseover=name_over(this); onmouseout=name_out(this); >"+current_name+"</p></a></div>";
            var picture="<img class='comment_profile_picture profile_picture' id='comment_profile_picture_"+page+"_"+index+"_"+new_index+"' src='"+current_profile_picture+"' /></a>";
            var comment="<p class='comment_text_body text_color'>"+text+"</p>";
            var timestamp="<p class='comment_timestamp text_color' id='temp_comment_timestamp_"+new_index+"'>1 second ago</p>";
            var options="<div class='comment_delete' id='comment_delete_"+page+"_"+index+"_"+new_index+"' onClick='delete_video_comment("+user_id+", "+video_id+", "+index+", "+new_comment_id+", "+new_index+", "+page+");' >x</div>";
            var options_id="comment_delete_"+page+'_'+index+'_'+new_index;



            var comment_id="comment_body_"+page+'_'+index+'_'+new_index;

            var body=get_post_format(picture, name, comment, '', timestamp, options, options_id, comment_id, badges);
            if($('#comment_body_'+page+'_'+index+'_0').length)
                $("#comment_body_"+page+"_"+index).html(body+$("#comment_body_"+page+"_"+index).html());
            else
                $("#comment_body_"+page+"_"+index).html(body);
            count_time(1, '#temp_comment_timestamp_'+new_index);

            change_color();
        }, "json");
    }
    else
        display_error("Your comment seems to be empty!", 'bad_errors');
}
function page_comment(page_id, poster_id, post_id, post_index, page, num_comments)
{
    var text=$("#comment_input_"+page+"_"+post_index).val();
    text = text.replace(/(\n)/gm, "");
    if(text!='')
    {
        $("#comment_input_"+page+"_"+post_index).val('');
        $.post('page_comment.php',
        {
            post_id: post_id,
            comment_text: text,
            page_id: page_id,
            poster_id: poster_id
        }, function (output)
        {
            var current_profile_picture=output.current_profile_picture;
            var current_name=output.current_name;
            var new_comment_id=output.new_comment_id;
            var current_user=output.current_user;
            var badges=output.badges;


            num_comments++;
            $('#comment_title_'+page+'_'+post_index).html("Comment ["+num_comments+"]");

            //displays new comment
            var new_index=0;
            while($('#comment_body_'+page+'_'+post_index+'_'+new_index).length)
                new_index++;

            text=convert_image(text_format(text), 'comment');
            
            var string="http://www.redlay.com/profile.php?user_id="+current_user;
            var name="<div class='comment_user_name_body'><a href='"+string+"' class='link' ><p class='comment_name title_color' id='comment_name_"+page+"_"+post_index+"_"+new_index+"' onmouseover=name_over(this); onmouseout=name_out(this); >"+current_name+"</p></a></div>";
            var picture="<img class='comment_profile_picture profile_picture' id='comment_profile_picture_"+page+"_"+post_index+"_"+new_index+"' src='"+current_profile_picture+"' /></a>";
            var comment="<p class='comment_text_body text_color'>"+text+"</p>";
            var timestamp="<p class='comment_timestamp text_color' id='temp_comment_timestamp_"+new_index+"'>1 second ago</p>";
            var options="<div class='comment_delete' id='comment_delete_"+page+"_"+post_index+"_"+new_index+"' onClick='delete_comment("+page_id+", "+post_id+", "+post_index+", "+new_comment_id+", "+new_index+", "+page+");' >x</div>";
            var options_id="comment_delete_"+page+'_'+post_index+'_'+new_index;



            var comment_id="comment_body_"+page+'_'+post_index+'_'+new_index;

            var body=get_post_format(picture, name, comment, '', timestamp, options, options_id, comment_id, badges);
            if($('#comment_body_'+page+'_'+post_index+'_0').length)
                $("#comment_body_"+page+"_"+post_index).html(body+$("#comment_body_"+page+"_"+post_index).html());
            else
                $("#comment_body_"+page+"_"+post_index).html(body);
            count_time(1, '#temp_comment_timestamp_'+new_index);

            change_color();
        }, "json");
    }
    else
        display_error("Your comment seems to be empty!", 'bad_errors');
}
//////////////////////functions without dislike
//function get_post_functions(like,dislike,comment)
//{
//    if(like!=''&&comment!='')
//        return "<table class='post_functions_table'><tbody><tr><td class='post_functions_unit'>"+like+"</td><td><span class='text_color'>|</span></td><td class='post_functions_post_comment_unit'>"+comment+"</td></tr></tbody></table>";
//    else if(like==''&&dislike!=''&&comment!='')
//        return "<table class='post_functions_table'><tbody><tr><td class='post_functions_unit'></td><td><span class='text_color'></span></td><td class='post_functions_unit'>"+dislike+"</td><td><span class='text_color'>|</span></td><td class='post_functions_post_comment_unit'>"+comment+"</td></tr></tbody></table>";
//    else if(like!=''&&dislike==''&&comment!='')
//        return "<table class='post_functions_table'><tbody><tr><td class='post_functions_unit'>"+like+"</td><td><span class='text_color'>|</span></td><td><span class='text_color'></span></td><td class='post_functions_post_comment_unit'>"+comment+"</td></tr></tbody></table>";
//    else if(like!=''&&dislike!=''&&comment=='')
//        return "<table class='post_functions_table'><tbody><tr><td class='post_functions_unit'>"+like+"</td><td><span class='text_color'>|</span></td><td class='post_functions_unit'>"+dislike+"</td></tr></tbody></table>";
//    else if(like==''&&dislike!=''&&comment=='')
//        return "<table class='post_functions_table'><tbody><tr><td class='post_functions_unit'>"+dislike+"</td><td><span class='text_color'></span></td><td class='post_functions_post_comment_unit'></td></tr></tbody></table>";
//    else if(like!=''&&dislike==''&&comment=='')
//        return "<table class='post_functions_table'><tbody><tr><td class='post_functions_unit'>"+like+"</td><td><span class='text_color'></span></td><td class='post_functions_unit'></td><td><span class='text_color'></span></td><td class='post_functions_post_comment_unit'></td></tr></tbody></table>";
//    else if(like==''&&dislike==''&&comment!='')
//        return "<table class='post_functions_table'><tbody><tr><td class='post_functions_post_comment_unit'>"+comment+"</td></tr></tbody></table>";
//    else
//        return "<table class='post_functions_table'><tbody><tr><td class='post_functions_unit'></td><td><span class='text_color'></span></td><td class='post_functions_unit'></td><td><span class='text_color'></span></td><td class='post_functions_post_comment_unit'></td></tr></tbody></table>";
//}
//function get_comment_functions(like,dislike)
//{
//    if(like!=''&&dislike!='')
//        return "<table class='post_functions_table'><tbody><tr><td class='post_functions_unit'>"+like+"</td><td><span class='text_color'>|</span></td><td class='post_functions_unit'>"+dislike+"</td><td><span class='text_color'></span></td><td class='post_functions_post_comment_unit'></td></tr></tbody></table>";
//    else if(like==''&&dislike!='')
//        return "<table class='post_functions_table'><tbody><tr><td class='post_functions_unit'></td><td><span class='text_color'></span></td><td class='post_functions_unit'>"+dislike+"</td><td><span class='text_color'></span></td><td class='post_functions_post_comment_unit'></td></tr></tbody></table>";
//    else if(like!=''&&dislike=='')
//        return "<table class='post_functions_table'><tbody><tr><td class='post_functions_unit'>"+like+"</td><td><span class='text_color'></span></td><td class='post_functions_unit'></td><td><span class='text_color'></span></td><td class='post_functions_post_comment_unit'></td></tr></tbody></table>";
//    else
//        return "";
//}

function get_post_functions(like, dislike, comment, timestamp)
{
    if(like!=''&&dislike!=''&&comment!='')
        return "<table class='post_functions_table'><tbody><tr><td class='post_functions_unit' style='display:inline-block;text-align:left;float:left;padding-right:5px;'>"+like+"</td><td class='post_functions_unit' style='display:inline-block;padding-left:5px;padding-right:5px;float:left;'>"+dislike+"</td><td class='post_functions_post_comment_unit'style='display:inline-block;padding-left:5px;padding-right:5px;float:left;'>"+comment+"</td><td style='text-align:right'>"+timestamp+"</td></tr></tbody></table>";
    else if(like==''&&dislike!=''&&comment!='')
        return "<table class='post_functions_table'><tbody><tr><td class='post_functions_unit' style='display:inline-block;text-align:left;padding-right:5px;float:left;'>"+dislike+"</td><td class='post_functions_post_comment_unit' style='display:inline-block;padding-left:5px;padding-right:5px;float:left;'>"+comment+"</td><td style='text-align:right'>"+timestamp+"</td></tr></tbody></table>";
    else if(like!=''&&dislike==''&&comment!='')
        return "<table class='post_functions_table'><tbody><tr><td class='post_functions_unit' style='display:inline-block;text-align:left;padding-right:5px;float:left;'>"+like+"</td><td class='post_functions_post_comment_unit' style='display:inline-block;padding-left:5px;padding-right:5px;float:left;'>"+comment+"</td><td style='text-align:right'>"+timestamp+"</td></tr></tbody></table>";
    else if(like!=''&&dislike!=''&&comment=='')
    {
        if(timestamp=="")
            var border="border-right:none;";
        else
            var border="";
        return "<table class='post_functions_table'><tbody><tr><td class='post_functions_unit' style='display:inline-block;text-align:left;padding-right:5px;float:left;'>"+like+"</td><td class='post_functions_unit' style='display:inline-block;padding-left:5px;padding-right:5px;float:left;"+border+"'>"+dislike+"</td><td style='text-align:right'>"+timestamp+"</td></tr></tbody></table>";
    }
    else if(like==''&&dislike!=''&&comment=='')
    {
        if(timestamp=="")
            var border="border-right:none;";
        else
            var border="";
        return "<table class='post_functions_table'><tbody><tr><td class='post_functions_unit' style='display:inline-block;text-align:left;padding-right:5px;float:left;"+border+"'>"+dislike+"</td><td style='text-align:right'>"+timestamp+"</td></tr></tbody></table>";
    }
    else if(like!=''&&dislike==''&&comment=='')
    {
        if(timestamp=="")
            var border="border-right:none;";
        else
            var border="";
        return "<table class='post_functions_table'><tbody><tr><td class='post_functions_unit' style='display:inline-block;text-align:left;padding-right:5px;float:left;"+border+"'>"+like+"</td><td style='text-align:right'>"+timestamp+"</td></tr></tbody></table>";
    }
    else if(like==''&&dislike==''&&comment!='')
    {
        if(timestamp=="")
            var border="border-right:none;";
        else
            var border="";
        return "<table class='post_functions_table'><tbody><tr><td class='post_functions_post_comment_unit' style='display:inline-block;text-align:left;padding-right:5px;float:left;"+border+"'>"+comment+"</td><td style='text-align:right'>"+timestamp+"</td></tr></tbody></table>";
    }
    else if(like==''&&dislike==''&&comment==''&&timestamp!='')
        return "<table class='post_functions_table'><tbody><tr><td style='text-align:right'>"+timestamp+"</td></tr></tbody></table>";
    else
        return "<table class='post_functions_table'><tbody><tr><td></td></tr></tbody></table>";
}
function get_comment_functions(like,dislike, timestamp)
{
    if(like!=''&&dislike!='')
    {
//        if(timestamp=="")
            var border="border-right:none;";
//        else
//            var border="";
        return "<table class='comment_functions_table'><tbody><tr><td><table><tbody><tr><td class='post_functions_unit' style='display:inline-block;text-align:left;float:left;padding-right:5px;'>"+like+"</td><td class='post_functions_unit' style='display:inline-block;padding-left:5px;padding-right:5px;float:left;"+border+"'>"+dislike+"</td></tr></tbody></table></td><td style='text-align:right;'>"+timestamp+"</td></tr></tbody></table>";
    }
    else if(like==''&&dislike!='')
    {
//        if(timestamp=="")
            var border="border-right:none;";
//        else
//            var border="";
        return "<table class='comment_functions_table'><tbody><tr><td><table><tbody><tr><td class='post_functions_unit' style='display:inline-block;text-align:left;float:left;padding-right:5px;"+border+"'>"+dislike+"</td></tr></tbody></table></td><td style='text-align:right;'>"+timestamp+"</td></tr></tbody></table>";
    }
    else if(like!=''&&dislike=='')
    {
//        if(timestamp=="")
            var border="border-right:none;";
//        else
//            var border="";
        return "<table class='comment_functions_table'><tbody><tr><td><table><tbody><tr><td class='post_functions_unit' style='display:inline-block;text-align:left;float:left;padding-right:5px;"+border+"'>"+like+"</td></tr></tbody></table></td><td style='text-align:right;'>"+timestamp+"</td></tr></tbody></table>";
    }
    else
        return "<table class='comment_functions_table'><tbody><tr><td style='text-align:right;'>"+timestamp+"</td></tr></tbody></table>";
}
function delete_photo_comment(user_id, picture_id, photo_index, comment_id, comment_index, page, type)
{
    $.post('delete_photo_comment.php',
    {
        profile_id: user_id,
        type: type,
        photo_id: picture_id,
        comment_id: comment_id
    }, function(output)
    {
        if(output=='Success')
            $('#comment_body_'+page+'_'+photo_index+'_'+comment_index).hide();
        else
            display_error(output, 'bad_errors');
    });
}
function copy_picture(picture_id, user_id, type)
{
    //gets the checked checkboxes and their values
    var audience_options_list=new Array();
    var num=0;
    var num2=0;
    while($('#copy_extra_unit_checkbox_'+num2).length)
    {
        if($('#copy_extra_unit_checkbox_'+num2).data('checked')=='yes')
        {
            audience_options_list[num]=$('#copy_extra_unit_checkbox_'+num2).data('group_name');
            num++;
        }
        num2++;
    }


    $.post('copy_picture.php',
    {
        picture_id: picture_id,
        user_id: user_id,
        type: type,
        description: $('#upload_picture_description').val(),
        audience:audience_options_list
    }, function(output)
    {
        if(output=='Image copied')
        {
            display_error(output, 'good_errors');
            close_alert_box();
        }
        else
            display_error(output, 'bad_errors');
    });
}
function display_actual_video(vid_id)
{
    $(vid_id).html($(vid_id).data('vid_embed'));
}
function display_copy_picture_menu(picture_id, user_id, type)
{
    var body="<table style='width:100%;'><tbody><tr><td><p class='text_color' style='margin:0px;'>This photo will be copied to your account.</p></td></tr><tr><td><textarea  id='upload_picture_description' class='input_box' maxlength='1000' placeholder='Describe the photo...' ></textarea></td></tr></tbody></table>";
    var confirm="<input type='button' class='button red_button' value='Copy' id='copy_picture_confirm'/>";
    display_alert("Copy", body, 'copy_extra_unit', 'copy_gif', confirm);
        display_groups('copy_extra_unit');

    $('#upload_picture_description').attr({'onFocus': "input_in(this);", 'onBlur': "input_out(this);"});
    $('#copy_picture_confirm').attr('onClick', "copy_picture('"+picture_id+"', "+user_id+", '"+type+"');");

    $('#copy_gif').hide();
    change_color();
}
function add_user(user_id)
{
   //gets the checked checkboxes and their values
   var audience_options_list=new Array();
   var num=0;
   var num2=0;
   while($('#add_groups_unit_checkbox_'+num2).length)
   {
      if($('#add_groups_unit_checkbox_'+num2).data('checked')=='yes')
      {
            audience_options_list[num]=$('#add_groups_unit_checkbox_'+num2).data('group_name');
            num++;
      }
      num2++;
   }

    $.post('add_user.php',
    {
       user_id: user_id,
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
function display_add_menu(user_id)
{
     var title="<a class='title title_color'>Add</p>";
     var body="<textarea maxlength='250' onFocus='input_in(this);' onBlur='input_out(this);' class='input_box' placeholder='Tell this person how you know them (optional)...' id='add_user_message_input'></textarea>";
     var extra_id="add_groups_unit";
     var load_id='add_load';
     var confirm="<input class='button red_button' type='button' value='Add' onClick='add_user("+user_id+");'/>";
     
     
            
     display_alert(title, body, extra_id, load_id, confirm);
     display_groups('add_groups_unit', 'add');
     $('#add_load').hide();
            
    change_color();
}
function display_accept_menu(ID, name)
{
    var title="<p class='title title_color' style='margin:5px;'>Add "+name+" to any group</p>";
    var body="<div id='groups_category_box'></div>";
    var confirm="<input type='button' class='button green_button' onClick='accept_request("+ID+");' value='Add' />";

    display_alert(title, body, 'add_extra_unit', 'add_gif', confirm);
    display_groups('groups_category_box', 'add');

    $('#add_gif').hide();
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


    $.post('add.php',
    {
        user_id: ID,
        audience_options_list: audience_options_list
    }, function (output)
    {
        if(output=="User added!")
        {
            display_error(output, 'good_errors');
            window.location.replace(window.location);
        }
        else
            display_error(output, 'bad_errors');
    });
}

function decline_request(ID)
{
    $.post('decline_add.php',
    {
        user_id: ID
    }, function (output)
    {
        if(output=="User add request deleted!")
            window.location.replace(window.location);
        else
            $('#friend_request_alert_errors').html(output).addClass('bad_errors').show();
    });
}
function like_photo(picture_id, user_id, type, page, index, num_likes)
{
    $.post('like_photo.php',
    {
        user_id: user_id,
        picture_id: picture_id,
        type: type
    }, function(output)
    {
        num_likes++;
        $('#home_photo_like_'+page+'_'+index).html("Unlike ["+num_likes+']').attr('onClick', "unlike_photo('"+picture_id+"', "+user_id+", '"+type+"', "+page+", "+index+", "+num_likes+");");
    });
}
function dislike_photo(picture_id, user_id, type, page, index, num_dislikes)
{
    $.post('dislike_photo.php',
    {
        user_id: user_id,
        picture_id: picture_id,
        type: type
    }, function(output)
    {
        num_dislikes++;
        $('#home_photo_dislike_'+page+'_'+index).html("Undislike ["+num_dislikes+']').attr('onClick', "undislike_photo('"+picture_id+"', "+user_id+", '"+type+"', "+page+", "+index+", "+num_dislikes+");");
    });
}

function unlike_photo(picture_id, user_id, type, page, index, num_likes)
{
    $.post('unlike_photo.php',
    {
        user_id: user_id,
        picture_id: picture_id,
        type: type
    }, function(output)
    {
        num_likes--;
        if(num_likes==0)
            $('#home_photo_like_'+page+'_'+index).html("Like").attr('onClick', "like_photo('"+picture_id+"', "+user_id+", '"+type+"', "+page+", "+index+", "+num_likes+");");
        else
            $('#home_photo_like_'+page+'_'+index).html("Like ["+num_likes+']').attr('onClick', "like_photo('"+picture_id+"', "+user_id+", '"+type+"', "+page+", "+index+", "+num_likes+");");
    });
}
function undislike_photo(picture_id, user_id, type, page, index, num_dislikes)
{
    $.post('undislike_photo.php',
    {
        user_id: user_id,
        picture_id: picture_id,
        type: type
    }, function(output)
    {
        num_dislikes--;
        if(num_dislikes==0)
            $('#home_photo_dislike_'+page+'_'+index).html("Dislike").attr('onClick', "dislike_photo('"+picture_id+"', "+user_id+", '"+type+"', "+page+", "+index+", "+num_dislikes+");");
        else
            $('#home_photo_dislike_'+page+'_'+index).html("Dislike ["+num_dislikes+']').attr('onClick', "dislike_photo('"+picture_id+"', "+user_id+", '"+type+"', "+page+", "+index+", "+num_dislikes+");");
    });
}
function comment_photo(picture_id, user_id, page, index, num_comments, input_id)
{
    var comment=$("#"+input_id).val();

    comment = comment.replace(/(\n)/gm, "");
    if(comment!='')
    {
        $('#'+input_id).val('');
        $.post('comment_picture.php',
        {
            user_id: user_id,
            picture_id: picture_id,
            comment: comment,
            type: 'user'
        }, function (output)
        {
            var current_profile_picture=output.current_profile_picture;
            var current_name=output.current_name;
            var new_comment_id=output.new_comment_id;
            var current_user=output.current_user;
            var badges=output.badges;

            num_comments++;
            $('#comment_title_'+page+'_'+index).html("Comment ["+num_comments+"]");

            //displays new comment
            var new_index=0;
            while($('#comment_body_'+page+'_'+index+'_'+new_index).length)
                new_index++;

            var string="http://www.redlay.com/profile.php?user_id="+current_user;
            var name="<div class='comment_user_name_body'><a href='"+string+"' class='link' ><p class='comment_name title_color' id='comment_name_"+page+"_"+index+"_"+new_index+"' onmouseover=name_over(this); onmouseout=name_out(this); >"+current_name+"</p></a></div>";
            var picture="<a href='"+string+"' class='link'><img class='comment_profile_picture profile_picture' id='comment_profile_picture_"+page+"_"+index+"_"+new_index+"' src='"+current_profile_picture+"' /></a>";
            var comment_body="<p class='comment_text_body text_color'>"+comment+"</p>";
            var timestamp="<p class='comment_timestamp text_color' id='temp_photo_comment_"+new_comment_id+"'>1 second ago</p>";
            var options="<div class='comment_delete' id='comment_delete_"+page+"_"+index+"_"+new_index+"'  >x</div>";
            var options_id="comment_options_"+page+'_'+index+'_'+new_index;


            var comment_id="comment_body_"+page+'_'+index+'_'+new_index;

            var body=get_post_format(picture, name, comment_body, '', timestamp, options, options_id, comment_id, badges);
            if($('#comment_body_'+page+'_'+index+'_0').length)
                $("#comment_body_"+page+"_"+index).html(body+$("#comment_body_"+page+"_"+index).html());
            else
                $("#comment_body_"+page+"_"+index).html(body);
            
            $('#comment_delete_'+page+'_'+index+'_'+new_index).attr('onClick', "delete_photo_comment("+user_id+", '"+picture_id+"', "+index+", "+new_comment_id+", "+new_index+", "+page+", 'user');");
            count_time(1, '#temp_photo_comment_'+new_comment_id);

            change_color();
        }, "json");
    }
    else
        display_error("Your comment seems to be empty", 'bad_errors');
}

function display_message_box(ID)
{
   var title="<p class='title title_color' style='margin:5px;'>Message</p>";
   var body="<textarea  id='message_body' class='input_box' maxlength='1000' placeholder='Send a message...' style='width:100%;'></textarea>";
   var confirm="<input type='button' class='button green_button' onClick=message("+ID+"); value='Send' />";
   display_alert(title, body, 'message_extra_unit', 'message_gif', confirm);
   $('#message_body').attr({'onFocus': "input_in(this);", 'onBlur': "input_out(this);"});


   $('#message_gif').hide();
   change_color();
}

function message(ID)
{
    var message=$('#message_body').val();
    $.post('message_user.php', 
    {
        user_id: ID,
        message: message
    }, function(output)
    {
        var current_user=output.current_user;
        var user_name=output.user_name;
        var errors=output.errors;
        var profile_picture=output.profile_picture;
        var new_message=output.new_message;
        
        if(errors=='Message sent!')
        {
            display_error(errors, 'good_errors');
            $('#message_body').val('');
            close_alert_box();
            
            //if at messages.php
            if($('#message_content').length)
            {
                var index=0;
                while($('#'+ID+'_message_'+index).length)
                    index++;

                new_message=convert_image(text_format(new_message), 'post');
                var name="<div class='user_name_body'><a href='http://www.redlay.com/profile.php?user_id="+current_user+"' class='message_name_link'><span class='user_name_sent' onmouseover=name_over(this); onmouseout=name_out(this); >"+user_name+"</span></a></div>";
                var picture="<div class='message_messages'><a href='http://www.redlay.com/profile.php?user_id="+current_user+"'><img class='profile_picture_message profile_picture' src='"+profile_picture+"' /></a>";
                var message_text="<span class='message'>"+new_message+"</span>";
                var timestamp="<span class='message_timestamp' id='message_timestamp_"+ID+"_messages_"+current_user+"_"+index+"'>1 second ago</span>";


                var body=get_post_format(picture, name, message_text, '', timestamp, '', ID+'_message_'+index)
                $('#message_content').html(body+$('#message_content').html());

                count_time(1, '#message_timestamp_'+ID+'_messages_'+current_user+'_'+index);
            }
            //display_messages(ID);
            change_color();
        }
        else
            display_error(errors, 'bad_errors');
    }, "json");
}

//fix this
function like_photo_comment(user_id, picture_id, photo_index, comment_id, comment_index, page, num_comment_likes)
{
    $.post('like_photo_comment.php',
    {
        user_id: user_id,
        picture_id: picture_id,
        type: 'user',
        comment_id: comment_id
    }, function(output)
    {
        num_comment_likes++;
        $('#comment_like_'+page+'_'+photo_index+'_'+comment_index).html("Unlike ["+num_comment_likes+"]").attr('onClick', "unlike_photo_comment("+user_id+", '"+picture_id+"', "+photo_index+", "+comment_id+", "+comment_index+", "+page+", "+num_comment_likes+");");
    });
}
function dislike_photo_comment(user_id, picture_id, photo_index, comment_id, comment_index, page, num_comment_dislikes)
{
    $.post('dislike_photo_comment.php',
    {
        user_id: user_id,
        picture_id: picture_id,
        type: 'user',
        comment_id: comment_id
    }, function(output)
    {
        num_comment_dislikes++;
        $('#comment_dislike_'+page+'_'+photo_index+'_'+comment_index).html("Undislike ["+num_comment_dislikes+"]").attr('onClick', "undislike_photo_comment("+user_id+", '"+picture_id+"', "+photo_index+", "+comment_id+", "+comment_index+", "+page+", "+num_comment_dislikes+");");
    });
}

function unlike_photo_comment(user_id, picture_id, photo_index, comment_id, comment_index, page, num_comment_likes)
{
    $.post('unlike_photo_comment.php',
    {
        user_id: user_id,
        picture_id: picture_id,
        type: 'user',
        comment_id: comment_id
    }, function(output)
    {
        num_comment_likes--;
        if(num_comment_likes==0)
            $('#comment_like_'+page+'_'+photo_index+'_'+comment_index).html("Like").attr('onClick', "like_photo_comment("+user_id+", '"+picture_id+"', "+photo_index+", "+comment_id+", "+comment_index+", "+page+", "+num_comment_likes+");");
        else
            $('#comment_like_'+page+'_'+photo_index+'_'+comment_index).html("Like ["+num_comment_likes+']').attr('onClick', "like_photo_comment("+user_id+", '"+picture_id+"', "+photo_index+", "+comment_id+", "+comment_index+", "+page+", "+num_comment_likes+");");
    });
}
function undislike_photo_comment(user_id, picture_id, photo_index, comment_id, comment_index, page, num_comment_dislikes)
{
    $.post('undislike_photo_comment.php',
    {
        user_id: user_id,
        picture_id: picture_id,
        type: 'user',
        comment_id: comment_id
    }, function(output)
    {
        num_comment_dislikes--;
        if(num_comment_dislikes==0)
            $('#comment_dislike_'+page+'_'+photo_index+'_'+comment_index).html("Dislike").attr('onClick', "dislike_photo_comment("+user_id+", '"+picture_id+"', "+photo_index+", "+comment_id+", "+comment_index+", "+page+", "+num_comment_dislikes+");");
        else
            $('#comment_dislike_'+page+'_'+photo_index+'_'+comment_index).html("Dislike ["+num_comment_dislikes+']').attr('onClick', "dislike_photo_comment("+user_id+", '"+picture_id+"', "+photo_index+", "+comment_id+", "+comment_index+", "+page+", "+num_comment_dislikes+");");
    });
}
function convert_time(seconds)
{
    if(seconds<2678400)
    {
        if(seconds>=3600)
        {
            if(seconds>604800)
                //old number format
                var new_time=format_number((seconds/86400))+" days ago";
            else if(seconds>=86400&&seconds<604800)
            {
                //old number format
                var num_days=format_number((seconds/86400));
                var num_hours=format_number(((seconds%86400)/3600));

                if(num_days!=1)
                    var days=num_days+" days";
                else
                    var days="1 day";

                if(num_hours!=0)
                {
                    if(num_hours!=1)
                        var hours=num_hours+" hours ago";
                    else
                        var hours="1 hour ago";
                }
                else
                {
                    //old number format
                    var minutes=format_number(((seconds%3600)/60));
                    hours=minutes+" minutes ago";
                }

                var new_time=days+" "+hours;
            }
            else if(seconds>=7200)
            {
                //old number format
                var minutes=format_number((seconds%3600)/60);
                if(minutes!=0)
                    var new_time=format_number((seconds/3600))+" hours "+minutes+" minutes ago";
                else
                    var new_time=format_number((seconds/3600))+" hours ago";
            }
            else if(seconds>=3660&&seconds<7200)
            {
                //old number format
                var minutes=format_number((seconds%3600)/60);
                if(minutes!=0)
                    var new_time="1 hour and "+minutes+" minutes ago";
                else
                    var new_item="1 hour ago";
            }
            else
                var new_time="1 hour ago";
        }
        else
        {
            if(seconds>=120)
            {
                //old number format
                var new_time=format_number((seconds/60))+" minutes ago";
            }
            else if(seconds>=60&&seconds<120)
                var new_time="1 minute ago";
            else if(seconds<60)
                var new_time=seconds+" seconds ago";
        }
    } 
    
    return new_time;
}
function count_time(seconds, id, prev_time)
{
    if($(id).length)
    {
        if(prev_time==undefined)
            prev_time="";
        
        seconds=parseInt(seconds);
        if(seconds<0)
            seconds=0;

        if(seconds<2678400)
        {
            var new_time=convert_time(seconds);

            if(new_time!=prev_time)
            {
                if(prev_time==''||$(id).html()==prev_time)
                    $(id).html(new_time);
            }

            seconds++;

            //creates recursion
            if($(id).length)
            {
                setTimeout(function(){
                    count_time(seconds, id, new_time);
                }, 1000);
            }
        }
    }
}
function format_number(number)
{
    var temp=number;
    
    while(number>=1000)
        number-=1000;
    
    while(number>=100)
        number-=100;
    
    while(number>=10)
        number-=10;
    
    while(number>=1)
        number-=1;
    
    return temp-number;   
}


function initialize_thumbnail_selection(image_id, width, height)
{
    //if image's width is bigger than its height
    if(width>=height)
    {
        if(width/height>=1.5)
        {
            var ratio=width/450;
            var new_width=format_number(width/ratio);
            var new_height=format_number(height/ratio);
            
            $('.draggable_thumbnail_selector').height(new_height);
            $('.draggable_thumbnail_selector').width(new_height);
        }
        else
        {
           var ratio=height/300;
            var new_width=format_number(width/ratio);;
            var new_height=format_number(height/ratio);
            
            $('.draggable_thumbnail_selector').height(300);
            $('.draggable_thumbnail_selector').width(300);
        }


        $('#thumbnail_image_preview').css('max-height', '150px');
    }

    //if image's height is bigger than its width
    else 
    {
           var ratio=height/300;
            var new_width=format_number(width/ratio);;
            var new_height=format_number(height/ratio);
            
            $('.draggable_thumbnail_selector').height(300);
            $('.draggable_thumbnail_selector').width(300);
            

        $('#thumbnail_image_preview').css('max-width', '150px');
    }
    $('#picture_preview_body').width(new_width).height(new_height);


    $('.draggable_thumbnail_selector').draggable(
    {
        containment: '#picture_preview_body',
        drag: function()
        {
            if($('#upload_photo_preview_image').width()>$('#upload_photo_preview_image').height())
            {
                var width=$('#upload_photo_preview_image').width();
                var small_width=$('#thumbnail_image_preview').width();


                var position=$('.draggable_thumbnail_selector').position();
                var ratio=width/small_width;
                $('#thumbnail_image_preview').css('left', Math.round(position.left/(ratio))*-1);
            }
            else
            {
                var height=$('#upload_photo_preview_image').height();
                var small_height=$('#thumbnail_image_preview').height();


                var position=$('.draggable_thumbnail_selector').position();
                var ratio=height/small_height;
                $('#thumbnail_image_preview').css('top', Math.round(position.top/(ratio))*-1);
            }    

        }
    });

    $('#thumbnail_info').html("<p style='color:<?php echo $color; ?>;'>Set the thumbnail for this picture</p><table><tbody><tr><td><input class='button red_button' type='button' onClick=set_picture_thumbnail('"+image_id+"'); value='Set'/></td><td><input class='button gray_button' type='button' value='Cancel' onClick='close_thumbnail_selection();'/></td></tr></tbody></table>");
    show_alert_box();
}
function set_picture_thumbnail(image_id)
{
    var left=parseFloat($('.draggable_thumbnail_selector').css('left'));
    var top=parseFloat($('.draggable_thumbnail_selector').css('top'));

    var width=parseFloat($('#upload_photo_preview_image').width());
    var height=parseFloat($('#upload_photo_preview_image').height());
    $.post('profile_query.php',
    {
        num:14,
        image_id: image_id,
        top:top,
        left:left,
        width:width,
        height:height
    }, function(output)
    {
        if(output=='Thumbnail set')
            display_error(output, 'good_errors');
        else
            display_error(output, 'bad_errors');
        close_thumbnail_selection();
        
        
        display_pictures(1);
    });
}
function close_thumbnail_selection()
{
    $('#photo_upload_message').animate({
        opacity:'0'
    }, 500,function()
    {
        $('#photo_upload_message').html('').css('height', '').hide();
    });
    
    $('#upload_photo_preview').animate({
        height:0
    }, 1000,function()
    {
        $('#upload_photo_preview').html('').css('height', '').hide();
    });
    
    $('#upload_picture_description').show();
    show_alert_box();
}
function disable_photo_upload()
{
    $('#photo_upload_button').attr('onChange', "undisable_photo_upload();");
    $('#photo_upload_submit').attr('class', 'red_button_disabled');
}
function undisable_photo_upload()
{
    $('#photo_upload_submit').attr('class', 'button red_button');
}

function display_photo_upload_box()
{
    display_dim();
    
    $('.alert_box_inside').html("<table id='photo_upload_options_table'><tbody><tr><td colspan='2'><p class='title_color title' style='text-align:center;'>Upload Photo</p></td></tr><tr>  <td id='file_photo_upload_unit' style='width:50%;cursor:pointer;' ></td><td id='url_photo_upload_unit' style='border-left:1px solid gray;width:50%;cursor:pointer;'></td>  </tr></tbody></table>    <div id='photo_computer_uploads'></div><div id='photo_url_uploads'></div>").css('width', '500px');
    $('#file_photo_upload_unit').html("<table style='margin: 0 auto;'><tbody><tr><td style='text-align:center;'>  <table><tbody><tr><td><span class='text_color' style='font-size:14px;'>Upload a photo from your computer</span></td></tr><tr><td><img src='http://pics.redlay.com/pictures/photo_upload_computer.png' style='height:70px;'/></td></tr></tbody></table>  </td></tr><tr></tr></tbody></table>");
    $('#file_photo_upload_unit').attr('onClick', "initialize_file_photo_upload();");
    $('#url_photo_upload_unit').html("<table style='margin: 0 auto;'><tbody><tr><td style='text-align:center;'>  <table><tbody><tr><td><span class='text_color' style='font-size:14px;'>Upload from a URL</span></td></tr><tr><td><img src='http://pics.redlay.com/pictures/photo_upload_url.png' style='height:70px;'/></td></tr></tbody></table>  </td></tr><tr></tr></tbody></table>");
    $('#url_photo_upload_unit').attr('onClick', "initialize_URL_photo_upload();");
    $('#photo_computer_uploads').hide();
    $('#photo_url_uploads').hide();
    show_alert_box();
    change_color();
}

function initialize_file_photo_upload()
{
    $('#photo_upload_options_table').hide();
    if(!$('#photo_upload_form').length)
    {
        $('#photo_computer_uploads').show();
        $('#photo_computer_uploads').html("<form method='post' action='upload_picture.php' enctype='multipart/form-data' target='photo_upload_iframe' id='photo_upload_form'><table class='alert_box_table' id='upload_photo_table'><tbody><tr class='alert_box_row' id='upload_photo_row_1'></tr><tr class='alert_box_row' id='upload_photo_row_2'></tr><tr class='alert_box_row' id='upload_photo_row_3'></tr><tr class='alert_box_row' id='upload_photo_row_5' ></tr><tr class='alert_box_row' id='upload_photo_row_4' ></tr></tbody></table></form>");
        $('#upload_photo_row_1').html("<td class='upload_photo_unit alert_box_title_unit' colspan='4'><p class='alert_box_title' class='text'>Upload a photo</p></td>");
        
            var max_quality="<td style='width: 120px;'><table><tbody><tr><td><span>Max quality</span></td><td><input type='checkbox' id='quality_checkbox' name='photo_quality' value='yes'/></td></tr></tbody></table></td>";
        
        $('#upload_photo_row_2').html("<td class='upload_photo_unit' colspan='3'><input type='file' id='photo_upload_button' class='file_input' name='image'/></td>"+max_quality);
        $('#upload_photo_row_3').html("<td class='upload_photo_unit' colspan='4'><textarea name='upload_picture_description' id='upload_picture_description' class='input_box' maxlength='1000' placeholder='Describe the photo...'></textarea></td>");
        $('#upload_picture_description').attr({'onFocus': "input_in(this);", 'onBlur': "input_out(this);"});
        $('#upload_photo_row_4').html("<td colspan='4'><table style='width:100%;'><tbody><tr><td class='upload_photo_unit alert_box_confirmation_row_unit_left'><div class='select_box' id='photo_audience_box'></div></td><td class='upload_photo_unit alert_box_load_unit'><img class='load_gif' id='upload_photo_gif' src='http://pics.redlay.com/pictures/load.gif'/></td><td class='upload_photo_unit alert_box_confirm_unit' ><input type='submit' class='button red_button' id='photo_upload_submit' value='Upload' /></td><td class='upload_photo_unit alert_box_cancel_unit' ><input type='button' class='button gray_button' id='photo_upload_cancel' onClick=close_alert_box(); value='Close' /></td></tr></tbody></table></td>");
        display_groups('photo_audience_box');
        $('#upload_photo_gif').hide();
        $('#upload_photo_row_5').html("<td colspan='4'><p id='photo_upload_message'></p><div id='upload_photo_preview'></div></td>");
        $('#photo_upload_message').hide();

        disable_photo_upload();
        $('#photo_upload_submit').attr('onClick', "{$('#upload_photo_gif').show();disable_photo_upload();}");
        change_color();
    }
    else
        $('#photo_computer_uploads').show();
    
//    display_dim();
//    $('.alert_box_inside').html("<form method='post' action='upload_picture.php' enctype='multipart/form-data' target='photo_upload_iframe'><table class='alert_box_table' id='upload_photo_table'><tbody><tr class='alert_box_row' id='upload_photo_row_1'></tr><tr class='alert_box_row' id='upload_photo_row_2'></tr><tr class='alert_box_row' id='upload_photo_row_3'></tr><tr class='alert_box_row' id='upload_photo_row_5' ></tr><tr class='alert_box_row' id='upload_photo_row_4' ></tr></tbody></table></form>");
//        $('#upload_photo_row_1').html("<td class='upload_photo_unit alert_box_title_unit' colspan='4'><p class='alert_box_title' class='text'>Upload a photo</p></td>");
//        $('#upload_photo_row_2').html("<td class='upload_photo_unit' colspan='3'><input type='file' id='photo_upload_button' class='file_input' name='image'/></td><?php if(has_redlay_gold($_SESSION['id'], 'photo_quality')) echo "<td style='width: 120px;'><table><tbody><tr><td><span>Max quality</span></td><td><input type='checkbox' id='quality_checkbox' name='photo_quality' value='yes'/></td></tr></tbody></table></td>"; ?>");
//        $('#upload_photo_row_3').html("<td class='upload_photo_unit' colspan='4'><textarea name='upload_picture_description' id='upload_picture_description' class='input_box' maxlength='1000' placeholder='Describe the photo...'></textarea></td>");
//            $('#upload_picture_description').attr({'onFocus': "input_in(this);", 'onBlur': "input_out(this);"});
//        $('#upload_photo_row_4').html("<td colspan='4'><table style='width:100%;'><tbody><tr><td class='upload_photo_unit alert_box_confirmation_row_unit_left'><div class='select_box' id='photo_audience_box'></div></td><td class='upload_photo_unit alert_box_load_unit'><img class='load_gif' id='upload_photo_gif' src='http://pics.redlay.com/pictures/load.gif'/></td><td class='upload_photo_unit alert_box_confirm_unit' ><input type='submit' class='red_button' id='photo_upload_submit' value='Upload' /></td><td class='upload_photo_unit alert_box_cancel_unit' ><input type='button' class='gray_button' id='photo_upload_cancel' onClick=close_alert_box(); value='Close' /></td></tr></tbody></table></td>");
//            display_groups('photo_audience_box');
//            $('#upload_photo_gif').hide();
//        $('#upload_photo_row_5').html("<td colspan='4'><p id='photo_upload_message'></p><div id='upload_photo_preview'></div></td>");
//        $('#photo_upload_message').hide();
//
//        disable_photo_upload();
//        $('#photo_upload_submit').attr('onClick', "{$('#upload_photo_gif').show();disable_photo_upload();}");
//
//        show_alert_box();
//    change_color();
}
function initialize_URL_photo_upload(has_gold)
{
    $('#photo_upload_options_table').hide();
    if(!$('#photo_url_upload_table').length)
    {
        $('#photo_url_uploads').show();
        $('#photo_url_uploads').html("<table class='alert_box_table' id='photo_url_upload_table'><tbody><tr class='alert_box_row' id='upload_photo_row_1'></tr><tr class='alert_box_row' id='upload_photo_row_2'></tr><tr class='alert_box_row' id='upload_photo_row_3'></tr><tr class='alert_box_row' id='upload_photo_row_5' ></tr><tr class='alert_box_row' id='upload_photo_row_4' ></tr></tbody></table>");
        $('#upload_photo_row_1').html("<td class='upload_photo_unit alert_box_title_unit' colspan='4'><p class='alert_box_title' class='text'>Upload a photo</p></td>");

        var max_quality="<td style='width: 120px;'><table><tbody><tr><td><span>Max quality</span></td><td><input type='checkbox' id='quality_checkbox' name='photo_quality' value='yes'/></td></tr></tbody></table></td>";

        $('#upload_photo_row_2').html("<td class='upload_photo_unit' colspan='3'><input type='text' style='width:300px;' id='photo_upload_input' class='input_box' placeholder='http://www.example.com/photo.jpg' onFocus='input_in(this);' onBlur='input_out(this);' /></td>"+max_quality);
        $('#upload_photo_row_3').html("<td class='upload_photo_unit' colspan='4'><textarea name='upload_picture_description' id='upload_picture_description' class='input_box' maxlength='1000' placeholder='Describe the photo...'></textarea></td>");
        $('#upload_picture_description').attr({'onFocus': "input_in(this);", 'onBlur': "input_out(this);"});
        $('#upload_photo_row_4').html("<td colspan='4'><table style='width:100%;'><tbody><tr><td class='upload_photo_unit alert_box_confirmation_row_unit_left'><div class='select_box' id='photo_audience_box'></div></td><td class='upload_photo_unit alert_box_load_unit'><img class='load_gif' id='upload_photo_gif' src='http://pics.redlay.com/pictures/load.gif'/></td><td class='upload_photo_unit alert_box_confirm_unit' ><input type='button' class='button red_button' id='photo_upload_submit' value='Upload' /></td><td class='upload_photo_unit alert_box_cancel_unit' ><input type='button' class='button gray_button' id='photo_upload_cancel' onClick=close_alert_box(); value='Close' /></td></tr></tbody></table></td>");
        display_groups('photo_audience_box');
        $('#upload_photo_gif').hide();
        $('#upload_photo_row_5').html("<td colspan='4'><p id='photo_upload_message'></p><div id='upload_photo_preview'></div></td>");
        $('#photo_upload_message').hide();

        $('#photo_upload_submit').attr('onClick', "{upload_url_photo();$('#upload_photo_gif').show();}");
        change_color();
    }
    else
        $('#photo_url_uploads').show();
}

function upload_url_photo()
{
    var description=$('#upload_picture_description').val();
    var url=$('#photo_upload_input').val();
    if($('#quality_checkbox').length&&$('#quality_checkbox').is(":checked"))
        var max_quality='true';
    else
        var max_quality='false';
    
    $.post('upload_picture_url.php',
    {
        url:url,
        description:description,
        max_quality: max_quality
    }, function(output)
    {
        var current_user=output.current_user;
        var photo_id=output.photo_id;
        var type=output.type;
        var width=output.width;
        var height=output.height;
        var errors=output.errors;
        //alert("Current_user: "+current_user+" | photo_id: "+photo_id+" | type: "+type+" | width: "+width+" | height: "+height);
        
        if(errors=="Photo uploaded")
        {
            display_error(errors, 'good_errors');
            
            //hides load gif
            $('#upload_photo_gif').hide();
            
            //sets up preview HTML
            $('#upload_photo_preview').html("<div id='picture_preview_body'><div class='draggable_thumbnail_selector'></div><img id='upload_photo_preview_image' src='https://s3.amazonaws.com/bucket_name/users/"+current_user+"/photos/"+photo_id+"."+type+"' /></div><div id='thumbnail_info_body'><div id='thumbnail_preview_body'><div id='thumbnail_preview_window'><img id='thumbnail_image_preview' src='https://s3.amazonaws.com/bucket_name/users/"+current_user+"/photos/"+photo_id+"."+type+"' /></div></div><div id='thumbnail_info'></div></div>").show();
            
            //clears description
            $('#upload_picture_description').val('').hide();
            
            //clears URL input
            $('#photo_upload_input').val('');
            
            //unchecks max quality checkbox (if it exists and is checked)
            if($('#quality_checkbox').length&&$('#quality_checkbox').is(":checked"))
            $('#quality_checkbox').attr('checked', false);
        
            $('#photo_upload_message').html("<span class='text_color'>Uploaded: </span>").show();
            
            //disable_photo_upload();
            show_alert_box();
            initialize_thumbnail_selection(photo_id, width, height);
        }
        else
        {
            display_error(errors, "bad_errors");
        }    
    }, "json");
}
function submit_bio()
{
   $.post('settings_query.php',
   {
      num:4,
      new_bio: $('#change_bio_text').val()
   },
   function(output)
   {
      if(output=='Change successful!')
         display_error(output, 'good_errors');
      else
         display_error(output, 'bad_errors');
   });
}
function change_school()
{
   $.post('settings_query.php',
   {
      num:7,
      school: $('#school').val()
   }, function(output)
   {
     if(output=='Change successful!')
         display_error(output, 'good_errors');
      else
         display_error(output, 'bad_errors');
   });
}

function change_high_school()
{
   $.post('settings_query.php',
   {
      num:7,
      high_school: $('#high_school').val()
   }, function(output)
   {
      if(output=='Change successful!')
         display_error(output, 'good_errors');
      else
         display_error(output, 'bad_errors');
   });
}
function change_college()
{
    $.post('settings_query.php',
    {
      num:12,
      college: $('#college').val()
    }, function(output)
    {
      if(output=='Change successful!')
         display_error(output, 'good_errors');
      else
         display_error(output, 'bad_errors');
    });
}


function change_birthday()
{
    if($('#show_birthday_checkbox').is(":checked"))
        var string="yes";
    else
        var string="no";
    
    $.post('settings_query.php',
    {
       num:3,
        month: $('#month').val(),
        day: $('#day').val(),
        year: $('#year').val(),
        show_year: string

    }, function(output)
    {
       if(output=="Change successful!")
           display_error(output, 'good_errors');
       else
           display_error(output, 'bad_errors');
    });
}
function change_email()
{
    $.post('settings_query.php',
    {
        num:18,
        new_email: $('#email_input').val()
    }, function(output)
    {
        if(output=="Confirmation email has been sent to specified email. Changes will occur once it is confirmed by clicking of the sent link")
            display_error(output, 'good_errors');
        else
            display_error(output, 'bad_errors');
    });
}
function report_photo(photo_id, user_id)
{
    $.post('report_photo.php',
    {
        photo_id: photo_id,
        user_id: user_id
    }, function(output)
    {
        if(output=='Photo reported')
            display_error(output, 'good_errors');
        else
            display_error(output, 'bad_errors');
    });
}
function post_page()
{
    $.post('post_page.php',
    {
        post: $('#social_update').val()
    }, function(output)
    {
        if(output=='Post successful')
        {
            $('#social_update').val('');
            display_error(output, 'good_errors');
            
            //for page_home.php
            display_everything(1);
            
            //for page.php
            display_posts(1, 'all', 'all', 'none', 1);
        }    
        else
            display_error(output, 'bad_errors');
    });
}