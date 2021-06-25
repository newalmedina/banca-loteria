@extends('admin.layouts.default')

@section('title')
    @parent {{ $page_title }}
@stop

@section('head_page')

    <!-- DataTables -->
    <link href="{{ asset("/assets/admin/vendor/datatables/css/dataTables.bootstrap.min.css") }}" rel="stylesheet" type="text/css" />
    <style>
        body.dragging, body.dragging * {
            cursor: move !important;
        }

        ol.vertical {
            margin: 0px;
            min-height: 10px;
            list-style-type: none;
            padding: 0px;
        }

        ol.vertical ol {
            list-style-type: none;
            padding-right: 0px;
            margin: 0px;
        }

        ol.vertical li {
            background: #fff none repeat scroll 0 0;
            border-bottom: 1px solid #f4f4f4;
            color: #333;
            display: block;
            padding-right: 0px;
            margin-top: 5px;
        }

        ol.vertical li .icon-move {
            cursor: move !important;
        }

        .dragged {
            position: absolute;
            background-color: #c0c0c0 !important;
            opacity: 0.9;
            z-index: 2000;
        }

        ol.vertical li.placeholder {
            position: relative;
            background-color: #fcfaf2 !important;
            border: solid 1px #fcefa1 !important;
            height: 40px;
            /** More li styles **/
        }
        ol.vertical li.placeholder:before {
            position: absolute;
            /** Define arrowhead **/
        }

        ol.vertical ol li:last-child {
            border-bottom:none;
        }

        ol.vertical ol li:first-child  {
            border-top: 1px solid #f4f4f4;
        }

        #spinner {
            background: rgba(0,0,0,0.1);
            position: absolute;
            width: 100%;
            padding: 50px;
            text-align: center;
            display: none;
        }
    </style>
@stop

@section('breadcrumb')
    <li><a href="{{ url("admin/menu") }}">{{ trans('basic::menu/admin_lang.listado_menu') }}</a></li>
    <li class="active">{{ $page_title }}</li>
@stop

