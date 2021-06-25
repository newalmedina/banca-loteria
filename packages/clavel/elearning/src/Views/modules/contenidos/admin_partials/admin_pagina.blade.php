<div class="box box-primary">
    <div class="box-header  with-border"><h3 class="box-title">{{ trans("elearning::contenidos/admin_lang.info_page") }}</h3></div>

    <div class="box-body">
        <div class="form-group">
            {!! Form::label('pantalla_completa', trans('elearning::contenidos/admin_lang.pantalla_completa'), array('class' => 'col-sm-2 control-label')) !!}

            <div class="col-md-10">
                <div class="radio-list">
                    <label class="radio-inline">
                        {!! Form::radio('pantalla_completa', '0', true, array('id'=>'pantalla_completa_0')) !!}
                        {{ Lang::get('general/admin_lang.no') }}</label>
                    <label class="radio-inline">
                        {!! Form::radio('pantalla_completa', '1', false, array('id'=>'pantalla_completa_1')) !!}
                        {{ Lang::get('general/admin_lang.yes') }} </label>
                </div>
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('descargar_pdf', trans('elearning::contenidos/admin_lang.descargar_pdf'), array('class' => 'col-sm-2 control-label')) !!}

            <div class="col-md-10">
                <div class="radio-list">
                    <label class="radio-inline">
                        {!! Form::radio('descargar_pdf', '0', true, array('id'=>'descargar_pdf_0')) !!}
                        {{ Lang::get('general/admin_lang.no') }}</label>
                    <label class="radio-inline">
                        {!! Form::radio('descargar_pdf', '1', false, array('id'=>'descargar_pdf_1')) !!}
                        {{ Lang::get('general/admin_lang.yes') }} </label>
                </div>
            </div>
        </div>

        <div id="mas_info_pdf" @if($contenido->descargar_pdf=='0') style="display: none;" @endif>
            <div class="form-group">
                {!! Form::label('generar_pdf', trans('elearning::contenidos/admin_lang.generar_pdf'), array('class' => 'col-sm-2 control-label')) !!}

                <div class="col-md-10">
                    <div class="radio-list" style="float: left; margin-bottom: 10px;">
                        <label class="radio-inline">
                            {!! Form::radio('generar_pdf', '0', true, array('id'=>'generar_pdf_0')) !!}
                            {{ Lang::get('general/admin_lang.no') }}</label>
                        <label class="radio-inline">
                            {!! Form::radio('generar_pdf', '1', false, array('id'=>'generar_pdf_1')) !!}
                            {{ Lang::get('general/admin_lang.yes') }} </label>
                    </div>
                    <div class="direct-chat-text" style=" float: left; margin-top: 0px; margin-left: 20px;">
                        {{trans("elearning::contenidos/admin_lang.geracion_info") }}
                    </div>
                </div>
            </div>

            <div id="pdf_archivo" class="form-group" @if($contenido->generar_pdf=='1') style="display: none;" @endif>
                {!! Form::hidden('delete_photo', 0, array('id' => 'delete_photo')) !!}
                {!! Form::label('myfile', trans('elearning::contenidos/admin_lang.generar_pdf'), array('class' => 'col-sm-2 control-label')) !!}
                <div class="col-sm-10">
                    <div class="input-group">
                        <input type="text" class="form-control" id="nombrefichero" readonly>
                        <span class="input-group-btn">
                            <div class="btn btn-primary btn-file">
                                {{ trans('elearning::contenidos/admin_lang.search_logo') }}
                                {!! Form::file('myfile[]',array('id'=>'myfile', 'multiple'=>true)) !!}
                            </div>
                        </span>
                    </div>
                    <div id="remove" style="margin-top: 5px; @if($contenido->pdf_archivo=='') display: none; @endif">
                        @if($contenido->pdf_archivo!='')
                            <div id="nombre_archivo" style="margin-bottom:10px;">
                                <strong>{{ trans("elearning::contenidos/admin_lang.nombre_archivo") }}:</strong> {{ $contenido->pdf_archivo }}
                            </div>
                        @endif
                        <a id="display_image" href="{{ url('contenido/detalle-contenido/openPDF/'.$contenido->id) }}" target="_blank" class="btn btn-primary"><i class="fa fa-search" aria-hidden="true"></i> {{ trans('elearning::contenidos/admin_lang.view_image') }}</a>
                        <a class="btn btn-danger" href="javascript:remove_image();"><i class="fa fa-times" aria-hidden="true"></i> {{ trans('elearning::contenidos/admin_lang.delete_image') }}</a>
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
                    <a href="#tabpagina_{{ $key }}" data-toggle="tab">
                        {{ $valor["idioma"] }}
                        @if($nX==1)- <span class="text-success">{{ trans('general/admin_lang._defecto') }}</span>@endif
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
                <div id="tabpagina_{{ $key }}" class="tab-pane @if($nX==1) active @endif">
                    <div class="form-group">
                        {!! Form::label('userlang['.$key.'][contenido]', trans('elearning::modulos/admin_lang.descripcion'), array('class' => 'col-sm-2 control-label')) !!}
                        <div class="col-sm-10">
                            {!! Form::textarea('userlang['.$key.'][contenido]', $contenido->{'contenido:'.$key} , array('class' => 'form-control textarea', 'id' => 'contenido_'.$key)) !!}
                        </div>
                    </div>
                </div>
                <?php
                $nX++;
                ?>
            @endforeach
        </div>
    </div>
</div>

@section('foot_page')
    @parent

    <script>
        $(document).ready(function() {
            $("#descargar_pdf_0").click(function(e) {
                $("#mas_info_pdf").slideUp(500);
                $("#generar_pdf_0").prop("checked", true);
                $("#pdf_archivo").css("display","block");
                remove_image();
            });

            $("#descargar_pdf_1").click(function(e) {
                $("#mas_info_pdf").slideDown(500);
            });

            $("#generar_pdf_0").click(function(e) {
                $("#pdf_archivo").slideDown(500);
            });

            $("#generar_pdf_1").click(function(e) {
                $("#pdf_archivo").slideUp(500);
                remove_image();
            });

            $("#myfile").change(function(){
                getFileName();
            });
        });

        function getFileName() {
            $('#nombrefichero').val($('#myfile')[0].files[0].name);
            $("#delete_photo").val('1');
            $("#remove").css("display","block");
            $("#nombre_archivo").css("display","none");
            $("#display_image").addClass("disabled");
        }

        function remove_image() {
            $("#display_image").addClass("disabled");
            $("#remove").css("display","none");
            $('#nombrefichero').val('');
            $('#myfile').val("");
            $("#delete_photo").val('1');
        }
    </script>
@stop
