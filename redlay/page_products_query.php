<?php
@include('init.php');
include('universal_functions.php');
$allowed="both";
include("security_checks.php");
// add_view('page_products_query');

$ID=(int)($_POST['page_id']);
$category=mysql_real_escape_string($_POST['category']);
$sort=mysql_real_escape_string($_POST['sort_by']);

if($ID>=1&&page_id_exists($ID))
{
    $query=mysql_query("SELECT product_number, product_name, product_price, product_purchase_link, product_link, product_category, product_categories FROM page_data WHERE page_id=$ID LIMIT 1");
    if($query&&mysql_num_rows($query)==1)
    {
        $array=mysql_fetch_array($query);
        $names=explode('|^|*|', $array[1]);
        $image_numbers=explode('|^|*|', $array[0]);
        $purchase_links=explode('|^|*|', $array[3]);
        $prices=explode('|^|*|', $array[2]);
        $links=explode('|^|*|', $array[4]);
        $product_category=explode('|^|*|', $array[5]);
        $categories=explode('|^|*|', $array[6]);


        $temp_product_names=array();
        $temp_prices=array();
        $temp_purchase_links=array();
        $temp_links=array();
        $temp_categories=array();
        for($x = 0; $x < $image_numbers[0]; $x++)
        {
            if($product_category[$x]==$category||$category=="all")
            {
                $dimentions=getimagesize("./users/pages/$ID/products/$x.jpg");
                $image_heights[]=$dimentions[1];
                $image_widths[]=$dimentions[0];
                $images[]="./users/pages/$ID/products/$x.jpg";
                $temp_product_names[]=$names[$x];
                $temp_prices[]=$prices[$x];
                $temp_purchase_links[]=$purchase_links[$x];
                $temp_links[]=$links[$x];
                $temp_categories[]=$categories[$x];
            }
        }
        $names=$temp_product_names;
        $prices=$temp_prices;
        $purchase_links=$temp_purchase_links;
        $links=$temp_links;
        $categories=$temp_categories;
        
        $JSON=array();
        $JSON['product_names']=$names;
        $JSON['product_prices']=$prices;
        $JSON['product_buy_links']=$purchase_links;
        $JSON['product_links']=$links;
        $JSON['product_category']=$product_category;
        $JSON['image_heights']=$image_heights;
        $JSON['image_widths']=$image_widths;
        $JSON['images']=$images;
        $JSON['categories']=$categories;
        echo json_encode($JSON);
        exit();
    }
}