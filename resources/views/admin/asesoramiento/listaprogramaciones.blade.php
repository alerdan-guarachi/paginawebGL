@extends('adminlte::page')

@section('content_header')
<a class="btn btn-sm float-right btn-cartareclamo" data-toggle="modal" data-target="#modalHistorico" style="margin-right: -2px;">VER HISTORIAL</a>
<div class="modal fade" id="modalHistorico" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">ASESORIAS PASADAS (ÚLTIMOS 7 DIAS)</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                        <thead class="thead-light">
                            <tr>
                                <th>N°</th>
                                <th>Nombre del Cliente</th>
                                <th>Celular</th>
                                <th>Modalidad</th>
                                <th>Ciudad</th>
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
                                    <td>{{ $p->modalidad }}</td>
                                    <td>{{ $p->sucursal }}</td>
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
                                            {{ $p->estado == 'SE REPROGRAMÓ' ? 'badge-secondary' : '' }}">
                                            {{ $p->estado }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted">
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
        padding: 0 6px;
    }
    .btn-asistio:hover {
        background-color: #94c93b;
        color: #ffffff;
    }
    .btn-confirmar {
        background-color:  #ffffff;
        color: #94c93b;
        border-color: #94c93b;
        border-radius: 5px;
        padding: 0 6px;
    }
    .btn-confirmar:hover {
        background-color: #94c93b;
        color: #ffffff;
    }
    .btn-noasistio {
        background-color:  #ffffff;
        color: red;
        border-color: red;
        border-radius: 5px;
        padding: 0 8px;
    }
    .btn-noasistio:hover {
        background-color: red;
        color: #ffffff;
    }
    .btn-reprogramo {
        background-color:  #ffffff;
        color: #fe9e4f;
        border-color: #fe9e4f;
        border-radius: 5px;
        padding: 0 8px;
    }
    .btn-reprogramo:hover {
        background-color: #fe9e4f;
        color: #ffffff;
    }
    @keyframes parpadeo {
        0%   { background-color: #f4fae8; }
        50%  { background-color: #e6f4c9; }
        100% { background-color: #f4fae8; }
    }

    .cita-en-curso {
        animation: parpadeo 1s infinite;
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
                        <th>N°</th>
                        <th>Nombre del Cliente</th>
                        <th>Celular</th>
                        <th>Modalidad</th>
                        <th>Ciudad</th>
                        <th>Motivo</th>
                        <th>Hora</th>
                        <th style="text-align: center;">Estado</th>
                        <th style="text-align: center;">Confirmar Asistencia</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($programacionesHoy as $p)
                        <tr 
                            data-desde="{{ \Carbon\Carbon::parse($p->horadesde)->format('H:i') }}"
                            data-hasta="{{ \Carbon\Carbon::parse($p->horahasta)->format('H:i') }}"
                            data-fecha="{{ $p->fecha }}"
                        >
                            <td>{{ $p->id }}</td>
                            <td>{{ $p->clientenombre }}</td>
                            <td>{{ $p->celular }}</td>
                            <td>{{ $p->modalidad }}</td>
                            <td>{{ $p->sucursal }}</td>
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
                                    {{ $p->estado == 'SE REPROGRAMÓ' ? 'badge-secondary' : '' }}">
                                    {{ $p->estado }}
                                </span>
                            </td>
                            <td style="text-align: center;">
                                @if($p->estado == 'PENDIENTE')

                                    <button class="btn btn-asistio btn-sm mx-1"
                                        onclick="actualizarEstado({{ $p->id }}, 'ASISTIÓ')"
                                        title="ASISTIÓ">
                                        <i class="fas fa-check"></i>
                                    </button>

                                    <button class="btn btn-noasistio btn-sm mx-1"
                                        onclick="actualizarEstado({{ $p->id }}, 'NO ASISTIÓ')"
                                        title="NO ASISTIÓ">
                                        <i class="fas fa-times"></i>
                                    </button>

                                    <a class="btn btn-reprogramo btn-sm mx-1"
                                        onclick="abrirModalReprogramar(
                                            {{ $p->id }},
                                            '{{ $p->clientenombre }}',
                                            '{{ $p->celular }}',
                                            '{{ $p->modalidad }}',
                                            '{{ $p->sucursal }}',
                                            '{{ $p->motivo }}',
                                            '{{ $p->fecha }}',
                                            '{{ $p->horadesde }}',
                                            '{{ $p->horahasta }}'
                                        )"
                                        title="REPROGRAMAR">
                                        <i class="fas fa-calendar-alt"></i>
                                    </a>
                                @else
                                    <span class="badge badge-secondary">
                                        FINALIZADO
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted">
                                No hay asesorias para hoy
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="modal fade" id="modalReprogramar" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">REPROGRAMAR ASESORÍA</h5>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" id="asesorid" value="{{ $asesorId }}">
                            <input type="hidden" id="reprogramar_id">
                            <div class="card">
                                <div class="card-header bg-light py-1">
                                    <strong>Datos de la Asesoría</strong>
                                </div>
                                <div class="card-body">
                                    <p><strong>Cliente:</strong> <span id="modal_cliente"></span></p>
                                    <p hidden><strong>Celular:</strong> <span id="modal_celular"></span></p>
                                    <p hidden><strong>Modalidad:</strong> <span id="modal_modalidad"></span></p>
                                    <p hidden><strong>Ciudad:</strong> <span id="modal_sucursal"></span></p>
                                    <p><strong>Motivo:</strong> <span id="modal_motivo"></span></p>
                                    <p hidden>
                                        <strong>Horario Actual:</strong>
                                        <span id="modal_fecha"></span>
                                        |
                                        <span id="modal_horario"></span>
                                    </p>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-header bg-light py-1">
                                    <strong>Nueva Programación</strong>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label>Nueva Fecha</label>
                                        <input type="date"
                                            id="nueva_fecha"
                                            class="form-control"
                                            min="{{ now()->toDateString() }}"
                                            max="{{ now()->addDays(7)->toDateString() }}" required>
                                    </div>
                                    <label>Horarios Disponibles:</label>
                                    <div id="tickets" class="row">
                                        <div class="col-12 text-muted">
                                            Seleccione una fecha para ver los horarios disponibles
                                        </div>
                                    </div>

                                    <!-- ocultos -->
                                    <input type="hidden" id="nueva_horadesde">
                                    <input type="hidden" id="nueva_horahasta">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-cartareclamo" onclick="guardarReprogramacion()">
                                REPROGRAMAR
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <script>
                function abrirModalReprogramar(id, cliente, celular, modalidad, sucursal, motivo, fecha, horadesde, horahasta) {
                    document.getElementById('reprogramar_id').value = id;
                    document.getElementById('modal_cliente').innerText = cliente;
                    document.getElementById('modal_celular').innerText = celular;
                    document.getElementById('modal_modalidad').innerText = modalidad;
                    document.getElementById('modal_sucursal').innerText = sucursal;
                    document.getElementById('modal_motivo').innerText = motivo;
                    document.getElementById('modal_fecha').innerText = fecha;
                    document.getElementById('modal_horario').innerText = horadesde + ' - ' + horahasta;
                    document.getElementById('nueva_fecha').value = '';
                    document.getElementById('nueva_horadesde').value = '';
                    document.getElementById('nueva_horahasta').value = '';
                    $('#modalReprogramar').modal('show');
                }

                function guardarReprogramacion() {
                    let id = document.getElementById('reprogramar_id').value;
                    let fecha = document.getElementById('nueva_fecha').value;
                    let horadesde = document.getElementById('nueva_horadesde').value;
                    let horahasta = document.getElementById('nueva_horahasta').value;

                    if (!fecha || !horadesde || !horahasta) {
                        alert('Seleccione fecha y horario disponible');
                        return;
                    }
                    if (!confirm('¿Está seguro de reprogramar esta asesoría?')) {
                        return;
                    }
                    fetch("{{ route('asesoria.actualizarEstado') }}", {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            id: id,
                            estado: 'SE REPROGRAMÓ',
                            nueva_fecha: fecha,
                            nueva_horadesde: horadesde,
                            nueva_horahasta: horahasta
                        })
                    })
                    .then(response => response.json())
                    .then(data => {

                        if (data.ok) {
                            alert('✅ Asesoría reprogramada correctamente');
                            location.reload();
                        } else {
                            alert('❌ Ocurrió un error');
                        }
                    })
                    .catch(error => {
                        console.error(error);
                        alert('❌ Error en la petición');
                    });
                }
            </script>
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

    <div class="card-header">
        <h5>PRÓXIMAS ASESORIAS</h5>
    </div>
    <div class="card-body" style="margin-top: -20px;">
        <div class="table-responsive">
            <table class="table table-bordered table-sm">
                <thead class="thead-light">
                    <tr>
                        <th>N°</th>
                        <th>Nombre del Cliente</th>
                        <th>Celular</th>
                        <th>Modalidad</th>
                        <th>Ciudad</th>
                        <th>Motivo</th>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th style="text-align: center;">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($programacionesFuturas as $p)
                        <tr 
                            data-desde="{{ \Carbon\Carbon::parse($p->horadesde)->format('H:i') }}"
                            data-hasta="{{ \Carbon\Carbon::parse($p->horahasta)->format('H:i') }}"
                            data-fecha="{{ $p->fecha }}"
                        >
                            <td>{{ $p->id }}</td>
                            <td>{{ $p->clientenombre }}</td>
                            <td>{{ $p->celular }}</td>
                            <td hidden>{{ $p->email }}</td>
                            <td>{{ $p->modalidad }}
                                @if($p->modalidad == 'PRESENCIAL POR CONFIRMAR')
                                    <button class="btn btn-sm btn-success btn-confirmar"
                                        data-id="{{ $p->id }}" title="CONFIRMAR ASESORIA">
                                        Confirmar
                                    </button>
                                    <a class="btn btn-reprogramo btn-sm mx-1"
                                        onclick="abrirModalReprogramar(
                                            {{ $p->id }},
                                            '{{ $p->clientenombre }}',
                                            '{{ $p->celular }}',
                                            '{{ $p->modalidad }}',
                                            '{{ $p->sucursal }}',
                                            '{{ $p->motivo }}',
                                            '{{ $p->fecha }}',
                                            '{{ $p->horadesde }}',
                                            '{{ $p->horahasta }}'
                                        )"
                                        title="REPROGRAMAR">
                                        <i class="fas fa-calendar-alt"></i>
                                    </a>
                                @endif
                            </td>
                            <td>{{ $p->sucursal }}</td>
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
                            <td colspan="9" class="text-center text-muted">
                                No hay asesorias futuras
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .ticket {
        border: 2px dashed #94c93b;
        cursor: pointer;
        transition: 0.2s;
    }
    .ticket:hover {
        background: #e7f1ff;
    }
    .ticket {
        cursor: pointer;
    }
    .ticket.selected {
        background: #94c93b;
        color: #fff;
    }
