
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
                共计流水:{{$total}}
            </div>
        </div>
        <div class="panel-body">
            <form id="search_form" action="{{Request::getRequestUri()}}" method="GET" class="form-horizontal" role="form" >
                <div class="row">
                    <div class="col-md-1"  style="display:none; float: left; padding-left:0px;">
                        <div class="input-group">
                            <select class="form-control" id="coin_type" name="coin_type" style="width:125px;">
                                <option value="0" @if($coin_type==0)selected="selected"@endif>平台币({{db_config("COIN_UNIT")}})</option>
                                @foreach($coin_list as $item)
                                    <option value="{{$item->id}}" @if($coin_type==$item->id)selected="selected"@endif>{{$item->name."(".$item->coin_unit.")"}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2 col-xs-2" style="display:none;">
                        <select v-model="selected" class="form-control" id="table" name="table">
                            <option v-for="yx in YX" :value="yx.value" :selected="yx.value==table ? true :false">
                                @{{yx.text}}
                            </option>
                        </select>
                    </div>
                    <div class="col-md-2 col-xs-2">
                        <select class="form-control" id="type" name="type">
                            <option v-for="(zy,index) in selection" :value="zy.value" :selected="zy.value == type ? true : false">
                                @{{zy.text}}
                            </option>
                        </select>
                    </div>
                    <div class="col-md-4 col-xs-4" style="display:block; float: left; padding-left:0px;">
                        <div class="input-group">
                            <span class="input-group-addon">时间</span>
                            <input type="text" class="form-control" id="date" name="date" placeholder="请选择时间"  value="{{$date}}"/>
                        </div>
                    </div>

                    <div class="col-md-2 col-xs-2" >
                        <button type="button" class="btn btn-primary" onclick="clearSearch()">清 除</button>
                        <button type="submit" class="btn btn-primary">查 询</button>
                    </div>
                </div>
                <div class="row" style="margin-top: 20px;">
                    <div class="col-md-3 col-xs-3">
                        <div class="input-group">
                        <span class="input-group-addon">金额</span>
                        <select class="form-control" id="than" name="than">
                            <option value=">">大于</option>
                            <option value="=">等于</option>
                            <option value="<">小于</option>
                        </select>
                        </div>
                    </div>
                    <div class="col-md-3 col-xs-3">
                        <input type="text" class="form-control" id="vc_amount" name="vc_amount" placeholder="请填写金额"  value="{{$vc_amount}}"/>
                    </div>
                </div>
            </form>
        </div>
        <table class="table table-bordered">
            <thead>
            <tr>
                <th>用户</th>
                <th>时间</th>
                <th>类型</th>
                <th>金额</th>
                <th>详情</th>
                @if($table=='freeze_log')
                    <th>状态</th>
                @endif
            </tr>
            </thead>
            <tbody>
            @foreach($lists as $key =>$item)
                <tr>
                    <td>
                        手机 : {{$item->user->mobile}}
                        <br/>
                        昵称 : {{$item->user->username}}
                    </td>
                    @if($table!='freeze_log')
                    <td>{{$item->create_time}}</td>
                    @else
                        <td>
                            冻结时间:{{$item->freeze_time}}<br/>
                            @if($item->free_time)
                                解冻时间:{{$item->free_time}}
                            @endif
                        </td>
                    @endif
                    <td>{{$item->typeName?$item->typeName:"其他"}} @if($item->typeName=='转账') （对方用户：{{$item->username}}） @endif</td>
                    <td>
                        @if(config("app.otc")&&$coin_type==0)
                            总金额 : {{$item->vc_amount}}
                            <br/>
                            可交易 : {{$item->vc_normal}}
                            <br/>
                            不可交易 : {{$item->vc_untrade}}
                        @else
                            {{$item->vc_amount}}
                        @endif
                    </td>
                    <td>{{$item->detail}}</td>
                    @if($table=='freeze_log')
                        <td>
                            @if($item->status==0)
                                未解冻
                            @else
                                已解冻
                            @endif
                        </td>
                    @endif
                </tr>
            @endforeach
            </tbody>
        </table>
        <div style="position:relative;top:10px;">{!! $lists->appends(['table'=>$table,'type'=>$type,'coin_type'=>$coin_type])->render() !!}</div>
        <script>
        var app  = new Vue({
                el: '#search_form',
                data: {
                    type:'{{$type}}',
                    selected: '{{$table}}',
                    YX: [{
                        text: '收入',
                        value:'income_log',
                        ZY: [{
                            text: '全部',
                            value:'all'
                        }, {
                            text: '挖矿',
                            value: 'mine'
                        }, {
                            text: "购买",
                            value:'purchase'
                        },{
                            text: "划拨",
                            value:'allot'
                        },{
                            text: "充值",
                            value:'incharge'
                        }]
                    }, {
                        text: '支出',
                        value:'expend_log',
                        ZY: [{
                            text: '全部',
                            value:'all'
                        }, {
                            text: '提现',
                            value:"withdraw"
                        }, {
                            text: "销售",
                            value:'sale'
                        },{
                            text: "兑换",
                            value:'exchange'
                        }
                        ]
                    },{
                        text:'冻结',
                        value:'freeze_log',
                        ZY: [{
                            text: '全部',
                            value:'all'
                        }, {
                            text: '挂卖',
                            value:"sale"
                        }, {
                            text: "提现",
                            value:'withdraw'
                        },  {
                            text: "平台",
                            value:'manual'
                        }
                        ]
                    },
                        {
                        text:'释放',
                        value:'free_log',
                        ZY: [{
                            text: '全部',
                            value:'all'
                        }, {
                            text: '锁仓',
                            value:"transfer_lock"
                        }, {
                            text: "糖果",
                            value:'withdraw'
                        }
                        ]
                    }
                    ]
                },
                computed: {
                    selection: {
                        get: function() {
                            var that = this;
                            return this.YX.filter(function(item) {
                                return item.value == that.selected;
                            })[0].ZY;
                        }
                    }
                }
            });

        $(function () {
            $('#coin_type,#table').change(function () {
                $('#type').val('all');
                $('#search_form').submit();
            });
            $('#type').change(function () {
                $('#search_form').submit();
            });
            $('#vc_amount').val('{{$vc_amount}}')
            var than = '{!!$than!!}'
            $('#than').val(than);
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
            }, function(start, end, label) {
                var beginTimeStore = start;
                var endTimeStore = end;
                if(!this.startDate){
                    this.element.val('');
                }else{
                    this.element.val(this.startDate.format(this.locale.format) + this.locale.separator + this.endDate.format(this.locale.format));
                }
            });
        })
            function clearSearch() {
                $('#date').val('')
            }
        </script>

@stop
