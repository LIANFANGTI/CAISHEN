<?php
//更新
include 'fun.php';
$html = GetUrl($_GET['url']);
// print_r($html);	
header("Content-type:text/".$format.";charset=GBK");
if($format=="json"){

	$preg = "/<script[\s\S]*?<\/script>/i";//去除Javascript脚本
	$html  = preg_replace($preg,'',$html); 

	$pregCss="'<style[^>]*?>.*?</style>'si";//去除CSS代码
	$html  = preg_replace($pregCss,'',$html); 


	$html = strip_tags($html);//去除html标签
	$pattern = '/\s/';//去除空白


	$html  = preg_replace($pattern,'',$html);
	$html = str_replace("&nbsp","",$html);
	$title=isset($_GET["title"])?$_GET["title"]:"";
	$RS=array('title'=>mb_convert_encoding($title,"GBK", "auto"),'content'=>$html);
	// $RS->content=$html;
	print_r(json_encode(gbk_to_utf8($RS)));
	
}else{
	
	$preg = "/<script[\s\S]*?<\/script>/i";//去除Javascript脚本
	$html  = preg_replace($preg,'',$html); 
	// $html=mb_convert_encoding($html,"GBK", "auto");
	echo $html;
}
// print_r($RS);
?>