<?php
$app->loadConfig("web_auth",'auth');

$app->routeMiddleware([
    'auth.web' => App\Http\Middleware\WebAuthenticate::class,
    'guest.web' => App\Http\Middleware\RedirectIfAuthenticated::class,
]);

$app->middleware([
    Illuminate\Cookie\Middleware\EncryptCookies::class,
    Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
    Illuminate\Session\Middleware\StartSession::class,
    Illuminate\View\Middleware\ShareErrorsFromSession::class
]);

$app->register(\App\Providers\WebPaginationServiceProvider::class);

$app->group([
    'namespace' => 'App\Http\Controllers\Web',
    'middleware' => [
        \App\Http\Middleware\FanwePageCsrfToken::class
    ]
], function() use ($app)
{
    $app->get('/r/{id}', ['as'=>"index_r",'uses'=>'IndexController@index']); //首页(推荐)
    $app->get('/', ['as'=>"index",'uses'=>'IndexController@index']); //H5首页
    $app->get('app', ['as'=>"app",'uses'=>'IndexController@app']); //APP首页
    $app->any('user/logout', ['as'=>"user_logout",'uses'=>'UserController@logout']);
    $app->any('smsqueue', ['as'=>"smsqueue",'uses'=>'ToolController@smsQueue']);  //当系统支持redis时，短信队列
    $app->any('init', ['as'=>"init",'uses'=>'IndexController@init']);  //APP初始化
    $app->any('download', ['as'=>"download",'uses'=>'IndexController@download']);  //APP下载
    $app->get('article/{id}',['as'=>"article",'uses'=>'ArticleController@detail']);
    //红包
    $app->any('redpacket', ['as'=>"redpacket",'uses'=>'RedpacketController@index']);  //红包领取
});



//post用的分组
$app->group([
    'namespace' => 'App\Http\Controllers\Web',
//     'middleware' => [
//         \App\Http\Middleware\FanweApiCsrfToken::class
//     ]
], function() use ($app)
{
    $app->post('smscode', ['as'=>"smscode",'uses'=>'ToolController@smscode']);
    $app->post('emailcode', ['as'=>"emailcode",'uses'=>'ToolController@emailcode']);

    //登录注册操作
    $app->post('user/dologin', ['as'=>"user_dologin",'uses'=>'UserController@dologin']);
    $app->post('user/doregister', ['as'=>"user_doregister",'uses'=>'UserController@doregister']);
    $app->post('user/doforget', ['as'=>"user_doforget",'uses'=>'UserController@doforget']);

    //在web端代发接口
    $app->post('api/{group}.{type}/', ['as'=>"api",'uses'=>'ApiController@index']);
    $app->post('api/{group}.{type}/{class}', ['as'=>"api",'uses'=>'ApiController@index']);
    $app->post('api/{group}.{type}/{class}/{func}', ['as'=>"api",'uses'=>'ApiController@index']);
});


//$app->group([
//    'namespace' => 'App\Http\Controllers\Test',
//    'prefix' => 'test',
//], function() use ($app)
//{
//    $app->any('user/register', ['as'=>"test_user_register",'uses'=>'UserController@register']);
//});