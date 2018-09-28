@extends('manage.layouts.dashboard')

@section('container')

    <div class="page_header">
        <div class="row" style="padding:20px;" id="sync_block">
            <div class="col-md-3 tjspan" style="height:30%">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">用户统计</h3>
                    </div>
                    <div class="panel-body" style="padding:10px 10px 0;">
                        <div style="width:100%;padding:10px;">
                            <p><strong>今日注册用户：<a href="{{url('user')}}" style="color: red">{{$stat['user_today']}}</a></strong></p>
                            <p><strong></strong></p>
                            <p><strong  style="margin-left: 23px;">用户总数：<a href="{{url('user')}}" style="color: red">{{$stat['user_total']}}</a></strong></p>
                        </div>
                    </div>
                </div>
            </div>

            <div>

            <div class="col-md-3 tjspan" style="height:30%"  v-show="!ethereum_block_datas" >
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">区块信息加载中</h3>
                    </div>
                    <div class="panel-body" style="padding:10px 10px 0;">
                        <div style="width:100%;padding:10px;">
                            <p><strong>区块链上最新区块编号：加载中...</strong></p>
                            <p><strong>平台最新区块编号：加载中...</strong></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 tjspan" style="height:30%" v-for="(ethereum_block_data, index) in ethereum_block_datas">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">@{{ethereum_block_data.block_chain}}区块信息</h3>
                    </div>
                    <div class="panel-body" style="padding:10px 10px 0;">
                        <div style="width:100%;padding:10px;">
                            <p><strong>区块链上最新区块编号：@{{ethereum_block_data.latest_block_number}}</strong></p>
                            <p><strong>平台最新区块编号：@{{ethereum_block_data.block_number}}</strong></p>
                        </div>
                    </div>
                </div>
            </div>

            {{--<div class="col-md-4 tjspan" style="height:30%">--}}
                {{--<div class="panel panel-default">--}}
                    {{--<div class="panel-heading">--}}
                        {{--<h3 class="panel-title">交易统计</h3>--}}
                    {{--</div>--}}
                    {{--<div class="panel-body" style="padding:10px 10px 0;">--}}
                        {{--<div style="width:100%;padding:10px;">--}}
                            {{--<p>--}}
                                {{--<strong>今日总订单数：<a href="{{url('transaction.saleorder')}}" style="color: red">{{$stat['otc_order_today']}}</a></strong>--}}
                                {{--<strong style="margin-left: 50px;">今日总订单总额：<a href="{{url('transaction.saleorder')}}" style="color: red">{{$stat['otc_sum_today']}}</a></strong>--}}
                            {{--</p>--}}
                            {{--<p><strong></strong></p>--}}
                            {{--<p>--}}
                                {{--<strong  style="margin-left: 23px;">总订单数：<a href="{{url('transaction.saleorder')}}" style="color: red">{{$stat['otc_order_total']}}</a></strong>--}}
                                {{--<strong style="margin-left: 50px;">总订单额：<a href="{{url('transaction.saleorder')}}" style="color: red">{{$stat['otc_order_sum']}}</a></strong>--}}
                            {{--</p>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                {{--</div>--}}
            {{--</div>--}}
        </div>
    </div>
    <form action="{{url('/')}}" id="form">
        <!-- 用户注册量图表 -->
        <div class="container">
            <div class="row">
                <div class="col-md-8">
                    <div id="user_chart" style="height:400px;"></div>
                </div>
                <div class="col-md-4">
                    <div style="float: right;width: 340px;height: 32px;line-height: 32px;">
                        <div style="float: left;">日期选择：</div>
                        <input type="text" id="user_chart_date" name="user_chart_date" value="{{$param['user_chart_date']}}" class="form-control" style="float: left;width: 180px;" />
                        <a href="javascript:void(0)" onclick="clearForm(1)" class="btn btn-x btn-default" style="margin-left: 10px;">重置</a>
                    </div>
                </div>
            </div>
        </div>
        {{--<div class="container">--}}
            {{--<div class="row">--}}
                {{--<div class="col-md-8">--}}
                    {{--<div id="otc_chart" style="height:400px;"></div>--}}
                {{--</div>--}}
                {{--<div class="col-md-4">--}}
                    {{--<div style="float: right;width: 340px;height: 32px;line-height: 32px;">--}}
                        {{--<div style="float: left;">日期选择：</div>--}}
                        {{--<input type="text" id="sale_chart_date" name="sale_chart_date" value="{{$param['sale_chart_date']}}" class="form-control" style="float: left;width: 180px;" />--}}
                        {{--<a href="javascript:void(0)" onclick="clearForm(1)" class="btn btn-x btn-default" style="margin-left: 10px;">重置</a>--}}
                    {{--</div>--}}
                {{--</div>--}}
            {{--</div>--}}
        {{--</div>--}}
    </form>
    <script src="{{asset('web/js/public/echarts/echarts.min.js')}}"></script>
    <script type="text/javascript">

        function repair_block(){
            if(confirm("立即开始快速同步任务吗？")){
                $.ajax({
                    url:"{{url("repairblock")}}",
                    type:"get",
                    dataType:"json",
                    success:function(obj){
                        if(obj.errcode)
                        {
                            alert(obj.message);
                        }
                        else
                        {
                            alert(obj.message);
                            location.reload();
                        }
                    },
                    error:function(){
                        alert("任务开启失败");
                    }
                });
            }
        }

        $('#user_chart_date').daterangepicker({
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
            beginTimeStore = start;
            endTimeStore = end;
            if(!this.startDate){
                this.element.val('');
            }else{
                this.element.val(this.startDate.format(this.locale.format) + this.locale.separator + this.endDate.format(this.locale.format));
                $("#form").submit();
            }
        });

//        $('#sale_chart_date').daterangepicker({
//            "linkedCalendars": false,
//            "autoUpdateInput": false,
//            "locale": {
//                format: 'YYYY-MM-DD',
//                separator: '~',
//                applyLabel: "应用",
//                cancelLabel: "取消",
//                resetLabel: "重置",
//            }
//        }, function(start, end, label) {
//            beginTimeStore = start;
//            endTimeStore = end;
//            if(!this.startDate){
//                this.element.val('');
//            }else{
//                this.element.val(this.startDate.format(this.locale.format) + this.locale.separator + this.endDate.format(this.locale.format));
//                $("#form").submit();
//            }
//        });

        var myChart = echarts.init(document.getElementById('user_chart'));
        var option = {
            title: {
                text: '用户注册量'
            },
            tooltip: {},
            legend: {
                data:['注册量']
            },
            xAxis: {
                data: [
                    @foreach($stat['user_chart'] as $day)
                            "{{$day['date']}}",
                    @endforeach
                ]
            },
            yAxis: {},
            series: [{
                name: '注册量',
                type: 'line',
                data: [
                    @foreach($stat['user_chart'] as $total)
                            "{{$total['num']}}",
                    @endforeach
                ]
            }]
        };
        myChart.setOption(option);

        {{--var otcChart = echarts.init(document.getElementById('otc_chart'));--}}
        {{--var otcOption = {--}}
            {{--title: {--}}
                {{--text: '订单统计'--}}
            {{--},--}}
            {{--tooltip: {--}}
                {{--trigger: 'axis'--}}
            {{--},--}}
            {{--legend: {--}}
                {{--data:['已完成订单金额','已完成订单总数']--}}
            {{--},--}}
            {{--xAxis: {--}}
                {{--data: [--}}
                    {{--@foreach($stat['otc_chart'] as $day)--}}
                        {{--"{{$day['date']}}",--}}
                    {{--@endforeach--}}
                {{--]--}}
            {{--},--}}
            {{--yAxis: {--}}
                {{--type:'value'--}}
            {{--},--}}
            {{--series: [{--}}
                {{--name: '已完成订单金额',--}}
                {{--type: 'line',--}}
                {{--data: [--}}
                    {{--@foreach($stat['otc_chart'] as $total)--}}
                        {{--"{{$total['total_price']}}",--}}
                    {{--@endforeach--}}
                {{--]--}}
            {{--},{--}}
                {{--name: '已完成订单总数',--}}
                {{--type: 'line',--}}
                {{--data: [--}}
                    {{--@foreach($stat['otc_chart'] as $total)--}}
                        {{--"{{$total['num']}}",--}}
                    {{--@endforeach--}}
                {{--]--}}
            {{--}--}}
            {{--]--}}
        {{--};--}}
        {{--otcChart.setOption(otcOption);--}}

        function clearForm(v){
            if(v==1){
                $("#user_chart_date").val("");
            }else{
//                $("#sale_chart_date").val("");
            }
            $("#form").submit();
        }



        var sync_block = new Vue({
            el:'#sync_block',
            data:{
                ethereum_block_datas:false,
            },
            mounted(){
                this.syncBlock();
            },
            methods:{
                syncBlock(){
                    var _this = this;
                    $.ajax({
                        url:"{{url("/?sync_block=1")}}",
                        dataType:"json",
                        type:"get",
                        success:function(data){
                            _this.ethereum_block_datas = data;
                        }
                    });
                }
            }
        });

    </script>

@stop
