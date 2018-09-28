
@extends('manage.layouts.dashboard')

@section('container')
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">交易挂单列表（共 {{$lists->total()}} 条记录）</h3>
        </div>
        <div class="panel-body">
            <form id="search_form" action="{{url("transaction.putorder")}}" method="GET" class="form-horizontal" role="form" >
                <div class="form-group">
                    <div class="col-md-2"  style="display:block; float: left; padding-left:0px;">
                        <div class="input-group">
                            <span class="input-group-addon">用户</span>
                            <input type="text" class="form-control" name="keyword" value="{{$keyword}}" placeholder="输入用户名/手机号">
                        </div>
                    </div>
                    <div class="col-md-5" style="display:block; float: left;">
                            <span style="float: left;line-height: 30px;">日期选择：</span>
                            <select class="form-control" name="time_type" style="width: 100px;float: left;margin-left: 20px;">
                                <option value="1" selected="selected">上架时间</option>
                                <option value="2">下架时间</option>
                            </select>
                            <input type="text" id="putorder_date" name="putorder_date" value="{{$param['putorder_date']}}" class="form-control" style="float: left;width: 180px;" />
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
                <th>呢称</th>
                <th>手机号</th>
                <th>类型</th>
                <th>总挂单</th>
                <th>剩余的数量</th>
                <th>单价</th>
                <th>状态</th>
                <th>上架时间</th>
                <th>下架时间</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            @foreach($lists as $key =>$item)
                <tr>
                    <td>{{$item->username}}</td>
                    <td>{{$item->mobile}}</td>
                    <td>@if($item->type==0)挂卖@else挂买@endif</td>
                    <td>{{$item->vc_amount}}</td>
                    <td>{{$item->vc_less_amount}}</td>
                    <td>{{$item->vc_unit_price}}</td>
                    <td>@if($item->status==0)下架@else上架@endif</td>
                    <td>{{$item->create_time}}</td>
                    <td>@if(strtotime($item->down_time)){{$item->down_time}}@endif</td>
                    <td>@if($item->status==1)  <button type="button" class="btn btn-primary" onclick="down({{$item->id}})">下架</button>@endif</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div style="position:relative;top:10px;">{!! $lists->appends(['keyword'=>$keyword,'putorder_date'=>$param['putorder_date'],'time_type'=>$param['time_type']])->render() !!}</div>

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
                                    <label for="status">状态</label>
                                    <select class="form-control" id="status">
                                        @foreach($statusName as $key => $item)
                                            <option value="{{$key}}">{{$item}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="input-group">
                                    <span class="input-group-addon">拒绝原因</span>
                                    <input type="text" class="form-control" placeholder="请输入拒绝原因" id="memo">
                                </div>
                                <input type="hidden" value="" id="examine_id">
                            </form>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                        <button type="button" class="btn btn-primary" onclick="submit()">提交更改</button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal -->
        </div>
        <script>
            function clearSearch(){
                $("input[name='keyword']").val('')
                $('#putorder_date').val('')
            }
            function down(id) {
                $.ajax({
                    url:'{{url('transaction.down.putorder')}}',
                    type:'POST',
                    dataType:'json',
                    data:{id},
                    success:function (response) {
                        layer.msg(response.message)
                        if(response.errcode==0){
                            setTimeout(function () {
                                location.reload();
                            },2000)
                        }
                    }
                })
            }
            $('#putorder_date').daterangepicker({
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

        </script>

@stop