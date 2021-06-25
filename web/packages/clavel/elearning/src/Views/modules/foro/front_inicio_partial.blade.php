<table class="table forum mt-md mb-none" aria-hidden="true">
    <thead>
    <tr>
        <th scope="col" class="cell-stat pl-md">
            <img src='{{ asset("assets/front/img/settings.svg") }}' alt="">
        </th>
        <th scope="col">{{ trans("elearning::foro/front_lang.tema") }}</th>
        <th scope="col" class="cell-stat-2x text-center hidden-xs hidden-sm">{{ trans("elearning::foro/front_lang.respuestas") }}</th>
        <th scope="col" class="cell-stat-3x hidden-xs hidden-sm">{{ trans("elearning::foro/front_lang.ultimo_mensaje") }}</th>
    </tr>
    </thead>
    <tbody>
    @if($temas->count()>0)

        @foreach($temas as $tema)
            <tr id="id_{{ $tema->id }}">
                <td class="pl-md">
                    @if((auth()->user()->id == $tema->user_id && auth()->user()->can("frontend-foro-delete-self")) || auth()->user()->can("frontend-foro-delete"))
                        <a href="javascript:delete_message('{{ $tema->id }}');">
                            <img src='{{ asset("assets/front/img/eraser.svg") }}' alt=''>
                        </a>
                    @endif
                </td>
                <td>
                    <h4>
                        <a href="javascript:show_tema('{{ url("foro/show/".$tema->id) }}')">{{ $tema->titulo }}</a><br>
                        <small>{{ trans("elearning::foro/front_lang.tema_iniciado_por") }} {{ $tema->user->userProfile->full_name }}, {{ $tema->creacion }}</small>
                    </h4>
                </td>
                <td class="text-center hidden-xs hidden-sm"><a>{{ $tema->respuestas }}</a></td>
                <td class="hidden-xs hidden-sm">
                    <?php
                        $ultimo = $tema->getUltimo();
                    ?>
                    {{ trans("elearning::foro/front_lang.last_por") }} <a>{{ $ultimo["Usuario"] }}</a><br>
                    <small><i class="fa  fa-clock-o" aria-hidden="true"></i> {{ $ultimo["Fecha"] }}</small>
                </td>
            </tr>
        @endforeach

    @else
        <tr>
            <td colspan="2" class="text-center">{{ trans("elearning::foro/front_lang.no_messajes") }}</td>
            <td colspan="2" class="text-center"></td>
        </tr>
    @endif
    </tbody>
</table>

<div class="pull-right">
    {!! $temas->render() !!}
</div>
<div class="clearfix"></div>

<script>

    $( ".page-link" ).click(function(e) {

        var url = $(this).attr('href');
        $("#foro_list").load(url);
        return false;
        e.PreventDefault();

    });

</script>
