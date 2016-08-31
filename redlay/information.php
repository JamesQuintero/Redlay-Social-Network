<?php

$query=mysql_query("SELECT user_birthday, user_relationship, user_sex, user_bio, high_school, college, user_mood FROM user_data WHERE user_id=$ID LIMIT 1");
if($query&&mysql_num_rows($query)==1)
{
    $array=mysql_fetch_row($query);
    $birthday=explode('|^|*|', $array[0]);
    
    //gets birthday_year
    $query=mysql_query("SELECT birthday_year FROM user_display WHERE user_id=$ID LIMIT 1");
    if($query&&mysql_num_rows($query)==1)
    {
        $array2=mysql_fetch_row($query);
        $birthday_year=$array2[0];
    }

    if($birthday_year=='yes')
        $birthday=$birthday[0]." ".$birthday[1].", ".$birthday[2];
    else
        $birthday=$birthday[0]." ".$birthday[1];

    $relationship=$array[1];
    $gender=$array[2];
    $bio=$array[3];
    $high_school=$array[4];
    $college=$array[5];
    $mood=$array[6];

    $query2=mysql_query("SELECT timestamps FROM users WHERE id=$ID LIMIT 1");
    if($query2&&mysql_num_rows($query2)==1)
    {
        $array2=mysql_fetch_row($query2);
        $date_joined=$array2[0];
    }

}

?>
<table id="information_table">
    <tbody>
        <tr class="information_row" id="information_row_1">
            <td class="info_item_left" ><span class="title_information" id="birthday_title_information">Birthday: </span></td>
            <td class="info_item_right" ><span class="text_information" id="birthday_information"><?php echo $birthday; ?></span></td>
        </tr>
        <tr class="information_row" id="information_row_2">
            <td class="info_item_left" ><span class="title_information" id="relationship_title_information">Relationship: </span></td>
            <td class="info_item_right" ><span class="text_information" id="relationship_information"><?php echo $relationship; ?></span></td>
        </tr>
        <tr class="information_row" id="information_row_3">
            <td class="info_item_left" ><span class="title_information" id="sex_title_information">Sex: </span></td>
            <td class="info_item_right" ><span class="text_information" id="sex_information" ><?php echo $gender; ?></span></td>
        </tr>
        <tr class="information_row" id="information_row_4">
            <td class="info_item_left" ><span class="title_information" id="bio_title_information">Bio: </span></td>
            <td class="info_item_right" ><span id="bio_information" class="text_information"><?php echo $bio; ?></span></td>
        </tr>
        <tr class="information_row" id="information_row_5">
            <td class="info_item_left" ><span class="title_information" id="high_school_title_information">High School: </span></td>
            <td class="info_item_right" ><span class="text_information" id="high_school_information"><?php echo $high_school; ?></span></td>
        </tr>
        <tr class="information_row" id="information_row_6">
            <td class="info_item_left" ><span class="title_information" id="college_title_information">College: </span></td>
            <td class="info_item_right" ><span class="text_information" id="college_information"><?php echo $college; ?></span></td>
        </tr>
        <tr class="information_row" id="information_row_7">
            <td class="info_item_left" ><span class="title_information" id="mood_title_information">Mood: </span></td>
            <td class="info_item_right" ><span class="text_information" id="mood_information"><?php echo $mood; ?></span></td>
        </tr>
        <tr class="information_row" id="information_row_8">
            <td class="info_item_left" ><span class="title_information" id="date_joined_title_information">Date joined: </span></td>
            <td class="info_item_right" ><span class="text_information" id="date_joined_information"><?php echo $date_joined; ?></span></td>
        </tr>
    </tbody>
</table>
