@extends('admin.layouts.default')

@section('title')
    @parent {{ $page_title }}
@stop

@section('head_page')
@stop

@section('breadcrumb')
    <li><a href="{{ url("admin/posts") }}">{{ trans('posts::admin_lang.tags_list') }}</a></li>
    <li class="active">{{ $page_title }}</li>
@stop

@section('content')

    @include('admin.includes.errors')
    @include('admin.includes.success')

    <div class="row">
        {!! Form::model($tag, $form_data, array('role' => 'form')) !!}

        <div class="col-md-12">

            <div class="box box-primary">
                <div class="box-header  with-border"><h3 class="box-title">{{ trans("posts::admin_lang.info_menu") }}</h3></div>
                <div class="box-body">

                    <div class="form-group">
                        {!! Form::label('active', trans('posts::admin_lang.status'), array('class' => 'col-sm-2 control-label', 'readonly' => true)) !!}
                        <div class="col-md-10">
                            <div class="radio-list">
                                <label class="radio-inline">
                                    {!! Form::radio('active', 0, true, array('id'=>'active_0')) !!}
                                    {{ Lang::get('general/admin_lang.no') }}</label>
                                <label class="radio-inline">
                                    {!! Form::radio('active', 1, false, array('id'=>'active_1')) !!}
                                    {{ Lang::get('general/admin_lang.yes') }} </label>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">

                    <?php
                    $nX = 1;
                    ?>
                    @foreach ($a_trans as $key => $valor)
                        <li @if($nX==1) class="active" @endif>
                            <a href="#tab_{{ $key }}" data-toggle="tab">
                                {{ $valor["idioma"] }}
                                @if($nX==1)- <span class="text-success">{{ trans('posts::admin_lang._defecto') }}</span>@endif
                            </a>
                        </li>
                        <?php
                        $nX++;
                        ?>
                    @endforeach

                </ul><!-- /.box-header -->

                <div class="tab-content">
                    <?php
                    $nX = 1;
                    ?>
                    @foreach ($a_trans as $key => $valor)
                        <div id="tab_{{ $key }}" class="tab-pane @if($nX==1) active @endif">
                            {!!  Form::hidden('userlang['.$key.'][id]', $valor["id"], array('id' => 'id')) !!}
                            {!!  Form::hidden('userlang['.$key.'][post_tag_id]', $tag->id, array('id' => 'post_tag_id')) !!}

                            <div class="form-group">
                                {!! Form::label('userlang['.$key.'][tag]', trans('posts::admin_lang.titulo'), array('class' => 'col-sm-2 control-label')) !!}
                                <div class="col-sm-10">
                                    {!! Form::text('userlang['.$key.'][tag]', $tag->{'tag:'.$key} , array('placeholder' => trans('posts::admin_lang.titulo'), 'class' => 'form-control', 'id' => 'tag_'.$key)) !!}
                                </div>
                            </div>
                        </div>
                        <?php
                        $nX++;
                        ?>
                    @endforeach
                </div>
            </div>

            <div class="box box-solid">

                <div class="box-footer">

                    <a href="{{ url('/admin/posts/tags') }}" class="btn btn-default">{{ trans('general/admin_lang.cancelar') }}</a>
                    <button type="submit" class="btn btn-info pull-right">{{ trans('general/admin_lang.save') }}</button>

                </div>

            </div>

        </div>

        {!! Form::close() !!}
    </div>

@endsection

@section("foot_page")
    <script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js')}}"></script>

    {!! JsValidator::formRequest('Clavel\Posts\Requests\AdminPostTagRequest')->selector('#formData') !!}

@stop
