@extends('adminlte::page')

@section('content_header')
<a class="btn btn-sm float-right btn-cartareclamo" data-toggle="modal" data-target="#bloquearFecha" style="margin-right: -2px;">BLOQUEAR FECHAS</a>
{{-- <a class="btn btn-sm float-right btn-cartareclamo" data-toggle="modal" data-target="#nuevoHorario">AGREGAR HORARIO FIJO</a> --}}
<h1>HORARIOS DE ATENCIÓN MÉDICA FIJOS</h1>
@stop

@section('css')
<link rel="stylesheet" href="{{ asset('css/tramitesgestora.css') }}">
<style>
    .btn-verhorarios {
        background-color: #ffffff;
        color: #94c93b;
        border-color: #94c93b;
        border-radius: 5px;
        padding: 1px 3px;
        margin-right: 10px;
    }
    .btn-verhorarios:hover {
        background-color: #94c93b;
        color: #ffffff;
    }
</style>
@stop

@section('content')
@if(session('error'))
    <div class="alert alert-warning alert-dismissible fade show">
        <strong>Atención:</strong> {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
@endif
@if (session('success'))
    <div id="alert-success" class="alert alert-success">
        <strong>{{ session('success') }}</strong>
    </div>
    <script>
        setTimeout(function() {
            $('#alert-success').fadeOut('fast');
        }, 3000);
    </script>
@endif

<div class="card">
    <div class="card-header">
        <h5>LISTA DE HORARIOS</h5>
    </div>
    <div class="card-body" style="margin-top: -20px;">
        <div class="row">
            <div class="container-fluid">
                <div class="card">
                    <div class="card-body p-2">
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered table-striped">
                                <thead class="table-secondary">
                                    <tr>
                                        <th class="text-center">ID Prov.</th>
                                        <th>Proveedor Médico</th>
                                        <th class="text-center">Horarios</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($proveedores as $proveedorId => $horariosProveedor)
                                        <tr>
                                            <td class="text-center">{{ $horariosProveedor->first()->proveedorid }}</td>
                                            <td>{{ $horariosProveedor->first()->proveedornombre }}</td>
                                            <td class="text-center">
                                                <a class="btn btn-sm btn-verhorarios" title="VER HORARIOS" data-toggle="modal" data-target="#modalHorarios{{ $proveedorId }}">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            @foreach($proveedores as $proveedorId => $horariosProveedor)
                                <div class="modal fade" id="modalHorarios{{ $proveedorId }}" tabindex="-1">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header bg-light">
                                                <h5 class="modal-title">
                                                    {{ $horariosProveedor->first()->proveedornombre }}
                                                </h5>
                                                <button type="button" class="close" data-dismiss="modal">
                                                    &times;
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                @php
                                                    $agrupado = $horariosProveedor
                                                        ->sortBy('horainicio')
                                                        ->groupBy(['sucursal','dia','tipo']);
                                                @endphp
                                                @foreach($agrupado as $sucursal => $dias)
                                                    <div class="mb-3">
                                                        <h6><strong>{{ $sucursal }}</strong></h6>
                                                        <table class="table table-sm table-bordered table-striped">
                                                            <thead class="table-secondary">
                                                                <tr>
                                                                    <th>Día</th>
                                                                    <th>Tipo</th>
                                                                    <th>Horarios</th>
                                                                    <th class="text-center">Estado</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($dias as $dia => $tipos)
                                                                    @foreach($tipos as $tipo => $registros)
                                                                        <tr>
                                                                            <td>{{ $dia }}</td>
                                                                            <td>{{ $tipo }}</td>
                                                                            <td>
                                                                                @foreach($registros as $r)
                                                                                    <span class="badge badge-light border">
                                                                                        {{ $r->horainicio }} - {{ $r->horafin }}
                                                                                    </span>
                                                                                @endforeach
                                                                            </td>
                                                                            <td class="text-center">
                                                                                @php
                                                                                    $activo = $registros->contains('estado','ACTIVO');
                                                                                @endphp

                                                                                <span class="badge {{ $activo ? 'badge-success' : 'badge-danger' }}">
                                                                                    {{ $activo ? 'ACTIVO' : 'INACTIVO' }}
                                                                                </span>
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card mt-4">
    <div class="card-header">
        <h5>BLOQUEOS Y AUSENCIAS DE ATENCIÓN MÉDICA</h5>
    </div>
    <div class="card-body" style="margin-top: -20px;">
        <table class="table table-sm table-bordered">
            <thead class="thead-light">
                <tr>
                    <th>ID Prov.</th>
                    <th>Proveedor Médico</th>
                    <th>Fecha</th>
                    <th>Horario</th>
                    <th>Motivo</th>
                    <th style="text-align: center;">Quitar</th>
                </tr>
            </thead>
            @php
                use Carbon\Carbon;
            @endphp
            <tbody>
                @forelse($bloqueos as $b)
                    @php
                        $fechaPasada = Carbon::parse($b->fecha)->lt(Carbon::today());
                    @endphp
                    <tr class="{{ $fechaPasada ? 'table-secondary text-muted' : '' }}">
                        <td>{{ $b->proveedorid }}</td>
                        <td>{{ $b->proveedornombre }}</td>
                        <td>{{ $b->fecha }}</td>
                        <td>
                            @if($b->horainicio)
                                {{ \Carbon\Carbon::parse($b->horainicio)->format('H:i') }}
                                -
                                {{ \Carbon\Carbon::parse($b->horafin)->format('H:i') }}
                            @else
                                TODO EL DÍA
                            @endif
                        </td>
                        <td>{{ $b->motivo }}</td>
                        <td class="text-center">
                            @if(!$fechaPasada)
                                <form method="POST"
                                    action="{{ route('admin.atmedicas.destroy', $b->id) }}"
                                    onsubmit="return confirm('¿Eliminar este bloqueo?')"
                                    class="m-0">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-xs btn-outline-danger" title="Eliminar bloqueo">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            @else
                                <i class="fas fa-lock text-muted" title="Bloqueo finalizado"></i>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">
                            No hay bloqueos registrados
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ===================== MODAL NUEVO HORARIO ===================== --}}
{{-- <div class="modal fade" id="nuevoHorario" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('admin.asesoramiento.store') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">NUEVO HORARIO</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <label>Día</label>
                    <select name="dia" class="form-control mb-2" required>
                        <option value="">Seleccione</option>
                        <option>LUNES</option>
                        <option>MARTES</option>
                        <option>MIÉRCOLES</option>
                        <option>JUEVES</option>
                        <option>VIERNES</option>
                        <option>SÁBADO</option>
                    </select>

                    <div class="row">
                        <div class="col-md-6">
                            <label>Hora inicio</label>
                            <input type="time" name="horainicio" class="form-control mb-2" required>
                        </div>
                        <div class="col-md-6">
                            <label>Hora fin</label>
                            <input type="time" name="horafin" class="form-control mb-2" required>
                        </div>
                    </div>
                    <label>Duración por cita (Minutos)</label>
                    <input type="number" name="duracioncita" class="form-control" required>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-crear">GUARDAR</button>
                </div>
            </div>
        </form>
    </div>
</div> --}}

