
@extends('manage.layouts.dashboard')

@section('container')
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">流量主申请列表（共 {{$lists->total()}} 条记录）</h3>
        </div>
        <div class="panel-body">
            <form id="search_form" action="{{url("user.flow")}}" method="GET" class="form-horizontal" role="form" >
                <div class="form-group">
                    <div class="col-md-2"  style="display:block; float: left; padding-left:0px;">
                        <div class="input-group">
                            <span class="input-group-addon">用户</span>
                            <input type="text" class="form-control" name="keyword" value="{{$keyword}}" placeholder="输入用户名/手机号">
                        </div>
                    </div>
                    <div class="col-md-2" style="display:block; float: left;">
                        <div class="input-group">
                            <span class="input-group-addon">时间</span>
                            <input type="text" id="date" name="date" value="{{$param['date']}}" class="form-control" style="float: left;width:170px;" />
                        </div>
                    </div>
                    <div class="col-md-2"  style="display:block; float: left;">
                        <button type="button" class="btn btn-primary"   onclick="appealMulti()">批量审核</button>
                    </div>
                    <div class="col-md-2"  style="display:block; float: left;">
                        <button type="button" class="btn btn-primary" onclick="clearSearch()">清 除</button>
                        <button type="submit" class="btn btn-primary">查 询</button>
                    </div>
                </div>
            </form>
        </div>
        <table class="table table-bordered">
            <thead>
            <tr>
                <th><input type="checkbox" onclick="checkAll()" /></th>
                <th>用户名</th>
                <th>手机号</th>
                <th>增加时间</th>
                <th>申请状态</th>
                <th>备注</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            @foreach($lists as $key =>$item)
                <tr>
                    <td> <input type="checkbox" name="id_arr"  value="{{$item->id}}" @if($item->status!=0) disabled @endif> </td>
                    <td>{{$item->username}}</td>
                    <td>{{$item->mobile}}</td>
                    <td>{{$item->create_time}}</td>
                    <td>
                        @if($item->status==0)
                            待处理
                        @elseif($item->status==1)
                            已拒绝
                        @elseif($item->status==2)
                            已通过审核
                        @endif
                    </td>
                    <td>{{$item->memo}}</td>
                    <td>
                        @if($item->status==0)
                        <button class="btn btn-danger" data-target="#myModal" data-toggle="modal" onclick="appeal('{{$item->id}}')">审核</button>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div style="position:relative;top:10px;">{!! $lists->appends(['keyword'=>$keyword,'date'=>$param['date'],'status'=>$param['status']])->render() !!}</div>
        <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title" id="myModalLabel">审核</h4>
                    </div>
                    <div class="modal-body">
                        <div style="padding: 10px 10px 10px;">
                            <form class="bs-example bs-example-form" role="form">
                                <div class="form-group">
                                    <label for="status">操作</label>
                                    <select class="form-control" id="status" style="width:100px;">
                                        <option value="2" selected="selected">审核通过</option>
                                        <option value="1">拒绝申请</option>
                                    </select>
                                </div>
                                <div class="form-group" >
                                    <label>备注</label>
                                    <input type="text" class="form-control" placeholder="请输入备注" id="memo"  style="width:300px;">
                                </div>
                                <input type="hidden" value="" id="id_arr">
                            </form>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                        <button type="button" class="btn btn-primary" onclick="submit()">审核</button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            function clearSearch(){
                $("input[name='keyword']").val('')
                $('#date').val('')
            }
            $('#date').daterangepicker({
                "linkedCalendars": false,
                "autoUpdateInput": false,
                "locale": {
                    format: 'YYYY-MM-DD',
                    separator: '~',
                    applyLabel: "应用",
                    cancelLabel: "取消",
                    resetLabel: "重置",
                }
            }, function() {
                if(!this.startDate){
                    this.element.val('');
                }else{
                    this.element.val(this.startDate.format(this.locale.format) + this.locale.separator + this.endDate.format(this.locale.format));
                }
            });
            var isCheckAll = false;
            function checkAll() {
                if (isCheckAll) {
                    $("input[name='id_arr']").each(function() {
                        this.checked = false;
                    });
                    isCheckAll = false;
                } else {
                    $("input[name='id_arr']").each(function() {
                        if(!$(this).attr("disabled")){
                            this.checked = true;
                        }
                    });
                    isCheckAll = true;
                }
            }
            function appeal(id) {
                var id_arr = [id];
                $('#id_arr').val(JSON.stringify(id_arr));
            }


        function appealMulti() {
            var id_arr= [];
            $("input[name='id_arr']:checkbox:checked").each(function(){
                id_arr.push($(this).val())
            })
            if(id_arr.length>0){
                $('#myModal').modal('show');
            }else{
                layer.msg("请选择要审核的记录");
            }
            $('#id_arr').val(JSON.stringify(id_arr));
        }
        
        function submit() {
            var id_arr = JSON.parse($('#id_arr').val());
            var formData = {
                id_arr : id_arr,
                status : $('#status').val(),
                memo : $('#memo').val()
            }
            $.ajax({
                url : '{{url('user.flow.appeal')}}',
                data : formData,
                type :'POST',
                dataType : 'JSON',
                success : function (res) {
                    layer.msg(res.message);
                    if(res.errcode==0){
                        setTimeout(function () {
                            location.reload();
                        },2000)
                    }
                }
            })
        }

        </script>

@stop
