<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>demo</title>
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>{{--IE8/9及以后的版本都会以最高版本IE来渲染页面。--}}
<meta http-equiv="windows-Target" content="_top">{{--强制页面在当前窗口中以独立页面显示，可以防止自己的网页被别人当作一个frame页调用--}}
<meta name="keywords" content="" />{{--关键词--}}
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="" />{{--描述--}}
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="shortcut icon" href="/favicon.ico">
</head>
<link href="{{asset('web/images/iconfont/iconfont.css')}}" rel="stylesheet">
<link href="{{asset('web/css/public.css')}}" rel="stylesheet">
@yield('styles')
<script src="{{asset('web/js/public/jq/jquery1.8.3.js')}}"></script>
<!-- <script src="{{asset('web/js/jquery.timer.js')}}"></script> -->
<script src="{{asset('web/js/jquery.cookie.js')}}"></script>
<script src="{{asset('web/js/public.js')}}"></script>
<script>
    var ajaxLoading;
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        beforeSend: function () {
            $("#ajaxLoading").removeClass("hide");
        },
        complete: function () {
            $("#ajaxLoading").addClass("hide");
        }
    });
    function ajaxCallBack(res,func){
        switch (res.errcode){
            case 10001:
                layer.msg("网页已过期，请刷新");
                break;
            default:
                if(func!=null){
                    func.call(this,res);
                }
                break;

        }
    }
    function apiPost(url,data,callback)
    {
        $.ajax( {
            url:url,
            data:data,
            type:"POST",
            dataType:"json",
            success:function(obj)
            {
                ajaxCallBack(obj,callback);
            },
            error: function(obj)
            {
//                if(obj.responseText)
//                alert(obj.responseText);
            }
        });
    }
</script>
<script src="{{asset('web/js/public/layer/layer.js')}}"></script><!-- 弹窗 -->
<body>
<!-- 初始加载图 -->
<div id="preloader">
    <div id="status">&nbsp;</div>
</div>

@include('web.incs.header')
@yield('content')
@include('web.incs.footer')

@yield('scripts')
<div id="ajaxLoading" class="pswp__preloader__icn hide">
  <div class="pswp__preloader__cut">
    <div class="pswp__preloader__donut"></div>
  </div>
</div>
</body>
</html>
