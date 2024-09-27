@extends('adminlte::page')

@section('content_header')
<a class="btn btn-sm float-right btn-regresar" href="{{ route('admin.asociados.index') }}">REGRESAR</a>
<h5>BATERIA DE:</h5>
<h3>{{$asociado->asociado}}</h3>
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
        {{-- <nav class="navbar navbar-expand-lg float-right">
            <div class="container-fluid">
                <div class="d-flex flex-wrap align-items-center">
                    <form action="{{ route('buscarclientesita') }}" method="get" class="form-inline">
                        <div class="flex-grow-1">
                            <input name="buscarpor" class="form-control mr-sm-2" type="search" placeholder="Cliente  /  CI  " aria-label="Search">
                        </div>
                        <button id="btn-buscar" class="btn btn-buscar my-2 my-sm-0" type="submit" disabled>Buscar</button>
                    </form>
                </div>
            </div>
        </nav> --}}
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Tipo de área</th>
                    <th>Área</th>
                    <th>Acción</th>
                    <th>Sucursal</th>
                    <th>Precio</th>
                    <th>Estado</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($asociados as $asociado)
                    <tr>
                        <td>{{$asociado->tiponombre}}</td>
                        <td>{{$asociado->area}}</td>
                        <td>{{$asociado->accion}}</td>
                        <td>{{$asociado->sucursal}}</td>
                        <td>{{$asociado->precio}}</td>
                        <td>{{$asociado->estado}}</td>
                        {{-- @can('admin.asociados.verclienteita') --}}
                        {{-- <td width="10px">
                            <abbr title="Editar accion">
                                <a class="btn btn-sm btn-editaraccion" href="{{ route('admin.asociados.editarbateriaasociado', $asociado) }}">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </abbr>
                        </td> --}}
                        {{-- @endcan --}}
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection
@section('js')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type="text/javascript" src="https://jeremyfagis.github.io/dropify/dist/js/dropify.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://jeremyfagis.github.io/dropify/dist/css/dropify.min.css"> 
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" />

<script>
$('.dropify').dropify();
</script>
@stop

@section('css')

