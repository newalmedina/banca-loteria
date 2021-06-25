@extends('admin.layouts.pdf')

@section('head_page')
    <!-- DataTables -->
    {!! Html::style('assets/front/css/certificado_pdf.css') !!}

    <link href="https://fonts.googleapis.com/css?family=Julius+Sans+One|Lobster" rel="stylesheet">


    <style>
        .page-break {
            page-break-after: always;
        }

        @page {
            margin:0px;
            padding:0px
        }

        body,html {
            margin:0px;
            padding:0px;
            width:1123px;
            height:794px;
        }
    </style>
@stop

@section('content')
    @if ($certificado->id != "")
        <?php $nDiapo = 0; ?>
        @foreach ($a_trans as $key => $valor)
            @for($nY=1; $nY<=$certificado->paginas; $nY++)
                @if(isset($certificado->paginasCertificado[$nY-1]) && $certificado->paginasCertificado[$nY-1]->{'plantilla:'.$key} != "")
                @if($nDiapo>0) <div class="page-break"></div> @endif
                <?php $nDiapo++;?>
                <div class="buttonsOfBlcok2" style="overflow: hidden; position: absolute; width:1123px; height:794px; @if(isset($certificado->paginasCertificado[$nY-1])) background-image: url({{ $certificado->paginasCertificado[$nY-1]->{'plantilla:'.$key} }}); @endif">
                    @if(isset($certificado->paginasCertificado[$nY-1]->translations->where("locale",$key)->first()->elementosPagina))
                        @foreach($certificado->paginasCertificado[$nY-1]->translations->where("locale",$key)->first()->elementosPagina as $keyEl=>$valEl)
                            <?php
                                $strStyle = "position:absolute; top:".$valEl->mtop."px; left:".$valEl->mleft."px;";
                                $strStyle .= "width:".$valEl->width."px !important; height:".$valEl->height."px !important;";
                                $strStyle .= ($valEl->fontcolor)?"color: ".$valEl->fontcolor."; ":"";
                                $strStyle .= ($valEl->fontfamily)?"font-family: ".$valEl->fontfamily."; ":"";
                                $strStyle .= ($valEl->fontsize)?"font-size: ".$valEl->fontsize."; ":"";
                                $strStyle .= ($valEl->fontsize)?"line-height: ".$valEl->fontsize."; ":"";
                            ?>
                            <div class="elementInfo" style="<?php echo $strStyle;?>">{!! $valEl->name !!}</div>
                        @endforeach
                    @endif
                </div>
                @endif
            @endfor
        @endforeach
    @endif
@endsection
