@extends('adminlte::page')

<link href="assets/img/logo.png" rel="icon">

@section('content_header')
<h1>BATERÍA DE PROVEEDORES</h1>
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
            {{-- ESPECIALIDADES --}}
            {{-- <div class="col-lg-8">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Especialidad</th>
                                <th>Proveedor</th>
                                <th>Sucursal</th>
                                <th>Venta</th>
                                @can('admin.areaacciones.verprecioscomprabateria')
                                <th>Compra</th>
                                @endcan
                                <th>Servicio</th>
                                <th>Asociado</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($areaacciones as $areaaccion)
                                <tr>
                                    <td>{{ $areaaccion->area }}</td>
                                    <td>{{ $areaaccion->proveedor }}</td>
                                    <td>{{ $areaaccion->sucursal }}</td>
                                    <td>{{ $areaaccion->precio }}</td>
                                    @can('admin.areaacciones.verprecioscomprabateria')
                                    <td>{{ $areaaccion->preciocompra }}</td>
                                    @endcan
                                    <td>{{ $areaaccion->servicio }}</td>
                                    <td>{{ $areaaccion->asociado }}</td>
                                    <td>
                                        @if ($areaaccion->estado == 'ACTIVO')
                                            <span class="badge badge-success">{{ $areaaccion->estado }}</span>
                                        @else
                                            <span class="badge badge-danger">{{ $areaaccion->estado }}</span>
                                        @endif
                                    </td>
                                    @can('admin.areaacciones.editaraccionesbateria')
                                    <td>
                                        <a class="btn btn-sm float-right btn-editar" href="{{ route('admin.areaacciones.edit', $areaaccion) }}">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </td>
                                    @endcan
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div> --}}
            <div class="col-lg-6">
                <strong style="font-size: 20px; margin-left: 10px;">LISTA DE ESPECIALIDADES</strong>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Especialidad</th>
                                <th>Sucursal</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($areaacciones as $especialidad => $sucursales)
                                @foreach ($sucursales as $sucursal => $areaacciones)
                                    <tr>
                                        <td>{{ $especialidad }}</td>
                                        <td>{{ $sucursal }}</td>
                                        <td>
                                            <button type="button" class="btn btn-ver" data-toggle="modal" data-target="#modal{{ str_replace(' ', '', $especialidad) }}_{{ $loop->index }}">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <!-- Modal -->
                                            <div class="modal fade" id="modal{{ str_replace(' ', '', $especialidad) }}_{{ $loop->index }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-xl" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h4 class="modal-title" id="exampleModalLabel"><strong style="margin-left: 10px;">PROVEEDORES PARA {{ $especialidad }} EN {{ $sucursal }}</strong></h4>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body table-responsive">
                                                            <table class="table table-striped">
                                                                <thead>
                                                                    <tr>
                                                                        <th style="background: white;">Proveedor</th>
                                                                        <th style="background: white;">Venta</th>
                                                                        @can('admin.areaacciones.verprecioscomprabateria')
                                                                        <th style="background: white;">Compra</th>
                                                                        @endcan
                                                                        <th style="background: white;">Servicio</th>
                                                                        <th style="background: white;">Asociado</th>
                                                                        <th style="background: white;">Estado</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach ($areaacciones as $areaaccion)
                                                                        <tr>
                                                                            <td>{{ $areaaccion->proveedor }}</td>
                                                                            <td>{{ $areaaccion->precio }}</td>
                                                                            @can('admin.areaacciones.verprecioscomprabateria')
                                                                            <td>{{ $areaaccion->preciocompra }}</td>
                                                                            @endcan
                                                                            <td>{{ $areaaccion->servicio }}</td>
                                                                            <td>{{ $areaaccion->asociado }}</td>
                                                                            <td>
                                                                                @if ($areaaccion->estado == 'ACTIVO')
                                                                                    <span class="badge badge-success">{{ $areaaccion->estado }}</span>
                                                                                @else
                                                                                    <span class="badge badge-danger">{{ $areaaccion->estado }}</span>
                                                                                @endif
                                                                            </td>
                                                                            @can('admin.areaacciones.editaraccionesbateria')
                                                                            <td>
                                                                                <a class="btn btn-sm float-right btn-editar" href="{{ route('admin.areaacciones.edit', $areaaccion) }}">
                                                                                    <i class="fas fa-edit"></i>
                                                                                </a>
                                                                            </td>
                                                                            @endcan
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-cerrar" data-dismiss="modal">Cerrar</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            
            
            {{-- ESTUDIOS --}}
            <div class="col-lg-6">
                <strong style="font-size: 20px; margin-left: 10px;">LISTA DE ESTUDIOS</strong>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Estudio</th>
                                <th>Sucursal</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($areaaccionesestudios as $area => $areasPorSucursal)
                                @foreach ($areasPorSucursal as $sucursal => $areas)
                                <tr>
                                    <td>{{ $area }}</td>
                                    <td>{{ $sucursal }}</td>
                                    <td>
                                        <button type="button" class="btn btn-ver" data-toggle="modal" data-target="#modal{{ str_replace(' ', '', $area) }}_{{ $loop->index }}">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <div class="modal fade" id="modal{{ str_replace(' ', '', $area) }}_{{ $loop->index }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-xl" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title" id="exampleModalLabel"><strong style="margin-left: 10px;">PROVEEDORES PARA {{ $area }} EN {{ $sucursal }}</strong></h4>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body table-responsive">
                                                        <table class="table table-striped">
                                                            <thead>
                                                                <tr>
                                                                    <th style="background: white;">Acción</th>
                                                                    <th style="background: white;">Proveedor</th>
                                                                    <th style="background: white;">Venta</th>
                                                                    @can('admin.areaacciones.verprecioscomprabateria')
                                                                    <th style="background: white;">Compra</th>
                                                                    @endcan
                                                                    <th style="background: white;">Servicio</th>
                                                                    <th style="background: white;">Asociado</th>
                                                                    <th style="background: white;">Estado</th>
                                                                    <th style="background: white;"></th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($areas as $areaaccion)
                                                                    <tr>
                                                                        <td>{{ $areaaccion->accion }}</td>
                                                                        <td>{{ $areaaccion->proveedor }}</td>
                                                                        <td>{{ $areaaccion->precio }}</td>
                                                                        @can('admin.areaacciones.verprecioscomprabateria')
                                                                        <td>{{ $areaaccion->preciocompra}}</td>
                                                                        @endcan
                                                                        <td>{{ $areaaccion->servicio}}</td>
                                                                        <td>{{ $areaaccion->asociado }}</td>
                                                                        <td>
                                                                            @if ($areaaccion->estado == 'ACTIVO')
                                                                                <span class="badge badge-success">{{ $areaaccion->estado }}</span>
                                                                            @else
                                                                                <span class="badge badge-danger">{{ $areaaccion->estado }}</span>
                                                                            @endif
                                                                        </td>
                                                                        @can('admin.areaacciones.editaraccionesbateria')
                                                                        <td>
                                                                            <a class="btn btn-sm float-right btn-editar" href="{{ route('admin.areaacciones.edit', $areaaccion) }}">
                                                                                <i class="fas fa-edit"></i>
                                                                            </a>
                                                                        </td>
                                                                        @endcan
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-cerrar" data-dismiss="modal">Cerrar</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
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