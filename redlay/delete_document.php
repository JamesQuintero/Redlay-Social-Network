<?php
@include('init.php');
include('universal_functions.php');
$allowed="users";
include('security_checks.php');

$doc_id=clean_string($_POST['doc_id']);
if($doc_id>=0)
{
    $query=mysql_query("SELECT doc_ids, document_names, file_ext, doc_viewability, doc_audiences, num_downloads FROM user_documents WHERE user_id=$_SESSION[id] LIMIT 1");
    if($query&&mysql_num_rows($query)==1)
    {
        $array=mysql_fetch_row($query);
        $doc_ids=explode('|^|*|', $array[0]);
        $document_names=explode('|^|*|', str_replace("'", "\'", $array[1]));
        $file_exts=explode('|^|*|', $array[2]);
        $doc_viewability=explode('|^|*|', $array[3]);
        $doc_audiences=explode('|^|*|', $array[4]);
        $num_downloads=explode('|^|*|', $array[5]);

        $temp_doc_ids=array();
        $temp_doc_names=array();
        $temp_file_exts=array();
        $temp_doc_viewability=array();
        $temp_doc_audiences=array();
        $temp_num_downloads=array();

        for($x = 0; $x < sizeof($doc_ids); $x++)
        {
            if($doc_ids[$x]!=$doc_id)
            {
                $temp_doc_ids[]=$doc_ids[$x];
                $temp_doc_names[]=$document_names[$x];
                $temp_file_exts[]=$file_exts[$x];
                $temp_doc_viewability[]=$doc_viewability[$x];
                $temp_doc_audiences[]=$doc_audiences[$x];
                $temp_num_downloads[]=$num_downloads[$x];
            }
            else
            {
                $path="./users/docs/$_SESSION[id]/archive/$doc_id.$file_exts[$x]";
                if(!file_exists($path))
                    exit();
            }
        }

        $doc_ids=implode('|^|*|', $temp_doc_ids);
        $doc_names=implode('|^|*|', $temp_doc_names);
        $file_exts=implode('|^|*|', $temp_file_exts);
        $doc_viewability=implode('|^|*|', $temp_doc_viewability);
        $doc_audiences=implode('|^|*|', $temp_doc_audiences);
        $num_downloads=implode('|^|*|', $temp_num_downloads);

        

        $query=mysql_query("UPDATE user_documents SET doc_ids='$doc_ids', document_names='$doc_names', file_ext='$file_exts', doc_viewability='$doc_viewability', doc_audiences='$doc_audiences', num_downloads='$num_downloads' WHERE user_id=$_SESSION[id]");
        if($query)
        {
            unlink($path);
            echo "File deleted";
        }
        else
        {
            echo "Something went wrong. We are working to fix it #2";
            log_error("delete_document.php: #2 ", mysql_error());
        }
    }
    else
    {
        echo "Something went wrong. We are working to fix it #1";
        log_error("delete_document.php: #1", mysql_error());
    }
}
else
{
    $query=mysql_query("SELECT doc_ids, file_ext FROM user_documents WHERE user_id=$_SESSION[id] LIMIT 1");
    if($query&&mysql_num_rows($query)==1)
    {
        $array=mysql_fetch_row($query);
        $doc_ids=explode('|^|*|', $array[0]);
        $file_exts=explode('|^|*|', $array[1]);

        for($x = 0; $x < sizeof($doc_ids); $x++)
        {
            $path="./users/docs/$_SESSION[id]/archive/$doc_ids[$x].$file_exts[$x]";
            unlink($path);
        }
        $query=mysql_query("UPDATE user_documents SET doc_ids='', file_ext='', document_names='', doc_descriptions='', doc_viewability='', doc_audiences='', num_downloads='' WHERE user_id=$_SESSION[id]");

    }
}
