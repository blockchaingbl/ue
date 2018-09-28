
@extends('manage.layouts.dashboard')

@section('container')
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">反馈（共 {{$lists->total()}} 条记录）</h3>
        </div>
        <div class="panel-body">
            <form id="search_form" action="{{url("setting.connect")}}" method="GET" class="form-horizontal" role="form" >
                <div class="form-group">
                    <div class="col-md-2">
                        <div class="input-group">
                            <span class="input-group-addon">时间</span>
                            <input type="text" class="form-control" id="date" name="date" value="{{$param['date']}}" placeholder="选择反馈时间" />
                        </div>
                    </div>
                    <div class="col-md-4" style="display:block; float: left;">
                        <button type="button" class="btn btn-primary" onclick="clearSearch()">清 除</button>
                        <button type="submit" class="btn btn-primary">查 询</button>
                    </div>
                </div>
            </form>
        </div>
        <table class="table table-bordered">
            <thead>
            <tr>
                <th style="width: 300px;">反馈内容</th>
                <th>图片</th>
                <th>用户昵称</th>
                <th>用户手机</th>
                <th>反馈时间</th>
                <th style="width: 300px;">回复</th>
                <td>操作</td>
            </tr>
            </thead>
            <tbody>
            @foreach($lists as $key =>$item)
                <tr>
                    <td>{{$item->content}}</td>
                    <td>@if($item->image)<img src="{{$item->image}}" alt="" style="width: 90px; height: 90px;" onclick="layer.open({ type: 2,content:'{{img($item->image,400,400)}}',area:['600px', '600px'],title:'大图'})">@endif</td>
                    <td>{{$item->user->username}}</td>
                    <td>{{$item->user->mobile}}</td>
                    <td>{{$item->create_time}}</td>
                    <td>{{$item->huifu}}</td>
                    <td>
                        <button type="submit" class="btn btn-success"  data-target="#myModal" data-toggle="modal" onclick="change('{{$item->id}}','{{$item->huifu}}')">回复</button>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div style="position:relative;top:10px;">{!! $lists->appends(['date'=>$param['date']])->render() !!}</div>
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
                                    <div class="row">
                                        <div class="col-xs-12">
                                            <span>回复内容</span>
                                        </div>
                                        <div class="col-xs-12">
                                            <textarea placeholder="请输入回复内容" id="huifu" class="form-control"></textarea>
                                        </div>
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
            function change(id,huifu) {
                $('#huifu').val(huifu)
                $('#id').val(id);
            }
            function clearSearch() {
                $('input[name="date"]').val('')
            }
            function save_change() {
                var data ={
                    id:$('#id').val(),
                    huifu:$('#huifu').val()
                }
                $.ajax({
                    url:'{{'setting.huifu'}}',
                    data:data,
                    type:'post',
                    dataType:'JSON',
                    success:function (res) {
                        layer.msg(res.message);
                        if(res.errcode==0)
                        {
                            setTimeout(function () {
                                location.reload();
                            },2000)
                        }
                    }
                })
            }
        </script>
@stop
