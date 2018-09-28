
@extends('manage.layouts.dashboard')

@section('container')
    <script src="{{asset('fanwe_eth.js')}}"></script>
    @include('manage.incs.batch_send_to')
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">提现列表（共 {{$lists->total()}} 条记录）</h3>
        </div>
        <div class="panel-body">
            <form id="search_form" action="{{url("withdraw")}}" method="GET" class="form-horizontal" role="form" >
                <div class="form-group">
                    <div class="col-md-1"  style="display:block; float: left; padding-left:0px;">
                        <div class="input-group">
                            <select class="form-control" name="coin_type" onchange="toSearch()" style="width:125px;">
                                <option value="0" @if($param['coin_type']==0)selected="selected"@endif decimals="{{$platform_token->token_decimals}}">平台币({{db_config("COIN_UNIT")}})</option>
                                @foreach($coin_list as $type)
                                    <option value="{{$type->id}}" @if($param['coin_type']==$type->id)selected="selected"@endif decimals="{{$type->token_decimals}}">{{$type->name."(".$type->coin_unit.")"}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2"  style="display:block; float: left; padding-left:10px;">
                        <div class="input-group">
                            <span class="input-group-addon">用户</span>
                            <input type="text" class="form-control" name="username" value="{{$param['username']}}" placeholder="输入用户名/手机号">
                        </div>
                    </div>
                    <div class="col-md-3" style="display:block; float: left;">
                        <div class="input-group" style="float: left;width: 150px;">
                            <span class="input-group-addon">日期</span>
                            <select class="form-control" id="time_type" name="time_type" style="width:100px;">
                                <option value="1" selected="selected">申请时间</option>
                                <option value="2">发放时间</option>
                            </select>
                        </div>
                        <input type="text" id="withdraw_date" name="withdraw_date" value="{{$param['withdraw_date']}}" class="form-control" style="float: left;width: 210px;" />
                    </div>
                    <div class="col-md-2"  style="display:block; float: left; padding-left:10px;">
                        <div class="input-group">
                            <span class="input-group-addon">状态</span>
                            <select class="form-control" name="status">
                                <option value="">所有</option>
                                <option value="1" @if($param['status']==1) selected="selected" @endif>待审核</option>
                                <option value="2" @if($param['status']==2) selected="selected" @endif>待发放</option>
                                <option value="3" @if($param['status']==3) selected="selected" @endif>发放中</option>
                                <option value="4" @if($param['status']==4) selected="selected" @endif>发放成功</option>
                                <option value="5" @if($param['status']==5) selected="selected" @endif>发放失败</option>
                                <option value="6" @if($param['status']==6) selected="selected" @endif>已拒绝</option>
                                {{--<option value="4" @if($param['status']==4) selected="selected" @endif>已导出</option>--}}
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4" style="display:block; float: left;">
                        <button type="button" class="btn btn-primary" onclick="clearSearch()">清 除</button>
                        <button type="submit" class="btn btn-primary">查 询</button>
                        <button type="button" class="btn btn-primary" onclick="batchExport()">批量导出</button>
                        <button type="button" class="btn btn-primary" onclick="send()">批量发放</button>
                    </div>
                </div>
            </form>
        </div>
        <table class="table table-bordered">
            <thead>
            <tr>
                <th>
                    <input type="checkbox" onclick="checkAll()" />
                </th>
                <th>用户名</th>
                <th>手机号</th>
                <th>金额</th>
                <th>手续费</th>
                @if(config("app.otc")&&$param['coin_type']==0)
                <th>可交易</th>
                <th>不可交易</th>
                @endif
                <th>提币地址</th>
                <th>交易单号</th>
                <th>申请时间</th>
                <th>发放时间</th>
                <th>状态</th>
                {{--<th>区块编号</th>--}}
                {{--<th>区块交易单号</th>--}}
                {{--<th>区块执行状态</th>--}}
                {{--<th>区块链确认时间</th>--}}
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            @foreach($lists as $key =>$item)
                <tr>
                    <td>
                        <input type="checkbox" name="export_id" id="withdraw_id_{{$item->id}}" value="{{$item->id}}" amount="{{$item->vc_amount}}" sendamount = "{{$item->vc_amount - $item->withdraw_fee}}" feeamount = "{{$item->withdraw_fee}}"  @if(!($item->status==1&&$item->send_status!="success")) disabled @endif />
                    </td>
                    <td>{{$item->username}}</td>
                    <td>{{$item->mobile}}</td>
                    <td>{{$item->vc_amount}}</td>
                    <td>{{$item->withdraw_fee}}</td>
                    @if(config("app.otc")&&$param['coin_type']==0)
                    <td>{{$item->vc_normal}}</td>
                    <td>{{$item->vc_untrade}}</td>
                    @endif
                    <td>{{$item->to_address}}</td>
                    <td>{{$item->tx_hash?$item->tx_hash:"无"}}</td>
                    <td>{{$item->create_time}}</td>
                    <td>@if($item->status==1&&$item->send_status=="success"){{$item->send_time}}@endif</td>
                    <td style="width:200px;">
                        @if($item->status==1)
                            @if($item->send_status=="success")
                                <span style="color:#5cb85c;">已发放</span>
                            @elseif($item->send_status=="error")
                                <span style="color:#5cb85c;">发放失败</span>
                            @else
                                @if($item->tx_hash)
                                     <span style="color:#5cb85c;">发放中</span>
                                @else
                                    <span style="color:#5cb85c;">等待发放</span>
                                @endif
                            @endif
                        @elseif($item->status==2)
                            <span style="color:#d9534f;">已拒绝：{{$item->memo}}</span>
                        @else
                            <span style="color:#428bca;">待审核</span>
                        @endif
                    {{--<td>{{$item->block_number}}</td>--}}
                    {{--<td>{{$item->tx_hash}}</td>--}}
                    {{--<td>--}}
                        {{--@if($item->send_status=='pending')--}}
                            {{--等待中--}}
                        {{--@elseif($item->send_status=='success')--}}
                            {{--成功--}}
                        {{--@elseif($item->send_status=='error')--}}
                            {{--失败--}}
                        {{--@endif--}}
                    {{--</td>--}}
                    {{--<td>@if(strtotime($item->confirm_time)>0){{$item->confirm_time}}@endif</td>--}}
                    <td style="text-align:center;width:60px;">
                        @if($item->status==0)
                            <button type="button" class="btn btn-primary btn-xs" data-target="#myModal" data-toggle="modal" onclick="examine('{{$item->id}}')">审核</button>
                         @elseif($item->status==1)
                            @if($item->send_status=="success")
                                <span style="color:#5cb85c;">已发放</span>
                            @else
                                <button type="button" class="btn btn-primary btn-xs"  onclick="send('{{$item->id}}')">发放</button>
                            @endif
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div style="position:relative;top:10px;">{!! $lists->appends(['keyword'=>$keyword,'withdraw_date'=>$param['withdraw_date'],'time_type'=>$param['time_type'],'coin_type'=>$param['coin_type']])->render() !!}</div>

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
                                        <option value="1" selected="selected">审核通过</option>
                                        <option value="2">拒绝通过</option>
                                    </select>
                                </div>
                                <div class="form-group" id="memo_dis" style="display: none;">
                                    <label>拒绝原因</label>
                                    <input type="text" class="form-control" placeholder="请输入拒绝原因" id="memo" disabled="disabled" style="width:300px;">
                                </div>
                                <input type="hidden" value="" id="examine_id">
                            </form>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                        <button type="button" class="btn btn-primary" onclick="submit()">提交更改</button>
                    </div>
                </div>
            </div>
        </div>


        <!-- 模态框（Modal） -->
        <div class="modal fade" id="batchSendSetting" tabindex="-1" role="dialog" aria-labelledby="batchSendSetting" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title">批量付款</h4>
                    </div>
                    <div class="modal-body">
                        <div class="input-group" style="margin-top:10px;">
                            <span class="input-group-addon">钱包地址：</span>
                            <input type="text" class="form-control" name="address" placeholder="请输入用于付款的钱包地址，用于校验私钥。">
                        </div>
                        <div class="input-group" style="margin-top:10px;">
                            <span class="input-group-addon">私钥：</span>
                            <input type="password" class="form-control" name="privatekey" placeholder="请输入用于付款的钱包私钥，系统不会保存私钥，请放心。">
                        </div>
                        <div class="input-group" style="margin-top:10px;">
                            <span class="input-group-addon">算力gas：</span>
                            <input type="text" class="form-control" name="gas" value="5" />
                        </div>
                        <div style="padding:15px; line-height:25px;">
                            特别注意：<br />
                            1. 为保证自动打款的成功率，请确保批量付款的钱包没有正在交易中的记录。<br />
                            2. 当批量打款的某几条记录长时间未执行或者失败，可以单独再次发起交易。
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                        <button type="button" class="btn btn-primary" data-dismiss="modal" id="startSend">开始</button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal -->
        </div>

        <script>
            function send(id){
                if(id){
                    $("input[name='export_id']").each(function(k,o){
                        if($(o).attr("id")!="withdraw_id_"+id){
                            o.checked = false;
                        }
                        else
                        {
                            o.checked = true;
                        }
                    });
                }
                $("#batchSendSetting").modal('show');
            }
            $("#startSend").bind("click",function(){
                var privatekey = $("#batchSendSetting").find("input[name='privatekey']").val();
                var address = $("#batchSendSetting").find("input[name='address']").val();
                var gas = $("#batchSendSetting").find("input[name='gas']").val();
                if(!address)
                {
                    layer.msg("付款地址不能为空");
                    return false;
                }

                if(!privatekey)
                {
                    layer.msg("付款私钥不能为空");
                    return false;
                }

                try{
                    var decAddress = FanweETH.wallet.privatekeyToAddress(privatekey);
                }catch(ex)
                {
                    layer.msg("请填写正确格式的私钥");
                    return false;
                }

                address = address.toLowerCase();
                if(decAddress.toLowerCase()!=address)
                {
                    layer.msg("私钥与地址不配匹，请确认");
                    return false;
                }

                var ids =[];
                var total_amount = 0;
                var total_send_amount = 0;
                var total_fee = 0;
                $('input[name="export_id"]:checked').each(function(){
                    ids.push($(this).val());
                    total_amount+= parseFloat($(this).attr("amount"));
                    total_send_amount += parseFloat($(this).attr("sendamount"));
                    total_fee += parseFloat($(this).attr("feeamount"));
                });
                if(ids.length==0){
                    layer.msg("请先选择要转出的记录");
                    return false;
                }
                var total = ids.length;
                var coin_name = $("select[name='coin_type']").find("option:selected").html();

                var text = "本次转出共计 "+total+" 交易，提现总额 "+total_amount+coin_name+" ,扣除手续费 "+total_fee+coin_name+" 实际需支付 "+total_send_amount+coin_name+" 。请确保钱包中的余额充足。";
                var confirm = layer.confirm(text,function(){
                    send_to(ids,address,privatekey,gas);
                    layer.close(confirm);
                });
            });

            function clearSearch(){
                $("input[name='keyword']").val('');
                $('#withdraw_date').val('');
            }
            function toSearch(){
                $("#search_form").submit();
            }
            function examine(id) {
                $('#examine_id').val(id);
            }
            function submit() {
                var data = {
                    'memo':$('#memo').val(),
                    'id':$('#examine_id').val(),
                    'status':$('#status').val()
                }
                $.ajax({
                    url:'{{url('withdraw.examine')}}',
                    data:data,
                    type:'POST',
                    dataType:'JSON',
                    success:function (response) {
                        layer.msg(response.message);
                        if(response.errcode==0){
                            $("#myModal").modal('hide');
                            location.reload();
                        }
                    }
                })
            }
            var isCheckAll = false;
            function checkAll() {
                if (isCheckAll) {
                    $("input[name='export_id']").each(function() {
                        this.checked = false;
                    });
                    isCheckAll = false;
                } else {
                    $("input[name='export_id']").each(function() {
                        if(!$(this).attr("disabled")){
                            this.checked = true;
                        }
                    });
                    isCheckAll = true;
                }
            }
            function batchExport(){
                var ids =[];
                $('input[name="export_id"]:checked').each(function(){
                    ids.push($(this).val());
                });
                if(ids.length==0){
                    layer.msg("请先选择要导出的记录");
                    return;
                }
                location.href = '{{url('withdraw.export')}}'+'?coin_type={{$param['coin_type']}}'+'&ids='+ids;
            }
            $('#status').change(function () {
                var status = $(this).val();
                if(status==2){
                    $('#memo_dis').show()
                    $('#memo').removeAttr('disabled')
                }else{
                    $('#memo_dis').hide()
                    $('#memo').attr('disabled','disabled')
                }
            })
            $('#withdraw_date').daterangepicker({
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
            $(function () {
                $('#time_type').val('{{$param['time_type']}}')
            })
        </script>
@stop
