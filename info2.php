<?php
header("Content-type: text/html; charset=UTF-8");
include 'fun.php';
if(!isset($_GET['bid'])){exit('参数错误！');}

$html = GetUrl("http://api.reader.m.so.com/mbook/index.php?m=mbook&c=index&a=detail&src=sohome&bid=".$_GET['bid'],$_GET['sid'],$_GET['site'],$_GET['bid']);
//用正则方式取出总页数
$html = str_replace('小说频道_360搜索','',$html);

$int = preg_match_all('{<option value="([0-9]+)">第([0-9]+)页</option>}',$html,$page);
// print_r($int);
//取token
$token = get_txt($html,'window.mbook_token="','"</script> <link rel="stylesheet"');
$token = urlencode($token);
preg_match_all('/<span id="site_text" class="tit" data-sid="([0-9]+)" data-site="(.[\w\W]*?)">/',$html,$s);

preg_match('/<span id="latest_chapter" onclick="readChapter\(this\)" data-href="(.*?)" data-cid="(.*?)" data-index="(.*?)">(.*?)<\/span><\/p><\/span><\/div><\/div>/',$html,$update);
$json=new  StdClass();


//这里是页码
$page = isset($_GET['page']) ? $_GET['page']:0;
if($page != 0 && $page != 1){
	$page = ($page-1) . '0';
}elseif($page == 0 || $page == 1){
	$page = 0;
}
$json->page=$int; 
$json->update=$update[4];
$titleList=array();

// echo '当前文章有'.$int.'页!<br/>'.'最新节章:'.$update[4].';<br>';

$str = api_file_get_contents('http://api.reader.m.so.com/mbook/index.php?a=getChapters&s='.$page.'&bid='.$_GET['bid'].'&site='.$s[2][0].'&sid='.$s[1][0].'&src=sohome&token='.$token);
// print_r($str);
$str = json_decode($str,true);

for($i=0;$i<count($str['data']['chapters']);$i++){
	$item=new stdClass();
	$title=$str['data']['chapters'][$i]['title'];
	if($page == 0 || $page == 1){
		$item->url=$CURRENT_PATH.'/book.php?url='.$str['data']['chapters'][$i]['url']."&title=".$title;
		$item->title=$str['data']['chapters'][$i]['title'];
		// echo '<a href="./book.php?url='.$str['data']['chapters'][$i]['url'].'">'.$str['data']['chapters'][$i]['title'].'</a><br>';
	}else{
		$s = $i + 1;
		$item->url=$CURRENT_PATH.'/book.php?url='.$str['data']['chapters'][$s]['url']."&title=".$title;
		$item->title=$str['data']['chapters'][$s]['title'];
		   // echo '<a href="./book.php?url='.$str['data']['chapters'][$s]['url'].'">'.$str['data']['chapters'][$s]['title'].'</a><br>';
	}
	array_push($titleList,$item);
}
$json->list=$titleList;
print_r(json_encode($json));

