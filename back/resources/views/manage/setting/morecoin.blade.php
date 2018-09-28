@extends('manage.layouts.dashboard')

@section('container')

    <style>
        .nav-tabs>li.active>a, .nav-tabs>li.active>a:focus, .nav-tabs>li.active>a:hover{
            background: #428bca;
            color: #ffffff;
        }
        .tab-content>.tab-pane{
            padding: 20px;
            border-left: 1px solid #dddddd;
            border-right: 1px solid #dddddd;
            border-bottom: 1px solid #dddddd;
        }
    </style>

    <div class="panel panel-default">
        <div class="panel-heading">
            当前资产共：{{$count}} 个
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-2">
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#coin_deal" onclick="App.add()">新增</button>
                </div>
                <div class="col-md-2">
                    <div class="input-group">
                    <span class="input-group-addon">价格同步间隔分钟数</span>
                    <input type="number" id="lock_time"  class="form-control" placeholder="请输入同步间隔分钟数" value="{{$lock_time}}">
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-primary" onclick="App.saveLockTime()">修改</button>
                </div>
            </div>

        </div>
        <table class="table table-bordered">
            <thead>
            <tr>
                <th style="text-align:center;">图标</th>
                <th>名称</th>
                <th>单位</th>
                <th>价格</th>
                <th>法币</th>
                <th style="width: 300px;">简介</th>
                <th>提现手续费</th>
                <th>最小的提现额度</th>
                <th>奖池可挖掘比例</th>
                <th>挖掘周期（天）</th>
                <th>挖掘份数</th>
                <th>最小的产出量</th>
                @if(config("app.friends"))
                <th>未领取被偷比例</th>
                @endif
                <th style="width:60px;">操作</th>
            </tr>
            </thead>
            <tbody>
            @foreach($list as $key =>$item)
                <tr>
                    <td style="width:40px;text-align:center;color:#cccccc;">
                        @if($item->icon)<img src="{{$item->icon}}" style="width:40px;">@else无@endif
                    </td>
                    <td>{{$item->name}}</td>
                    <td>{{$item->coin_unit}}</td>
                    <td>{{$item->price}}</td>
                    <td>{{$item->price_unit}}</td>
                    <td>{{$item->description}}</td>
                    <td>{{$item->withdraw_rate}}</td>
                    <td>{{$item->min_withdraw}}</td>
                    <td>{{$item->mine_percent}}</td>
                    <td>{{$item->mine_peroid}}</td>
                    <td>{{$item->mine_div}}</td>
                    <td>{{$item->mine_min}}</td>
                    @if(config("app.friends"))
                    <td>{{$item->steal_percent}}</td>
                    @endif
                    <td style="text-align:center;">
                        <button type="button" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#coin_deal" config-data="{{$item}}" onclick="App.edit(this)">修改</button>
                        <button style="margin-top: 10px;" type="button" type="button" class="btn btn-info btn-xs" onclick="App.showLog('{{$item->api_param}}')">采集详情</button>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div style="position:relative;top:10px;">{!! $list->render() !!}</div>
    </div>

    <div class="modal fade" id="coin_deal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog" style="width:600px;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel">资产设置</h4>
                </div>
                <div class="modal-body">
                    <form class="bs-example bs-example-form" role="form" id="setting">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="name">中文名称</label>
                                    <input type="text" class="form-control" placeholder="请输入中文名称" id="name" name="name">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="en_name">英文名称</label>
                                    <input type="text" class="form-control" placeholder="请输入英文名称" id="en_name" name="en_name">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="coin_unit">单位</label>
                                    <input type="text" class="form-control" placeholder="例：ETH、BTC" id="coin_unit" name="coin_unit">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="token_decimals">代币精度</label>
                                    <input type="number" class="form-control" placeholder="请输入代币精度" id="token_decimals" name="token_decimals">
                                </div>
                            </div>
                        </div>
                        <ul class="nav nav-tabs">
                            <li class="active"><a href="#base" data-toggle="tab">基础设置</a></li>
                            <li><a href="#mine" data-toggle="tab">挖矿设置</a></li>
                            <li><a href="#trend" data-toggle="tab">行情设置</a></li>
                            <li><a href="#wallet" data-toggle="tab">钱包设置</a></li>
                        </ul>
                        <div id="myTabContent" class="tab-content">
                            <!--基础设置-->
                            <div class="tab-pane active" id="base">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="withdraw_rate">图标</label>
                                            <div class="row">
                                                <div class="col-md-5">
                                                    <div style="background:#dfdfdf;width:90px;height:90px;line-height:90px;text-align:center;position:relative;">
                                                        <img id="show_icon" style="max-width:90%;max-height:90%;">
                                                        <input type="hidden" id="icon" name="icon">
                                                    </div>
                                                </div>
                                                <div class="col-md-7">
                                                    <button type="button" class="btn btn-primary btn-xs" id="coin_icon_upload">选择</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="status">是否开启</label>
                                            <select class="form-control" name="status" id="status" style="width: 60px;">
                                                <option value="1">是</option>
                                                <option value="0">否</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="withdraw_rate">超出此数值的部分可以用于转账</label>
                                            <input type="number" class="form-control" placeholder="超出此数值的部分可以用于转账" id="transfer_over_limit" name="transfer_over_limit">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="withdraw_open">是否开启提现</label>
                                            <select class="form-control" name="withdraw_open" id="withdraw_open" style="width:60px;">
                                                <option value="1">是</option>
                                                <option value="0">否</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="withdraw_rate">提现手续费</label>
                                            <input type="number" class="form-control" placeholder="请输入手续费" id="withdraw_rate" name="withdraw_rate">
                                        </div>
                                        <div class="form-group">
                                            <label for="min_withdraw">最小的提现额度</label>
                                            <input type="number" class="form-control" placeholder="请输入额度" id="min_withdraw" name="min_withdraw">
                                        </div>
                                        <div class="form-group">
                                            <label for="min_withdraw">可提现比例(otc历史购买量x此比例为可提现额度)</label>
                                            <input type="number" class="form-control" placeholder="请输入比例" id="limit_rate" name="limit_rate">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="description">简介</label>
                                    <textarea class="form-control" placeholder="在此输入简介" id="description" name="description" style="height:160px;"></textarea>
                                </div>
                            </div>
                            <!--挖矿设置-->
                            <div class="tab-pane" id="mine">
                                <div class="form-group">
                                    <label for="mine_percent">奖池可挖掘比例</label>
                                    <input type="number" class="form-control" placeholder="请输入比例" id="mine_percent" name="mine_percent" style="width: 150px;">
                                </div>
                                <div class="form-group">
                                    <label for="mine_peroid">挖掘周期（天）</label>
                                    <input type="text" class="form-control" placeholder="请输入挖掘周期" id="mine_peroid" name="mine_peroid" style="width: 150px;">
                                </div>
                                <div class="form-group">
                                    <label for="mine_div">挖掘份数</label>
                                    <input type="number" class="form-control" placeholder="请输入份数" id="mine_div" name="mine_div" style="width: 150px;">
                                </div>
                                <div class="form-group">
                                    <label for="mine_min">最小的产出量</label>
                                    <input type="number" class="form-control" placeholder="请输入最小产出量" id="mine_min" name="mine_min" style="width: 150px;">
                                </div>
                                @if(config("app.friends"))
                                <div class="form-group">
                                    <label for="steal_percent">未领取被偷比例</label>
                                    <input type="number" class="form-control" placeholder="请输入比例" id="steal_percent" name="steal_percent" style="width: 150px;">
                                </div>
                                 @endif
                            </div>
                            <!--行情设置-->
                            <div class="tab-pane" id="trend">
                                <div class="form-group">
                                    <label for="api_available">是否从api获取行情价格</label>
                                    <select class="form-control" name="api_available" id="api_available" style="width: 60px;">
                                        <option value="1">是</option>
                                        <option value="0">否</option>
                                    </select>
                                </div>
                                <div class="form-group" id="api_p">
                                    <label for="api_available">选择api</label>
                                    <select class="form-control" name="api_param" id="api_param" style="width: 120px;">
                                        @foreach($api as $item)
                                            <option value="{{$item}}">{{$item}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="price">行情价格</label>
                                    <input type="number" class="form-control" placeholder="请输入价格" id="price" name="price" style="width: 120px;">
                                </div>
                            </div>
                            <!--钱包设置-->
                            <div class="tab-pane" id="wallet">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="isopen">是否开启</label>
                                            <select class="form-control" name="isopen" id="isopen" style="width: 60px;">
                                                <option value="1">是</option>
                                                <option value="0">否</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group" id="showBlockChain">
                                            <label for="block_chain">所属区块链</label>
                                            <select class="form-control" name="block_chain" id="block_chain" style="width: 140px;">
                                                @foreach(config("app.block_chain") as $chain_name=>$row)
                                                    <option value="{{$chain_name}}">{{$chain_name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group" id="showTokenType">
                                            <label for="token_type">币类型</label>
                                            <select class="form-control" name="token_type" id="token_type" style="width: 140px;">
                                                <option value="2" selected="selected">ERC20代币</option>
                                                <option value="1">基础币</option>
                                                {{--<option value="3">其它</option>--}}
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group" id="showContractAddress">
                                    <label for="contract_address">合约地址</label>
                                    <div class="row">
                                        <div class="col-md-10">
                                            <input type="text" class="form-control" placeholder="请输入合约地址" id="contract_address" name="contract_address">
                                        </div>
                                        <div class="col-md-2">
                                            <button type="button" class="btn btn-primary" onclick="App.checkContractAddress()">验证</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group" id="showDefault">
                                    <label for="default">是否默认显示</label>
                                    <select class="form-control" name="default" id="default" style="width: 60px;">
                                        <option value="1">是</option>
                                        <option value="0">否</option>
                                    </select>
                                </div>
                                <div class="form-group" id="showInchargeOpen">
                                    <label for="incharge_open">是否开启充值</label>
                                    <select class="form-control" name="incharge_open" id="incharge_open" style="width: 60px;">
                                        <option value="1">是</option>
                                        <option value="0">否</option>
                                    </select>
                                </div>
                                <div class="form-group" id="showInchargeAddress">
                                    <label for="incharge_address">充币地址</label>
                                    <input type="text" class="form-control" placeholder="请输入地址" id="incharge_address" name="incharge_address">
                                </div>
                                <div class="form-group">
                                    <label for="exchange_open">是否开启兑换</label>
                                    <select class="form-control" name="exchange_open" id="exchange_open" style="width: 60px;">
                                        <option value="1">是</option>
                                        <option value="0">否</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" id="coin_id" name="id">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                    <button type="button" class="btn btn-primary" onclick="App.save()">确认</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal -->
    </div>

    <script>
        window.App ={
            add:function(){
                $('#setting')[0].reset();
                $('#api_p').show();
                $('#showTokenType').show();
                $('#showContractAddress').show();
                $('#showDefault').show();
                $('#showInchargeOpen').show();
                $('#showInchargeAddress').show();
            },
            edit:function(obj){
                var config = JSON.parse($(obj).attr("config-data"));
                $("#coin_id").val(config.id);
                $("#show_icon").attr("src",config.icon);
                $("#icon").val(config.icon);
                $("#name").val(config.name);
                $("#en_name").val(config.en_name);
                $("#coin_unit").val(config.coin_unit);
                $("#token_decimals").val(config.token_decimals);
                $("#description").val(config.description);
                $("#withdraw_rate").val(config.withdraw_rate);
                $("#min_withdraw").val(config.min_withdraw);
                $("#mine_percent").val(config.mine_percent);
                $("#mine_peroid").val(config.mine_peroid);
                $("#mine_div").val(config.mine_div);
                $("#mine_min").val(config.mine_min);
                $("#steal_percent").val(config.steal_percent);
                $('#api_available').val(config.api_available);
                $("#price").val(config.price);
                $('#status').val(config.status);
                $('#withdraw_open').val(config.withdraw_open);
                $('#limit_rate').val(config.limit_rate);
                $('#transfer_over_limit').val(config.transfer_over_limit)
                if(config.api_available){
                    $('#api_p').show();
                    $('#api_param').val(config.api_param);
                }else{
                    $('#api_p').hide()
                }
                $('#isopen').val(config.isopen);
                if(config.isopen){
                    $('#showTokenType').show();
                    $('#token_type').val(config.token_type);
                    $('#block_chain').val(config.block_chain);
                    if(config.token_type==2){
                        $('#showContractAddress').show();
                    }else{
                        $('#showContractAddress').hide();
                    }
                    $('#showInchargeOpen').show();
                    $('#showDefault').show();
                    $('#contract_address').val(config.contract_address);
                    if(config.incharge_open){
                        $('#showInchargeAddress').show();
                        $('#incharge_address').val(config.incharge_address);
                    }else{
                        $('#showInchargeAddress').hide();
                    }
                }else{
                    $('#showTokenType').hide();
                    $('#showContractAddress').hide();
                    $('#showInchargeOpen').hide();
                    $('#showDefault').hide();
                    $('#showInchargeAddress').hide();
                }
                $('#incharge_open').val(config.incharge_open);
                $('#exchange_open').val(config.exchange_open);
                $("#default").val(config.default);
                $('#token_id').val(config.token_id);
            },
            save:function(){
                var data  = $('#setting').serializeArray();
                $.ajax({
                    url:'{{url('save_morecoin_conf')}}',
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
            },
            saveLockTime : function () {
                if( parseInt($('#lock_time').val())<=0){
                    layer.msg('请输入正确的时间间隔');
                    return false;
                }
                var data = {
                    'PRICE_FETCH_LOCK_TIME' : parseInt($('#lock_time').val()) || 1
                }
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
            },
            showLog : function (api_param) {
                layer.open({
                    type: 2,
                    content: '{{url('price.log.detail')}}'+'/'+api_param,
                    area: ['850px', '600px'],
                    title:'采集详情'
                });
            },
            checkContractAddress:function(){
                $.ajax({
                    url:'{{url('check_contract_address')}}',
                    data:{contract_address:$('#contract_address').val(),block_chain:$("#block_chain").val()},
                    type:'POST',
                    dataType:'JSON',
                    success:function (response) {
                        layer.msg(response.message);
                        if(response.errcode==0){
                            $("#en_name").val(response.data.name);
                            $("#coin_unit").val(response.data.symbol);
                            $("#token_decimals").val(response.data.decimals);
                        }
                    },error:function (error) {
                        layer.msg(error.message);
                    }
                });
            }
        };
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
        $(function () {
            $('#api_available').change(function () {
                var value = $(this).val();
                if(value==0){
                    $('#api_p').hide();
                }else{
                    $('#api_p').show();
                }
            });
            $('#isopen').change(function () {
                var value = $(this).val();
                if(value==0){
                    $('#showTokenType').hide();
                    $('#showContractAddress').hide();
                    $('#showInchargeOpen').hide();
                    $('#showInchargeAddress').hide();
                    $('#showDefault').hide();
                }else{
                    $('#showTokenType').show();
                    $('#showContractAddress').show();
                    $('#showInchargeOpen').show();
                    $('#showInchargeAddress').show();
                    $('#showDefault').show();
                }
            });
            $('#incharge_open').change(function () {
                var value = $(this).val();
                if(value==0){
                    $('#showInchargeAddress').hide();
                }else{
                    $('#showInchargeAddress').show();
                }
            });
            $('#token_type').change(function () {
                var value = $(this).val();
                if(value==2){
                    $('#showContractAddress').show();
                }else{
                    $('#showContractAddress').hide();
                }
            });
        });
    </script>

@stop
