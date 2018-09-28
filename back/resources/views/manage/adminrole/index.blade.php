@extends('manage.layouts.dashboard')

@section('container')

  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">角色管理</h3>
    </div>
    <div class="panel-body">
      <a href="javascript:openAddRole()" class="btn btn-primary btn-xs"><i class="iconfont">&#xe609;</i> 创建角色</a>
    </div>
    <table class="table table-bordered">
      <thead>
      <tr>
        <th>序号</th>
        <th>角色名称</th>
        <th>操作</th>
      </tr>
      </thead>
      <tbody>
      <script>
          var access_group = {};
      </script>
      @foreach($lists as $key =>$value)
        <tr>
          <td>{{$value->id}}</td>
          <td>{{$value->role_name}}</td>
          <td>
              <script>
                  access_group[{{$value->id}}] = {!! $value->access_group?json_encode($value->access_group):[] !!};
              </script>
              <a href="javascript:openEditRole({{$value->id}},'{{$value->role_name}}')" class="btn btn-warning btn-xs"><i class="iconfont">&#xe699;</i> 权限设置</a>
              <a href="javascript:delRole({{$value->id}})" class="btn btn-danger btn-xs"><i class="iconfont">&#xe605;</i> 删除</a>
          </td>
        </tr>
      @endforeach
      </tbody>
    </table>
  </div>

  <!-- 模态框（Modal） -->
  <div class="modal fade" id="updateModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <form id="updateForm">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title" id="myModalLabel">修改角色</h4>
          </div>
          <div class="modal-body">
            <div class="input-group" style="width:240px;margin-top:10px;">
              <span class="input-group-addon">角色名称：</span>
              <input type="text" class="form-control" name="role_name" placeholder="请输入角色名称">
            </div>
           <div class="input-group" style="width:440px;margin-top:10px;">
                <span class="input-group-addon">权限设置：</span>

             <div class="access_list clearfix">
               <label class="blank"><input type="checkbox" onclick="checkall(this);" /><span>全选</span></label>
               @foreach($access_list as $k=>$row)
                 <label class="access_list_item">
                   <input type="checkbox" access_key="{{$k}}" name="access_item" /><span>{{$row['name']}}</span>
                 </label>
               @endforeach
             </div>

          </div>
          </div>
          <div class="modal-footer">
            <input type="hidden" id="update_id" name="id">
            <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
            <a href="javascript:updateRole()" class="btn btn-primary">提交</a>
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
            <h4 class="modal-title" id="myModalLabel">添加角色</h4>
          </div>
          <div class="modal-body">
            <div class="input-group" style="width:240px;margin-top:10px;">
              <span class="input-group-addon">角色名称：</span>
              <input type="text" class="form-control" name="role_name" placeholder="请输入角色名称">
            </div>
            <div class="input-group" style="width:440px;margin-top:10px;">
              <span class="input-group-addon">权限设置：</span>

              <div class="access_list clearfix">
                <label class="blank"><input type="checkbox"  onclick="checkall(this);" /><span>全选</span></label>
                @foreach($access_list as $k=>$row)
                  <label class="access_list_item">
                    <input type="checkbox" access_key="{{$k}}"  name="access_item" /><span>{{$row['name']}}</span>
                  </label>
                @endforeach
              </div>

            </div>
          </div>
          <div class="modal-footer">
            <input type="hidden" id="update_id" name="id">
            <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
            <a href="javascript:insertRole()" class="btn btn-primary">提交</a>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal -->
      @include('manage.incs.csrf_token')
    </form>
  </div>
  <script>
    function checkall(dom){
        var checked = dom.checked;
        $(dom).parent().parent().find("input[name='access_item']").each(function(){
            this.checked = checked;
        });
    }
    function insertRole(){
        var role_name = $("#addModal").find("input[name='role_name']").val();
        var access_group = [];
        $("#addModal").find("input[name='access_item']").each(function(){
            if(this.checked){
                access_group.push($(this).attr("access_key"));
            }
        });
        $.ajax({
            url:"{{url("setting.admin_role.insert")}}",
            data:{role_name:role_name,access_group:access_group},
            type:"post",
            dataType:"json",
            success:function(res){
                if(res.errcode){
                    layer.msg(res.message);
                }
                else {
                    layer.msg(res.message);
                    location.reload();
                }
            },
            error:function(res){

            }
        });
    }
    function updateRole(){
        var role_name = $("#updateModal").find("input[name='role_name']").val();
        var access_group = [];
        var id = $("#updateModal").find("#update_id").val();
        $("#updateModal").find("input[name='access_item']").each(function(){
            if(this.checked){
                access_group.push($(this).attr("access_key"));
            }
        });
        $.ajax({
            url:"{{url("setting.admin_role.update")}}",
            data:{role_name:role_name,access_group:access_group,id:id},
            type:"post",
            dataType:"json",
            success:function(res){
                if(res.errcode){
                    layer.msg(res.message);
                }
                else {
                    layer.msg(res.message);
                    location.reload();
                }
            },
            error:function(res){

            }
        });
    }
    function openAddRole(){
        $("#addModal").modal("show");
    }
    function openEditRole(id,role_name){
        $("#updateModal").modal("show");
        $("#updateModal").find("input[name='role_name']").val(role_name);
        $("#updateModal").find("#update_id").val(id);
        var access_group_item = access_group[id];

        $("#updateModal").find("input[name='access_item']").prop("checked",false);

        $("#updateModal").find("input[name='access_item']").each(function(){
            var dom = this;
            for(var i=0;i<access_group_item.length;i++){
                var key = $(dom).attr("access_key");
                if(key==access_group_item[i]['access_group']){
                    dom.checked = true;
                }
            }

        });
    }
    function delRole(id){
        $.showConfirm("确认要删除该角色吗？",function(){
            $.ajax({
                url:"{{url("setting.admin_role.del")}}",
                data:{id:id},
                type:"post",
                dataType:"json",
                success:function(res){
                    if(res.errcode){
                        layer.msg(res.message);
                    }
                    else {
                        layer.msg(res.message);
                        location.reload();
                    }
                },
                error:function(res){

                }
            });
        });
    }
  </script>
  <style>
    .access_list{ padding:10px; border: #ccc solid 1px; }
    .access_list label.blank{ display:  block; padding:5px; width:120px; height:30px;}
    .access_list label.access_list_item{ display: block; float:left; width:120px; padding:5px;}
    .access_list label input,.access_list label span{ display: block; float: left; margin-right:5px;}
  </style>
@stop