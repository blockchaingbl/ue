<?php
//模拟的授权跳转
require "CurlInvoker.php";
//$res = CurlInvoker::invoke("app.user/user/authlogin",['username'=>'13959199747','password'=>'111111'],true);

//$res = CurlInvoker::invoke("app.open/act/cp",['openid'=>'230c36aed809f38595e679d1c3636037','amount'=>'30'],true);

$res = CurlInvoker::invoke("app.open/act/pay",['openid'=>'230c36aed809f38595e679d1c3636037','amount'=>'30','security'=>'111111',"memo"=>"购买手机支付30LKC"],true);


header("Content-type:application/json;charset=utf-8");
echo json_encode($res,JSON_UNESCAPED_UNICODE);