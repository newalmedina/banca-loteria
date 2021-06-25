@extends('admin.layouts.default')

@section('title')
    @parent {{ $page_title }}
@endsection

@section('head_page')

    <style>
        #bs-modal-images {
            z-index: 99999999;
        }
    </style>
@stop

@section('breadcrumb')
    <li><a href="{{ url("admin/plantillas") }}">{{ trans('notificationbroker::plantillas/admin_lang.listado_plantillas') }}</a></li>
    <li class="active">{{ $page_title }}</li>
@endsection

@section('content')

    @include('admin.includes.errors')
    @include('admin.includes.success')
    @include('admin.includes.modals')

    <!-- Imágenes multimedia  -->
    <div class="modal modal-note fade in" id="bs-modal-images">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button aria-label="Close" data-dismiss="modal" class="close" type="button"><span aria-hidden="true">×</span></button>
                    <h4 class="modal-title">{{ trans('basic::pages/admin_lang.selecciona_un_archivo') }}</h4>
                </div>
                <div id="responsibe_images" class="modal-body">

                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>

    <div class="row">
        {!! Form::model($plantilla, $form_data, array('role' => 'form')) !!}
            {!! Form::hidden('generar', 0 , array('id' => 'generar')) !!}

            <div class="col-md-12">


                <div class="box box-primary">
                    <div class="box-header  with-border"><h3 class="box-title">{{ trans("notificationbroker::plantillas/admin_lang.info_menu") }}</h3></div>
                    <div class="box-body">

                        <div class="form-group">
                            {!! Form::label('titulo', trans('notificationbroker::plantillas/admin_lang.titulo')." *", array('class' => 'col-sm-2 control-label')) !!}
                            <div class="col-sm-10">
                                {!! Form::text('titulo', null , array('placeholder' => trans('notificationbroker::plantillas/admin_lang.titulo'), 'class' => 'form-control', 'maxlength' => '255')) !!}
                            </div>
                        </div>

                        @if($plantilla->id!='')

                            <div class="form-group">
                                {!! Form::label('slug', trans('notificationbroker::plantillas/admin_lang.slug'), array('class' => 'col-sm-2 control-label')) !!}
                                <div class="col-sm-10">
                                    {!! Form::text('slug', null , array('placeholder' => trans('notificationbroker::plantillas/admin_lang.slug'), 'class' => 'form-control', 'maxlength' => '255', 'readonly' => true)) !!}
                                </div>
                            </div>

                            <div class="form-group">
                                {!! Form::label('archivo', trans('notificationbroker::plantillas/admin_lang.archivo'), array('class' => 'col-sm-2 control-label')) !!}
                                <div class="col-sm-10">
                                    {!! Form::text('archivo', $plantilla->archivoHuman, array('placeholder' => trans('notificationbroker::plantillas/admin_lang.archivo'), 'class' => 'form-control', 'maxlength' => '255', 'readonly' => true)) !!}
                                    @if($plantilla->archivo=='')
                                        <span class="help-block "><span class="text-info">* Para obtener el nombre de la plantilla, primero debe generarla.</span></span>
                                    @endif
                                </div>
                            </div>

                        @endif

                        <div class="form-group">
                            {!! Form::label('tipo', trans('notificationbroker::plantillas/admin_lang.tipo')." *", array('class' => 'col-sm-2 control-label')) !!}
                            <div class="col-sm-10">
                                @if($plantilla->archivo=='')
                                    <select id="tipo" name="tipo" class="form-control">
                                        <option value="email" @if($plantilla->tipo=='email') selected @endif>E-mail</option>
                                        <option value="sms" @if($plantilla->tipo=='sms') selected @endif>SMS</option>
                                    </select>
                                @else
                                    {!! Form::text('tipo', null , array('placeholder' => trans('notificationbroker::plantillas/admin_lang.tipo'), 'class' => 'form-control', 'maxlength' => '255', 'readonly' => true)) !!}
                                @endif
                            </div>
                        </div>

                        <div id="email" class="annexFields">

                            <div class="form-group">
                                {!! Form::label('subject', trans('notificationbroker::plantillas/admin_lang.subject'), array('class' => 'col-sm-2 control-label')) !!}
                                <div class="col-sm-10">
                                    {!! Form::text('subject', null , array('placeholder' => trans('notificationbroker::plantillas/admin_lang.subject'), 'class' => 'form-control', 'maxlength' => '255')) !!}
                                </div>
                            </div>

                            <div class="form-group">
                                {!! Form::label('mensaje', trans('notificationbroker::plantillas/admin_lang.mensaje'), array('class' => 'col-sm-2 control-label')) !!}
                                <div class="col-sm-7">
                                    @if(isset($iguales))
                                        @if(!$iguales)
                                        <div class="alert alert-danger alert-dismissible">
                                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                            <h4><i class="icon fa fa-ban" aria-hidden="true"></i> ¡Atención!</h4>
                                            La plantilla generada no es la misma que la que está editando.
                                            Para que los cambios surjan efecto debe pulsar el botón "Generar la plantilla".
                                        </div>
                                        @else
                                        <div class="alert alert-success alert-dismissible">
                                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                            <h4><i class="icon fa fa-check" aria-hidden="true"></i> ¡Información!</h4>
                                            La plantilla editada y generado son la misma.
                                        </div>
                                        @endif
                                    @endif
                                    {!! Form::textarea('mensaje_email', ($plantilla->tipo=='email') ? $plantilla->mensaje : '', array('class' => 'form-control textarea', 'id' => 'mensaje_email', "style" => "resize:none;")) !!}
                                </div>
                                <div class="col-sm-3">
                                    <p><span class="text-info">Escriba en la caja de la izquierda el texto de la plantilla que desea crear.</span></p>
                                    <p>Es importante entender, que los parámetros seán bien interpretados por el envío automático, sus variables deben ir entre {## nombre_variable ##}.</p>
                                    <p>Así por ejemplo en la siguiente estructura:</p>
                                    <pre>
"receivers": [
    {
        ...
        "params": {
            "name": "Jose Juan",
            "surname": "Calvo",
            "code": "000305850667"
        }
    }
]
                                    </pre>
                                    <p>Los parámetros "name", "surname", y "code" serían deben incluirse en la caja de texto como {## name ##}, {## surname ##} y {## code ##}</p>
                                </div>
                            </div>

                        </div>

                        <div id="sms" class="annexFields" style="display: none;">

                            <div class="form-group">
                                {!! Form::label('mensaje', trans('notificationbroker::plantillas/admin_lang.mensaje'), array('class' => 'col-sm-2 control-label')) !!}
                                <div class="col-sm-7">
                                    @if(isset($iguales))
                                        @if(!$iguales)
                                            <div class="alert alert-danger alert-dismissible">
                                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                                <h4><i class="icon fa fa-ban" aria-hidden="true"></i> ¡Atención!</h4>
                                                La plantilla generada no es la misma que la que está editando.
                                                Para que los cambios surjan efecto debe pulsar el botón "Generar la plantilla".
                                            </div>
                                        @else
                                            <div class="alert alert-success alert-dismissible">
                                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                                <h4><i class="icon fa fa-check" aria-hidden="true"></i> ¡Información!</h4>
                                                La plantilla editada y generado son la misma.
                                            </div>
                                        @endif
                                    @endif
                                    {!! Form::textarea('mensaje_sms', ($plantilla->tipo=='sms') ? strip_tags($plantilla->mensaje) : '', array('class' => 'form-control textarea', 'id' => 'mensaje_sms', "maxlength" => 255, "style" => "resize:none; height:500px;")) !!}
                                </div>
                                <div class="col-sm-3">
                                    <p><span class="text-info">Escriba en la caja de la izquierda el texto de la plantilla que desea crear.</span></p>
                                    <p>Es importante entender, que los parámetros seán bien interpretados por el envío automático, sus variables deben ir entre {## nombre_variable ##}.</p>
                                    <p>Así por ejemplo en la siguiente estructura:</p>
                                    <pre>
"receivers": [
    {
        ...
        "params": {
            "name": "Jose Juan",
            "surname": "Calvo",
            "code": "000305850667"
        }
    }
]
                                    </pre>
                                    <p>Los parámetros "name", "surname", y "code" serían deben incluirse en la caja de texto como {## name ##}, {## surname ##} y {## code ##}</p>
                                </div>
                            </div>

                        </div>

                    </div>

                    <div class="box-footer">

                        <a href="{{ url('/admin/plantillas') }}" class="btn btn-default">{{ trans('general/admin_lang.cancelar') }}</a>

                        @if(auth()->user()->can("admin-plantillas-generar"))
                            <a href="javascript:generate();" class="btn btn-success pull-right" style="margin-left: 10px;">{{ trans('notificationbroker::plantillas/admin_lang.generar_plantilla') }}</a>
                        @endif

                        <a href="javascript:save();" class="btn btn-info pull-right">{{ trans('general/admin_lang.save') }}</a>

                        <p class="text-warning text-right" style="margin-top: 20px;">
                            * {{ trans("notificationbroker::plantillas/admin_lang.generar_plantilla_info") }}
                        </p>

                    </div>

                </div>

            </div>

        {!! Form::close() !!}
    </div>

@endsection

@section("foot_page")
    <script type="text/javascript" src="{{ asset("assets/admin/vendor/tinymce/") }}/tinymce.min.js"></script>
    <script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js')}}"></script>

    <script>

        $(document).ready(function () {

            $("#tipo").change(function(e) {
                e.preventDefault();
                setFields();
            });

            tinymce.init({
                selector: "textarea#mensaje_email",
                menubar: true,
                resize:true,
                statusbar: true,
                relative_urls : false,
                remove_script_host : false,
                convert_urls : true,
                height: 500,
                language: 'es',
                plugins: [
                    "fullpage hr pagebreak nonbreaking anchor",
                    "advlist autolink lists link image charmap preview anchor",
                    "searchreplace visualblocks code",
                    "insertdatetime media table contextmenu paste"
                ],
                menubar: 'edit insert view format table tools help',
                toolbar: "undo redo | styleselect | bold italic strikethrough | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media | code",

                file_browser_callback : function(field_name, url, type, win) {

                    openImageController(field_name, '0');

                }
            });

            setFields();
        });

        function setFields() {
            var key = "#" + $("#tipo").val();
            $(".annexFields").css("display","none");
            $(key).css("display","block");
        }

        function save() {
            $("#generar").val('0');
            $("#formTemplate").submit();
        }

        function generate() {
            $("#generar").val('1');
            $("#formTemplate").submit();
        }

        function openImageController(input, only_img) {
            $('#bs-modal-images').modal({
                keyboard: false,
                backdrop: 'static',
                show: 'toggle'
            });

            var style = "width: 100%;padding: 50px; text-align: center;";
            $("#responsibe_images").html('<div id="spinner" class="overlay" style="'+style+'"><i class="fa fa-refresh fa-spin" aria-hidden="true"></i></div>');
            $("#responsibe_images").load("{{ url("admin/media/viewer/") }}/" + input + "/" + only_img);

        }
    </script>

    {!! JsValidator::formRequest('Clavel\NotificationBroker\Requests\AdminPlantillaRequest')->selector('#formTemplate') !!}
@stop
