@extends('adminlte::page')

@section('content_header')
<a class="btn btn-sm float-right btn-cartareclamo" data-toggle="modal" data-target="#bloquearFecha" style="margin-right: -2px;">BLOQUEAR FECHAS</a>
<a class="btn btn-sm float-right btn-cartareclamo" data-toggle="modal" data-target="#nuevoHorario">AGREGAR HORARIO</a>
<h1>HORARIOS DE ASESORÍA</h1>
@stop

@section('css')
<link rel="stylesheet" href="{{ asset('css/tramitesgestora.css') }}">
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
        <h5>HORARIOS DE ATENCIÓN</h5>
    </div>
    <div class="card-body" style="margin-top: -20px;">
        <div class="row">
            @php
                $diasSemana = ['LUNES','MARTES','MIÉRCOLES','JUEVES','VIERNES','SÁBADO'];
            @endphp
            <div class="container-fluid">
                <div class="row">
                    @foreach($diasSemana as $dia)
                        <div class="col-12 col-md-2 mb-3">
                            <div class="card h-100 border border-dark">
                                <div class="card-header text-center bg-light p-2">
                                    <strong>{{ $dia }}</strong>
                                </div>

                                <div class="card-body p-2">
                                    @php
                                        $horariosDia = $horarios
                                            ->where('dia', $dia)
                                            ->sortBy('horainicio');
                                    @endphp

                                    @if($horariosDia->count())
                                        @foreach($horariosDia as $horario)
                                            <div class="card mb-2 border {{ $horario->estado == 'ACTIVO' ? 'border-success' : 'border-secondary' }}">
                                                <div class="card-body p-2">
                                                    <div class="small">
                                                        ⏰ {{ $horario->horainicio }} - {{ $horario->horafin }}
                                                    </div>
                                                    <div class="small text-muted">
                                                        ⌛ {{ $horario->duracioncita }} min. por cita
                                                    </div>
                                                    <div class="d-flex justify-content-between align-items-center mt-2">
                                                        <span class="badge {{ $horario->estado == 'ACTIVO' ? 'badge-success' : 'badge-danger' }}">
                                                            {{ $horario->estado }}
                                                        </span>
                                                        <div class="d-flex align-items-center">
                                                            <a class="btn btn-xs btn-outline-secondary me-2"
                                                            data-toggle="modal"
                                                            data-target="#editarHorario{{ $horario->id }}"
                                                            title="Editar" style="margin-right: 5px;">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                            <form method="POST"
                                                                action="{{ route('admin.asesoramiento.toggle', $horario->id) }}"
                                                                class="m-0">
                                                                @csrf
                                                                <button type="submit"
                                                                        class="btn btn-xs {{ $horario->estado == 'ACTIVO' ? 'btn-outline-danger' : 'btn-outline-success' }}"
                                                                        title="{{ $horario->estado == 'ACTIVO' ? 'Desactivar horario' : 'Activar horario' }}">
                                                                    <i class="fas {{ $horario->estado == 'ACTIVO' ? 'fa-power-off' : 'fa-toggle-on' }}"></i>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="text-center text-muted small">
                                            SIN HORARIOS
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            @forelse($horarios as $horario)
                {{-- MODAL EDITAR HORARIO --}}
                <div class="modal fade" id="editarHorario{{ $horario->id }}" tabindex="-1">
                    <div class="modal-dialog">
                        <form method="POST" action="{{ route('admin.asesoramiento.update', $horario->id) }}">
                            @csrf
                            @method('PUT')

                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">EDITAR HORARIO</h5>
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                </div>

                                <div class="modal-body">
                                    <div class="mb-2">
                                        <label>Día</label>
                                        <input class="form-control" value="{{ $horario->dia }}" disabled>
                                    </div>

                                    <div class="row">
                                        <div class="col">
                                            <label>Hora inicio</label>
                                            <input type="time" name="horainicio" class="form-control" value="{{ $horario->horainicio }}" required>
                                        </div>
                                        <div class="col">
                                            <label>Hora fin</label>
                                            <input type="time" name="horafin" class="form-control" value="{{ $horario->horafin }}" required>
                                        </div>
                                    </div>

                                    <div class="mt-2">
                                        <label>Duración por cita (min)</label>
                                        <input type="number" name="duracioncita" class="form-control" value="{{ $horario->duracioncita }}" required>
                                    </div>
                                </div>

                                <div class="modal-footer">
                                    <button class="btn btn-crear">GUARDAR</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info">
                        No tienes horarios registrados.
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</div>

<div class="card mt-4">
    <div class="card-header">
        <h5>BLOQUEOS Y AUSENCIAS DE ASESORÍA</h5>
    </div>
    <div class="card-body" style="margin-top: -20px;">
        <table class="table table-sm table-bordered">
            <thead class="thead-light">
                <tr>
                    <th>ID</th>
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
                        <td>{{ $b->id }}</td>
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
                                    action="{{ route('admin.asesoramiento.destroy', $b->id) }}"
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
<div class="modal fade" id="nuevoHorario" tabindex="-1">
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
</div>

{{-- ===================== MODAL BLOQUEAR FECHAS ===================== --}}
<div class="modal fade" id="bloquearFecha" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('admin.asesoramiento.bloquearasesora') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">BLOQUEAR FECHAS</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <div class="row">
                        {{-- <div class="col-md-6">
                            <label>Desde</label>
                            <input type="date" name="fecha_inicio" class="form-control mb-2" required>
                        </div>
                        <div class="col-md-6">
                            <label>Hasta</label>
                            <input type="date" name="fecha_fin" class="form-control mb-2" required>
                        </div> --}}
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
    </div>
</div>
@stop
