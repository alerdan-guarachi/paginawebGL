@extends('adminlte::page')

<link href="assets/img/logo.png" rel="icon">

@section('content_header')
<a class="btn btn-sm float-right btn-crear" href="{{route('admin.acciones.create')}}">NUEVA ACCIÓN</a>
<h1>LISTA DE ACCIONES</h1>
@stop

@section('css')
<link rel="stylesheet" href="{{ asset('css/estilogl.css') }}">
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
            <div class="col-lg-12">
                <nav class="navbar navbar-expand-lg float-right">
                    <div class="container-fluid">
                        <div class="d-flex flex-wrap align-items-center">
                            <form action="{{ route('buscarareaacciones') }}" method="get" class="form-inline">
                                <div class="flex-grow-1">
                                    <input name="buscarpor" class="form-control buscador mr-sm-2" type="search" placeholder="Área / Acción / Sucursal" aria-label="Search">
                                </div>
                                <button id="btn-buscar" class="btn btn-buscar my-2 my-sm-0" type="submit" disabled>BUSCAR</button>
                            </form>
                        </div>
                    </div>
                </nav>
            </div>
            <div class="col-lg-12">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Área</th>
                                <th>Acción</th>
                                <th>Proveedor</th>
                                <th>Sucursal</th>
                                <th>Venta</th>
                                <th>Compra</th>
                                <th>Asociado</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($areaacciones as $areaaccion)
                                <tr>
                                    <td>{{ $areaaccion->id }}</td>
                                    <td>{{ $areaaccion->area }}</td>
                                    <td>{{ $areaaccion->accion }}</td>
                                    <td>{{ $areaaccion->proveedor }}</td>
                                    <td>{{ $areaaccion->sucursal }}</td>
                                    <td>{{ $areaaccion->precio }}</td>
                                    <td>{{ $areaaccion->preciocompra }}</td>
                                    <td>{{ $areaaccion->asociado }}</td>
                                    <td>
                                        @if ($areaaccion->estado == 'ACTIVO')
                                            <span class="badge badge-success">{{ $areaaccion->estado }}</span>
                                        @else
                                            <span class="badge badge-danger">{{ $areaaccion->estado }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a class="btn btn-sm float-right btn-editar" href="{{ route('admin.areaacciones.edit', $areaaccion) }}">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
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
        text: "El rol se eliminará definitivamente",
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