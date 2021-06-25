@extends('front.layouts.popup')

@section('content')

{!! Form::model($mensaje, $form_data, array('role' => 'form')) !!}
    {!! Form::hidden('user_id', Auth::user()->id, array('id' => 'user_id')) !!}
    {!! Form::hidden('parent_id', $mensaje->parent_id, array('id' => 'parent_id')) !!}
    {!! Form::hidden('asignatura_id', $mensaje->asignatura_id, array('id' => 'asignatura_id')) !!}
    {!! Form::hidden('modulo_id', $mensaje->modulo_id, array('id' => 'modulo_id')) !!}
    {!! Form::hidden('contenido_id', $mensaje->contenido_id, array('id' => 'contenido_id')) !!}
    {!! Form::hidden('visible', true, array('id' => 'visible')) !!}

    <div class="row">
        <div class="col-sm-12">
            <div class="form-group mb-lg">
                {!! Form::label('titulo', trans('elearning::foro/front_lang.titulo'), array('class' => 'control-label')) !!} <span class="asterisk">*</span>
                {!! Form::text('titulo', null, array('placeholder' => trans('elearning::foro/front_lang.titulo'), 'class' => 'form-control', 'id' => 'titulo', "readonly" => !empty($mensaje->parent_id))) !!}
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="form-group mb-lg">
                {!! Form::label('mensaje', trans('elearning::foro/front_lang.contenido'), array('class' => 'control-label')) !!}
                {!! Form::textarea('mensaje', null, array('class' => 'form-control textarea', 'id' => 'mensaje')) !!}
            </div>
        </div>
    </div>

{!! Form::close() !!}

@endsection

@section("foot_page")
    <script type="text/javascript" src="{{ asset("assets/front/vendor/tinymce/tinymce.min.js") }}"></script>
    <script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js')}}"></script>

    <script>
        $(document).ready(function() {
            tinymce.init({
                selector: "textarea.textarea",
                menubar: false,
                resize:false,
                statusbar: false,
                relative_urls : false,
                remove_script_host : false,
                convert_urls : true,
                height: 300,
                plugins: [
                    "advlist autolink lists link image charmap print preview anchor",
                    "searchreplace visualblocks code fullscreen",
                    "insertdatetime media table contextmenu paste emoticons"
                ],
                content_css: [
                    '{{ url('assets/front/css/content_tiny.css') }}',
                ],
                toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link media image | emoticons",
                setup: function (editor) {
                    editor.on('change', function () {
                        tinymce.triggerSave();
                    });
                }
            });

            $(document).on('focusin', function(event) {
                if ($(event.target).closest(".mce-window").length) {
                    event.stopImmediatePropagation();
                }
            });

            $("#frmSendHilo").submit(function(e) {
                $("#btnSaveHilo").addClass("disabled");
                $("#btnSaveHilo").addClass("active");
                if(!$(this).valid()) {
                    $("#btnSaveHilo").removeClass("disabled");
                    $("#btnSaveHilo").removeClass("active");
                    return;
                }
                $.ajax({
                    type: "POST",
                    url: $(this).attr("action"),
                    data: $(this).serialize(),
                    success: function(data) {
                        $("#btnSaveHilo").removeClass("disabled");
                        $("#btnSaveHilo").removeClass("active");
                        if(data!='NOK') {
                            if(data.parent_id != null) {
                                show_tema("{{ url("foro/show") }}/" + data.parent_id);
                            } else {
                                loadMensajes();
                            }
                            $("#modalHilo").modal("hide");
                            $("#successHilo").fadeIn(500, function() {
                                $(this).delay(2000).fadeOut(500);
                            });
                        } else {
                            alert("Error guardando la consulta");
                        }
                    }
                });

                e.preventDefault();
            });
        });

        function saveHilo() {
            $("#frmSendHilo").submit();
        }
    </script>

    {!! JsValidator::formRequest('Clavel\Elearning\Requests\ForoMsgRequest')->selector('#frmSendHilo') !!}

@endsection