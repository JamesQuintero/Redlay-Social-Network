<?php
@include('init.php');
include('universal_functions.php');
$allowed="users";
include('security_checks_user.php');

$doc_id=clean_string($_GET['doc_id']);
$ID=(int)($_GET['user_id']);

if($doc_id>=0)
{
    $query=mysql_query("SELECT doc_ids, file_ext, document_names, num_downloads FROM user_documents WHERE user_id=$ID LIMIT 1");
    if($query&&mysql_num_rows($query)==1)
    {
        $array=mysql_fetch_row($query);
        $doc_ids=explode('|^|*|', $array[0]);
        $file_exts=explode('|^|*|', $array[1]);
        $doc_names=explode('|^|*|', $array[2]);
        $num_downloads=explode('|^|*|', $array[3]);

        $index=-1;
        for($x = 0; $x < sizeof($doc_ids); $x++)
        {
            if($doc_ids[$x]==$doc_id)
                $index=$x;
        }

        if($ID==$_SESSION['id'])
        {
            $num_downloads[$index]++;
            $num_downloads=implode('|^|*|', $num_downloads);
            $query=mysql_query("UPDATE user_documents SET num_downloads='$num_downloads' WHERE user_id=$ID");
        }


        if($index!=-1)
        {
            $file_extention=$file_exts[$index];

            if($file_extention=="aif")
                $content_type="audio/x-aiff";

            //if m4a
            else if($file_extention=="m4a")
                $content_type="audio/mp4a-latm";

            //if code
            else if($file_extention=="php"||$file_extention=="css"||$file_extention=="js"||$file_extention=="jsp"||$file_extention=="jar"||$file_extention=="java"||
                    $file_extention=="class"||$file_extention=="cpp"||$file_extention=="vcxproj"||$file_extention=="c"||$file_extention=="h"||$file_extention=="py"||
                    $file_extention=="sql"||$file_extention=="ruby"||$file_extention=="text"||$file_extention=="txt"||$file_extention=="html"||$file_extention=="htm"||
                    $file_extention=="xhtml"||$$file_extention=="asp"||$file_extention=="aspx"||$file_extention=="xml")
                $content_type="text/plain";

            //if mp4
            else if($file_extention=="mp4")
                $content_type="video/mp4";

            //if mov
            else if($file_extention=="mov")
                $content_type="video/quicktime";

            //if mp3
            else if($file_extention=="mp3"||$file_extention=="mp2")
                $content_type="audio/mpeg";

            //if mpeg or mpeg
            else if($file_extention=="mpeg"||$file_extention=="mpg")
                $content_type="video/mpeg";

            //if wav
            else if($file_extention=="wav")
                $content_type="audio/x-wav";

            //if wma
            else if($file_extention=="wma")
                $content_type="audio/wma";

            //if avi
            else if($file_extention=="avi")
                $content_type="video/x-msvideo";

            //if flv
            else if($file_extention=="flv")
                $content_type="video/x-flv";

            //if vob
            else if($file_extention=="vob")
                $content_type="video/dvd";

            //if wmv
            else if($file_extention=="wmv")
                $content_type="video/x-ms-wmv";

            //if png
            else if($file_extention=="png")
                $content_type="image/png";
            else if($file_extention=="jpeg"||$file_extention=="jpg")
                $content_type="image/jpeg";
            else if($file_extention=="gif")
                $content_type="image/gif";
            else if($file_extention=="bmp")
                $content_type="image/bmp";

            //if pdf
            else if($file_extention=="pdf")
                $content_type="application/pdf";

            else
                $content_type="octet-stream";

            //content type
            header('Content-type: '.$content_type);

            //open/save dialog box
            header("Content-Disposition: attachment; filename='".$doc_names[$index]."'");

            //read from server and write to buffer
            readfile('./users/docs/'.$ID.'/archive/'.$doc_id.'.'.$file_extention);
        }
    }
}