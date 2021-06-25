@extends('admin.layouts.default')

@section('title')
    @parent {{ $page_title }}
@stop

@section('head_page')
    @parent
    <link href="{{ asset("/assets/admin/vendor/fontawesome-iconpicker/css/fontawesome-iconpicker.min.css") }}" rel="stylesheet" type="text/css" />
@stop


@section('breadcrumb')
    <li><a href="{{ url("admin/crud-generator") }}">{{ trans('crud-generator::modules/admin_lang.list') }}</a></li>
    <li class="active">{{ $page_title }}</li>
@stop

@section('content')

    @include('admin.includes.errors')
    @include('admin.includes.success')


    <div class="row">

        {!! Form::model($module, $form_data, array('role' => 'form')) !!}

            <div class="col-md-12">

                <div class="box box-primary">
                    <div class="box-header  with-border"><h3 class="box-title">{{ trans("general/admin_lang.info_menu") }}</h3></div>
                    <div class="box-body">

                        <div class="form-group">
                            {!! Form::label('title', trans('crud-generator::modules/admin_lang.name'), array('class' => 'col-sm-2 control-label required required-input')) !!}
                            <div class="col-sm-10">
                                {!! Form::text('title', null,
                                    array('placeholder' => trans('crud-generator::modules/admin_lang.name'),
                                    'class' => 'form-control input-xlarge',
                                    'id' => 'title')) !!}
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('model', trans('crud-generator::modules/admin_lang.model'), array('class' => 'col-sm-2 control-label required required-input')) !!}
                            <div class="col-sm-10">
                                {!! Form::text('model', null,
                                    array('placeholder' => trans('crud-generator::modules/admin_lang.model'),
                                    'class' => 'form-control input-xlarge',
                                    'style' => 'width:50%; display:inline;',
                                    'id' => 'model',
                                    !empty($module->id) ? 'readonly':''
                                    )) !!}
                                    {!! __("crud-generator::modules/admin_lang.model_info") !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('model_plural', trans('crud-generator::modules/admin_lang.model_plural'), array('class' => 'col-sm-2 control-label required required-input')) !!}
                            <div class="col-sm-10">
                                {!! Form::text('model_plural', null,
                                    array('placeholder' => trans('crud-generator::modules/admin_lang.model_plural'),
                                    'class' => 'form-control input-xlarge',
                                    'style' => 'width:50%; display:inline;',
                                    'id' => 'model_plural',
                                    !empty($module->id) ? 'readonly':''
                                    )) !!}
                                    {!! __("crud-generator::modules/admin_lang.model_plural_info") !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('table_name', trans('crud-generator::modules/admin_lang.table_name'), array('class' => 'col-sm-2 control-label required required-input')) !!}
                            <div class="col-sm-10">
                                {!! Form::text('table_name', null,
                                    array('placeholder' => trans('crud-generator::modules/admin_lang.table_name'),
                                    'class' => 'form-control input-xlarge',
                                    'style' => 'width:50%; display:inline;',
                                    'id' => 'table_name'
                                    )) !!}
                                    {!! __("crud-generator::modules/admin_lang.table_name_info") !!}
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('icon', trans('crud-generator::modules/admin_lang.icon'), array('class' => 'col-sm-2 control-label required required-input')) !!}
                            <div class="col-sm-10">
                                {!! Form::hidden('icon', null, array('id' => 'icon')) !!}

                                <div class="btn-group">
                                    <button data-selected="graduation-cap" type="button" class="icp demo btn btn-default dropdown-toggle iconpicker-component" data-toggle="dropdown">
                                        <i class="fa {{$module->icon}}" aria-hidden="true"></i>
                                        <span class="caret"></span>
                                    </button>
                                    <div class="dropdown-menu"></div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('active', trans('crud-generator::modules/admin_lang.active'), array('class' => 'col-sm-2 control-label', 'readonly' => true)) !!}
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

                        <div class="form-group">
                            {!! Form::label('has_soft_deletes', trans('crud-generator::modules/admin_lang.has_soft_deletes'), array('class' => 'col-sm-2 control-label', 'readonly' => true)) !!}
                            <div class="col-md-10">
                                <div class="radio-list">
                                    <label class="radio-inline">
                                        {!! Form::radio('has_soft_deletes', '0', true, array('id'=>'has_soft_deletes_0')) !!}
                                        {{ Lang::get('general/admin_lang.no') }}</label>
                                    <label class="radio-inline">
                                        {!! Form::radio('has_soft_deletes', '1', false, array('id'=>'has_soft_deletes_1')) !!}
                                        {{ Lang::get('general/admin_lang.yes') }} </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('has_api_crud', trans('crud-generator::modules/admin_lang.has_api_crud'), array('class' => 'col-sm-2 control-label', 'readonly' => true)) !!}
                            <div class="col-md-10">
                                <div class="radio-list">
                                    <label class="radio-inline">
                                        {!! Form::radio('has_api_crud', '0', true, array('id'=>'has_api_crud_0')) !!}
                                        {{ Lang::get('general/admin_lang.no') }}</label>
                                    <label class="radio-inline">
                                        {!! Form::radio('has_api_crud', '1', false, array('id'=>'has_api_crud_1')) !!}
                                        {{ Lang::get('general/admin_lang.yes') }} </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('has_api_crud_secure', trans('crud-generator::modules/admin_lang.has_api_crud_secure'), array('class' => 'col-sm-2 control-label', 'readonly' => true)) !!}
                            <div class="col-md-10">
                                <div class="radio-list">
                                    <label class="radio-inline">
                                        {!! Form::radio('has_api_crud_secure', '0', true, array('id'=>'has_api_crud_secure_0')) !!}
                                        {{ Lang::get('general/admin_lang.no') }}</label>
                                    <label class="radio-inline">
                                        {!! Form::radio('has_api_crud_secure', '1', false, array('id'=>'has_api_crud_secure_1')) !!}
                                        {{ Lang::get('general/admin_lang.yes') }} </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('has_create_form', trans('crud-generator::modules/admin_lang.has_create_form'), array('class' => 'col-sm-2 control-label', 'readonly' => true)) !!}
                            <div class="col-md-10">
                                <div class="radio-list">
                                    <label class="radio-inline">
                                        {!! Form::radio('has_create_form', '0', true, array('id'=>'has_create_form_0')) !!}
                                        {{ Lang::get('general/admin_lang.no') }}</label>
                                    <label class="radio-inline">
                                        {!! Form::radio('has_create_form', '1', false, array('id'=>'has_create_form_1')) !!}
                                        {{ Lang::get('general/admin_lang.yes') }} </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('has_edit_form', trans('crud-generator::modules/admin_lang.has_edit_form'), array('class' => 'col-sm-2 control-label', 'readonly' => true)) !!}
                            <div class="col-md-10">
                                <div class="radio-list">
                                    <label class="radio-inline">
                                        {!! Form::radio('has_edit_form', '0', true, array('id'=>'has_edit_form_0')) !!}
                                        {{ Lang::get('general/admin_lang.no') }}</label>
                                    <label class="radio-inline">
                                        {!! Form::radio('has_edit_form', '1', false, array('id'=>'has_edit_form_1')) !!}
                                        {{ Lang::get('general/admin_lang.yes') }} </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('has_show_form', trans('crud-generator::modules/admin_lang.has_show_form'), array('class' => 'col-sm-2 control-label', 'readonly' => true)) !!}
                            <div class="col-md-10">
                                <div class="radio-list">
                                    <label class="radio-inline">
                                        {!! Form::radio('has_show_form', '0', true, array('id'=>'has_show_form_0')) !!}
                                        {{ Lang::get('general/admin_lang.no') }}</label>
                                    <label class="radio-inline">
                                        {!! Form::radio('has_show_form', '1', false, array('id'=>'has_show_form_1')) !!}
                                        {{ Lang::get('general/admin_lang.yes') }} </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('has_delete_form', trans('crud-generator::modules/admin_lang.has_delete_form'), array('class' => 'col-sm-2 control-label', 'readonly' => true)) !!}
                            <div class="col-md-10">
                                <div class="radio-list">
                                    <label class="radio-inline">
                                        {!! Form::radio('has_delete_form', '0', true, array('id'=>'has_delete_form_0')) !!}
                                        {{ Lang::get('general/admin_lang.no') }}</label>
                                    <label class="radio-inline">
                                        {!! Form::radio('has_delete_form', '1', false, array('id'=>'has_delete_form_1')) !!}
                                        {{ Lang::get('general/admin_lang.yes') }} </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('has_exports', trans('crud-generator::modules/admin_lang.has_exports'), array('class' => 'col-sm-2 control-label', 'readonly' => true)) !!}
                            <div class="col-md-10">
                                <div class="radio-list">
                                    <label class="radio-inline">
                                        {!! Form::radio('has_exports', '0', true, array('id'=>'has_exports_0')) !!}
                                        {{ Lang::get('general/admin_lang.no') }}</label>
                                    <label class="radio-inline">
                                        {!! Form::radio('has_exports', '1', false, array('id'=>'has_exports_1')) !!}
                                        {{ Lang::get('general/admin_lang.yes') }} </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('has_fake_data', trans('crud-generator::modules/admin_lang.has_fake_data'), array('class' => 'col-sm-2 control-label', 'readonly' => true)) !!}
                            <div class="col-md-10">
                                <div class="radio-list">
                                    <label class="radio-inline">
                                        {!! Form::radio('has_fake_data', '0', true, array('id'=>'has_fake_data_0')) !!}
                                        {{ Lang::get('general/admin_lang.no') }}</label>
                                    <label class="radio-inline">
                                        {!! Form::radio('has_fake_data', '1', false, array('id'=>'has_fake_data_1')) !!}
                                        {{ Lang::get('general/admin_lang.yes') }} </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('entries_page', trans('crud-generator::modules/admin_lang.entries_page'),
                            array('class' => 'col-sm-2 control-label required required-input')) !!}
                            <div class="col-sm-10">
                                {!! Form::select('entries_page',
                                ['' => trans('crud-generator::modules/admin_lang.select_option')] + $entries_page_list,
                                !empty($module->entries_page) ? $module->entries_page : null ,
                                ['id'=>'entries_page', 'class' => 'form-control select2']) !!}
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('order_by_field', trans('crud-generator::modules/admin_lang.order_by_field'),
                            array('class' => 'col-sm-2 control-label required required-input')) !!}
                            <div class="col-sm-10">
                                {!! Form::select('order_by_field',
                                ['' => trans('crud-generator::modules/admin_lang.select_option')] + $order_by_field_list,
                                !empty($module->order_by_field) ? $module->order_by_field : null ,
                                ['id'=>'order_by_field', 'class' => 'form-control select2']) !!}
                            </div>
                        </div>


                        <div class="form-group">
                            {!! Form::label('order_direction', trans('crud-generator::modules/admin_lang.order_direction'),
                            array('class' => 'col-sm-2 control-label required required-input')) !!}
                            <div class="col-sm-10">
                                {!! Form::select('order_direction',
                                ['' => trans('crud-generator::modules/admin_lang.select_option')] + $order_direction_list,
                                !empty($module->order_direction) ? $module->order_direction : null ,
                                ['id'=>'order_direction', 'class' => 'form-control select2']) !!}
                            </div>
                        </div>


                    </div>
                </div>

                <div class="box box-solid">
                    <div class="box-footer">
                        <a href="{{ url('/admin/crud-generator') }}" class="btn btn-default">{{ trans('general/admin_lang.cancelar') }}</a>
                        <button type="submit" class="btn btn-info pull-right">{{ trans('general/admin_lang.save') }}</button>

                    </div>

                </div>

            </div>


        {!! Form::close() !!}
    </div>

