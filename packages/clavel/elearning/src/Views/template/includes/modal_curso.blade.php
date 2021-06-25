<div class="modal fade" id="more_info" tabindex="-1" role="dialog" aria-labelledby="largeModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="largeModalLabel">{{ $asignatura->titulo }}</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-9">
                        @if($asignatura->image!='')
                            <img style="width: 100%; display: block;" src="{{ url("asignaturas/openImage/".$asignatura->id) }}" alt="">
                        @else
                            <div style="height: 150px; width: 100%; display: block; background-color: #F4F4F4; text-align: center">
                                <i class="fa fa-laptop" style="font-size: 64px; margin-top: 50px;" aria-hidden="true"></i>
                            </div>
                        @endif
                        <br clear="all">
                        {!! ($asignatura->descripcion!='') ? $asignatura->descripcion : $asignatura->breve !!}
                    </div>
                    <div class="col-md-3">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <dl>
                                    @if($asignatura->cursoPivot()->count()>0)
                                        <dt>{{ trans("elearning::asignaturas/front_lang.cursos") }}</dt>
                                        <dd>
                                            <?php $nLoop=1; ?>
                                            @foreach($asignatura->cursoPivot()->get() as $curso)
                                                @if($nLoop>1), @endif{{ $curso->nombre }}<?php $nLoop++; ?>
                                            @endforeach
                                        </dd>
                                    @endif
                                    @if($asignatura->academico!='')
                                        <dt>{{ trans("elearning::asignaturas/front_lang.academico") }}</dt>
                                        <dd>{{ $asignatura->academico }}</dd>
                                    @endif
                                    @if($asignatura->caracteristica!='')
                                        <dt>{{ trans("elearning::asignaturas/front_lang.caracteristicas") }}</dt>
                                        <dd>{{ $asignatura->caracteristica }}</dd>
                                    @endif
                                    @if($asignatura->plazas!='')
                                        <dt>{{ trans("elearning::asignaturas/front_lang.plazas") }}</dt>
                                        <dd>{{ $asignatura->plazas }}</dd>
                                    @endif
                                    @if($asignatura->admision!='')
                                        <dt>{{ trans("elearning::asignaturas/front_lang.admision") }}</dt>
                                        <dd>{{ $asignatura->admision }}</dd>
                                    @endif
                                    @if($asignatura->coordinacion!='')
                                        <dt>{{ trans("elearning::asignaturas/front_lang.coordinacion") }}</dt>
                                        <dd>{{ $asignatura->coordinacion }}</dd>
                                    @endif
                                    @if($asignatura->estudiantes!='')
                                        <dt>{{ trans("elearning::asignaturas/front_lang.estudiantes") }}</dt>
                                        <dd>{{ $asignatura->estudiantes }}</dd>
                                    @endif
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans("general/front_lang.cerrar") }}</button>
            </div>
        </div>
    </div>
</div>
