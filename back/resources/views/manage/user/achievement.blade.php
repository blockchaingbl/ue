
@extends('manage.layouts.page')
@section('page_content')
    <style>
        body{
            padding-top: 0;
        }
    </style>
    <div class="panel panel-default">
        <div class="panel-heading">
            <div>
                历史累计业绩：{{$sum}}
                @if(isset($curr_sum))  选择日期累计业绩： {{$curr_sum}} @endif
            </div>
        </div>
        <div class="panel-body">
            <form id="search_form" action="{{Request::getRequestUri()}}" method="GET" class="form-horizontal" role="form" >
                <div class="row">
                    <div class="col-md-4 col-xs-4">
                        <div class="input-group">
                            <span class="input-group-addon">时间</span>
                            <input type="text" id="date" name="date" value="{{$date}}" class="form-control" />
                        </div>
                    </div>
                    <div class="col-md-2 col-xs-2" style="float: right">
                        <button type="button" class="btn btn-primary" onclick="clearSearch()">清 除</button>
                        <button type="submit" class="btn btn-primary">查 询</button>
                    </div>
                </div>

            </form>
        </div>
        <table class="table table-bordered">
            <thead>
            <tr>
                <th>用户名</th>
                <th>手机号</th>
                <th>金额</th>
                <th>备注</th>
                <th>时间</th>
            </tr>
            </thead>
            <tbody>
            @foreach($lists as $key =>$item)
                <tr>
                    <td>{{$item->invite_user->username}}</td>
                    <td>{{$item->invite_user->mobile}}</td>
                    <td>{{$item->total_amount}}</td>
                    <td>{{$item->memo}}</td>
                    <td>{{$item->create_time}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div style="position:relative;top:10px;">{!! $lists->appends(['date'=>$date])->render() !!}</div>
        <script>
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
            function clearSearch() {
                $('#date').val('')
            }
        </script>

@stop
