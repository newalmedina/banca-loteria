@extends('admin.layouts.default')

@section('title')
    @parent {{ $page_title }}
@stop

@section('head_page')
    <!-- iCheck -->
    <link rel="stylesheet" type="text/css" href="{{ asset("/assets/admin/vendor/iCheck/square/blue.css") }} ">
    <link rel="stylesheet" type="text/css" href="{{ asset("assets/admin/vendor/datepicker/css/bootstrap-datepicker.min.css") }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset("/assets/admin/vendor/timepicker/bootstrap-timepicker.css") }}">

    <style>
        .deleted-attachment { text-decoration: line-through; font-style: italic; color: #999; }
    </style>
@stop

@section('breadcrumb')
    <li><a href="{{ url("admin/newsletter-campaings") }}">{{ trans('Newsletter::admin_lang_campaigns.newsletter-campaigns') }}</a></li>
    <li class="active">{{ $page_title }}</li>
@stop

@section('content')

    @include('admin.includes.errors')
    @include('admin.includes.success')
    @include('admin.includes.modals')

    {!! Form::model($campaign, $form_data, array('role' => 'form')) !!}

    <div class="row">
        <div class="col-xs-12">

            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#campaign" data-toggle="tab">{{ trans('Newsletter::admin_lang_campaigns.info_menu') }}</a></li>
                    <li><a href="#campaign-scheduling" data-toggle="tab">{{ trans('Newsletter::admin_lang_campaigns.scheduling') }}</a></li>
                </ul>
                <div class="tab-content">

                    <div id="campaign" class="tab-pane active">
                        <div class="form-group">
                            {!! Form::label('name', trans('Newsletter::admin_lang_campaigns.name'), array('class' => 'col-sm-2 control-label required-input')) !!}
                            <div class="col-md-10">
                                {!! Form::text('name', null, array('placeholder' => trans('Newsletter::admin_lang_campaigns.name'), 'class' => 'form-control', 'id' => 'name')) !!}
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('list_id', Lang::get('Newsletter::admin_lang_campaigns.mailing_list'), array('class' => 'col-md-2 control-label required-input')) !!}
                            <div class="col-md-10">
                                <select class="form-control select2" name="list_id[]" multiple="multiple" data-placeholder="{{ trans('Newsletter::admin_lang_campaigns.select_mailing_list') }}" style="width: 100%;">
                                    @foreach($lists as $value)
                                        <option value="{{ $value->id }}"  @if($value->campaignSelected($campaign->id)) selected @endif > {{ $value->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('newsletter_id', Lang::get('Newsletter::admin_lang_campaigns.newsletter'), array('class' => 'col-md-2 control-label required-input')) !!}
                            <div class="col-md-10">
                                <select class="form-control select2" name="newsletter_id" style="width: 100%;" data-placeholder="{{ trans('Newsletter::admin_lang_campaigns.select_newsletter') }}">
                                    @foreach($newsletters as $value)
                                        <option value="{{ $value->id }}"  @if($campaign->newsletter_id == $value->id) selected="selected" @endif > {{ $value->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div id="campaign-scheduling" class="tab-pane">
                        <div class="form-group">
                            <div class="col-sm-offset-1 col-sm-10">
                                <label id = 'scheduled-checkbox'>
                                    {!! Form::checkbox('is_scheduled', 1, null, array( 'class' => 'minimal', 'id' => 'is_scheduled')) !!}
                                    {!! trans('Newsletter::admin_lang_campaigns.schedule_campaign')!!}
                                </label>
                            </div>
                        </div>

                        <div style="{{ old('is_scheduled', $campaign->is_scheduled) ? '' : 'display:none;'}}" id="schedule-controls">
                            <div class="form-group">
                                {!! Form::label('scheduled_for_date', trans('Newsletter::admin_lang_campaigns.scheduled_for_date'), array('class' => 'col-sm-2 control-label')) !!}
                                <div class="col-md-2">
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar" hidden="true" aria-hidden="true"></i>
                                        </div>
                                        {!! Form::text('scheduled_for_date', $campaign->scheduled_for_date_formatted, array('placeholder' => trans('Newsletter::admin_lang_campaigns.scheduled_for_date'), 'class' => 'form-control', 'id' => 'scheduled_for_date')) !!}
                                    </div>
                                </div>
                            </div>

                            <div class="form-group ">
                                {!! Form::label('scheduled_for_time', trans('Newsletter::admin_lang_campaigns.scheduled_for_time'), array('class' => 'col-sm-2 control-label')) !!}
                                <div class="col-md-2">
                                    <div class="input-group bootstrap-timepicker">
                                        <div class="input-group-addon">
                                            <i class="fa  fa-clock-o" aria-hidden="true"></i>
                                        </div>
                                        {!! Form::text('scheduled_for_time', $campaign->scheduled_for_time_formatted, array('placeholder' => trans('Newsletter::admin_lang_campaigns.scheduled_for_time'), 'class' => 'form-control timepicker ', 'id' => 'scheduled_for_time' )) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>

    <div class="box box-solid">

        <div class="box-footer">

            <a href="{{ url('/admin/newsletter-campaigns') }}" class="btn btn-default">{{ trans('general/admin_lang.cancelar') }}</a>
            <button type="submit" class="btn btn-info pull-right">{{ trans('general/admin_lang.save') }}</button>

        </div>

    </div>
    {!! Form::close() !!}




@endsection

@section("foot_page")
    <!-- iCheck -->
    <script src="{{ asset("/assets/admin/vendor/iCheck/js/icheck.min.js") }}"></script>
    <script type="text/javascript" src="{{ asset("/assets/admin/vendor/timepicker/bootstrap-timepicker.js") }}"></script>
    <script type="text/javascript" src="{{ asset('assets/admin/vendor/datepicker/js/bootstrap-datepicker.min.js')}}"></script>
    <script type="text/javascript" src="{{ asset('assets/admin/vendor/datepicker/locales/bootstrap-datepicker.'.app()->getLocale(). '.min.js')}}"></script>
    <script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js')}}"></script>

    <script>

        $(document).ready(function() {

            $(".select2").select2();

            $('input').iCheck({
                checkboxClass: 'icheckbox_square-blue',
                radioClass: 'iradio_square-blue',
                increaseArea: '20%' // optional
            });

            $('#is_scheduled').on('ifToggled', function(){
                toggleCalendar();
            });


            //Timepicker
            $('#scheduled_for_time').timepicker(
                {
                    minuteStep: 1,
                    defaultTime: false,
                    showMeridian: false
                }
            );

            $("#scheduled_for_date").datepicker({
                isRTL: false,
                format: 'dd/mm/yyyy',
                autoclose:true,
                language: 'es'
            });

            toggleCalendar();
        });

        function toggleCalendar() {
            var $controls = $('#schedule-controls');
            if($('#is_scheduled').prop('checked')) {
                $controls.slideDown();
            } else {
                $controls.slideUp();
            }
        }

    </script>
    {!! JsValidator::formRequest('App\Modules\Newsletter\Requests\AdminNewsletterCampaignsRequest')->selector('#formData') !!}

@stop
