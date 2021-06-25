@extends('admin.layouts.default')

@section('title')
    @parent {{ $page_title }}
@stop

@section('head_page')
@stop

@section('breadcrumb')
    <li><a href="{{ url("admin/newsletter-lists") }}">{{ trans('Newsletter::admin_lang_lists.newsletter-lists') }}</a></li>
    <li class="active">{{ $page_title }}</li>
@stop

@section('content')

    @include('admin.includes.errors')
    @include('admin.includes.success')

    <div class="row">
        {!! Form::model($list, $form_data, array('role' => 'form')) !!}

        <div class="col-md-12">

            <div class="box box-primary">
                <div class="box-header  with-border"><h3 class="box-title">{{ trans("Newsletter::admin_lang_lists.info_menu") }}</h3></div>
                <div class="box-body">
                    <div class="form-group">
                        {!! Form::label('slug', trans('Newsletter::admin_lang_lists.slug'), array('class' => 'col-sm-2 control-label', 'readonly' => true)) !!}
                        <div class="col-md-10">
                            {!! Form::text('slug', null, array('placeholder' => trans('Newsletter::admin_lang_lists.slug'), 'class' => 'form-control', 'id' => 'slug', 'readonly' => true)) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('name', trans('Newsletter::admin_lang_lists.name'), array('class' => 'col-sm-2 control-label required-input')) !!}
                        <div class="col-md-10">
                            {!! Form::text('name', null, array('placeholder' => trans('Newsletter::admin_lang_lists.name'), 'class' => 'form-control', 'id' => 'name')) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('requires_opt_in', trans('Newsletter::admin_lang_lists.requires_opt_in'), array('class' => 'col-sm-2 control-label', 'readonly' => true)) !!}
                        <div class="col-md-10">
                            <div class="radio-list">
                                <label class="radio-inline">
                                    {!! Form::radio('requires_opt_in', 0, true, array('id'=>'requires_opt_in_0')) !!}
                                    {{ Lang::get('general/admin_lang.no') }}</label>
                                <label class="radio-inline">
                                    {!! Form::radio('requires_opt_in', 1, false, array('id'=>'requires_opt_in_1')) !!}
                                    {{ Lang::get('general/admin_lang.yes') }} </label>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="box box-solid">

                <div class="box-footer">

                    <a href="{{ url('/admin/newsletter-lists') }}" class="btn btn-default">{{ trans('general/admin_lang.cancelar') }}</a>
                    <button type="submit" class="btn btn-info pull-right">{{ trans('general/admin_lang.save') }}</button>

                </div>

            </div>

        </div>

        {!! Form::close() !!}
    </div>

@endsection

@section("foot_page")
    <script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js')}}"></script>

    {!! JsValidator::formRequest('App\Modules\Newsletter\Requests\AdminNewsletterListsRequest')->selector('#formData') !!}

@stop
