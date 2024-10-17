@extends('adminlte::page')
    
@section('content_header')
<a class="btn btn-sm float-right btn-regresar" href="{{route('admin.asociados.index')}}">REGRESAR</a>
@can('admin.asociados.crearclienteita')
<a class="btn btn-sm float-right btn-crearcliente" href="{{ route('admin.asociados.crearclienteita', 6) }}">CREAR CLIENTE</a>
@endcan
<h1>CLIENTES ITA</h1>
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
                    <form action="{{ route('buscarclientesita') }}" method="get" class="form-inline">
                        <div class="flex-grow-1">
                            <input name="buscarpor" class="form-control mr-sm-2" type="search" placeholder="Nombre  /  CI  / ID" aria-label="Search">
                        </div>
                        <button id="btn-buscar" class="btn btn-buscar my-2 my-sm-0" type="submit" disabled>Buscar</button>
                    </form>
                </div>
            </div>
        </nav>
        <table class="table table-striped">
            <thead>
                <tr> 
                    <th style="width: 20px;">ID</th>
                    <th style="width: 200px;">Nombres y Apellidos</th>
                    <th style="width: 150px;">Servicio</th>
                    <th style="width: 100px;">CI</th>
                    <th style="width: 80px;">Edad</th>
                    <th style="width: 120px;">Sucursal</th>
                    <th style="width: 50px;"></th>
                </tr>
                
            </thead>
            <tbody>
                @foreach ($clientes as $cliente)
                    <tr>
                        <td class="align-middle">{{$cliente->id}}</td>
                        <td class="align-middle">{{$cliente->nombrecompleto}}</td>
                        <td class="align-middle"> 
                            {{ implode(', ', $cliente->servicios->pluck('tramite')->unique()->toArray()) }} <!-- Mostrar servicios únicos separados por comas -->
                        </td>                        
                        <td class="align-middle">{{$cliente->ci}}</td>
                        <td class="align-middle">{{$cliente->edad}}</td>
                        <td class="align-middle">{{$cliente->sucursal}}</td>
                        @can('admin.asociados.verclienteita')
                        <td width="10px">
                            <abbr title="Ver Cliente">
                                <a class="btn btn-sm btn-bateria" href="{{ route('admin.asociados.verclienteita', $cliente) }}">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </abbr>
                        </td>
                        @endcan
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
    .btn-crearcliente {
        background-color: #ffffff;
        color: #94c93b;
        border-color: #94c93b;
        border-radius: 5px;
        padding: 10px 10px;
        margin-left: 10px;
        margin-right: 10px;
    }
    .btn-crearcliente:hover {
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