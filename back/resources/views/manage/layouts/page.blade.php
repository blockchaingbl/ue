<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="">
  <meta name="author" content="">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>{{config("app.name")}}运维平台</title>

  <!-- Bootstrap core CSS -->
  <link href="{{asset("manage/css/bootstrap.min.css")}}" rel="stylesheet">
  <link href="{{asset("manage/css/jquery-ui.css")}}" rel="stylesheet">
  <link href="{{asset("manage/css/fanwe.min.css")}}" rel="stylesheet">
  <link href="{{asset("adminpf/iconfont/iconfont.css")}}" rel="stylesheet">
  <link href="{{asset("manage/libs/colpick/colpick.css")}}" rel="stylesheet">
  @yield('styles')

  <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
  <!--[if lt IE 9]>
  <script src="{{asset("manage/js/html5shiv.min.js")}}"></script>
  <script src="{{asset("manage/js/respond.min.js")}}"></script>
  <![endif]-->

  <script src="{{asset("manage/js/jquery.js")}}"></script>
  <script src="{{asset("manage/js/jquery-ui.js")}}"></script>
  <script src="{{asset('manage/libs/colpick/colpick.js')}}"></script>

  <!-- Bootstrap core JavaScript
================================================== -->
  <!-- Placed at the end of the document so the pages load faster -->
  <script src="{{asset('manage/libs//vue/vue.min.js')}}"></script>
  <script src="{{asset('plupload.full.min.js')}}"></script>
  <script src="{{asset('web/js/jquery.timer.js')}}"></script>
  <script src="{{asset("manage/js/bootstrap.min.js")}}"></script>
  <script src="{{asset("manage/js/moment.js")}}"></script>
  <script src="{{asset("manage/js/daterangepicker.js")}}"></script>
  <script src="{{asset('web/js/public/layer/layer.js')}}"></script><!-- 弹窗 -->
  <script src="{{asset('web/js/web3.js')}}"></script>
  <script>
    var loading_image = "{{asset("scene/css/images/loading.gif")}}";
    //plupload设置
    var flash_swf_url = "{{asset("Moxie.swf")}}";
    var silverlight_xap_url = "{{asset("Moxie.xap")}}";
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });
    function apiPost(url,data,callback)
    {
      $.ajax( {
        url:url,
        data:data,
        type:"POST",
        dataType:"json",
        success:function(obj)
        {
          if(callback) callback.call(this,obj);
        },
        error: function(obj)
        {
          alert(obj.responseText);
        }
      });
    }

    var loading_index;
    $.loading = function(state,msg)
    {
      if(state == 1){
        loading_index = layer.load(0, {shade: false});
      }else{
        layer.close(loading_index);
      }
    };

  </script>

  @yield('scripts')

</head>

<body>

@yield('page_content')
</body>
</html>
