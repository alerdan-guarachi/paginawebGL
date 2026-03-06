@extends('adminlte::page')


@section('content_header')

<h1>AGENDAR ASESORÍA</h1>
@stop

@section('css')
<link rel="stylesheet" href="{{ asset('css/tramitesgestora.css') }}">
<style>
.input-group-material {
    position: relative;
    margin-top: 22px;
}

/* INPUT Y SELECT */
.input-material,
.select-material {
    width: 100%;
    border: none;
    border-bottom: 2px solid #dcdcdc;
    outline: none;
    padding: 10px 30px 8px 0;
    font-size: 15px;
    background: transparent;
    transition: 0.3s ease all;
}

/* EFECTO HOVER */
.select-material:hover {
    border-bottom: 2px solid #b5b5b5;
}

/* FOCUS */
.input-material:focus,
.select-material:focus {
    border-bottom: 2px solid #94c93b;
}

/* LABEL */
.label-material {
    position: absolute;
    left: 0;
    top: 10px;
    color: #888;
    font-size: 15px;
    transition: 0.25s ease all;
    pointer-events: none;
}

/* LABEL ACTIVO */
.input-material:focus + .label-material,
.input-material:not(:placeholder-shown) + .label-material,
.select-material:focus + .label-material,
.select-material.has-value + .label-material {
    top: -14px;
    font-size: 12px;
    color: #94c93b;
}

/* LÍNEA ANIMADA */
.linea-animada {
    position: absolute;
    bottom: 0;
    left: 50%;
    width: 0;
    height: 2px;
    background: #94c93b;
    transition: 0.3s ease all;
}

.input-material:focus ~ .linea-animada,
.select-material:focus ~ .linea-animada {
    width: 100%;
    left: 0;
}

