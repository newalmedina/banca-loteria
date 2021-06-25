@extends('admin.layouts.default')

@section('title')
    @parent {{ $page_title }}
@stop

@section('head_page')
    @parent
    <link href="{{ asset("/assets/admin/vendor/colorpicker/css/bootstrap-colorpicker.min.css") }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset("assets/admin/vendor/datepicker/css/bootstrap-datepicker.min.css") }}" rel="stylesheet" type="text/css" />

@stop


@section('breadcrumb')
    <li><a href="{{ url("admin/projects") }}">{{ trans('timetracker::projects/admin_lang.list') }}</a></li>
    <li class="active">{{ $page_title }}</li>
@stop

@section('content')

    @include('admin.includes.errors')
    @include('admin.includes.success')

    <div class="row">
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box bg-green">
                <span class="info-box-icon"><i class="fa fa-money" aria-hidden="true"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">Rentabilidad</span>
                    <span class="info-box-number">15000</span>

                    <div class="progress">
                        <div class="progress-bar" style="width: 70%"></div>
                    </div>
                    <span class="progress-description">
                    100%
                  </span>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>
        <!-- /.col -->
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box bg-red">
                <span class="info-box-icon"><i class="fa fa-thumbs-o-down" aria-hidden="true"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">Gasto</span>
                    <span class="info-box-number">0</span>

                    <div class="progress">
                        <div class="progress-bar" style="width: 20%"></div>
                    </div>
                    <span class="progress-description">
                    0%
                  </span>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>

        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box bg-blue">
                <span class="info-box-icon"><i class="fa fa-clock-o" aria-hidden="true"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">Tiempo Estimado</span>
                    <span class="info-box-number">{{ $tiempo_estimado }} h</span>
                    <div class="progress">
                        <div class="progress-bar" style="width: 0%"></div>
                    </div>
                    <span class="progress-description">
                    0%
                  </span>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>
    </div>
    <!-- /.row -->

    <div class="row">

        {!! Form::model($project, $form_data, array('role' => 'form')) !!}

            <div class="col-md-12">

                <div class="box box-primary">
                    <div class="box-header  with-border"><h3 class="box-title">{{ trans("general/admin_lang.info_menu") }}</h3></div>
                    <div class="box-body">

                        <div class="form-group">
                            {!! Form::label('name', trans('timetracker::projects/admin_lang.name'), array('class' => 'col-sm-2 control-label required required-input')) !!}
                            <div class="col-sm-10">
                                {!! Form::text('name', null,
                                    array('placeholder' => trans('timetracker::projects/admin_lang.name'),
                                    'class' => 'form-control input-xlarge',
                                    'id' => 'name')) !!}
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('project_type_id', trans('timetracker::projects/admin_lang.type'), array('class' => 'col-sm-2 control-label required required-input')) !!}
                            <div class="col-sm-10">
                                <select name="project_type_id" id="project_type_id" class="form-control select2">
                                    @foreach($typesList as $key=>$value)
                                        <option value="{{ $value->id }}" @if($value->id==$project->project_type_id) selected @endif>{{ $value->name }}</option>
                                    @endforeach
                                </select>

                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('order_number', trans('timetracker::projects/admin_lang.order_number'), array('class' => 'col-sm-2 control-label required-input')) !!}
                            <div class="col-sm-1">
                                <div class="input-group">
                                {!! Form::text('order_number', null,
                                    array('placeholder' => trans('timetracker::projects/admin_lang.order_number'),
                                    'class' => 'form-control input-xlarge',
                                    'id' => 'order_number')) !!}
                                    @if(!empty($project->id) && empty($project->order_number))
                                        <span id="btn-order_number" class="input-group-addon"  onclick="btnOrderNumber('{{url('admin/projects/order-number/'.$project->id)}}');">
                                            <a href="#">
                                                <i class="fa fa-bolt" aria-hidden="true"></i>
                                            </a>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {!! Form::label('budget_number', trans('timetracker::projects/admin_lang.budget_number'), array('class' => 'col-sm-1 control-label required-input')) !!}
                            <div class="col-sm-1">
                                <div class="input-group">
                                {!! Form::text('budget_number', null,
                                    array('placeholder' => trans('timetracker::projects/admin_lang.budget_number'),
                                    'class' => 'form-control input-xlarge',
                                    'id' => 'budget_number')) !!}
                                    @if(!empty($project->id) && empty($project->budget_number))
                                        <span id="btn-budget_number" class="input-group-addon">
                                            <a href="#" onclick="btnBudgetNumber('{{url('admin/projects/budge-number/'.$project->id)}}');">
                                                <i class="fa fa-bolt" aria-hidden="true"></i>
                                            </a>
                                        </span>

                                    @endif
                                </div>
                            </div>

                            {!! Form::label('customer_number', trans('timetracker::projects/admin_lang.customer_number'), array('class' => 'col-sm-2 control-label required-input')) !!}
                            <div class="col-sm-1">
                                {!! Form::text('customer_number', null,
                                    array('placeholder' => trans('timetracker::projects/admin_lang.customer_number'),
                                    'class' => 'form-control input-xlarge',
                                    'id' => 'customer_number')) !!}

                            </div>

                            {!! Form::label('invoice_number', trans('timetracker::projects/admin_lang.invoice_number'), array('class' => 'col-sm-2 control-label required-input')) !!}
                            <div class="col-sm-2">
                                {!! Form::text('invoice_number', null,
                                    array('placeholder' => trans('timetracker::projects/admin_lang.invoice_number'),
                                    'class' => 'form-control input-xlarge',
                                    'id' => 'invoice_number')) !!}

                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('description', trans('timetracker::projects/admin_lang.description'), array('class' => 'col-sm-2 control-label required-input')) !!}
                            <div class="col-sm-10">
                                {!! Form::textarea('description', null,
                                    array('placeholder' => trans('timetracker::projects/admin_lang.description'),
                                    'class' => 'form-control textarea',
                                    'rows' => 3,
                                    'id' => 'description')) !!}
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('customer_id', trans('timetracker::projects/admin_lang.customer'), array('class' => 'col-sm-2 control-label required')) !!}
                            <div class="col-sm-10">
                                {!! Form::select('customer_id', $customersList, !empty($project->customer_id) ? $project->customer_id : null , ['id'=>'customer_id', 'class' => 'form-control select2']) !!}
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('customer_final_id', trans('timetracker::projects/admin_lang.customer_final'), array('class' => 'col-sm-2 control-label required required-input')) !!}
                            <div class="col-sm-10">

                                {!! Form::select('customer_final_id',
                                ['' => ''] + $customersList,
                                !empty($project->customer_final_id) ? $project->customer_final_id : null , ['id'=>'customer_final_id', 'class' => 'form-control select2']) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('responsable_id', trans('timetracker::projects/admin_lang.responsable'), array('class' => 'col-sm-2 control-label required')) !!}
                            <div class="col-sm-10">
                                {!! Form::select('responsable_id', $responsableList, !empty($project->responsable_id) ? $project->responsable_id : null , ['id'=>'responsable_id', 'class' => 'form-control select2','placeholder' => 'Sin responsable']) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('expire_at', trans('timetracker::projects/admin_lang.expire_at'), array('class' => 'col-sm-2 control-label')) !!}
                            <div class="col-md-2">
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar" hidden="true" aria-hidden="true"></i>
                                    </div>
                                    {!! Form::text('expire_at', $project->expire_at_formatted, array('placeholder' => trans('timetracker::projects/admin_lang.expire_at'), 'class' => 'form-control', 'id' => 'expire_at', 'autocomplete' => 'off')) !!}
                                </div>
                            </div>
                            {!! Form::label('closed_at', trans('timetracker::projects/admin_lang.closed_at'), array('class' => 'col-sm-2 control-label')) !!}
                            <div class="col-md-2">
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar" hidden="true" aria-hidden="true"></i>
                                    </div>
                                    {!! Form::text('closed_at', $project->closed_at_formatted, array('placeholder' => trans('timetracker::projects/admin_lang.closed_at'), 'class' => 'form-control', 'id' => 'closed_at', 'autocomplete' => 'off')) !!}
                                </div>
                            </div>
                        </div>
                        <!--
                        <div class="form-group">
                            {!! Form::label('fixed_rate', trans('timetracker::projects/admin_lang.fixed_rate'), array('class' => 'col-sm-2 control-label required-input')) !!}
                            <div class="col-sm-10">
                                <div class="input-group">

                                    {!! Form::text('fixed_rate', null,
                                    array('placeholder' => trans('timetracker::projects/admin_lang.fixed_rate'),
                                    'class' => 'form-control input-xlarge',
                                    'id' => 'fixed_rate')) !!}
                                    <span class="input-group-addon"><i class="fa fa-euro" aria-hidden="true"></i></span>
                                </div>
                            </div>
                        </div>
                        -->

                        <div class="form-group">
                            {!! Form::label('hourly_rate', trans('timetracker::projects/admin_lang.hourly_rate'), array('class' => 'col-sm-2 control-label required-input')) !!}
                            <div class="col-sm-2">
                                <div class="input-group">

                                    {!! Form::text('hourly_rate', null,
                                    array('placeholder' => trans('timetracker::projects/admin_lang.hourly_rate'),
                                    'class' => 'form-control input-xlarge',
                                    'id' => 'hourly_rate')) !!}
                                    <span class="input-group-addon"><i class="fa fa-euro" aria-hidden="true"></i></span>
                                </div>
                            </div>
                            {!! Form::label('work_hours', trans('timetracker::projects/admin_lang.work_hours'), array('class' => 'col-sm-2 control-label required-input')) !!}
                            <div class="col-sm-2">
                                <div class="input-group">

                                    {!! Form::text('work_hours', null,
                                    array('placeholder' => trans('timetracker::projects/admin_lang.work_hours'),
                                    'class' => 'form-control input-xlarge',
                                    'id' => 'work_hours')) !!}
                                    <span class="input-group-addon"><strong>h</strong></span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('budget', trans('timetracker::projects/admin_lang.budget'), array('class' => 'col-sm-2 control-label')) !!}
                            <div class="col-sm-2">
                                <div class="input-group">

                                    {!! Form::text('budget', null,
                                    array('placeholder' => trans('timetracker::projects/admin_lang.budget'),
                                    'class' => 'form-control input-xlarge',
                                    'id' => 'budget')) !!}
                                    <span class="input-group-addon"><i class="fa fa-euro" aria-hidden="true"></i></span>
                                </div>
                            </div>

                            {!! Form::label('vat', trans('timetracker::projects/admin_lang.vat'), array('class' => 'col-sm-2 control-label')) !!}
                            <div class="col-sm-2">
                                <div class="input-group">

                                    {!! Form::text('vat', null,
                                    array('placeholder' => trans('timetracker::projects/admin_lang.vat'),
                                    'class' => 'form-control input-xlarge',
                                    'id' => 'vat')) !!}
                                    <span class="input-group-addon"><i class="fa fa-percent" aria-hidden="true"></i></span>
                                </div>
                            </div>

                            {!! Form::label('total', trans('timetracker::projects/admin_lang.total'), array('class' => 'col-sm-2 control-label ')) !!}
                            <div class="col-sm-2">
                                <div class="input-group">

                                    {!! Form::text('total', null,
                                    array('placeholder' => trans('timetracker::projects/admin_lang.total'),
                                    'class' => 'form-control input-xlarge',
                                    'id' => 'total', 'disabled' => 'disabled')) !!}
                                    <span class="input-group-addon"><i class="fa fa-euro" aria-hidden="true"></i></span>
                                </div>
                            </div>
                        </div>



                        <div class="form-group">
                            {!! Form::label('bill_info', trans('timetracker::projects/admin_lang.bill_info'), array('class' => 'col-sm-2 control-label required-input')) !!}
                            <div class="col-sm-10">
                                {!! Form::textarea('bill_info', null,
                                    array('placeholder' => trans('timetracker::projects/admin_lang.bill_info'),
                                    'class' => 'form-control textarea',
                                    'rows' => 3,
                                    'id' => 'bill_info')) !!}
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('slug_state', trans('timetracker::projects/admin_lang.state'), array('class' => 'col-sm-2 control-label ')) !!}
                            <div class="col-sm-10">
                                <select name="slug_state" id="slug_state" class="form-control select2">
                                @foreach($statesList as $key=>$value)
                                    <option value="{{ $value->slug }}" @if($value->slug==$project->slug_state) selected @endif>{{ $value->name }}</option>
                                @endforeach
                                </select>

                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('invoiced', trans('timetracker::projects/admin_lang.invoiced'), array('class' => 'col-sm-2 control-label ')) !!}
                            <div class="col-sm-10 checkbox">
                                <select name="invoiced" id="invoiced" class="form-control select2">
                                    @foreach($invoicedList as $key=>$value)
                                        <option value="{{ $value->id }}" @if($value->id==$project->invoiced) selected @endif>{{  $value->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('color', trans('timetracker::projects/admin_lang.color'), array('class' => 'col-sm-2 control-label')) !!}
                            <div class="col-sm-10">
                                <div class="input-group my-colorpicker2 colorpicker-element">
                                    {!! Form::text('color', null, array('placeholder' => trans('timetracker::projects/admin_lang.color'), 'class' => 'form-control', 'id' => 'color')) !!}

                                    <div class="input-group-addon">
                                        <em style="background-color: rgb(136, 119, 119);"></em>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('active', trans('timetracker::projects/admin_lang.active'), array('class' => 'col-sm-2 control-label', 'readonly' => true)) !!}
                            <div class="col-md-10">
                                <div class="radio-list">
                                    <label class="radio-inline">
                                        {!! Form::radio('active', '0', true, array('id'=>'active_0')) !!}
                                        {{ Lang::get('general/admin_lang.no') }}</label>
                                    <label class="radio-inline">
                                        {!! Form::radio('active', '1', false, array('id'=>'active_1')) !!}
                                        {{ Lang::get('general/admin_lang.yes') }} </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>



                <div class="box box-solid">

                    <div class="box-footer">

                        <a href="{{ url('/admin/projects') }}" class="btn btn-default">{{ trans('general/admin_lang.cancelar') }}</a>
                        <button type="submit" class="btn btn-info pull-right">{{ trans('general/admin_lang.save') }}</button>

                    </div>

                </div>

            </div>


        {!! Form::close() !!}
    </div>

