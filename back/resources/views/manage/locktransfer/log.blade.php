
@extends('manage.layouts.dashboard')

@section('container')
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">转账明细（共 {{$lists->total()}} 条记录）</h3>
        </div>
        <div class="panel-body">
            <form id="search_form" action="{{url("lock_transfer.log")}}" method="GET" class="form-horizontal" role="form" >
                <div class="form-group">
                    <div class="col-md-1"  style="display:block; float: left; padding-left:0px;">
                        <div class="input-group">
                            <select name="coin_type" id="coin_type" class="form-control" onchange="toSearch()">
                                @foreach($coin_type_all as $item)
                                    <option value="{{$item['coin_type']}}">{{$item['coin_unit']}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2"  style="display:block; float: left; padding-left:10px;">
                        <div class="input-group">
                            <span class="input-group-addon">转账人手机号</span>
                            <input type="text" class="form-control" id="from" name="from" value="{{$param['from']}}" placeholder="输入用户名/手机号">
                        </div>
                    </div>
                    <div class="col-md-2"  style="display:block; float: left; padding-left:10px;">
                        <div class="input-group">
                            <span class="input-group-addon">接收者手机号</span>
                            <input type="text" class="form-control" name="to" id="to" value="{{$param['to']}}" placeholder="输入用户名/手机号">
                        </div>
                    </div>
                    <div class="col-md-3" style="display:block; float: left;">
                        <div class="input-group" style="float: left;width: 150px;">
                            <span class="input-group-addon">日期</span>
                            <select class="form-control" id="time_type" name="time_type" style="width:100px;">
                                <option value="1" selected="selected">转账时间</option>
                                <option value="2">解锁时间</option>
                            </select>
                        </div>
                        <input type="text" id="date" name="date" value="{{$param['date']}}" class="form-control" style="float: left;width: 210px;" />
                    </div>
                    <div class="col-md-4" style="display:block; float: left;">
                        <button type="button" class="btn btn-primary" onclick="clearSearch()">清 除</button>
                        <button type="submit" class="btn btn-primary">查 询</button>
                    </div>
                </div>
            </form>
        </div>
        <table class="table table-bordered">
            <thead>
            <tr>
                <th>转账时间</th>
                <th>转账人者</th>
                <th>接收者</th>
                <th>转账数量</th>
                <th>转账手续费</th>
                <th>剩余释放天数</th>
                <th>已释放</th>
                <td>每次释放</td>
                <th>下次释放时间</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            @foreach($lists as $key =>$item)
                <tr>
                    <td>{{$item->create_time}}</td>
                    <td>{{$item->from_user->mobile}}</td>
                    <td>{{$item->to_user->mobile}}</td>
                    <td>{{$item->amount}}</td>
                    <td>{{$item->sugar_fee.'  '.db_config('COIN_UNIT')}}</td>
                    <td>{{$item->free_day}}</td>
                    <td>{{$item->amount-$item->less_amount}}</td>
                    <td>{{$item->amount/$item->lock_time}}</td>
                    <td>{{date('Y-m-d H:i:s',strtotime($item->last_release_time)+86400)}}</td>
                    <td>
                        @if($item->free==0)
                        <button type="submit" class="btn btn-danger" onclick="cancel_trans('{{$item->id}}')">撤回转账</button>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div style="position:relative;top:10px;">{!! $lists->appends(['coin_type'=>$param['coin_type'],'from'=>$param['from'],'to'=>$param['to'],'date'=>$param['date'],'time_type'=>$param['time_type']])->render() !!}</div>

        <script>
            function clearSearch(){
                $("#from").val('');
                $('#date').val('');
                $('#to').val('');
            }
            function cancel_trans(id) {
                layer.confirm('是否确认撤销', {icon: 3, title:'提示'}, function(index){
                    $.ajax({
                        url:'{{'locktransfercancel'}}',
                        data:{id:id},
                        type:'post',
                        dataType:"JSON",
                        success:function (res) {
                            layer.close(index);
                            layer.msg(res.message);
                            setTimeout(function () {
                                location.reload();
                            },2000)
                        }
                    })

                });
            }
            function toSearch(){
                $("#search_form").submit();
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
                }
            });
            $(function () {
                $('#time_type').val('{{$param['time_type']}}')
                $('#coin_type').val('{{$param['coin_type']}}')
            })
        </script>
@stop