@extends('front.layouts.default')

@section('title')
    @parent {{ $page_title }}
@stop

@section('head_page')
@stop

@section('content')

    <!-- Page Content -->
    <div class="container">

        <!-- Page Heading/Breadcrumbs -->
        <h1 class="mt-4 mb-3">{{ $page_title }}</h1>

        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ url('/') }}">Home</a>
            </li>
            <li class="breadcrumb-item active">{{ $page_title }}</li>
        </ol>


        <h3 class="section-title font-alt mt0">
            {{ $isSubscribed?
                trans('Newsletter::front_lang.newsletter_desc_unsubscribe'):
                trans('Newsletter::front_lang.newsletter_desc') }}
        </h3>
        <br clear="all">

        @include('front.includes.errors', array('errors' => $errors))
        @include('front.includes.success')

        <div class='row'>
            <div class='col-md-12'>

                {{ Form::open(array('url' => 'newsletter/subscribe', 'method' => 'POST', 'id' => 'formFolder')) }}
                {!! Form::hidden('isSubscribed', $isSubscribed, array('id' => 'isSubscribed')) !!}

                <div class="row">
                    <div class="col-lg-12 form-group">
                        {{ $user->userProfile->fullname }}
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-6 form-group">
                        {!! Form::label('email', trans('Newsletter::front_lang.newsletter_email')) !!} <span class="text-danger">*</span>
                        {!! Form::text('email', $user->email, array('placeholder' =>  trans('Newsletter::front_lang.newsletter_instructions'), 'class' => 'form-control', 'readonly' => 'readonly')) !!}
                    </div>
                </div>
                <br clear="all">

                <div class="box-footer">

                    <button type="submit" class="btn btn-info">
                        {{ $isSubscribed?
                           trans('Newsletter::front_lang.newsletter_unsubscribe'):
                           trans('Newsletter::front_lang.newsletter_subscribe') }}

                    </button>

                </div>

                {!! Form::close() !!}

            </div><!-- /.col -->
        </div><!-- /.row -->




    </div>
    <!-- /.container -->

    <br><br><br><br><br><br><br>

@endsection

@section("foot_page")

@stop