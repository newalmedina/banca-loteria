@if (Session::get('success',"") != "")
    <div class="alert alert-success">
        <button class="close" aria-hidden="true" data-dismiss="alert" type="button">&times;</button>
        <strong>{{ date('d/m/Y H:i:s') }}</strong>
        {{ Session::get('success',"") }}
    </div>
@endif