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

                <div class="col-md-3 mb-2">
                    <label style="margin-bottom: -15px;">CI</label>
                    <input type="text" class="form-control" id="ci" name="ci" maxlength="20" placeholder="Escriba su ci" required>
                </div>
                <div class="col-md-3 mb-2">
                    <label style="margin-bottom: -15px;">Email</label>
                    <input type="text" class="form-control" id="email" name="email" maxlength="100" placeholder="Escriba su email" required>
                </div>
                <div class="col-md-3 mb-2">
                    <label style="margin-bottom: -15px;">Celular</label>
                    <input type="text" class="form-control" id="celular" name="celular" maxlength="20" placeholder="Escriba su número de celular" required>
                </div>
                <div class="col-md-3 mb-2">
                    <label style="margin-bottom: -15px;">Sucursal</label>
                    <input type="text" class="form-control" id="sucursal" name="sucursal" value="SANTA CRUZ" readonly>
                </div>
                <div class="col-md-6 mb-2">
                    <label style="margin-bottom: -15px;">Motivo</label>
                    <select class="form-control" id="motivo" name="motivo" required>
                        <option value="" disabled selected>Seleccione un motivo</option>
                        <option value="ENFERMEDADES">ENFERMEDADES</option>
                        <option value="BENEFICIOS EN PENSIONES">BENEFICIOS EN PENSIONES</option>
                    </select>
                </div>
                <div class="col-md-6 mb-2">
                    <label style="margin-bottom: -15px;">Fecha</label>
                    <input type="date" class="form-control" id="fecha" name="fecha" min="{{ now()->toDateString() }}" max="{{ now()->addDays(7)->toDateString() }}" required>
                </div>
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
            const ocupados = await ocupadosRes.json();

            const ocupadosSet = new Set(
                ocupados.map(o => `${o.horadesde}-${o.horahasta}`)
            );

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
                    if (hasta <= horaFin && !ocupadosSet.has(rango)) {
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



@endsection

