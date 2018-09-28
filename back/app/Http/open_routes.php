<?php
$app->loadConfig("web_auth",'auth');
//config(["app.api_crypto"=>false]);

//开放平台的路由组
//组件结构
$app->group([
    'namespace' => 'App\Open\Controllers\Open',
], function() use ($app)
{
    $app->any('{group}.{type}', ['as'=>"index",'uses'=>'IndexController@index']);
    $app->any('{group}.{type}/{class}', ['as'=>"index",'uses'=>'IndexController@index']);
    $app->any('{group}.{type}/{class}/{func}', ['as'=>"index",'uses'=>'IndexController@index']);
});