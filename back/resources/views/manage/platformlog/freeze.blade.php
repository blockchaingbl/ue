
@extends('manage.layouts.page')
@section('page_content')
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">冻结列表（共 {{$lists->total()}} 条记录）</h3>
        </div>
        <div class="panel-body">
            <form id="search_form" action="{{url("platformlog.freeze")}}" method="GET" class="form-horizontal" role="form" >
                <div class="form-group">
                    <div style="display:block; float: left; padding-left:0px;">
                        <div class="input-group">
                            <select class="form-control" id="coin_type" name="coin_type" style="width:125px;">
                                <option value="0" @if($coin_type==0)selected="selected"@endif>平台币({{db_config("COIN_UNIT")}})</option>
                                @foreach($coin_list as $type)
                                    <option value="{{$type->id}}" @if($coin_type==$type->id)selected="selected"@endif>{{$type->name."(".$type->coin_unit.")"}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2"  style="display:none; float: left; padding-left:10px;">
                        <div class="input-group">
                            <span class="input-group-addon">用户</span>
                            <input type="text" class="form-control" name="keyword" value="{{$keyword}}" placeholder="输入用户名/手机号">
                        </div>
                    </div>
                    <div class="col-md-3" style="display:block; float: left;">
                        <div class="input-group" style="float: left;width: 150px;">
                            <span class="input-group-addon">日期</span>
                            <select class="form-control" id="time_type" name="time_type" style="width:100px;">
                                <option value="1" selected="selected">冻结时间</option>
                                <option value="2">解冻时间</option>
                            </select>
                        </div>
                        <input type="text" id="freeze_date" name="freeze_date" value="{{$param['freeze_date']}}" class="form-control" style="float: left;width: 170px;" />
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
                <th>金额</th>
                <th>冻结时间</th>
                <th>解冻时间</th>
                <th>状态</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            @foreach($lists as $key =>$item)
                <tr>
                    <td>{{$item->username}}</td>
                    <td>{{$item->mobile}}</td>
                    <td>
                        @if(config("app.otc"))
                            总金额：{{$item->vc_amount}}&nbsp;&nbsp;可交易：{{$item->vc_normal}}&nbsp;&nbsp;不可交易：{{$item->vc_untrade}}
                        @else
                            {{$item->vc_amount}}
                        @endif
                    </td>
                    <td>{{$item->freeze_time}}</td>
                    <td>@if($item->status==1){{$item->free_time}}@endif</td>
                    <td style="color:@if($item->status==0) #d9534f @else #5cb85c @endif">@if($item->status==0)冻结中@else已解冻@endif</td>
                    <td style="text-align:center;width:60px;">
                        @if($item->status==0 && $item->type == 'manual')
                            <button type="button" class="btn btn-primary btn-xs" onclick="free({{$item->id}})">解冻</button>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div style="position:relative;top:10px;">{!! $lists->appends(['keyword'=>$keyword,'freeze_date'=>$param['freeze_date'],'time_type'=>$param['time_type'],'type'=>$param['type']])->render() !!}</div>

        <script>
            function clearSearch(){
                $("input[name='keyword']").val('')
                $('#freeze_date').val('')
            }
            function free(id) {
                layer.confirm("确定要解冻吗？",{title:'提示'},function(){
                    $.ajax({
                        url:'{{url('platformlog.free')}}',
                        type:'POST',
                        dataType:'json',
                        data:{id:id},
                        success:function (response) {
                            layer.msg(response.message)
                            if(response.errcode==0){
                                setTimeout(function () {
                                    location.reload();
                                },2000)
                            }
                        }
                    });
                });
            }
            $('#freeze_date').daterangepicker({
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
            $(function () {
                $('#time_type').val('{{$param['time_type']}}');
                $('#coin_type').change(function () {
                    $('#search_form').submit();
                })
            })

        </script>

@stop
