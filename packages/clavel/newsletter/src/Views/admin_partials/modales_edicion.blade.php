<div class="modal modal-add-info fade in" id="bs-modal-add-info">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button aria-label="Close" data-dismiss="modal" class="close" type="button"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title">{{ trans('Newsletter::admin_lang.formulario_contenido') }}</h4>
            </div>
            <div id="content-add-info" class="modal-body"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans("Newsletter::admin_lang.cerrar") }}</button>
                <button type="button" class="btn btn-primary" onclick="save_field();">{{ trans("Newsletter::admin_lang.guardar") }}</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>

<div class="modal modal-add-style fade in" id="modifyStyle">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button aria-label="Close" data-dismiss="modal" class="close" type="button"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title">{{ trans('Newsletter::admin_lang_template.modificar_fila') }}</h4>
            </div>
            <div id="content-add-info" class="modal-body">
                <input type="hidden" id="row_file" name="row_file" value="">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('padding', trans('Newsletter::admin_lang_template.padding'), array('class' => 'col-sm-4 control-label', 'readonly' => true)) !!}
                            <div class="col-md-8">
                                <select name="padding" id="padding" class="form-control">
                                    @for($nX=0; $nX<21; $nX++)
                                        <option value="{{$nX}}px">{{ $nX }}px</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <br clear="all">

                <h5 class="text-primary">{{ trans("Newsletter::admin_lang_template.Background") }}</h5>
                <hr>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('inicial', trans('Newsletter::admin_lang_template.inicial'), array('class' => 'col-sm-4 control-label', 'readonly' => true)) !!}
                            <div class="col-md-8">
                                <div class="input-group my-colorpicker2">
                                    <div class="input-group-addon">
                                        <i aria-hidden="true"></i>
                                    </div>
                                    {!! Form::text('inicial', null, array('placeholder' => trans('Newsletter::admin_lang_template.inicial'), 'class' => 'form-control color-picker', 'id' => 'inicial')) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('final', trans('Newsletter::admin_lang_template.final'), array('class' => 'col-sm-4 control-label', 'readonly' => true)) !!}
                            <div class="col-md-8">
                                <div class="input-group my-colorpicker2">
                                    <div class="input-group-addon">
                                        <i aria-hidden="true"></i>
                                    </div>
                                    {!! Form::text('final', null, array('placeholder' => trans('Newsletter::admin_lang_template.final'), 'class' => 'form-control color-picker', 'id' => 'final')) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <br clear="all">

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('radial', trans('Newsletter::admin_lang_template.radial'), array('class' => 'col-sm-4 control-label', 'readonly' => true)) !!}
                            <div class="col-md-8">
                                <select name="radial" id="radial" class="form-control">
                                    <option value="to top">{{ trans("Newsletter::admin_lang_template.top") }}</option>
                                    <option value="to left">{{ trans("Newsletter::admin_lang_template.left") }}</option>
                                    <option value="to right">{{ trans("Newsletter::admin_lang_template.right") }}</option>
                                    <option value="to bottom">{{ trans("Newsletter::admin_lang_template.bottom") }}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <br clear="all">

                <h5 class="text-primary">{{ trans("Newsletter::admin_lang_template.Boder_inferior") }}</h5>
                <hr>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('tamanyo', trans('Newsletter::admin_lang_template.tamanyo'), array('class' => 'col-sm-4 control-label', 'readonly' => true)) !!}
                            <div class="col-md-8">
                                <select name="tamanyo" id="tamanyo" class="form-control">
                                    @for($nX=0; $nX<21; $nX++)
                                        <option value="{{$nX}}px">{{ $nX }}px</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('border_color_2', trans('Newsletter::admin_lang_template.border_color_2'), array('class' => 'col-sm-4 control-label', 'readonly' => true)) !!}
                            <div class="col-md-8">
                                <div class="input-group my-colorpicker2">
                                    <div class="input-group-addon">
                                        <i aria-hidden="true"></i>
                                    </div>
                                    {!! Form::text('border_color_2', null, array('placeholder' => trans('Newsletter::admin_lang_template.border_color_2'), 'class' => 'form-control color-picker', 'id' => 'border_color_2')) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans("Newsletter::admin_lang.cerrar") }}</button>
                <button type="button" class="btn btn-primary" onclick="save_formato();">{{ trans("Newsletter::admin_lang.guardar") }}</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>

<div class="modal modal-add-content-hf fade in" id="modalContenidos">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button aria-label="Close" data-dismiss="modal" class="close" type="button"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title">{{ trans('Newsletter::admin_lang_template.contenidos') }}</h4>
            </div>
            <div id="content-add-info-hf" class="modal-body">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans("Newsletter::admin_lang.cerrar") }}</button>
                <button type="button" class="btn btn-primary" onclick="save_contenido();">{{ trans("Newsletter::admin_lang.guardar") }}</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
