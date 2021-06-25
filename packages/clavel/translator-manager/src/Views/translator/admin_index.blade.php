@extends('admin.layouts.default')

@section('title')
    @parent {{ $page_title }}
@stop

@section('head_page')


@stop

@section('breadcrumb')
    <li class="active">{{ $page_title }}</li>
@stop

@section('content')
    @include('admin.includes.errors')
    @include('admin.includes.success')
    @include('admin.includes.modals')

    <!-- Modal Enviando-->
    <div class="modal fade" id="sendingImportModal" tabindex="-1" role="dialog" aria-labelledby="sendingImportModal">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <div class="loader"></div>
                    <div clas="loader-txt">
                        <p>{!! trans("translator-manager::translator/admin_lang.importando_grupos") !!}<br><br><small>{!! trans("translator-manager::translator/admin_lang.paciencia") !!}</small></p>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-6">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">{{ trans('translator-manager::translator/admin_lang.idiomas_disponibles') }}</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body no-padding">

                        {!! Form::open(array('url' => 'admin/translator', 'method' => 'POST', 'id' => 'frmDelete')) !!}
                        {!! method_field('DELETE') !!}
                            <table id="table_languages" class="table table-bordered table-striped" aria-hidden="true">
                                <thead>
                                <tr>
                                    <th scope="col">{{ trans('translator-manager::translator/admin_lang.idiomas') }}</th>
                                    <th scope="col" style="width: 40px">{{ trans('translator-manager::translator/admin_lang.actions') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($locales as $locale)
                                    <tr>
                                        <td>{{ $locale }}</td>
                                        <td>
                                            <button type="button" class="btn btn-danger btn-sm"
                                                    onclick="javascript:deleteLang('{{ url('admin/translator/'.$locale) }}');"
                                                    data-content="{{ trans('general/admin_lang.borrar') }}"
                                                    data-placement="right" data-toggle="popover">
                                                <i class="fa fa-trash-o" aria-hidden="true"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        {!! Form::close() !!}
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>

            <div class="col-md-6">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">{{ trans('translator-manager::translator/admin_lang.nuevo_idioma') }}</h3>
                    </div>
                    <!-- /.box-header -->
                    <!-- form start -->
                    {!! Form::open(array('url' => 'admin/translator', 'method' => 'POST', 'id' => 'frmAdd')) !!}
                        <div class="box-body">
                            <div class="form-group">
                                <label for="new-locale">{{ trans('translator-manager::translator/admin_lang.nuevo_idioma') }}</label>
                                <input type="text" class="form-control" name="new-locale" id="new-locale" placeholder="{{ trans('translator-manager::translator/admin_lang.idioma_key') }}">
                            </div>
                        </div>
                        <div class="box-footer">
                            <button type="submit" class="btn btn-primary">{{ trans('translator-manager::translator/admin_lang.add') }}</button>
                        </div>
                    {!! Form::close() !!}
                </div>

                <!-- /.box -->
            </div>
        </div>


        <div class="row">
            <div class="col-md-12">
                <div class="box box-warning">
                    <div class="box-header with-border">
                        <h3 class="box-title">{{ trans('translator-manager::translator/admin_lang.grupos') }}</h3>
                    </div>
                    <div class="box-header with-border">
                        <div class="mailbox-controls">
                            <div class="btn-group">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-sm-5">
                                            <select id="replace" name="replace" class="form-control">
                                                <option value="0">{{ trans('translator-manager::translator/admin_lang.importar_grupos_add') }}</option>
                                                <option value="1">{{ trans('translator-manager::translator/admin_lang.importar_grupos_replace') }}</option>
                                            </select>
                                        </div>
                                        <div class="col-sm-7">
                                            <div class="btn-group">
                                                <button type="button" id="btnImportarGrupos" class="btn bg-purple" onclick="javascript:doImport();" style="margin-right: 5px;">
                                                    {{ trans('translator-manager::translator/admin_lang.importar_grupos') }}
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /.btn-group -->
                            <div class="pull-right">
                                <div class="btn-group">
                                    <button type="button" id="btnBuscarTextos" style="margin-right: 15px;" class="btn btn-danger" onclick="javascript:doPublish();">
                                        {{ trans('translator-manager::translator/admin_lang.publicar_todo') }}
                                    </button>
                                    <button type="button" id="btnBuscarTextos" class="btn bg-aqua" onclick="javascript:doFind();">
                                        {{ trans('translator-manager::translator/admin_lang.buscar_textos') }}
                                    </button>
                                </div>
                                <!-- /.btn-group -->
                            </div>

                        </div>
                     </div>
                    <!-- /.box-header -->
                    <div class="box-body no-border">
                        {!! Form::open(array('url' => 'admin/translator/group/manage', 'method' => 'POST', 'id' => 'frmGroup')) !!}
                        {!! Form::hidden('group', '', array('id' => 'group')) !!}
                        <table id="table_languages" class="table table-bordered table-striped" aria-hidden="true">
                            <thead>
                            <tr>
                                <th scope="col">{{ trans('translator-manager::translator/admin_lang.grupo') }}</th>
                                <th scope="col" style="width: 40px">{{ trans('translator-manager::translator/admin_lang.actions') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($groups as $key => $value)
                                <tr>
                                    <td>{{ $value }}</td>
                                    <td>
                                        <button type="button" class="btn btn-success btn-sm"
                                                onclick="javascript:doGroup('{{ $key }}');"
                                                data-content="{{ trans('general/admin_lang.grupo') }}"
                                                data-placement="right" data-toggle="popover">
                                            <i class="fa fa-magic" aria-hidden="true"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        {!! Form::close() !!}
                    </div>

                    <!-- /.box -->
                </div>
            </div>
        </div>
    </section>
    <!-- /.content -->
@endsection

@section("foot_page")
    <script type="text/javascript">
        function deleteLang(url) {
            var strBtn = "";

            $("#confirmModalLabel").html("{{ trans('general/admin_lang.warning_title') }}");
            $("#confirmModalBody").html("{{ trans('general/admin_lang.delete_question') }}");
            strBtn+= '<button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('general/admin_lang.close') }}</button>';
            strBtn+= '<button type="button" class="btn btn-primary" onclick="javascript:deleteinfo(\''+url+'\');">{{ trans('general/admin_lang.borrar_item') }}</button>';
            $("#confirmModalFooter").html(strBtn);
            $('#modal_confirm').modal('toggle');
        }

        function deleteinfo(url) {
            $('#frmDelete').attr('action', url);
            $("#frmDelete").submit();
            return false;
        }

        function doGroup($group) {
            $('#group').val($group);
            $("#frmGroup").submit();
            return false;
        }

        function doImport() {
            var strBtn = "";

            $("#confirmModalLabel").html("{{ trans('general/admin_lang.warning_title') }}");
            $("#confirmModalBody").html("{{ trans('translator-manager::translator/admin_lang.seguro_importar') }}");
            strBtn+= '<button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('general/admin_lang.close') }}</button>';
            strBtn+= '<button type="button" class="btn btn-primary" onclick="javascript:goImport();">{{ trans('translator-manager::translator/admin_lang.importar') }}</button>';
            $("#confirmModalFooter").html(strBtn);
            $('#modal_confirm').modal('toggle');
        }

        function goImport() {
            $("#sendingImportModal").modal({
                backdrop: "static", //remove ability to close modal with click
                keyboard: false, //remove option to close with keyboard
                show: true //Display loader!
            });
            $('#modal_confirm').modal('hide');

            $.ajax({
                url: "{{url('admin/translator/import')}}",
                type: "POST",
                headers: { 'X-CSRF-Token': '{{ csrf_token() }}' },
                data: {
                    replace_id: $('#replace').val()
                },
                success       : function ( data ) {
                    $('#sendingImportModal').modal('hide');
                    if(data) {
                        if(data.status) {
                            $("#modal_alert").addClass('modal-success');
                            $("#alertModalHeader").html('{{ trans('translator-manager::translator/admin_lang.import_ok') }}');
                            $("#alertModalBody").html("<i class='fa fa-check-circle' style='font-size: 64px; float: left; margin-right:15px;'></i> " + data.msg);
                            $("#modal_alert").modal('toggle');
                            window.location.reload();
                        } else {
                            $("#modal_alert").addClass('modal-warning');
                            $("#alertModalBody").html("<i class='fa fa-warning' style='font-size: 64px; float: left; margin-right:15px;'></i> " + data.msg);
                            $("#modal_alert").modal('toggle');
                        }
                    } else {
                        $("#modal_alert").addClass('modal-danger');
                        $("#alertModalBody").html("<i class='fa fa-bug' style='font-size: 64px; float: left; margin-right:15px;'></i> {{ trans('translator-manager::translator/admin_lang.errorajax') }}");
                        $("#modal_alert").modal('toggle');
                    }
                    return false;

                }
            });
            return false;
        }

        function doFind() {
            var strBtn = "";

            $("#confirmModalLabel").html("{{ trans('general/admin_lang.warning_title') }}");
            $("#confirmModalBody").html("{{ trans('translator-manager::translator/admin_lang.seguro_buscar') }}");
            strBtn+= '<button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('general/admin_lang.close') }}</button>';
            strBtn+= '<button type="button" class="btn btn-primary" onclick="javascript:goFind();">{{ trans('translator-manager::translator/admin_lang.buscar') }}</button>';
            $("#confirmModalFooter").html(strBtn);
            $('#modal_confirm').modal('toggle');
        }

        function goFind() {
            $("#sendingImportModal").modal({
                backdrop: "static", //remove ability to close modal with click
                keyboard: false, //remove option to close with keyboard
                show: true //Display loader!
            });
            $('#modal_confirm').modal('hide');

            $.ajax({
                url: "{{url('admin/translator/find')}}",
                type: "POST",
                headers: { 'X-CSRF-Token': '{{ csrf_token() }}' },
                success       : function ( data ) {
                    $('#sendingImportModal').modal('hide');
                    if(data) {
                        if(data.status) {
                            $("#modal_alert").addClass('modal-success');
                            $("#alertModalHeader").html('{{ trans('translator-manager::translator/admin_lang.import_ok') }}');
                            $("#alertModalBody").html("<i class='fa fa-check-circle' style='font-size: 64px; float: left; margin-right:15px;'></i> " + data.msg);
                            $("#modal_alert").modal('toggle');
                            window.location.reload();
                        } else {
                            $("#modal_alert").addClass('modal-warning');
                            $("#alertModalBody").html("<i class='fa fa-warning' style='font-size: 64px; float: left; margin-right:15px;'></i> " + data.msg);
                            $("#modal_alert").modal('toggle');
                        }
                    } else {
                        $("#modal_alert").addClass('modal-danger');
                        $("#alertModalBody").html("<i class='fa fa-bug' style='font-size: 64px; float: left; margin-right:15px;'></i> {{ trans('translator-manager::translator/admin_lang.errorajax') }}");
                        $("#modal_alert").modal('toggle');
                    }
                    return false;

                }
            });
            return false;
        }

        function doPublish() {
            var strBtn = "";

            $("#confirmModalLabel").html("{{ trans('general/admin_lang.warning_title') }}");
            $("#confirmModalBody").html("{{ trans('translator-manager::translator/admin_lang.seguro_publicar') }}");
            strBtn+= '<button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('general/admin_lang.close') }}</button>';
            strBtn+= '<button type="button" class="btn btn-primary" onclick="javascript:goPublish();">{{ trans('translator-manager::translator/admin_lang.publicar') }}</button>';
            $("#confirmModalFooter").html(strBtn);
            $('#modal_confirm').modal('toggle');
        }

        function goPublish() {
            $("#sendingImportModal").modal({
                backdrop: "static", //remove ability to close modal with click
                keyboard: false, //remove option to close with keyboard
                show: true //Display loader!
            });
            $('#modal_confirm').modal('hide');

            $.ajax({
                url: "{{url('admin/translator/publish')}}",
                type: "POST",
                headers: { 'X-CSRF-Token': '{{ csrf_token() }}' },
                success       : function ( data ) {
                    $('#sendingImportModal').modal('hide');
                    if(data) {
                        if(data.status) {
                            $("#modal_alert").addClass('modal-success');
                            $("#alertModalHeader").html('{{ trans('translator-manager::translator/admin_lang.import_ok') }}');
                            $("#alertModalBody").html("<i class='fa fa-check-circle' style='font-size: 64px; float: left; margin-right:15px;'></i> " + data.msg);
                            $("#modal_alert").modal('toggle');
                            window.location.reload();
                        } else {
                            $("#modal_alert").addClass('modal-warning');
                            $("#alertModalBody").html("<i class='fa fa-warning' style='font-size: 64px; float: left; margin-right:15px;'></i> " + data.msg);
                            $("#modal_alert").modal('toggle');
                        }
                    } else {
                        $("#modal_alert").addClass('modal-danger');
                        $("#alertModalBody").html("<i class='fa fa-bug' style='font-size: 64px; float: left; margin-right:15px;'></i> {{ trans('translator-manager::translator/admin_lang.errorajax') }}");
                        $("#modal_alert").modal('toggle');
                    }
                    return false;

                }
            });
            return false;
        }

    </script>
@stop
