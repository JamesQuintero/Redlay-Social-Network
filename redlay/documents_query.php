<?php
@include('init.php');
include('universal_functions.php');
$allowed="users";
include('security_checks.php');


$num=(int)($_POST['num']);

$has_gold=has_redlay_gold($_SESSION['id']);

//gets list of documents
if($num==1)
{
    $query=mysql_query("SELECT doc_ids, document_names, file_ext FROM user_documents WHERE user_id=$_SESSION[id] LIMIT 1");
    if($query&&mysql_num_rows($query)==1)
    {
        $array=mysql_fetch_row($query);

        $doc_ids=explode('|^|*|', $array[0]);
        $document_names=explode('|^|*|', $array[1]);
        $file_exts=explode('|^|*|', $array[2]);

        //gets the total size of all files
        $path="./users/docs/$_SESSION[id]/archive";
        $directory=opendir($path);
        $total=0;
        while($temp_file=readdir($directory))
        {
            if(substr($temp_file, 0, 1)!=".")
                $total=$total+filesize($path."/".$temp_file);
        }
        closedir($directory);

        //gets percentage of used space
        if($has_gold)
            $percentage=number_format($total/42949672, 2);
        else
            $percentage=number_format($total/21474836, 2);

        $total=get_size($total);


        //gets icons from extentions
        for($x = 0; $x < sizeof($doc_ids); $x++)
        {
            $file_exts[$x]=strtolower($file_exts[$x]);
            $file_pictures[$x]=get_doc_icon(1, $file_exts[$x]);
        }

        if($array[0]!='')
        {
            $JSON=array();
            $JSON['doc_ids']=$doc_ids;
            $JSON['document_names']=$document_names;
            $JSON['file_picture']=$file_pictures;
            $JSON['total_size']=$total;
            $JSON['size_percentage']=$percentage;
            $JSON['file_exts']=$file_exts;
            echo json_encode($JSON);
            exit();
        }
        else
        {
            $JSON['doc_ids']=array();
            $JSON['document_names']=array();
            $JSON['file_picture']=array();
            $JSON['total_size']=0;
            $JSON['size_percentage']='0';
            echo json_encode($JSON);
            exit();
        }
    }
}

//gets information on a single document
else if($num==2)
{
    $doc_id=clean_string($_POST['doc_id']);
    $query=mysql_query("SELECT doc_ids, document_names, file_ext, num_downloads, doc_audiences, doc_viewability FROM user_documents WHERE user_id=$_SESSION[id] LIMIT 1");
    if($query&&mysql_num_rows($query)==1)
    {
        $array=mysql_fetch_row($query);
        $doc_ids=explode('|^|*|', $array[0]);
        $document_names=explode('|^|*|', $array[1]);
        $file_exts=explode('|^|*|', $array[2]);
        $num_downloads=explode('|^|*|', $array[3]);
        $doc_audiences=explode('|^|*|', $array[4]);
        $viewability=explode('|^|*|', $array[5]);

        //gets the index of the document
        $index=0;
        for($x = 0; $x < sizeof($doc_ids); $x++)
        {
            if($doc_ids[$x]==$doc_id)
                $index=$x;
        }

        $path="./users/docs/$_SESSION[id]/archive/$doc_id.$file_exts[$index]";

        //gets the total size of all files
        $path2="./users/docs/$_SESSION[id]/archive";
        $directory=opendir($path2);
        $total=0;
        while($temp_file=readdir($directory))
        {
            if(substr($temp_file, 0, 1)!=".")
                $total=$total+filesize($path2."/".$temp_file);
        }
        closedir($directory);

        //gets size and size percentage of file
        $size=filesize($path);
        if($has_gold)
            $size_percentage=number_format($size/42949672, 4);
        else
            $size_percentage=number_format($size/21474836, 4);

        //gets user readable size data
        if($size/1000000000>=1)
            $size=number_format($size/1000000000, 2)."GB";
        else if($size/1000000>=1)
            $size=number_format($size/1000000, 2)."MB";
        else if($size/1000>=1)
            $size=number_format($size/1000, 2)."KB";
        else
            $size=$size."B";

        $JSON=array();
        $JSON['name']=$document_names[$index];
        $JSON['timestamp']=date("F j, Y g:i:s A", filemtime($path));
        $JSON['num_downloads']=$num_downloads[$index];
        $JSON['viewability']=$viewability[$index];
        $JSON['doc_audiences']=explode('|%|&|', $doc_audiences[$index]);
        $JSON['file_ext']=$file_exts[$index];
        $JSON['file_type']=get_doc_icon(3, strtolower($file_exts[$index]));
        $JSON['size']=$size;
        $JSON['size_percentage']=$size_percentage;
        $JSON['doc_pic']=get_doc_icon(2, strtolower($file_exts[$index]));
        echo json_encode($JSON);
        exit();
    }
}

//loads text document's information
else if($num==3)
{
    $doc_id=clean_string($_POST['doc_id']);
    $query=mysql_query("SELECT doc_ids, document_names, file_ext FROM user_documents WHERE user_id=$_SESSION[id] LIMIT 1");
    if($query&&mysql_num_rows($query)==1)
    {
        $array=mysql_fetch_row($query);
        $doc_ids=explode('|^|*|', $array[0]);
        $doc_names=explode('|^|*|', $array[1]);
        $file_exts=explode('|^|*|', $array[2]);

        $index=-1;
        for($x = 0; $x < sizeof($doc_ids); $x++)
        {
            if($doc_ids[$x]==$doc_id)
                $index=$x;
        }


        if(($file_exts[$index]=='text'||$file_exts[$index]=='txt')&&$index!=-1)
        {
            $path="./users/docs/$_SESSION[id]/archive/$doc_id.$file_exts[$index]";
            $doc_name=$doc_names[$index];
            $doc_contents=file_get_contents($path);

            $JSON=array();
            $JSON['doc_name']=$doc_name;
            $JSON['doc_contents']=$doc_contents;
            echo json_encode($JSON);
            exit();
        }
        else
        {
            $JSON=array();
            $JSON['doc_name']='';
            $JSON['doc_contents']='';
            echo json_encode($JSON);
            exit();
        }
    }
}

//loads editable text document's information
else if($num==4)
{
    $doc_id=clean_string($_POST['doc_id']);

    $query=mysql_query("SELECT doc_ids, document_names, doc_viewability, doc_audiences FROM user_documents WHERE user_id=$_SESSION[id] LIMIT 1");
    if($query&&mysql_num_rows($query)==1)
    {
        $array=mysql_fetch_row($query);
        $doc_ids=explode('|^|*|', $array[0]);
        $doc_names=explode('|^|*|', $array[1]);
        $doc_viewability=explode('|^|*|', $array[2]);
        $doc_audiences=explode('|^|*|', $array[3]);

        $index=-1;
        for($x = 0; $x < sizeof($doc_ids); $x++)
        {
            if($doc_ids[$x]==$doc_id)
                $index=$x;
        }

        $JSON=array();
        $JSON['doc_name']=$doc_names[$index];
        $JSON['doc_viewability']=$doc_viewability[$index];
        $JSON['doc_audiences']=explode('|%|&|', $doc_audiences[$index]);
        echo json_encode($JSON);
        exit();
    }
}