</style>

@stop


@section('js')
<script>
const horariosUrl = "{{ route('admin.asesoramiento.progasesoramiento.horarios', ['asesor' => 'ASESOR', 'dia' => 'DIA']) }}";
</script>
<script>
    const ocupadosUrl = "{{ route('admin.asesoramiento.ocupados', ['asesor' => 'ASESOR', 'fecha' => 'FECHA']) }}";
</script>

<script>
    document.getElementById('nueva_fecha').addEventListener('change', async function () {

    const fecha = this.value;
    const asesorId = document.getElementById('asesorid').value;
    const ticketsDiv = document.getElementById('tickets');

    ticketsDiv.innerHTML = '<div class="col-12 text-muted">Cargando horarios...</div>';

    const dias = ['DOMINGO','LUNES','MARTES','MIERCOLES','JUEVES','VIERNES','SABADO'];
    const diaSemana = dias[new Date(fecha + 'T00:00:00').getDay()];

    try {

        const horariosUrlFinal = horariosUrl
            .replace('ASESOR', asesorId)
            .replace('DIA', diaSemana);

        const ocupadosUrlFinal = ocupadosUrl
            .replace('ASESOR', asesorId)
            .replace('FECHA', fecha);

        const [horariosRes, ocupadosRes] = await Promise.all([
            fetch(horariosUrlFinal),
            fetch(ocupadosUrlFinal)
        ]);

        const horarios = await horariosRes.json();
        const ocupadosData = await ocupadosRes.json();

            if (ocupadosData.dia_completo) {
                ticketsDiv.innerHTML =
                    `<div class="col-12 text-danger">ASESOR BLOQUEADO TODO EL DÍA</div>`;
                return;
            }

            const ocupados = ocupadosData.rangos;


                    ticketsDiv.innerHTML = '';

                    if (!horarios.length) {
                        ticketsDiv.innerHTML =
                            `<div class="col-12 text-danger">NO HAY HORARIOS DISPONIBLES</div>`;
                        return;
                    }

                    horarios.forEach(h => {

                        let actual = h.horainicio.substring(0,5);
                        const duracion = parseInt(h.duracioncita);
                        const horaFin = h.horafin.substring(0,5);

                        function convertirMinutos(hora) {
                const [h, m] = hora.split(':').map(Number);
                return h * 60 + m;
            }

            function estaOcupado(desde, hasta, ocupados) {

                const inicioNuevo = convertirMinutos(desde);
                const finNuevo = convertirMinutos(hasta);

                for (let o of ocupados) {

                    const inicioOcupado = convertirMinutos(o.horadesde);
                    const finOcupado = convertirMinutos(o.horahasta);

                    // 🔴 Validación real de cruce
                    if (inicioNuevo < finOcupado && finNuevo > inicioOcupado) {
                        return true;
                    }
                }

                return false;
            }

            while (actual < horaFin) {

                const [hh, mm] = actual.split(':').map(Number);
                const inicio = new Date(0,0,0,hh,mm);
                const fin = new Date(inicio.getTime() + duracion * 60000);
                const hasta = fin.toTimeString().substring(0,5);
                const rango = `${actual}-${hasta}`;

                if (hasta <= horaFin && !estaOcupado(actual, hasta, ocupados)) {

                    ticketsDiv.innerHTML += `
                        <div class="col-md-4 mb-2">
                            <div class="card ticket text-center p-2"
                                data-desde="${actual}"
                                data-hasta="${hasta}"
                                style="cursor:pointer">
                                <strong>${actual} - ${hasta}</strong>
                            </div>
                        </div>
                    `;
                }

                actual = hasta;
            }

        });

        if (!ticketsDiv.children.length) {
            ticketsDiv.innerHTML =
                `<div class="col-12 text-danger">AGENDA LLENA PARA ESTE DÍA</div>`;
        }

    } catch (e) {
        ticketsDiv.innerHTML =
            `<div class="col-12 text-danger">Error al cargar horarios</div>`;
        console.error(e);
    }

    });

        document.addEventListener('click', function (e) {

        const ticket = e.target.closest('.ticket');
        if (!ticket) return;

        document.querySelectorAll('.ticket')
            .forEach(t => t.classList.remove('selected'));

        ticket.classList.add('selected');

        document.getElementById('nueva_horadesde').value = ticket.dataset.desde;
        document.getElementById('nueva_horahasta').value = ticket.dataset.hasta;

    });
