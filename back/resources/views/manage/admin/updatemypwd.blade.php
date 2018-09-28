@extends('manage.layouts.dashboard')

@section('container')

  <div class="page_header">
    <h3>更改密码</h3>
  </div>

  <form class="form-horizontal fanwe_form" style="width:350px;margin:0 auto;" role="form" action="{{url('update_my_pwd')}}" method="post">

    @if($errors->first('errcode') != 0)
      <div id="errorAlert" class="alert alert-danger">
        <a href="#" class="close" data-dismiss="alert">&times;</a>
        <h4>错误</h4>
        <p>{{$errors->first('message')}}</p>
      </div>
    @endif

    <div class="form-group">
      <label for="old_password" class="col-md-1 control-label">原密码：</label>
      <div class="col-sm-2">
        <input type="password" class="form-control" style="width:240px;" name="old_password" placeholder="请输入原密码">
      </div>
    </div>
    <div class="form-group">
      <label for="new_password" class="col-md-1 control-label">新密码：</label>
      <div class="col-md-2">
        <input type="password" class="form-control" style="width:240px;" name="new_password" placeholder="请输入新密码">
      </div>
    </div>
    <div class="form-group">
      <label for="check_password" class="col-md-1 control-label">确认密码：</label>
      <div class="col-md-2">
        <input type="password" class="form-control" style="width:240px;" name="check_password" placeholder="请输入确认密码">
      </div>
    </div>
    <div class="form-group">
      <div class="col-md-11">
        <button type="submit" class="btn btn-primary pull-right">提 交</button>
      </div>
    </div>
    @include('manage.incs.csrf_token')
  </form>

@stop