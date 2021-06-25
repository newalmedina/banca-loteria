@if (!empty($errors) && $errors->any())
    <div class="alert alert-danger">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <i class="fa fa-times" aria-hidden="true"></i>
        <strong>{{ trans('general/admin_lang.corregir_errores') }}</strong>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if (Session::get('error',"") != "")
    <div class="alert alert-danger">
        <button class="close" aria-hidden="true" data-dismiss="alert" type="button">&times;</button>
        <strong>{{ date('d/m/Y H:i:s') }}</strong>
        {{ Session::get('error',"") }}
    </div>
@endif
