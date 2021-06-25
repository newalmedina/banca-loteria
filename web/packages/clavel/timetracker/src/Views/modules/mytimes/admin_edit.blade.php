@extends('admin.layouts.default')

@section('title')
    @parent {{ $page_title }}
@stop

@section('head_page')
    <link rel="stylesheet" type="text/css" href="{{ asset("assets/admin/vendor/daterangepicker/css/daterangepicker.css") }}" />
@stop

@section('breadcrumb')
    <li><a href="{{ url("admin/mytimes") }}">{{ trans('timetracker::mytimes/admin_lang.list') }}</a></li>
    <li class="active">{{ $page_title }}</li>
@stop

@section('content')

    @include('admin.includes.errors')
    @if (Session::get('success',"") != "")
        <div class="alert alert-success">
            <button class="close" aria-hidden="true" data-dismiss="alert" type="button">&times;</button>
            <strong>{{ date('d/m/Y H:i:s') }}</strong>
            {{ Session::get('success',"") }}
        </div>
    @endif

    <div class="row">
        {!! Form::model($timesheet, $form_data, array('role' => 'form')) !!}

            <div class="col-md-12">

                <div class="box box-primary">
                    <div class="box-header  with-border"><h3 class="box-title">{{ trans("general/admin_lang.info_menu") }}</h3></div>
                    <div class="box-body">

                        <div class="form-group">
                            {!! Form::label('start_time', trans('timetracker::mytimes/admin_lang.start_time'),
                            array('class' => 'col-sm-2 control-label required required-input')) !!}
                            <div class="col-md-3">
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar" hidden="true" aria-hidden="true"></i>
                                    </div>
                                    {!! Form::text('start_time',
                                    $timesheet->start_time_formatted,
                                    array('placeholder' => trans('timetracker::mytimes/admin_lang.start_time'),
                                    'class' => 'form-control', 'id' => 'start_time', "autocomplete" => "off")) !!}
                                </div>
                            </div>

                        </div>

                        <div class="form-group">
                            {!! Form::label('end_time', trans('timetracker::mytimes/admin_lang.end_time'),
                            array('class' => 'col-sm-2 control-label')) !!}
                            <div class="col-md-3">
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar" hidden="true" aria-hidden="true"></i>
                                    </div>
                                    {!! Form::text('end_time', $timesheet->end_time_formatted,
                                    array('placeholder' => trans('timetracker::mytimes/admin_lang.end_time'),
                                    'class' => 'form-control', 'id' => 'end_time', "autocomplete" => "off")) !!}
                                </div>
                            </div>

                        </div>


                        <div class="form-group">
                            {!! Form::label('customer_id', trans('timetracker::mytimes/admin_lang.customer'),
                             array('class' => 'col-sm-2 control-label required required-input')) !!}
                            <div class="col-sm-10">
                                {!! Form::select('customer_id',
                                ['' => trans('timetracker::mytimes/admin_lang.select_customer')] + $customers_list,
                                !empty($timesheet->customer_id) ? $timesheet->customer_id : null ,
                                [   'id'=>'customer_id',
                                    'class' => 'form-control select2'
                                    ]) !!}
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('project_id', trans('timetracker::mytimes/admin_lang.project'),
                            array('class' => 'col-sm-2 control-label required required-input')) !!}
                            <div class="col-sm-10">
                                {!! Form::select('project_id',
                                ['' => trans('timetracker::mytimes/admin_lang.select_project')] + $projects_list,
                                !empty($timesheet->project_id) ? $timesheet->project_id : null ,
                                ['id'=>'project_id', 'class' => 'form-control select2']) !!}
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('activity_id', trans('timetracker::mytimes/admin_lang.activity'),
                            array('class' => 'col-sm-2 control-label required required-input')) !!}
                            <div class="col-sm-10">
                                {!! Form::select('activity_id',
                                ['' => trans('timetracker::mytimes/admin_lang.select_activity')] + $activities_list,
                                !empty($timesheet->activity_id) ? $timesheet->activity_id : null ,
                                ['id'=>'activity_id', 'class' => 'form-control select2']) !!}
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('description', trans('timetracker::mytimes/admin_lang.description'), array('class' => 'col-sm-2 control-label required-input')) !!}
                            <div class="col-sm-10">
                                {!! Form::textarea('description', null,
                                    array('placeholder' => trans('timetracker::mytimes/admin_lang.description'),
                                    'class' => 'form-control textarea',
                                    'rows' => 3,
                                    'id' => 'description')) !!}
                            </div>
                        </div>


                        <div class="form-group">
                            {!! Form::label('fixed_rate', trans('timetracker::mytimes/admin_lang.fixed_rate'), array('class' => 'col-sm-2 control-label required-input')) !!}
                            <div class="col-sm-10">
                                <div class="input-group">

                                    {!! Form::text('fixed_rate', null,
                                    array('placeholder' => trans('timetracker::mytimes/admin_lang.fixed_rate'),
                                    'class' => 'form-control input-xlarge',
                                    'id' => 'fixed_rate')) !!}
                                    <span class="input-group-addon"><i class="fa fa-euro" aria-hidden="true"></i></span>
                                </div>
                            </div>
                        </div>


                        <div class="form-group">
                            {!! Form::label('hourly_rate', trans('timetracker::mytimes/admin_lang.hourly_rate'), array('class' => 'col-sm-2 control-label required-input')) !!}
                            <div class="col-sm-10">
                                <div class="input-group">

                                    {!! Form::text('hourly_rate', null,
                                    array('placeholder' => trans('timetracker::mytimes/admin_lang.hourly_rate'),
                                    'class' => 'form-control input-xlarge',
                                    'id' => 'hourly_rate')) !!}
                                    <span class="input-group-addon"><i class="fa fa-euro" aria-hidden="true"></i></span>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>



                <div class="box box-solid">

                    <div class="box-footer">

                        <a href="{{ url('/admin/mytimes') }}" class="btn btn-default">{{ trans('general/admin_lang.cancelar') }}</a>
                        <button type="submit" class="btn btn-info pull-right">{{ trans('general/admin_lang.save') }}</button>

                    </div>

                </div>

            </div>


        {!! Form::close() !!}
    </div>