@section('content')
    @include('admin.includes.errors')
    @include('admin.includes.success')
    @include('admin.includes.modals')


    <div class="row">
        <div id="col-folders" class="col-md-12">

            <div id="col-folders-container" class="box box-default">
                <div class="box-header with-border">
                    <h3 class="box-title">{{ trans("basic::menu/admin_lang.estructura") }}</h3>
                </div>

                <div class="box-body">
                    @if(Auth::user()->can("admin-menu-create"))
                        <a id="btn_new" href="javascript:createMenu();" class="btn btn-success pull-right"><i class="fa fa-plus-circle" aria-hidden="true"></i> {{ trans('basic::menu/admin_lang.new_menu_item') }}</a>
                        <a class="btn btn-default" href="{{ url('/admin/menu/') }}"><i class="fa fa-arrow-left" aria-hidden="true"></i> {{ trans('basic::menu/admin_lang.cancelar') }}</a>
                    @endif
                </div>

                <div id="spinner" class="overlay"><i class="fa fa-refresh fa-spin" aria-hidden="true"></i></div>

                @if($menuTree->count()>0)
                    <?php $actDepth = 0; ?>

                    <ol class="serialization vertical">
                    @foreach($menuTree as $value)

                        @if($actDepth!=$value->depth)
                            @if($actDepth>$value->depth)
                                @for($nX=$actDepth;$nX>$value->depth; $nX--)
                                    {!! "</ol>" !!}
                                    {!! "</li>" !!}
                                @endfor
                            @endif
                            <?php $actDepth=$value->depth; ?>
                        @endif

                        <li id="{{ $value->id }}" class="list_folder" data-name="{{ $value->title }}" data-id="{{ $value->id }}">
                        <div style="padding: 10px 15px;  @if($value->status=='0') background-color:#C0c0C0; background-image: linear-gradient(45deg, rgba(255, 255, 255, 0.15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, 0.15) 50%, rgba(255, 255, 255, 0.15) 75%, transparent 75%, transparent); background-size: 40px 40px; @endif">
                            <i class="fa fa-arrows text-info icon-move" aria-hidden="true"></i>&nbsp;
                            <i class="fa {{ $value->menuItemType->ico }} text-warning fa-icon_f" style="margin-right: 10px; font-size: 16px;" aria-hidden="true"></i> {!!$value->title!!}
                            <button onclick="deletedir('{{ url('admin/menu/structure/'.$value->id.'/destroy') }}');" class="btn btn-xs btn-danger pull-right"><i class="fa fa-trash" aria-hidden="true"></i></button>
                            <button onclick="changeFolder('{{ $value->id }}');" class="btn btn-xs btn-success pull-right" style="margin-right: 10px;"><i class="fa fa-pencil" aria-hidden="true"></i></button>

                        </div>

                        @if($value->descendants()->count()>0)
                            {!! "<ol class=''>" !!}
                        @else
                            <ol class=''></ol>
                            {!! "</li>" !!}
                        @endif
                    @endforeach

                    @if($actDepth>0)
                        @for($nX=$actDepth;$nX>0; $nX--)
                            {!! "</ol>" !!}
                            {!! "</li>" !!}
                        @endfor
                    @endif

                   </ol>
                @else
                    <div class="alert alert-warning alert-dismissible fade show m-3" role="alert">
                        <strong>¡Atención!</strong> {{ trans("basic::menu/admin_lang.not_data_found") }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                  </div>

                @endif
            </div>

        </div>

        <div id="frmDataInfo" class="col-md-0" style="display: none;"></div>
    </div>

@endsection

@section("foot_page")
    <script type="text/javascript" src="{{ asset("assets/admin/vendor/jquery-sortable/js/jquery-sortable-min.js") }}"></script>

    <script type="text/javascript">


        $(function () {
            var group = $("ol.vertical").sortable({
                group: 'serialization',
                handle: 'i.icon-move',
                onDrop: function ($item, container, _super) {
                    $("#spinner").fadeIn(500);
                    var data = $item.attr("data-id");
                    var parent = $("#" + data).parent("OL").parent("LI").attr("data-id");
                    var previous = $("#" + data).prev().attr("data-id");

                    if(parent==undefined) parent = '0';
                    if(previous==undefined) previous = '0';

                    var url = "{{ url('admin/menu/structure/'.$menu_id."/tree/") }}/" + data + "/"+ parent + "/" +previous;

                    $.get(url, function(data) {
                        $("#spinner").fadeOut(500);
                    });

                    _super($item, container);
                }
            });

            var position = $("#col-folders-container").position();


            $("#spinner").css("height", ($("#col-folders-container").height()));
            $("#spinner").css("width", ($("#col-folders-container").width() + 20));

        });

        function changeFolder(id) {
            animateToggle(true, id);
        }

        function createMenu() {
            animateToggle(true, '0');
        }


        function animateToggle(isopen, id) {
            if(isopen) {
                $("#frmDataInfo").load("{{ url("admin/menu/structure/".$menu_id."/edit/") }}/" + id, function () {
                    $("#col-folders").switchClass("col-md-12", "col-md-3", 300);
                    $("#frmDataInfo").switchClass("col-md-0", "col-md-9", 300);
                    $("#frmDataInfo").fadeIn(300);
                });
            } else {
                $("#frmDataInfo").html("");
                $("#frmDataInfo").fadeOut(300, function() {
                    $("#frmDataInfo").switchClass("col-md-9", "col-md-0", 300);
                    $("#col-folders").switchClass("col-md-3", "col-md-12", 300);
                });
            }

        }

        function formclose() {
            $(".list_folder").removeClass("active");
            animateToggle(false);
        }

        function deletedir(url) {
            var strBtn = "";

            $("#confirmModalLabel").html("{{ trans('general/admin_lang.warning_title') }}");
            $("#confirmModalBody").html("{{ trans('general/admin_lang.delete_question') }}");
            strBtn+= '<button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('general/admin_lang.close') }}</button>';
            strBtn+= '<button type="button" class="btn btn-primary" onclick="javascript:deleteinfo(\''+url+'\');">{{ trans('general/admin_lang.borrar_item') }}</button>';
            $("#confirmModalFooter").html(strBtn);
            $('#modal_confirm').modal('toggle');
        }

        function deleteinfo(url) {
            $.ajax({
                url     : url,
                type    : 'GET',
                success : function(data) {
                    $('#modal_confirm').modal('hide');
                    if(data) {
                        window.location = "{{ url('admin/menu/structure/'.$menu_id) }}";
                    } else {
                        $("#modal_alert").addClass('modal-danger');
                        $("#alertModalBody").html("<i class='fa fa-bug' style='font-size: 64px; float: left; margin-right:15px;'></i> {{ trans('general/admin_lang.errorajax') }}");
                        $("#modal_alert").modal('toggle');
                    }
                    return false;
                }
            });
            return false;
        }


    </script>
@stop
