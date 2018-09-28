
@extends('manage.layouts.dashboard')

@section('container')
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">（共 {{$lists->total()}} 条记录）</h3>
        </div>
        <div class="panel-body">
            <form id="search_form" action="{{url("lock_transfer.auth")}}" method="GET" class="form-horizontal" role="form" >
                <div class="row">
                    <div class="col-md-2">
                        <div class="input-group">
                            <span class="input-group-addon">数字资产</span>
                            <select name="coin_type" id="coin_type" class="form-control">
                                @foreach($coin_type_all as $item)
                                <option value="{{$item['coin_type']}}">{{$item['coin_unit']}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row" style="margin-top: 20px;">
                    <div class="col-md-2">
                        <div class="input-group">
                            <button type="button" class="btn btn-primary" data-target="#myModal" data-toggle="modal">添加权限</button>
                        </div>
                    </div>
                </div>
                <div class="row" style="margin-top: 20px;">
                    <div class="form-group">
                        <div class="col-md-2">
                            <div class="input-group">
                                <span class="input-group-addon">用户</span>
                                <input type="text" class="form-control" name="keyword" value="{{$param['keyword']}}" placeholder="请输入手机号">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-addon">授权时间</span>
                                <input type="text" id="date" name="date" value="{{$param['date']}}" class="form-control" style="float: left;width:170px;" />
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-primary" onclick="clearSearch()">清 除</button>
                            <button type="submit" class="btn btn-primary">查 询</button>
                        </div>
                    </div>
                </div>

            </form>
        </div>
        <table class="table table-bordered">
            <thead>
            <tr>
                <th>授权时间</th>
                <th>转账者</th>
                <th>资产量</th>
                <th>转账次数</th>
                <th>转账总额</th>
                <th>权限状态</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            @foreach($lists as $key =>$item)
                <tr>
                    <td>{{$item->create_time}}</td>
                    <td>{{$item->mobile}}</td>
                    <td>{{$item->vc_total}}</td>
                    <td>{{$item->sugar_time}}</td>
                    <td>{{$item->sugar_total}}</td>
                    <td>
                        @if($item->status)
                        已启用
                        @else
                        已禁用
                        @endif
                    </td>
                    <td>
                        <button type="button" class="btn btn-default" onclick="detail('{{$item->mobile}}')">明细</button>
                        <button type="button" class="btn btn-danger"  data-target="#edit_modal" data-toggle="modal"   onclick="edit('{{json_encode($item)}}');">编辑</button>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div style="position:relative;top:10px;">{!! $lists->appends(['keyword'=>$param['keyword'],'date'=>$param['date'],'coin_type'=>$param['coin_type']])->render() !!}</div>
        <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title" id="myModalLabel">添加权限</h4>
                    </div>
                    <div class="modal-body">
                        <div style="padding: 10px 10px 10px;">
                            <form class="bs-example bs-example-form" role="form">
                                <div class="form-group">
                                    <div class="input-group">
                                        <span class="input-group-addon">用户</span>
                                        <input style="width: 250px;" type="text" class="form-control" id="search_mobile"  placeholder="请输入手机号">
                                        <button type="button"  class="btn btn-primary" onclick="searchUser();">搜索</button>
                                    </div>
                                </div>
                                <div class="form-group" >
                                    <div class="row" style="background: rgba(0,0,0,0.2);height: 50px;line-height: 50px; display: none" id="result">
                                        <div class="col-xs-2">搜索结果：</div>
                                        <div class="col-xs-2" id="result_mobile"></div>
                                        <div class="col-xs-8" id="result_money"></div>
                                    </div>
                                    <div id="no_result" style="background: rgba(0,0,0,0.2);height: 50px;line-height: 50px; display: none">
                                        查无此用户,请确认输入手机号
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-xs-12">
                                            <span>转账时收取总额等额的{{db_config('COIN_UNIT')}}作为手续费，手续费比例：</span>
                                            <input placeholder="如0.01 为百分之一" id="receive_fee" type="number" class="form-control" style="width: 150px; display: inline-block">
                                        </div>
                                        <div class="col-xs-6"></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xs-6">
                                            <span>低于</span>
                                            <input id="min_limit" type="number" class="form-control" style="width: 100px; display: inline-block">
                                            <span>{{db_config('COIN_UNIT')}} 时，取消锁仓资格</span>
                                        </div>
                                        <div class="col-xs-6"></div>
                                    </div>
                                    <div class="radio" style="margin-top: 20px;" id="auth_chose">
                                        <span>权限类型</span>
                                        <label>
                                            <input type="radio" checked name="auth_type" value="0"> 限制最低锁定天数
                                        </label>
                                        <label>
                                            <input type="radio" name="auth_type" value="1"> 固定锁定天数
                                        </label>
                                        <label>
                                            <input type="radio" name="auth_type" value="2"> 可自由设定锁定天数
                                        </label>
                                    </div>
                                    <div class="row">
                                        <div class="col-xs-6">
                                            <span>天数</span>
                                            <input id="limit_day" type="number" class="form-control" style="width: 100px; display: inline-block">
                                        </div>
                                        <div class="col-xs-6"></div>
                                    </div>
                                    <div class="row" style="margin-top: 10px;">
                                        <div class="col-xs-12">
                                            <span>接收方获取算力比例</span>
                                            <input id="cp_rate" value="0.01" type="number" placeholder="默认为0.01" class="form-control" style="width: 100px; display: inline-block">
                                            <span>接收方推荐人获取算力比例</span>
                                            <input id="invite_cp_rate"  value="0.005" type="number" placeholder="默认为0.005" class="form-control" style="width: 100px; display: inline-block">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="row" style="margin-top: 20px;">
                                            <div class="col-xs-6">
                                                <div class="input-group">
                                                    <span class="input-group-addon">权限状态</span>
                                                    <select id="status" class="form-control">
                                                        <option value="1">启用</option>
                                                        <option value="0">禁用</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-xs-6"></div>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" value="" id="user_id">
                            </form>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                        <button type="button" class="btn btn-primary" onclick="save_auth()">提交更改</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="edit_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title" id="myModalLabel">编辑权限</h4>
                    </div>
                    <div class="modal-body">
                        <div style="padding: 10px 10px 10px;">
                            <form class="bs-example bs-example-form" role="form" id="edit_form">
                                <div class="form-group">
                                    <div class="row" style="margin-top: 20px;">
                                        <div class="col-xs-6">
                                            <div class="input-group">
                                                <span class="input-group-addon">权限状态</span>
                                                <select name="status" class="form-control">
                                                    <option value="1">启用</option>
                                                    <option value="0">禁用</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-xs-6"></div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-xs-12">
                                            <span>转账时收取总额等额的{{db_config('COIN_UNIT')}}作为手续费，手续费比例：</span>
                                            <input placeholder="如0.01 为百分之一" name="receive_fee" type="number" class="form-control" style="width: 150px; display: inline-block">
                                        </div>
                                        <div class="col-xs-6"></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xs-6">
                                            <span>低于</span>
                                            <input name="min_limit" type="number" class="form-control" style="width: 100px; display: inline-block">
                                            <span>{{db_config('COIN_UNIT')}} 时，取消锁仓资格</span>
                                        </div>
                                        <div class="col-xs-6"></div>
                                    </div>
                                    <div class="radio" style="margin-top: 20px;">
                                        <span>权限类型</span>
                                        <label>
                                            <input type="radio" name="auth_type" value="0"> 限制最低锁定天数
                                        </label>
                                        <label>
                                            <input type="radio" name="auth_type" value="1"> 固定锁定天数
                                        </label>
                                        <label>
                                            <input type="radio" name="auth_type" value="2"> 可自由设定锁定天数
                                        </label>
                                    </div>
                                    <div class="row" id="edit_day">
                                        <div class="col-xs-6">
                                            <span>天数</span>
                                            <input name="limit_day" type="number" class="form-control" style="width: 100px; display: inline-block">
                                        </div>
                                        <div class="col-xs-6"></div>
                                    </div>
                                    <div class="row" style="margin-top: 10px;">
                                        <div class="col-xs-12">
                                            <span>接收方获取算力比例</span>
                                            <input name="cp_rate" type="number" placeholder="默认为0.01" class="form-control" style="width: 100px; display: inline-block">
                                            <span>接收方推荐人获取算力比例</span>
                                            <input name="invite_cp_rate" type="number" placeholder="默认为0.005" class="form-control" style="width: 100px; display: inline-block">
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" name="id" id="id">
                            </form>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                        <button type="button" class="btn btn-primary" onclick="edit_auth()">提交更改</button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            var coin_unit = '{{$selectCoin['coin_unit']}}'
            function searchUser() {
                var data = {
                    mobile:$('#search_mobile').val(),
                    coin_type:'{{$param['coin_type']}}',
                };
                $.ajax({
                    url:'{{url('user.asset')}}',
                    data:data,
                    type:'POST',
                    dataType:'JSON',
                    success:function (res) {
                        if(res.errcode==0){
                            $('#result_mobile').html(res.data.mobile)
                            $('#result_money').html('拥有'+res.data.vc_total+coin_unit);
                            $('#result').show();
                            $('#no_result').hide();
                            $('#user_id').val(res.data.user_id)
                        }else{
                            $('#result').hide();
                            $('#no_result').show();
                            $('#user_id').val(0)
                        }
                    }
                })
            }
            $(function () {
                $('#coin_type').val('{{$param['coin_type']}}');
                $('#coin_type').change(function () {
                    $('#search_form').submit();
                });
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

                $('#myModal').on('hide.bs.modal', function () {
                    $('#user_id').val('');
                    $('#min_limit').val('')
                    $('#receive_fee').val('')
                    $('#cp_rate').val('0.01')
                    $('#invite_cp_rate').val('0.005')
                    $('#status').val(1)
                    $('#search_mobile').val('')
                    $('#result').hide()
                    $('#search_result').hide()
                    $('input[name="auth_type"][value="0"]').prop("checked", true);
                })
                $('input[name="auth_type"]').change(function () {
                    var val = $(this).val()
                    if(val==2){
                        $(this).parent().parent().next().hide();
                    }else{
                        $(this).parent().parent().next().show();
                    }
                })
            })
            function save_auth() {
                var data ={
                    'user_id':$('#user_id').val(),
                    'min_limit':$('#min_limit').val(),
                    'receive_fee':$('#receive_fee').val(),
                    'coin_type':'{{$param['coin_type']}}',
                    'status':$('#status').val(),
                    'limit_day':$('#limit_day').val(),
                    'auth_type': $('input[name="auth_type"]:checked').val(),
                    'cp_rate':$('#cp_rate').val(),
                    'invite_cp_rate':$('#invite_cp_rate').val()
                }
                $.ajax({
                    url:'{{url('lock_transfer.auth.save')}}',
                    data:data,
                    type:'POST',
                    dataType:'json',
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
            function detail(mobile) {
                var url ='{{url('lock_transfer.log')}}'+'?from='+mobile+'&coin_type={{$param['coin_type']}}';
                location.href = url
            }
            function clearSearch(){
                $("input[name='keyword']").val('')
                $('#date').val('')
            }
            function edit(param) {
                param = JSON.parse(param)
                $('#edit_modal').find('select[name="status"]').val(param.status)
                $('#edit_modal').find('input[name="id"]').val(param.id)
                $('#edit_modal').find('input[name="min_limit"]').val(param.min_limit)
                $('#edit_modal').find('input[name="receive_fee"]').val(param.receive_fee)
                $('#edit_modal').find('input[name="limit_day"]').val(param.limit_day)
                $('#edit_modal').find('input[name="invite_cp_rate"]').val(param.invite_cp_rate)
                $('#edit_modal').find('input[name="cp_rate"]').val(param.cp_rate)
                $('#edit_modal').find('input[name="auth_type"][value="'+param.auth_type+'"]').prop("checked", true);
                if(param.auth_type==2)
                {
                    $('#edit_day').hide();
                }else{
                    $('#edit_day').show()
                }
            }
            function edit_auth() {
                var data = $('#edit_form').serializeArray();
                $.ajax({
                    url:'{{url('lock_transfer.auth.edit')}}',
                    data:data,
                    type:'POST',
                    dataType:'json',
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
