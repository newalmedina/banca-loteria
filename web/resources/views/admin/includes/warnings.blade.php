@if (Session::get('warning',"") != "")
    <div class="alert alert-warning">
        <button class="close" aria-hidden="true" data-dismiss="alert" type="button">&times;</button>
        <strong>{{ date('d/m/Y H:i:s') }}</strong>
        {{ Session::get('warning',"") }}
    </div>
@endif
