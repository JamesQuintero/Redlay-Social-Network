<?php
@include('init.php');
include('universal_functions.php');
$allowed="both";
include("security_checks.php");


//DEPRECATED FEATURE

// add_view('send_document');
$ID=clean_string($_POST['user_to']))));
$file_id=clean_string($_POST['file_id']))));
$file_name=clean_string($_POST['file_name'])))));

$query=mysql_query("SELECT * FROM user_documents WHERE user_id=$_SESSION[id] LIMIT 1");
if($query&&mysql_num_rows($query)==1)
{
    $array=mysql_fetch_array($query);
    $user_sent_to=explode('|^|*|', $array['user_sent_to']);
    $file_sent=explode('|^|*|', $array['file_sent']);
    $timestamps_sent=explode('|^|*|', $array['timestamps_sent']);
    if($array['user_sent_to']=='')
    {
        $user_sent_to[0]=$ID;
        $file_sent[0]=$file_name;
        $timestamps_sent[0]=get_date();
    }
    else
    {
        $user_sent_to[]=$ID;
        $file_sent[]=$file_name;
        $timestamps_sent[]=get_date();
    }
    $user_sent_to=implode('|^|*|', $user_sent_to);
    $file_sent=implode('|^|*|', $file_sent);
    $timestamps_sent=implode('|^|*|', $timestamps_sent);
    $query=mysql_query("UPDATE user_documents SET user_sent_to='$user_sent_to', file_sent='$file_sent', timestamps_sent='$timestamps_sent' WHERE user_id=$_SESSION[id]");
    if($query)
    {
        $query=mysql_query("SELECT * FROM user_documents WHERE user_id=$ID LIMIT 1");
        if($query&&mysql_num_rows($query)==1)
        {
            $array=mysql_fetch_array($query);
            $user_received_from=explode('|^|*|', $array['user_received_from']);
            $file_received=explode('|^|*|', $array['file_received']);
            $timestamps_received=explode('|^|*|', $array['timestamps_received']);
            $new_document_alerts=$array['new_document_alerts'];
            if($array['user_received_from']=='')
            {
                $user_received_from[0]=$_SESSION['id'];
                $file_received[0]=$file_name;
                $timestamps_received[0]=get_date();
            }
            else
            {
                $user_received_from[]=$_SESSION['id'];
                $file_received[]=$file_name;
                $timestamps_received[]=get_date();
            }
            $user_received_from=implode('|^|*|', $user_received_from);
            $file_received=implode('|^|*|', $file_received);
            $timestamps_received=implode('|^|*|', $timestamps_received);
            $new_document_alerts++;
            $query=mysql_query("UPDATE user_documents SET user_received_from='$user_received_from', file_received='$file_received', timestamps_received='$timestamps_received', new_document_alerts='$new_document_alerts' WHERE user_id=$ID");
            if($query)
            {
                $path="./users/docs/$ID/received";
                $directory=opendir($path);
                $num=0;
                while($file=readdir($directory))
                {
                    if(substr($file, 0, 1)!=".")
                        $num++;
                }
                closedir($directory);
                $path="./users/docs/$ID/received/$num.txt";
                $path2='./users/docs/'.$_SESSION[id].'/archive/'.$file_id.'.txt';
                copy($path2, $path);
                if(file_exists($path2))
                    echo "File sent!";
                else
                    echo"File failed to send";
            }
            else
                echo "Something went wrong.";
        }
        else
            echo "Failed to save data 2 ";
    }
    else
        echo "Failed to save data ";
}