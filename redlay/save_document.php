<?php
@include('init.php');
if(!isset($_SESSION['id']))
{
    header("Location: http://www.redlay.com/index.php");
    exit();
}
include('universal_functions.php');
include('security_checks.php');
//add_view('save_new_document');


//DEPRECATED FEATURE

$name=mysql_real_escape_string(htmlentities($_GET['document_title'], ENT_COMPAT, 'UTF-8'));
$doc_id=(int)($_GET['doc_id']);
$viewability=$_POST['doc_viewability'];

if($doc_id==-1)
{
    if($name!='')
    {
        if(isset($_POST['viewability_options'])&&$viewability=='public')
        {
            if(!empty($groups)&&!in_array('Everyone', $groups))
            {
                //checks if all groups are valid
                $bool=true;
                for($x = 0; $x < sizeof($groups); $x++)
                {
                    if(!is_valid_audience($groups[$x]))
                       $bool=false;
                }
            }
            else
            {
                $groups[0]='Everyone';
                $bool=true;
            }

            if($bool==false)
            {
                $groups=array();
                $viewability='private';
            }
        }
        else
            $groups=array();


        
            $contents=$_GET['document_contents'];
            $query=mysql_query("SELECT doc_ids, document_names, file_exts, doc_viewability, doc_audiences, num_downloads FROM user_documents WHERE user_id=$_SESSION[id] LIMIT 1");
            if($query&&mysql_num_rows($query)==1)
            {
                $array=mysql_fetch_row($query);
                $doc_ids=explode('|^|*|', $array[0]);
                $file_names=explode('|^|*|', $array[1]);
                $file_exts=explode('|^|*|', $array[2]);
                $doc_viewability=explode('|^|*|', $array[3]);
                $doc_audiences=explode('|^|*|', $array[4]);
                $num_downloads=explode('|^|*|', $array[5]);

                //checks if user has existing documents

                if($array[0]!='')
                    $num=(end($doc_ids))+1;
                else
                    $num=0;

                //creates a new text file and writes conents to it
                $path="./users/docs/$_SESSION[id]/archive/$num.txt";
                $writer=fopen($path, "w");
                fwrite($writer, utf8_encode(stripslashes($contents)));
                fclose($writer);

                //adds title
                if($array[1]!='')
                {
                    $file_names[]=$name;
                    $doc_ids[]=$num;
                    $file_exts[]='txt';
                    $doc_viewability[]=$doc_viewability;
                    $doc_audiences[]=implode('|%|&|', $groups);
                    $num_downloads[]=0;
                }
                else if($array[1]=='')
                {
                    $file_names[0]=$name;
                    $doc_ids[0]=$num;
                    $file_exts[0]='txt';
                    $doc_viewability[0]=$doc_viewability;
                    $doc_audiences[0]=implode('|%|&|', $groups);
                    $num_downloads[0]=0;
                }

                $file_names=implode('|^|*|', $file_names);
                $doc_ids=implode('|^|*|', $doc_ids);
                $file_exts=implode('|^|*|', $file_exts);
                $doc_viewability=implode('|^|*|', $doc_viewability);
                $doc_audiences=implode('|^|*|', $doc_audiences);
                $num_downloads=implode('|^|*|', $num_downloads);
                $query=mysql_query("UPDATE user_documents SET doc_ids='$doc_ids', document_names='$file_names', file_exts='$file_exts', doc_viewability='$doc_viewability', doc_audiences='$doc_audiences', num_downloads='$num_downloads' WHERE user_id=$_SESSION[id]");
                if($query)
                    echo "File added!";
                else
                {
                    echo "File could not be saved";
                    log_error("save_document.php: ", mysql_error());
                }
        }
        else
        {
            echo "Something went wrong. We are working to fix it";
            log_error('save_document.php', mysql_error());
        }
    }
    else
        echo "Please enter a name";
}
else if(file_exists("./users/docs/$_SESSION[id]/archive/$doc_id.txt"))
{
    $query=mysql_query("SELECT doc_ids, document_names FROM user_documents WHERE user_id=$_SESSION[id] LIMIT 1");
    if($query&&mysql_num_rows($query)==1)
    {
        $array=mysql_fetch_row($query);
        $doc_ids=explode('|^|*|', $array[0]);
        $doc_names=explode('|^|*|', $array[1]);

        $index=-1;
        for($x = 0; $x < sizeof($doc_ids); $x++)
        {
            if($doc_ids[$x]==$doc_id)
                $index=$x;
        }

        $doc_names[$index]=$name;
        $doc_names=implode('|^|*|', $doc_names);

        $query=mysql_query("UPDATE user_documents SET document_names='$doc_names' WHERE user_id=$_SESSION[id]");
        if($query)
        {
            //saves contents to current document
            $contents=$_POST['document_contents'];
            $path="./users/docs/$_SESSION[id]/archive/$doc_id.txt";
            $writer=fopen($path, "w");
            fwrite($writer, utf8_encode(stripslashes($contents)));
            fclose($writer);
            echo "File modified!";
        }
        else
        {
            echo "Something went wrong. We are working to fix it";
            log_error('save_document.php', mysql_error());
        }
    }
    else
    {
        echo "Something went wrong. We are working to fix it";
        log_error('save_document.php', mysql_error());
    }
}
else
    echo "Invalid document ID";
