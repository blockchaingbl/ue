<style>
    .box{
        width: 800px;
        margin: 0 auto;
        font-family: 'Microsoft YaHei', Helvetica, sans-serif;
    }
    .error_img{
        width: 600px;
        margin: 100px auto 0;
        text-align: center;
    }
    .error_img img{
        width: 100%;
    }
    .message{
        width: 100%;
        margin: 40px 0 20px;
        font-size: 24px;
        text-align: center;
        color: #222222;
    }
    .link{
        width: 100%;
        text-align: center;
        font-size: 24px;
    }
    .link a{
        color: #D2B184;
        text-decoration: none;
        margin: 0 auto;
    }
</style>

<div class="box">
    <div class="error_img">
        <img src="{{asset('web/images/404.png')}}">
    </div>
    <div class="message">哎呀，你查看的网页不见了</div>
    <div class="link">
        <a href="{{url('/')}}" class="link">返回首页</a>
    </div>
</div>