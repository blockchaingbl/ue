@extends('manage.layouts.dashboard')

@section('container')
    <div class="container">
        <div class="panel panel-default">
            <div class="panel-body">
                <button type="button" class="btn btn-primary" onclick="save()">保存配置</button>
            </div>
        </div>
        <form class="bs-example bs-example-form" role="form" id="setting">
            <div class="row">
                <div class="col-md-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            基本设置
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="withdraw_rate">图标</label>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div style="background:#dfdfdf;width:60px;height:60px;line-height:60px;text-align:center;position:relative;">
                                                    <img src="{{$conf['COIN_ICON']}}" id="show_icon" style="max-width:90%;max-height:90%;">
                                                    <input type="hidden" id="icon" name="COIN_ICON" value="{{$conf['COIN_ICON']}}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <button type="button" class="btn btn-primary btn-xs" id="coin_icon_upload">选择</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group" style="margin-top: 60px;">
                                        <label for="COIN_CNNAME">中文名称</label>
                                        <input type="text" class="form-control" placeholder="请输入中文名称" id="COIN_CNNAME" name="COIN_CNNAME" value="{{$conf['COIN_CNNAME']}}">
                                    </div>
                                    <div class="form-group">
                                        <label for="COIN_ENNAME">英文名称</label>
                                        <input type="text" class="form-control" placeholder="请输入英文名称" id="COIN_ENNAME" name="COIN_ENNAME" value="{{$conf['COIN_ENNAME']}}">
                                    </div>
                                    <div class="form-group">
                                        <label for="COIN_UNIT">单位</label>
                                        <input type="text" class="form-control" placeholder="请输入单位" id="COIN_UNIT" name="COIN_UNIT" value="{{$conf['COIN_UNIT']}}">
                                    </div>
                                    <div class="form-group">
                                        <label for="COIN_CNNAME">精度</label>
                                        <input type="text" class="form-control" placeholder="请输入精度" id="COIN_DECIMALS" name="COIN_DECIMALS" value="{{$conf['COIN_DECIMALS']}}">
                                    </div>
                                    <div class="form-group">
                                        <label for="COIN_CNNAME">充值锁仓时间</label>
                                        <input type="text" class="form-control" placeholder="充值锁仓时间" id="INCHARGE_LOCK_TIME" name="INCHARGE_LOCK_TIME" value="{{$conf['INCHARGE_LOCK_TIME']}}">
                                    </div>
                                    <div class="form-group">
                                        <label for="COIN_CNNAME">充值增加算力比例</label>
                                        <input type="text" class="form-control" placeholder="充值增加算力比例" id="IC" name="IC" value="{{$conf['IC']}}">
                                    </div>
                                    <div class="form-group">
                                        <label for="COIN_CNNAME">充值增加邀请人算力比例</label>
                                        <input type="text" class="form-control" placeholder="充值锁仓时间" id="IIC" name="IIC" value="{{$conf['IIC']}}">
                                    </div>
                                    <div class="form-group">
                                        <label for="APPLY_CP_SOCIETY">申请社区建设算力限制</label>
                                        <input type="number" class="form-control" placeholder="超出此数值的部分可以用于转账" id="APPLY_CP_SOCIETY" name="APPLY_CP_SOCIETY" value="{{$conf['APPLY_CP_SOCIETY']}}">
                                    </div>
                                    <div class="form-group">
                                        <label for="APPLY_CP_SOCIETY">申请行业节点算力限制</label>
                                        <input type="number" class="form-control" placeholder="超出此数值的部分可以用于转账" id="APPLY_CP_NODE" name="APPLY_CP_NODE" value="{{$conf['APPLY_CP_NODE']}}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="COIN_PRICE">币单价（元）</label>
                                        <input type="number" class="form-control" placeholder="请输入币单价（元）" id="COIN_PRICE" name="COIN_PRICE" value="{{$conf['COIN_PRICE']}}">
                                    </div>
                                    <div class="form-group">
                                        <label for="WITHDRAW_OPEN">是否可提现</label>
                                        <select name="WITHDRAW_OPEN" class="form-control" id="WITHDRAW_OPEN">
                                            <option value="1" @if($conf['WITHDRAW_OPEN']==1) selected="selected" @endif>是</option>
                                            <option value="0" @if($conf['WITHDRAW_OPEN']==0) selected="selected" @endif>否</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="WITHDRAW_RATE">提现的手续费比例</label>
                                        <input type="number" class="form-control" placeholder="请输入提现的手续费比例" id="REBATE_PERIOD" name="WITHDRAW_RATE" value="{{$conf['WITHDRAW_RATE']}}">
                                    </div>
                                    <div class="form-group">
                                        <label for="WITHDRAW_RATE">可提现比例(otc历史购买量x此比例为可提现额度)</label>
                                        <input type="number" class="form-control" placeholder="请输入比例" id="WITHDRAW_LIMIT_RATE" name="WITHDRAW_LIMIT_RATE" value="{{$conf['WITHDRAW_LIMIT_RATE']}}">
                                    </div>
                                    <div class="form-group">
                                        <label for="MIN_WITHDRAW">最小的提现额度</label>
                                        <input type="number" class="form-control" placeholder="请输入最小的提现额度" id="MIN_WITHDRAW" name="MIN_WITHDRAW" value="{{$conf['MIN_WITHDRAW']}}">
                                    </div>
                                    <div class="form-group">
                                        <label for="SIGN_CP">每日签到获取的算力</label>
                                        <input type="number" class="form-control" placeholder="请输入获取的算力" id="SIGN_CP" name="SIGN_CP" value="{{$conf['SIGN_CP']}}">
                                    </div>
                                    <div class="form-group">
                                        <label for="INVITE_CP">每邀请一人获取的算力</label>
                                        <input type="number" class="form-control" placeholder="请输入获取的算力" id="INVITE_CP" name="INVITE_CP" value="{{$conf['INVITE_CP']}}">
                                    </div>
                                    <div class="form-group">
                                        <label for="INVITE_CP">转账手续费0.1即(10%)</label>
                                        <input type="number" class="form-control" placeholder="请输入转账手续费0.1即(10%)" id="TRANS_FEE" name="TRANS_FEE" value="{{$conf['TRANS_FEE']}}">
                                    </div>
                                    <div class="form-group">
                                        <label for="SIGN_CP">转账人算力获取比例</label>
                                        <input type="number" class="form-control" placeholder="请输入转账收款人算力获取比例" id="TRANSFER_CP" name="TRANSFER_CP" value="{{$conf['TRANSFER_CP']}}">
                                    </div>
                                    <div class="form-group">
                                        <label for="INVITE_CP">转账人推荐人算力获取比例</label>
                                        <input type="number" class="form-control" placeholder="请输入转账收款人推荐人算力获取比例" id="TRANSFER_INVITE" name="TRANSFER_INVITE" value="{{$conf['TRANSFER_INVITE']}}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="panel panel-default" style="height:496px;">
                        <div class="panel-heading">
                            挖矿设置
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="MINE_PERCENT">奖池可挖掘比例</label>
                                        <input type="number" class="form-control" placeholder="请输入比例" id="MINE_PERCENT" name="MINE_PERCENT" value="{{$conf['MINE_PERCENT']}}">
                                    </div>
                                    <div class="form-group">
                                        <label for="MINE_PEROID">挖掘周期（天）</label>
                                        <input type="text" class="form-control" placeholder="请输入挖掘周期" id="MINE_PEROID" name="MINE_PEROID" value="{{$conf['MINE_PEROID']}}">
                                    </div>
                                    <div class="form-group">
                                        <label for="MINE_DIV">挖掘出的奖金最大份数</label>
                                        <input type="number" class="form-control" placeholder="请输入份数" id="MINE_DIV" name="MINE_DIV" value="{{$conf['MINE_DIV']}}">
                                    </div>
                                    <div class="form-group">
                                        <label for="MINE_MIN">掘金时最小产出的量</label>
                                        <input type="number" class="form-control" placeholder="请输入最小产出的量" id="MINE_MIN" name="MINE_MIN" value="{{$conf['MINE_MIN']}}">
                                    </div>
                                </div>
                                @if(config("app.friends"))
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="STEAL_PERCENT">未领取奖金会被偷取的比例</label>
                                        <input type="number" class="form-control" placeholder="请输入比例" id="STEAL_PERCENT" name="STEAL_PERCENT" value="{{$conf['STEAL_PERCENT']}}">
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                @if(config("app.otc"))
                    <div class="col-md-6">
                        <div class="panel panel-default" style="height: 425px;">
                            <div class="panel-heading">
                                交易设置
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="MIN_OTC_SALE">最小出售数量</label>
                                            <input type="number" class="form-control" placeholder="请输入最小出售数量" id="MIN_OTC_SALE" name="MIN_OTC_SALE" value="{{$conf['MIN_OTC_SALE']}}">
                                        </div>
                                        <div class="form-group">
                                            <label for="OTC_SALE_LIMIT">卖家同时允许出售的单子数</label>
                                            <input type="number" class="form-control" placeholder="请输入单子数量" id="OTC_SALE_LIMIT" name="OTC_SALE_LIMIT" value="{{$conf['OTC_SALE_LIMIT']}}">
                                        </div>
                                        <div class="form-group">
                                            <label for="OTC_BUY_LIMIT">买家同时允许购买的单子数</label>
                                            <input type="number" class="form-control" placeholder="请输入单子数量" id="OTC_BUY_LIMIT" name="OTC_BUY_LIMIT" value="{{$conf['OTC_BUY_LIMIT']}}">
                                        </div>
                                        <div class="form-group">
                                            <label for="DAY_VIOLATE_LIMIT">买家、卖家违约每日上限</label>
                                            <input type="number" class="form-control" placeholder="请输入上限次数" id="DAY_VIOLATE_LIMIT" name="DAY_VIOLATE_LIMIT" value="{{$conf['DAY_VIOLATE_LIMIT']}}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="OTC_FEE_RATE">手续费比例</label>
                                            <input type="number" class="form-control" placeholder="请输入手续费比例" id="OTC_FEE_RATE" name="OTC_FEE_RATE" value="{{$conf['OTC_FEE_RATE']}}">
                                        </div>
                                        <div class="form-group">
                                            <label for="OTC_ORDER_OVERTIME">下单超时时间（分钟）</label>
                                            <input type="number"  name="OTC_ORDER_OVERTIME" value="{{$conf['OTC_ORDER_OVERTIME']}}"   class="form-control" id="OTC_ORDER_OVERTIME" placeholder="请输入时间（分钟）">
                                        </div>
                                        @if(config("app.otc_sale_price"))
                                        <div class="form-group">
                                            <label for="OTC_SALEPRICE_RATE">用户出价浮动比例</label>
                                            <input type="number" class="form-control" placeholder="请输入比例" id="OTC_SALEPRICE_RATE" name="OTC_SALEPRICE_RATE" value="{{$conf['OTC_SALEPRICE_RATE']}}">
                                        </div>
                                        @endif
                                        <div class="form-group">
                                            <label for="OTC_SALE_AUTH">卖家是否需要授权</label>
                                            <select class="form-control" name="OTC_SALE_AUTH" id="OTC_SALE_AUTH" style="width: 60px;">
                                                <option value="0" @if($conf['OTC_SALE_AUTH']==0) selected="selected" @endif>否</option>
                                                <option value="1" @if($conf['OTC_SALE_AUTH']==1) selected="selected" @endif>是</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="TRANSFER_OPEN">全平台用户转账权限</label>
                                            <select class="form-control" name="TRANSFER_OPEN" id="TRANSFER_OPEN" style="width: 80px;">
                                                <option value="0" @if($conf['TRANSFER_OPEN']==0) selected="selected" @endif>关闭</option>
                                                <option value="1" @if($conf['TRANSFER_OPEN']==1) selected="selected" @endif>开启</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="TRANSFER_OVER_LIMIT">超出此数值的部分可以用于转账</label>
                                            <input type="number" class="form-control" placeholder="超出此数值的部分可以用于转账" id="TRANSFER_OVER_LIMIT" name="TRANSFER_OVER_LIMIT" value="{{$conf['TRANSFER_OVER_LIMIT']}}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                <div class="col-md-6">
                    <div class="panel panel-default" style="height: 485px;">
                        <div class="panel-heading">
                            钱包设置
                        </div>
                        <div class="panel-body">
                            <div class="form-group">
                                <label for="isopen">是否开启</label>
                                <select class="form-control" name="isopen" id="isopen" style="width: 60px;">
                                    <option value="1" @if($platform_token->isopen==1) selected="selected" @endif>是</option>
                                    <option value="0" @if($platform_token->isopen==0) selected="selected" @endif>否</option>
                                </select>
                            </div>
                            <div class="form-group" id="showDefault">
                                <label for="default">是否默认显示</label>
                                <select class="form-control" name="default" id="default" style="width: 60px;">
                                    <option value="1" @if($platform_token->default==1) selected="selected" @endif>是</option>
                                    <option value="0" @if($platform_token->default==0) selected="selected" @endif>否</option>
                                </select>
                            </div>
                            <div class="form-group" id="showBlockChain">
                                <label for="block_chain">所属区块链</label>
                                <select class="form-control" name="block_chain" id="block_chain" style="width: 140px;">
                                    @foreach(config("app.block_chain") as $chain_name=>$row)
                                    <option value="{{$chain_name}}" @if($platform_token->block_chain==$chain_name) selected="selected" @endif>{{$chain_name}}</option>
                                     @endforeach
                                </select>
                            </div>
                            <div class="form-group" id="showTokenType">
                                <label for="token_type">币类型</label>
                                <select class="form-control" name="token_type" id="token_type" style="width: 140px;">
                                    <option value="1" @if($platform_token->token_type==1) selected="selected" @endif>基础币</option>
                                    <option value="2" @if($platform_token->token_type==2) selected="selected" @endif>ERC20代币</option>
                                    {{--<option value="3" @if($platform_token->token_type==3) selected="selected" @endif>其它</option>--}}
                                </select>
                            </div>
                            <div class="form-group" id="showContractAddress">
                                <label for="contract_address">合约地址</label>
                                <div class="row">
                                    <div class="col-md-10">
                                        <input type="text" class="form-control" placeholder="请输入合约地址" id="contract_address" name="contract_address" value="{{$platform_token->contract_address}}">
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-primary" onclick="checkContractAddress()">验证</button>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="incharge_address">充币地址</label>
                                <input type="text" class="form-control" placeholder="请输入地址" id="incharge_address" name="incharge_address" value="{{$platform_token->incharge_address}}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <form action="{{url('setting.coinprice')}}" id="form">
        <div class="container">
            <div class="row">
                <div class="col-md-8">
                    <div id="price_chart" style="height:400px;"></div>
                </div>
                <div class="col-md-4">
                    <div style="float: right;width: 340px;height: 32px;line-height: 32px;">
                        <div style="float: left;">日期选择：</div>
                        <input type="text" id="chart_date_time" name="chart_date_time" value="{{$param['chart_date_time']}}" class="form-control" style="float: left;width: 180px;" />
                        <a href="javascript:void(0)" onclick="clearForm()" class="btn btn-x btn-default" style="margin-left: 10px;">重置</a>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <script src="{{asset('web/js/public/echarts/echarts.min.js')}}"></script>
    <script>
        $(function(){
            var token_type = '{{$platform_token->token_type}}';
            if(token_type==2){
                $('#showContractAddress').show();
            }else{
                $('#showContractAddress').hide();
            }
            $('#token_type').change(function () {
                var value = $(this).val();
                if(value==2){
                    $('#showContractAddress').show();
                }else{
                    $('#showContractAddress').hide();
                }
            });
        });
        function save(){
            layer.confirm("确定要保存更改？",{title:'提示',offset:'300px'},function(){
                var data  = $('#setting').serializeArray();
                $.ajax({
                    url:'{{url('save_basecoin_conf')}}',
                    data:data,
                    type:'POST',
                    dataType:'JSON',
                    success:function (response) {
                        layer.msg(response.message);
                        if(response.errcode==0) {
                            setTimeout(function () {
                                location.reload();
                            },2000)
                        }
                    },error:function (error) {
                        layer.msg(error.message);
                    }
                });
            });
        }
        function checkContractAddress(){
            $.ajax({
                url:'{{url('check_contract_address')}}',
                data:{contract_address:$('#contract_address').val(),block_chain:$("#block_chain").val()},
                type:'POST',
                dataType:'JSON',
                success:function (response) {
                    layer.msg(response.message);
                    if(response.errcode==0){
                        $("#COIN_ENNAME").val(response.data.name);
                        $("#COIN_UNIT").val(response.data.symbol);
                        $("#COIN_DECIMALS").val(response.data.decimals);
                    }
                },error:function (error) {
                    layer.msg(error.message);
                }
            });
        }
        $('#chart_date_time').daterangepicker({
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
            beginTimeStore = start;
            endTimeStore = end;
            if(!this.startDate){
                this.element.val('');
            }else{
                this.element.val(this.startDate.format(this.locale.format) + this.locale.separator + this.endDate.format(this.locale.format));
                $("#form").submit();
            }
        });
        function clearForm(){
            $("#chart_date_time").val("");
            $("#form").submit();
        }
        $.fanwe_upload("coin_icon_upload",'{{url("upload_icon")}}',{},"jpg,png,gif,jpeg",1,false,function(){
            //上传中
        },function(up,file,res){
            var data = JSON.parse(res.response);
            var timestamp = Date.parse(new Date());
            $("#show_icon").attr("src",data['data']['src']+"?x-oss-process=image/resize,m_fill,h_90,w_90&v="+timestamp);
            $("#icon").val(data['data']['src']);
        },function(){
            //上传完成
        },function(up, err){

        });
        var myChart = echarts.init(document.getElementById('price_chart'));
        var option = {
            title: {
                text: '价格走势'
            },
            tooltip: {},
            legend: {
                data:['价格']
            },
            xAxis: {
                data: [
                    @foreach($chartInfo as $day)
                        "{{$day['date']}}",
                    @endforeach
                ]
            },
            yAxis: {},
            series: [{
                name: '价格',
                type: 'line',
                data: [
                    @foreach($chartInfo as $price)
                        "{{$price['price']}}",
                    @endforeach
                ]
            }]
        };
        myChart.setOption(option);
    </script>

@stop
