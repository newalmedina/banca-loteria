
    <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">

            <?php
            $nX = 1;
            ?>
            @foreach ($a_trans as $key => $valor)
                <li @if($nX==1) class="active" @endif>
                    <a href="#tab_{{ $key }}" data-toggle="tab">
                        {{ $valor["idioma"] }}
                        @if($nX==1)- <span class="text-success">{{ trans('Idiomas::idiomas/admin_lang.defecto') }}</span>@endif
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
                <div id="tab_{{ $key }}" class="tab-pane @if($nX==1) active @endif">
                    {!!  Form::hidden('lang['.$key.'][id]', $valor["id"], array('id' => 'id')) !!}

                    {{-- Text Lang - name --}}
<div class="form-group">
    {!! Form::label('lang['.$key.'][name]', trans('Idiomas::idiomas/admin_lang.fields.name'), array('class' => 'col-sm-2 control-label')) !!}
    <div class="col-sm-10">
        {!! Form::text('lang['.$key.'][name]', $idioma->{'name:'.$key} , array('placeholder' => trans('Idiomas::idiomas/admin_lang.fields.name_helper'), 'class' => 'form-control', 'id' => 'name_'.$key)) !!}
    </div>
</div>


                </div>
                <?php
                $nX++;
                ?>
            @endforeach
        </div>
    </div>

