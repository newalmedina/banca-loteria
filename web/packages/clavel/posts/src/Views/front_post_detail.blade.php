@extends('front.layouts.default')

@section('title')
    @parent {{ $post->title }}
@stop

@section('metas')
    <meta name="title" content="{!! $post->meta_title !!}">
    <meta name="description" content="{!! $post->meta_content !!}">
@stop


@section('head_page')
    <link href="{{ asset("assets/front/css/posts.css") }}" rel="stylesheet" type="text/css" />
    <link href="https://vjs.zencdn.net/7.6.0/video-js.css" rel="stylesheet">
@stop

@section('breadcrumb')
    <li class="active"><a href="{{ route('posts') }}">{{ trans("posts::front_lang.newpost") }}</a></li>
    <li>{{ substr(strip_tags($post->title),0, 20) }}</li>

@stop

@section('content')

    <!-- Page Content -->
    <div class="container">

        <!-- Page Heading/Breadcrumbs -->
        <h1 class="mt-2 mb-2">{!! $post->title !!}</h1>

        <div class="row">

            <!-- Post Content Column -->
            <div class="col-lg-12">

                <!-- Preview Image -->
                @if($post->images->count()>0)
                    @foreach($post->images as $image)
                        <img class="img-fluid rounded" src="{{ $image->path }}" alt="{!! $post->title !!}">
                    @endforeach
                @endif

                <hr>
                <div class="row">
                    <div class="col-md-6">
                        <!-- Date/Time -->
                        <p><strong>{{ trans("posts::front_lang.posted_on") }}</strong>&nbsp;&nbsp;<span><i class="fa fa-calendar" aria-hidden="true"></i>&nbsp;&nbsp;{{ $post->datePostFormatted }} </span><br></p>
                    </div>
                    <div class="col-md-6" >
                        @if($post->has_comment=='1')
                            <span class="pull-right"><i class="fa fa-comments" aria-hidden="true"></i> <a href="javascript:gotocomments();">{{ $post->comments()->count() }} {{ trans("posts::front_lang.comentarios") }}</a></span>
                        @endif
                    </div>
                </div>

                <!-- Post Content -->

                {!! $post->body !!}


                @if(!empty($post->author))
                    <?php
                        $profile = $post->author->userProfile;
                    ?>
                    <div class="post-block mt-4 pt-2 post-author">
                        <h4 class="mb-3">{{ trans("posts::front_lang.author") }}</h4>
                        <div class="img-thumbnail img-thumbnail-no-borders d-block pb-3">
                            @if(!empty($profile->photo))
                                <img class="avatar" alt="" src='{{ url('profile/getphoto/'.$profile->photo) }}' id='image_ouptup' width='100%'>
                            @else
                                <img class="avatar" alt="" src="{{ asset("assets/front/img/man-user.png") }}">
                            @endif
                        </div>
                        <p>
                            <strong class="name">
                                {{ $profile->fullName }}
                            </strong>
                        </p>
                        <p>{{ $profile->bio }}</p>

                        <ul class="social-icons">
                            @if(!empty($profile->facebook))
                                <li><a href="https://www.facebook.com/{{ $profile->facebook }}" target="_blank"><span class="fa fa-facebook" aria-hidden="true"></span></a></li>
                            @endif
                            @if(!empty($profile->twitter))
                                <li><a href="http://www.twitter.com/{{ $profile->twitter }}" target="_blank"><span class="fa fa-twitter" aria-hidden="true"></span></a></li>
                            @endif
                            @if(!empty($profile->linkedin))
                                <li><a href="https://www.linkedin.com/{{ $profile->linkedin }}" target="_blank"><span class="fa fa-linkedin" aria-hidden="true"></span></a></li>
                            @endif
                            @if(!empty($profile->youtube))
                                <li><a href="https://www.youtube.com/{{ $profile->youtube }}" target="_blank"><span class="fa fa-youtube" aria-hidden="true"></span></a></li>
                            @endif
                        </ul>

                    </div>
                @endif


                @if($post->tags()->count()>0)
                <hr>
                <div class="row  mb-lg">
                    <div class="col-md-12">
                        <span>
                            <i class="fa fa-tag" aria-hidden="true"></i>
                            @foreach($post->tags()->actives()->get() as $key=>$value)
                                @if($key>0){{ ", " }}@endif
                                <a href="{{ url("posts/".$value->id) }}">{{ $value->tag }}</a>
                            @endforeach
                        </span>
                    </div>
                </div>

                @endif

                @if($post->has_shared=='1')
                    <div class="post-block post-share">
                        <h5 class="heading-primary"><i class="fa fa-share" aria-hidden="true"></i> {{ trans("posts::front_lang.compartir") }}</h5>

                        <div class="addthis_inline_share_toolbox_88gb"></div>



                    </div>
                    <hr>
                @endif

                @if($post->has_comment=='1')
                    <h4 id="post-block-comments" class="heading-primary mb-3 mt-4"><i class="fa fa-comments" aria-hidden="true"></i>  {{ trans("posts::front_lang.comentarios") }} ({{ $post->comments()->count() }})</h4>
                    @if($post->comments()->count()>0)
                        <ul class="comments mb-lg">
                        @foreach($post->comments()->onlyParents()->orderBy("created_at","DESC")->limit($paginationComments)->get() as $comment)
                            <li>
                                <div class="comment" id="comment_{{ $comment->id }}">
                                    <div class="img-thumbnail img-thumbnail-no-borders d-none d-sm-block">
                                        @php
                                            $userComment = null;
                                            if(isset($comment->user_id)) {
                                                $userComment = App\Models\User::find($comment->user_id);
                                            }
                                        @endphp
                                        @if(!empty($userComment) && !empty($userComment->userProfile->photo))
                                            <img class="avatar" alt="" src='{{ url('profile/getphoto/'.$userComment->userProfile->photo) }}' id='image_ouptup' width='100%'>
                                        @else
                                            <img class="avatar" alt="" src="{{ asset("assets/front/img/man-user.png") }}">
                                        @endif
                                    </div>
                                    <div class="comment-block">
                                        <div class="comment-arrow"></div>
                                        <span class="comment-by">
                                            <strong>{{ $comment->user }}</strong>
                                            <span class="pull-right">
                                                <span> <a href="javascript:replyComment('{{ $comment->id }}', '{{ $comment->user }}' );"><i class="fa fa-reply" aria-hidden="true"></i> {{ trans("posts::front_lang.reply") }}</a></span>
                                            </span>
                                        </span>
                                        <p>{!! nl2br($comment->comment) !!}</p>
                                        <span class="date pull-right">{!! $comment->date_comment_formatted !!}</span>
                                    </div>
                                </div>

                                @if($post->comments()->children($comment->id)->count()>0)
                                    <ul class="comments reply">
                                    @foreach($post->comments()->children($comment->id)->orderBy("created_at","ASC")->get() as $reply)
                                        <li>
                                            <div class="comment" id="reply_{{ $reply->id }}">
                                                <div class="img-thumbnail img-thumbnail-no-borders d-none d-sm-block">
                                                    @php
                                                        $userComment = null;
                                                        if(isset($reply->user_id)) {
                                                            $userReply = App\Models\User::find($reply->user_id);
                                                        }
                                                    @endphp
                                                    @if(!empty($userReply) && !empty($userReply->userProfile->photo))
                                                        <img class="avatar" alt="" src='{{ url('profile/getphoto/'.$userReply->userProfile->photo) }}' id='image_ouptup' width='100%'>
                                                    @else
                                                        <img class="avatar" alt="" src="{{ asset("assets/front/img/man-user.png") }}">
                                                    @endif
                                                </div>
                                                <div class="comment-block">
                                                    <div class="comment-arrow"></div>
                                                    <span class="comment-by">
                                                        <strong>{{ $reply->user }}</strong>
                                                        <span class="pull-right">
                                                            <span> <a href="javascript:replyComment('{{ $comment->id }}', '{{ $reply->user }}' );"><i class="fa fa-reply" aria-hidden="true"></i> {{ trans("posts::front_lang.reply") }}</a></span>
                                                        </span>
                                                    </span>
                                                    <p>{!! nl2br($reply->comment) !!}</p>
                                                    <span class="date pull-right">{!! $reply->date_comment_formatted !!}</span>
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                    </ul>
                                @endif
                            </li>
                        @endforeach
                        </ul>
                    @endif

                    @if($post->has_comment_only_user=='0' || ($post->has_comment_only_user=='1' && auth()->check()))
                        @include('admin.includes.errors')
                        @include('admin.includes.success')

                        <!-- Comments Form -->
                        <div class="mt-lg mb-lg">
                            <div class="card my-4 mt-5 mb-5 ">
                                <h5 class="card-header">{{ trans("posts::front_lang.dejacomentario") }}</h5>
                                <div class="card-body">
                                    {!! Form::model($user, $form_data, array('role' => 'form')) !!}
                                        {!!  Form::hidden('user_id', (isset($user)) ? $user->id : null, array('id' => 'user_id')) !!}
                                        {!!  Form::hidden('post_id', $post->id, array('id' => 'post_id')) !!}
                                        {!!  Form::hidden('parent_id', 0, array('id' => 'parent_id')) !!}
                                        @if(isset($user->id))
                                        @else
                                            <div class="row">
                                                <div class="form-group">
                                                    <div class="col-md-6">
                                                        {!! Form::label('fullname', trans("posts::front_lang.nombre")." *", array('class' => 'control-label', 'readonly' => true)) !!}
                                                        {!! Form::text('fullname', null, array('class' => 'form-control')) !!}
                                                    </div>
                                                    <div class="col-md-6">
                                                        {!! Form::label('email', trans("posts::front_lang.email")." *", array('class' => 'control-label', 'readonly' => true)) !!}
                                                        {!! Form::text('email', null, array('class' => 'form-control')) !!}
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        <div class="form-group">
                                                {!! Form::label('message', trans("posts::front_lang.comentario")." *", array('class' => 'control-label', 'readonly' => true)) !!}
                                                {!! Form::textarea('message', null, array('class' => 'form-control', 'style' => 'resize:none;', 'id'=>'message')) !!}
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <input type="submit" value="{{ trans("posts::front_lang.comentar") }}" class="btn btn-primary pull-right" data-loading-text="Loading...">
                                                <a href="javascript:newcomment();" class="btn btn-info">{{ trans("posts::front_lang.nuevocomeatrio") }}</a>
                                            </div>
                                        </div>
                                    {!! Form::close() !!}
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="post-block post-leave-comment">
                            <h3 class="heading-primary">{{ trans("posts::front_lang.user_register") }}</h3>
                        </div>
                    @endif
                @endif


            </div>



        </div>
        <!-- /.row -->

    </div>
    <!-- /.container -->
