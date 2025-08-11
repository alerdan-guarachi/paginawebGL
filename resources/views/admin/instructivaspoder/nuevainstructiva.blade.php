@extends('adminlte::page')
    
@section('content_header')
<h1>INSTRUCTIVAS DE PODER</h1>
@stop 

@section('css')
<link rel="stylesheet" href="{{ asset('css/estilogl.css') }}">
<style>
    .table td {
        padding: 5px 10px;
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
    .btn-bateria {
        background-color:  #ffffff;
        color: #94c93b;
        border-color: #94c93b;
        border-radius: 5px;
        padding: 3px 7px;
    }
    .btn-bateria:hover {
        background-color: #94c93b;
        color: #ffffff;
    }
</style>
@stop

@section('content')
@if (session('info'))
    <div id="alert-info" class="alert alert-success">
        <strong>{{ session('info') }}</strong>
    </div>
    <script>
        setTimeout(function() {
            $('#alert-info').fadeOut('fast');
        }, 3000);
    </script>
@endif

<div class="card">
    <nav class="navbar navbar-expand-lg float-right" style="margin-top: 10px;">
        <div class="container-fluid">
            <div class="d-flex flex-wrap align-items-center">
                <form action="{{ route('buscarclientesitainstructiva') }}" method="get" class="form-inline">
                    <div class="flex-grow-1">
                        <input name="buscarpor" class="form-control mr-sm-2" type="search" placeholder="Buscar por Nombre  /  CI  / ID" aria-label="Search" style="width: 400px;">
                    </div>
                    <button id="btn-buscar" class="btn btn-buscar my-2 my-sm-0" type="submit" disabled>Buscar</button>
                </form>
            </div>
        </div>
    </nav>
    
    <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs" id="myTabs">
            <li class="nav-item">
                <a class="nav-link active" id="tab-1" data-toggle="tab" href="#tab-content-1" role="tab" aria-controls="tab-content-1" aria-selected="true">
                    CLIENTES ITA
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="tab-2" data-toggle="tab" href="#tab-content-2" role="tab" aria-controls="tab-content-2" aria-selected="true">
                    CLIENTES AUDITORIA
                </a>
            </li>
        </ul>
    </div>
    
    <div class="card-body">
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="tab-content-1" role="tabpanel" aria-labelledby="tab-1">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombres y Apellidos</th>
                                <th>CI</th>
                                <th>Edad</th>
                                <th>Sucursal</th>
                                <th>Ver</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($clientes as $cliente)
                                <tr>
                                    <td class="align-middle">{{$cliente->id}}</td>
                                    <td class="align-middle">{{$cliente->nombrecompleto}}</td>
                                    <td class="align-middle">{{$cliente->ci}}</td>
                                    <td class="align-middle">{{$cliente->edad}}</td>
                                    <td class="align-middle">{{$cliente->sucursal}}</td>
                                    <td width="10px">
                                        <abbr title="CREAR INSTRUCTIVA DE PODER">
                                            <a class="btn btn-sm btn-bateria" href="{{ route('admin.instructivaspoder.crearinstructivapoder', $cliente) }}">
                                                <i class="fas fa-file"></i>
                                            </a>
                                        </abbr>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{ $clientes->links() }}
            </div>

            <div class="tab-pane fade" id="tab-content-2" role="tabpanel" aria-labelledby="tab-2">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombres y Apellidos</th>
                                <th>CI</th>
                                <th>Edad</th>
                                <th>Sucursal</th>
                                <th>Ver</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($clientesauditoria as $clienteauditoria)
                                <tr>
                                    <td class="align-middle">{{$clienteauditoria->id}}</td>
                                    <td class="align-middle">{{$clienteauditoria->nombrecompleto}}</td>
                                    <td class="align-middle">{{$clienteauditoria->ci}}</td>
                                    <td class="align-middle">{{$clienteauditoria->edad}}</td>
                                    <td class="align-middle">{{$clienteauditoria->sucursal}}</td>
                                    <td width="10px">
                                        <abbr title="CREAR INSTRUCTIVA DE PODER">
                                            <a class="btn btn-sm btn-bateria" href="{{ route('admin.instructivaspoder.crearinstructivapoderauditoria', $clienteauditoria) }}">
                                                <i class="fas fa-file"></i>
                                            </a>
                                        </abbr>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{ $clientes->links() }}
            </div>
        </div>
    </div>
</div>

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