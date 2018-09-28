<ul class="nav navbar-nav navbar-right">
  <li><a href="{{url("/")}}"><i class="iconfont">&#xe62a;</i> {{$admin->username}}</a></li>
  <li class="dropdown">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown">帐户管理 <span class="caret"></span></a>
    <ul class="dropdown-menu" role="menu">
      <li><a href="{{url("to_update_mypwd")}}"><i class="iconfont">&#xe67e;</i> 更改密码</a></li>
      <li class="divider"></li>
      <li><a href="#" onclick="$.showConfirm('确定要退出？',function(){location.href='{{url("logout")}}';});"><i class="iconfont">&#xe64c;</i> 退出</a></li>
    </ul>
  </li>
</ul>