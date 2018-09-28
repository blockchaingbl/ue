
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
                统计日期 {{$calc_log->calc_date}}
            </div>
            <div>
                统计时间{{$calc_log->create_time}}
            </div>
            <div>
                共:{{$lists->total()}}人 双倍充值总额:{{$calc_log->inchage_amount}} 今日双倍充值金额：{{$calc_log->incharge_today_amount}}
            </div>
            <div>
                双倍充值未释放金额:{{$calc_log->incharge_less_amount}}
                日流通总额(不含双倍充值):{{$calc_log->expend_amount}}
            </div>
            if
            @if($calc_log->month_status==2)
            <div>
                月统计  双倍充值{{$calc_log->month_incharge_amount}}  会员转出:{{$calc_log->month_expend_amount}}
            </div>
            @endif
        </div>

        <table class="table table-bordered">
            <thead>
            <tr>
                <th>用户手机</th>
                <th>用户名</th>
                <th>账户余额</th>
                <th>冻结余额</th>
            </tr>
            </thead>
            <tbody>
            @foreach($lists as $key =>$item)
                <tr>
                    <td>{{$item->mobile}}</td>
                    <td> {{$item->username}}</td>
                    <td>{{$item->vc_total}}</td>
                    <td>{{$item->vc_freeze}}</td>


                </tr>
            @endforeach
            </tbody>
        </table>
        <div style="position:relative;top:10px;">{!! $lists->render() !!}</div>
        <script>


        </script>

@stop
