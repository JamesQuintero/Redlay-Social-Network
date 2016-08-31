<?php
@include('init.php');
include('universal_functions.php');
$allowed="users";
include('security_checks.php');



//DEPRECATED FEATURE


?>
<html>
    <head>
        <title>Your Documents</title>
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
                $('.box').css({'background-color': '<?php echo $box_background_color; ?>'});

                $('.doc_info_title, .alert_box_title').css('color', '<?php echo $color; ?>');
                $('.documents_page_box').css('border', '1px solid grey');
                $('.documents_text, #company_footer, .alert_box_title, .audience_option, .file_input, .file_name, .information_text').css('color', '<?php echo $text_color; ?>');
                
                $('.title_color').css('color', '<?php echo $color; ?>');
                $('.text_color').css('color', '<?php echo $text_color; ?>')

            }

            // Prevent "event.layerX and event.layerY are broken and deprecated in WebKit. They will be removed from the engine in the near future."
            // in latest Chrome builds.
            (function () {
                // remove layerX and layerY
                var all = $.event.props,
                    len = all.length,
                    res = [];
                while (len--) {
                    var el = all[len];
                    if (el != 'layerX' && el != 'layerY') res.push(el);
                }
                $.event.props = res;
            } ());

            function delete_document(doc_id)
            {
                $('#existing_documents_row_'+doc_id).html('');
                $.post('delete_document.php',
                {
                    doc_id: doc_id
                }, function(output)
                {
                    if(doc_id!=-1)
                    {
                        display_document(output);
                        reset_menu();

                        var num_documents=$('#existing_documents_title').data('number');
                        num_documents--;
                        $('#existing_documents_title').html('Documents ['+num_documents+']');
                        $('#existing_documents_title').data('number', num_documents);
                    }
                    else
                        window.location.replace(window.location);
                });
            }


            function display_document(doc_id)
            {
                reset_menu();
                $('#documents_menu_unit_2').html("<input class='green_button' type='button' value='Delete' onClick=delete_document('"+doc_id+"'); id='delete_document_button'/>");
                $('#delete_document_button').attr({'onmouseover': "display_title(this, 'Delete this document');", 'onmouseout': "hide_title(this);"});
                $('#documents_menu_unit_3').html("<input class='green_button' type='button' id='download_document_button' value='Download' onClick=window.location.replace('http://www.redlay.com/download_document.php?doc_id="+doc_id+"&&user_id=<?php echo $_SESSION['id']; ?>'); />");
                $('#download_document_button').attr({'onmouseover': "display_title(this, 'Download this document');", 'onmouseout': "hide_title(this);"});
                $('#documents_workspace_content').html('');
                
                $.post('documents_query.php',
                {
                    num:2,
                    doc_id: doc_id
                }, function(output)
                {
                    var name=output.name;
                    var timestamp=output.timestamp;
                    var num_downloads=output.num_downloads;
                    var viewability=output.viewability
                    var doc_audiences=output.doc_audiences;
                    var file_ext=output.file_ext;
                    var file_type=output.file_type;
//                    var doc_description=output.doc_description;
                    var size=output.size;
                    var size_percentage=output.size_percentage;
                    var doc_pic=output.doc_pic;

//                    $('#documents_workspace_content').html("<input class='red_button' type='button' onClick='edit_document("+doc_id+");' id='doc_edit_button' value='Edit'/>");

                    if(file_type=='audio'&&(file_ext=='mp3'||file_ext=='ogg'))
                        $('#documents_workspace_content').html($('#documents_workspace_content').html()+"<audio controls='controls' height='50px' width='100px'><source src='./users/docs/<?php echo $_SESSION['id']; ?>/archive/"+doc_id+".mp3' type='audio/mpeg' /><source src='./users/docs/<?php echo $_SESSION['id']; ?>/archive/"+doc_id+".ogg' type='audio/ogg' /><embed height='50px' width='100px' src='song.mp3' /></audio>");
                    else if(file_type=='video')
                        $('#documents_workspace_content').html($('#documents_workspace_content').html()+"<a href='http://www.redlay.com/users/docs/<?php echo $_SESSION['id'] ?>/archive/"+doc_id+"."+file_ext+"'><img class='doc_pic' src='"+doc_pic+"' /></a>");
                    else if(file_type=='image')
                        $('#documents_workspace_content').html($('#documents_workspace_content').html()+"<a href='http://www.redlay.com/users/docs/<?php echo $_SESSION['id'] ?>/archive/"+doc_id+"."+file_ext+"'><img class='image_preview' src='./users/docs/<?php echo $_SESSION['id']; ?>/archive/"+doc_id+"."+file_ext+"' /></a>");
                    else
                        $('#documents_workspace_content').html($('#documents_workspace_content').html()+"<img class='doc_pic' src='"+doc_pic+"'/>");
                    
                    $('#documents_workspace_content').html($('#documents_workspace_content').html()+"<table id='document_table'><tbody id='document_table_body'></tbody></table>");
                        $('#document_table_body').html("<tr class='doc_info_row' id='doc_info_row_1'></tr><tr class='doc_info_row' id='doc_info_row_2'></tr><tr class='doc_info_row' id='doc_info_row_3'></tr>\n\
                            <tr class='doc_info_row' id='doc_info_row_4'></tr><tr class='doc_info_row' id='doc_info_row_5'></tr><tr class='doc_info_row' id='doc_info_row_6'></tr>\n\
                                <tr class='doc_info_row' id='doc_info_row_7'></tr><tr class='doc_info_row' id='doc_info_row_8'></tr><tr class='doc_info_row' id='doc_info_row_9'></tr>");
                            $('#doc_info_row_1').html("<td class='doc_info_unit'><p class='information_text'><span class='doc_info_title'>Name:</span> <span id='name_text'>"+name+"</span></p></td>");
                            $('#doc_info_row_2').html("<td class='doc_info_unit'><p class='information_text'><span class='doc_info_title'>Uploaded:</span> <span class='uneditable_text'>"+timestamp+"</span></p></td>");
                            $('#doc_info_row_3').html("<td class='doc_info_unit'><p class='information_text'><span class='doc_info_title'>Viewability:</span> <span id='viewability_text'>"+viewability+"</span></p></td>")
                            if(viewability=='public')
                            {
                                $('#doc_info_row_4').html("<td class='doc_info_unit'><p class='information_text'><span class='doc_info_title'>Downloads:</span> <span class='uneditable_text'>"+num_downloads+"</span></p></td>");
                                $('#doc_info_row_5').html("<td class='doc_info_unit'><p class='information_text'><span class='doc_info_title'>Audiences:</span> <span id='audience_text'>"+doc_audiences+"</span></p></td>");
                            }
                            $('#doc_info_row_6').html("<td class='doc_info_unit'><p class='information_text'><span class='doc_info_title'>Type:</span> <span class='uneditable_text'>"+file_ext+"</span></p></td>");
//                            $('#doc_info_row_7').html("<td class='doc_info_unit'><p class='information_text'><span class='doc_info_title'>Description:</span> "+doc_description+"</p></td>");
                            $('#doc_info_row_8').html("<td class='doc_info_unit'><p class='information_text'><span class='doc_info_title'>Size:</span> <span class='uneditable_text'>"+size+"</span></p></td>");
                            $('#doc_info_row_9').html("<td class='doc_info_unit'><p class='information_text'><span class='doc_info_title'>Space taken:</span> <span class='uneditable_text'>"+size_percentage+"%</span></p></td>");

                    change_color();
                }, "json");
            }

            function display_existing_documents()
            {
                reset_menu();
                $('#existing_documents_table_body').html('');
                $.post('documents_query.php',
                {
                    num: 1
                }, function(output)
                {
                    var doc_ids=output.doc_ids;
                    var document_names=output.document_names;
                    var file_pictures=output.file_picture;
                    var total_size=output.total_size;
                    var size_percentage=output.size_percentage;
                    var file_exts=output.file_exts;

                    if(total_size!=0)
                    {
                        for(var x= 0; x < doc_ids.length; x++)
                        {
                            $('#existing_documents_table_body').html($('#existing_documents_table_body').html()+"<tr class='existing_documents_row' id='existing_documents_row_"+x+"'></tr>");
                            $('#existing_documents_row_'+x).html("<td class='existing_documents_picture' id='picture_"+x+"'></td><td class='existing_documents_name' id='existing_documents_name_"+x+"'></td>");
                                $('#picture_'+x).html("<img class='document_thumbnail' src='"+file_pictures[x]+"' />");
                                $('#existing_documents_name_'+x).html("<p class='file_name' id='file_name_"+x+"'>"+document_names[x]+"</p>");
                                    $('#file_name_'+x).attr({'onmouseover': "name_over(this);", 'onmouseout': "name_out(this);"});
    //                                if(file_exts[x]=='txt'||file_exts[x]=='text')
    //                                    $('#file_name_'+doc_ids[x]).attr({'onClick': "display_text_document("+doc_ids[x]+");"});
    //                                else
                                        $('#file_name_'+x).attr({'onClick': "display_document('"+doc_ids[x]+"');"});
                        }
                        $('#existing_documents_title').html("Documents ["+doc_ids.length+"]").data('number', doc_ids.length);
                        $('#documents_page_left_box').html("<p class='information_text doc_title_information' >Total size: "+total_size+"</p><p class='information_text doc_title_information'>Space taken: "+size_percentage+"%</p>");

                        change_color();
                        $('#existing_documents_gif').hide();
                        display_document(doc_ids[0]);
                    }
                    else
                    {
                        $('#documents_workspace_content').html("<p style='color: <?php echo $text_color; ?>'>You have no documents. Upload one by pressing the Upload button above!</p>");
                        $('#existing_documents_gif').hide();

                        $('#existing_documents_title').html("Documents ["+doc_ids.length+"]").data('number', doc_ids.length);
                        $('#documents_page_left_box').html("<p class='information_text doc_title_information' >Total size: "+total_size+"</p><p class='information_text doc_title_information'>Space taken: "+size_percentage+"%</p>");
                    }

                }, "json");
            }

            function reset_menu()
            {
                $('#documents_menu_unit_2').html('');
                $('#documents_menu_unit_3').html('');

            }

            function display_document_groups()
            {
                var audience_list=new Array();
               <?php
               $query=mysql_query("SELECT audience_defaults FROM public WHERE num=1 LIMIT 1");
               $query2=mysql_query("SELECT audience_group_lists FROM user_data WHERE user_id=$_SESSION[id] LIMIT 1");
               if($query&&mysql_num_rows($query)==1&&$query2&&mysql_num_rows($query2)==1)
               {
                  $array=mysql_fetch_row($query);
                  $array2=mysql_fetch_row($query2);
                  $audience_list=explode('|^|*|', $array2[0]);
                  $audience_defaults=explode('|^|*|', $array[0]);

                  echo "audience_list[0]='Everyone';";
                  $num=1;
                  for($x = 0; $x < sizeof($audience_defaults); $x++)
                  {
                        echo "audience_list[$num]='$audience_defaults[$x]';";
                        $num++;
                  }
                  if($audience_list[0]!='')
                  {
                        for($x =0; $x < sizeof($audience_list); $x++)
                        {
                           echo "audience_list[$num]='$audience_list[$x]';";
                           $num++;
                        }
                  }
               }
               ?>
               //adds rows to table
               if(audience_list.length>=4)
               {
                  for(var x = 0; x < (audience_list.length/3); x++)
                        $('#photo_group_table').html($('#photo_group_table').html()+"<tr class='audience_category_row' id='photo_group_row_"+x+"'></tr>");
               }
               else
                  $('#photo_group_table').html("<tr class='photo_group_table' id='photo_group_row_0'></tr>");

               //adds actual list items
               var num=0;
               for(var x =0; x < audience_list.length; x++)
               {
                  $('#photo_group_row_'+num).html($('#photo_group_row_'+num).html()+"<td id='option_"+x+"'><input type='checkbox' id='checkbox_"+x+"' name='groups[]' value='"+audience_list[x]+"'/><span class='audience_option' id='checkbox_name_"+x+"' >"+audience_list[x]+"</span></td>");
                  if(x%3==0&&x!=0)
                        num++;
               }
               for(var x =0; x < audience_list.length; x++)
                  $('#checkbox_'+x).data("group_name", audience_list[x]);
            }
            function display_upload_menu()
            {
                 $('.alert_box').css('opacity', 1).show().draggable();
                $('.alert_box_inside').html("<form method='post' action='upload_document.php' enctype='multipart/form-data' style='margin-bottom:0px'><table class='alert_box_table' id='upload_photo_table'><tbody><tr class='alert_box_row' id='upload_photo_row_1'></tr><tr class='alert_box_row' id='upload_photo_row_2'></tr><tr class='alert_box_row' id='upload_photo_row_3'></tr><tr class='alert_box_row' id='upload_photo_row_4' ></tr><tr class='alert_box_row' id='upload_photo_row_5' ></tr></tbody></table></form>");
                    $('#upload_photo_row_1').html("<td class='upload_photo_unit alert_box_title_unit' colspan='4'><p class='title_color alert_box_title'>Upload a document</p></td>");
                    $('#upload_photo_row_2').html("<td class='upload_photo_unit' colspan='4'><input type='file' id='photo_upload_button' class='file_input' name='file[]' multiple/></td>");
                    $('#upload_photo_row_3').html("<p class='text_color'>Please do not upload copyrighted music. We don't want to be sued.</p>");
                    $('#upload_photo_row_4').html("<td class='upload_photo_unit' colspan='4'><hr /></td>");
                    $('#upload_photo_row_5').html("<td class='upload_photo_unit alert_box_confirmation_row_unit_left'><select id='viewability_options' name='viewability_options' onChange='toggle_viewability_options();'></select><div id='public_document_audience' class='select_box'></div></td><td class='upload_photo_unit alert_box_load_unit'><img class='load_gif' id='upload_photo_gif' src='load.gif'/></td><td class='upload_photo_unit alert_box_confirm_unit' ><input type='submit' class='green_button' id='photo_upload_submit' onClick=$('#upload_photo_gif').show(); value='Upload' /></td><td class='upload_photo_unit alert_box_cancel_unit' ><input type='button' class='gray_button' id='photo_upload_cancel' onClick=close_alert_box(); value='Cancel' /></td>");

                        $('#viewability_options').html("<option value='public'>Public</option><option value='private' selected='selected'>Private</option>");
                        $('#upload_photo_gif').hide();


                change_color();
            }
            function toggle_viewability_options()
            {
                if($('#viewability_options').val()=='public')
                {
                    display_groups('public_document_audience');
                    $('#photo_group_table').show();
                }
                else
                {
                    $('#photo_group_table').html('').hide();
                    $('#public_document_audience').html('');
                }
            }
            $(document).ready(function()
            {
                <?php $path="./users/images/$_SESSION[id]/background.jpg"; if(file_exists($path)==true) echo "$('body').css({'background-image': 'url(\'$path\')', 'background-position' :'center 50px'});";  else echo "$('body').css({'background-image': 'url(\'./pictures/default_background.png\')', 'background-position' :'center 50px'});"; ?>
                display_existing_documents();
                
                $('#footer').css('width', '910px');

                <?php include('required_jquery.php'); ?>
            });
        </script>
        <script type="text/javascript">

          <?php include("required_google_analytics.js"); ?>

        </script>
    </head>
    <body>
        <?php include('required_html.php'); ?>
        <div id="top">
            <?php include('top.php'); ?>
        </div>
        <div id="main">
            <div id="documents_content" class="box">
                <table id="documents_page_table">
                    <tbody id="documents_page_table_body">
                        <tr class="documents_page_row" id="documents_page_row_1">
                            <td class="documents_page_unit" id="documents_page_unit_1">
                                <div class="documents_page_box" id="documents_page_left_box">

                                </div>
                            </td>
                            <td class="documents_page_unit" id="documents_page_unit_2">
                                <div class="documents_page_box" id="documents_page_menu_box">
                                    <table id="documents_menu_table">
                                        <tbody id="documents_menu_table_body">
                                            <tr class="documents_menu_row">
                                                <td class="documents_menu_unit" id="documents_menu_unit_1">
                                                    <input class="green_button" type="button" value="Upload" onmouseover="display_title(this, 'Upload any type of document');" onmouseout="hide_title(this);" onClick="display_upload_menu();" />
                                                </td>
                                                <td class="documents_menu_unit" id="documents_menu_unit_2">
                                                    
                                                </td>
                                                <td class="documents_menu_unit" id="documents_menu_unit_3">

                                                </td>
                                                <td class="documents_menu_unit" id="documents_menu_unit_4">
                                                    <input class="green_button" type="button" value="Delete ALL" onmouseover="display_title(this, 'Deletes ALL of your documents');" onmouseout="hide_title(this);" onClick="delete_document(-1);"/>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </td>
                        </tr>
                        <tr class="documents_page_row" id="documents_page_row_2">
                            <td class="documents_page_unit" id="documents_page_unit_3">
                                <div class="documents_page_box" id="existing_documents_box">
                                    <div><p class="documents_text" id="existing_documents_title" style="position:relative;margin-left:10px;text-decoration:underline;">Documents: </p><img class="load_gif" id="existing_documents_gif" src="load.gif"/></div>
                                    <hr />
                                    <table id="existing_documents_table">
                                        <tbody id="existing_documents_table_body">

                                        </tbody>
                                    </table>
                                </div>
                            </td>
                            <td class="documents_page_unit" id="documents_page_unit_4">
                                <div class="documents_page_box" id="documents_workspace_box">
                                    <div id="documents_workspace_content">
                                                <table id='document_table'>
                                                    <tbody id='document_table_body'>
                                                        <tr class='document_row' id='document_row_1'>
                                                            <td id='document_title_unit'>
                                                                <div id='document_title'>
                                                                    <input type='text' id='new_document_title' placeholder='Name...' class='document_input input_box' onFocus="input_in(this);" onBlur="input_out(this);"/>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr class='document_row' id='document_row_2'>
                                                            <td>
                                                                <textarea id='document_input' class='document_input input_box' placeholder="Type something here..." onFocus="input_in(this);" onBlur="input_out(this);" onChange="$(this).data('changed', 'yes');" onkeyup="$(this).data('changed', 'yes');"></textarea>
                                                            </td>
                                                        </tr>
                                                        <tr class='document_row' id='document_row_3'>
                                                            <td id='document_button_unit'>
                                                                <input type='button' value='Save' class='red_button' id='new_document_submit' onClick="save_text_document(-1);"/>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
             <?php include('footer.php'); ?>
        </div>
       <iframe name="doc_upload_iframe" style="display:none;"></iframe>
    </body>
</html>