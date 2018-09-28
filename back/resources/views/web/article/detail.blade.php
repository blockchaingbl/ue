
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>资讯详情 </title>
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
    <style>
        *{
            -webkit-box-sizing: border-box;
            box-sizing: border-box;
            font-family:Arial,"Microsoft YaHei" !important;
        }

        html{
            height: 100%;
            width: 100%;
        }
        body{
            margin: 0;
            position: relative;
            height: 100%;
            width: 100%;
        }
        @font-face {
            font-family: 'iconfont';  /* project id 694278 */
            src: url('http://at.alicdn.com/t/font_694278_bjt3hw3ho2u.eot');
            src: url('http://at.alicdn.com/t/font_694278_bjt3hw3ho2u.eot?#iefix') format('embedded-opentype'),
            url('http://at.alicdn.com/t/font_694278_bjt3hw3ho2u.woff') format('woff'),
            url('http://at.alicdn.com/t/font_694278_bjt3hw3ho2u.ttf') format('truetype'),
            url('http://at.alicdn.com/t/font_694278_bjt3hw3ho2u.svg#iconfont') format('svg');
        }
        .iconfont {
            font-family: "iconfont" !important;
            font-size: 16px;
            font-style: normal;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            vertical-align: baseline;
        }
        .flex-box {
            display: -webkit-box;
            display: -webkit-flex;
            display: flex;
            -webkit-box-align: center;
            -webkit-align-items: center;
            align-items: center;
        }

        .flex-1 {
            -webkit-flex: 1;
            -webkit-box-flex: 1;
            flex: 1;
        }
        .header{
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2.5rem;
            background: #2e3d6c;
            color: #fff;
            padding: 0 0.5rem;
            line-height: 2.5rem;
        }
        .header a{
            text-decoration:none; 
        }
        .header-title{
            text-align: center;
            font-size: 1rem;
        }
        .backicon,.opearicon{
            font-size: 1rem;
            width: 1.5rem;
            height: 1.5rem;
            line-height: 1.5rem;
            text-align: center;
            display: block;
        }
        .article-detail{
            padding: 0.9375rem;
            overflow-x: hidden;
            position: absolute;
            left: 0;
            right: 0;
            @if(!Helper::isApp())
            top: 2.5rem;
            @else
            top:0;
            @endif;
            bottom: 0;
            overflow-y: scroll;
        }
        .article-detail img{
            max-width: 100% !important;
            margin: 0.625rem 0;
        }
        .article-detail p{
            font-family:Arial,"Microsoft YaHei" !important;
            font-size: 0.875rem;
        }
        .article-detail p span{
            font-size: 1.0625rem;
            font-weight: bold !important;
            text-align: center;
            display: block;
        }
        .article_title{
            font-size:1.5rem;
            text-align:center;
            font-weight: bold;
            line-height: 2.375rem;
        }
        .create_time{
            text-align:center;
            font-size: 0.9375rem;
            color: #999;
            line-height: 1.625rem;
        }
        .content{
            width:100%;
            overflow:hidden;
        }
    </style>
    @if(!Helper::isApp())
    <div class="header flex-box">
        <a href="javascript:history.back(-1);" class="backicon iconfont">&#xe70a;</a>
        <div class="header-title flex-1">资讯详情</div>
        {{--<div class="opearicon iconfont">&#xe7c5;</div>--}}
    </div>
    @endif
    <div class="article-detail">
        <div class="article_title">
            {{$article->title}}
        </div>
        <div class="create_time">{{$article->create_time}}</div>
        <div class="content">
            {!! $article->content !!}
        </div>
    </div>
    <div id="ajaxLoading" class="pswp__preloader__icn hide">
        <div class="pswp__preloader__cut">
            <div class="pswp__preloader__donut"></div>
        </div>
    </div>
    <script>
        @if($article->cp_return)
         var data = {id:'{{$article->id}}','_user_token':JSON.parse(localStorage.getItem('token'))}
         apiPost('{{url('api/app.cms/article/cp_return')}}',data,function (res) {
             if(res.data.return_cp>0){
                 layer.msg('算力增加'+res.data.return_cp);
             }
        })

        @endif
    </script>
</body>
</html>