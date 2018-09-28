@extends('manage.layouts.dashboard')

@section('container')
    <div class="panel panel-default">

        <div class="row" style="padding:20px;" id="sync_block">
            <div class="col-md-3 tjspan" style="height:30%">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">今日统计</h3>
                    </div>
                    <div class="panel-body" style="padding:10px 10px 0;">
                        <div style="width:100%;padding:10px;">
                            <p><strong>今日销售量：{{$data['num_today']}}</strong></p>
                            <p><strong></strong></p>
                            <p><strong>今日销售金额：{{$data['amount_today']}}</strong></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 tjspan" style="height:30%">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">总计</h3>
                    </div>
                    <div class="panel-body" style="padding:10px 10px 0;">
                        <div style="width:100%;padding:10px;">
                            <p><strong>销售量：{{$data['num_all']}}</strong></p>
                            <p><strong></strong></p>
                            <p><strong>销售金额：{{$data['amount_all']}}</strong></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>



        <div class="panel-heading">
            <h3 class="panel-title">产品列表（共 {{$list->total()}} 条记录）</h3>
        </div>
        <div class="panel-body">
            <form id="search_form" action="{{url("miner.salelist")}}" method="GET" class="form-horizontal" role="form" >
                <div class="form-group">
                    <div class="col-md-2"  style="display:block; float: left; padding-left:10px;">
                        <div class="input-group">
                            <span class="input-group-addon">用户</span>
                            <input type="text" class="form-control" name="username" value="{{$param['username']}}" placeholder="输入用户名/手机号">
                        </div>
                    </div>
                    <div class="col-md-2" style="display:block;float:left;">
                        <div class="input-group">
                            <span class="input-group-addon">状态</span>
                            <select class="form-control" name="status">
                                <option value="-1">所有</option>
                                <option value="1" @if($param["status"]==1)selected="selected"@endif>正常</option>
                                <option value="2" @if($param["status"]==2)selected="selected"@endif>过期</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3" style="display:block; float: left;">
                        <div class="input-group" style="float: left;width: 150px;">
                            <span class="input-group-addon">购买时间</span>
                            <input type="text" id="date" name="date" value="{{$param['date']}}" class="form-control" style="float: left;width: 210px;" />
                        </div>

                    </div>
                    <div class="col-md-2"  style="display:block;float:left;">
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
                <th>矿机名</th>
                <th>价格</th>
                <th>状态</th>
                <th>购买时间</th>
                <th>过期时间</th>
            </tr>
            </thead>
            <tbody>
            @foreach($list as $k =>$v)
                <tr>
                    <td>{{$v->username}}</td>
                    <td>{{$v->name}}</td>
                    <td>{{$v->price}}</td>
                    <td>@if($v->status == 1)正常@else过期@endif</td>
                    <td>{{$v->create_time}}</td>
                    <td>{{$v->expire_time}}</td>

                </tr>
            @endforeach
            </tbody>
        </table>
        <div style="position:relative;top:10px;">{!! $list->appends($param)->render() !!}</div>

    </div>

    <script>
        function clearSearch(){
            $("#search_form")[0].reset();
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

    </script>
@stop
