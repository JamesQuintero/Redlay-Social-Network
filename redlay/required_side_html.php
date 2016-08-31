<div id="redlay_theme_box" onmouseover="$(this).css('background-color', 'whitesmoke');" onmouseout="$(this).css('background-color', 'white');">
    <table style="height:100%;width:100%;border-spacing:0;">
        <tbody>
            <tr>
                <td id="redlay_theme_text_unit" style="width:33%;cursor:pointer;" onClick="pull_theme();">
                    <p style="margin:0px;" id="redlay_theme_text" style="cursor:pointer;">Themes</p>
                </td>
            </tr>
            <tr>
                <td id="black_redlay_unit" onmouseover="name_over('#redlay_theme_black_text');" onmouseout="name_out('#redlay_theme_black_text');" onClick="redlay_black_theme();">
                    <p style="margin:0px;color:white;text-align:center;" id="redlay_theme_black_text">Dark</p>
                </td>
            </tr>
            <tr>
                <td id="white_redlay_unit" style="width:33%;background-color:white;border:1px solid gray;" onClick="redlay_white_theme();" onmouseover="name_over('#redlay_theme_white_text');" onmouseout="name_out('#redlay_theme_white_text');" >
                    <p style="margin:0px;color:black;text-align:center;" id="redlay_theme_white_text">Light</p>
                </td>
            </tr>
            <tr>
                <td id="aluminum_redlay_unit" style="width:33%;background-color:white;border:1px solid gray;" onClick="redlay_aluminum_theme();" onmouseover="name_over('#redlay_theme_aluminum_text');" onmouseout="name_out('#redlay_theme_aluminum_text');" >
                    <p style="margin:0px;color:black;text-align:center;" id="redlay_theme_aluminum_text">Aluminum</p>
                </td>
            </tr>
            <tr>
                <td id="neon_redlay_unit" style="width:33%;background-color:white;border:1px solid gray;" onClick="redlay_neon_theme();" onmouseover="name_over('#redlay_theme_neon_text');" onmouseout="name_out('#redlay_theme_neon_text');" >
                    <p style="margin:0px;color:white;text-align:center;" id="redlay_theme_neon_text">Neon</p>
                </td>
            </tr>
            <tr>
                <td id="beach_redlay_unit" style="width:33%;background-color:white;border:1px solid gray;" onClick="redlay_beach_theme();" onmouseover="name_over('#redlay_theme_beach_text');" onmouseout="name_out('#redlay_theme_beach_text');" >
                    <p style="margin:0px;color:white;text-align:center;" id="redlay_theme_beach_text">Beach</p>
                </td>
            </tr>
            <tr>
                <?php
                $side_has_gold=has_redlay_gold($_SESSION['id']);
                ?>
                <td id="custom_redlay_unit" style="width:33%;background-color:white;border:1px solid gray;<?php if(!$side_has_gold) echo "cursor:default;"; else echo "cursor:pointer;" ?>" onmouseover="name_over('#redlay_theme_custom_text');" onmouseout="name_out('#redlay_theme_custom_text');"  >
                    <p style="margin:0px;color:white;text-align:center;" id="redlay_theme_custom_text" <?php if($side_has_gold) echo "onClick=redlay_custom_theme();"; else echo "onClick='window.location.replace(\"http://www.redlay.com/redlay_gold.php\");'"; ?> >Custom</p>
                </td>
            </tr>
        </tbody>
    </table>
</div>
<div id="instant_messaging_table">
    <table>
        <tbody>
            <tr>
                <td>

                </td>
            </tr>
            <tr>
                <td id="instant_messaging_text_unit" style="cursor:pointer;width:150px;" >
                    <div id="instant_messaging_box" onmouseover="$(this).css('background-color', 'whitesmoke');" onmouseout="$(this).css('background-color', 'white');">
                        <p style="margin:0px;">Messages</p>
                    </div>
                </td>
            </tr>
        </tbody>  
    </table>
</div>