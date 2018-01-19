<?php

include 'fun.php';


$header=array(
        "Accept:application/json",
        "Accept-Encoding:gzip, deflate, br",
        "Accept-Language:zh-CN,zh;q=0.9",
        "Connection:keep-alive",
        "Host:api.reader.m.so.com",
        "Cookie:",
        "Referer:",
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
$out->requestUrl=$url2;
$out->option=$option;
$out->res=$res;

print_r(json_encode($out));