/* FLECHA PERSONALIZADA */
.select-material {
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    cursor: pointer;
    background-image: url("data:image/svg+xml,%3Csvg fill='%2394c93b' height='20' viewBox='0 0 24 24' width='20' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M7 10l5 5 5-5z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 0 center;
    background-size: 18px;
}

/* EFECTO DISABLED */
.select-material:disabled {
    opacity: 0.6;
    cursor: not-allowed;
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

{{-- CALCULO DE BIOMETRICO --}}
{{-- <div class="container mt-4">
    <h4>Control de Horarios desde Excel</h4>
    <input type="file" id="excelInput" accept=".xlsx,.xls" class="form-control mb-3">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Hora</th>
                <th>Turno</th>
                <th>Atraso (min)</th>
                <th>A Favor (min)</th>
            </tr>
        </thead>
        <tbody id="resultado"></tbody>
    </table>
    <div class="row mb-3">
        <div class="col-md-4">
            <div class="alert alert-success">
                <strong>Total A Favor:</strong>
                <span id="totalFavor">0</span> min
            </div>
        </div>
        <div class="col-md-4">
            <div class="alert alert-danger">
                <strong>Total En Contra:</strong>
                <span id="totalContra">0</span> min
            </div>
        </div>
        <div class="col-md-4">
            <div class="alert alert-info">
                <strong>Resultado Final:</strong>
                <span id="resultadoFinal">—</span>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/xlsx/dist/xlsx.full.min.js"></script>
<script>
    document.getElementById('excelInput').addEventListener('change', function (e) {
        const file = e.target.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = function (e) {
            const data = new Uint8Array(e.target.result);
            const workbook = XLSX.read(data, { type: 'array' });
            const sheet = workbook.Sheets[workbook.SheetNames[0]];
            const rows = XLSX.utils.sheet_to_json(sheet, { header: 1 });
            const tbody = document.getElementById('resultado');
            tbody.innerHTML = '';

            let totalFavor = 0;
            let totalContra = 0;
            const VENTANA = 50;
            rows.forEach((row) => {
                if (!row[0] || isNaN(new Date(row[0]).getTime())) return;
                const fechaHora = new Date(row[0]);
                const dia = fechaHora.getDay();
                const minutosDia = fechaHora.getHours() * 60 + fechaHora.getMinutes();
                let turnos = [];
                if (dia >= 1 && dia <= 5) {
                    turnos = [
                        { nombre: 'Entrada Mañana', hora: 8 * 60, tipo: 'entrada' },
                        { nombre: 'Salida Mañana', hora: 12 * 60, tipo: 'salida' },
                        { nombre: 'Entrada Tarde', hora: 14 * 60, tipo: 'entrada' },
                        { nombre: 'Salida Tarde', hora: 18 * 60, tipo: 'salida' }
                    ];
                } else if (dia === 6) {
                    turnos = [
                        { nombre: 'Entrada Sábado', hora: 8 * 60 + 30, tipo: 'entrada' },
                        { nombre: 'Salida Sábado', hora: 12 * 60 + 30, tipo: 'salida' }
                    ];
                } else {
                    return;
                }
                let turnoAsignado = null;
                for (let t of turnos) {
                    if (
                        minutosDia >= (t.hora - VENTANA) &&
                        minutosDia <= (t.hora + VENTANA)
                    ) {
                        turnoAsignado = t;
                        break;
                    }
                }
                if (!turnoAsignado) return;
                const base = new Date(fechaHora);
                base.setHours(
                    Math.floor(turnoAsignado.hora / 60),
                    turnoAsignado.hora % 60,
                    0
                );
                const diffMin = Math.round((fechaHora - base) / 60000);
                let atraso = 0;
                let favor = 0;
                if (turnoAsignado.tipo === 'entrada') {
                    if (diffMin > 0) atraso = diffMin;
                    else favor = Math.abs(diffMin);
                }
                if (turnoAsignado.tipo === 'salida') {
                    if (diffMin < 0) atraso = Math.abs(diffMin);
                    else favor = diffMin;
                }
                totalFavor += favor;
                totalContra += atraso;
                tbody.innerHTML += `
                    <tr>
                        <td>${fechaHora.toISOString().slice(0,10)}</td>
                        <td>${fechaHora.toTimeString().slice(0,8)}</td>
                        <td>${turnoAsignado.nombre}</td>
                        <td class="text-danger">${atraso}</td>
                        <td class="text-success">${favor}</td>
                    </tr>
                `;
            });
            document.getElementById('totalFavor').innerText = totalFavor;
            document.getElementById('totalContra').innerText = totalContra;
            let resultado = 'EN EQUILIBRIO';
            if (totalFavor > totalContra) {
                resultado = `A FAVOR (+${totalFavor - totalContra} min)`;
            } else if (totalContra > totalFavor) {
                resultado = `EN CONTRA (-${totalContra - totalFavor} min)`;
            }
            document.getElementById('resultadoFinal').innerText = resultado;
        };

        reader.readAsArrayBuffer(file);
    });
</script> --}}

<form id="formAsesoria">
    <div class="card">
        <div class="card-body">
            <input type="hidden" id="asesorid" value="{{ $asesorId }}">
            <div class="row">
                <input type="hidden" id="asesorid" name="asesorid" value="{{ $asesorId }}">
                <input type="hidden" name="horadesde" id="horadesde">
                <input type="hidden" name="horahasta" id="horahasta">
                <input type="text" class="form-control" id="nombre" name="nombre" maxlength="100" hidden>

                <div class="col-md-4">
                    <div class="input-group-material">
                        <input type="text" 
                            class="input-material" 
                            id="apepaterno" 
                            name="apepaterno" 
                            placeholder=" " 
                            maxlength="20" 
                            required>

                        <label class="label-material">Apellido Paterno</label>
                        <span class="linea-animada"></span>
                    </div>
                </div>

                 <!-- Apellido Materno -->
                <div class="col-md-4">
                    <div class="input-group-material">
                        <input type="text" class="input-material" id="apematerno" name="apematerno" placeholder=" " maxlength="30">
                        <label class="label-material">Apellido Materno</label>
                        <span class="linea-animada"></span>
                    </div>
                </div>

                <!-- Nombres -->
                <div class="col-md-4">
                    <div class="input-group-material">
                        <input type="text" class="input-material" id="nombres" name="nombres" placeholder=" " maxlength="60" required>
                        <label class="label-material">Nombres</label>
                        <span class="linea-animada"></span>
                    </div>
                </div>

                <!-- CI -->
                <div class="col-md-4">
                    <div class="input-group-material">
                        <input type="text" class="input-material" id="ci" name="ci" maxlength="20" placeholder=" " required>
                        <label class="label-material">CI</label>
                        <span class="linea-animada"></span>
                    </div>
                </div>

                <!-- Email -->
                <div class="col-md-4">
                    <div class="input-group-material">
                        <input type="email" class="input-material" id="email" name="email" maxlength="100" placeholder=" " required>
                        <label class="label-material">Email</label>
                        <span class="linea-animada"></span>
                    </div>
                </div>

                <!-- Celular -->
                <div class="col-md-4">
                    <div class="input-group-material">
                        <input type="text" class="input-material" id="celular" name="celular" maxlength="30" placeholder=" " required>
                        <label class="label-material">Celular</label>
                        <span class="linea-animada"></span>
                    </div>
                </div>

                <!-- Sucursal -->
                <div class="col-md-3">
                    <div class="input-group-material">
                        <select class="select-material" id="sucursal" name="sucursal" required>
                            <option value="" disabled selected hidden></option>
                            <option value="SANTA CRUZ">SANTA CRUZ</option>
                            <option value="COCHABAMBA">COCHABAMBA</option>
                            <option value="LA PAZ">LA PAZ</option>
                            <option value="BENI">BENI</option>
                            <option value="ORURO">ORURO</option>
                            <option value="POTOSI">POTOSI</option>
                            <option value="TARIJA">TARIJA</option>
                            <option value="SUCRE">SUCRE</option>
                            <option value="PANDO">PANDO</option>
                        </select>
                        <label class="label-material">Sucursal</label>
                        <span class="linea-animada"></span>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="input-group-material">
                        <input type="text" class="input-material" id="modalidad" name="modalidad" readonly>
                        <label class="label-material">Modalidad</label>
                        <span class="linea-animada"></span>
                        <div id="modalidad-alert" class="text-danger mt-1" style="display:none; font-style: italic;">
                            SE LE CONFIRMARÁ LA ASESORIA A SU EMAIL
                        </div>
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                const sucursalSelect = document.getElementById('sucursal');
                                const alertDiv = document.getElementById('modalidad-alert');
                                function actualizarAlerta() {
                                    if (sucursalSelect.value && sucursalSelect.value.toUpperCase() !== 'SANTA CRUZ') {
                                        alertDiv.style.display = 'block';
                                    } else {
                                        alertDiv.style.display = 'none';
                                    }
                                }
                                sucursalSelect.addEventListener('change', actualizarAlerta);
                                actualizarAlerta();
                            });
                        </script>
                    </div>
                </div>

                <!-- Motivo -->
                <div class="col-md-3">
                    <div class="input-group-material">
                        <select class="select-material" id="motivo" name="motivo" required>
                            <option value="" disabled selected hidden></option>
                            <option value="ENFERMEDADES">ENFERMEDADES</option>
                            <option value="LEY DE PENSIONES">LEY DE PENSIONES</option>
                        </select>
                        <label class="label-material">Motivo</label>
                        <span class="linea-animada"></span>
                    </div>
                </div>

                <!-- Fecha -->
                <div class="col-md-3">
                    <div class="input-group-material">
                        <input type="date" 
                            class="input-material" 
                            id="fecha" 
                            name="fecha"
                             onkeydown="return false"
                            disabled
                            required>
                        <label class="label-material">Fecha</label>
                        <span class="linea-animada"></span>
                    </div>
                </div>
                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        const sucursalSelect = document.getElementById('sucursal');
                        const fechaInput = document.getElementById('fecha');
                        function formatearFecha(fecha) {
                            return fecha.toISOString().split('T')[0];
                        }
                        function actualizarRangoFecha() {
                            const hoy = new Date();
                            if (!sucursalSelect.value) {
                                fechaInput.value = '';
                                fechaInput.disabled = true;
                                fechaInput.removeAttribute('min');
                                fechaInput.removeAttribute('max');
                                return;
                            }

                            fechaInput.disabled = false;
                            let fechaMin = new Date(hoy);
                            let fechaMax = new Date(hoy);

                            if (sucursalSelect.value === 'SANTA CRUZ') {
                                fechaMin.setDate(hoy.getDate() + 1);
                            } else {
                                fechaMin.setDate(hoy.getDate() + 7);
                            }
                            fechaMax.setDate(hoy.getDate() + 30);
                            fechaInput.min = formatearFecha(fechaMin);
                            fechaInput.max = formatearFecha(fechaMax);
                            if (fechaInput.value && fechaInput.value < fechaInput.min) {
                                fechaInput.value = '';
                            }
                        }

                        sucursalSelect.addEventListener('change', actualizarRangoFecha);

                    });
                </script>
                <script>
                    const nombres     = document.getElementById('nombres');
                    const apepaterno  = document.getElementById('apepaterno');
                    const apematerno  = document.getElementById('apematerno');
                    const nombreFinal = document.getElementById('nombre');

                    function actualizarNombre() {
                        // Convertir a MAYÚSCULAS
                        nombres.value    = nombres.value.toUpperCase();
                        apepaterno.value = apepaterno.value.toUpperCase();
                        apematerno.value = apematerno.value.toUpperCase();

                        // Construir nombre completo
                        nombreFinal.value = [
                            nombres.value.trim(),
                            apepaterno.value.trim(),
                            apematerno.value.trim()
                        ].filter(Boolean).join(' ');
                    }

                    // Escuchar escritura en todos los campos
                    nombres.addEventListener('input', actualizarNombre);
                    apepaterno.addEventListener('input', actualizarNombre);
                    apematerno.addEventListener('input', actualizarNombre);
                </script>
            </div>
            <hr>
            <label>Horarios Disponibles:</label>
            <div id="tickets" class="row">
                <div class="col-12 text-muted">
                    Seleccione una fecha para ver los horarios disponibles
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-12">
                    <button type="submit" id="btnGuardar" class="btn btn-crear btn-sm" disabled>GUARDAR ASESORIA</button>
                </div>
            </div>
        </div>
    </div>
