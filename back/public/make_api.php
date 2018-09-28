<?php

$time = time();
$platform_id = substr(md5($time),0,16);
$platform_secret =  md5($time);

$json = [
    "platform_id"=>$platform_id,
    "platform_secret"=>$platform_secret,
    "platform_name"=>"区块链平台",
    "platform_icon"=>"http://www.fanwewallets.co/icon.png"
];

$str = json_encode($json,JSON_UNESCAPED_UNICODE);

//file_put_contents("../storage/apikey/".$platform_id.".key",$str);
header('content-type:application/json;charset=utf8');
echo $str;