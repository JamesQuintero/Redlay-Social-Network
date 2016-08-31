function show_menu()
{
    if($('#menu').css('display')=='none')
        $('#menu').show();
    else
        $('#menu').hide();
}
function get_timezone()
{
    var date = new Date()
    var timezone = date.getTimezoneOffset();
    return timezone;
}
function online()
{
    setInterval(function()
    {
        $.post('online.php',
        {
            num:1
        }, function(output)
        {});
    }, 1000);
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
function get_post_format(profile_picture, name, body, second_row, third_row, options, option_id, body_id)
{
    var line_break="<hr class='break'/>";
    return "<div class='status_update' id='"+body_id+"' onmouseover=$('#"+option_id+"').show(); onmouseout=$('#"+option_id+"').hide();>"+options+"<table style='width:100%;'><tbody><tr id='post_row_1' class='post_row'>  <td class='post_profile_picture_unit'>"+profile_picture+"</td><td class='post_body_unit'>"+name+body+"</td>  </tr><tr id='post_row_2' class='post_row'>  <td colspan='2' class='post_functions_unit'>"+second_row+"</td>  </tr><tr id='post_row_3' class='post_row'>  <td colspan='2' class='post_comments_unit'>"+third_row+"</td>  </tr></tbody></table>"+line_break+"</div>";
}
function show_alert_box()
{
    $('.alert_box').css('display', 'block').animate({opacity: 1}, 350, function(){});
}
function display_error(error, type)
{
    $('#errors').css('opacity', 0).html("<p style='margin:15px;'>"+error+"</p>").attr('class', type).show();
    $('#errors').animate({
        opacity :1
    }, 500, function()
    {
        setTimeout(function()
        {
            $('#errors').animate({
                opacity:0
            }, 500, function()
            {
                $('#errors').html('').hide();
            });
        }, 3500);
    });
}
function display_dim()
{
    $('#dim').css({'opacity': '0'}).show();
    $('#dim').animate({opacity:.3}, 350, function(){});
}
function input_in(ID)
{
    $(ID).css({'box-shadow': 'inset 0px 0px 4px 0px rgb(220,21,0)', 'border-color': 'rgb(220,21,0)'});
}
function input_out(ID)
{
    $(ID).css({'box-shadow': 'inset 0px 0px 7px 0px gray', 'border-color': 'gray'});
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
        type:type
    }, function(output)
    {
       var groups=output.groups;
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
              $('#'+item_id+'_checkbox_'+x).attr('onClick', "toggle_checkbox('"+item_id+"', "+x+");");
              if(groups[x]=='Everyone')
                  $('#'+item_id+'_checkbox_'+x).data('checked', 'yes').attr('src', './pictures/gray_checkbox_checked.png');
              else
                  $('#'+item_id+'_checkbox_'+x).data('checked', 'no').attr('src', './pictures/gray_checkbox.png');
          }
       }

    }, "json");
}
function display_current_groups(item_id, user_id)
{
    //displays appropriate HTML
    $('#'+item_id).html("<img class='display_to_button' src='./pictures/display_to_button.png' id='show_user_groups_button' /><div id='"+item_id+"_box_inside' class='select_body_options'></div>");
    $('#'+item_id+'_box_inside').html("<table class='select_body_options_table' ><tbody id='"+item_id+"_body' class='select_body_options_table_body'></tbody></table>");
    $('#'+item_id+'_box_inside').hide();
    $('#show_user_groups_button').attr({'onClick': "toggle_group_display('"+item_id+"');"});

    //gets the groups
    $.post('main_groups_query.php',
    {
        access:33,
        num:2,
        user_id: user_id
    }, function(output)
    {
       var groups=output.groups;
       var groups_in=output.groups_in;

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
              $('#'+item_id+'_checkbox_'+x).attr('onClick', "toggle_checkbox('"+item_id+"', "+x+");");

              var bool=false;

              if(groups[x]=='Everyone'||groups_in[x]=='yes')
                  $('#'+item_id+'_checkbox_'+x).data('checked', 'yes').attr('src', './pictures/gray_checkbox_checked.png');
              else
                  $('#'+item_id+'_checkbox_'+x).data('checked', 'no').attr('src', './pictures/gray_checkbox.png');
          }
       }

    }, "json");
}

