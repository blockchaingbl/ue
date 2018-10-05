@extends('manage.layouts.dashboard')

@section('container')

    <div class="page_header">
        @foreach($lists as $val)
        <div class="col-md-12" class="clearfix" style="position: relative">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <span class="panel-title">  <img src="{{$val['icon']}}" alt="" style="width: 32px;height: 32px;">
                        {{$val['name']}} ({{$val['coin_unit']}}) </span>
                </div>
                <div class="panel-body" style="padding:10px 10px 0;">
                    <div style="width:100%;padding:10px;">
                        <p><strong>矿池总量：{{$val['mine_pool']}}</strong></p>
                        <p><strong>已产出待挖取：{{$val['un_mined']}}</strong></p>
                        <p><strong>已挖取：{{$val['mined']}}</strong></p>
                        <p>
                            <a class="btn btn-danger"  href="#" onclick="turnIncharge('{{$val['coin_type']}}')">充值记录</a>
                            <a class="btn btn-success" href="#" onclick="turnMineLog('{{$val['coin_type']}}')">产出记录</a>
                        </p>
                        <p>
                            <strong>用户可用资产总量：{{$val['vc_total']}} </strong>
                            @if(config('app.otc') && $val['coin_type']==0)
                            <strong>用户可交易资产总量：{{$val['vc_normal']}} </strong>
                            <strong>用户不可交易产总量：{{$val['vc_untrade']}}</strong>
                            @endif
                        </p>
                        <p>
                            <strong>用户冻结资产总量：{{$val['vc_freeze']}}</strong>
                            @if(config('app.otc') && $val['coin_type']==0)
                                <strong>用户冻结可交易资产总量：{{$val['vc_freeze_normal']}}</strong>
                                <strong>用户冻结不可交易资产总量：{{$val['vc_freeze_untrade']}}</strong>
                            @endif
                        </p>
                        <p>
                            <strong>今日释放总量:{{$val['free_total']}}</strong>
                        </p>
                        <p>
                            <a class="btn btn-success" onclick="open_log('income_log','{{$val['coin_type']}}',this)">收入</a>
                            <a class="btn btn-danger" onclick="open_log('expend_log','{{$val['coin_type']}}',this)">支出</a>
                            <a class="btn btn-primary" onclick="open_log('freeze_log','{{$val['coin_type']}}',this)">冻结</a>
                            <a class="btn btn-warning" onclick="open_log('free_log','{{$val['coin_type']}}',this)">释放</a>

                        </p>
                        <p>
                            <a class="btn btn-default" href="lock_transfer.log">锁仓转账</a>
                            @if($val['coin_type']==0)
                                <a class="btn btn-default" onclick="open_incharge()">兑换锁仓</a>
                                <a class="btn btn-default" href="{{url('incharge.export')}}">兑换锁仓导出</a>
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <div id="chart" style="height:600px;"></div>
            </div>
            <div class="col-md-2">
                <div class="input-group">
                    <select class="form-control" id="coin_type" name="coin_type" style="width:125px;">
                        @foreach($lists as $item)
                            <option value="{{$item['coin_unit']}}">{{$item['name']}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-2">
                <div style="float: right;width: 340px;height: 32px;line-height: 32px;">
                    <div style="float: left;">日期选择：</div>
                    <input type="text" id="chart_date" name="chart_date" value="" class="form-control" style="float: left;width: 180px;" />
                    <a href="javascript:void(0)" onclick="clearForm()" class="btn btn-x btn-default" style="margin-left: 10px;">重置</a>
                </div>
            </div>
        </div>
    </div>
    <script src="{{asset('web/js/public/echarts/echarts.min.js')}}"></script>

    <script>

        function turnIncharge(coin_type) {
            layer.open({
                type: 2,
                content: '{{url('platformlog.mincharge')}}'+'?'+'coin_type='+coin_type,
                area: ['850px', '600px'],
                title:'矿池充值记录'
            });
        }
        function open_incharge() {
            layer.open({
                type: 2,
                content: '{{url('lock_transfer.logs')}}',
                area: ['850px', '600px'],
                title:'兑换锁仓'
            });
        }
        function turnMineLog(coin_type) {
            layer.open({
                type: 2,
                content: '{{url('platformlog.minelog')}}'+'?'+'coin_type='+coin_type,
                area: ['850px', '600px'],
                title:'矿池产出详情'
            });
        }
        function open_log(table,coin_type,t) {
            var title = $(t).html()+'明细'
            layer.open({
                type: 2,
                content: '{{url('platformlog.detail')}}'+'?'+'coin_type='+coin_type+'&table='+table,
                area: ['850px', '600px'],
                title:title
            });
        }

        $('#chart_date').daterangepicker({
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

        var myChart = echarts.init(document.getElementById('chart'));
        function clearForm()
        {
            let data = {
                coin_type : $('#coin_type').val(),
                chart_date : $('#chart_date').val()
            }
            $.get('{{url('platformlog.chart')}}',data,function (res) {
                var data = res.data
                var date =Object.keys(data);
                var type = Object.keys(data[date[0]]);
                var amount = [];
                type.map(function (val) {
                    amount.push({name:val,type:'line',stack:'总量',data:[]})
                })
                amount.map(function (val) {
                    for (let x in data)
                    {
                        val.data.push(data[x][val.name])
                    }
                })
                var option = {
                    title: {
                        text: '用户收入来源'
                    },
                    tooltip: {
                        trigger: 'axis'
                    },
                    legend: {
                        data:type
                    },
                    grid: {
                        left: '3%',
                        right: '4%',
                        bottom: '3%',
                        containLabel: true
                    },
                    toolbox: {
                        feature: {
                            saveAsImage: {}
                        }
                    },
                    xAxis: {
                        type: 'category',
                        boundaryGap: false,
                        data: date
                    },
                    yAxis: {
                        type: 'value'
                    },
                    series: amount
                };
                myChart.setOption(option)
            })
        }
        $(function () {
            clearForm()
        })




    </script>


@stop
