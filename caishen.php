<?php
    
	include 'fun.php';
	include './lib/httplib.class.php';
	
	header("content-type:text/json;charset=utf-8");
    //小说名称
    $bookName = $_GET['bookName'];
    $bookName = urlencode($bookName);
    

    //获取token
    $url1 = 'https://api.reader.m.so.com/mbook/index.php?q='.$bookName.'&src=sohome&a=search';
//	 print_r("<a href='".$url1."'>第一次请求地址$url1</a><br>");
    // echo '第一次请求url:<br>'.$url,'<br>';

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.10; rv:40.0) Gecko/20100101 Firefox/40.0');
    curl_setopt($ch, CURLOPT_HEADER, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $rs = curl_exec($ch);
    
//  print_r($rs);
    curl_close($ch);
    
//  $d=GetUrl($url1);
//	print_r($d);
    //解析token
  
//  $token=1;
    $token = explode('window.mbook_token="', $rs);
    $token = explode('"</script> </head>', $token[1]);
    $token = trim($token[0]);
    $token = urlencode(utf8_encode($token));
//	 print_r($token);
//     echo '获取到的token:<br>',$token,'<br>';

    //抓取数据
     $url = 'https://api.reader.m.so.com/mbook/index.php?q='.$bookName.'&src=sohome&a=querySearch&s=0&n=20&token='.$token;
//	 print_r("<a href='".$url."]'>生成请求地址$url</a><br>");
    // echo '第二次请求url:<br>'.$url,'<br>';

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.10; rv:40.0) Gecko/20100101 Firefox/40.0');
    curl_setopt($ch, CURLOPT_HEADER, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_REFERER, $url1); 
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $rs = curl_exec($ch);
    curl_close($ch);
  
    //整理数据
    $data = explode('Accept-Encoding', $rs);
	 // print_r($data);
    $data = trim($data[1], '"');

    // echo '<hr>抓取结果:<br>';
	$data=json_decode($data); 	
	if($data->errno==0){
		$data=$data->data;
		for($i=0;$i<count($data);$i++){
			$data[$i]->url=$CURRENT_PATH."/index.php?bid=".$data[$i]->id;
			
		}
		print_r(json_encode($data));
		 // print_r($data);
	}else{
		
	}
   