
@extends('manage.layouts.dashboard')

@section('container')
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">文章（共 {{$lists->total()}} 条记录）</h3>
        </div>
        <div class="panel-body">
            <form id="search_form" action="{{url("cms.article")}}" method="GET" class="form-horizontal" role="form" >
                <div class="form-group">
                    <div class="col-md-1">
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal">新 增</button>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group">
                            <span class="input-group-addon">标题</span>
                            <input type="text" class="form-control" name="keyword" value="{{$param['keyword']}}" placeholder="请输入标题">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group">
                            <span class="input-group-addon">时间</span>
                            <input type="text" class="form-control" id="date" name="date" value="{{$param['date']}}" placeholder="选择创建时间" />
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group">
                            <span class="input-group-addon">状态</span>
                            <select class="form-control" name="publish_status" id="publish_status">
                                <option value="">所有</option>
                                <option value="0">待发布</option>
                                <option value="1">已发布</option>
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
                <th>标题</th>
                <th>文章链接</th>
                <th>创建时间</th>
                <th>图片</th>
                <th>是否发布</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            @foreach($lists as $key =>$item)
                <tr>
                    <td>{{$item->title}}</td>
                    <td>{{'http://www.'.config('app.route_domain').'/article/'.$item->id}}</td>
                    <td>{{$item->create_time}}</td>
                    <td><img src="{{$item->image}}" alt="" style="width: 90px; height: 90px;"></td>
                    <td>@if($item->publish) 已发布 @else 待发布 @endif</td>
                    <td>
                        <button type="button" class="btn btn-success" data-dismiss="modal" onclick="edit('{{$item->id}}')">编辑</button>
                        <button type="button" class="btn btn-danger"  onclick="delete_art('{{$item->id}}')">删除</button>
                    </td>
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
                                <span class="input-group-addon">标题</span>
                                <input type="text" class="form-control" name="title" id="title" placeholder="请输入标题">
                            </div>
                            <div class="input-group" style="margin-top: 20px;">
                                <span class="input-group-addon">是否发布</span>
                                <select id="publish" name="publish" class="form-control" style="width: 120px;">
                                    <option value="0">待发布</option>
                                    <option value="1">已发布</option>
                                </select>
                            </div>
                            <div class="radio" style="margin-top: 20px;">
                                <span>阅读是否增加算力</span>
                                <label>
                                    <input type="radio" name="cp_return" value="1"> 是
                                </label>
                                <label>
                                    <input type="radio" name="cp_return" value="0"> 否
                                </label>
                            </div>
                            <div class="input-group" style="margin-top: 20px;" id="cp_r">
                                <span class="input-group-addon">阅读得到的算力</span>
                                <input type="number" class="form-control" name="cp_return_num"  placeholder="请输入阅读得到的算力">
                            </div>
                            <div class="input-group" style="margin-top: 20px;" >
                                <span class="input-group-addon">排序</span>
                                <input type="number" class="form-control" name="sort" id="sort"  placeholder="请输入排序">
                            </div>
                            <div class="form-group" style="margin-top: 20px;">
                                <label for="article_image">缩略图</label>
                                <div class="row">
                                    <div class="col-md-2">
                                        <div style="background:#dfdfdf;width:90px;height:90px;line-height:90px;text-align:center;position:relative;">
                                            <img id="show_image" style="max-width:90%;max-height:90%;">
                                            <input type="hidden" id="image" name="image">
                                        </div>
                                    </div>
                                    <div class="col-md-10">
                                        <button type="button" class="btn btn-primary btn-xs" id="article_image">选择</button>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>文章内容</label>
                                <div id="content"></div>
                            </div>
                            <input type="hidden" name="id" id="id">
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                        <button type="button" class="btn btn-primary" onclick="save()">提交更改</button>
                    </div>
                </div>
            </div>
        </div>
        <link href="{{asset('summernote/dist/summernote.css')}}" rel="stylesheet">
        <script type="text/javascript" src="{{asset('summernote/dist/summernote.js')}}"></script>
        <script type="text/javascript" src="{{asset('summernote/lang/summernote-zh-CN.js')}}"></script>
        <script>
            $.fanwe_upload("article_image",'{{url("upload_article_image")}}',{},"jpg,png,gif,jpeg",1,false,function(){
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
            $('#publish_status').val('{{$param['publish_status']}}');
            $(document).ready(function() {
                $('#content').summernote({
                    toolbar: [
                        ['style', ['bold', 'italic', 'underline', 'clear']],
                        ['font', ['strikethrough', 'superscript', 'subscript']],
                        ['fontsize', ['fontsize']],
                        ['color', ['color']],
                        ['para', ['ul', 'ol', 'paragraph']],
                        ['height', ['height']]
                    ]
                });
            });
            function clearSearch() {
                $('input[name="keyword"]').val('')
                $('input[name="date"]').val('')
            }
            function save() {
                var data = $('#article_form').serializeArray();
                var content = $('#content').summernote('code');
                data.push({name:'content',value:content})
                $.ajax({
                    url:'{{url('article.save')}}',
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
            $(function () {
                $('#myModal').on('hide.bs.modal', function () {
                    $('#title').val('')
                    $('#publish').val(0);
                    $('#show_image').attr('src','');
                    $('#image').val(0);
                    $('#content').summernote('code','')
                    $('#sort').val('')
                    $('input[name="cp_return"][value="0"]').prop("checked", true);
                    $('input[name="cp_return_num"]').val(0);
                })
                $('input[name="cp_return"]').change(function () {
                    var val = $(this).val()
                    if(val==1){
                        $('#cp_r').show()
                    }else{
                        $('#cp_r').hide()
                    }
                })
            })

            function edit(id) {
               $.ajax({
                   url:'{{'article.detail'}}',
                   data:{id:id},
                   type:'POST',
                   dataType:'JSON',
                   success:function (response) {
                       if(response.errcode==0) {
                           var article = response.article;
                           $('#myModal').modal('show');
                           $('#image').val(article.image);
                           $('#show_image').attr('src',article.image);
                           $('#title').val(article.title);
                           $('#publish').val(article.publish);
                           $('#id').val(article.id)
                           $('#sort').val(article.sort)
                           $('input[name="cp_return"][value="'+article.cp_return+'"]').prop("checked", true);
                           if(article.cp_return==1){
                               $('#cp_r').show()
                           }else{
                               $('#cp_r').hide()
                           }
                           $('input[name="cp_return_num"]').val(article.cp_return_num);
                           $('#content').summernote('code',article.content);
                       }else{
                       }
                   }
               })
            }
            function delete_art(id) {
                layer.confirm('是否确认删除', function(index){
                    $.ajax({
                        url:'{{'article.delete'}}',
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
