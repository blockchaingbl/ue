@extends('doc.layouts.base')
@section('scripts')
@endsection
@section('styles')
    <style>
        .row{ line-height: 40px;}
    </style>
@endsection
@section('content')
    以下是接口清单：
    <br />
    接口统一返回标准  {data:数据集,message:返回的文字消息,errcode:错误代码}，文档中返回只描述data数据集的结构
    <br />
    @foreach($apis as $title=>$content)
    <div class="row"> {!! $content !!}</div>
    @endforeach
@endsection