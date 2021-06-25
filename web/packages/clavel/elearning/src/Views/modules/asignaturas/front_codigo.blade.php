@extends('front.layouts.default')

@section('title')
@parent {{ $page_title }}
@stop

@section('breadcrumb')
<li class="active">{{ $page_title }}</li>
@stop

@section('content')


{!! Form::open($form_data, ['role' => 'form', 'class' => 'codigo-form text-black pt-sm']) !!}
{!! Form::hidden('asignatura_id', $asignatura->id, array('id' => 'asignatura_id')) !!}
<div class="container pb-xlg">
    @include('front.includes.errors')
    <div class="d-flex justify-content-center align-items-center">


        <div class="light-rounded-box row p-lg">
            <div class="d-flex align-items-center pb-lg">
                <div class="svg_img_wrapper">
                    <i class="fa fa-barcode text-primary" aria-hidden="true" class="t"></i>
                </div>
                <h4 class="mt-xs mb-xs">{{ trans('elearning::asignaturas/front_lang.codigo_de_asignatura') }}</h4>
            </div>
            <div class="col-sm-12">
                <p>{{ trans("elearning::asignaturas/front_lang.info_acceso") }}</p>
                <div style="padding-bottom: 30px;">
                    <div class="form-group text-left">
                        {!! Form::label('codigo', trans('profile/front_lang.codigo')) !!} <span class="text-danger">*</span>
                        {!! Form::text('codigo', null, array('placeholder' =>  trans('profile/front_lang.codigo'), 'class' => 'form-control', 'id'=>'codigo')) !!}
                    </div>

                    <div class="box-footer">
                        <button id="btnSendCode" onclick="validateCode();" class="btn btn-default text-uppercase background-color-secondary pt-sm pr-xlg pb-sm pl-xlg mt-xlg text-light has-spinner"><span class="spinner"><img src="{{ asset("assets/front/img/ajax_loader_vector.gif") }}" width="16" alt=""> </span> {{ trans('profile/front_lang.enviar') }}</button>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>
{!! Form::close() !!}




@endsection




@section('foot_page')
<script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js')}}"></script>


<script>
    $(document).ready(function() {
        $('.codigo-form input').keypress(function(e) {
            if (e.which == 13) {
                $('.login-form').submit(); //form validation success, call ajax form submit
                return false;
            }
        });
    });
</script>

{!! JsValidator::formRequest('Clavel\Elearning\Requests\CodigoAsignaturaRequest')->selector('#formData') !!}

@endsection
