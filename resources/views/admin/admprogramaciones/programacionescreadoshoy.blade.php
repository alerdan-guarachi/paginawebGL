@extends('adminlte::page')
    
@section('content_header')
<h1>PROGRAMACIONES CREADAS {{ $fechaActual }}</h1>
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
        <nav class="navbar navbar-expand-lg float-right">
            <div class="container-fluid">
                <div class="d-flex flex-wrap align-items-center">
                    <form action="{{ route('buscarprogramacionesporfecha') }}" method="get" class="form-inline">
                        <div class="flex-grow-1">
                            <!-- Utilizamos el input de tipo 'date' para seleccionar la fecha -->
                            <input name="buscarpor" class="form-control buscador mr-sm-2" type="date" 
                                  max="{{ now()->toDateString() }}" value="{{ old('buscarpor') ?? $fechaActual }}" aria-label="Fecha">
                            <!-- Aquí $fechaActual es la fecha actual obtenida en el controlador -->
                        </div>
                        <button id="btn-buscar" class="btn btn-buscar my-2 my-sm-0" type="submit">BUSCAR</button>
                    </form>
                </div>
            </div>
        </nav>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Tipo de Cliente</th>
                    <th>Cliente</th>
                    <th>Proveedor</th>
                    <th>Acción</th>
                    <th>Fecha de bateria</th>
                    <th>Fecha programada</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($programacioneshoyita as $programacion)
                    <tr>
                        <td>CLIENTE ITA</td>
                        <td>{{$programacion->clienteitanombre}}</td>
                        <td>{{$programacion->proveedornombre}}</td>
                        <td>{{$programacion->accionnombre}}</td>
                        <td>{{$programacion->fechabateria}}</td>
                        <td>{{$programacion->fechaasignada}}</td>
                    </tr>
                @endforeach
                @foreach ($programacioneshoycomun as $programacion)
                    <tr>
                        <td>CLIENTE COMÚN</td>
                        <td>{{$programacion->clientecomunnombre}}</td>
                        <td>{{$programacion->proveedornombre}}</td>
                        <td>{{$programacion->accionnombre}}</td>
                        <td>{{$programacion->fechabateria}}</td>
                        <td>{{$programacion->fechaasignada}}</td>
                    </tr>
                @endforeach
                @foreach ($programacioneshoyauditoria as $programacion)
                    <tr>
                        <td>CLIENTE AUDITORÍA</td>
                        <td>{{$programacion->clienteauditorianombre}}</td>
                        <td>{{$programacion->proveedornombre}}</td>
                        <td>{{$programacion->accionnombre}}</td>
                        <td>{{$programacion->fechabateria}}</td>
                        <td>{{$programacion->fechaasignada}}</td>
                    </tr>
                @endforeach
                
            </tbody>
        </table>
    </div>
</div>

@stop

