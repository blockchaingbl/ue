<?php
$app->loadConfig("web_auth",'auth');

//api定时器接口路由， 无需session
$app->group([
    'namespace' => 'App\Http\Controllers\Web'
], function() use ($app)
{
    $app->any('queue/orderqueue', ['as'=>"orderqueue",'uses'=>'OrderController@cancelQueue']);  //订单取消定时器
    $app->any('queue/pricefetch', ['as'=>"price-fetch",'uses'=>'PriceController@fetch']);  //行情价格采集器
    $app->any("queue/downblock", ['as'=>"queue_down_block",'uses'=>'ApiController@downblock']);
    $app->any("queue/repairblock", ['as'=>"queue_repair_block",'uses'=>'ApiController@repairblock']);
    $app->any("queue/down", ['as'=>"queue_down",'uses'=>'ApiController@down']);
    $app->any("queue/downtoken", ['as'=>"queue_down_token",'uses'=>'ApiController@downtoken']);
    $app->any("queue/confirm", ['as'=>"queue_confirm",'uses'=>'ApiController@confirm']);
    $app->any("queue/confirmtoken", ['as'=>"queue_confirm_token",'uses'=>'ApiController@confirmtoken']);
    $app->any("queue/push", ['as'=>"queue_push",'uses'=>'ApiController@push']);
    $app->any("queue/pushtoken", ['as'=>"queue_push_token",'uses'=>'ApiController@pushtoken']);
    $app->any("queue/transunlock", ['as'=>"transunlock",'uses'=>'LockTransferController@transunlock']);
    $app->any("queue/withdraw", ['as'=>"transunlock",'uses'=>'ApiController@withdraw']);
    $app->any("queue/sugarfree", ['as'=>"sugarfree",'uses'=>'SugarController@free']);
    $app->any("queue/sugarback", ['as'=>"sugarback",'uses'=>'SugarController@back']);
    $app->any("queue/otcfree", ['as'=>"otcfree",'uses'=>'OtcunlockController@otcfree']);
    $app->any('queue/exrate',['as'=>'exrate','uses'=>'ExchangeRateController@fetch']);

    $app->any('queue/calc',['as'=>'calc_day_amount','uses'=>'CalcController@calc']);
    $app->any('queue/calc_day_amount',['as'=>'calc_month_amount','uses'=>'CalcController@calc_day_amount']);
    $app->any('queue/calc_month_amount',['as'=>'calc_month_amount','uses'=>'CalcController@calc_month_amount']);
});
