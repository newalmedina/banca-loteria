
<div class="box box-primary">
    <div class="box-header  with-border"><h3 class="box-title">{{ trans("crud-generator::fields/admin_lang.extra_options") }} - Radio</h3></div>
    <div class="box-body">
        <a href="#" id="addRow" class="btn btn-info pull-right"><i class="fa fa-plus" aria-hidden="true"></i> Añadir opción</a>

        <table id="table_options" class="table table-bordered table-striped" style="width:100%" aria-hidden="true">
            <thead>
            <tr>
                <th scope="col">Valor</th>
                <th scope="col">Texto</th>
                <th scope="col">Acción</th>
            </tr>
            </thead>
            <tbody>
                @if(!empty($field->data))
                    @foreach(json_decode($field->data) as $radio)
                        <tr>
                            <td>
                                {!! Form::text('radio_data[]', $radio[0],
                               array('placeholder' => 'Valor',
                               'class' => 'form-control input-xlarge',
                               'style' => 'width: 100% !important;',
                               'id' => 'radio_data[]')) !!}
                            </td>
                            <td>
                                {!! Form::text('radio_value[]', $radio[1],
                                array('placeholder' => 'Texto',
                                'class' => 'form-control input-xlarge',
                                'style' => 'width: 100% !important;',
                                'id' => 'radio_value[]')) !!}
                            </td>
                            <td>
                                <button class="btn btn-danger btn-sm" data-content="Borrar" data-placement="left" data-toggle="popover">
                                    <i class="fa fa-trash" aria-hidden="true"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>


    </div>
</div>

