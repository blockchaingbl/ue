@extends('manage.layouts.page')

@section('page_content')

  <!--begin nav-->
  <nav class="navbar navbar-header navbar-fixed-top">

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse">
      {{--<a class="navbar-brand fanwe_logo" href="{{url('/')}}">Fanwe</a>--}}
      <ul class="nav navbar-nav">

        @foreach ($web_nav as $key=>$group)
          <li @if ($group_key==$key)class='active'@endif><a href="{{url($key)}}"><i class="iconfont">{{$group[$key]['icon']}}</i> {{$web_nav_name[$key]}}</a></li>
        @endforeach

      </ul>

      @include('manage.incs.account')
    </div><!-- /.navbar-collapse -->

  </nav>
  <!--end nav-->

  <div class="container">
    <div class="row">

      <div class="sideicon">

        <ul class="nav nav-sideicon">
          @foreach ($web_nav_shortcut as $left_key=>$left_item)
            <li @if ($route_name==$left_key)class='active'@endif><a href="{{url($left_key)}}"><i class="iconfont">{{$left_item['icon']}}</i><div>{{$left_item['sub']}}</div></a></li>
          @endforeach
        </ul>

      </div>

      @if(count($left_nav)>1)
        <div class="sidebar">

          <ul class="nav nav-sidebar">
            @foreach ($left_nav as $left_key=>$left_item)
              <li @if ($route_name==$left_key)class='active'@endif><a href="{{url($left_key)}}"><i class="iconfont">{{$left_item['icon']}}</i> {{$left_item['name']}}</a></li>
            @endforeach
          </ul>

        </div>
        <span class="toogle_sidebar"><i class="iconfont">&#xe624;</i></span>
      @endif
	  <script src="{{asset("manage/js/fanwe.js")}}"></script>

      <div class="@if(count($left_nav)>1) main-container @else main-container-sideicon  @endif">
        <!--path_nav-->
      @include('manage.incs.path_nav')
      <!--path_nav-->

        <!--右侧内容区-->
        <div class="container">

          @include('manage.incs.public')

          @yield('container')

        </div>
        <!--右侧内容区-->

      </div><!--end right-->
    </div><!--end row-->
  </div>

  <div id="loading" style="display:none;position:absolute;top:0;z-index:9999;width:100%;height:100%;text-align:center;opacity:0.5;background:#FFFFFF;">
    <img src="{{asset('manage/images/loading.gif')}}" style="position:absolute;top:30%;">
  </div>

  <nav class="navbar-footer navbar-fixed-bottom" role="navigation">{{config("app.name")}} 运维平台</nav>

@stop