@section('css')
<link rel="styleheet" href="/css/admin_custom.css">
<style>
    .custom-select-wrapper {
        position: relative;
        display: inline-block;
        width: 150px;
    }
    .custom-select-wrapper select {
        width: 100%;
        padding: 6px 26px 6px 10px;
        font-size: 14px;
        border: none;
        border-radius: 3px;
        background-color: #f8f9fa;
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        cursor: pointer;
    }
    .custom-select-wrapper select:focus {
        outline: none;
    }
    .custom-select-icon {
        position: absolute;
        top: 50%;
        right: 10px;
        transform: translateY(-50%);
        pointer-events: none;
        color: #000000;
    }
    .custom-select-wrapper select {
        background-color:  #eff9df;
        color: #000000;
        border-color: #000000;
        border-radius: 5px;
        padding: 10px 20px;
    }
    .custom-select-wrapper select:hover {
        background-color: #f4e1c6;
        color: #000000;
    }
    .custom-select-wrapper select option {
        background-color: #ffffff;
    }
    h1, th {
        color:#94c93b; 
        font-family: "Segoe UI";
        font-weight: 900;
    }
    .btn-crear {
        background-color:  #ffffff;
        color: #94c93b;
        border-color: #94c93b;
        border-radius: 5px;
        padding: 10px 20px;
    }
    .btn-crear:hover {
        background-color: #94c93b;
        color: #ffffff;
    }
    .btn-buscar { 
        background-color:  #ffffff;
        color: #faa625;
        border-color: #faa625;
        border-radius: 5px;
    }
    .btn-buscar:hover {
        background-color: #faa625;
        color: #ffffff;
    }  
    .btn-mostrartodo { 
        background-color:  #ffffff;
        color: #94c93b;
        border-color: #94c93b;
        border-radius: 5px;
    }
    .btn-mostrartodo:hover {
        background-color: #94c93b;
        color: #ffffff;
    }
    .btn-bateria {
        background-color:  #ffffff;
        color: #94c93b;
        border-color: #94c93b;
        border-radius: 5px;
    }
    .btn-bateria:hover {
        background-color: #94c93b;
        color: #ffffff;
    }
    .btn-programar {
        background-color:  #ffffff;
        color: #2136bd;
        border-color: #2136bd;
        border-radius: 5px;
    }
    .btn-programar:hover {
        background-color: #2136bd;
        color: #ffffff;
    }
    .btn-estadoprogramacion {
        background-color:  #ffffff;
        color: #58a6f4;
        border-color: #58a6f4;
        border-radius: 5px;
    }
    .btn-estadoprogramacion:hover {
        background-color: #58a6f4;
        color: #ffffff;
    }
    .btn-subirdocumentacion {
        background-color:  #ffffff;
        color: #faa625;
        border-color: #faa625;
        border-radius: 5px;
    }
    .btn-subirdocumentacion:hover {
        background-color: #faa625;
        color: #ffffff;
    }
    .btn-verdocumentacion {
        background-color:  #ffffff;
        color: #8721f3;
        border-color: #8721f3;
        border-radius: 5px;
    }
    .btn-verdocumentacion:hover {
        background-color: #8721f3;
        color: #ffffff;
    }
    .btn-formulario {
        background-color:  #ffffff;
        color: #ea3ab8;
        border-color: #ea3ab8;
        border-radius: 5px;
    }
    .btn-formulario:hover {
        background-color: #ea3ab8;
        color: #ffffff;
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
    .btn-verdocumentacion {
        background-color:  #ffffff;
        color: #faa625;
        border-color: #faa625;
        border-radius: 5px;
    }
    .btn-verdocumentacion:hover {
        background-color: #faa625;
        color: #ffffff;
    }
    .btn-verimagen {
        background-color:  #ffffff;
        color: #faa625;
        border-color: #faa625;
        border-radius: 5px;
    }
    .btn-verimagen:hover {
        background-color: #faa625;
        color: #ffffff;
    }
    .btn-descargarimagen {
        background-color:  #ffffff;
        color: #94c93b;
        border-color: #94c93b;
        border-radius: 5px;
    }
    .btn-descargarimagen:hover {
        background-color: #94c93b;
        color: #ffffff;
    }
    .btn-descargardocumentacion {
        background-color:  #ffffff;
        color: #94c93b;
        border-color: #94c93b;
        border-radius: 5px;
    }
    .btn-descargardocumentacion:hover {
        background-color: #94c93b;
        color: #ffffff;
    }
</style>
@stop

@section('js')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type="text/javascript" src="https://jeremyfagis.github.io/dropify/dist/js/dropify.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://jeremyfagis.github.io/dropify/dist/css/dropify.min.css"> 
<script>
$('.dropify').dropify();
</script>
    @if (session('eliminar')=='ok')
    <script>
        Swal.fire(
      '¡Eliminado!',
      'El perfil se eliminó con éxito',
      'success')
    </script>
    @endif
<script>
    $('.formulario-eliminar').submit(function(e){
        e.preventDefault();

        Swal.fire({
        title: '¿Estás seguro?',
        text: "Este perfil se eliminará definitivamente",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: '¡Si, eliminar!',
        cancelButtonText: 'Cancelar'
        }).then((result) => {
        if (result.isConfirmed) {
            this.submit();
        }
        }) 
    });
    $(document).ready(function() {
        $('input[name="buscarpor"]').on('keyup', function() {
            var query = $(this).val();
            var botonBuscar = $('#btn-buscar');
            if (query.trim() === '') {
                botonBuscar.prop('disabled', true);
            } else {
                botonBuscar.prop('disabled', false);
            }
        });
    });
</script>
@endsection