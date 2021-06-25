<script>
    $(document).ready(function() {
        $("#default_value").select2();

        $('#default_value').on('change',function(e){
            var model = $('#default_value option:selected').attr('value');
            var field = '{{ $field->data }}';
            if(model) {
                $.ajax({
                    url     : '{{url('admin/crud-generator/fields/model')}}',
                    type    : 'POST',
                    data: {model: model},
                    "headers": {"X-CSRF-TOKEN": "{{ csrf_token() }}"},
                    success : function(data) {
                        if(data) {
                            $('#data').empty();
                            $('#data').focus;

                            $.each(data, function(key, value){
                                var selected = '';
                                if(key === field) {
                                    selected = ' selected ';
                                }
                                $('#data').append('<option value="'+ key +'" '+ selected +'>' + value+ '</option>');
                            });
                        } else {
                            $('#data').empty();
                            $("#modal_alert").addClass('modal-danger');
                            $("#alertModalBody").html("<i class='fa fa-bug' style='font-size: 64px; float: left; margin-right:15px;'></i> {{ trans('general/admin_lang.errorajax') }}");
                            $("#modal_alert").modal('toggle');
                        }
                        return false;
                    }
                });
            } else {
                $('#data').empty();
            }
        });

        $( "#default_value" ).change();
    });
</script>
