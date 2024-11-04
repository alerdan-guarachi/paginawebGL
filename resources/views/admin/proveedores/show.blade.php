@extends('adminlte::page')

@section('content_header')
<a class="btn btn-sm float-right btn-regresar" href="{{ route('admin.proveedores.index') }}">REGRESAR</a>
@can('admin.proveedores.edit')
<a class="btn btn-sm float-right btn-editar" href="{{route('admin.proveedores.edit', $proveedor)}}">EDITAR  PROVEEDOR</a>
@endcan
<h5>DATOS DE:</h5>
<h3>{{$proveedor->proveedor}}</h3>
@stop

@section('content')
@if (session('info'))
    <div id="alert-info" class="alert alert-success">
        <strong>{{ session('info') }}</strong>
    </div>
    <script>
        setTimeout(function() {
            $('#alert-info').fadeOut('fast');
        }, 5000);
    </script>
@endif
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-lg-4">
                <div class="profile-feed">
                    <div class="d-flex align-items-start profile-feed-item">
                        <div class="form-group col-md-12">
                            <div class="row">
                                <div class="col-md-12">
                                    <strong>DATOS PERSONALES</strong><br>
                                    <table style="width: 100%;">
                                        <tbody>
                                            <tr>
                                                <th>ID</th>
                                                <td>{{$proveedor->id}}</td>
                                            </tr>
                                            <tr> 
                                                <th>Dirección</th>
                                                <td>
                                                    {{$proveedor->direccion}}
                                                    <!-- Verifica si existe un enlace en linkubicacion -->
                                                    @if(!empty($proveedor->linkubicacion))
                                                        <!-- Añade el ícono de ubicación como un enlace y con color naranja -->
                                                        <a href="{{ $proveedor->linkubicacion }}" target="_blank" style="margin-left: 8px; color: orange;">
                                                            <i class="fas fa-map-marker-alt"></i>
                                                        </a>
                                                    @endif
                                                </td>
                                            </tr>
                                            
                                            <tr>
                                                <th>Celular</th>
                                                <td>{{$proveedor->celular}}</td>
                                            </tr>
                                            <tr>
                                                <th>Teléfono</th>
                                                <td>{{$proveedor->nit}}</td>
                                            </tr>
                                            <tr>
                                                <th>Ciudad</th>
                                                <td>{{$proveedor->ciudad}}</td>
                                            </tr>
                                            <tr>
                                                <th>Estado</th>
                                                <td>{{$proveedor->estadoproveedor}}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="profile-feed">
                    <div class="d-flex align-items-start profile-feed-item">
                        <div class="form-group col-md-12">
                            <div class="row">
                                <div class="col-md-12">
                                    <strong>TIPO DE PAGO</strong><br>
                                    <table style="width: 100%;">
                                        <tbody>
                                            <tr>
                                                <th>Modo de pago</th>
                                                <td>{{$proveedor->mododepago}}</td>
                                            </tr>
                                            <tr>
                                                <th>Banco</th>
                                                <td>{{$proveedor->banco}}</td>
                                            </tr>
                                            <tr>
                                                <th>Cuenta</th>
                                                <td>{{$proveedor->cuenta}}</td>
                                            </tr>
                                            <tr>
                                                <th>Tipo de cuenta</th>
                                                <td>{{$proveedor->tipocuenta}}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="profile-feed">
                    <div class="d-flex align-items-start profile-feed-item">
                        <div class="form-group col-md-12">
                            <div class="row">
                                <div class="col-md-12">
                                    <strong>DATOS REFERENCIALES</strong><br>
                                    <table style="width: 100%;">
                                        <tbody>
                                            <tr>
                                                <th>Nombre</th>
                                                <td>{{$proveedor->personacontacto}}</td>
                                            </tr>
                                            <tr>
                                                <th>Celular</th>
                                                <td>{{$proveedor->celularreferencia}}</td>
                                            </tr>
                                            <tr>
                                                <th>Teléfono</th>
                                                <td>{{$proveedor->telefonoreferencia}}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('js')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type="text/javascript" src="https://jeremyfagis.github.io/dropify/dist/js/dropify.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://jeremyfagis.github.io/dropify/dist/css/dropify.min.css"> 
<script>
$('.dropify').dropify();
</script>
@stop

@section('css')
<style>
    th, td {
        border-bottom: 1px solid #94c93b;
    }
    h1, th {
        color:#94c93b; 
        font-family: "Segoe UI";
        font-weight: 900;
        }
    h5 {
        color:#94c93b; 
        font-family: "Segoe UI";
        font-weight: 500;
        margin-bottom: 0%;
        }
    h3 {
        color:#94c93b; 
        font-family: "Segoe UI";
        font-weight: 1000;
        }
    table {
        border-collapse: separate;
        border-spacing: 2px;
    }
    th, td {
        padding: 10px;
    }
    td{
        text-align: right;
    }
    .btn-regresar {
        background-color: #ffffff;
        color: #2926e2;
        border-color: #2926e2;
        border-radius: 5px;
        padding: 10px 10px;
    }
    .btn-regresar:hover {
        background-color: #2926e2;
        color: #ffffff;
    }
    .btn-editar {
        background-color:  #ffffff;
        color: #faa625;
        border-color: #faa625;
        border-radius: 5px;
        padding: 10px 20px;
        margin-left: 10px;
        margin-right: 10px;
        }
    .btn-editar:hover {
        background-color: #faa625;
        color: #ffffff;
        }
</style>
@stop