function display_current_photo_groups(item_id, picture_id)
{
    //displays appropriate HTML
    $('#'+item_id).html("<input type='button' class='gray_button' id='show_user_groups_button' value='Display to' /><div id='"+item_id+"_box_inside' class='select_body_options'></div>");
    $('#'+item_id+'_box_inside').html("<table class='select_body_options_table' ><tbody id='"+item_id+"_body' class='select_body_options_table_body'></tbody></table>");
    $('#'+item_id+'_box_inside').hide();
    $('#show_user_groups_button').attr({'onClick': "toggle_group_display('"+item_id+"');"});

    //gets the groups
    $.post('main_groups_query.php',
    {
        access:33,
        num:3,
        picture_id: picture_id
    }, function(output)
    {
       var groups=output.groups;
       var groups_in=output.groups_in;

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
              $('#'+item_id+'_checkbox_'+x).attr('onClick', "toggle_checkbox('"+item_id+"', "+x+");");

              var bool=false;

              if(groups_in[x]=='yes')
                  $('#'+item_id+'_checkbox_'+x).data('checked', 'yes').attr('src', './pictures/gray_checkbox_checked.png');
              else
                  $('#'+item_id+'_checkbox_'+x).data('checked', 'no').attr('src', './pictures/gray_checkbox.png');
          }
       }

    }, "json");
}

function display_current_post_groups(item_id, post_id)
{
    //displays appropriate HTML
    $('#'+item_id).html("<input type='button' class='gray_button' id='show_user_groups_button' value='Display to' /><div id='"+item_id+"_box_inside' class='select_body_options'></div>");
    $('#'+item_id+'_box_inside').html("<table class='select_body_options_table' ><tbody id='"+item_id+"_body' class='select_body_options_table_body'></tbody></table>");
    $('#'+item_id+'_box_inside').hide();
    $('#show_user_groups_button').attr({'onClick': "toggle_group_display('"+item_id+"');"});

    //gets the groups
    $.post('main_groups_query.php',
    {
        access:33,
        num:4,
        post_id: post_id
    }, function(output)
    {
       var groups=output.groups;
       var groups_in=output.groups_in;

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
              $('#'+item_id+'_checkbox_'+x).attr('onClick', "toggle_checkbox('"+item_id+"', "+x+");");

              var bool=false;

              if(groups_in[x]=='yes')
                  $('#'+item_id+'_checkbox_'+x).data('checked', 'yes').attr('src', './pictures/gray_checkbox_checked.png');
              else
                  $('#'+item_id+'_checkbox_'+x).data('checked', 'no').attr('src', './pictures/gray_checkbox.png');
          }
       }

    }, "json");
}



function toggle_checkbox(item_id, index)
{
    var checked=$('#'+item_id+'_checkbox_'+index).data('checked');
    if(checked=='yes')
        $('#'+item_id+'_checkbox_'+index).attr('src', './pictures/gray_checkbox.png').data('checked', 'no');
    else
        $('#'+item_id+'_checkbox_'+index).attr('src', './pictures/gray_checkbox_checked.png').data('checked', 'yes');
}

