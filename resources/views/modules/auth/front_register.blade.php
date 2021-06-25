@extends('front.layouts.default')

@section('title')
    @parent Home
@stop

@section('head_page')
@stop

@section('content')

    @include('front.includes.success')
    @include('front.includes.errors')


    <!-- Page Content -->
    <div class="container">

        <!-- Page Heading/Breadcrumbs -->
        <h1 class="mt-4 mb-3">Register
        </h1>

        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ url('/') }}">Home</a>
            </li>
            <li class="breadcrumb-item active">Register</li>
        </ol>

        <form id='formData' class="form-horizontal" role="form" method="POST"  autocomplete="off" action="{{ url('/register') }}">
            {{ csrf_field() }}
            <div class="row">
                <div class="col-md-3"></div>
                <div class="col-md-6">
                    <h2>Register New User</h2>
                    <hr>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3 field-label-responsive">
                    <label for="first_name">Nombre</label>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <div class="input-group mb-2 mr-sm-2 mb-sm-0">
                            <div class="input-group-addon"><i class="fa fa-user" aria-hidden="true"></i></div>
                            <input type="text" name="first_name" class="form-control" id="first_name"
                                   placeholder="John" value="{{ old('first_name') }}" autofocus>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3 field-label-responsive">
                    <label for="last_name">Apellidos</label>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <div class="input-group mb-2 mr-sm-2 mb-sm-0">
                            <div class="input-group-addon"><i class="fa fa-user" aria-hidden="true"></i></div>
                            <input type="text" name="last_name" class="form-control" id="last_name"
                                   placeholder="Doe" value="{{ old('last_name') }}">
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3 field-label-responsive">
                    <label for="name">User name</label>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <div class="input-group mb-2 mr-sm-2 mb-sm-0">
                            <div class="input-group-addon"><i class="fa fa-user" aria-hidden="true"></i></div>
                            <input type="text" name="username" class="form-control" id="username"
                                   placeholder="john_doe" value="{{ old('username') }}" autocomplete="false">
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3 field-label-responsive">
                    <label for="email">E-Mail Address</label>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <div class="input-group mb-2 mr-sm-2 mb-sm-0">
                            <div class="input-group-addon"><i class="fa fa-at" aria-hidden="true"></i></div>
                            <input type="text" name="email" class="form-control" id="email"
                                   placeholder="you@example.com"  value="{{ old('email') }}">
                        </div>
                    </div>
                </div>

            </div>
            <div class="row">
                <div class="col-md-3 field-label-responsive">
                    <label for="password">Password</label>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <div class="input-group mb-2 mr-sm-2 mb-sm-0">
                            <div class="input-group-addon"><i class="fa  fa-key" aria-hidden="true"></i></div>
                            <input type="password" name="password" class="form-control" id="password"
                                   placeholder="Password" >
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3 field-label-responsive">
                    <label for="password">Confirm Password</label>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <div class="input-group mb-2 mr-sm-2 mb-sm-0">
                            <div class="input-group-addon">
                                <i class="fa fa-repeat" aria-hidden="true"></i>
                            </div>
                            <input type="password" name="password_confirmation" class="form-control"
                                   id="password-confirm" placeholder="Password" >
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-3"></div>
                <div class="col-md-6">
                    <button type="submit" class="btn btn-success"><i class="fa fa-user-plus" aria-hidden="true"></i> Register</button>
                </div>
            </div>
        </form>



    </div>
    <!-- /.container -->
    <br />
    <br />
    <br />
    <br />
    <br />
    <br />
    <br />

@endsection

@section("foot_page")
    <script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js')}}"></script>

    {!! JsValidator::formRequest('App\Http\Requests\FrontRegisterUserRequest')->selector('#formData') !!}
@stop
