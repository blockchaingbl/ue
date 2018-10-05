
@extends('manage.layouts.page')
@section('page_content')
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">充值待释放（共 {{$lists->total()}} 条记录）</h3>
        </div>
        <div class="panel-body">
            <form id="search_form" action="{{url("lock_transfer.logs")}}" method="GET" class="form-horizontal" role="form" >
                <div class="form-group">
                    {{--<div class="col-md-1"  style="display:block; float: left; padding-left:0px;">--}}
                        {{--<div class="input-group">--}}
                            {{--<select name="coin_type" id="coin_type" class="form-control" onchange="toSearch()">--}}
                                {{--@foreach($coin_type_all as $item)--}}
                                    {{--<option value="{{$item['coin_type']}}">{{$item['coin_unit']}}</option>--}}
                                {{--@endforeach--}}
                            {{--</select>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                    <div class="col-md-2 col-xs-6"  style="display:block; float: left; padding-left:10px;">
                        <div class="input-group">
                            <span class="input-group-addon">接收者手机号</span>
                            <input type="text" class="form-control" name="to" id="to" value="{{$param['to']}}" placeholder="输入用户名/手机号">
                        </div>
                    </div>
                    <div class="col-md-3" style="display:block; float: left;">
                        <div class="input-group" style="float: left;width: 150px;">
                            <span class="input-group-addon">日期</span>
                            <select class="form-control" id="time_type" name="time_type" style="width:100px;">
                                <option value="1" selected="selected">充值时间</option>
                                <option value="2">释放完毕时间</option>
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
                <th>充值时间</th>
                <th>接收者</th>
                <th>数量</th>
                <th>剩余未释放</th>
                <th>剩余释放天数</th>
            </tr>
            </thead>
            <tbody>
            @foreach($lists as $key =>$item)
                <tr>
                    <td>{{$item->create_time}}</td>
                    <td>{{$item->to_user->mobile}}</td>
                    <td>{{$item->amount}}</td>
                    <td>{{$item->less_amount}}</td>
                    <td>{{$item->remain_time}}天</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div style="position:relative;top:10px;">{!! $lists->appends(['coin_type'=>$param['coin_type'],'to'=>$param['to'],'date'=>$param['date'],'time_type'=>$param['time_type']])->render() !!}</div>

        <script>
            function clearSearch(){
                $("#from").val('');
                $('#date').val('');
                $('#to').val('');
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
            function export_log() {
                location.href='{{url('incharge.export')}}'
            }
        </script>
@stop
