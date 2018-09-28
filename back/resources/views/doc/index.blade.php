@extends('doc.layouts.base')
@section('scripts')
@endsection
@section('styles')
    <style>
        .row{ line-height: 40px;}
    </style>
@endsection
@section('content')
    <a href="/">返回接口目录</a><br />
    @foreach($info as $title=>$content)
    <div class="row"> {{$title}}：{!! nl2br($content) !!}</div>
    @endforeach
@endsection