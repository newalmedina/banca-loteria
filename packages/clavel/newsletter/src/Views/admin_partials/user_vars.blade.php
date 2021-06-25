<!-- VARIABLES DE USUARIO -->
<div class="modal modal-note fade in" id="bs-modal-users">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button aria-label="Close" data-dismiss="modal" class="close" type="button"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title">{{ trans('Newsletter::admin_lang.selecciona_una_variable') }}</h4>
            </div>
            <div class="modal-body">
                <strong>{{ trans('Newsletter::admin_lang.selecciona_una_variable_2') }}:</strong>
                <br clear="all"><br clear="all"><br clear="all">
                <div class="row">
                    <div class="col-md-3">
                        <a href="javascript:execTC('##NOMBRE##');">{{ trans("Newsletter::admin_lang.nombre") }}</a>
                    </div>
                    <div class="col-md-3">
                        <a href="javascript:execTC('##APELLIDOS##');">{{ trans("Newsletter::admin_lang.apellidos") }}</a>
                    </div>
                </div>
                <br clear="all"><br clear="all">
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>