<!-- DataTables -->
<link href="{{ asset("/assets/admin/vendor/datatables/css/dataTables.bootstrap.min.css") }}" rel="stylesheet" type="text/css" />
<script src="{{ asset("/assets/admin/vendor/datatables/js/jquery.dataTables.min.js") }}"></script>
<script src="{{ asset("/assets/admin/vendor/datatables/js/dataTables.bootstrap.min.js") }}"></script>


<!-- page script -->
<script type="text/javascript">
    var oTable = '';
    var count = 0;
    $(document).ready(function() {
        oTable = $('#table_options').DataTable( {
            searching: false,
            "bLengthChange": false,
            columns: [
                {
                    "title"         : "{!! trans('crud-generator::fields/admin_lang.name') !!}",
                    orderable       : false,
                    searchable      : false,
                    sWidth          : ''
                },
                {
                    "title"         : "{!! trans('crud-generator::fields/admin_lang.visual') !!}",
                    orderable       : false,
                    searchable      : false,
                    sWidth          : ''
                },
                {
                    "title"         : "{!! trans('crud-generator::fields/admin_lang.actions') !!}",
                    orderable       : false,
                    searchable      : false,
                    sWidth          : '80px'
                }

            ],
            "order": [[0, 'asc']],
            oLanguage: {!! json_encode(trans('datatable/lang')) !!}
        });

        $('#addRow').on( 'click', function (event) {
            event.preventDefault();
            oTable.row.add( [
                '<input placeholder="Valor" class="form-control input-xlarge" style="width: 100% !important;" id="checkboxMulti_data[]" name="checkboxMulti_data[]" type="text">',
                '<input placeholder="Texto" class="form-control input-xlarge" style="width: 100% !important;" id="checkboxMulti_value[]" name="checkboxMulti_value[]" type="text">',
                '<button class="btn btn-danger btn-sm" data-content="Borrar" data-placement="left" data-toggle="popover"><i class="fa fa-trash" aria-hidden="true"></i></button>'
            ] ).draw( false );
            count++;
        } );

        oTable.on("click", "button", function(){
            event.preventDefault();
            console.log($(this).parent());
            oTable.row($(this).parents('tr')).remove().draw(false);
        });

    });




</script>