@endsection

@section('foot_page')
    <script type="text/javascript" src="{{ asset('assets/admin/vendor/moment/moment.min.js')}}"></script>
    <script type="text/javascript" src="{{ asset('assets/admin/vendor/daterangepicker/js/daterangepicker.js')}}"></script>

    <script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js')}}"></script>

    <script type="text/javascript">
        $(document).ready(function() {

            $("#customer_id").select2();
            $("#project_id").select2();
            $("#activity_id").select2();

            $("#start_time").daterangepicker({
                singleDatePicker: true,
                timePicker24Hour: true,
                timePicker: true,
                timePickerIncrement: 1,
                autoApply: true,
                locale: {
                    format: 'DD/MM/YYYY HH:mm',
                    "separator": " - ",
                    "applyLabel": "Aceptar",
                    "cancelLabel": "Cancelar",
                    "fromLabel": "Desde",
                    "toLabel": "Hasta",
                    "customRangeLabel": "Custom",
                    "weekLabel": "W",
                    "daysOfWeek": [
                        "Do",
                        "Lu",
                        "Ma",
                        "Mi",
                        "Ju",
                        "Vi",
                        "Sa"
                    ],
                    "monthNames": [
                        "Enero",
                        "Febrero",
                        "Marzo",
                        "Abril",
                        "Mayo",
                        "Junio",
                        "Julio",
                        "Agosto",
                        "Septiembre",
                        "Octubre",
                        "Noviembre",
                        "Diciembre"
                    ],
                    "firstDay": 1
                }
            }, function(start, end, label) {


            });

            $("#end_time").daterangepicker({
                singleDatePicker: true,
                timePicker24Hour: true,
                timePicker: true,
                timePickerIncrement: 1,
                autoUpdateInput: false,
                autoApply: true,
                locale: {
                    format: 'DD/MM/YYYY HH:mm',
                    "separator": " - ",
                    "applyLabel": "Aceptar",
                    "cancelLabel": "Cancelar",
                    "fromLabel": "Desde",
                    "toLabel": "Hasta",
                    "customRangeLabel": "Custom",
                    "weekLabel": "W",
                    "daysOfWeek": [
                        "Do",
                        "Lu",
                        "Ma",
                        "Mi",
                        "Ju",
                        "Vi",
                        "Sa"
                    ],
                    "monthNames": [
                        "Enero",
                        "Febrero",
                        "Marzo",
                        "Abril",
                        "Mayo",
                        "Junio",
                        "Julio",
                        "Agosto",
                        "Septiembre",
                        "Octubre",
                        "Noviembre",
                        "Diciembre"
                    ],
                    "firstDay": 1
                }
            }, function(start, end, label) {
                $('#end_time').val(start.format('DD/MM/YYYY HH:mm'));
            });
        });

        $('#customer_id').on('change',function(e){
            var customer_id = $('#customer_id option:selected').attr('value');

            $.ajax({
                url     : '{{url('api/v1/timetracker/projects')}}/'+customer_id,
                type    : 'POST',
                "headers": {"X-CSRF-TOKEN": "{{ csrf_token() }}"},
                success : function(data) {
                    if(data) {
                        $('#project_id').empty();
                        $('#project_id').focus;
                        $('#project_id').append('<option value="">' + '{{ trans('timetracker::mytimes/admin_lang.select_project') }}' + '</option>');
                        $.each(data, function(key, value){
                            $('#project_id').append('<option value="'+ key +'">' + value+ '</option>');
                        });
                    } else {
                        $('#project_id').empty();
                        $("#modal_alert").addClass('modal-danger');
                        $("#alertModalBody").html("<i class='fa fa-bug' style='font-size: 64px; float: left; margin-right:15px;'></i> {{ trans('general/admin_lang.errorajax') }}");
                        $("#modal_alert").modal('toggle');
                    }
                    return false;
                }
            });
        });


    </script>

    {!! JsValidator::formRequest('Clavel\TimeTracker\Requests\MyTimesRequest')->selector('#formData') !!}
@stop
