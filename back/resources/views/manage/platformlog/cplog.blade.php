
@extends('manage.layouts.dashboard')

@section('container')
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">算力列表（共 {{$lists->total()}} 条记录）</h3>
        </div>
        <div class="panel-body">
            <form id="search_form" action="{{url("platformlog.cplog")}}" method="GET" class="form-horizontal" role="form" >
                <div class="form-group">
                    <div class="col-md-2"  style="display:block; float: left; padding-left:0px;">
                        <div class="input-group">
                            <span class="input-group-addon">用户</span>
                            <input type="text" class="form-control" name="keyword" value="{{$keyword}}" placeholder="输入用户名/手机号">
                        </div>
                    </div>
                    <div class="col-md-1"  style="display:block; float: left; padding-left:0px;">
                        <div class="input-group">
                            <select name="api" id="api" class="form-control" onchange="toSearch()">
                                <option value="0">全部</option>
                                @foreach($api as $key=>$val)
                                    <option value="{{$key}}">{{$val}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2" style="display:block; float: left;">
                        <div class="input-group">
                            <span class="input-group-addon">时间</span>
                            <input type="text" id="cp_log_date" name="cp_log_date" value="{{$param['cp_log_date']}}" class="form-control" style="float: left;width:170px;" />
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
                <th>来源</th>
                <th>增加时间</th>
                <th>增加算力</th>
                <th>总算力</th>
            </tr>
            </thead>
            <tbody>
            @foreach($lists as $key =>$item)
                <tr>
                    <td>{{$item->username}}</td>
                    <td>{{$item->mobile}}</td>
                    <td>{{$api[$item->api]}}</td>
                    <td>{{$item->create_time}}</td>
                    <td>{{$item->cp_amount}}</td>
                    <td>{{$item->cp_total}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div style="position:relative;top:10px;">{!! $lists->appends(['keyword'=>$keyword,'api'=>$param['api'],'cp_log_date'=>$param['cp_log_date'],'status'=>$param['status']])->render() !!}</div>
        <script>
            function clearSearch(){
                $("input[name='keyword']").val('')
                $('#cp_log_date').val('')
            }
            $('#cp_log_date').daterangepicker({
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
            function appeal(id) {
                layer.confirm('是否确认添加算力', {icon: 3, title:'提示'}, function(index){
                    $.ajax({
                        url:'{{'platformlog.calc.add'}}',
                        data:{id_arr:[id]},
                        type:'post',
                        success:function (res) {
                            layer.close(index);
                            layer.msg(res.message);
                            setTimeout(function () {
                                location.reload();
                            },2000)
                        }
                    })

                });
            }

        $(function () {
            $('#status').val('{{$param['status']}}');
            $('#status').change(function () {
                $('#search_form').submit();
            });
            $('#check_all').change(function () {
                var flag = $('#check_all').prop('checked');
                if(flag){
                    $('.id_arr').not(':disabled').prop('checked',true)
                }else{
                    $('.id_arr').prop('checked',false)
                }
            })
            $('#api').val('{{$param['api']}}');
        })
        function appealMulti() {
            layer.confirm('是否确认批量增加', {icon: 3, title:'提示'}, function(index){
                var id_arr= [];
                $("input[name='id_arr']:checkbox:checked").each(function(){
                    id_arr.push($(this).val())
                })
                if(id_arr.length<=0){
                    layer.msg('请选择要增加的算力');
                    return false;
                }
                $.ajax({
                    url:'{{'platformlog.calc.add'}}',
                    data:{id_arr:id_arr},
                    type:'post',
                    success:function (res) {
                        layer.close(index);
                        layer.msg(res.message);
                        if(res.errcode==0){
                            setTimeout(function () {
                                location.reload();
                            },2000)
                        }

                    }
                })
        })}


        </script>

@stop
