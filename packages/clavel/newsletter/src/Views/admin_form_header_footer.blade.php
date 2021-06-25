<style>

    #bs-modal-images, #bs-modal-users {
        z-index: 99999999;
    }

    i.mce-i-icon-database:before {
        content: "\f1c0";
        font-family: FontAwesome, sans-serif;
    }
</style>

<input type="hidden" id="row_file_content" name="row_file_content" value="{{ $row_id }}">
<input type="hidden" id="col_file_content" name="col_file_content" value="{{ $col_id }}">

<div class="nav-tabs-custom">
    <ul class="nav nav-tabs">
        <?php
        $nX = 1;
        ?>
        @foreach ($a_trans as $key => $valor)
            <li @if($nX==1) class="active" @endif>
                <a href="#tab_hf_{{ $key }}" data-toggle="tab">
                    {{ $valor["idioma"] }}
                    @if($nX==1)- <span class="text-success">{{ trans('Posts::admin_lang._defecto') }}</span>@endif
                </a>
            </li>
            <?php
            $nX++;
            ?>
        @endforeach

    </ul>

    <div class="tab-content">
        <?php
        $nX = 1;
        ?>
        @foreach ($a_trans as $key => $valor)
            <div id="tab_hf_{{ $key }}" class="tab-pane @if($nX==1) active @endif">
                {!! Form::textarea('userlang['.$key.'][body]', null, array('class' => 'form-control textarea2', 'id' => 'body_'.$key)) !!}
            </div>
            <?php
            $nX++;
            ?>
        @endforeach
    </div>
</div>

<script type="text/javascript" src="{{ asset("assets/admin/vendor/tinymce/tinymce.min.js") }}"></script>
<script>
    $(document).ready(function() {
        for (i=0; i < tinyMCE.editors.length; i++) {
            tinyMCE.editors[i].remove();
        }

        tinymce.init({
            mode : "exact",
            selector: "textarea",
            menubar: false,
            height: 300,
            convert_urls : false,
            resize:false,
            plugins: [
                "advlist autolink lists link image charmap print preview anchor",
                "searchreplace visualblocks code fullscreen table textcolor",
                "insertdatetime media table contextmenu paste"
            ],
            toolbar: "insertfile undo redo | styleselect | fontsizeselect | forecolor, backcolor | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | table | link media image | code, user_button",
            file_browser_callback : function(field_name, url, type, win) {

                openImageController(field_name, '0');

            },
            setup : function(ed) {

                my_editor = ed;

                // Add a custom button
                ed.addButton('user_button', {
                    title: 'Datos generados',
                    icon: 'icon-database',
                    onclick: function () {
                        $('#bs-modal-database').modal({
                            keyboard: false,
                            backdrop: 'static',
                            show: 'toggle'
                        });
                    }
                });

                ed.on('init', function () {
                    var row_id = $("#row_file_content").val();
                    var col_id = $("#col_file_content").val();
                    var _parent = $("#" + row_id + "_" + col_id);

                    this.setContent(_parent.children("." + this.id).html());
                });
            }
        });

    });

    function save_contenido() {
        var row_id = $("#row_file_content").val();
        var col_id = $("#col_file_content").val();
        var $_html = $("#"+row_id+"_"+col_id);
        var content = "";

        $_html.html("");

        for (i=0; i < tinyMCE.editors.length; i++) {
            var html_tiny = tinyMCE.editors[i].getContent();
            if(html_tiny!='') {
                if(content=='') {
                    content+= "<div class='" + tinyMCE.editors[i].id + "'>" + tinyMCE.editors[i].getContent() + "</div>";
                } else {
                    content+= "<div style='display:none;' class='" + tinyMCE.editors[i].id + "'>" + tinyMCE.editors[i].getContent() + "</div>";
                }
            }
        }

        if(content=='') {
            content="<div class='text-center action_sort'><a href='javascript:showModal(\""+row_id+"\", \""+col_id+"\");'>{{ trans("Newsletter::admin_lang.arrastrar_contenido_aqui") }}<br><i class='fa fa-share-square-o' style='font-size:18px; margin-top:10px;'></i></a></div>";
        } else {
            content+= "<div class='ModifyContents'><div onclick='showModal(\""+row_id+"\", \""+col_id+"\");' class='text-success icon_mod'><i class='fa fa-pencil' style='font-size: 36px;'></i></div>";
        }

        $_html.html(content);

        $('#modalContenidos').modal('hide');
        updateListadoHeaderFooter();
    }

    /* funciones del tinymce */
    function execTC_hf(word) {
        $('#bs-modal-database').modal("hide");
        tinyMCE.execCommand('mceInsertContent',false, word);
    }

    function openImageController(input, only_img) {
        $('#bs-modal-images').modal({
            keyboard: false,
            backdrop: 'static',
            show: 'toggle'
        });

        var style = "width: 100%;padding: 50px; text-align: center;";
        $("#responsibe_images").html('<div id="spinner" class="overlay" style="'+style+'"><i class="fa fa-refresh fa-spin" aria-hidden="true"></i></div>');
        $("#responsibe_images").load("{{ url("admin/media/viewer/") }}/" + input + "/" + only_img);
    }

</script>
