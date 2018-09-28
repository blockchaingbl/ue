<?php

require_once __DIR__.'/../vendor/autoload.php';

// Dotenv::load(__DIR__.'/../');

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| Here we will load the environment and create the application instance
| that serves as the central piece of this framework. We'll use this
| application as an "IoC" container and router for this framework.
|
*/

$app = new App\NhdApplication(
    realpath(__DIR__.'/../')
);

$nhd_config = require $app->basePath("config/nhd_config.php");

$app->loadConfig("app");
$app->loadConfig("database");
$app->loadConfig("cache");
$app->loadConfig("session");
$app->withFacades();
$app->withEloquent();


date_default_timezone_set(config("app.timezone"));
if(!config("app.debug"))
{
    error_reporting(0);
}
else
{
    error_reporting(E_ALL^E_NOTICE^E_WARNING);
}

/*
|--------------------------------------------------------------------------
| Register Container Bindings
|--------------------------------------------------------------------------
|
| Now we will register a few bindings in the service container. We will
| register the exception handler and the console kernel. You may add
| your own bindings here if you like or you can make another file.
|
*/

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);


//本地调试用的memcached支持
$app->register(App\Providers\AliMemcacheSASLSessionServiceProvider::class);
$app->register(App\Providers\AliMemcacheSASLCacheServiceProvider::class);

//验证码
$app->register(Aishan\LumenCaptcha\CaptchaServiceProvider::class);
class_alias('Aishan\LumenCaptcha\Facades\Captcha','Captcha');
//二维码
$app->register(SimpleSoftwareIO\QrCode\QrCodeServiceProvider::class);
class_alias('SimpleSoftwareIO\QrCode\Facades\QrCode','QrCode');

//Excel扩展
$app->register(Maatwebsite\Excel\ExcelServiceProvider::class);


/**
 * captcha配置
 */
config(['captcha'=>
    [
        'useful_time'=>5,//验证码有效时间，单位（分钟）
        'captcha_characters'=>'2346789abcdefghjmnpqrtuxyzABCDEFGHJMNPQRTUXYZ',
        'sensitive' =>false,//验证码大小写是否敏感
        'login'   => [//登陆验证码样式
            'length'    => 4,//验证码字数
            'width'     => 120,//图片宽度
            'height'    => 36,//字体大小和图片高度
            'angle'    => 10,//验证码中字体倾斜度
            'lines'    => 2,//生成横线条数
            'quality'   => 90,//品质
            'invert'    =>false,//反相
            'bgImage'   =>true,//是否有背景图
            'bgColor'   =>'#ffffff',
            'blur'   =>0,//模糊度
            'sharpen'   =>0,//锐化
            'contrast'   =>0,//反差
            'fontColors'=>['#339900','#ff3300','#9966ff','#3333ff'],//字体颜色
        ],
    ]
]);

/*
|--------------------------------------------------------------------------
| Load The Application Routes
|--------------------------------------------------------------------------
|
| Next we will include the routes file so that they can all be added to
| the application. This will provide all of the URLs the application
| can respond to, as well as the controllers that may handle them.
|
*/

$domain = $_SERVER['HTTP_HOST'];
$uri = $_SERVER['REQUEST_URI'];
switch($domain)
{
    case "www.".config("app.route_domain"):
        if(strpos($uri,"queue"))
            @include __DIR__."/../app/Http/queue_routes.php";
        else
        @include __DIR__."/../app/Http/web_routes.php";
        break;
    case 'manage.'.config("app.route_domain"):
        @include __DIR__."/../app/Http/manage_routes.php";
        break;
    case 'open.'.config("app.route_domain"):
        @include __DIR__."/../app/Http/open_routes.php";
        break;
    case 'doc.'.config("app.route_domain"):
        @include __DIR__."/../app/Http/doc_routes.php";
        break;
    //调试用的直接访问
    default:
        if(strpos($uri,"queue"))
            @include __DIR__."/../app/Http/queue_routes.php";
        else
        @include __DIR__."/../app/Http/web_routes.php";
        break;
}

return $app;


