
@extends('manage.layouts.dashboard')

@section('container')
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">板块（共 {{$lists->total()}} 条记录）</h3>
        </div>
        <div class="panel-body">
            <form id="search_form" action="{{url("cms.article")}}" method="GET" class="form-horizontal" role="form" >
                <div class="form-group">
                    <div class="col-md-2">
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal">新 增</button>
                    </div>
                </div>
            </form>
        </div>
        <table class="table table-bordered">
            <thead>
            <tr>
                <th>板块</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            @foreach($lists as $key =>$item)
                <tr>
                    <td>{{$item->name}}</td>
                    <td><a class="btn btn-default" href="{{url('cms.adverst').'/'.$item->id}}">查看广告</a></td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div style="position:relative;top:10px;">{!! $lists->appends(['keyword'=>$keyword,'date'=>$param['date']])->render() !!}</div>

        <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title" id="myModalLabel">编辑</h4>
                    </div>
                    <div class="modal-body">
                        <form id="article_form">
                            <div class="input-group">
                                <span class="input-group-addon">板块</span>
                                <input type="text" class="form-control" name="name" placeholder="请输入板块内容">
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                        <button type="button" class="btn btn-primary" onclick="save()">提交更改</button>
                    </div>
                </div>
            </div>
        </div>
        <script>

            function save() {
                var data = $('#article_form').serializeArray();
                var content = $('#content').summernote('code');
                data.push({name:'content',value:content})
                console.log(data);
                $.ajax({
                    url:'{{url('ad.cate.save')}}',
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
        </script>
@stop
