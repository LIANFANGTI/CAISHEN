<?php

include 'fun.php';

if(!isset($_GET['bid'])){exit('参数错误！');}
$html = GetUrl('http://api.reader.m.so.com/mbook/index.php?m=mbook&c=index&a=sources&src=sohome&bid='.$_GET['bid']);
preg_match_all('{<li data-site="(.*?)" data-sid="([0-9]+)"><h1>(.*?)<a>(.*?)</a></h1><p>(.*?)</p></li>}',$html,$list);


if($format!="json"){
	for($i=0;$i<count($list[3]);$i++){
		echo '<a href="./info.php?sid='.$list[2][$i].'&site='.$list[1][$i].'&bid='.$_GET['bid'].'">'.$list[3][$i].'</a><br>';
	}
}else{
	$siteList=array();
	// print_r($_SERVER);

	// print_r($CURRENT_PATH);
	for($i=0;$i<count($list[0]);$i++){
		$item=new stdClass();
		$item->title=$list[3][$i];
		$item->sid=$list[2][$i];
		$item->site=$list[1][$i];
		$item->new=$list[4][$i];
		$item->url=$CURRENT_PATH.'/info.php?sid='.$list[2][$i].'&site='.$list[1][$i].'&bid='.$_GET['bid']."&format=json";
		array_push($siteList,$item);
		// echo '<a href="./info.php?sid='.$list[2][$i].'&site='.$list[1][$i].'&bid='.$_GET['bid'].'">'.$list[3][$i].'</a><br>';
	}
	print_r (json_encode($siteList));
}



?>