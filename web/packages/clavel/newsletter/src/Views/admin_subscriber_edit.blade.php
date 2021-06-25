@extends('admin.layouts.default')

@section('title')
    @parent {{ $page_title }}
@stop

@section('head_page')
    <!-- iCheck -->
    <link rel="stylesheet" href="{{ asset("/assets/admin/vendor/iCheck/square/blue.css") }} ">

@stop

@section('breadcrumb')
    <li><a href="{{ url("admin/events") }}">{{ trans('Newsletter::admin_lang.newsletter-subscribers') }}</a></li>
    <li class="active">{{ $page_title }}</li>
@stop

@section('content')

    @include('admin.includes.errors')
    @include('admin.includes.success')

    <div class="row">
        {!! Form::open($form_data) !!}
        {!! Form::hidden('iduser', $subscriber->id, array('id' => 'iduser')) !!}
        {!! Form::hidden('otros_datos', null, array('id' => 'otros_datos')) !!}
        <div class="col-md-8">

            <div class="box box-primary">
                <div class="box-header  with-border"><h3 class="box-title">{{ trans("Newsletter::admin_lang.subscriptor") }}</h3></div>
                <div class="box-body">

                    <div class="form-group">
                        {!! Form::label('subscriptor_name', trans('Newsletter::admin_lang.subscriber_name'), array('class' => 'col-sm-3 control-label')) !!}
                        <div class="col-md-9">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-user" aria-hidden="true"></i>
                                </span>
                                {!! Form::text('subscriptor_name', $subscriber->userProfile->first_name, array('placeholder' => trans('Newsletter::admin_lang.subscriber_surname'), 'class' => 'form-control', 'id' => 'subscriptor_name')) !!}
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('subscriptor_surname', trans('Newsletter::admin_lang.subscriber_surname'), array('class' => 'col-sm-3 control-label')) !!}
                        <div class="col-md-9">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-user" aria-hidden="true"></i>
                                </span>
                                {!! Form::text('subscriptor_surname', $subscriber->userProfile->last_name, array('placeholder' => trans('Newsletter::admin_lang.subscriptor_surname'), 'class' => 'form-control', 'id' => 'subscriptor_name')) !!}
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('subscriptor_gender', Lang::get('Newsletter::admin_lang.subscriber_genero_sexusal'), array('class' => 'col-md-3 control-label required-input')) !!}
                        <div class="col-md-9">
                            <div class="radio-list">
                                <div class="rdio rdio-primary radio-inline">
                                    <input id="male" name="subscriptor_gender" type="radio" name="radio" value="male" checked="checked" required />
                                    {!! trans('Newsletter::admin_lang.subscriber_hombre') !!}
                                </div>
                                <div class="rdio rdio-primary radio-inline">
                                    <input id="female" name="subscriptor_gender" type="radio" name="radio" value="female" @if ($subscriber->userProfile->gender == 'female') checked="checked" @endif />
                                    {!! trans('Newsletter::admin_lang.subscriber_mujer') !!}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('subscriptor_user_lang', Lang::get('Newsletter::admin_lang.subscriber_idioma'), array('class' => 'col-md-3 control-label required-input')) !!}
                        <div class="col-md-9">

                            <select name="subscriptor_user_lang" class="form-control">
                                @foreach(\App\Models\Idioma::all() as $key=>$value)
                                    <option value="{{ $value->code }}" @if($value->code==$subscriber->userProfile->user_lang) selected @endif>{{ $value->name }}</option>
                                @endforeach
                            </select>

                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('email', Lang::get('Newsletter::admin_lang.subscriber_email'), array('class' => 'col-md-3 control-label')) !!}
                        <div class="col-md-9">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-envelope" aria-hidden="true"></i>
                                </span>
                                {!! Form::text('email', $subscriber->email, array('placeholder' =>  Lang::get('Newsletter::admin_lang.subscriber_email'), 'class' => 'form-control', 'id' => 'subscriptor_email')) !!}
                            </div>
                        </div>
                    </div>

                </div>

            </div>

            <div class="box box-solid">

                <div class="box-footer">

                    <a href="{{ url('/admin/newsletter-subscribers') }}" class="btn btn-default">{{ trans('general/admin_lang.cancelar') }}</a>
                    <button type="button" id="newsletter-subscriber-save" class="btn btn-info pull-right">{{ trans('general/admin_lang.save') }}</button>

                </div>

            </div>

        </div>

        <div class="col-md-4">

            <div class="box box-primary">
                <div class="box-header  with-border"><h3 class="box-title">{{ trans("Newsletter::admin_lang.lists") }}</h3></div>
                <div class="box-body">
                    <div class="form-group">
                        {!! Form::label('lists', Lang::get('Newsletter::admin_lang.lists'), array('class' => 'col-md-4 control-label required-input')) !!}
                        <div class="col-md-8">

                            <select class="form-control select2" name="lists[]" multiple="multiple" style="width: 100%;">
                                @foreach($lists as $value)
                                    <option value="{{ $value->id }}"  @if($subscriber->hasSubscription($value->id)) selected="selected" @endif > {{ $value->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-12">
                            <div class="input-group">
                                {!! Form::checkbox('authorized', null, false, array( 'class' => 'minimal')) !!}
                                {!! Lang::get('Newsletter::admin_lang.subscriber_authorized') !!}
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>


        {!! Form::close() !!}
    </div>

@endsection

@section("foot_page")
    <!-- iCheck -->
    <script src="{{ asset("/assets/admin/vendor/iCheck/js/icheck.min.js") }}"></script>
    <script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js')}}"></script>

    <script>
        $(function () {

            $(".select2").select2();

            $('input').iCheck({
                checkboxClass: 'icheckbox_square-blue',
                radioClass: 'iradio_square-blue',
                increaseArea: '20%' // optional
            });

            $("#newsletter-subscriber-save").click(function(){
                var strInfo = "";

                $(".otros_datos_info").each(function() {
                    strInfo += ($(this).is(":checked")) ? "1" : "0";
                });
                $("#otros_datos").val(strInfo);

                $("#formData").submit();
            });
        });
    </script>

    {!! JsValidator::formRequest('App\Modules\Newsletter\Requests\AdminNewsletterSubscriberRequest')->selector('#formData') !!}
@stop