@endsection

@section('foot_page')
    <script src="{{ asset("/assets/admin/vendor/fontawesome-iconpicker/js/fontawesome-iconpicker.min.js") }}"></script>
    <script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js')}}"></script>

    <script>
        $(document).ready(function() {

        });

        @if(empty($module->id))
            $( "#title" ).change(function() {
                var title = $( "#title" ).val();

                $( "#model" ).val( Modelo(title));
                $( "#model_plural" ).val( ModeloPlural(title));
                $( "#table_name" ).val( ModeloPluralLowercase(title));
            });
        @endif

        $('.demo').iconpicker();
        $('.demo').on('iconpickerSelected', function(event){
            $('#icon').val(event.iconpickerValue);
        });

        function Capitalizar(str) {
            var splitStr = str.toLowerCase().split(' ');
            for (var i = 0; i < splitStr.length; i++) {
                // You do not need to check if i is larger than splitStr length, as your for does that for you
                // Assign it back to the array
                splitStr[i] = splitStr[i].charAt(0).toUpperCase() + splitStr[i].substring(1);
            }
            // Directly return the joined string
            return splitStr.join(' ');
        }


        function Modelo(str) {
            str = Capitalizar(str);
            str = str.replace(/\s/g, "");
            return str;
        }


        function ModeloLowercase(str) {
            str = Modelo(str).toLowerCase();
            return str;
        }

        function ModeloPlural(str) {
            return Modelo(str)+(str.endsWith('s')?'':'s');
        }

        function ModeloPluralLowercase(str) {
            return ModeloPlural(str).toLowerCase();
        }

    </script>

    {!! JsValidator::formRequest('Clavel\CrudGenerator\Requests\ModuleRequest')->selector('#formData') !!}
@stop
