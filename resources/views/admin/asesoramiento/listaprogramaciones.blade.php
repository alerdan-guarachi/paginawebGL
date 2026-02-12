@extends('adminlte::page')

@section('content_header')
<a class="btn btn-sm float-right btn-cartareclamo" data-toggle="modal" data-target="#modalHistorico" style="margin-right: -2px;">VER HISTORIAL</a>
<div class="modal fade" id="modalHistorico" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">ASESORIAS PASADAS</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                        <thead class="thead-light">
                            <tr>
                                <th>N° Ticket</th>
                                <th>Nombre del Cliente</th>
                                <th>Celular</th>
                                <th>Motivo</th>
                                <th>Fecha</th>
                                <th>Hora</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($programacionesHistoricas as $p)
                                <tr>
                                    <td>{{ $p->id }}</td>
                                    <td>{{ $p->clientenombre }}</td>
                                    <td>{{ $p->celular }}</td>
                                    <td>{{ $p->motivo }}</td>
                                    <td>{{ \Carbon\Carbon::parse($p->fecha)->format('d-m-Y') }}</td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($p->horadesde)->format('H:i') }}
                                        -
                                        {{ \Carbon\Carbon::parse($p->horahasta)->format('H:i') }}
                                    </td>
                                    <td>
                                        <span class="badge
                                            {{ $p->estado == 'PENDIENTE' ? 'badge-warning' : '' }}
                                            {{ $p->estado == 'ASISTIÓ' ? 'badge-success' : '' }}
                                            {{ $p->estado == 'NO ASISTIÓ' ? 'badge-danger' : '' }}
                                            {{ $p->estado == 'SE REPROGRAMÓ' ? 'badge-primary' : '' }}">
                                            {{ $p->estado }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted">
                                        No hay asesorias pasadas
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<h1>LISTA DE ATENCIÓN DE ASESORÍA</h1>
@stop

@section('css')
<link rel="stylesheet" href="{{ asset('css/tramitesgestora.css') }}">
<style>
    .btn-asistio {
        background-color:  #ffffff;
        color: #94c93b;
        border-color: #94c93b;
        border-radius: 5px;
        padding: 0 5px;
    }
    .btn-asistio:hover {
        background-color: #94c93b;
        color: #ffffff;
    }
    .btn-noasistio {
        background-color:  #ffffff;
        color: red;
        border-color: red;
        border-radius: 5px;
        padding: 0 5px;
    }
    .btn-noasistio:hover {
        background-color: red;
        color: #ffffff;
    }
    .btn-reprogramo {
        background-color:  #ffffff;
        color: #737373;
        border-color: #737373;
        border-radius: 5px;
        padding: 0 5px;
    }
    .btn-reprogramo:hover {
        background-color: #737373;
        color: #ffffff;
    }
</style>
@stop

@section('content')
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
        <h5>ASESORIAS DE HOY</h5>
    </div>
    <div class="card-body" style="margin-top: -20px;">
        <div class="table-responsive">
            <table class="table table-bordered table-sm">
                <thead class="thead-light">
                    <tr>
                        <th>N° Ticket</th>
                        <th>Nombre del Cliente</th>
                        <th>Celular</th>
                        <th>Motivo</th>
                        <th>Hora</th>
                        <th style="text-align: center;">Estado</th>
                        <th style="text-align: center;">Confirmar Asistencia</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($programacionesHoy as $p)
                        <tr>
                            <td>{{ $p->id }}</td>
                            <td>{{ $p->clientenombre }}</td>
                            <td>{{ $p->celular }}</td>
                            <td>{{ $p->motivo }}</td>
                            <td>
                                {{ \Carbon\Carbon::parse($p->horadesde)->format('H:i') }}
                                -
                                {{ \Carbon\Carbon::parse($p->horahasta)->format('H:i') }}
                            </td>
                            <td style="text-align: center;">
                                <span class="badge
                                    {{ $p->estado == 'PENDIENTE' ? 'badge-warning' : '' }}
                                    {{ $p->estado == 'ASISTIÓ' ? 'badge-success' : '' }}
                                    {{ $p->estado == 'NO ASISTIÓ' ? 'badge-danger' : '' }}
                                    {{ $p->estado == 'SE REPROGRAMÓ' ? 'badge-primary' : '' }}">
                                    {{ $p->estado }}
                                </span>
                            </td>
                            <td style="text-align: center;">
                                @if($p->estado == 'PENDIENTE')
                                    <button class="btn btn-asistio btn-sm"
                                        onclick="actualizarEstado({{ $p->id }}, 'ASISTIÓ')">
                                        ASISTIÓ
                                    </button>

                                    <button class="btn btn-noasistio btn-sm"
                                        onclick="actualizarEstado({{ $p->id }}, 'NO ASISTIÓ')">
                                        NO ASISTIÓ
                                    </button>

                                    <button class="btn btn-reprogramo btn-sm"
                                        onclick="actualizarEstado({{ $p->id }}, 'SE REPROGRAMÓ')">
                                        REPROGRAMAR
                                    </button>
                                @else
                                    <span class="badge badge-secondary">
                                        FINALIZADO
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">
                                No hay asesorias para hoy
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
        
    <div class="card-header">
        <h5>PRÓXIMAS ASESORIAS</h5>
    </div>
    <div class="card-body" style="margin-top: -20px;">
        <div class="table-responsive">
            <table class="table table-bordered table-sm">
                <thead class="thead-light">
                    <tr>
                        <th>N° Ticket</th>
                        <th>Nombre del Cliente</th>
                        <th>Celular</th>
                        <th>Motivo</th>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th style="text-align: center;">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($programacionesFuturas as $p)
                        <tr>
                            <td>{{ $p->id }}</td>
                            <td>{{ $p->clientenombre }}</td>
                            <td>{{ $p->celular }}</td>
                            <td>{{ $p->motivo }}</td>
                            <td>{{ \Carbon\Carbon::parse($p->fecha)->format('d-m-Y') }}</td>
                            <td>
                                {{ \Carbon\Carbon::parse($p->horadesde)->format('H:i') }}
                                -
                                {{ \Carbon\Carbon::parse($p->horahasta)->format('H:i') }}
                            </td>
                            <td class="text-center">
                                <span class="badge badge-warning">
                                    {{ $p->estado }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">
                                No hay asesorias futuras
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
    function actualizarEstado(id, estado) {
        if (!confirm('¿CONFIRMAR CAMBIO DE ESTADO?')) return;

        fetch("{{ route('asesoria.actualizarEstado') }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ id, estado })
        })
        .then(() => location.reload());
    }
</script>

@stop


