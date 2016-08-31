<?php
@include('init.php');
if(!isset($_SESSION['id']))
{
    header("Location: http://www.redlay.com/index.php");
    exit();
}
include('universal_functions.php');
include('security_checks.php');


///DEPRECATED FEATURE
exit();

//$description=mysql_real_escape_string($_POST['upload_document_description']);
$groups=$_POST['groups'];
$viewability=$_POST['viewability_options'];
$num_files=sizeof($_FILES['file']['size']);

if(isset($_FILES['file']['tmp_name'][0]))
{
    //checks if files have been uploaded
    //also gets the size of files to see if it doesn't exceed space limit
    $total=0;
    $bool=true;
    for($x = 0; $x < $num_files; $x++)
    {
        if($_FILES['file']['size'][$x]==0)
            $bool=false;
        else
            $total+=$_FILES['file']['size'][$x];
    }

    if($bool)
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
            $groups[0]='Everyone';


        $path="./users/docs/$_SESSION[id]/archive";
        $directory=opendir($path);
        while($temp_file=readdir($directory))
        {
            if(substr($temp_file, 0, 1)!=".")
                $total=$total+filesize($path."/".$temp_file);
        }
        closedir($directory);


        //if the user hasn't exceeded space limit
        if(($total<=2147483648)||(has_redlay_gold($_SESSION['id'])&&$total<=4294967296))
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

                    for($x = 0; $x < $num_files; $x++)
                    {

                        $doc_name=explode('.', $_FILES['file']['name'][$x]);
                        if(sizeof($doc_name)>1)
                        {
                            $bool=false;
                            for($y = 0; $y < sizeof($document_names); $y++)
                            {
                                if($document_names[$y]==$_FILES['file']['name'][$x])
                                {
                                    $bool=true;
                                    $index=$y;
                                }
                            }

                            $doc_id=sha1(uniqid(rand()));

                            if($bool==false)
                            {
                                if($document_names[0]=='')
                                {
                                    $doc_ids[0]=$doc_id;
                                    $document_names[0]=mysql_real_escape_string($_FILES['file']['name'][$x]);
                                    $file_exts[0]=end($doc_name);
                                    $doc_viewability[0]=$viewability;
                                    $doc_audiences[0]=implode('|%|&|', $groups);
                                    $num_downloads[0]=0;
                                }
                                else
                                {
                                    $doc_ids[]=$doc_id;
                                    $document_names[]=mysql_real_escape_string($_FILES['file']['name'][$x]);
                                    $file_exts[]=end($doc_name);
                                    $doc_viewability[]=$viewability;
                                    $doc_audiences[]=implode('|%|&|', $groups);
                                    $num_downloads[]=0;
                                }

                                move_uploaded_file($_FILES['file']['tmp_name'][$x], $path."/$doc_id.".end($doc_name));
                            }
                            else
                            {
                                    $doc_id=$doc_ids[$index];
                                    $file_exts[$index]=end($doc_name);
                                    $doc_viewability[$index]=$viewability;
                                    $doc_audiences[$index]=implode('|%|&|', $groups);
                                    $num_downloads[$index]=0;
                                    unlink($path."/$doc_id.".end($doc_name));

                                move_uploaded_file($_FILES['file']['tmp_name'][$x], $path."/$doc_id.".end($doc_name));

                            }
                        }
                    }

                    $doc_ids=implode('|^|*|', $doc_ids);
                    $document_names=implode('|^|*|', $document_names);
                    $file_exts=implode('|^|*|', $file_exts);
                    $doc_viewability=implode('|^|*|', $doc_viewability);
                    $doc_audiences=implode('|^|*|', $doc_audiences);
                    $num_downloads=implode('|^|*|', $num_downloads);

                    $query=mysql_query("UPDATE user_documents SET doc_ids='$doc_ids', document_names='$document_names', file_ext='$file_exts',  doc_viewability='$doc_viewability', doc_audiences='$doc_audiences', num_downloads='$num_downloads' WHERE user_id=$_SESSION[id]");
                    if($query)
                    {
                        header("Location: http://www.redlay.com/documents.php");
                    }
                    else
                    {
                        echo "File faled to upload! We are working on it";
                        log_error("update_document.php: 2 :", mysql_error());
                    }
                }
                else
                {
                    echo "Something went wrong. We are working to fix it";
                    log_error("upload_document.php: 1 : ", mysql_error());
                }
        }
        else
            echo "You don't have enough free space to upload this document.";
    }
    else
        echo "Please choose a file";
}
else
    echo "Please choose a file";