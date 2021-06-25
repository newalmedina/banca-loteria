<div class="row form-group">
    {!! Form::model(null, ['route' => array('admin.alumnos.storeGrupos'), 'method' => 'POST', 'id' => 'formDataGrupos']) !!}
    <div class="row>">
        <div class="col-md-12"><h4>{{ $alumno->first_name . " " . $alumno->last_name }}</h4></div>
    </div>
    <br clear="all"><br clear="all">
    <div class="row>">
        <div class="col-md-12">
            {!! Form::hidden('user_id', $user_id, array('id' => 'user_id')) !!}
            {!! Form::label('sel_grupos', trans('elearning::alumnos/admin_lang.grupos'), array('class' => 'col-sm-2 control-label', 'readonly' => true)) !!}

            <div class="col-md-9">
                <select class="form-control select2" name="sel_grupos[]" multiple="multiple"
                        data-placeholder="{{ trans('elearning::alumnos/admin_lang.grupos') }}" style="width: 100%;">
                    @foreach($grupos as $grupo)
                        <option value="{{ $grupo->id }}" @if($grupo->alumnoSelected($user_id)) selected @endif>
                            ({{ $grupo->id }}) {{ $grupo->nombre }}</option>
                    @endforeach
                </select>

            </div>
        </div>
    </div>
    {!! Form::close() !!}
</div>


<script>
    $(document).ready(function () {
        $(".select2").select2();
    });
</script>
