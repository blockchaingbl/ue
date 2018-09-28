
@extends('manage.layouts.dashboard')

@section('container')
    <div class="panel panel-default">
        <div class="panel-body">
            <form id="search_form" action="{{url("setting.pool")}}" method="GET" class="form-horizontal" role="form" >
                <div class="form-group">

                    <div class="col-md-4" style="display:block; float: left;">
                        <span style="float: left;line-height: 30px;">类型：</span>
                        <select class="form-control" name="fixed" id="fixed" style="width: 100px;float: left;margin-left: 20px;">
                            <option value="0">挖取中</option>
                            <option value="1">已挖完</option>
                        </select>
                        <div class="input-group">
                            <span class="input-group-addon">时间</span>
                            <input type="text" id="date" name="date" value="{{$date}}" class="form-control" style="float: left;width:170px;" />
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
                <th>图标</th>
                <th>币种</th>
                <th>@if($fixed==1)已挖完@else挖取中@endif矿池数量</th>
                <th>矿池总量</th>
                <td>添加</td>
            </tr>
            </thead>
            <tbody>
            @foreach($lists as $key =>$item)
                <tr>
                    <td style="width:40px;text-align:center;color:#cccccc;">
                        @if($item['icon'])<img src="{{$item['icon']}}" style="width:40px;">@else无@endif
                    </td>
                    <td>
                    @if($item->info['name'])
                        {{$item['name']."({$item['coin_unit']})"}}
                    @else
                    {{$item['coin_unit']}}
                    @endif
                    </td>
                    <td>{{$item['total']}}</td>
                    <td>{{number_format($item['total_amount'],8)}}</td>
                    <td>
                        <button type="button" class="btn btn-success" data-toggle="modal" data-target="#myModal" onclick="change('{{$item['id']}}')" >调整矿池</button>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title" id="myModalLabel">调整矿池</h4>
                    </div>
                    <div class="modal-body">

                        <div style="padding: 10px 10px 10px;">
                            <form class="bs-example bs-example-form" role="form">
                                <input type="hidden" id="coin_type" >
                                <div class="input-group">
                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="change_type" value="1" checked> 增加
                                        </label>
                                        <label>
                                            <input type="radio" name="change_type" value="2" > 扣减
                                        </label>
                                    </div>
                                </div>
                                <div class="input-group" style="margin-top: 20px;">
                                    <span class="input-group-addon">数量</span>
                                    <input type="number" class="form-control" placeholder="请输入数量" id="amount">
                                </div>
                                <div class="input-group" style="margin-top: 20px;">
                                    <span class="input-group-addon">备注</span>
                                    <input type="text" class="form-control" placeholder="请输入备注" id="memo">
                                </div>

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
                $('#date').val('')
            }
            function change(id){
                $('#coin_type').val(id)
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
                    $('#search_form').submit();
                }
            });
            function submit() {
                var formData = {
                    change_type:$('input[name="change_type"]:checked').val(),
                    amount:$('#amount').val(),
                    memo:$('#memo').val(),
                    coin_type:$('#coin_type').val()
                }
                $.ajax({
                    url:"{{url('setting.pool.add')}}",
                    data:formData,
                    type:'POST',
                    dataType:'JSON',
                    success:function (response) {
                        if(response.errcode==0) {
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
            function clean(){
                $('input[name="change_type"]').val(0)
                $('#amount').val('')
                $('#memo').val('')
                $('#coin_type').val(0)
            }
            $(function () {
                $('#fixed').val('{{$fixed}}')
            })
            $('#fixed').change(function () {
                $('#search_form').submit();
            })
        </script>

@stop