@endsection

@section('foot_page')
    <script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js')}}"></script>
    <script src='https://vjs.zencdn.net/7.6.0/video.js'></script>
    <script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-5dcac17029f57215"></script>


    <script>
        $(document).ready(function(){
            var classes = ["teg-1", "teg-2", "teg-3", "teg-4", "teg-5"];

            $("#tegcloud a").each(function(){
                $(this).addClass(classes[~~(Math.random()*classes.length)]);
            });

            $("#posts-form").submit(function( event ) {
                if(! $(this).valid()) return false;

                return true;
            });


        });

        function gotocomments() {
            $('html,body').animate({
                scrollTop: $("#post-block-comments").offset().top - 100}, 'slow');
        }

        function newcomment() {
            $("#parent_id").val("0");
            $("#message").val("");
            gotocomments();
        }

        function replyComment(comentario_id,usuario) {
            $("#parent_id").val(comentario_id);
            $("#message").val("@" + usuario);
            gotocomments();
        }


        var vids = document.getElementsByTagName('video')
        // vids is an HTMLCollection
        for( var i = 0; i < vids.length; i++ ){
            var fuentes = vids.item(i).getElementsByTagName("source");
            for( var j = 0; j < fuentes.length; j++ ){
                var fuente = fuentes.item(j);
                if(!fuentes.item(j).hasAttribute('type')) {
                    fuentes.item(j).setAttribute('type', 'video/mp4');
                }
            }

            vids.item(i).classList.add("video-js");
            vids.item(i).classList.add("vjs-big-play-centered");

            if(!vids.item(i).hasAttribute('data-setup')) {
                vids.item(i).setAttribute('data-setup', '{}');
                vids.item(i).setAttribute('playsinline', '');
                vids.item(i).setAttribute('controlsList', 'nodownload');
                vids.item(i).setAttribute('controls', 'true');
                vids.item(i).setAttribute('preload', 'true');
                if((vids.item(i).style.getPropertyValue('width') === "300" &&
                    vids.item(i).style.getPropertyValue('height') === "150") ||
                    (vids.item(i).getAttribute('width') === "300" &&
                    vids.item(i).getAttribute('height'))

                ) {
                    vids.item(i).style.setProperty('width', '100%', '');
                    vids.item(i).style.setProperty('height', '100%', '');
                }

                vids.item(i).style.setProperty('background-color', 'transparent', '');

            }
        }

    </script>
    {!! JsValidator::formRequest('Clavel\Posts\Requests\FrontPostCommentRequest')->selector('#posts-form') !!}

@stop
