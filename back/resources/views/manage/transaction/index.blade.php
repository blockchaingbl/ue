
@extends('manage.layouts.dashboard')

@section('container')
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">交易挂单列表（共 {{$lists->total()}} 条记录）</h3>
        </div>
        <div class="panel-body">
            <form id="search_form" action="{{url("trans")}}" method="GET" class="form-horizontal" role="form" >
                <div class="form-group">
                    <div class="col-md-2"  style="display:block; float: left; padding-left:0px;">
                        <span style="float: left;line-height: 30px;">转出人：</span>
                        <input type="text" class="form-control" style="width: 150px;float: left;margin-left: 10px;" name="from" value="{{$param['from']}}" placeholder="输入用户名/手机号">
                    </div>
                    <div class="col-md-2"  style="display:block; float: left; padding-left:0px;">
                        <span style="float: left;line-height: 30px;">转入人：</span>
                        <input type="text" class="form-control" style="width: 150px;float: left;margin-left: 10px;" name="to" value="{{$param['to']}}" placeholder="输入用户名/手机号">
                    </div>

                    <div class="col-md-2" style="display:block; float: left;">
                        <span style="float: left;line-height: 30px;">转账时间：</span>
                        <input type="text" id="saleorder_date" name="saleorder_date" value="{{$param['date']}}" class="form-control" style="float: left;width: 180px;" />
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
                <th>转出人</th>
                <th>转入人</th>
                <th>转账数量({{db_config('COIN_UNIT')}})</th>
                <th>到账数量({{db_config('COIN_UNIT')}})</th>
                <th>手续费</th>
                <th>转账时间</th>
                <th>转账选择货币</th>
                <th>转账时刻汇率</th>
                <th>转账货币数量</th>
            </tr>
            </thead>
            <tbody>
            @foreach($lists as $key =>$item)
                <tr>
                    <td>{{$item->from_user->username}}</td>
                    <td>{{$item->to_user->username}}</td>
                    <td>{{$item->amount}}</td>
                    <td>{{$item->receive}}</td>
                    <td>{{$item->trans_fee}}</td>
                    <td>{{$item->create_time}}</td>
                    <td>{{$item->ex_rate_info->name}}({{$item->ex_rate_info->symbol}})</td>
                    <td>{{$item->ex_rate}}</td>
                    <td>{{$item->currency}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div style="position:relative;top:10px;">{!! $lists->appends($param)->render() !!}</div>

        <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title" id="myModalLabel">申诉处理</h4>
                    </div>
                    <div class="modal-body">
                        <div style="padding: 10px 10px 10px;">
                            <form class="bs-example bs-example-form" role="form">
                                <div class="input-group">
                                    <span class="input-group-addon">处理方式</span>
                                    <select class="form-control" id="deal_type" style="width:100px;">
                                        <option value="">请选择</option>
                                        <option value="1">立即发币</option>
                                        <option value="2">取消订单</option>
                                    </select>
                                </div>
                                <div class="input-group" style="margin-top:10px;width:300px;">
                                    <span class="input-group-addon">处理备注</span>
                                    <input type="text" class="form-control" placeholder="请输入处理备注" id="deal_memo">
                                </div>
                                <input type="hidden" value="" id="otc_order_id">
                                <input type="hidden" value="" id="type">
                            </form>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                        <button type="button" class="btn btn-primary" onclick="submit()" id="button_text">确认处理</button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal -->
        </div>
        <script>
            $('#saleorder_date').daterangepicker({
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
            function clearSearch(){
                $("input[name='keyword']").val('')
                $('#saleorder_date').val('')
                $('#deal_search').val('')
            }
            function appeal(id,type,status) {
                $('#otc_order_id').val(id);
                $('#type').val(type);
            }
            function submit() {
                var id = $('#otc_order_id').val();
                var deal_memo = $('#deal_memo').val();
                var deal_type = $('#deal_type').val();
                if(deal_type==""){
                    layer.msg("请选择处理方式");
                    return;
                }
                var formData = {id:id,deal_memo:deal_memo,type:deal_type};
                $.ajax({
                    url:'{{url('transaction.appeal')}}',
                    data:formData,
                    dataType:'json',
                    type:'post',
                    success:function (response) {
                        layer.msg(response.message);
                        if(response.errcode==0){
                            setTimeout(function () {
                                location.reload();
                            },2000)
                        }
                    }
                })
            }
            $(function () {
                $('#status').val('{{$param['status']}}')
            })
        </script>
@stop
