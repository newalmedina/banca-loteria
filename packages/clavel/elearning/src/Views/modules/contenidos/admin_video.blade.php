<div class="box box-primary">
    <div class="box-header  with-border"><h3 class="box-title">{{ trans("elearning::contenidos/admin_lang.info_video") }}</h3></div>
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
                        {!! Form::label('myfile', trans('elearning::contenidos/admin_lang.MP4'), array('class' => 'col-sm-2 control-label')) !!}
                        <div class="col-sm-10">
                            <div class="input-group">
                                <input type="text" class="form-control" id="nombrefichero" readonly>
                                <span class="input-group-btn">
                                        <div class="btn btn-primary btn-file">
                                            {{ trans('elearning::asignaturas/admin_lang.search_logo') }}
                                            {!! Form::file('myfile_'.$key.'[]',array('id'=>'myfile', 'multiple'=>true)) !!}
                                        </div>
                                </span>
                            </div>
                            <div id="remove" style="margin-top: 5px; @if($contenido->{'mp4:'.$key}=='') display: none; @endif">
                                @if($contenido->{'mp4:'.$key}!='')
                                    <div id="nombre_archivo" style="margin-bottom:10px;">
                                        <strong>{{ trans("elearning::asignaturas/admin_lang.nombre_archivo") }}:</strong> {{ $contenido->{'mp4:'.$key} }}
                                    </div>
                                @endif
                                <a class="btn btn-danger" href="javascript:remove_image('');"><i class="fa fa-times" aria-hidden="true"></i> {{ trans('elearning::asignaturas/admin_lang.delete_image') }}</a>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('myfile', trans('elearning::contenidos/admin_lang.WEBM'), array('class' => 'col-sm-2 control-label')) !!}
                        <div class="col-sm-10">
                            <div class="input-group">
                                <input type="text" class="form-control" id="nombrefichero1" readonly>
                                <span class="input-group-btn">
                                        <div class="btn btn-primary btn-file">
                                            {{ trans('elearning::asignaturas/admin_lang.search_logo') }}
                                            {!! Form::file('myfile_'.$key.'[]',array('id'=>'myfile1', 'multiple'=>true)) !!}
                                        </div>
                                </span>
                            </div>
                            <div id="remove1" style="margin-top: 5px; @if($contenido->{'webm:'.$key}=='') display: none; @endif">
                                @if($contenido->{'webm:'.$key}!='')
                                    <div id="nombre_archivo1" style="margin-bottom:10px;">
                                        <strong>{{ trans("elearning::asignaturas/admin_lang.nombre_archivo") }}:</strong> {{ $contenido->{'webm:'.$key} }}
                                    </div>
                                @endif
                                <a class="btn btn-danger" href="javascript:remove_image('1');"><i class="fa fa-times" aria-hidden="true"></i> {{ trans('elearning::asignaturas/admin_lang.delete_image') }}</a>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('myfile', trans('elearning::contenidos/admin_lang.VTT'), array('class' => 'col-sm-2 control-label')) !!}
                        <div class="col-sm-10">
                            <div class="input-group">
                                <input type="text" class="form-control" id="nombrefichero2" readonly>
                                <span class="input-group-btn">
                                        <div class="btn btn-primary btn-file">
                                            {{ trans('elearning::asignaturas/admin_lang.search_logo') }}
                                            {!! Form::file('myfile_'.$key.'[]',array('id'=>'myfile2', 'multiple'=>true)) !!}
                                        </div>
                                </span>
                            </div>
                            <div id="remove2" style="margin-top: 5px; @if($contenido->{'vtt:'.$key}=='') display: none; @endif">
                                @if($contenido->{'vtt:'.$key}!='')
                                    <div id="nombre_archivo2" style="margin-bottom:10px;">
                                        <strong>{{ trans("elearning::asignaturas/admin_lang.nombre_archivo") }}:</strong> {{ $contenido->{'vtt:'.$key} }}
                                    </div>
                                @endif
                                <a class="btn btn-danger" href="javascript:remove_image('2');"><i class="fa fa-times" aria-hidden="true"></i> {{ trans('elearning::asignaturas/admin_lang.delete_image') }}</a>
                            </div>
                        </div>
                    </div>
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
