@extends('front.layouts.default')

@section('title')
    @parent {{ $page_title }}
@stop

@section('head_page')
@stop

@section('content')
<div class="container">

    <!-- Page Content -->
    @if(!is_null($listado) && !empty($listado))

        @if($listado->newsletter->newsletterRows()->count()>0)

            <!-- AddThis Button BEGIN -->
            <!--<div class="addthis_toolbox addthis_default_style  addthis_32x32_style">
                <a class="addthis_button_print"></a>
                <a class="addthis_button_mailto"></a>
                <a class="addthis_button_twitter"></a>
                <a class="addthis_button_facebook"></a>
                <a class="addthis_button_google_plusone_share"></a>
                <a class="addthis_button_meneame"></a>
                <a class="addthis_button_tuenti"></a>
                <a class="addthis_button_pocket"></a>
                <a class="addthis_button_tumblr"></a>
                <a class="addthis_button_linkedin"></a>
                <a class="addthis_button_whatsapp"></a>
                <a class="addthis_button_pinterest_share"></a>
                <a class="addthis_button_more"></a>
            </div>
            <script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-5669460febf78224" async="async"></script>-->
            <?php $first_new = true; ?>
            @foreach($listado->newsletter->newsletterRows as $row)
                <div class="col-sm-12 @if($row->cols==1) new @endif @if(isset($a_news[$row->id][1]["in_box"]) && $a_news[$row->id][1]["in_box"]=='1' && $row->cols==1) new-big @endif block-news" @if($first_new) style="margin-top:0" @endif>
                    <?php $first_new = false; ?>
                    @for($nX=1; $nX<=$row->cols; $nX++)

                        @if(isset($a_news[$row->id][$nX]))
                            <?php
                            $cols = (12 / $row->cols);
                            $class = ($nX==1) ? "first-small" : "";
                            if ($class=='') {
                                $class = ($nX==$row->cols) ? "last-small" : "";
                            }
                            $posImage = "";
                            $styleTitle = "";
                            $styleDesc = "";
                            $cImageLeft = ($a_news[$row->id][$nX]["in_box"]=='0') ? "first-small" : "";
                            if (isset($a_news[$row->id][$nX]["post"])) {
                                if ($a_news[$row->id][$nX]["post"]["img"]!='') {
                                    $posImage = $a_news[$row->id][$nX]["post"]["image_position"];
                                }
                                //if($a_news[$row->id][$nX]["post"]["title_color"]!='') $styleTitle = 'style="color: '.$a_news[$row->id][$nX]["post"]["title_color"].' !important;"';
                                //if($a_news[$row->id][$nX]["post"]["text_color"]!='') $styleDesc = 'style="color: '.$a_news[$row->id][$nX]["post"]["text_color"].' !important;"';
                            }
                            ?>

                            @if(isset($a_news[$row->id][$nX]["in_box"]))
                                @if($row->cols>1)
                                    <div class="col-sm-{{ $cols }} new @if($a_news[$row->id][$nX]["in_box"]=='1') new-small @endif {{ $class }}">
                                        <div class="content">
                                @elseif($row->cols==1 && ($posImage=='' || $posImage=='t' || $posImage=='b') && $a_news[$row->id][$nX]["in_box"]=='1')
                                    <div style="padding: 10px;">
                                @endif
                            @endif

                            @if(isset($a_news[$row->id][$nX]["post"]))
                                <div onclick="window.location='{{ url("posts/post") }}/{{ $a_news[$row->id][$nX]["post"]["url_seo"] }}'" style="cursor: pointer;">

                                    @if(($posImage=='t' || $posImage=='l'))
                                        @if($posImage=='l') <div class="col-sm-6 {{ $cImageLeft }}"> @endif
                                            <div style="text-align:center"><img class="img-responsive" src="{{$a_news[$row->id][$nX]["post"]["img"]}}" alt=""></div>
                                        @if($posImage=='l') </div> @endif
                                    @endif

                                    @if($posImage=='l' || $posImage=='r') <div class="col-sm-6"> @endif

                                    <h4 {!! $styleTitle !!}>{!! $a_news[$row->id][$nX]["post"]["title"] !!}</h4>
                                    <p class="date">{{ $a_news[$row->id][$nX]["post"]["fecha"] }} {{ $a_news[$row->id][$nX]["post"]["fuente"] }}</p>
                                    <p {!! $styleDesc !!}>{!! $a_news[$row->id][$nX]["post"]["text"] !!}</p>

                                    @if($posImage=='l' || $posImage=='r') </div> @endif

                                    @if(($posImage=='b' || $posImage=='r'))
                                        @if($posImage=='r') <div class="col-sm-6"> @endif
                                            <div style="text-align:center"><img class="img-responsive" src="{{$a_news[$row->id][$nX]["post"]["img"]}}" alt=""></div>
                                        @if($posImage=='r') </div> @endif
                                    @endif

                                </div>
                            @else
                                {!! $a_news[$row->id][$nX]["value"] !!}
                            @endif

                            @if(isset($a_news[$row->id][$nX]["in_box"]))
                                @if($row->cols>1)
                                        </div>
                                    </div>
                                @elseif($row->cols==1 && ($posImage=='' || $posImage=='t' || $posImage=='b') && $a_news[$row->id][$nX]["in_box"]=='1')
                                    </div>
                                @endif
                            @endif

                        @endif
                    @endfor
                </div>


            @endforeach



        @else
            <p class="text-warning">{{ trans("home/front_lang.newsletter_vacia") }}</p>
        @endif
    @else
        <p class="text-warning">{{ trans("home/front_lang.sin_newsletter") }}</p>
    @endif
    <!-- /.container -->
</div>

@endsection

@section("foot_page")

@stop
