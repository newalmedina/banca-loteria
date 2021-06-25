<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header  with-border"><h3
                    class="box-title">{{ trans("elearning::contenidos/admin_lang.info_vid") }}</h3></div>
            <div class="box-body">
                <div class="form-group">
                    {!! Form::label('media_url', trans('elearning::contenidos/admin_lang.media_url'), array('class' => 'col-sm-2 control-label')) !!}
                    <div class="col-sm-10">
                        <div class="input-group">
                            {!! Form::text('media_url', $contenido->media_url, array('placeholder' => trans('elearning::contenidos/admin_lang.media_url'), 'class' => 'form-control', 'id' => 'media_url', 'readonly' => true)) !!}
                            <span class="input-group-btn">
                                        <div id="select_video" class="btn btn-primary btn-file">
                                            {{ trans('elearning::modulos/admin_lang.search_logo') }}
                                        </div>
                                    </span>
                        </div>
                    </div>
                </div>


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
            </div>
        </div>
    </div>
</div>
