<?php

include 'fun.php';
if(!isset($_GET['bid'])){exit('参数错误！');}
$url1="https://api.reader.m.so.com/mbook/index.php?m=mbook&c=index&a=detail&src=sohome&bid=".$_GET['bid'];
$result = get($url1);
//print_r($url1);
//print_r($result);
$html=$result->content;
//用正则方式取出总页数
$html = str_replace('小说频道_360搜索','',$html);
//print_r($html);
$int = preg_match_all('{<option value="([0-9]+)">第([0-9]+)页</option>}',$html,$page);
//取token
$token = get_txt($html,'window.mbook_token="','"</script> <link rel="stylesheet"');
$token = urlencode($token);
$site=$_GET["site"];
$sid=$_GET["sid"];
$bid=$_GET["bid"];
$url2="https://api.reader.m.so.com/mbook/index.php?a=getChapters&s=0&bid=".$_GET['bid']."&site=$site&sid=$sid&src=sohome&token=$token";
//echo "<a href='$url2'>$url2</a>";
//print_r($token);
preg_match_all('/<span id="site_text" class="tit" data-sid="([0-9]+)" data-site="(.[\w\W]*?)">/',$html,$s);

preg_match('/<span id="latest_chapter" onclick="readChapter\(this\)" data-href="(.*?)" data-cid="(.*?)" data-index="(.*?)">(.*?)<\/span><\/p><\/span><\/div><\/div>/',$html,$update);

//print_r($result->cookie);
$bookinfo='book_'.$bid.'={'.urlencode('""count":3562,site":"'.$site.'","sid":"'.$sid.'","readMode":2"').'};';

$header=array(
        "Accept:application/json",
        "Accept-Encoding:gzip, deflate, br",
        "Accept-Language:zh-CN,zh;q=0.9",
        "Connection:keep-alive",
        "Host:api.reader.m.so.com",
        "Cookie:".trim($result->cookie[0]).";".$result->cookie[1].";".$bookinfo."; __sid=54947323.1042108324227801100.1516293188951.6638; monitor_count=6; __gid=54947323.5652007.1513362968631.1516293205553.48;",
        "Referer:https://api.reader.m.so.com/mbook/index.php?m=mbook&c=index&a=detail&src=sohome&bid=".$_GET["bid"],
        "User-Agent:Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko)Chrome/63.0.3239.132 Safari/537.36",
        "X-Requested-With:XMLHttpRequest"
    );
//print_r(json_encode($header));
$option=array(
    CURLOPT_URL=>$url2,
    CURLOPT_RETURNTRANSFER=>1,
    CURLOPT_SSL_VERIFYHOST=>false,
    CURLOPT_HEADER=>1,
    CURLOPT_FORBID_REUSE=>0,
    CURLOPT_CONNECTTIMEOUT=>120,
    CURLOPT_HTTPHEADER=>$header,
    CURLOPT_SSL_VERIFYPEER=>false

);
$curl=curl_init($url2);
curl_setopt_array($curl,$option);
$res=curl_exec($curl);
if($res){
//    echo "Success";
}else{
//    echo "失败【错误代码".curl_errno($curl).",详细信息[".curl_error($curl)."]】";

}
$out=new stdClass();
$out->option=$option;
$out->res=$res;

print_r(json_encode($out));



//这里是页码
$page = isset($_GET['page']) ? $_GET['page']:0;
if($page != 0 && $page != 1){
	$page = ($page-1) . '0';
}elseif($page == 0 || $page == 1){
	$page = 0;
}


$str = api_file_get_contents($_GET["bid"],$result->cookie,'https://api.reader.m.so.com/mbook/index.php?a=getChapters&s='.$page.'&bid='.$_GET['bid'].'&site='.$s[2][0].'&sid='.$s[1][0].'&src=sohome&token='.$token);
//print_r($str); 
$str = json_decode($str,true);
$size=55;




if($format=="json"){
	$json=new stdClass();
	$json->page=$int; 
	$json->update=$update[4];
	$titleList=array();
	for($i=0;$i<count($str['data']['chapters']);$i++){
		$item=new stdClass();
		$title=$str['data']['chapters'][$i]['title'];
		if($page == 0 || $page == 1){
			$item->url=$CURRENT_PATH.'/book.php?url='.$str['data']['chapters'][$i]['url']."&title=".$title."&format=json";
			$item->title=$str['data']['chapters'][$i]['title'];
			// echo '<a href="./book.php?url='.$str['data']['chapters'][$i]['url'].'">'.$str['data']['chapters'][$i]['title'].'</a><br>';
		}else{
			$s = $i + 1;
			$item->url=$CURRENT_PATH.'/book.php?url='.$str['data']['chapters'][$s]['url']."&title=".$title."&format=json";
			$item->title=$str['data']['chapters'][$s]['title'];
			   // echo '<a href="./book.php?url='.$str['data']['chapters'][$s]['url'].'">'.$str['data']['chapters'][$s]['title'].'</a><br>';
		}
		array_push($titleList,$item);
	}
	$json->list=$titleList;
//	print_r(json_encode($json));
}else{
	$reData=new stdClass();
	// echo '当前文章有'.$int.'页!<br/>'.'最新节章:'.$update[4].';<br>';
	$data=$str["data"];//章节数组
	$count=$data["count"];//总章节数
	$allPage=round(($count/$size)+1,0);//总页数 +1为余下章节
	$moreCount=$count%$size;//余下章节数
	$currentPage=isset($_GET["page"])?$_GET["page"]:1;//当前页码
	
	$chaptersList=array();//章节列表
	if($currentPage<=$allPage){
		// print_r("条件成立");
		// print_r(json_encode($chaptersList));
		for($a=0;$a<$size/11;$a++){
			$da= api_file_get_contents('https://api.reader.m.so.com/mbook/index.php?a=getChapters&s='.$a.'&bid='.$_GET['bid'].'&site='.$s[2][0].'&sid='.$s[1][0].'&src=sohome&token='.$token);
			$da = json_decode($da,true);
			for($i=0;$i<count($da["data"]["chapters"]);$i++){
				array_push($chaptersList,$da["data"]["chapters"][$i]);
			}
			
		}
		print_r(json_encode($chaptersList));
	}else{
		$reData->error="$currentPage / ".$allPage." 超出页数范围";
		print_r(json_encode($reData));
	}
	// print_r("page=$count/$size=".$count/$size."more=".$moreCount.",allPage=$allPage");
	// for($a=0;$a<)
	
	// $data->page=$int;
	$data["page"]=$int;
	// print_r(json_encode($data));	
	for($i=0;$i<count($str['data']['chapters']);$i++){
		if($page == 0 || $page == 1){
			// echo '<a href="./book.php?url='.$str['data']['chapters'][$i]['url'].'">'.$str['data']['chapters'][$i]['title'].'</a><br>';
		}else{
			$s = $i + 1;
			// echo '<a href="./book.php?url='.$str['data']['chapters'][$s]['url'].'">'.$str['data']['chapters'][$s]['title'].'</a><br>';
		}
	}	
	function getItemList($page){
		
	}
	
}

