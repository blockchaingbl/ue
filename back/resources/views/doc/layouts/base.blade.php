<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>document</title>
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>{{--IE8/9及以后的版本都会以最高版本IE来渲染页面。--}}
<meta http-equiv="windows-Target" content="_top">{{--强制页面在当前窗口中以独立页面显示，可以防止自己的网页被别人当作一个frame页调用--}}
</head>
@yield('styles')
<style>
    body{ font-size: 14px; line-height: 22px; padding:30px; margin: 0px; font-family: "微软雅黑"}
</style>
<body>
@yield('content')
@yield('scripts')
</body>
</html>
