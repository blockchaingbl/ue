@extends('manage.layouts.page')
@section('page_content')
  <div class="container login">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title">{{$app_name}}后台登录</h3>
      </div>
      <div class="panel-body">
        <form class="form-horizontal fanwe_form" role="form" action="{{url('dologin')}}" method="post">
          <div class="form-group">
              <input type="text" class="form-control" autocomplete="off" id="username" name="username" placeholder="帐号{{$errors->first('username')}}">
              
          </div>
          <div class="form-group">
              <input type="password" class="form-control" id="password" name="password" placeholder="密码{{$errors->first('password')}}">
              
          </div>
          <div class="form-group">
            <div class="form-input">
              <input type="text" class="form-control" autocomplete="off" id="captcha" name="captcha" placeholder="验证码{{$errors->first('captcha')}}">
              <div class="form-check">
                <input type="hidden" id="uuid" name="uuid">
                <img id="showCheckCode" style="height:34px;" onclick="getCheckCode()">
              </div>
            </div>
            @if($errors->first('login_message'))
                  <p class="form-tis">{{$errors->first('login_message')}}</p>
              @endif
          </div>
          @include('manage.incs.csrf_token')
          <div class="form-group">
              <button type="submit" class="btn btn-primary">立即登录</button>
          </div>
        </form>
      </div><!--end panel-body-->
    </div>
  </div>
<script>

  window.onload = function(){
    getCheckCode();
  }

  function getCheckCode(){
    $.ajax({
      url: "captchaInfo/login",
      type: "GET",
      dataType: "json",
      success:function(data) {
        $('#showCheckCode').attr('src',data.captchaUrl);
        $('#uuid').val(data.captchaUuid);
      }
    })
  }

</script>
@stop
@section('scripts')
@endsection
@section('styles')
  <style type="text/css">
  	html{
      background:#f7f7f7;
      height:100%;
    }
    body{
      background:#f7f7f7;
      height:100%;
    }
    .container{
      position: absolute;
      top:50%;
      left:50%;
      -webkit-transform:translate(-50%,-50%);
      transform:translate(-50%,-50%);
      width:418px;
      -webkit-box-sizing: border-box;
      box-sizing: border-box;
    }
    .panel-heading{
      background:url(../manage/images/login_bg.jpg) no-repeat top center;
      height:137px;
    }
    .panel-title{
      color: #fff;
      font-size: 30px;
      margin-top: 68px;
      margin-left: 20px;
    }
    .panel-body{
      padding:20px 36px;
    }
    .form-input{
      position:relative;
    }
    .form-check{
      position:absolute;
      right:0;
      top:10px;
    }
    .form-horizontal .form-group{
      margin:0;
      position:relative;
    }
    .form-control{
      font-size: 14px;
      font-family: Roboto-light,sans-serif;
      padding: 20px 0 10px;
      color: #333;
      background: transparent;
      border: none;
      border-bottom: 2px solid #eaeaea;
      box-shadow: none;
      position: relative;
      margin: 0;
      height: 50px;
      border-radius:0;
    }
    .form-control::-webkit-input-placeholder{ 
      color: #333; 
    } 
    .form-control:-moz-placeholder{ 
      color: #333; 
    } 
    .form-control::-moz-placeholder{ 
      color: #333; 
    } 
    .form-control:-ms-input-placeholder{ 
      color: #333; 
    } 
    .form-group .form-control:focus {
      border-color: #66afe9;
      outline: 0;
      -webkit-transition: color .2s cubic-bezier(.25,.46,.45,.94),border-color .2s cubic-bezier(.25,.46,.45,.94),background-color .2s cubic-bezier(.25,.46,.45,.94);
      transition: color .2s cubic-bezier(.25,.46,.45,.94),border-color .2s cubic-bezier(.25,.46,.45,.94),background-color .2s cubic-bezier(.25,.46,.45,.94);
      box-shadow: none;
      border-bottom-color: #465d9f;
      color:#999!important;
    }
    .form-group .form-control:focus::-webkit-input-placeholder{ 
      color: #999; 
    } 
    .form-tis{
      position:absolute;
      left:0;
      bottom:-40px;
      color:#de0000;
      font-size:14px;
      padding-left:24px;
    }
    .form-tis::before{
      content:'';
      display:block;
      position:absolute;
      top:1px;
      left:0;
      background:url(../manage/images/icon_err.png) no-repeat top center;
      width:18px;
      height:17px;
    }
    .btn-primary{
      width: 100%;
      height: 46px;
      border-radius: 0;
      font-size: 16px;
      background-color: #465d9f;
      border: none;
      margin-top: 44px;
    }
  </style>
@endsection