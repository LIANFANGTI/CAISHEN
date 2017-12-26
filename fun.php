<?php
$format=isset($_GET["format"])?$_GET["format"]:"json";
header("Content-type: text/".$format."; charset=UTF-8"); 
$CURRENT_PATH =$_SERVER['REQUEST_SCHEME']."://".$_SERVER['HTTP_HOST']."".str_replace('\\','/',dirname($_SERVER['SCRIPT_NAME']))."";
 function gbk_to_utf8($data){  
    if(is_array($data)){  
      return array_map('gbk_to_utf8', $data);  
    }  
	// print_r($data);
    return iconv('gbk','utf-8',$data);  
  } 
function api_file_get_contents($url){
	print_r($url."<br>");
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	$headers = array(
		'User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2623.221 Safari/537.36 SE 2.X MetaSr 1.0',
		'Accept: application/json',
		'Connection: Keep-Alive',
		'Keep-Alive: 300',
		'Referer: http://api.reader.m.so.com/mbook/index.php?m=mbook&c=index&a=detail&src=sohome&bid=12353547927232841753'
	);
	curl_setopt($ch, CURLOPT_FORBID_REUSE, 0);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 120);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	$api_str=curl_exec($ch);
	curl_close($ch);
	return $api_str;
}

function GetUrl($url,$sid='',$site='',$bid=''){
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, SetHeader($url,$sid,$site,$bid));
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, 0);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	$output = curl_exec($ch);
	$httpCode = curl_getinfo($ch);
	curl_close($ch);
	if($httpCode['http_code']==301 || $httpCode['http_code']==302){
		return GetUrl($httpCode['redirect_url']);
	}
	return $output;
}
	
function SetHeader($url,$sid='',$site='',$bid=''){
	preg_match('/[a-zA-Z0-9][-a-zA-Z0-9]{0,62}(\.[a-zA-Z0-9][-a-zA-Z0-9]{0,62})+\.?/',$url,$host);
	$header[0] = "Accept:text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8";
	$header[] = "Accept-Language:zh-CN,zh;q=0.8";
	$header[] = "Cache-Control:max-age=0";
	$header[] = "Connection:keep-alive";
	$header[] = "Host:".$host[0];
	$header[] = "Upgrade-Insecure-Requests:1";
	$header[] = "Pragma:no-cache";
	if($sid!='' || $site!=''){
		$header[] = 'Cookie: book_'.$bid.'={"site":"'.$site.'","sid":"'.$sid.'","count":4150,"readMode":1};';
	}
	$header[] = 'User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2623.221 Safari/537.36 SE 2.X MetaSr 1.0';
	return $header;		
}

function get_txt($str, $leftStr, $rightStr){
	$left = strpos($str, $leftStr);
	$right = strpos($str, $rightStr,$left);
	if($left < 0 or $right < $left) return '';
	return substr($str, $left + strlen($leftStr), $right-$left-strlen($leftStr));
}	

?>