@extends('manage.layouts.dashboard')

@section('container')

  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">账号维护</h3>
    </div>
    <div class="panel-body">
      <a href="javascript:openAddAdmin()" class="btn btn-primary btn-xs"><i class="iconfont">&#xe609;</i> 创建账号</a>
    </div>
    <table class="table table-bordered">
      <thead>
      <tr>
        <th>序号</th>
        <th>账号</th>
        <th>身份</th>
        <th>操作</th>
      </tr>
      </thead>
      <tbody>
      @foreach($admininfo as $key =>$value)
        <tr>
          <td>{{$key+1}}</td>
          <td>{{$value->username}}</td>
          <td>{{$value->rolename}}</td>
          <td>
              <a href="javascript:openSetAccess({{$value->id}},'{{$value->role_key}}')" class="btn btn-warning btn-xs"><i class="iconfont">&#xe699;</i> 身份设置</a>
              <a href="javascript:openUpdatePwd({{$value->id}})" class="btn btn-primary btn-xs"><i class="iconfont">&#xe67e;</i> 更改密码</a>
              <a href="javascript:delAdmin({{$value->id}})" class="btn btn-danger btn-xs"><i class="iconfont">&#xe605;</i> 删除</a>
          </td>
        </tr>
      @endforeach
      </tbody>
    </table>
    <div style="position:relative;top:10px;">{!! $admininfo->render() !!}</div>
  </div>

  <!-- 模态框（Modal） -->
  <div class="modal fade" id="updateModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <form id="updateForm">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title" id="myModalLabel">更改密码</h4>
          </div>
          <div class="modal-body">
            <div class="input-group" style="width:240px;margin-top:10px;">
              <span class="input-group-addon">新密码：</span>
              <input type="password" class="form-control" name="new_password" placeholder="请输入新密码">
            </div>
            <div class="input-group" style="width:240px;margin-top:10px;">
              <span class="input-group-addon">确认密码：</span>
              <input type="password" class="form-control" name="check_password" placeholder="请输入确认密码">
            </div>
          </div>
          <div class="modal-footer">
            <input type="hidden" id="update_id" name="id">
            <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
            <a href="javascript:updateAdminPwd()" class="btn btn-primary">提交</a>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal -->
      @include('manage.incs.csrf_token')
    </form>
  </div>

  <!-- 模态框（Modal） -->
  <div class="modal fade" id="accessModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <form id="accessForm">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title" id="myModalLabel">身份设置</h4>
        </div>
        <div class="modal-body">
          <div class="input-group" style="width:240px;margin-top:10px;">
            <span class="input-group-addon">身份：</span>
            <select id="role_id" name="role_id" class="form-control" style="width: 200px;">
              <option value="0">超级管理员</option>
              @foreach($roleinfo as $key =>$role)
                <option value="{{$role->id}}">{{$role->role_name}}</option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <input type="hidden" id="id" name="id">
          <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
          <a href="javascript:setAccess()" class="btn btn-primary">提交</a>
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal -->
      @include('manage.incs.csrf_token')
    </form>
  </div>

  <!-- 模态框（Modal） -->
  <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <form id="addForm">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title" id="myModalLabel">创建账号</h4>
          </div>
          <div class="modal-body">
            <div class="input-group" style="width:200px;">
              <span class="input-group-addon">账号：</span>
              <input type="text" class="form-control" name="username" placeholder="请输入账号">
            </div>
            <div class="input-group" style="width:200px;margin-top:10px;">
              <span class="input-group-addon">密码：</span>
              <input type="password" class="form-control" name="password" placeholder="请输入密码">
            </div>
            <div class="input-group" style="width:200px;margin-top:10px;">
              <span class="input-group-addon">确认：</span>
              <input type="password" class="form-control" name="check_password" placeholder="请输入确认密码">
            </div>
            <div class="input-group" style="width:200px;margin-top:10px;">
              <span class="input-group-addon">身份：</span>
              <select id="role_id" name="role_id" class="form-control">
                <option value="0">超级管理员</option>
                @foreach($roleinfo as $key =>$role)
                  <option value="{{$role->id}}">{{$role->role_name}}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
            <a href="javascript:addAdmin()" class="btn btn-primary">提交</a>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal -->
      @include('manage.incs.csrf_token')
    </form>
  </div>

@stop