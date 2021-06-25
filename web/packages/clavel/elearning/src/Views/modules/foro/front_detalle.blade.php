<br class="clean"/>
<div class="row mb-lg">
    <div id="comentarios_hilo" class="col-xs-12">
        <?php $esTema = true; ?>
        @foreach($mensajes as $mensaje)
            <section class="panel-forum" id="id_{{ $mensaje->id }}">
                <div class="panel-forum-group hidden-lg hidden-md visible-sm visible-xs">
                    <div class="row">
                        <div class="col-xs-2 profile-forum-small">
                            @if(!empty($mensaje->user->userProfile->photo))
                                <img src="{{ url('profile/getphoto/'.$mensaje->user->userProfile->photo) }}" class="profile-pic" alt="">
                            @else
                                <img src="{{ asset("assets/front/img/inicio/icon-user.svg") }}" class="profile-pic" alt="">
                            @endif
                        </div>
                        <div class="col-xs-10">
                            {{ $mensaje->user->userProfile->full_name }}<br>
                            @if($mensaje->user->roles()->count()>0)
                                {{ $mensaje->user->roles[0]->display_name }}<br>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="panel-forum-group {{ $esTema ? "" : "forum_answer" }}">
                    <div class="profile-forum hidden-sm hidden-xs">
                        <div class="clearfix"></div>
                        @if($mensaje->user->userProfile->photo!='')
                            <img src="{{ url('profile/getphoto/'.$mensaje->user->userProfile->photo) }}" class="profile-pic" alt="">
                        @else
                            <img src="{{ asset("assets/front/img/inicio/icon-user.svg") }}" class="profile-pic" alt="">
                        @endif
                        <br>
                        <strong>{{ $mensaje->user->userProfile->full_name }}</strong><br>
                        @if($mensaje->user->roles()->count()>0)
                            <span>{{ $mensaje->user->roles[0]->display_name }}</span><br>
                        @endif
                    </div>
                    <div class="mensaje-forum">
                        <div class="row">
                            <div class="col-md-11">
                                <div class="title-msg-forum mb-lg">
                                    <h4 class="text-info">
                                        <img class="mr-md"  alt="" src='{{ asset("assets/front/img/titol.png") }}'>{{ $mensaje->titulo }}
                                        <br>
                                        <img class="mr-md"  alt="" src='{{ asset("assets/front/img/hora.png") }}'>{{ $mensaje->creacion }}
                                            / {{ $mensaje->creacion_humanos }}
                                    </h4>
                                </div>
                                {!! $mensaje->mensaje !!}
                            </div>

                            <div class="col-md-1">
                                <div class="pull-right text-center">
                                    @if(auth()->user()->id == $mensaje->user_id && auth()->user()->can("frontend-foro-update"))
                                        <a href="javascript:modify_info('{{ $mensaje->id }}');">
                                            <img class="mb-sm"  alt="" src='{{ asset("assets/front/img/edit.png") }}'></a><br>
                                    @endif
                                    @if((auth()->user()->id == $mensaje->user_id && auth()->user()->can("frontend-foro-delete-self")) || auth()->user()->can("frontend-foro-delete"))
                                        <a href="javascript:delete_message('{{ $mensaje->id }}');">
                                            <img src='{{ asset("assets/front/img/eliminar.png") }}' alt=''></a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <?php $esTema = false; ?>
        @endforeach
    </div>
</div>

<div class="row mb-lg">
    <div class="col-md-12 text-right">
        @if(auth()->user()->can("frontend-foro-create"))
            <button type="button" class="mb-xs mt-xs mr-xs btn btn-info" onclick="openCreateForm({{$mensajes[0]->id}})"
                    id="NuevoHilo2">
                {{ trans("elearning::foro/front_lang.reponder_en_el_hilo") }}
            </button>
        @endif

        <a href="javascript:loadMensajes()"
           class="mb-xs mt-xs mr-xs btn btn-primary">{{ trans("elearning::foro/front_lang.volver") }}</a>
    </div>
    <div class="col-md-6">
        <div class="pull-right">{!! $mensajes->appends(Illuminate\Support\Facades\Request::except('page'))->render() !!}</div>
    </div>
</div>