@endsection

@section('foot_page')
    <script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js')}}"></script>
    <script type="text/javascript" src="{{ asset('/assets/admin/vendor/colorpicker/js/bootstrap-colorpicker.min.js')}}"></script>
    <script type="text/javascript" src="{{ asset('assets/admin/vendor/datepicker/js/bootstrap-datepicker.min.js')}}"></script>
    <script type="text/javascript" src="{{ asset('assets/admin/vendor/datepicker/locales/bootstrap-datepicker.'.app()->getLocale(). '.min.js')}}"></script>

    <script>
        $(document).ready(function() {
            $(".my-colorpicker2").colorpicker();

            $('#budget, #vat').change(function() {
                calculateTotal();
            });

            $('#budget, #vat').keyup(function () {
                calculateTotal();
            });

            $("#expire_at, #closed_at").datepicker({
                isRTL: false,
                format: 'dd/mm/yyyy',
                autoclose:true,
                language: 'es'
            });


            $("#hourly_rate, #work_hours").change(function() {
                if($("#hourly_rate").val() !== '' &&
                    $("#work_hours").val() !== ''
                ) {
                    calculateImport();
                }


            });
        });

        function calculateImport() {
            var hourly_rate = parseFloat($( "#hourly_rate" ).val()) || 0;
            var work_hours = parseFloat($( "#work_hours" ).val()) || 0;
            var total = hourly_rate * work_hours;
            $('#budget').val(total);
            calculateTotal();
        }

        function calculateTotal() {
            var budget = parseFloat($( "#budget" ).val()) || 0;
            var vat = parseFloat($( "#vat" ).val()) || 0;
            var total = budget + ((budget*vat)/100);
            $('#total').val(total);
        }

        function btnOrderNumber(url) {
            $.ajax({
                url     : url,
                type    : 'POST',
                "headers": {"X-CSRF-TOKEN": "{{ csrf_token() }}"},
                success : function(data) {

                    $("#order_number").val(data.order_number);
                    $("#btn-order_number").hide();
                    return false;
                }
            });
            return false;
        }

        function btnBudgetNumber(url) {
            $.ajax({
                url     : url,
                type    : 'POST',
                "headers": {"X-CSRF-TOKEN": "{{ csrf_token() }}"},
                success : function(data) {

                    $("#budget_number").val(data.budget_number);
                    $("#btn-budget_number").hide();
                    return false;
                }
            });
            return false;
        }

    </script>

    {!! JsValidator::formRequest('Clavel\TimeTracker\Requests\ProjectRequest')->selector('#formData') !!}
@stop