{{-- ===================== MODAL BLOQUEAR FECHAS ===================== --}}
<div class="modal fade" id="bloquearFecha" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('admin.atmedicas.bloquear') }}">
@csrf
<div class="modal-content">

    <div class="modal-header">
        <h5 class="modal-title">BLOQUEAR FECHAS</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
    </div>

    <div class="modal-body">

        {{-- PROVEEDOR --}}
        <label>Proveedor</label>
        <select name="proveedorid" id="proveedorBloqueo" class="form-control mb-2" required>
            <option value="">Seleccione proveedor...</option>
            @foreach($proveedoresmedicos as $prov)
                <option value="{{ $prov->id }}"
                        data-nombre="{{ $prov->proveedor }}">
                    {{ $prov->proveedor }}
                </option>
            @endforeach
        </select>

        {{-- NOMBRE OCULTO --}}
        <input type="hidden" name="proveedornombre" id="proveedornombreBloqueo">

        {{-- SUCURSAL --}}
        <label>Sucursal</label>
        <select name="sucursal" class="form-control mb-2" required>
            <option value="">Seleccione sucursal...</option>
            <option value="SANTA CRUZ">SANTA CRUZ</option>
            <option value="COCHABAMBA">COCHABAMBA</option>
        </select>

        <div class="row">
            <div class="col-md-6">
                <label>Desde</label>
                <input
                    type="date"
                    name="fecha_inicio"
                    class="form-control mb-2"
                    min="{{ now()->toDateString() }}"
                    required
                >
            </div>

            <div class="col-md-6">
                <label>Hasta</label>
                <input
                    type="date"
                    name="fecha_fin"
                    class="form-control mb-2"
                    min="{{ now()->toDateString() }}"
                    required
                >
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <label>Hora inicio (Opcional)</label>
                <input type="time" name="horainicio" class="form-control mb-2">
            </div>
            <div class="col-md-6">
                <label>Hora fin (Opcional)</label>
                <input type="time" name="horafin" class="form-control mb-2">
            </div>
        </div>

        <label>Motivo</label>
        <select name="motivo" class="form-control" required>
            <option value="" disabled selected>Seleccione un motivo</option>
            <option value="VIAJE">VIAJE</option>
            <option value="REUNIÓN">REUNIÓN</option>
            <option value="VACACIONES">VACACIONES</option>
            <option value="ENFERMEDAD">ENFERMEDAD</option>
            <option value="OTRO">OTRO</option>
        </select>

    </div>

    <div class="modal-footer">
        <button class="btn btn-danger">BLOQUEAR</button>
    </div>

</div>
</form>
<script>
document.getElementById('proveedorBloqueo').addEventListener('change', function () {
    let selected = this.options[this.selectedIndex];
    document.getElementById('proveedornombreBloqueo').value =
        selected.getAttribute('data-nombre');
});
</script>
    </div>
</div>
@stop
