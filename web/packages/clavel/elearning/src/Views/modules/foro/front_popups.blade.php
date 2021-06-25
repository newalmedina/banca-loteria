<div id="modalHilo" class="modal fade" tabindex="-1" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
                <h4 class="modal-title">{{ trans('elearning::foro/front_lang._FICVIS_HILO') }}</h4>
            </div>
            <div id="hilo_form" class="modal-body"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('elearning::foro/front_lang._FICVIS_CLOSE') }}</button>
                <a id="btnSaveHilo" class="btn btn-primary has-spinner" href="javascript:saveHilo();"><span class="spinner"><img style="margin-right: 20px;" src="{{ asset("assets/front/img/ajax_loader_vector.gif") }}" width="12" alt="" /></span>{{ trans('elearning::foro/front_lang._FICVIS_SAVE') }}</a>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_alert" tabindex="-1" role="dialog" aria-labelledby="alertModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div id="alertModalBody" class="modal-body"></div>
            <div id="alertModalFooter" class="modal-footer"></div>
        </div>
    </div>
</div>
