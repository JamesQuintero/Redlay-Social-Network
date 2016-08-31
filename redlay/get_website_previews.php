<?php
@include('init.php');
include('universal_functions.php');
$allowed="users";
include('security_checks.php');



$website=clean_string($_POST['website']);
    

if(strstr($website, 'http://')==false&&strstr($website, 'https://')==false)
    $website="http://".$website;



$temp=explode('/', $website);

//if url ends in a folder
if(strpos($temp[sizeof($temp)-1], '.')==false||$temp[sizeof($temp)-1]=='')
{
    $temp_website=explode('/', $website);
    $string=array();
    $string[0]=$temp_website[0]."/".$temp_website[1]."/".$temp_website[2]."/";
    for($x = 3; $x < sizeof($temp_website); $x++)
        $string[1].=$temp_website[$x]."/";
    
    $website=$string[0].urlencode($string[1]);
}


//checks if website actually exists
$file_headers = @get_headers($website);
if($file_headers[0] != 'HTTP/1.1 404 Not Found')
    $contents=file_get_contents($website);
else
    $contents='';



$total_images=array();  
while(strpos($contents, "<img")&&sizeof($total_images)<25)
{
    //gets <img > tags
    $contents=stristr($contents, "<img");
    $total_images[]=substr($contents, 0, strpos($contents, ">")+1);
    $contents=substr($contents, (strpos($contents, ">")+1));
}



$final_images=array();
$heights=array();
$widths=array();

$temp_images=explode('.', $website);
$extension=strtolower($temp_images[sizeof($temp_images)-1]);

if($extension=='jpg'||$extension=='jpeg'||$extension=='png'||$extension=='gif')
{
    $file_headers = @get_headers($website);
    if($file_headers[0] != 'HTTP/1.1 404 Not Found')
    {
        list($width, $height)=getimagesize($website);
        if($width>50||$height>50)
        {
            $final_images[0]=$website;
            $heights[0]=$height;
            $widths[0]=$width;
        }
    }
    else
        $final_images[0]='';
}
else
{
    $final_images=array();
    
    for($x = 0; $x < sizeof($total_images); $x++)
    {
                $contents=$total_images[$x];
                
                while(strpos($contents, 'src'))
                {
                    $contents=stristr($contents, "src");
                    $begin_src=substr($contents, 5);

                    if(strpos($begin_src, '"')==true&&strpos($begin_src, "'")==true)
                    {
                        if(strpos($begin_src, "'")<strpos($begin_src, '"'))
                        {
                            $total_src=substr($begin_src, 0, strpos($begin_src, "'"));
                            $contents=substr($begin_src, strpos($begin_src, "'")+1);
                        }
                        else
                        {
                            $total_src=substr($begin_src, 0, strpos($begin_src, '"'));
                            $contents=substr($begin_src, strpos($begin_src, '"')+1);
                        }
                    }
                    else if(strpos($begin_src, '"')==true)
                    {
                        $total_src=substr($begin_src, 0, strpos($begin_src, '"'));
                        $contents=substr($begin_src, strpos($begin_src, '"')+1);
                    }
                    else
                    {
                        $total_src=substr($begin_src, 0, strpos($begin_src, "'"));
                        $contents=substr($begin_src, strpos($begin_src, "'")+1);
                    }
                    
                                    
                    //gets rid of / in front of url
                    while(strpos($total_src, '/')==0)
                        $total_src=substr($total_src, 1);


                    $file_headers = @get_headers($total_src);
                    if($file_headers[0] == 'HTTP/1.1 404 Not Found'||$file_headers==false)
                    {
                        $string='';
                        if(strstr($total_src, 'http://')==false&&strstr($total_src, 'https://')==false)
                        {
                            //gets original website url and explodes by .'s
                            $original=explode('/', $website);

                            if(strpos($total_src, "/")==0)
                                $string="http://".$original[2].$total_src;
                            else
                                $string="http://".$original[2]."/".$total_src;
                        }
                        else
                            $string=$total_src;
                    }
                    else
                        $string=$total_src;
                    


                    list($width, $height)=getimagesize($string);
                    if(($width>50||$height>50))
                    {
                        $final_images[]=$string;
                        $heights[]=$height;
                        $widths[]=$width;
                    }
                }

                
    }

}

$JSON=array();
$JSON['previews']=$final_images;
$JSON['website']=$website;
$JSON['heights']=$heights;
$JSON['widths']=$widths;
echo json_encode($JSON);
exit();