<?php
@include('init.php');
include('universal_functions.php');
$allowed="users";
include('security_checks.php');


$url=clean_string($_POST['video_url']);

$first=strpos($url, '&');
if($first!=false)
    $url=substr($url, 0, $first);
$url=str_replace('watch?v=', 'embed/', $url);

if($url!=''&&(strstr($url, 'http://youtube.com')!=false||strstr($url, 'http://www.youtube.com')!=false||strstr($url, 'www.youtube.com')!=false||strstr($url, 'youtube.com')!=false))
{
    $query=mysql_query("SELECT * FROM public WHERE num=1 LIMIT 1");
    if($query && mysql_num_rows($query)==1)
    {
        $array=mysql_fetch_array($query);
        $videos=explode('|^|*|', $array['videos']);
        //removes old video if video list is getting too long
        if(sizeof($videos)==200)
        {
            $num=0;
            $temp_array=$videos;
            for($x = 1; $x < sizeof($videos); $x++)
            {
                $videos[$num]=$temp_array[$x];
                $num++;
            }
        }

        if($array['videos']=='')
            $videos[0]=$url;
        else
            $videos[]=$url;

        $videos=implode('|^|*|', $videos);
        $query=mysql_query("UPDATE public SET videos='$videos' WHERE num=1 LIMIT 1");
        if($query)
            echo "Video posted!";
        else
            echo "Video failed!";
    }
}
else
    echo "Invalid youtube link!";
