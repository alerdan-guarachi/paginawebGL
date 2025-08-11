@extends('adminlte::page')

<link href="assets/img/logo.png" rel="icon">

@section('content_header')
<h1>BATERÍA DE PROVEEDORES</h1>
@stop 

@section('css')
<link rel="stylesheet" href="{{ asset('css/estilogl.css') }}">
<style>
    .table td {
        padding: 5px 10px;
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
        }, 5000);
    </script>
@endif

<div class="card">
    <div class="card-body">
        <div class="row">
            {{-- <div class="col-lg-12">
                <div class="card">
                    <div class="card-body" style="background-color: #f8f8f8;">
                        <label for="BUSCAR">BUSCAR:</label>
                        <input type="text" id="buscador-especialidad-sucursal" class="form-control mb-3" placeholder="BUSCAR POR ESPECIALIDAD / ESTUDIO O SUCURSAL...">
                    </div>
                </div>
            </div> --}}
            <div class="col-lg-6">
                <strong style="font-size: 20px; margin-left: 10px;">LISTA DE ESPECIALIDADES</strong>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead class="table-secondary">
                            <tr>
                                <th>Especialidad</th>
                                <th>Sucursal</th>
                                <th>Ver</th>
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
                                                            <div class="col-lg-12">
                                                                <div class="card">
                                                                    <div class="card-body" style="background-color: #f8f8f8;">
                                                                        <label for="BUSCAR">BUSCAR:</label>
                                                                        <input type="text" class="form-control mb-3 buscador-modal" placeholder="BUSCAR POR PROVEEDOR..." data-target="modal{{ str_replace(' ', '', $especialidad) }}_{{ $loop->index }}">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <table class="table table-striped">
                                                                <thead class="table-secondary">
                                                                    <tr>
                                                                        <th>ID</th>
                                                                        <th>Proveedor</th>
                                                                        <th>Venta</th>
                                                                        @can('admin.areaacciones.verprecioscomprabateria')
                                                                        <th>Compra</th>
                                                                        @endcan
                                                                        <th>Atención</th>
                                                                        <th>Pago</th>
                                                                        <th>Asociado</th>
                                                                        <th>Estado</th>
                                                                        <th>Edit.</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach ($areaacciones as $areaaccion)
                                                                        <tr>
                                                                            <td>{{ $areaaccion->id }}</td>
                                                                            <td>{{ $areaaccion->proveedor }}</td>
                                                                            <td>{{ $areaaccion->precio }}</td>
                                                                            @can('admin.areaacciones.verprecioscomprabateria')
                                                                            <td>{{ $areaaccion->preciocompra }}</td>
                                                                            @endcan
                                                                            <td>{{ $areaaccion->servicio }}</td>
                                                                            <td>{{ $areaaccion->pagoservicio }}</td>
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
                                                                                <a class="btn btn-sm btn-editar" href="{{ route('admin.areaacciones.edit', $areaaccion) }}">
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
                                                            <button type="button" class="btn btn-cerrar" data-dismiss="modal">CERRAR</button>
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
                    <table class="table table-striped table-bordered">
                        <thead class="table-secondary">
                            <tr>
                                <th>Estudio</th>
                                <th>Sucursal</th>
                                <th>Ver</th>
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
                                                        <div class="col-lg-12">
                                                            <div class="card">
                                                                <div class="card-body" style="background-color: #f8f8f8;">
                                                                    <label for="BUSCAR">BUSCAR:</label>
                                                                    <input type="text" class="form-control mb-3 buscador-modal" placeholder="BUSCAR POR ESTUDIO O PROVEEDOR..." data-target="modal{{ str_replace(' ', '', $area) }}_{{ $loop->index }}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <table class="table table-striped">
                                                            <thead class="table-secondary">
                                                                <tr>
                                                                    <th>ID</th>
                                                                    <th>Estudio</th>
                                                                    <th>Proveedor</th>
                                                                    <th>Venta</th>
                                                                    @can('admin.areaacciones.verprecioscomprabateria')
                                                                    <th>Compra</th>
                                                                    @endcan
                                                                    <th>Atención</th>
                                                                    <th>Pago</th>
                                                                    <th>Asociado</th>
                                                                    <th>Estado</th>
                                                                    <th>Edit.</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody class="tabla-modal" id="tbody-modal{{ str_replace(' ', '', $area) }}_{{ $loop->index }}">

                                                                @foreach ($areas as $areaaccion)
                                                                    <tr>
                                                                        <td>{{ $areaaccion->id }}</td>
                                                                        <td>{{ $areaaccion->accion }}</td>
                                                                        <td>{{ $areaaccion->proveedor }}</td>
                                                                        <td>{{ $areaaccion->precio }}</td>
                                                                        @can('admin.areaacciones.verprecioscomprabateria')
                                                                        <td>{{ $areaaccion->preciocompra}}</td>
                                                                        @endcan
                                                                        <td>{{ $areaaccion->servicio}}</td>
                                                                        <td>{{ $areaaccion->pagoservicio}}</td>
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
                                                                            <a class="btn btn-sm btn-editar" href="{{ route('admin.areaacciones.edit', $areaaccion) }}">
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
                                                        <button type="button" class="btn btn-cerrar" data-dismiss="modal">CERRAR</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            @endforeach
                            <script>
                                document.addEventListener("DOMContentLoaded", function () {
                                    // Filtrar filas según búsqueda
                                    document.querySelectorAll('.buscador-modal').forEach(input => {
                                        input.addEventListener('keyup', function () {
                                            const filtro = this.value.toLowerCase();
                                            const modalId = this.dataset.target;
                                            const filas = document.querySelectorAll(`#${modalId} tbody tr`);

                                            filas.forEach(fila => {
                                                const estudio = fila.children[1]?.textContent.toLowerCase();
                                                const proveedor = fila.children[2]?.textContent.toLowerCase();
                                                if (estudio.includes(filtro) || proveedor.includes(filtro)) {
                                                    fila.style.display = '';
                                                } else {
                                                    fila.style.display = 'none';
                                                }
                                            });
                                        });
                                    });

                                    // Limpiar búsqueda al cerrar cualquier modal
                                    document.querySelectorAll('.modal').forEach(modal => {
                                        modal.addEventListener('hidden.bs.modal', function () {
                                            const input = this.querySelector('.buscador-modal');
                                            if (input) {
                                                input.value = '';
                                                const filas = this.querySelectorAll('tbody tr');
                                                filas.forEach(fila => fila.style.display = '');
                                            }
                                        });
                                    });
                                });
                            </script>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- <script>
    document.addEventListener("DOMContentLoaded", function () {
        const buscador = document.getElementById("buscador-especialidad-sucursal");
        const filas = document.querySelectorAll("table.table-striped tbody tr");

        buscador.addEventListener("keyup", function () {
            const filtro = buscador.value.toLowerCase();

            filas.forEach(fila => {
                const especialidad = fila.children[0]?.textContent.toLowerCase();
                const sucursal = fila.children[1]?.textContent.toLowerCase();

                if (especialidad.includes(filtro) || sucursal.includes(filtro)) {
                    fila.style.display = "";
                } else {
                    fila.style.display = "none";
                }
            });
        });
    });
</script> --}}
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