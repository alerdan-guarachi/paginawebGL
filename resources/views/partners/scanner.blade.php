@extends('adminlte::page')

@section('title', 'Escanear Asistencia')

@section('content')
<div class="container mt-4">
    <h4 class="mb-3 text-center">Escanear Código QR</h4>

    <div class="mb-3 text-center d-flex justify-content-center align-items-center gap-3">
    <div>
        <label for="reason" class="form-label"><strong>Motivo de la asistencia:</strong></label>
        <select id="reason" class="form-control">
            <option value="REUNIÓN" selected>REUNIÓN</option>
            <option value="CAPACITACIÓN">CAPACITACIÓN</option>
        </select>
    </div>

    <div>
        <label for="date_reason" class="form-label"><strong>Fecha de Asistencia:</strong></label>
        <input type="date" id="date_reason" class="form-control" value="{{ date('Y-m-d') }}">
    </div>
</div>


    <div id="reader" style="width: 100%; max-width: 400px; margin: auto;"></div>
</div>

<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
let html5QrcodeScanner = new Html5QrcodeScanner(
    "reader", { fps: 10, qrbox: 250 }
);

function onScanSuccess(decodedText, decodedResult) {
    // Pausar temporalmente el scanner
    html5QrcodeScanner.clear(); 

    // Obtener motivo seleccionado
    // Obtener motivo y fecha seleccionados
let reason = document.getElementById('reason').value;
let dateReason = document.getElementById('date_reason').value;

// Agregar parámetros a la URL
let urlParams = `scanner=1&reason=${encodeURIComponent(reason)}&date_reason=${encodeURIComponent(dateReason)}`;
let url = decodedText.includes('?') ? decodedText + '&' + urlParams : decodedText + '?' + urlParams;

    fetch(url)
        .then(response => response.json())
        .then(data => {
            Swal.fire({
                icon: 'success',
                title: 'Asistencia Registrada',
                html: `<b>${data.partner_name} ${data.partner_last_name}</b><br>Motivo: ${reason}`,
                confirmButtonText: 'OK',
                allowOutsideClick: false,
                allowEscapeKey: false
            }).then(() => {
                html5QrcodeScanner.render(onScanSuccess, onScanFailure);
            });
        })
        .catch(err => {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No se pudo registrar la asistencia',
                confirmButtonText: 'OK',
                allowOutsideClick: false,
                allowEscapeKey: false
            }).then(() => {
                html5QrcodeScanner.render(onScanSuccess, onScanFailure);
            });
        });
}

function onScanFailure(error) {}

html5QrcodeScanner.render(onScanSuccess, onScanFailure);
</script>
@endsection
