
@extends('manage.layouts.dashboard')

@section('container')
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">奖励日志（共 {{$lists->total()}} 条记录）</h3>
        </div>
        <div class="panel-body">
            <form id="search_form" action="{{url("incharge.reward")}}" method="GET" class="form-horizontal" role="form" >
                <div class="form-group">
                    <div class="col-md-2"  style="display:block; float: left; padding-left:0px;">
                        <div class="input-group">
                            <span class="input-group-addon">用户</span>
                            <input type="text" class="form-control" name="keyword" value="{{$param['keyword']}}" placeholder="输入用户名/手机号">
                        </div>
                    </div>
                    <div class="col-md-2" style="display:block; float: left;">
                        <div class="input-group">
                            <span class="input-group-addon">日期</span>
                            <input type="text" id="exchange_date" name="exchange_date" value="{{$param['exchange_date']}}" class="form-control" style="float: left;width: 170px;" />
                        </div>
                    </div>
                    <div class="col-md-2"  style="display:block; float: left;">
                        <button type="button" class="btn btn-primary" onclick="clearSearch()">清 除</button>
                        <button type="button" class="btn btn-primary" onclick="export_token()">导 出</button>
                        <button type="submit" class="btn btn-primary">查 询</button>
                    </div>
                </div>
            </form>
        </div>
        <table class="table table-bordered">
            <thead>
            <tr>
                <th>充值人用户名</th>
                <th>充值人手机</th>
                <th>奖励人用户名</th>
                <th>奖励人手机</th>
                <th>奖励数额USDG</th>
                <th>代数</th>
                <th>时间</th>
            </tr>
            </thead>
            <tbody>
            @foreach($lists as $key =>$item)
                <tr>
                    <td>{{$item->from_user->username}}</td>
                    <td>{{$item->from_user->mobile}}</td>
                    <td>{{$item->reward_user->username}}</td>
                    <td>{{$item->reward_user->mobile}}</td>
                    <td>{{$item->amount}}</td>
                    <td>{{$item->lv_diff}}</td>
                    <td>{{$item->create_time}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div style="position:relative;top:10px;">{!! $lists->appends($param)->render() !!}</div>

        <script>
            function clearSearch(){
                $("input[name='keyword']").val('')
                $('#exchange_date').val('')
            }
            $('#exchange_date').daterangepicker({
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
                var beginTimeStore = start;
                var endTimeStore = end;
                if(!this.startDate){
                    this.element.val('');
                }else{
                    this.element.val(this.startDate.format(this.locale.format) + this.locale.separator + this.endDate.format(this.locale.format));
                }
            });
            function export_token() {
                var url = `{!! $url !!}`;
                location.href = url;
            }


        </script>

@stop
