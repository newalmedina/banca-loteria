@extends('front.layouts.default')

@section('title')
    @parent {{ trans("elearning::home/front_lang.inicio") }}
@stop

@section("head_page")
@stop

@section('content')
<div class="slider-home">

    @include('elearning::home.login_home')

</div>

@include('elearning::home.aditional_info')

@endsection

@section('foot_page')
    <script>
        $(document).ready(function() {
            $('.login-form input').keypress(function(e) {
                if (e.which == 13) {
                    $('.login-form').submit(); //form validation success, call ajax form submit
                    return false;
                }
            });
        });
    </script>
@endsection
