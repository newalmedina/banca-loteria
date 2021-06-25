@extends('admin.layouts.default')

@section('title')
    @parent {{ $page_title }}
@stop

@section('head_page')

@stop

@section('breadcrumb')
    <li class="active">{{ $page_title }}</li>
@stop

@section('content')
    @include('admin.includes.modals')
    @include('admin.includes.errors')
    @include('admin.includes.success')



@endsection

@section("foot_page")

@stop