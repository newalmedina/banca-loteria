    <style>
        DIV.image_block {
            border: solid 1px #C0C0C0;
            margin-bottom: 25px;
        }

        DIV.comment_img {
            padding:20px 20px;
            background-color:#f1f1f1;
            text-align:left;
            border-top:solid 1px #C0C0C0;
            -moz-box-shadow:    inset 0 0 10px #C0C0C0;
            -webkit-box-shadow: inset 0 0 10px #C0C0C0;
            box-shadow:         inset 0 0 10px #C0C0C0;
        }

        DIV.image_content {
            cursor: pointer !important;
            border: solid 1px #C0C0C0 !important;
        }

        IMG#image_img {
            cursor: zoom-in;
        }

        .modal-dialog {
            max-width: 1024px;
        }

        @media (min-width: 992px) {
            .modal-dialog {
                max-width: 1024px;
            }
        }
    </style>

    @if(!empty($contenido->nombre))
        <h3>{{ $contenido->nombre }}</h3>
    @else
        <div class="alert alert-warning mb-none">
            <?php $traducidos = $contenido->getTraduccionesReales(); ?>
            {{ trans_choice("general/front_lang.no_lang", $traducidos->count()) }}:
            <strong>{{ $contenido->getTraduccionesReales()->implode("name", ", ") }}</strong>
        </div>
    @endif

    @if($contenido->getMedia()->count()>0)
        <div class="modal fade" id="zoom_image" tabindex="-1" role="dialog" aria-labelledby="largeModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="largeModalLabel">{{ trans("elearning::contenidos/front_lang.zoom_imagen") }}</h4>
                    </div>
                    <div class="modal-body">
                        <img id="image_zoom" src="" class="img-responsive" alt="" />
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans("general/front_lang.cerrar") }}</button>
                    </div>
                </div>
            </div>
        </div>


        <div class="owl-carousel owl-theme stage-margin" @if($contenido->getMedia()->count()<2) style="display:none;" @endif data-plugin-options="{'items': 5, 'margin': 10, 'loop': false, 'nav': true, 'dots': false, 'stagePadding': 40}">
            @foreach($contenido->getMedia()->get() as $key => $imagen)
                <div id="img_{{ $key }}" class="image_content" data-value="{{ $key }}">
                    <img alt="" class="img-fluid rounded" src="{{ url("media/getAnnex/".$imagen->id) }}">
                    <article style="display: none;">
                        <strong>{{ trans("elearning::contenidos/front_lang.galeria_imagen") }} {{ $key+1 }}/{{ $contenido->getMedia()->count() }}</strong>

                        @if($imagen->comment!='')
                            <br>
                            {!! $imagen->comment !!}
                        @endif

                    </article>
                </div>
            @endforeach
        </div>

        <hr>

        <div class="image_block">
            <img id="image_img" src="" class="img-responsive" style="margin: auto;" alt="" />

            <div class="comment_img">
                <div class="row">
                    <div class="col-sm-1 col-xs-2"><a href="javascript:load_image_btn('minus');" id="arrow_left" class="pull-left btn btn-primary"><i class="fa fa-chevron-left" aria-hidden="true"></i></a></div>
                    <div id="comment_article" class="col-sm-10  col-xs-8"></div>
                    <div class="col-sm-1 col-xs-2"><a href="javascript:load_image_btn('plus');" id="arrow_right" class="pull-right btn btn-primary"><i class="fa fa-chevron-right" aria-hidden="true"></i></a></div>
                </div>
            </div>

        </div>
    @else
        <p class="text-warning">{{ trans("elearning::contenidos/front_lang.sin_contenidos_galeria") }}</p>
    @endif

    <br clear="all">
    <br clear="all">

@section('foot_page')
    @parent

    <script>
        var image_show = 0;
        var last_image = {{ $contenido->getMedia()->count() - 1  }};
        var loading_img = false;

        $(document).ready(function() {
            $(".image_content").click(function(e) {
                e.preventDefault();
                image_show = $(this).attr("data-value");
                loadImage($(this));
            });

            loadImage($(".image_content:first"));

            $("#image_img").click(function(e) {
                e.preventDefault();
                $("#image_zoom").attr("src", $(this).attr("src"));
                $("#zoom_image").modal("toggle");
            });
        });

        function loadImage($obj) {
            $_url_obj = $obj.children("IMG").attr("src");
            $_comment_html = $obj.children("article").html();
            $_obj_caption = $("#comment_article");
            $("#image_img").attr("src", $_url_obj);
            if($_comment_html!='') $($_obj_caption).html($_comment_html);
            show_hide_buttons();
        }

        function load_image_btn(dsp) {
            if(loading_img) return;
            loading_img = true;
            if(dsp=='minus') image_show--;
            if(dsp=='plus') image_show++;
            loadImage($("#img_" + image_show));
            loading_img = false;
        }

        function show_hide_buttons() {
            var $_obj_left = $("#arrow_left");
            var $_obj_right = $("#arrow_right");

            if(image_show==0) {
                $_obj_left.addClass("disabled");
            } else {
                $_obj_left.removeClass("disabled");
            }

            if(image_show==last_image) {
                $_obj_right.addClass("disabled");
                trackContent();
            } else {
                $_obj_right.removeClass("disabled");
            }
        }

        function trackContent() {
            $.get("{{ url("contenido/trackGaleria/" . $contenido->id) }}", function (data) {
                console.log(data);
            });
        }

    </script>
@endsection
