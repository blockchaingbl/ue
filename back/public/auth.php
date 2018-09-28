<?php
//模拟的授权跳转
require "CurlInvoker.php";

$security_params = trim($_REQUEST['_security_params']);
if($security_params)
{
    $result = CurlInvoker::decodeSecurityParams($security_params);
    print_r($result);
}
else
{
    $url = CurlInvoker::makeSecurityUrl("http://localhost:2222/#/login/auth",["callback"=>"http://www.fanwewallets.co/auth.php"],true,30);
    header('Location: '.$url);
}


