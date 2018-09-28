
@extends('manage.layouts.page')
@section('page_content')
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">产出记录（共 {{$lists->total()}} 条记录）</h3>
        </div>
        <table class="table table-bordered">
            <thead>
            <tr>
                <th>用户名</th>
                <th>手机号</th>
                <th>生成时间</th>
                <th>挖取时间</th>
                <th>是否已挖取</th>
            </tr>
            </thead>
            <tbody>
            @foreach($lists as $key =>$item)
                <tr>
                    <td>{{$item->user->username}}</td>
                    <td>{{$item->user->mobile}}</td>
                    <td>{{date('Y-m-d H:i:s',$item->create_time)}}</td>
                    <td>@if($item->mined_time){{date('Y-m-d H:i:s',$item->mined_time)}}@endif</td>
                    <td>@if($item->mined)是@else否@endif</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div style="position:relative;top:10px;">{!! $lists->appends(['coin_type'=>$coin_type])->render() !!}</div>
        <script>


        </script>

@stop
