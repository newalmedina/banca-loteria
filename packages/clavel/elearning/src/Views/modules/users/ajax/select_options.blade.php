@if(!empty($municipios))

    <option disabled  selected="selected"
            value="">{{ trans('profile/front_lang.municipio') }}</option>

    @foreach($municipios as $municipio)
        <option value="{{ $municipio->id }}">{{ $municipio->nombre }}</option>
    @endforeach

@elseif (!empty($csalud))

    <option selected="selected"
            value="">{{ trans('profile/front_lang.centro') }}</option>

    @foreach($csalud as $centro)
        <option value="{{ $centro->id }}">{{ $centro->nombre }}</option>
    @endforeach

@endif
