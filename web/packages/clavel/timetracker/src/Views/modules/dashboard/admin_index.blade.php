@extends('admin.layouts.default')

@section('title')
    @parent {{ $page_title }}
@stop

@section('head_page')

@stop

@section('breadcrumb')
    <li class="active">{{ trans('timetracker::dashboard/admin_lang.title') }}</li>
@stop

@section('content')

    @include('admin.includes.modals')

    <!-- Main content -->
    <section class="content">

    <h2 class="page-header">My statistics</h2>

    <div class="row">
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon bg-aqua"><i class="fa fa-clock-o" aria-hidden="true"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">Working hours today</span>
                    <span class="info-box-number">00:00 h</span>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>
        <!-- /.col -->
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon bg-green"><i class="fa fa-calendar-check-o" aria-hidden="true"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">Working hours this week</span>
                    <span class="info-box-number">00:00 h</span>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>
        <!-- /.col -->
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon bg-purple"><i class="fa fa-calendar" aria-hidden="true"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">Working hours this month</span>
                    <span class="info-box-number">00:00 h</span>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>
        <!-- /.col -->
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon bg-yellow"><i class="fa fa-star-o" aria-hidden="true"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">Working hours this year</span>
                    <span class="info-box-number">00:00 h</span>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>
        <!-- /.col -->
    </div>
    <!-- /.row -->

    <h2 class="page-header">All user</h2>

        <div class="row">
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-aqua"><i class="fa fa-clock-o" aria-hidden="true"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">Working hours today</span>
                        <span class="info-box-number">00:00 h</span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>
            <!-- /.col -->
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-green"><i class="fa fa-calendar-check-o" aria-hidden="true"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">Working hours this week</span>
                        <span class="info-box-number">00:00 h</span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>
            <!-- /.col -->
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-purple"><i class="fa fa-calendar" aria-hidden="true"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">Working hours this month</span>
                        <span class="info-box-number">00:00 h</span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>
            <!-- /.col -->
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-yellow"><i class="fa fa-star-o" aria-hidden="true"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">Working hours this year</span>
                        <span class="info-box-number">00:00 h</span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->

        <div class="row">
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-aqua"><i class="fa fa-user" aria-hidden="true"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">Active users today</span>
                        <span class="info-box-number">0</span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>
            <!-- /.col -->
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-green"><i class="fa fa-users" aria-hidden="true"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">Active users this week</span>
                        <span class="info-box-number">0</span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>
            <!-- /.col -->
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-purple"><i class="fa fa-industry" aria-hidden="true"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">Active users this month</span>
                        <span class="info-box-number">0</span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>
            <!-- /.col -->
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-yellow"><i class="fa fa-building-o" aria-hidden="true"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">Active users this year</span>
                        <span class="info-box-number">0</span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->


    <h2 class="page-header">General</h2>

    <!-- =========================================================== -->

    <!-- Small boxes (Stat box) -->
    <div class="row">
        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-aqua">
                <div class="inner">
                    <h3>0</h3>

                    <p>Amount users</p>
                </div>
                <div class="icon">
                    <i class="fa fa-user" aria-hidden="true"></i>
                </div>
                <a href="admin/users" class="small-box-footer">
                    More info <i class="fa fa-arrow-circle-right" aria-hidden="true"></i>
                </a>
            </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-green">
                <div class="inner">
                    <h3>0</h3>

                    <p>Amount customers</p>
                </div>
                <div class="icon">
                    <i class="fa fa-users" aria-hidden="true"></i>
                </div>
                <a href="admin/customers" class="small-box-footer">
                    More info <i class="fa fa-arrow-circle-right" aria-hidden="true"></i>
                </a>
            </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-yellow">
                <div class="inner">
                    <h3>0</h3>

                    <p>Amount open projects</p>
                </div>
                <div class="icon">
                    <i class="fa fa-share-alt" aria-hidden="true"></i>
                </div>
                <a href="admin/projects" class="small-box-footer">
                    More info <i class="fa fa-arrow-circle-right" aria-hidden="true"></i>
                </a>
            </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-red">
                <div class="inner">
                    <h3>0</h3>

                    <p>Amount closed projects</p>
                </div>
                <div class="icon">
                    <i class="fa fa-share-alt-square" aria-hidden="true"></i>
                </div>
                <a href="admin/projects" class="small-box-footer">
                    More info <i class="fa fa-arrow-circle-right" aria-hidden="true"></i>
                </a>
            </div>
        </div>
        <!-- ./col -->
    </div>
    <!-- /.row -->

    </section>
@endsection

@section("foot_page")


    <script type="text/javascript">

        $(function () {


        });



    </script>
@stop