<style>
    .btn-editaraccion {
        background-color:  #ffffff;
        color: #94c93b;
        border-color: #94c93b;
        border-radius: 5px;
    }
    .btn-editaraccion:hover {
        background-color: #94c93b;
        color: #ffffff;
    }
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
    
    th, td {
        padding: 3px;
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
    
</style>
<style>
    .btn-cerrar {
        background-color: #ffffff;
        color: #94c93b;
        border-color: #94c93b;
        border-radius: 5px;
        padding: 5px 10px;
    }
    .btn-cerrar:hover {
        background-color: #94c93b;
        color: #ffffff;
    }
    .btn-editar {
        width: 100px;
        height: 90px;
        font-size: 24px;
        line-height: 80px;
        text-align: center;
        padding: 0;
        background-color: #ffffff;
        color: #faa625;
        border-color: #faa625;
        border-radius: 5px;
    }
    .btn-editar:hover {
        background-color: #faa625;
        color: #ffffff;
    }
    .btn-editar i {
        display: inline-block;
        vertical-align: middle;
    }
    .btn-bateria {
        width: 100px;
        height: 90px;
        font-size: 24px;
        line-height: 80px;
        text-align: center;
        padding: 0;
        background-color: #ffffff;
        color: #47b4e7;
        border-color: #47b4e7;
        border-radius: 5px;
    }
    .btn-bateria:hover {
        background-color: #47b4e7;
        color: #ffffff;
    }
    .btn-bateria i {
        display: inline-block;
        vertical-align: middle;
    }
    .btn-cotizacion {
        width: 100px;
        height: 90px;
        font-size: 24px;
        line-height: 80px;
        text-align: center;
        padding: 0;
        background-color: #ffffff;
        color: #c8ce22;
        border-color: #c8ce22;
        border-radius: 5px;
    }
    .btn-cotizacion:hover {
        background-color: #c8ce22;
        color: #ffffff;
    }
    .btn-cotizacion i {
        display: inline-block;
        vertical-align: middle;
    }
    .btn-aprobacion {
        width: 100px;
        height: 90px;
        font-size: 24px;
        line-height: 80px;
        text-align: center;
        padding: 0;
        background-color: #ffffff;
        color: #20a425;
        border-color: #20a425;
        border-radius: 5px;
    }
    .btn-aprobacion:hover {
        background-color: #20a425;
        color: #ffffff;
    }
    .btn-aprobacion i {
        display: inline-block;
        vertical-align: middle;
    }
    .btn-programar {
        width: 100px;
        height: 90px;
        font-size: 24px;
        line-height: 80px;
        text-align: center;
        padding: 0;
        background-color: #ffffff;
        color: #1d31e2;
        border-color: #1d31e2;
        border-radius: 5px;
    }
    .btn-programar:hover {
        background-color: #1d31e2;
        color: #ffffff;
    }
    .btn-programar i {
        display: inline-block;
        vertical-align: middle;
    }
    .btn-estado {
        width: 100px;
        height: 90px;
        font-size: 24px;
        line-height: 80px;
        text-align: center;
        padding: 0;
        background-color: #ffffff;
        color: #2f755a;
        border-color: #2f755a;
        border-radius: 5px;
    }
    .btn-estado:hover {
        background-color: #2f755a;
        color: #ffffff;
    }
    .btn-estado i {
        display: inline-block;
        vertical-align: middle;
    }
    .btn-subirdocumento {
        width: 100px;
        height: 90px;
        font-size: 24px;
        line-height: 80px;
        text-align: center;
        padding: 0;
        background-color: #ffffff;
        color: #c547e8;
        border-color: #c547e8;
        border-radius: 5px;
    }
    .btn-subirdocumento:hover {
        background-color: #c547e8;
        color: #ffffff;
    }
    .btn-subirdocumento i {
        display: inline-block;
        vertical-align: middle;
    }
    .btn-listadodocumentos {
        width: 100px;
        height: 90px;
        font-size: 24px;
        line-height: 80px;
        text-align: center;
        padding: 0;
        background-color: #ffffff;
        color: #531f84;
        border-color: #531f84;
        border-radius: 5px;
    }
    .btn-listadodocumentos:hover {
        background-color: #531f84;
        color: #ffffff;
    }
    .btn-listadodocumentos i {
        display: inline-block;
        vertical-align: middle;
    }
    .btn-formulario {
        width: 100px;
        height: 90px;
        font-size: 24px;
        line-height: 80px;
        text-align: center;
        padding: 0;
        background-color: #ffffff;
        color: #8f6542;
        border-color: #8f6542;
        border-radius: 5px;
    }
    .btn-formulario:hover {
        background-color: #8f6542;
        color: #ffffff;
    }
    .btn-formulario i {
        display: inline-block;
        vertical-align: middle;
    }
    .btn-etiqueta {
        width: 100px;
        height: 90px;
        font-size: 24px;
        line-height: 80px;
        text-align: center;
        padding: 0;
        background-color: #ffffff;
        color: #2e5362;
        border-color: #2e5362;
        border-radius: 5px;
    }
    .btn-etiqueta:hover {
        background-color: #2e5362;
        color: #ffffff;
    }
    .btn-etiqueta i {
        display: inline-block;
        vertical-align: middle;
    }
    .btn-requisitos {
        width: 100px;
        height: 90px;
        font-size: 24px;
        line-height: 80px;
        text-align: center;
        padding: 0;
        background-color: #ffffff;
        color: #556f34;
        border-color: #556f34;
        border-radius: 5px;
    }
    .btn-requisitos:hover {
        background-color: #556f34;
        color: #ffffff;
    }
    .btn-requisitos i {
        display: inline-block;
        vertical-align: middle;
    }
    .btn-contactos {
        width: 100px;
        height: 90px;
        font-size: 24px;
        line-height: 80px;
        text-align: center;
        padding: 0;
        background-color: #ffffff;
        color: #94c93b;
        border-color: #94c93b;
        border-radius: 5px;
    }
    .btn-contactos:hover {
        background-color: #94c93b;
        color: #ffffff;
    }
    .btn-contactos i {
        display: inline-block;
        vertical-align: middle;
    }
    
</style>
@stop
