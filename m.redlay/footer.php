<table id="footer" class="box">
    <tbody>
        <tr>
            <td colspan="10" style="text-align:center;">
                <p id="company_footer" style="color:<?php echo $text_color; ?>">redlay</p>
            </td>
        </tr>
        <tr>
            <td colspan="10">
                <hr id="footer_line_break" />
            </td>
        </tr>
        <tr>
            <td>
                <table style="font-size:25px;">
                    <tbody>
                        <tr>
                            <td class="footer_unit">
                                <a href="http://m.redlay.com/about.php" style="text-decoration:none;"><span class="footer_text" id="about_footer" onmouseover="name_over(this);" onmouseout="name_out(this);">About</span></a>
                            </td>
                            <td class="footer_seperator_unit"><span class="footer_seperator" style="color:<?php echo $text_color; ?>">|</span></td>
                            <td class="footer_unit">
                                <a href="http://m.redlay.com/report.php" style="text-decoration:none;"><span class="footer_text" id="report_footer" onmouseover="name_over(this);" onmouseout="name_out(this);">Report</span></a>
                            </td>
                            <td class="footer_seperator_unit"><span class="footer_seperator" style="color:<?php echo $text_color; ?>">|</span></td>
                            <td class="footer_unit">
                                <a href="http://m.redlay.com/contact.php" style="text-decoration:none;"><span class="footer_text" id="contact_footer" onmouseover="name_over(this);" onmouseout="name_out(this);">Contact</span></a>
                            </td>
                            <td class="footer_seperator_unit"><span class="footer_seperator" style="color:<?php echo $text_color; ?>">|</span></td>
                            <td class="footer_unit">
                                <?php if(isset($_SESSION['id'])) echo "<a href='http://m.redlay.com/help.php' style='text-decoration:none;'><span class='footer_text' id='help_footer' onmouseover=name_over(this); onmouseout=name_out(this); >Help</span></a>";  ?>
                                <?php if(isset($_SESSION['page_id'])) echo "<a href='http://m.redlay.com/help_page.php' style='text-decoration:none;'><span class='footer_text' id='help_footer' onmouseover=name_over(this); onmouseout=name_out(this);>Help</span></a>"; ?>
                            </td>
<!--                            <td class="footer_seperator_unit"><span class="footer_seperator" style="color:<?php echo $text_color; ?>">|</span></td>
                            <td class="footer_unit">
                                <a href="http://m.redlay.com/stats.php" style="text-decoration:none;"><span class="footer_text" id="stats_footer" onmouseover="name_over(this);" onmouseout="name_out(this);">Stats</span></a>
                            </td>-->
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </tbody>
</table>