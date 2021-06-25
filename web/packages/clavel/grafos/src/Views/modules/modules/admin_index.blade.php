@extends('admin.layouts.default')

@section('title')
@parent {{ $page_title }}
@stop

@section('head_page')
<style>

</style>
@stop

@section('breadcrumb')
<li class="active">{{ $page_title }}</li>
@stop

@section('content')

<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title">Demostraciones de mapas</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body table-responsive no-padding">
                <table class="table table-hover">
                    <tbody>
                        <tr>
                            <th>Titulo</th>
                            <th>Motor</th>
                            <th>Descripción</th>
                            <th></th>
                        </tr>

                        <tr>
                            <td>Prueba Lat Long</td>
                            <td>
                                <span class="label label-danger">JS</span>
                                <span class="label label-primary">LeafFlet</span>
                            </td>
                            <td>Pruebas mapas</td>
                            <td>
                                <a type="button" class="btn btn-primary btn-sm" href="{{url('/admin/grafos/latlong')}}"><i
                                        class="fa fa-fw fa-map-o"></i> Ir</a>
                            </td>
                        </tr>

                        <tr>
                            <td>Prueba enrutado JS con GraphHopper</td>
                            <td>
                                <span class="label label-success">GraphHopper</span>
                                <span class="label label-danger">JS</span>
                                <span class="label label-primary">LeafFlet</span>
                            </td>
                            <td>Cálculo de la ruta mediante JS GraphHopper y presentación con LeafFlet</td>
                            <td>
                                <a type="button" class="btn btn-primary btn-sm" href="{{url('/admin/grafos/js')}}"><i
                                        class="fa fa-fw fa-map-o"></i> Ir</a>
                            </td>
                        </tr>


                        <tr>
                            <td>Prueba enrutado PHP con GraphHopper</td>
                            <td>
                                <span class="label label-success">GraphHopper</span>
                                <span class="label label-warning">PHP</span>
                                <span class="label label-primary">LeafFlet</span>
                            </td>
                            <td>Cálculo de la ruta mediante PHP GraphHopper y presentación con LeafFlet</td>
                            <td>
                                <a type="button" class="btn btn-primary btn-sm" href="{{url('/admin/grafos/lf')}}"><i
                                        class="fa fa-fw fa-map-o"></i> Ir</a>
                            </td>
                        </tr>


                        <tr>
                            <td>Prueba Routing API PHP con GraphHopper</td>
                            <td>
                                <span class="label label-success">GraphHopper</span>
                                <span class="label label-danger">PHP</span>
                            </td>
                            <td>Cálculo de la ruta mediante PHP GraphHopper</td>
                            <td>
                                <a type="button" class="btn btn-primary btn-sm" href="{{url('/admin/grafos/ra')}}"><i
                                        class="fa fa-fw fa-map-o"></i> Ir</a>
                            </td>
                        </tr>



                        <tr>
                            <td>Prueba optimizacion de rutas con JS con GraphHopper</td>
                            <td>
                                <span class="label label-success">GraphHopper</span>
                                <span class="label label-danger">JS</span>
                                <span class="label label-primary">LeafFlet</span>
                            </td>
                            <td>Cálculo de optimización de rutas mediante JS GraphHopper y presentación con LeafFlet</td>
                            <td>
                                <a type="button" class="btn btn-primary btn-sm" href="{{url('/admin/grafos/vrpjs')}}"><i
                                        class="fa fa-fw fa-map-o"></i> Ir</a>
                            </td>
                        </tr>

                        <tr>
                            <td>Prueba optimizacion de rutas con PHP con GraphHopper</td>
                            <td>
                                <span class="label label-success">GraphHopper</span>
                                <span class="label label-warning">PHP</span>
                                <span class="label label-primary">LeafFlet</span>
                            </td>
                            <td>Cálculo de optimización de rutas mediante PHP GraphHopper y presentación con LeafFlet</td>
                            <td>
                                <a type="button" class="btn btn-primary btn-sm" href="{{url('/admin/grafos/vrpphp')}}"><i
                                        class="fa fa-fw fa-map-o"></i> Ir</a>
                            </td>
                        </tr>

                        <tr>
                            <td>Prueba Optimization API PHP con GraphHopper</td>
                            <td>
                                <span class="label label-success">GraphHopper</span>
                                <span class="label label-danger">PHP</span>
                            </td>
                            <td>Cálculo de optimización de rutas con PHP GraphHopper</td>
                            <td>
                                <a type="button" class="btn btn-primary btn-sm" href="{{url('/admin/grafos/vrp')}}"><i
                                        class="fa fa-fw fa-map-o"></i> Ir</a>
                            </td>
                        </tr>

                        <tr>
                            <td>Prueba Geocoding API PHP con GraphHopper</td>
                            <td>
                                <span class="label label-success">GraphHopper</span>
                                <span class="label label-danger">PHP</span>
                            </td>
                            <td>Cálculo de geocoding inverso con PHP GraphHopper</td>
                            <td>
                                <a type="button" class="btn btn-primary btn-sm" href="{{url('/admin/grafos/gc')}}"><i
                                        class="fa fa-fw fa-map-o"></i> Ir</a>
                            </td>
                        </tr>

                        <tr>
                            <td>Prueba Geocoding API JS con GraphHopper</td>
                            <td>
                                <span class="label label-success">GraphHopper</span>
                                <span class="label label-danger">JS</span>
                                <span class="label label-primary">LeafFlet</span>
                            </td>
                            <td>Cálculo de geocoding inverso con JS GraphHopper</td>
                            <td>
                                <a type="button" class="btn btn-primary btn-sm" href="{{url('/admin/grafos/gcjs')}}"><i
                                        class="fa fa-fw fa-map-o"></i> Ir</a>
                            </td>
                        </tr>

                        <tr>
                            <td>Prueba Isocronica API JS con GraphHopper</td>
                            <td>
                                <span class="label label-success">GraphHopper</span>
                                <span class="label label-danger">JS</span>
                                <span class="label label-primary">LeafFlet</span>
                            </td>
                            <td>Cálculo de Isocronica con JS GraphHopper</td>
                            <td>
                                <a type="button" class="btn btn-primary btn-sm" href="{{url('/admin/grafos/isjs')}}"><i
                                        class="fa fa-fw fa-map-o"></i> Ir</a>
                            </td>
                        </tr>









                        <tr>
                            <td>Prueba Matrix API PHP con GraphHopper</td>
                            <td>
                                <span class="label label-success">GraphHopper</span>
                                <span class="label label-danger">PHP</span>
                            </td>
                            <td>Cálculo de matriz de distancias de rutas con PHP GraphHopper</td>
                            <td>
                                <a type="button" class="btn btn-primary btn-sm" href="{{url('/admin/grafos/ma')}}"><i
                                        class="fa fa-fw fa-map-o"></i> Ir</a>
                            </td>
                        </tr>

                        <tr>
                            <td>Prueba Geocoding API PHP con GraphHopper</td>
                            <td>
                                <span class="label label-success">GraphHopper</span>
                                <span class="label label-danger">PHP</span>
                            </td>
                            <td>Cálculo de geocoding inverso con PHP GraphHopper</td>
                            <td>
                                <a type="button" class="btn btn-primary btn-sm" href="{{url('/admin/grafos/gc')}}"><i
                                        class="fa fa-fw fa-map-o"></i> Ir</a>
                            </td>
                        </tr>

                        <tr>
                            <td>Prueba Isochrone API PHP con GraphHopper</td>
                            <td>
                                <span class="label label-success">GraphHopper</span>
                                <span class="label label-danger">PHP</span>
                            </td>
                            <td>Cálculo de Isochrone API con PHP GraphHopper</td>
                            <td>
                                <a type="button" class="btn btn-primary btn-sm" href="{{url('/admin/grafos/is')}}"><i
                                        class="fa fa-fw fa-map-o"></i> Ir</a>
                            </td>
                        </tr>

                        <tr>
                            <td>Prueba Cluster API PHP con GraphHopper</td>
                            <td>
                                <span class="label label-success">GraphHopper</span>
                                <span class="label label-danger">PHP</span>
                            </td>
                            <td>Cálculo de Cluster API con PHP GraphHopper</td>
                            <td>
                                <a type="button" class="btn btn-primary btn-sm" href="{{url('/admin/grafos/ca')}}"><i
                                        class="fa fa-fw fa-map-o"></i> Ir</a>
                            </td>
                        </tr>

                        <tr>
                            <td>Prueba presentación mapa Google</td>
                            <td>
                                <span class="label label-info">Google</span>
                                <span class="label label-danger">JS</span>
                            </td>
                            <td>Cálculo de Cluster API con PHP GraphHopper</td>
                            <td>
                                <a type="button" class="btn btn-primary btn-sm" href="{{url('/admin/grafos/goo')}}"><i
                                        class="fa fa-fw fa-map-o"></i> Ir</a>
                            </td>
                        </tr>

                    </tbody>
                </table>
            </div>
            <!-- /.box-body -->
        </div>
        <!-- /.box -->
    </div>
</div>

@endsection

@section("foot_page")

@stop
