<?php
@include('init.php');
if(!isset($_SESSION['id'])&&!isset($_SESSION['page_id']))
{
    header("Location: http://m.redlay.com/");
    exit();
}
else
{
    echo $_FILES['image']['tmp_name'];
    include('../upload_picture.php');
}
?>