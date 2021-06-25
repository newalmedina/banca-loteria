@extends('admin.layouts.popup')

@section('head_page')
    @parent
    <link href="{{ asset("assets/admin/css/newsletter_builder/newsletter-builder-template.css") }}" rel="stylesheet" type="text/css" />

    <style>
        .modifyItem, .removeItem, .ModifyContents {
            display: none;
        }

        .noticia_principal {
            cursor: default !important;
        }
    </style>
@endsection

@section("content")

    <div class="selector" style="padding: 20px;">
        <div class="row">
            <div class="col-md-2">
                <strong>{{ trans("Newsletter::admin_lang_template.select_idioma") }}</strong>
            </div>
            <div class="col-md-10">
                <select name="idioma" id="idioma" class="form-control">
                    @foreach($idiomas as $idioma)
                        <option value="{{ $idioma->code }}">{{ $idioma->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div id="plantilla" style="background: {{ $template->background_content }}; padding: 20px;">
        <div class="newsletter">
            <table class="structure" style="background: {{ $template->background_page }}; @if($template->border && !$template->border_shadow) border:solid 1px {{ $template->border_color }} @elseif($template->border) box-shadow: 0px 0px 1px {{ $template->border_color }}; @endif " aria-hidden="true">
                <thead>
                <tr>
                    <td id="header" class="header sortable_content">
                        <?php
                            $header = str_replace("##NOMBRE##", "Jhon", $template->header);
                            $header = str_replace("##APELLIDOS##", "Doe", $header);
                            $header = str_replace("##FECHA##", Carbon\Carbon::now()->format("d/m/Y"), $header);
                            $header = str_replace("##NEWSLETTER_NAME##", "My newsletter", $header);
                            $header = str_replace("##CAMPAIGN_NAME##", "My campaign", $header);
                        ?>
                        {!! $header !!}
                    </td>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td id="body" class="content_newsletter">
                        <table cellspacing="0" cellpadding="0" border="0" style=" width: 100%;" class="news_block" aria-hidden="true">
                            <tbody>
                            <tr>
                                <td class="noticia_principal" style="position : relative; width: 100%" valign="top">
                                    <table width='100%' class="resaltar"
                                           style="
                                                   background-color: {{ $template->resaltar_background_color }};
                                           @if($template->resaltar_border && !$template->resaltar_sombra)
                                                   border:solid 1px {{ $template->resaltar_border_color }}
                                           @elseif($template->resaltar_border)
                                                   box-shadow: 0px 0px 17px 1px {{ $template->resaltar_border_color }};
                                           @endif
                                                   " aria-hidden="true">
                                        <tbody class='noSortable'>
                                        <tr>
                                            <td style='text-align: left;'>
                                                <div class='titulo' style="color: {{ $template->title_font_color }};">Este será el contenedor las cajas resaltadas</div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="texto" style="color: {{ $template->font_color }};">
                                                    <p>Maecenas eget odio mi. Proin eget nisl vel nunc consectetur sagittis. Nullam sodales elit vitae nulla faucibus, vel hendrerit nisi pulvinar. Phasellus eget convallis ante. Integer pretium tempor tortor, id mollis leo molestie a. Praesent sit amet malesuada lectus. Phasellus non vulputate magna.</p>
                                                </div>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                    <br ><br>
                                    <table width='100%' aria-hidden="true">
                                        <tbody class='noSortable'>
                                        <tr>
                                            <td style='text-align: left;'>
                                                <div class='titulo' style="color: {{ $template->title_font_color }};">Este será el contenedor de la newsletter para noticias y textos</div>
                                            </td>
                                        </tr>
                                        <tr><td class='paddingTxtH2'></td></tr>
                                        <tr>
                                            <td>
                                                <div class="texto" style="color: {{ $template->font_color }};">
                                                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi consectetur tincidunt nibh, sit amet sagittis nisi ornare vel. Vestibulum placerat sapien orci, ut maximus nibh porttitor sit amet. Pellentesque sodales nulla ut purus dapibus, eget lacinia sapien maximus. Nullam sagittis, tortor sit amet porttitor elementum, diam tortor vestibulum quam, at commodo elit turpis eu purus. Duis dapibus id libero eu placerat. Proin eget ipsum sodales, lobortis ante ut, bibendum quam. Morbi in ornare odio, ut varius justo. Donec eros diam, dapibus nec quam in, ornare tincidunt ex. Morbi lacinia ex vel mattis commodo. Phasellus justo diam, efficitur at mi a, vehicula tempus nunc. Donec turpis leo, convallis vitae velit nec, pharetra varius enim.</p>
                                                </div>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            </tbody>
                        </table>

                    </td>
                </tr>
                </tbody>
                <tfoot>
                <tr>
                    <td id="footer" class="footer sortable_content">
                        <?php
                        $footer = str_replace("##NOMBRE##", "Jhon", $template->footer);
                        $footer = str_replace("##APELLIDOS##", "Doe", $footer);
                        $footer = str_replace("##FECHA##", Carbon\Carbon::now()->format("d/m/Y"), $footer);
                        $footer = str_replace("##NEWSLETTER_NAME##", "My newsletter", $footer);
                        $footer = str_replace("##CAMPAIGN_NAME##", "My campaign", $footer);
                        ?>
                        {!! $footer !!}
                    </td>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>

@endsection

@section('foot_page')
    <script>
        $(document).ready(function() {
            loadIdioma();

            $("#idioma").change(function(e) {
                loadIdioma();
            });
        });

        function loadIdioma() {
            $(".noticia_principal").children("DIV").css("display","none");
            $(".body_" + $("#idioma").val()).css("display","block");
        }
    </script>
@endsection
