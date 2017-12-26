<?php
header("Content-type: text/html; charset=UTF-8");
include 'fun.php';
$html = GetUrl($_GET['url']);
$html = str_replace('小说频道_360搜索','',$html);
echo $html;
?>