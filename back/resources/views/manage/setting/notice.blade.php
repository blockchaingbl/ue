
@extends('manage.layouts.dashboard')


@section('container')
    <div class="container">
        <form class="bs-example bs-example-form" role="form" id="setting">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="SYSTEM_NOTICE">系统公告</label>
                        <textarea class="form-control" rows="5" id="SYSTEM_NOTICE" name="SYSTEM_NOTICE">{{$conf['SYSTEM_NOTICE']}}</textarea>
                    </div>
                </div>
                <div class="col-md-8"></div>
            </div>
            <div class="row" style="margin-top: 30px;">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-addon">关于我们</span>
                        <select id="about_open" name="about_open" class="form-control" style="width: 120px;">
                            <option value="1">显示</option>
                            <option value="0">不显示</option>
                        </select>
                    </div>
                    <div id="about"></div>
                </div>
                <div class="col-md-6"></div>
            </div>
            <div class="row" style="margin-top: 30px;">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-addon">联系我们</span>
                        <select id="connect_open" name="connect_open" class="form-control" style="width: 120px;">
                            <option value="1">显示</option>
                            <option value="0">不显示</option>
                        </select>
                    </div>
                    <div id="connect"></div>
                </div>
                <div class="col-md-6"></div>
            </div>
            <div class="row" style="margin-top: 30px;">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-addon">用户中心处公告</span>
                        <select id="NOTICE_TEXT_OPEN" name="NOTICE_TEXT_OPEN" class="form-control" style="width: 120px;">
                            <option value="1">显示</option>
                            <option value="0">不显示</option>
                        </select>
                    </div>
                    <div id="NOTICE_TEXT"></div>
                </div>
                <div class="col-md-6"></div>
            </div>
            <button type="button" class="btn btn-primary" onclick="app.save()">保存</button>
        </form>
    </div>
    <link href="{{asset('summernote/dist/summernote.css')}}" rel="stylesheet">
    <script type="text/javascript" src="{{asset('summernote/dist/summernote.js')}}"></script>
    <script type="text/javascript" src="{{asset('summernote/lang/summernote-zh-CN.js')}}"></script>
    <script>
        window.app ={
            save:function () {
                var about = $('#about').summernote('code');
                var connect = $('#connect').summernote('code');
                var NOTICE_TEXT = $('#NOTICE_TEXT').summernote('code')
                var data = {
                    SYSTEM_NOTICE:$('#SYSTEM_NOTICE').val(),
                    ABOUT : about,
                    CONNECT:connect,
                    ABOUT_OPEN:$('#about_open').val(),
                    CONNECT_OPEN:$('#connect_open').val(),
                    NOTICE_TEXT_OPEN:$('#NOTICE_TEXT_OPEN').val(),
                    NOTICE_TEXT:NOTICE_TEXT
                }
                $.ajax({
                    url:'{{url('save_notice')}}',
                    data:data,
                    type:'POST',
                    dataType:'JSON',
                    success:function (response) {
                        layer.msg(response.message);
                        if(response.errcode==0) {
                            setTimeout(function () {
                                location.reload();
                            },2000)
                        }
                    },error:function (error) {
                        layer.msg(error.message);
                    }
                })
            }
        }

        $(function () {
            $('#about').summernote();
            var about = `{!! $conf['ABOUT'] !!}`;
            var connect =`{!! $conf['CONNECT'] !!}`;
            var NOTICE_TEXT = `{!! $conf['NOTICE_TEXT'] !!}`
            $('#about').summernote('code',about);
            $('#connect').summernote({height: 150});
            $('#connect').summernote('code',connect);
            $('#NOTICE_TEXT').summernote({height: 150});
            $('#NOTICE_TEXT').summernote('code',NOTICE_TEXT);
            $('#connect_open').val('{{$conf['CONNECT_OPEN']}}')
            $('#about_open').val('{{$conf['ABOUT_OPEN']}}')
            $('#NOTICE_TEXT_OPEN').val('{{$conf['NOTICE_TEXT_OPEN']}}')
        })
    </script>


@stop
