
@extends('manage.layouts.dashboard')

@section('container')
    <div class="panel panel-default">
        <div class="panel-body">
            <form id="search_form" action="{{url("setting.recharge")}}" method="GET" class="form-horizontal" role="form" >
                <div class="form-group">
                    <div class="col-md-4" style="display:block; float: left;">
                        <span style="float: left;line-height: 30px;">地址类型：</span>
                        <select class="form-control" name="address_type" id="address_type" style="width: 100px;float: left;margin-left: 20px;">
                            <option value="1">合约地址</option>
                            <option value="2">充币地址</option>
                        </select>
                        <div class="input-group">
                            <span class="input-group-addon">地址</span>
                            <input type="text" class="form-control" id="address" name="address" value="{{$address}}" placeholder="请输入地址">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group">
                            <span class="input-group-addon">代币标识</span>
                            <input type="text" class="form-control" id="token_symbol" name="token_symbol" value="{{$token_symbol}}" placeholder="请输入代币标识">
                        </div>
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
                <th>合约的地址</th>
                <th>代币名称</th>
                <th>代币标识</th>
                <th>代币精度</th>
                <td>充币地址</td>
                <th>兑换基础币比率(1个币 = xx基础币)</th>
                <th>设置</th>
            </tr>
            </thead>
            <tbody>
            @foreach($lists as $key =>$item)
                <tr>
                    <td>@if($item->contract_address =='0x')ETH @elseif($item->contract_address) {{$item->contract_address}} @else 其他链的币 @endif</td>
                    <td>{{$item->token_name}}</td>
                    <td>{{$item->token_symbol}}</td>
                    <td>{{$item->token_decimals}}</td>
                    <td>{{$item->incharge_address}}</td>
                    <td>{{$item->incharge_rate}}</td>
                    <td><button data-toggle="modal" data-target="#myModal" type="button" class="btn btn-success" onclick="setAddress('{{$item->id}}','{{$item->incharge_address}}','{{$item->incharge_rate}}')"> 设置</button></td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div style="position:relative;top:10px;">{!! $lists->appends(['address_type'=>$address_type,'address'=>$address,'token_symbol'=>$token_symbol])->render() !!}</div>

        <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title" id="myModalLabel">设置</h4>
                    </div>
                    <div class="modal-body">

                        <div style="padding: 10px 10px 10px;">
                            <form class="bs-example bs-example-form" role="form" id="form">
                                <input type="hidden" id="coin_type" >
                                <div class="input-group" style="margin-top: 20px;">
                                    <span class="input-group-addon">充币地址</span>
                                    <input type="text" class="form-control" placeholder="请输入充币地址" id="incharge_address" name="incharge_address">
                                </div>
                                <div class="input-group" style="margin-top: 20px;">
                                    <span class="input-group-addon">兑换基础币比率</span>
                                    <input type="text" class="form-control" placeholder="请输入兑换基础币比率" id="incharge_rate" name="incharge_rate">
                                </div>
                                <input type="hidden" id="id" name="id">
                            </form>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal" onclick="clean()">关闭</button>
                        <button type="button" class="btn btn-primary" onclick="submit()">提交更改</button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal -->
        </div>

        <script>
            function clearSearch(){
                $('#address').val('')
            }
            function setAddress(id,incharge_address,incharge_rate) {
                $('#id').val(id);
                $('#incharge_address').val(incharge_address);
                $('#incharge_rate').val(incharge_rate)
            }
            function submit() {
                var formData =  $('#form').serialize();

                $.ajax({
                    url:"{{url('setting.recharge_save')}}",
                    data:formData,
                    type:'POST',
                    dataType:'JSON',
                    success:function (response) {
                        if(response.errcode==0) {
                            clearSearch();
                            layer.msg('设置成功');
                            setTimeout(function () {
                                location.reload();
                            },2000)
                        }else{
                            layer.msg(response.message);
                        }
                    },error:function(err){
                        layer.msg('设置失败');
                    }
                })
            }
            $(function () {
                $('#address_type').val('{{$address_type}}')
            })
            $('#fixed').change(function () {
                $('#search_form').submit();
            })
        </script>

@stop
