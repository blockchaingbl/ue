
@extends('manage.layouts.dashboard')

@section('container')
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">汇率列表（共 {{$lists->total()}} 条记录）</h3>
        </div>
        <div class="panel-body">
            <form id="search_form" action="{{url("exrate.index")}}" method="GET" class="form-horizontal" role="form">
                <div class="form-group">
                    <div class="col-md-1"  style="display:block; float: left; padding-left:0px;">
                        <div class="input-group">
                            <select class="form-control" name="open" id="open">
                                <option value="all">全部</option>
                                <option value="1">开启</option>
                                <option value="0">关闭</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2"  style="display:block; float: left; padding-left:0px;">
                        <div class="input-group">
                            <span class="input-group-addon">币种</span>
                            <input type="text" class="form-control" name="keyword" value="{{$keyword}}" placeholder="输入币种">
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
                <th>币种</th>
                <th>英文符号</th>
                <th>当前汇率</th>
                <th>更新时间</th>
                <th>是否启用</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            @foreach($lists as $key =>$item)
                <tr>
                    <td>{{$item->name}}</td>
                    <td>{{$item->symbol}}</td>
                    <td>{{$item->rate}}</td>
                    <td>{{$item->update_time}}</td>
                    <th>@if($item->open)是@else否@endif</th>
                    <td>
                        <button class="btn btn-success"  data-target="#myModal" data-toggle="modal" onclick="change('{{json_encode($item)}}')">编辑</button>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div style="position:relative;top:10px;">{!! $lists->appends(['keyword'=>$keyword,'open'=>$open])->render() !!}</div>
        <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title" id="myModalLabel">修改</h4>
                    </div>
                    <div class="modal-body">
                        <div style="padding: 10px 10px 10px;">
                            <form class="bs-example bs-example-form" role="form">
                                <div class="form-group">
                                    <div class="form-group">
                                        <div class="row" style="margin-top: 20px;">
                                            <div class="col-xs-6">
                                                <div class="input-group">
                                                    <span class="input-group-addon">是否启用</span>
                                                    <select id="status" class="form-control">
                                                        <option value="1">启用</option>
                                                        <option value="0">禁用</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-xs-6"></div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xs-12">
                                            <span>会员端显示排序</span>
                                            <input placeholder="越小的显示在越前" id="sort" type="number" class="form-control" style="width: 150px; display: inline-block">
                                        </div>
                                        <div class="col-xs-6"></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xs-12">
                                            <span>币种名称</span>
                                            <input placeholder="币种名称" id="name" type="text" class="form-control" style="width: 150px; display: inline-block">
                                        </div>
                                        <div class="col-xs-6"></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xs-12">
                                            <span>汇率</span>
                                            <input placeholder="汇率" id="rate" type="number" class="form-control" style="width: 150px; display: inline-block">
                                        </div>
                                        <div class="col-xs-6"></div>
                                    </div>
                                </div>
                                <input type="hidden" value="" id="id">
                            </form>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                        <button type="button" class="btn btn-primary" onclick="save_change()">提交更改</button>
                    </div>
                </div>
            </div>
        </div>
        <script>
        function clearSearch(){
            $("input[name='keyword']").val('')
        }

        function change(item) {
            item = JSON.parse(item);
            $('#status').val(item.open);
            $('#sort').val(item.sort);
            $('#name').val(item.name);
            $('#id').val(item.id);
            $('#rate').val(item.rate)
        }
        function save_change() {
            var data ={
                id:$('#id').val(),
                sort:$('#sort').val(),
                open:$('#status').val(),
                name:$('#name').val(),
                rate:$('#rate').val()
            }
            $.ajax({
                url:'{{'exrate.change'}}',
                data:data,
                type:'post',
                dataType:'JSON',
                success:function (res) {
                    layer.msg(res.message);
                    setTimeout(function () {
                        location.reload();
                    },2000)
                }
            })
        }

        $(function () {
           $('#open').val("{{$open}}")
        })


        </script>

@stop
