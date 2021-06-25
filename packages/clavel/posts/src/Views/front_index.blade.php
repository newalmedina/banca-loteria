@extends('front.layouts.default')

@section('title')
    @parent {{ $page_title }}
@stop

@section('head_page')

    <link href="{{ asset("assets/front/css/posts.css") }}" rel="stylesheet" type="text/css" />
@stop

@section('breadcrumb')
    <li class="active">{{ $page_title }}</li>
@stop

@section('content')

    <!-- Page Content -->
    <div class="container paddingTop30">

        @if($selected_tag!='')
            <div class="row">
                <div class="col-md-8">
                    <h5>{{ trans("posts::front_lang.filtradopor") }}: <span class="text-success">{{ $tag->tag }}</span> <a href="{{ url("posts") }}"><i class="fa fa-times text-danger" aria-hidden="true"></i></a></h5>
                </div>
            </div>
        @endif

        <div class="row">
            <!-- Blog Entries Column -->
            <div class="col-md-8">
            @if(count($posts)==0)
                {{ trans("posts::front_lang.not_found") }}
            @else

                @foreach($posts as $post)
                    <!-- Blog Post -->
                    <div class="thumbnail post-list-element">
                        @if($post->images->count()>0)
                            @foreach($post->images as $image)
                                <img src="{{ $image->path }}" alt="{!! $post->title !!}">
                            @endforeach

                        @endif
                        <div class="caption">
                            <a href="{{ url("posts/post/".$post->url_seo) }}"><h3>{!! $post->title !!}</h3></a>
                            <strong>{{ trans("posts::front_lang.posted_on") }}</strong>&nbsp;&nbsp;<span><i class="fa fa-calendar" aria-hidden="true"></i>&nbsp;&nbsp;{{ $post->datePostFormatted }} </span>

                            <p>
                                {!! $post->lead_new !!}
                            </p>
                        </div>
                        <div class="post-footer text-muted">
                            @if(!empty($post->author))
                                <span class="mr-md"><i class="fa fa-user" aria-hidden="true"></i>&nbsp;&nbsp;{{ $post->author->userProfile->fullName }} </span>
                            @endif

                            <div class="row">
                                <div class="col-md-10">
                                    @if($post->tags()->actives()->count()>0)
                                    <span>
                                        <i class="fa fa-tag" aria-hidden="true"></i>
                                        @foreach($post->tags()->actives()->get() as $key=>$value)
                                            @if($key>0){{ ", " }}@endif
                                            <a href="{{ url("posts/".$value->id) }}">{{ $value->tag }}</a>
                                        @endforeach
                                    </span>
                                    @endif
                                </div>
                                <div class="col-md-2">
                                    <a href="{{ url("posts/post/".$post->url_seo) }}" class="btn btn-info pull-right">{{ trans("posts::front_lang.continue_reading") }} &rarr;</a>
                                </div>
                            </div>

                        </div>
                    </div>

                @endforeach

                <!-- Pagination -->
                {!! $posts->links('front.includes.pagination') !!}
            @endif


            </div>

            <!-- Sidebar Widgets Column -->
            <div class="col-md-4">
                {{ Form::open(array('url' => 'posts', 'method' => 'POST', 'id' => 'formSearch')) }}

                <!-- Search Widget -->
                <div class="panel panel-default">
                    <h5 class="panel-heading">{{ trans("posts::front_lang.search") }}</h5>
                    <div class="panel-body">
                        <div class="input-group">
                            <input type="text" id="q" name="q" class="form-control"
                                   placeholder="{{ trans("posts::front_lang.search_for") }}" value="{{ $q }}">
                            <span class="input-group-btn">
                              <button class="btn btn-info" type="button" onclick="javascript:$('#formSearch').submit();">{{ trans("posts::front_lang.go") }}</button>
                            </span>
                        </div>
                    </div>
                </div>
                {!! Form::close() !!}
                <!-- Categories Widget -->
                <div class="panel panel-default">
                    <h5 class="panel-heading">{{ trans("posts::front_lang.categories") }}</h5>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <div id="tegcloud">
                                    @if($tags->count()>0)
                                        @foreach($tags as $tag)
                                            <a href="{{ url("posts/".$tag->id) }}">{{ $tag->tag }}</a>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <!-- /.row -->

    </div>
    <!-- /.container -->

@endsection

@section("foot_page")
    <script>
        $(document).ready(function(){
            var classes = ["teg-1", "teg-2", "teg-3", "teg-4", "teg-5"];

            $("#tegcloud a").each(function(){
                $(this).addClass(classes[~~(Math.random()*classes.length)]);
            });

            $("#formSearch").submit(function(e) {

                var q = $("#q").val();
                if(q.length == 0 || q.length > 3) {
                    return true;
                }
                return false;
            });
        });
    </script>
@stop
