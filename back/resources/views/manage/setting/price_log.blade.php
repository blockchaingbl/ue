
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
                <img src="{{$coin_type->icon}}" alt="" style="width: 50px;height: 50px;">
               {{$coin_type->name}} 单位 : {{$coin_type->coin_unit}} 当前价格 : {{$coin_type->price}}￥
            </div>
        </div>
        <div class="panel-body">
            <div id="main" style="height:400px;"></div>
        </div>
        <table class="table table-bordered">
            <thead>
            <tr>
                <th>采集时间</th>
                <th>当前价格</th>
                <th>24小时最高成交价格</th>
                <th>24小时最低成交价格</th>
                <th>24小时成交量加权平均价格</th>
                <th>采集时美元汇率</th>
            </tr>
            </thead>
            <tbody>
            @foreach($lists as $key =>$item)
                <tr>
                    <td>{{$item->create_time}}</td>
                    <td>
                        {{$item->last}}$
                        <br/>
                        {{$item->last*$item->usd_rate}}￥
                    </td>
                    <td>
                        {{$item->high}}$
                        <br/>
                        {{$item->high*$item->usd_rate}}￥
                    </td>
                    <td>
                        {{$item->low}}$
                        <br/>
                        {{$item->low*$item->usd_rate}}￥
                    </td>
                    <td>
                        {{$item->vwap}}$
                        <br/>
                        {{$item->vwap*$item->usd_rate}}￥
                    </td>
                    <td>{{$item->usd_rate}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div style="position:relative;top:10px;">{!! $lists->appends(['table'=>$table,'type'=>$type,'coin_type'=>$coin_type])->render() !!}</div>
        <script src="{{asset('web/js/public/echarts/echarts.min.js')}}"></script>
        <script>
        var myChart = echarts.init(document.getElementById('main'));
        option = {
        xAxis: {
            data: [
                @foreach($chart as $key=>$val)
                    '{{$key}}',
                @endforeach
            ]
        },
        yAxis: {},
        series: [{
            type: 'k',
            data: [
                @foreach($chart as $key=>$val)
                [{{$val['day_start']}}, {{$val['day_end']}}, {{$val['day_min']}}, {{$val['day_max']}}],
                @endforeach
            ]
        }]
         };
        myChart.setOption(option);

        </script>

@stop
