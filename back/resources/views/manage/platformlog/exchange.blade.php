
@extends('manage.layouts.dashboard')

@section('container')
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">兑换列表（共 {{$lists->total()}} 条记录）</h3>
        </div>
        <div class="panel-body">
            <form id="search_form" action="{{url("platformlog.exchange")}}" method="GET" class="form-horizontal" role="form" >
                <div class="form-group">
                    <div class="col-md-2"  style="display:block; float: left; padding-left:0px;">
                        <div class="input-group">
                            <span class="input-group-addon">用户</span>
                            <input type="text" class="form-control" name="keyword" value="{{$keyword}}" placeholder="输入用户名/手机号">
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
                <th>钱包地址</th>
                <th>金额</th>
                <th>兑换时间</th>
            </tr>
            </thead>
            <tbody>
            @foreach($lists as $key =>$item)
                <tr>
                    <td>{{$item->username}}</td>
                    <td>{{$item->mobile}}</td>
                    <td>{{$item->user_address}}</td>
                    <td>
                        @if(config("app.otc"))
                            总金额：{{$item->vc_amount}}&nbsp;&nbsp;可交易：{{$item->vc_normal}}&nbsp;&nbsp;不可交易：{{$item->vc_untrade}}
                        @else
                            {{$item->vc_amount}}
                        @endif
                    </td>
                    <td>{{$item->create_time}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div style="position:relative;top:10px;">{!! $lists->appends(['keyword'=>$keyword,'exchange_date'=>$param['exchange_date']])->render() !!}</div>

        <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title" id="myModalLabel">审核</h4>
                    </div>
                    <div class="modal-body">

                        <div style="padding: 10px 10px 10px;">
                            <form class="bs-example bs-example-form" role="form">
                                <div class="form-group">
                                    <label for="status">状态</label>
                                    <select class="form-control" id="status">
                                        @foreach($statusName as $key => $item)
                                            <option value="{{$key}}">{{$item}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="input-group">
                                    <span class="input-group-addon">拒绝原因</span>
                                    <input type="text" class="form-control" placeholder="请输入拒绝原因" id="memo">
                                </div>
                                <input type="hidden" value="" id="examine_id">
                            </form>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                        <button type="button" class="btn btn-primary" onclick="submit()">提交更改</button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal -->
        </div>
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

        </script>

@stop
