<?php
//@include('init.php');
//include('universal_functions.php');
//$allowed="users";
//include('security_checks.php');
//
//$user_id=(int)($_POST['user_id']);
//$picture_id=clean_string($_POST['picture_id']);
//$type=clean_string($_POST['type']);
//
//
//if($type=='user')
//{
//    $query=mysql_query("SELECT pictures, picture_dislikes FROM pictures WHERE user_id=$user_id LIMIT 1");
//    if($query && mysql_num_rows($query)==1)
//    {
//        $array=mysql_fetch_row($query);
//        $pictures=explode('|^|*|', $array[0]);
//        $dislikes=explode('|^|*|', $array[1]);
//
//        $index=-1;
//        for($x = 0; $x < sizeof($pictures); $x++)
//        {
//            if($pictures[$x]==$picture_id)
//                $index=$x;
//        }
//
//        if($index!=-1)
//        {
//            $dislikes[$index]=explode('|%|&|', $dislikes[$index]);
//            $temp_dislikes=array();
//            for($x = 0; $x < sizeof($dislikes[$index]); $x++)
//            {
//                if($_SESSION['id']!=$dislikes[$index][$x])
//                    $temp_dislikes[]=$dislikes[$index][$x];
//            }
//
//
//            $dislikes[$index]=implode('|%|&|', $temp_dislikes);
//            $dislikes=implode('|^|*|', $dislikes);
//
//            $query=mysql_query("UPDATE pictures SET picture_dislikes='$dislikes' WHERE user_id='$user_id'");
//        }
//    }
//}
//else if($type=='page')
//{
//
//}
?>
