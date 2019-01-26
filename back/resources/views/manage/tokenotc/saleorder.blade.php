
@extends('manage.layouts.dashboard')

@section('container')
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">交易挂单列表（共 {{$lists->total()}} 条记录）</h3>
        </div>
        <div class="panel-body">
            <form id="search_form" action="{{url("token.transaction.saleorder")}}" method="GET" class="form-horizontal" role="form" >
                <div class="form-group">
                    <div class="col-md-2">
                        <div class="input-group">
                            <span class="input-group-addon">数字资产</span>
                            <select name="coin_type" id="coin_type" class="form-control">
                                <option value="all">全部</option>
                                @foreach($coin_type_all as $item)
                                    <option value="{{$item['coin_type']}}">{{$item['coin_unit']}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2"  style="display:block; float: left; padding-left:0px;">
                        <span style="float: left;line-height: 30px;">单号：</span>
                        <input type="text" class="form-control" style="width: 150px;float: left;margin-left: 10px;" name="order_sn" value="{{$param['order_sn']}}" placeholder="输入订单号">
                    </div>
                    <div class="col-md-2"  style="display:block; float: left; padding-left:0px;">
                        <span style="float: left;line-height: 30px;">用户：</span>
                        <input type="text" class="form-control" style="width: 150px;float: left;margin-left: 10px;" name="keyword" value="{{$keyword}}" placeholder="输入用户名/手机号">
                    </div>
                    {{--<div class="col-md-2"  style="display:block; float: left; padding-left:0px;">--}}
                        {{--<div class="input-group">--}}
                            {{--<span class="input-group-addon">申诉处理备注</span>--}}
                            {{--<input type="text" class="form-control" name="deal_memo" id="deal_search" value="{{$param['deal_memo']}}" placeholder="请输入处理备注">--}}
                        {{--</div>--}}
                    {{--</div>--}}
                    <div class="col-md-2" style="display:block; float: left;">
                        <span style="float: left;line-height: 30px;">订单状态：</span>
                        <select class="form-control" name="status" style="width: 100px;float: left;margin-left: 10px;" id="status">
                            <option value="all" selected="selected">全部</option>
                            <option value="0">未付款</option>
                            <option value="1">已付款</option>
                            <option value="2">已发资产</option>
                            <option value="3">取消</option>
                        </select>
                    </div>
                    <div class="col-md-2" style="display:block; float: left;">
                        <span style="float: left;line-height: 30px;">申诉状态：</span>
                        <select class="form-control" name="appeal_status" style="width: 100px;float: left;margin-left: 10px;">
                            <option value="all" selected="selected">全部</option>
                            <option value="1" @if($param['appeal_status']==1)selected="selected"@endif>买家申诉</option>
                            <option value="2" @if($param['appeal_status']==2)selected="selected"@endif>卖家申诉</option>
                            <option value="4" @if($param['appeal_status']==4)selected="selected"@endif>共同申诉</option>
                            <option value="3" @if($param['appeal_status']==3)selected="selected"@endif>已处理</option>
                        </select>
                    </div>
                    <div class="col-md-4" style="display:block; float: left;">
                        <span style="float: left;line-height: 30px;">日期选择：</span>
                        <select class="form-control" name="time_type" style="width: 100px;float: left;margin-left: 10px;">
                            <option value="1" selected="selected">下单时间</option>
                            <option value="2" @if($param['time_type']==2)selected="selected"@endif>支付时间</option>
                            <option value="3" @if($param['time_type']==3)selected="selected"@endif>发资产时间</option>
                            <option value="4" @if($param['time_type']==4)selected="selected"@endif>取消时间</option>
                        </select>
                        <input type="text" id="saleorder_date" name="saleorder_date" value="{{$param['saleorder_date']}}" class="form-control" style="float: left;width: 180px;" />
                    </div>

                    <div class="col-md-2"  style="display:block; float: left;">
                        <button type="submit" class="btn btn-primary">查 询</button>
                        <button type="button" class="btn btn-primary" onclick="export_saleorder()">导 出</button>
                    </div>
                </div>
            </form>
        </div>
        <table class="table table-bordered">
            <thead>
            <tr>
                <td>订单号</td>
                <th>买家</th>
                <th>卖家</th>
                <th>成交数量</th>
                <th>成交单价</th>
                <th>成交总额</th>
                <th>下单时间</th>
                <th>支付时间</th>
                <th>发资产时间</th>
                <th>订单状态</th>
                <th>申诉状态</th>
                <th>付款信息</th>
                <th>取消时间</th>
                <th>取消备注</th>
                <th>申诉处理备注</th>
            </tr>
            </thead>
            <tbody>
            @foreach($lists as $key =>$item)
                <tr>
                    <td>{{Helper::formatOrderSN($item->create_time,$item->id)}}</td>
                    <td>{{$item->buyer->username}}</td>
                    <td>{{$item->seller->username}}</td>
                    <td>{{$item->vc_amount.$coin_type_all[$item->coin_type]['coin_unit']}}</td>
                    <td>{{$item->vc_uint_price}}</td>
                    <td>{{$item->vc_total_price}}</td>
                    <td>@if($item->create_time>0){{date('Y-m-d H:i:s',$item->create_time)}}@endif</td>
                    <td>@if($item->pay_time>0){{date('Y-m-d H:i:s',$item->pay_time)}}@endif</td>
                    <td>@if($item->send_time>0){{date('Y-m-d H:i:s',$item->send_time)}}@endif</td>
                    <td>
                        @if($item->status==0)
                            未付款
                        @elseif($item->status==1)
                            已付款未发资产
                        @elseif($item->status==2)
                            已发资产
                        @elseif($item->status==3)
                            取消
                        @endif
                    </td>
                    <td>
                        @if($item->appeal_status==0)
                            未申诉
                        @elseif($item->appeal_status==1 && $item->status!=2)
                            买家申诉&nbsp;<button type="button" class="btn btn-primary btn-xs"  data-target="#myModal"  data-toggle="modal"  onclick="appeal('{{$item->id}}',1,'{{$item->status}}')">处理</button>
                        @elseif($item->appeal_status==2 && $item->status!=2)
                            卖家申诉&nbsp;<button type="button" class="btn btn-primary btn-xs"  data-target="#myModal"  data-toggle="modal" onclick="appeal('{{$item->id}}',2,'{{$item->status}}')" >处理</button>
                        @elseif($item->appeal_status==3)
                            申诉已处理
                        @elseif($item->appeal_status==4 && $item->status!=2)
                            共同申诉&nbsp;<button type="button" class="btn btn-primary btn-xs"  data-target="#myModal"  data-toggle="modal" onclick="appeal('{{$item->id}}',4,'{{$item->status}}')" >处理</button>
                        @endif
                    </td>
                    <td>
                          支付方式: {{$item->pay_info->payment_org}} <br/>
                          收款账号 :  {{$item->pay_info->payment_account}} <br/>
                          收款人 : {{$item->pay_info->payment_receipt}}
                          @if($item->pay_info->payment_key=='bankcard')
                            <br/>
                          支行 : {{$item->pay_info->branch_bank}}
                          @endif
                    </td>
                    <td>@if($item->cancel_time>0){{date('Y-m-d H:i:s',$item->cancel_time)}}@endif</td>
                    <td>{{$item->cancel_memo}}</td>
                    <td>{{$item->deal_memo}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div style="position:relative;top:10px;">{!! $lists->appends(['keyword'=>$keyword,'saleorder_date'=>$param['saleorder_date'],'time_type'=>$param['time_type'],'appeal_status'=>$param['appeal_status'],'status'=>$param['status'],'deal_memo'=>$param['deal_memo'],'coin_type'=>$param['coin_type']])->render() !!}</div>

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
                                        <option value="1">立即发资产</option>
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
                    url:'{{url('token.transaction.appeal')}}',
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
                $('#coin_type').val('{{$param['coin_type']}}')
            })
            function export_saleorder() {
                var url = `{!! $url !!}`;
                location.href = url;
            }
        </script>
@stop
