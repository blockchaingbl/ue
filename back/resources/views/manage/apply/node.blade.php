
@extends('manage.layouts.dashboard')

@section('container')
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">申请记录（共 {{$lists->total()}} 条记录）</h3>
        </div>
        <div class="panel-body">
            <form id="search_form" action="{{url("apply.node")}}" method="GET" class="form-horizontal" role="form" >
                <div class="form-group">
                    <div class="col-md-2">
                        <div class="input-group">
                            <span class="input-group-addon">申请人</span>
                            <input type="text" class="form-control" id="to" name="from" value="{{$param['from']}}" placeholder="输入用户名/手机号">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group">
                            <span class="input-group-addon">申请状态</span>
                            <select name="status" id="status_form" class="form-control">
                                <option value="all">全部</option>
                                <option value="0">申请中</option>
                                <option value="1">审核通过</option>
                                <option value="2">已拒绝</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2" >
                        <div class="input-group" style="float: left;width: 150px;">
                            <span class="input-group-addon">申请日期</span>
                            <input type="text" id="date" name="date" value="{{$param['date']}}" class="form-control" style="float: left;width: 210px;" />
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-primary" onclick="clearSearch()">清 除</button>
                        <button type="submit" class="btn btn-primary">查 询</button>
                    </div>
                </div>
            </form>
        </div>
        <table class="table table-bordered">
            <thead>
            <tr>
                <th>申请时间</th>
                <th>申请人用户名</th>
                <th>申请人系统手机号</th>
                <th>姓名</th>
                <th>手机号码</th>
                <th>所在市县</th>
                <th>行业</th>
                <th>是否全职发展</th>
                <th>现月营业额</th>
                <th>加入平台预期月营业额</th>
                <th>审核状态</th>
                <th>审核备注</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            @foreach($lists as $key =>$item)
                <tr>
                    <td>{{$item->update_time}}</td>
                    <td>{{$item->user->username}}</td>
                    <td>{{$item->user->mobile}}</td>
                    <td>{{$item->name}}</td>
                    <td>{{$item->mobile}}</td>
                    <td>{{$item->area}}</td>
                    <td>{{$item->industry_type}}:{{$item->industry}}</td>
                    <td>@if($item->full_time==0)否@else是@endif</td>
                    <td>{{$item->team_number}}</td>
                    <td>{{$item->team_number_expect}}</td>
                    <td>@if($item->status==0)申请中@elseif($item->status==1)审核通过@else已拒绝@endif</td>
                    <td>{{$item->memo}}</td>
                    <td>@if($item->status==0)
                        <button type="button" onclick="applyTeacher('{{$item->user_id}}')"  class="btn btn-primary" data-target="#myModal" data-toggle="modal">审核</button>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div style="position:relative;top:10px;">{!! $lists->appends(['date'=>$param['date'],'keyword'=>$param['keyword'],'status'=>$param['status']])->render() !!}</div>
        <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title" id="myModalLabel">行业节点资格审核</h4>
                    </div>
                    <div class="modal-body">
                        <div style="padding: 10px 10px 10px;">
                            <form class="bs-example bs-example-form" role="form">
                                <div class="form-group">
                                    <div class="row" style="margin-top: 20px;">
                                        <div class="col-xs-6">
                                            <div class="input-group">
                                                <span class="input-group-addon">审核</span>
                                                <select id="status" class="form-control">
                                                    <option value="1">通过申请</option>
                                                    <option value="2">拒绝申请</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-xs-6"></div>
                                    </div>
                                </div>
                                <div class="form-group" id="lock_transfer_text">
                                    <div class="row" style="margin-top: 20px;">
                                        <div class="col-xs-6">
                                            <div class="input-group">
                                                <span class="input-group-addon">开启锁仓转账权限</span>
                                                <select id="lock_transfer" class="form-control">
                                                    <option value="1">是</option>
                                                    <option value="0">否</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-xs-6"></div>
                                    </div>
                                </div>
                                <div class="form-group"  id="memo_text">
                                    <div class="row">
                                        <div class="col-xs-12">
                                            <span>审核备注：</span>
                                            <input placeholder="审核备注" id="memo" type="text" class="form-control" style="width: 400px; display: inline-block">
                                        </div>
                                        <div class="col-xs-6"></div>
                                    </div>
                                </div>

                                <input type="hidden" value="" id="user_id">
                            </form>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                        <button type="button" class="btn btn-primary" onclick="subApply()">提交更改</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
            <div class="modal-body">
            <img src="" alt="" class="img-responsive center-block" id="big_image">
            </div>
            </div>
            </div>
        </div>

        <script>
            function clearSearch(){
                $('#date').val('');
                $('#to').val('')
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
            }, function(start, end, label) {
                var beginTimeStore = start;
                var endTimeStore = end;
                if(!this.startDate){
                    this.element.val('');
                }else{
                    this.element.val(this.startDate.format(this.locale.format) + this.locale.separator + this.endDate.format(this.locale.format));
                }
            });

            $(function () {
                $('#status').change(function () {
                    var status = $(this).val()
                    if(status==1)
                    {
                        $('#lock_transfer_text').show()
                    }else{
                        $('#lock_transfer_text').hide()
                    }
                })
                $('#status_form').val('{{$param['status']}}')
            })
            $('#myModal').on('hide.bs.modal', function () {
                $('#user_id').val('');
                $('#lock_transfer').val(1)
                $('#status').val(1)
                $('#memo_text').hide()
            })

            function openimg(url) {
                $('#big_image').attr('src',url)
                $('#myModal2').modal('show')
            }
            function applyTeacher(user_id) {
                $('#user_id').val(user_id);
            }
            function subApply() {
                var formData = {
                    user_id : $('#user_id').val(),
                    status : $('#status').val(),
                    memo : $('#memo').val(),
                    lock_transfer : $('#lock_transfer').val()
                }
                $.ajax({
                    url : '{{url('apply.node.apply')}}',
                    data :formData,
                    type:'POST',
                    success:function (res) {
                        layer.msg(res.message);
                        if(res.errcode==0){
                            setTimeout(function () {
                                location.reload()
                            },2000)
                        }
                    }
                })
            }
        </script>
@stop
