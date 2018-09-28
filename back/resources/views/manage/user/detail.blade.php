
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
                @if(config("app.otc")&&$coin_type==0)
                    账户余额：{{$user->vc_total}}&nbsp;&nbsp;可交易：{{$user->vc_normal}}&nbsp;&nbsp;不可交易：{{$user->vc_untrade}}&nbsp;&nbsp;冻结金额：{{$user->vc_freeze}}
                @else
                    账户余额：{{$user->vc_total}}&nbsp;&nbsp;冻结金额：{{$user->vc_freeze}}
                @endif
            </div>
        </div>
        <div class="panel-body">
            <form id="search_form" action="{{Request::getRequestUri()}}" method="GET" class="form-horizontal" role="form" >
                <div class="col-md-1"  style="display:block; float: left; padding-left:0px;">
                    <div class="input-group">
                        <select class="form-control" id="coin_type" name="coin_type" style="width:125px;">
                            <option value="0" @if($coin_type==0)selected="selected"@endif>平台币({{db_config("COIN_UNIT")}})</option>
                            @foreach($coin_list as $item)
                                <option value="{{$item->id}}" @if($coin_type==$item->id)selected="selected"@endif>{{$item->name."(".$item->coin_unit.")"}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-2 col-xs-2">
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
            </form>
        </div>
        <table class="table table-bordered">
            <thead>
            <tr>
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
                            text: "偷币",
                            value:'steal'
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
                    } ]
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
        })
        </script>

@stop
