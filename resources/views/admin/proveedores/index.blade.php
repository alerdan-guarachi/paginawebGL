@extends('adminlte::page')

@section('content_header')

@can('admin.proveedores.create')
@if ($nombreusuario != 'SIDHARTA HELEN SOTO CHUQUIMIA')
<a class="btn btn-sm float-right btn-crear" href="{{route('admin.proveedores.create')}}">CREAR PROVEEDOR</a>
@endif
@endcan
<h1>PROVEEDORES</h1>
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
                    <form action="{{ route('buscarproveedor') }}" method="get" class="form-inline">
                        <div class="flex-grow-1">
                            <input name="buscarpor" class="form-control buscador mr-sm-2" type="search" placeholder="ID / Proveedor / Ciudad" aria-label="Search">
                        </div>
                        <button id="btn-buscar" class="btn btn-buscar my-2 my-sm-0" type="submit" disabled>BUSCAR</button>
                    </form>
                </div>
            </div>
        </nav>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Proveedor</th>
                        <th>Ciudad</th>
                        <th>Direccion</th>
                        <th>Estado</th>
                        <th colspan="2"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($proveedores as $proveedor)
                        <tr>
                            <td>{{$proveedor->id}}</td>
                            <td>{{$proveedor->proveedor}}</td>
                            <td>{{$proveedor->ciudad}}</td>
                            <td>{{$proveedor->direccion}}</td>
                            <td>{{$proveedor->estadoproveedor}}</td>
                            <td class="align-middle">
                                <style>
                                    .custom-dropdown {
                                        position: relative;
                                        display: inline-block;
                                    }
                                    .custom-select-wrapper {
                                        border: 1px solid black;
                                        background-color: #fceacf;
                                        padding: 1px;
                                        text-align: center;
                                        border-radius: 5px;
                                        width: 140px; 
                                    }
                                    .custom-dropdown-content {
                                        display: none;
                                        position: absolute;
                                        background-color: #f9f9f9;
                                        width: 200px;
                                        box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
                                        z-index: 1;
                                    }
                                    .custom-dropdown-content a {
                                        color: black;
                                        padding: 0px 5px;
                                        text-decoration: none;
                                        display: block;
                                    }
                                    .custom-dropdown-content a:hover {
                                        background-color: #eefed3;
                                    }
                                    .custom-dropdown:hover .custom-dropdown-content {
                                        display: block;
                                    }
                                </style>

                                <div class="custom-dropdown">
                                    <div class="custom-select-wrapper">
                                        <span>ACCIONES</span>
                                    </div>
                                    <div class="custom-dropdown-content">
                                        @can('admin.proveedores.index')
                                        <a href="{{ route('admin.proveedores.show', $proveedor)}}">Ver Proveedor</a>
                                        @endcan
                                        @can('admin.proveedores.crearbateriaproveedor')
                                        <a href="{{ route('admin.proveedores.crearbateriaproveedor', $proveedor)}}">Ver y Crear Batería</a>
                                        @endcan
                                        @can('admin.empresas.verbateriaproveedor')
                                        <a href="{{ route('admin.proveedores.verbateriaproveedor', $proveedor)}}">Ver Batería</a>
                                        @endcan
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@stop

@section('css')
<link rel="styleheet" href="/css/admin_custom.css">
<style>
h1, th {
    color:#94c93b; 
    font-family: "Segoe UI";
    font-weight: 900;
}
.btn-editar {
    background-color:  #ffffff;
    color: #0400ff;
    border-color: #0400ff;
    border-radius: 5px;
}
.btn-editar:hover {
    background-color: #0400ff;
    color: #ffffff;
}
.btn-eliminar {
    background-color:  #ffffff;
    color: #ff0000;
    border-color: #ff0000;
    border-radius: 5px;
}
.btn-eliminar:hover {
    background-color: #ff0000;
    color: #ffffff;
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

</style>
@stop

@section('js')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @if (session('eliminar')=='ok')
    <script>
        Swal.fire(
      '¡Eliminado!',
      'El rol se eliminó con éxito',
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