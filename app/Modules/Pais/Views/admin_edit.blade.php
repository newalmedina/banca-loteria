@extends('admin.layouts.default')

@section('title')
    @parent {{ $page_title }}
@stop

@section('head_page')
    
@stop

@section('breadcrumb')
    <li><a href="{{ url("admin/pais") }}">{{ trans('Pais::pais/admin_lang.pais') }}</a></li>
    <li class="active">{{ $page_title }}</li>
@stop

@section('content')


    @include('admin.includes.errors')
    @include('admin.includes.success')


    <div class="row">
        {!! Form::model($pais, $form_data, array('role' => 'form')) !!}
         {!! Form::hidden("form_return", 0, array('id' => 'form_return')) !!}
        <div class="col-md-12">

            <div class="box box-primary">
                <div class="box-header  with-border"><h3 class="box-title">{{ trans("Pais::pais/admin_lang.info_menu") }}</h3></div>
                <div class="box-body">
                    {{-- Text - name --}}
<div class="form-group">
    {!! Form::label('name', trans('Pais::pais/admin_lang.fields.name'), array('class' => 'col-sm-2 control-label')) !!}
    <div class="col-sm-10">
        {!! Form::text('name', null , array('placeholder' => trans('Pais::pais/admin_lang.fields.name_helper'), 'class' => 'form-control', 'id' => 'name')) !!}
    </div>
</div>
{{-- TextArea - description --}}
<div class="form-group">
    {!! Form::label('description', trans('Pais::pais/admin_lang.fields.description'), array('class' => 'col-sm-2 control-label')) !!}
    <div class="col-sm-10">
        {!! Form::textarea('description', null , array('class' => 'form-control textarea', 'id' => 'description')) !!}
    </div>
</div>
{{-- Radio yes/no - active --}}
<div class="form-group">
    {!! Form::label('active', trans('Pais::pais/admin_lang.fields.active'), array('class' => 'col-sm-2 control-label')) !!}
    <div class="col-md-10">
        <div class="radio-list">
            <label class="radio-inline">
                {!! Form::radio('active', 0, true, array('id'=>'active_0')) !!}
                {{ trans('general/admin_lang.no') }}</label>
            <label class="radio-inline">
                {!! Form::radio('active', 1, false, array('id'=>'active_1')) !!}
                {{ trans('general/admin_lang.yes') }} </label>
        </div>
    </div>
</div>
{{-- Text - code --}}
<div class="form-group">
    {!! Form::label('code', trans('Pais::pais/admin_lang.fields.code'), array('class' => 'col-sm-2 control-label')) !!}
    <div class="col-sm-10">
        {!! Form::text('code', null , array('placeholder' => trans('Pais::pais/admin_lang.fields.code_helper'), 'class' => 'form-control', 'id' => 'code')) !!}
    </div>
</div>

                </div>
            </div>

            @if(\View::exists('Pais::admin_edit_lang'))
                @include('Pais::admin_edit_lang')
            @endif

            <div class="box box-solid">
                <div class="box-footer">
                    <a href="{{ url('/admin/pais') }}" class="btn btn-default">{{ trans('general/admin_lang.cancelar') }}</a>
                    <button type="submit" class="btn btn-info pull-right">{{ trans('general/admin_lang.save') }}</button>
                    <button id="btnSaveReturn" class="btn btn-success pull-right" style="margin-right:20px;">{{ trans('general/admin_lang.save_and_return') }}</button>
                </div>
            </div>

        </div>


        {!! Form::close() !!}
    </div>


@endsection

@section("foot_page")
    <script type="text/javascript"
            src="{{ asset("assets/admin/vendor/tinymce/tinymce.min.js") }}">
            </script>
    <script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js')}}"></script>

    <script>
        $(document).ready(function() {
            $(".select2").select2();

            tinymce.init({
    selector: "textarea.textarea",
    menubar: false,
    height: 300,
    resize:false,
    convert_urls: false,
    plugins: [
        "advlist autolink lists link image charmap print preview anchor",
        "searchreplace visualblocks code fullscreen",
        "insertdatetime media table contextmenu paste"
    ],
    toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link media image | code",
    file_browser_callback : function(field_name, url, type, win) {
        openImageController(field_name, '0');
    }
});
        });

        $('#btnSaveReturn').on( 'click', function (event) {
            event.preventDefault();
            $('#form_return').val(1);
            $('#formData').submit();
        } );

    </script>
    {!! JsValidator::formRequest('App\Modules\Pais\Requests\AdminPaisRequest')->selector('#formData') !!}
@stop