</form>

<style>
    .ticket {
    border: 2px solid #94c93b;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.25s ease;
    background: #fff;
    font-weight: 600;
    letter-spacing: 0.5px;
    box-shadow: 0 3px 8px rgba(0,0,0,0.05);
}

/* Hover elegante */
.ticket:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 15px rgba(0,0,0,0.12);
    background: #f4fbec;
}

/* Estado seleccionado */
.ticket.selected {
    background: linear-gradient(135deg, #94c93b, #6db52a);
    color: #fff;
    border-color: #6db52a;
    box-shadow: 0 6px 15px rgba(109,181,42,0.4);
    transform: scale(1.05);
}

/* Animación suave */
.ticket strong {
    font-size: 15px;
}

/* Cuando está deshabilitado */
.ticket.disabled {
    background: #f1f1f1;
    color: #aaa;
    border-color: #ddd;
    cursor: not-allowed;
    box-shadow: none;
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
    document.getElementById('fecha').addEventListener('change', async function () {
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
        `<div class="col-12 text-danger">NO HAY HORARIOS DISPONIBLES PARA ESTE DIA</div>`;
    return;
}

const ocupados = ocupadosData.rangos;



            ticketsDiv.innerHTML = '';

            if (horarios.length === 0) {
                ticketsDiv.innerHTML =
                    `<div class="col-12 text-danger">NO HAY HORARIOS DISPONIBLES PARA ESTE DIA</div>`;
                return;
            }

            horarios.forEach(h => {
                let actual = h.horainicio.substring(0,5);
                const duracion = parseInt(h.duracioncita);
                const horaFin = h.horafin.substring(0,5);
                const hoy = new Date();
                const hoyISO = hoy.toISOString().slice(0,10);
                const esHoy = fecha === hoyISO;

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

                    if (esHoy) {
                        const ahora = new Date();
                        const inicioReal = new Date(
                            ahora.getFullYear(),
                            ahora.getMonth(),
                            ahora.getDate(),
                            hh,
                            mm
                        );

                        if (inicioReal <= ahora) {
                            actual = hasta;
                            continue;
                        }
                    }
                    if (hasta <= horaFin && !estaOcupado(actual, hasta, ocupados)) {
                        ticketsDiv.innerHTML += `
                            <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3">

                                <div class="card ticket text-center p-2 selectable"
                                    data-desde="${actual}"
                                    data-hasta="${hasta}">
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
                    `<div class="col-12 text-danger">AGENDA LLENA PARA ESTE DIA</div>`;
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

        document.querySelectorAll('.ticket').forEach(t =>
            t.classList.remove('selected')
        );

        ticket.classList.add('selected');

        document.getElementById('horadesde').value = ticket.dataset.desde;
        document.getElementById('horahasta').value = ticket.dataset.hasta;
        document.getElementById('btnGuardar').disabled = false;
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
    document.getElementById('sucursal').addEventListener('change', function() {

        let modalidadInput = document.getElementById('modalidad');

        if (this.value === 'SANTA CRUZ') {
            modalidadInput.value = 'PRESENCIAL';
        } 
        else if (this.value !== 'SANTA CRUZ') {
            modalidadInput.value = 'PRESENCIAL POR CONFIRMAR';
        } 
        else {
            modalidadInput.value = '';
        }

    });
</script>

<script>
document.querySelectorAll('.select-material').forEach(function(select) {
    select.addEventListener('change', function() {
        if (this.value !== '') {
            this.classList.add('has-value');
        } else {
            this.classList.remove('has-value');
        }
    });
});
</script>
@endsection