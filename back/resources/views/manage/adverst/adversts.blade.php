
@extends('manage.layouts.dashboard')

@section('container')
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">{{$cate->name}}板块 广告（共 {{$lists->total()}} 条记录）</h3>
        </div>
        <div class="panel-body">
            <form id="search_form" action="{{Request::getRequestUri()}}" method="GET" class="form-horizontal" role="form" >
                <div class="form-group">
                    <div class="col-md-1">
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal">新 增</button>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group">
                            <span class="input-group-addon">名称</span>
                            <input type="text" class="form-control" name="keyword" value="{{$param['keyword']}}" placeholder="请输入广告名">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group">
                            <span class="input-group-addon">状态</span>
                            <select class="form-control" name="open" id="search_open">
                                <option value="">所有</option>
                                <option value="1">开启</option>
                                <option value="0">关闭</option>
                            </select>
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
                <th>广告名</th>
                <th>广告链接</th>
                <th>图片</th>
                <th>是否开启</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            @foreach($lists as $key =>$item)
                <tr>
                    <td>{{$item->name}}</td>
                    <td>{{$item->url}}</td>
                    <td><img src="{{$item->image}}" alt="" style="width: 90px; height: 90px;"></td>
                    <td>@if($item->open) 已开启 @else 未开启 @endif</td>
                    <td>
                        <button type="button" class="btn btn-success" data-dismiss="modal" onclick="edit('{{json_encode($item)}}')">编辑</button>
                        <button type="button" class="btn btn-danger"  onclick="delete_art('{{$item->id}}')">删除</button>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div style="position:relative;top:10px;">{!! $lists->appends(['keyword'=>$param['keyword'],'open'=>$param['open'],''])->render() !!}</div>

        <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title" id="myModalLabel">编辑</h4>
                    </div>
                    <div class="modal-body">
                        <form id="ad_form">
                            <div class="input-group">
                                <span class="input-group-addon">名称</span>
                                <input type="text" class="form-control" name="name" id="name" placeholder="请输入广告名称">
                            </div>
                            <div class="input-group" style="margin-top: 20px;">
                                <span class="input-group-addon">网址</span>
                                <input type="text" class="form-control" name="url" id="url" placeholder="请输入网址">
                            </div>
                            <div class="form-group" style="margin-top: 20px;">
                                <label for="adverst_image">广告图</label>
                                <div class="row">
                                    <div class="col-md-2">
                                        <div style="background:#dfdfdf;width:90px;height:90px;line-height:90px;text-align:center;position:relative;">
                                            <img id="show_image" style="max-width:90%;max-height:90%;">
                                            <input type="hidden" id="image" name="image">
                                        </div>
                                    </div>
                                    <div class="col-md-10">
                                        <button type="button" class="btn btn-primary btn-xs" id="adverst_image">选择</button>
                                    </div>
                                </div>
                            </div>
                            <div class="input-group" style="margin-top: 20px;">
                                <span class="input-group-addon">是否开启</span>
                                <select id="open" name="open" class="form-control" style="width: 120px;">
                                    <option value="1">开启</option>
                                    <option value="0">关闭</option>
                                </select>
                            </div>
                            {{--<div class="input-group" style="margin-top: 20px;">--}}
                                {{--<span class="input-group-addon">是否以app打开链接</span>--}}
                                {{--<select id="app_open" name="app_open" class="form-control" style="width: 120px;">--}}
                                    {{--<option value="1">是</option>--}}
                                    {{--<option value="0">否</option>--}}
                                {{--</select>--}}
                            {{--</div>--}}
                            <input type="hidden" name="id" id="id" value="">
                            <input type="hidden" name="cate_id" value="{{$cate->id}}">
                        </form>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal" onclick="do_close()">关闭</button>
                        <button type="button" class="btn btn-primary" onclick="save()">提交更改</button>
                    </div>
                </div>
            </div>
        </div>
        <script>
            $.fanwe_upload("adverst_image",'{{url("upload_adverst_image")}}',{},"jpg,png,gif,jpeg",1,false,function(){
                //上传中
            },function(up,file,res){
                var data = JSON.parse(res.response);
                var timestamp = Date.parse(new Date());
                $("#show_image").attr("src",data['data']['src']+"?x-oss-process=image/resize,m_fill,h_90,w_90&v="+timestamp);
                $("#image").val(data['data']['src']);
            },function(){
                //上传完成
            },function(up, err){

            });
            function clearSearch() {
                $('input[name="keyword"]').val('')
            }
            $('#search_open').val('{{$param['open']}}')
            function save() {
                var data = $('#ad_form').serializeArray();
                $.ajax({
                    url:'{{url('adverst.save')}}',
                    data:data,
                    type:'POST',
                    dataType:'JSON',
                    success:function (response) {
                        if(response.errcode==0) {
                            layer.msg('保存成功');
                            location.reload();
                        }else{
                            layer.msg(response.message);
                        }
                    }
                })
            }

            function edit(config) {
                var conf = JSON.parse(config)
                $('#id').val(conf.id)
                $('#open').val(conf.open)
                $('#image').val(conf.image)
                $('#show_image').attr('src',conf.image)
                $('#name').val(conf.name)
                $('#app_open').val(conf.app_open)
                $('#url').val(conf.url)
                $('#myModal').modal('show')
            }
            function do_close() {
                $('#id').val('');
                $('#show_image').attr('src','');
                $('#image').val(0);
                $('#open').val(1)
                $('#name').val('')
                $('#app_open').val(1)
                $('#url').val('')
            }
            function delete_art(id) {
                layer.confirm('是否确认删除', function(index){
                    $.ajax({
                        url:'{{url('adverst.delete')}}',
                        data:{id:id},
                        type:'POST',
                        dataType:'JSON',
                        success:function (response) {
                            if(response.errcode==0) {
                                layer.msg('删除成功');
                                setTimeout(function () {
                                    location.reload()
                                },2000)
                            }else{
                                layer.msg(response.message);
                            }
                        }
                    })

                    layer.close(index);
                });

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