function toggle_group_display(item_id)
{
    if($('#'+item_id+'_box_inside').css('display')!='block')
        $('#'+item_id+'_box_inside').show();
    else
        $('#'+item_id+'_box_inside').hide();
}
function show_alert_box()
{
    var document_width=$(window).width()/2;
    var alert_width=$('.alert_box').width()/2;
    
    $('.alert_box').css('left', document_width-alert_width);
    setTimeout(function(){
        $('.alert_box').css({'margin-top': (-1*($('.alert_box').height()/2))});
        $('.alert_box').css('display', 'block').animate({opacity: 1}, 350, function(){});
    }, 200);
}
function display_alert(title, body, extra_id, load_id, confirm)
{
    display_dim();
    
    $('.alert_box_inside').html("<table class='alert_box_table' ><tbody><tr class='alert_box_row' id='alert_box_row_1'></tr><tr class='alert_box_row' id='alert_box_row_2'></tr><tr class='alert_box_row' id='alert_box_row_3'></tr></tbody></table>");
        $('#alert_box_row_1').html("<td class='alert_box_title_unit' colspan='4'><p class='alert_box_title text_color' >"+title+"</p></td>");
        $('#alert_box_row_2').html("<td class='alert_box_body_unit' colspan='4'>"+body+"</td>");
        $('#alert_box_row_3').html("<td colspan='4'><table style='width:100%;margin-top:10px'><tbody><tr><td class='alert_box_confirmation_row_unit_left' id='"+extra_id+"'></td><td class='alert_box_load_unit'><img class='load_gif' id='"+load_id+"' src='http://www.redlay.com/load.gif'/></td><td class='alert_box_confirm_unit' >"+confirm+"</td><td class='alert_box_cancel_unit' ><input onClick=close_alert_box(); type='button' class='gray_button' value='Close'/></tr></tbody></table></td>");
    
    show_alert_box();
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
function isTouchDevice()
{
    try
    {
        document.createEvent("TouchEvent");
        return true;
    } catch(e)
    {
        return false;
    }
}
function touchScroll(id)
{
    //if touch events exist...
    if(isTouchDevice())
    {
        var interval=setInterval(function()
        {
            if($(id).css('overflow', 'auto'))
                clearInterval(interval);
        },100,function()
        {
           var el=document.getElementById(id);
            var scrollStartPos=0;

            document.getElementById(id).addEventListener("touchstart", function(event) 
            {
                    scrollStartPos=this.scrollTop+event.touches[0].pageY;
                    event.preventDefault();
            },false);

            document.getElementById(id).addEventListener("touchmove", function(event) 
            {
                    this.scrollTop=scrollStartPos-event.touches[0].pageY;
                    event.preventDefault();
            },false); 
        });
    }
}
function toggle_checkbox(id)
{
    var src=$(id).attr('src');
    if(src=='./pictures/gray_checkbox.png')
        $(id).attr('src', './pictures/gray_checkbox_checked.png');
    else
        $(id).attr('src', './pictures/gray_checkbox.png');
}

function toggle_gold_checkbox(id)
{
    var src=$(id).attr('src');
    if(src=='./pictures/gray_checkbox.png')
        $(id).attr('src', './pictures/gray_checkbox_checked.png');
    else
        $(id).attr('src', './pictures/gray_checkbox.png');
    calculate_gold_total();
}
function toggle_all_gold_checkboxes()
{
    if($('#gold_checkbox_all').attr('src')=='./pictures/gray_checkbox_checked.png')
    {
        var num=0;
        while($('#gold_checkbox_'+num).length)
        {
            $('#gold_checkbox_'+num).attr('src', './pictures/gray_checkbox_checked.png');
            num++;
        }
    }
    else
    {
        var num=0;
        while($('#gold_checkbox_'+num).length)
        {
            $('#gold_checkbox_'+num).attr('src', './pictures/gray_checkbox.png');
            num++;
        }
    }
    calculate_gold_total();
}
function calculate_gold_total()
{
    var num=0;
    var total=0;
    while($('#gold_checkbox_'+num).length)
    {
        if($('#gold_checkbox_'+num).attr('src')=='./pictures/gray_checkbox_checked.png')
            total++;
        num++;
    }
    var length=$('#redlay_gold_length').val();
    
    if(total==num)
    {
        if(length!=1)
            $('#total_gold').html("3.99/m");
        else
            $('#total_gold').html("2.99/m");
    }
    else if(total!=0)
    {
        if(length!=1)
            var money=total-.01;
        else
        {
            //subtracts 10 cents for every dollar
            //rounds to nearest 2 decimal places
            var money=Math.round(((total-(total*.1))-.01)*100)/100;
        }
        $('#total_gold').html(money+"/m");
    }
    else
        $('#total_gold').html("0.00");
}
function buy_user_gold()
{
    var gold=new Array();
    var index=0;
    if($('#gold_checkbox_0').attr('src')=='./pictures/gray_checkbox_checked.png')
    {
        gold[index]='site_customization';
        index++;
    }
    if($('#gold_checkbox_1').attr('src')=='./pictures/gray_checkbox_checked.png')
    {
        gold[index]='profile_stats';
        index++;
    }
    if($('#gold_checkbox_2').attr('src')=='./pictures/gray_checkbox_checked.png')
    {
        gold[index]='doc_space';
        index++;
    }
    if($('#gold_checkbox_3').attr('src')=='./pictures/gray_checkbox_checked.png')
    {
        gold[index]='photo_quality';
        index++;
    }
    if($('#gold_checkbox_4').attr('src')=='./pictures/gray_checkbox_checked.png')
    {
        gold[index]='the_public_access';
        index++;
    }
    if($('#gold_checkbox_5').attr('src')=='./pictures/gray_checkbox_checked.png')
    {
        gold[index]='new_feature_test';
        index++;
    }
    if($('#gold_checkbox_6').attr('src')=='./pictures/gray_checkbox_checked.png')
    {
        gold[index]='badge';
        index++;
    }
    

    $.post('buy_gold.php',
    {
        features:gold,
        user_id:-1,
        page_id:-1,
        length:$('#redlay_gold_length').val()
    }, function(output)
    {
        window.location.replace("http://www.redlay.com/start.php");
    });
}
function show_preview(type)
{
    if(type==1)
    {
        
    }
    else if(type==2)
    {
        
    }
}
function toggle_regular_checkbox(id)
{
    var src=$(id).attr('src');
    if(src=='./pictures/gray_checkbox.png')
        $(id).attr('src', './pictures/gray_checkbox_checked.png');
    else
        $(id).attr('src', './pictures/gray_checkbox.png');
}
function text_format(text)
{
    
    var final_text=text;
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
        
        else
        {
            if(final_text.toLowerCase().indexOf('[red](')!=-1)
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
    }
    return final_text;
}
