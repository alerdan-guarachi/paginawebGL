@extends('adminlte::page')

@section('content_header')

<h1>AGENDAR ASESORÍA</h1>
@stop

@section('css')
<link rel="stylesheet" href="{{ asset('css/tramitesgestora.css') }}">
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
{{-- <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<div class="card text-center">
    <div class="card-body">
        <h5 class="mb-2">Escanea para programar tu asesoría</h5>

        <div id="qrGoodLife" style="display:flex; justify-content:center;"></div>

        <button class="btn btn-sm btn-primary mt-3" onclick="descargarQR()">
            Descargar QR
        </button>
    </div>
</div>
<script>
    const url = "https://goodlife.com.bo/agendarasesoria";
    const qr = new QRCode(document.getElementById("qrGoodLife"), {
        text: url,
        width: 180,
        height: 180,
        colorDark: "#000000",
        colorLight: "#ffffff",
        correctLevel: QRCode.CorrectLevel.H
    });
    function descargarQR() {
        const qrCanvas = document.querySelector('#qrGoodLife canvas');
        const margen = 10;
        const size = qrCanvas.width + margen * 2;
        const canvasFinal = document.createElement('canvas');
        canvasFinal.width = size;
        canvasFinal.height = size;
        const ctx = canvasFinal.getContext('2d');
        ctx.fillStyle = '#FFFFFF';
        ctx.fillRect(0, 0, size, size);
        ctx.drawImage(qrCanvas, margen, margen);
        const link = document.createElement('a');
        link.download = 'QR_PROGRAMAR_ASESORIA_GOOD_LIFE.png';
        link.href = canvasFinal.toDataURL('image/png');
        link.click();
    }
</script> --}}

<form id="formAsesoria">
    <div class="card">
        <div class="card-body">
            <input type="hidden" id="asesorid" value="{{ $asesorId }}">
            <div class="row">
                <input type="hidden" id="asesorid" name="asesorid" value="{{ $asesorId }}">
                <input type="hidden" name="horadesde" id="horadesde">
                <input type="hidden" name="horahasta" id="horahasta">

                <div class="col-md-4 mb-2">
                    <label style="margin-bottom: -15px;">Apellido Paterno</label>
                    <input type="text" class="form-control" id="apepaterno" name="apepaterno" placeholder="Escriba su apellido paterno" maxlength="20">
                </div>
                <div class="col-md-4 mb-2">
                    <label style="margin-bottom: -15px;">Apellido Materno</label>
                    <input type="text" class="form-control" id="apematerno" name="apematerno" placeholder="Escriba su apellido materno" maxlength="20">
                </div>
                <div class="col-md-4 mb-2">
                    <label style="margin-bottom: -15px;">Nombres</label>
                    <input type="text" class="form-control" id="nombres" name="nombres" placeholder="Escriba sus nombres" maxlength="20" required>
                </div>
                <input type="text" class="form-control" id="nombre" name="nombre" maxlength="100" hidden>

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

                <div class="col-md-4 mb-2">
                    <label style="margin-bottom: -15px;">CI</label>
                    <input type="text" class="form-control" id="ci" name="ci" maxlength="20" placeholder="Escriba su ci" required>
                </div>
                <div class="col-md-4 mb-2">
                    <label style="margin-bottom: -15px;">Email</label>
                    <input type="text" class="form-control" id="email" name="email" maxlength="100" placeholder="Escriba su email" required>
                </div>
                <div class="col-md-4 mb-2">
                    <label style="margin-bottom: -15px;">Celular</label>
                    <input type="text" class="form-control" id="celular" name="celular" maxlength="20" placeholder="Escriba su número de celular" required>
                </div>
                <div class="col-md-3 mb-2">
                    <label style="margin-bottom: -15px;">Ciudad</label>
                    <select class="form-control" id="sucursal" name="sucursal" required>
                        <option value="">Seleccione...</option>
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
                </div>
                <div class="col-md-3 mb-2">
                    <label style="margin-bottom: -15px;">Modalidad</label>
                    <input type="text" class="form-control" id="modalidad" name="modalidad" readonly>
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
                <div class="col-md-3 mb-2">
                    <label style="margin-bottom: -15px;">Motivo</label>
                    <select class="form-control" id="motivo" name="motivo" required>
                        <option value="" disabled selected>Seleccione un motivo</option>
                        <option value="ENFERMEDADES">ENFERMEDADES</option>
                        <option value="BENEFICIOS EN PENSIONES">BENEFICIOS EN PENSIONES</option>
                    </select>
                </div>
                <div class="col-md-3 mb-2">
                    <label style="margin-bottom: -15px;">Fecha</label>
                    <input type="date"
                        class="form-control"
                        id="fecha"
                        name="fecha"
                        onkeydown="return false"
                        disabled
                        required>
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
            </div>
            <hr>
            <label>Horarios Disponibles:</label>
            <div id="tickets" class="row">
                <div class="col-12 text-muted">
                    Seleccione una fecha para ver los horarios disponibles
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-md-12">
                    <button type="submit" id="btnGuardar" class="btn btn-crear btn-sm" disabled>CONFIRMAR ASESORIA</button>
                </div>
            </div>
        </div>
    </div>
</form>

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
                            <div class="col-md-2 mb-2">
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

@endsection