</script>

<script>
    document.getElementById('formAsesoria').addEventListener('submit', function (e) {
        e.preventDefault();

        const btn = document.getElementById('btnGuardar');
        const formData = new FormData(this);

        if (!formData.get('horadesde')) {
            alert('Seleccione un horario');
            return;
        }

        btn.disabled = true;
        btn.innerText = 'GUARDANDO...';

        fetch("{{ route('admin.asesoramiento.guardar') }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (!data.ok) {
                alert(data.msg);
                btn.disabled = false;
                btn.innerText = 'CONFIRMAR ASESORIA';
                return;
            }

            window.open(data.ticket_url, '_blank');
            setTimeout(() => location.reload(), 800);
        })
        .catch(() => {
            alert('Error al guardar');
            btn.disabled = false;
            btn.innerText = 'CONFIRMAR ASESORIA';
        });
    });
</script>

<script>
    function verificarCitasEnCurso() {

        const ahora = new Date();
        const hoyISO = ahora.toISOString().slice(0,10);
        const horaActual = ahora.toTimeString().slice(0,5);

        document.querySelectorAll('tr[data-desde]').forEach(fila => {

            const fecha = fila.dataset.fecha;
            const desde = fila.dataset.desde;
            const hasta = fila.dataset.hasta;

            if (fecha === hoyISO && horaActual >= desde && horaActual < hasta) {
                fila.classList.add('cita-en-curso');
            } else {
                fila.classList.remove('cita-en-curso');
            }

        });
    }

    // Ejecutar cada 30 segundos
    setInterval(verificarCitasEnCurso, 30000);

    // Ejecutar al cargar
    verificarCitasEnCurso();
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        document.querySelectorAll('.btn-confirmar').forEach(btn => {

            btn.addEventListener('click', function() {

                if (!confirm('¿Está seguro de confirmar la asesoría?')) return;

                const id = this.dataset.id;
                this.disabled = true;
                this.innerText = 'ENVIANDO...';

                fetch("{{ route('admin.asesoramiento.confirmarCorreo') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ id: id })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.ok) {
                        alert('Correo de confirmación enviado.');
                    } else {
                        alert(data.msg);
                    }

                    this.disabled = false;
                    this.innerText = 'Confirmar';
                })
                .catch(() => {
                    alert('Error al enviar correo.');
                    this.disabled = false;
                    this.innerText = 'Confirmar';
                });

            });

        });

    });
</script>

@endsection
