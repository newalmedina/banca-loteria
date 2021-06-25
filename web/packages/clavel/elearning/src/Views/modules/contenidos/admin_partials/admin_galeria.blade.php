<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header  with-border"><h3 class="box-title">{{ trans("elearning::contenidos/admin_lang.info_gale") }}</h3></div>
            <div class="box-body">
                <div class="alert alert-warning">
                    {{ trans("elearning::contenidos/admin_lang.txt_media") }} <a href="{{ url("admin/media") }}">{{ trans("elearning::contenidos/admin_lang.gotomedia") }}</a>
                </div>
                <div class="form-group">
                    {!! Form::label('storepath', trans('elearning::contenidos/admin_lang.storepath'), array('class' => 'col-sm-2 control-label')) !!}

                    <div class="col-sm-10">
                        <div class="input-group">
                            <select name="storepath" class="form-control">
                                <option value="">{{ trans("elearning::contenidos/admin_lang.storepath") }}</option>
                                @foreach($subfolders as $folder)
                                    <option value="/media{{$folder}}" @if($contenido->storepath == "/media".$folder) selected @endif >{{$folder}}</option>
                                @endforeach
                            </select>
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